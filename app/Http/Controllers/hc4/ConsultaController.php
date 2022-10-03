<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Medicina;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Seguro;
use Sis_medico\User;

class ConsultaController extends Controller
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

        $paciente = Paciente::find($id_paciente);

        //Obtengo el id_doctor visita
        $id_doctor_visita = Auth::user()->id;

        //dd($paciente->agenda);
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->leftjoin('especialidad as e', 'e.id', 'a.espid')
            ->join('hc_evolucion as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.hc_id_procedimiento')
        //->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->where('hc_p.id_procedimiento_completo', '40')
            ->where('a.estado_cita', '<>', '3')
            ->select('h.*', 'a.*', 'hc_proto.*', 'hc_p.*', 'e.nombre as espe_nombre')
            ->OrderBy('h.created_at', 'desc')
            ->paginate(7);

        //dd($pro_completo_0);

        //dd($pro_completo_0.' -- '.$pro_final_0);

        $doctores     = User::where('id_tipo_usuario', '3')->where('estado', '1')->OrderBy('apellido1')->get();
        $seguros      = Seguro::where('inactivo', '1')->get();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();
        if (isset($_REQUEST['page'])) {
            return view('hc4/consulta/paginador', ['paciente' => $paciente, 'procedimientos2' => $pro_completo_0, 'doctores' => $doctores, 'seguros' => $seguros, 'alergiasxpac' => $alergiasxpac, 'id_doctor_visita' => $id_doctor_visita]);
        }
        return view('hc4/consulta/index', ['paciente' => $paciente, 'procedimientos2' => $pro_completo_0, 'doctores' => $doctores, 'seguros' => $seguros, 'alergiasxpac' => $alergiasxpac, 'id_doctor_visita' => $id_doctor_visita]);
    }

    public function crear_evolucion($id, $ag)
    {
        //dd($id);

        //1. crear agenda y log proc_consul='4' estado='2'
        $paciente = Paciente::find($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_doctor = Auth::user()->id;

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $input_agenda = [
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'id_paciente'     => $id,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '4',
            'estado_cita'     => '4',
            'id_empresa'      => '0992704152001',
            'espid'           => $espid,
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '4',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);

        if ($ag == 'no') {
            $ag = $id_agenda;
        }

        $consulta_crear_new = [
            'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'id_paciente'     => $id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($consulta_crear_new);

        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'estado'          => '4',
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',

            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        $idusuario = $id_doctor;

        Log_agenda::create($input_log);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id,
            'id_seguro'       => $paciente->id_seguro,

            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_procedimiento_completo = '40';

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_hc_procedimiento = [
            'id_hc'                     => $id_historia,
            'id_seguro'                 => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $input_hc_evolucion = [
            'hc_id_procedimiento' => $id_hc_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => '0',
            'fecha_ingreso'       => ' ',
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,

        ];
        $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);
        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];
        hc_receta::insert($input_hc_receta);

        return redirect()->route('paciente.consulta', ['id_paciente' => $id]);
    }

    public function actualizar_consulta(Request $request)
    {

        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $consulta_datos = hc_procedimientos::find($request['id_hc_procedimiento']);

        if (!is_null($consulta_datos)) {
            $consulta_datos_new = [
                'anterior'         => 'CONSULTA-> DATOS_GENERALES Medico_Examinador: ' . $consulta_datos->id_doctor_examinador . '  Seguro: ' . $consulta_datos->id_seguro . '   Observaciones: ' . $consulta_datos->observaciones,
                'nuevo'            => 'CONSULTA-> DATOS_GENERALES Medico_Examinador: ' . $request["id_doctor_examinador"] . '  Seguro: ' . $request["id_seguro"] . '  Cortesia: ' . $request['consulta_cortesia_paciente'] . '   Observaciones: ' . $request["observaciones"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_datos_new);
        }

        $hist_clinica = Historiaclinica::find($request['hcid']);

        if (!is_null($hist_clinica)) {
            $consulta_preparacion_new = [
                'anterior'         => 'CONSULTA->PREPARACION-> P.arterial: ' . $hist_clinica->presion . '  Pulso: ' . $hist_clinica->pulso . '    Temperatura: ' . $hist_clinica->temperatura . '   SaO2: ' . $hist_clinica->o2 . '   Estatura: ' . $hist_clinica->altura . '   Peso: ' . $hist_clinica->peso . '   Perimetro_Abdominal: ' . $hist_clinica->perimetro . '   Examenes_Realizar: ' . $hist_clinica->examenes_realizar,
                'nuevo'            => 'CONSULTA->PREPARACION-> P.arterial: ' . $request["presion"] . '  Pulso: ' . $request["pulso"] . '    Temperatura: ' . $request["temperatura"] . '   SaO2: ' . $request["o2"] . '   Estatura: ' . $request["estatura"] . '   Peso: ' . $request["peso"] . '   Perimetro_Abdominal: ' . $request["perimetro"] . '   Examenes_Realizar: ' . $request["examenes_realizar"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_preparacion_new);
        }

        $receta = hc_receta::where('id_hc', $request['hcid'])->OrderBy('created_at', 'desc')->first();

        $child_pugh = hc_child_pugh::find($request['id_child_pugh']);

        if (!is_null($child_pugh)) {
            $consulta_child_pugh_new = [
                'anterior'         => 'CONSULTA->Child_Pugh-> Ascitis: ' . $child_pugh->ascitis . '  Encefalopatia: ' . $child_pugh->encefalopatia . '    Albumina: ' . $child_pugh->albumina . '   Bilirrubina: ' . $child_pugh->bilirrubina . '   Inr: ' . $child_pugh->inr . '   Examen_Fisico: ' . $child_pugh->examen_fisico,
                'nuevo'            => 'CONSULTA->Child_Pugh-> Ascitis: ' . $request["ascitis"] . '  Encefalopatia: ' . $request["encefalopatia"] . '    Albumina: ' . $request["albumina"] . '   Bilirrubina: ' . $request["bilirrubina"] . '   Inr: ' . $request["inr"] . '   Examen_Fisico: ' . $request["examen_fisico"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_child_pugh_new);
        }

        $consulta_evol = Hc_Evolucion::find($request['id_evolucion']);

        if (!is_null($consulta_evol)) {
            $consulta_evolucion_new = [
                'anterior'         => 'CONSULTA-> Motivo: ' . $consulta_evol->motivo . '  Evolucion: ' . $consulta_evol->cuadro_clinico . ' Indicacion: ' . $consulta_evol->indicaciones . ' Resultado: ' . $consulta_evol->resultado,
                'nuevo'            => 'CONSULTA-> Motivo: ' . $request["motivo"] . '  Evolucion: ' . $request["historia_clinica"] . ' Indicacion: ' . $request["indicacion"] . ' Resultado: ' . $request["resultado_exam"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_evolucion_new);
        }

        $hist_recetas = DB::table('hc_receta as r')->where('r.id_hc', $request['hcid'])->first();

        if (!is_null($hist_recetas)) {
            $receta_new = [
                'anterior'         => 'CONSULTA-> RECETA -> Rp: ' . $hist_recetas->rp . ' Prescripcion: ' . $hist_recetas->prescripcion,
                'nuevo'            => 'CONSULTA-> RECETA -> Rp:' . $request["rp"] . ' Prescripcion:' . $request["prescripcion"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($receta_new);
        }

        $id = $request['hcid'];

        $input1 = [
            'id_doctor1'        => $request['id_doctor_examinador'],
            'presion'           => $request["presion"],
            'pulso'             => $request["pulso"],
            'temperatura'       => $request["temperatura"],
            'o2'                => $request["o2"],
            'altura'            => $request["estatura"],
            'peso'              => $request["peso"],
            'perimetro'         => $request["perimetro"],
            'examenes_realizar' => $request["examenes_realizar"],
            'ip_modificacion'   => $ip_cliente,
            'id_usuariomod'     => $idusuario,
        ];
        Historiaclinica::where('hcid', $id)
            ->update($input1);

        $input_evo = [
            'motivo'          => $request["motivo"],
            'cuadro_clinico'  => $request["historia_clinica"],
            //'indicacion' => $request["indicacion"],
            'resultado'       => $request["resultado_exam"],
            'fecha_doctor'    => $request["fecha_doctor"],
            'indicaciones'    => $request["indicacion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_evolucion = $request['id_evolucion'];
        Hc_Evolucion::where('id', $id_evolucion)
            ->update($input_evo);

        $input_child = [
            'ascitis'         => $request["ascitis"],
            'encefalopatia'   => $request["encefalopatia"],
            'albumina'        => $request["albumina"],
            'bilirrubina'     => $request["bilirrubina"],
            'inr'             => $request["inr"],
            'examen_fisico'   => $request["examen_fisico"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_child = $request['id_child_pugh'];
        hc_child_pugh::where('id', $id_child)
            ->update($input_child);

        $input_hc_receta = [
            'rp'              => $request["rp"],
            'prescripcion'    => $request["prescripcion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_receta = $request['id_receta'];
        hc_receta::where('id', $id_receta)->update($input_hc_receta);

        if (($request['id_doctor_examinador'] == '9666666666') || ($request['id_doctor_examinador'] == 'GASTRO')) {
            $input_hc_procedimiento = [
                'id_doctor_examinador' => $idusuario,
                'id_seguro'            => $request["id_seguro"],
                'observaciones'        => $request["observaciones"],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];
        } else {
            $input_hc_procedimiento = [
                'id_doctor_examinador' => $request["id_doctor_examinador"],
                'id_seguro'            => $request["id_seguro"],
                'observaciones'        => $request["observaciones"],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];
        }

        $id_hc_procedimiento = $request['id_hc_procedimiento'];
        hc_procedimientos::where('id', $id_hc_procedimiento)
            ->update($input_hc_procedimiento);

        $agenda_cortesia = agenda::where('id', $request['id_agenda'])->first();
        //dd ($agenda_cortesia);

        //Agregar Visita OMNI ubicacion_omni
        $ubicacion_omni = $request['ubicacion_omni'];
        //Agregar Visita OMNI sala_omni
        $sala_omni = $request['sala'];
        //Agregar Visita OMNI estado_omni
        //dd($sala_omni);
        $estado_omni = $request['estado_visita'];

        //dd($estado_omni);

        if (!is_null($agenda_cortesia)) {

            //Agregar Visita OMNI
            //if ((!is_null($ubicacion_omni)) && (!is_null($sala_omni)) && (!is_null($estado_omni))) {
            if ((!is_null($ubicacion_omni)) && (!is_null($estado_omni))) {

                $input = [
                    'cortesia'      => $request['consulta_cortesia_paciente'],
                    'procedencia'   => $ubicacion_omni,
                    'sala_hospital' => $sala_omni,
                    'estado_cita'   => $estado_omni,
                ];
                $agenda_cortesia->update($input);

            } else {

                $input = [
                    'cortesia' => $request['consulta_cortesia_paciente'],
                    //'ip_modificacion' => $ip_cliente,
                    //'id_usuariomod' => $idusuario
                ];
                $agenda_cortesia->update($input);
            }

        }

        return "ok";

    }

    public function actualizar_receta(Request $request)
    {

        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $receta       = hc_receta::where('id_hc', $request['hcid'])->OrderBy('created_at', 'desc')->first();
        $hist_recetas = DB::table('hc_receta as r')->where('r.id_hc', $request['hcid'])->first();
        if (!is_null($hist_recetas)) {
            $receta_new = [
                'anterior'         => 'CONSULTA-> RECETA -> Rp: ' . $hist_recetas->rp . ' Prescripcion: ' . $hist_recetas->prescripcion,
                'nuevo'            => 'CONSULTA-> RECETA -> Rp:' . $request["rp"] . ' Prescripcion:' . $request["prescripcion"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($receta_new);
        }

        $input_hc_receta = [
            'rp'              => $request["rp"],
            'prescripcion'    => $request["prescripcion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_receta = $request['id_receta'];
        hc_receta::where('id', $id_receta)->update($input_hc_receta);

        return "ok";

    }

    public function agregar_cie10(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $request['hc_id_procedimiento'])->get();

        //dd($cie10);
        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {

                $diagnostico_new = [
                    'anterior'         => 'CONSULTA -> Diagnostico: ' . $value->cie10,
                    'nuevo'            => 'CONSULTA -> Diagnostico: ' . $request['codigo'],
                    'hc_id'            => $value->hcid,
                    'id_paciente'      => $request['id_paciente'],
                    'id_procedimiento' => $value->hc_id_procedimiento,
                    'id_usuariomod'    => $idusuario,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'ip_creacion'      => $ip_cliente,
                ];
                Hc_Log::create($diagnostico_new);

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

    //Agregar Visita HC4
    public function crear_visita($id, $ag)
    {

        $paciente = Paciente::find($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_doctor = Auth::user()->id;

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        //Version HC3
        $input_agenda = [

            //Visita Omni Hospital
            'fechaini'        => Date('Y-m-d H:i:s'), //Campo Ingreso de seleccion Hc3
            'fechafin'        => Date('Y-m-d H:i:s'),
            'id_paciente'     => $id,
            'id_doctor1'      => $id_doctor,
            'procedencia'     => 'OMNI HOSPITAL', //  ONNI HOSPITAL $request['procedencia'] Campo Ubicacion Hc3 de Ingreso
            'sala_hospital'   => '', // $request['sala_hospital']Campo Sala Hc3
            'omni'            => 'OM',
            'proc_consul'     => '4', //3:hospitalizados, 4:evoluciones'-->3
            'estado_cita'     => '4', //'0: por confirmar,  1: confirmada, 2: reagendado, 3:suspendido, 4:admisionado'-->0
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR', //CAMPO OBSERVACION HC3 de Ingreso
            'est_amb_hos'     => '1',
            'id_seguro'       => $paciente->id_seguro, //Campo Tipo Seguro HC3 Seleccion
            'estado'          => '4', //Campo Estado HC3 -->1
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
            'cortesia'        => 'NO',
            //Fin Visita

            //HC4 ADICIONALES
            'id_empresa'      => '0992704152001',
            'espid'           => $espid,

        ];

        $id_agenda = agenda::insertGetId($input_agenda);
        // Fin Version HC3

        //Version HC4
        if ($ag == 'no') {
            $ag = $id_agenda;
        }

        $visita_crear_new = [
            'anterior'        => 'VISITA: -> El Dr. creo nueva visita -> id_agenda: ' . $id_agenda,
            'nuevo'           => 'VISITA: -> El Dr. creo nueva visita -> id_agenda: ' . $id_agenda,
            'id_paciente'     => $id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($visita_crear_new);
        //Fin Version HC4

        //Version HC3
        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => Date('Y-m-d H:i:s'), //Campo Ingreso HC3
            'fechafin'        => Date('Y-m-d H:i:s'), //Campo Ingreso HC3
            'estado'          => '4', //Version Hc3 -->1
            'observaciones'   => 'VISITA CREADA POR EL DOCTOR', //Campo Observacion Hc3
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'HOSPITALIZADO', //'VISITA CREADA POR EL DOCTOR',
            'descripcion2'    => 'INGRESO',
            'descripcion3'    => '',
            /*
            'descripcion' => 'HOSPITALIZADO',
            'descripcion2' => 'INGRESO',
            'descripcion3' => '',*/

            /* 'campos' => "UBICACION:".$request['procedencia']." SALA:".$request['sala_hospital']." SEGURO:".$request['id_seguro']."-".$seguro->nombre,*/

            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        $idusuario = $id_doctor;

        Log_agenda::create($input_log);
        // Fin Version HC3

        //Version HC4
        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id,
            'id_seguro'       => $paciente->id_seguro,

            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_procedimiento_completo = '40';

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_hc_procedimiento = [
            'id_hc'                     => $id_historia,
            'id_seguro'                 => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $input_hc_evolucion = [
            'hc_id_procedimiento' => $id_hc_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => '0',
            'fecha_ingreso'       => ' ',
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,

        ];
        $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);
        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];
        hc_receta::insert($input_hc_receta);
        //Fin Version HC4

        //return redirect()->route('paciente.visita',['id_paciente' => $id]);
        return redirect()->route('paciente.consulta', ['id_paciente' => $id]);
    }

    public function vademecun(Request $request)
    {
        $variable = $request['variable'];
        $texto    = '%' . $request['texto'] . '%';
        $nombre   = Medicina::WhereRaw("CONCAT(nombre, ' ', presentacion) LIKE '" . $texto . "'")->where('estado', 1)->orderBy('nombre', 'asc')->get();
        return view('hc4/consulta/vademecum', ['nombre' => $nombre, 'variable' => $variable, 'texto' => $request['texto']]);
    }

    public function vademecun2(Request $request)
    {
        $texto  = '%' . $request['texto'] . '%';
        $nombre = Medicina::WhereRaw("CONCAT(nombre, ' ', presentacion) LIKE '" . $texto . "'")->where('estado', 1)->orderBy('nombre', 'asc')->get();
        return view('hc4/consulta/vademecum_interno', ['nombre' => $nombre]);
    }

    public function carga_hora_inicio($hcid)
    {
        $hc_procedimientos = hc_procedimientos::where('id_hc', $hcid)->where('estado', '1')->first();
        //dd($hc_procedimientos);
        $arr = [
            'hora_inicio' => date('H:i:s'),
        ];
        //dd($arr);

        $hc_procedimientos->update($arr);

        return "ok";
    }

    public function carga_hora_fin($hcid)
    {
        $hc_procedimientos = hc_procedimientos::where('id_hc', $hcid)->where('estado', '1')->first();
        //dd($hc_procedimientos);
        $arr2 = [
            'hora_fin' => date('H:i:s'),
        ];
        //dd($arr);

        $hc_procedimientos->update($arr2);

        return "ok";
    }

}
