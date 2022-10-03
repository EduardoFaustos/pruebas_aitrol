<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Movimiento;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Insumo_Plantilla_Item_Control;
use Sis_medico\Procedimiento;
use Sis_medico\Planilla_Procedimiento;
use Sis_medico\Insumo_Plantilla_Tipo;
use DNS1D;
use DNS2D;
use Excel;

class PlantillaControlController extends Controller
{
    protected $redirectTo = '/dashboard';

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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 7)) == false) {
            return true;
        }
    }
    public function index()
    {
        $plantillas = Insumo_Plantilla_Control::orderby('id', 'desc')->paginate(10);
        return view('insumos.plantilla_control.index', ['plantillas' => $plantillas, 'nombre' => '']);
    }

    public function create()
    {
        $procedimiento = Procedimiento::get();
        $producto = Producto::where('estado', 1)->get();
        //dd($procedimiento);
        $tipo_plantilla = Insumo_Plantilla_Tipo::get();
        //dd($tipo_plantilla);

        return view('insumos.plantilla_control.create', ['procedimiento' => $procedimiento, 'producto' => $producto, 'tipo_plantilla'=>$tipo_plantilla]);
    }

    public function save(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
       
        $procedimientos = $request->procedimientos;

        $nombre_oculto = "";
       

        DB::beginTransaction();
        try{

            $datos = Procedimiento::where('estado', 1)->whereIn('id', $procedimientos)->get();

            foreach ($datos as $value){
                $nombre_oculto .= "{$value->id}+"; 
            }
            
            $planilla_id = Insumo_Plantilla_Control::insertGetId([
                'codigo'                     => $request->codigo,
                'nombre'                     => $request->nombre,
                'estado'                     => $request->estado,
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
                'nombre_oculto'              => $nombre_oculto,  
            ]);
            //dd($request->all());
            if (count($request->producto) > 0) {
                for ($i = 0; $i < count($request->producto); $i++) {
                    $data2 = array(
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'id_producto'      => $request->producto[$i],
                        'id_plantilla'     => $planilla_id, //si va
                        'cantidad'         => $request->cantidad[$i],
                        'total'            => $request->total[$i],
                        'valor_uni'        => $request->valor_unitario[$i],
                        'tipo_plantilla'   => $request->tipo_plantilla[$i],

                    );
                    Insumo_Plantilla_Item_Control::create($data2);
                }
            }
            
            if (count($request->procedimientos) > 0) {
                for ($j = 0; $j < count($request->procedimientos); $j++) {
                    $datos = array(
                        'id_planilla'      => $planilla_id,
                        'id_procedimiento' => $request->procedimientos[$j],
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario
                    );
                    Planilla_Procedimiento::create($datos);
                }
            }

            
            DB::commit();
            $mensaje = "exito";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            $mensaje = "error";
            DB::rollBack();
            return $e->getMessage();
        }

        //dd($request->all());

        return ['respuesta'=> $mensaje];

    }



    public function edit($id)
    {
        $producto = Producto::where('estado', 1)->get();
        //$procedimiento = Procedimiento::get();
        $plantilla = Insumo_Plantilla_Control::find($id);
        //dd($plantilla);
        $planPro = Planilla_Procedimiento::where('id_planilla',$plantilla->id)->get();
        
        $plantillas_items = Insumo_Plantilla_Item_Control::where('id_plantilla', $id)->get();

        $tipo_plantilla = Insumo_Plantilla_Tipo::get();

            //->select('prod.precio_compra', 'insumo_plantilla_item_control.id_plantilla', 'insumo_plantilla_item_control.id_producto', 'insumo_plantilla_item_control.orden', 'insumo_plantilla_item_control.cantidad', 'prod.nombre as nom_prod', 'insumo_plantilla_item_control.total')->get();
        //dd($plantillas_items);
        return view('insumos.plantilla_control.editar', ['producto' => $producto, 'planPro' => $planPro, 'plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'tipo_plantilla' => $tipo_plantilla]);
    }
    
    public function update(Request $request)
    {

        $mensaje = "";
       
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //$plantilla= Insumo_Plantilla::where('codigo',$request['codigo']);
        DB::beginTransaction();
        try{
            $plantilla = Insumo_Plantilla_Control::find($request->id_plantilla);

                $plantilla->codigo          = $request->codigo;
                $plantilla->nombre          = $request->nombre;
                $plantilla->estado          = $request->estado;
                $plantilla->ip_modificacion = $ip_cliente;
                $plantilla->save();

            //$plantilla_detalle = Insumo_Plantilla_Item_Control::where('id_plantilla', $request->id_plantilla)->get();

            $items = DB::table('insumo_plantilla_item_control')->where('id_plantilla',  $request->id_plantilla)->delete();

       

            if (count($request->producto) > 0) {
                for ($i = 0; $i < count($request->producto); $i++) {
                    $datos = array(
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'id_producto'      => $request->producto[$i],
                        'id_plantilla'     => $request->id_plantilla, //si va
                        'cantidad'         => $request->cantidad[$i],
                        'total'            => $request->total[$i],
                        'valor_uni'        => $request->valor_unitario[$i],
                        'tipo_plantilla'   => $request->tipo_plantilla[$i],
                        //'iva'              => $request->iva[$i],
                        //'orden'            => $request->orden[$item],

                    );
                    Insumo_Plantilla_Item_Control::create($datos);
                }
            }
            DB::commit();
            $mensaje = "exito";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            $mensaje = "error";
            DB::rollBack();
            return $e->getMessage();
        }

        //dd($request->all());

        return ['respuesta'=> $mensaje];




    }


   
    public function item_lista($id)
    {
        // se elimino el el ->where('estado', '1') en plantilla 

        $plantilla = Insumo_Plantilla_Control::where('id', $id)->first();

        $plantillas_items = Insumo_Plantilla_Item_Control::where('id_plantilla', $id)
            ->join('producto as prod', 'prod.id', 'insumo_plantilla_item_control.id_producto')
            ->select('insumo_plantilla_item_control.tipo_plantilla', 'insumo_plantilla_item_control.updated_at as fecha', 'insumo_plantilla_item_control.total', 'insumo_plantilla_item_control.id_plantilla','insumo_plantilla_item_control.valor_uni', 'insumo_plantilla_item_control.id_producto', 'insumo_plantilla_item_control.orden', 'insumo_plantilla_item_control.cantidad', 'prod.nombre as nom_prod')->get();
        //dd($plantillas_items);

        $planilla_procedimiento = Planilla_Procedimiento::where('id_planilla', $id)
            ->join('procedimiento as p', 'p.id', 'planilla_procedimiento.id_procedimiento')
            ->select('p.id as id', 'p.observacion as nombre')->get();

        //dd($planilla_procedimiento);

        $tipo_plantilla = Insumo_Plantilla_Tipo::get();

        return view('insumos.plantilla_control.lista', ['plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'procedimiento' => $planilla_procedimiento, 'tipo_plantilla' => $tipo_plantilla]);
    }

    public function buscar(Request $request)
    {
        $nombre = $request['nombre'];
        $plantillas = Insumo_Plantilla_Control::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->paginate(10);
        
        return view('insumos.plantilla_control.index', ['nombre' => $nombre, 'plantillas' => $plantillas]);
    }

    public function comprobar(Request $request){

        $procedimientos = $request->procedimientos;

        $nombre_oculto = "";

        $datos = Procedimiento::where('estado', 1)->whereIn('id', $procedimientos)->get();

        foreach ($datos as $value){
            $nombre_oculto .= "{$value->id}+"; 
        }

        $verificar = Insumo_Plantilla_Control::where('estado', 1)->where('nombre_oculto', $nombre_oculto)->first();

        if(!is_null($verificar)){
            return ['validar'=>'si','mensaje'=> 'Ya existe una plantilla con los mismos procedimientos'];
        }else{
            return ['validar'=>'no','mensaje'=> 'No existe'];
        }
    }

    public function masivo_plantilla_costo(){
       
        Excel::filter('chunk')->load('insert_plantilla.xlsx')->chunk(600, function ($reader)  {
            foreach ($reader as $book) {
                //dd($book);
            $producto = Producto::where('nombre', $book->producto)->first();
            $tipo = Insumo_Plantilla_Tipo::where('nombre', $book->tipo)->first();
            
                $cont=0;
                $idusuario  = Auth::user()->id;
            if(is_null($producto)){
                $id_producto = Producto::insertGetId([
                    'nombre'=>$book->producto,
                    'descripcion'=> $book->producto,
                    'estado'=>1,
                    'medida'=>'Uni',
                    'minimo'=>1,
                    'codigo'=>'141120211844'.$cont . rand(),
                    'despacho'=>0,
                    'cantidad'=>1,
                    'tipo_producto'=>1,
                    'cantidad_unidad'=>1,
                    'id_marca'=>2,
                    'usos'=>1,
                    'codigo_siempre'=>0,
                    'id_usuariocrea'=>$idusuario,
                    'id_usuariomod'=> $idusuario,
                    'ip_creacion'=>'::1',
                    'ip_modificacion'=>'::1',
                    'iva'=>0,
                    'descuento'=>0
                ]);
                $cont++;
               Insumo_Plantilla_Item_Control::create([
                    'ip_modificacion'  => '::1',
                    'id_usuariomod'    => $idusuario,
                    'id_usuariocrea'   => $idusuario,
                    'ip_creacion'      => '::1',
                    'id_producto'      => $id_producto,
                    'id_plantilla'     => 16, //si va
                    'cantidad'         => $book->cantidad,
                    'total'            => $book->total,
                    'valor_uni'        => $book->unitario,
                    'tipo_plantilla'   => $tipo->id,
                ]);
            }else{
                $details = Insumo_Plantilla_Item_Control::where('id_plantilla', 16)->where('id_producto', $producto->id)->first();
                if(is_null($details)){
                    Insumo_Plantilla_Item_Control::create([
                        'ip_modificacion'  => '::1',
                        'id_usuariomod'    => $idusuario,
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => '::1',
                        'id_producto'      => $producto->id,
                        'id_plantilla'     => 16, //si va
                        'cantidad'         => $book->cantidad,
                        'total'            => $book->total,
                        'valor_uni'        => $book->unitario,
                        'tipo_plantilla'   => $tipo->id,
                    ]);
                }
               
            }
           
            }
        });
        dd("ok");
    }

}
