<?php

namespace Sis_medico\Http\Controllers\hospital;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Sis_medico\Ho_Prioridad_Emergencia;


class PrioridadEmergenciaController extends Controller
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
    public function index(){
        $tipos= Ho_Prioridad_Emergencia::where('estado','1')->get();
        return view('hospital.prioridademergencia.index',['tipos'=> $tipos]);

    }
    public function crear(){
        $tipos= Ho_Prioridad_Emergencia::where('estado','1')->get();
        return view('hospital.prioridademergencia.crear',['tipos'=> $tipos]);
    }
    public function guardar(Request $request)
    {
         //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $tipos= Ho_Prioridad_Emergencia::where('estado','1')->get();
        date_default_timezone_set('America/Guayaquil');
          Ho_Prioridad_Emergencia::create([
            'nombre'  => $request['nombre'],
            'prioridad'=> $request['prioridad'],
            'color'=>$request['color'],
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,  
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,                              
          ]);

        return view('hospital.prioridademergencia.index',['tipos'=> $tipos]);
    }

    public function editar($id)
    {
     $tipo = Ho_Prioridad_Emergencia::find($id);
      return view('hospital.prioridademergencia.editar',['tipo'=> $tipo]);
    }

    public function actualizar_pri(Request $request)
    {
        //dd($request->all());
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario = Auth::user()->id;
      $idtipo = $request['idtipo'];
      $tipos = Ho_Prioridad_Emergencia::where('estado', '1')->get();

      $tipo= Ho_Prioridad_Emergencia::find($idtipo);
           
      $tipo->update([
               
        'nombre' => $request['nombre'],
        'prioridad'=> $request['prioridad'],
        'color'=>$request['color'],
        'ip_modificacion' => $ip_cliente,
        'id_usuariomod' => $idusuario,
                                   
      ]);
  
      
                    
      return "ok";
    }

        public function eliminar_prioridade($id_tipo){
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario=Auth::user()->id;
    
            $tipo = Ho_Prioridad_Emergencia::find($id_tipo);
    
            $arr_tipo = [
                'estado' => 0,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];
    
            $tipo->update($arr_tipo);
    
            $tipos = Ho_Prioridad_Emergencia::where('estado', '1')->get();
                            
            return view('hospital.prioridademergencia.index',['tipos'=> $tipos]);
    
        }
    
}