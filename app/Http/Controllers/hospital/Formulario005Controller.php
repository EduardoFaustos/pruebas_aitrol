<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Carbon\Carbon;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_solicitud;
use Sis_medico\User;
use Sis_medico\Ho_Triaje_Manchester;
use Sis_medico\Agenda;
use Sis_medico\Paciente;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_child_pugh;
use Sis_medico\Medicina;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;
use Sis_medico\Ho_Evoluciones_Enfermeria;
use Sis_medico\Ho_Hc_Receta_Evolucion;

class Formulario005Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index_005($id_solicitud)
    {

        $solicitud = Ho_solicitud::find($id_solicitud);
        $paciente = Paciente::find($solicitud->id_paciente);

        $log = $solicitud->log->last();

        $historia = $solicitud->agenda->historia_clinica;
        //dd($historia);

        $evolucion = $historia->evoluciones->last();

        $child_pugh = $evolucion->child_pug;
        $examenes = Examen_Orden::where('id_paciente', $solicitud->id_paciente)->latest('created_at')->first();
        return view('hospital.formulario_005.index_005', ['examenes' => $examenes, 'paciente' => $paciente, 'solicitud' => $solicitud, 'log' => $log, 'evolucion' => $evolucion, 'child_pugh' => $child_pugh]);
    }

    public function f5_evolucion($id)
    {
        $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();


        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $solicitud->id_hcproc)->Orderby('created_at', 'DESC')->get();
        //dd($evoluciones);
        $historia = $solicitud->agenda->historia_clinica;
        //dd($request->all());
        //$recetas = hc_receta::where('id_hc', $historia->hcid)->orderby('created_at', 'DESC')->get();
        $hc_receta_evolucion = Ho_Hc_Receta_Evolucion::where('id_evolucion', $hc_receta_evolucion->evolucion->id)->get();

        $receta = $hc_receta_evolucion->id_receta;
        
        return view('hospital.formulario_005.evolucion', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones, 'recetas' => $recetas]);
    }

    public function guardar_evolucion(Request $request, $id_evol)
    {

        //dd($request->all());

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request['solicitud_id'];
        $solicitud = Ho_solicitud::find($id_solicitud);
        $evolucion = Hc_Evolucion::find($id_evol);


        if (!is_null($evolucion)) {
            $arr_evolucion = [
                'motivo'            => $request['motivo'],
                'cuadro_clinico'    => $request['n_evolucion'],
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $evolucion->update($arr_evolucion);

            $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

            if (!is_null($child_pugh)) {
                $arr_chill = [
                    'examen_fisico'     => $request['examen_fisico'],
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $child_pugh->update($arr_chill);
            }
        }

        return view('hospital.formulario_005.nueva_evolucion', ['evolucion' => $evolucion, 'child_pugh' => $child_pugh, 'solicitud' => $solicitud]);
    }

    public function evolucion_detalle($id, Request $request)
    {
        //dd($request->all());
        $id_solicitud = $request['id_solicitud'];
        $evolucion = Hc_Evolucion::find($id);
        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

        $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id_solicitud)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();
        //dd($solicitud);
        //$id_receta = $request['id_receta'];
        //$receta = hc_receta::find($id_receta);
        //$detalles = $receta->detalles;
        $historia = $solicitud->agenda->historia_clinica;
        
        $receta = hc_receta::where('id_hc', $historia->hcid)->orderby('created_at', 'DESC')->first();
        $detalles = $receta->detalles;

        return view('hospital.formulario_005.nueva_evolucion', ['evolucion' => $evolucion, 'child_pugh' => $child_pugh, 'solicitud' => $solicitud, 'receta'=>$receta, 'detalles'=>$detalles]);
    }

    public function crear_evolucion($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();


        $arr_evolucion = [
            'hc_id_procedimiento'   => $solicitud->id_hcproc,
            'hcid'                  => $solicitud->hcid,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $evolucion = Hc_Evolucion::insertGetId($arr_evolucion);


        $arr_chill = [
            'id_hc_evolucion'       => $evolucion,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
        ];

        $child_pugh = hc_child_pugh::create($arr_chill);

        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $solicitud->id_hcproc)->Orderby('created_at', 'DESC')->get();

        $historia = $solicitud->agenda->historia_clinica;

        $arr_receta = [
            'id_hc'                 => $historia->hcid,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $receta = hc_receta::create($arr_receta);

        //$recetas = hc_receta::where('id_hc', $historia->hcid)->orderby('created_at', 'DESC')->get();
        $arr_receta_evolucion =[
            'id_receta'             => $arr_receta->id,
            'id_evolucion'          => $arr_evolucion->id,
            'estado'                => 1,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $receta_evolucion = ho_hc_receta_evolucion::create($arr_receta_evolucion);
        
        
        return view('hospital.formulario_005.evolucion', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones, 'evolucion' => $evolucion, 'recetas'=>$recetas ]);

    }

    public function f5_diagnostico()
    {

        return view('hospital.formulario_005.diagnostico');
    }

    public function f5_medidas_generales()
    {

        return view('hospital.formulario_005.med_generales');
    }

    public function f5_tratamiento()
    {

        return view('hospital.formulario_005.tratamiento');
    }

    public function f5_plan()
    {

        return view('hospital.formulario_005.plan');
    }

    public function f5_medicamentos($id)
    {
        $solicitud = Ho_Solicitud::find($id);

        $historia = $solicitud->agenda->historia_clinica;

        $recetas = hc_receta::where('id_hc', $historia->hcid)->orderby('created_at', 'DESC')->get();

        //dd($receta);

        return view('hospital.formulario_005.medicamentos', ['solicitud' => $solicitud, 'recetas' => $recetas]);
    }

    public function receta_detalle($id, Request $request)
    {
        $id_solicitud = $request['id_solicitud'];
        $solicitud = Ho_Solicitud::find($id_solicitud);
        $receta = hc_receta::find($id);
        $detalles = $receta->detalles;

        return view('hospital.formulario_005.nueva_receta', ['solicitud' => $solicitud, 'detalles' => $detalles, 'receta' => $receta]);
    }

    public function crear_receta($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id);
        $historia = $solicitud->agenda->historia_clinica;

        $arr_receta = [
            'id_hc'                 => $historia->hcid,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $receta = hc_receta::create($arr_receta);

        $recetas = hc_receta::where('id_hc', $historia->hcid)->orderby('created_at', 'DESC')->get();


        return view('hospital.formulario_005.medicamentos', ['solicitud' => $solicitud, 'recetas' => $recetas]);
    }

    public function eliminar_medicina($id_detalle, Request $request)
    {

        $detalle_receta = hc_receta_detalle::find($id_detalle);
        $detalle_receta->delete();
        $id_solicitud = $request['id_solicitud'];
        $solicitud = Ho_Solicitud::find($id_solicitud);
        $id_receta = $request['id_receta'];
        $receta = hc_receta::find($id_receta);
        $detalles = $receta->detalles;


        return view('hospital.emergencia.treceavopaso.receta_detalle', ['solicitud' => $solicitud, 'detalles' => $detalles, 'receta' => $receta]);
    }

    public function editar_medicina(Request $request, $id_detalle)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $detalle_receta = hc_receta_detalle::find($id_detalle);
        $arr_med = [
            'dosis'             => $request['dosis' . $id_detalle],
            'cantidad'          => $request['cantidad' . $id_detalle],
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $detalle_receta->update($arr_med);

        return "ok";
    }

    public function f05_medicina_guardar(Request $request, $id_rec)
    {
        //dd($request->all());

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request['solicitud_id'];
        $solicitud = Ho_Solicitud::find($id_solicitud);

        $id_generico = $request['id_generico'];

        $receta = hc_receta::find($id_rec);

        $medicina = Medicina::find($id_generico);

        if (is_null($medicina)) {
            return "0";
        }

        $receta_detalle = $receta->detalles->where('id_medicina', $medicina->id)->first();

        if (is_null($receta_detalle)) {

            hc_receta_detalle::create([
                'id_hc_receta'    => $receta->id,
                'id_medicina'     => $medicina->id,
                'nombre'          => $medicina->nombre,
                'cantidad'        => 1,
                'dosis'           => $medicina->dosis,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario

            ]);
        }

        $receta2 = hc_receta::find($receta->id);

        return view('hospital.formulario_005.recetajs', ['solicitud' => $solicitud, 'detalles' => $receta2->detalles, 'receta' => $receta]);
    }

    public function f5_examenes()
    {

        return view('hospital.formulario_005.examenes');
    }

    public function f5_salas()
    {

        return view('hospital.formulario_005.salas');
    }

    public function pdf_formulario005()
    {


        $view = \View::make('hospital.formulario_005.formulario005_pdf')->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        return $pdf->stream('formulario005.pdf');
    }

    public function cargar_examenes($id)
    {

        $idPaciente = Ho_Solicitud::find($id);
        $paciente = Paciente::find($idPaciente->id_paciente);
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        $ordenes = DB::table('examen_orden as eo')
            ->where('eo.id_paciente', $idPaciente->id_paciente)
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            ->leftjoin('empresa as em', 'em.id', 'eo.id_empresa')
            ->leftjoin('nivel as n', 'n.id', 'eo.id_nivel')
            ->leftjoin('protocolo as proto', 'proto.id', 'eo.id_protocolo')
            ->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')
            ->join('users as cu', 'cu.id', 'eo.id_usuariocrea')
            ->join('users as mu', 'mu.id', 'eo.id_usuariomod')
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 's.nombre as snombre', 'n.nombre as nnombre', 'em.nombrecomercial', 'cu.nombre1 as cnombre1', 'cu.apellido1 as capellido1', 'mu.nombre1 as mnombre1', 'mu.apellido1 as mapellido1', 'em.nombre_corto', 'proto.pre_post', 's.tipo as stipo')
            ->OrderBy('eo.fecha_orden', 'desc')
            ->get();

        return view('hospital.formulario_005.mostrar_examenes', ['ordenes' => $ordenes, 'usuarios' => $usuarios, 'paciente' => $paciente]);
    }

    public function imprimir_resultado($id)
    {
        $orden = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //$detalle = $orden->detalles;
        $resultados =  $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
        //$agrupador = Examen_Agrupador::all();

        //Recalcula Porcentaje 
        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }


        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }


        // Fin recalcula Porcentaje


        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::all();
        }
        $ucreador = $orden->crea;
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //$vistaurl="hc4.laboratorio.resultados_pdf";
        $vistaurl = "hospital.formulario_005.pdf_resultados";
        $view =  \View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador', 'user'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function puede_imprimir($id)
    {

        $orden = Examen_Orden::find($id);
        $detalle = $orden->detalles;
        $resultados =  $orden->resultados;

        $cant_par = 0;
        foreach ($detalle as $d) {

            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad = 0;

        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }

        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        return ['cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par];
    }


    public function evolucion_enfermeria($id)
    {

        $solicitud = Ho_Solicitud::find($id);
        $historia = $solicitud->agenda->historia_clinica;

        $evoluciones = Ho_Evoluciones_Enfermeria::where('hcid', $historia->hcid)->Orderby('created_at', 'DESC')->get();


        return view('hospital.formulario_005.evolucion_enfermeria', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones]);
    }

    public function evol_detalle_enfermeria($id, Request $request)
    {
        $id_solicitud = $request['id_solicitud'];
        $solicitud = Ho_Solicitud::find($id_solicitud);
        $evolucion = Ho_Evoluciones_Enfermeria::find($id);
        return view('hospital.formulario_005.nueva_evolucion_enfermeria', ['solicitud' => $solicitud, 'evolucion' => $evolucion]);
    }


    public function crear_evolucion_enfer($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $solicitud = Ho_Solicitud::find($id);
        $historia = $solicitud->agenda->historia_clinica;


        $arr_evolucion = [
            'id_solicitud'          => $solicitud->id,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'hcid'                  => $historia->hcid,
        ];

        $evolucion = Ho_Evoluciones_Enfermeria::create($arr_evolucion);


        $evoluciones = Ho_Evoluciones_Enfermeria::where('hcid', $historia->hcid)->Orderby('created_at', 'DESC')->get();


        return view('hospital.formulario_005.evolucion_enfermeria', ['solicitud' => $solicitud, 'evoluciones' => $evoluciones]);
    }

    public function guardar_evolucion_enfermeria(Request $request, $id_evol)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request['solicitud_id'];
        $solicitud = Ho_solicitud::find($id_solicitud);
        $evolucion = Ho_Evoluciones_Enfermeria::find($id_evol);

        if (!is_null($evolucion)) {
            $arr_evolucion = [
                'ip_modificacion'       => $ip_cliente,
                'id_usuariomod'         => $idusuario,
                'parenteral'            => $request['parenteral'],
                'via_oral'              => $request['via_oral'],
                'total_ingreso'         => $request['total_ingresos'],
                'orina'                 => $request['orina'],
                'drenaje'               => $request['drenaje'],
                'otros_elimina'         => $request['otros_elimina'],
                'total_elimina'         => $request['total_elimina'],
                'aseo_bano'             => $request['aseo_bano'],
                'peso'                  => $request['peso'],
                'dieta'                 => $request['dieta'],
                'num_comidas'           => $request['num_comidas'],
                'num_micciones'         => $request['num_micciones'],
                'num_deposiciones'      => $request['num_deposiciones'],
                'actividad_fisica'      => $request['actividad_fisica'],
                'cambio_sonda'          => $request['cambio_sonda'],
                'recanalizacion'        => $request['recanalizacion'],
                'responsable'           => $request['responsable'],
                'frec_respiratoria'     => $request['fRespiratori'],
                'presion_arterial'      => $request['pArterial'],
            ];

            $evolucion->update($arr_evolucion);
        }

        return view('hospital.formulario_005.nueva_evolucion_enfermeria', ['solicitud' => $solicitud, 'evolucion' => $evolucion]);
    }

    public function guardarimagen(Request $request, $id)
    {
    
        $evolucion = Ho_Evoluciones_Enfermeria::find($id);
        $extension = 'ho_'.$id.'_'.date('Ymds').'_'.$request['my-file']->getClientOriginalName();
        Storage::disk('hc_ima')->put($extension, \File::get($request['my-file']));
        $evolucion->imagen_signovitales = $extension;
        $evolucion->save();
    }
    public function verpdf($id){
        $evolucion = Ho_Evoluciones_Enfermeria::find($id);
        $datoPaciente = Paciente::where('id',$evolucion->dato->id_paciente)->first();
        $view = \View::make('hospital.formulario_005.verpdf005', compact('evolucion','datoPaciente'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream('Evolució Enfermeria' . '.pdf');

    }
}
