<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
//use Session;
use Illuminate\Support\Facades\Session;
use Mail;
use Sis_medico\Bodega;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Detalle_Pedido;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvContableCab;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\Inventario;
use Sis_medico\InvInventario;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvTrasladosBodegas;
use Sis_medico\Log_Detalle_Pedido;
use Sis_medico\Log_movimiento;
use Sis_medico\LogAsiento;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Producto;
use Sis_medico\Proveedor;


class IngresoController extends Controller
{
    //
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
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $bodegas     = Bodega::where('estado', '1')->whereNull('deleted_at')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa     = Empresa::all();
        $bod_princ   = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        $documentos  = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->where('id_inv_tipo_movimiento', 1)->whereIn('id', [1, 2, 3, 4])->get();
        return view('insumos/ingreso/index', [
            'bodegas' => $bodegas, 'proveedores' => $proveedores,
            'empresa'                                       => $empresa, 'doc_bodega'  => $documentos, 'bodega_principal' => $bod_princ
        ]);
    }

    public function editar_pedido($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido = pedido::findOrFail($id);

        // $cantidad_pedido = Movimiento::where('id_pedido', $pedido->id)->get();
        //dd($pedido);

        /* $cantidad_pedido = DB::table('movimiento as m')
        ->where('m.id_pedido', $id)
        ->join('pedido as p', 'p.id', 'm.id_pedido')
        ->join('producto as  pro', 'pro.id', 'm.id_producto')
        ->join('bodega as b', 'b.id', 'm.id_bodega')
        ->where('m.tipo', '1')
        ->where('m.estado', '1')
        ->groupBy('m.serie')
        ->select('pro.codigo as codigo', 'pro.nombre as nombre_producto', DB::raw('count(*) as cantidad, m.serie'), 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'pro.usos', 'm.precio', 'm.descuentop', 'm.descuento', 'pro.iva', 'pro.id as id_producto', 'pro.registro_sanitario', 'm.consecion_det')
        ->get();*/
        //dd($cantidad_pedido);

        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();

        $empresa = Empresa::all();

        /************************** */

        $cantidad_pedido = DB::table('movimiento as m')
            ->where('m.id_pedido', $id)
            ->join('pedido as p', 'p.id', 'm.id_pedido')
            ->join('producto as  pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->groupBy('m.serie')
            ->select('m.id', 'pro.codigo as codigo', 'pro.nombre as nombre_producto', DB::raw('m.cantidad, m.serie'), 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'pro.usos', 'm.precio', 'm.descuentop', 'm.descuento', 'pro.iva', 'pro.id as id_producto', 'pro.registro_sanitario', 'm.consecion_det')
            ->get();
        //$detalle = Movimiento::where('id_pedido', $id_pedido)->get();

        /*********************1 */

        //dd($detalle);
        $documentos = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->get();
        $bod_princ  = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        return view('insumos/ingreso/editar_pedido', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa, 'pedido' => $pedido, 'cantidad_pedido' => $cantidad_pedido, 'doc_bodega' => $documentos, 'bodega_principal' => $bod_princ]);
    }

    public function formulario(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $codigo = $request['codigo'];
        $nombre = $request['nombre'];

        $producto = Producto::where('codigo', 'LIKE', '%' . $codigo . '%')
            ->where('nombre', 'like', '%' . $nombre . '%')
            ->where('estado', '1')
            ->with('inv_costo')
            ->get();
        return $producto;
    }

    private function ValidatePedido(Request $request)
    {

        $prules = [
            'pedido'       => 'required',
            'id_proveedor' => 'required',
        ];
        $pmsn = [
            'pedido.required'       => 'Ingrese el numero del pedido.',
            'pedido.unique'         => 'El numero de pedido ya esta registrado.',
            'id_proveedor.required' => 'El proveedor es requerido',

        ];

        $this->validate($request, $prules, $pmsn);
    }

    public function guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $iva      = 0;
        $variable = $request['contador'];
        $bodega   = Bodega::find($request['bodega_recibe']);
        if (isset($bodega->id)) {
            $request['id_empresa'] = $bodega->id_empresa;
        }
        $input = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'tipo'            => $request['tipo_'],
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'id_bodega'       => $request['bodega_recibe'],
            'observaciones'   => $request['observaciones'],
            'consecion'       => $request['consecion'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'descuento'       => $request['descuentx'],
            'iva'             => $request['iva'],
            'total'           => $request['total'],
            'id_empresa'      => Session::get('id_empresa'),
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->validatePedido($request);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i              => 'required',
                    //'fecha_vencimiento' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required'              => 'Ingrese el numero de Lote.',
                    //'fecha_vencimiento' . $i . '.required' => 'Ingrese la fecha de vencimiento.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }
        //

        DB::beginTransaction();
        try {
            $id_pedido = Pedido::insertGetId($input);
            // GUARDO EN LA TABLA DE MOVIMIENTOS DE INVENTARIO //
            //      INGRESO LA CABECERA DEL MOVIMIENTO       //
            $secuencia = InvDocumentosBodegas::getSecuecia($request['tipo_'], $request['bodega_recibe']);
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $request['tipo_'])
                    ->where('id_bodega', $request['bodega_recibe'])
                    ->first();
                $doc_bodega                         = InvDocumentosBodegas::find($request['tipo_']);
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $request['tipo_'];
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $request['bodega_recibe'];
                $cab_mov_inv->id_bodega_destino     = $request['bodega_recibe'];
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $request['observaciones'];
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->num_doc_ext           = $request['pedido'];
                $cab_mov_inv->num_doc_cont          = $request['num_factura'];
                $cab_mov_inv->descuento             = $request['descuentx'];
                $cab_mov_inv->subtotal              = $request['subtotal_12'];
                $cab_mov_inv->subtotal_0            = $request['subtotal_0'];
                $cab_mov_inv->iva                   = $request['iva'];
                $cab_mov_inv->total                 = $request['total'];
                $cab_mov_inv->id_pedido             = $id_pedido;
                $cab_mov_inv->id_empresa            = Session::get('id_empresa');
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->save();
            }
            for ($i = 0; $i < $variable; $i++) {

                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {

                    $cant_uso = 0;
                    $iva      = 0;
                    $producto = Producto::find($request['id' . $i]);
                    if (isset($producto->id)) {
                        if ($producto->iva == 1) {
                            $conf = Ct_Configuraciones::find(3);
                            $iva  = ($request['cantidad' . $i] * $request['precio' . $i]) * $conf->iva;
                        }
                        if ($producto->usos != null) {
                            $cant_uso = $producto->usos;
                        }
                        if ($cant_uso == null or $cant_uso < 0) {
                            $cant_uso = 0;
                        }
                    }

                    $cantidad          = $request['cantidad' . $i];
                    $consecion_detalle = 0;
                    if ($request['consecion_det' . $i] == 1) {
                        $consecion_detalle = 1;
                    }
                    // for ($x = 0; $x < $cantidad; $x++) {
                    if ($request['fecha_vencimiento' . $i] == NULL or !isset($request['fecha_vencimiento' . $i])) {
                        $request['fecha_vencimiento' . $i] = date('Y-m-d');
                    }
                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => $request['cantidad' . $i],
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'usos'              => $request['cantidad' . $i] * $cant_uso,
                        'descuentop'        => $request['descuento' . $i],
                        'descuento'         => $request['descuentof' . $i],
                        'precio'            => $request['precio' . $i],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => $consecion_detalle,
                    ];
                    $id_movimiento = DB::table('movimiento')->insertGetId($input2);
                    $id_producto   = $request['id' . $i];
                    $producto      = Producto::find($id_producto);
                    //$id_ct_productos   = Ct_productos_insumos::where('id_insumo',$producto->id)->get();
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;
                    // }
                }

                //  DETALLE DE INGRESO   //
                if (isset($cab_mov_inv->id) and $request['cantidad' . $i] != 0 and $visibilidad == 1) {
                    if (isset($documentos->id) and $documentos->tipo != '') {
                        $tipo = $documentos->tipo;
                    } else {
                        $tipo = 'C';
                    }
                    $inventario = InvInventario::getInventario($request['id' . $i], $request['bodega_recibe'], $tipo);
                    if ($inventario == '[]') {
                        $inventario = InvInventario::setNeoInventario($request['id' . $i], $request['bodega_recibe'], $tipo, 0, 0);
                    }

                    $det_mov_inv                         = new InvDetMovimientos;
                    $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                    $det_mov_inv->id_producto            = $request['id' . $i];
                    $det_mov_inv->id_inv_inventario      = $inventario->id;
                    $det_mov_inv->cantidad               = $request['cantidad' . $i];
                    $det_mov_inv->cant_uso               = $request['cantidad' . $i] * $cant_uso;
                    $det_mov_inv->serie                  = $request['serie' . $i];
                    $det_mov_inv->lote                   = $request['lote' . $i];
                    $det_mov_inv->fecha_vence            = $request['fecha_vencimiento' . $i];
                    $det_mov_inv->valor_unitario         = $request['precio' . $i];
                    $det_mov_inv->subtotal               = $request['cantidad' . $i] * $request['precio' . $i];
                    $det_mov_inv->descuento              = $request['descuento' . $i];
                    $det_mov_inv->iva                    = $iva;
                    $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                    $det_mov_inv->motivo                 = 'INGRESO PEDIDO';
                    $det_mov_inv->id_pedido              = $id_pedido;
                    // $det_mov_inv->id_detalle_origen         = $id_movimiento;
                    //$det_mov_inv->id_pedido         = $id_pedido;
                    $det_mov_inv->id_detalle_pedido = $id_movimiento;
                    $det_mov_inv->ip_creacion       = $ip_cliente;
                    $det_mov_inv->ip_modificacion   = $ip_cliente;
                    $det_mov_inv->id_usuariocrea    = $idusuario;
                    $det_mov_inv->id_usuariomod     = $idusuario;
                    $det_mov_inv->save();
                }
            }
            // MOVIMIENTO EN KARDEX


            $kardex = InvKardex::setKardex($cab_mov_inv->id);

            // CONTABILIDAD
            $id_asiento              = InvContableCab::setAsientoContablePedido($cab_mov_inv->id);

            $cab_mov_inv->id_asiento = $id_asiento;
            $cab_mov_inv->save();



            //ENVIO DE CORREO A LA ING SHEYLA
            if (Session::get('id_empresa') == "0992704152001") {
                $correo = 'fortiz@mdconsgroup.com';
                $array = ['id_pedido' => $id_pedido];
                Mail::send('insumos/ingreso/mail', $array, function ($msj) use ($correo) {
                    $msj->subject('Numero de Pedido');
                    $msj->to($correo);
                    $msj->from('soporte@mdconsgroup.com');
                    $msj->bcc('torbi10@hotmail.com');
                });
            }

            $url = route('producto.index');
            DB::commit();
            // DB::rollBack();
            $data['msj'] = 'ok';
            return response()->json($data);
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function guardarDocumentoBodega($id_pedido)
    {
        # code...
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $pedido = Pedido::find($id_pedido);
    }

    private function ValidatePedido_actualizar(Request $request)
    {

        $id     = $request['id_pedido'];
        $prules = [
            'pedido'       => 'required|unique:pedido,id,' . $id,
            'id_proveedor' => 'required',
        ];
        $pmsn = [
            'pedido.required'       => 'Ingrese el numero del pedido.',
            'pedido.unique'         => 'El numero de pedido ya esta registrado.',
            'id_proveedor.required' => 'El proveedor es requerido',

        ];

        $this->validate($request, $prules, $pmsn);
    }

    public function actualizar_pedido(Request $request)
    {
        //return $request['total'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $pedido = Pedido::findOrFail($request['id_pedido']);

        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'tipo'            => $request['tipo_'],
            'id_empresa'      => Session::get('id_empresa'),
            'consecion'       => $request['consecion'],
            'observaciones'   => $request['observaciones'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'iva'             => $request['iva'],
            'descuento'       => $request['descuentx'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        $pedido->update($input);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required' => 'Ingrese el numero de Lote.',

                ];
                $this->validate($request, $prules, $pmsn);
            }
        }

        $id        = $request['id_pedido'];
        $id_pedido = $id;

        DB::beginTransaction();
        try {

            for ($i = 0; $i < $variable; $i++) {
                $serie = $request['serie' . $i];

                $cantidad_pedido = DB::table('movimiento as m')
                    ->where('m.id_pedido', $id)
                    ->join('pedido as p', 'p.id', 'm.id_pedido')
                    ->join('producto as  pro', 'pro.id', 'm.id_producto')
                    ->join('bodega as b', 'b.id', 'm.id_bodega')
                    ->where('m.tipo', '1')
                    ->where('m.estado', '1')
                    ->where('m.serie', $serie)
                    ->groupBy('m.serie')
                    ->select('pro.codigo as codigo', 'pro.nombre as nombre_producto', 'm.cantidad', 'm.serie', 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'm.precio', 'pro.iva')
                    ->first();
                if (!is_null($cantidad_pedido)) {
                    $visibilidad = $request['visibilidad' . $i];
                    if ($visibilidad == 1) {
                        // if ($cantidad_pedido->cantidad == $request['cantidad' . $i]) {
                        $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->get();
                        foreach ($detalles_actualizar as $value) {
                            $movimiento_detalle_ac = Movimiento::find($value->id);
                            $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                            $cantidad_producto     = $producto->cantidad;
                            $input_ac              = [
                                'precio'            => $request['precio' . $i],
                                'cantidad'          => $request['cantidad' . $i],
                                'lote'              => $request['lote' . $i],
                                'id_bodega'         => $request['id_bodega' . $i],
                                'descuentop'        => $request['descuento' . $i],
                                'descuento'         => $request['descuentof' . $i],
                                'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariomod'     => $idusuario,
                                'consecion_det'     => $request['consecion_det' . $i],
                            ];
                            Log_movimiento::create([
                                'id_producto'     => $movimiento_detalle_ac->id_producto,
                                'id_movimiento'   => $value->id,
                                'id_encargado'    => $idusuario,
                                'observacion'     => "Actualizacion de precio/lote",
                                'tipo'            => '1',
                                'ip_creacion'     => $ip_cliente,
                                'cantidad'        => $request['cantidad' . $i],
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                            $movimiento_detalle_ac->update($input_ac);
                        }
                        // }
                    }
                } else {
                    # agrego el nuevo detalle
                    $visibilidad = $request['visibilidad' . $i];
                    if ($visibilidad == 1) {
                        $producto = Producto::find($request['id' . $i]);
                        $input2   = [
                            'id_producto'       => $request['id' . $i],
                            'cantidad'          => $request['cantidad' . $i],
                            'id_encargado'      => $idusuario,
                            'serie'             => $request['serie' . $i],
                            'id_bodega'         => $request['id_bodega' . $i],
                            'tipo'              => '1',
                            'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                            'lote'              => $request['lote' . $i],
                            'descuentop'        => $request['descuento' . $i],
                            'descuento'         => $request['descuentof' . $i],
                            'usos'              => $producto->usos,
                            'precio'            => $request['precio' . $i],
                            'id_pedido'         => $id_pedido,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                        ];

                        $id_movimiento = Movimiento::insertGetId($input2);
                    }
                }
            }
            if ($id_pedido != null) {
                # actualizo el movimiento de inventario o el ingreso pedido
                InvCabMovimientos::actualizarIngresoPedido($id_pedido);
            }
            DB::commit();
            $data['msj'] = 'ok';
            return response()->json($data);
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function ___actualizar_pedido(Request $request)
    {
        //return $request['total'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $pedido = Pedido::findOrFail($request['id_pedido']);

        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'tipo'            => $request['tipo_'],
            'id_empresa'      => $request['id_empresa'],
            'consecion'       => $request['consecion'],
            'observaciones'   => $request['observaciones'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'iva'             => $request['iva'],
            'descuento'       => $request['descuentx'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $pedido->update($input);
        //$this->ValidatePedido_actualizar($request);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required' => 'Ingrese el numero de Lote.',

                ];
                $this->validate($request, $prules, $pmsn);
            }
        }
        $id        = $request['id_pedido'];
        $id_pedido = $id;
        for ($i = 0; $i < $variable; $i++) {
            $serie = $request['serie' . $i];

            $cantidad_pedido = DB::table('movimiento as m')
                ->where('m.id_pedido', $id)
                ->join('pedido as p', 'p.id', 'm.id_pedido')
                ->join('producto as  pro', 'pro.id', 'm.id_producto')
                ->join('bodega as b', 'b.id', 'm.id_bodega')
                ->where('m.tipo', '1')
                ->where('m.estado', '1')
                ->where('m.serie', $serie)
                ->groupBy('m.serie')
                ->select('pro.codigo as codigo', 'pro.nombre as nombre_producto', 'm.cantidad', 'm.serie', 'm.serie', 'b.nombre as nombre_bodega', 'm.lote', 'm.fecha_vencimiento', 'm.precio', 'pro.iva')
                ->first();
            if (!is_null($cantidad_pedido)) {
                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {
                    if ($cantidad_pedido->cantidad == $request['cantidad' . $i]) {
                        $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->get();
                        foreach ($detalles_actualizar as $value) {
                            $movimiento_detalle_ac = Movimiento::find($value->id);
                            $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                            $cantidad_producto     = $producto->cantidad;
                            $input_ac              = [
                                'precio'            => $request['precio' . $i],
                                'lote'              => $request['lote' . $i],
                                'id_bodega'         => $request['id_bodega' . $i],
                                'descuentop'        => $request['descuento' . $i],
                                'descuento'         => $request['descuentof' . $i],
                                'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariomod'     => $idusuario,
                                'consecion_det'     => $request['consecion_det' . $i],
                            ];
                            Log_movimiento::create([
                                'id_producto'     => $movimiento_detalle_ac->id_producto,
                                'id_movimiento'   => $value->id,
                                'id_encargado'    => $idusuario,
                                'observacion'     => "Actualizacion de precio/lote",
                                'tipo'            => '1',
                                'ip_creacion'     => $ip_cliente,
                                'cantidad'        => $request['cantidad' . $i],
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                            $movimiento_detalle_ac->update($input_ac);
                        }
                    } elseif ($cantidad_pedido->cantidad < $request['cantidad' . $i]) {
                        $cantidad   = $request['cantidad' . $i] - $cantidad_pedido->cantidad;
                        $pedido_act = Movimiento::where('serie', $serie)->where('estado', 1)->first();
                        // for ($x = 0; $x < $cantidad; $x++) {
                        $input2 = [
                            'id_producto'       => $pedido_act->id_producto,
                            'cantidad'          => $request['cantidad' . $i],
                            'id_encargado'      => $idusuario,
                            'serie'             => $pedido_act->serie,
                            'id_bodega'         => $pedido_act->id_bodega,
                            'tipo'              => '1',
                            'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                            'lote'              => $request['lote' . $i],
                            'descuentop'        => $request['descuento' . $i],
                            'descuento'         => $request['descuentof' . $i],
                            'usos'              => $pedido_act->usos,
                            'precio'            => $request['precio' . $i],
                            'id_pedido'         => $id_pedido,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'consecion_det'     => $request['consecion_det' . $i],
                        ];

                        $id_movimiento = Movimiento::insertGetId($input2);

                        $id_producto       = $pedido_act->id_producto;
                        $producto          = Producto::find($id_producto);
                        $cantidad_producto = $producto->cantidad;
                        $nueva_cantidad    = $cantidad_producto + 1;

                        $input3 = [
                            'cantidad'        => $nueva_cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        Log_movimiento::create([
                            'id_producto'     => $pedido_act->id_producto,
                            'id_movimiento'   => $id_movimiento,
                            'id_encargado'    => $idusuario,
                            'observacion'     => "Ingreso del producto",
                            'tipo'            => '1',
                            'ip_creacion'     => $ip_cliente,
                            'cantidad'        => $request['cantidad' . $i],
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        Producto::where('id', $id_producto)->update($input3);
                        // }
                    } elseif ($cantidad_pedido->cantidad > $request['cantidad' . $i]) {
                        $cantidad            = $cantidad_pedido->cantidad - $request['cantidad' . $i];
                        $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->first();
                        // for ($x = 0; $x < $cantidad; $x++) {
                        $movimiento_detalle_ac = Movimiento::find($detalles_actualizar->id);
                        $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                        $cantidad_producto     = $producto->cantidad;
                        $nueva_cantidad        = $cantidad_producto - 1;
                        $input3                = [
                            'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];
                        $input_ac = [
                            'tipo'            => 4,
                            'estado'          => 0,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'consecion_det'   => $request['consecion_det'],
                        ];
                        Log_movimiento::create([
                            'id_producto'     => $movimiento_detalle_ac->id_producto,
                            'id_movimiento'   => $detalles_actualizar->id,
                            'id_encargado'    => $idusuario,
                            'observacion'     => "Eliminacion de Producto de pedido",
                            'tipo'            => '4',
                            'ip_creacion'     => $ip_cliente,
                            'cantidad'        => $request['cantidad' . $i],
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        $movimiento_detalle_ac->update($input_ac);
                        $producto->update($input3);
                        // }
                    }
                } else {
                    $detalles_actualizar = Movimiento::where('serie', $serie)->where('estado', 1)->get();
                    foreach ($detalles_actualizar as $value) {
                        $movimiento_detalle_ac = Movimiento::find($value->id);
                        $producto              = Producto::find($movimiento_detalle_ac->id_producto);
                        $cantidad_producto     = $producto->cantidad;
                        $nueva_cantidad        = $cantidad_producto - 1;
                        $input3                = [
                            'cantidad'        => $nueva_cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];
                        $input_ac = [
                            'tipo'            => 4,
                            'estado'          => 0,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];
                        Log_movimiento::create([
                            'id_producto'     => $movimiento_detalle_ac->id_producto,
                            'id_movimiento'   => $value->id,
                            'id_encargado'    => $idusuario,
                            'observacion'     => "Eliminacion de Producto de pedido",
                            'tipo'            => '4',
                            'ip_creacion'     => $ip_cliente,
                            'cantidad'        => '1',
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                        $movimiento_detalle_ac->update($input_ac);
                        $producto->update($input3);
                    }
                }
            } else {
                $visibilidad = $request['visibilidad' . $i];
                if ($visibilidad == 1) {
                    $cantidad = $request['cantidad' . $i];

                    // for ($x = 0; $x < $cantidad; $x++) {
                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => $request['cantidad' . $i],
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'descuentop'        => $request['descuento' . $i],
                        'descuento'         => $request['descuentof' . $i],
                        'usos'              => $request['usos' . $i],
                        'precio'            => $request['precio' . $i],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => $request['consecion_det' . $i],
                    ];

                    $id_movimiento = DB::table('movimiento')->insertGetId($input2);

                    $id_producto       = $request['id' . $i];
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Log_movimiento::create([
                        'id_producto'     => $request['id' . $i],
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Ingreso del producto",
                        'tipo'            => '1',
                        'ip_creacion'     => $ip_cliente,
                        'cantidad'        => $request['cantidad' . $i],
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Producto::where('id', $id_producto)->update($input3);
                    // }
                }
            }
        }
        $url = route('producto.index');
        return $url;
    }

    public function eliminar_pedido($id)
    {
        $seq = InvDocumentosBodegas::getSecueciaTipo(env('BODEGA_EGR_PACI1', 14), 'T');
        //dd($seq);
        // $this->anularIngPedido($id);
        // $cabcera        = InvCabMovimientos::where('id_pedido',1)->where('estado',1)->first();
        // $traslados      = InvTrasladosBodegas::where('id_inv_cab_mov_origen', $cabcera->id)->orderBy('id','asc')->get();
        // dd($traslados);
        $productos = Movimiento::where('id_pedido', $id)
            ->where(function ($query) {
                $query->where('tipo', 0)
                    ->orWhere('tipo', 2)->orWhere('tipo', 4);
            })->get();
        //dd($productos);
        return view('insumos/ingreso/eliminar', ['productos' => $productos, 'id' => $id]);
    }

    public function eliminar_clave(Request $request)
    {
        DB::beginTransaction();
        try {
            $ingresada      = $request['password'];
            $id             = $request['id'];
            $hashedPassword = Auth::user()->password;
            $pedido_v       = Pedido::find($id);
            if (Hash::check($ingresada, $hashedPassword) && $pedido_v->estado_contable == 0) {
                $productos = Movimiento::where('id_pedido', $id)->get();
                foreach ($productos as $value) {
                    Log_movimiento::where('id_movimiento', $value->id)->delete();
                    $producto           = Producto::find($value->id_producto);
                    $cantidad           = $producto->cantidad - 1;
                    $producto->cantidad = $cantidad;
                    $producto->save();
                    $movimiento = Movimiento::find($value->id);
                    $movimiento->delete();
                }
                $this->anularIngPedido($id);
                $pedido = Pedido::find($id);
                $pedido->delete();
                DB::commit();
                return "okay";
            }
            // DB::rollBack();
            return "no";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function anularIngPedido($id_pedido)
    {
        # busco el documento
        $cabcera = InvCabMovimientos::where('id_pedido', $id_pedido)->where('estado', 1)->first();
        if ($cabcera->id) {
            # busco los traslados
            $traslados = InvTrasladosBodegas::where('id_inv_cab_mov_origen', $cabcera->id)->get();
            foreach ($traslados as $row) {
                InvCabMovimientos::anularTransaccionBodega($row->id_inv_cab_movimientos);
                // break;
            }
            # se anulan primero los traslados y se generan los egresos
            InvCabMovimientos::anularTransaccionBodega($cabcera->id);
            if (!is_null($cabcera->id_asiento)) {
                InvContableCab::anularAsiento($cabcera->id_asiento);
            }


            //dd('entra');
            $cabcera->id_pedido = null;
            $cabcera->save();
        }
    }

    public function consecion_detalle()
    {
        $consecion = Pedido::where('consecion', '1')->get();
        //dd($consecion);
        foreach ($consecion as $value) {
            foreach ($value->detalle as $detalle) {
                //dd($detalle);
                $arr = [
                    'consecion_det'   => '1',
                    'ip_modificacion' => 'cambio consecion',
                ];
                $detalle->update($arr);
            }
        }

        return "ok";
    }
    public function generar_factura(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'tipo'            => '3',
            'factura'         => $request['num_factura'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'id_empresa'      => $request['id_empresa'],
            'observaciones'   => $request['observaciones'],
            'consecion'       => $request['consecion'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'descuento'       => $request['descuentx'],
            'iva'             => $request['iva'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->validatePedido($request);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required' => 'Ingrese el numero de Lote.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }

        $id_pedido = Pedido::insertGetId($input);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $cantidad          = $request['cantidad' . $i];
                $consecion_detalle = 0;
                if ($request['consecion_det' . $i] == 1) {
                    $consecion_detalle = 1;
                }
                for ($x = 0; $x < $cantidad; $x++) {
                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => '1',
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'usos'              => $request['usos' . $i],
                        'descuentop'        => $request['descuento' . $i],
                        'descuento'         => $request['descuentof' . $i],
                        'precio'            => $request['precio' . $i],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => $consecion_detalle,

                    ];
                    $id_movimiento     = DB::table('movimiento')->insertGetId($input2);
                    $id_producto       = $request['id' . $i];
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $nueva_cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Log_movimiento::create([
                        'id_producto'     => $request['id' . $i],
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Ingreso del producto",
                        'tipo'            => '1',
                        'ip_creacion'     => $ip_cliente,
                        'cantidad'        => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Producto::where('id', $id_producto)->update($input3);
                }
            }
        }

        return redirect()->route('codigo.barra');
    }
    public function conglomerada(Request $request)
    {
        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->get();
        return view('insumos/ingreso/master', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa]);
    }
    public function details(Request $request)
    {
        $serie       = $request['serie'];
        $series      = $request['series'];
        $pedido      = $request['pedido'];
        $codigo      = $request['codigo'];
        $lote        = $request['lote'];
        $tipo        = $request['tipo'];
        $idproveedor = $request['proveedor'];
        $fdesde      = $request['desde'];
        $fhasta      = $request['hasta'];
        $ds          = DB::table('pedido')->where('pedido', $pedido)->first();
        $proveedor   = null;
        $objpedido   = null;
        if (isset($ds->id)) {
            $proveedor = Proveedor::find($ds->id_proveedor);
        }
        $val_series = "";
        $separador  = "";
        if (!empty($series)) {
            foreach ($series as $row) {
                $val_series .= "$separador '$row'";
                $separador = ",";
            }
        }

        $productos = array();
        if ($tipo == 1) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vence as fecha_vencimiento,det.lote,
            det.descuento as descuentop, p.id, p.id_proveedor, prov.razonsocial, p.fecha,
            p.vencimiento, prov.id_cuentas, px.nombre,cab.id_documento_bodega as id_bodega,det.subtotal as precio,
            px.iva, prov.direccion, p.id_proveedor, p.id_empresa, p.pedido, b.nombre as bodega, prov.nombrecomercial
            FROM inv_det_movimientos det
            JOIN inv_cab_movimientos cab ON det.id_inv_cab_movimientos =cab.id
            JOIN inv_documentos_bodegas doc ON cab.id_documento_bodega = doc.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN pedido AS p ON p.id = cab.id_pedido
            INNER JOIN proveedor AS prov ON prov.id = p.id_proveedor
            JOIN bodega b ON b.id =cab.id_bodega_origen
            WHERE 1 = 1 ";
            if ($serie != "") {
                $sql .= " AND det.serie = '" . $serie . "' ";
            }
            if ($codigo != "") {
                $sql .= " AND px.codigo = '" . $codigo . "' ";
            }
            if ($lote != "") {
                $sql .= " AND det.lote = '" . $lote . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 2) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vence as fecha_vencimiento,det.lote,
            det.descuento as descuentop, p.id, p.id_proveedor, prov.razonsocial, p.fecha,
            p.vencimiento, prov.id_cuentas, px.nombre,cab.id_documento_bodega as id_bodega,det.subtotal as precio,
            px.iva, prov.direccion, p.id_proveedor, p.id_empresa, p.pedido, b.nombre as bodega, prov.nombrecomercial
            FROM inv_det_movimientos det
            JOIN inv_cab_movimientos cab ON det.id_inv_cab_movimientos =cab.id
            JOIN inv_documentos_bodegas doc ON cab.id_documento_bodega = doc.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN pedido AS p ON p.id = cab.id_pedido
            INNER JOIN proveedor AS prov ON prov.id = p.id_proveedor
            JOIN bodega b ON b.id =cab.id_bodega_origen
            WHERE   det.serie NOT IN
            (
                SELECT  d.serie
                FROM    detalle_pedido d
                INNER JOIN pedido AS p ON p.id = d.id_pedido
                WHERE  p.pedido='" . $pedido . "'
            )";
            if ($pedido != "") {
                $sql .= " AND p.pedido = '" . $pedido . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 3) {
            $sql = " SELECT px.codigo, d.cantidad, d.id_producto, d.serie, d.fecha_vence as fecha_vencimiento, d.lote, d.descuento as descuentop, p.id
                        , p.id_proveedor, prov.nombrecomercial, p.fecha, p.vencimiento, prov.id_cuentas, px.nombre, c.id_bodega_origen as id_bodega
                        , d.valor_unitario as precio, px.iva, prov.direccion, p.id_empresa, p.pedido, b.nombre as bodega
                        FROM inv_cab_movimientos c
                        JOIN inv_det_movimientos d ON c.id = d.id_inv_cab_movimientos
                        JOIN pedido p ON p.id = d.id_pedido
                        JOIN inv_documentos_bodegas doc ON c.id_documento_bodega = doc.id
                        JOIN proveedor AS prov ON prov.id = p.id_proveedor
                        JOIN producto px ON px.id = d.id_producto
                        JOIN bodega b ON b.id = c.id_bodega_origen
                        LEFT JOIN detalle_pedido xz ON px.id=xz.id_producto and p.id = xz.id_pedido
                        WHERE c.estado = 1
                        AND xz.id IS NULL
                        AND p.id_proveedor = '$idproveedor'
                        AND doc.abreviatura_documento = 'EGP'
                        AND p.fecha BETWEEN '$fdesde' AND '$fhasta' ";
            if ($val_series != "") {
                $sql .= " AND d.serie NOT IN ( $val_series ) ";
            }
        }
        $productos = DB::select(DB::raw($sql));

        if (isset($productos[0]->id) and $proveedor == null) {
            $proveedor = Proveedor::find($productos[0]->id_proveedor);
        }
        if ($objpedido == null && isset($productos[0]->pedido)) {
            $objpedido = Pedido::find($productos[0]->id);
        }
        // dd($pedido);
        $bodegas = Bodega::where('estado', '1')->get();
        return view('insumos/ingreso/details', [
            'numero_pedido' => $pedido, 'bodegas'      => $bodegas,
            'productos'                                             => $productos, 'proveedor' => $proveedor, 'objpedido' => $objpedido
        ]);
    }
    public function conglomeradaAnterior(Request $request)
    {
        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();
        $empresa     = Empresa::all();
        return view('insumos/ingreso/master_anterior', ['bodegas' => $bodegas, 'proveedores' => $proveedores, 'empresa' => $empresa]);
    }
    public function detailsConglomeradaAnterior(Request $request)
    {
        $serie       = $request['serie'];
        $series      = $request['series'];
        $pedido      = $request['pedido'];
        $codigo      = $request['codigo'];
        $lote        = $request['lote'];
        $tipo        = $request['tipo'];
        $idproveedor = $request['proveedor'];
        $fdesde      = $request['desde'];
        $fhasta      = $request['hasta'];
        $ds          = DB::table('pedido')->where('pedido', $pedido)->first();
        $proveedor   = null;
        $objpedido   = null;
        if (isset($ds->id)) {
            $proveedor = Proveedor::find($ds->id_proveedor);
        }
        $val_series = "";
        $separador  = "";
        if (!empty($series)) {
            foreach ($series as $row) {
                $val_series .= "$separador '$row'";
                $separador = ",";
            }
        }

        $productos = array();
        if ($tipo == 1) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vencimiento,det.lote,
            det.descuento as descuentop, cab.id, cab.id_proveedor, prov.razonsocial, cab.fecha,
            cab.vencimiento, prov.id_cuentas, px.nombre,det.precio,
            px.iva, prov.direccion, cab.id_proveedor, cab.id_empresa, cab.pedido, b.nombre as bodega, prov.nombrecomercial,
            cab.id_bodega, prov.nombrecomercial, mp.id as id_movimiento_paciente
            FROM movimiento det
            JOIN pedido cab ON det.id_pedido =cab.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN proveedor AS prov ON prov.id = cab.id_proveedor
            LEFT JOIN bodega b ON b.id =cab.id_bodega
            LEFT JOIN movimiento_paciente mp ON mp.id_movimiento = det.id
            WHERE 1 = 1 AND cab.tipo = 1 ";
            if ($serie != "") {
                $sql .= " AND det.serie = '" . $serie . "' ";
            }
            if ($codigo != "") {
                $sql .= " AND px.codigo = '" . $codigo . "' ";
            }
            if ($lote != "") {
                $sql .= " AND det.lote = '" . $lote . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 2) {
            $sql = " SELECT px.codigo,det.cantidad,det.id_producto,det.serie,det.fecha_vencimiento ,det.lote,
            det.descuento as descuentop, cab.id, cab.id_proveedor, prov.razonsocial, cab.fecha,
            cab.vencimiento, prov.id_cuentas, px.nombre, det.precio,
            px.iva, prov.direccion,cab.id_proveedor, cab.id_empresa, cab.pedido, b.nombre as bodega, prov.nombrecomercial,
            cab.id_bodega
            FROM movimiento det
            JOIN pedido cab ON det.id_pedido =cab.id
            JOIN producto px ON px.id =det.id_producto
            INNER JOIN proveedor AS prov ON prov.id = cab.id_proveedor
            LEFT JOIN bodega b ON b.id =cab.id_bodega
            WHERE det.serie NOT IN
            (
                SELECT  d.serie
                FROM    detalle_pedido d
                INNER JOIN pedido AS p ON p.id = d.id_pedido
                WHERE  p.pedido='$pedido'
            ) ";
            if ($pedido != "") {
                $sql .= " AND cab.pedido = '" . $pedido . "' ";
            }
            if ($val_series != "") {
                $sql .= " AND det.serie NOT IN ( $val_series ) ";
            }
        }
        if ($tipo == 3) {
            $sql = " SELECT px.codigo, d.cantidad, d.id_producto, d.serie, d.fecha_vencimiento, d.lote, d.descuento as descuentop, c.id
                , c.id_proveedor, prov.nombrecomercial, c.fecha, c.vencimiento, prov.id_cuentas, px.nombre, c.id_bodega
                , d.precio, px.iva, prov.direccion, c.id_empresa, c.pedido, b.nombre as bodega, mp.id, prov.razonsocial,
                c.id_bodega
                FROM movimiento_paciente mp
                JOIN movimiento d ON mp.id_movimiento = d.id
                JOIN pedido c ON d.id_pedido =c.id
                JOIN proveedor AS prov ON prov.id = c.id_proveedor
                JOIN producto px ON px.id = d.id_producto
                JOIN bodega b ON b.id = c.id_bodega
                LEFT JOIN detalle_pedido xz ON px.id=xz.id_producto and c.id = xz.id_pedido
                WHERE xz.id IS NULL
                AND c.fecha BETWEEN '$fdesde' AND '$fhasta' ";
            if ($val_series != "") {
                $sql .= " AND d.serie NOT IN ( $val_series ) ";
            }
        }
        $productos = DB::select(DB::raw($sql));

        if (isset($productos[0]->id) and $proveedor == null) {
            $proveedor = Proveedor::find($productos[0]->id_proveedor);
        }
        if ($objpedido == null && isset($productos[0]->id)) {
            $objpedido = Pedido::find($productos[0]->id);
        }
        $bodegas = Bodega::where('estado', '1')->get();
        return view('insumos/ingreso/details', [
            'numero_pedido' => $pedido, 'bodegas'      => $bodegas,
            'productos'                                             => $productos, 'proveedor' => $proveedor, 'objpedido' => $objpedido, 'conglomerada' => 1, 'anterior' => 1
        ]);
    }
    public function store_new(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        //dd($request->all());
        DB::beginTransaction();

        try {
            $input = [
                'id_proveedor'    => $request['id_proveedor'],
                'pedido'          => $request['num_factura'],
                'tipo'            => '20',
                'factura'         => $request['num_factura'],
                'fecha'           => $request['fecha'],
                'vencimiento'     => $request['vencimiento'],
                'id_empresa'      => $request['id_empresa'],
                'observaciones'   => $request['observaciones'],
                'consecion'       => $request['consecion'],
                'subtotal_12'     => $request['subtotal_12'],
                'subtotal_0'      => $request['subtotal_0'],
                'descuento'       => $request['descuentx'],
                'iva'             => $request['iva'],
                'total'           => $request['total'],
                'id_empresa'      => Session::get('id_empresa'),
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            //$this->validatePedido($request);

            $id_pedido = Pedido::insertGetId($input);

            for ($x = 0; $x < count($request['cantidad']); $x++) {
                $codeP       = Producto::find($request['id'][$x]);
                $pedido_find = Pedido::find($id_pedido);
                if (isset($pedido_find->id)) {
                    Detalle_Pedido::create([
                        'id_pedido'       => $pedido_find->id,
                        'cantidad'        => trim($request['cantidad'][$x]),
                        'total'           => trim($request['precio'][$x]),
                        'id_producto'     => trim($request['id'][$x]),
                        'serie'           => trim($request['serie'][$x]),
                        'estado'          => '1',
                        'observacion'     => 'Ingreso de pedido por factura conglomerada',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]);
                    $input2 = [
                        'id_producto'       => $request['id'][$x],
                        'cantidad'          => trim($request['cantidad'][$x]),
                        'id_encargado'      => $idusuario,
                        'serie'             => trim($request['serie'][$x]),
                        'id_bodega'         => trim($request['bodega'][$x]),
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento'][$x],
                        'lote'              => $request['lote'][$x],
                        'usos'              => $codeP->usos,
                        'descuentop'        => $request['pDescuento'][$x],
                        'descuento'         => $request['descuento'][$x],
                        'precio'            => $request['precio'][$x],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'consecion_det'     => '0',
                    ];
                    $id_movimiento     = DB::table('movimiento')->insertGetId($input2);
                    $id_producto       = $request['id'][$x];
                    $producto          = Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $nueva_cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $precio_uni   = str_replace(",", "", $request['precio'][$x]);
                    $precio_total = $request['cantidad'][$x] * $precio_uni;
                    /*Log_Detalle_Pedido::create([
                    'id_pedido'=>$pedido_find->id,
                    'id_pedidon'=>$id_pedido,
                    'concepto'=>'LOG DE ENTRADA '.date('Y-m-d'),
                    'valor'=>$precio_total,
                    'valor_ant'=>$pedido_find->total,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    ]); */
                    Log_movimiento::create([
                        'id_producto'     => $request['id'][$x],
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Ingreso del producto conglomerada",
                        'tipo'            => '1',
                        'ip_creacion'     => $ip_cliente,
                        'cantidad'        => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Producto::where('id', $id_producto)->update($input3);
                }
            }

            Inventario::consigna($id_pedido, Session::get('id_empresa'));

            DB::commit();
            return response()->json('Guardado correctamente');
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function update_conglomerada(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        //dd($request->all());
        DB::beginTransaction();

        try {
            $input = [
                'id_proveedor'    => $request['id_proveedor'],
                'pedido'          => $request['num_factura'],
                'factura'         => $request['num_factura'],
                'fecha'           => $request['fecha'],
                'vencimiento'     => $request['vencimiento'],
                'id_empresa'      => $request['id_empresa'],
                'observaciones'   => $request['observaciones'],
                'subtotal_12'     => $request['subtotal_12'],
                'subtotal_0'      => $request['subtotal_0'],
                'descuento'       => $request['descuentx'],
                'iva'             => $request['iva'],
                'total'           => $request['total'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            //$this->validatePedido($request);
            $id_pedido   = $request['id_pedido'];
            $pedido_find = Pedido::find($id_pedido);
            Pedido::where('id', $request['id_pedido'])->update($input);

            /* BORRO LOS DETALLE Y SE VUELVEN A CREAR */
            $log_detalle_pedido = Log_Detalle_Pedido::where('id_pedidon', $request['id_pedido'])->delete();
            $detalle_pedido     = Detalle_Pedido::where('id_pedido', $request['id_pedido'])->delete();
            $movimientos        = Movimiento::where('id_pedido', $request['id_pedido'])->get();
            foreach ($movimientos as $value) {
                Log_movimiento::where('id_movimiento', $value->id)->delete();
                $movimiento = Movimiento::find($value->id);
                $movimiento->delete();
            }
            for ($x = 0; $x < count($request['cantidad']); $x++) {
                $codeP = Producto::find($request['id'][$x]);
                Detalle_Pedido::create([
                    'id_pedido'       => $pedido_find->id,
                    'cantidad'        => $request['cantidad'][$x],
                    'total'           => '0',
                    'id_producto'     => $request['id'][$x],
                    'serie'           => $request['serie'][$x],
                    'estado'          => '1',
                    'observacion'     => 'Ingreso de pedido por factura',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $input2 = [
                    'id_producto'       => $request['id'][$x],
                    'cantidad'          => $request['cantidad'][$x],
                    'id_encargado'      => $idusuario,
                    'serie'             => $request['serie'][$x],
                    'id_bodega'         => $request['bodega'][$x],
                    'tipo'              => '1',
                    'fecha_vencimiento' => $request['fecha_vencimiento'][$x],
                    'lote'              => $request['lote'][$x],
                    'usos'              => $codeP->usos,
                    'descuentop'        => $request['pDescuento'][$x],
                    'descuento'         => $request['descuento'][$x],
                    'precio'            => $request['precio'][$x],
                    'id_pedido'         => $id_pedido,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'consecion_det'     => '0',

                ];
                $id_movimiento     = DB::table('movimiento')->insertGetId($input2);
                $id_producto       = $request['id'][$x];
                $producto          = Producto::find($id_producto);
                $cantidad_producto = $producto->cantidad;
                $nueva_cantidad    = $cantidad_producto + 1;

                $input3 = [
                    'cantidad'        => $nueva_cantidad,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $precio_total = $request['cantidad'][$x] * $request['precio'][$x];
                Log_Detalle_Pedido::create([
                    'id_pedido'      => $pedido_find->id,
                    'id_pedidon'     => $id_pedido,
                    'concepto'       => 'LOG DE ENTRADA ' . date('Y-m-d'),
                    'valor'          => $precio_total,
                    'valor_ant'      => $pedido_find->total,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                ]);
                Log_movimiento::create([
                    'id_producto'     => $request['id'][$x],
                    'id_movimiento'   => $id_movimiento,
                    'id_encargado'    => $idusuario,
                    'observacion'     => "Ingreso del producto",
                    'tipo'            => '1',
                    'ip_creacion'     => $ip_cliente,
                    'cantidad'        => '1',
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
                Producto::where('id', $id_producto)->update($input3);
            }

            DB::commit();
            return response()->json('Guardado correctamente');
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function editar_conglomerada($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido = pedido::findOrFail($id);

        $bodegas     = Bodega::where('estado', '1')->get();
        $proveedores = Proveedor::where('estado', '1')->get();

        $empresa = Empresa::all();

        /***************************/

        $cantidad_pedido = DB::table('movimiento as m')
            ->where('m.id_pedido', $id)
            ->join('pedido as p', 'p.id', 'm.id_pedido')
            ->join('producto as  pro', 'pro.id', 'm.id_producto')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->groupBy('m.serie')
            ->select(
                'm.id',
                'pro.codigo as codigo',
                'pro.nombre as nombre_producto',
                DB::raw('m.cantidad, m.serie'),
                'm.serie',
                'b.nombre as nombre_bodega',
                'm.lote',
                'm.fecha_vencimiento',
                'pro.usos',
                'm.precio',
                'm.descuentop',
                'm.descuento',
                'pro.iva',
                'pro.id as id_producto',
                'pro.registro_sanitario',
                'm.consecion_det',
                'p.id_proveedor'
            )
            ->get();

        /***************************/
        $vence      = $pedido->vencimiento;
        $documentos = InvDocumentosBodegas::where('id_inv_tipo_movimiento', 1)->get();
        $bod_princ  = Bodega::where('estado', '1')->where('id', env('BODEGA_PRINCIPAL', 1))->first();
        return view('insumos/ingreso/master', [
            'bodegas' => $bodegas, 'proveedores'    => $proveedores, 'empresa'        => $empresa,
            'pedido'                                         => $pedido, 'cantidad_pedido' => $cantidad_pedido, 'doc_bodega' => $documentos, 'bodega_principal' => $bod_princ
        ]);
    }

    public function crear_orden_conglomerada(Request $request)
    {
        // dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido                     = pedido::findOrFail($request->id_pedido);
        $pedido->orden_conglomerada = 1;
        $pedido->save();
    }

    public function anular_envio_orden(Request $request)
    {
        // dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $pedido                     = pedido::findOrFail($request->id_pedido);
        $pedido->orden_conglomerada = 0;
        $pedido->save();
    }

    public function eliminar_conglomerada($id_pedido)
    {
        $pedido   = Pedido::find($id_pedido);
        $eliminar = null;
        if ($pedido->orden_conglomerada == 1) {
            $eliminar = 1;
        }
        return view('insumos/ingreso/eliminar_conglomerada', ['eliminar' => $eliminar, 'id' => $id_pedido]);
    }

    public function eliminar_conglomerada_clave(Request $request)
    {
        DB::beginTransaction();
        try {
            $idusuario  = Auth::user()->id;
            $ingresada      = $request['password'];
            $id             = $request['id'];
            $hashedPassword = Auth::user()->password;
            $pedido_v       = Pedido::find($id);
            if (Hash::check($ingresada, $hashedPassword) && $pedido_v->estado_contable == 0) {

                $productos = Movimiento::where('id_pedido', $id)->get();
                // movimientos inventario
                $inv_movimientos = InvCabMovimientos::where('id_pedido', $id)->get();
                foreach ($inv_movimientos as $mov) {
                    if(Auth::user()->id == '0953905999'){
                        dd("aqui", $mov);
                    }
                    $movimiento            = InvCabMovimientos::find($mov->id);
                    $movimiento->id_pedido = null;
                    $movimiento->save();

                    if (!is_null($mov->id_asiento)) {
                        LogAsiento::anularAsiento($mov->id_asiento, 'I-PG');
                    }

                    $inv_det_mov = $mov->detalles;

                    foreach ($inv_det_mov as $inv_det) {
                        $inv_det->estado = 0;
                        $inv_det->id_usuariomod  = $idusuario;
                        $inv_det->save();

                        $inv_kardex = InvKardex::where('id_inv_det_movimientos', $inv_det->id)->first();
                        $inv_kardex->estado = 0;
                        $inv_kardex->id_usuariomod  = $idusuario;
                        $inv_kardex->save();
                    }
                }

                $detalles = Detalle_Pedido::where('id_pedido', $id)->get();
                foreach ($detalles as $row) {
                    $detalle = Detalle_Pedido::find($row->id);
                    $detalle->delete();
                }
                foreach ($productos as $value) {
                    Log_movimiento::where('id_movimiento', $value->id)->delete();
                    $producto           = Producto::find($value->id_producto);
                    $cantidad           = $producto->cantidad - 1;
                    $producto->cantidad = $cantidad;
                    $producto->save();
                    $movimiento = Movimiento::find($value->id);
                    $movimiento->delete();
                }
                // $this->anularIngPedido($id);
                $pedido = Pedido::find($id);
                $pedido->delete();
                DB::commit();
                return "okay";
            }
            // DB::rollBack();
            return "no";
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function validarNumeroPedido(Request $request)
    {
        dd($request->all());
    }
}
