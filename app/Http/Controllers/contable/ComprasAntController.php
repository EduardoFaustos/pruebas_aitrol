<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\CierreCaja;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Inventario;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_rubros;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Termino;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_Deposito_Bancario;
use Sis_medico\Ct_Detalle_Acreedores;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Inventario;
use Sis_medico\Ct_Nota_Credito;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\Ct_Nota_Inventario;
use Sis_medico\Ct_Transferencia_Bancaria;
use Sis_medico\Ct_ventas;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Nota_Debito;
use Sis_medico\Pagosenlinea;
use Sis_medico\ParametersConglomerada;
use Sis_medico\PrecioProducto;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_Globales;
use Svg\Tag\Rect;
use Session;
use Sis_medico\Contable;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\Medicina;
use Sis_medico\Producto_Medicina;

use function GuzzleHttp\json_decode;

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
            ->select('ct_c.id', 'ct_c.numero', 'ct_c.fecha', 'p.razonsocial', 'ct_c.autorizacion', 'u.nombre1', 'u.apellido1', 'ct_c.secuencia_factura', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.observacion', 'ct_c.id_asiento_cabecera')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->orderby('ct_c.id', 'desc')
            ->paginate(10);

        $var = 2;

        return view('contable/compra/index', ['compras' => $compras, 'empresa' => $empresa, 'tipo_comprobante' => $tipo_comprobante, 'proveedor' => $proveedor]);
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
        $iva_param      = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        $caja_chica     = Ct_Configuraciones::where('id_plan', '1.01.01.1')->first();
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

    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $id_empresa      = $request->session()->get('id_empresa');
        $idusuario       = Auth::user()->id;
        $iva_acreditable = $request['iva_final'];
        $objeto_validar  = new Validate_Decimals();
        DB::beginTransaction();

        try {
            $sucursal        = $request['sucursal'];
            $errores         = "";
            $iva_param       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
            $punto_emision   = $request['serie'];
            $sucursal        = substr($punto_emision, 0, -4);
            $punto_emision   = substr($punto_emision, 4);
            $empresa = Empresa::find($id_empresa);
            $total_final     = $objeto_validar->set_round($request['total1']);
            $contador_ctv    = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)->get()->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {

                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                $max_id = intval($max_id->secuencia_f);
                //dd(strlen($max_id));
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $numeroconcadenado   = $request['serie'] . '-' . $request['secuencia_factura'];
            $comprobacion_compra = Ct_compras::where('numero', $numeroconcadenado)->where('tipo', '1')->where('proveedor', $request['proveedor'])->where('id_empresa', $id_empresa)->where('estado', '<>', '0')->first();
            if (is_null($comprobacion_compra) || $comprobacion_compra == '[]') {
                $fechahoy  = $request['fecha'];
                $comp= Ct_compras::where('pedido',$request->pedido_nombre)->where('estado','<>','0')->first();
                if(!is_null($comp)){
                    return response()->json(['errores'=>'Ya existe factura con este pedido']);
                }
                $cabeceraa = [
                    'observacion'     => $request['observacion'],
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
                if ($empresa->id != '0992704152001') {
                    $id_proveedor = $request['proveedor'];
                    $proveedor_find = Proveedor::find($id_proveedor);
                    $cabeceraa = [
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
                $input     = [
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
                    'sucursal'            => $sucursal,
                    'punto_emision'       => $punto_emision,
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

                    if (count($consulta_product) > 0) {
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
                $asientos     = $this->asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf);
                $data['id']   = $id_compra;
                $data['tipo'] = '1';
                $msj          = Ct_Kardex::generar_kardex($data);
                $errores .= " el kardex respuesta:  " . $msj;
                DB::commit();
                return response()->json(['id'=>$id_compra,'errores'=>'no','id_asiento'=>$id_asiento_cabecera]);
                //return [$id_compra, $errores, $id_asiento_cabecera];
            } else {
                return response()->json(['errores'=>'Ya existe factura de compra']);
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf)
    {
        $id_empresa = Session::get('id_empresa');
        $globales = Ct_Globales::where('id_modulo',1)->where('id_empresa', $id_empresa)->first();
        $cuenta_prod_ter = Ct_Configuraciones::obtener_cuenta('COMPRASANT_PROD_TER');
        if ($request['tarifa_iva1'] > 0) {
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_prod_ter->cuenta_guardar,
                'descripcion'         => $cuenta_prod_ter->nombre_mostrar,
                'fecha'               => $fechahoy,
                'debe'                => $request['base1'],
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            //$plan_cuentas = Plan_Cuentas::find('1.01.05.01.01');
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
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_prod_ter->cuenta_guardar,
                'descripcion'         => $cuenta_prod_ter->nombre_mostrar,
                'fecha'               => $fechahoy,
                'debe'                => $request['total1'],
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        }
        $valor_descuento = $request['descuento1'];
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
                        'haber'               => $request['total1'] - $request['descuento1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                    //$errores .= " no tiene cuenta el proveedor ";
                }
            }
            $desc_cuenta = Ct_Configuraciones::obtener_cuenta('COMPRASANT_DES_COM');
            //$desc_cuenta = Plan_Cuentas::where('id', '4.1.08')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $desc_cuenta->cuenta_guardar,
                'descripcion'         => $desc_cuenta->nombre_mostrar,
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
                            'id_plan_cuenta'      => $desc_cuenta->cuenta_guardar,
                            'descripcion'         => $desc_cuenta->nombre_mostrar,
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
                        $desc_cuenta = Ct_Configuraciones::obtener_cuenta('COMPRASANT_PROVE_LOCALES');
                        //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                       
                        Ct_Asientos_Detalle::create([

                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $desc_cuenta->cuenta_guardar,
                            'descripcion'         => $desc_cuenta->nombre_mostrar,
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
                    $desc_cuenta = Ct_Configuraciones::obtener_cuenta('COMPRASANT_PROVE_LOCALES');
                    //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $desc_cuenta->cuenta_guardar,
                        'descripcion'         => $desc_cuenta->nombre_mostrar,
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
            'ct_c.id'               => $request['id'],
            'proveedor'             => $request['proveedor'],
            'observacion'           => $request['detalle'],
            'ct_c.id_asiento_cabecera' => $request['id_asiento_cabecera'],
            'fecha'                 => $request['fecha'],
            'ct_c.tipo_comprobante' => $request['tipo'],
            'secuencia_factura'     => $request['secuencia_f'],
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
            ->select('ct_c.id as id', 'ct_c.fecha', 'p.razonsocial', 'u.nombre1', 'u.apellido1', 'ct_c.autorizacion', 'ct_c.secuencia_factura', 'ct_c.numero', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.tipo', 'ct_c.archivo_sri', 'ct_c.proveedor', 'ct_c.orden_compra', 'ct_c.f_caducidad', 'ct_c.tipo_gasto', 'ct_c.f_autorizacion', 'ct_c.serie', 'ct_c.secuencia_factura', 'ct_c.credito_tributario', 'ct_c.observacion', 'ct_c.id_asiento_cabecera');
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
      WHERE p.pedido = "' . $pedido . '"
      GROUP BY m.id_producto'));
        //dd($productos);

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
        /*
        $retencion= Ct_Retenciones::where('id_compra',$compras->id)->first();
        if(!is_null($retencion) && $retencion!='[]'){
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $retencion->update($actualiza);
        $asiento_retencion= Ct_Asientos_Cabecera::where('id',$retencion->id_asiento_cabecera)->first();
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $asiento_retencion->update($actualiza);
        $detalles = $asiento_retencion->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
        'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
        'fecha_asiento'   => date('Y-m-d H:i:s'),
        'id_empresa'      => $id_empresa,
        'fact_numero'     => $asiento_retencion->secuencia,
        'valor'           => $asiento_retencion->valor,
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
        'fecha'               => date('Y-m-d H:i:s'),
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        }

        //anular todo
        $egresos= Ct_Detalle_Comprobante_Egreso::where('id_compra',$compras->id)->get();
        if(!is_null($egresos) && $egresos!='[]'){
        $egresosf= Ct_Comprobante_Egreso::where('id',$egresos[0]->id_comprobante)->first();
        if(!is_null($egresosf)){
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $egresosf->update($actualiza);
        $asiento_retencion= Ct_Asientos_Cabecera::where('id',$egresosf->id_asiento_cabecera)->first();
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $asiento_retencion->update($actualiza);
        $detalles = $asiento_retencion->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
        'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
        'fecha_asiento'   => date('Y-m-d H:i:s'),
        'id_empresa'      => $id_empresa,
        'fact_numero'     => $asiento_retencion->secuencia,
        'valor'           => $asiento_retencion->valor,
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
        'fecha'               => date('Y-m-d H:i:s'),
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        }

        }
        $bancos= Ct_Debito_Bancario_Detalle::where('id_compra',$compras->id)->get();
        if(!is_null($bancos) && $bancos!='[]'){
        $bancosf= Ct_Debito_Bancario::where('id',$bancos[0]->id_debito)->first();
        if(!is_null($bancosf)){
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $bancosf->update($actualiza);
        $asiento_retencion= Ct_Asientos_Cabecera::where('id',$bancosf->id_asiento_cabecera)->first();
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $asiento_retencion->update($actualiza);
        $detalles = $asiento_retencion->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
        'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
        'fecha_asiento'   => date('Y-m-d H:i:s'),
        'id_empresa'      => $id_empresa,
        'fact_numero'     => $asiento_retencion->secuencia,
        'valor'           => $asiento_retencion->valor,
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
        'fecha'               => date('Y-m-d H:i:s'),
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        }
        }
        $cruce_valores= Ct_Detalle_Cruce::where('id_factura',$compras->id)->get();
        if(!is_null($cruce_valores) && $cruce_valores!='[]'){
        $cruce_valoresf= Ct_Cruce_Valores::where('id',$cruce_valores[0]->id_comprobante)->first();
        if(!is_null($cruce_valoresf)){
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $cruce_valoresf->update($actualiza);
        $asiento_retencion= Ct_Asientos_Cabecera::where('id',$cruce_valoresf->id_asiento_cabecera)->first();
        $actualiza = [
        'estado' =>'0',
        'id_usuariocrea'                => $idusuario,
        'id_usuariomod'                 => $idusuario,
        'ip_creacion'                   => $ip_cliente,
        'ip_modificacion'               => $ip_cliente,
        ];
        $asiento_retencion->update($actualiza);
        $detalles = $asiento_retencion->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
        'observacion'     => 'ANULACIÓN ' . $asiento_retencion->observacion,
        'fecha_asiento'   => date('Y-m-d H:i:s'),
        'id_empresa'      => $id_empresa,
        'fact_numero'     => $asiento_retencion->secuencia,
        'valor'           => $asiento_retencion->valor,
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
        'fecha'               => date('Y-m-d H:i:s'),
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        }
        }*/
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
        $idusuario  = Auth::user()->id;
       
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
        $idusuario  = Auth::user()->id;
        if($idusuario == "0957258056"){
           // dd($productos);
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
        $idusuario  = Auth::user()->id;
        
        if (!is_null($productos)) {
            
           $detalle= Ct_Detalle_Acreedores::where('id_proveedor',$productos->id)->first();
            if(is_null($detalle)){ //esto es tu cagada tursi era este if y comentaste lalinea que no
                return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion,'caducidad'=>'']; 
            }
            return ['value' => $productos->id, 'direccion' => $productos->direccion, 'serie' => $productos->serie, 'autorizacion' => $productos->autorizacion,'caducidad'=>$detalle->f_caducidad];
        } else {
            return ['value' => 'no'];
        }
    }
    public function subir_masivo(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //dd($request);
/*         $empresa= Empresa::all();
        //dd($empresa);
        foreach($empresa as $e){
            $contador=1;
            $deposito= Ct_Deposito_Bancario::where('empresa',$e->id)->where('estado','1')->orderBy('id','ASC')->get();
            //dd($deposito);
            foreach($deposito as $value){
                $depositos= Ct_Deposito_Bancario::find($value->id);
                $depositos->numero=$contador;
                $depositos->save();
                $contador++;
            }
        } */

       /*  $array=['417','418','419'];
        $notadebito= Ct_Nota_Debito_Cliente::whereIn('id',$array)->get();
        //dd($notadebito);
        $countdebito= Ct_Nota_Debito_Cliente::where('id_empresa','0992704152001')->get()->count();
        foreach($notadebito as $x){
            
            $factura_venta = [
                'sucursal'            => '999',
                'punto_emision'       => '001',
                'numero'              => $countdebito,
                'nro_comprobante'     => $countdebito,
                'id_asiento'          => $x->id_asiento_cabecera,
                'id_empresa'          => '0992704152001',
                'tipo'                => 'N-D',
                'fecha'               => $x->fecha,
                'concepto'            => $x->concepto,
                'divisas'             => '1',
                'nombre_cliente'      => $x->cliente->nombre,
                'tipo_consulta'       => '',
                'id_cliente'          => $x->id_cliente, //nombre_cliente
                'direccion_cliente'   => '',
                'ruc_id_cliente'      => $x->id_cliente,
                'telefono_cliente'    => '',
                'email_cliente'       => '',
                'orden_venta'         => '',
                'nro_autorizacion'    => '',
                'id_paciente'         => '3333333333',
                'nombres_paciente'    => '',
                'id_hc_procedimiento' => '',
                'seguro_paciente'     => '',
                'procedimientos'      => '',
                'fecha_procedimiento' => '',
                'copago'              => '',
                'subtotal_0'          => $x->valor_contable,
                'subtotal_12'         => '0',
                'descuento'           => '',
                'base_imponible'      => $x->valor_contable,
                'impuesto'            => $x->valor_contable,
                'total_final'         => $x->valor_contable,
                'valor_contable'      => $x->valor_contable,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
            $countdebito++;
        } */
        //03
        //$empresa= Empresa::all();
        //dd($empresa);
       /*      foreach($empresa as $e){
                // $activofijo= AfFacturaActivoCabecera::where('id_empresa',$e->id)->get();
                // foreach($activofijo as $c){
                //     $compras= Ct_compras::where('tipo_gasto',$c->id)->where('id_empresa',$e->id)->get();
                //     foreach($compras as $x){
                //         $xp= Ct_compras::find($x->id);
                //         $xp->credito_tributario= "03";
                //         $xp->ip_creacion="creacion";
                //         $xp->save();
                //     }
                // }

                $compras = Ct_Compras::where('id_empresa', $e)->where('tipo', 2)->where('estado', '<>', '0')->get();
                foreach ($compras as $c){
                    $asi_cab = Ct_Asientos_Detalle::where('id_asiento_cabecera', $c->id_asiento_cabecera)->get();
                    foreach ($asi_cab as $ac){
                        if($ac->id_plan_cuenta== '1.01.05.01.02'){
                            $det = Ct_Asientos_Detalle::find($ac);
                            $det->plan_cuentas = '5.2.02.16.15';
                            $det->save();
                        }
                    }
                }
            } */
/*         $empresa= Empresa::all();
        foreach($empresa as $xz){
            $ventas=Ct_compras::where('estado','<>','0')->where('id_empresa',$xz->id)->get();
            foreach($ventas as $x){
                $z= $x->id;
                $pruebate= Contable::recovery_price($z,'C');
            }
    
        }
         */
        //$fix= Contable::fix_secuencia('CI');
        $data_array = array(
            "tipo"                    => "MEM-VEN",//tipo de de orden de venta
            "fecha"                   => "2021-08-01", //fecha de orden
            "id_empresa"              => "0992704152001", //empresa
            "divisas"                 => "1",//cualquiera
            "nombre_cliente"          =>"PAPI CHILAN", //nombre del cliente
            "tipo_consulta"           =>"1", //esto clavado
            "identificacion_cliente"  =>"1316262193", // ci de cliente
            "direccion_cliente"       =>"MUCHO LOTE", //direccion del cliente
            "telefono_cliente"        =>"132231", // telefono de cliente
            "mail_cliente"            =>"tumarido@mail.com", //mail del cliente
            "orden_venta"             =>"321", //idde referencia
            "identificacion_paciente" =>"1316262193", // ci de paciente
            "nombre_paciente"         => "PAPI CHILAN", // nonbre de paciente
            "id_seguro"               =>"1", //de la tabla seguros,
            "subtotal_01"             =>"12.00",
            "subtotal_121"            =>"12.00",
            "descuento1"              =>"12.00",
            "tarifa_iva1"             =>"12.00",
            "base"                    => "12.00",
            "totalc"                  => "12.00",
            "valor_contable"          => "12.00",
            "details"         => array(
                array(
                    "codigo"       => "001",
                    "nombre"       => "HEY YOU",
                    "cantidad"     => "1",
                    "precio"       => "2.00",
                    "extendido"    => "3.00",
                    "iva"          => "1",
                    "descpor"      => "10",
                    "descuento"    => "10",
                    "copago"       => "10",
                    "detalle"      =>"EXAMEN PAPI",
                    "precioneto"   =>"10", 

                )
            ),
            "id_usuario"=> "1316262193"
        ); 
       /*  $data_array['id']='3188';
        $data_array['id_usuario']='1316262193';

        $fix= Contable::update_data($data_array); */
        //return view('insumos.ingreso.two');
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
    public function verificar_anulacion(Request $request)
    {
        $verificacion = $request['verificar'];
        $tabla ="";
        $conf=0;
        $tiene="no";
        $id_compra =  $request['id_compra'];

        // switch ($verificacion) {
        //     case '1':
        //        /***************************COMPROBANTE DE EGRESOS************************/

        //         $id =  $request['id_compra'];
               
        //         $egreso = Ct_Detalle_Comprobante_Egreso::where('id_comprobante', $id)->get();
        //         $tabla_cruce="";
        //         $id_cruce =0;
        //         if(count($egreso)){
        //             foreach($egreso as $value){
        //                 $cruce_detalle = DB::table('ct_detalle_cruce')->where('id_factura',$value->id_compra)->get();
        //                     if(count($cruce_detalle)){
        //                         foreach($cruce_detalle as $values){
        //                             $cruce = Ct_Cruce_Valores::where('id', $values->id_comprobante)->where('estado', '1')->get();
        //                             if(count($cruce)){
        //                                 $tiene="si";
        //                                 $tabla_cruce ="Cruce de valores";
        //                                 $id_cruce = $cruce[0]->id;
        //                             }
        //                         }
                                   
        //                     }
        //             }
        //         }
        //         $tablas = [$tabla_cruce];
        //         $ids = [$id_cruce];
        //         return ['id'=>$id, 'tablas'=>$tablas, 'respuesta'=> $tiene ,"ids" => $ids];
        //     //return ['existe'=>$conf, 'tabla'=>'Cruce Valores', 'respuesta'=> $tiene, 'id_egreso'];
        //     break;
        //     case '2':
        //         /**************************DEBITO BANCARIO**********************************/
        //         $id =  $request['id_compra'];
        //         $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_debito',$id)->where('estado', "!=", "0")->get();
        //         $tiene = "no";
        //         $tabla_cruce="";
        //         $id_cruce =0;
        //         if(count($debito_banca_det)){
        //            foreach($debito_banca_det as $value){
        //                 $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura',$value->id_compra)->get();
        //                 if(count($detalle_cruce)){
        //                     foreach($detalle_cruce as $values){
        //                         $cruce = DB::table('ct_cruce_valores')->where('id', $values->id_comprobante)->where('estado', '1')->get();
        //                         if(count($cruce)){
        //                             $tiene="si";
        //                             $tabla_cruce ="Cruce de valores";
        //                             $id_cruce = $cruce[0]->id;
        //                         }
        //                     }
        //                 }  
        //            } 
        //         }
        //         $tablas = [$tabla_cruce];
        //         $ids = [$id_cruce];
        //         return ['id'=>$id, 'tablas'=>$tablas, 'respuesta'=> $tiene ,"ids" => $ids];

        //         //return ['existe'=>"si", 'tabla'=>'Cruce Valores', 'respuesta'=> $tiene];
        //     break;
        //     case '3':
        //         /**********************RETENCIONES*********************************/
        //         $tiene="no";
        //         $tabla ="";
        //         $id =  $request['id_compra'];
        //         $tabla_egre ="";
        //         $tabla_banca="";
        //         $tabla_cruce="";
        //         $tabla_rete="";
        //         $id_egreso = 0;
        //         $retenciones = Ct_Retenciones::where('id', $id)->first();
        //         $com_egre = Ct_Detalle_Comprobante_Egreso::where('id_compra', $retenciones->id_compra)->get();
               
        //         if(count($com_egre)>0){
        //             foreach($com_egre as $value){
        //                 $egreso        = Ct_Comprobante_Egreso::where('id', $value->id_comprobante)->where('estado','=','1')->get();
        //                 if(count($egreso)>0){
        //                         $tiene = "si";
        //                         $tabla_egre = "Comprobante de Egreso ";
        //                         $id_egreso = $egreso[0]->id;
        //                 }   
        //             }
                    
        //         }

        //         $id_deb_ban = 0;
        //         $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_compra',$retenciones->id_compra)->get();

        //         if(count($debito_banca_det)>0){

        //             foreach($debito_banca_det as $value){

        //                 $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado','=','1')->get();
        //                 if(count($debit_banca)>0){

        //                     $tiene= "si";
        //                     $tabla_banca = "Debito Bancario ";
        //                     $id_deb_ban = $debit_banca[0]->id;

        //                 }
        //             }
                   
        //         }

        //         $id_cruce_valores =0;
                   
        //         $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura',$retenciones->id_compra)->get();
            
        //         if(count($detalle_cruce)>0){
        //             foreach($detalle_cruce as $value){
        //                 $cruce_valores = Ct_Cruce_Valores::where('id', $value->id_comprobante)->where('estado', '1')->get();
        //                 if(count($cruce_valores)>0){
        //                     $tiene = "si";
        //                     $tabla_cruce = "Cruce Valores ";
        //                     $id_cruce_valores = $cruce_valores[0]->id;
        //                 }
        //             }
                    
        //         }

        //         $tablas = [$tabla_egre , $tabla_banca , $tabla_cruce];
        //         $ids = [$id_egreso, $id_deb_ban, $id_cruce_valores];

        //         return ['id'=>$retenciones->id, 'tablas'=>$tablas, 'respuesta'=> $tiene ,"ids" => $ids];

        //     break;
        //     case '4':
        //         $tabla_egre ="";
        //         $tabla_banca="";
        //         $tabla_cruce="";
        //         $tabla_rete="";
        //         $id_egreso=0;
        //         $id_deb_ban=0;
        //         $id_cruce_valores=0;
        //         $id_retencion = 0;
        //         /*********************COMPRAS*****************************************/
        //         $id =  $request['id_compra'];
        //         $tiene="";
        //         $tabla ="";

              

        //         $retenciones = Ct_Retenciones::where('id_compra', $id)->get();
               
        //         if(count($retenciones)>0){
        //             foreach($retenciones as $value){
        //                 if($value->estado == '1'){
        //                     $tiene = "si";
        //                     $id_retencion = $retenciones[0]->id;
        //                 }
        //             }
        //             if($tiene == "si"){
        //                 $tabla_rete = "Retenciones ";
        //             }
        //         }
        //         $com_egre = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id)->get();
        //        // dd("Hola");
              
        //         if(count($com_egre)>0){
        //             foreach($com_egre as $value){
        //                 $egreso = Ct_Comprobante_Egreso::where('id', $value->id_comprobante)->where('estado','=','1')->get();
        //                 if(count($egreso)>0){
        //                         $tiene = "si";
        //                         $tabla_egre = "Comprobante de Egreso ";
        //                         $id_egreso = $egreso[0]->id;
                                
        //                 }   
        //             }
        //         }

        //         $debito_banca_det = Ct_Debito_Bancario_Detalle::where('id_compra',$id)->get();
        //         if(count($debito_banca_det)>0){
        //             foreach($debito_banca_det as $value){
        //                 $debit_banca = DB::table('ct_debito_bancario')->where('id', $value->id_debito)->where('estado','=','1')->get();
        //                 if(count($debit_banca)>0){
        //                     $tiene= "si";
        //                     $tabla_banca = "Debito Bancario ";
        //                     $id_deb_ban = $debit_banca[0]->id;
        //                 }
        //             }
        //         }

        //         $detalle_cruce = DB::table('ct_detalle_cruce')->where('id_factura',$id)->get();
        //         if(count($detalle_cruce)>0){
        //             foreach($detalle_cruce as $value){
        //                 $cruce_valores = Ct_Cruce_Valores::where('id', $value->id_comprobante)->where('estado', '1')->get();
        //                 if(count($cruce_valores)>0){
        //                     $tiene = "si";
        //                     $tabla_cruce = "Cruce Valores ";
        //                     $id_cruce_valores = $cruce_valores[0]->id;
        //                 }
        //             }
                    
        //         }
               

               
        //         $tablas = [$tabla_egre , $tabla_banca , $tabla_cruce,$tabla_rete];
        //         $ids = [$id_egreso, $id_deb_ban, $id_cruce_valores,$id_retencion];
        //       //  dd($tablas);
        //         return ['id'=>$id, 'tablas'=>$tablas, 'respuesta'=> $tiene ,"ids" => $ids];
                
        //     break;
        // }
       
        
        
        

        switch ($verificacion) {
            case '1':
                $verificar   = 0;
                $egresos     = 0;
                $retencionid = 0;
                $cabecera    = null;
                $compras     = Ct_Detalle_Comprobante_Egreso::where('id_compra', $request['id_compra'])->where('estado', '!=', '0')->get();

                if (is_null($cabecera) || $cabecera == '[]') {
                    $verificar = 1;
                } else {
                    $verificar = 0;
                    $cabecera  = Ct_Comprobante_Egreso::where('id', $compras[0]->id_comprobante)->first();
                    if ($cabecera->estado == 1) {
                        $egresos = $compras[0]->id_comprobante;
                    } else {
                        $verificar = 1;
                    }
                }
                $retenciones = Ct_Retenciones::where('id_compra', $request['id_compra'])->where('estado', '!=', '0')->first();
                //dd($retenciones);
                if (is_null($retenciones) || $retenciones == '[]') {
                    if ($verificar == 1) {
                        $verificar = 3;
                    } else {
                        $verificar = 2;
                    }
                } else {
                    $verificar   = 0;
                    $retencionid = $retenciones->id;
                }
                $debito = Ct_Debito_Bancario_Detalle::where('id_compra', $request['id_compra'])->first();
                $id_debito = 0;
                if (!is_null($debito)) {
                    $id_debito = $debito->id_debito;
                }
                //dd($retenciones);

                return [$verificar, $egresos, $retencionid, $id_debito];

                break;
            case '2':
                $verificar = 0;
                $compras   = Ct_Detalle_Comprobante_Egreso::where('id_comprobante', $request['id_compra'])->where('estado', '!=', '0')->get();
                foreach ($compras as $x) {
                    $reten = Ct_Retenciones::where('id_compra', $x->id_compra)->first();
                    if (is_null($reten) || $reten == '[]') {
                        $verificar++;
                    }
                }
                return [$verificar, $compras];

                break;
            case '3':
                $verificar           = 0;
                $id_egreso           = 0;
                $verificar_retencion = Ct_Retenciones::where('id', $request['id_compra'])->where('estado', '!=', '0')->first();
                $id_compra           = $verificar_retencion->id_compra;
                $compras             = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id_compra)->where('estado', '!=', '0')->get();

                if (is_null($compras) || $compras == '[]') {

                    $verificar++;
                } else {
                    $da        = Ct_Comprobante_Egreso::where('id', $compras[0]->id_comprobante)->first();
                    $id_egreso = 0;
                    if ($da->estado != 0) {
                        $id_egreso = $compras[0]->id_comprobante;
                    }
                }
                return [$id_egreso, $verificar];
                break;
            case '4':
                $verificar = 0;
                $compras   = Ct_Debito_Bancario_Detalle::where('id_debito', $request['id_compra'])->where('estado', '<>', '0')->get();
                foreach ($compras as $x) {
                    $reten = Ct_Retenciones::where('id_compra', $x->id_compra)->first();
                    if (is_null($reten) || $reten == '[]') {
                        $verificar++;
                    }
                }
                return [$verificar, $compras];

                break;
        }
    }
    public function masivo_final()
    {
        //$this->fixSupreme();
    }
    public function getDetail($rol = "", $setBussines)
    {
        if ($rol != "") {
            if ($rol == "compras") {
                $detailsCompras = Ct_Detalle_compra::where('estado', '1')->join('ct_compras as cp', 'cp.id', 'ct_detalle_compra.id_compra')->where('cp.id_empresa', $setBussines)->get();
                foreach ($detailsCompras as $detailsCompras) {
                    $id_producto = Ct_productos::where('codigo', $detailsCompras)->where('id_empresa', $setBussines)->first();
                    $cantidad = $detailsCompras->cantidad * $detailsCompras->precio;
                    $saldo_valor_unitario = $detailsCompras->extendido;
                    Ct_Kardex::create([
                        'fecha' => $detailsCompras->fecha,
                        'id_movimiento' => $detailsCompras->id_compra,
                        'tipo' => 'COMP',
                        'numero' => $detailsCompras->numero,
                        'producto_id' => $id_producto,
                        'cantidad' => $detailsCompras->cantidad,
                        'valor_unitario' => $detailsCompras->precio,
                        'total' => $detailsCompras->precio_extendido,
                        'saldo_cantidad' => $cantidad,
                        'saldo_valor_unitario' => $saldo_valor_unitario,
                        'saldo_total' => $saldo_valor_unitario,
                        'bodega_id' => $detailsCompras->bodega,
                    ]);
                }
                return response()->json(['success' => 'success']);
            } elseif ($rol == "ventas") {
                $detailsVentas = Ct_detalle_venta::where('estado', '1')->join('ct_ventas as v', 'v.id', 'ct_detalle_venta.id_ct_ventas')->where('v.id_empresa', $setBussines)->get();
                foreach ($detailsVentas as $detailsVentas) {
                    if (!is_null($detailsVentas)) {
                    }
                }
                return response()->json(['success' => 'success']);
            } elseif ($rol == "ingreso") {
                //aqui existen las existencias
                $detailsInventario = Ct_Detalle_Inventario::where('estado', '1')->join("ct_inventario as inventario", "inventario.id", "ct_detalle_inventario.id_comprobante")->where('inventario.id_empresa', $setBussines)->get();
                foreach ($detailsInventario as $detailsInventario) {
                    if (!is_null($detailsInventario)) {
                    }
                }
                return response()->json(['success' => 'success']);
            }
        } else {
            return response()->json(['error' => 'No llega el rol']);
        }
    }
    public function uploadProductos()
    {
        $ip_cliente = "subidaSistema";
        $idusuario  = Auth::user()->id;
        $id_empresa = "0992704152001";
        $insumos = Producto::whereRaw('tipo_producto = 5 OR tipo_producto = 6')->get();
        //dd($insumos);
        // {"mensaje ":"ok gracias amigo","El log de errores:":["4293051","7800062001914"]}{"mensaje ":"ok gracias amigo","El log de errores:":["4293051","7800062001914"]}
        $acumulate = [];
        foreach ($insumos as $insumos) {
            $validate = Ct_productos::where('codigo', $insumos->codigo)->first();
            if (is_null($validate)) {
                $id_producto = Ct_productos::insertGetId([
                    'codigo'                     => $insumos->codigo,
                    'nombre'                     => strtoupper($insumos->nombre),
                    'codigo_barra'               => $insumos->codigo,
                    'descripcion'                => $insumos->descripcion,
                    'id_empresa'                 => $id_empresa,
                    'clase'                      => '1',
                    'grupo'                      => '2',
                    'proveedor'                  => '',
                    'cta_gastos'                 => '1.01.03.01.02',
                    'cta_ventas'                 => '4.1.01.02',
                    'cta_costos'                 => '5.1.01.05',
                    'cta_devolucion'             => '4.1.01.02',
                    'reg_serie'                  => '',
                    'mod_precio'                 => '1',
                    'mod_desc'                   => '1',
                    'iva'                        => '1',
                    'promedio'                   => '',
                    'reposicion'                 => '',
                    'lista'                      => '',
                    'ultima_compra'              => '',
                    'descuento'                  => '',
                    'financiero'                 => '',
                    'marca'                      => '',
                    'modelo'                     => '',
                    'stock_minimo'               => '',
                    'fecha_expiracion'           => '',
                    'impuesto_iva_compras'       => '1.01.05.01.01',
                    'impuesto_iva_ventas'        => '2.01.07.01.01',
                    'impuesto_servicio'          => '',
                    'impuesto_ice'               => '',
                    'clasificacion_impuesto_ice' => '',
                    'ip_creacion'                => $ip_cliente,
                    'ip_modificacion'            => $ip_cliente,
                    'id_usuariocrea'             => $idusuario,
                    'id_usuariomod'              => $idusuario,
                ]);

                $data = array(
                    'id_usuariocrea'  => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'codigo_producto' => $insumos->id,
                    'id_insumo'       => $insumos->id,
                    'id_producto'     => $id_producto,

                );

                Ct_productos_insumos::insert($data);
            } else {
                array_push($acumulate, $insumos->codigo);
            }
        }
        return $acumulate;
    }

    public function nota_credito(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $proveedores = Proveedor::where('estado', 1)->get();
        //dd($request->all());
        $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        $id_proveedor = $request['id_proveedor'];
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
        return view("contable/acreedores_credito/nueva_vista", ['proveedores' => $proveedores, 'id_proveedor' => $id_proveedor, 'credito_acreedores' => $credito_acreedores, 'fecha_desde' => $request['desde'], 'fecha_hasta' => $request['hasta'], 'empresa' => $empresa]);
    }
    public function carga_logo(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $id_proovedor = $request['id_proveedor'];
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $logo = Proveedor::where('id', $id_proovedor)->first();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $ct_credito_acreedores = Ct_Credito_Acreedores::where('estado', 1)->get();
        //dd($ct_credito_acreedores);
        //dd($ct_credito_acreedores);
        return  view("contable/acreedores_credito/logo", ['ct_credito_acreedores' => $ct_credito_acreedores, 'empresa' => $empresa, 'logo' => $logo, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function getExcel(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_desde = $request['desde'];
        $proveedor = $request['id_proveedor'];
        $fecha_hasta = $request['hasta'];
        $empresa = Empresa::where('id', $id_empresa)->first();
        $credito_acreedores = Ct_Credito_Acreedores::where('estado', '1')->where('id_empresa', $id_empresa);
        $id_proveedor = $request['id_proveedor'];
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
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:H2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:H3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME NOTA DE CREDITO");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:H4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
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
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C5:D5');
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROVEEDOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // DETALLES

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                $i = $this->setDetalles($credito_acreedores, $sheet, 6, $fecha_hasta);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL 
                $sheet->cells('A3:H3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:H5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });


                $sheet->setWidth(array(
                    'A'     =>  12,
                    'B'     =>  12,
                    'C'     =>  12,
                    'D'     =>  12,
                    'E'     =>  12,
                    'F'     =>  12,
                    'G'     =>  12,
                    'H'     =>  12,
                ));
            });
        })->export('xlsx');
    }
    public function setDetalles($consulta, $sheet, $i, $fecha_hasta)
    {
        $acumulador = 0;
        foreach ($consulta as $value) {
            $acumulador += $value->valor_contable;
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
            $sheet->cell('G' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->valor_contable);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $this->setSangria($cont, $cell);
                $cell->setValue($value->valor_contable);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $i++;
        }
        $sheet->cell('G' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue('TOTAL:');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('H' . $i, function ($cell) use ($acumulador) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);
            $cell->setValue($acumulador);
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        $sheet->cells('G5:G' . $i, function ($cells) {
            $cells->setAlignment('right');
        });

        return $i;
    }
    public function getCompras()
    {
        /* 
           "fecha" => "05/01/2021"
            "ruc" => "0991179550001"
            "proveedor" => "TECNOCARGA EXPRESO Y TURISMO CIA.LTDA"
            "aut_sri" => "05012021001"
            "numero_de_factura" => "001-002-000006880"
            "cuenta_contable" => "Otros Honorarios "
            "detalle" => "Otros Honorarios "
            "subtotal_12" => 80.0
            "subtotal_0" => 0.0
            "subtotal" => 80.0
            "descuento" => null
            "iva_12" => 9.6
            "total" => 89.6
            "retencion" => "001002-000125"
            "aut_ret" => "05012021071"
            "base_fuente" => 80.0
            "codigo" => "3440"
            "pfuente" => "2.75%"
            "fuent_ret" => 2.2
            "base_iva" => 9.6
            "piva" => "70%"
            "iva_reten" => 6.72
            "total_ret" => 8.92
            "saldo" => 80.68
          */
        Excel::filter('chunk')->load('fcompra.xlsx')->chunk(250, function ($reader) {
            $contador = 0;
            foreach ($reader as $book) {
                //dd(date('Y-m-d',strtotime(date('Y-m-d',strtotime($book->fecha)))));
                $proveedores = $this->getProveedores($book);
                $idusuario = "1316262193";
                $ip_cliente = "arreglarxd";
                $cuenta_principal = $this->getCount($book->cuenta_contable);

                $cabeceraa = [
                    'observacion'     => $book->detalle,
                    'fecha_asiento'   => date('Y-m-d', strtotime($book->fecha)),
                    'fact_numero'     => '',
                    'valor'           => $book->total,
                    'id_empresa'      => '1307189140001',
                    'estado'          => '1',
                    'aparece_sri'     => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
                $nueva_fecha         = null;
                $modfecha            = date('Y-m-d', strtotime($book->fecha));
                $proveedor = Proveedor::find($book->ruc);
                $input     = [
                    'tipo'                => '2',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha'               => date('Y-m-d', strtotime($book->fecha)),
                    'archivo_sri'         => '1',
                    'proveedor'           => $book->ruc,
                    'direccion_proveedor' => $proveedor->direccion,
                    'termino'             => '6',
                    'orden_compra'        => '1',
                    'f_caducidad'         => date('Y-m-d', strtotime($book->fecha)),
                    'tipo_gasto'          => '',
                    'sucursal'            => substr($book->numero_de_factura, 0, 3),
                    'punto_emision'       => substr($book->numero_de_factura, 4, 3),
                    'valor_contable'      => $book->saldo,
                    'fecha_termino'       => $modfecha,
                    'secuencia_f'         => substr($book->numero_de_factura, 8),
                    'estado'              => '1',
                    'autorizacion'        => $book->aut_sri,
                    'f_autorizacion'      => date('Y-m-d', strtotime($book->fecha)),
                    'serie'               => substr($book->numero_de_factura, 8),
                    'id_empresa'          => '13071891400011307189140001',
                    'numero'              => $book->numero_de_factura,
                    'secuencia_factura'   => substr($book->numero_de_factura, 8),
                    'credito_tributario'  => "00",
                    'tipo_comprobante'    => "01",
                    'observacion'         => $book->detalle,
                    'subtotal_0'          => $book->subtotal_0,
                    'subtotal_12'         => $book->subtotal_12,
                    'subtotal'            => $book->subtotal,
                    'descuento'           => $book->descuento,
                    'iva_total'           => $book->iva_12,
                    'ice_total'           => "",
                    'total_final'         => $book->total,
                    'transporte'          => "",
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                //dd($input);
                $id_compra = Ct_compras::insertGetId($input);
                $precio = 0;
                $iva = 0;
                if ($book->iva_12 > 0) {
                    $precio = $book->subtotal_12;
                    $iva = 1;
                } else {
                    $precio = $book->subtotal_0;
                }
                $plan_cuentas = Plan_Cuentas::find($cuenta_principal);

                $detalle = [
                    'id_ct_compras'        => $id_compra,
                    'codigo'               => $cuenta_principal,
                    'nombre'               => $plan_cuentas->nombre,
                    'cantidad'             => '1',
                    'precio'               => $precio,
                    'bodega'               => '',
                    'descuento_porcentaje' => '',
                    'estado'               => '1',
                    'descuento'            => $book->descuento,
                    'extendido'            => $precio,
                    'detalle'              => $book->detalle,
                    'iva'                  => $iva,
                    'porcentaje'           => '0.12',
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];
                //dd($detalle);
                Ct_detalle_compra::create($detalle);
                $desc_cuenta = Ct_Configuraciones::obtener_cuenta('COMPRASANT_PROVE_LOCALES');
                //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $desc_cuenta->cuenta_guardar,
                    'descripcion'         => $desc_cuenta->nombre_mostrar,
                    'fecha'               => date('Y-m-d', strtotime($book->fecha)),
                    'haber'               => $book->total,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => date('Y-m-d', strtotime($book->fecha)),
                    'debe'               => $book->total,
                    'haber'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
                $retenciones = $this->retenciones($book, substr($book->numero_de_factura, 0, 3), substr($book->numero_de_factura, 4, 3), $id_compra);
                $contador++;
            }
        });
    }
    public function retenciones($book, $sucursal, $punto_emision, $id_compra)
    {
        /* 
           "fecha" => "05/01/2021"
            "ruc" => "0991179550001"
            "proveedor" => "TECNOCARGA EXPRESO Y TURISMO CIA.LTDA"
            "aut_sri" => "05012021001"
            "numero_de_factura" => "001-002-000006880"
            "cuenta_contable" => "Otros Honorarios "
            "detalle" => "Otros Honorarios "
            "subtotal_12" => 80.0
            "subtotal_0" => 0.0
            "subtotal" => 80.0
            "descuento" => null
            "iva_12" => 9.6
            "total" => 89.6
            "retencion" => "001002-000125"
            "aut_ret" => "05012021071"
            "base_fuente" => 80.0
            "codigo" => "3440"
            "pfuente" => "2.75%"
            "fuent_ret" => 2.2
            "base_iva" => 9.6
            "piva" => "70%"
            "iva_reten" => 6.72
            "total_ret" => 8.92
            "saldo" => 80.68
          */
        //dd($sucursal,$punto_emision);
        $idusuario = "1316262193";
        $ip_cliente = "arreglarxd";
        $contador_ctv = DB::table('ct_retenciones')->where('id_empresa', '1307189140001')->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();
        $numero_factura = "0";
        if ($contador_ctv == 0) {
            //return 'No Retorno nada';
            $num = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
        } else {

            //Obtener Ultimo Registro de la Tabla ct_ventas
            $max_id = DB::table('ct_retenciones')->where('id_empresa', '1307189140001')->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
            $max_id = intval($max_id->secuencia);

            if (strlen($max_id) < 10) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }
        $cabeceraa = [
            'observacion'     => $numero_factura . $book->detalle,
            'fecha_asiento'   => date('Y-m-d', strtotime($book->fecha)),
            'fact_numero'     => $numero_factura,
            'valor'           => $book->total_ret,
            'estado'          => '1',
            'id_empresa'      => '1307189140001',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
        $input               = [
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_proveedor'        => $book->ruc,
            'id_compra'           => $id_compra,
            'autorizacion'        => $book->aut_ret,
            'nro_comprobante'     => $book->numero_factura,
            'valor_fuente'        => $book->base_fuente,
            'fecha'               => date('Y-m-d', strtotime($book->fecha)),
            'valor_iva'           => $book->base_iva,
            'id_empresa'          => '1307189140001',
            'sucursal'            => $sucursal,
            'punto_emision'       => $punto_emision,
            'tipo'                => '1',
            'id_tipo'             => '1',
            'descripcion'         => $book->detalle,
            'estado'              => '1',
            'total'               => $book->total_ret,
            'secuencia'           => $numero_factura,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];
        $id_retenciones = Ct_Retenciones::insertGetId($input);

        $rfir    = DB::table('ct_porcentaje_retenciones')->where('codigo', $book->codigo)->where('tipo', '2')->first();

        $rfiva      = DB::table('ct_porcentaje_retenciones')->where('codigo', $book->codigo)->where('tipo', '1')->first();

        if (!is_null($rfir)) {
            Ct_detalle_retenciones::create([
                'id_retenciones'  => $id_retenciones,
                'observacion'     => $book->detalle,
                'id_tipo'         => $rfir->id,
                'tipo'            => 'RENTA',
                'id_porcentaje'   => $rfir->id,
                'codigo'          => $book->codigo,
                'base_imponible'  => $book->base_fuente,
                'estado'          => '1',
                'totales'         => $book->fuent_ret,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }
        if (!is_null($rfiva)) {

            Ct_detalle_retenciones::create([
                'id_retenciones'  => $id_retenciones,
                'observacion'     => $book->detalle,
                'id_tipo'         => $rfiva->id,
                'tipo'            => 'IVA',
                'id_porcentaje'   => $rfiva->id,
                'codigo'          => $book->codigo,
                'base_imponible'  => $book->base_iva,
                'estado'          => '1',
                'totales'         => $book->iva_reten,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }
        //dd($rfiva); dd($rfiva); dd($rfiva);

    }
    public function getProveedores($book)
    {
        $proveedor = Proveedor::find($book->ruc);
        if (is_null($proveedor)) {
            Proveedor::create([
                'id' => $book->ruc,
                'razonsocial' => strtoupper($book->proveedor),
                'nombrecomercial' => strtoupper($book->provedor),
                'ciudad' => strtoupper('GUAYAQUIL'),
                'direccion' => strtoupper('AV JOSE ORRANTIA'),
                'email' => $book->ruc . '@mail.com',
                'telefono1' => '12',
                'telefono2' => '1',
                'id_tipoproveedor' => 1,
                'ip_creacion' => 'arreglarxd',
                'ip_modificacion' => 'arreglarxd',
                'id_usuariocrea' => '1316262193',
                'id_usuariomod' => '1316262193'
            ]);
        } else {
        }
    }
    public function getCount($counts)
    {
        $findme    = 'Otros Honorarios';
        $findme2 = 'Flete y Embalaje';
        $findme3 = 'Mantenimiento de Locales';
        $findme4 = 'Recolección Deshechos';
        $findme5 = 'Productos Terminado (Compras)';
        $account = "";
        $pos = stripos($counts, $findme);
        if ($pos !== false) {
            $account = "5.2.02.04.10";
        }
        $pos2 = stripos($counts, $findme2);
        if ($pos2 !== false) {
            $account = "5.2.02.08.01";
        }
        $pos3 = stripos($counts, $findme3);
        if ($pos3 !== false) {
            $account = "5.2.02.05.01";
        }
        $pos4 = stripos($counts, $findme4);
        if ($pos4 !== false) {
            $account = "5.2.01.07.06";
        }
        $pos5 = stripos($counts, $findme5);
        if ($pos5 !== false) {
            $account = "1.01.03.01.02";
        }
        if (($account) != "") {
            return $account;
        } else {
            return false;
        }
    }
    public function fixEgresos()
    {
        //function to change empresa
        /*   $proveedor="1790972186001";
        $egresos= Ct_Comprobante_Egreso::where('id_proveedor',$proveedor)->where('tipo','<>','2')->where('id_empresa','0992704152001')->where('estado','1')->get();  //ECUASURGICAL
        //$detalles= $egresos->detalles;
        //dd($egresos);
        foreach($egresos as $value){
            if(($value->detalles)!=null){
                foreach($value->detalles as $fix){
                    if(($fix->compras)!=null){
                        if($fix->compras->proveedor!=$proveedor){
                            //dd("dasda");
                            $cabecera= Ct_Comprobante_Egreso::find($fix->id_comprobante);
                            if(!is_null($cabecera)){
                                $cabecera->id_empresa="32222222222";
                                $cabecera->ip_creacion="revisarac";
                                $cabecera->save();
                            }
                        }
                    }
                }
                
            }
           
        } 
        $arreglarasientoegreso= Ct_Comprobante_Egreso::where('id_empresa','32222222222')->where('ip_creacion','revisarac')->get();
        foreach($arreglarasientoegreso as $value){
            $asiento= Ct_Asientos_Cabecera::find($value->id_asiento_cabecera);
            if(!is_null($asiento)){
                $asiento->id_empresa="32222222222";
                $asiento->ip_creacion="revisarac";
                $asiento->save();
            }
        }  
        #
        */
        $id_empresa = "0993170887001";
        $egresos = Ct_Comprobante_Egreso::where('id_empresa', $id_empresa)->where('estado', '1')->orderBy('id','asc')->get();
        //dd($egresos);
        $contador = 1;
        foreach ($egresos as $e) {
            $numero_factura = str_pad($contador, 10, "0", STR_PAD_LEFT);
            $s=Ct_Comprobante_Egreso::find($e->id);
            $s->secuencia=$numero_factura;
            $s->save();
            $contador++;
        }
        $egresos_varios= Ct_Comprobante_Egreso_Varios::where('id_empresa',$id_empresa)->where('estado','1')->orderBy('id','asc')->get();
        $contado1=1;
        foreach($egresos_varios as $varios){
            $numero_factura = str_pad($contado1, 10, "0", STR_PAD_LEFT);
            $s=Ct_Comprobante_Egreso_Varios::find($varios->id);
            $s->secuencia=$numero_factura;
            $s->save();
            $contado1++;
        }
        return 'ok, gracias amigo';
    }
    public function fixSupreme()
    {

        $compras = Ct_compras::where('f_autorizacion', '<=', '2020/12/31')->where('id_empresa', '0992704152001')->where('estado', '<>', '0')->get();
        foreach ($compras as $f) {
            $x = Ct_compras::find($f->id);
            if (!is_null($x)) {
                $x->id_empresa = "0992704152";
                $x->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($f->id_asiento_cabecera);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $retenciones = Ct_Retenciones::where('fecha', '<=', '2020/12/31')->where('id_empresa', '0992704152001')->where('estado', '<>', '0')->get();
        foreach ($retenciones as $r) {
            $ret = Ct_Retenciones::find($r->id);
            if (!is_null($ret)) {
                $ret->id_empresa = "0992704152";
                $ret->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($r->id_asiento_cabecera);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $egresos = Ct_Comprobante_Egreso::where('fecha_comprobante', '<=', '2020/12/31')->where('id_empresa', '0992704152001')->where('estado', '1')->get();  //ECUASURGICAL
        //dd($egresos);
        //$detalles= $egresos->detalles;
        //dd($egresos);
        foreach ($egresos as $value) {
            if (($value->detalles) != null) {
                foreach ($value->detalles as $fix) {
                    if (($fix->compras) != null) {
                        $cabecera = Ct_Comprobante_Egreso::find($fix->id_comprobante);
                        if (!is_null($cabecera)) {
                            $cabecera->id_empresa = "0992704152";
                            $cabecera->save();
                        }
                    }
                    $asi = Ct_Asientos_Cabecera::find($fix->id_asiento_cabecera);
                    if (!is_null($asi)) {
                        $asi->id_empresa = "0992704152";
                        $asi->save();
                    }
                }
            }
        }
        $debito = Ct_Debito_Bancario::where('fecha', '<=', '2020/12/31')->where('id_empresa', '0992704152001')->where('estado', '1')->get();
        foreach ($debito as $d) {
            $p = Ct_Debito_Bancario::find($d->id);
            if (!is_null($p)) {
                $p->id_empresa = "0992704152";
                $p->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($d->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $re = Ct_Nota_Credito::where('empresa', '0992704152001')->where('estado', '<>', '0')->where('fecha', '<=', '2020/12/31')->get();
        foreach ($re as $d) {
            $p = Ct_Nota_Credito::find($d->id);
            if (!is_null($p)) {
                $p->empresa = "0992704152";
                $p->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($d->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $re = Nota_Debito::where('empresa', '0992704152001')->where('estado', '<>', '0')->where('fecha', '<=', '2020/12/31')->get();
        foreach ($re as $d) {
            $p = Nota_Debito::find($d->id);
            if (!is_null($p)) {
                $p->empresa = "0992704152";
                $p->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($d->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $deposito = Ct_Deposito_Bancario::where('fecha_asiento', '<=', '2020/12/31')->where('estado', '<>', '0')->where('empresa', '0992704152001')->get();

        foreach ($deposito as $d) {
            $p = Ct_Deposito_Bancario::find($d->id);
            if (!is_null($p)) {
                $p->empresa = "0992704152";
                $p->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($d->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }

        $varios = Ct_Comprobante_Egreso_Varios::where('id_empresa', '0992704152001')->where('fecha_comprobante', '<=', '2020/12/31')->where('estado', '<>', '0')->get();
        foreach ($varios as $v) {
            $s = Ct_Comprobante_Egreso_Varios::find($v->id);
            if (!is_null($s)) {
                $s->id_empresa = "0992704152";
                $s->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($v->id_asiento_cabecera);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $transferencia = Ct_Transferencia_Bancaria::where('fecha_asiento', '<=', '2020/12/31')->where('empresa', '0992704152001')->where('estado', '<>', '0')->get();
        foreach ($transferencia as $p) {
            $p = Ct_Transferencia_Bancaria::find($p->id);
            if (!is_null($p)) {
                $p->empresa = "0992704152";
                $p->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($p->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $ventas = Ct_ventas::where('fecha', '<=', '2020/12/31')->where('id_empresa', '0992704152001')->where('estado', '<>', '0')->get();
        foreach ($ventas as $v) {
            $fact = Ct_ventas::find($v->id);
            if (!is_null($fact)) {
                $fact->id_empresa = "0992704152";
                $fact->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($v->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $ingresos = Ct_Comprobante_Ingreso::where('fecha', '<=', '2020/12/31')->where('estado', '<>', '0')->where('id_empresa', '0992704152001')->get();
        foreach ($ingresos as $i) {
            $fact = Ct_Comprobante_Ingreso::find($i->id);
            if (!is_null($fact)) {
                $fact->id_empresa = "0992704152";
                $fact->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($i->id_asiento_cabecera);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $inventarios = Ct_Nota_Inventario::where('id_empresa', '0992704152001')->where('fecha', '<=', '2020/12/31')->where('estado', '<>', '0')->get();
        foreach ($inventarios as $p) {
            $fact = Ct_Nota_Inventario::find($p->id);
            if (!is_null($fact)) {
                $fact->id_empresa = "0992704152";
                $fact->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($p->id_asiento_cabecera);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $retenciones = Ct_Cliente_Retencion::where('fecha', '<=', '2020/12/31')->where('id_empresa', '0992704152001')->where('estado', '<>', '0')->get();
        foreach ($retenciones as $r) {
            $ret = Ct_Cliente_Retencion::find($r->id);
            if (!is_null($ret)) {
                $ret->id_empresa = "0992704152";
                $ret->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($r->id_asiento_cabecera);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }
        $re = Nota_Debito::where('empresa', '0992704152001')->where('estado', '<>', '0')->where('fecha', '<=', '2020/12/31')->get();
        foreach ($re as $d) {
            $p = Nota_Debito::find($d->id);
            if (!is_null($p)) {
                $p->empresa = "0992704152";
                $p->save();
            }
            $asiento = Ct_Asientos_Cabecera::find($d->id_asiento);
            if (!is_null($asiento)) {
                $asiento->id_empresa = "0992704152";
                $asiento->save();
            }
        }



        // falta ahora eliminar los anulados para que no cuenten

        return response()->json("ok");
    }
}
