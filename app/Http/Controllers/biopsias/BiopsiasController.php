<?php

namespace Sis_medico\Http\Controllers\biopsias;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Biopsias;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Biopsias_Result;
use Sis_medico\Paciente;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\User;
use Illuminate\Support\Facades\Storage;

class BiopsiasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

     private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 15,16)) == false ){
          return true;
        }
        

    }


    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }


        //$examenes = DB::table('cie_10_3 as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->paginate(30);
        //$cie_10_4 = DB::table('cie_10_4 as e')->where('e.estado','1')->paginate(30);

        $tipo_usuario = Auth::user()->id_tipo_usuario;
        $biopsias = Biopsias::where('id_tipo_usuario', $tipo_usuario)->get();

        //dd($biopsias);
        //dd($hospitalizados);
       return view('biopsias/index',['biopsias' =>$biopsias]);
    }

public function detalles($hc_id_procedimiento)
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }


        //$examenes = DB::table('cie_10_3 as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->paginate(30);
        //$cie_10_4 = DB::table('cie_10_4 as e')->where('e.estado','1')->paginate(30);

        $tipo_usuario = Auth::user()->id_tipo_usuario;
        $detalle_frascos = Biopsias::where('hc_id_procedimiento', $hc_id_procedimiento)->get();
        $paciente = Biopsias::where('hc_id_procedimiento', $hc_id_procedimiento)->first();
        $doctor = Biopsias::where('hc_id_procedimiento', $hc_id_procedimiento)->first();
        $procs = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_id_procedimiento)->get();

        //dd($biopsias);
        //dd($hospitalizados);
       return view('biopsias/detalles',['detalle_frascos' =>$detalle_frascos,'paciente' =>$paciente,'doctor' =>$doctor,'procs' =>$procs]);
    }

   


    

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
         
          //$biopsia_resultado_id= Biopsias_Result::find($id);
           $biopsia_resultado_id = Biopsias_Result::where('id_hc_biopsia', $id)->first();

           
       return view('biopsias/edit',['biopsia_resultado_id'=>$biopsia_resultado_id]);
        //////return view('biopsias/edit');
        /*
        $cie_10_3c = DB::table('cie_10_3 as e')->where('e.estado','1')->get();
        //return view('cie_10/cie_10_4/create',['cie_10_3c' => $cie_10_3c]);

        $cie_10_4 = Cie_10_4::find($id);
        if(!is_null($cie_10_4)){

        //$agrupadores = Examen_Agrupador::where('estado','1')->get();
            return view('cie_10/cie_10_4/edit', ['cie_10_4' => $cie_10_4],['cie_10_3c' => $cie_10_3c]);

        }*/


    }

    public function editresultado($id, Request $request)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
         
          //$biopsia_resultado_id= Biopsias_Result::find($id);
           $biopsia_resultado_id = Biopsias_Result::where('id_hc_biopsia', $id)->first();
      // return view('biopsias/edit',['biopsia_resultado_id'=>$biopsia_resultado_id]);
       return back();


    }

    public function registro ($id){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $biopsia = Hc4_biopsias::findorfail($id);
        $idpaciente = $biopsia->id_paciente;
        $iddoctor = $biopsia->id_doctor;

        $paciente = Paciente::find($idpaciente);
        $doctor = User::where('id', $iddoctor)->get();

        return view('biopsias/registro',['id'=>$id,'paciente'=>$paciente,'doctor'=>$doctor]);

    }

    public function registroguardar (Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $hospitalentra= Biopsias_Result::all();
        $biopsia = Hc4_biopsias::findorfail($request['id_hc_biopsia']);
        
         
        $datos_resultado=  [
            'id_hc_biopsia'=> $request['id_hc_biopsia'],
            'campo_registro'=> $request['campo_registro'],
            'nombre_paciente'   =>$request['nombre_paciente'],
            'md_solicitante'  => $request['md_solicitante'],
            'edad'=>$request['edad'],
            'obtenido'=>  $request['obtenido'],
            'recibido'=> $request['recibido'],
            'reportado'   => $request['reportado'],
            'Ori_diagnostica'  => $request['Ori_diagnostica'],
            'macroscopia'=>$request['macroscopia'],
            'microscopia'=> $request['microscopia'],
            'img1'=> $request['img1'],
            'img2'   =>$request['img2'],
            'diagnostico'  => $request['diagnostico'],
            'observacion'=>$request['observacion'],
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente, 
            'subido' => 1,
        ];
        //dd($datos_resultado);
        $id_logo = Biopsias_Result::insertGetId($datos_resultado);

        //editar estado subido resultado
        $input = [
                    'subido'=> 1,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];
      //dd($input);
                Biopsias::where('id', $request['id_hc_biopsia'])->update($input);

        //img 1
        $id= $request['img1'];
        $nombre_original=$request['img1']->getClientOriginalName();
        $nuevo_nombre="img1_biopsiaresult".$nombre_original;
            
        $r12=Storage::disk('biopsias')->put($nuevo_nombre,  \File::get($request['img1']) );

        $rutadelaimagen=$nuevo_nombre;

        //img 2
        $id2= $request['img2'];
        $nombre_original2=$request['img2']->getClientOriginalName();
        $nuevo_nombre2="img2_biopsiaresult".$nombre_original2;
            
        $r12_2=Storage::disk('biopsias')->put($nuevo_nombre2,  \File::get($request['img2']) );

        $rutadelaimagen2=$nuevo_nombre2;

            
        //if img 1
        if ($r12){
   
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $logito=Biopsias_Result::find($id_logo);
            $logito->img1=$rutadelaimagen;
            $logito->ip_modificacion=$ip_cliente;
            $logito->id_usuariomod=$idusuario;
            $r22=$logito->save();
               
            //return back();
          }

           //if img 2
        if ($r12_2){
   
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $logito2=Biopsias_Result::find($id_logo);
            $logito2->img2=$rutadelaimagen2;
            $logito2->ip_modificacion=$ip_cliente;
            $logito2->id_usuariomod=$idusuario;
            $r22=$logito2->save();

          }
          return redirect()->route('biopsias.detalles',['hc_id_procedimiento' => $biopsia->hc_id_procedimiento]);


    }



        public function buscadorporfecha (Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //$hospitalentra= Biopsias_Result::all();
        //$biopsiasporfecha = Hc4_biopsias::findorfail($request['obtenido'],$request['recibido']);
        //$biopsiasporfecha = Biopsias::where('id_tipo_usuario', $tipo_usuario)->groupBy("hc_id_procedimiento")->get();        
        /*$biopsiasporfecha = DB::select("SELECT tipo 
                                    FROM hc4_biopsias
                                    Where id_doctor = '".$id."' AND 
                                    ndia = '".$n_dia."' AND
                                    '".$hora."' BETWEEN hora_ini AND hora_fin ; ");*/
          return redirect()->route('biopsias.buscadorporfecha',['biopsiasporfecha' => $biopsiasporfecha]);


    }

   

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */




}
