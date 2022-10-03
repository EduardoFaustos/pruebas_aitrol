<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\Agenda;
use Sis_medico\Log_Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Historiaclinica;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Pentax;
use Sis_medico\Pentax_log;
use Sis_medico\PentaxProc;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\hc_child_pugh;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_cpre_eco;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Hc_protocolo_training;
use Response;
use Sis_medico\Examen_Orden;
use Sis_medico\Log_usuario;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Horario_Doctor;
use Sis_medico\Excepcion_Horario;
use Sis_medico\CallCenter;
use Sis_medico\CallCenter_Control;
use Mail;
use Excel;


class ReporteSubirController extends Controller
{
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request){
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        /*
        if($fecha == 0){
            $fecha_2 = date('Y-m-d');   
        }else{
            $fecha_2 = date('Y-m-d', $fecha);
        }
        if($fechafin ==0){
            $fechafin1= date('Y-m-d');
        }else{
            $fechafin1= date('Y-m-d', $fechafin);
        }

        
        $variable1 = Agenda::where("estado_cita","0")->where('proc_consul', '<', 2)->whereBetween('fechaini', [$fecha_2.' 00:00:00', $fechafin1.' 23:59:59'])->get();*/
        
        $callcontroler= CallCenter_Control::paginate(20);
        //dd($callcontroler);
        return view('reportesubir/index',['callcontroler'=>$callcontroler]);
   }
   public function detalle($id){
       $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        $consultas= CallCenter::where('id_callcenter', '=', $id)->get();
         //dd($consulta_id);
        return view('reportesubir/detalle',['consultas'=>$consultas,'id'=>$id]);
   }


   public function vistasubida(Request $request){
    //return "hola";
       $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        //$callcontroler= CallCenter_Control::all();
        //dd($id);
        $nombre_original=$request['archivo']->getClientOriginalName();
        $extension=$request['archivo']->getClientOriginalExtension();
        $nuevo_nombre=$nombre_original.$extension;       
        $r1=Storage::disk('hc_agenda')->put($nuevo_nombre,  \File::get($request['archivo']) );
        $id_agenda= $request['id_agenda'];
        $url = Storage::url('/app/hc_agenda/'.$nuevo_nombre);
        ini_set('max_execution_time', 100);
        $contador = 0;
        Excel::filter('chunk')->load($url)->chunk(250, function($reader) use($contador) {
  
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;   
            $contasis=0;
            $noasis=0;
            $noresp=0;
            $suspendido=0;
            $contnoprocesado=0;
           //No se como funciona pero lo hace

             $input = [    
                
                'total_aceptado' => $contasis,
                'total_rechazado' => $noasis,
                'total_noresp' => $noresp,
                'total_noprocesado'=>$contnoprocesado,
                'id_usuariocrea'=>$idusuario, 
                //falta ip                              

            ]; 
            $id_callcenter = CallCenter_Control::insertGetId($input);

            foreach ($reader as $book) {
                //dd($book);
                $id_agenda = $book['cedula'];
                $telefono= trim($book['telefono']);
                $fecha= $book['fecha_y_hora'];
                $agenda = Agenda::find($id_agenda);
                
                if(!is_null($id_agenda)){

                    $nuevo = explode(".", $id_agenda);
                    $id_agenda = $nuevo[0];
                    //dd($id_agenda);
                    //dd($book['asistencia']);
                    if(($book['asistencia']) =="ASISTO"){
                        //dd("ASISTO");

                        if(($agenda->estado_cita)==0){ //por confirmar
                                $estado= 1;
                                $descripcion="CONFIRMO LA CITA SISTEMA AUTOMATICO";
                                $input_agenda = [
                                    'estado' => '1',
                                    'estado_cita' => '1',
                                    'id_usuarioconfirma' => $idusuario,
                                    'id_usuariomod' => $idusuario,
                                    'ip_modificacion' => $ip_cliente,
                                ];
                               
                                //dd($book['asistencia'],$agenda);
                                //crea un registro en la master que confirmó la cita

                                Log_agenda::create([
                                    'id_agenda' => $agenda->id,
                                    'estado_cita_ant' => $agenda->estado_cita,
                                    'estado_cita' => '1',
                                    'descripcion' => $descripcion,
                                    'descripcion2' => '',
                                    'descripcion3' => '',
                                    'observaciones' => $descripcion,
                                    'id_usuarioconfirma' => $agenda->id_usuarioconfirma,
                                    'id_usuariomod' => $idusuario,
                                    'id_usuariocrea' => $idusuario,
                                    'ip_modificacion' => $ip_cliente,
                                    'ip_creacion'=>$ip_cliente,

                                ]); 
                                $agenda->update($input_agenda);
                                $contasis++;
                                
                        }else{
                            $contnoprocesado++;
                            $estado= 3; //no procesó
                        }                                              
                    }elseif(($book['asistencia']) =="NO ASISTO"){
                        
                        if(($agenda->estado_cita)==0){
                            $estado= 0;
                            
                            $agendas = Agenda::findOrFail($id_agenda);
                            $ip_cliente= $_SERVER["REMOTE_ADDR"];
                            $idusuario = Auth::user()->id;
                            date_default_timezone_set('America/Guayaquil');
                            $descripcion="SISTEMA AUTOMATICO/REAGENDAR PACIENTE";
                            $input = [
                                'estado' => '-1',
                                'estado_cita' => '2',
                                'observaciones' => $descripcion,
                                'id_usuariomod' => $idusuario,
                                'ip_modificacion' => $ip_cliente,
                            ];
                            Log_agenda::create([
                                'id_agenda' => $agendas->id,
                                'estado_cita_ant' => $agendas->estado_cita, 
                                'estado_ant' => $agendas->estado,
                                'observaciones_ant' => $agendas->observaciones,
                                'estado_cita' => '2',
                                'estado' => '-1',
                                'descripcion' => 'REAGENDAR PACIENTE',
                                'id_usuarioconfirma' => $agendas->id_usuarioconfirma,
                                'id_usuariomod' => $idusuario,
                                'id_usuariocrea' => $idusuario,
                                'ip_modificacion' => $ip_cliente,
                                'ip_creacion' => $ip_cliente,           
                            ]); 
                            $agendas::where('id', $id_agenda)
                            ->update($input);
                            $noasis++;

                        }else{
                            $contnoprocesado++;
                            $estado= 3;
                        }
                    }elseif (($book['asistencia']) ==null) {
                        $estado= 2;
                        $noresp++;
                    }
                        $input = [ 
                                'total_aceptado' =>$contasis,
                                'total_rechazado' =>$noasis,
                                'total_noresp' =>$noresp,
                                'total_noprocesado'=>$contnoprocesado,
                                'id_usuariocrea'=>$idusuario,
                        ];
                        $callcontroler = CallCenter_Control::find($id_callcenter);
                        $callcontroler->update($input);
                       
                            CallCenter::create([
                                'estado'=>$estado,
                                //el estado de agenda actual
                                'estado_agenda'=>$agenda->estado_cita,  
                                'id_usuariocrea'=>$idusuario,
                                'id_agenda'=>$agenda->id,
                                'id_callcenter'=>$id_callcenter,
                                'telefono'=>$telefono,
                                'fecha'=>$fecha,
                                'id_usuarioactualiza'=>$idusuario,
                                'ip_modificacion'=>$ip_cliente,
                                'ip_creacion'=>$ip_cliente,
                                'estado_correo'=>'0',
                                
                            ]);
                        //dd($contasis);                    
                }


            }             //ojo aqui cierra el foreach        

        });
      
        return back();
   }
    public function vistareporte()
    {
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        return view ('reportesubir/vistareporte');
    }
    public function correo($id,$id_detalle)
    {
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
                    $idusuario = Auth::user()->id;
                    $fausto= Agenda::find($id);
                    //dd($fausto);
                    $tipo = $fausto->proc_consul;
                    $inicio = $fausto->fechaini;
                    $doctor = DB::table('users')->where('id', '=', $fausto->id_doctor1)->get();
                    $sala =  DB::table('sala')->where('id', '=', $fausto->id_sala)->get();
                    $id_paciente = $fausto->id_paciente;
                    $especialidad =  DB::table('especialidad')->where('id', '=', $fausto->espid)->get();
                    $especialidad_nombre = $especialidad[0]->nombre;
                    $paciente2 = DB::table('paciente')->where('id', '=', $id_paciente)->get();
                    $usuario =  DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
                    $correo = $usuario[0]->email;
                    $nombre_paciente =  $paciente2[0]->nombre1." ";
                    if($paciente2[0]->nombre2 != '(N/A)')
                    {
                        $nombre_paciente = $nombre_paciente.$paciente2[0]->nombre2." ";
                    }
                    $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido1." ";
                    if($paciente2[0]->apellido2 != '(N/A)')
                    {
                        $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido2." ";
                    }
                    $nombre_doctor =  $doctor[0]->nombre1." ";
                    if($doctor[0]->nombre2 != '(N/A)')
                    {
                        $nombre_doctor = $nombre_doctor.$doctor[0]->nombre2." ";
                    }
                    $nombre_doctor = $nombre_doctor.$doctor[0]->apellido1." ";
                    if($doctor[0]->apellido2 != '(N/A)')
                    {
                        $nombre_doctor = $nombre_doctor.$doctor[0]->apellido2." ";
                    }
                    $cnombre = $sala[0]->nombre_sala;
                    $hospital =  DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
                    $hnombre = $hospital[0]->nombre_hospital;

                    $hdireccion = $hospital[0]->direccion;
                    //dd($tipo);*/
                        
                       if($tipo == 1){

                                $procedimiento_enviar = null;

                                $procedimiento_de_agenda = $fausto->id_procedimiento;
                                $procedimiento_a = DB::table('procedimiento')->where('id', '=', $procedimiento_de_agenda)->get();
                                $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                                
                                $procedimientos =  DB::table('agenda_procedimiento')->where('id_agenda', '=', $id)->get();    
                                foreach ($procedimientos as $value){
                                    $procedimiento_a = DB::table('procedimiento')->where('id', '=', $value->id_procedimiento)->get();

                                    $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                                }

                                $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);
                                
                                $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $fausto->fechaini, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);
                                Mail::send('mails.procedimiento', $avanza, function($msj)  use ($correo){
                                    $msj->subject('Reservación de procedimiento médico IECED');
                                    $msj->to($correo);
                                    $msj->bcc('torbi10@hotmail.com');      
                                });
                          }
                       if($tipo == 0){
                            
                                $avanza = array("nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $inicio, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);

                                Mail::send('mails.consulta', $avanza, function($msj)  use ($correo){
                                    $msj->subject('Reservacion de cita médica IECED');
                                    $msj->to($correo);
                                    $msj->bcc('torbi10@hotmail.com');      
                                });
                            
                         }     
                      $input_detalle = [ 
                        
                                'estado_correo'=>'1',
                            
                        ];

                        $detalle = CallCenter::find($id_detalle);
                        //dd($detalle);
                        $detalle->update($input_detalle);
                         return back();

    }
    public function correotodos($id,$id_agenda){
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        $consultas= DB::table('callcenter_subir')->where('id_callcenter', '=', $id)->get();
        //dd($consultas);
        foreach ($consultas as $value) {
            
            if(($value->estado_correo)==0 && ($value->estado)==1){
                //dd($value);
                    $idusuario = Auth::user()->id;
                    //dd($idusuario);
                    $fausto= Agenda::find($id_agenda);
                    //dd($fausto);
                    $tipo = $fausto->proc_consul;
                    $inicio = $fausto->fechaini;
                    $doctor = DB::table('users')->where('id', '=', $fausto->id_doctor1)->get();
                    $sala =  DB::table('sala')->where('id', '=', $fausto->id_sala)->get();
                    $id_paciente = $fausto->id_paciente;
                    $especialidad =  DB::table('especialidad')->where('id', '=', $fausto->espid)->get();
                    $especialidad_nombre = $especialidad[0]->nombre;
                    $paciente2 = DB::table('paciente')->where('id', '=', $id_paciente)->get();
                    $usuario =  DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
                    $correo = $usuario[0]->email;
                    $nombre_paciente =  $paciente2[0]->nombre1." ";
                    if($paciente2[0]->nombre2 != '(N/A)')
                    {
                        $nombre_paciente = $nombre_paciente.$paciente2[0]->nombre2." ";
                    }
                    $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido1." ";
                    if($paciente2[0]->apellido2 != '(N/A)')
                    {
                        $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido2." ";
                    }

                    $nombre_doctor =  $doctor[0]->nombre1." ";
                    if($doctor[0]->nombre2 != '(N/A)')
                    {
                        $nombre_doctor = $nombre_doctor.$doctor[0]->nombre2." ";
                    }
                    $nombre_doctor = $nombre_doctor.$doctor[0]->apellido1." ";
                    if($doctor[0]->apellido2 != '(N/A)')
                    {
                        $nombre_doctor = $nombre_doctor.$doctor[0]->apellido2." ";
                    }
                    $cnombre = $sala[0]->nombre_sala;
                    $hospital =  DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
                    $hnombre = $hospital[0]->nombre_hospital;

                    $hdireccion = $hospital[0]->direccion;
                    //dd($tipo);*/
                        
                       if($tipo == 1){

                                $procedimiento_enviar = null;

                                $procedimiento_de_agenda = $fausto->id_procedimiento;
                                $procedimiento_a = DB::table('procedimiento')->where('id', '=', $procedimiento_de_agenda)->get();
                                $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                                
                                $procedimientos =  DB::table('agenda_procedimiento')->where('id_agenda', '=', $id)->get();    
                                foreach ($procedimientos as $value){
                                    $procedimiento_a = DB::table('procedimiento')->where('id', '=', $value->id_procedimiento)->get();

                                    $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                                }

                                $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);
                                
                                $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $fausto->fechaini, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);
                                Mail::send('mails.procedimiento', $avanza, function($msj)  use ($correo){
                                    $msj->subject('Reservación de procedimiento médico IECED');
                                    $msj->to($correo);
                                    $msj->bcc('torbi10@hotmail.com');      
                                });
                          }
                       if($tipo == 0){
                            
                                $avanza = array("nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $inicio, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);

                                Mail::send('mails.consulta', $avanza, function($msj)  use ($correo){
                                    $msj->subject('Reservacion de cita médica IECED');
                                    $msj->to($correo);
                                    $msj->bcc('torbi10@hotmail.com');      
                                });
                            
                         }
                         $input_detalle = [ 
                        
                                'estado_correo'=>'1',
                            
                        ];

                        $detalle = CallCenter::find($id);
                        //dd($detalle);
                        $detalle->update($input_detalle);

            }
            
        }
        return back();

    }

    public function reagendar($id_agenda){
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        return back();

    }

    
    
   
   
}