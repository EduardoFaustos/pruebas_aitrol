<?php

namespace Sis_medico\Http\Controllers\vacunas;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Sis_medico\Vacunacion;

class VacunaController extends Controller
{
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

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1)) == false){
          return true;
        }
    }

    
    public function vacunas_empleados(){

        $usuarios = User::where('id_tipo_usuario','!=','2')->where('estado','1')->paginate(20);
        return view('vacunas/index',['usuarios' =>$usuarios,'cedula' => '', 'nombres' => '']);
    }

    public function buscar_empleados(Request $request){
        $cedula =$request['cedula'];
        //dd($cedula);
        $nombres =$request['usuario'];
        
        $usuarios = User::where('id_tipo_usuario','!=','2')->where('estado','1');
        
        if ($cedula!=null) {
            $usuarios = $usuarios->where('id',$cedula);
            //dd($usuarios->get());
        }
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $usuarios = $usuarios->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(nombre1," ",apellido1," ",apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(nombre2," ",apellido1," ",apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(apellido1," ",nombre1," ",nombre2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(apellido2," ",nombre1," ",nombre2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(nombre2," ",apellido2) LIKE ?', ['%'.$nombres.'%']) 
                            ->orwhereraw('CONCAT(apellido2," ",nombre2) LIKE ?', ['%'.$nombres.'%']);   
                    });
                      
            }
            else{

                $usuarios = $usuarios->whereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }
        $usuarios = $usuarios->paginate(20);

        return view('vacunas/index',['usuarios' =>$usuarios,'cedula' =>$cedula,'nombres' =>$nombres]);
    }

    public function revisar_vacunas($id){
        $fecha = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');
        $usuario =User::find($id);
        //dd($usuario);
        $vacunas =Vacunacion::where('id_usuario',$id)
        ->where('estado','1')
        ->join('users as u','u.id','vacunacion.id_usuario')
        ->select('vacunacion.id_usuario','u.nombre1','u.nombre2','u.apellido1','u.apellido2','vacunacion.edad','vacunacion.biologico','vacunacion.fecha')->get();

        return view('vacunas/revisar',['vacunas' =>$vacunas, 'fecha' =>$fecha, 'fecha_hasta' =>$fecha_hasta,'usuario' =>$usuario]);
    }
     public function buscar_vacunas(Request $request){
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $usuario =User::find($request->id);
        
        //dd($id);

        $vacunas =Vacunacion::where('id_usuario',$usuario->id)
        ->where('estado','1')
        ->join('users as u','u.id','vacunacion.id_usuario')
        ->select('vacunacion.id_usuario','u.nombre1','u.nombre2','u.apellido1','u.apellido2','vacunacion.edad','vacunacion.biologico','vacunacion.fecha');

        if($fecha!=null && $fecha_hasta!=null){
            $vacunas = $vacunas->whereBetween('vacunacion.fecha', [$fecha.' 00:00', $fecha_hasta.' 23:59']);    
        }

        $vacunas = $vacunas->get();


        return view('vacunas/revisar',['vacunas' =>$vacunas, 'fecha' =>$fecha, 'fecha_hasta' =>$fecha_hasta,'usuario' =>$usuario]);
    }



    public function crear_registro($id){
        $user =User::find($id);
        $fecha = date('Y/m/d');

        return view('vacunas/create',['user'=>$user,'fecha'=>$fecha]);
    }

    public function guardar(Request $request){
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $arr=[
            'id_usuario' => $request['cedula'],
            'edad' =>$request['edad'],
            'biologico' =>$request['biologico'],
            'lote' =>$request['lote'],
            'fecha'=>$request['fecha'],
            'responsable'=>$request['responsable'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        Vacunacion::create($arr);

        return "ok";
    }

    public function reporte_vacunas(){
        
        $fecha = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');
       
        //dd($usuario);
        $vacunas =Vacunacion::where('estado','1')
        ->join('users as u','u.id','vacunacion.id_usuario')
        ->select('vacunacion.id_usuario','u.nombre1','u.nombre2','u.apellido1','u.apellido2','vacunacion.edad','vacunacion.biologico','vacunacion.fecha')
        ->orderBy('vacunacion.fecha','desc')->paginate(20);

        return view('vacunas/reporte',['vacunas' =>$vacunas, 'fecha' =>$fecha, 'fecha_hasta' =>$fecha_hasta,'cedula' => '', 'nombres' => '']);
    }

    public function buscar_reporte(Request $request){
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $cedula =$request['cedula'];
        //dd($cedula);
        $nombres =$request['usuario'];
    
        $vacunas =Vacunacion::where('estado','1')
        ->join('users as u','u.id','vacunacion.id_usuario')
        ->select('vacunacion.id_usuario','u.nombre1','u.nombre2','u.apellido1','u.apellido2','vacunacion.edad','vacunacion.biologico','vacunacion.fecha')
        ->orderBy('vacunacion.fecha','desc');

        if($fecha!=null && $fecha_hasta!=null){
            $vacunas = $vacunas->whereBetween('vacunacion.fecha', [$fecha.' 00:00', $fecha_hasta.' 23:59']);    
        }
        if ($cedula!=null) {
            $vacunas = $vacunas->where('vacunacion.id_usuario',$cedula);
            //dd($vacunas->get());
        }
        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $vacunas = $vacunas->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(u.apellido1," ",u.apellido2," ",u.nombre1," ",u.nombre2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(u.apellido1," ",u.nombre1," ",u.nombre2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(u.apellido2," ",u.nombre1," ",u.nombre2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(u.nombre2," ",u.apellido2) LIKE ?', ['%'.$nombres.'%']) 
                            ->orwhereraw('CONCAT(u.apellido2," ",u.nombre2) LIKE ?', ['%'.$nombres.'%']);     
                    });
                      
            }
            else{

                $vacunas = $vacunas->whereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        $vacunas = $vacunas->paginate(20);


        return view('vacunas/reporte',['vacunas' =>$vacunas, 'fecha' =>$fecha, 'fecha_hasta' =>$fecha_hasta,'cedula' =>$cedula,'nombres' =>$nombres]);
    }
    public function pdf_informe_epp(Request $request)
    {

        $view = \View::make('vacunas/informe_epp_pdf')->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Depreciacion_activo_fijo.pdf');
    }

    public function pdf_informe_013(Request $request)
    {

        $view = \View::make('vacunas/informe_013_pdf')->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Depreciacion_activo_fijo.pdf');
    }
    
}