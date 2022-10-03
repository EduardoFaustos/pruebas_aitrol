<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Ct_Arqueo_Caja;
use Sis_medico\Ct_Arqueo_Caja_Detalle;
use Session;
use Sis_medico\Ct_Denominacion;
use Sis_medico\Ct_Orden_Venta;

class Ct_Arqueo_CajaController extends Controller
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
    public function crear_arqueo(Request $request){

        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)->whereBetween("created_at", [$request->fecha_arqueo. " 00:00:00", $request->fecha_arqueo. " 23:59:59"] )->get();
        $tefectivo = 0;
        $acum_efectivo = 0;
        $total = 0;
        foreach($ordenes as $value){
            $pagos = $value->pagos;
            $efectivo = 0;
            foreach($pagos as $pago){
                $total += $pago->valor;
                if($pago->tipo == '1'){
                $efectivo += $pago->valor;
                }
            }
            $acum_efectivo = $acum_efectivo + $efectivo;
        }
      
        //dd($request->all());
        $id_usuario   = Auth::user()->id;
        $id_empresa   = session()->get('id_empresa');
        $idusuario    = Auth::user()->id;
        $fecha_arqueo = $request->fecha_arqueo;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $arqueo_caja = Ct_Arqueo_Caja::where('id_empresa', $id_empresa)->where('id_usuario', $idusuario)->where('fecha_proceso', $fecha_arqueo)->first();
        if(is_null($arqueo_caja)){
            $arr_arqueo_caja = [ 
                'fecha_proceso'   => $fecha_arqueo,
                'id_empresa'      => $id_empresa,
                'id_usuario'      => $idusuario,
                'valor_efectivo'  => $acum_efectivo,  
                'estado'          => 1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            //dd($arr_arqueo_caja);
            $id_arqueo = Ct_Arqueo_Caja::insertGetId($arr_arqueo_caja);  
            $arqueo_caja = Ct_Arqueo_Caja::find($id_arqueo); 

            $ctdenominaciones = Ct_Denominacion::where('estado',1)->get();
            foreach($ctdenominaciones as $ctdenominacion){
                $array_det = [
                    'id_arqueo_caja'   => $id_arqueo,
                    'id_denominacion'  => $ctdenominacion->id,
                    'denominacion'     => $ctdenominacion->nombre,
                    'cantidad'         => 0,
                    'estado'          => 1,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,  
                ];
                Ct_Arqueo_Caja_Detalle::create($array_det);
            }
            
        }
        
        $detalles = $arqueo_caja->detalles;
        //dd($detalles);
        //dd($ordenes);
        return view('contable/arqueo_caja/create',['arqueo_caja' => $arqueo_caja,  'ordenes'=> $ordenes, 'detalles'=>$detalles] );
    }
    public function update_arqueo(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id; 
        $detalle= Ct_Arqueo_Caja_Detalle::find( $request['id_detalle']) ;
        $detalle->cantidad          = $request['valor'];
        $detalle->estado               = 1;
        $detalle->ip_modificacion      = $ip_cliente;
        $detalle->id_usuariomod        = $idusuario;
        $detalle->save();

        return json_encode('ok');

        }      


 

}