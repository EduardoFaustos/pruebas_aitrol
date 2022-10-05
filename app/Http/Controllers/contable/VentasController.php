<?php

namespace Sis_medico\Http\Controllers\contable;

use DateTime;
use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Support\Facades\Session;
use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_Detalle_Cliente_Retencion;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Detalle_Venta_Conglomerada;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Factura_Omni;
use Sis_medico\Ct_Factura_Procedimiento;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\Ct_Porcentajes_Retencion_Fuente;
use Sis_medico\Ct_Porcentajes_Retencion_Iva;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Ct_Retencion_Fventas;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Ventas_Recibo_Cobro;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inventario;
use Sis_medico\Log_usuario;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\ParametersConglomerada;
use Sis_medico\Planilla;
use Sis_medico\Plan_Cuentas;
use Sis_medico\PrecioProducto;
use Sis_medico\Procedimiento;
use Sis_medico\Producto;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Validate_Decimals;
use Sis_medico\LogConfig;
use Sis_medico\Ct_Comision_Detalle;
use Sis_medico\Ct_Comision_Cabecera;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\De_Empresa;
use Sis_medico\De_Info_Tributaria;
use Sis_medico\Http\Controllers\contable\VentasController as ContableVentasController;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Log_movimiento;
use Sis_medico\InvInventario;

