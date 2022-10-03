<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_rubros;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Seguro_tipos;

class SeguroTiposController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                           
        $arr_seguro_tipos = [
          'nombre'           => $request['nombre'],
          'detalle'          => $request['detalle'],
          'estado'               => 1,
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,

        ];
   
        Seguro_tipos::create($arr_seguro_tipos);
   
        return redirect(route('seguroTipos.index'));
    }

    public function index(){
        $seguro_tipos = Seguro_tipos:: where('estado', '1')->get();
        return view ('contable/seguro_tipos/index', ['seguro_tipos'=> $seguro_tipos]);
    }

    public function create(){
        return view ('contable/seguro_tipos/create');
    }

    public function edit($id){
        $seguro_tipo = Seguro_tipos::find($id);
        return view ('contable/seguro_tipos/edit', ['tipo' => $seguro_tipo , 'id' => $id]);
    }

    public function update(Request $request){
      
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $id = $request->id;
  
        $seguro_tipos = Seguro_tipos::find($id);
                             
          $arr_seguro_tipos = [
            'nombre'               => $request['nombre'],
            'detalle'              => $request['detalle'],
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,   
          ];
        $seguro_tipos->update($arr_seguro_tipos);
         
        return redirect(route('seguroTipos.index'));
    }

    public function delete($id){

    }

    
}