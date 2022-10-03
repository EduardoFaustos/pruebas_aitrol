<?php

namespace Sis_medico\Http\Controllers\mantenimientos_botones_labs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Protocolo;
use Sis_medico\Examen;

class ProtocoloController extends Controller
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
        $protocolos = Protocolo:: where('estado', '3')->get();
        $examenes = Examen::where('id_agrupador', '1')->orderBy('id_agrupador')->get();
        foreach ($protocolos as $value) {
            $protocolo = $value;
        }
        return view ('mantenimientos_botones_labs/mantenimiento_protocolo/index', ['protocolos'=> $protocolos, 'examenes' => $examenes]);
    }

    public function cargarExamenes($id){
        //$protocolos = Protocolo:: where('estado', '3')->get();
        //dd($request);
        $examenes = Examen::where('id_agrupador', $id)->orderBy('id_agrupador')->get();
        return view ('mantenimientos_botones_labs/mantenimiento_protocolo/modal/examen', ['protocolo'=> $id, 'examenes' => $examenes]);
    }

    public function crear(){   
        return view ('mantenimientos_botones_labs/mantenimiento_protocolo/crear');
    }

    public function store(Request $request){
    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                           
        $arr_protocolo = [
          'nombre'           => $request['nombre'],
          'est_amb_hos'         => 0,
          'estado'               => 3,
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,

        ];
   
        Protocolo::create($arr_protocolo);
   
        return redirect(route('protocolo.index'));
    }

    public function editar($id){
        $protocolo = Protocolo::find($id);
        return view ('mantenimientos_botones_labs/mantenimiento_protocolo/editar', ['protocolo' => $protocolo , 'id' => $id]);
    }

    public function update(Request $request){
      
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $id = $request->id;
  
        $protocolo = Protocolo::find($id);
                             
          $arr_protocolo = [
            'nombre'           => $request['nombre'],
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
  
                   
          ];
        $protocolo->update($arr_protocolo);
        
         
        return redirect(route('protocolo.index'));
  
    }
}