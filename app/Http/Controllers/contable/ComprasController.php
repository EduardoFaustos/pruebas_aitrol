<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Faker\Provider\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Session;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Detalle_Acreedores;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_rubros;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inventario;
use Sis_medico\Log_Contable;
use Sis_medico\Pedido;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\Validate_Decimals;
use Sis_medico\LogConfig;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Cruce_Cuentas;

class ComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa       = $request->session()->get('id_empresa');
        $proveedor        = Proveedor::all();
        $empresa          = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $compras          = DB::table('ct_compras as ct_c')
            ->leftjoin('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
        //->where('ct_c.estado', '1')
            ->select('ct_c.id', 'ct_c.rutapdf', 'ct_c.numero', 'ct_c.fecha', 'p.razonsocial', 'ct_c.autorizacion', 'u.nombre1', 'u.apellido1', 'ct_c.secuencia_factura', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.observacion', 'ct_c.id_asiento_cabecera')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->orderby('ct_c.id', 'desc')
            ->paginate(10);

        $var = 2;

        return view('contable/compra/index', ['compras' => $compras, 'empresa' => $empresa, 'tipo_comprobante' => $tipo_comprobante, 'proveedor' => $proveedor]);
    }

    public function proveedorsearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where('razonsocial', 'LIKE', '%' . $request['search'] . '%')->where('estado', '1')->select('proveedor.id as id', 'proveedor.razonsocial as text')->get();
        }

        return response()->json($proveedor);
    }

    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas = Ct_Divisas::where('estado', '1')->get();
        //dd($divisas);
        $id_empresa     = $request->session()->get('id_empresa');
        $proveedor      = proveedor::where('estado', '1')->get();
        $bodega         = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $c_tributario   = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante  = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $tipo_pago      = Ct_Forma_Pago::where('estado', '1')->get();
        $tipo_tarjeta   = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $lista_banco    = Ct_Bancos::where('estado', '1')->get();

        //$cuentaiva = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_IVA');

        $cuentaiva = LogConfig::busqueda('4.1.01.02');
        
        // /$cuentacaja = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_CAJA');

        //dd($cuentaiva, $cuentacaja);
        //$iva_param      = Ct_Configuraciones::where('id_plan', $cuentaiva->cuenta_guardar)->first();
        $iva_param      = Ct_Configuraciones::where('id_plan', $cuentaiva)->first();
        //$caja_chica     = Ct_Configuraciones::where('id_plan', $cuentacaja->cuenta_guardar)->first();
        $rubros         = Ct_rubros::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $termino        = Ct_Termino::where('estado', '1')->get();
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $empresa_sucurs = Empresa::findorfail($id_empresa);
        //dd($empresa_sucurs);

        $empresa_general = Empresa::all();
        $sucursales      = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        //dd($sucursales);
        return view('contable/compra/create', ['divisas' => $divisas, 'empresa' => $empresa, 'rubros' => $rubros, 'iva_param' => $iva_param, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco, 'tipo_pago' => $tipo_pago, 'proveedor' => $proveedor, 'productos' => $productos, 'bodega' => $bodega, 't_comprobante' => $t_comprobante, 'c_tributario' => $c_tributario, 'empresa_sucurs' => $empresa_sucurs, 'empresa_general' => $empresa_general, 'termino' => $termino, 'sucursales' => $sucursales]);
    }

    private function valida_secuencia(Request $request)
    {
        $prules = [
            'secuencia_factura' => 'unique:secuencia_factura',
        ];
        $pmsn = [
            'secuencia_factura.unique' => 'El número de factura ya está registrado',
        ];
        $this->validate($request, $prules, $pmsn);
    }
    private static function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
    public function estado_comprobante($data)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = $this->getNonce(12);
        $empresa    = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        if ($data['tipo'] == "comprobante") {
            $url = "https://api.pagosyfacturas.com/api/billing/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "pdf") {
            $url = "https://api.pagosyfacturas.com/api/billing/ride/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "xml") {
            $url = "https://api.pagosyfacturas.com/api/billing/xml/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } else {
            return "no exite el tipo";
        }
        //dd($url);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => 'prueba',
            ),
        );
        //dd($options);
        $context = stream_context_create($options);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        return $response;
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (Auth::user()->id == "0953905999") {
            // dd($request->all());
        }

        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $id_empresa      = $request->session()->get('id_empresa');
        $idusuario       = Auth::user()->id;
        $iva_acreditable = $request['iva_final'];
        $objeto_validar  = new Validate_Decimals();
        DB::beginTransaction();
        try {

            $pedido = Pedido::where('pedido', $request['pedido_nombre'])->first();
            if (!is_null($pedido)) {
                if ($pedido->tipo == 3) {
                    $cabecera_c = [
                        'observacion'     => 'Salida de Consignacion Pedido ' . $pedido->id,
                        'fecha_asiento'   => $request['fecha'],
                        'fact_numero'     => $request['secuencia_factura'],
                        'valor'           => $request['base1'],
                        'id_empresa'      => $id_empresa,
                        'estado'          => '1',
                        'aparece_sri'     => $request['archivosri'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];
                    // $cuenta_cambio = '2.01.03.01.03';
                    // $nombre = "CxP Proveedores locales consignacion";
                    // if($id_empresa == "1793135579001"){
                    //     $cuenta_cambio = "1436";
                    //     $nombre = "PROVISIONES CUENTAS POR PAGAR MERCADERÍA EN CONSIGNACIÓN";
                    // }

                    $cuentacxpmerc = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_CXPMERCADERIA');
                      //$cuentamerc_consig = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_MERCADERIA_CONSIG');
                     // $id_plan_confg = LogConfig::busqueda('1.01.03.01.01');
                    //$cuentacxpmerc = Plan_Cuentas::find($cuentacxpmerc);

                    $id_cabecera_c = Ct_Asientos_Cabecera::insertGetId($cabecera_c);
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_cabecera_c,
                        'id_plan_cuenta'      => $cuentacxpmerc->cuenta_guardar,
                        'descripcion'         => $cuentacxpmerc->nombre_mostrar,
                        'fecha'               => $request['fecha'],
                        'debe'                => $request['base1'],
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);

                    // $cuenta_cambio = '1.01.03.01.01';
                    // if($id_empresa == "1793135579001"){
                    //     $cuenta_cambio = "1.01.03.01.04";
                    // }
                    
                    //$cuentamerc_consig = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_MERCADERIA_CONSIG');
                    $id_plan_confg = LogConfig::busqueda('1.01.03.01.01');
                    $cuentamerc_consig = Plan_Cuentas::find($id_plan_confg);
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_cabecera_c,
                        'id_plan_cuenta'      => $cuentamerc_consig->id,
                        'descripcion'         => $cuentamerc_consig->nombre,
                        'fecha'               => $request['fecha'],
                        'debe'                => '0',
                        'haber'               => $request['base1'],
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);

                }
                $pedido->estado_contable = 1;
                $pedido->save();
            }
           // $sucursal      = $request['sucursal'];
            $errores       = "";
            //$cuentaiva = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_IVA');
            
            $id_plan_confg = LogConfig::busqueda('VENTA_TARIFA_12');
           // $cuentaiva = 
            $iva_param     = Ct_Configuraciones::where('id_plan',$id_plan_confg)->first();
            $punto_emision = $request['serie'];
            $sucursal      = substr($punto_emision, 0, -4);
            $punto_emision = substr($punto_emision, 4);

            
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            //dd($cod_sucurs);
            if(!is_null($cod_sucurs)){
                $sucursal2 = $cod_sucurs->codigo_sucursal;
            }else{
                $sucursal2 = "0";
            }
            
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();

            if(!is_null($cod_caj)){
                $punto_emision2     = $cod_caj->codigo_caja;
            }else{
                $punto_emision2 = "0";
            }

            
            $empresa      = Empresa::find($id_empresa);
            $total_final  = $objeto_validar->set_round($request['total1']);
            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->get()->count();
            
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                $max_id = intval($max_id->secuencia_f);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }

            $numeroconcadenado   = $request['serie'] . '-' . $request['secuencia_factura'];

            //$numeroconcadenado   = "{$c_sucursal}-{$c_caja}-{$request['secuencia_factura']}";
            $comprobacion_compra = Ct_compras::where('numero', $numeroconcadenado)->where('tipo', '1')->where('proveedor', $request['proveedor'])->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->first();
            
            if (is_null($comprobacion_compra) || $comprobacion_compra == '[]') {
                $fechahoy = $request['fecha'];
                // $comp= Ct_compras::where('pedido',$request->pedido_nombre)->where('estado','<>','0')->first();
                // if(!is_null($comp)){
                //     return response()->json(['errores'=>'Ya existe factura con este pedido']);
                // }
                $cabeceraa = [
                    'observacion'     => $request['observacion'],
                    'fecha_asiento'   => $request['fecha'],
                    'fact_numero'     => $request['secuencia_factura'],
                    'valor'           => $total_final,
                    'id_empresa'      => $id_empresa,
                    'estado'          => '1',
                    'sucursal'            => $sucursal2,
                    'punto_emision'       => $punto_emision2,
                    'aparece_sri'     => $request['archivosri'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                if ($empresa->id != '0992704152001') {
                    $id_proveedor   = $request['proveedor'];
                    $proveedor_find = Proveedor::find($id_proveedor);
                    $cabeceraa      = [
                        'observacion'     => $proveedor_find->razonsocial . ' # ' . $numeroconcadenado,
                        'fecha_asiento'   => $request['fecha'],
                        'fact_numero'     => $request['secuencia_factura'],
                        'valor'           => $total_final,
                        'id_empresa'      => $id_empresa,
                        'estado'          => '1',
                        'aparece_sri'     => $request['archivosri'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];
                }

                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                $nueva_fecha         = null;
                $modfecha            = null;
                $consulta_fecha      = Ct_Termino::where('id', $request['termino'])->first();
                if ($consulta_fecha != null) {
                    $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($fechahoy));
                    $modfecha    = date("Y-m-d", $nueva_fecha);
                } else {
                    $errores .= " la fecha del termino no funciona ";
                }
                $subtotalf = $request['base1'];
                if ($request['fecha'] != $request['f_autorizacion']) {
                }

                
                $input = [
                    'tipo'                => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha'               => $request['fecha'],
                    'archivo_sri'         => $request['archivosri'],
                    'proveedor'           => $request['proveedor'],
                    'direccion_proveedor' => $request['direccion_proveedor'],
                    'termino'             => $request['termino'],
                    'pedido'              => $request['pedido_nombre'],
                    'orden_compra'        => $request['o_compra'],
                    'f_caducidad'         => $request['f_caducidad'],
                    'tipo_gasto'          => $request['tipo_gasto'],
                    'sucursal'            => $sucursal2,
                    'punto_emision'       => $punto_emision2,
                    'valor_contable'      => $total_final,
                    'fecha_termino'       => $modfecha,
                    'secuencia_f'         => $numero_factura,
                    'estado'              => '1',
                    'autorizacion'        => $request['autorizacion'],
                    'f_autorizacion'      => $request['f_autorizacion'],
                    'serie'               => $request['serie'],
                    'id_empresa'          => $id_empresa,
                    'numero'              => $numeroconcadenado,
                    'secuencia_factura'   => $request['secuencia_factura'],
                    'credito_tributario'  => $request['credito_tributario'],
                    'tipo_comprobante'    => $request['tipo_comprobante'],
                    'observacion'         => $request['observacion'],
                    'subtotal_0'          => $request['subtotal_01'],
                    'subtotal_12'         => $request['subtotal_121'],
                    'subtotal'            => $subtotalf,
                    'descuento'           => $request['descuento1'],
                    'iva_total'           => $request['tarifa_iva1'],
                    'ice_total'           => $request['ice_final1'],
                    'total_final'         => $total_final,
                    'transporte'          => $request['transporte'],
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                $id_compra = Ct_compras::insertGetId($input);
                $arr_total = [];
                for ($i = 0; $i < count($request->input("nombre")); $i++) {

                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $arr = [
                            'nombre'     => $request->input("nombre")[$i],
                            'cantidad'   => $request->input("cantidad")[$i],
                            'bodega'     => $request->input("bodega")[$i],
                            'codigo'     => $request->input("codigo")[$i],
                            'precio'     => $request->input("precio")[$i],
                            'descpor'    => $request->input("descpor")[$i],
                            'copago'     => $request->input("copago")[$i],
                            'descuento'  => $request->input("desc")[$i],
                            'precioneto' => $request->input("precioneto")[$i],
                            'detalle'    => $request->input("descrip_prod")[$i],
                            'iva'        => $request->input("iva")[$i],

                        ];
                        array_push($arr_total, $arr);
                    }
                }
                
                $cuentas_iva = [];
                foreach ($arr_total as $valor) {
                    $consulta_product = Ct_productos::where('codigo', $valor['codigo'])->first();

                    if ($consulta_product != '') {
                        $cuentas_iva = $consulta_product->impuesto_iva_compras;
                    }
                    $detalle = [
                        'id_ct_compras'        => $id_compra,
                        'codigo'               => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
                        'bodega'               => $valor['bodega'],
                        'descuento_porcentaje' => $valor['descpor'],
                        'estado'               => '1',
                        'descuento'            => $valor['descuento'],
                        'extendido'            => $valor['precioneto'],
                        'detalle'              => $valor['detalle'],
                        'iva'                  => $valor['iva'],
                        'porcentaje'           => $request['ivareal'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];

                    Ct_detalle_compra::create($detalle);
                    /*
                for($j = 0; $j < $valor['cantidad']; $j++){
                $cod_prod = Ct_Movimiento_Producto::where('id_ct_producto',$consulta_product->id)->get();
                if(!is_null($cod_prod)){
                $mov_producto = [
                'estado_producto' => '1',
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                ];
                }
                Ct_Movimiento_Producto::where('id',$cod_prod->id)
                ->update($mov_producto);
                }
                 */
                }
                $asientos     = $this->asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $request->total1);
                $data['id']   = $id_compra;
                $data['tipo'] = '1';
                $msj          = Ct_Kardex::generar_kardex($data);
                $errores .= " el kardex respuesta:  " . $msj;

                DB::commit();
                $inventario = Inventario::build_process('C', $id_compra, $empresa->id, 1);
                return response()->json(['id' => $id_compra, 'errores' => 'no', 'msj' => $errores, 'id_asiento' => $id_asiento_cabecera, 'inventario' => $inventario]);
            } else {
                return response()->json(['errores' => 'Ya existe factura de compra']);
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $total1)
    {
        //dd($request);
        $id_empresa      = Session::get('id_empresa');
        $globales        = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
        $valor_descuento = $request['descuento1'];
        $cuentas_iva     = 0;
        $base1           = $request['base1'];
        $total1          = $request['total1'];
        if ($valor_descuento > 0) {
            $base1  = $base1 + $valor_descuento;
            $total1 = $total1 + $valor_descuento;
        }  

        $id_plan_confg = LogConfig::busqueda('1.01.03.01.02');
        $cuenta_prod_term = Plan_Cuentas::find($id_plan_confg);

        
        if ($request['tarifa_iva1'] > 0) {
            $global = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_prod_term->id,
                'descripcion'         => $cuenta_prod_term->nombre,
                'fecha'               => $fechahoy,
                'debe'                => $base1,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            $plan_cuentas = Plan_Cuentas::find($globales->debe);
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fechahoy,
                'debe'                => $request['tarifa_iva1'],
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        } else {

            $global = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_prod_term->id,
                'descripcion'         => $cuenta_prod_term->nombre,
                'fecha'               => $fechahoy,
                'debe'                => $total1,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        }

        if ($valor_descuento > 0) {
            $cuenta_proveedor = $request['proveedor'];
            if ($cuenta_proveedor != null) {
                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                if (($consulta_en_proveedor) != null && $consulta_en_proveedor != '[]') {
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $desc_cuenta->id,
                        'descripcion'         => $desc_cuenta->nombre,
                        'fecha'               => $fechahoy,
                        'haber'               => $request['total1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                }
            }

            $global_des = Ct_Globales::where('id_modulo', 21)->where('id_empresa', $id_empresa)->first();
                
            $id_plan_confg = LogConfig::busqueda('FACTCONTABLE_DESC_COMP');
            $cuenta_desc_ven = Plan_Cuentas::find($id_plan_confg);

            //$desc_cuenta = Plan_Cuentas::where('id', '4.1.06.01')->first();
            //$cuenta_desc_ven = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_DESC_VENTA');
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_desc_ven->id,
                'descripcion'         => $cuenta_desc_ven->nombre,
                'fecha'               => $fechahoy,
                'debe'                => '0',
                'haber'               => $valor_descuento,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        } else {
            $cuenta_proveedor = $request['proveedor'];
            if ($cuenta_proveedor != null) {
                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                if (($consulta_en_proveedor) != null) {
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                    if (!is_null($desc_cuenta)) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $desc_cuenta->id,
                            'descripcion'         => $desc_cuenta->nombre,
                            'fecha'               => $fechahoy,
                            'haber'               => $request['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    } else {

                        $globalh = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                        //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                        $cuenta_locales = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_PROV_LOC');
                        //$id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
                        //$cuenta_locales = Plan_Cuentas::find($id_plan_confg);

                        Ct_Asientos_Detalle::create([

                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $cuenta_locales->cuenta_guardar,
                            'descripcion'         => $cuenta_locales->nombre_mostrar,
                            'fecha'               => $fechahoy,
                            'haber'               => $request['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                } else {
                    //$errores .= " cuenta por defecto de proveedores ";
                    //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                    $globalh = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();

                    $cuenta_locales = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPRAS_PROV_LOC');
                    //$id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
                    //$cuenta_locales = Plan_Cuentas::find($id_plan_confg);
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_locales->cuenta_guardar,
                        'descripcion'         => $cuenta_locales->nombre_mostrar,
                        'fecha'               => $fechahoy,
                        'haber'               => $request['descuento1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            } else {
                //$errores .= " no tiene cuenta el proveedor 2";
            }
        }
    }
    public function editar($id, Request $request)
    {
        $compras        = Ct_compras::where('id', $id)->first();
        $punto_emision  = $compras->punto_emision;
        $sucursal       = $compras->sucursal;
        $detalle_compra = Ct_detalle_compra::where('id_ct_compras', $compras->id)->get();
        $proveedor      = proveedor::where('estado', '1')->get();
        $id_empresa     = $request->session()->get('id_empresa');
        $termino        = Ct_Termino::where('estado', '1')->get();
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $bodega         = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $c_tributario   = ct_master_tipos::where('estado', '1')->where('tipo', '2')->get();
        $t_comprobante  = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $productos      = Ct_productos::where('id_empresa', $id_empresa)->get();
        return view('contable/compra/edit', ['compras' => $compras, 'c_tributario' => $c_tributario, 'productos' => $productos, 'bodega' => $bodega, 'termino' => $termino, 't_comprobante' => $t_comprobante, 'proveedor' => $proveedor, 'detalle_compra' => $detalle_compra, 'empresa' => $empresa]);
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipo_comprobante = ct_master_tipos::where('estado', '1')->where('tipo', '1')->get();
        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $constraints      = [
            'ct_c.id'                  => $request['id'],
            'proveedor'                => $request['proveedor'],
            'observacion'              => $request['detalle'],
            'ct_c.id_asiento_cabecera' => $request['id_asiento_cabecera'],
            'fecha'                    => $request['fecha'],
            'ct_c.tipo_comprobante'    => $request['tipo'],
            'secuencia_factura'        => $request['secuencia_f'],
        ];
        //dd($request->all());
        $compras   = $this->doSearchingQuery($constraints, $request);
        $proveedor = Proveedor::all();
        return view('contable/compra/index', ['compras' => $compras, 'searchingVals' => $constraints, 'tipo_comprobante' => $tipo_comprobante, 'empresa' => $empresa, 'proveedor' => $proveedor]);
    }
    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query      = DB::table('ct_compras as ct_c')
            ->join('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->orderby('ct_c.id', 'desc')
            ->select('ct_c.id as id', 'ct_c.fecha', 'ct_c.rutapdf', 'p.razonsocial', 'u.nombre1', 'u.apellido1', 'ct_c.autorizacion', 'ct_c.secuencia_factura', 'ct_c.numero', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.tipo', 'ct_c.archivo_sri', 'ct_c.proveedor', 'ct_c.orden_compra', 'ct_c.f_caducidad', 'ct_c.tipo_gasto', 'ct_c.f_autorizacion', 'ct_c.serie', 'ct_c.secuencia_factura', 'ct_c.credito_tributario', 'ct_c.observacion', 'ct_c.id_asiento_cabecera');
        //dd($query->get());
        $fields = array_keys($constraints);
        $index  = 0;
        //dd($constraints);
        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                //$query = $query->where($fields[$index], $constraint);
                if ($fields[$index] == "ct_c.id_asiento_cabecera") {
                    //dd("da");
                    $query = $query->where($fields[$index], $constraint);
                } elseif ($fields[$index] == "ct_c.id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function buscar_pedido(Request $request)
    {
        //return $request['id_pedido'];1123123123
        $pedido    = $request['id_pedido'];
        $idusuario = Auth::user()->id;

        $data      = null;
        $productos = DB::select(DB::raw('SELECT COUNT(DISTINCT (m.id)) AS cantidad,
        SUM(m.cantidad) AS cantidad_total,
        m.id_producto,
        m.serie,
        cp.codigo,
        cp.nombre,
        p.id_proveedor,
        prov.razonsocial,
        p.fecha,
        p.vencimiento,
        prov.id_cuentas,
        prods.codigo_producto,
        m.id_bodega,
        m.precio,
        cp.iva,
        prov.direccion,
        p.id_empresa,
        p.pedido
      FROM movimiento AS m
        INNER JOIN pedido AS p
          ON p.id = m.id_pedido
        INNER JOIN proveedor AS prov
          ON prov.id = p.id_proveedor
        LEFT JOIN ct_productos_insumos AS prods
          ON prods.id_insumo = m.id_producto
        LEFT OUTER JOIN ct_productos cp
          ON cp.id = prods.id_producto
        LEFT OUTER JOIN producto px
          ON px.id = prods.id_insumo
      WHERE p.pedido = "' . $pedido . '"
     AND m.estado =1
      GROUP BY m.id_producto'));
        if (count($productos) > 0) {

            $data = [$productos[0]->id_proveedor, $productos[0]->fecha, $productos[0]->vencimiento, $productos, $productos[0]->direccion, $productos[0]->id_cuentas, $productos[0]->id_empresa];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function codigo2(Request $request)
    {
        $codigo     = $request['codigo'];
        $data       = null;
        $id_empresa = $request['id_empresa'];
        $productos  = DB::table('ct_productos')->where('codigo', $codigo)->where('id_empresa', $id_empresa)->first();

        $iva_producto = DB::table('producto')->where('codigo', $codigo)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->nombre, 'iva' => $productos->iva];
        } else {
            return ['value' => 'no'];
        }
    }
    public function codigo(Request $request)
    {

        $codigo     = $request['term'];
        $data       = array();
        $id_empresa = $request['id_empresa'];
        $productos  = DB::table('ct_productos')->where('codigo', 'like', '%' . $codigo . '%')->where('id_empresa', $id_empresa)->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->codigo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function buscar_factura(Request $request)
    {
        $factura   = $request['id_factura'];
        $data      = null;
        $pedido    = $request['id_pedido'];
        $data      = null;
        $productos = DB::select(DB::raw('SELECT COUNT(DISTINCT (m.id)) AS cantidad_total,
        m.id_producto,
        m.serie,
        cp.codigo,
        cp.nombre,
        p.id_proveedor,
        prov.razonsocial,
        p.fecha,
        p.vencimiento,
        prov.id_cuentas,
        prods.codigo_producto,
        m.id_bodega,
        m.precio,
        cp.iva,
        prov.direccion,
        p.id_empresa,
        p.pedido
      FROM movimiento AS m
        INNER JOIN pedido AS p
          ON p.id = m.id_pedido
        INNER JOIN proveedor AS prov
          ON prov.id = p.id_proveedor
        LEFT JOIN ct_productos_insumos AS prods
          ON prods.id_insumo = m.id_producto
        LEFT OUTER JOIN ct_productos cp
          ON cp.id = prods.id_producto
        LEFT OUTER JOIN producto px
          ON px.id = prods.id_insumo
      WHERE p.factura = ' . $factura . '
      GROUP BY m.id_producto'));
        //dd($productos);
        if ($productos != '[]') {

            $data = [$productos[0]->id_proveedor, $productos[0]->fecha, $productos[0]->vencimiento, $productos, $productos[0]->direccion, $productos[0]->id_cuentas, $productos[0]->id_empresa];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }

    public function anular_factura_compras($id, Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $act_estado = [
            'estado'          => '0',
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $fechahoy = Date('Y-m-d H:i:s');
        Ct_compras::where('id', $id)->update($act_estado);
        //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
        $compras      = Ct_compras::where('id', $id)->first();
        $contador_ctv = DB::table('ct_compras')->get()->count();
        $id_empresa   = $request->session()->get('id_empresa');

        $cabecera  = Ct_Asientos_Cabecera::where('id', $compras->id_asiento_cabecera)->first();
        $actualiza = [
            'estado'          => 1,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $cabecera->update($actualiza);
        $detalles   = $cabecera->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => strtoupper($request['observacion']),
            'fecha_asiento'   => $cabecera->fecha_asiento,
            'id_empresa'      => $id_empresa,
            'fact_numero'     => $cabecera->secuencia,
            'valor'           => $cabecera->valor,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        foreach ($detalles as $value) {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento,
                'id_plan_cuenta'      => $value->id_plan_cuenta,
                'debe'                => $value->haber,
                'haber'               => $value->debe,
                'descripcion'         => $value->descripcion,
                'fecha'               => $cabecera->fecha_asiento,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }

        Log_Contable::create([
            'tipo'           => 'C',
            'valor_ant'      => $cabecera->valor,
            'valor'          => $cabecera->valor,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
            'observacion'    => $cabecera->concepto,
            'id_ant'         => $cabecera->id,
            'id_referencia'  => $id_asiento,
        ]);
        return redirect()->intended('/contable/compras');
    }

    public function identificacion(Request $request)
    {

        $codigo      = $request['term'];
        $data        = array();
        $proveedores = DB::table('proveedor')->where('id', 'like', '%' . $codigo . '%')->get();
        foreach ($proveedores as $prov) {
            $data[] = array('value' => $prov->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function buscar_proveedor(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('proveedor')->where('id', $codigo)->first();
        $idusuario = Auth::user()->id;

        if (!is_null($productos)) {
            return ['value' => $productos->razonsocial, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion];
        } else {
            return ['value' => 'no'];
        }
    }

    public function nombre_proveedor(Request $request)
    {
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('proveedor')->where('razonsocial', 'like', '%' . $nombre . '%')->get();
        $idusuario = Auth::user()->id;
        if ($idusuario == "0957258056") {
            //dd($productos);
        }
        foreach ($productos as $product) {
            $data[] = array('value' => $product->razonsocial, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function nombre(Request $request)
    {

        $nombre     = $request['term'];
        $id_empresa = $request['id_empresa'];
        $data       = array();
        $productos  = DB::table('ct_productos')->where('nombre', 'like', '%' . $nombre . '%')->where('id_empresa', $id_empresa)->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function nombre2(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $productos    = DB::table('ct_productos')->where('nombre', 'like', "%$nombre%")->first();
        $iva_producto = DB::table('producto')->where('nombre', $nombre)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->codigo, 'iva' => $productos->iva];
        } else {
            return ['value' => 'no'];
        }
    }

    public function buscar_nombreproveedor(Request $request)
    {
        $nombre = $request['nombre'];

        $data      = null;
        $productos = DB::table('proveedor')->where('id', $nombre)->first();
        $idusuario = Auth::user()->id;

        if (!is_null($productos)) {

            $detalle = Ct_Detalle_Acreedores::where('id_proveedor', $productos->id)->first();
            if (is_null($detalle)) {
                //esto es tu cagada tursi era este if y comentaste lalinea que no
                return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion, 'caducidad' => ''];
            }
            return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion, 'caducidad' => $detalle->f_caducidad];
        } else {
            return ['value' => 'no'];
        }
    }
    public function subir_masivo(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $data_array = array(
            "tipo"                    => "MEM-VEN", //tipo de de orden de venta
            "fecha"                   => "2021-08-01", //fecha de orden
            "id_empresa"              => "0992704152001", //empresa
            "divisas"                 => "1", //cualquiera
            "nombre_cliente"          => "PAPI CHILAN", //nombre del cliente
            "tipo_consulta"           => "1", //esto clavado
            "identificacion_cliente"  => "1316262193", // ci de cliente
            "direccion_cliente"       => "MUCHO LOTE", //direccion del cliente
            "telefono_cliente"        => "132231", // telefono de cliente
            "mail_cliente"            => "tumarido@mail.com", //mail del cliente
            "orden_venta"             => "321", //idde referencia
            "identificacion_paciente" => "1316262193", // ci de paciente
            "nombre_paciente"         => "PAPI CHILAN", // nonbre de paciente
            "id_seguro"               => "1", //de la tabla seguros,
            "subtotal_01"             => "12.00",
            "subtotal_121"            => "12.00",
            "descuento1"              => "12.00",
            "tarifa_iva1"             => "12.00",
            "base"                    => "12.00",
            "totalc"                  => "12.00",
            "valor_contable"          => "12.00",
            "details"                 => array(
                array(
                    "codigo"     => "001",
                    "nombre"     => "HEY YOU",
                    "cantidad"   => "1",
                    "precio"     => "2.00",
                    "extendido"  => "3.00",
                    "iva"        => "1",
                    "descpor"    => "10",
                    "descuento"  => "10",
                    "copago"     => "10",
                    "detalle"    => "EXAMEN PAPI",
                    "precioneto" => "10",

                ),
            ),
            "id_usuario"              => "1316262193",
        );
        /*  $procedimientos= Procedimiento::where('estado',1)->get();
        $codigo=12;
        foreach($procedimientos as $p){
        $jp= Planilla_Procedimiento::where('id_procedimiento',$p->id)->first();

        if(is_null($jp)){
        $secuencia = str_pad($codigo, 3, "0", STR_PAD_LEFT);
        $insumo_id=Insumo_Plantilla_Control::insertGetId([
        'codigo'=>$secuencia,
        'nombre'=>$p->nombre,
        'estado'=>1,
        ]);
        Planilla_Procedimiento::create([
        'id_planilla'=>$insumo_id,
        'id_procedimiento'=>$p->id
        ]);
        }
        $codigo++;
        } */
        /*   $plan_cuentas= Plan_Cuentas::all();
        $empresa= Empresa::all();
        foreach($plan_cuentas as $p){
        //dd($p);
        foreach($empresa as $emp){
        Plan_Cuentas_Empresa::create([
        'id_plan'=>$p->id,
        'id_padre'        => $p->id_padre,
        'nombre'          => $p->nombre,
        'plan'            => $p->id,
        'estado'          => $p->estado,
        'id_empresa'      => $emp->id,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        }

        } */
        return 'ok, gracias amigo';
    }
    public function verificar_anulacion(Request $request)
    {
        $verificacion = $request['verificar'];
        $tabla        = "";
        $conf         = 0;
        $tiene        = "no";
        switch ($verificacion) {
            case '1':
                /***************************COMPROBANTE DE EGRESOS************************/

                $id = $request['id_compra'];

                $egreso      = Ct_Detalle_Comprobante_Egreso::where('id_comprobante', $id)->get();
                $tabla_cruce = "";
                $id_cruce    = 0;
                if (count($egreso)) {
                    foreach ($egreso as $value) {
                        $cruce_detalle = DB::table('ct_detalle_cruce')->where('id_factura', $value->id_compra)->get();
                        if (count($cruce_detalle)) {
                            foreach ($cruce_detalle as $values) {
                                $cruce = Ct_Cruce_Valores::where('id', $values->id_comprobante)->where('estado', '1')->get();
                                if (count($cruce)) {
                                    $tiene       = "si";
                                    $tabla_cruce = "Cruce de valores";
                                    $id_cruce    = $cruce[0]->id;
                                }
                            }
                        }
                    }
                }
                $tablas = [$tabla_cruce];
                $ids    = [$id_cruce];
                return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];
                //return ['existe'=>$conf, 'tabla'=>'Cruce Valores', 'respuesta'=> $tiene, 'id_egreso'];
                break;
            case '2':
                /**************************DEBITO BANCARIO**********************************/
                $id               = $request['id_compra'];
                $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_debito', $id)->where('estado', "!=", "0")->get();
                $tiene            = "no";
                $tabla_cruce      = "";
                $id_cruce         = 0;
                if (count($debito_banca_det)) {
                    foreach ($debito_banca_det as $value) {
                        $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $value->id_compra)->get();
                        if (count($detalle_cruce)) {
                            foreach ($detalle_cruce as $values) {
                                $cruce = DB::table('ct_cruce_valores')->where('id', $values->id_comprobante)->where('estado', '1')->get();
                                if (count($cruce)) {
                                    $tiene       = "si";
                                    $tabla_cruce = "Cruce de valores";
                                    $id_cruce    = $cruce[0]->id;
                                }
                            }
                        }
                    }
                }
                $tablas = [$tabla_cruce];
                $ids    = [$id_cruce];
                return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];

                //return ['existe'=>"si", 'tabla'=>'Cruce Valores', 'respuesta'=> $tiene];
                break;
            case '3':
                /**********************RETENCIONES*********************************/
                $tiene       = "no";
                $tabla       = "";
                $id          = $request['id_compra'];
                $tabla_egre  = "";
                $tabla_banca = "";
                $tabla_cruce = "";
                $tabla_rete  = "";
                $id_egreso   = 0;
                $retenciones = Ct_Retenciones::where('id', $id)->first();
                $com_egre    = Ct_Detalle_Comprobante_Egreso::where('id_compra', $retenciones->id_compra)->get();

                if (count($com_egre) > 0) {
                    foreach ($com_egre as $value) {
                        $egreso = Ct_Comprobante_Egreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                        if (count($egreso) > 0) {
                            $tiene      = "si";
                            $tabla_egre = "Comprobante de Egreso ";
                            $id_egreso  = $egreso[0]->id;
                        }
                    }
                }

                $id_deb_ban       = 0;
                $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_compra', $retenciones->id_compra)->get();

                if (count($debito_banca_det) > 0) {

                    foreach ($debito_banca_det as $value) {

                        $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                        if (count($debit_banca) > 0) {

                            $tiene       = "si";
                            $tabla_banca = "Debito Bancario ";
                            $id_deb_ban  = $debit_banca[0]->id;
                        }
                    }
                }

                $id_cruce_valores = 0;

                $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $retenciones->id_compra)->get();

                if (count($detalle_cruce) > 0) {
                    foreach ($detalle_cruce as $value) {
                        $cruce_valores = Ct_Cruce_Valores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                        if (count($cruce_valores) > 0) {
                            $tiene            = "si";
                            $tabla_cruce      = "Cruce Valores ";
                            $id_cruce_valores = $cruce_valores[0]->id;
                        }
                    }
                }

                $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce];
                $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores];

                return ['id' => $retenciones->id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];

                break;
            case '4':
                $tabla_egre        = "";
                $tabla_banca       = "";
                $tabla_cruce       = "";
                $tabla_rete        = "";
                $tabla_egre_masivo = "";
                $tabla_cruce_cuentas = "";
                $id_egreso         = 0;
                $id_deb_ban        = 0;
                $id_cruce_valores  = 0;
                $id_retencion      = 0;
                $id_egreso_masivo  = 0;
                $id_cruce_cuentas  = 0;
                /*********************COMPRAS*****************************************/
                $id    = $request['id_compra'];
                $tiene = "";
                $tabla = "";

                $retenciones = Ct_Retenciones::where('id_compra', $id)->get();

                if (count($retenciones) > 0) {
                    foreach ($retenciones as $value) {
                        if ($value->estado == '1') {
                            $tiene        = "si";
                            $id_retencion = $retenciones[0]->id;
                        }
                    }
                    if ($tiene == "si") {
                        $tabla_rete = "Retenciones ";
                    }
                }
                $com_egre = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id)->get();
                // dd("Hola");

                if (count($com_egre) > 0) {
                    foreach ($com_egre as $value) {
                        $egreso = Ct_Comprobante_Egreso::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                        if (count($egreso) > 0) {
                            $tiene      = "si";
                            $tabla_egre = "Comprobante de Egreso ";
                            $id_egreso  = $egreso[0]->id;
                        }
                    }
                }

                $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_compra', $id)->get();
                if (count($debito_banca_det) > 0) {
                    foreach ($debito_banca_det as $value) {
                        $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado', '=', '1')->get();
                        if (count($debit_banca) > 0) {
                            $tiene       = "si";
                            $tabla_banca = "Debito Bancario ";
                            $id_deb_ban  = $debit_banca[0]->id;
                        }
                    }
                }

                $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura', $id)->get();
                if (count($detalle_cruce) > 0) {
                    foreach ($detalle_cruce as $value) {
                        $cruce_valores = Ct_Cruce_Valores::where('id', $value->id_comprobante)->where('estado', '1')->get();
                        if (count($cruce_valores) > 0) {
                            $tiene            = "si";
                            $tabla_cruce      = "Cruce Valores ";
                            $id_cruce_valores = $cruce_valores[0]->id;
                        }
                    }
                }

                $det_egreso_masivo = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_compra', $id)->get();

                if (count($det_egreso_masivo) > 0) {

                    foreach ($det_egreso_masivo as $value) {

                        $egreso_masivo = Ct_Comprobante_Egreso_Masivo::where('id', $value->id_comprobante)->where('estado', '=', '1')->get();
                        if (count($egreso_masivo) > 0) {

                            $tiene             = "si";
                            $tabla_egre_masivo = "Comprobante de Egreso Masivo";
                            $id_egreso_masivo  = $egreso_masivo[0]->id;

                        }
                    }
                }

                $cruce_cuentas = Ct_Cruce_Cuentas::where('id_factura', $id)->where('estado', 1)->first();

                if(!is_null($cruce_cuentas)){
                    $tabla_cruce_cuentas = "Cruce de Cuentas";
                    $id_cruce_cuentas = $cruce_cuentas->id;
                }



                $tablas = [$tabla_egre, $tabla_banca, $tabla_cruce, $tabla_rete, $tabla_egre_masivo, $tabla_cruce_cuentas];
                $ids    = [$id_egreso, $id_deb_ban, $id_cruce_valores, $id_retencion, $id_egreso_masivo, $id_cruce_cuentas];
                //  dd($tablas);
                return ['id' => $id, 'tablas' => $tablas, 'respuesta' => $tiene, "ids" => $ids];

                break;
        }
    }
    public function nota_credito(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $proveedores = Proveedor::where('estado', 1)->get();
        //dd($request->all());
        $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        $id_proveedor       = $request['id_proveedor'];
        if (is_null($request['desde']) && is_null($request['hasta'])) {
            $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
            if (!is_null($id_proveedor)) {
                $credito_acreedores = $credito_acreedores->where('id_proveedor', $id_proveedor);
            }
        } else {

            if (!is_null($request['desde']) && !is_null($request['hasta'])) {
                $credito_acreedores = $credito_acreedores->whereBetween('fecha', [str_replace('/', '-', $request['desde']) . ' 00:00:00', str_replace('/', '-', $request['hasta']) . ' 23:59:59']);
            }
            if (!is_null($request['hasta']) && is_null($request['desde'])) {
                $credito_acreedores = $credito_acreedores->where('fecha', '<', $request['hasta']);
            }
            if (!is_null($id_proveedor)) {
                $credito_acreedores = $credito_acreedores->where('id_proveedor', $id_proveedor);
            }
        }

        $credito_acreedores = $credito_acreedores->get();

        $empresa = Empresa::where('id', $id_empresa)->first();
        if ($request['excel'] == 1) {
            $excel = $this->getExcel($request);
        }
        //dd($credito_acreedores);
        return view("contable/acreedores_credito/nueva_vista", ['proveedores' => $proveedores, 'id_proveedor' => $id_proveedor, 'credito_acreedores' => $credito_acreedores, 'fecha_desde' => $request['desde'], 'fecha_hasta' => $request['hasta'], 'empresa' => $empresa]);
    }
    public function carga_logo(Request $request)
    {
        $id_empresa            = $request->session()->get('id_empresa');
        $id_proovedor          = $request['id_proveedor'];
        $desde                 = $request['desde'];
        $hasta                 = $request['hasta'];
        $logo                  = Proveedor::where('id', $id_proovedor)->first();
        $empresa               = Empresa::where('id', $id_empresa)->first();
        $ct_credito_acreedores = Ct_Credito_Acreedores::where('estado', 1)->get();
        //dd($ct_credito_acreedores);
        //dd($ct_credito_acreedores);
        return view("contable/acreedores_credito/logo", ['ct_credito_acreedores' => $ct_credito_acreedores, 'empresa' => $empresa, 'logo' => $logo, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function getExcel(Request $request)
    {
        $id_empresa         = $request->session()->get('id_empresa');
        $fecha_desde        = $request['desde'];
        $proveedor          = $request['id_proveedor'];
        $fecha_hasta        = $request['hasta'];
        $empresa            = Empresa::where('id', $id_empresa)->first();
        $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        $id_proveedor       = $request['id_proveedor'];
        if (is_null($request['desde']) && is_null($request['hasta'])) {
            $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        } else {
            if (!is_null($request['desde']) && !is_null($request['hasta'])) {
                $credito_acreedores = $credito_acreedores->whereBetween('fecha', [str_replace('/', '-', $request['desde']) . ' 00:00:00', str_replace('/', '-', $request['hasta']) . ' 23:59:59']);
                //dd("dsada");
            }
            if (!is_null($request['hasta']) && is_null($request['desde'])) {
                $credito_acreedores = $credito_acreedores->where('fecha', '<', $request['hasta']);
            }
            if (!is_null($id_proveedor)) {
                $credito_acreedores = $credito_acreedores->where('id_proveedor', $id_proveedor);
            }
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $credito_acreedores = $credito_acreedores->get();
        Excel::create('INFORME NOTA DE CREDITO ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $credito_acreedores, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $credito_acreedores) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:K2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:K3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue("INFORME NOTA DE CREDITO");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:K4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . $fecha_desde . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue("Al " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    $cell->setValue('NUMERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C5:D5');
                $sheet->cell('C5', function ($cell) {
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    $cell->setValue('PROVEEDOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I5', function ($cell) {
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J5', function ($cell) {
                    $cell->setValue('IMPUESTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K5', function ($cell) {
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));
                $i = $this->setDetalles($credito_acreedores, $sheet, 6, $fecha_hasta);
                $sheet->cells('A3:H3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:K5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->setWidth(array(
                    'A' => 17,
                    'B' => 17,
                    'C' => 17,
                    'D' => 17,
                    'E' => 17,
                    'F' => 18,
                    'G' => 17,
                    'H' => 17,
                    'I' => 17,
                    'J' => 17,
                    'K' => 17,
                ));

            });
        })->export('xlsx');
    }
    public function setDetalles($consulta, $sheet, $i, $fecha_hasta)
    {
        $acumulador = 0;
        $subtotal0  = 0;
        $subtotal12 = 0;
        $impuesto   = 0;
        $subtotal   = 0;

        foreach ($consulta as $value) {
            $acumulador += $value->valor_contable;
            $subtotal0 += $value->subtotal_0;
            $subtotal12 += $value->subtotal_12;
            $impuesto += $value->impuesto;
            $subtotal += $value->subtotal;

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue(date('d-m-Y', strtotime($value->fecha)));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->secuencia);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('C' . $i . ':D' . $i);
            $sheet->cell('C' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->concepto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('E' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->compra->proveedorf->razonsocial);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('F' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->compra->numero);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            /*$sheet->cell('G' . $i, function ($cell) use ($value) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($value->impuesto);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });*/
            $sheet->cell('G' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                if (is_null($value->subtotal_0)) {
                    $cell->setValue("0,00");
                    $cell->setAlignment('right');
                } else {
                    $cell->setValue($value->subtotal_0);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                if (is_null($value->subtotal_12)) {
                    $cell->setValue("0,00");
                    $cell->setAlignment('right');
                } else {
                    $cell->setValue($value->subtotal_12);
                }
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('I' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->subtotal);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('J' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->impuesto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->cell('K' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->valor_contable);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $i++;
        }
        $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue('TOTAL:');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('G' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($subtotal0);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('H' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($subtotal12);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('I' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($subtotal);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($impuesto);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('K' . $i, function ($cell) use ($acumulador) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($acumulador);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('I5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('J5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });

        return $i;
    }

    public function masivo_subtotal()
    {

        $acreedores = Ct_Credito_Acreedores::all();

        foreach ($acreedores as $value) {
            $credito = Ct_Credito_Acreedores::find($value->id);

            if ($credito->impuesto > 0) {
                $credito->subtotal_12 = $credito->subtotal;
                $credito->subtotal_0  = 0;
            } else {
                $credito->subtotal_0  = $credito->subtotal;
                $credito->subtotal_12 = 0;
            }

            $credito->save();
        }

        return ("ok gracias amigo");
    }

    public function subirpdf($id, $parametro)
    {
        return view("contable/compra/modalpdf", ['id' => $id, 'parametro' => $parametro]);
    }

    public function guardarpdf(Request $request)
    {
        //dd($request->all());
        $id              = $request['id'];
        $nombre_original = $request['file']->getClientOriginalName();
        $extension       = $request['file']->getClientOriginalExtension();
        $r1              = Storage::disk('public')->put($nombre_original . date('YmdHis'), \File::get($request['file']));
        $rutadelaimagen  = $nombre_original . date('YmdHis');
        $editar          = DB::table('ct_compras')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]);
        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        if ($request->parametro == 1) {
            return redirect()->route('compras_index');
        } else {
            return redirect()->route('fact_contable_index');
        }
    }
    /* public function pdf_visualizar($id)
    {
    $visualizar = Ct_compras::find($id);
    $ver        = $visualizar->rutapdf;
    $path       = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
    //dd($path);
    return Response::make(file_get_contents($path), 200, [
    'Content-Type'        => 'application/pdf',
    'Content-Disposition' => 'inline; filename="' . $ver . '"',
    ]);
    }*/
    public function pdf_visualizar_nuevo(Request $request)
    {
        $visualizar = Ct_compras::where('id', $request['id'])->first();
        $path       = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
        // $nombreArchivo = TicketPermiso::where('id', $request['id'])->first();
        // $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->ruta_archivo;
        //dd($path);
        return response()->file($path);
    }
    public function anularpdf($id, $validate)
    {
        $visualizar = Ct_compras::find($id);
        $path       = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
        Storage::disk('public')->delete($visualizar->rutapdf);
        $visualizar->rutapdf = null;
        $visualizar->save();
        if ($validate == 1) {
            return redirect()->route('compras_index');
        } else {
            return redirect()->route('fact_contable_index');
        }
    }
}
