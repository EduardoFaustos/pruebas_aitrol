<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User; 
use Sis_medico\Bodega;
use Sis_medico\hc_procedimientos; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;   
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;  
use Sis_medico\Agenda;
use Sis_medico\Log_Agenda;
use Sis_medico\Hc_Log;
use Sis_medico\Historiaclinica;
use Sis_medico\Seguro;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\hc_receta;
use Sis_medico\Paciente;
use Sis_medico\Cortesia_Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Opcion_Usuario;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_child_pugh;
use Sis_medico\Procedimiento;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Principio_Activo;
use Sis_medico\Cie_10_3;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_4;
use Sis_medico\User_espe;
use Response;

class VisitaController extends Controller
{
    
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
    
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
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

    
    public function crear_visita($id, $ag){
      
        $paciente = Paciente::find($id);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];

        $id_doctor = Auth::user()->id; 

        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if(!is_null($especialidad)){
            $espid = $especialidad->espid;
        }else{
            $espid = '4';
        }
         
     
        //Version HC3 
        $input_agenda = [
            
            //Visita Omni Hospital
            'fechaini' => Date('Y-m-d H:i:s'),//Campo Ingreso de seleccion Hc3
            'fechafin' => Date('Y-m-d H:i:s'),
            'id_paciente' => $id,
            'id_doctor1' => $id_doctor,
            'procedencia' =>'OMNI HOSPITAL',//  ONNI HOSPITAL $request['procedencia'] Campo Ubicacion Hc3 de Ingreso  
            'sala_hospital' => '',// $request['sala_hospital']Campo Sala Hc3 
            'proc_consul' => '4',//3:hospitalizados, 4:evoluciones'-->3
            'estado_cita' => '4',//'0: por confirmar,  1: confirmada, 2: reagendado, 3:suspendido, 4:admisionado'-->0
            'observaciones' => '',//CAMPO OBSERVACION HC3 de Ingreso
            'id_seguro' => $paciente->id_seguro,//Campo Tipo Seguro HC3 Seleccion
            'estado' => '4',//Campo Estado HC3 -->1
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $id_doctor,
            'id_usuariomod' => $id_doctor,
            'cortesia' => 'NO',
            //Fin Visita 
           
            //HC4 ADICIONALES
            'id_empresa' => '0992704152001',
            'espid' => $espid,
            
        ];

        $id_agenda = agenda::insertGetId($input_agenda);
        // Fin Version HC3

        
        //Version HC4
        if($ag=='no'){
            $ag = $id_agenda;
        }

           $visita_crear_new = [
            'anterior' => 'VISITA: -> El Dr. creo nueva visita -> id_agenda: '.$id_agenda,
            'nuevo' => 'VISITA: -> El Dr. creo nueva visita -> id_agenda: '.$id_agenda,
            'id_paciente' => $id,
            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Hc_Log::create($visita_crear_new);
        //Fin Version HC4

        //Version HC3 
        $input_log = [
            'id_agenda' => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita' => '0',
            'fechaini' => Date('Y-m-d H:i:s'),//Campo Ingreso HC3
            'fechafin' => Date('Y-m-d H:i:s'),//Campo Ingreso HC3
            'estado' => '4',//Version Hc3 -->1
            'observaciones' => 'VISITA CREADA POR EL DOCTOR',//Campo Observacion Hc3
            'id_doctor1' => $id_doctor,
            'descripcion' => 'HOSPITALIZADO',//'VISITA CREADA POR EL DOCTOR',
            'descripcion2' => 'INGRESO',
            'descripcion3' => '',
            /*
            'descripcion' => 'HOSPITALIZADO',
            'descripcion2' => 'INGRESO',
            'descripcion3' => '',*/

             /* 'campos' => "UBICACION:".$request['procedencia']." SALA:".$request['sala_hospital']." SEGURO:".$request['id_seguro']."-".$seguro->nombre,*/
          

            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        $idusuario = $id_doctor;

        Log_agenda::create($input_log);
        // Fin Version HC3

        
        //Version HC4 
        $input_historia = [
            
            'parentesco' => $paciente->parentesco,
            'id_usuario' => $paciente->id_usuario,
            'id_agenda' => $id_agenda,
            'id_paciente' => $id,
            'id_seguro' => $paciente->id_seguro,
            
            'id_doctor1' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $id_doctor,
            'ip_creacion' => $ip_cliente,
            
        ]; 

        $id_procedimiento_completo = '40';

        $id_historia=Historiaclinica::insertGetId($input_historia); 

        $input_hc_procedimiento = [
            'id_hc' => $id_historia,
            'id_seguro' => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            
        ]; 

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $input_hc_evolucion = [
            'hc_id_procedimiento' => $id_hc_procedimiento,
            'hcid' => $id_historia,
            'secuencia' => '0',
            'fecha_ingreso' => ' ', 
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,                    
            'id_usuariocrea' => $idusuario,
            'ip_creacion' => $ip_cliente,
            
        ]; 
        $id_evolucion = Hc_Evolucion::insertGetId($input_hc_evolucion);
        $input_hc_receta = [
            'id_hc' => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
             
        ]; 
        hc_receta::insert($input_hc_receta);
        //Fin Version HC4
       
        //return redirect()->route('paciente.visita',['id_paciente' => $id]);
        return redirect()->route('paciente.consulta',['id_paciente' => $id]);
    }   

}
