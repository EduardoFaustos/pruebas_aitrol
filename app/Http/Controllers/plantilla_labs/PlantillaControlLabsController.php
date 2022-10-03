<?php

namespace Sis_medico\Http\Controllers\plantilla_labs;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Detalle_Pedido;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Derivado;
use Sis_medico\Examen_Orden;
use Sis_medico\Http\Controllers\Insumos\PlantillaController;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvContableCab;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Planilla_Cabecera_Labs;
use Sis_medico\Planilla_Detalle_Labs;
use Sis_medico\InvCosto;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Movimiento;
use Sis_medico\Plantilla_Control_Labs;
use Sis_medico\Plantilla_Item_Control_Labs;
use Sis_medico\Plantilla_Tipo_Labs;
use Sis_medico\Producto;

use Storage;

class PlantillaControlLabsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }
    // private function rol()
    // {
    //     $rolUsuario = Auth::user()->id_tipo_usuario;
    //     if (in_array($rolUsuario, array(1, 4, 5, 20, 11)) == false) {
    //         return true;
    //     }
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function orden_plantilla($id_orden)
    {
        $orden = Examen_Orden::find($id_orden);
        //dd($orden);
        $empresa = Empresa::find(Session::get('id_empresa'));
        // dd($empresa, $orden);
        $planilla = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', '1')->first();
        $plantilla_control = Plantilla_Control_Labs::where('estado', 1)->where('id_empresa', $empresa->id)->get();
        $examenes = Examen::where('estado', 1)->get();
        $planilla_derivado = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', '3')->first();
        return view('plantilla_labs/edit', ["orden" => $orden, 'empresa' => $empresa, 'planilla' => $planilla, 'plantillas' => $plantilla_control, 'examenes' => $examenes, 'planilla_derivado' => $planilla_derivado]);
    }

    public function buscarProducto(Request $request)
    {

        $id_empresa = Session::get('id_empresa');
        $serie = $request->serie;
        $id_orden = $request->id_orden;
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        // dd($id_empresa);
        $id_bodega = 1;
        $inv_serie = InvInventarioSerie::where('serie', $serie)->where('existencia', '>', 0)->where('existencia_uso', '>', 0)->where('id_empresa', $id_empresa)->where('id_bodega',$id_bodega)->first();
        if (is_null($inv_serie)) {
            return ['status' => 'error', 'msj' => 'No se encontro en el inventario o no tiene existencias'];
        }
        DB::beginTransaction();
        try {

            $planilla = Planilla_Cabecera_Labs::where('id_orden', $id_orden)->where('estado', 1)->first();

            if (is_null($planilla)) {
                $examen_orden = Examen_Orden::find($id_orden);
                $data = [
                    'fecha'                 => $examen_orden->fecha_orden,
                    'id_orden'              => $examen_orden->id,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'observacion'           => "Paciente: {$examen_orden->paciente->nombre1} {$examen_orden->paciente->apellido1}",
                    'aprobado'              => 0,
                    'id_empresa'            => $id_empresa
                ];
                $id_planilla = Planilla_Cabecera_Labs::insertGetId($data);
                $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                $detalle = [
                    'id_producto'               => $inv_serie->id_producto,
                    'codigo'                    => $inv_serie->producto->codigo,
                    'id_planilla_cabecera'      => $id_planilla,
                    // 'procedimiento'             => ,
                    'precio'                    => is_null($precio) ? 0 : $precio->costo_promedio,
                    'check'                     => 1,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    'cantidad'                  => 1,
                    'serie'                     => $serie,
                    'lote'                      => $inv_serie->lote,
                    'fecha_vencimiento'         => $inv_serie->fecha_vence,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_bodega'                 => $id_bodega,
                ];
                Planilla_Detalle_Labs::create($detalle);
            } else {
                $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                $detalle = [
                    'id_producto'               => $inv_serie->id_producto,
                    'codigo'                    => $inv_serie->producto->codigo,
                    'id_planilla_cabecera'      => $planilla->id,
                    'precio'                    => is_null($precio) ? 0 : $precio->costo_promedio,
                    'check'                     => 1,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    // 'movimiento',
                    // 'id_movimiento_paciente',
                    'cantidad'                  => 1,
                    'serie'                     => $serie,
                    'lote'                      => $inv_serie->lote,
                    'fecha_vencimiento'         => $inv_serie->fecha_vence,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_bodega'                 => $id_bodega,
                ];
                Planilla_Detalle_Labs::create($detalle);
            }

            $tr = PlantillaControlLabsController::crearFila($inv_serie);
            //dd($tr);
            $comprometer = InvInventarioSerie::comprometer($inv_serie, 1);
            // $inventario = InvInventario::getInventario($producto->id, $id_bodega);
            $inventario = InvInventario::getInventario($inv_serie->id_producto, $inv_serie->id_bodega);

            DB::commit();
            return ['status' => 'success', 'fila' => $tr];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp"=>$e->getMessage()];
        }
    }

    public static function crearFila($data)
    {
        $tr = '';
        $fecha = date('Y-m-d H:i:s');
        $usuario = Auth::user()->nombre1 . " " . Auth::user()->apellido1;
        $tr .= "
        <tr>
            <td>{$data->serie}</td>
            <td>{$data->producto->nombre}</td>
            <td>{$fecha}</td>
            <td>1</td>
            <td>{$usuario}</td>
            <td><button class='btn btn-danger'>Delete</button></td>
        </tr>";
        return $tr;
    }



    public function index()
    {
        $plantillas = Plantilla_Control_Labs::orderby('id', 'desc')->paginate(10);
        return view('plantillas_labs.index', ['plantillas' => $plantillas, 'nombre' => '']);
    }
    public function create()
    {
        $producto = Producto::where('estado', 1)->get();
        //dd($procedimiento);
        $tipo_plantilla = Plantilla_Tipo_Labs::get();
        //dd($tipo_plantilla);

        return view('plantillas_labs.create', ['producto' => $producto, 'tipo_plantilla' => $tipo_plantilla]);
    }

    public function save(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        DB::beginTransaction();
        try {

            $planilla_id = Plantilla_Control_Labs::insertGetId([
                'codigo'                     => $request->codigo,
                'nombre'                     => $request->nombre,
                'estado'                     => $request->estado,
                'id_empresa'                 => $id_empresa,
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
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
                        'id_tipo_labs'     => $request->tipo_plantilla[$i],

                    );
                    Plantilla_Item_Control_Labs::create($data2);
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


        return ['respuesta' => $mensaje];
    }

    public function edit($id)
    {
        $producto = Producto::where('estado', 1)->get();
        //$procedimiento = Procedimiento::get();
        $plantilla = Plantilla_Control_Labs::find($id);
        //dd($plantilla);
        //$planPro = Planilla_Procedimiento::where('id_planilla',$plantilla->id)->get();

        $plantillas_items = Plantilla_Item_Control_Labs::where('id_plantilla', $id)->get();

        $tipo_plantilla = Plantilla_Tipo_Labs::get();

        //->select('prod.precio_compra', 'insumo_plantilla_item_control.id_plantilla', 'insumo_plantilla_item_control.id_producto', 'insumo_plantilla_item_control.orden', 'insumo_plantilla_item_control.cantidad', 'prod.nombre as nom_prod', 'insumo_plantilla_item_control.total')->get();
        //dd($plantillas_items);
        return view('plantillas_labs.editar', ['producto' => $producto, 'plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'tipo_plantilla' => $tipo_plantilla]);
    }

    public function update(Request $request)
    {

        $mensaje = "";

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //$plantilla= Insumo_Plantilla::where('codigo',$request['codigo']);
        DB::beginTransaction();
        try {
            $plantilla = Plantilla_Control_Labs::find($request->id_plantilla);

            $plantilla->codigo          = $request->codigo;
            $plantilla->nombre          = $request->nombre;
            $plantilla->estado          = $request->estado;
            $plantilla->ip_modificacion = $ip_cliente;
            $plantilla->save();

            //$plantilla_detalle = Insumo_Plantilla_Item_Control::where('id_plantilla', $request->id_plantilla)->get();

            $items = DB::table('plantilla_item_control_labs')->where('id_plantilla',  $request->id_plantilla)->delete();



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
                        'id_tipo_labs'   => $request->tipo_plantilla[$i],
                        //'iva'              => $request->iva[$i],
                        //'orden'            => $request->orden[$item],

                    );
                    Plantilla_Item_Control_Labs::create($datos);
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

        return ['respuesta' => $mensaje];
    }

    public function comprobar(Request $request)
    {

        $verificar = Plantilla_Control_Labs::where('estado', 1)->first();

        if (!is_null($verificar)) {
            return ['validar' => 'si', 'mensaje' => 'Ya existe una plantilla con los mismos procedimientos'];
        } else {
            return ['validar' => 'no', 'mensaje' => 'No existe'];
        }
    }

    public function item_lista($id)
    {
        // se elimino el el ->where('estado', '1') en plantilla 

        $plantilla = Plantilla_Control_Labs::where('id', $id)->first();

        $plantillas_items = Plantilla_Item_Control_Labs::where('id_plantilla', $id)
            ->join('producto as prod', 'prod.id', 'plantilla_item_control_labs.id_producto')
            ->select('plantilla_item_control_labs.id_tipo_labs', 'plantilla_item_control_labs.updated_at as fecha', 'plantilla_item_control_labs.total', 'plantilla_item_control_labs.id_plantilla', 'plantilla_item_control_labs.valor_uni', 'plantilla_item_control_labs.id_producto', 'plantilla_item_control_labs.cantidad', 'prod.nombre as nom_prod')->get();
        //dd($plantillas_items);

        /*$planilla_procedimiento = Planilla_Procedimiento::where('id_planilla', $id)
            ->join('procedimiento as p', 'p.id', 'planilla_procedimiento.id_procedimiento')
            ->select('p.id as id', 'p.observacion as nombre')->get();*/

        //dd($planilla_procedimiento);

        $tipo_plantilla = Plantilla_Tipo_Labs::get();

        return view('plantillas_labs.lista', ['plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'tipo_plantilla' => $tipo_plantilla]);
    }

    public function buscar(Request $request)
    {
        $nombre = $request['nombre'];
        $plantillas = Plantilla_Control_Labs::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->paginate(10);

        return view('plantillas_labs.index', ['nombre' => $nombre, 'plantillas' => $plantillas]);
    }

    public function cargarPanltilla(Request $request)
    {
        //dd($request->all());
        $id_empresa = Session::get('id_empresa');
        $id_plantilla = $request->id_plantilla;
        $items = Plantilla_Item_Control_Labs::where('id_plantilla', $id_plantilla)->where('estado', 1)->get();
        $falta = "";
        $tr = "";
        $id_bodega = 1;
        DB::beginTransaction();
        try {
            foreach ($items as $value) {
                $inv_serie = InvInventarioSerie::where('id_producto', $value->id_producto)->where('existencia', '>', 0)->where('existencia_uso', '>', 0)->where('id_empresa', $id_empresa)->where('id_bodega', $id_bodega)->first();

                if (is_null($inv_serie)) {
                    // return ['status' => 'error', 'msj' => 'No se encontro en el inventario o no tiene existencias'];
                    $falta .= isset($value->producto) ? "<span class='badge bg-warning'>{$value->producto->nombre}</span><br>" : '';
                } else {
                    for ($i = 0; $i < $value->cantidad; $i++) {
                        $guardar = PlantillaControlLabsController::guardarPlanilla($request->id_orden, $inv_serie, $id_plantilla);
                        if ($guardar['status'] == 'success') {
                            $tr .= $guardar["fila"];
                        } else {
                            DB::rollback();
                            return $guardar;
                        }
                    }
                }
            }
            DB::commit();
            return ['status' => 'success', 'fila' => $tr, "falta" => $falta];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", 'fila' => $tr];
        }
    }

    public static function guardarPlanilla($id_orden, $inv_serie, $id_plantilla = "")
    {
        $id_empresa = Session::get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            //$inv_serie = InvInventarioSerie::where('id_producto', $value->id_producto)->where('existencia', '>', 0)->where('existencia_uso', '>', 0)->where('id_empresa', $id_empresa)->first();
            $id_bodega = 1;
            $planilla = Planilla_Cabecera_Labs::where('id_orden', $id_orden)->where('estado', 1)->first();

            if (is_null($planilla)) {
                $examen_orden = Examen_Orden::find($id_orden);
                $data = [
                    'fecha'                 => $examen_orden->fecha_orden,
                    'id_orden'              => $examen_orden->id,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 1,
                    'observacion'           => "Paciente: {$examen_orden->paciente->nombre1} {$examen_orden->paciente->apellido1}",
                    'aprobado'              => 0,
                    'id_empresa'            => $id_empresa
                ];
                $id_planilla = Planilla_Cabecera_Labs::insertGetId($data);
                $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                $detalle = [
                    'id_producto'               => $inv_serie->id_producto,
                    'codigo'                    => $inv_serie->producto->codigo,
                    'id_planilla_cabecera'      => $id_planilla,
                    // 'procedimiento'             => ,
                    'precio'                    => is_null($precio) ? 0 : $precio->costo_promedio,
                    'check'                     => 0,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    // 'movimiento',
                    // 'id_movimiento_paciente',
                    'cantidad'                  => 1,
                    'serie'                     => $inv_serie->serie,
                    'lote'                      => $inv_serie->lote,
                    'fecha_vencimiento'         => $inv_serie->fecha_vence,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_planilla'               => $id_plantilla,
                    'id_bodega'                 => $id_bodega,
                ];
                Planilla_Detalle_Labs::create($detalle);
            } else {
                $precio = InvCosto::where('id_producto', $inv_serie->id_producto)->first();
                $detalle = [
                    'id_producto'               => $inv_serie->id_producto,
                    'codigo'                    => $inv_serie->producto->codigo,
                    'id_planilla_cabecera'      => $planilla->id,
                    'precio'                    => is_null($precio) ? 0 : $precio->costo_promedio,
                    'check'                     => 0,
                    'estado'                    => 1,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                    'cantidad'                  => 1,
                    'serie'                     => $inv_serie->serie,
                    'lote'                      => $inv_serie->lote,
                    'fecha_vencimiento'         => $inv_serie->fecha_vence,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_planilla'              => $id_plantilla,
                    'id_bodega'                 => $id_bodega,
                ];
                Planilla_Detalle_Labs::create($detalle);
            }

            $tr = PlantillaControlLabsController::crearFila($inv_serie);

            $comprometer = InvInventarioSerie::comprometer($inv_serie, 1);
            // $inventario = InvInventario::getInventario($producto->id, $id_bodega);
            $inventario = InvInventario::getInventario($inv_serie->id_producto, $inv_serie->id_bodega);

             DB::commit();
            return ['status' => 'success', 'fila' => $tr];
        } catch (\Exception $e) {
             DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage()];
        }
    }

    public static function comparativo($id_orden){
        $orden = Examen_Orden::find($id_orden);
        $empresa = Empresa::find(Session::get('id_empresa'));
       // dd($empresa);
        $planilla = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', 1)->first();

        $planilla_derivados = Planilla_Cabecera_Labs::where('id_empresa', $empresa->id)->where('id_orden', $orden->id)->where('estado', 3)->first();
        //dd($planilla_derivados->detalles);
        $plantilla_control = Plantilla_Control_Labs::where('estado', 1)->where('id_empresa', $empresa->id)->get();
        return view('plantilla_labs/comparativo', ["orden" => $orden, 'empresa' => $empresa, 'planilla' => $planilla, 'plantillas' => $plantilla_control,'planilla_derivado' => $planilla_derivados]);
    }

    public function storePlanilla(Request $request){
        /*
            1) Inv_Inventario
            2) Inv_cab_Movimiento
            3) Inv_Detalle_Movimiento
            4) Inv_inventario Serie
            5) Inv_Kardex
        */
        $id_empresa = Session::get('id_empresa');
        $examen_orden = Examen_Orden::find($request->id_orden);
        $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
        $planilla_cabecera_labs = Planilla_Cabecera_Labs::find($request->id_planilla);
        $pl_cab_derivado = Planilla_Cabecera_Labs::where('id_orden', $examen_orden->id)->where('estado',3)->first();

        $total_asiento = 0;
        $id_bodega = 1;
        if($planilla_cabecera_labs->aprobado == 1){
            return ["status"=>"error", "msj"=> "Ya se encuentra aprobada la plantilla"];
        }
        //dd($planilla_cabecera_labs);
        if(isset($pl_cab_derivado->detalles)){
            $detalle = $pl_cab_derivado->detalles->sum('precio');
            $total_asiento += $detalle;

        }
        $id_usuario = Auth::user()->id;
        $numero = "000000001";
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        if(!is_null($cab_movimiento)){
            $numero = str_pad(intval($cab_movimiento->numero_documento)+1, 9, "0", STR_PAD_LEFT);
        }
        $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EPL')->where('id_empresa', $id_empresa)->first();
       // dd($inv_documento_bod);

       if(is_null($inv_documento_bod)){
            return ['status' => 'error', 'msj' => "No tiene configurado documento de bodegas"];
       }

        DB::beginTransaction();

        try{

            $inv_transacciones_bodegas = InvTransaccionesBodegas::insertGetId([
                'id_documento_bodega'   => $inv_documento_bod->id,
                'id_bodega'             => $id_bodega,//cambiar por bodefa de empresa prioridad_labs
                'secuencia'             => 100,
                'id_empresa'            => $id_empresa,
                'id_usuariocrea'        => $id_usuario,
                'id_usuariomod'         => $id_usuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente
            ]);

            // $inv_trans_bodega = InvTransaccionesBodegas::where('id_documento_bodega', 18)->first();
            $data_cab_movimiento = [
                'id_documento_bodega'       => $inv_documento_bod->id, 
                'id_transaccion_bodega'     => $inv_transacciones_bodegas, 
                'id_bodega_origen'          => 1, 
                // 'id_bodega_destino'=> null, 
                'numero_documento'          => $numero, 
                // 'num_doc_ext'=> '', 
                // 'num_doc_cont'=> '', 
                'observacion'               => "EGRESO LAB PACIENTE: {$examen_orden->id_paciente}, Orden: {$examen_orden->id}", 
                'fecha'                     => date("Y-m-d", strtotime($examen_orden->fecha_orden)),
                'descuento'                 => 0, 
                'subtotal'                  => 0, 
                'subtotal_0'                => 0, 
                'iva'                       => 0, 
                'total'                     => 0, 
                'estado'                    => 1, 
                // 'id_movimiento_estado'=> '', 
                // 'id_pedido'=> '', 
                // 'id_agenda'=> '', 
                // 'id_docum_origen'=> '', 
                // 'id_hc_procedimientos'=> '', 
                // 'id_asiento'=> '', 
                'id_empresa'                => $id_empresa, 
                'id_usuariocrea'            => $id_usuario,
                'id_usuariomod'             => $id_usuario, 
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente, 
            ];
    
            $id_cab_movimiento = InvCabMovimientos::insertGetId($data_cab_movimiento);
            if(isset($request->detalles)){
                for($i = 0; $i < count($request->detalles); $i++){
                    if($request['check_aprobado'][$i] == 1){
                        $planilla_detalle_labs = Planilla_Detalle_Labs::find($request['detalles'][$i]);
                        $inv_inventario = InvInventario::where('id_producto', $planilla_detalle_labs->id_producto)->where('id_bodega', $planilla_detalle_labs->id_bodega)->latest()->first();
                        // dd($inv_inventario);
                        $total_asiento +=  $planilla_detalle_labs->precio;
                        $data_inv_inventario = [
                            'id_producto'=> $planilla_detalle_labs->id_producto,
                            'id_bodega'=> $planilla_detalle_labs->id_bodega,
                            'tipo'=> 'C',
                            'existencia'=> intval($inv_inventario->existencia) - 1,
                            'existencia_uso'=> intval($inv_inventario->existencia_uso) - 1,
                            'existencia_min'=> $inv_inventario->existencia_min,
                            'existencia_max'=> $inv_inventario->existencia_max,
                            'comprometido'  => intval($inv_inventario->comprometido) + 1,
                            'comprometido_uso'  => intval($inv_inventario->comprometido_uso) + 1,
                            // 'costo_promedio'    => $,
                            // 'ubicacion'=> ,
                            'estado'=> 1,
                            'id_empresa'=> $id_empresa,
                            'id_usuariocrea'=> $id_usuario,
                            'id_usuariomod'=> $id_usuario,
                            'ip_creacion'=> $ip_cliente,
                            'ip_modificacion'=> $ip_cliente,
                        ];
                        $id_inv_inventario = InvInventario::insertGetId($data_inv_inventario);

                        $movimiento = Movimiento::where('serie', "%{$planilla_detalle_labs->serie}%")->where('estado', 1)->first();

                        $data_inv_det_movimiento = [
                            'id_inv_cab_movimientos'        => $id_cab_movimiento,
                            'id_producto'                   => $planilla_detalle_labs->id_producto,
                            'serie'                         => $planilla_detalle_labs->serie,
                            'fecha_vence'                   => date('Y-m-d', strtotime($planilla_detalle_labs->fecha_vencimiento)),
                            'lote'                          => $planilla_detalle_labs->lote,
                            'id_inv_inventario'             => $id_inv_inventario,
                            'cantidad'                      => 1,
                            'cant_uso'                      => 1,
                            'valor_unitario'                => $planilla_detalle_labs->precio,
                            'subtotal'                      => $planilla_detalle_labs->precio,
                            'descuento'                     => !is_null($movimiento) ? $movimiento->descuento : 0,
                            'iva'                           => 0,
                            'total'                         => $planilla_detalle_labs->precio,
                            'estado'                        => 1,
                            'kardex'                        => 1,
                            'motivo'                        => "EGRESO LAB PACIENTE: {$examen_orden->id_paciente} ", 
                            // 'id_detalle_origen'=> '',
                            'id_pedido'                     => !is_null($movimiento) ? $movimiento->id_pedido : null,
                            // 'id_detalle_pedido'=> '',
                            // 'id_procedimiento'=> '',
                            // 'id_movimiento_paciente'=> '',
                            'id_usuariocrea'                => $id_usuario,
                            'id_usuariomod'                 => $id_usuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ];
                        $inv_det_movimiento = InvDetMovimientos::insertGetId($data_inv_det_movimiento);

                        $inv_serie =InvInventarioSerie::where('id_empresa', $id_empresa)->where('serie', $planilla_detalle_labs->serie)
                                    ->where('id_bodega', $planilla_detalle_labs->id_bodega)->first();
                        if(!is_null($inv_serie)){
                            if($inv_serie->existencia > 0){
                                $inv_serie->existencia = $inv_serie->existencia - 1;
                                $inv_serie->existencia_uso = $inv_serie->existencia_uso - 1;
                                $inv_serie->id_usuariomod = $id_usuario;
                                $inv_serie->ip_modificacion = $ip_cliente;
                                $inv_serie->save();
                            }
                        }

                        $data_inv_kardex = [
                                'id_inv_inventario' => $id_inv_inventario, 
                                'id_bodega' => $planilla_detalle_labs->id_bodega, 
                                'id_producto' => $planilla_detalle_labs->id_producto, 
                                'tipo' => 'E', 
                                'descripcion' => 'EGR-', 
                                'referencia' => "EGRESO LAB PACIENTE: {$examen_orden->id_paciente}",
                                'fecha' => date('Y-m-d'),
                                'cantidad' => 1, 
                                'cant_uso' => $inv_serie->existencia, 
                                'valor_unitario' => $planilla_detalle_labs->precio, 
                                // 'iva' => '', 
                                'total' => $planilla_detalle_labs->precio, 
                                'exist_cant' => $inv_inventario->existencia, 
                                'exist_uso' => $inv_inventario->existencia_uso, 
                                // 'exist_valor_unitario' => '', 
                                // 'exist_total' => '', 
                                'estado' => 1, 
                                'id_documento_bodega' => $inv_documento_bod->id, 
                                'id_inv_det_movimientos' => $inv_det_movimiento, 
                                'id_empresa' => $id_empresa,
                                'id_usuariocrea' => $id_usuario, 
                                'id_usuariomod' => $id_usuario, 
                                'ip_creacion' => $ip_cliente, 
                                'ip_modificacion' => $ip_cliente,
                                // 'dar_baja' => '', 
                        ];
                        $id_inv_kardex = InvKardex::insertGetId($data_inv_kardex);

                    }
                
                }
            }
            $id_asiento_cab = null;
            $inv_contable_cab = InvContableCab::where('id_documento_bodega', $inv_documento_bod->id)->where('id_empresa', $id_empresa)->first();
            if(!is_null($inv_contable_cab)){
                $input_cabecera = [

                    'fecha_asiento'   => $examen_orden->fecha_orden,
                    'fact_numero'     => $numero,
                    'id_empresa'      => $id_empresa,
                    'observacion'     => "EXAMEN LAB PACIENTE: {$examen_orden->id_paciente} ", 
                    'valor'           => $total_asiento,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
    
                $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                foreach($inv_contable_cab->detalles as $det_labs){
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cab,
                        'id_plan_cuenta'      => $det_labs->cuenta,
                        'descripcion'         => '',
                        'fecha'               => $examen_orden->fecha_orden,
                        'debe'                => $det_labs->tipo == 'D' ? $total_asiento : 0,
                        'haber'               => $det_labs->tipo == 'H' ? $total_asiento : 0,
                        'estado'              => '1',
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            }

            if(!is_null($planilla_cabecera_labs)){
                $planilla_cabecera_labs->aprobado = 1;
                $planilla_cabecera_labs->id_usuariomod = $id_usuario;
                $planilla_cabecera_labs->id_asiento_cabecera = $id_asiento_cab;
                $planilla_cabecera_labs->save();
            }
           
            if(!is_null($pl_cab_derivado)){
                $pl_cab_derivado->aprobado = 1;
                $pl_cab_derivado->id_usuariomod = $id_usuario;
                $pl_cab_derivado->id_asiento_cabecera = $id_asiento_cab;
                $pl_cab_derivado->save();
            }
           
            
            DB::commit();
            return ['status' => 'success', "msj"=>'Guardado con exito' , 'asiento'=>$id_asiento_cab];
        }catch(\Exception $e){
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage()];
        }
    


    }

    public function busca_examen(Request $request){
       
        $id_examen = $request->id_examen;

        $derivado = Examen_Derivado::where('examen_derivado.id_examen', $id_examen)->where('examen_derivado.estado', '1')
        ->join('examen_tipo_derivado as etd','etd.id','examen_derivado.id_tipo')->select('examen_derivado.*', 'etd.nombre as nombre_tipo')->get();

        return $derivado;
        
    }

    public function guardar_derivado(Request $request){
        $id_empresa = Session::get('id_empresa');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $id_orden = $request->id_orden;
            $id_derivado = $request->id_derivado;
            $planilla = Planilla_Cabecera_Labs::where('id_orden', $id_orden)->where('estado', 3)->where('id_empresa', $id_empresa)->first();
             //dd($planilla);
            if (is_null($planilla)) {
                $examen_orden = Examen_Orden::find($id_orden);
                //dd($examen_orden);
                $data = [
                    'fecha'                 => $examen_orden->fecha_orden,
                    'id_orden'              => $examen_orden->id,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'estado'                => 3,
                    'observacion'           => "Paciente: {$examen_orden->paciente->nombre1} {$examen_orden->paciente->apellido1}",
                    'aprobado'              => 0,
                    'id_empresa'            => $id_empresa
                ];
                $id_planilla = Planilla_Cabecera_Labs::insertGetId($data);

                $examen_derivado = Examen_Derivado::find($id_derivado); 
                $detalle = [
                    'id_planilla_cabecera'      => $id_planilla,
                    'precio'                    => $examen_derivado->valor,
                    'check'                     => 0,
                    'estado'                    => 1,
                    'cantidad'                  => 1,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_examen_derivado'        => $id_derivado,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                ];
                Planilla_Detalle_Labs::create($detalle);
            }else{
                $examen_derivado = Examen_Derivado::find($id_derivado); 
                $detalle = [
                    'id_planilla_cabecera'      => $planilla->id,
                    'precio'                    => $examen_derivado->valor,
                    'check'                     => 0,
                    'estado'                    => 1,
                    'cantidad'                  => 1,
                    'observacion'               => "",
                    'tipo_plantilla'            => "",
                    'id_examen_derivado'        => $id_derivado,
                    'id_usuariocrea'            => $id_usuario,
                    'id_usuariomod'             => $id_usuario,
                    'ip_creacion'               => $ip_cliente,
                    'ip_modificacion'           => $ip_cliente,
                ];
                Planilla_Detalle_Labs::create($detalle);
            }
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', "msj" => "Error al cargar el insumo", "exp" => $e->getMessage()];
        }
    }

    public function eliminar_det($id){

        $det_planilla = Planilla_Detalle_Labs::find($id);
        $det_planilla->delete();

        return ['status' => 'success', 'msj' => 'Eliminado'];

    }
}
