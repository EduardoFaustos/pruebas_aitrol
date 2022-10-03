<?php

namespace Sis_medico\Http\Controllers\mantenimientos_botones_labs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nivel;

class NivelController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $niveles = Nivel:: where('estado', '1')->get();
        //dd($niveles);
        return view ('mantenimientos_botones_labs/mantenimiento_nivel/index', ['niveles'=> $niveles]);
    }

    public function crear(){           
     return view ('mantenimientos_botones_labs/mantenimiento_nivel/crear');
    }

    public function store(Request $request){
    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                           
        $arr_nivel = [
          'nombre'           => $request['nombre'],
          'nombre_corto'         => $request['nombre_corto'],
          'grupo'                => $request['grupo'],
          'estado'               => 1,
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,

        ];
   
        Nivel::create($arr_nivel);
   
        return redirect(route('nivel.index'));
    }

    public function editar($id){
        $nivel = Nivel::find($id);
    
        return view ('mantenimientos_botones_labs/mantenimiento_nivel/editar', ['nivel' => $nivel , 'id' => $id]);
      }

      public function update(Request $request){
      
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $id = $request->id;
  
        $niveles = Nivel::find($id);
                             
          $arr_nivel = [
            'nombre'           => $request['nombre'],
            'nombre_corto'           => $request['nombre_corto'],
            'grupo'           => $request['grupo'],
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
  
                   
          ];
        $niveles->update($arr_nivel);
        
         
        return redirect(route('nivel.index'));
  
      }
}