class VentasController extends Controller
{
    private $controlador = 'ventas';
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
    /*******************************
     ***LISTADO FACTURAS DE VENTA ***
    /******************************/
    public function index(Request $request)
    {
        config(['data' => []]);
        if ($request->opcion == '') {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            $id_empresa = $request->session()->get('id_empresa');
            $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
            $data['controlador'] = $this->controlador;
            config(['data' => $data]);
            return view('contable/ventas/index', ['empresa' => $empresa]);
        } elseif ($request->opcion == 'getventasjs') {
            $data = Ct_ventas::join('users as uc', 'ct_ventas.id_usuariocrea', '=', 'uc.id')
                ->join('users as um', 'ct_ventas.id_usuariomod', '=', 'um.id')
                ->leftjoin('seguros as se', 'ct_ventas.seguro_paciente', '=', 'se.id')
                ->where('ct_ventas.estado', '<', 2)
                ->where('ct_ventas.id_empresa', session('id_empresa'))
                ->where('ct_ventas.tipo', "VEN-FA")
                ->orderby('ct_ventas.id', 'desc')
                ->get([
                    'ct_ventas.id',
                    'nro_comprobante',
                    'ct_ventas.tipo',
                    'ct_ventas.fecha',
                    'ct_ventas.estado',
                    'nombre_cliente',
                    'nombres_paciente',
                    'seguro_paciente',
                    'procedimientos',
                    'fecha_procedimiento',
                    'uc.nombre1',
                    'uc.nombre2',
                    'uc.apellido1',
                    'uc.apellido2',
                    'um.nombre1 as nombremod1',
                    'um.nombre2 as nombremod2',
                    'um.apellido1 as apellidomod1',
                    'um.apellido2 as apellidomod2',
                    'ct_ventas.estado_electronica',
                    'se.nombre as seguro',
                    'doc_electronico',
                    'nro_autorizacion',
                    'ct_ventas.id_empresa'
                ]);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            echo json_encode($results);
        } elseif ($request->opcion == 'logFactura') {
            echo '<pre>';
            print_r($request->input());
            exit;
        }
    }
    public function index2(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $ventas     = Ct_ventas::where('estado', '<', 2)
            ->where('id_empresa', $id_empresa)
            ->where('tipo', "VENFA-CO")
            ->orderby('id', 'desc')->paginate(10);
        return view('contable/ventas/index2', ['ventas' => $ventas, 'empresa' => $empresa]);
    }
    public function index2_buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $idusuario   = Auth::user()->id;
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id'               => $request['id'],
            'numero'           => $request['numero'],
            'id_asiento'       => $request['asiento'],
            'fecha'            => $request['fecha'],
            'nombre_cliente'   => $request['nombre_cliente'],
            'nombres_paciente' => $request['nombres_paciente'],
        ];
        $fields = array_keys($constraints);
        $ventas = array();
        for ($i = 0; $i < count($constraints); $i++) {
            if (!is_null($constraints[$fields[$i]])) {
                $ventas = Ct_ventas::where($fields[$i], "LIKE", "%" . $constraints[$fields[$i]] . "%")->where('id_empresa', $id_empresa)->where('tipo', "VENFA-CO")->paginate(10);
                break;
            }
        }
        if (count($ventas) <= 0) {
            $ventas = Ct_ventas::where('estado', '<', 2)
                ->where('id_empresa', $id_empresa)
                ->where('tipo', "VENFA-CO")
                ->orderby('id', 'desc')->paginate(10);
        }

        return view('contable/ventas/index2', ['ventas' => $ventas, 'empresa' => $empresa]);
    }
    /*******************************************
     **********ANULAMOS LA FACTURA VENTA*********
    /*******************************************/
    public function anular_factura($id, Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //Obtenemos la fecha de Hoy
        $fechahoy      = Date('Y-m-d H:i:s');
        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $idusuario     = Auth::user()->id;
        $concepto      = $request['concepto'];
        $estado_ventas = Ct_ventas::where('id', $id)->where('estado', '<>', 0)->first();
        if (!empty($estado_ventas)) {
            $act_estado = [
                'estado'        => '0',
                'id_usuariomod' => $idusuario,
            ];
            $id_empresa     = $request->session()->get('id_empresa');
            $registro_venta = Ct_ventas::findorfail($id);
            $data['id']     = $id;
            $data['tipo']   = 'VEN-FA';
            $msj            = Ct_Kardex::anular_kardex($data);
            Ct_ventas::where('id', $id)->update($act_estado);
            $ordenes = Ct_Ven_Orden::where('id_referencia', $id)->get();
            if (!is_null($ordenes)) {
                foreach ($ordenes as $v) {
                    $vs          = Ct_Ven_Orden::find($v->id);
                    $act_estadox = [
                        'estado_pago'   => '0',
                        'id_usuariomod' => $idusuario,
                    ];
                    $vs->update($act_estadox);
                }
            }
            //INVERSO
            //Lo que esta en el Haber va al Debe
            //Lo que esta en el Debe va al Haber

            $consulta_cabecera_ctventas = Ct_Asientos_Cabecera::where('estado', '1')
                ->where('id', $registro_venta->id_asiento)->first();

            $text = 'Fact #' . ':' . $registro_venta->nro_comprobante . '-' . $registro_venta->procedimientos . '-' . 'ANULACION FACTURA';

            $input_cabecera = [

                'fecha_asiento'   => $consulta_cabecera_ctventas->fecha_asiento,
                'fact_numero'     => $consulta_cabecera_ctventas->fact_numero,
                'id_empresa'      => $registro_venta->id_empresa,
                'observacion'     => $concepto . " " . $text,
                'valor'           => $consulta_cabecera_ctventas->valor,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $consulta_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $consulta_cabecera_ctventas->id)->get();

            if ($consulta_detalle != '[]') {

                foreach ($consulta_detalle as $value) {

                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cab,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $consulta_cabecera_ctventas->fecha_asiento,
                        'haber'               => $value->debe,
                        'debe'                => $value->haber,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }
            }

            return redirect()->intended('/contable/ventas');
        }
    }
    public function crearComprobante($nfactura, Request $request, $array_pagos, $factura_id)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        if (is_null($nfactura)) {
            $nfactura = $contador_ctv;
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num            = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->latest()->first();
                //dd($max_id);
                $secuencia = intval($max_id->secuencia);
                //dd($max_id->secuencia);
                if (strlen($secuencia) < 10) {
                    $nu             = $secuencia + 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $nfactura = $numero_factura;
        }
        $objeto_validar = new Validate_Decimals();
        $id_comprobante = 0;
        if (sizeOf($array_pagos) > 0) {
            $total_pagos = $request['valor_totalPagos'];

            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura,
                'fecha_asiento'   => $request['fecha_asiento'],
                'fact_numero'     => $nfactura,
                'valor'           => $objeto_validar->set_round($total_pagos),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $confg = LogConfig::busqueda('1.01.02.05.01');

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $confg,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $request['fecha_asiento'],
                'haber'               => $objeto_validar->set_round($total_pagos),
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO FACT: ' . $nfactura,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $request['fecha_asiento'],
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($total_pagos),
                'id_cliente'          => $request['identificacion_cliente'],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => $request['cedula_vendedor'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                if ($valor['fecha_pago'] != "") {
                }
                $val = 0;
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : $request['fecha_asiento'];
                $arraydetallepago = [
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $fecha_pago,
                    'numero'          => $valor['numero_pago'],
                    'id_banco'        => $valor['id_banco_pago'],
                    'id_tipo_tarjeta' => $valor['tipo_tarjeta'],
                    'id_tipo'         => $valor['id_tip_pago'],
                    'total'           => $val,
                    'cuenta'          => $valor['id_cuenta_pago'],
                    'girador'         => $valor['giradoa'],
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                //echo '<pre>';print_r($arraydetallepago);exit;
                Ct_Detalle_Pago_Ingreso::insert($arraydetallepago);
            }

            $confg = LogConfig::busqueda('1.01.01.1.01');

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();

            $desc_cuenta = Plan_Cuentas::where('id', $confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta'      => $confg,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $request['fecha_asiento'],
                'debe'                => $total_pagos, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            $consulta_venta    = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $request['fecha_asiento'],
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $request['total1'], //$request['saldo_a'.$i],
                    'total'             => $total_pagos, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            if ($total_pagos < $request['total1']) {
                //aqui creo una orden de venta nueva

            }

            //$orden_id = $this->ordenVenta($request['id_venta'], $request);

            $valor_actual = Ct_ventas::where('id', $factura_id)->first();
            $id__ventas   = [

                'valor_contable' => floatVal($valor_actual->valor_contable) - $total_pagos,
                'estado_pago'    => 2,
            ];

            Ct_ventas::where('id', $factura_id)->update($id__ventas);
            $consulta_venta  = null;
            $input_actualiza = null;

            return $id__ventas;
        } else {
            return 0;
        }
    }
    /**********************************************
     ****CREAR FACTURAS DE VENTA DESDE LA AGENDA****
    /**********************************************/
    public function crear($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id);
        $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
        $agenda = Agenda::findorfail($procedimiento->historia->id_agenda);
        $paciente = Paciente::findorfail($agenda->id_paciente);
        $ct_cliente = ct_clientes::where('identificacion', $paciente->id_usuario)->first();
        $divisas  = Ct_Divisas::where('estado', '1')->get();
        $clientes = Ct_Clientes::where('estado', '1')->get();
        $bodega   = bodega::where('estado', '1')->get();
        //$tipo_pago = Ct_Forma_Pago::where('estado', '1')->get();
        $seguros = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empresa_general = Empresa::all();
        $empresa_sucurs = Empresa::findorfail($agenda->id_empresa);
        //Obtenemos los % de Retenciones al Iva y la Fuente
        $rete_iva    = Ct_Porcentajes_Retencion_Iva::where('estado', '1')->get();
        $rete_fuente = Ct_Porcentajes_Retencion_Fuente::where('estado', '1')->get();
        //Obtenemos el Tipo de Pago
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        //Obtenemos el Listado de bancos
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva = Ct_Configuraciones::where('id_plan', $id_plan_confg)->where('estado', '1')->first();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        return view('contable/ventas/create', ['clientes' => $clientes, 'bodega' => $bodega, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'divisas' => $divisas, 'procedimiento' => $procedimiento, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'id_cliente' => $paciente->id_usuario, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'empresa_general' => $empresa_general, 'empresa_sucurs' => $empresa_sucurs, 'rete_iva' => $rete_iva, 'rete_fuente' => $rete_fuente, 'cuentas' => $cuentas, 'sucursales' => $sucursales, 'productos' => $productos, 'iva' => $iva]);
    }
    /*************************************
     ****CREAR FACTURAS DE VENTA MANUAL****
    /*************************************/
    public function crear_factura(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $idusuario   = Auth::user()->id;
        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $clientes    = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago   = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        //$seguros         = Seguro::all();
        //Obtenemos los seguros Validos
        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $bodega = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        $confg = LogConfig::busqueda('4.1.01.02');
        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $iva       = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        //dd($bodega);
        return view('contable/ventas/create_factura', ['divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, 'tipo_tarjeta' => $tipo_tarjeta]);
    }
    /*************************************
     ****CREAR Orden DE VENTA MANUAL****
    /*************************************/
    public function crear_orden(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $bodega     = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        //dd($bodega);
        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();
        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();
        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas   = Plan_Cuentas::where('estado', '2')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $confg = LogConfig::busqueda('4.1.01.02');
        $iva       = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();
        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        return view('contable/ventas/create_orden', ['divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas]);
    }
    public function precios(Request $request)
    {
        $producto_id = $request->id;
        $id_seguro   = $request->id_seguro;
        $id_nivel    = $request->id_nivel;
        $precios = PrecioProducto::where('codigo_producto', $producto_id)
            ->where('estado', 1)->get();
        return $precios;
        $inf_prod_tar = Ct_Productos_Tarifario::where('id_producto', $producto_id)
            ->where('id_seguro', $id_seguro)
            ->where('nivel', $id_nivel)
            ->where('estado', 1)->first();
        return $inf_prod_tar;
    }
    /*************************************
     ****CREAR FACTURAS DE VENTA MANUAL****
    /*************************************/
    public function insumos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::where('inactivo', '1')->get();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();

        $confg = LogConfig::busqueda('4.1.01.02');
        $iva       = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();

        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        //$searchinsumos= $this->searchInsumos($request);
        $primeros_datos = "";
        $segundos_datos = "";
        $fecha_desde    = "";
        $tipox          = 0;
        $fecha_hasta    = "";
        //dd($searchinsumos);
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $bodegas      = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        return view('contable/ventas/insumos', ['bodegas' => $bodegas, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, 'primeros_datos' => $primeros_datos, 'segundos_datos' => $segundos_datos, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'tipo_tarjeta' => $tipo_tarjeta, 'tipox' => $tipox]);
    }
    public function searchInsumos(Request $request)
    {
        //$finicio, $ffin, $seguro){ ahora tb el tipo

        $fecha        = $request->fecha_desde;
        $fecha_hasta  = $request->fecha_hasta;
        $personal     = [];
        $total        = [];
        $id_empresa   = $request->session()->get('id_empresa');
        $paciente     = $request->identificacion_paciente;
        $ordenes      = "";
        $secure       = $request->id_seguro;
        $sss          = [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'];
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $tipox        = 1;
        //dd($request->tipo_servicio);

        if ($request->tipo_servicio == 2) {
            $ordenes = Ct_Ven_Orden::where('estado', '1')

                ->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '2');

            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])
                ->orderBy('fecha_procedimiento', 'desc')->get();
            //dd($ordenes);
            $tipox = 2;
        } else if ($request->tipo_servicio == 1) {
            $tipox = 1;

            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '1');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])
                ->orderBy('fecha_procedimiento', 'desc')->get();
        } else if ($request->tipo_servicio == 3) {
            $tipox   = 3;
            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('id_empresa', $id_empresa)->where('estado_pago', '<>', '1');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])->orderBy('fecha_procedimiento', 'desc')->get();
        }

        //dd($ordenes);
        //dd($secure);
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos      = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();
        // $iva            = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();

        $confg = LogConfig::busqueda('4.1.01.02');
        $iva       = Ct_Configuraciones::where('id_plan', $confg)->where('estado', '1')->first();

        $searchinsumos  = "";
        $primeros_datos = "";
        $segundos_datos = "";
        //dd($productos);
        $fecha_desde = $fecha;
        $fecha_hasta = $request->fecha_hasta;
        if (is_null($fecha_desde)) {
            $fecha_desde = date('Y-m-d');
        }
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $secure  = $request->id_seguro;
        $bodegas = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        //dd($bodegas);

        return view('contable/ventas/insumos', ['divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'tipo_tarjeta' => $tipo_tarjeta, 'cuentas' => $cuentas, 'searchinsumos' => $searchinsumos, 'ordenes' => $ordenes, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'tipox' => $tipox, 'secure' => $secure, 'bodegas' => $bodegas]);
    }
    public function modalDetalle($id, Request $request)
    {
        //dd($request->all());
        $ven_orden = Ct_Ven_Orden::find($request->id);

        $venta_orden     = Ct_Orden_Venta::find($ven_orden->orden_venta);
        $agenda          = $venta_orden->agenda;
        $ex_orden_agenda = Examen_Orden_Agenda::where('id_agenda', $agenda->id)->first();
        $ex_orden        = Examen_Orden::find($ex_orden_agenda->id_orden);
        $ex_detalle      = $ex_orden->detalles;
        // dd($ex_detalle);

        return view('contable/ventas/modal_detalles', ['examen_detalle' => $ex_detalle, 'id' => $request->id]);
    }
    public function selectsearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos  = [];
        if ($request['search'] != null) {
            $productos = Ct_productos::where('id_empresa', $id_empresa)->where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('ct_productos.codigo as id', 'ct_productos.nombre as text')->get();
        }

        return response()->json($productos);
    }
    public function clientesearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $clientes  = [];
        if ($request['search'] != null) {
            $clientes = Ct_Clientes::where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('ct_clientes.identificacion as id', 'ct_clientes.nombre as text')->get();
        }

        return response()->json($clientes);
    }
    public function cedulasearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $cedula_cliente  = [];
        if ($request['search'] != null) {
            $cedula_cliente = Ct_Clientes::where('identificacion', 'LIKE', '%' . $request['search'] . '%')->select('ct_clientes.identificacion as id', 'ct_clientes.identificacion as text')->get();
        }

        return response()->json($cedula_cliente);
    }
    public function productosearch(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos  = [];
        if ($request['search'] != null) {
            $productos = Ct_productos::where('nombre', 'LIKE', '%' . $request['search'] . '%')
                ->where('estado_tabla', '1')
                ->where('id_empresa', $id_empresa)
                ->select('ct_productos.codigo as id', 'ct_productos.nombre as text')->get();
        }

        return response()->json($productos);
    }
    public function buscar_producto_codigo(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        //$buscador = $request['search'];
        $buscador = trim($request->q);
        $productos  = [];

        if ($buscador != null) {
            $productos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', '1');

            $productos = $productos->where(function ($jq1) use ($buscador) {
                $jq1->orwhere('codigo', "LIKE", "%{$buscador}%")
                    ->orwhere('nombre', 'LIKE', "%{$buscador}%");
            });

            $productos = $productos->select('codigo as id', DB::raw('CONCAT(codigo," | ",nombre) as text'))->get();
        }

        //dd($productos);

        return response()->json($productos);
    }
    public function paginate($items, $perPage = 5, $page = null, $baseUrl = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $lap = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }
    public function previewData(Request $request)
    {
        //$finicio, $ffin, $seguro){

        $fecha       = date("Y/m/d", strtotime($request->finicio));
        $fecha_hasta = date("Y/m/d", strtotime($request->ffin));

        $consultas = DB::table('hc_evolucion as he')
            ->join('historiaclinica as h', 'h.hcid', 'he.hcid')
            ->join('users as hd', 'hd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as p', 'p.id', 'h.id_paciente')
            ->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'a.id')
            ->leftjoin('ct_ventas as v', 'v.orden_venta', 'ov.id')
            ->leftjoin('ct_ven_orden as vo', 'vo.id_ct_venta', 'v.id')
            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
            ->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')
            ->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')
            ->select('he.*', 'ov.*', 'v.id as asd', 'v.seguro_paciente', 'vo.total_final as valor_final', 'vo.id as idventa', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->where('pc.id', '40');

        if ($fecha != null) {
            $consultas = $consultas->whereBetween('a.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])->orderBy('a.fechaini');
        } else {
            $consultas = $consultas->orderBy('a.fechaini', 'desc');
        }
        $consultas = $consultas->where('a.espid', '<>', '10');
        $consultas = $consultas->where('v.seguro_paciente', $request->seguro);

        $consultas = $consultas->whereNotIn('he.hc_id_procedimiento', function ($q) {
            $q->select('id_hc_procedimientos')->from('ct_factura_procedimientos');
        })->get();

        return $consultas;
    }
    public function excelPreview($fecha, $fecha_hasta, $seguro, $tipo, $id_empresa, $secuencia = "001-00022-002")
    {

        $date  = new DateTime();
        $fecha = date('Y-m-d', ($fecha / 1000));

        $fecha_hasta = date('Y-m-d', ($fecha_hasta / 1000));
        $ordenes     = "";
        if ($tipo == 2) {
            $ordenes = Ct_Ven_Orden::where('estado', '1')
                ->where('seguro_paciente', $seguro)
                ->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '2')
                ->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta . ' 23:59:59')])
                ->orderBy('fecha', 'desc')
                ->get();
            $tipox = 2;
        } else if ($tipo == 1) {
            $tipox = 1;

            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('seguro_paciente', $seguro)->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '1')
                ->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta . ' 00:00:00')])
                ->orderBy('fecha_procedimiento', 'desc')->get();
        } else if ($tipo == 3) {
            $tipox   = 3;
            $ordenes = Ct_Ven_Orden::where('estado', '1')->where('seguro_paciente', $seguro)->where('id_empresa', $id_empresa)->where('estado_pago', '<>', '1')->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta . ' 00:00:00')])->orderBy('fecha_procedimiento', 'desc')->get();
        }

        //array_push($personal, $consultas);
        //dd($ordenes);

        Excel::create('Consultas Pre-Factura Conglomerada', function ($excel) use ($fecha, $fecha_hasta, $ordenes, $secuencia) {
            $excel->sheet('Factura por confirmar ', function ($sheet) use ($ordenes, $fecha, $fecha_hasta, $secuencia) {
                $sheet->mergeCells('A1' . ':D1');
                $contador = 1;
                $sheet->cell('A1', function ($cell) use ($fecha_hasta, $fecha, $secuencia) {
                    // manipulate the cel
                    $cell->setValue('FACTURA ' . $secuencia);
                    $cell->setFontWeight('bold');
                });
                $contador++;
                $sheet->cell('A' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ODA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TITULAR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLAN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $contador = 3;
                $sumatot  = 0;
                foreach ($ordenes as $x) {

                    $sumatot += $x->copago;
                    if (!is_null($x)) {
                        $sheet->cell('A' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            /*$id_x= $x->id_ct_venta;
                            $consult= Ct_ventas::where('id',$id_x)->where('estado','!=','0')->first();
                            if(!is_null($consult)){
                            if(!is_null($consult->orden_venta)){
                            $cell->setValue($consult->orden_venta->numero_oda);
                            }
                            }
                             */
                            $cell->setValue($x->id);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue(substr($x->fecha_procedimiento, 0, 10));

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x->nombres_paciente);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x->nombre_cliente);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x->estado_pago);

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F' . $contador, function ($cell) use ($x) {
                            // manipulate the cel
                            $total = $x->copago;
                            $cell->setValue(number_format($total, 2, '.', ','));

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $contador++;
                }
                $contador++;
                $sheet->cell('D' . $contador, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F' . $contador, function ($cell) use ($sumatot) {
                    // manipulate the cel
                    $cell->setValue(number_format($sumatot, 2, '.', ','));
                });
            });
        })->export('xlsx');
        //return $request;

    }
    public function validarStock(Request $request)
    {
        $producto = $request->producto;
        $bodega   = $request->bodega;

        //$query = Ct_Inventario::where('bodega_id',$bodega)->where('producto_id', $producto)->first();
        $query = DB::table('ct_productos as p')
            ->join('ct_inventario as i', 'i.producto_id', 'p.id')
            ->where('i.bodega_id', $bodega)
            ->join('ct_productos_insumos as pi', 'pi.id_producto', 'p.id')->where('p.codigo', $producto)
            ->select('*')
            ->get();
        return $query;
    }
    public function esInventariable(Request $request)
    {
        $producto = $request->producto;

        //$query = Ct_Productos_Insumos::where('id_producto',$producto)->count();

        $query = DB::table('ct_productos as p')
            ->where('p.codigo', $producto)
            ->join('ct_productos_insumos as pi', 'pi.id_producto', 'p.id')
            ->count();
        return $query;
    }
    public function excel($id, Request $request)
    {
        $factura   = $id;
        $consultas = DB::table('ct_factura_procedimientos as fp')
            ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'fp.id_hc_procedimientos')
            ->join('historiaclinica as h', 'h.hcid', 'he.hcid')
            ->join('users as hd', 'hd.id', 'h.id_doctor1')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('paciente as p', 'p.id', 'h.id_paciente')
            ->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', '=', 'h.id_agenda')
            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
            ->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')
            ->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')

            //        ->select('fp.*','he.*','ov.*','p.apellido1','p.apellido2','p.nombre1','p.nombre2','p.id as id_paciente','pc.nombre_general','a.fechaini','p.id as id_paciente','hp.id_seguro','hp.id_doctor_examinador','hs.nombre as hsnombre','hu.apellido1 as huapellido','hu.nombre1 as hunombre','pc.nombre_general','hd.apellido1 as hdapellido','hd.nombre1 as hdnombre','a.proc_consul')->where('pc.id','40')->get();
            ->select('ov.*', 'a.id', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->get();

        $consultas = $consultas->where('a.espid', '<>', '10');
        // dd($consultas);
        Excel::create('Reporte Consultas Factura -' . $factura, function ($excel) use ($factura) {

            $consultas = DB::table('ct_factura_procedimientos as fp')
                ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'fp.id_hc_procedimientos')
                ->join('historiaclinica as h', 'h.hcid', 'he.hcid')
                ->join('users as hd', 'hd.id', 'h.id_doctor1')
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->join('paciente as p', 'p.id', 'h.id_paciente')
                ->join('hc_procedimientos as hp', 'hp.id', 'he.hc_id_procedimiento')
                ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'a.id')
                ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
                ->leftjoin('seguros as hs', 'hs.id', 'hp.id_seguro')
                ->leftjoin('users as hu', 'hu.id', 'hp.id_doctor_examinador')
                ->select('fp.*', 'he.*', 'ov.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'p.id as id_paciente', 'pc.nombre_general', 'a.fechaini', 'p.id as id_paciente', 'hp.id_seguro', 'hp.id_doctor_examinador', 'hs.nombre as hsnombre', 'hu.apellido1 as huapellido', 'hu.nombre1 as hunombre', 'pc.nombre_general', 'hd.apellido1 as hdapellido', 'hd.nombre1 as hdnombre', 'a.proc_consul')->where('pc.id', '40');
            $consultas = $consultas->where('fp.id_ct_ventas', $factura);
            $consultas = $consultas->where('a.espid', '<>', '10')->get();

            $excel->sheet('Llamadas por confirmar', function ($sheet) use ($consultas) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ODA');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                });
                $contador = 2;
                foreach ($consultas as $value) {
                    $sheet->cell('A' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->numero_oda);
                    });
                    $sheet->cell('B' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fechaini);
                    });
                    $sheet->cell('C' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $nombre = "$value->apellido1 $value->apellido2 $value->nombre1 $value->apellido2";
                        $cell->setValue($value->id_paciente);
                    });
                    $sheet->cell('D' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $nombre = "$value->apellido1 $value->apellido2 $value->nombre1 $value->apellido2";
                        $cell->setValue($nombre);
                    });
                    $sheet->cell('E' . $contador, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total);
                    });
                    $contador++;
                }
            });
        })->export('xlsx');
    }
    /*************************************
     ****CARGA PRODUCTOS DESDE LA AGENDA***
    /*************************************/
    public function buscar_insumos_mov(Request $request)
    {

        $id_proced = $request['id_hc_proced'];
        $data      = null;

        if (!is_null($id_proced)) {

            $insumos = DB::table('movimiento_paciente as mp')
                ->where('mp.id_hc_procedimientos', $id_proced)
                ->join('movimiento as m', 'm.id', 'mp.id_movimiento')
                ->join('producto as p', 'p.id', 'm.id_producto')
                ->groupBy('m.serie')
                ->select('p.codigo', DB::raw('count(*) as  total'), 'p.nombre', 'm.id_bodega', 'p.iva', 'm.precio as cost_vent')
                ->get();
        }

        if ($insumos != '[]') {

            $data = [$insumos];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    /*******************************************
     ***OBTENEMOS SECUENCIA DE LA FACTURA VENTA**
    /*******************************************/
    public function obtener_numero_factura($idempresa, $sucursal, $punto_emision)
    {
        $contador_ctv   = Ct_ventas::where('id_empresa', $idempresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->get()->count();
        $numero_factura = 0;
        if ($contador_ctv == 0) {
            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {

            //Obtener Ultimo Registro de la Tabla ct_compras
            $max_id = Ct_ventas::where('id_empresa', $idempresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
            $max_id = intval($max_id->numero);
            //dd(strlen($max_id));
            if (strlen($max_id) < 9) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
        }
        return $numero_factura;
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $fecha_as        = $request['fecha_asiento'];
        $id_empresa      = $request->session()->get('id_empresa');
        $llevaOrden      = false;
        $numero          = $request['numero'];
        $numero1         = null;
        $nfactura        = 0;
        $num_comprobante = 0;
        $empresa         = Empresa::find($id_empresa);
        $cliente = Ct_Clientes::where('identificacion', '=', $request['identificacion_cliente'])->count();
        //return $cliente. " - ". $request['identificacion_cliente'];
        DB::beginTransaction();
        $msj = "no";
        try {
            // dd()
            if ($cliente == 0) {
                // cliente
                Ct_Clientes::create([
                    'nombre'                  => strtoupper($request['nombre_cliente']),
                    'tipo'                    => '5',
                    'identificacion'          => $request['identificacion_cliente'],
                    'clase'                   => '1',
                    'nombre_representante'    => $request['nombre_cliente'],
                    'cedula_representante'    => $request['identificacion_cliente'],
                    'ciudad_representante'    => $request['ciudad_cliente'],
                    'direccion_representante' => $request['direccion_cliente'],
                    'telefono1_representante' => $request['telefono_cliente'],
                    'email_representante'     => $request['mail_cliente'],
                    'estado'                  => '1',
                    'id_usuariocrea'          => $idusuario,
                    'id_usuariomod'           => $idusuario,
                    'ip_creacion'             => $ip_cliente,
                    'ip_modificacion'         => $ip_cliente,
                ]);
            }
            if ($request['sucursal'] != 0) {
                $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
                $c_caja     = $cod_caj->codigo_caja;
                $proced     = $request['procedimiento'];
                // echo($numero);
                if (!is_null($numero)) {
                    $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                    $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                        ->where('nro_comprobante', $num_comprobante)
                        ->where('tipo', '<>', 'VEN-FACT')
                        ->where('estado', '!=', '0')->first();
                    if (!is_null($verifica_num_factura)) {
                        return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                    } else {
                        $nfactura = $numero;
                    }
                } else {
                    $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                }
            } else {
                $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('codigo_sucursal', $empresa->establecimiento)->first();
                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj    = Ct_Caja::where('estado', '1')->where('codigo_caja', $empresa->punto_emision)->first();
                $c_caja = $cod_caj->codigo_caja;
                $proced = $request['procedimiento'];
                // echo($numero);
                if (!is_null($numero)) {
                    $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                    $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                        ->where('nro_comprobante', $num_comprobante)
                        ->where('tipo', '<>', 'VEN-FACT')
                        ->where('estado', '!=', '0')->first();
                    if (!is_null($verifica_num_factura)) {
                        return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                    } else {
                        $nfactura = $numero;
                    }
                } else {
                    $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                }
            }
            $pac = "";
            if ($request['nombre_paciente'] != "") {
                $pac = " | " . $request['nombre_paciente'];
            }
            $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
            $id_paciente = $request['identificacion_paciente'];
            $patient     = Paciente::find($id_paciente);
            $pc          = "9999999999";
            if (is_null($patient)) {
                $pc = "9999999999";
            } else {
                $pc = $patient->id;
            }
            //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
            //7******GUARDAdo TABLA ASIENTO CABECERA********
            $input_cabecera = [
                'sucursal'        => $c_sucursal,
                'punto_emision'   => $c_caja,
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text . " " . $request['concepto'],
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //$id_asiento_cabecera = 0;
            //GUARDAdo TABLA CT_VENTA.
            $diasPlazo = "0";
            $nota_electronica = "";
            if ($request->electronica == 1) {
                $diasPlazo = $request->diasPlazo;
                $nota_electronica = $request->nota_electronica;
            }
            $factura_venta = [
                'sucursal'            => $c_sucursal,
                'punto_emision'       => $c_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $pc,
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                //'id_nivel'            => $request['id_nivel'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'concepto'            => $request['concepto'],
                'copago'              => $request['totalc'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'ci_vendedor'         => $request['cedula_vendedor'],
                'vendedor'            => $request['vendedor'],
                'electronica'         => $request['electronica'],
                //'nota'                          => $request['nota'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                //'subtotal'          => $request['subtotal1'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'impuesto'            => $request['tarifa_iva1'],
                // 'transporte'                    => $request['transporte'],
                'total_final'         => $request['total1'],
                'valor_contable'      => $request['total1'],
                'dias_plazo'          => $diasPlazo,
                'nota_electronica'    => $nota_electronica,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            if (is_null($id_paciente)) {
                $factura_venta = [
                    'sucursal'            => $c_sucursal,
                    'punto_emision'       => $c_caja,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => $id_empresa,
                    'tipo'                => $request['tipo'],
                    'fecha'               => $request['fecha_asiento'],
                    'divisas'             => $request['divisas'],
                    'nombre_cliente'      => $request['nombre_cliente'],
                    'tipo_consulta'       => $request['tipo_consulta'],
                    'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente'   => $request['direccion_cliente'],
                    'ruc_id_cliente'      => $request['identificacion_cliente'],
                    'telefono_cliente'    => $request['telefono_cliente'],
                    'email_cliente'       => $request['mail_cliente'],
                    'orden_venta'         => $request['orden_venta'],
                    'nro_autorizacion'    => $request['numero_autorizacion'],
                    'nombres_paciente'    => $request['nombre_paciente'],
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente'     => $request['id_seguro'],
                    'procedimientos'      => $request['procedimiento'],
                    'fecha_procedimiento' => $request['fecha_proced'],
                    'concepto'            => $request['concepto'],
                    'copago'              => $request['totalc'],
                    'id_recaudador'       => $request['cedula_recaudador'],
                    'ci_vendedor'         => $request['cedula_vendedor'],
                    'vendedor'            => $request['vendedor'],
                    'electronica'         => $request['electronica'],
                    'subtotal_0'          => $request['subtotal_01'],
                    'subtotal_12'         => $request['subtotal_121'],
                    'descuento'           => $request['descuento1'],
                    'base_imponible'      => $request['subtotal_121'],
                    'impuesto'            => $request['tarifa_iva1'],
                    'total_final'         => $request['total1'],
                    'valor_contable'      => $request['total1'],
                    'dias_plazo'          => $diasPlazo,
                    'nota_electronica'    => $nota_electronica,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
            }
            // return $factura_venta;
            $id_venta = Ct_ventas::insertGetId($factura_venta);
            //$id_venta = 0;
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;
            for ($i = 0; $i < count($request->input("nombre")); $i++) {
                if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                    // if(Auth::user()->id =="0955728803"){
                    //         dd($request->input("nombre"),$request->input("cantidad"),$request->input("bodega"),$request->input("codigo"),$request->input("precio"),$request->input("descpor"),$request->input("copago"),$request->input("desc"),
                    //         $request->input("precioneto"),$request->input("descrip_prod"),$request->input("iva"),$request->input("bodega"));
                    // }
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
                        'bodega'     => $request->input("bodega")[$i],
                    ];
                    array_push($arr_total, $arr);
                }
            }
            foreach ($arr_total as $valor) {
                if ($valor['copago'] > 0) {
                    //registra orden de venta
                    $llevaOrden = true;
                }
                $detalle = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'bodega'               => $valor['bodega'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['precioneto'],
                    'detalle'              => $valor['detalle'],
                    'copago'               => $valor['copago'],
                    'check_iva'            => $valor['iva'],
                    'porcentaje'           => $request['ivareal'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];
                Ct_detalle_venta::create($detalle);
            }
            //***MODULO CUENTA POR COBRAR***
            //cUENTAS X COBRAR CLIENTES
            $val_tol = $request['total1'];
            if ($val_tol > 0) {
                $id_plan = LogConfig::busqueda('1.01.02.05.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                echo '<pre>';print_r($id_plan);DB::rollBack();exit;
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan = LogConfig::busqueda('2.01.07.01.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan = LogConfig::busqueda('4.1.01.02');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan = LogConfig::busqueda('4.1.01.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $valor_descuento = $request['descuento1'];
            if ($valor_descuento > 0) {
                $id_plan = LogConfig::busqueda('4.1.06.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan)->first();
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'haber'               => '0',
                    'debe'                => $request['descuento1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $variable = $request['contador_pago'];
            for ($i = 0; $i < $variable; $i++) {
                $visibilidad_p = $request['visibilidad_pago' . $i];
                if ($visibilidad_p == 1) {
                    Ct_Forma_Pago::create([
                        'id_ct_ventas'    => $id_venta,
                        'tipo'            => $request['id_tip_pago' . $i],
                        'fecha'           => $request['fecha_pago' . $i],
                        'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                        'numero'          => $request['numero_pago' . $i],
                        'banco'           => $request['id_banco_pago' . $i],
                        'cuenta'          => $request['id_cuenta_pago' . $i],
                        'giradoa'         => $request['giradoa' . $i],
                        'valor'           => $request['valor' . $i],
                        'valor_base'      => $request['valor_base' . $i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
            }
            $arr_p     = [];
            $validates = false;
            for ($i = 0; $i < $variable; $i++) {
                $visibilidad_pa = $request['visibilidad_pago' . $i];
                if ($visibilidad_pa == 1) {
                    if ($request['id_tip_pago' . $i] == '7') {
                        $validates = true;
                    }
                    $arr_pagos = [
                        'id_tip_pago'    => $request['id_tip_pago' . $i],
                        'fecha_pago'     => $request['fecha_pago' . $i],
                        'tipo_tarjeta'   => $request['tipo_tarjeta' . $i],
                        'numero_pago'    => $request['numero_pago' . $i],
                        'id_banco_pago'  => $request['id_banco_pago' . $i],
                        'id_cuenta_pago' => $request['id_cuenta_pago' . $i],
                        'giradoa'        => $request['giradoa' . $i],
                        'valor'          => $request['valor' . $i],
                        'valor_base'     => $request['valor_base' . $i],
                    ];
                    array_push($arr_p, $arr_pagos);
                }
            }
            //agregar comprobantes de ingreso
            $erf = "";
            if ($validates == true) {
            } else {
                $erf = $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            }
            if ($request['id_venta'] != "") {
                $nuevo_copago = [
                    'copago' => '0',
                ];
                Ct_ventas::where('id', $request['id_venta'])->update($nuevo_copago);
            }
            if ($llevaOrden) {
                $orden_id     = $this->ordenVenta($request['id_venta'], $request);
                $id_ct_ventas = [
                    'id_ct_venta' => $id_venta,
                ];
                Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
            } else {
                $orden_id = 0;
            }
            $data['id']     = $id_venta;
            $data['tipo']   = 'VEN-FA';
            //2022-09-08
            $msj            = Ct_Kardex::generar_kardex($data);
            $num_sec_vent   = Ct_ventas::find($id_venta);
            $num_vent       = $num_sec_vent->numero;
            $asiento_kardex = Contable::kardex_asiento($id_venta);
            $getSri         = "No";
            //dd("hola");
            if ($request['electronica'] == '1') {
                if ($empresa->electronica == 1) {
                    $getSri = $this->getSRI($id_venta);
                    if ($getSri != 'ok') {
                        DB::rollBack();
                        $arrayRespuesta = [
                            'error' => 'errorSri',
                            'getSri' => $getSri,
                        ];
                        return json_encode($arrayRespuesta);
                    }
                }
            }
            $num_sec_vent = Ct_ventas::find($id_venta);
            $num_vent = $num_sec_vent->numero;
            $inventario = null;
            if ($id_empresa == "0993069299001") {
                //dd("Hola");
                $info = VentasController::darBajaProducto($request, $id_venta);
                //dd($info);
            }
            DB::commit();
            $inventario = Inventario::build_process('V', $id_venta, $id_empresa);
            return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'orden_id' => $orden_id, 'arr_p' => $arr_p, 'erf' => $erf, 'kardex' => $msj, 'getSri' => $getSri, 'num_vent' => $num_vent, 'error' => 'no', "respuesta" => "si", 'mensaje' => "exito", 'inventario' => $inventario];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['error' => $e->getMessage(), 'controlador' => $this->controlador, 'line' => $e->getLine()];
        }
    }
    public static function darBajaProducto($request, $id_venta)
    {
        $id_empresa = Session::get('id_empresa');
        $mensaje = [];
        $respuesta = [];
        $band = false;
        try {
            for ($i = 0; $i < count($request["codigo"]); $i++) {

                $total_cantidad = 0;
                $ct_productos = Ct_productos::where("codigo", $request["codigo"][$i])->where('id_empresa', $id_empresa)->first();

                if (!is_null($ct_productos)) {
                    $ct_insumos_producto = Ct_productos_insumos::where('id_producto', $ct_productos->id)->first();

                    if (!is_null($ct_insumos_producto)) {

                        $inv_serie = InvInventarioSerie::where('existencia', '>', 0)->where('id_producto', $ct_insumos_producto->id_insumo)->groupBy('id_producto', 'id_bodega', 'serie')
                            ->select(DB::raw('SUM(existencia) as existencia'), 'id_bodega', 'serie')->get();

                        $cantidad = 0;
                        // dd($ct_insumos_producto->id_insumo,$inv_serie);
                        foreach ($inv_serie as $serie) {
                            if ($serie->existencia > 0) {
                                $cantidad += $serie->existencia;
                            }
                        }
                        $total_cantidad = $request["cantidad"][$i];

                        if ($cantidad >= $total_cantidad) {
                            $inv_serie2 = InvInventarioSerie::where('existencia', '>', 0)->where('id_producto', $ct_insumos_producto->id_insumo)->select('existencia', 'id_bodega', 'serie')->get();



                            foreach ($inv_serie2 as $serie2) {
                                //dd($total_cantidad, $serie2->existencia);
                                $value_valor = 0;
                                if ($total_cantidad >= $serie2->existencia) {
                                    $value_valor = $serie2->existencia;
                                    $total_cantidad -= $serie2->existencia;
                                } else {
                                    $value_valor = $total_cantidad;
                                    $total_cantidad = 0;
                                }

                                //dd("queda: ", $total_cantidad);
                                //$total_cantidad -= $serie2->existencia;
                                if ($serie2->existencia > 0) {
                                    $newRequest = new Request([
                                        "serie" => "{$serie2->serie}",
                                        "id_bodega" => $serie2->id_bodega,
                                        "cantidad_baja" => $value_valor,
                                        "observacion" => "Egreso por FACT-V: {$id_venta}"
                                    ]);

                                    $dataKarex = VentasController::vt_dar_baja_producto($newRequest);
                                    // dd($dataKarex);
                                    array_push($mensaje, $dataKarex);
                                    if ($total_cantidad == 0) {
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        $band = false;
                        $respuesta = ["status" => "error", "msj" => "No se encontro ligue", "exp" => $mensaje];
                    }
                } else {
                    $band = false;
                    $respuesta = ["status" => "error", "msj" => "No se encontro el producto", "exp" => $mensaje];
                }
            }
            return $respuesta;
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => "ocurrido un error...",  "exp" => $e->getMessage()];
        }
    }
    public static function vt_dar_baja_producto(Request $request)
    {

        //dd($request);
        $serie         = $request->serie;
        $id_bodega     = $request->id_bodega;
        $cantidad_baja = $request->cantidad_baja;
        $observacion   = $request->observacion;

        $fecha_actual = date('Y-m-d H:i:s');

        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try {

            $id_empresa = Session::get('id_empresa');
            $empresa    = Empresa::find($id_empresa);

            if (is_null($empresa)) {
                return ['estado' => 'Error', 'mensaje' => 'No existe empresa'];
            }

            //$movimiento = Movimiento::where( 'serie', $serie )->where( 'id_bodega', $id_bodega )->first();
            $movimiento = Movimiento::where('serie', $serie)->first();
            //EDUARDO INDICA QUE NO DEBE TENER BODEGA

            if (!is_null($movimiento)) {

                $producto = $movimiento->producto;
                $cantidad = $movimiento->cantidad;

                if ($cantidad_baja > $cantidad) {
                    //return [ 'estado' => 'Error', 'mensaje' => 'Cantidad supera a la que existe' ];     
                }

                $cantidad -= $cantidad_baja;


                $tipo_documento = '6';
                $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EGR')->first();
                if (is_null($inv_documento_bod)) {
                    return ['estado' => 'Error', 'mensaje' => 'No existe Documento'];
                }
                $tipo_documento = $inv_documento_bod->id; //dd($tipo_documento);

                $secuencia = InvDocumentosBodegas::getSecuecia($tipo_documento, $id_bodega);

                if ($secuencia != 0) {

                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $tipo_documento)
                        ->where('id_bodega', $id_bodega)
                        ->first();

                    $inventario = InvInventario::where('id_producto', $producto->id)
                        ->where('id_bodega', $id_bodega)
                        //->where('tipo', $tipo)
                        ->where('estado', 1)
                        ->where('id_empresa', $id_empresa)
                        ->first();

                    $iserie = InvInventarioSerie::where('serie', $serie)
                        ->where('id_bodega', $id_bodega)
                        //->where('tipo', $tipo)
                        ->where('estado', 1)
                        ->where('id_empresa', $id_empresa)
                        ->first();

                    if (is_null($inventario)) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe Inventario'];
                    }

                    $existencia = $inventario->existencia;
                    if ($cantidad_baja > $existencia) {
                        return ['estado' => 'Error', 'mensaje' => 'Cantidad supera a la existencia en Inventario'];
                    }

                    if (is_null($iserie)) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe Inventario Serie'];
                    }

                    $existencia2 = $iserie->existencia;
                    if ($cantidad_baja > $existencia2) {
                        return ['estado' => 'Error', 'mensaje' => 'Cantidad supera a la existencia en Inventario por Serie'];
                    }

                    $primer_movimiento = InvDetMovimientos::where('serie', $serie)->where('estado', '1')->OrderBy('created_at', 'asc')->first();
                    if (is_null($primer_movimiento)) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe primer movimiento'];
                    }

                    $cabecera_1mov = $primer_movimiento->cabecera;

                    if ($cabecera_1mov->id_pedido == null) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe pedido'];
                    }

                    if ($cabecera_1mov->id_documento_bodega == null) {
                        return ['estado' => 'Error', 'mensaje' => 'No existe documento del pedido'];
                    }

                    $pedido_1mov = $cabecera_1mov->pedido;

                    $documento_1mov = $cabecera_1mov->documento_bodega;

                    $tipo_1mov = $documento_1mov->tipo;

                    $estado_contable = $pedido_1mov->estado_contable;

                    $genera_asiento = false;
                    if ($tipo_1mov == 'C') {
                        $genera_asiento = true;
                    }
                    if ($tipo_1mov == 'F') {
                        if ($estado_contable == '1') {
                            $genera_asiento = true;
                        }
                    }

                    if ($genera_asiento) {
                        $plan_cuentas_debe  = null;
                        $plan_cuentas_haber = null;
                        if ($tipo_1mov == 'C') { //CONCESION
                            $plan_cuentas_debe  = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_PROVISIONES_CUENTAS_POR_PAGAR');
                            // 2.01.01.01.02 
                            $plan_cuentas_haber = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_MERCADERIA_EN_CONSIGNACION');
                            //1.01.03.01.04 

                        }
                        if ($tipo_1mov == 'F') { //FACTURA
                            $plan_cuentas_debe  = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_PERDIDA_POR_FALTANTE_INVENTARIO');
                            // 5.2.01.06.02 
                            $plan_cuentas_haber = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DAR_BAJA_PRODUCTO_INVENTARIOS_PROD_ALMACEN_COMPRADO_TERCEROS');
                            // 1.01.03.01.01 
                        }

                        if (is_null($plan_cuentas_debe)) {
                            return ['estado' => 'Error', 'mensaje' => 'No esta configurada la cuenta para el Debe'];
                        }
                        if (is_null($plan_cuentas_haber)) {
                            return ['estado' => 'Error', 'mensaje' => 'No esta configurada la cuenta para el Haber'];
                        }
                    }


                    if (!is_null($transaccion)) {

                        $descuento      = 0;
                        $mov_unitario   = $inventario->costo_promedio;
                        $mov_subtotal   = $cantidad_baja * $mov_unitario;
                        $mov_subtotal_0 = $mov_subtotal;

                        $movimiento->update([
                            'cantidad' => $cantidad,
                        ]);

                        $id_asiento_cabecera = null;

                        if ($mov_subtotal > 0) {

                            if ($genera_asiento) {

                                $input_cabecera = [
                                    'fecha_asiento'   => $fecha_actual,
                                    'id_empresa'      => $id_empresa,
                                    'observacion'     => 'DAR DE BAJA PRODUCTO: ' . $observacion,
                                    'valor'           => $mov_subtotal,
                                    'id_usuariocrea'  => $idusuario,
                                    'id_usuariomod'   => $idusuario,
                                    'ip_creacion'     => $ip_cliente,
                                    'ip_modificacion' => $ip_cliente,
                                ];

                                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera' => $id_asiento_cabecera,
                                    'id_plan_cuenta'      => $plan_cuentas_debe->cuenta_guardar,
                                    'descripcion'         => $plan_cuentas_debe->nombre_mostrar,
                                    'fecha'               => $fecha_actual,
                                    'debe'                => $mov_subtotal,
                                    'haber'               => 0,
                                    'id_usuariocrea'      => $idusuario,
                                    'id_usuariomod'       => $idusuario,
                                    'ip_creacion'         => $ip_cliente,
                                    'ip_modificacion'     => $ip_cliente,

                                ]);

                                Ct_Asientos_Detalle::create([

                                    'id_asiento_cabecera' => $id_asiento_cabecera,
                                    'id_plan_cuenta'      => $plan_cuentas_haber->cuenta_guardar,
                                    'descripcion'         => $plan_cuentas_haber->nombre_mostrar,
                                    'fecha'               => $fecha_actual,
                                    'debe'                => 0,
                                    'haber'               => $mov_subtotal,
                                    'id_usuariocrea'      => $idusuario,
                                    'id_usuariomod'       => $idusuario,
                                    'ip_creacion'         => $ip_cliente,
                                    'ip_modificacion'     => $ip_cliente,

                                ]);
                            }
                        }
                        //CABECERA MOVIMIENTO
                        $a_cab_mov = [
                            'id_documento_bodega'       => $tipo_documento,
                            'id_transaccion_bodega'     => $transaccion->id,
                            'id_bodega_origen'          => $id_bodega, //'en caso de traslado',
                            'id_bodega_destino'         => $id_bodega, //'en caso de traslado',
                            'numero_documento'          => str_pad($secuencia, 9, "0", STR_PAD_LEFT), //'Numero del documento interno',
                            //'num_doc_ext'  //'Numero de la guia, Numero proforma',
                            //'num_doc_cont'  //'Numero de la factura',
                            'observacion'               => $observacion,
                            'fecha'                     => date('Y-m-d'),
                            'descuento'                 => $descuento,
                            'subtotal'                  => $mov_subtotal,
                            'subtotal_0'                => $mov_subtotal_0,
                            'iva'                       => 0,
                            'total'                     => $mov_subtotal_0,
                            'estado'                    => 1,
                            //'id_movimiento_estado' ,
                            //'id_pedido'  //'hace referencia a la tabla de pedido',
                            //'id_agenda'  //'hace referencia a la agenda',
                            //'id_docum_origen'  //'hace referencia a la misma tabla',
                            //'id_hc_procedimientos' ,
                            'id_asiento'                => $id_asiento_cabecera,
                            'id_empresa'                => $id_empresa,

                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,

                        ];

                        $id_cabecera = InvCabMovimientos::insertGetId($a_cab_mov);

                        //DETALLE MOVIMIENTO
                        $arr_detalle = [
                            'id_inv_cab_movimientos'      => $id_cabecera,
                            'id_producto'                 => $producto->id,
                            'serie'                       => $serie,
                            //'fecha_vence'                 =>,
                            //'lote'                        =>,
                            'id_inv_inventario'           => $inventario->id,
                            'cantidad'                    => $cantidad_baja,
                            'cant_uso'                    => $cantidad_baja,
                            'valor_unitario'              => $mov_unitario,
                            'subtotal'                    => $mov_subtotal,
                            'descuento'                   => $descuento,
                            'iva'                         => 0,
                            'total'                       => $mov_subtotal,
                            'estado'                      => 1,
                            'kardex'                      => 1, //'1: si ya realizo el movimiento en inventario, inv serie y kardex; 0: No',
                            'motivo'                      => $observacion,
                            //'id_detalle_origen'           => ,
                            //.'id_pedido'                   => ,
                            'id_detalle_pedido'           => $movimiento->id,
                            //'id_procedimiento'            => ,
                            //'id_movimiento_paciente'      => ,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $inv_det_mov = InvDetMovimientos::insertGetId($arr_detalle);

                        $existencia = $inventario->existencia;

                        $existencia -= $cantidad_baja;
                        //$existencia --;
                        $existencia_uso = $inventario->existencia_uso;
                        //$existencia_uso --;
                        $existencia_uso -= $cantidad_baja;

                        //INVENTARIO
                        $inventario->update([
                            'existencia'      => $existencia,
                            'existencia_uso'  => $existencia_uso,
                        ]);

                        $existencia2 = $iserie->existencia;
                        //$existencia2 --;
                        $existencia2 -= $cantidad_baja;
                        $existencia_uso2 = $iserie->existencia_uso;
                        //$existencia_uso2 --;
                        $existencia_uso2 -= $cantidad_baja;

                        //INVENTARIO SERIE
                        $iserie->update([
                            'existencia'      => $existencia2,
                            'existencia_uso'  => $existencia_uso2,
                        ]);

                        $arr_kardex = [
                            'id_inv_inventario'         => $inventario->id,
                            'id_bodega'                 => $id_bodega,
                            'id_producto'               => $producto->id,
                            'tipo'                      => 'E',
                            'descripcion'               => $observacion,
                            'referencia'                => 'DAR DE BAJA PRODUCTO ' . $producto->id . '-' . $producto->nombre, //'referencia contable',
                            'fecha'                     => date('Y-m-d'),
                            'cantidad'                  => $cantidad_baja,
                            'cant_uso'                  => $cantidad_baja,
                            'valor_unitario'            => $mov_unitario,
                            'iva'                       => 0,
                            'total'                     => $mov_subtotal,
                            'exist_cant'                => $existencia,
                            'exist_uso'                 => $existencia_uso,
                            //'exist_valor_unitario' ,
                            //'exist_total' ,
                            'estado'                    => 1,
                            'id_documento_bodega'       => $tipo_documento,
                            'id_inv_det_movimientos'    => $inv_det_mov,
                            'id_empresa'                => $id_empresa,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'dar_baja'        => 1,

                        ];

                        InvKardex::create($arr_kardex);
                        // DB::commit();
                        return ['estado' => 'Ok', 'mensaje' => 'Dado de Baja'];
                    }
                }

                return ['estado' => 'Error', 'mensaje' => 'No existe documento bodega'];
            } else {

                return ['estado' => 'Error', 'mensaje' => 'No existe Movimiento'];
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            // DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }

        return ['estado' => 'Error', 'mensaje' => 'No existe producto por bodega'];
    }
    public function update($id, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $fecha_as   = date('Y-m-d', strtotime($request['fecha_asiento']));
        //dd($fecha_as);
        $ventas = Ct_ventas::find($id);
        if ($ventas->electronica != 1) {
            $asiento_cabecera = Ct_Asientos_Cabecera::find($ventas->id_asiento);
            $input_cabecera   = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $ventas->nro_comprobante,
                'id_empresa'      => $id_empresa,
                'observacion'     => $request['concepto'],
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            //dd($request->all());
            $id_asiento_cabecera = $asiento_cabecera->update($input_cabecera);
            foreach ($asiento_cabecera->detalles as $value) {
                $detalis                = Ct_Asientos_Detalle::find($value->id);
                $detalis->fecha         = $fecha_as;
                $detalis->id_usuariomod = $idusuario;
                $detalis->save();
            }
            $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal      = $cod_sucurs->codigo_sucursal;
            $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja          = $cod_caj->codigo_caja;
            $num_comprobante = $c_caja . '-' . $c_sucursal . '-' . $request['numero'];
            $id_paciente     = $request['identificacion_paciente'];
            $patient         = Paciente::find($id_paciente);
            $pc              = "9999999999";
            if (is_null($patient)) {
                $pc = "9999999999";
            } else {
                $pc = $patient->id;
            }
            $factura_venta = [
                'numero'              => $request['numero'],
                'nro_comprobante'     => $num_comprobante,
                'fecha'               => $fecha_as,
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $pc,
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                //'id_nivel'            => $request['id_nivel'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_procedimiento'],
                'concepto'            => $request['concepto'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'ci_vendedor'         => $request['cedula_vendedor'],
                'vendedor'            => $request['vendedor'],
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
            ];
            $ventas->update($factura_venta);
        }
        return redirect()->back()->withErrors(['msg', 'The Message']);
    }
    public function getSRI($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden           = Ct_ventas::find($id);
        $data['empresa'] = $orden->id_empresa; // DR CARLOS ROBLES
        //$data['empresa']      = '0992704152001';   //gastroclinica
        $getType = $orden->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $cliente['cedula']   = $orden->ruc_id_cliente;
        $cliente['tipo']     = $getType; //eduardo dice q el lo calcula
        $cliente['nombre']   = $orden->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $orden->cliente->nombre);
        if (count($explode) >= 4) {
            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1];
        }
        //dd($cliente);
        $cliente['email']     = $orden->email_cliente;
        $cliente['telefono']  = $orden->telefono_cliente;
        $direccion['calle']   = $orden->direccion_cliente;
        $direccion['ciudad']  = $orden->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        //dd($direccion);
        $msn_error  = '';
        $flag_error = false;
        if ($cliente['cedula'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en cedula';
        }
        if ($cliente['nombre'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Nombre';
        }
        if ($cliente['email'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en email';
        }
        if ($cliente['telefono'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en telefono';
        }
        if ($direccion['calle'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en calle';
        }
        if ($direccion['ciudad'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Ciudad';
        }
        $cant          = 0;
        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
        $setProducto   = "EXAMEN LABS";
        //dd($venta_detalle);
        foreach ($venta_detalle as $value) {
            $detalle               = trim(preg_replace("/\s+/", " ", $value->detalle));
            $producto['sku']       = $value->id_ct_productos; //ID EXAMEN
            $producto['nombre']    = $value->nombre . ' ' . $detalle; // NOMBRE DEL EXAMEN
            $producto['cantidad']  = $value->cantidad;
            $producto['precio']    = $value->precio; //DETALLE
            $pricetot              = $value->cantidad * $value->precio;
            $producto['descuento'] = $value->descuento;
            $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
            $tax                   = "0";
            if ($value->check_iva == 1) {
                $tax = ($pricetot - $value->descuento) * $value->porcentaje;
            }
            $producto['tax']    = $tax;
            $producto['total']  = $pricetot - $value->valor_descuento; //SUBTOTAL
            $producto['copago'] = "0";
            $productos[$cant]   = $producto;
            $cant++;
        }
        $data['productos'] = $productos;
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */
        /*
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;
         */
        if ($orden->id_paciente != null) {
            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
            $info[0]                  = $info_adicional;
        }
        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $orden->email_cliente; //EMAIL
        $info[2]                  = $info_adicional;
        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $orden->cliente->ciudad_representante; //EMAIL
        $info[3]                  = $info_adicional;
        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $orden->direccion_cliente; //EMAIL
        $info[4]                  = $info_adicional;
        /*
        $info_adicional['nombre']      = "ORDEN";
        $info_adicional['valor']       = ''.$orden->id.'';//EMAIL
        $info[5]                       = $info_adicional;
         */
        if ($orden->seguro != null) {
            $info_adicional['nombre'] = "SEGURO";
            $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
            $info[5]                  = $info_adicional;
        }
        $sform = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $id)->get();
        $forma_pago = array();
        if (count($sform) > 1) {
            $sformid                  = $sform[0]->id_comprobante;
            $forma_pago               = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $texto                    = '';
            foreach ($forma_pago as $fp) {
                $total = $fp->total;
                $total = round($total, 2);
                $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
            }
            $info_adicional['valor'] = $texto;
            $info[6]                 = $info_adicional;
        } else {
            //dd("hi");
            if ($sform == '[]') {
                $pago['forma_pago']       = '20';
                $texto                    = "OTROS UTILIZANDO EL SISTEMA FINANCIERO";
                $info_adicional['nombre'] = "FORMA_PAGO";
                $info_adicional['valor']  = $texto;
                $info[6]                  = $info_adicional;
            } else {
                $sformid = $sform[0]->id_comprobante;
                $forma_pago = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
                //$forma_pago = $orden->detalle_forma_pago->first();
                //dd($forma_pago);
                $tipo = $forma_pago[0]->id_tipo;
                if ($tipo == '1') {
                    $pago['forma_pago'] = '01';
                } elseif ($tipo == '2') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '3') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '4') {
                    $pago['forma_pago'] = '19';
                } elseif ($tipo == '5') {
                    $pago['forma_pago'] = '20';
                } else {
                    $pago['forma_pago'] = '16';
                }
            }
        }
        //NOTA PEDIDA POR MIKELIN
        if (trim($orden->nota_electronica) != '') {
            $info_adicional['nombre'] = "NOTA";
            $info_adicional['valor']  = $orden->nota_electronica; //EMAIL
            $info[7]                  = $info_adicional;
        }
        $pago['informacion_adicional'] = $info;
        //  $pago['dias_plazo']            = '30';
        // if(Auth::user()->id == "0957258056"){
        $pago['dias_plazo']            = "{$orden->dias_plazo}";
        // }
        $data['pago']                  = $pago;
        $data['contable']              = 0; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 0;
        if ($orden->id_paciente != null) {
            $data['paciente'] = $orden->id_paciente;
        }
        $data['concepto']      = 'Factura Electronica -' . $orden->concepto;
        $data['copago']        = 0;
        $data['id_seguro']     = '0'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura'] = $orden->total_final;
        //dd($data);
        $fp_cant = 0;
        //dd($forma_pago);
        if (count($forma_pago) > 0) {
            foreach ($forma_pago as $fp) {
                $tipos_pago['id_tipo']            = $fp->id_tipo; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = substr($fp->fecha, 0, 10);
                $tipos_pago['tipo_tarjeta']       = $fp->id_tipo_tarjeta; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                $tipos_pago['id_banco']           = $fp->id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
                $tipos_pago['giradoa']            = $fp->girador; //si es efectivo no se envia
                $tipos_pago['valor']              = $fp->total; //valor a pagar de total
                $tipos_pago['valor_base']         = $fp->total; //valor a pagar de base
                $pagos[$fp_cant] = $tipos_pago;
                $fp_cant++;
            }
        } else {
            $tipos_pago['id_tipo']            = "7"; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha, 0, 10);
            $tipos_pago['tipo_tarjeta']       = ""; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = ""; //si es efectivo no se envia
            $tipos_pago['id_banco']           = ""; //si es efectivo no se envia
            $tipos_pago['cuenta']             = ""; //si es efectivo no se envia
            $tipos_pago['giradoa']            = ""; //si es efectivo no se envia
            $tipos_pago['valor']              = "0"; //valor a pagar de total
            $tipos_pago['valor_base']         = "0"; //valor a pagar de base
            $pagos[0]                         = $tipos_pago;
        }
        //dd($forma_pago);
        //dd($pagos);
        $data['formas_pago'] = $pagos;
        if (Auth::user()->id == "0957258056") {
            dd($data);
        }
        if ($orden->fecha_envio != null) {
            $flag_error = true;
            $msn_error  = 'Ya enviado al SRI';
        }
        if ($flag_error) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CARLOS ROBLES",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }
        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        try {
            //$envio = ApiFacturacionController::envio_factura($data);
            $data['idVenta'] = $id;
            $envio = new EmisionDocumentosController;
            $envio = $envio->getFactura($data);
            if (!isset($envio->nro_comprobante)) {
                $data = json_decode($envio);
                $mensajes = json_decode($data->data);
                DB::rollBack();
                return $mensajes->mensajes;
            } else {
                /*DB::rollBack();
                return json_encode(['error' => $envio, 'controller' => $this->controlador]);*/
            }
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage(), 'line' => $e->getLine(), 'controller' => $this->controlador]);
        }
        //dd($envio);
        $orden->update([
            'nro_comprobante' => $envio->nro_comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);
        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "CARLOS ROBLES FAC VENTA",
            'dato_ant1'   => $orden->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);
        return "ok";
    }
    public function getSRI1($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden           = Ct_ventas::find($id);
        $data['empresa'] = $orden->id_empresa; // DR CARLOS ROBLES
        //$data['empresa']      = '0992704152001';   //gastroclinica
        $getType = $orden->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $cliente['cedula']   = $orden->ruc_id_cliente;
        $cliente['tipo']     = $getType; //eduardo dice q el lo calcula
        $cliente['nombre']   = $orden->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $orden->cliente->nombre);
        if (count($explode) >= 4) {
            $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']   = $explode[0];
            $cliente['apellido'] = $explode[1];
        }
        //dd($cliente);
        $cliente['email']     = $orden->email_cliente;
        $cliente['telefono']  = $orden->telefono_cliente;
        $direccion['calle']   = $orden->direccion_cliente;
        $direccion['ciudad']  = $orden->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        //dd($direccion);
        $msn_error  = '';
        $flag_error = false;
        if ($cliente['cedula'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en cedula';
        }
        if ($cliente['nombre'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Nombre';
        }
        if ($cliente['email'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en email';
        }
        if ($cliente['telefono'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en telefono';
        }
        if ($direccion['calle'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en calle';
        }
        if ($direccion['ciudad'] == null) {
            $flag_error = true;
            $msn_error  = 'Error en Ciudad';
        }

        $cant          = 0;
        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
        $setProducto   = "EXAMEN LABS";
        //dd($venta_detalle);

        if (count($venta_detalle) > 0) {
            if ($orden->id_empresa == '0993075000001') {
                $prices  = Ct_detalle_venta::where('id_ct_ventas', $id)->sum('precio');
                $discont = Ct_detalle_venta::where('id_ct_ventas', $id)->sum('descuento');
                if (is_null($discont)) {
                    $discont = 0;
                }
                $cants                 = Ct_detalle_venta::where('id_ct_ventas', $id)->sum('cantidad');
                $detailsp              = Ct_detalle_venta::where('id_ct_ventas', $id)->whereNotNull('detalle')->first();
                $pricetots             = $cants * $prices;
                $cantidad              = '1';
                $producto['sku']       = $setProducto;
                $producto['nombre']    = 'EXAMENES DE LABORATORIO ' . $orden->concepto; // NOMBRE DEL EXAMEN
                $producto['cantidad']  = '1';
                $producto['precio']    = $prices;
                $producto['descuento'] = $discont;
                $producto['subtotal']  = $orden->total_final; //precio-descuento
                $tax                   = "0";
                $producto['tax']       = $tax;
                $producto['total']     = $orden->total_final; //SUBTOTAL
                $producto['copago']    = "0";
                $productos[0]          = $producto;
            }
        } else {
            foreach ($venta_detalle as $value) {
                $detalle = trim(preg_replace("/\s+/", " ", $value->detalle));
                //se envian los productos
                $producto['sku']       = $value->id_ct_productos; //ID EXAMEN
                $producto['nombre']    = $value->nombre . ' ' . $detalle; // NOMBRE DEL EXAMEN
                $producto['cantidad']  = $value->cantidad;
                $producto['precio']    = $value->precio; //DETALLE
                $pricetot              = $value->cantidad * $value->precio;
                $producto['descuento'] = $value->descuento;
                $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
                $tax                   = "0";
                if ($value->check_iva == 1) {
                    $tax = ($pricetot - $value->descuento) * $value->porcentaje;
                }
                $producto['tax']    = $tax;
                $producto['total']  = $pricetot - $value->valor_descuento; //SUBTOTAL
                $producto['copago'] = "0";
                $productos[$cant]   = $producto;
                $cant++;
            }
        }

        $data['productos'] = $productos;
        if ($idusuario == '1316262193') {
            //dd($data);
        }
        //dd($data);
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */
        /*
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;
         */
        if ($orden->id_paciente != null) {
            $info_adicional['nombre'] = "PACIENTE";
            $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
            $info[0]                  = $info_adicional;
        }

        $info_adicional['nombre'] = "MAIL";
        $info_adicional['valor']  = $orden->email_cliente; //EMAIL
        $info[2]                  = $info_adicional;

        $info_adicional['nombre'] = "CIUDAD";
        $info_adicional['valor']  = $orden->cliente->ciudad_representante; //EMAIL
        $info[3]                  = $info_adicional;

        $info_adicional['nombre'] = "DIRECCION";
        $info_adicional['valor']  = $orden->direccion_cliente; //EMAIL
        $info[4]                  = $info_adicional;
        if ($orden->seguro != null) {
            $info_adicional['nombre'] = "SEGURO";
            $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
            $info[5]                  = $info_adicional;
        }
        $sform = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $id)->get();

        $forma_pago = array();
        if (count($sform) > 1) {
            $sformid                  = $sform[0]->id_comprobante;
            $forma_pago               = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
            $pago['forma_pago']       = '20';
            $info_adicional['nombre'] = "FORMA_PAGO";
            $texto                    = '';

            foreach ($forma_pago as $fp) {
                $total = $fp->total;
                $total = round($total, 2);
                $texto = $texto . ' ' . $fp->tipo_pago->nombre . ': ' . $total;
            }
            $info_adicional['valor'] = $texto;
            $info[6]                 = $info_adicional;
        } else {
            //dd("hi");
            if ($sform == '[]') {
                $pago['forma_pago']       = '20';
                $texto                    = "OTROS UTILIZANDO EL SISTEMA FINANCIERO";
                $info_adicional['nombre'] = "FORMA_PAGO";
                $info_adicional['valor']  = $texto;
                $info[6]                  = $info_adicional;
            } else {
                $sformid = $sform[0]->id_comprobante;

                $forma_pago = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $sformid)->get();
                //$forma_pago = $orden->detalle_forma_pago->first();
                //dd($forma_pago);
                $tipo = $forma_pago[0]->id_tipo;
                if ($tipo == '1') {
                    $pago['forma_pago'] = '01';
                } elseif ($tipo == '2') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '3') {
                    $pago['forma_pago'] = '20';
                } elseif ($tipo == '4') {
                    $pago['forma_pago'] = '19';
                } elseif ($tipo == '5') {
                    $pago['forma_pago'] = '20';
                } else {
                    $pago['forma_pago'] = '16';
                }
            }
        }
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;
        $data['contable']              = 0; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 0;
        if ($orden->id_paciente != null) {
            $data['paciente'] = $orden->id_paciente;
        }
        $data['concepto']      = 'Factura Electronica -' . $orden->concepto;
        $data['copago']        = 0;
        $data['id_seguro']     = '0'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura'] = $orden->total_final;
        //dd($data);
        $fp_cant = 0;
        //dd($forma_pago);
        if (count($forma_pago) > 0) {
            foreach ($forma_pago as $fp) {
                $tipos_pago['id_tipo']            = $fp->id_tipo; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = substr($fp->fecha, 0, 10);
                $tipos_pago['tipo_tarjeta']       = $fp->id_tipo_tarjeta; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $fp->numero; //si es efectivo no se envia
                $tipos_pago['id_banco']           = $fp->id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']             = $fp->cuenta; //si es efectivo no se envia
                $tipos_pago['giradoa']            = $fp->girador; //si es efectivo no se envia
                $tipos_pago['valor']              = $fp->total; //valor a pagar de total
                $tipos_pago['valor_base']         = $fp->total; //valor a pagar de base

                $pagos[$fp_cant] = $tipos_pago;
                $fp_cant++;
            }
        } else {
            $tipos_pago['id_tipo']            = "7"; //metodo de pago efectivo, tarjeta, etc
            $tipos_pago['fecha']              = substr($orden->fecha, 0, 10);
            $tipos_pago['tipo_tarjeta']       = ""; //si es efectivo no se envia
            $tipos_pago['numero_transaccion'] = ""; //si es efectivo no se envia
            $tipos_pago['id_banco']           = ""; //si es efectivo no se envia
            $tipos_pago['cuenta']             = ""; //si es efectivo no se envia
            $tipos_pago['giradoa']            = ""; //si es efectivo no se envia
            $tipos_pago['valor']              = "0"; //valor a pagar de total
            $tipos_pago['valor_base']         = "0"; //valor a pagar de base
            $pagos[0]                         = $tipos_pago;
        }

        //dd($forma_pago);
        //dd($pagos);
        $data['formas_pago'] = $pagos;

        if ($orden->fecha_envio != null) {
            $flag_error = true;
            $msn_error  = 'Ya enviado al SRI';
        }

        if ($flag_error) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CARLOS ROBLES",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }

        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
        /*try {
            //$envio = ApiFacturacionController::envio_factura($data);
            //2022-09-08
            $envio = EmisionDocumentosController::getFactura($data);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage(), 'line' => $e->getLine(), 'controller' => $this->controlador]);
        }

        //dd($envio);

        $orden->update([
            'nro_comprobante' => $envio->comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);

        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "CARLOS ROBLES FAC VENTA",
            'dato_ant1'   => $orden->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);*/

        return "ok";
    }
    public function store_ordenes(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');

        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'estado_pago'         => '0',
            'id_paciente'         => $request['identificacion_paciente'],
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],

            'copago'              => $request['total1'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            //'nota'                          => $request['nota'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            //'subtotal'                      => $request['subtotal1'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            // 'transporte'                    => $request['transporte'],
            'total_final'         => $request['totalc'],
            'valor_contable'      => $request['total1'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];

        // return $factura_venta;

        $id_venta = Ct_ven_orden::insertGetId($factura_venta);
        //$id_venta = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;

        for ($i = 0; $i < count($request->input("nombre")); $i++) {
            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre'     => $request->input("nombre")[$i],
                    'cantidad'   => $request->input("cantidad")[$i],
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
        foreach ($arr_total as $valor) {
            $detalle = [
                'id_ct_ven_orden'      => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['copago'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['precioneto'],
                'check_iva'            => $valor['iva'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_ven_orden_detalle::create($detalle);
        }

        return ['id' => $id_venta];
    }
    public function valida_numero_factura($idempresa, $sucursal, $punto_emision, $numero)
    {
        $numeroconcadenado    = $sucursal . '-' . $punto_emision . '-' . $numero;
        $verifica_num_factura = null;
        $validate             = true;
        $nfactura             = '';

        // do{
        $verifica_num_factura = Ct_ventas::where('id_empresa', $idempresa)
            ->where('nro_comprobante', $numeroconcadenado)
            ->where('tipo', '<>', 'VEN-FACT')
            ->where('estado', '!=', '0')->first();
        // if(!is_null($verifica_num_factura)){
        //  //   $nfactura        = $this->obtener_numero_factura($idempresa,$sucursal, $numero);
        //  //   $numeroconcadenado = $sucursal . '-' . $numero . '-' . $nfactura;
        //     //echo $numero;
        //     $numero ++;
        //     $nfactura = str_pad($numero, 10, "0", STR_PAD_LEFT);
        //     $numeroconcadenado = "{$sucursal}-{$punto_emision}-{$nfactura}";
        // }else{
        //     $validate = false;
        //     //dd ("sii entra");
        //     break;
        // }
        //}while(true);
        if (is_null($verifica_num_factura)) {
            //   dd("Numero");
            return $numero;
        } else {
            //  dd("existe");
            return "existe";
        }
        // dd("si {$numeroconcadenado}");

    }
    public function ordenVenta($id_v, Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = Date('Y-m-d H:i:s');
        $id_empresa = $request->session()->get('id_empresa');

        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'estado_pago'         => '0',
            'id_paciente'         => $request['identificacion_paciente'],
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],
            'copago'              => $request['total1'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            'total_final'         => $request['totalc'],
            'valor_contable'      => $request['total1'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'id_ct_venta'         => $id_v,
        ];
        $id_venta  = Ct_ven_orden::insertGetId($factura_venta);
        $arr_total = [];
        for ($i = 0; $i < count($request->input("nombre")); $i++) {
            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre'     => $request->input("nombre")[$i],
                    'cantidad'   => $request->input("cantidad")[$i],
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
        foreach ($arr_total as $valor) {
            $detalle = [
                'id_ct_ven_orden'      => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['precioneto'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['copago'],
                'check_iva'            => $valor['iva'],
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_ven_orden_detalle::create($detalle);
        }

        return ['id' => $id_venta];
    }
    public function updateorden(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = Date('Y-m-d H:i:s');
        $id_empresa = $request->session()->get('id_empresa');

        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'            => $c_sucursal,
            'punto_emision'       => $c_caja,
            'numero'              => $nfactura,
            'nro_comprobante'     => $num_comprobante,
            'id_asiento'          => $id_asiento_cabecera,
            'id_empresa'          => $id_empresa,
            'tipo'                => $request['tipo'],
            'fecha'               => $request['fecha_asiento'],
            'divisas'             => $request['divisas'],
            'nombre_cliente'      => $request['nombre_cliente'],
            'tipo_consulta'       => $request['tipo_consulta'],
            'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
            'direccion_cliente'   => $request['direccion_cliente'],
            'ruc_id_cliente'      => $request['identificacion_cliente'],
            'telefono_cliente'    => $request['telefono_cliente'],
            'email_cliente'       => $request['mail_cliente'],
            'orden_venta'         => $request['orden_venta'],
            'nro_autorizacion'    => $request['numero_autorizacion'],
            'id_paciente'         => $request['identificacion_paciente'],
            'nombres_paciente'    => $request['nombre_paciente'],
            'id_hc_procedimiento' => $request['mov_paciente'],
            'seguro_paciente'     => $request['id_seguro'],
            'procedimientos'      => $request['procedimiento'],
            'fecha_procedimiento' => $request['fecha_proced'],

            'copago'              => $request['totalc'],
            'id_recaudador'       => $request['cedula_recaudador'],
            'ci_vendedor'         => $request['cedula_vendedor'],
            'vendedor'            => $request['vendedor'],
            //'nota'                          => $request['nota'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            //'subtotal'                      => $request['subtotal1'],
            'descuento'           => $request['descuento1'],
            'base_imponible'      => $request['subtotal_121'],
            'impuesto'            => $request['tarifa_iva1'],
            // 'transporte'                    => $request['transporte'],
            'total_final'         => $request['total1'],
            'valor_contable'      => $request['total1'],
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,

        ];

        // return $factura_venta;

        Ct_ven_orden::where('id', $request['id'])->update($factura_venta);

        //$id_venta = Ct_ven_orden::insertGetId($factura_venta);
        $id_venta       = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;

        for ($i = 0; $i < count($request->input("nombre")); $i++) {
            if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                $arr = [
                    'nombre'     => $request->input("nombre")[$i],
                    'id_detalle' => $request->input("id_detalle")[$i],
                    'cantidad'   => $request->input("cantidad")[$i],
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
        foreach ($arr_total as $valor) {
            $detalle = [
                //'id_ct_ven_orden'                  => $id_venta,
                'id_ct_productos'      => $valor['codigo'],
                'nombre'               => $valor['nombre'],
                'cantidad'             => $valor['cantidad'],
                'precio'               => $valor['precio'],
                'descuento_porcentaje' => $valor['descpor'],
                'descuento'            => $valor['descuento'],
                'extendido'            => $valor['precioneto'],
                'detalle'              => $valor['detalle'],
                'copago'               => $valor['copago'],
                'check_iva'            => $valor['iva'],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];

            // Ct_ven_orden_detalle::create($detalle);
            Ct_ven_orden_detalle::where('id', $valor['id_detalle'])->update($detalle);
        }

        //$request['id'];
        //return ['fact' => $factura_venta, 'det' => $detalle];
        return ['id' => $request['id']];
    }
    public function index_cierre(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            //$id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $caja = $request['caja'];

        $doctor = $request['doctor'];
        $id_paciente = $request['id_paciente'];
        $usuario_paciente = null;
        $tipo = $request['tipo'];
        /*if (is_null($tipo)) {
        $tipo = '0';
        }*/

        // $facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', 'orden.id_agenda', 'agenda.id')
        //                         ->join('paciente as p', 'p.id', 'agenda.id_paciente')
        //                         ->join('users as u', 'agenda.id_usuariocrea', 'u.id')
        //                         ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
        //                         ->where('p.id_seguro', '<>', '3')
        //                         ->where('p.id_seguro', '<>', '6')
        //                         ->whereNull('orden.id');


        //$facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', 'orden.id_agenda', 'agenda.id')
        $facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', function ($join) {
            $join->on(function ($query) {
                $query->on('orden.id_agenda', 'agenda.id')
                    ->where('orden.estado', '=', '1');
            });
        })
            ->join('paciente as p', 'p.id', 'agenda.id_paciente')
            ->join('users as u', 'agenda.id_usuariomod', 'u.id')
            ->leftjoin('apps_agenda as app', 'app.id_agenda', 'agenda.id')
            ->whereNull('app.id')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->where('s.tipo', '<>', '0')->where('agenda.proc_consul', '<', '2')
            ->whereRaw('(agenda.omni = "%NO%" OR agenda.omni IS NULL)')
            ->where('agenda.estado', '<>', '0')
            ->whereNotNull('agenda.id_doctor1')
            ->whereNull('orden.id')
            ->where('agenda.estado_cita', '4')
            ->where('agenda.id_doctor1', '<>', '4444444444');


        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            //->where('ct_orden_venta.id_empresa', $id_empresa)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');

        if (!is_null($id_empresa)) {
            $ordenes  = $ordenes->where('ct_orden_venta.id_empresa', $id_empresa);
        }

        if (!is_null($tipo)) {
            $ordenes             = $ordenes->where('a.proc_consul', $tipo);
            $facturas_pendientes = $facturas_pendientes->where('agenda.proc_consul', $tipo);
        }

        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);
            if ($caja == 'LABORATORIO') {
                $ordenes = $ordenes->where('ct_orden_venta.caja', $caja)->where('ct_orden_venta.id_Seguro', '<>', '1');
            }
        } else {
            $ordenes = $ordenes->where('ct_orden_venta.caja', '<>', 'LABORATORIO');
        }

        if (!is_null($doctor)) {
            $ordenes             = $ordenes->where('a.id_doctor1', $doctor);
            $facturas_pendientes = $facturas_pendientes->where('agenda.id_doctor1', $doctor);
        }

        if (!is_null($id_paciente)) {
            $ordenes  = $ordenes->where('a.id_paciente', $id_paciente);
            $usuario_paciente = Paciente::find($id_paciente);
        }
        $ordenes = $ordenes->get();

        $doctores = User::where('id_tipo_usuario', '3')->where('training', '0')->where('uso_sistema', '0')->orderby('apellido1')->get();

        $empresas            = Empresa::where('prioridad', '1')->where('estado', 1)->get();
        $facturas_pendientes = $facturas_pendientes->select('agenda.*', 'p.nombre1 as nombre1', 'p.apellido1 as apellido1', 'p.apellido2 as apellido2', 'u.nombre1 as unombre1', 'u.apellido1 as uapellido1', 'u.apellido2 as uapellido2')->get();
        return view('contable/ventas/index_cierre', ['empresas' => $empresas, 'facturas_pendientes' => $facturas_pendientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ordenes' => $ordenes, 'request' => $request, 'doctores' => $doctores, 'usuario_paciente' => $usuario_paciente]);
    }
    public function facturas_omni(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $consultar     = $request->all();
        $fecha         = $request['fecha'];
        $fechafin      = $request['fecha_hasta'];
        $id_empresa    = $request->session()->get('id_empresa'); //$request['id_empresa'];
        $empresas      = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $procedimiento = Procedimiento::all();
        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }
        $nombres        = $request['nombres'];
        $cedula         = $request['cedula'];
        $seguros        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();
        $procedimientos = [];
        if ($request->id_agenda != null) {
            $procedimientos = Agenda::whereIn('id', $request->id_agenda)->get();
        } else {
            if ($request->tipo != null) {
                $procedimientos = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', $request->tipo);
            } else {
                $procedimientos = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', '<>', 2);
            }

            $procedimientos = $procedimientos->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59']);
            $procedimientos = $procedimientos->join('historiaclinica as h', 'h.id_agenda', 'agenda.id');
            if (!is_null($request['id_seguro'])) {

                $procedimientos = $procedimientos->whereIn('h.id_seguro', $request['id_seguro']);
            }
            if (!is_null($request['procedimientos'])) {
                $procedimientos = $procedimientos->whereIn('agenda.id_procedimiento', $request['procedimientos']);
            }
            if (!is_null($request['omni'])) {
                if ($request['omni'] == "SI") {
                    $procedimientos = $procedimientos->whereRaw('(agenda.omni LIKE "%OM%" OR agenda.omni LIKE "%SI%")');
                } else {
                    $procedimientos = $procedimientos->where('agenda.omni', $request['omni']);
                }
            }
            $procedimientos = $procedimientos->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
            if (!is_null($cedula)) {
                $procedimientos->where('id_paciente', $cedula);
            }

            if ($nombres != null) {
                //dd("dada");
                $nombres2 = explode(" ", $nombres);
                $cantidad = count($nombres2);

                $nombres_sql = '';

                foreach ($nombres2 as $n) {
                    $nombres_sql = $nombres_sql . '%' . $n;
                }
                $nombres_sql = $nombres_sql . '%';

                if ($cantidad > 1) {
                    $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                        $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                    });
                } else {

                    $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                        $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                    });
                }
            }
            //dd($procedimientos);
            $procedimientos = $procedimientos->select('agenda.*', 'h.id_seguro as seguro_final')->get();
        }

        //dd($request->all());
        return view('contable/ventas/facturas_omni', ['procedimientos' => $procedimientos, 'procedimiento' => $procedimiento, 'empresas' => $empresas, 'empresa' => $empresas, 'seguros' => $seguros, 'fecha' => $fecha, 'fecha_hasta' => $fechafin, 'request' => $request]);
    }
    //crea la factura desde el recibo/reporte de caja
    public function factura_caja($id_orden, Request $request)
    {
        $fact_venta           = Ct_Orden_Venta::findorfail($id_orden);
        $request['id_agenda'] = $fact_venta->id_agenda;
        $getReload            = $this->getReloadRecibo($request);
        $ct_for_pag           = Ct_Orden_Venta_Pago::where('id_orden', $id_orden)->get();

        $vistaurl = "contable.facturacion.pdf_comprobante_tributario";
        $view     = \View::make($vistaurl, compact('fact_venta', 'ct_for_pag'))->render();

        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::where('inactivo', '1')->get();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();

        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos          = Ct_productos::where('estado_tabla', '1')->where('id_empresa', $id_empresa)->get();

        $id_plan = LogConfig::busqueda('4.1.01.02');

        //$iva                = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $iva                = Ct_Configuraciones::where('id_plan', $id_plan)->where('estado', '1')->first();
        $tipo_tarjeta       = Ct_Tipo_Tarjeta::all();
        $fact_venta_detalle = Ct_Orden_Venta_Detalle::where('id_orden', $id_orden)->get();

        $bodega_in = Ct_Bodegas::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        return view('contable/ventas/create_facturaRecibo', ['bodega_in' => $bodega_in, 'fact_venta' => $fact_venta, 'tipo_tarjeta' => $tipo_tarjeta, 'fact_venta_detalle' => $fact_venta_detalle, 'ct_for_pag' => $ct_for_pag, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas]);
    }
    public function store_varios(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        $llevaOrden = false;
        $idusuario  = Auth::user()->id;
        if ($idusuario == '1316262193') {
            //dd($request->all());
        }

        $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $numero     = $request['numero'];
        $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();

        $proced = $request['procedimiento'];

        if ($request['sucursal'] != 0) {
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;
            $proced     = $request['procedimiento'];

            //if($idusuario !="0957258056"){
            if (!is_null($numero)) {
                $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                    ->where('nro_comprobante', $num_comprobante)
                    ->where('tipo', '<>', 'VEN-FACT')
                    ->where('estado', '!=', '0')->first();
                if (!is_null($verifica_num_factura)) {
                    return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                } else {
                    $nfactura = $numero;
                }
            } else {
                $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
            }
        } else {
            $c_sucursal      = 0;
            $c_caja          = 0;
            $num_comprobante = 0;
            $nfactura        = 0;
            $proced          = $request['procedimiento'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced;
        DB::beginTransaction();
        try {

            $input_cabecera = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $arr_total2          = [];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera); //activo
            $factura_venta       = [
                'sucursal'            => $cod_sucurs->codigo_sucursal,
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => 'VENFA-CO',
                'ambulatorio'         => $request['amBu'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $request['identificacion_paciente'],
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'concepto'            => $request['concepto'],
                'seguro_paciente'     => $request['id_seguro'],
                'electronica'         => $request['electronica'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['totalc'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['total1'],
                'valor_contable'      => $request['total1'],
                'ip_creacion'         => "conglomerada",
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_venta = Ct_ventas::insertGetId($factura_venta);
            for ($i = 0; $i < count($request->codigo); $i++) {

                $upd = [
                    'estado_pago'     => '1',
                    'id_referencia'   => $id_venta,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];

                $nombre = "";
                if (isset($request->input("nombre")[$i])) {
                    $nombre = $request->input("nombre")[$i];
                }
                $codigo = "";
                if (isset($request->input("codigo")[$i])) {
                    $codigo = $request->input("codigo")[$i];
                }
                $cantidad = 0;
                if (isset($request->input("cantidad")[$i])) {
                    $cantidad = $request->input("cantidad")[$i];
                }
                $paciente = "";
                if (isset($request->input("paciente")[$i])) {
                    $paciente = $request->input("paciente")[$i];
                }
                $precio = 0;
                if (isset($request->input("precio")[$i])) {
                    $precio = $request->input("precio")[$i];
                }
                $descuentopor = 0;
                if (isset($request->input("descpor")[$i])) {
                    $descuentopor = $request->input("descpor")[$i];
                }
                $descuento = 0;
                if (isset($request->input("desc")[$i])) {
                    $descuentopor = $request->input("desc")[$i];
                }
                $copago = 0;
                if (isset($request->input("copago")[$i])) {
                    $copago = $request->input("copago")[$i];
                }
                $precio_neto = 0;
                if (isset($request->input("precioneto")[$i])) {
                    $precio_neto = $request->input("precioneto")[$i];
                }
                $detalle = "";
                $detalle = $request->input("descrip_prod")[$i];

                $iva = 0;
                if (isset($request->input("iva")[$i])) {
                    $iva = $request->input("iva")[$i];
                }
                $id_orden = 1;
                if (isset($request->input("id_orden")[$i])) {
                    $id_orden = $request->input("id_orden")[$i];
                    $ventax   = Ct_ven_orden::where('id', $request->input("id_orden")[$i])->first();
                    //dd($ventax);
                    if (!is_null($ventax)) {
                        $ventax->update($upd);
                    }
                }
                $bodega_in = "";
                if (isset($request->input("bodega")[$i])) {
                    $bodega_in = $request->input("bodega")[$i];
                }

                $arr = [
                    'nombre'      => $nombre,
                    'cantidad'    => $cantidad,
                    'codigo'      => $codigo,
                    'id_paciente' => $paciente,
                    'precio'      => $precio,
                    'id_orden'    => $id_orden,
                    'descpor'     => $descuentopor,
                    'copago'      => $copago,
                    'descuento'   => $descuento,
                    'precioneto'  => $precio_neto,
                    'detalle'     => $detalle,
                    'iva'         => $iva,
                    'id_hc_proc'  => $request->input('id_hc_proc')[$i],
                    'obs_pac'     => $text,
                    'bodega'      => $bodega_in,
                ];
                array_push($arr_total2, $arr);
            }
            foreach ($arr_total2 as $valor) {
                //dd($valor);
                $paciente = "";
                if (isset($valor['paciente'])) {
                    $paciente = $valor['paciente'];
                }
                $precio_neto = "";
                if (isset($valor['precioneto'])) {
                    $precio_neto = $valor['precioneto'];
                }
                $nombre = "";
                if (isset($valor['nombre'])) {
                    $nombre = $valor['nombre'];
                }
                $cantidad = 0;
                if (isset($valor['cantidad'])) {
                    $cantidad = $valor['cantidad'];
                }
                $codigo = "";
                if (isset($valor['codigo'])) {
                    $codigo = $valor['codigo'];
                }
                $nombre_procedimiento = "";
                if (isset($valor['nombre_procedimiento'])) {
                    $nombre_procedimiento = $valor['nombre_procedimiento'];
                }
                $fecha_procedimiento = "";
                if (isset($valor['fecha_procedimiento'])) {
                    $fecha_procedimiento = $valor['fecha_procedimiento'];
                }
                $precio = 0;
                if (isset($valor['precio'])) {
                    $precio = $valor['precio'];
                }
                $paciente = "";
                if (isset($valor['paciente'])) {
                    $paciente = $valor['paciente'];
                }
                $iva = 0;
                if (isset($valor['iva'])) {
                    $iva = $valor['iva'];
                }
                $descuento = 0;
                if (isset($valor['descuento'])) {
                    $descuento = $valor['descuento'];
                }
                $descrip_prod = "";
                if (isset($valor['detalle'])) {
                    $descrip_prod = $valor['detalle'];
                }
                $id_hc_proc = "";
                if (isset($valor['id_hc_proc'])) {
                    $id_hc_proc = $valor['id_hc_proc'];
                }
                $id_orden = 1;
                if (isset($valor['id_orden'])) {
                    $id_orden = $valor['id_orden'];
                }
                $bodega_in = "";
                if (isset($valor['bodega'])) {
                    $bodega_in = $valor['bodega'];
                }

                $precio_neto = floatval($cantidad) * floatval($precio);
                $detalle     = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => $codigo,
                    'nombre'               => $codigo,
                    'cantidad'             => $cantidad,
                    'precio'               => $precio,
                    'descuento_porcentaje' => $descuento,
                    'descuento'            => $descuento,
                    'extendido'            => $precio_neto,
                    'detalle'              => $descrip_prod,
                    'copago'               => '0',
                    'check_iva'            => $iva,
                    'codigo'               => $codigo,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'bodega'               => $bodega_in,
                ];

                Ct_detalle_venta::create($detalle); //
                $detalles = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => $codigo,
                    'nombre'               => $codigo,
                    'cantidad'             => $cantidad,
                    'precio'               => $precio,
                    'descuento_porcentaje' => $descuento,
                    'descuento'            => $descuento,
                    'extendido'            => $precio_neto,
                    'id_paciente'          => $valor['id_paciente'],
                    'detalle'              => $descrip_prod,
                    'copago'               => '0',
                    'check_iva'            => $iva,
                    'id_orden'             => $valor['id_orden'],
                    'codigo'               => $codigo,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,

                ];
                Ct_Detalle_Venta_Conglomerada::create($detalles);
            }
            //*
            //***MODULO CUENTA POR COBRAR***
            //
            //cUENTAS X COBRAR CLIENTES
            // --activo
            $val_tol      = $request['total1'];
            $data['id']   = $id_venta;
            $data['tipo'] = 'VEN-FA';
            $getSri       = "No";
            $empresa      = Empresa::find($id_empresa);
            //dd("hola");
            if ($request['electronica'] == '1') {
                if ($empresa->electronica == 1) {
                    $getSri = $this->getSRI1($id_venta);
                }
            }
            $msj = Ct_Kardex::generar_kardex($data);
            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.02');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {

                $id_plan_confg = LogConfig::busqueda('4.1.01.01');

                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            //****REGISTRO FORMAS DE PAGO*** --activo falta****
            $arr_p = [];
            for ($i = 0; $i < $request['contador_pago']; $i++) {

                $visibilidad_pa = $request['visibilidad_pago' . $i];

                if ($visibilidad_pa == 1) {
                    $arr_pagos = [
                        'id_tip_pago'    => $request['id_tip_pago' . $i],
                        'fecha_pago'     => $request['fecha_pago' . $i],
                        'tipo_tarjeta'   => $request['tipo_tarjeta' . $i],
                        'numero_pago'    => $request['numero_pago' . $i],
                        'id_banco_pago'  => $request['id_banco_pago' . $i],
                        'id_cuenta_pago' => $request['id_cuenta_pago' . $i],
                        'giradoa'        => $request['giradoa' . $i],
                        'valor'          => $request['valor' . $i],
                        'valor_base'     => $request['valor_base' . $i],
                    ];

                    array_push($arr_p, $arr_pagos);
                }
            }

            foreach ($arr_p as $valor) {
                Ct_Forma_Pago::create([
                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                    'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                    'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                    'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                    'valor'           => $valor['valor'], //$request['valor'.$i],
                    'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }

            //agregar comprobantes de ingreso
            $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            DB::commit();
            return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'kardex' => $msj, 'prd_val' => $request->input("prd_val"), 'llevaOrden' => $llevaOrden, 'mis inputs' => $request->all(), 'getSri' => $getSri, "respuesta" => "exito", 'mensaje' => "Guardado exitosamente"];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function view_omni(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $validate = $request['validate'];

        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();

        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //dd($request->all());
        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();
        $iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        /*
        if (!empty($request->input("id_agenda"))) {
        for ($i = 0; $i < count($request->input("id_agenda")); $i++) {

        $consulta_agenda =  DB::select('SELECT
        a.id AS id_agenda,
        ct.codigo AS codigo_producto,
        ct.nombre AS nombre_producto,
        h.fecha_atencion AS fecha_procedimiento,
        pc.id AS id_paciente,
        hc.id AS id_hc_procedimientos,
        pc.nombre1 AS nombre1,
        pc.nombre2 AS nombre2,
        pc.apellido1 AS apellido1,
        pc.apellido2 AS apellido2,

        pr.nombre AS nombre_completo,
        COUNT(DISTINCT(m.id)) AS cantidad_total,
        SUM(m.precio) AS precio_final
        FROM agenda AS a
        JOIN historiaclinica AS h
        ON h.id_agenda = a.id
        JOIN hc_procedimientos AS hc
        ON h.hcid = hc.id_hc
        JOIN hc_procedimiento_final as hcfinal
        ON hcfinal.id_hc_procedimientos = hc.id
        LEFT JOIN procedimiento as pr
        ON pr.id= hcfinal.id_procedimiento
        LEFT JOIN procedimiento_completo AS pcompleto
        ON pcompleto.id = hc.id_procedimiento_completo
        LEFT JOIN movimiento_paciente AS mp
        ON mp.id_hc_procedimientos = hc.id
        JOIN movimiento AS m
        ON m.id = mp.id_movimiento
        JOIN ct_productos_insumos AS proct
        ON proct.id_insumo = m.id_producto
        LEFT JOIN ct_productos AS ct
        ON ct.id = proct.id_producto
        JOIN paciente AS pc
        ON a.id_paciente = pc.id

        WHERE a.id =' . $request->input("id_agenda")[$i] . '
        GROUP BY m.id_producto  ORDER BY h.fecha_atencion DESC');
        //dd($consulta_agenda);
        //$segundo = Agenda::find($request->input("id_agenda")[$i]);
        if (count($consulta_agenda) > 0) {
        array_push($arr_todas, $consulta_agenda);
        }

        }
        }
        $group_procedures=array();
        foreach($arr_todas as $value){
        //dd($value);

        foreach($value as $x){
        //$group_store[$value->id_hc_procedimientos][] = $value;
        //dd($x);

        //$hc=hc_procedimientos::find($x->id_hc_procedimientos);
        $procedimiento_final= Hc_Procedimiento_Final::where('id_hc_procedimientos',$x->id_hc_procedimientos)->get();
        foreach($procedimiento_final as $final){
        if(!is_null($final)){
        //dd($value); $group[$x->id_paciente][] = $value;

        $array_groups=['paciente_principal'=>$x->id_paciente,'procedimiento_principal'=>$final->procedimiento->nombre."/".$x->id_paciente."/".$x->id_hc_procedimientos,'valores'=>$x];
        array_push($group_procedures,$array_groups);
        }
        }
        }
        }
        //dd($arr_todas);
        $finaly_array=array();
        $ter_A=array();
        foreach($group_procedures as $j=>$ter){
        if(!is_null($ter)){
        //dd($ter);
        //dd($ter['procedimiento_principal']);
        $ter_A[$ter['procedimiento_principal']][] = $ter;
        //array_push($finaly_array,$terA);
        }
        }
        //creo que debo partir desde paciente para agrupar los procedimientos por pacientes y dentro por procedimientos
        //dd($ter_A);
        //dd($ter_A);
        //dd($request->all());
        $group = array();
        $group2 = array();
        //group by procedures
        foreach ($arr_todas as $value) {
        foreach ($value as $x) {
        //dd($x);
        $group[$x->id_paciente][] = $value;
        array_push($pacientes, $x->id_paciente);
        }
        }
        if(count($group)>0){
        }else{
        //dd(count($request->input("paciente")));
        for ($i = 0; $i < count($request->input("paciente")); $i++) {
        if(!is_null($request->input("paciente")[$i])){
        //dd($request->input("paciente")[$i]);
        array_push($pacientes,$request->input("paciente")[$i]);
        }
        }
        }
        //dd($pacientes);
        //procedures group by
        $group_procedures=array();
        foreach($arr_todas as $z){
        foreach($z as $a){
        $group2[$a->id_hc_procedimientos][] = $z;
        array_push($group_procedures, $a->id_hc_procedimientos);
        }

        }*/
        $final = $this->getReloadOmni($request);
        //dd($final);
        //dd($pacientes);
        //dd(array_unique($group_procedures));
        //dd($request->all());
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $sec_part     = array();
        $sec_private  = array();
        $last_public  = array();
        //$array_todos = array();
        $t_factura = $request['tipo_fact'];
        //solo equipos
        if ($t_factura == 2) {
            $sec_part    = ParametersConglomerada::particulares($seguros);
            $sec_private = ParametersConglomerada::privados($seguros);
            $last_public = ParametersConglomerada::publicos($seguros);
        }

        $bodegas_hosp = Ct_Bodegas::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        //dd($bodegas);

        //dd($t_factura);
        if ($t_factura == 3) {
            return view('contable/ventas/createHonorarios', ['finalArray' => $final, 'tipo_tarjeta' => $tipo_tarjeta, 't_factura' => $t_factura, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'sec_part' => $sec_part, 'sec_private' => $sec_private, 'last_public' => $last_public, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas]);
        }
        return view('contable/ventas/create_facturaOmni', ['bodegas_hosp' => $bodegas_hosp, 'finalArray' => $final, 'tipo_tarjeta' => $tipo_tarjeta, 't_factura' => $t_factura, 'divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'sec_part' => $sec_part, 'sec_private' => $sec_private, 'last_public' => $last_public, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas]);
    }
    public function getReloadOmni(Request $request)
    {
        if (!is_null($request)) {
            $array_Pusher = array();
            $group2       = array();

            if (!empty($request->input("id_agenda"))) {
                for ($i = 0; $i < count($request->input("id_agenda")); $i++) {
                    $agenda = Agenda::find($request->input("id_agenda")[$i]);
                    //$patient= Paciente::find($request->input("paciente")[$i]);
                    $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
                        ->where('hc.id_agenda', $request->input("id_agenda")[$i])->get();
                    foreach ($procedimientos as $value) {
                        $texto = "";
                        if ($value->nombre_general == null) {
                            $adicionales = Hc_procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                            $mas         = true;
                            $texto       = "";
                            foreach ($adicionales as $value2) {
                                if ($mas == true) {
                                    $texto = $texto . $value2->procedimiento->nombre;
                                    $mas   = false;
                                } else {
                                    $texto = $texto . ' + ' . $value2->procedimiento->nombre;
                                }
                            }
                        }
                        $arr_AC = [];

                        //$productos =Movimiento_Paciente::where('id_hc_procedimientos', $value->id)->get();
                        $productos                    = DB::table('movimiento_paciente as mp')->where('mp.id_hc_procedimientos', $value->id)->join('movimiento as m', 'm.id', 'mp.id_movimiento')->join('ct_productos_insumos as pro', 'pro.id_insumo', 'm.id_producto')->join('ct_productos as productos', 'productos.id', 'pro.id_producto')->groupBy('m.id_producto')->select(DB::raw('COUNT(DISTINCT(m.id)) AS cantidad'), 'productos.codigo as codigo', 'productos.nombre as nombre')->get();
                        $arr_AC['paciente_principal'] = $request->input("paciente")[$i];
                        $arr_AC['nombre_principal']   = $texto;
                        $arr_AC['hc_procedimiento']   = $value->id;
                        $arr_AC['fecha']              = $agenda->fechaini;
                        $arr_AC['agenda']             = $agenda->id;
                        $productosAC                  = [];
                        foreach ($productos as $xsa) {
                            //$arr_AC['productos']=$xsa;
                            //dd($xsa);
                            array_push($productosAC, $xsa);
                        }
                        $arr_AC['productos'] = $productosAC;
                        //update date 19 January 2021
                        //update date 16 March
                        //dd($value,$agenda->seguro);

                        $seguro            = DB::table('seguros')->where('id', $value->id_seguro)->first();
                        $arr_AC['seguros'] = $seguro->nombre;
                        array_push($array_Pusher, $arr_AC);
                    }
                }
                //dd($array_Pusher);
                foreach ($array_Pusher as $array_Pusher) {
                    if (!empty($array_Pusher)) {
                        //dd($array_Pusher);
                        $group2[$array_Pusher['paciente_principal']][] = $array_Pusher;
                    }
                }
            }
            //dd($group2);
            return $group2;
        } else {
            return response()->json("error no llega el request");
        }
    }
    public function getReloadRecibo(Request $request)
    {
        if (!is_null($request)) {
            $array_Pusher = array();
            $group2       = array();

            if (!empty($request->input("id_agenda"))) {
                $agenda         = Agenda::find($request->input("id_agenda"));
                $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
                    ->where('hc.id_agenda', $request->input("id_agenda"))->get();
                foreach ($procedimientos as $value) {
                    $texto = "";
                    if ($value->nombre_general == null) {
                        $adicionales = Hc_procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
                        $mas         = true;
                        $texto       = "";
                        foreach ($adicionales as $value2) {
                            if ($mas == true) {
                                $texto = $texto . $value2->procedimiento->nombre;
                                $mas   = false;
                            } else {
                                $texto = $texto . ' + ' . $value2->procedimiento->nombre;
                            }
                        }
                    }
                    $arr_AC = [];

                    //$productos =Movimiento_Paciente::where('id_hc_procedimientos', $value->id)->get();
                    $productos = DB::table('movimiento_paciente as mp')->where('mp.id_hc_procedimientos', $value->id)->join('movimiento as m', 'm.id', 'mp.id_movimiento')->join('ct_productos_insumos as pro', 'pro.id_insumo', 'm.id_producto')->join('ct_productos as productos', 'productos.id', 'pro.id_producto')->groupBy('m.id_producto')->select(DB::raw('COUNT(DISTINCT(m.id)) AS cantidad'), 'productos.codigo as codigo', 'productos.nombre as nombre')->get();

                    $arr_AC['paciente_principal'] = $agenda->paciente->id;
                    $arr_AC['nombre_principal']   = $texto;
                    $arr_AC['hc_procedimiento']   = $value->id;
                    $arr_AC['fecha']              = $agenda->fechaini;
                    $arr_AC['agenda']             = $agenda->id;
                    $productosAC                  = [];
                    foreach ($productos as $xsa) {
                        //$arr_AC['productos']=$xsa;
                        //dd($xsa);
                        array_push($productosAC, $xsa);
                    }
                    $arr_AC['productos'] = $productosAC;
                    //update date 19 January 2021
                    $arr_AC['seguros'] = $agenda->seguro->nombre;
                    array_push($array_Pusher, $arr_AC);
                }
                //dd($array_Pusher);
                foreach ($array_Pusher as $array_Pusher) {
                    if (!empty($array_Pusher)) {
                        //dd($array_Pusher);
                        $group2[$array_Pusher['paciente_principal']][] = $array_Pusher;
                    }
                }
            }
            //dd($group2);
            return $group2;
        } else {
            return response()->json("error no llega el request");
        }
    }
    public function store_omni(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //aqui ando

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        $llevaOrden = false;
        $validate   = $request['validate'];
        $numero     = $request->numero;
        //$idusuario  = Auth::user()->id;
        $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $nfactura        = 0;
        $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $num_comprobante = 0;
        //dd($request->all());
        $proced = $request['procedimiento'];
        if ($request['sucursal'] != 0) {
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;
            $proced     = $request['procedimiento'];

            //if($idusuario != "0957258056"){
            if (!is_null($numero)) {
                $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                    ->where('nro_comprobante', $num_comprobante)
                    ->where('tipo', '<>', 'VEN-FACT')
                    ->where('estado', '!=', '0')->first();
                if (!is_null($verifica_num_factura)) {
                    return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                } else {
                    $nfactura = $numero;
                }
            } else {
                $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
            }
            /*}else{
        if (!is_null($numero)) {

        $numero1 = $this->valida_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja, $numero);

        if (is_null($numero1)) {
        $nfactura = $numero;
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        }else {
        $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        }
        } else {

        $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        }
        }*/
        } else {
            $c_sucursal      = 0;
            $c_caja          = 0;
            $num_comprobante = 0;
            $nfactura        = 0;
            $proced          = $request['procedimiento'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced;
        //**
        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //*
        //*
        //******GUARDAdo TABLA ASIENTO CABECERA********
        //*
        if ($request['tipo_factura'] == 3) {
            //dd("sa");
            $get = $this->getHonorarios($request);
        } else {
            DB::beginTransaction();
            $msj = "no    o";
            try {
                $input_cabecera = [
                    'fecha_asiento'   => $fecha_as,
                    'fact_numero'     => $nfactura,
                    'id_empresa'      => $id_empresa,
                    'observacion'     => $text,
                    'valor'           => $request['total1'],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera); //activo
                //$id_asiento_cabecera = 0;
                //GUARDAdo TABLA CT_VENTA.
                $factura_venta = [
                    'sucursal'            => $cod_sucurs->codigo_sucursal,
                    'punto_emision'       => $cod_caj->codigo_caja,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => $id_empresa,
                    'tipo'                => $request['tipo'],
                    'fecha'               => $request['fecha_asiento'],
                    'divisas'             => $request['divisas'],
                    'nombre_cliente'      => $request['nombre_cliente'],
                    'tipo_consulta'       => $request['tipo_consulta'],
                    'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente'   => $request['direccion_cliente'],
                    'ruc_id_cliente'      => $request['identificacion_cliente'],
                    'telefono_cliente'    => $request['telefono_cliente'],
                    'email_cliente'       => $request['mail_cliente'],
                    'orden_venta'         => $request['orden_venta'],
                    'nro_autorizacion'    => $request['numero_autorizacion'],
                    'id_paciente'         => $request['identificacion_paciente'],
                    'nombres_paciente'    => $request['pacienteinfo'],
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente'     => $request['segurosinfo'],
                    'procedimientos'      => $request['procedimientoinfo'],
                    'fecha_procedimiento' => $request['fecha_proced'],
                    'copago'              => $request['totalc'],
                    'id_recaudador'       => $request['cedula_recaudador'],
                    'subtotal_0'          => $request['subtotal_01'],
                    'subtotal_12'         => $request['subtotal_121'],
                    'descuento'           => $request['descuento1'],
                    'base_imponible'      => $request['subtotal_121'],
                    'valor_contable'      => $request['total1'],
                    'impuesto'            => $request['tarifa_iva1'],
                    'total_final'         => $request['total1'],
                    'ip_creacion'         => "OMNI",
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];

                $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
                $arr_total      = [];
                $total_iva      = 0;
                $total_impuesto = 0;
                $total_0        = 0;
                $arr_id_hc      = [];
                $arr_activos    = [];
                $cont           = 0;
                $arr_obs        = [];
                if (!empty($request->input("veractivo"))) {
                    for ($i = 0; $i < count($request->input("veractivo")); $i++) {
                        if ($request->input("veractivo")[$i] == 1) {
                            $ar = $request->input("hc_procedimiento")[$i];
                            array_push($arr_activos, $ar);
                            $aobs = [
                                'procedimiento' => $request->input("hc_procedimiento")[$i],
                                'obs'           => $request->input("nom_paciente")[$i] . " - " . $request->input("obs_paciente")[$i],
                            ];
                            array_push($arr_obs, $aobs);
                        }
                    }
                }

                $observacion = "";
                if (!empty($request->input("nombre"))) {
                    for ($i = 0; $i < count($request->input("nombre")); $i++) {
                        if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                            for ($k = 0; $k < sizeof($arr_obs); $k++) {
                                if ($arr_obs[$k]['procedimiento'] == $request->input('id_hc_proc')[$i]) {
                                    $observacion = $arr_obs[$k]['obs'];
                                }
                            }
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $precio_neto = "";
                            if (isset($request->input("precioneto")[$i])) {
                                $precio_neto = $request->input("precioneto")[$i];
                            }
                            $nombre = "";
                            if (isset($request->input("nombre")[$i])) {
                                $nombre = $request->input("nombre")[$i];
                            }
                            $cantidad = 0;
                            if (isset($request->input("cantidad")[$i])) {
                                $cantidad = $request->input("cantidad")[$i];
                            }
                            $codigo = "";
                            if (isset($request->input("codigo")[$i])) {
                                $codigo = $request->input("codigo")[$i];
                            }
                            $nombre_procedimiento = "";
                            if (isset($request->input("id_principal")[$i])) {
                                $nombre_procedimiento = $request->input("id_principal")[$i];
                            }
                            $fecha_procedimiento = "";
                            if (isset($request->input("fecha_procedimiento")[$i])) {
                                $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                            }
                            $precio = 0;
                            if (isset($request->input("precio")[$i])) {
                                $precio = $request->input("precio")[$i];
                            }
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $iva = "";
                            if (isset($request->input("iva")[$i])) {
                                $iva = $request->input("iva")[$i];
                            }
                            $descuento = "";
                            if (isset($request->input("desc")[$i])) {
                                $descuento = $request->input("desc")[$i];
                            }
                            $descrip_prod = "";
                            if (isset($request->input("descrip_prod")[$i])) {
                                $descrip_prod = $request->input("descrip_prod")[$i];
                            }
                            $id_hc_proc = "";
                            if (isset($request->input("id_hc_proc")[$i])) {
                                $id_hc_proc = $request->input("id_hc_proc")[$i];
                            }
                            $id_agenda = "";
                            if (isset($request->input("id_agenda")[$i])) {
                                $id_agenda = $request->input("id_agenda")[$i];
                            }
                            $hc_procedimiento = "";
                            if (isset($request->input("hc_procedimiento")[$i])) {
                                $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                            }

                            $bodega_in = "";
                            if (isset($request->input("bodega")[$i])) {
                                $bodega_in = $request->input("bodega")[$i];
                            }

                            $precioneto = $cantidad * $precio;
                            $arr        = [
                                'nombre'     => $nombre,
                                'cantidad'   => $cantidad,
                                'codigo'     => $codigo,
                                'precio'     => $precio,
                                'descpor'    => "0.12",
                                'copago'     => "",
                                'descuento'  => $descuento,
                                'precioneto' => $precio_neto,
                                'detalle'    => $descrip_prod,
                                'iva'        => $iva,
                                'id_hc_proc' => $hc_procedimiento,
                                'obs_pac'    => $observacion,
                                'bodega_in'  => $bodega_in,
                            ];
                            if ($request["tipo_servicio"] == "2") {
                                $id_hc = ['id' => $request->input('id_hc_proc')[$i]];
                                if (in_array($request->input('id_hc_proc')[$i], $arr_activos)) {
                                    // array_push($arr, [);
                                    array_push($arr_total, $arr);
                                    // $cont++;
                                    array_push($arr_id_hc, $id_hc);
                                }
                            } elseif ($request["tipo_servicio"] == "1") {
                                array_push($arr_total, $arr);
                            }
                        }
                    }
                }

                $arr_consultas = [];
                if (!empty($request->input("prd_id"))) {
                    for ($i = 0; $i < count($request->input("prd_id")); $i++) {
                        if ($request->input("prd_id")[$i] != "" || $request->input("prd_id")[$i] != null) {
                            $arr_con = [
                                'value'      => $request->input("prd_val")[$i],
                                'prd_id'     => $request->input("prd_id")[$i],
                                'prd_activo' => $request->input("prd_activo")[$i],
                            ];
                            array_push($arr_consultas, $arr_con);
                        }
                    }
                }
                // $ids_consultas = json_encode($request->input('id_hc_consulta'));

                foreach ($arr_total as $valor) {
                    //dd($valor);
                    if ($valor['copago'] > 0) {
                        //registra orden de venta
                        $llevaOrden = true;
                    }
                    $detalle = [
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
                        'descuento_porcentaje' => $valor['descpor'],
                        'descuento'            => $valor['descuento'],
                        'extendido'            => $valor['precioneto'],
                        'detalle'              => $valor['detalle'],
                        'copago'               => $valor['copago'],
                        'check_iva'            => $valor['iva'],
                        'codigo'               => $valor['obs_pac'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'bodega'               => $valor['bodega'],

                    ];

                    Ct_detalle_venta::create($detalle); //activo
                }

                //*
                //***MODULO CUENTA POR COBRAR***
                //
                //cUENTAS X COBRAR CLIENTES
                // --activo
                $val_tol = $request['total1'];

                if ($val_tol > 0) {
                    $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');

                    //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => $request['total1'],
                        'haber'               => '0',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }

                //    2.01.07.01.01 iva sobre ventas
                if ($request['tarifa_iva1'] > 0) {
                    $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');

                    //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $request['tarifa_iva1'],
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }
                // 4.1.01.02    Ventas Mercaderia Tarifa 12%
                if ($request['subtotal_121'] > 0) {
                    $id_plan_confg = LogConfig::busqueda('4.1.01.02');

                    //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $request['subtotal_121'],
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }

                // 4.1.01.01    Ventas Mercaderia Tarifa 0%
                if ($request['subtotal_01'] > 0) {

                    $id_plan_confg = LogConfig::busqueda('4.1.01.01');

                    $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fecha_as,
                        'debe'                => '0',
                        'haber'               => $request['subtotal_01'],
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }

                //****REGISTRO FORMAS DE PAGO*** --activo falta****
                $arr_p = [];
                if (!empty($request->input("id_tip_pago"))) {
                    for ($i = 0; $i < count($request->input("id_tip_pago")); $i++) {
                        if ($request->input("valor")[$i] != "" || $request->input("valor_base")[$i] != null) {
                            $arr_pagos = [
                                'id_tip_pago'    => $request->input("id_tip_pago")[$i],
                                'fecha_pago'     => $request->input("fecha_pago")[$i],
                                'numero_pago'    => $request->input("numero_pago")[$i],
                                'id_banco_pago'  => $request->input("id_banco_pago")[$i],
                                'id_cuenta_pago' => $request->input("id_cuenta_pago")[$i],
                                'giradoa'        => $request->input("giradoa")[$i],
                                'valor'          => $request->input("valor")[$i],
                                'valor_base'     => $request->input("valor_base")[$i],
                            ];
                            array_push($arr_p, $arr_pagos);
                        }
                    }
                }

                foreach ($arr_p as $valor) {
                    Ct_Forma_Pago::create([
                        'id_ct_ventas'    => $id_venta,
                        'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                        'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                        'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                        'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                        'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                        'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                        'valor'           => $valor['valor'], //$request['valor'.$i],
                        'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }

                //agregar comprobantes de ingreso
                $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
                $arr_agendas = [];
                if ($request->tipo_factura == 1) {
                    foreach ($arr_consultas as $valor) {
                        if ($valor['prd_activo'] == "1") {
                            Ct_factura_procedimiento::create([
                                'id_ct_ventas'         => $id_venta,
                                'id_hc_procedimientos' => $valor['prd_id'],
                                'estado'               => "1",
                                'id_usuariocrea'       => $idusuario,
                                'id_usuariomod'        => $idusuario,
                                'ip_creacion'          => $ip_cliente,
                                'ip_modificacion'      => $ip_cliente,
                            ]);
                        }
                    }

                    if ($llevaOrden) {
                        $orden_id     = $this->ordenVenta($id_venta, $request);
                        $id_ct_ventas = [

                            'id_ct_venta' => $id_venta,

                        ];

                        Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
                    } else {
                        $orden_id = 0;
                    }
                    $arr_agendas = [];
                    if (!empty($request->input("hc_procedimiento"))) {

                        for ($i = 0; $i < count($request->input("hc_procedimiento")); $i++) {

                            if ($request->input("hc_procedimiento")[$i] != "" || $request->input("paciente")[$i] != null) {
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $precio_neto = "";
                                if (isset($request->input("precioneto")[$i])) {
                                    $precio_neto = $request->input("precioneto")[$i];
                                }
                                $nombre = "";
                                if (isset($request->input("nombre")[$i])) {
                                    $nombre = $request->input("nombre")[$i];
                                }
                                $cantidad = 0;
                                if (isset($request->input("cantidad")[$i])) {
                                    $cantidad = $request->input("cantidad")[$i];
                                }
                                $codigo = "";
                                if (isset($request->input("codigo")[$i])) {
                                    $codigo = $request->input("codigo")[$i];
                                }
                                $nombre_procedimiento = "";
                                if (isset($request->input("id_principal")[$i])) {
                                    $nombre_procedimiento = $request->input("id_principal")[$i];
                                }
                                $fecha_procedimiento = "";
                                if (isset($request->input("fecha_procedimiento")[$i])) {
                                    $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                                }
                                $precio = 0;
                                if (isset($request->input("precio")[$i])) {
                                    $precio = $request->input("precio")[$i];
                                }
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $iva = "";
                                if (isset($request->input("iva")[$i])) {
                                    $iva = $request->input("iva")[$i];
                                }
                                $descuento = "";
                                if (isset($request->input("desc")[$i])) {
                                    $descuento = $request->input("desc")[$i];
                                }
                                $descrip_prod = "";
                                if (isset($request->input("descrip_prod")[$i])) {
                                    $descrip_prod = $request->input("descrip_prod")[$i];
                                }
                                $id_hc_proc = "";
                                if (isset($request->input("id_hc_proc")[$i])) {
                                    $id_hc_proc = $request->input("id_hc_proc")[$i];
                                }
                                $id_agenda = "";
                                if (isset($request->input("id_agenda")[$i])) {
                                    $id_agenda = $request->input("id_agenda")[$i];
                                }
                                $hc_procedimiento = "";
                                if (isset($request->input("hc_procedimiento")[$i])) {
                                    $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                                }
                                $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento, 'nombre' => $nombre];
                                array_push($arr_agendas, $arr_ags);
                            }
                        }
                    }
                    $contador = 1;
                    foreach ($arr_agendas as $agenda) {
                        $id_omni = Ct_Factura_Omni::insertGetId([
                            'id_ct_ventas'    => $id_venta,
                            'id_agenda'       => $agenda["id_agenda"],
                            'id_paciente'     => $agenda["id_paciente"],
                            'tipo_factura'    => $request['tipo_factura'],
                            'fecha'           => $agenda['fecha_procedimiento'],
                            'estado'          => "1",
                            'id_empresa'      => $id_empresa,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ]);
                        $precio_neto = $agenda['cantidad'] * ($agenda['precio']);
                        $serie       = "";

                        if ($agenda['hc_procedimiento'] != null) {

                            $sx = Movimiento_Paciente::where('id_hc_procedimientos', $agenda['hc_procedimiento'])->get();

                            if (!is_null($sx)) {
                                foreach ($sx as $p) {

                                    $movimiento = Movimiento::find($p->id_movimiento);

                                    if (!is_null($movimiento)) {

                                        $find = Ct_Detalle_Venta_Omni::where('serie', $movimiento->serie)->where('id_hc_procedimiento', $agenda['hc_procedimiento'])->first();
                                        if (is_null($find)) {
                                            $serie = $movimiento->serie;
                                        } else {
                                            //$serie= $p->serie;
                                        }
                                    }
                                }
                            }
                        }
                        Ct_Detalle_Venta_Omni::create([
                            'id_ct_ventas'         => $id_venta,
                            'id_ct_productos'      => $agenda['producto'],
                            'nombre'               => $agenda['producto'],
                            'id_omni'              => $id_omni,
                            'cantidad'             => $agenda['cantidad'],
                            'fecha_procedimiento'  => $agenda['fecha_procedimiento'],
                            'id_agenda'            => $agenda['id_agenda'],
                            'nombre_principal'     => $agenda['nombre_principal'],
                            'id_hc_procedimiento'  => $agenda['hc_procedimiento'],
                            'precio'               => $agenda['precio'],
                            'serie'                => $serie, //serie
                            'descuento_porcentaje' => $request['iva_real'],
                            'extendido'            => $precio_neto,
                            'id_paciente'          => $agenda['id_paciente'],
                            'detalle'              => $agenda['descripcion'],
                            'check_iva'            => $agenda['check_iva'],
                            'ip_creacion'          => $ip_cliente,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                        ]);
                    }
                    $data['id']   = $id_venta;
                    $data['tipo'] = 'VEN-FA';
                    $data['omni'] = '1';
                    $msj = Ct_Kardex::generar_kardex($data);
                } else {
                    //dd("dsadsa");
                    $arr_agendas = [];
                    if (!empty($request->input("codigo"))) {
                        for ($i = 0; $i < count($request->input("codigo")); $i++) {
                            if ($request->input("codigo")[$i] != "" || $request->input("codigo")[$i] != null) {
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $precio_neto = "";
                                if (isset($request->input("precioneto")[$i])) {
                                    $precio_neto = $request->input("precioneto")[$i];
                                }
                                $nombre = "";
                                if (isset($request->input("nombre")[$i])) {
                                    $nombre = $request->input("nombre")[$i];
                                }
                                $cantidad = 0;
                                if (isset($request->input("cantidad")[$i])) {
                                    $cantidad = $request->input("cantidad")[$i];
                                }
                                $codigo = "";
                                if (isset($request->input("codigo")[$i])) {
                                    $codigo = $request->input("codigo")[$i];
                                }
                                $nombre_procedimiento = "";
                                if (isset($request->input("id_principal")[$i])) {
                                    $nombre_procedimiento = $request->input("id_principal")[$i];
                                }
                                $fecha_procedimiento = "";
                                if (isset($request->input("fecha_procedimiento")[$i])) {
                                    $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                                }
                                $precio = 0;
                                if (isset($request->input("precio")[$i])) {
                                    $precio = $request->input("precio")[$i];
                                }
                                $paciente = "";
                                if (isset($request->input("paciente")[$i])) {
                                    $paciente = $request->input("paciente")[$i];
                                }
                                $iva = "";
                                if (isset($request->input("iva")[$i])) {
                                    $iva = $request->input("iva")[$i];
                                }
                                $descuento = "";
                                if (isset($request->input("desc")[$i])) {
                                    $descuento = $request->input("desc")[$i];
                                }
                                $descrip_prod = "";
                                if (isset($request->input("descrip_prod")[$i])) {
                                    $descrip_prod = $request->input("descrip_prod")[$i];
                                }
                                $id_agenda = "";
                                if (isset($request->input("id_agenda")[$i])) {
                                    $id_agenda = $request->input("id_agenda")[$i];
                                }
                                $hc_procedimiento = "";
                                if (isset($request->input("hc_procedimiento")[$i])) {
                                    $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                                }
                                $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento];
                                array_push($arr_agendas, $arr_ags);
                            }
                        }
                    }
                    foreach ($arr_agendas as $agenda) {

                        $id_omni = Ct_Factura_Omni::insertGetId([
                            'id_ct_ventas'    => $id_venta,
                            'id_agenda'       => $agenda['id_agenda'],
                            'fecha'           => $agenda['fecha_procedimiento'],
                            'id_paciente'     => $agenda["id_paciente"],
                            'tipo_factura'    => $request['tipo_factura'],
                            'estado'          => "1",
                            'id_empresa'      => $id_empresa,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ]);
                        $precio_neto = $agenda['cantidad'] * ($agenda['precio']);
                        Ct_Detalle_Venta_Omni::create([
                            'id_ct_ventas'         => $id_venta,
                            'id_omni'              => $id_omni,
                            'id_ct_productos'      => $agenda['producto'],
                            'nombre'               => $agenda['producto'],
                            'cantidad'             => $agenda['cantidad'],
                            'fecha_procedimiento'  => $agenda['fecha_procedimiento'],
                            'id_hc_procedimiento'  => $agenda['hc_procedimiento'],
                            'nombre_principal'     => $agenda['nombre_principal'],
                            'precio'               => $agenda['precio'],
                            'descuento_porcentaje' => $request['iva_real'],
                            'extendido'            => $precio_neto,
                            'id_paciente'          => $agenda['id_paciente'],
                            'detalle'              => $agenda['descripcion'],
                            'check_iva'            => $agenda['check_iva'],
                            'ip_creacion'          => $ip_cliente,
                            'ip_modificacion'      => $ip_cliente,
                            'id_usuariocrea'       => $idusuario,
                            'id_usuariomod'        => $idusuario,
                        ]);
                    }
                }
                $data['id']   = $id_venta;
                $data['tipo'] = 'VEN-FA';
                $msj          = Ct_Kardex::generar_kardex($data);
                DB::commit();
                return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'arr_consultas' => $arr_consultas, 'prd_val' => $request->input("prd_val"), 'request' => $request, 'llevaOrden' => $llevaOrden, 'arr_agendas' => $arr_agendas, 'kardex' => $msj, "respuesta" => "exito", 'mensaje' => "Guardado exitosamente"];
            } catch (\Exception $e) {

                DB::rollBack();
                return $e->getMessage();
            }
        }

        return $request;
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id'               => $request['id'],
            'nro_comprobante'  => $request['numero'],
            'id_asiento'       => $request['id_asiento'],
            'fecha'            => $request['fecha'],
            'nombre_cliente'   => $request['nombre_cliente'],
            'nombres_paciente' => $request['nombres_paciente'],
            'omni'             => $request['omni'],
        ];

        $ventas = $this->doSearchingQuery($constraints, $id_empresa);
        //dd($request );
        return view('contable/ventas/index', ['ventas' => $ventas, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }
    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query = Ct_ventas::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                if ($fields[$index] == "id" || $fields[$index] == "id_asiento") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->where('estado', '<', '2')->where('tipo', "VEN-FA")->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(20);
    }
    public function editar($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ventas = DB::table('ct_ventas as ct_v')
            ->leftjoin('ct_clientes as ct_c', 'ct_c.identificacion', 'ct_v.id_cliente')
            ->where('ct_v.id', $id)
            ->select('ct_v.*')->first();

        $detalle_venta = DB::table('ct_detalle_venta as ct_det_vent')
            ->where('ct_det_vent.id_ct_ventas', $id)
            ->where('ct_det_vent.estado', '1')
            ->select('ct_det_vent.*')
            ->get();
        $detalle_venfaco = DB::table('ct_detalle_venta as ct_detalle')
            ->where('ct_detalle.id_ct_ventas', $id)
            ->where('ct_detalle.estado', '1')
            ->groupBy('ct_detalle.id_ct_productos')
            ->select('id_ct_productos', 'nombre')
            ->select(DB::raw('id_ct_productos, nombre, SUM(precio) as precio, SUM(cantidad) as cantidad', 'SUM(descuento) as descuento'))
            ->get();
        $clientes   = Ct_Clientes::where('estado', '1')->get();
        $forma_pago = DB::table('ct_forma_pago')
            ->where('id_ct_ventas', $id)
            ->where('estado', '1')
            ->get();
        $tip_tarjeta     = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $banco           = Ct_Bancos::where('estado', '1')->get();
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->first();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $tipo_pago   = Ct_Tipo_Pago::where('estado', '1')->get();
        $cuentas     = Plan_Cuentas::where('estado', '2')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        return view('contable/ventas/edit', ['ventas' => $ventas, 'sucursales' => $sucursales, 'clientes' => $clientes, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'detalle_venta' => $detalle_venta, 'forma_pago' => $forma_pago, 'tip_tarjeta' => $tip_tarjeta, 'seguros' => $seguros, 'banco' => $banco, 'divisas' => $divisas, 'empresa' => $empresa, 'tipo_pago' => $tipo_pago, 'cuentas' => $cuentas, 'detalle_venfaco' => $detalle_venfaco, 'lista_banco' => $lista_banco]);
    }
    public function buscarCliente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();
        //$productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        $clientes = Ct_Clientes::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->get();
        foreach ($clientes as $cliente) {
            $data[] = array('value' => $cliente->nombre, 'id' => $cliente->identificacion, 'direccion' => $cliente->direccion_representante, 'ciudad' => $cliente->ciudad_representante, 'mail' => $cliente->email_representante, 'telefono' => $cliente->telefono1_representante, 'tipo' => $cliente->clase);
        }
        // print_r($clientes);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscarClientexId(Request $request)
    {
        //dd($request->valor);
        $busqueda = 'identificacion';
        $nombre   = $request['term'];
        $data     = array();
        if ($request->busq == "identificacion2") {
            $busqueda = "identificacion";
        }
        // dd($request->all());
        //$productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        $clientes = Ct_Clientes::where($busqueda, $request->valor)->where('estado', '1')->first();
        //dd($clientes);
        // foreach ($clientes as $cliente) {
        //     $data[] = array('value' => $cliente->identificacion, 'nombre' => $cliente->nombre, 'direccion' => $cliente->direccion_representante, 'ciudad' => $cliente->ciudad_representante, 'mail' => $cliente->email_representante, 'telefono' => $cliente->telefono1_representante, 'tipo' => $cliente->clase);
        // }
        // // print_r($clientes);
        // if (count($data) > 0) {
        //     return $data;
        // } else {
        //     return ['value' => 'No se encontraron resultados', 'id' => ''];
        // }
        return $clientes;
    }
    public function buscarPaciente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();

        //$productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        $paciente = Paciente::where('id', 'like', $nombre . '%')->get();
        foreach ($paciente as $value) {
            $nom_completo = "$value->nombre1 $value->nombre2 $value->apellido1 $value->apellido2";
            $data[]       = array('value' => $value->id, 'nombre' => $nom_completo, 'seguro' => $value->id_seguro);
        }
        // print_r($clientes);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscarPaciente_nombre(Request $request)
    {
        $nombres  = $request['term'];
        $data     = array();
        $paciente = Paciente::where('id', '<>', '7777773333');
        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            if ($cantidad == '2' || $cantidad == '3') {
                $paciente = $paciente->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {
                $paciente = $paciente->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }
        $paciente = $paciente->get();
        foreach ($paciente as $value) {
            $nom_completo = "$value->nombre1 $value->nombre2 $value->apellido1 $value->apellido2";
            $data[]       = array('value' => $nom_completo, 'nombre' => $value->id, 'seguro' => $value->id_seguro);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscarProducto(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('ct_productos')->where('codigo', $codigo)->first();
        if (!is_null($productos)) {
            $data = $productos->nombre;
            return $data;
        } else {
            //return ['value' => 'No se encontraron resultados'];
            return "error";
        }
    }
    public function buscar_identificacion(Request $request)
    {

        $id_cliente = $request['cliente'];
        $clientes   = DB::table('ct_clientes')->where('identificacion', $id_cliente)->first();
        if (!is_null($clientes)) {
            $client_cedula         = $clientes->cedula_representante;
            $client_direccion      = $clientes->direccion_representante;
            $client_identificacion = $clientes->identificacion;
            $client_telefono       = $clientes->telefono1_representante;
            $client_email          = $clientes->email_representante;
            $client_ciudad         = $clientes->ciudad_representante;
            return ['client_cedula' => $client_cedula, 'client_direccion' => $client_direccion, 'client_identificacion' => $client_identificacion, 'client_telefono' => $client_telefono, 'client_email' => $client_email, 'client_ciudad' => $client_ciudad];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscador_paciente(Request $request)
    {
        $paciente = [];
        if ($request['search'] != null) {
            $paciente = Paciente::whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '%" . $request['search'] . "%' ")->select(DB::raw('CONCAT_WS(" ", apellido1, " " , apellido2, " ", nombre1," ", nombre2) as text'), 'id as id')->get();
        }
        return response()->json($paciente);
    }
    //Vendedor
    public function buscar_identificacion_vendedor(Request $request)
    {

        $id_vendedor   = $request['vendedor'];
        $user_vendedor = User::where('id', $id_vendedor)
            ->where('estado', 1)->first();

        if (!is_null($user_vendedor)) {

            $vendedor_cedula = $user_vendedor->id;

            return ['vendedor_cedula' => $vendedor_cedula];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_identificacion_recaudador(Request $request)
    {

        $id_recaudador   = $request['recaudador'];
        $user_recaudador = User::where('id', $id_recaudador)
            ->where('estado', 1)->first();

        if (!is_null($user_recaudador)) {

            $recaudador_cedula = $user_recaudador->id;

            return ['recaudador_cedula' => $recaudador_cedula];
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_nombre(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();

        $productos = DB::table('ct_productos')->where('nombre', 'like', '%' . $nombre . '%')->get();

        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->codigo);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscar_codigo(Request $request)
    {

        //return  $request->all();

        $nombre = $request['nombre'];

        $data      = null;
        $productos = DB::table('ct_productos')->where('nombre', $nombre)->first();

        if (!is_null($productos)) {

            $prod_insum = DB::table('producto as prd')->where('codigo', $productos->codigo)
                ->select('prd.iva as iva_prod')
                ->first();
            $iva_prod = $prod_insum->iva_prod;

            $cod_product    = $productos->codigo;
            $prec_uno       = $productos->precio1;
            $prec_dos       = $productos->precio2;
            $prec_tres      = $productos->precio3;
            $prec_cuatr     = $productos->precio4;
            $prec_promocion = $productos->promocion;
            $cost_vent      = $productos->ultima_compra;

            return ['cod_product' => $cod_product, 'prec_uno' => $prec_uno, 'prec_dos' => $prec_dos, 'prec_tres' => $prec_tres, 'prec_cuatr' => $prec_cuatr, 'prec_promocion' => $prec_promocion, 'prec_promocion' => $prec_promocion, 'cost_vent' => $cost_vent, 'iva_prod' => $iva_prod];
            //return $productos;

        } else {

            return "error";
        }
    }
    public function buscar_codigo2(Request $request)
    {

        $codigo = $request['term'];

        $data      = array();
        $productos = DB::table('ct_productos')->where('codigo', 'like', '%' . $codigo . '%')->get();

        foreach ($productos as $product) {
            $data[] = array('value' => $product->codigo);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_nombre2(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('ct_productos')->where('codigo', $codigo)->first();
        if (!is_null($productos)) {
            $data = $productos->nombre;
            return $data;
        } else {
            //return ['value' => 'No se encontraron resultados'];
            return "error";
        }
    }
    public function update_direccion(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $iden_cliente = $request['ident_cliente'];
        $direccion    = $request['direc_cliente'];

        if (!is_null($iden_cliente)) {

            $input_direccion = [

                'direccion_representante' => $direccion,

            ];

            Ct_Clientes::where('identificacion', $iden_cliente)->update($input_direccion);
        }
    }
    public function completa_cedula_pacie(Request $request)
    {
        $id_pacient = $request['term'];
        $data       = array();

        $paciente = DB::table('paciente')->where('id', 'like', '%' . $id_pacient . '%')->get();

        foreach ($paciente as $pac) {
            $data[] = array('value' => $pac->id);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function buscar_paciente(Request $request)
    {
        $ced_pacient = $request['ced_paciente'];

        $paciente = Paciente::find($ced_pacient);

        //return $paciente;

        if (!is_null($paciente)) {

            //$texto = $paciente->nombre1. ' ' . $paciente->nombre2. ' ' . $paciente->apellido1. ' ' .$paciente->apellido2;

            $texto = $paciente->nombre1 . ' ' . $paciente->apellido1;

            $seguro = Seguro::find($paciente->id_seguro);

            if (!is_null($seguro)) {

                $id_seg = $seguro->id;
            }

            return ['texto' => $texto, 'id_seg' => $id_seg];
        } else {

            return "error";
        }
    }
    /************************************************************
     ***************OBTENER SUCURSAL DE CADA EMPRESA**************
    /************************************************************/
    public function obtener_sucursal_empresa(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empre = $request['id_emp'];

        if (!is_null($id_empre)) {

            $suc_caja = DB::table('ct_sucursales as ct_s')
                ->where('ct_s.estado', 1)
                ->where('ct_s.id_empresa', $id_empre)
                ->get();

            return $suc_caja;
        }

        return 'no';
    }

    /************************************************************
     *************OBTENER LAS CAJAS DE CADA SUCURSAL**************
    /************************************************************/
    public function obtener_caja_sucursal(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_sucur = $request['id_sucur'];

        if (!is_null($id_sucur)) {

            $caja = DB::table('ct_sucursales as ct_s')
                ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
                ->where('ct_c.estado', 1)
                ->where('ct_s.id', $id_sucur)
                ->get();

            return $caja;
        }

        return 'no';
    }

    /************************************************************
     *************OBTENER LAS CAJAS DE CADA SUCURSAL**************
    /************************************************************/
    public function obtener_secuencial(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_sucur = $request['id_sucur'];

        if (!is_null($id_sucur)) {

            $caja = DB::table('ct_sucursales as ct_s')
                ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
                ->where('ct_c.estado', 1)
                ->where('ct_s.id', $id_sucur)
                ->get();

            return $caja;
        }

        return 'no';
    }

    /************************************************************
     *************IMPRIMIR COMPROBANTE FACTURA DE VENTA***********
    /************************************************************/
    public function imprimir_comprobante_factura($id)
    {

        $fact_venta = Ct_ventas::findorfail($id);
        $ct_for_pag = Ct_Forma_Pago::where('id_ct_ventas', $id)->get();
        $ct_val_ret = Ct_Retencion_Fventas::where('id_ct_ventas', $id)->first();

        if ($fact_venta != null) {

            $emp             = Empresa::find($fact_venta->id_empresa);
            $recaud          = User::find($fact_venta->id_recaudador);
            $cliente         = Ct_Clientes::where('identificacion', $fact_venta->id_cliente)->first();
            $pacient         = Paciente::find($fact_venta->id_paciente);
            $deta_vent       = Ct_detalle_venta::where('id_ct_ventas', $id)->get();
            $detalle_venfaco = DB::table('ct_detalle_venta as ct_detalle')
                ->where('ct_detalle.id_ct_ventas', $id)
                ->where('ct_detalle.estado', '1')
                ->groupBy('ct_detalle.id_ct_productos')
                ->select('id_ct_productos', 'nombre')
                ->select(DB::raw('id_ct_productos, nombre, SUM(precio) as precio, SUM(cantidad) as cantidad', 'SUM(descuento) as descuento'))
                ->get();
        }

        $vistaurl = "contable.ventas.pdf_comprobante_tributario";
        $view     = \View::make($vistaurl, compact('fact_venta', 'emp', 'recaud', 'cliente', 'pacient', 'deta_vent', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret'))->render();

        //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function ordenes(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $seguro     = Seguro::where('inactivo', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        if ($request->fecha_hasta == null) {
            $request->fecha_hasta = date('Y-m-d');
        }
        $ventas = Ct_ven_orden::where('estado', '!=', null)->where('id_empresa', $id_empresa);
        if ($request['id'] != null) {
            $ventas = $ventas->where('id', $request->id);
        } else {

            if ($request->fecha_desde != null && $request->fecha_hasta != null) {
                $ventas = $ventas->whereBetween('fecha', [$request->fecha_desde . ' 00:00:00', $request->fecha_hasta . ' 23:59:59']);
            }
            if ($request->fecha_desde == null) {
                $ventas = $ventas->where('fecha', '<', $request->fecha_hasta . ' 23:59:59');
            }

            if ($request->nombres_paciente != null) {
                $ventas = $ventas->where('nombres_paciente', 'LIKE', '%' . $request->nombres_paciente . '%');
            }
            if ($request->nombre_cliente != null) {
                $ventas = $ventas->where('nombre_cliente', 'LIKE', '%' . $request->nombre_cliente . '%');
            }
            if ($request->nro_comprobante != null) {
                $ventas = $ventas->where('nro_comprobante', 'LIKE', '%' . $request->nro_comprobante . '%');
            }
        }
        $ventas = $ventas->orderby('id', 'desc')->paginate(10);

        return view('contable/ventas/ordenes', ['ventas' => $ventas, 'empresa' => $empresa, 'request' => $request, 'seguro' => $seguro]);
    }

    public function searchOrdenes(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $ventas      = array();
        $constraints = [
            'id'               => $request['id'],
            'fecha_desde'      => $request['fecha_desde'],
            'fecha_hasta'      => $request['fecha_hasta'],
            'nombre_cliente'   => $request['nombre_cliente'],
            'nombres_paciente' => $request['nombres_paciente'],
            'nro_comprobante'  => $request['nro_comprobante'],
        ];
        if ($request['id'] != null || $request['numero'] != null || $request['fecha'] != null || $request['nombre_cliente'] != null || $request['nombres_paciente'] != null) {
            $constraints = [
                'id'               => $request['id'],
                'fecha_desde'      => $request['fecha_desde'],
                'fecha_hasta'      => $request['fecha_hasta'],
                'nombre_cliente'   => $request['nombre_cliente'],
                'nombres_paciente' => $request['nombres_paciente'],
                'nro_comprobante'  => $request['nro_comprobante'],
            ];

            $ventas = $this->doSearchingQuery2($constraints, $id_empresa);
        } else {
            $ventas = Ct_Ven_Orden::where('estado', '<', '2')->where('tipo', "VEN-FA")->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);
        }
        $idusuario = Auth::user()->id;
        if ($idusuario == "0957258056") {
            // dd($constraints);
        }

        return view('contable/ventas/ordenes', ['ventas' => $ventas, 'empresa' => $empresa, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery2($constraints, $id_empresa)
    {

        $query = Ct_Ven_Orden::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('estado', '<', '2')->where('tipo', "VEN-FA")->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(10);
    }
    /*************************************
     **CREAR FACTURAS DE VENTA DESDE ORDEN**
    /*************************************/
    public function crear_facturaOrden($id, Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $divisas     = Ct_Divisas::where('estado', '1')->get();
        $clientes    = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago   = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $seguros     = Seguro::all();
        $idusuario   = Auth::user()->id;

        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $bodega = bodega::where('estado', '1')->get();
        //$venta  = Ct_ventas::findorfail($id); //::where("id", $id)->first();
        if ($idusuario == '0957258056') {
            // dd($venta);
        }
        // print_r($venta['id_empresa']);
        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        //sucursales
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        //punto emision
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            //->where('ct_s.id', $sucursales['id'])
            ->get();

        //Obtenemos Todas las Empresas que van a Emitir Facturas
        $empre = Empresa::all();

        //Obtenemos el listado de Cuentas de la Tabla Plan de Cuentas
        $cuentas = Plan_Cuentas::where('estado', '2')->get();

        //$productos = Producto::where('estado', '1')->get();
        $productos = Ct_productos::where('estado_tabla', '1')->get();

        $id_plan_confg = LogConfig::busqueda('4.1.01.02');

        //$iva       = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        $iva       = Ct_Configuraciones::where('id_plan', $id_plan_confg)->where('estado', '1')->first();

        $detalle = Ct_ven_orden_detalle::where("id_ct_ven_orden", $id)->get();

        $orden_venta = Ct_ven_orden::findorfail($id); //::where("id",$id)->get();//::where("id", $id)->first();
        //return $id;

        return view('contable/ventas/create_facturaOrden', ['divisas' => $divisas, 'sucursales' => $sucursales, 'punto' => $punto, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'bodega' => $bodega, 'empresa' => $empresa, 'empre' => $empre, 'productos' => $productos, 'iva' => $iva, 'cuentas' => $cuentas, "venta" => $orden_venta, "detalle" => $detalle]);
    }

    public function eliminar($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $ventas     = Ct_ven_orden::where('id', $id)->first();
        $inp        = [
            'estado'          => '0',
            'estado_pago'     => '0',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ];

        $ventas->update($inp);
        return redirect()->route('orden_venta');
    }

    //function to upload more data with differents values and move to process contable
    public function masivo_ventas(Request $request)
    {

        $ventas = Ct_ventas::where('id_empresa', '0993075000001')->get();
        //$facturas= $this->vf($book,$idusuario,$ip_cliente);
        //we can return excel invoker to load big data
        //$verd= $this->cliente_retencion();
        //$ahor = $this->egresoMasivo();
        //$kardex= $this->updateKardex();
        Excel::filter('chunk')->load('vfactura.xlsx')->chunk(250, function ($reader) {
            /*
            "fecha_de_emision" => "12/01/2021"
            "autorizacion" => "1201202101130718914000120010020000002781234567815"
            "numero_de_factura" => "001-002-000000278"
            "ruc" => "0991353119001"
            "cliente" => "INTERNATIONAL LABORATORIES SERVICES"
            "seguro" => "0000 General"
            "procedimiento" => "HONORARIOS MEDICOS "
            "paciente" => "11AG-10 OCT 2020"
            "fecha_de_procedimeinto" => "12/01/2021"
            "subtotal_12" => 0.0
            "subtotal_0" => 361.45
            "subtotal" => 361.45
            "descuento" => 0.0
            "iva" => 0.0
            "total" => 361.45
            "estado" => null*/
            $idusuario  = "1316262193";
            $ip_cliente = "subidach";
            foreach ($reader as $book) {

                //dd($book);
                $cliente = Ct_Clientes::where('identificacion', $book->ruc)->first();
                if (is_null($cliente)) {
                    Ct_Clientes::create([
                        'identificacion'          => $book->ruc,
                        'nombre'                  => $book->cliente,
                        'tipo'                    => '5',
                        'clase'                   => 'normal',
                        'nombre_representante'    => $book->cliente,
                        'cedula_representante'    => $book->ruc,
                        'ciudad_representante'    => 'GUAYAQUIL',
                        'telefono1_representante' => '11111',
                        'pais'                    => 'Ecuador',
                        'estado'                  => '1',
                        'id_usuariocrea'          => $idusuario,
                        'id_usuariomod'           => $idusuario,
                        'ip_creacion'             => $ip_cliente,
                        'ip_modificacion'         => $ip_cliente,

                    ]);
                }
                $input_cabecera = [
                    'fecha_asiento'   => date('Y-m-d', strtotime($book->fecha_de_emision)),
                    'fact_numero'     => $book->numero_de_factura,
                    'id_empresa'      => '1307189140001',
                    'observacion'     => 'Factura #' . $book->numero_de_factura,
                    'valor'           => $book->total,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                if (isset($book->estado)) {
                    if ($book->estado == "ANULADA") {
                        $input_cabecera = [
                            'fecha_asiento'   => date('Y-m-d', strtotime($book->fecha_de_emision)),
                            'fact_numero'     => $book->factura,
                            'id_empresa'      => '0993075000001',
                            'estado'          => '0',
                            'observacion'     => 'Factura #' . $book->factura,
                            'valor'           => $book->total,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                    }
                }
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $punto               = $book->numero_de_factura;
                $sucursal            = substr($punto, 0, 3);
                $punto_emision       = substr($punto, 4, 3);
                $nfactura            = substr($punto, 8);
                $factura_venta       = [
                    'sucursal'            => $sucursal,
                    'punto_emision'       => $punto_emision,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $book->numero_de_factura,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => '1307189140001',
                    'tipo'                => 'VEN-FA',
                    'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                    'divisas'             => '1',
                    'nombre_cliente'      => $book->cliente,
                    'tipo_consulta'       => '1',
                    'id_cliente'          => $book->ruc, //nombre_cliente
                    'direccion_cliente'   => 'DIRECCION',
                    'ruc_id_cliente'      => $book->ruc,
                    'telefono_cliente'    => '1111',
                    'email_cliente'       => '',
                    'orden_venta'         => '',
                    'nro_autorizacion'    => $book->autorizacion, //autorizacion de humanlabs
                    'id_paciente'         => '9999999999',
                    'nombres_paciente'    => $book->paciente,
                    'id_hc_procedimiento' => '',
                    'seguro_paciente'     => $book->seguro,
                    'procedimientos'      => $book->procedimiento,
                    'fecha_procedimiento' => date('Y-m-d', strtotime($book->fecha_de_procedimeinto)),
                    'concepto'            => '',
                    'copago'              => '',
                    'ci_vendedor'         => '',
                    'vendedor'            => '',
                    //'nota'                          => $request['nota'],
                    'subtotal_0'          => $book->subtotal,
                    'subtotal_12'         => '0',
                    //'subtotal'                      => $request['subtotal1'],
                    'descuento'           => $book->descuento,
                    'base_imponible'      => $book->descuento,
                    'impuesto'            => '0',
                    // 'transporte'                    => $request['transporte'],
                    'total_final'         => $book->total,
                    'valor_contable'      => $book->total,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                $id_venta = Ct_ventas::insertGetId($factura_venta);

                $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '1.01.02.05.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                    'debe'                => $book->total,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
                // 4.1.01.01    Ventas Mercaderia Tarifa 0%
                $precio = 0;
                $iva    = 0;
                if ($book->subtotal_12 > 0) {
                    $precio       = $book->subtotal_12;
                    $iva          = 1;
                    $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => '2.01.07.01.01',
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                        'debe'                => '0',
                        'haber'               => $book->subtotal_12,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                } else {
                    $precio       = $book->subtotal_0;
                    $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => '4.1.01.01',
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => date('Y-m-d', strtotime($book->fecha_de_emision)),
                        'debe'                => '0',
                        'haber'               => $book->subtotal_0,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }

                $detalle = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => 'HON-MED',
                    'nombre'               => 'HON-MED',
                    'cantidad'             => '1',
                    'precio'               => $precio,
                    'descuento_porcentaje' => '',
                    'descuento'            => $book->descuento,
                    'extendido'            => $book->total,
                    'detalle'              => 'HON-MED',
                    'copago'               => '',
                    'check_iva'            => $iva,
                    'porcentaje'           => '0.12',
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];

                Ct_detalle_venta::create($detalle);
            }
        });
        return response()->json(['mensaje' => 'disculpe se guardo correctamente']);
    }
    public function cliente_retencion()
    {
        $cliente_retencion = Ct_Cliente_Retencion::all();
        foreach ($cliente_retencion as $retencion) {
            $fecha = "";
            foreach ($retencion->detalle_retencion as $value) {
                if ($value->fechaauto != null) {
                    $fecha = $value->fechaauto;
                }
            }
            $update = Ct_Cliente_Retencion::find($retencion->id);
            if ($update != null) {
                $update->fecha = $fecha;
                $update->save();
            }
        }
    }
    public function debitoBancario()
    { //No la hice porque no vi que se usa 
        $default        = "";
        $debitoBancario = Ct_Debito_Bancario::where('id_empresa', '0992704152001')->get();

        foreach ($debitoBancario as $values) {
            //dd($values);
            $as           = Ct_Caja_Banco::find($values->id_banco);
            $cuenta_mayor = $as->cuenta_mayor;
            if (!is_null($values)) {
                $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $values->id_asiento)->get();
                foreach ($asiento as $asi) {

                    if ($asi->id_plan_cuenta != "2.01.03.01.01" && $asi->id_plan_cuenta != "2.01.03.01.02") {
                        $asi = Ct_Asientos_Detalle::find($asi->id);
                        //dd($asi);
                        if (!is_null($asi)) {
                            $asi->id_plan_cuenta = $cuenta_mayor;
                            $asi->save();
                        }
                    }
                }
            }
        }
    }
    public function egresoVarios()
    {
        $default        = "";
        $debitoBancario = Ct_Comprobante_Egreso_Varios::where('id_empresa', '0992704152001')->get();

        foreach ($debitoBancario as $values) {
            //dd($values);
            $as           = Ct_Caja_Banco::find($values->id_caja_banco);
            $cuenta_mayor = $as->cuenta_mayor;
            if (!is_null($values)) {
                $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $values->id_asiento_cabecera)->get();
                foreach ($asiento as $asi) {

                    if ($asi->debe == 0) {
                        $asi = Ct_Asientos_Detalle::find($asi->id);
                        //dd($asi,$cuenta_mayor);
                        if (!is_null($asi)) {
                            $asi->id_plan_cuenta  = $cuenta_mayor;
                            $asi->ip_modificacion = "sistemas";
                            $asi->save();
                        }
                    }
                }
            }
        }
    }
    public function egresoMasivo()
    {
        $default        = "";
        $debitoBancario = Ct_Comprobante_Egreso::where('id_empresa', '0992704152001')->get();

        foreach ($debitoBancario as $values) {
            //dd($values);
            $as = Ct_Caja_Banco::find($values->id_caja_banco);
            if (!is_null($as)) {
                $cuenta_mayor = $as->cuenta_mayor;
                if (!is_null($values)) {
                    $asiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $values->id_asiento_cabecera)->get();
                    foreach ($asiento as $asi) {

                        if ($asi->debe == 0) {
                            $asi = Ct_Asientos_Detalle::find($asi->id);
                            //dd($asi,$cuenta_mayor);
                            if (!is_null($asi)) {
                                $asi->id_plan_cuenta  = $cuenta_mayor;
                                $asi->ip_modificacion = "sistemas";
                                $asi->save();
                            }
                        }
                    }
                }
            }
        }
    }
    public function identificar_pagos($pago)
    {
        $id_ = "";
        if ($pago->tipo == 'CREDITO') {
            switch ($pago) {
                case 'MASTERCARD':
                    $id_ = 2;
                    break;
                case 'VISA':
                    $id_ = 1;
                    break;
                case 'PACIFICARD':
                    $id_ = 3;
                    break;
                case 'DINNERS':
                    $id_ = 4;
                    break;
                case 'BANCO PICHINCHA':
                    $id_ = 5;
                    break;
                case 'AMERICAN':
                    $id_ = 6;
                    break;
                case 'MAESTRO':
                    $id_ = 8;
                    break;
                case 'DISCOVER':
                    $id_ = 7;
                    break;
                case 'JCB':
                    $id_ = 9;
                    break;
                case 'ALIPAY':
                    $id_ = 10;
                    break;
                case 'PAYPAL':
                    $id_ = 11;
                    break;
                case 'ELECTRON':
                    $id_ = 12;
                    break;
            }
        } else {
            //banco
            switch ($pago) {
                case 'PICHINCHA':
                    $id_ = 1;
                    break;
                case 'PACIFICO':
                    $id_ = 2;
                    break;
                case 'GUAYAQUIL':
                    $id_ = 3;
                    break;
                case 'INTERNACIONAL':
                    $id_ = 4;
                    break;
                case 'BOLIVARIANO':
                    $id_ = 5;
                    break;
                case 'PRODUBANCO':
                    $id_ = 6;
                    break;
                case 'AUSTRO':
                    $id_ = 7;
                    break;
                case 'SOLIDARIO':
                    $id_ = 8;
                    break;
                case 'GENERAL':
                    $id_ = 9;
                    break;
                case 'LOJA':
                    $id_ = 10;
                    break;
                case 'MACHALA':
                    $id_ = 11;
                    break;
                case 'PROCREDIT':
                    $id_ = 12;
                    break;
                case 'AMERICA':
                    $id_ = 13;
                    break;
                case 'CHASE':
                    $id_ = 14;
                    break;
            }
        }
        return $id_;
    }
    //details master invoice, method paid, etc.
    //important more square values with covid at Monday
    //UPDATE: success function at 7 December 2020
    //NO UPDATE: no work at day 28 December 2020
    public function vf($book, $idusuario, $ip_cliente)
    {
        $cliente = Ct_Clientes::where('identificacion', $book->cedula)->first();
        if (!is_null($cliente)) {
            $input_cabecera = [
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $book->factura,
                'id_empresa'      => '0993075000001',
                'observacion'     => 'Factura #' . $book->factura,
                'valor'           => $book->total,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            if (isset($book->estado)) {
                if ($book->estado == "ANULADA") {
                    $input_cabecera = [
                        'fecha_asiento'   => $book->fecha,
                        'fact_numero'     => $book->factura,
                        'id_empresa'      => '0993075000001',
                        'estado'          => '0',
                        'observacion'     => 'Factura #' . $book->factura,
                        'valor'           => $book->total,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                }
            }
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $punto               = $book->factura;
            $sucursal            = substr($punto, 0, 3);
            $punto_emision       = substr($punto, 4, 3);
            $nfactura            = substr($punto, 8);
            $factura_venta       = [
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'numero'              => $nfactura,
                'nro_comprobante'     => $book->factura,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => '0993075000001',
                'tipo'                => 'VEN-FA',
                'fecha'               => $book->fecha,
                'divisas'             => '1',
                'nombre_cliente'      => $book->paciente,
                'tipo_consulta'       => '1',
                'id_cliente'          => $book->cedula, //nombre_cliente
                'direccion_cliente'   => 'DIRECCION',
                'ruc_id_cliente'      => $book->cedula,
                'telefono_cliente'    => '1111',
                'email_cliente'       => '',
                'orden_venta'         => '',
                'nro_autorizacion'    => '1127272146', //autorizacion de humanlabs
                'id_paciente'         => $book->cedula,
                'nombres_paciente'    => $book->paciente,
                'id_hc_procedimiento' => '',
                'seguro_paciente'     => 'PRIVADO',
                'procedimientos'      => '',
                'fecha_procedimiento' => $book->fecha,
                'concepto'            => '',
                'copago'              => '',
                'ci_vendedor'         => '',
                'vendedor'            => '',
                //'nota'                          => $request['nota'],
                'subtotal_0'          => $book->subtotal,
                'subtotal_12'         => '0',
                //'subtotal'                      => $request['subtotal1'],
                'descuento'           => $book->descuento,
                'base_imponible'      => $book->descuento,
                'impuesto'            => '0',
                // 'transporte'                    => $request['transporte'],
                'total_final'         => $book->total,
                'valor_contable'      => $book->total,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_venta = Ct_ventas::insertGetId($factura_venta);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');

            $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->total,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%

            $id_plan_confg = LogConfig::busqueda('4.1.01.01');

            $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $book->fecha,
                'debe'                => '0',
                'haber'               => $book->total,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            $detalle = [
                'id_ct_ventas'         => $id_venta,
                'id_ct_productos'      => 'EXAMEN LABS',
                'nombre'               => 'EXAMEN LABS',
                'cantidad'             => '1',
                'precio'               => $total_final['total'],
                'descuento_porcentaje' => '',
                'descuento'            => $book->descuento,
                'extendido'            => $total_final['total'],
                'detalle'              => 'EXAMEN LABS',
                'copago'               => '',
                'check_iva'            => '0',
                'porcentaje'           => '0.12',
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
            $detail = $this->vf_detail($book, $idusuario, $ip_cliente, $nfactura, $id_venta);
        } else {

            Ct_Clientes::create([
                'identificacion'          => $book->cedula,
                'nombre'                  => $book->paciente,
                'tipo'                    => '5',
                'clase'                   => 'normal',
                'nombre_representante'    => $book->paciente,
                'cedula_representante'    => $book->cedula,
                'ciudad_representante'    => 'GUAYAQUIL',
                'telefono1_representante' => '11111',
                'pais'                    => 'Ecuador',
                'estado'                  => '1',
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,

            ]);
            $input_cabecera = [
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $book->factura,
                'id_empresa'      => '0993075000001',
                'observacion'     => 'Factura #' . $book->factura,
                'valor'           => $book->total,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            if (isset($book->estado)) {
                if ($book->estado == "ANULADA") {
                    $input_cabecera = [
                        'fecha_asiento'   => $book->fecha,
                        'fact_numero'     => $book->factura,
                        'id_empresa'      => '0993075000001',
                        'estado'          => '0',
                        'observacion'     => 'Factura #' . $book->factura,
                        'valor'           => $book->total,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                }
            }
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $punto               = $book->factura;
            $sucursal            = substr($punto, 0, 3);
            $punto_emision       = substr($punto, 4, 3);
            $nfactura            = substr($punto, 8);
            $factura_venta       = [
                'sucursal'            => $sucursal,
                'punto_emision'       => $punto_emision,
                'numero'              => $nfactura,
                'nro_comprobante'     => $book->factura,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => '0993075000001',
                'tipo'                => 'VEN-FA',
                'fecha'               => $book->fecha,
                'divisas'             => '1',
                'nombre_cliente'      => $book->paciente,
                'tipo_consulta'       => '1',
                'id_cliente'          => $book->cedula, //nombre_cliente
                'direccion_cliente'   => 'DIRECCION',
                'ruc_id_cliente'      => $book->cedula,
                'telefono_cliente'    => '1111',
                'email_cliente'       => '',
                'orden_venta'         => '',
                'nro_autorizacion'    => '1127272146', //autorizacion de humanlabs
                'id_paciente'         => $book->cedula,
                'nombres_paciente'    => $book->paciente,
                'id_hc_procedimiento' => '',
                'seguro_paciente'     => 'PRIVADO',
                'procedimientos'      => '',
                'fecha_procedimiento' => $book->fecha,
                'concepto'            => '',
                'copago'              => '',
                'ci_vendedor'         => '',
                'vendedor'            => '',
                //'nota'                          => $request['nota'],
                'subtotal_0'          => $book->subtotal,
                'subtotal_12'         => '0',
                //'subtotal'                      => $request['subtotal1'],
                'descuento'           => $book->descuento,
                'base_imponible'      => $book->descuento,
                'impuesto'            => '0',
                // 'transporte'                    => $request['transporte'],
                'total_final'         => $book->total,
                'valor_contable'      => $book->total,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            if (isset($book->estado)) {
                if ($book->estado == "ANULADA") {
                    $factura_venta = [
                        'sucursal'            => $sucursal,
                        'punto_emision'       => $punto_emision,
                        'numero'              => $nfactura,
                        'nro_comprobante'     => $book->factura,
                        'id_asiento'          => $id_asiento_cabecera,
                        'id_empresa'          => '0993075000001',
                        'tipo'                => 'VEN-FA',
                        'fecha'               => $book->fecha,
                        'divisas'             => '1',
                        'estado'              => '0',
                        'nombre_cliente'      => $book->paciente,
                        'tipo_consulta'       => '1',
                        'id_cliente'          => $book->cedula, //nombre_cliente
                        'direccion_cliente'   => 'DIRECCION',
                        'ruc_id_cliente'      => $book->cedula,
                        'telefono_cliente'    => '1111',
                        'email_cliente'       => '',
                        'orden_venta'         => '',
                        'nro_autorizacion'    => '1127272146', //autorizacion de humanlabs
                        'id_paciente'         => $book->cedula,
                        'nombres_paciente'    => $book->paciente,
                        'id_hc_procedimiento' => '',
                        'seguro_paciente'     => 'PRIVADO',
                        'procedimientos'      => '',
                        'fecha_procedimiento' => $book->fecha,
                        'concepto'            => '',
                        'copago'              => '',
                        'ci_vendedor'         => '',
                        'vendedor'            => '',
                        //'nota'                          => $request['nota'],
                        'subtotal_0'          => $book->subtotal,
                        'subtotal_12'         => '0',
                        //'subtotal'                      => $request['subtotal1'],
                        'descuento'           => $book->descuento,
                        'base_imponible'      => $book->descuento,
                        'impuesto'            => '0',
                        // 'transporte'                    => $request['transporte'],
                        'total_final'         => $book->total,
                        'valor_contable'      => $book->total,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ];
                }
            }
            $id_venta     = Ct_ventas::insertGetId($factura_venta);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');

            //            $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();

            $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->total,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            $id_plan_confg = LogConfig::busqueda('4.1.01.01');
            $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
            //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '4.1.01.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $book->fecha,
                'debe'                => '0',
                'haber'               => $book->total,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            //master detail
            $detail = $this->vf_detail($book, $idusuario, $ip_cliente, $nfactura, $id_venta);
        }
    }
    public function vf_detail($book, $idusuario, $ip_cliente, $nfactura, $id_venta)
    {
        //would change with other name
        //had left subtotal_0
        $numero_factura = "";
        if (!is_null($book->examen_laboratorio)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $total_final = $this->indentify_valor($book, 1);
            $detalle     = [
                'id_ct_ventas'         => $id_venta,
                'id_ct_productos'      => 'EXAMEN LABS',
                'nombre'               => 'EXAMEN LABS',
                'cantidad'             => '1',
                'precio'               => $total_final['total'],
                'descuento_porcentaje' => '',
                'descuento'            => $book->descuento,
                'extendido'            => $total_final['total'],
                'detalle'              => 'EXAMEN LABS',
                'copago'               => '',
                'check_iva'            => '0',
                'porcentaje'           => '0.12',
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }
        if (!is_null($book->prueba_covid)) {
            $total_final = $this->indentify_valor($book, 1);
            $detalle     = [
                'id_ct_ventas'         => $id_venta,
                'id_ct_productos'      => 'COVID',
                'nombre'               => 'COVID',
                'cantidad'             => '1',
                'precio'               => $total_final['total'],
                'descuento_porcentaje' => '',
                'descuento'            => $book->descuento,
                'extendido'            => $total_final['total'],
                'detalle'              => 'COVID',
                'copago'               => '',
                'check_iva'            => '0',
                'porcentaje'           => '0.12',
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }
        if (!is_null($book->fee)) {
            $total_final = $this->indentify_valor($book, 1);
            $detalle     = [
                'id_ct_ventas'         => $id_venta,
                'id_ct_productos'      => 'COMISION',
                'nombre'               => 'COMISION',
                'cantidad'             => '1',
                'precio'               => $total_final['comision'],
                'descuento_porcentaje' => '',
                'descuento'            => $book->descuento,
                'extendido'            => $total_final['total'],
                'detalle'              => 'COMISION',
                'copago'               => '',
                'check_iva'            => '0',
                'porcentaje'           => '0.12',
                'ip_creacion'          => $ip_cliente,
                'ip_modificacion'      => $ip_cliente,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
            ];
            Ct_detalle_venta::create($detalle);
        }
        //retenciones
        /*
        if(!is_null($book->retencion)){
        $rete= $this->retencionv($book,$idusuario,$ip_cliente,$numero_factura,$id_venta);
        }*/
        //comprobante de ingreso and values with paid method
        if (!is_null($book->efectivo)) {
            $numero_factura = str_pad($nfactura, 9, "0", STR_PAD_LEFT);
            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $book->efectivo,
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $numero_factura,
                'valor'           => $book->efectivo,
                'id_empresa'      => '0993075000001',
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            //1.01.02.05.01
            //TRAER PAGOS
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'haber'               => $book->efectivo,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $book->efectivo,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $book->fecha,
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => '0993075000001',
                'total_ingreso'       => $book->efectivo,
                'id_cliente'          => $book->cedula,
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => '',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante    = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            $identificar_pagos = $this->identificar_pagos($book);

            Ct_Detalle_Pago_Ingreso::create([
                'id_comprobante'  => $id_comprobante,
                'fecha'           => $book->fecha,
                'numero'          => '',
                'id_tipo_tarjeta' => '',
                'id_tipo'         => '1',
                'total'           => $book->efectivo,
                'cuenta'          => '',
                'girador'         => $book->paciente,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->efectivo, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'    => $id_comprobante,
                'fecha'             => $book->fecha,
                'observaciones'     => "Cancela FV : " . $nfactura,
                'id_factura'        => $id_venta, ////$consulta_venta->id,
                'secuencia_factura' => $nfactura, //$request['numero'.$i],
                'total_factura'     => $book->total, //$request['saldo_a'.$i],
                'total'             => $book->efectivo, //$request['abono_a'.$i],
                'estado'            => '1',
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
        } elseif (!is_null($book->tcredito)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $tots = $this->indentify_valor($book, 2);
            if (!is_null($book->comision)) {
                $tedua          = $book->tcredito + $book->comision;
                $numero_factura = str_pad($nfactura, 9, "0", STR_PAD_LEFT);
                $input_cabecera = [
                    'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'fecha_asiento'   => $book->fecha,
                    'fact_numero'     => $numero_factura,
                    'valor'           => $tedua,
                    'id_empresa'      => '0993075000001',
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                //1.01.02.05.01
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $desc_cuenta->id,
                    'descripcion'         => $desc_cuenta->nombre,
                    'fecha'               => $book->fecha,
                    'haber'               => $tedua,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);

                $input_comprobante = [
                    'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'estado'              => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha'               => $book->fecha,
                    'secuencia'           => $nfactura,
                    'divisas'             => '1',
                    'id_empresa'          => '0993075000001',
                    'total_ingreso'       => $tedua,
                    'id_cliente'          => $book->cedula,
                    'autollenar'          => "Cancela FV : " . $nfactura,
                    'id_vendedor'         => '',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);

                $identificar_pagos = $this->identificar_pagos($book);
                //type with decide the moment to save paid method
                if (!is_null($book->tipo)) {
                    if ($book->tipo == 'CREDITO') {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'  => $id_comprobante,
                            'fecha'           => $book->fecha,
                            'numero'          => $book->referencia2,
                            'id_tipo_tarjeta' => $identificar_pagos,
                            'id_tipo'         => '4',
                            'total'           => $tedua,
                            'cuenta'          => $book->lote,
                            'girador'         => $book->paciente,
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    } else {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'  => $id_comprobante,
                            'fecha'           => $book->fecha,
                            'numero'          => $book->referencia2,
                            'id_tipo_tarjeta' => $identificar_pagos,
                            'id_tipo'         => '6',
                            'total'           => $tedua,
                            'cuenta'          => $book->lote,
                            'girador'         => $book->paciente,
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    }
                }

                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $book->fecha,
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $id_venta, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $book->total, //$request['saldo_a'.$i],
                    'total'             => $tedua, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);

                $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $desc_cuenta->id,
                    'descripcion'         => $desc_cuenta->nombre,
                    'fecha'               => $book->fecha,
                    'debe'                => $tedua, //$nuevo_saldof,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            } else {
                $tedua          = $book->tcredito;
                $numero_factura = str_pad($nfactura, 10, "0", STR_PAD_LEFT);
                $input_cabecera = [
                    'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'fecha_asiento'   => $book->fecha,
                    'fact_numero'     => $numero_factura,
                    'valor'           => $book->efectivo,
                    'id_empresa'      => '0993075000001',
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                //1.01.02.05.01
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $desc_cuenta->id,
                    'descripcion'         => $desc_cuenta->nombre,
                    'fecha'               => $book->fecha,
                    'haber'               => $tedua,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);

                $input_comprobante = [
                    'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $tedua,
                    'estado'              => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha'               => $book->fecha,
                    'secuencia'           => $nfactura,
                    'divisas'             => '1',
                    'id_empresa'          => '0993075000001',
                    'total_ingreso'       => $tedua,
                    'id_cliente'          => $book->cedula,
                    'autollenar'          => "Cancela FV : " . $nfactura,
                    'id_vendedor'         => '',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                $id_comprobante    = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
                $tedua             = $book->tcredito + $book->comision;
                $identificar_pagos = $this->identificar_pagos($book);
                //identify paid method
                if ($book->tipo == 'CREDITO') {
                    Ct_Detalle_Pago_Ingreso::create([
                        'id_comprobante'  => $id_comprobante,
                        'fecha'           => $book->fecha,
                        'numero'          => $book->referencia2,
                        'id_banco'        => $identificar_pagos,
                        'id_tipo'         => '4',
                        'total'           => $tedua,
                        'cuenta'          => $book->lote,
                        'girador'         => $book->paciente,
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                } else if ($book->tipo == 'DEBITO') {
                    Ct_Detalle_Pago_Ingreso::create([
                        'id_comprobante'  => $id_comprobante,
                        'fecha'           => $book->fecha,
                        'numero'          => $book->referencia2,
                        'id_tipo_tarjeta' => $identificar_pagos,
                        'id_tipo'         => '6',
                        'total'           => $tedua,
                        'cuenta'          => $book->lote,
                        'girador'         => $book->paciente,
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }

                $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
                $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

                //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();

                //aqui me quede
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.01.1.01',
                    'id_plan_cuenta'      => $desc_cuenta->id,
                    'descripcion'         => $desc_cuenta->nombre,
                    'fecha'               => $book->fecha,
                    'debe'                => $tedua, //$nuevo_saldof,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $book->fecha,
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $id_venta, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $book->total, //$request['saldo_a'.$i],
                    'total'             => $tedua, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
        } elseif (!is_null($book->transfdep)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $tots           = $this->indentify_valor($book, 4);
            $numero_factura = str_pad($nfactura, 10, "0", STR_PAD_LEFT);
            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $book->transfdep,
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $numero_factura,
                'valor'           => $book->transfdep,
                'id_empresa'      => '0993075000001',
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            //1.01.02.05.01
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');

            //$desc_cuenta         = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta         = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'haber'               => $book->transfdep,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $book->transfdep,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $book->fecha,
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => '0993075000001',
                'total_ingreso'       => $book->transfdep,
                'id_cliente'          => $book->cedula,
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => '',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante    = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            $identificar_pagos = $this->identificar_pagos($book);

            Ct_Detalle_Pago_Ingreso::create([
                'id_comprobante'  => $id_comprobante,
                'fecha'           => $book->fecha,
                'numero'          => '',
                'id_tipo_tarjeta' => '',
                'id_tipo'         => '5',
                'total'           => $book->transfdep,
                'cuenta'          => '',
                'girador'         => $book->paciente,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.01.1.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->transfdep, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'    => $id_comprobante,
                'fecha'             => $book->fecha,
                'observaciones'     => "Cancela FV : " . $nfactura,
                'id_factura'        => $id_venta, ////$consulta_venta->id,
                'secuencia_factura' => $nfactura, //$request['numero'.$i],
                'total_factura'     => $book->total, //$request['saldo_a'.$i],
                'total'             => $book->transfdep, //$request['abono_a'.$i],
                'estado'            => '1',
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
        } elseif (!is_null($book->cheque)) {
            //1: EFECTIVO 2: TARJETA DE CREDITO 3: CHEQUE 4: TRANSFERENCIA
            $tots           = $this->indentify_valor($book, 3);
            $numero_factura = str_pad($nfactura, 10, "0", STR_PAD_LEFT);
            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $book->cheque,
                'fecha_asiento'   => $book->fecha,
                'fact_numero'     => $numero_factura,
                'valor'           => $book->cheque,
                'id_empresa'      => '0993075000001',
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                //'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'haber'               => $book->cheque,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $book->cheque,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $book->fecha,
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => '0993075000001',
                'total_ingreso'       => $book->transfdep,
                'id_cliente'          => $book->cedula,
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => '',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante    = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            $identificar_pagos = $this->identificar_pagos($book);

            Ct_Detalle_Pago_Ingreso::create([
                'id_comprobante'  => $id_comprobante,
                'fecha'           => $book->fecha,
                'numero'          => '',
                'id_tipo_tarjeta' => '',
                'id_tipo'         => '2',
                'total'           => $book->cheque,
                'cuenta'          => '',
                'girador'         => $book->paciente,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            //$desc_cuenta = Plan_Cuentas::where('id', '1.01.01.1.01')->first();

            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $book->fecha,
                'debe'                => $book->cheque, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'    => $id_comprobante,
                'fecha'             => $book->fecha,
                'observaciones'     => "Cancela FV : " . $nfactura,
                'id_factura'        => $id_venta, ////$consulta_venta->id,
                'secuencia_factura' => $nfactura, //$request['numero'.$i],
                'total_factura'     => $book->total, //$request['saldo_a'.$i],
                'total'             => $book->cheque, //$request['abono_a'.$i],
                'estado'            => '1',
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
            ]);
        }
    }
    public function retencionv($book, $idusuario, $ip_cliente, $numero_factura, $id_venta)
    {
        //function retencion
        $cabeceraa = [
            'observacion'     => 'Retencion: ' . $numero_factura . " ",
            'fecha_asiento'   => $book->fecha,
            'fact_numero'     => $numero_factura,
            'valor'           => $book->retencion,
            'estado'          => '2',
            'id_empresa'      => '0993075000001',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
        // $id_asiento_cabecera      = 1;
        $input = [
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_cliente'          => $book->cedula,
            'autorizacion'        => '',
            'id_factura'          => $id_venta,
            'nro_comprobante'     => $numero_factura,
            'valor_fuente'        => $book->retencion,
            'valor_iva'           => '0',
            'id_empresa'          => '0993075000001',
            'tipo'                => '1',
            'id_tipo'             => '15',
            'descripcion'         => 'retencion ventas',
            'estado'              => '1',
            'total'               => $book->total,
            'secuencia'           => $numero_factura,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];
        $id_retenciones = Ct_Cliente_Retencion::insertGetId($input);
        Ct_Detalle_Cliente_Retencion::create([
            'id_cliente_retencion' => $id_retenciones,
            'observacion'          => 'RETENCION FUENTE',
            'numerorefs'           => $numero_factura,
            'fechaauto'            => $book->fecha,
            'id_tipo'              => '15',
            'tipo'                 => 'RENTA',
            'id_porcentaje'        => '15',
            'codigo'               => '3440',
            'base_imponible'       => $book->total,
            'estado'               => '1',
            'totales'              => $book->retencion,
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
        ]);
        //query ventas
        $ventas = Ct_ventas::find($id_venta);
        if (!is_null($ventas)) {
            $totalf                 = $ventas->valor_contable - $book->retencion;
            $ventas->estado_pago    = 2;
            $ventas->valor_contable = $totalf;
            $ventas->save();
        }

        $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
        //En guayaquil esta 1.01.02.05.01 cuenta x cobrar clientes comerciales
        $consulta_plan = Ct_Configuraciones::where('id', 5)->first();

        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $consulta_plan->id_plan,
            'descripcion'         => $consulta_plan->cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
            'fecha'               => $book->fecha,
            'debe'                => '0',
            'haber'               => $book->retencion,
            'estado'              => '1',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $idusuario,
            'ip_modificacion'     => $idusuario,
        ]);

        $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
        //$consulta_plan_debe = Plan_Cuentas::find('1.01.05.02.12');
        $consulta_plan_debe = Plan_Cuentas::find($id_plan_confg);

        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
            'descripcion'         => $consulta_plan_debe->nombre,
            'fecha'               => $book->fecha,
            'debe'                => $book->retencion,
            'haber'               => '0',
            'estado'              => '1',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $idusuario,
            'ip_modificacion'     => $idusuario,
        ]);
    }
    public function indentify_valor($book, $referencia)
    {
        $data                          = [];
        $data['total_efectivo']        = 0;
        $data['total_tarjeta_credito'] = 0;
        $data['total_cheque']          = 0;
        $data['total_transferencia']   = 0;
        $data['comision']              = 0;
        if ($referencia == 1) {
            $data['total_efectivo'] = floatval($book->efectivo);
        } else if ($referencia == 2) {
            $data['total_tarjeta_credito'] = floatval($book->tcredito);
        } else if ($referencia == 3) {
            $data['total_cheque'] = floatval($book->cheque);
        } else if ($referencia == 4) {
            $data['total_transferencia'] = floatval($book->transfdep);
        }
        $data['total'] = floatval($book->tcredito) + floatval($book->transfdep) + floatval($book->cheque);
        if ($book->comision != null) {
            $data['comision'] = floatval($book->comision);
        }
        return $data;
    }
    //use this for the finally way to process fact_omni and conglomerada when the products have the same code, build to the array.
    public function getArrayPreview(Request $request, $tipo = "")
    {
        $idusuario = Auth::user()->id;
        if (!is_null($request)) {
            //dd($request->all());
            $group_productos = [];
            $empresa         = Empresa::find($request->session()->get('id_empresa'));
            $arr             = [];
            $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal      = $cod_sucurs->codigo_sucursal;
            $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja          = $cod_caj->codigo_caja;
            $proced          = $request['procedimiento'];
            $numero          = $request['numero'];
            $num_comprobante = "";

            //if($idusuario != "0957258056"){
            if (!is_null($numero)) {
                $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                $verifica_num_factura = Ct_ventas::where('id_empresa', $empresa->id)
                    ->where('nro_comprobante', $num_comprobante)
                    ->where('tipo', '<>', 'VEN-FACT')
                    ->where('estado', '!=', '0')->first();
                if (!is_null($verifica_num_factura)) {
                    return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                } else {
                    $nfactura = $numero;
                }
            } else {
                $nfactura        = $this->obtener_numero_factura($empresa->id, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
            }
            $cabecera                           = [];
            $cabecera['nro_comprobante']        = $num_comprobante;
            $cabecera['direccion']              = $empresa->direccion;
            $cabecera['nombre']                 = $empresa->nombrecomercial;
            $cabecera['razonsocial']            = $empresa->razonsocial;
            $cabecera['direccion']              = $empresa->direccion;
            $cabecera['logo']                   = $empresa->logo;
            $cabecera['cliente']                = $request->identificacion_cliente;
            $cabecera['nombre_cliente']         = $request->nombre_cliente;
            $cabecera['mail_cliente']           = $request->mail_cliente;
            $cabecera['fecha']                  = $request->fecha_asiento;
            $cabecera['autorizacion']           = $request->numero_autorizacion;
            $cabecera['emailinfo']              = $request->emailinfo;
            $cabecera['procedimientoinfo']      = $request->procedimientoinfo;
            $cabecera['pacienteinfo']           = $request->pacienteinfo;
            $cabecera['seguroinfo']             = $request->segurosinfo;
            $cabecera['direccioninfo']          = $request->direccioninfo;
            $cabecera['fechaprocedimientoinfo'] = $request->fechaprocedimientoinfo;
            $cabecera['id']                     = $empresa->id;
            $cabecera['total']                  = $request->total1;
            $cabecera['tipo_factura']           = $request->tipo_factura;
            $cabecera['subtotal_0']             = $request->subtotal_01;
            $cabecera['subtotal_12']            = $request->subtotal_121;
            $cabecera['impuesto']               = $request->tarifa_iva1;
            $cabecera['descuento']              = $request->descuento1;
            $cabecera['subtotal']               = number_format($request->subtotal_01 + $request->subtotal12, 2, '.', '');
            $err                                = [];
            $arr_total                          = [];
            if (!empty($request->input("nombre"))) {
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $precio_neto = "";
                        if (isset($request->input("precioneto")[$i])) {
                            $precio_neto = $request->input("precioneto")[$i];
                        }
                        $nombre = "";
                        if (isset($request->input("nombre")[$i])) {
                            $nombre = $request->input("nombre")[$i];
                        }
                        $cantidad = 0;
                        if (isset($request->input("cantidad")[$i])) {
                            $cantidad = $request->input("cantidad")[$i];
                        }
                        $codigo = "";
                        if (isset($request->input("codigo")[$i])) {
                            $codigo = $request->input("codigo")[$i];
                        }
                        $nombre_procedimiento = "";
                        if (isset($request->input("id_principal")[$i])) {
                            $nombre_procedimiento = $request->input("id_principal")[$i];
                        }
                        $fecha_procedimiento = "";
                        if (isset($request->input("fecha_procedimiento")[$i])) {
                            $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                        }
                        $precio = 0;
                        if (isset($request->input("precio")[$i])) {
                            $precio = $request->input("precio")[$i];
                        }
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $iva = 0;
                        if (isset($request->input("iva")[$i])) {
                            $iva = $request->input("iva")[$i];
                        }
                        $descuento = 0;
                        if (isset($request->input("desc")[$i])) {
                            $descuento = $request->input("desc")[$i];
                        }
                        $descrip_prod = "";
                        if (isset($request->input("descrip_prod")[$i])) {
                            $descrip_prod = $request->input("descrip_prod")[$i];
                        }
                        $id_hc_proc = "";
                        if (isset($request->input("hc_procedimiento")[$i])) {
                            $id_hc_proc = $request->input("hc_procedimiento")[$i];
                        }
                        $precio_neto = floatval($cantidad) * floatval($precio);
                        $arr         = array(
                            'nombre'               => $nombre,
                            'cantidad'             => $cantidad,
                            'codigo'               => $codigo,
                            'nombre_procedimiento' => $nombre_procedimiento,
                            'fecha_procedimiento'  => $fecha_procedimiento,
                            'precio'               => $precio,
                            'id_paciente'          => $paciente,
                            'descpor'              => "0.12",
                            'copago'               => "",
                            'descuento'            => $descuento,
                            'precioneto'           => $precio_neto,
                            'detalle'              => $descrip_prod,
                            'iva'                  => $iva,
                            'id_hc_proc'           => $id_hc_proc,
                            'obs_pac'              => $request['ss'],
                        );
                        //$arrayFinally= $this->getProductPrices($arr);

                        array_push($arr_total, $arr);
                    }
                }
            }
            $id_seguro       = $request->id_seguro;
            $ambulatorio     = $request->amBu;
            $data            = $this->getProductPrices($arr_total, $tipo, $id_seguro, $ambulatorio);
            $group_productos = $this->group_by("id_paciente", $data);
            if ($request->tipo_factura == 1) {
                $group_productos = $this->group_by("id_hc_proc", $arr_total);
            }
            if ($request->tipo_factura == 3) {
                $group_productos = $this->group_by("id_paciente", $arr_total);
            }
            if ($ambulatorio == 1) {
                $group_productos = $this->group_by("id_paciente", $arr_total);
            }
            return [$group_productos, $cabecera];
        } else {

            $ds = [];
            return $ds;
        }
    }
    //created by A. Chilan at 20 January 2021
    //use this array function to send invoice with process Faustos's API
    //only work with use
    public function getProductPrices($data, $tipo = "", $id_seguro = "", $ambulatorio = "")
    {
        //if u want sum values and group by values
        $groups = array();
        foreach ($data as $ks => $item) {
            //dd($item);
            $key = $item['codigo'];
            //only when key is distinc to null
            if (!is_null($key)) {
                if (!is_null($item['nombre'])) {
                    if ($tipo == "conglomerada") {
                        //esta son las combinaciones para facturacion humana  Procedimientos Ambulatorios de Gastroenterologia
                        if ($id_seguro == 4) {
                            if ($ambulatorio == 1) {
                                $key = $item['codigo'];
                            } else {
                                $getParameters = ParametersConglomerada::getHumana($key);
                                if ($getParameters != false) {
                                    $key = $getParameters;
                                }
                            }
                        } else {
                            if ($ambulatorio == 1) {

                                $key = $item['codigo'];
                            }
                        }
                    }
                    if (!array_key_exists($key, $groups)) {
                        $codigo = $item['codigo'];
                        $nombre = $item['nombre'];
                        if ($tipo == "conglomerada") {
                            //esta son las combinaciones para facturacion humana  Procedimientos Ambulatorios de Gastroenterologia
                            if ($id_seguro == 4) {
                                if ($ambulatorio == 1) {
                                    $codigo = $item['codigo'];
                                } else {
                                    $getParameters = ParametersConglomerada::getHumana($codigo);
                                    //dd($getParameters);
                                    if ($getParameters != false) {
                                        $codigo = $getParameters;
                                    }
                                }
                            } else {
                                if ($ambulatorio == 1) {
                                    $codigo = $item['codigo'];
                                }
                            }
                        }
                        $nombre       = Ct_productos::where('codigo', $codigo)->first();
                        $nombre       = $nombre->descripcion; //no hay valicacion aqui
                        $groups[$key] = array(
                            'codigo'               => $codigo,
                            'nombre'               => $nombre,
                            'fecha_procedimiento'  => $item['fecha_procedimiento'],
                            'nombre_procedimiento' => $item['nombre_procedimiento'],
                            'id_paciente'          => $item['id_paciente'],
                            'precio'               => $item['precio'],
                            'precioneto'           => $item['precioneto'],
                            'cantidad'             => $item['cantidad'],
                            'descuento'            => $item['descuento'],
                            'detalle'              => $item['detalle'],
                        );
                    } else {
                        if ($tipo == "conglomerada") {
                            /*need to group by procedures endoscopia+colono+cpre+eco, en humana es  41253 Procedimientos Ambulatorios de Gastroenterologia - ecografias ,en humana es  46877Ultrasonografia Diagnostica*/
                            //dd($groups[$key]['precio'],$item['precio']);
                            //no group procedures
                            $groups[$key]['precio'] = $groups[$key]['precio'] + $item['precio']; //no working date 26 January 2021
                        } else {
                            $groups[$key]['cantidad'] = $groups[$key]['cantidad'] + $item['cantidad'];
                        }
                        $groups[$key]['detalle']    = $groups[$key]['detalle'];
                        $groups[$key]['precioneto'] = $groups[$key]['precioneto'] + $item['precioneto'];
                        $groups[$key]['descuento']  = $groups[$key]['descuento'] + $item['descuento'];
                    }
                }
            }
        }
        //dd($groups);
        return $groups;
    }
    public function getLoader(Request $request)
    {
        $arPor = $request['getLoader'];
        if (!is_null($arPor)) {
            $arPor = json_decode($request['getLoader']);
            if (count($arPor) > 0) {
                $fecha = "";
                if ($arPor->fecha != "") {
                    $fecha = $arPor->fecha;
                }
                $comprobante = "";
                if ($arPor->comprobante != "") {
                    $comprobante = $arPor->comprobante;
                }
                $eq = "";
                if ($arPor->eqs != "") {
                    $eq = $arPor->eqs;
                }
                $cs = "";
                if ($arPor->cs != "") {
                    $cs = $arPor->cs;
                }
                $ep = "";
                if ($arPor->ep != "") {
                    $ep = $arPor->ep;
                }
                $px = "";
                if ($arPor->px != "") {
                    $px = $arPor->px;
                }
                $fff = "";
                if ($arPor->fff != null) {
                    $fff = $arPor->fff;
                }
            }
            return response()->json("ok");
        }
        return response()->json("error");
    }
    public function group_by($key, $data)
    {
        $result = array();
        //result data or group by
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }
    public function modal_preview(Request $request)
    {
        $arrayGroup = $this->getArrayPreview($request, "");
        return view('contable/ventas/modal_preview', ['arrayGroup' => $arrayGroup]);
    }
    public function modal_preview_c(Request $request)
    {
        $tipo_servicio = $request->tipo_servicio;
        $arrayGroup    = $this->getArrayPreview($request, "conglomerada");
        return view('contable/ventas/modal_previewc', ['arrayGroup' => $arrayGroup]);
    }
    public function pdf_nuevo($id = "1", Request $request)
    {
        $vistaurl = "contable.ventas.pdf_nuevo";

        if (is_null($request->getCabecera)) {
            $id       = $request['id'];
            $detalles = DB::table('ct_detalle_venta_c as c')->join('ct_productos as productos', 'productos.codigo', 'c.id_ct_productos')->join('ct_ventas as v', 'v.id', 'c.id_ct_ventas')->join('paciente as p', 'c.id_paciente', 'p.id')->where('v.id', $id)->select(DB::raw('SUM(c.extendido) as sumatoria'), 'productos.nombre as nombre_producto', 'productos.codigo as codigo_producto', 'p.nombre1 as nombre1', 'p.apellido1 as apellido')->get();
            $valid    = 0;
            $ventas   = Ct_ventas::find($id);
            $view     = \View::make($vistaurl, compact('detalles', 'emp', 'recaud', 'cliente', 'pacient', 'ventas', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret', 'valid'))->render();
            //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('PreImpresoVentas-' . '.pdf');
        } else {
            //dd("a");
            //dd($request->all());
            $detalles = json_decode($request['arrayPreview']);
            $ventas   = json_decode($request['getCabecera']);
            //dd($ventas->descuento);
            $valid = 1;
            $view  = \View::make($vistaurl, compact('detalles', 'emp', 'recaud', 'cliente', 'pacient', 'ventas', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret', 'valid'))->render();

            //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream('PreImpresoVentas-' . '.pdf');
        }
    }
    public function pdf_nuevo2($id = "1", Request $request)
    {
        $vistaurl = "contable.ventas.pdf_nuevo2";
        $detalles = json_decode($request['arrayPreview']);
        $ventas   = json_decode($request['getCabecera']);
        $valid    = 1;
        $view     = \View::make($vistaurl, compact('detalles', 'emp', 'recaud', 'cliente', 'pacient', 'ventas', 'detalle_venfaco', 'ct_for_pag', 'ct_val_ret', 'valid'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('PreImpresoVentas-' . '.pdf');
    }
    public function getPrices(Request $request)
    {
        $codigo_producto = $request->codigo_producto;
        $id_cliente      = $request->id_cliente;
        if (!is_null($codigo_producto) && !is_null($id_cliente)) {
            $cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();
            $clase   = $cliente->clase;
            if ($clase != null) {
                $producto = PrecioProducto::where('codigo_producto', $codigo_producto)->where('nivel', $clase)->first();
                if (!is_null($producto)) {
                    return response()->json($producto->precio);
                }
                return response()->json("vacio");
            } else {
                return response()->json("no tiene clase");
            }
        }
        return response()->json("error");
    }
    public function getReportUses(Request $request)
    {
        //usar fecha desde y fecha hasta
        $fecha_desde     = date('Y-m-d', strtotime($request->fecha));
        $fecha_hasta     = date('Y-m-d', strtotime($request->fecha_hasta));
        $nombre_paciente = $request->nombres;
        //dd($request->all());
        //dd($fecha_desde);
        $group_productos = array();
        $id_empresa      = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $detalles = Ct_Detalle_Venta_Omni::whereBetween('fecha_procedimiento', [$fecha_desde, $fecha_hasta])->join('ct_factura_omni as ctx', 'ctx.id', 'ct_detalle_venta_omni.id_omni')->where('ctx.tipo_factura', 2)->where('ctx.id_empresa', $id_empresa);
        $detalles = $detalles->get()->toArray();
        //dd($detalles);
        $group_productos = $this->group_by("id_ct_productos", $detalles);
        $vistaurl        = "contable.ventas.pdf_usos_equipos";
        $view            = \View::make($vistaurl, compact('detalles', 'omni', 'group_productos', 'fecha_desde'))->render();
        $pdf             = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('usos-' . '.pdf');
    }
    public function getReportUsesExcel(Request $request)
    {
        $fecha_desde = $request->fecha;
        $id_empresa  = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $fecha_hasta = $request->fecha_hasta;
        $detalles    = Ct_Detalle_Venta_Omni::whereBetween('fecha_procedimiento', [$fecha_desde, $fecha_hasta])->join('ct_factura_omni as ctx', 'ctx.id', 'ct_detalle_venta_omni.id_omni')->where('ctx.tipo_factura', 2)->where('ctx.id_empresa', $id_empresa);

        $detalles = $detalles->get()->toArray();

        $group_productos = $this->group_by("id_ct_productos", $detalles);
        $empresa         = Empresa::where('prioridad', '1')->get();
        $omni            = array();
        Excel::create('Informe usos ' . $fecha_desde . ' a ' . $fecha_hasta, function ($excel) use ($detalles, $group_productos, $empresa, $omni, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe usos', function ($sheet) use ($detalles, $empresa, $group_productos, $omni, $fecha_desde, $fecha_hasta) {
                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('A5:I5');

                $sheet->cell('A5', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setValue($empresa->razonsocial);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $i = 6;

                foreach ($group_productos as $key => $values) {
                    //dd($values);
                    $countValues = count($values);
                    $conter      = 0;
                    if ($conter <= $countValues) {
                        $mes = ["ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"][date('m', strtotime($values[$conter]['fecha_procedimiento'])) - 1];
                        //only u can change this method similar downside code beacuse only show in a one case

                        $contador = 1;
                        foreach ($values as $value) {
                            $verified = Ct_Factura_Omni::find($value['id_omni']);
                            if ($verified->tipo_factura == 2) {
                                if ($contador == 1) {
                                    $sheet->cell('D' . $i, function ($cell) use ($mes) {
                                        // manipulate the cel
                                        // $this->setSangria($cont, $cell);
                                        $cell->setFontWeight('bold');
                                        $cell->setFontSize(16);
                                        $cell->setFontColor("#FF4F29");
                                        $cell->setValue($mes);
                                        $cell->setValignment('center');

                                        //   $cell->setBorder('', '', 'thin','');
                                    });
                                    $i++;
                                    $nombre = Ct_productos::where('codigo', $key)->first();
                                    $sheet->cell('D' . $i, function ($cell) use ($nombre) {
                                        // manipulate the cel
                                        // $this->setSangria($cont, $cell);
                                        $cell->setFontWeight('bold');
                                        $cell->setFontSize(16);
                                        $cell->setFontColor("#FF4F29");
                                        $cell->setValue($nombre->nombre);
                                        $cell->setValignment('center');

                                        //   $cell->setBorder('', '', 'thin','');
                                    });
                                    $i++;
                                }
                                $paciente = Paciente::find($value['id_paciente']);
                                $sheet->cell('A' . $i, function ($cell) use ($contador) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($contador);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('B' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue("");
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('C' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($value['fecha_procedimiento']);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('D' . $i, function ($cell) use ($paciente) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('E' . $i, function ($cell) use ($paciente) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($paciente->seguro->nombre);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('F' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue($value['nombre_principal']);
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $sheet->cell('G' . $i, function ($cell) {
                                    // manipulate the cel
                                    // $this->setSangria($cont, $cell);

                                    $cell->setFontSize(16);
                                    $cell->setValue("");
                                    $cell->setValignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    //   $cell->setBorder('', '', 'thin','');
                                });
                                $i++;
                                $contador++;
                            }
                        }
                    }
                    $conter++;
                }
            });
        })->export('xlsx');
    }
    public function getHonorarios(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $fecha_as        = Date('Y-m-d H:i:s');
        $id_empresa      = $request->session()->get('id_empresa');
        $llevaOrden      = false;
        $validate        = $request['validate'];
        $cod_sucurs      = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $cod_caj         = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
        $proced          = $request['procedimiento'];
        $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
        $text            = 'Fact #' . ':' . $num_comprobante . '-' . $proced;
        $msj             = "no    o";

        $id_paciente = $request['identificacion_paciente'];
        if (is_null($request['identificacion_paciente'])) {
            $id_paciente = '9999999999';
        }
        //fix contranstrain
        $pacis = Paciente::find($id_paciente);
        if (is_null($pacis)) {
            $id_paciente = '9999999999';
        }
        DB::beginTransaction();
        $msj = "no    o";
        try {
            $input_cabecera = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $factura_venta       = [
                'sucursal'            => $cod_sucurs->codigo_sucursal,
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'],
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $id_paciente,
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['totalc'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['total1'],
                'ip_creacion'         => "OMNI",
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;
            $arr_id_hc      = [];
            $arr_activos    = [];
            $cont           = 0;
            $arr_obs        = [];
            if (!empty($request->input("veractivo"))) {
                for ($i = 0; $i < count($request->input("veractivo")); $i++) {
                    if ($request->input("veractivo")[$i] == 1) {
                        $ar = $request->input("hc_procedimiento")[$i];
                        array_push($arr_activos, $ar);
                        $aobs = [
                            'procedimiento' => $request->input("hc_procedimiento")[$i],
                            'obs'           => $request->input("nom_paciente")[$i] . " - " . $request->input("obs_paciente")[$i],
                        ];
                        array_push($arr_obs, $aobs);
                    }
                }
            }

            $observacion = "";
            if (!empty($request->input("nombre"))) {
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $precio_neto = "";
                        if (isset($request->input("precioneto")[$i])) {
                            $precio_neto = $request->input("precioneto")[$i];
                        }
                        $nombre = "";
                        if (isset($request->input("nombre")[$i])) {
                            $nombre = $request->input("nombre")[$i];
                        }
                        $cantidad = 0;
                        if (isset($request->input("cantidad")[$i])) {
                            $cantidad = $request->input("cantidad")[$i];
                        }
                        $codigo = "";
                        if (isset($request->input("codigo")[$i])) {
                            $codigo = $request->input("codigo")[$i];
                        }
                        $nombre_procedimiento = "";
                        if (isset($request->input("id_principal")[$i])) {
                            $nombre_procedimiento = $request->input("id_principal")[$i];
                        }
                        $fecha_procedimiento = "";
                        if (isset($request->input("fecha_procedimiento")[$i])) {
                            $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                        }
                        $precio = 0;
                        if (isset($request->input("precio")[$i])) {
                            $precio = $request->input("precio")[$i];
                        }
                        $paciente = "";
                        if (isset($request->input("paciente")[$i])) {
                            $paciente = $request->input("paciente")[$i];
                        }
                        $iva = "";
                        if (isset($request->input("iva")[$i])) {
                            $iva = $request->input("iva")[$i];
                        }
                        $descuento = "";
                        if (isset($request->input("desc")[$i])) {
                            $descuento = $request->input("desc")[$i];
                        }
                        $descrip_prod = "";
                        if (isset($request->input("descrip_prod")[$i])) {
                            $descrip_prod = $request->input("descrip_prod")[$i];
                        }
                        $copago = "";
                        if (isset($request->input("copago")[$i])) {
                            $copago = $request->input("copago")[$i];
                        }
                        $id_hc_proc = "";
                        if (isset($request->input("id_hc_proc")[$i])) {
                            $id_hc_proc = $request->input("id_hc_proc")[$i];
                        }
                        $id_agenda = "";
                        if (isset($request->input("id_agenda")[$i])) {
                            $id_agenda = $request->input("id_agenda")[$i];
                        }
                        $hc_procedimiento = "";
                        if (isset($request->input("hc_procedimiento")[$i])) {
                            $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                        }
                        $precioneto = $cantidad * $precio;
                        $arr        = [
                            'nombre'     => $nombre,
                            'cantidad'   => $cantidad,
                            'codigo'     => $codigo,
                            'precio'     => $precio,
                            'descpor'    => "0.12",
                            'copago'     => $copago,
                            'descuento'  => $descuento,
                            'precioneto' => $precio_neto,
                            'detalle'    => $descrip_prod,
                            'iva'        => $iva,
                            'id_hc_proc' => $hc_procedimiento,
                            'obs_pac'    => $observacion,
                        ];

                        array_push($arr_total, $arr);
                    }
                }
            }

            // $ids_consultas = json_encode($request->input('id_hc_consulta'));

            foreach ($arr_total as $valor) {
                if ($valor['copago'] > 0) {
                    //registra orden de venta
                    $llevaOrden = true;
                }
                //dd($valor);

            }

            $val_tol = $request['total1'];

            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan_confg  = LogConfig::busqueda('4.1.01.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            //****REGISTRO FORMAS DE PAGO*** --activo falta****
            $arr_p = [];
            if (!empty($request->input("id_tip_pago"))) {
                for ($i = 0; $i < count($request->input("id_tip_pago")); $i++) {
                    if ($request->input("valor")[$i] != "" || $request->input("valor_base")[$i] != null) {
                        $arr_pagos = [
                            'id_tip_pago'    => $request->input("id_tip_pago")[$i],
                            'fecha_pago'     => $request->input("fecha_pago")[$i],
                            'numero_pago'    => $request->input("numero_pago")[$i],
                            'id_banco_pago'  => $request->input("id_banco_pago")[$i],
                            'id_cuenta_pago' => $request->input("id_cuenta_pago")[$i],
                            'giradoa'        => $request->input("giradoa")[$i],
                            'valor'          => $request->input("valor")[$i],
                            'valor_base'     => $request->input("valor_base")[$i],
                        ];
                        array_push($arr_p, $arr_pagos);
                    }
                }
            }

            foreach ($arr_p as $valor) {
                Ct_Forma_Pago::create([
                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                    'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                    'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                    'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                    'valor'           => $valor['valor'], //$request['valor'.$i],
                    'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }

            //agregar comprobantes de ingreso
            $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            $arr_agendas = [];
            if ($request->tipo_factura == 3) {
                if ($llevaOrden) {
                    //dd("a");
                    $orden_id = $this->ordenVentaConglomerada($id_venta, $request);
                    //dd($orden_id);
                    $id_ct_ventas = [

                        'id_ct_venta' => $id_venta,

                    ];

                    Ct_ven_orden::where('id', $orden_id)->update($id_ct_ventas);
                } else {
                    $orden_id = 0;
                }
                $arr_agendas = [];
                if (!empty($request->input("hc_procedimiento"))) {
                    for ($i = 0; $i < count($request->input("hc_procedimiento")); $i++) {
                        if ($request->input("hc_procedimiento")[$i] != "" || $request->input("paciente")[$i] != null) {
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $precio_neto = "";
                            if (isset($request->input("precioneto")[$i])) {
                                $precio_neto = $request->input("precioneto")[$i];
                            }
                            $nombre = "";
                            if (isset($request->input("nombre")[$i])) {
                                $nombre = $request->input("nombre")[$i];
                            }
                            $cantidad = 0;
                            if (isset($request->input("cantidad")[$i])) {
                                $cantidad = $request->input("cantidad")[$i];
                            }
                            $codigo = "";
                            if (isset($request->input("codigo")[$i])) {
                                $codigo = $request->input("codigo")[$i];
                            }
                            $nombre_procedimiento = "";
                            if (isset($request->input("id_principal")[$i])) {
                                $nombre_procedimiento = $request->input("id_principal")[$i];
                            }
                            $fecha_procedimiento = "";
                            if (isset($request->input("fecha_procedimiento")[$i])) {
                                $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                            }
                            $precio = 0;
                            if (isset($request->input("precio")[$i])) {
                                $precio = $request->input("precio")[$i];
                            }
                            $paciente = "";
                            if (isset($request->input("paciente")[$i])) {
                                $paciente = $request->input("paciente")[$i];
                            }
                            $iva = "";
                            if (isset($request->input("iva")[$i])) {
                                $iva = $request->input("iva")[$i];
                            }
                            $descuento = "";
                            if (isset($request->input("desc")[$i])) {
                                $descuento = $request->input("desc")[$i];
                            }
                            $descrip_prod = "";
                            if (isset($request->input("descrip_prod")[$i])) {
                                $descrip_prod = $request->input("descrip_prod")[$i];
                            }
                            $id_hc_proc = "";
                            if (isset($request->input("id_hc_proc")[$i])) {
                                $id_hc_proc = $request->input("id_hc_proc")[$i];
                            }
                            $id_agenda = "";
                            if (isset($request->input("id_agenda")[$i])) {
                                $id_agenda = $request->input("id_agenda")[$i];
                            }
                            $hc_procedimiento = "";
                            if (isset($request->input("hc_procedimiento")[$i])) {
                                $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                            }
                            $copago = 0;
                            if (isset($request->input("copago")[$i])) {
                                $copago = $request->input("copago")[$i];
                            }
                            $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento, 'nombre' => $nombre, 'copago' => $copago];
                            array_push($arr_agendas, $arr_ags);
                        }
                    }
                }
                foreach ($arr_agendas as $agenda) {
                    $id_omni = Ct_Factura_Omni::insertGetId([
                        'id_ct_ventas'    => $id_venta,
                        'id_agenda'       => $agenda["id_agenda"],
                        'id_paciente'     => $agenda["id_paciente"],
                        'tipo_factura'    => $request['tipo_factura'],
                        'fecha'           => $agenda['fecha_procedimiento'],
                        'estado'          => "1",
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                    $precio_neto = $agenda['cantidad'] * ($agenda['precio']);
                    Ct_Detalle_Venta_Omni::create([
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $agenda['producto'],
                        'nombre'               => $agenda['producto'],
                        'id_omni'              => $id_omni,
                        'cantidad'             => $agenda['cantidad'],
                        'fecha_procedimiento'  => $agenda['fecha_procedimiento'],
                        'id_agenda'            => $agenda['id_agenda'],
                        'nombre_principal'     => $agenda['nombre_principal'],
                        'id_hc_procedimiento'  => $agenda['hc_procedimiento'],
                        'precio'               => $agenda['precio'],
                        'descuento_porcentaje' => $request['iva_real'],
                        'extendido'            => $precio_neto,
                        'id_paciente'          => $agenda['id_paciente'],
                        'detalle'              => $agenda['descripcion'],
                        'check_iva'            => $agenda['check_iva'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ]);
                    $detalle = [
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $agenda['producto'],
                        'nombre'               => $agenda['producto'],
                        'cantidad'             => $agenda['cantidad'],
                        'precio'               => $agenda['precio'],
                        'descuento_porcentaje' => "0.12",
                        'descuento'            => "0",
                        'estado'               => '1',
                        'extendido'            => $precio_neto,
                        'detalle'              => $agenda['descripcion'],
                        'copago'               => $agenda['copago'],
                        'check_iva'            => $agenda['check_iva'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,

                    ];
                    Ct_detalle_venta::create($detalle);
                }
                $msj = "";
            } else {
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'prd_val' => $request->input("prd_val"), 'request' => $request, 'llevaOrden' => $llevaOrden, 'arr_agendas' => $arr_agendas, 'kardex' => $msj];

        return $request;
    }
    public function ordenVentaConglomerada($id_v, Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = Date('Y-m-d H:i:s');
        $id_empresa = $request->session()->get('id_empresa');

        $c_sucursal      = 0;
        $c_caja          = 0;
        $num_comprobante = 0;
        $nfactura        = 0;
        $proced          = $request['procedimiento'];
        $pac             = "";
        if ($request['nombre_paciente'] != "") {
            $pac = " | " . $request['nombre_paciente'];
        }
        $text = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;

        $id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.arr_agendas
        $arr_agendas = [];
        if (!empty($request->input("hc_procedimiento"))) {
            for ($i = 0; $i < count($request->input("hc_procedimiento")); $i++) {
                if ($request->input("hc_procedimiento")[$i] != "" || $request->input("paciente")[$i] != null) {
                    $paciente = "";
                    if (isset($request->input("paciente")[$i])) {
                        $paciente = $request->input("paciente")[$i];
                    }
                    $precio_neto = "";
                    if (isset($request->input("precioneto")[$i])) {
                        $precio_neto = $request->input("precioneto")[$i];
                    }
                    $copago = 0;
                    if (isset($request->input("copago")[$i])) {
                        $copago = $request->input("copago")[$i];
                    }

                    $nombre = "";
                    if (isset($request->input("nombre")[$i])) {
                        $nombre = $request->input("nombre")[$i];
                    }
                    $cantidad = 0;
                    if (isset($request->input("cantidad")[$i])) {
                        $cantidad = $request->input("cantidad")[$i];
                    }
                    $codigo = "";
                    if (isset($request->input("codigo")[$i])) {
                        $codigo = $request->input("codigo")[$i];
                    }
                    $nombre_procedimiento = "";
                    if (isset($request->input("id_principal")[$i])) {
                        $nombre_procedimiento = $request->input("id_principal")[$i];
                    }
                    $fecha_procedimiento = "";
                    if (isset($request->input("fecha_procedimiento")[$i])) {
                        $fecha_procedimiento = $request->input("fecha_procedimiento")[$i];
                    }
                    $precio = 0;
                    if (isset($request->input("precio")[$i])) {
                        $precio = $request->input("precio")[$i];
                    }
                    $paciente = "";
                    if (isset($request->input("paciente")[$i])) {
                        $paciente = $request->input("paciente")[$i];
                    }
                    $iva = "";
                    if (isset($request->input("iva")[$i])) {
                        $iva = $request->input("iva")[$i];
                    }
                    $descuento = "";
                    if (isset($request->input("desc")[$i])) {
                        $descuento = $request->input("desc")[$i];
                    }
                    $descrip_prod = "";
                    if (isset($request->input("descrip_prod")[$i])) {
                        $descrip_prod = $request->input("descrip_prod")[$i];
                    }
                    $id_hc_proc = "";
                    if (isset($request->input("id_hc_proc")[$i])) {
                        $id_hc_proc = $request->input("id_hc_proc")[$i];
                    }
                    $id_agenda = "";
                    if (isset($request->input("id_agenda")[$i])) {
                        $id_agenda = $request->input("id_agenda")[$i];
                    }
                    $hc_procedimiento = "";
                    if (isset($request->input("hc_procedimiento")[$i])) {
                        $hc_procedimiento = $request->input("hc_procedimiento")[$i];
                    }
                    $arr_ags = ['id_agenda' => $id_agenda, 'id_paciente' => $paciente, 'producto' => $codigo, 'copago' => $copago, 'descripcion' => $descrip_prod, 'cantidad' => $cantidad, 'precio' => $precio, 'check_iva' => $iva, 'hc_procedimiento' => $hc_procedimiento, 'nombre_principal' => $nombre_procedimiento, 'fecha_procedimiento' => $fecha_procedimiento, 'nombre' => $nombre];
                    array_push($arr_agendas, $arr_ags);
                }
            }
        }
        $agrupproducto = $this->group_by("id_paciente", $arr_agendas);
        foreach ($agrupproducto as $key => $value) {

            if (!is_null($value)) {
                $contador            = 0;
                $paciente            = Paciente::find($key);
                $fecha_procedimiento = $request['fecha_asiento'];
                if (isset($value[$contador])) {
                    $fecha_procedimiento = $value[$contador]['fecha_procedimiento'];
                }
                $factura_venta = [
                    'sucursal'            => $c_sucursal,
                    'punto_emision'       => $c_caja,
                    'numero'              => $nfactura,
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => $id_empresa,
                    'tipo'                => $request['tipo'],
                    'fecha'               => $request['fecha_asiento'],
                    'divisas'             => $request['divisas'],
                    'nombre_cliente'      => $request['nombre_cliente'],
                    'tipo_consulta'       => $request['tipo_consulta'],
                    'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                    'direccion_cliente'   => $request['direccion_cliente'],
                    'ruc_id_cliente'      => $request['identificacion_cliente'],
                    'telefono_cliente'    => $request['telefono_cliente'],
                    'email_cliente'       => $request['mail_cliente'],
                    'orden_venta'         => $request['orden_venta'],
                    'estado_pago'         => '0',
                    'honorarios'          => '1',
                    'id_paciente'         => $key,
                    'nombres_paciente'    => $paciente->nombre1 . ' ' . $paciente->apellido1,
                    'id_hc_procedimiento' => $request['mov_paciente'],
                    'seguro_paciente'     => $paciente->id_seguro,
                    'procedimientos'      => $request['procedimiento'],
                    'fecha_procedimiento' => $fecha_procedimiento,
                    'copago'              => "0",
                    'id_recaudador'       => $request['cedula_recaudador'],
                    'ci_vendedor'         => $request['cedula_vendedor'],
                    'vendedor'            => $request['vendedor'],
                    //'nota'                          => $request['nota'],
                    'subtotal_0'          => "0",
                    'subtotal_12'         => "0",
                    //'subtotal'                      => $request['subtotal1'],
                    'descuento'           => "0",
                    'base_imponible'      => "0",
                    'impuesto'            => "0",
                    // 'transporte'                    => $request['transporte'],
                    'total_final'         => "0",
                    'valor_contable'      => "0",
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'id_ct_venta'         => $id_v,
                ];

                // return $factura_venta;
                $id_venta = Ct_ven_orden::insertGetId($factura_venta);
                foreach ($value as $x) {
                    $detalle = [
                        'id_ct_ven_orden' => $id_venta,
                        'id_ct_productos' => $x['producto'],
                        'nombre'          => $x['descripcion'],
                        'cantidad'        => $x['cantidad'],
                        'precio'          => $x['copago'],
                        'extendido'       => $x['precio'],
                        'detalle'         => $x['descripcion'],
                        'check_iva'       => $x['check_iva'],
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Ct_ven_orden_detalle::create($detalle);
                }
            }
        }
        return ['id' => $id_venta];
    }
    public function infome_labs(Request $request)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos      = $request['esfac_contable'];
        $variable    = 0;
        $variable2   = 0;
        $totales     = 0;
        $subtotal12  = 0;
        $subtotal0   = 0;
        $subtotal    = 0;
        $descuento   = 0;
        $impuesto    = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas($request);
        }

        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->get();
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia);
            //dd($deudas);
            $deudas2 = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $secuencia);
            foreach ($deudas2 as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $totales += $value->total_final;
                        $subtotal12 += $value->subtotal_12;
                        $subtotal0 += $value->subtotal_0;
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                        $descuento += $value->descuento;
                        $impuesto += $value->impuesto;
                    }
                }
            }
        }
        return view('contable/ventas/informe_labs', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo]);
    }
    public function pdf_omni($id, Request $request)
    {
        //dd("a");
        //dd($request->all());
        //$detalles = json_decode($request['arrayPreview']);
        $ventas   = Ct_ventas::find($id);
        $detalles = Ct_Detalle_Venta_Omni::where('id_ct_ventas', $id)->get();
        //dd($ventas->descuento);
        $vistaurl = "contable.ventas.pdf_omni";
        $valid    = 1;
        $view     = \View::make($vistaurl, compact('detalles', 'ventas'))->render();

        //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Factura de Venta -' . $id . '.pdf');
    }
    public function pdf_conglomerada($id, Request $request)
    {
    }
    public function informe_nca(Request $request)
    {
        //5000 id_venta revisar OJOOOOOOO

        $id_empresa    = $request->session()->get('id_empresa');
        $empresa       = Empresa::where('id', $id_empresa)->first();
        $fecha_desde   = $request['fecha_desde'];
        $gastos        = $request['esfac_contable'];
        $variable      = 0;
        $variable2     = 0;
        $totales       = 0;
        $subtotal12    = 0;
        $subtotal0     = 0;
        $subtotal      = 0;
        $descuento     = 0;
        $impuesto      = 0;
        $n_credito     = 0;
        $final_credito = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas_nca($request);
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->get();

        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia);

            $deudas2 = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $secuencia);

            foreach ($deudas2 as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $totales += $value->total_final;
                        $subtotal12 += $value->subtotal_12;
                        $subtotal0 += $value->subtotal_0;
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                        $descuento += $value->descuento;
                        $impuesto += $value->impuesto;
                    }
                }
            }
        }

        return view('contable/ventas/informe_ventas_nca', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo]);
    }
    public function excel_ventas_nca(Request $request)
    {
        //dd("Ingreso");
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $proveedor   = $request['id_cliente'];
        $fecha_hasta = $request['fecha_hasta'];
        $concepto    = $request['secuencia'];
        $gastos      = $request['tipo'];
        $variable    = 0;
        $fech        = 0;
        $empresa     = Empresa::where('id', $id_empresa)->first();

        // $nota_credito= DB::table('ct_detalle_credito_clientes')->where('id_factura',$id)->first();
        $consulta = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, 0, $concepto);
        //dd($consulta);
        Excel::create('Informe Factura de Ventas Netas ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Factura de Ventas Netas', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                if ($empresa->logo != null && $empresa->logo != '(N/A)') {
                    $fech = 1;
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C2:Q2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:Q3');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME FACTURA DE VENTAS NETAS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:Q4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al - " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUC');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setFontColor('#FFFFFF');
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G5:H5');
                $sheet->cell('G5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENTAS BRUTAS');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL NCA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VENTAS NETAS');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANULADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',
                    'P' => '0.00',

                ));
                $i = $this->setDetalleInforme_nca($consulta, $sheet, 6, $gastos);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL
                $sheet->cells('C3:S3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C2:S2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:S5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(16)->setAutosize(false);
        })->export('xlsx');
    }
    public function setDetalleInforme_nca($consulta, $sheet, $i, $variable)
    {
        $x                  = 0;
        $valor              = 0;
        $resta              = 0;
        $totales            = 0;
        $subtotal12         = 0;
        $subtotal0          = 0;
        $subtotal           = 0;
        $descuento          = 0;
        $impuesto           = 0;
        $finalsub           = 0;
        $valor_nota_credito = 0;
        $final_con_credito  = 0;
        $n_credito          = 0;
        $final_credito      = 0;

        foreach ($consulta as $value) {
            if ($value != null) {
                $nota_credito = DB::table('ct_detalle_credito_clientes')->where('id_factura', $value->id)->first();
                if ($value->estado != 0) {
                    if ($value->electronica == 1) {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12 - $value->descuento;
                    } else {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                    }
                    $subtotal12 += $value->subtotal_12;

                    $subtotal0 += $value->subtotal_0;

                    $descuento += $value->descuento;
                    $impuesto += $value->impuesto;
                    $finalsub = $value->subtotal_12 + $value->subtotal_0;

                    if (!is_null($nota_credito)) {
                        $valor_nota_credito = $nota_credito->abono; // el valor de las notas de credito
                        $final_con_credito  = $value->total_final - $valor_nota_credito; // valor final si existe nota de credito
                        $n_credito += $nota_credito->abono; // suma de las notas de credito
                        $final_credito += $final_con_credito; //suma de totales con nota de credito
                    } else {
                        $final_con_credito = $value->total_final; // si no tiene el valor la nca va a ser el final de la factura
                        $final_credito += $value->total_final; // suma final
                    }
                }

                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue(date("d-m-Y", strtotime($value->fecha)));
                    $cell->setFontWeight('bold');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->nro_comprobante);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });
                $sheet->cell('C' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue(' ' . $value->id_cliente);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->cliente != null) {
                        $cell->setValue($value->cliente->nombre);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->paciente != null && $value->id_paciente != "9999999999") {
                        $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->tipo);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G' . $i . ':H' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    $cell->setValue($value->concepto . "# Asiento " . $value->id_asiento);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->subtotal_12);

                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->subtotal_0);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($finalsub, $value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($finalsub);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->descuento);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->impuesto);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->electronica == 1) {
                        $get = $value->total_final + $value->descuento;
                        $cell->setValue($get);
                    } else {
                        $cell->setValue($value->total_final);
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($value, $valor_nota_credito, $nota_credito) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->electronica == 1) {
                        $get = $value->total_final;
                        if (!is_null($nota_credito)) {
                            $get = $nota_credito->abono;
                        }

                        $cell->setValue($get);
                    } else {
                        if (!is_null($nota_credito)) {
                            $cell->setValue($nota_credito->abono); // correcion

                        } else {
                            $cell->setValue('0.00'); // correcion

                        }
                    }

                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) use ($value, $final_con_credito, $valor_nota_credito) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->electronica == 1) {
                        $get = $value->total_final + $valor_nota_credito; // se suma si es electronica
                        $cell->setValue($get);
                    } else {
                        $cell->setValue($final_con_credito); //ya hicimos la resta arriba
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($value, $nota_credito) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue('ANULADA');
                    } elseif (!is_null($nota_credito)) {

                        $cell->setValue('NCA');
                    } else {

                        $cell->setValue('ACTIVO');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
            }
        }
        $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
        });
        $sheet->cell('I' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal12);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal0);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($descuento) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($descuento);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($impuesto);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('O' . $i, function ($cell) use ($n_credito) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($n_credito);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('P' . $i, function ($cell) use ($final_credito) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($final_credito);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        return $i;
    }
    public function estadistico(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $anio = $request['anio'];
        //dd($request->all());
        $mes      = $request['mes'];
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        if (is_null($mes)) {
            $mes = date('m');
        }
        //0  es publico
        //1  es privado
        $array_agrupado = array();
        for ($i = 1; $i < 13; $i++) {
            $mes = '0' . $i;
            if (strlen($i) < 1) {
                $mes = $i;
            }
            $ventas_mes        = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')
                ->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')
                ->where('ct_ventas.id_empresa', $id_empresa)
                ->whereMonth('ct_ventas.fecha', $mes)
                ->whereYear('ct_ventas.fecha', $anio)
                ->where('ct_ventas.estado', '<>', '0')
                ->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            $estructure['mes'] = $mes;
            foreach ($ventas_mes as $value) {
                $estructure['privado']   = $value->privado;
                $estructure['publico']   = $value->publico;
                $estructure['cpublico']  = $value->cpublico;
                $estructure['cprivado']  = $value->cprivado;
                $estructure['privadosc'] = $value->privadosc;
                $estructure['publicosc'] = $value->publicosc;
                array_push($array_agrupado, $estructure);
            }
        }
        $array_agrupado_anio = array();
        $anios               = [$request->anio];
        $aniosf              = 0;
        foreach ($anios as $key => $z) {
            //dd($z);
            $ventas_anio = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')->where('ct_ventas.id_empresa', $id_empresa)->whereYear('ct_ventas.fecha', $z)->where('ct_ventas.estado', '<>', '0')->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            foreach ($ventas_anio as $s) {
                $estructure['anio']      = $aniosf;
                $estructure['privado']   = $s->privado;
                $estructure['publico']   = $s->publico;
                $estructure['cpublico']  = $s->cpublico;
                $estructure['cprivado']  = $s->cprivado;
                $estructure['privadosc'] = $s->privadosc;
                $estructure['publicosc'] = $s->publicosc;
                array_push($array_agrupado_anio, $estructure);
            }
            $aniosf++;
        }
        $empresa = Empresa::all();

        return view('contable/ventas/estadisticos', ['array_agrupado' => $array_agrupado, 'array_agrupado_anio' => $array_agrupado_anio, 'fechaini' => $fechaini, 'fechafin' => $fechafin, 'empresas' => $empresa, 'id_empresa' => $id_empresa, 'request' => $request]);
        //dd($array_agrupado_anio,$array_agrupado);p
    }
    public function estadisticoshc4(Request $request)
    {
        $empresa    = Empresa::all();
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        return view('contable/ventas/estadisticohc4', ['empresas' => $empresa, 'id_empresa' => $id_empresa]);
    }
    public function graphics(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $anio     = $request['anio'];
        $mes      = $request['mes'];
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        if (is_null($anio)) {
            $anio = date('Y');
        }
        if (is_null($mes)) {
            $mes = date('m');
        }
        $array_agrupado = array();
        for ($i = 1; $i < 13; $i++) {
            $mes = '0' . $i;
            if (strlen($i) < 1) {
                $mes = $i;
            }
            $ventas_mes        = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')->where('ct_ventas.id_empresa', $id_empresa)->whereMonth('ct_ventas.fecha', $mes)->whereYear('ct_ventas.fecha', date('Y'))->where('ct_ventas.estado', '<>', '0')->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            $estructure['mes'] = $mes;
            foreach ($ventas_mes as $value) {
                $estructure['privado']   = $value->privado;
                $estructure['publico']   = $value->publico;
                $estructure['cpublico']  = $value->cpublico;
                $estructure['cprivado']  = $value->cprivado;
                $estructure['privadosc'] = $value->privadosc;
                $estructure['publicosc'] = $value->publicosc;
                array_push($array_agrupado, $estructure);
            }
        }
        $array_agrupado_anio = array();
        $anios               = ['2021'];
        $aniosf              = 0;
        foreach ($anios as $key => $z) {
            //dd($z);
            $ventas_anio = Ct_ventas::join('seguros as s', 's.id', 'ct_ventas.seguro_paciente')->join('ct_detalle_comprobante_ingreso as ingreso', 'ingreso.id_factura', 'ct_ventas.id')->where('ct_ventas.id_empresa', $id_empresa)->where('ct_ventas.estado', '<>', '0')->whereYear('ct_ventas.fecha', $z)->select(DB::raw('SUM(ct_ventas.total_final) as total_final'), DB::raw('SUM(ingreso.total) as ingresado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ingreso.total ELSE 0 END) as cpublico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ingreso.total ELSE 0 END) as cprivado'), DB::raw('SUM(CASE WHEN s.tipo=0 THEN ct_ventas.total_final ELSE 0 END) as publico'), DB::raw('SUM( CASE WHEN s.tipo <> 0 THEN ct_ventas.total_final ELSE 0 END) as privado'), DB::raw('COUNT(CASE WHEN s.tipo=0 THEN 0 ELSE null END) as publicosc'), DB::raw('COUNT(CASE WHEN s.tipo <> 0 THEN 0 ELSE null END) as privadosc'))->get();
            foreach ($ventas_anio as $s) {
                $estructure['anio']      = $aniosf;
                $estructure['privado']   = $s->privado;
                $estructure['publico']   = $s->publico;
                $estructure['cpublico']  = $s->cpublico;
                $estructure['cprivado']  = $s->cprivado;
                $estructure['privadosc'] = $s->privadosc;
                $estructure['publicosc'] = $s->publicosc;
                array_push($array_agrupado_anio, $estructure);
            }
            $aniosf++;
        }
        return view('contable/ventas/graphics', ['array_agrupado' => $array_agrupado, 'array_agrupado_anio' => $array_agrupado_anio, 'fechaini' => $fechaini, 'fechafin' => $fechafin]);
    }
    public function informe_ordenes_pendientes(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $seguro      = $request['id_seguro'];

        $seguros     = Seguro::all();
        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $ordenes = '[]';

        $ordenes = Ct_Ven_Orden::where('estado', '!=', '0')->where('id_empresa', $id_empresa)
            ->where('estado_pago', '=', '0')
            ->where('tipo', '=', 'VEN-OV')
            ->wherebetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->where('valor_contable', '!=', 0);

        if (!is_null($seguro)) {
            $ordenes  = $ordenes->where('seguro_paciente', $seguro);
        }
        $ordenes  = $ordenes->orderBy('id', 'desc')->get();


        return view('contable/ventas/informe_ordenes_pendientes', ['informe' => $ordenes, 'id_empresa' => $id_empresa, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'seguros' => $seguros]);
    }
    public function guardar_comision(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $id_plantilla = $request['id_plantilla'];
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $total_final = $request['subtotal1'];
        $porcentaje = $request['porcentaje1'];
        $total_comision = $request['total1'];
        $check_liq = $request['check_liq'];

        if (is_null($check_liq)) {
            return ['respuesta' => 'error', 'msj' => 'Seleccione un item', 'titulos' => 'Error'];
        }

        if (is_null($id_plantilla)) {
            return ['respuesta' => 'error', 'msj' => 'Seleccione la plantilla', 'titulos' => 'Error'];
        }

        DB::beginTransaction();
        try {
            $comision_cabecera = [
                'id_plantilla'    => $id_plantilla,
                'fecha_inicio'    => $fecha_desde,
                'fecha_fin'       => $fecha_hasta,
                'estado'          => '1',
                'id_empresa'      => $id_empresa,
                'total'           => $total_final,
                'porcentaje'      => $porcentaje,
                'total_comision'  => $total_comision,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $ct_comision_cabecera = Ct_Comision_Cabecera::insertGetId($comision_cabecera);

            foreach ($check_liq as $value) {
                $detalle_ventas = Ct_detalle_venta::where('estado', 1)->where('id', $value)->first();
                if (!is_null($detalle_ventas)) {
                    $ventas = Ct_ventas::where('estado', 1)->where('id_empresa', $id_empresa)->where('id', $detalle_ventas->id_ct_ventas)->first();
                }
                Ct_Comision_Detalle::create([
                    'id_comision_cabecera' => $ct_comision_cabecera,
                    'id_detalle_venta'     => $detalle_ventas->id,
                    'codigo_producto'      => $detalle_ventas->id_ct_productos,
                    'id_paciente'          => $ventas->id_paciente,
                    'nro_comprobante'      => $ventas->nro_comprobante,
                    'fecha_procedimiento'  => $ventas->fecha_procedimiento,
                    'seguro'               => $ventas->seguro_paciente,
                    'valor_total'          => $detalle_ventas->extendido,
                    'estado'               => '1',
                    'id_usuariocrea'       => $id_usuario,
                    'id_usuariomod'        => $id_usuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                ]);
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function buscar_precio($id, Request $request)
    {

        $valor_total = 0;

        $orden_venta = Ct_Detalle_Venta::find($id);

        $valor_total = $orden_venta->extendido;

        return ['valor_total' => $valor_total];
    }
    public function informe_liquidaciones_comisiones(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $productos_buscados   = $request['productos'];
        $id_seguro = $request['id_seguro'];
        $id_plantilla = $request['id_plantilla'];
        $seguros = Seguro::where('inactivo', '1')->Orderby('nombre', 'asc')->get();

        if (!is_null($id_plantilla)) {
            if ($id_plantilla == '1') {
                $productos_buscados = ['COLO-PENTAX', 'EDA-PENTAX'];
                $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->whereIn('codigo', ['COLO-PENTAX', 'EDA-PENTAX'])->get();
            } elseif ($id_plantilla == '2') {
                $productos_buscados = [];
                $productos_det_venta = Ct_Detalle_Venta::distinct()->where('estado', '1')->wherebetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get(['id_ct_productos']);
                //dd($productos_det_venta);
                if (!is_null($productos_det_venta)) {
                    $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1)->whereNotIn('codigo', ['COLO-PENTAX', 'EDA-PENTAX']);
                    $todos = $todos->where(function ($jq1) use ($productos_det_venta) {
                        foreach ($productos_det_venta as $prod_buscados) {
                            $jq1 = $jq1->orwhere('codigo', $prod_buscados->id_ct_productos);
                        }
                    });
                }

                $todos = $todos->get();
                if (!is_null($todos)) {
                    $i = 0;
                    foreach ($todos as $value) {
                        $productos_buscados[$i] = $value->codigo;
                        $i++;
                    }
                }
            }
        } elseif (!is_null($productos_buscados)) {
            $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1);
            $todos = $todos->where(function ($jq1) use ($productos_buscados) {
                foreach ($productos_buscados as $prod_buscados) {
                    $jq1 = $jq1->orwhere('codigo', $prod_buscados);
                }
            });

            $todos = $todos->get();
        } else {
            $productos_buscados = ['COLO-PENTAX', 'EDA-PENTAX'];
            $todos = Ct_productos::where('id_empresa', $id_empresa)->where('estado_tabla', 1);

            $todos = $todos->where(function ($jq1) {
                $jq1->orwhere('codigo', 'COLO-PENTAX')
                    ->orwhere('codigo', 'EDA-PENTAX');
            });

            $todos = $todos->get();
        }

        if ($fecha_desde == null) {
            $fecha_desde = date("Y-m-d");
        }
        if ($fecha_hasta == null) {
            $fecha_hasta = date("Y-m-d");
        }
        $ventas = '[]';
        $ventas = Ct_Ventas::where('estado', '!=', '0')->where('id_empresa', $id_empresa)
            ->where('tipo', '=', 'VEN-FA')
            ->wherebetween('fecha_procedimiento', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);

        if (!is_null($id_seguro)) {
            $ventas = $ventas->where(function ($jq2) use ($id_seguro) {
                foreach ($id_seguro as $seguros_todos) {
                    $jq2 = $jq2->orwhere('seguro_paciente', $seguros_todos);
                }
            });
        }

        $ventas  = $ventas->orderBy('fecha_procedimiento', 'desc')->get();

        $comision_cabecera = Ct_Comision_Cabecera::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->get();

        return view('contable/ventas/informe_liquidaciones_comisiones', ['ventas' => $ventas, 'id_empresa' => $id_empresa, 'seguros' => $seguros, 'id_seguro' => $id_seguro, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'productos_todos' => $todos, 'productos_buscados' => $productos_buscados, 'id_plantilla' => $id_plantilla, 'comision_cabecera' => $comision_cabecera]);
    }
    public function pdf_liquidacion_comision($id, Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();

        $cabecera = Ct_Comision_Cabecera::where('id', $id)->where('estado', 1)->where('id_empresa', $id_empresa)->first();
        if (!is_null($cabecera)) {
            $detalle = Ct_Comision_Detalle::where('id_comision_cabecera', $id)->where('estado', 1)->get();
        }

        $vistaurl = "contable.ventas.pdf_liquidacion_comisiones";
        $view     = \View::make($vistaurl, compact('cabecera', 'detalle', 'empresa'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Liquidacion de comisiones' . '.pdf');
    }
    public function eliminar_liquidacion_comision($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $cabecera_comision = Ct_Comision_Cabecera::find($id);

        $arreglo = [
            'estado'            => 0,
            'id_usuariomod'     => $id_usuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $cabecera_comision->update($arreglo);

        $detalle_comision = Ct_Comision_Detalle::where('id_comision_cabecera', $id)->where('estado', '1')->get();

        foreach ($detalle_comision as $value) {
            $value->estado = 0;
            $value->id_usuariomod = $id_usuario;
            $value->ip_modificacion = $ip_cliente;
            $value->save();
        }
    }
    public function informe_ventas(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $gastos      = $request['esfac_contable'];
        $variable    = 0;
        $variable2   = 0;
        $totales     = 0;
        $subtotal12  = 0;
        $subtotal0   = 0;
        $subtotal    = 0;
        $descuento   = 0;
        $impuesto    = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ventas($request);
        }
        $activo      = $request->activo;
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];
        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->get();
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia, $activo);
        }

        if (count($deudas) > 0) {

            if (is_null($activo) or $activo == 1) {
                $activo = 1;
            } else {
                $activo = 0;
            }
            $totales    = $deudas->where('estado', $activo)->sum('total_final');
            $subtotal12 = $deudas->where('estado', $activo)->sum('subtotal_12');
            //$subtotal = $deudas->where('estado', $activo)->sum('subtotal');
            $subtotal0 = $deudas->where('estado', $activo)->sum('subtotal_0');
            $descuento = $deudas->where('estado', $activo)->sum('descuento');
            $impuesto  = $deudas->where('estado', $activo)->sum('impuesto');

            if (Auth::user()->id == "0957258056") {
                //dd($descuento);
            }
            //*******************ELECTRONICA*********************//
            //$sumElectronica =  $deudas->where('estado', $activo)->sum('subtotal_0');
            $descuentoElectronica = $deudas->where('estado', $activo)->where('electronica', 1)->sum('descuento');
            //*******************NO ELECTRONICA*********************//


            // if($activo == 2){
            //     $totales = $deudas->sum('total_final');
            //     $subtotal12 = $deudas->sum('subtotal_12');
            //     $subtotal = $deudas->sum('subtotal');
            //     $subtotal0 = $deudas->sum('subtotal_0');
            //     $descuento = $deudas->sum('descuento');
            //     $impuesto = $deudas->sum('iva_total');
            // }else{
            //     $totales = $deudas->where('estado', '<>', '0')->sum('total_final');
            //     $subtotal12 = $deudas->where('estado', '<>', '0')->sum('subtotal_12');
            //     $subtotal = $deudas->where('estado', '<>', '0')->sum('subtotal');
            //     $subtotal0 = $deudas->where('estado', '<>', '0')->sum('subtotal_0');
            //     $descuento = $deudas->where('estado', '<>', '0')->sum('descuento');
            //     $impuesto = $deudas->where('estado', '<>', '0')->sum('iva_total');
            // }
            //  dd($sumElectronica);
            $subtotal = $subtotal0 + $subtotal12;
            $totales  = $subtotal  + $impuesto - $descuento;
            // dd($descuentoElectronica);
            //$totales = $totales + $descuentoElectronica;

            if (Auth::user()->id == "0957258056") {
                //dd($totales);
            }
        }

        return view('contable/ventas/informe', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'activo' => $activo, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo]);
    }
    public function informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $referencia, $r, $secuencia, $activo = "")
    {
        $deudas    = null;
        $condicion = ['VEN-FA', 'VENFA-CO'];
        $deudas    = Ct_ventas::whereIn('tipo', $condicion)
            ->where('id_empresa', $id_empresa);
        if ($activo != null or $activo != "") {
            if ($activo == 2) {
                $activo = 0;
            }
            $deudas = Ct_ventas::whereIn('tipo', $condicion)->where('estado', $activo)
                ->where('id_empresa', $id_empresa);
            //dd('aqui');
        }
        if (!is_null($fecha_desde)) {
            $deudas = $deudas->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $deudas = $deudas->where('fecha', '<=', $fecha_hasta);
        }
        if (!is_null($secuencia)) {
            $deudas = $deudas->where('nro_comprobante', 'like', '%' . $secuencia . '%');
        }
        if (!is_null($proveedor)) {
            $deudas = $deudas->where('id_cliente', $proveedor);
        }
        if (($variable) == 1) {
            $deudas = $deudas->paginate(20);
        } else {
            $deudas = $deudas->orderBy('fecha', 'ASC')->get();
        }

        return $deudas;
    }
    public function excel_ventas(Request $request)
    {
        //dd("Ingreso");
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $proveedor   = $request['id_cliente'];
        $fecha_hasta = $request['fecha_hasta'];
        $concepto    = $request['secuencia'];
        $gastos      = $request['tipo'];
        $variable    = 0;
        $fech        = 0;
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $consulta    = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $gastos, 0, $concepto);
        //dd($consulta);
        Excel::create('Informe Factura de Ventas ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Factura de Ventas', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
                if ($empresa->logo != null && $empresa->logo != '(N/A)') {
                    $fech = 1;
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C2:Q2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:Q3');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("INFORME FACTURA DE VENTAS ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:Q4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
                    if ($fecha_desde != null) {
                        $cell->setValue("Desde " . date("d-m-Y", strtotime($fecha_desde)) . " Hasta " . date("d-m-Y", strtotime($fecha_hasta)));
                    } else {
                        $cell->setValue(" Al - " . date("d-m-Y", strtotime($fecha_hasta)));
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AUTORIZACION');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUC');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G5', function ($cell) {
                    $cell->setFontColor('#FFFFFF');
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });;
                $sheet->cell('H5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 12');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL 0');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCUENTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANULADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // DETALLES

                $sheet->setColumnFormat(array(
                    'H' => '0.00',
                    'I' => '0.00',
                    'J' => '0.00',
                    'K' => '0.00',
                    'L' => '0.00',
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',

                ));
                $i = $this->setDetalleInforme($consulta, $sheet, 6, $gastos);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                //  CONFIGURACION FINAL
                $sheet->cells('C3:Q3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('C2:Q2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:Q5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#BFBFBF');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(23)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(24)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(16)->setAutosize(false);
        })->export('xlsx');
    }
    public function setDetalleInforme($consulta, $sheet, $i, $variable)
    {
        $x          = 0;
        $valor      = 0;
        $resta      = 0;
        $totales    = 0;
        $subtotal12 = 0;
        $subtotal0  = 0;
        $subtotal   = 0;
        $descuento  = 0;
        $impuesto   = 0;
        $finalsub   = 0;
        foreach ($consulta as $value) {
            if ($value != null) {
                if ($value->estado != 0) {
                    if ($value->electronica == 1) {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12 + $value->descuento;
                    } else {
                        $subtotal += $value->subtotal_0 + $value->subtotal_12;
                    }
                    $subtotal12 += $value->subtotal_12;
                    $subtotal0 += $value->subtotal_0;
                    $descuento += $value->descuento;
                    $impuesto += $value->impuesto;
                    $finalsub = $value->subtotal_12 + $value->subtotal_0;
                    if ($value->electronica == 1) {
                        $finalsub = $value->subtotal_12 + $value->subtotal_0 + $value->descuento;
                    }
                    $totales += $value->total_final;
                }

                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue(date("d-m-Y", strtotime($value->fecha)));
                    $cell->setFontWeight('bold');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->nro_comprobante);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });
                $sheet->cell('C' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->nro_autorizacion);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue(' ' . $value->id_cliente);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->cliente != null) {
                        $cell->setValue($value->cliente->nombre);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->paciente != null && $value->id_paciente != "9999999999") {
                        $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->tipo);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    $cell->setValue($value->concepto . "# Asiento " . $value->id_asiento);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->subtotal_12);

                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->subtotal_0);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($finalsub, $value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($finalsub);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->descuento);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->impuesto);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->electronica == 1) {
                        $get = $value->total_final;
                        if ($value->estado == 0) {
                            $cell->setBackground('#E64725');
                        }
                        $cell->setValue($get);
                    } else {
                        if ($value->estado == 0) {
                            $cell->setBackground('#E64725');
                        }
                        $cell->setValue($value->total_final);
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue('ANULADA');
                    } else {
                        $cell->setValue('ACTIVO');
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    if ($value->estado == 0) {
                        $cell->setValue($value->usuariomod->nombre1 . " " . $value->usuariomod->apellido1);
                    }
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;
            }
        }
        $sheet->cell('F' . $i, function ($cell) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
        });
        $sheet->cell('I' . $i, function ($cell) use ($subtotal12) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal12);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($subtotal0) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal0);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($subtotal) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($descuento) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($descuento);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($impuesto) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($impuesto);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($totales) {
            // manipulate the cel
            // $this->setSangria($cont, $cell);

            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        return $i;
    }
    public function imprimirPlanillaDetalle($id, $id_hc_procedimiento) //id_procedimiento//
    {
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $empresa    = Empresa::find($agenda->id_empresa);
        $planilla   = Planilla::where('id_hc_procedimiento', $id_hc_procedimiento)
            ->where('estado', '!=', 0)
            ->where('aprobado', '!=', 0)
            ->orderBy('id', 'desc')
            ->first();
        // dd($planilla);
        if (!isset($planilla->id)) {
            $detalles = '[]';
        } else {
            $detalles = $planilla->detalles_validos;
        }

        $vistaurl = "contable.ventas.pdf_detalle_planilla";
        $view     = \View::make($vistaurl, compact('fact_venta', 'hc', 'hcp', 'detalles', 'orden', 'empresa', 'paciente'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    //LZ
    //Modal Planillas Agenda
    public function obtenerPlanillasAgenda($id)
    {
        $id_usuario = Auth::user()->id;
        $hcp        = hc_procedimientos::find($id);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $orden      = array();

        //HONORARIOS MEDICOS Y ANASTESIOLOGICOS 18/FEB/2022 VICTOR
        /*$seguro = $hc->seguro;
        if( $hcp->id_seguro != null ){
        $seguro = $hcp->seguro;
        }

        $id_empresa = $agenda->id_empresa;
        if($hcp->id_empresa != null){
        $id_empresa = $hcp->id_empresa;
        }

        $incluir_convenio = false; $convenio = null;

        if($seguro->tipo == 0){ //PUBLICO
        $convenio = Convenio::where('id_empresa',$id_empresa)->where('id_seguro',$seguro->id)->first();
        }

        if($seguro->tipo == 1){ //PRIVADO
        $convenios = Convenio::where('id_seguro',$seguro->id)->get();
        if($convenios->count() > 1){
        $incluir_convenio = true;
        }
        if($convenios->count() == 1){
        $incluir_convenio = false;
        $convenio = $convenios->first();
        }
        }*/

        //dd($seguro,$convenio);
        //
        return view('contable/ventas/modal_detalle_planillas', ['id' => $id, 'paciente' => $paciente, 'orden' => $orden, 'hc' => $hc, 'id_usuario' => $id_usuario, 'agenda' => $agenda]);
    }
    public function create_recibo(Request $request)
    {
        $divisas         = Ct_Divisas::where('estado', '1')->get();
        $clientes        = Ct_Clientes::where('estado', '1')->get();
        $tipo_pago       = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco     = Ct_Bancos::where('estado', '1')->get();
        $seguros         = Seguro::all();
        $user_recaudador = User::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $bodega     = bodega::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $punto = DB::table('ct_sucursales as ct_s')
            ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
            ->where('ct_c.estado', 1)
            ->get();
        $cuentas      = Plan_Cuentas::where('estado', '2')->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $productos    = Ct_productos::where('estado_tabla', '1')->get();
        $iva          = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();
        return view('contable/ventas/create_recibo', ['divisas' => $divisas, 'clientes' => $clientes, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'seguros' => $seguros, 'user_recaudador' => $user_recaudador, 'user_vendedor' => $user_vendedor, 'bodega' => $bodega, 'empresa' => $empresa, 'sucursales' => $sucursales, 'punto' => $punto, 'cuentas' => $cuentas, 'productos' => $productos, 'iva' => $iva, 't_factura' => '1', 'tipo_tarjeta' => $tipo_tarjeta]);
    }
    public function htmlrecibo(Request $request)
    {
        $id         = $request->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $recibo     = Ct_Orden_Venta::find($id);
        $productos  = Ct_productos::where('estado_tabla', '1')->get();
        return view('contable/ventas/detalles', ['id' => $id, 'recibo' => $recibo, 'productos' => $productos, 'empresa' => $empresa]);
    }
    public function index_recibo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ventas     = Ct_ventas::where('tipo', 'VEN-R')->where('id_empresa', $id_empresa);
        if ($request->id != null) {
            $ventas = $ventas->where('id', $request->id);
        } else {
            if ($request->id_asiento != null) {
                $ventas = $ventas->where('id_asiento', $request->id_asiento);
            }
            if ($request->fecha != null) {
                $ventas = $ventas->where('fecha', $request->fecha);
            }
            if ($request->nombre_cliente != null) {
                $ventas = $ventas->where('nombre_cliente', 'LIKE', '%' . $request->nombre_cliente . '%');
            }
            if ($request->nombre_paciente != null) {
                $ventas = $ventas->where('nombres_paciente', 'LIKE', '%' . $request->nombre_paciente . '%');
            }
        }
        $empresa = Empresa::find($id_empresa);
        $ventas  = $ventas->orderBy('fecha', 'DESC')->paginate(20);
        return view('contable.ventas.index_recibo', ['ventas' => $ventas, 'request' => $request, 'empresa' => $empresa]);
    }
    public function store_recibo(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request->fecha_asiento;
        DB::beginTransaction();
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $msj        = "no    o";
        $numero     = $request['numero'];
        $cliente    = Ct_Clientes::where('identificacion', '=', $request['identificacion_cliente'])->count();
        try {
            if ($cliente == 0) {
                // cliente
                Ct_Clientes::create([
                    'nombre'                  => strtoupper($request['nombre_cliente']),
                    'tipo'                    => '5',
                    'identificacion'          => $request['identificacion_cliente'],
                    'clase'                   => '1',
                    'nombre_representante'    => $request['nombre_cliente'],
                    'cedula_representante'    => $request['identificacion_cliente'],
                    'ciudad_representante'    => $request['ciudad_cliente'],
                    'direccion_representante' => $request['direccion_cliente'],
                    'telefono1_representante' => $request['telefono_cliente'],
                    'email_representante'     => $request['mail_cliente'],
                    'estado'                  => '1',
                    'id_usuariocrea'          => $idusuario,
                    'id_usuariomod'           => $idusuario,
                    'ip_creacion'             => $ip_cliente,
                    'ip_modificacion'         => $ip_cliente,

                ]);
            }

            if ($request['sucursal'] != 0) {
                $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();

                $c_sucursal = $cod_sucurs->codigo_sucursal;
                $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
                $c_caja     = $cod_caj->codigo_caja;
                $proced     = $request['procedimiento'];
                // echo($numero);

                if (!is_null($numero)) {
                    $num_comprobante      = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $numero;
                    $verifica_num_factura = Ct_ventas::where('id_empresa', $id_empresa)
                        ->where('nro_comprobante', $num_comprobante)
                        ->where('tipo', '<>', 'VEN-FACT')
                        ->where('estado', '!=', '0')->first();
                    if (!is_null($verifica_num_factura)) {
                        return ["respuesta" => "existe", 'mensaje' => "Ya existe un número de factura"];
                    } else {
                        $nfactura = $numero;
                    }
                } else {
                    $nfactura        = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                }
            } else {
                $c_sucursal      = 0;
                $c_caja          = 0;
                $num_comprobante = 0;
                $nfactura        = 0;
                $proced          = $request['procedimiento'];
            }
            $pac = "";
            if ($request['nombre_paciente'] != "") {
                $pac = " | " . $request['nombre_paciente'];
            }
            $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
            $id_paciente = $request['identificacion_paciente'];
            $patient     = Paciente::find($id_paciente);
            $pc          = "9999999999";
            if (is_null($patient)) {
                $pc = "9999999999";
            } else {
                $pc = $patient->id;
            }
            $input_cabecera = [
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $request['total1'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera); //activo
            //$id_asiento_cabecera = 0;
            //GUARDAdo TABLA CT_VENTA.
            $factura_venta = [
                'sucursal'            => $cod_sucurs->codigo_sucursal,
                'punto_emision'       => $cod_caj->codigo_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'], //nombre_cliente
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'nro_autorizacion'    => $request['numero_autorizacion'],
                'id_paciente'         => $request['identificacion_paciente'],
                'nombres_paciente'    => $request['pacienteinfo'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['segurosinfo'],
                'procedimientos'      => $request['procedimientoinfo'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['totalc'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'valor_contable'      => $request['total1'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['total1'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_venta       = Ct_ventas::insertGetId($factura_venta); //activo
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;
            $arr_id_hc      = [];
            $arr_activos    = [];
            $cont           = 0;
            $arr_obs        = [];

            $observacion = "";
            //dd(count($request->input("recibo")));
            for ($p = 0; $p < count($request->input("recibo")); $p++) {
                Ct_Ventas_Recibo_Cobro::create([
                    'id_venta'        => $id_venta,
                    'id_recibo'       => $request->input("recibo")[$p],
                    'id_empresa'      => $id_empresa,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
            if (!empty($request->input("nombre"))) {
                for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {

                        $precio_neto = "";
                        if (isset($request->input("precioneto")[$i])) {
                            $precio_neto = $request->input("precioneto")[$i];
                        }
                        $nombre = "";
                        if (isset($request->input("nombre")[$i])) {
                            $nombre = $request->input("nombre")[$i];
                        }
                        $cantidad = 0;
                        if (isset($request->input("cantidad")[$i])) {
                            $cantidad = $request->input("cantidad")[$i];
                        }
                        $codigo = "";
                        if (isset($request->input("codigo")[$i])) {
                            $codigo = $request->input("codigo")[$i];
                        }

                        $precio = 0;
                        if (isset($request->input("precio")[$i])) {
                            $precio = $request->input("precio")[$i];
                        }

                        $iva = "";
                        if (isset($request->input("iva")[$i])) {
                            $iva = $request->input("iva")[$i];
                        }
                        $descuento = "";
                        if (isset($request->input("desc")[$i])) {
                            $descuento = $request->input("desc")[$i];
                        }
                        $descrip_prod = "";
                        if (isset($request->input("descrip_prod")[$i])) {
                            $descrip_prod = $request->input("descrip_prod")[$i];
                        }
                        $pdesc = "";
                        if (isset($request->input("descpor")[$i])) {
                            $pdesc = $request->input("descpor")[$i];
                        }
                        //$precioneto = $cantidad * $precio;
                        $arr = [
                            'nombre'     => $nombre,
                            'cantidad'   => $cantidad,
                            'codigo'     => $codigo,
                            'precio'     => $precio,
                            'descpor'    => $pdesc,
                            'copago'     => "",
                            'descuento'  => $descuento,
                            'precioneto' => $precio_neto,
                            'detalle'    => $descrip_prod,
                            'iva'        => $iva,
                            'obs_pac'    => $observacion,
                        ];
                        array_push($arr_total, $arr);
                    }
                }
            }
            foreach ($arr_total as $valor) {
                $detalle = [
                    'id_ct_ventas'         => $id_venta,
                    'id_ct_productos'      => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['precioneto'],
                    'detalle'              => $valor['detalle'],
                    'copago'               => $valor['copago'],
                    'check_iva'            => $valor['iva'],
                    'codigo'               => $valor['obs_pac'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,

                ];

                Ct_detalle_venta::create($detalle); //activo
            }
            $val_tol = $request['total1'];

            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $request['total1'],
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($request['tarifa_iva1'] > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['tarifa_iva1'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($request['subtotal_121'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.02');
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '4.1.01.02',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_121'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($request['subtotal_01'] > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.01');

                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                $plan_cuentas = Plan_Cuentas::where('id', $id_plan_confg)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '4.1.01.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $request['subtotal_01'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            //****REGISTRO FORMAS DE PAGO*** --activo falta****
            $arr_p = [];
            if (!empty($request->input("id_tip_pago"))) {
                for ($i = 0; $i < count($request->input("id_tip_pago")); $i++) {
                    if ($request->input("valor")[$i] != "" || $request->input("valor_base")[$i] != null) {
                        $arr_pagos = [
                            'id_tip_pago'    => $request->input("id_tip_pago")[$i],
                            'fecha_pago'     => $request->input("fecha_pago")[$i],
                            'numero_pago'    => $request->input("numero_pago")[$i],
                            'id_banco_pago'  => $request->input("id_banco_pago")[$i],
                            'id_cuenta_pago' => $request->input("id_cuenta_pago")[$i],
                            'giradoa'        => $request->input("giradoa")[$i],
                            'valor'          => $request->input("valor")[$i],
                            'valor_base'     => $request->input("valor_base")[$i],
                        ];
                        array_push($arr_p, $arr_pagos);
                    }
                }
            }

            foreach ($arr_p as $valor) {
                Ct_Forma_Pago::create([
                    'id_ct_ventas'    => $id_venta,
                    'tipo'            => $valor['id_tip_pago'], //$request['id_tip_pago'.$i],
                    'fecha'           => $valor['fecha_pago'], //$request['fecha'.$i],
                    'numero'          => $valor['numero_pago'], //$request['numero'.$i],
                    'banco'           => $valor['id_banco_pago'], //$request['id_banco'.$i],
                    'cuenta'          => $valor['id_cuenta_pago'], //$request['id_cuenta'.$i],
                    'giradoa'         => $valor['giradoa'], //$request['id_cuenta'.$i],
                    'valor'           => $valor['valor'], //$request['valor'.$i],
                    'valor_base'      => $valor['valor_base'], //$request['valor_base'.$i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }

            //agregar comprobantes de ingreso
            $this->crearComprobante($nfactura, $request, $arr_p, $id_venta);
            $arr_agendas  = [];
            $data['id']   = $id_venta;
            $data['tipo'] = 'VEN-R';
            $msj          = Ct_Kardex::generar_kardex($data);
            DB::commit();
            return ['idasiento' => $id_asiento_cabecera, 'idventa' => $id_venta, 'prd_val' => $request->input("prd_val"), 'request' => $request, 'arr_agendas' => $arr_agendas, 'kardex' => $msj, "respuesta" => "exito", 'mensaje' => "Guardado exitosamente"];
        } catch (\Exception $e) {

            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function pdf_ieced($id)
    {
        $ventas   = Ct_ventas::findorfail($id);
        $vistaurl = "contable.ventas.pdf_ieced";
        $view     = \View::make($vistaurl, compact('ventas'))->render();

        //return view('contable.ventas.pdf_comprobante_tributario',compact('fact_venta','emp','recaud','cliente','pacient','deta_vent','ct_for_pag','ct_val_ret'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function visualizador_pdf($id, Request $request)
    {
        $ventas  = Ct_ventas::find($id);
        $empresa = Empresa::find($ventas->id_empresa);
        return view('contable.ventas.html_visualizador', ['ventas' => $ventas, 'empresa' => $empresa]);
    }
    public function envio_correo($id, Request $request)
    {
        $rol     = Ct_ventas::find($id);
        $usuario = $rol->cliente;
        $correo  = $rol->cliente->email_representante;
        $mes     = "";
        $roldate = date('m', strtotime($rol->fecha));
        if ($roldate == 1) {
            $mes = 'Enero';
        } elseif ($roldate == 2) {
            $mes = 'Febrero';
        } elseif ($roldate == 3) {
            $mes = 'Marzo';
        } elseif ($roldate == 4) {
            $mes = 'Abril';
        } elseif ($roldate == 5) {
            $mes = 'Mayo';
        } elseif ($roldate == 6) {
            $mes = 'Junio';
        } elseif ($roldate == 7) {
            $mes = 'Julio';
        } elseif ($roldate == 8) {
            $mes = 'Agosto';
        } elseif ($roldate == 9) {
            $mes = 'Septiembre';
        } elseif ($roldate == 10) {
            $mes = 'Octubre';
        } elseif ($roldate == 11) {
            $mes = 'Noviembre';
        } elseif ($roldate == 12) {
            $mes = 'Diciembre';
        }
        $rol_2 = $this->pdf_ieced($id);

        $asunto = "Comprobante " . $rol->fecha;
        $titulo = "Comprobante  " . $rol->fecha . '.pdf';
        Mail::send('mails.clientes', ['cliente' => $usuario], function ($msj) use ($correo, $asunto, $rol_2, $titulo) {
            $msj->subject($asunto);
            $msj->from('rol@mdconsgroup.com', 'Sistema de Comprobantes SIAAM');
            $msj->to($correo);
            $msj->attachData($rol_2, $titulo, [
                'mime' => 'application/pdf',
            ]);
        });
        return 'ok';
    }
    public function guardarCiudad(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $value = [
            'ciudad_representante'      => $request->ciudad,
            'direccion_representante'   => $request->direccion,
            'id_usuariomod'             => $idusuario,
            'ip_modificacion'           => $ip_cliente,
        ];
        Ct_Clientes::where('identificacion', $request->cedula)->update($value);

        return "ok";
    }
}
