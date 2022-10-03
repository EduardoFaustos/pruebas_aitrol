<?php

namespace Sis_medico\Http\Controllers\hc4;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_cpre_eco;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;
use Sis_medico\User;

class ProcedimientosEndoscopicosController extends Controller
{
    private function rol_new($opcion)
    {
        //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;

        }

    }

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id_paciente)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $paciente       = Paciente::find($id_paciente);
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '0')
            ->where('hc_p.estado', '1')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_p.id_seguro as seguro_final', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos')
            ->OrderBy('a.fechaini', 'desc')->get();

        //dd($pro_completo_0);

        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '0')
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'h.id_seguro as hc_id_seguro', 'hc_p.id_seguro as seguro_final', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos')->OrderBy('a.fechaini', 'desc')->get();
        //dd($)

        //dd($pro_completo_0);
        //dd($pro_final_0);

        return view('hc4/procedimiento_endoscopico/index', ['paciente' => $paciente, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0]);
    }

    public function editar($id_procedimiento, $id_paciente)
    {

        //dd($proto_hcid);
        $protocolo = hc_protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
        //dd($protocolo->hcid);
        $id_seguro_pro = hc_procedimientos::where('id', $id_procedimiento)->first();
        //dd($id_seguro_pro);
        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();
        $px        = Procedimiento::where('procedimiento.estado', '1')->get();
        //dd($hc_seguro->nombre);

        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $tipo          = 0;
        $doctores      = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->orderBy('apellido1')->get();

        $proc_completo = procedimiento_completo::all();

        return view('hc4/procedimiento_endoscopico/editar', ['protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'hc_seguro' => $hc_seguro, 'px' => $px, 'doctores' => $doctores, 'proc_completo' => $proc_completo, 'procedimiento_completo_plantilla' => null]);
    }

    public function editar2($id_procedimiento)
    {
        $protocolo     = hc_protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $tipo          = 1;
        // $agendaprocedimientos=AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
        //$procedimientos_todos=Procedimiento::all();
        return view('hc4/procedimiento_endoscopico/editar', ['protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo]);
    }

    public function guardar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //dd($request['id_procedimiento']);

        $id_seguro_pro  = hc_procedimientos::where('id', $request['id_procedimiento'])->first();
        $procedimientos = $request['procedimiento'];
        if (isset($request['procedimiento'])) {
            if (is_null($procedimientos)) {
                return "No puede Guardar sin Procedimiento";
            } else {
                $valor = 0;
                foreach ($procedimientos as $value) {
                    $procedimiento_verificar = procedimiento::find($value);
                    if (!is_null($procedimiento_verificar->id_grupo_procedimiento)) {
                        $valor = 1;
                    }
                }

                if ($valor == 0) {
                    return "Seleccione un procedimiento principal.";
                }

            }
        }
        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();

        //dd($request);

        $protocolo     = hc_protocolo::find($request['id_protocolo']);
        $procedimiento = hc_procedimientos::find($request['id_procedimiento']);

        $protocolo_new     = $protocolo;
        $procedimiento_new = $procedimiento;

        if ($protocolo_new != null && $procedimiento_new != null) {

            $procedimientos_new = [
                'anterior'         => 'PROC_ENDOS -> Conclusion: ' . $protocolo_new->conclusion . ' hallazgos:' . $protocolo_new->hallazgos . ' doctor: ' . $procedimiento_new->id_doctor_examinador,
                'nuevo'            => 'PROC_ENDOS -> Conclusion: ' . $request['conclusion'] . ' hallazgos:' . $request['hallazgos'] . ' doctor:' . $request['id_doctor_examinador'],
                'hc_id'            => $protocolo_new->hcid,
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $procedimiento_new->id,
                'id_protocolo'     => $protocolo_new->id,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($procedimientos_new);
        }

        $input = [
            'conclusion'         => $request['conclusion'],
            'hallazgos'          => $request['hallazgos'],
            'tipo_procedimiento' => '0',
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,
        ];
        if (($request['id_doctor_examinador'] == '9666666666') || ($request['id_doctor_examinador'] == 'GASTRO')) {
            $input2 = [
                'id_doctor_examinador'      => $idusuario,
                'ip_modificacion'           => $ip_cliente,
                'id_procedimiento_completo' => null,
                'id_usuariomod'             => $idusuario,
            ];
        } else {
            $input2 = [
                'id_doctor_examinador'      => $request['id_doctor_examinador'],
                'ip_modificacion'           => $ip_cliente,
                'id_procedimiento_completo' => null,
                'id_usuariomod'             => $idusuario,
            ];
        }

        $procedimiento->update($input2);
        $protocolo->update($input);
        $anteriores = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['id_procedimiento']);

        //dd($procedimientos);
        if (!is_null($procedimientos)) {
            $anteriores->delete();
            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $request['id_procedimiento'],
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }
        }
        return view('hc4/procedimiento_endoscopico/unico', ['hc_seguro' => $hc_seguro, 'protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $request['tipo']]);
    }

    public function guardar_2(Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $protocolo      = hc_protocolo::find($request['id_protocolo']);
        $procedimiento  = hc_procedimientos::find($request['id_procedimiento']);
        $id_seguro_pro  = $procedimiento;
        $procedimientos = $request['procedimiento'];
        if (isset($request['procedimiento'])) {

            if ($idusuario == "9666666666") {
                //dd($request);
            }
            if (is_null($procedimientos)) {
                return "No puede Guardar sin Procedimiento";
            } else {
                $valor = 0;
                foreach ($procedimientos as $value) {
                    $procedimiento_verificar = procedimiento::find($value);
                    if (!is_null($procedimiento_verificar->id_grupo_procedimiento)) {
                        $valor = 1;
                    }
                }

                if ($valor == 0) {
                    return "Seleccione un procedimiento principal.";
                }

            }
        }
        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();
        //dd($request);

        $input = [
            'conclusion'      => $request['conclusion'],
            'hallazgos'       => $request['hallazgos'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        if (($request['id_doctor_examinador'] == '9666666666') || ($request['id_doctor_examinador'] == 'GASTRO')) {
            $input2 = [
                'id_doctor_examinador' => $idusuario,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];
        } else {
            $input2 = [
                'id_doctor_examinador' => $request['id_doctor_examinador'],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];
        }

        $procedimiento->update($input2);
        $protocolo->update($input);
        $anteriores = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['id_procedimiento']);

        //dd($procedimientos);
        if (!is_null($procedimientos)) {
            $anteriores->delete();
            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $request['id_procedimiento'],
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }
        }
        return "ok";
    }

    public function editar_evolucion($id_procedimiento, $id_paciente)
    {
        $evolucion = hc_evolucion::find($id_procedimiento);
        return view('hc4/procedimiento_endoscopico/editar_evolucion', ['evolucion' => $evolucion, 'id_paciente' => $id_paciente]);
    }

    public function guardar_evolucion(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $evolucion  = hc_evolucion::find($request['id_evolucion']);

        $evolucion_new = $evolucion;
        if (!is_null($evolucion_new)) {

            $procedimientos_hist_new = [
                'anterior'         => 'PROC_ENDOS_h.evolucion -> Motivo: ' . $evolucion_new->motivo . ' Detalle:' . $evolucion_new->cuadro_clinico,
                'nuevo'            => 'PROC_ENDOS_h.evolucion -> Motivo: ' . $request['motivo'] . ' Detalle:' . $request['cuadro_clinico'],
                'hc_id'            => $evolucion_new->hcid,
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $evolucion_new->hc_id_procedimiento,
                'id_evolucion'     => $evolucion_new->id,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($procedimientos_hist_new);
        }

        $input = [
            'motivo'          => $request['motivo'],
            'cuadro_clinico'  => $request['cuadro_clinico'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        $evolucion->update($input);
        return view('hc4/procedimiento_endoscopico/unico_evolucion', ['evolucion' => $evolucion]);
    }

    public function mostrar_foto_eliminar($id)
    {
        $imagen = hc_imagenes_protocolo::find($id);
        return view('hc4/procedimiento_endoscopico/modal', ['imagen' => $imagen]);
    }

    public function eliminar_foto_eliminar($id)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'estado'          => '0',
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        hc_imagenes_protocolo::where('id', $id)
            ->update($input1);
        return "Archivo eliminado correctamente";
    }

    public function mostrar_proc_endo_plantilla($id)
    {
        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();
        return view('hc4/procedimiento_endoscopico/modal_plantilla', ['proc_completo' => $proc_completo, 'id' => $id]);
    }

    public function tecnica_plantilla(Request $request)
    {

        //return $request->all();
        $proc_com = $request['proc_com'];

        $procedimiento = procedimiento_completo::find($proc_com);

        return $procedimiento;

    }

    public function agregar_evolucion($id_procedimiento)
    {

        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $id_historia   = $procedimiento->id_hc;
        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $idusuario     = Auth::user()->id;
        $evolucion     = hc_evolucion::where('hc_id_procedimiento', $id_procedimiento)->OrderBy('id', 'Desc')->first();
        if (!is_null($evolucion)) {
            $secuencia = $evolucion->secuencia + 1;
        } else {
            $secuencia = 0;
        }
        $input_hc_evolucion_pos = [
            'hc_id_procedimiento' => $id_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => $secuencia,
            'motivo'              => ' ',
            'ip_modificacion'     => $ip_cliente,
            'fecha_ingreso'       => ' ',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];
        $id_evolucion = hc_evolucion::insertGetId($input_hc_evolucion_pos);
        $evolucion    = hc_evolucion::find($id_evolucion);
        return view('hc4/procedimiento_endoscopico/unico_evolucion_agregar', ['evolucion' => $evolucion]);
    }
    //proce_cargar_tabla
    public function cargar_cie10($id)
    {

        $c = [];
        $x = 0;

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $id)->get();

        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $c[$x] = ['cie10' => $value->cie10, 'id' => $value->id, 'descripcion' => $c3->descripcion, 'pre_def' => $value->presuntivo_definitivo, 'ingreso_egreso' => $value->ingreso_egreso];
                }
                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $c[$x] = ['cie10' => $value->cie10, 'id' => $value->id, 'descripcion' => $c4->descripcion, 'pre_def' => $value->presuntivo_definitivo, 'ingreso_egreso' => $value->ingreso_egreso];
                }
                $x++;
            }
            return $c;
        } else {
            return "no";
        }

    }

    //Carga Tabla Biopsias
    public function cargar_biopsia_frasco($id)
    {

        $c = [];
        $x = 0;

        $biopsia = DB::table('hc4_biopsias')->where('hc4_biopsias.hc_id_procedimiento', $id)->get();

        if (!is_null($biopsia)) {

            foreach ($biopsia as $value) {

                $c[$x] = ['id' => $value->id, 'descripcion' => $value->descripcion_frasco, 'numero_frasco' => $value->numero_frasco, 'observacion' => $value->observacion];

                $x++;
            }

            return $c;

        } else {
            return "no";
        }

    }

    public function agregar_cie10_proc(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }
        /*if($request['pre_def']==null){
        return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }
        if($request['in_eg']==null){
        return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }*/
        //return $request->all();

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $request['hc_id_procedimiento'])->get();

        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {

                $proc_endo_new = [
                    'anterior'         => 'PROC_CIE10 -> Diagnostico: ' . $value->cie10 . ' Presuntivo_Definitivo: ' . $value->presuntivo_definitivo . ' ingreso_egreso: ' . $value->ingreso_egreso,
                    'nuevo'            => 'PROC_CIE10 -> Diagnostico: ' . $request['codigo'] . ' Presuntivo_Definitivo: ' . $request['pre_def'] . ' ingreso_egreso: ' . $request['in_eg'],
                    'hc_id'            => $value->hcid,
                    'id_paciente'      => $request['id_paciente'],
                    'id_procedimiento' => $value->hc_id_procedimiento,
                    //'id_cie10' => $value->id,
                    'id_usuariomod'    => $idusuario,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'ip_creacion'      => $ip_cliente,
                ];
                Hc_Log::create($proc_endo_new);

            }
        }

        $input2 = [
            'hcid'                  => $request['hcid'],
            'cie10'                 => $request['codigo'],
            'hc_id_procedimiento'   => $request['hc_id_procedimiento'],
            'ingreso_egreso'        => $request['in_eg'],
            'presuntivo_definitivo' => $request['pre_def'],

            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        $id = Hc_Cie10::insertGetId($input2);

        $count = Hc_Cie10::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();

        $cie10 = Hc_Cie10::find($id);

        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }

    //Agregar Prescripcion Biopsias
    public function agregar_biopsia_proc(Request $request)
    {

        //return $request->all();

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        /*if($request['codigo_biop']==null){
        return ['estado' => 'err', 'msn' => 'Ingrese detalle Prescripcion'];
        }*/

        //Verifica Si existe el procedimiento Biopsia Agregado
        $cont_biop   = 0;
        $cont_biop_p = 0;
        $cont_biop_g = 0;

        $proced_agregados = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['hc_id_procedimiento'])->get();

        if (!is_null($proced_agregados)) {

            foreach ($proced_agregados as $value) {

                if ($value->id_procedimiento == 19) {
                    $cont_biop++;
                }

            }

            if ($cont_biop == 0) {

                $input_pro_final = [
                    'id_hc_procedimientos' => $request['hc_id_procedimiento'],
                    'id_procedimiento'     => 19,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);

                $verifica_proced = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['hc_id_procedimiento'])->get();

                foreach ($verifica_proced as $value) {

                    if (($value->id_procedimiento == 8) || ($value->id_procedimiento == 9)) {

                        $cont_biop_p++;

                    } else {

                        if (($value->id_procedimiento == 38) || ($value->id_procedimiento == 24)) {

                            $cont_biop_g++;

                        }

                    }

                }

            } else {

                $verifica_proced = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['hc_id_procedimiento'])->get();

                foreach ($verifica_proced as $value) {

                    if (($value->id_procedimiento == 8) || ($value->id_procedimiento == 9)) {

                        $cont_biop_p++;

                    } else {

                        if (($value->id_procedimiento == 38) || ($value->id_procedimiento == 24)) {

                            $cont_biop_g++;

                        }

                    }

                }

            }

        }

        //return $existe_biopsia;

        $biopsias = hc4_biopsias::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->OrderBy('id', 'Desc')->first();

        if (!is_null($biopsias)) {

            $secuencia = $biopsias->numero_frasco + 1;
        } else {

            $secuencia = 1;
        }

        $log_biopsia = DB::table('hc4_biopsias')->where('hc4_biopsias.hc_id_procedimiento', $request['hc_id_procedimiento'])->get();

        if (!is_null($log_biopsia)) {

            foreach ($log_biopsia as $value) {

                $proc_endo_biopsias = [

                    'anterior'         => 'PROC_ENDOSC_BIOPSIA -> Detalle_Frasco: ' . $value->descripcion_frasco . ' Detalle_Observacion: ' . $value->observacion,
                    'nuevo'            => 'PROC_ENDOSC_BIOPSIA -> Detalle_Frasco: ' . $request['descripcion_frasco'] . ' Detalle_Observacion: ' . $request['detalle_observacion'],
                    'hc_id'            => $value->hcid,
                    'id_paciente'      => $request['id_paciente'],
                    'id_procedimiento' => $value->hc_id_procedimiento,
                    'id_usuariomod'    => $idusuario,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'ip_creacion'      => $ip_cliente,

                ];

                Hc_Log::create($proc_endo_biopsias);

            }

        }
        $tipo_usuario = '';

        if ($cont_biop_p > 0) {

            $tipo_usuario = 15;

        } else {

            if ($cont_biop_g > 0) {

                $tipo_usuario = 16;

            }

        }

        $input_biopsia = [
            'hc_id_procedimiento' => $request['hc_id_procedimiento'],
            'hcid'                => $request['hcid'],
            'id_paciente'         => $request['id_paciente'],
            'id_doctor'           => $idusuario,
            //'fecha_orden' => $idusuario,
            'descripcion_frasco'  => $request['descripcion_frasco'],
            'observacion'         => $request['detalle_observacion'],
            'numero_frasco'       => $secuencia,
            'id_tipo_usuario'     => $tipo_usuario,
            'estado'              => 1,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
        ];

        $id = Hc4_Biopsias::insertGetId($input_biopsia);

        $count = Hc4_Biopsias::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();

        $biopsias = Hc4_Biopsias::find($id);

        return ['count' => $count, 'id' => $id, 'biopsias' => $biopsias->descripcion_frasco, 'observacion' => $biopsias->observacion, 'secuencia' => $secuencia];

    }

    //Eliminar Prescripcion Biopsias
    public function eliminar_prescripcion($id)
    {

        $biopsia = DB::table('hc4_biopsias')->where('id', $id)->delete();
        return "ok";

    }

    //PERMITE IMPRIMIR BIOPSIAS EN HOJA MEMBRETADA
    public function imprime_biopsia($id, $id_hcid, $id_doct)
    {
        // dd($id);
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        //$contador_biop = DB::table('hc4_biopsias')->get()->count();

        $hc_proced = hc_procedimientos::find($id);

        $count_biospsias = Hc4_Biopsias::where('hc_id_procedimiento', $id)->get()->count();

        if (($count_biospsias > 0) && (!is_null($hc_proced))) {

            $pro   = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id)->get();
            $mas   = true;
            $texto = "";

            foreach ($pro as $value) {
                if ($mas == true) {
                    $texto = $texto . $value->procedimiento->nombre;
                    $mas   = false;
                } else {
                    $texto = $texto . ' + ' . $value->procedimiento->nombre;
                }
            }

            $lista_proced = $texto;

            $historia = historiaclinica::find($id_hcid);
            $paciente = paciente::find($historia->id_paciente);

            if ($paciente->fecha_nacimiento != null) {
                $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            }

            $biop_first = Hc4_Biopsias::where('hc_id_procedimiento', $id)->first();

            $impr_biospsias = Hc4_Biopsias::where('hc_id_procedimiento', $id)->get();

            $doctor_solicitante = DB::table('users as us')
                ->where('us.id', $id_doct)
                ->first();

            $vistaurl = "hc4.procedimiento_endoscopico.pdf_orden_biopsias";
            $view     = \View::make($vistaurl, compact('historia', 'paciente', 'edad', 'impr_biospsias', 'doctor_solicitante', 'biop_first', 'hc_proced', 'lista_proced'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
            $pdf->setPaper('a4', 'landscape');

            return $pdf->stream($historia->id_paciente . '_Biopsias_' . $id . '.pdf');
        }

        //$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
        //dd($impr_biospsias);
    }

    //Agregar CPRE+ECO
    public function modal_cpre_eco_hc4($hcid)
    {
        //dd($hcid);
        $cpre_eco = Hc_cpre_eco::where('hcid', $hcid)->first();
        $proc     = hc_protocolo::where('hcid', $hcid)->get();
        $texto    = '';

        foreach ($proc as $p) {
            $texto = $texto . $p->hallazgos . '<br>';

        }
        //dd($texto);
        //dd($cpre_eco);
        /*return view('hc_admision/protocolo/cpre_eco_modal',['cpre_eco' => $cpre_eco, 'hcid' => $hcid, 'texto' => $texto]);*/
        return view('hc4/procedimiento_endoscopico/modal_cpre_eco', ['cpre_eco' => $cpre_eco, 'hcid' => $hcid, 'texto' => $texto]);
    }

    public function modal_hc4_crear_editar(Request $request)
    {
        //dd($request -> all());
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $hcid       = $request->hcid;
        $cpre_eco   = Hc_cpre_eco::where('hcid', $hcid)->first();
        if (is_null($cpre_eco)) {
            $input = [

                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'hcid'            => $request["hcid"],
                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Hc_cpre_eco::create($input);
            //return "Procedimiento CPRE + ECO Guardado";
        } else {
            $input = [
                'hallazgos'       => $request["cphallazgos"],
                'conclusion'      => $request["cpconclusion"],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            $cpre_eco->update($input);
            //return "Procedimiento CPRE + ECO Actualizado";
        }
        //return redirect()->route('paciente.procedimiento_endoscopico');

    }

    public function unico_proc_index($id, $id_paciente)
    {
        //dd($id);

        $paciente       = Paciente::find($id_paciente);
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('hc_proto.id', $id)
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_p.id_seguro as seguro_final', 'hc_proto.tipo_procedimiento as tipo', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos')
            ->OrderBy('hc_proto.created_at', 'desc')->get();

        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.id', $id)
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'h.id_seguro as hc_id_seguro', 'hc_p.id_seguro as seguro_final', 'hc_proto.tipo_procedimiento as tipo', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos')->OrderBy('hc_proto.created_at', 'desc')->get();

        //dd($pro_final_0);
        return view('hc4/procedimiento_endoscopico/unico_regresar_proc', ['paciente' => $paciente, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0]);
    }

    //Obtener cuadro clinico y Diagnostico biopsias

    public function obtener_cuad_diagnost(Request $request)
    {

        $id = $request["id_proced"];

        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $id)
            ->where('secuencia', 0)
            ->whereNotNull('cuadro_clinico')
            ->orderby('hcid', 'desc')
            ->first();

        $x_diagnosticos      = null;
        $evol_cuadro_clinico = null;
        $texto               = "";

        if (!is_null($evoluciones)) {

            $x_diagnosticos = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $evoluciones->hc_id_procedimiento)->groupBy('cie10')->get();

            $evol_cuadro_clinico = $evoluciones->cuadro_clinico;

        }

        if (!is_null($x_diagnosticos)) {

            $mas = true;
            foreach ($x_diagnosticos as $value) {

                $c3 = Cie_10_3::find($value->cie10);

                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }

                $c4 = Cie_10_4::find($value->cie10);

                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }

                if ($mas == true) {
                    $texto = $value->cie10 . ':' . $descripcion . '-' . $value->presuntivo_definitivo;
                    $mas   = false;

                } else {

                    $texto = $texto . '<br>' . $value->cie10 . ':' . $descripcion . '-' . $value->presuntivo_definitivo;
                }
            }

        }

        if (!is_null($id)) {

            $input_cuadclini_diagnostico = [
                'cuadro_clinico_bp' => $evol_cuadro_clinico,
                'diagnosticos_bp'   => $texto,

            ];

            hc_procedimientos::where('id', $id)->update($input_cuadclini_diagnostico);

        }

        return ['evol_cuadro_clinico' => $evol_cuadro_clinico, 'texto' => $texto];

    }

    public function actualiza_cuadclin_biopsia(Request $request)
    {

        $id_hc_prod = $request['id_hc_proc'];

        $cua_cli = $request['cua_cl'];

        if (!is_null($id_hc_prod)) {

            $input_cuadr = [

                'cuadro_clinico_bp' => $cua_cli,

            ];

            hc_procedimientos::where('id', $id_hc_prod)->update($input_cuadr);

            return "ok";

        }

    }

    public function actualiza_diagnostico_biopsia(Request $request)
    {

        $id_hc_prod = $request['id_hc_proc'];
        $diag       = $request['diag'];

        if (!is_null($id_hc_prod)) {

            $input_diag = [

                'diagnosticos_bp' => $diag,

            ];

            hc_procedimientos::where('id', $id_hc_prod)->update($input_diag);

            return "ok";

        }

    }

    public function eliminar_evolucion($id)
    {
        $evolucion         = Hc_Evolucion::find($id);
        $evolucion->estado = 0;
        $evolucion->save();
        return "ok";
    }
    public function eliminar_procedimiento($id)
    {
        $evolucion         = hc_procedimientos::find($id);
        $evolucion->estado = 0;
        $evolucion->save();
        return "ok";
    }

}
