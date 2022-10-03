<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;

use Sis_medico\Examen_Resultado;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Empresa;
use Sis_medico\Protocolo;
use Sis_medico\Forma_de_pago;
use Sis_medico\Convenio;
use Sis_medico\Examen_Sub_Resultado;
use Sis_medico\Examen_Detalle_Costo;
use Sis_medico\Opcion_Usuario;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use laravel\laravel;
use Carbon\Carbon;




class OrdenController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion){ //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          
          return true;
        
        }

    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 4, 5)) == false ){
          return true;
        }
        

    }
    private function rol_sis(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1)) == false ){
          return true;
        }
        

    }
    private function rol_supervision(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 12,11, 3)) == false && $id_auth!='1307189140'){
          return true;
        }
    }

    private function rol_control(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 10)) == false){
          return true;
        }
    }

    private function rol_control2(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 4, 5, 10)) == false){
          return true;
        }
    }

    private function rol_doctor(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 3)) == false){
          return true;
        }
    }
    

    public function recupera_ordenes(){

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->join('users as cu','cu.id','eo.id_usuariocrea')->join('users as mu','mu.id','eo.id_usuariomod')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo','p.sexo');

        return $ordenes;

    }

    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }
        $fecha = date('Y/m/d');

        
        $ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha.' 23:59'])->where('eo.estado','<>','0')->paginate(30);
        //dd($ordenes);
        
        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha, 'ex_det' => $ex_det, 'nombres' => null, 'seguro' => null, 'seguros' => $seguros]);
    }

    public function parametro($id_examen)
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $examen_parametros = Examen_Parametro::where('id_examen',$id_examen)->paginate(30);
        $examen = Examen::find($id_examen);

        return view('laboratorio/examen/parametro',['examen' => $examen,'examen_parametros' => $examen_parametros]);
    }

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        } 
        
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        
        return view('laboratorio/orden/create',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);

    }

    public function crear_particular(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $seguros1 = DB::table('seguros as s')->where('s.inactivo','1')->where('s.tipo','>','1');
        $seguros2 = DB::table('seguros as s')->where('s.inactivo','1')->where('s.tipo','>','0')->join('convenio as c','c.id_seguro','s.id')->select('s.*');
       // dd($seguros2->get());
        $seguros = $seguros1->union($seguros2)->get();
        
        $protocolos = Protocolo::where('estado','1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        
        /*return view('laboratorio/orden/create_particular',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/

        return view('laboratorio/orden/crear_cotizacion',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);

    }

    public function crear_particular2($id){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();

        $paciente = Paciente::find($id);
        
        return view('laboratorio/orden/create_particular2',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas, 'paciente' => $paciente]);

    }

    public function create_admin($id_agenda,$url){

        if($this->rol()){
            return response()->view('errors.404');
        }

        /*$historia = DB::table('historiaclinica as h')->where('h.id_agenda',$id_agenda)->join('paciente as p','p.id','h.id_paciente')->join('users as d','d.id','h.id_doctor1')->select('h.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2','d.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $agenda = Agenda::find($id_agenda);*/
        $agenda = DB::table('agenda as a')->where('a.id',$id_agenda)->join('paciente as p','p.id','a.id_paciente')->leftjoin('users as d','d.id','a.id_doctor1')->select('a.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2','d.nombre1 as dnombre1', 'd.apellido1 as dapellido1','p.sexo','p.fecha_nacimiento')->first();
        
        
         //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        
        return view('laboratorio/orden/create_admin',['url_doctor' => $url, 'agenda' => $agenda, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);

    }

    /*public function create_parametro($id_examen){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $examen = Examen::find($id_examen);
        return view('laboratorio/examen/create_parametro',['examen' => $examen]);

    }*/

    public function store(Request $request)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput($request);

        $nivel='0';
        $convenio = Convenio::where('id_seguro',$request['id_seguro'])->where('id_empresa',$request['id_empresa'])->first();
        $seguro = Seguro::find($request['id_seguro']);
        //dd($convenio);
        $bandera_err=false;
        if($seguro->tipo!='0'){

            $bandera_err=true;
            $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];
        
        }else{

            if(is_null($convenio)){
                $bandera_err=true;
                $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
            }
        }

        if($bandera_err){

            $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
            $examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado','1')->get();
            $seguros = Seguro::where('inactivo','1')->get();
            $protocolos = Protocolo::where('estado','1')->get();
            $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
            
            
            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa'], 'fecha_nacimiento' => $request->fecha_nacimiento, 'sexo' => $request->sexo]);    
        
        }
        
        //CREAR USUARIO
        $input_usu_c = [

            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1' => '1',
            'telefono2' => '1',
            'id_tipo_usuario' => 2,
            'email' => $request['id'].'@mail.com',
            'password' => bcrypt($request['id']),
            'tipo_documento' => 1,
            'estado' => 1,
            'imagen_url' => ' ',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario    

            ];

        
        $user = User::find($request['id']); 
                
            if (!is_null($user)) {
                //$user->update($input_usu_a);
            }else{
                //User::create($input_usu_c);
            }   

        $input_pac = [

            'id' => $request['id'],
            'id_usuario' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'sexo' => $request['sexo'],
            'telefono1' => '1',
            'telefono2' => '1',
            'nombre1familiar' => strtoupper($request['nombre1']),
            'nombre2familiar' => strtoupper($request['nombre2']),
            'apellido1familiar' => strtoupper($request['apellido1']),
            'apellido2familiar' => strtoupper($request['apellido2']),
            'parentesco' => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento' => 1,
            'id_seguro' => 1,
            'imagen_url' => ' ',
            'menoredad' => 0,
                
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

            ];      
        
        $paciente=Paciente::find($request['id']);

        if(is_null($paciente)){

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            }else{
                User::create($input_usu_c);
            } 

            paciente::create($input_pac);

            $input_log = [
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "CREA NUEVO PACIENTE",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => " PARENTESCO: Principal",
            'dato2' => 'HOSPITALIZADO',
            ]; 

            Log_usuario::create($input_log);     
        }else{
            if($paciente->fecha_nacimiento==null||$paciente->sexo==null){

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo' => $request['sexo'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
                $paciente->update($pac);

            }
        } 
        
        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        $input_ex = [
            'id_paciente' => $request['id'],
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            'id_protocolo' => $request['id_protocolo'],
            'id_seguro' => $request['id_seguro'],
            'id_nivel' => $nivel,
            'est_amb_hos' => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt' => $request['doctor_txt'],
            'observacion' => $request['observacion'],
            'id_empresa' => $request['id_empresa'],
            'cantidad' => $cont,
            'valor' => $total,
            'total_valor' => $total,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'fecha_orden' => date('Y-m-d h:i:s'),
                        
        ];

        //dd($input_ex);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        /*$examenes = $request['examen'];

        foreach ($examenes as $examen) {
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen,
                    
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            } */
   
        $valor=0;
        $cont=0;
        $examen_nombre="";
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                
                $valor = $examen->valor;
                $examen_nombre=$examen_nombre."+".$examen->nombre;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                
                

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen->id,
                    'valor' => $valor,
                    
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            }
        }

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => $examen_nombre,
                ]);    
                
                    
        
        //dd($request->all(),$cont);                 

       
        return redirect()->route('orden.index');
    }

    public function store_particular(Request $request)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput($request);

        $nivel=null;
        $convenio = Convenio::where('id_seguro',$request['id_seguro'])->where('id_empresa',$request['id_empresa'])->first();
        $seguro = Seguro::find($request['id_seguro']);
        //dd($convenio);
        $bandera_err=false;
        /*if($seguro->tipo!='0'){

            $bandera_err=true;
            $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];
        
        }else{

            if(is_null($convenio)){
                $bandera_err=true;
                $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
            }
        }*/

        if($bandera_err){

            $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
            $examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado','1')->get();
            $seguros = Seguro::where('inactivo','1')->get();
            $protocolos = Protocolo::where('estado','1')->get();
            $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
            
            
            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);    
        
        }
        
        //CREAR USUARIO
        $input_usu_c = [

            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1' => '1',
            'telefono2' => '1',
            'id_tipo_usuario' => 2,
            'email' => $request['id'].'@mail.com',
            'password' => bcrypt($request['id']),
            'tipo_documento' => 1,
            'estado' => 1,
            'imagen_url' => ' ',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario    

            ];

        
        $user = User::find($request['id']); 
                
            if (!is_null($user)) {
                //$user->update($input_usu_a);
            }else{
                //User::create($input_usu_c);
            }   

        $input_pac = [

            'id' => $request['id'],
            'id_usuario' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'sexo' => $request['sexo'],
            'telefono1' => '1',
            'telefono2' => '1',
            'nombre1familiar' => strtoupper($request['nombre1']),
            'nombre2familiar' => strtoupper($request['nombre2']),
            'apellido1familiar' => strtoupper($request['apellido1']),
            'apellido2familiar' => strtoupper($request['apellido2']),
            'parentesco' => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento' => 1,
            'id_seguro' => 1,
            'imagen_url' => ' ',
            'menoredad' => 0,
                
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

            ];   

          //dd($input_pac,$input_usu_c);     
        
        $paciente=Paciente::find($request['id']);

        if(is_null($paciente)){

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            }else{
                User::create($input_usu_c);
            } 
                    
            paciente::create($input_pac);

            $input_log = [
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "CREA NUEVO PACIENTE",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => " PARENTESCO: Principal",
            'dato2' => 'HOSPITALIZADO',
            ]; 

            Log_usuario::create($input_log);     
        }else{
            if($paciente->fecha_nacimiento==null||$paciente->sexo==null){

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo' => $request['sexo'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
                $paciente->update($pac);

            }
        } 
        

        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        //dd($nivel);

        $input_ex = [
            'id_paciente' => $request['id'],
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            'id_protocolo' => $request['id_protocolo'],
            'id_seguro' => $request['id_seguro'],
            'id_nivel' => $nivel,
            'est_amb_hos' => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt' => $request['doctor_txt'],
            'observacion' => $request['observacion'],
            'id_empresa' => $request['id_empresa'],
            'cantidad' => $cont,
            'valor' => $total,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
                        
        ];

        //dd($input_ex);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        /*$examenes = $request['examen'];

        foreach ($examenes as $examen) {
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen,
                    
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            } */
   
        $valor=0;
        $cont=0;
        $examen_nombre="";
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                
                $valor = $examen->valor;
                $examen_nombre=$examen_nombre."+".$examen->nombre;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                
                

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen->id,
                    'valor' => $valor,
                    
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            }
        }

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN PARTICULAR",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => $examen_nombre,
                ]);    
                
                    
        
        //dd($request->all(),$cont);                 

       
        return redirect()->route('orden.index');
    }

    public function store_admin(Request $request)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput_admin($request);

        $nivel='0';
        $convenio = Convenio::where('id_seguro',$request['id_seguro'])->where('id_empresa',$request['id_empresa'])->first();
        $seguro = Seguro::find($request['id_seguro']);
        //dd($convenio);
        $bandera_err=false;
        if($seguro->tipo!='0'){

            $bandera_err=true;
            $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];
        
        }else{

            if(is_null($convenio)){
                $bandera_err=true;
                $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
            }
        }

        if($bandera_err){

            $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
            $examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
            $agrupadores = Examen_Agrupador::where('estado','1')->get();
            $seguros = Seguro::where('inactivo','1')->get();
            $protocolos = Protocolo::where('estado','1')->get();
            $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
            
            
            return redirect()->back()->with(['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas])->withErrors($arr_campo)->withInput(['id' => $request['id'], 'nombre1' => $request['nombre1'], 'nombre2' => $request['nombre2'], 'apellido1' => $request['apellido1'], 'apellido2' => $request['apellido2'], 'id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'est_amb_hos' => $request['est_amb_hos'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);    
        
        }
        
        
        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        $hcid = Historiaclinica::where('id_agenda',$request['id_agenda'])->first();

        $hcid_id=null;
        if(!is_null($hcid)){
            $hcid_id = $hcid->hcid; 
        }

        $input_ex = [
            'id_paciente' => $request['id'],
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            'id_protocolo' => $request['id_protocolo'],
            'id_seguro' => $request['id_seguro'],
            'id_nivel' => $nivel,
            'est_amb_hos' => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt' => $request['doctor_txt'],
            'observacion' => $request['observacion'],
            'id_empresa' => $request['id_empresa'],
            'hcid' => $hcid_id,
            'id_agenda' => $request['id_agenda'],
            'cantidad' => $cont,
            'valor' => $total,
            'total_valor' => $total,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'fecha_orden' => date('Y-m-d h:i:s'),
                        
        ];

        $agenda = Agenda::find($request['id_agenda']);

        $input_ag = [
            
            'id_empresa' => $request['id_empresa'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
                        
        ];

        $agenda->update($input_ag);

        //dd($input_ex);

        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        /*$examenes = $request['examen'];

        foreach ($examenes as $examen) {
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen,
                    
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            } */
   
        $valor=0;
        $cont=0;
        $examen_nombre="";
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                
                $valor = $examen->valor;
                $examen_nombre=$examen_nombre."+".$examen->nombre;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                
                

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen->id,
                    'valor' => $valor,
                    
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            }
        } 


        $paciente=Paciente::find($request['id']);

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERA ORDEN EXAMEN",
            'dato_ant1' => $request['id'],
            'dato1' => $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2,
            'dato_ant4' => $examen_nombre,
                ]);   

        $cedula = $request['id'];

        if($paciente->fecha_nacimiento==null||$paciente->sexo==null){

            $pac = [
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'sexo' => $request['sexo'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];
            $paciente->update($pac);

        }

        //return $this->index_admin($cedula);
        return redirect()->route('orden.index_admin',['cedula' => $cedula]);    
        
    }

    public function index_admin ($cedula){

        $fecha_hasta= Date('Y-m-d'); 
        $fecha = Date('Y-m-d',strtotime ( '-1 month' , strtotime ( $fecha_hasta ) ) );  

        $paciente = Paciente::find($cedula);
        $nombres = $paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2;  
            
        $ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30);

        /*$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30); */                

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $seguros = Seguro::where('inactivo','1')->get();


        
        return view('laboratorio/orden/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => null, 'seguros' => $seguros ]);
    }

    public function index_doctor ($cedula, $agenda){

        $fecha_hasta= Date('Y-m-d'); 

        $fecha = Date('Y-m-d',strtotime ( '-6 month' , strtotime ( $fecha_hasta ) ) );  

        $paciente = Paciente::find($cedula);
        $nombres = $paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2;  
            
        $ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->where('eo.estado','1')->orderBy('fecha_orden','asc')->paginate(30);

        /*$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30); */                

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $seguros = Seguro::where('inactivo','1')->get();


        
        return view('laboratorio/orden/index_doctor', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => null, 'seguros' => $seguros, 'agenda' => $agenda ]);

    }//INDEX_DOCTOR

    public function index_doctor_menu (){

        $fecha_hasta= Date('Y-m-d'); 

        $fecha = Date('Y-m-d');  
            
        $ordenes = $this->recupera_ordenes()->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.estado','1')->orderBy('fecha_orden','asc')->paginate(30);

        /*$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.id_paciente',$cedula)->paginate(30); */                

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }

        $seguros = Seguro::where('inactivo','1')->get();


        
        return view('laboratorio/orden/index_doctor', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'seguro' => null, 'seguros' => $seguros, 'agenda' => null, 'nombres' => null ]);

    }//INDEX_DOCTOR

    public function store_parametro(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        //$this->validateInput($request);

        
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['nombre']),
            'texto1' => strtoupper($request['texto1']),
            'texto2' => strtoupper($request['texto2']),
            'texto3' => strtoupper($request['texto3']),
            'texto4' => strtoupper($request['texto4']),
            'valor1' => $request['valor1'],
            'valor2' => $request['valor2'],
            'valor3' => $request['valor3'],
            'valor4' => $request['valor4'],
            'valor1g' => $request['valor1g'],
            'valor2g' => $request['valor2g'],
            'valor3g' => $request['valor3g'],
            'valor4g' => $request['valor4g'],
            'unidad1' => strtoupper($request['unidad1']),
            'unidad2' => strtoupper($request['unidad2']),
            'unidad3' => strtoupper($request['unidad3']),
            'unidad4' => strtoupper($request['unidad4']),
            'id_examen' => $request['id_examen'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];


        Examen_parametro::create($input);           

       
        return redirect()->route('examen.parametro',['id_examen' => $request['id_examen']]);
    }

    private function validateInput($request) {

        $rules = [
           
            'id' =>  'required|max:10',
            'nombre1' =>  'required|max:60',
            'nombre2' =>  'required|max:60',
            'apellido1' =>  'required|max:60',
            'apellido2' =>  'required|max:60',
            'id_doctor_ieced' =>  'required',
            'observacion' =>  'max:200',

        ];
         
        $messages= [
       
        'id.required' => 'Ingrese la cédula del paciente',
        'id.max' => 'La cédula no puede ser mayor a :max caracteres',
        'nombre1.required' => 'Ingresa el Nombre.',      
        'nombre1.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'nombre2.required' => 'Ingresa el Nombre.',      
        'nombre2.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'apellido1.required' => 'Ingresa el Nombre.',      
        'apellido1.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'apellido2.required' => 'Ingresa el Nombre.',      
        'apellido2.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'id_doctor_ieced.required' => 'Selecciona el doctor.',
        'observacion.max' =>'La observación no puede ser mayor a :max caracteres.',
        
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput_admin($request) {

        
        $rules = [
           
            'id' =>  'required|max:10',
            'id_doctor_ieced' =>  'required',
            'observacion' =>  'max:200',
            'id_empresa' => 'required',
            'id_protocolo' => 'required',

        ];
         
        $messages= [
       
        'id_protocolo.required' => 'Seleccione el Protocolo',
        'id_empresa.required' => 'Seleccione la Empresa',
        'id.required' => 'Ingrese la cédula del paciente',
        'id.max' => 'La cédula no puede ser mayor a :max caracteres',
        'nombre1.required' => 'Ingresa el Nombre.',      
        'nombre1.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'nombre2.required' => 'Ingresa el Nombre.',      
        'nombre2.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'apellido1.required' => 'Ingresa el Nombre.',      
        'apellido1.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'apellido2.required' => 'Ingresa el Nombre.',      
        'apellido2.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'id_doctor_ieced.required' => 'Selecciona el doctor.',
        'observacion.max' =>'La observación no puede ser mayor a :max caracteres.',
        
        ];    

        $this->validate($request, $rules, $messages);
    }


    private function validateInput2($request) {

        $rules = [
           
            'id_doctor_ieced' =>  'required',
            'observacion' =>  'max:200',

        ];
         
        $messages= [
       
        'id.required' => 'Ingrese la cédula del paciente',
        'id.max' => 'La cédula no puede ser mayor a :max caracteres',
        'nombre1.required' => 'Ingresa el Nombre.',      
        'nombre1.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'nombre2.required' => 'Ingresa el Nombre.',      
        'nombre2.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'apellido1.required' => 'Ingresa el Nombre.',      
        'apellido1.max' =>'El nombre no puede ser mayor a :max caracteres.',
        'apellido2.required' => 'Ingresa el Nombre.',      
        'apellido2.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'id_doctor_ieced.required' => 'Selecciona el doctor.',
        'observacion.max' =>'La observación no puede ser mayor a :max caracteres.',
        
        ];    

        $this->validate($request, $rules, $messages);
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seguro = Seguro::find($orden->id_seguro);
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();
        if(!is_null($orden)){

        
            return view('laboratorio/orden/edit', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => 'rec']);

        }


    }
    public function edit1_c($id)
    {
        if($this->rol_control()){
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seguro = Seguro::find($orden->id_seguro);
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();
        if(!is_null($orden)){

        
            return view('laboratorio/orden/edit', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => 'CON']);

        }


    }

    public function edit2($id, $dir)
    {
        //dd($dir);
        if($this->rol_control2()){
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos

        
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seguro = Seguro::find($orden->id_seguro);
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();
        if(in_array($orden->id_seguro, [1, 4])){
        
            $examenes = Examen::orderBy('id_agrupador')->get();

        }else{
            $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get(); 
            
        }
        
        if(!is_null($orden)){

        
            return view('laboratorio/orden/edit2', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => $dir]);

        }


    }

    public function detalle($id,$dir)
    {
        
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seguro = Seguro::find($orden->id_seguro);
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();
        if(!is_null($orden)){

        
            return view('laboratorio/orden/detalle', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => $dir]);

        }


    }

    
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($id);
        $this->validateInput2($request);

        $nivel='0';
        $convenio = Convenio::where('id_seguro',$request['id_seguro'])->where('id_empresa',$request['id_empresa'])->first();
        $seguro = Seguro::find($request['id_seguro']);

        $bandera_err=false;
        if($seguro->tipo!='0'){

            $bandera_err=true;
            $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];
        
        }else{

            if(is_null($convenio)){
                $bandera_err=true;
                $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
            }
        }

        if($bandera_err){

            $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        $examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seguro = Seguro::find($orden->id_seguro);
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();
            
            
            return redirect()->back()->with(['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros])->withErrors($arr_campo)->withInput(['id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);    
        
        }

        $input_ex1 = [
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt' => $request['doctor_txt'],
            'observacion' => $request['observacion'],
            'id_empresa' => $request['id_empresa'],
            'id_protocolo' => $request['id_protocolo'],
            'id_seguro' => $request['id_seguro'],
            
            
            'ip_modificacion' => $ip_cliente,
            
            'id_usuariomod' => $idusuario,
                        
        ];

        $orden->update($input_ex1);

        
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->get();
        
            

        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }


        $input_ex2 = [
            
            'cantidad' => $cont,
            'valor' => $total,
            'total_valor' => $total,
            
            'ip_modificacion' => $ip_cliente,
            
            'id_usuariomod' => $idusuario,
                        
        ];

        $orden->update($input_ex2);
        
        foreach ($detalles as $detalle) {
            $detalle->Delete();
        }
            


        $valor=0;
        $cont=0;
        $examen_nombre="";
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                
                $valor = $examen->valor;
                $examen_nombre=$examen_nombre."+".$examen->nombre;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                
                

            $cont++;
            $input_det = [
                'id_examen_orden' => $id,
                'id_examen' => $examen->id,
                'valor' => $valor,
                    
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                        
            ]; 

            Examen_detalle::create($input_det);
                
            }
        } 

        $paciente=Paciente::find($orden->id_paciente);
        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN EXAMEN",
            'dato_ant1' => $orden->id_paciente,
            'dato1' => $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2,
            'dato_ant4' => $examen_nombre,
                ]);   

        $cedula = $request['id'];                   
       
        if($request['dir']=='CON'){
            return redirect()->route('orden.index_control_b',['id' => $id]);
                
        }else{
           return redirect()->route('orden.index'); 
        }
        
    } 
     public function update_particular(Request $request, $id)
    {
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden::find($id);
        $this->validateInput2($request);

        $nivel=null;
        $convenio = Convenio::where('id_seguro',$request['id_seguro'])->where('id_empresa',$request['id_empresa'])->first();
        $seguro = Seguro::find($request['id_seguro']);

        $bandera_err=false;
        /*if($seguro->tipo!='0'){

            $bandera_err=true;
            $arr_campo=['id_seguro' => 'Seguro particular no habilitado'];
        
        }else{

            if(is_null($convenio)){
                $bandera_err=true;
                $arr_campo=['id_seguro' => 'Convenio no habilitado','id_empresa' => 'Convenio no habilitado'];
            }
        }*/

        if($bandera_err){

            $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        $examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seguro = Seguro::find($orden->id_seguro);
        $seguros = Seguro::where('inactivo','1')->get();
        $protocolos = Protocolo::where('estado','1')->get();
            
            
            return redirect()->back()->with(['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros])->withErrors($arr_campo)->withInput(['id_doctor_ieced' => $request['id_doctor_ieced'], 'id_seguro' => $request['id_seguro'], 'id_protocolo' => $request['id_protocolo'], 'id_empresa' => $request['id_empresa']]);    
        
        }

        $input_ex1 = [
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt' => $request['doctor_txt'],
            'observacion' => $request['observacion'],
            'id_empresa' => $request['id_empresa'],
            'id_protocolo' => $request['id_protocolo'],
            'id_seguro' => $request['id_seguro'],
            
            
            'ip_modificacion' => $ip_cliente,
            
            'id_usuariomod' => $idusuario,
                        
        ];

        $orden->update($input_ex1);

        
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->get();
        
            

        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }


        $input_ex2 = [
            
            'cantidad' => $cont,
            'valor' => $total,
            
            'ip_modificacion' => $ip_cliente,
            
            'id_usuariomod' => $idusuario,
                        
        ];

        $orden->update($input_ex2);
        
        foreach ($detalles as $detalle) {
            $detalle->Delete();
        }
            


        $valor=0;
        $cont=0;
        $examen_nombre="";
        $examenes = Examen::where('estado','1')->get();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                
                $valor = $examen->valor;
                $examen_nombre=$examen_nombre."+".$examen->nombre;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                
                

            $cont++;
            $input_det = [
                'id_examen_orden' => $id,
                'id_examen' => $examen->id,
                'valor' => $valor,
                    
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                        
            ]; 

            Examen_detalle::create($input_det);
                
            }
        } 

        $paciente=Paciente::find($orden->id_paciente);
        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN EXAMEN PARTICULAR",
            'dato_ant1' => $orden->id_paciente,
            'dato1' => $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2,
            'dato_ant4' => $examen_nombre,
                ]);   

        $cedula = $request['id'];                   
       
        if($request['dir']=='CON'){
            return redirect()->route('orden.index_control_b',['id' => $id]);
                
        }else{
           return redirect()->route('orden.index'); 
        }
        
    }    

    

    public function buscapaciente($id){
        $paciente = Paciente::find($id);
        if(!is_null($paciente))
        {
            return $paciente;    
        }
        else
        {
            return 'no';
        }    


        
    }

    public function search(Request $request) {

         
        if($this->rol()){
            return response()->view('errors.404');
        }
        $seguro = $request['seguro'];
        $nombres = $request['nombres'];    
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        
        $ordenes = $this->recupera_ordenes();
        /*]$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial');*/

        //buscadorxpaciente

        if($fecha!=null){

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }  
        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }    
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $nombres_sql='';$cantidad = count($nombres2); 
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
             

            }
             
            else{

                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }    
  
        }

        //dd($ordenes->get());

        $ordenes = $ordenes->where('eo.estado','<>','0')->paginate(30);

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros]);

    }

    public function search_doctor(Request $request) {

         
        if($this->rol_supervision()){
            return response()->view('errors.404');
        }
        $seguro = $request['seguro'];
        $nombres = $request['nombres'];    
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        
        $ordenes = $this->recupera_ordenes();
        /*]$ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial');*/

        //buscadorxpaciente

        if($fecha!=null){

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }  
        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }    
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            $nombres_sql='';
             foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
             

            }
             
            else{

                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }      
  
        }

        //dd($ordenes->get());

        $ordenes = $ordenes->where('eo.estado','1')->orderBy('fecha_orden','asc')->paginate(30);

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index_doctor', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros, 'agenda' => $request['agenda']]);

    }//INDEX_DOCTOR

    public function index_supervision(Request $request) {

        if($this->rol_supervision()){
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];    
        if($request['fecha']==null){
            $fecha = date('Y-m-d');
        }else{
            $fecha = $request['fecha'];
        }
        if($request['fecha_hasta']==null){
            $fecha_hasta = date('Y-m-d');
        }else{
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];
        
        
        
        $ordenes = $this->recupera_ordenes();
        

        if($fecha!=null){

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        } 
        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }     
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        //dd($ordenes->get());
        $ordenes = $ordenes->where('eo.estado','1')->paginate(30);

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index_supervision', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros]);

    }

    public function search_supervision(Request $request) {

        if($this->rol_supervision()){
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];    
        if($request['fecha']==null){
            $fecha = null;
        }else{
            $fecha = $request['fecha'];
        }
        if($request['fecha_hasta']==null){
            $fecha_hasta = date('Y-m-d');
        }else{
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro'];
        
        
        
        $ordenes = $this->recupera_ordenes();
        

        if($fecha!=null){

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        } 
        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }     
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        //dd($ordenes->get());
        $ordenes = $ordenes->where('eo.estado','1')->paginate(30);

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->nombre; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->nombre;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index_supervision', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros]);

    }

    public function index_control(Request $request) {

        //
        if($this->rol_control()){
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];    
        if($request['fecha']==null){
            $fecha = date('Y-m-d');
        }else{
            $fecha = $request['fecha'];
        }
        if($request['fecha_hasta']==null){
            $fecha_hasta = date('Y-m-d');
        }else{
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro']; 
        
        //$ordenes = $this->recupera_ordenes();
        $ordenes = Examen_Orden::join('paciente as p','p.id','id_paciente')->leftjoin('protocolo as proto','proto.id','id_protocolo')->select('examen_orden.*','p.apellido1','p.apellido2','p.nombre1','p.nombre2','proto.pre_post');
        
        
        //buscadorxpaciente

        if($fecha!=null){
            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            
            
        } 
        if($seguro!=null){

            //$ordenes = $ordenes->where('eo.id_seguro',$seguro);
            $ordenes = $ordenes->where('id_seguro',$seguro);
            
        }    
        if($nombres!=null)
        {
            $nombres2 = explode(" ", $nombres); 
            $nombres_sql='';$cantidad = count($nombres2); 
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                });*/
                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                });
             
            }
             
            else{

                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });*/
                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                });    
            }         
  
        }

        //dd($ordenes->get());

        //$ordenes = $ordenes->where('eo.estado','1')->paginate(30);
        $ordenes = $ordenes->where('examen_orden.estado','1')->orderBy('fecha_orden','asc')->paginate(30);


        $seguros = Seguro::where('inactivo','1')->get();

        $arr_or = [];
        foreach ($ordenes as $o) {
            $hema = 0; $bio = 0; $man = 0; 
             
            foreach ($o->detalles as $d) {
                if($d->examen->maquina=='1'){
                    $hema ++;
                }
                if($d->examen->maquina=='2'){
                    $bio ++;
                }
                if($d->examen->maquina=='0'){
                    $man ++;
                }
            }
            $arr_or[$o->id] = ['hema' => $hema, 'bio' => $bio, 'man' => $man];
        }
        
        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => $seguro, 'arr_or' => $arr_or]);

    }//INDEX_CONTROL

    public function index_control_b($id) {//RECUERDA AGENDALABSCONTROLLER

        //
        if($this->rol_control()){
            return response()->view('errors.404');
        }
        
        $orden = Examen_Orden::find($id);
        $paciente = $orden->paciente;
        //dd($paciente);
        $seguro = null;

        $nombres = $paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2;
        //dd($nombres);    
        
        //$fecha = $orden->created_at;
        $fecha = $orden->fecha_orden;
        $fecha_hasta = $fecha;

        
        $ordenes = $this->recupera_ordenes();
        //dd($ordenes->get());
        //buscadorxpaciente

        if($fecha!=null){

            //$ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        } 
        

        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }    
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
           $nombres_sql='';$cantidad = count($nombres2); 
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
             

            }
             
            else{

                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }       
  
        }

        

        $ordenes = $ordenes->where('eo.estado','1')->paginate(30);

       $ex_det=[];
        foreach ($ordenes as $orden) {
            
            $examen_par = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen_parametro as ep','ep.id_examen','ed.id_examen')->select('ed.id_examen')->groupBy('ed.id_examen')->get(); 
            $resultado = DB::table('examen_resultado as er')->where('er.id_orden',$orden->id)->join('examen_parametro as ep','ep.id','er.id_parametro')->select('ep.id_examen')->groupBy('ep.id_examen')->get();
            $ex_det[$orden->id] = $examen_par->count() - $resultado->count();
            //dd($resultado->count(),$examen_par->count());
        }

        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => $seguro]);

    }

    public function search_control(Request $request) {

        //
        if($this->rol_control()){
            return response()->view('errors.404');
        }
        $nombres = $request['nombres'];    
        if($request['fecha']==null){
            $fecha = null;
        }else{
            $fecha = $request['fecha'];
        }
        if($request['fecha_hasta']==null){
            $fecha_hasta = date('Y-m-d');
        }else{
            $fecha_hasta = $request['fecha_hasta'];
        }
        $seguro = $request['seguro']; 
        
        
        //$ordenes = $this->recupera_ordenes();
      
        $ordenes = Examen_Orden::join('paciente as p','p.id','id_paciente')->leftjoin('protocolo as proto','proto.id','id_protocolo')->select('examen_orden.*','p.apellido1','p.apellido2','p.nombre1','p.nombre2','proto.pre_post');


        if($fecha!=null){

            //$ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
            $ordenes = $ordenes->whereBetween('fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        } 

        if($seguro!=null){

            //$ordenes = $ordenes->where('eo.id_seguro',$seguro);
            $ordenes = $ordenes->where('id_seguro',$seguro);
            
        }  


        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $nombres_sql='';           
            $cantidad = count($nombres2); 
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                    /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });*/
                    $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });

                    
             

            }
             
            else{

                /*$ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });*/
                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }       
  
        }


        //

        //$ordenes = $ordenes->where('eo.estado','1')->paginate(30);
        $ordenes = $ordenes->where('examen_orden.estado','1')->orderBy('fecha_orden','asc')->paginate(30);
        //dd($ordenes->count());
        $arr_or = [];
        foreach ($ordenes as $o) {
            $hema = 0; $bio = 0; $man = 0; 
             
            foreach ($o->detalles as $d) {
                if($d->examen->maquina=='1'){
                    $hema ++;
                }
                if($d->examen->maquina=='2'){
                    $bio ++;
                }
                if($d->examen->maquina=='0'){
                    $man ++;
                }
            }
            $arr_or[$o->id] = ['hema' => $hema, 'bio' => $bio, 'man' => $man];
        }

        //dd($arr_or);


        $seguros = Seguro::where('inactivo','1')->get();
        
        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => $seguro, 'arr_or' => $arr_or]);

    }//INDEX_CONTROL

    public function fecha_convenios(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];

        $idusuario = Auth::user()->id;
        $id = $request['id'];

        $orden = Examen_Orden::find($id);
        $input_ex1 = [
            'fecha_convenios' => $request['fecha_convenios'],                      
        ];

        $orden->update($input_ex1);
        return "ok";
    }

    public function eliminar ($id){

        if($this->rol()){
            return response()->view('errors.404');
        }

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
    
        $orden = Examen_Orden::find($id);
        $paciente=Paciente::find($orden->id_paciente);

        if($orden->realizado=='0'){
            Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ELIMINA ORDEN EXAMEN",
            'dato_ant1' => $orden->id_paciente,
            'dato1' => $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2,
            'dato_ant4' => $orden->id,
                ]);   
            $detalles =Examen_detalle::where('id_examen_orden',$orden->id)->get();
            foreach ($detalles as $detalle) {
                $detalle->Delete();
            }
            //$orden->Delete();
            $input_ex1 = [
                
                'estado' => '0',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $orden->update($input_ex1);
        }
        

        return redirect()->route('orden.index');

    }

    /*public function realizar ($id,$i){

        if($this->rol_control()){
            return response()->view('errors.404');
        }
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');    
        if($i == 1){
            $orden = Examen_Orden::find($id);
            $input_ex1 = [
                
                'realizado' => $i,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $orden->update($input_ex1);

            //return redirect()->route('orden.index_control');
        }

        if($i == 3){//nunca llega a este estado
            $orden = Examen_Orden::find($id);
            $input_ex1 = [
                
                'realizado' => '0',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $orden->update($input_ex1);

            return redirect()->route('orden.index_control');
        }

        if($i == 0|| $i== 1){
            $orden = Examen_Orden::find($id);
            $detalle = Examen_Detalle::where('id_examen_orden', $id)->get();
            $resultados =  Examen_resultado::where('id_orden', '=', $id)->get();
            $agrupador = Examen_Agrupador::all();
            $parametros = Examen_parametro::where('estado',1)->orderBy('orden')->get();
            return view('laboratorio.orden.resultados', ['orden' => $orden, 'detalle' => $detalle, 'resultados' => $resultados, 'agrupador' => $agrupador, 'parametros' => $parametros]);
        } 
        
    }*/
     public function realizar (Request $request,$id,$maq ){

        //dd($request->all());
        //$i es la maquina 1 BIOMETRIA -- 2 BIOQUIMICA -- 0 manual
        if($this->rol_control()){
            return response()->view('errors.404');
        }
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil'); 

        $orden = Examen_Orden::find($id);
        $detalle = Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();

        if($orden->realizado=='0'){//PARA LAS ORDENES PUBLICAS

            $input_ex1 = [
                
                'realizado' => '1',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $orden->update($input_ex1);

        }    
        
        if($orden->seguro->tipo=='0'){
            $agrupador = Examen_Agrupador::all();  
            
        }else{
            $agrupador = Examen_Agrupador_labs::all();
            
        }
        
        $parametros = Examen_parametro::orderBy('orden')->get();
        return view('laboratorio.orden.resultados', ['orden' => $orden, 'agrupador' => $agrupador, 'parametros' => $parametros, 'maq' => $maq, 'fecha' => $request->fecha, 'fecha_hasta' => $request->fecha_hasta, 'seguro' => $request->seguro, 'nombres' => $request->nombres, 'detalle' => $detalle]);
        
        
    }


    public function puede_imprimir($id){

        $orden = Examen_Orden::find($id);
        $detalle = $orden->detalles;
        $resultados =  $orden->resultados;
        //$parametros = Examen_parametro::orderBy('orden')->get();
       
        $cant_par = 0;
        foreach($detalle as $d){
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if($d->examen->no_resultado=='0'){

                if(count($d->parametros)=='0'){
                    $cant_par ++;  
                } 
                if($d->examen->sexo_n_s=='0'){
                  $parametro_nuevo = $d->parametros->where('sexo','3'); 
                  
                }else{
                  $parametro_nuevo = $d->parametros->where('sexo',$orden->paciente->sexo);

                }
                foreach ($parametro_nuevo as $p) {
                    $cant_par ++;    
                }
            }           
            
        }

        $certificados = 0;
        $cantidad = 0;
        foreach($resultados as $r){
            $cantidad ++;
            if($r->certificado=='1'){
                $certificados ++;
                
            }
        }
        if($certificados>$cant_par){
            $certificados = $cant_par;
        }

        return [ 'cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par ];

    }

    
    public function imprimir_resultado($id){
        //dd($poce);

        
        $orden = Examen_Orden::find($id);
        //$detalle = $orden->detalles;
        $detalle = Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
        //dd($detalle);
        $resultados =  $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();

        //Recalcula Porcentaje 
        $cant_par = 0;
        foreach($detalle as $d){
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if($d->examen->no_resultado=='0'){

                if(count($d->parametros)=='0'){
                    $cant_par ++;  
                } 
                if($d->examen->sexo_n_s=='0'){
                  $parametro_nuevo = $d->parametros->where('sexo','3'); 
                  
                }else{
                  $parametro_nuevo = $d->parametros->where('sexo',$orden->paciente->sexo);

                }
                foreach ($parametro_nuevo as $p) {
                    $cant_par ++;    
                }
            }           
            
        }

        $certificados = 0;
        $cantidad = 0;
        foreach($resultados as $r){
            $cantidad ++;
            if($r->certificado=='1'){
                $certificados ++;
                
            }
        }
        if($certificados>$cant_par){
            $certificados = $cant_par;
        }


        if($cant_par=='0'){
          $pct=0;
        }else{
          $pct = $certificados/$cant_par*100;  
        }
        //dd($pct);
        //dd($detalle);
        // Fin recalcula Porcentaje
        
        if($orden->seguro->tipo=='0'){
            $agrupador = Examen_Agrupador::all();  
            
        }else{
            $agrupador = Examen_Agrupador_labs::all();
            
        }
        
        $ucreador = $orden->crea;
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl="laboratorio.orden.resultados_pdf";
        $view =  \View::make($vistaurl, compact('orden','pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-'.$id.'.pdf'); 

    }

    public function ver_doctor ($id){
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');    
        $orden = Examen_Orden::find($id);
        $detalle = Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();

        if($orden->seguro->tipo=='0'){
            $agrupador = Examen_Agrupador::all();  
            
        }else{
            $agrupador = Examen_Agrupador_labs::all();
            
        }
       
        $parametros = Examen_parametro::orderBy('orden')->get();

        return view('laboratorio.orden.resultados_convenios', ['orden' => $orden, 'agrupador' => $agrupador, 'parametros' => $parametros, 'detalle' => $detalle]);

        
    }

    public function ver_convenios ($id){
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');    
        $orden = Examen_Orden::find($id);
        $detalle = Examen_Detalle::where('id_examen_orden', $id)->get();
        $resultados =  Examen_resultado::where('id_orden', '=', $id)->get();
        $agrupador = Examen_Agrupador::all();
        $parametros = Examen_parametro::where('estado',1)->orderBy('orden')->get();
        return view('laboratorio.orden.resultados_doctor', ['orden' => $orden, 'detalle' => $detalle, 'resultados' => $resultados, 'agrupador' => $agrupador, 'parametros' => $parametros]);
    }

    public function crea_modifica($id_orden, $id_parametro){
        $parametro = Examen_parametro::find($id_parametro);
        $resultado = Examen_resultado::where('id_orden', $id_orden)->where('id_parametro', $id_parametro)->first();
        
        return view('laboratorio.orden.modal', ['resultado' => $resultado, 'parametro' => $parametro, 'id_orden' => $id_orden]);
    }

    public function guarda_actualiza_resultados(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil'); 
        $id_parametro = $request['id_parametro'];
        $id_orden = $request['id_orden'];
        $parametro = Examen_parametro::find($id_parametro);
        $resultado = Examen_resultado::where('id_orden', $id_orden)->where('id_parametro', $id_parametro)->first();

        if($request['valor']==null){
            $valor = 0;
        }else{
            $valor = $request['valor'];
        }

        if($resultado != ""){
            $input_ex1 = [
                
                'valor' => $valor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $resultado->update($input_ex1);
        }else{
            $input_det = [
                'id_orden' => $id_orden,
                'id_parametro' => $id_parametro,
                'valor' => $valor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                        
            ]; 

            Examen_resultado::create($input_det);
        }
        return [$id_parametro,$request['valor']];
    }
    public function reporte(Request $request) {

        
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres = $request['nombres'];
        $seguro = $request['seguro'];
        //dd($request->all());
        
        /*$ordenes = DB::table('examen_orden as eo')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->get();*/

        $ordenes = DB::table('examen_orden as eo')->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->leftjoin('forma_de_pago as fp','fp.id','eo.id_forma_de_pago')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','fp.nombre as fpnombre')->where('eo.realizado','1')->where('eo.estado','1');

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }
         if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        } 

        $ordenes = $ordenes->get();


        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre','e.descripcion')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->descripcion; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->descripcion;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        

        $i=0;
        
        $fecha_d = date('Y/m/d'); 
        
        Excel::create('Examenes-'.$fecha_d, function($excel) use($ordenes, $ex_det) {

            $excel->sheet('Examenes', function($sheet) use($ordenes, $ex_det) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                
                $sheet->mergeCells('A3:G3'); 
                
                $mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                
                
                $sheet->cells('A1:M3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:M4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE ORDENES DE EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FORMA DE PAGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

               

                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO %');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('RECARGO %');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('RECARGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                $cant=1; $total=0;
                foreach($ordenes as $value){
                    $txtcolor='#000000';
                    
                    
                    $sheet->cell('A'.$i, function($cell) use($value, $txtcolor,$cant){
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    

                    $sheet->cell('C'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        if($value->papellido2 != "(N/A)"){
                            $vnombre= $value->papellido1.' '.$value->papellido2;   
                        }
                        else{
                            $vnombre= $value->papellido1;   
                        }

                        if($value->pnombre2 != "(N/A)"){
                            $vnombre= $vnombre.' '.$value->pnombre1.' '.$value->pnombre2;   
                        }
                        else
                        {
                            $vnombre= $vnombre.' '.$value->pnombre1;
                        }   
                        
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });


                    $sheet->cell('D'.$i, function($cell) use($value, $txtcolor) {
                            
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                     $sheet->cell('E'.$i, function($cell) use($value, $txtcolor) {
                            
                        $cell->setValue($value->snombre );
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1.' '.$value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                    });

                    $sheet->cell('H'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->fpnombre);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('I'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('J'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });


                    $sheet->cell('K'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_p);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('L'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('M'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    

                    $i= $i+1;


                    $cant = $cant + 1;
                    $total = $total + $value->total_valor; 
                }
                $sheet->getStyle('G5:G'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('J5:J'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('L5:M'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');

                 $sheet->cell('A'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL PACIENTES:');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('K'.$i, function($cell) use($cant){
                        // manipulate the cel
                        $cell->setValue($cant - 1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('L'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL:');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('M'.$i, function($cell) use($total){
                        // manipulate the cel
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                   
                    
            });
        })->export('xlsx');
    }

    public function reporte_index(Request $request) {

        //dd($request->all());
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro = $request['seguro'];
        $nombres = $request['nombres'];
        //dd($request->all());
        
        /*$ordenes = DB::table('examen_orden as eo')->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->get();*/

        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.realizado','1')->where('eo.estado','1');

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }  

        $ordenes = $ordenes->get();

        $seguros = Seguro::where('inactivo','1')->get();

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre','e.descripcion')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->descripcion; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->descripcion;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        

        $i=0;
        
        $fecha_d = date('Y/m/d');


        return view('laboratorio/orden/reporte_index',['ordenes' => $ordenes, 'ex_det' => $ex_det, 'nombres' => $nombres, 'fecha_hasta' => $fecha_hasta, 'fecha' => $fecha, 'seguro' => $seguro, 'seguros' => $seguros]); 
        
        
    }

    public function reporte_detalle(Request $request) {

        //dd($request->all());
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro = $request['seguro'];
        $nombres = $request['nombres'];
        //dd($request->all());
        
        $ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->join('users as cu','cu.id','eo.id_usuariocrea')->join('users as mu','mu.id','eo.id_usuariomod')->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->join('examen as e','e.id','ed.id_examen')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo','e.descripcion','ed.valor as edvalor', 'ed.cubre');


        $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.estado','1');

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }  

        $ordenes = $ordenes->get();
        
        //dd($ordenes);

        $seguros = Seguro::where('inactivo','1')->get();

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre','e.descripcion')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->descripcion; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->descripcion;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        
        $i=0;
        
        $fecha_d = date('Y/m/d'); 
        
        Excel::create('Examenes_detalle-'.$fecha_d, function($excel) use($ordenes) {

            $excel->sheet('Examenes_detalle', function($sheet) use($ordenes) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                
                $sheet->mergeCells('A3:G3'); 
                
                $mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                
                
                $sheet->cells('A1:G3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:G4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE DETALLE DE EXÁMENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CUBRE SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

               

                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/
                

                $cant=1; $total=0;
                foreach($ordenes as $value){

                    
                    $sheet->cell('A'.$i, function($cell) use($value, $cant){
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) use($value){
                        // manipulate the cel
                        //$cell->setValue(substr($value->created_at,0,10));
                        if($value->papellido2 != "(N/A)"){
                            $vnombre= $value->papellido1.' '.$value->papellido2;   
                        }
                        else{
                            $vnombre= $value->papellido1;   
                        }

                        if($value->pnombre2 != "(N/A)"){
                            $vnombre= $vnombre.' '.$value->pnombre1.' '.$value->pnombre2;   
                        }
                        else
                        {
                            $vnombre= $vnombre.' '.$value->pnombre1;
                        }   
                        
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    

                    $sheet->cell('C'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });


                    $sheet->cell('D'.$i, function($cell) use($value) {
                            
                        $cell->setValue(substr($value->fecha_orden,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('E'.$i, function($cell) use($value) {
                            
                        
                        $cell->setValue($value->snombre );
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('F'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('G'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->edvalor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                    });

                    $sheet->cell('H'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->cubre);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    /*
                    $sheet->cell('I'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('J'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });


                    $sheet->cell('K'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_p);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('L'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('M'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });*/

                    

                    $i= $i+1;


                    $cant = $cant + 1;
                    $total = $total + $value->total_valor; 
                }
                $sheet->getStyle('G5:G'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                //$sheet->getStyle('J5:J'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                //$sheet->getStyle('L5:M'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');

                 /*$sheet->cell('A'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL PACIENTES:');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('K'.$i, function($cell) use($cant){
                        // manipulate the cel
                        $cell->setValue($cant - 1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('L'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL:');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('M'.$i, function($cell) use($total){
                        // manipulate the cel
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });*/
                   
                    
            });
        })->export('xlsx');
        
        
    }

    public function codigo_barras($id){
        $data = $id;

        $orden =  examen_orden::find($id);
        $id_paciente  = $orden->id_paciente;
        $paciente = Paciente::find($id_paciente);

        $date = date('Y-m-d');

        $view =  \View::make('laboratorio.orden.pdf', compact('data', 'date', 'paciente'))->render();
        //$pdf = \App::make('dompdf.wrapper');
        //$pdf->loadHTML($view);
        return $view;
    }

    public function descargar($id){
        
        
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
        $examenes = Examen::where('publico_privado','0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $orden = DB::table('examen_orden as eo')->where('eo.id',$id)->join('paciente as p','p.id','eo.id_paciente')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.fecha_nacimiento as pfecha_nacimiento','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->first();
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->where('estado','1')->get();
        $seleccionados = [];
        foreach($detalles as $detalle){
            $seleccionados[]=$detalle->id_examen;
        }
        
        $seguro = Seguro::find($orden->id_seguro);
        $protocolos = Protocolo::where('estado','1')->get();

        $empresa = Empresa::find($orden->id_empresa);

//dd($orden);
        $arreglo=[];
        $arreglo1=[];
        $nro=0;
        $agrupa_ant=0;
        foreach ($examenes as $examen) {
            if($agrupa_ant!=$examen->id_agrupador){
                $nro=0;
                $agrupa_ant=$examen->id_agrupador;
                $arreglo1=[];
            }
            $arreglo1[$nro]=$examen->id;
            
            $arreglo[$examen->id_agrupador]=$arreglo1;
            $nro++;   
            
        }

        //dd(count($arreglo[3]));

        
        if(!is_null($orden)){
            $tipo_usuario = Auth::user()->id_tipo_usuario;
            //return $tipo_usuario;

            $vistaurl="laboratorio.orden.orden";
            $view =  \View::make($vistaurl, compact('orden', 'usuarios', 'examenes', 'agrupadores', 'detalles', 'empresa', 'seguro', 'protocolos', 'arreglo','seleccionados', 'tipo_usuario'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150]);
            return $pdf->download('orden-de-laboratorio-'.$id.'.pdf'); 
            //return view('laboratorio/orden/orden', ['orden' => $orden,'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresa]);

        }
    }

    public function buscaexamendb($id_orden,$id_examen){

        
        //return $id_examen;

        $id_examen = substr($id_examen,2);

        

        $examen = Examen_Detalle::where('id_examen_orden',$id_orden)->where('id_examen',$id_examen)->first();

        $examen_pri = Examen::find($id_examen);
        
        //return $examen->id;
        if(!is_null($examen)){
            if($examen_pri->publico_privado=='1'){
                return "no";
            }

            return "ok";
        }

        return "no";
        
        
    }

    public function pentax_semaforo(){

        $fecha = Date('Y-m-d');

        $pentax = DB::table('agenda')->where('agenda.estado','1')->where('agenda.proc_consul','1')->join('pentax','pentax.id_agenda','agenda.id')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->join('users', 'users.id', '=', 'pentax.id_doctor1')->leftJoin('users as u2', 'u2.id', '=', 'pentax.id_doctor2')->leftJoin('users as u3', 'u3.id', '=', 'pentax.id_doctor3')->join('seguros', 'seguros.id', '=', 'pentax.id_seguro')->join('sala', 'sala.id', '=', 'agenda.id_sala')->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','procedimiento.observacion as pobservacion','users.nombre1 as dnombre1','users.apellido1 as dapellido1','users.color as dcolor','u2.nombre1 as d2nombre1','u2.apellido1 as d2apellido1','u3.nombre1 as d3nombre1','u3.apellido1 as d3apellido1','seguros.nombre as snombre','seguros.color as scolor','sala.nombre_sala','pentax.id as pentax','pentax.estado_pentax','pentax.id_doctor1 as pid_doctor1')->whereBetween('agenda.fechaini', [$fecha.' 00:00', $fecha.' 23:59'])->where('sala.id_hospital','2')->orderBy('agenda.fechaini')->get();

        $procedimientos = DB::table('procedimiento')->get();

        $semaforo_controller = new SemaforoController();
        $pentax_pend = $semaforo_controller->Cargar_pendientes($fecha);

        //dd(array_key_exists($pentax['0']->id,$pentax_pend));
        //dd($pentax_pend[$pentax['0']->id]);

        return view('laboratorio/orden/pentax_semaforo',['pentax' => $pentax, 'procedimientos' => $procedimientos, 'pentax_pend' => $pentax_pend]);
    }

    public function estad_mes(){
        
        $estadistico_total=[];
        
        $anio = Date('Y');

        $convenios = DB::table('convenio as c')->join('seguros as s','s.id','c.id_seguro')->join('empresa as e','e.id','c.id_empresa')->select('c.*','s.nombre','e.nombre_corto')->orderBy('c.id')->get();
        
        for($i=1;$i<=12;$i++){

            $ordenes_mes = Examen_Orden::where('anio',$anio)->where('mes',$i)->where('realizado','1')->select('anio','mes')->groupBy('anio','mes');
            if($ordenes_mes->count()>0){
                
                $estadistico_conv = [];
                foreach($convenios as $convenio){

                    $ordenes_mes_convenio = Examen_Orden::where('anio',$anio)->where('mes',$i)->where('realizado','1')->where('id_seguro',$convenio->id_seguro)->where('id_empresa',$convenio->id_empresa)->select('anio','mes','id_seguro','id_empresa')->groupBy('anio','mes','id_seguro','id_empresa');

                    

                        $estadistico_conv[$convenio->id] = ['ordenes' => $ordenes_mes_convenio->count(), 'cantidad' => $ordenes_mes_convenio->sum('cantidad'), 'valor' => $ordenes_mes_convenio->sum('valor')];
                        
                    
                    

                }

                $estadistico_total[$i] = ['mes' => $i, 'ordenes' => $ordenes_mes->count(), 'examenes' => $ordenes_mes->sum('cantidad'), 'valor' =>$ordenes_mes->sum('valor'), 'convenios' => $estadistico_conv];
    
            }
            $ordenes_part = Examen_Orden::where('anio',$anio)->where('mes',$i)->where('realizado','1')->select('anio','mes')->groupBy('anio','mes')->where('id_seguro','1');
            $estad_part[$i] = ['cantidad' => $ordenes_part->sum('cantidad'), 'valor' => $ordenes_part->sum('valor'), 'ordenes' => $ordenes_part->count()]; 
            
        }

        $or_anio = DB::table('examen_orden')
                    ->select('anio')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(valor) as valor')
                    ->orderBy('anio')
                    ->groupBy('anio')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->get();

        $or_anio_tipo = DB::table('examen_orden')
                    ->join('seguros as s','s.id','id_seguro') 
                    ->select('anio','s.tipo')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(valor) as valor')
                    ->orderBy('anio','s.tipo')
                    ->groupBy('anio','s.tipo')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->get();            

        //dd($or_anio_tipo);
        $arr_anio_tipo = null;
        foreach ($or_anio_tipo as $value) {
            $arr_anio_tipo[$value->anio.'-'.$value->tipo] = [$value->cantidad, $value->valor];
        }  
        //dd($arr_anio_tipo);          
                   

        $or_anio_mes = DB::table('examen_orden')
                    ->select('anio', 'mes')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(valor) as valor')
                    ->orderBy('anio', 'mes')
                    ->groupBy('anio', 'mes')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->where('anio','2019')
                    ->get(); 

        $or_anio_mes_tipo = DB::table('examen_orden')
                    ->join('seguros as s','s.id','id_seguro') 
                    ->select('anio', 'mes', 's.tipo')
                    ->selectRaw('count(*) as cantidad')
                    ->selectRaw('sum(valor) as valor')
                    ->orderBy('anio', 'mes' ,'s.tipo')
                    ->groupBy('anio', 'mes' ,'s.tipo')
                    ->where('estado','1')
                    ->where('realizado','1')
                    ->where('anio','2019')
                    ->get(); 

        $arr_aniomes_tipo = null;
        foreach ($or_anio_mes_tipo as $value) {
            $arr_aniomes_tipo[$value->anio.'-'.$value->mes.'-'.$value->tipo] = [$value->cantidad, $value->valor];
        }                                
//dd($arr_anio_tipo);

        return view('laboratorio/estadistico/index',['estadistico_total' => $estadistico_total, 'convenios' => $convenios, 'anio' => $anio, 'estad_part' => $estad_part, 'or_anio' => $or_anio, 'or_anio_mes' => $or_anio_mes, 'arr_anio_tipo' => $arr_anio_tipo, 'arr_aniomes_tipo' => $arr_aniomes_tipo]);
        

    }
    public function estad_examen($mes,$anio){
        
        //dd($mes,$anio);
        $estadistico=[];
        

        $convenios = DB::table('convenio as c')->join('seguros as s','s.id','c.id_seguro')->join('empresa as e','e.id','c.id_empresa')->select('c.*','s.nombre','e.nombre_corto')->orderBy('c.id')->get();

        $examenes = Examen::all();
        //dd($examenes);

        foreach($convenios as $convenio){

            $total_conv[$convenio->id] = ['cantidad' => 0, 'valor' => 0];
            
        }

        $total_cantidad = 0; $total_valor=0; $total_part_cant=0; $total_part_valor=0;

        foreach ($examenes as $examen) {
        
            $ordenes_mes = DB::table('examen_orden as eo')->where('eo.anio',$anio)->where('eo.mes',$mes)->where('realizado','1')->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->where('ed.id_examen',$examen->id)->select('eo.anio','eo.mes','ed.id_examen')->groupBy('eo.anio','eo.mes','ed.id_examen');

            $estadistico_conv = [];

            
            //dd($total_cantidad);

            foreach($convenios as $convenio){

                $ordenes_mes_convenio = DB::table('examen_orden as eo')->where('eo.anio',$anio)->where('eo.mes',$mes)->where('realizado','1')->where('eo.id_seguro',$convenio->id_seguro)->where('eo.id_empresa',$convenio->id_empresa)->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->where('ed.id_examen',$examen->id)->select('eo.anio','eo.mes','ed.id_examen','eo.id_seguro','eo.id_empresa')->groupBy('eo.anio','eo.mes','ed.id_examen','eo.id_seguro','eo.id_empresa');

                

                    $estadistico_conv[$convenio->id] = ['cantidad' => $ordenes_mes_convenio->count(), 'valor' => $ordenes_mes_convenio->sum('ed.valor')];

                    $total_conv[$convenio->id] = ['cantidad' => $total_conv[$convenio->id]['cantidad']+$estadistico_conv[$convenio->id]['cantidad'], 'valor' => $total_conv[$convenio->id]['valor']+$estadistico_conv[$convenio->id]['valor']];

                    

            }

            $ordenes_mes_part = DB::table('examen_orden as eo')->where('eo.anio',$anio)->where('eo.mes',$mes)->where('realizado','1')->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->where('ed.id_examen',$examen->id)->select('eo.anio','eo.mes','ed.id_examen')->groupBy('eo.anio','eo.mes','ed.id_examen')->where('id_seguro','1');

            //$estad_part[$examen->id] = ['cantidad' => $ordenes_mes_part->count(), 'valor' => $ordenes_mes_part->sum('ed.valor')];

            //dd($estad_part);

            $total_cantidad = $total_cantidad + $ordenes_mes->count(); $total_valor = $total_valor + $ordenes_mes->sum('ed.valor');  

            $total_part_cant = $total_part_cant +  $ordenes_mes_part->count(); $total_part_valor = $total_part_valor +  $ordenes_mes_part->sum('ed.valor');         

            $estadistico[$examen->id] = ['examen' => $examen->nombre, 'cantidad' => $ordenes_mes->count(), 'valor' => $ordenes_mes->sum('ed.valor'), 'convenios' => $estadistico_conv, 'cant_part' => $ordenes_mes_part->count(), 'val_part' => $ordenes_mes_part->sum('ed.valor') ];
            //dd($ordenes_mes->get()); 


        }

        //dd($estadistico_conv,$total_cantidad);

       
        //dd($estadistico);
        

        return view('laboratorio/estadistico/index_examen',['estadistico' => $estadistico, 'convenios' => $convenios, 'mes' => $mes, 'anio' => $anio, 'total_conv' => $total_conv, 'total_cantidad' => $total_cantidad, 'total_valor' => $total_valor, 'total_part_cant' => $total_part_cant, 'total_part_valor' => $total_part_valor  ]);
        

    }
    
    public function to_excel($mes,$anio) {

        
        //dd($mes,$anio);
        $estadistico=[];
        

        $convenios = DB::table('convenio as c')->join('seguros as s','s.id','c.id_seguro')->join('empresa as e','e.id','c.id_empresa')->select('c.*','s.nombre','e.nombre_corto')->orderBy('id_seguro')->get();

        $examenes = Examen::where('publico_privado','0')->get();
        //dd($examenes);

        foreach($convenios as $convenio){

            $total_conv[$convenio->id] = ['cantidad' => 0, 'valor' => 0];
            
        }

        $total_cantidad = 0; $total_valor=0;

        foreach ($examenes as $examen) {
        
            $ordenes_mes = DB::table('examen_orden as eo')->where('eo.anio',$anio)->where('eo.mes',$mes)->where('realizado','1')->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->where('ed.id_examen',$examen->id)->select('eo.anio','eo.mes','ed.id_examen')->groupBy('eo.anio','eo.mes','ed.id_examen');

            $estadistico_conv = [];

            
            //dd($total_cantidad);

            foreach($convenios as $convenio){

                $ordenes_mes_convenio = DB::table('examen_orden as eo')->where('eo.anio',$anio)->where('eo.mes',$mes)->where('realizado','1')->where('eo.id_seguro',$convenio->id_seguro)->where('eo.id_empresa',$convenio->id_empresa)->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->where('ed.id_examen',$examen->id)->select('eo.anio','eo.mes','ed.id_examen','eo.id_seguro','eo.id_empresa')->groupBy('eo.anio','eo.mes','ed.id_examen','eo.id_seguro','eo.id_empresa');

                

                    $estadistico_conv[$convenio->id] = ['cantidad' => $ordenes_mes_convenio->count(), 'valor' => $ordenes_mes_convenio->sum('ed.valor')];

                    $total_conv[$convenio->id] = ['cantidad' => $total_conv[$convenio->id]['cantidad']+$estadistico_conv[$convenio->id]['cantidad'], 'valor' => $total_conv[$convenio->id]['valor']+$estadistico_conv[$convenio->id]['valor']];

                    

            }

            $total_cantidad = $total_cantidad + $ordenes_mes->count(); $total_valor = $total_valor + $ordenes_mes->sum('ed.valor');            

            $estadistico[$examen->id] = ['examen' => $examen->nombre, 'cantidad' => $ordenes_mes->count(), 'valor' => $ordenes_mes->sum('ed.valor'), 'convenios' => $estadistico_conv ];
            //dd($ordenes_mes->get()); 


        }

        $fecha_d = date('Y/m/d');
        
        Excel::create('Examenes_Mes-'.$fecha_d, function($excel) use( $mes, $anio, $convenios, $estadistico, $total_conv, $total_cantidad, $total_valor) {

            $excel->sheet('Examenes', function($sheet) use($mes, $anio, $convenios, $estadistico, $total_conv, $total_cantidad, $total_valor) {

                $a_mes = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                
                $sheet->mergeCells('A3:P3'); 
                
                
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                
                
                $sheet->cells('A1:P5', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:P5', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function($cell) use($mes, $anio, $a_mes) {
                    // manipulate the cel
                    $cell->setValue('ESTADISTICO DE EXAMENES DE '.$a_mes[$mes].'/'.$anio);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:I4'); 
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setAlignment('center');
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J4:P4'); 
                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setAlignment('center');
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $letras = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'];
                $l=2;

                foreach($convenios as $convenio){
                    $sheet->cell($letras[$l].'5', function($cell) use($convenio) {
                        // manipulate the cel
                        $cell->setValue($convenio->nombre.' '.$convenio->nombre_corto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $l++;
                }
                $sheet->cell($letras[$l].'5', function($cell) use($convenio) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $l++;
                foreach($convenios as $convenio){
                    $sheet->cell($letras[$l].'5', function($cell) use($convenio) {
                        // manipulate the cel
                        $cell->setValue($convenio->nombre.' '.$convenio->nombre_corto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $l++;
                }
                $sheet->cell($letras[$l].'5', function($cell) use($convenio) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $l++;

                $i=6;
                $x=1;
                foreach ($estadistico as $value){
                    if($value['cantidad']>0){
                        $sheet->cell('A'.$i, function($cell) use($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('B'.$i, function($cell) use($value) {
                            // manipulate the cel
                            $cell->setValue($value['examen']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontWeight('bold');
                        });
                        $l=2;

                        foreach($value['convenios'] as $convenio){
                            $sheet->cell($letras[$l].$i, function($cell) use($convenio) {
                                // manipulate the cel
                                $cell->setValue($convenio['cantidad']);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $l++;
                        }
                        $sheet->cell($letras[$l].$i, function($cell) use($convenio, $value) {
                            // manipulate the cel
                            $cell->setBackground('#FFE4E1');
                            $cell->setValue($value['cantidad']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $l++;
                        foreach($value['convenios'] as $convenio){
                            $sheet->cell($letras[$l].$i, function($cell) use($convenio) {
                                // manipulate the cel
                                $cell->setValue(round($convenio['valor'],2));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $l++;
                        }
                        $sheet->cell($letras[$l].$i, function($cell) use($convenio, $value) {
                            // manipulate the cel
                            $cell->setBackground('#FFE4E1');
                            $cell->setValue(round($value['valor'],2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $l++;

                        $i++;
                        $x++;
                    }
                    $sheet->cell('A'.$i, function($cell) use($value, $x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue('TOTAL');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                    $l=2;
                    foreach($total_conv as $convenio){
                        $sheet->cell($letras[$l].$i, function($cell) use($convenio) {
                            // manipulate the cel
                            $cell->setValue($convenio['cantidad']);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontWeight('bold');
                        });
                        $l++;
                    }
                    $sheet->cell($letras[$l].$i, function($cell) use($convenio, $total_cantidad) {
                        // manipulate the cel
                        $cell->setBackground('#FFE4E1');
                        $cell->setValue($total_cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                    $l++;
                    foreach($total_conv as $convenio){
                        $sheet->cell($letras[$l].$i, function($cell) use($convenio) {
                            // manipulate the cel
                            $cell->setValue(round($convenio['valor'],2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontWeight('bold');
                        });
                        $l++;
                    }
                    $sheet->cell($letras[$l].$i, function($cell) use($convenio, $total_valor) {
                        // manipulate the cel
                        $cell->setBackground('#FFE4E1');
                        $cell->setValue(round($total_valor,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontWeight('bold');
                    });
                }
                
                
                

                
                   
                    
            });
        })->export('xlsx');
    }

    public function genera_costo(){

        if($this->rol_sis()){
            return response()->view('errors.404');
        }

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil'); 

        $detalles = Examen_detalle::all();
        //dd($detalles);
        foreach($detalles as $detalle){
            $examen = Examen::find($detalle->id_examen);
            //dd($examen->valor_reactivo);
            $input = [

                'id_examen_detalle' => $detalle->id,
                'id_examen' => $detalle->id_examen,
                'valor_reactivo' => $examen->valor_reactivo,
                'valor_implemento' => $examen->valor_implementos,
                'id_usuariocrea' => $idusuario,   
                'id_usuariomod' => $idusuario,
                'ip_creacion'   => $ip_cliente,
                'ip_modificacion' => $ip_cliente

                ];
            $costo = Examen_Detalle_Costo::where('id_examen_detalle',$detalle->id)->first();
            if(is_null($costo)){
                Examen_Detalle_Costo::create($input);
            }    
        }
        return "CARGADO";
    }

    
    public function imprimir_resultado2($id){


        $orden = Examen_Orden::find($id);
        $detalle = Examen_Detalle::where('id_examen_orden', $id)->get();
        $resultados =  Examen_resultado::where('id_orden', '=', $id)->get();
        $agrupador = Examen_Agrupador::all();
        $parametros = Examen_parametro::all();
        $ucreador = User::find($orden->id_usuariocrea);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl="laboratorio.orden.resultados_pdf2";
        $view =  \View::make($vistaurl, compact('orden', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        

        //dd($pdf);
        
        return $pdf->download('resultado-'.$id.'.pdf'); 

    } 

    public function imprimir_resultado3($id){ //FORMATO GASTROCLINICA


        $orden = Examen_Orden::find($id);
        //$detalle = Examen_Detalle::where('id_examen_orden', $id)->get();
        $detalle = Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
        $resultados =  Examen_resultado::where('id_orden', '=', $id)->get();
        $agrupador = Examen_Agrupador::all();
        $parametros = Examen_parametro::all();
        $ucreador = User::find($orden->id_usuariocrea);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl="laboratorio.orden.resultados_pdf_gastro";
        $view =  \View::make($vistaurl, compact('orden', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        

        //dd($pdf);
        

        

        return $pdf->download('resultado-'.$id.'.pdf'); 

    } 

    public function convenio_buscar($seguro, $empresa){

        $convenio = Convenio::where('id_seguro',$seguro)->where('id_empresa',$empresa)->first();
        if(!is_null($convenio)){
            return $convenio->id_nivel;
        }

        return "no";
    } 

    public function convenio_buscar_examen($nivel, $examen){


        
        $ex_nivel = Examen_Nivel::where('id_examen',$examen)->where('nivel',$nivel)->first();
        if(!is_null($ex_nivel)){
            return "ok";
        }

        return "no";
    }   

    public function detalle_valor($id){

        if($this->rol_supervision()){
            return response()->view('errors.404');
        }
                
        $detalles = Examen_Detalle::where('id_examen_orden',$id)->get();

        //dd($detalles['0']->examen->descripcion);
        
        
        
        return view('laboratorio/orden/detalle_valor', ['detalles' => $detalles]);

    }  

    public function carga_totales(){

        if($this->rol_sis()){
            return response()->view('errors.404');
        }

        $ordenes = Examen_Orden::where('fecha_orden',null)->get();
        //dd($ordenes);
        foreach ($ordenes as $value) {
            
            $input = [
                'fecha_orden' => $value->created_at,
                
                'ip_modificacion' => 'AUTOM_FO'

            ];

            $value->update($input);

        }
        return "listo";
        

    }

    /*public function carga_totales(){

        if($this->rol_sis()){
            return response()->view('errors.404');
        }

        $ordenes = Examen_Orden::where('total_valor','0')->get();
        //dd($ordenes);
        foreach ($ordenes as $value) {
            $valor_descuento = 0;
            $sub_total_des;

            $valor_descuento = $value->valor * $value->descuento_p / 100;
            //dd($valor_descuento);
            $valor_descuento = round($valor_descuento,2);

            $sub_total_des = $value->valor - $valor_descuento;
            //dd($sub_total_des);
            $forma_pago = Forma_de_pago::where('id',$value->id_forma_de_pago)->first();
            if(is_null($forma_pago)){
                $porcentaje_recargo = 0;
            }else{
                $porcentaje_recargo = $forma_pago->recargo_p;
            }
            $valor_recargo = $sub_total_des * $porcentaje_recargo / 100;
            $valor_recargo = round($valor_recargo,2);
            $valor_total =  $sub_total_des + $valor_recargo;
            //dd($valor_total);
            $input = [
                'recargo_p' => $porcentaje_recargo,
                'recargo_valor' => $valor_recargo,
                'descuento_valor' => $valor_descuento,
                'total_valor' => $valor_total,
                'ip_modificacion' => 'AUTOM'

            ];

            $value->update($input);

        }
        return "listo";
        

    }*/

    public function validacion (Request $request){

        if($request->id==null){
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if($request->nombre1==null){
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if($request->apellido1==null){
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if($request->sexo==null){
            return "<h5 style='color: #ff6600;'>INGRESE EL SEXO</h5>";
        }
        if($request->fecha_nacimiento==null){
            return "<h5 style='color: #ff6600;'>INGRESE LA FECHA DE NACIMIENTO</h5>";
        }
        if($request->id_doctor_ieced==null){
            return "<h5 style='color: #ff6600;'>INGRESE EL MEDICO</h5>";
        }

        if($request->id_seguro!=null){
            $seguro = Seguro::find($request->id_seguro);
            $convenio = Convenio::where('id_seguro',$request->id_seguro)->where('id_empresa',$request->id_empresa)->get();
            if($convenio->count()>1){
                if($request->id_nivel==null){
                    //return $request->all();
                    return "<h5 style='color: #ff6600;'>SELECCIONE EL NIVEL PARA REALIZAR LA COTIZACIÓN</h5>";
                }    
            }
               
        }else{

            return "<h5 style='color: #ff6600;'>SELECCIONE EL SEGURO PARA REALIZAR LA COTIZACIÓN</h5>";    
        
        }

        if($request->descuento_p>0){
            if($request->motivo_descuento==null){
                return "<h5 style='color: #ff6600;'>INGRESE QUIEN AUTORIZA EL DESCUENTO Y POR QUE MEDIO LO REALIZA</h5>";
            }
            
        }

        return "OK";

    }

    public function agrupador_labs_buscar(Request $request){
        
        if($this->validacion($request)!='OK'){
            return $this->validacion($request);
        }
        
        //return $request->all();
        $agrupador_labs = DB::table('examen_agrupador_labs');
        $examenes_labs = DB::table('examen_agrupador_sabana as sa')->join('examen as e','e.id','sa.id_examen')->where('e.estado','1')->select('sa.*','e.descripcion','e.nombre','e.valor','e.id as ex_id');
       
        

        $seguro = Seguro::find($request->id_seguro);
        $convenio = DB::table('convenio as c')->where('c.id_seguro',$seguro->id)->get();

        if($request->buscador!=null){
            $examenes_labs = $examenes_labs->where('e.descripcion','like','%'.$request->buscador.'%');   
        }

        if($request->buscador2!=null){
            $agrupador_labs = $agrupador_labs->where('nombre','like','%'.$request->buscador2.'%');   
        }

        $examenes_labs = $examenes_labs->orderBy('sa.nro_orden')->get();
        $agrupador_labs = $agrupador_labs->get();

        $detalles_ch = [];
        $i=0;
        /*foreach ($examenes_labs as $examen) {
            $detalle = Examen_Detalle::where('id_examen_orden',$request->cotizacion)->where('id_examen',$examen->ex_id)->first();
            if(!is_null($detalle)){
                $detalles_ch[$i] = $examen->ex_id; 
                $i = $i + 1;  
            }
            
        }*/

        $nuevo_detalles = Examen_Detalle::where('id_examen_orden',$request->cotizacion)->get();
        foreach ($nuevo_detalles as $nuevo_detalle) {
            $detalles_ch[$i] = $nuevo_detalle->id_examen; 
            $i = $i + 1;       
        } 

        $orden = Examen_Orden::find($request->cotizacion);

        //return $detalles_ch;
        

        return view('laboratorio.orden.listado',[ 'agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'seguro' => $seguro, 'id_nivel' => $request->id_nivel, 'cotizacion' =>$request->cotizacion, 'detalles_ch' => $detalles_ch, 'orden' => $orden]); 

    }

    

    public function agrupador_labs_nivel(Request $request){
        
        
        $convenios = DB::table('convenio as c')->where('c.id_seguro',$request->id_seguro)->join('nivel as n','n.id','c.id_nivel')->select('c.*','n.nombre','n.id as id_nivel')->get();

        $orden = null;
        if($request->cotizacion!=null){
            $orden = Examen_Orden::find($request->cotizacion);
        }

        if($convenios->count()>0){
             return view('laboratorio.orden.niveles',[ 'convenios' => $convenios, 'orden' => $orden]);
        }

        return "no";
        
       

        

    }

    public function cotizador_store(Request $request)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        if($this->validacion($request)!='OK'){
            return $this->validacion($request);
        }

        if($request->id_nivel==null){
            $convenio = Convenio::where('id_seguro',$request->id_seguro)->where('id_empresa',$request->id_empresa)->first();
        }else{
            $convenio = Convenio::where('id_seguro',$request->id_seguro)->where('id_empresa',$request->id_empresa)->where('id_nivel',$request->id_nivel)->first();    
        }
        
        $seguro = Seguro::find($request->id_seguro);
        
        //CREAR USUARIO
        $input_usu_c = [

            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1' => '1',
            'telefono2' => '1',
            'id_tipo_usuario' => 2,
            'email' => $request['id'].'@mail.com',
            'password' => bcrypt($request['id']),
            'tipo_documento' => 1,
            'estado' => 1,
            'imagen_url' => ' ',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario    

            ];

        
        $user = User::find($request['id']); 
                  

        $input_pac = [

            'id' => $request['id'],
            'id_usuario' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'sexo' => $request['sexo'],
            'telefono1' => '1',
            'telefono2' => '1',
            'nombre1familiar' => strtoupper($request['nombre1']),
            'nombre2familiar' => strtoupper($request['nombre2']),
            'apellido1familiar' => strtoupper($request['apellido1']),
            'apellido2familiar' => strtoupper($request['apellido2']),
            'parentesco' => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento' => 1,
            'id_seguro' => 1,
            'imagen_url' => ' ',
            'menoredad' => 0,
                
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

            ];   
     
        
        $paciente=Paciente::find($request['id']);

        if(is_null($paciente)){

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            }else{
                User::create($input_usu_c);
            } 
                    
            paciente::create($input_pac);

            $input_log = [
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "CREA NUEVO PACIENTE",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => "COTIZACION",
            'dato2' => 'COTIZACION',
            ]; 

            Log_usuario::create($input_log);     
        }else{
            if($paciente->fecha_nacimiento==null||$paciente->sexo==null){

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo' => $request['sexo'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
                $paciente->update($pac);

            }
        } 
        
        $nivel = null;
        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::all();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        //dd($nivel);

        

        //dd($input_ex);

        if($request->cotizacion!=null){
            $input_ex = [
                
                'id_seguro' => $request['id_seguro'],
                'id_nivel' => $request->id_nivel,
                'est_amb_hos' => $request['est_amb_hos'],
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt' => $request['doctor_txt'],
                'observacion' => $request['observacion'],
                'id_empresa' => $request['id_empresa'],
                'cantidad' => $cont,
                'estado' => '-1',
                'valor' => $total,
                
                'ip_modificacion' => $ip_cliente,
                
                'id_usuariomod' => $idusuario,
                            
            ];
            Examen_Orden::find($request->cotizacion)->update($input_ex);
            $id_examen_orden = $request->cotizacion;
        }else{
            $input_ex = [
                'id_paciente' => $request['id'],
                'anio' => substr(date('Y-m-d'),0,4),
                'mes' => substr(date('Y-m-d'),5,2),
                'id_protocolo' => $request['id_protocolo'],
                'id_seguro' => $request['id_seguro'],
                'id_nivel' => $request->id_nivel,
                'est_amb_hos' => $request['est_amb_hos'],
                'id_doctor_ieced' => $request['id_doctor_ieced'],
                'doctor_txt' => $request['doctor_txt'],
                'observacion' => $request['observacion'],
                'id_empresa' => $request['id_empresa'],
                'cantidad' => $cont,
                'estado' => '-1',
                'valor' => $total,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                            
            ];
            $id_examen_orden = Examen_Orden::insertGetId($input_ex);    
        }
        
        $detalles_cotizacion = Examen_detalle::where('id_examen_orden',$request->cotizacion)->get();
        foreach ($detalles_cotizacion as $value) {
            $value->delete();
        }
       
   
        $valor=0;
        $cont=0;
        $examen_nombre="";
        $examenes = Examen::all();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                
                $valor = $examen->valor;
                $cubre ='';
                if($request->id_seguro!='1'){
                    $cubre ='NO';
                }
                $examen_nombre=$examen_nombre."+".$examen->nombre;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                        $cubre ='SI';
                    }
                }
                
                

                $cont++;
                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen' => $examen->id,
                    'valor' => $valor,
                    'cubre' => $cubre,   
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);
            }
        }

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERA COTIZACION",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => $examen_nombre,
                ]);    
                
                    
        
        return "ok";

    }

    public function cotizador_recalcular(Request $request)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        if($this->validacion($request)!='OK'){
            return $this->validacion($request);
        }
                  
        $orden = Examen_Orden::find($request->cotizacion);
        
        $cambio_paciente = false;
        if($orden->paciente->sexo != $request->sexo){
            $cambio_paciente = true;
        }
        if($orden->paciente->fecha_nacimiento != $request->fecha_nacimiento){
            $cambio_paciente = true;
        }
        if($orden->paciente->id_seguro != $request->id_seguro){
            $cambio_paciente = true;
        }

        if($cambio_paciente){

            $input_log = [
                    'id_usuario' => $idusuario,
                    'ip_usuario' => $ip_cliente,
                    'descripcion' => "ACTUALIZA NUEVO PACIENTE COTIZACION",
                    'dato_ant1' => $orden->id_paciente,
                    'dato_ant4' => 'sexo: '.$orden->paciente->sexo.' fecha nacimiento: '.$orden->paciente->fecha_nacimiento.' seguro: '.$orden->paciente->id_seguro,
                    'dato4' => 'sexo: '.$request->sexo.' fecha nacimiento: '.$request->fecha_nacimiento.' seguro: '.$request->id_seguro,
                    
                ]; 

            Log_usuario::create($input_log); 

            $paciente=Paciente::find($orden->id_paciente);
            $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo' => $request['sexo'],
                    'id_seguro' => $request['id_seguro'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
                $paciente->update($pac);   

        }

        //RECALCULAR
        $total = 0;
        $cantidad = 0;
        $detalles = Examen_Detalle::where('id_examen_orden',$request->cotizacion)->get();
        foreach ($detalles as $detalle) {
            $cantidad = $cantidad + 1;
            $examen = Examen::find($detalle->id_examen);    
            $valor = $examen->valor;
            $cubre = 'NO';
            $ex_nivel = Examen_Nivel::where('id_examen',$detalle->id_examen)->where('nivel',$request->id_nivel)->first();
            if(!is_null($ex_nivel)){
                if($ex_nivel->valor1!=0){

                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';

                }
            }

            $input_det = [
                
                'valor' => $valor,
                'cubre' => $cubre,   
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                    
            ]; 

            $detalle->update($input_det);
            $total = $total + $valor;
            
        }
        $total = round($total,2); 
        $descuento_p = $request->descuento_p;
        if($request->descuento_p==null){
            $descuento_valor = 0;
            $descuento_p = 0;
        }
        //return $request->all();

        if($request->descuento_p>100){
            $descuento_valor = $total;
        }else{
            $descuento_valor = $request->descuento_p * $total/100; 
            $descuento_valor = round($descuento_valor,2);   
        }
        
        $subtotal_pagar = $total - $descuento_valor;

        $recargo_p = DB::table('forma_de_pago')->where('id',$request->id_forma_pago)->first()->recargo_p;

        $recargo_valor = $subtotal_pagar * $recargo_p/100;
        $recargo_valor = round($recargo_valor,2);
        $valor_total = $subtotal_pagar + $recargo_valor;
        $valor_total = round($valor_total,2);

        //ACTUALIZAR ORDEN
        $input_ex = [
            'motivo_descuento' => $request->motivo_descuento,
            'id_forma_de_pago' => $request->id_forma_pago,
            'descuento_p' => $descuento_p,
            'descuento_valor' => $descuento_valor,
            'recargo_p' =>  $recargo_p,
            'recargo_valor' =>  $recargo_valor,
            'total_valor' => $valor_total,  
            'id_seguro' => $request['id_seguro'],
            'id_nivel' => $request->id_nivel,
            'est_amb_hos' => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'id_empresa' => $request['id_empresa'],
            'cantidad' => $cantidad,
            
            'valor' => $total,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
                        
        ];
        $orden->update($input_ex);
        

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA COTIZACION X SEGURO O NIVEL",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            
                ]);    
                
                    
        
        return "ok";

    }

    public function cotizador_update($cotizacion, $id)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Examen_Orden::find($cotizacion);
        
        $detalle = Examen_Detalle::where('id_examen_orden',$cotizacion)->where('id_examen',$id)->first();
        if(is_null($detalle)){
            
            
            $examen =  Examen::find($id);
            $valor = $examen->valor;
            $cubre = 'NO';
            $ex_nivel = Examen_Nivel::where('id_examen',$id)->where('nivel',$orden->id_nivel)->first();
            if(!is_null($ex_nivel)){
                if($ex_nivel->valor1!=0){

                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';

                }
            }

            $input_det = [
                'id_examen_orden' => $orden->id,
                'id_examen' => $id,
                'valor' => $valor,
                'cubre' => $cubre,   
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                    
            ]; 

            Examen_detalle::create($input_det);

            /*$input_ex = [
                
                'cantidad' => $orden->cantidad + 1,
                'valor' => $orden->valor + $valor,

                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];
            $orden->update($input_ex);

            Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA COTIZACION",
            'dato_ant1' => $orden->id,
            'dato1' => $orden->id_paciente,
            'dato_ant4' => $id,
                ]);  */  

            /// orden
            $total = $orden->valor + $valor;
            $total = round($total,2);     
            $descuento_p = $orden->descuento_p;
            if($orden->descuento_p==null){
                $descuento_valor = 0;
                $descuento_p = 0;
            }
            

            if($orden->descuento_p>100){
                $descuento_valor = $total;
            }else{
                $descuento_valor = $orden->descuento_p * $total/100;
                $descuento_valor = round($descuento_valor,2);     
            }
            
            $subtotal_pagar = $total - $descuento_valor;

            $recargo_p = $orden->recargo_p;

            $recargo_valor = $subtotal_pagar * $recargo_p/100;
            $recargo_valor = round($recargo_valor,2);
            $valor_total = $subtotal_pagar + $recargo_valor;
            $valor_total = round($valor_total,2);

            //ACTUALIZAR ORDEN
            $input_ex = [
                
                'descuento_valor' => $descuento_valor,
                'recargo_valor' =>  $recargo_valor,
                'total_valor' => $valor_total,  
                'cantidad' => $orden->cantidad + 1,
                'valor' => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $orden->update($input_ex);

            Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA COTIZACION",
            'dato_ant1' => $orden->id,
            'dato1' => $orden->id_paciente,
            'dato_ant4' => $id,
                ]);
            ///    
                
        } 


        
                    
        
        return ['cantidad' => $orden->cantidad, 'valor' => $orden->valor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $orden->total_valor];

    }

    public function cotizador_delete($cotizacion, $id)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $detalle = Examen_Detalle::where('id_examen_orden',$cotizacion)->where('id_examen',$id)->first();
        if(!is_null($detalle)){

            $orden = Examen_Orden::find($cotizacion);
            
            /*$input_ex = [
                
                'cantidad' => $orden->cantidad - 1,
                'valor' => $orden->valor - $detalle->valor,

                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];
            $orden->update($input_ex);*/
            
            

            /*Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA COTIZACION",
            'dato_ant1' => $orden->id,
            'dato1' => $orden->id_paciente,
            'dato_ant4' => $id,
                ]);*/ 

            /// orden
            $total = $orden->valor - $detalle->valor;
            $total = round($total,2);  
            if($total<0){

                $total = 0;
            }   
            $descuento_p = $orden->descuento_p;
            if($orden->descuento_p==null){
                $descuento_valor = 0;
                $descuento_p = 0;
            }
            

            if($orden->descuento_p>100){
                $descuento_valor = $total;
            }else{
                $descuento_valor = $orden->descuento_p * $total/100; 
                $descuento_valor = round($descuento_valor,2);   
            }
            
            $subtotal_pagar = $total - $descuento_valor;

            $recargo_p = $orden->recargo_p;

            $recargo_valor = $subtotal_pagar * $recargo_p/100;
            $recargo_valor = round($recargo_valor,2);
            $valor_total = $subtotal_pagar + $recargo_valor;
            $valor_total = round($valor_total,2);

            //ACTUALIZAR ORDEN
            $input_ex = [
                
                'descuento_valor' => $descuento_valor,
                'recargo_valor' =>  $recargo_valor,
                'total_valor' => $valor_total,  
                'cantidad' => $orden->cantidad - 1,
                'valor' => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                            
            ];

            $orden->update($input_ex);

            Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA COTIZACION",
            'dato_ant1' => $orden->id,
            'dato1' => $orden->id_paciente,
            'dato_ant4' => $id,
                ]);
            ///    
            
            $detalle->delete();      
            

            
        } 


           
                
                    
        
        return ['cantidad' => $orden->cantidad, 'valor' => $orden->valor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $orden->total_valor];

    }

    public function crear_cabecera(Request $request)
    {
        

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $reglas = [

            'id' => 'required',
            'nombre1' => 'required',
            //'nombre2' => 'required',
            'apellido1' => 'required',
            //'apellido2' => 'required',
            'sexo' => 'required',
            'fecha_nacimiento' => 'required',
            'id_doctor_ieced' => 'required',
            'id_seguro' => 'required',

        ];

        $mensaje =  [
        
            'id.required' => 'Ingrese la cédula del paciente',
            'nombre1.required' => 'Ingrese el nombre',
            'apellido1.required' => 'Ingrese el apellido',
            'sexo.required' => 'Seleccione el sexo',
            'fecha_nacimiento.required' => 'Ingrese la fecha de nacimiento',
            'id_doctor_ieced.required' => 'Seleccione el Doctor',
            'id_seguro.required' => 'Seleccione el seguro',
        ];

        $this->validate($request,$reglas,$mensaje);    

        if($request->id_nivel==null){
            $convenio = Convenio::where('id_seguro',$request->id_seguro)->where('id_empresa',$request->id_empresa)->first();
        }else{
            $convenio = Convenio::where('id_seguro',$request->id_seguro)->where('id_empresa',$request->id_empresa)->where('id_nivel',$request->id_nivel)->first();    
        }
        
        $seguro = Seguro::find($request->id_seguro);
        
        //CREAR USUARIO
        $input_usu_c = [

            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'telefono1' => '1',
            'telefono2' => '1',
            'id_tipo_usuario' => 2,
            'email' => $request['id'].'@mail.com',
            'password' => bcrypt($request['id']),
            'tipo_documento' => 1,
            'estado' => 1,
            'imagen_url' => ' ',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario    

            ];

        
        $user = User::find($request['id']); 
                  

        $input_pac = [

            'id' => $request['id'],
            'id_usuario' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'sexo' => $request['sexo'],
            'telefono1' => '1',
            'telefono2' => '1',
            'nombre1familiar' => strtoupper($request['nombre1']),
            'nombre2familiar' => strtoupper($request['nombre2']),
            'apellido1familiar' => strtoupper($request['apellido1']),
            'apellido2familiar' => strtoupper($request['apellido2']),
            'parentesco' => 'Principal',
            'parentescofamiliar' => 'Principal',
            'tipo_documento' => 1,
            'id_seguro' => 1,
            'imagen_url' => ' ',
            'menoredad' => 0,
                
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

            ];   
     
        
        $paciente=Paciente::find($request['id']);

        if(is_null($paciente)){

            if (!is_null($user)) {
                //$user->update($input_usu_a);
            }else{
                User::create($input_usu_c);
            } 
                    
            paciente::create($input_pac);

            $input_log = [
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "CREA NUEVO PACIENTE",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            'dato_ant4' => "COTIZACION",
            'dato2' => 'COTIZACION',
            ]; 

            Log_usuario::create($input_log);     
        }else{
            if($paciente->fecha_nacimiento==null||$paciente->sexo==null){

                $pac = [
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'sexo' => $request['sexo'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
                $paciente->update($pac);

            }
        } 
        
        $nivel = null;
        $valor=0;
        $cont=0;
        $total=0;
        $examenes = Examen::all();
        foreach ($examenes as $examen) {
            if(!is_null($request['ch'.$examen->id])){
                $cont++;
                
                $valor = $examen->valor;
                if(!is_null($convenio)){
                    $nivel = $convenio->id_nivel;
                    //dd($nivel);
                    $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$nivel)->first();
                    if(!is_null($ex_nivel)){
                        $valor = $ex_nivel->valor1;
                    }
                }
                $total = $total + $valor;
            }
        }

        
        $input_ex = [
            'id_paciente' => $request['id'],
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            'id_protocolo' => $request['id_protocolo'],
            'id_seguro' => $request['id_seguro'],
            'id_nivel' => $request['id_nivel'],
            //'id_forma_de_pago' => $request['id_forma_pago'],
            'est_amb_hos' => $request['est_amb_hos'],
            'id_doctor_ieced' => $request['id_doctor_ieced'],
            'doctor_txt' => $request['doctor_txt'],
            'observacion' => $request['observacion'],
            'id_empresa' => $request['id_empresa'],
            'cantidad' => $cont,
            'estado' => '-1',
            'valor' => $total,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'motivo_descuento' => $request['motivo_descuento'],
            'fecha_orden' => date('Y-m-d h:i:s'),
                        
        ];
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);    

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERA COTIZACION",
            'dato_ant1' => $request['id'],
            'dato1' => strtoupper($request['nombre1']." ".$request['nombre2']." ".$request['apellido1']." ".$request['apellido2']),
            
                ]);    


        return redirect()->route('cotizador.editar',['id' => $id_examen_orden]);

    }

    public function cotizador_editar($id){
        if($this->rol()){
            return response()->view('errors.404');
        }

        $orden = Examen_Orden::find($id);
        
        $usuarios = User::where('id_tipo_usuario','3')->where('estado','1')->get();

        $formas = DB::table('forma_de_pago')->where('estado','1')->get();
        //dd($formas);
        
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        $seguros1 = DB::table('seguros as s')->where('s.inactivo','1')->where('s.tipo','>','1');
        $seguros2 = DB::table('seguros as s')->where('s.inactivo','1')->where('s.tipo','>','0')->join('convenio as c','c.id_seguro','s.id')->select('s.*');
        $seguros = $seguros1->union($seguros2)->get();
        $protocolos = Protocolo::where('estado','1')->get();
        

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c','c.id_empresa','e.id')->select('e.id','e.nombrecomercial')->groupBy('e.id','e.nombrecomercial')->get();
        
        /*return view('laboratorio/orden/create_particular',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/
//dd($orden);
        return view('laboratorio/orden/editar_cotizacion',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'formas' => $formas, 'protocolos' => $protocolos]);

    }

    public function cotizador_imprimir($id)
    {
    
        $orden = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen_agrupador_sabana as es','es.id_examen','ed.id_examen')->select('ed.*','e.descripcion')->join('examen as e','e.id','ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        
        $view =  \View::make('laboratorio.orden.cotizacion_pdf', compact('orden','detalles','age'))->render();
        $pdf = \App::make('dompdf.wrapper');

        
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        
        
        
        return $pdf->stream('cotizador-'.$id.'.pdf');
    }

    
    public function cotizador_orden_imprimir($id)
    {
    
        $orden = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen_agrupador_sabana as es','es.id_examen','ed.id_examen')->select('ed.*','e.descripcion')->join('examen as e','e.id','ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        
        $view =  \View::make('laboratorio.orden.cotizacion_orden_pdf', compact('orden','detalles','age'))->render();
        $pdf = \App::make('dompdf.wrapper');

        
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        
        
        
        return $pdf->stream('cotizador-'.$id.'.pdf');
    }

    public function cotizador_imprimir_gastro($id)
    {
    
        $orden = Examen_Orden::find($id);
        $detalles = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen_agrupador_sabana as es','es.id_examen','ed.id_examen')->select('ed.*','e.descripcion')->join('examen as e','e.id','ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        
        $view =  \View::make('laboratorio.orden.cotizacion_pdf_gas', compact('orden','detalles','age'))->render();
        $pdf = \App::make('dompdf.wrapper');

        
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        
        
        
        return $pdf->stream('cotizador-'.$id.'.pdf');
    }

    public function cotizador_generar($id)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $detalles = Examen_Detalle::where('id_examen_orden',$id)->get();
        
        if($detalles->count()=='0'){

            return redirect()->route('cotizador.editar',['id' => $id])->withInput(['mensaje' => 'Cotización sin exámenes']);
        }
        
        $orden = Examen_Orden::find($id);
        //ACTUALIZAR ORDEN
        $input_ex = [
            'estado' => '1',
            'fecha_orden' => date('Y-m-d h:i:s'),
            'realizado' => '1',
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
                        
        ];
        $orden->update($input_ex);
        

        Log_usuario::create([
            'id_usuario' => $idusuario,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERAR ORDEN DE LABORATORIO",
            'dato_ant1' => $orden->id,
            'dato1' => $orden->id_paciente,
            
            
                ]);    
                
                    
        
        return redirect()->route('orden.index');
    }

    public function subresultado_crear($id_orden, $id_examen){
        $examen = Examen::find($id_examen);
        $orden = Examen_Orden::find($id_orden);
        
        return view('laboratorio.orden.modal_sub', ['examen' => $examen, 'orden' => $orden]);
    }

    public function subresultado_store(Request $request){
        //return $request;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil'); 
        $valor1 = $request['valor1'];
        $valor2 = $request['valor2'];
        $valor3 = $request['valor3'];
        $id_orden = $request['id_orden'];
        $id_examen = $request['id_examen'];
        
        $rules = [
            'valor1' => 'required',
            'valor2' => 'required',
            'valor3' => 'required'
        ];
        $msg = [
            'valor1.required' => 'Ingrese Valor',
            'valor2.required' => 'Ingrese Valor',
            'valor3.required' => 'Ingrese Valor',
        ];
        $this->validate($request,$rules,$msg);

        
        $input = [
            'id_orden' => $id_orden,
            'id_examen' => $id_examen,
            'campo1' => $valor1,
            'campo2' => $valor2,
            'campo3' => $valor3,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
                    
        ]; 

        Examen_Sub_Resultado::create($input);
        
        $sub_resultados = Examen_Sub_Resultado::where('id_orden',$id_orden)->where('id_examen',$id_examen)->get();

        return view('laboratorio.orden.sub_lis',['sub_resultados' => $sub_resultados]);
    }

    public function subresultado_eliminar($id){
        //return $id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        
        $subresultado = Examen_Sub_Resultado::find($id);

        if(!is_null($subresultado)){
             $input = [
                'estado' => '0',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                        
            ]; 
            $subresultado->update($input);
        }


        return $id;
    }

    public function index_privado() //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS
    { 
        $opcion = '1'; //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }
        
        $fecha = date('Y/m/d');

        
        $ordenes = $this->recupera_ordenes()->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha.' 23:59'])->where('eo.estado','1')->where('s.tipo','1')->where('eo.realizado','1')->paginate(30);
        //dd($ordenes);
        
        
        $seguros = Seguro::where('inactivo','1')->where('tipo','1')->get();
        
        return view('laboratorio/privado/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha, 'nombres' => null, 'seguro' => null, 'seguros' => $seguros]);
    }

    public function search_privado(Request $request) { //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS

         
        $opcion = '1'; //ORDENES DE LABORATORIO PARA CONVENIOS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $seguro = $request['seguro'];
        $nombres = $request['nombres'];    
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        
        $ordenes = $this->recupera_ordenes();

        if($fecha!=null){

            $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59']);
        
        }  

        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        
        }

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $nombres_sql='';$cantidad = count($nombres2); 
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
             

            }
             
            else{

                $ordenes = $ordenes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }    
  
        }

        //dd($ordenes->get());

        $ordenes = $ordenes->where('eo.estado','1')->where('s.tipo','1')->where('eo.realizado','1')->paginate(30);

        $seguros = Seguro::where('inactivo','1')->where('tipo','1')->get();
        
        return view('laboratorio/privado/index', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'seguro' => $seguro, 'seguros' => $seguros]);

    }

    public function ordenes_rpt(Request $request) {

        
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres = $request['nombres'];
        $seguro = $request['seguro'];
      
        $ordenes = DB::table('examen_orden as eo')->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->leftjoin('forma_de_pago as fp','fp.id','eo.id_forma_de_pago')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','fp.nombre as fpnombre')->where('eo.realizado','1')->where('eo.estado','1')->where('s.tipo','1');

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }
         if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        } 

        $ordenes = $ordenes->get();

        $i=0;
        
        $fecha_d = date('Y/m/d'); 
        
        Excel::create('Examenes-'.$fecha_d, function($excel) use($ordenes) {

            $excel->sheet('Examenes', function($sheet) use($ordenes) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                
                $sheet->mergeCells('A3:G3'); 
                
                $mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                
                
                $sheet->cells('A1:M3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:M4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE ORDENES DE EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FORMA DE PAGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

               

                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO %');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('RECARGO %');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('RECARGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                $cant=1; $total=0;
                foreach($ordenes as $value){
                    $txtcolor='#000000';
                    
                    
                    $sheet->cell('A'.$i, function($cell) use($value, $txtcolor,$cant){
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_orden,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    

                    $sheet->cell('C'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        if($value->papellido2 != "(N/A)"){
                            $vnombre= $value->papellido1.' '.$value->papellido2;   
                        }
                        else{
                            $vnombre= $value->papellido1;   
                        }

                        if($value->pnombre2 != "(N/A)"){
                            $vnombre= $vnombre.' '.$value->pnombre1.' '.$value->pnombre2;   
                        }
                        else
                        {
                            $vnombre= $vnombre.' '.$value->pnombre1;
                        }   
                        
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });


                    $sheet->cell('D'.$i, function($cell) use($value, $txtcolor) {
                            
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                     $sheet->cell('E'.$i, function($cell) use($value, $txtcolor) {
                            
                        $cell->setValue($value->snombre );
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1.' '.$value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                    });

                    $sheet->cell('H'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->fpnombre);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('I'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_p);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('J'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descuento_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });


                    $sheet->cell('K'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_p);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('L'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->recargo_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('M'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    

                    $i= $i+1;


                    $cant = $cant + 1;
                    $total = $total + $value->total_valor; 
                }
                $sheet->getStyle('G5:G'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('J5:J'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');
                $sheet->getStyle('L5:M'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');

                 $sheet->cell('A'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL PACIENTES:');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('K'.$i, function($cell) use($cant){
                        // manipulate the cel
                        $cell->setValue($cant - 1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('L'.$i, function($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL:');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('M'.$i, function($cell) use($total){
                        // manipulate the cel
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                   
                    
            });
        })->export('xlsx');
    }

    public function detalle_rpt(Request $request) {

        //dd($request->all());
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro = $request['seguro'];
        $nombres = $request['nombres'];
        //dd($request->all());
        
        $ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->join('users as cu','cu.id','eo.id_usuariocrea')->join('users as mu','mu.id','eo.id_usuariomod')->join('examen_detalle as ed','ed.id_examen_orden','eo.id')->join('examen as e','e.id','ed.id_examen')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo','e.descripcion','ed.valor as edvalor', 'ed.cubre');


        $ordenes = $ordenes->whereBetween('eo.fecha_orden', [$fecha.' 00:00', $fecha_hasta.' 23:59'])->where('eo.realizado','1')->where('eo.estado','1')->where('s.tipo','1');

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $ordenes = $ordenes->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        if($seguro!=null){

            $ordenes = $ordenes->where('eo.id_seguro',$seguro);
        }  

        $ordenes = $ordenes->get();
        
        //dd($ordenes);

        $seguros = Seguro::where('inactivo','1')->get();

        $ex_det=[];
        foreach ($ordenes as $orden) {
            $txt_examen="";
            $detalle = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen as e','e.id','ed.id_examen')->select('ed.*','e.nombre','e.descripcion')->get();
            $bandera=0;
            foreach ($detalle as $value) {
                if($bandera==0){
                    $txt_examen = $value->descripcion; 
                    $bandera=1;   
                }else{
                    $txt_examen = $txt_examen.'+'.$value->descripcion;    
                }
            }
            $ex_det[$orden->id] = $txt_examen;
        }
        
        $i=0;
        
        $fecha_d = date('Y/m/d'); 
        
        Excel::create('Examenes_detalle-'.$fecha_d, function($excel) use($ordenes) {

            $excel->sheet('Examenes_detalle', function($sheet) use($ordenes) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                
                $sheet->mergeCells('A3:G3'); 
                
                $mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                
                
                $sheet->cells('A1:G3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:G4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE DETALLE DE EXÁMENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('EXAMEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CUBRE SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                $cant=1; $total=0;
                foreach($ordenes as $value){

                    
                    $sheet->cell('A'.$i, function($cell) use($value, $cant){
                        // manipulate the cel
                        $cell->setValue($cant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function($cell) use($value){
                        // manipulate the cel
                        //$cell->setValue(substr($value->created_at,0,10));
                        if($value->papellido2 != "(N/A)"){
                            $vnombre= $value->papellido1.' '.$value->papellido2;   
                        }
                        else{
                            $vnombre= $value->papellido1;   
                        }

                        if($value->pnombre2 != "(N/A)"){
                            $vnombre= $vnombre.' '.$value->pnombre1.' '.$value->pnombre2;   
                        }
                        else
                        {
                            $vnombre= $vnombre.' '.$value->pnombre1;
                        }   
                        
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    

                    $sheet->cell('C'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });


                    $sheet->cell('D'.$i, function($cell) use($value) {
                            
                        $cell->setValue(substr($value->fecha_orden,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('E'.$i, function($cell) use($value) {
                            
                        
                        $cell->setValue($value->snombre );
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('F'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('G'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->edvalor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                    });

                    $sheet->cell('H'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->cubre);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $i= $i+1;


                    $cant = $cant + 1;
                    $total = $total + $value->total_valor; 
                }
                $sheet->getStyle('G5:G'.$i)->getNumberFormat()->setFormatCode('$#,##0.00_-');    
                    
            });
        })->export('xlsx');
        
        
    }

    public function certificar($id_orden, $id, $n, $maq){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $orden = Examen_Orden::find($id_orden);
        //MARCA COMO CERTIFICADO
        $resultado = $orden->resultados->where('id_parametro', $id)->first();
        if(!is_null($resultado)){

            $input = [

                'certificado' => $n,
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,

            ];

            $resultado->update($input);  

        }
        
        $estado = '2';//0 pendiente: tiene un examen sin ingresar 1:certificar: todos los exámenes ya ingresados y pendiente de certificar  2:listo
        $detalles = Examen_Detalle::where('id_examen_orden',$id_orden)->join('examen as e','e.id','examen_detalle.id_examen')->where('maquina',$maq)->where('no_resultado','0')->get();
        //return $detalles;
        foreach ($detalles as $detalle) {
            if($detalle->examen->no_resultado=='0'){


                if(count($detalle->parametros)=='0'){
                    $estado = '0';
                    break;
                }
                if($detalle->examen->sexo_n_s=='0'){
                    
                    foreach($detalle->parametros->where('sexo','3') as $parametro){
                        $resultado = $orden->resultados->where('id_parametro',$parametro->id)->first();
                        if(is_null($resultado)){
                          $estado = '0';
                          break;
                        }else{
                          if($resultado->certificado=='0'){
                            $estado = '1';    
                          }      
                        }  
                    }
                }else{
                    foreach($detalle->parametros->where('sexo',$orden->paciente->sexo) as $parametro){
                        $resultado = $orden->resultados->where('id_parametro',$parametro->id)->first();
                        if(is_null($resultado)){
                          $estado = '0';
                          break;
                        }else{
                          if($resultado->certificado=='0'){
                            $estado = '1';    
                          }  
                        }

                    }
                }
            }    
        }

        $txt_maquina = '';
        if($maq == '1'){
            $txt_maquina = 'HEMATOLOGÍA';
            if($orden->er_biometria != $estado){

                $input = [

                    'er_biometria' => $estado,     
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if($estado=='0'){
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                }elseif($estado == '1'){
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";    
                }else{
                    $descripcion = "ORDEN CERTIFICADA";     
                }

                Log_usuario::create([
                    
                    'id_usuario' => $idusuario,
                    'ip_usuario' => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1' => "ORDEN: ".$id_orden,
                    'dato1' => "MAQUINA: ".$txt_maquina,
                    
                    
                ]); 
            
            }


        } 

        if($maq == '2'){
            $txt_maquina = 'BIOQUIMICA';
            if($orden->er_bioquimica != $estado){

                $input = [

                    'er_bioquimica' => $estado,     
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if($estado=='0'){
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                }elseif($estado == '1'){
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";    
                }else{
                    $descripcion = "ORDEN CERTIFICADA";     
                }

                Log_usuario::create([
                    
                    'id_usuario' => $idusuario,
                    'ip_usuario' => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1' => "ORDEN: ".$id_orden,
                    'dato1' => "MAQUINA: ".$txt_maquina,
                    
                    
                ]); 
            
            }

        } 

        if($maq == '0'){
            $txt_maquina = 'MANUAL';
            if($orden->er_manual != $estado){

                $input = [

                    'er_manual' => $estado,     
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,

                ];

                $orden->update($input);

                if($estado=='0'){
                    $descripcion = "ORDEN PENDIENTE DE INGRESAR EXAMEN";
                }elseif($estado == '1'){
                    $descripcion = "ORDEN PENDIENTE DE CERTIFICACION";    
                }else{
                    $descripcion = "ORDEN CERTIFICADA";     
                }

                Log_usuario::create([
                    
                    'id_usuario' => $idusuario,
                    'ip_usuario' => $ip_cliente,
                    'descripcion' => $descripcion,
                    'dato_ant1' => "ORDEN: ".$id_orden,
                    'dato1' => "MAQUINA: ".$txt_maquina,
                    
                    
                ]); 
            
            }


        }    

       
        
        return $estado;
                        

    }

    public function marca_listo(){

        $ordenes = Examen_Orden::where('estado','1')->get();
        foreach ($ordenes as $orden) {
            //$orden = Examen_Orden::find($id);
            $detalle = $orden->detalles;
            $resultados =  $orden->resultados;
            //$parametros = Examen_parametro::orderBy('orden')->get();
           
            $cant_par = 0;
            foreach($detalle as $d){
                //$parametros = $parametros->where('id_examen', $d->id_examen);
                if($d->examen->no_resultado=='0'){

                    if(count($d->parametros)=='0'){
                        $cant_par ++;  
                    } 
                    if($d->examen->sexo_n_s=='0'){
                      $parametro_nuevo = $d->parametros->where('sexo','3'); 
                      
                    }else{
                      $parametro_nuevo = $d->parametros->where('sexo',$orden->paciente->sexo);

                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par ++;    
                    }
                }           
                
            }

            $certificados = 0;
            $cantidad = 0;
            foreach($resultados as $r){
                $cantidad ++;
                if($r->certificado=='1'){
                    $certificados ++;
                    
                }
            }
            if($certificados>$cant_par){
                $certificados = $cant_par;
            }
            if($certificados == $cant_par){
                $input = [
                    'er_biometria' => '2',
                    'er_bioquimica' => '2',
                    'er_manual' => '2'
                ];
                $orden->update($input);
            }
        }
        

        return "ok";

    }

    public function genera_protocolo_privado($id_orden, $id_protocolo){


        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $orden = Examen_Orden::find($id_orden);

        $pexamenes = Examen_Protocolo::where('id_protocolo',$id_protocolo)->get();
        $detalles_elim = Examen_Detalle::where('id_examen_orden',$id_orden)->get();

        foreach($detalles_elim as $det_elim){
            $det_elim->delete();
        }

        foreach ($pexamenes as $pexamen) {

            //ACTUALIZA DETALLE
            $detalle = Examen_Detalle::where('id_examen_orden',$id_orden)->where('id_examen',$pexamen->id_examen)->first();

            if(is_null($detalle)){
                
                $examen =  Examen::find($pexamen->id_examen);
                //return $examen;
                $valor = $examen->valor;
                $cubre = 'NO';
                $ex_nivel = Examen_Nivel::where('id_examen',$examen->id)->where('nivel',$orden->id_nivel)->first();
                if(!is_null($ex_nivel)){
                    if($ex_nivel->valor1!=0){

                        $valor = $ex_nivel->valor1;
                        $cubre = 'SI';

                    }
                }

                $input_det = [
                    'id_examen_orden' => $orden->id,
                    'id_examen' => $examen->id,
                    'valor' => $valor,
                    'cubre' => $cubre,   
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                        
                ]; 

                Examen_detalle::create($input_det);

                /// orden
                $total = $orden->valor + $valor;
                $total = round($total,2);     
                $descuento_p = $orden->descuento_p;
                if($orden->descuento_p==null){
                    $descuento_valor = 0;
                    $descuento_p = 0;
                }
                

                if($orden->descuento_p>100){
                    $descuento_valor = $total;
                }else{
                    $descuento_valor = $orden->descuento_p * $total/100;
                    $descuento_valor = round($descuento_valor,2);     
                }
                
                $subtotal_pagar = $total - $descuento_valor;

                $recargo_p = $orden->recargo_p;

                $recargo_valor = $subtotal_pagar * $recargo_p/100;
                $recargo_valor = round($recargo_valor,2);
                $valor_total = $subtotal_pagar + $recargo_valor;
                $valor_total = round($valor_total,2);

                  
                    
            } 
              
           
        }

        //ACTUALIZAR ORDEN
        if($id_protocolo=='0'){
            $id_protocolo = null;
        }

        $input_ex = [
            'id_protocolo' => $id_protocolo,
            /*'descuento_valor' => $descuento_valor,
            'recargo_valor' =>  $recargo_valor,
            'total_valor' => $valor_total,  
            'cantidad' => $orden->cantidad + 1,
            'valor' => $total,*/
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
                        
        ];

        $orden->update($input_ex);

        Log_usuario::create([
        'id_usuario' => $idusuario,
        'ip_usuario' => $ip_cliente,
        'descripcion' => "ACTUALIZA COTIZACION",
        'dato_ant1' => $orden->id,
        'dato1' => $orden->id_paciente,
        //'dato_ant4' => $examen->id,
            ]);
                ///  

         return "listo";
            //ACTUALIZA DETALLE
                

    }

    function buscar_orden($id_paciente){

        $hoy = date('Y/m/d');
        $cant_ordenes_hoy = Examen_Orden::where('id_paciente',$id_paciente)
                                        ->where('estado','1')
                                        ->whereBetween('fecha_orden',[$hoy.' 0:00:00',$hoy.' 23:59:00'])
                                        ->get()->count();

        return $cant_ordenes_hoy;

    }
               

    


}