<?php

namespace Sis_medico\Http\Controllers\mantenimiento_nomina;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Ct_Rh_Tipo_Pago;

class Ct_Rh_Tipo_PagoController extends Controller
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
        $tipos_pagos = Ct_Rh_Tipo_Pago::where('estado','1')->get();
        return view('mantenimiento_nomina/tipo_pago/index' , ['tipos_pagos'=>$tipos_pagos]);
    }

    public function crear(){
       
        return view('mantenimiento_nomina/tipo_pago/crear');
    }
    
       
    public function store(Request $request){
    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
                               
        $arr_tipo_pago = [
            'tipo'           => $request['descripcion_tipo'],
            'estado'                => 1,
       
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
    
                     
        ];
       
        Ct_Rh_Tipo_Pago::create($arr_tipo_pago);
       
        return redirect(route('tipo_pago.index'));
    }
    
    public function editar($id){
        $tipos_pagos = Ct_Rh_Tipo_Pago::find($id);
    
        return view ('mantenimiento_nomina/tipo_pago/editar', ['tipos_pagos' => $tipos_pagos , 'id' => $id]);
    }
    
    public function update(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $id = $request->id;
  
        $tipos_pagos = Ct_Rh_Tipo_Pago::find($id);
        $estado = $request->estado;
                             
          $arr_tipo_pago = [
            'tipo'           => $request['descripcion_tipo'],
            'estado'                => $estado,
     
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
  
                   
          ];
        $tipos_pagos->update($arr_tipo_pago);
        
         
        return redirect(route('tipo_pago.index'));

    }
}
