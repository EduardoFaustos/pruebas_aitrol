<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Condiciones;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Establecimientos;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Sala;
use Sis_medico\User;

class CatorceavoPasoController extends Controller
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
    public function index(Request $request)
    {
        $condiciones = Ho_Condiciones::where('estado', '1')->get();
        $establecimientos = Ho_Establecimientos::where('estado', '1')->get();
        $sala = Sala::where('estado', '1')->where('id_hospital','5')->get();
        $doctores = User::where('id_tipo_usuario', '3')->get();
        $new = null;
        if ($request['ep'] != null) {
            $new = Ho_Traspaso_Sala008::where('id_solicitud', $request['ep'])->whereDate('fecha',date('Y-m-d'))->first();
        }
        return view('hospital.emergencia.catorceavopaso', ['condiciones' => $condiciones, 'new' => $new, 'establecimientos' => $establecimientos, 'sala' => $sala, 'doctores' => $doctores]);
    }
    public function store(Request $request)
    {
        $id = $request['id'];
        $verificar = Ho_Traspaso_Sala008::where('id_solicitud', $id)->first();
        $solicitud = Ho_Solicitud::find($id);
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        if (is_null($verificar)) {
            Ho_Traspaso_Sala008::create([
                'id_solicitud'      => $id,
                'id_sala'           => $request['id_sala'],
                'id_paciente'       => $solicitud->id_paciente,
                'id_condicion'      => $request['id_condicion'],
                'dias_reposo'       => $request['dias_reposo'],
                'servicio_reposo'   => $request['servicio_reposo'],
                'id_establecimiento' => $request['id_establecimiento'],
                'seccion'           => $request['nocheck'][0],
                'paso'               => $request['paso'],
                'causa'             => $request['causa'],
                'observaciones'     => $request['observaciones'],
                'fecha'             => $request['fecha'],
                'id_doctor'         => $request['id_doctor'],
                'estado'            => '1',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
            $sol_log = [
                'id_ho_solicitud'       => $solicitud->id,
                'estado_paso'           => '3',
                'id_agenda'             => $solicitud->id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
            ];
            $log_soli = Ho_Log_Solicitud::create($sol_log);
            $solicitud->estado_paso = $request->paso;
            $solicitud->id_usuariomod = $idusuario;
            $solicitud->save();
        } else {
            /* $verificar->url_imagen='1';
            $verificar->save(); */
            $eq = [
                'id_sala'            => $request['id_sala'],
                'id_condicion'       => $request['id_condicion'],
                'seccion'            => $request['nocheck'][0],
                'paso'               => $request['paso'],
                'dias_reposo'        => $request['dias_reposo'],
                'servicio_reposo'    => $request['servicio_reposo'],
                'id_establecimiento' => $request['id_establecimiento'],
                'causa'              => $request['causa'],
                'observaciones'      => $request['observaciones'],
                'fecha'              => $request['fecha'],
                'id_doctor'          => $request['id_doctor'],
                'estado'             => '1',
                'id_usuariomod'      => $idusuario,
            ];
            $verificar->update($eq);
        }
        if($request->paso==4){
            //when use select quirofano
            $request['cedula']=$solicitud->paciente->id;
            $request['id_seguro']=$solicitud->paciente->id_seguro;
            $this->agenda($request);
        } //if use step 3 only create traspaso

    }
    public function agenda(Request $request)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_doctor    = Auth::user()->id;
        $id_paciente = $request['cedula'];
        $paciente = paciente::find($id_paciente);
        $user = User::find($id_paciente);
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $input_agenda = [
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'id_paciente'     => $id_paciente,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '1',
            'estado_cita'     => '1',
            'id_empresa'      => '0992704152001',
            'espid'           => $espid,
            'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);


        $consulta_crear_new = [
            'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($consulta_crear_new);

        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '1',
            'estado_cita'     => '1',
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

        Log_agenda::create($input_log);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
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

        $input_child_pugh = [
            'id_hc_evolucion'       => $id_evolucion,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
        ];

        $id_child = hc_child_pugh::create($input_child_pugh);

        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];
        hc_receta::insert($input_hc_receta);
/* 
        $id_solicitud = $request['idsolicitud'];
        $arr_solicitud = [
            'id_paciente'           => $request['cedula'],
            'id_agenda'             => $id_agenda,
            'id_seguro'             => $request['id_seguro'],
            'fecha_ingreso'         => date('Y-m-d H:i:s'),
            'estado_paso'           => '2',
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $ho_solicitud = Ho_solicitud::insertGetId($arr_solicitud);

        $solicitud_log = [
            'id_ho_solicitud'       => $ho_solicitud,
            'estado_paso'           => '2',
            'id_agenda'             => $id_agenda,
            'fecha_ingreso'         => date('Y-m-d'),
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
        ];

        $log = Ho_Log_solicitud::create($solicitud_log); */
    }
}
