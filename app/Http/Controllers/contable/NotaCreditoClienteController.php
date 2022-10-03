<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Detalle_Credito_Clientes;
use Sis_medico\Ct_Detalle_Rubro_Credito;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Devolucion_Productos;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Rubros_Cliente;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\LogAsiento;
use Sis_medico\Log_usuario;
use Sis_medico\Numeros_Letras;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Validate_Decimals;
use Sis_medico\Contable;
use Illuminate\Support\Facades\Session;
use Sis_medico\LogConfig;

class NotaCreditoClienteController extends Controller
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

    public static function confgClientesComerciales()
    {

        /*$id_empresa = Session::get("id_empresa");
        $cuenta = "1.01.02.05.01";
        if($id_empresa == "1793135579001"){
            $cuenta = "1.01.02.01.01";
        }
        return $cuenta;*/

        //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/C_CLIENTE_CXC_CLIENTES_COMERCIALES'); //Cuentas por cobrar clientes comerciales
        $cuenta = LogConfig::busqueda('1.01.02.05.01');
        return $cuenta;
    }

    /************************************************************/
    /****************INDEX NOTA DE CREDITO CLIENTE***************
    /***********************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $clientes     = Ct_Clientes::where('estado', '1')->get();
        $nota_credito = Ct_Nota_Credito_Clientes::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
        return view('contable/nota_credito_cliente/index', ['nota_credito' => $nota_credito, 'clientes' => $clientes, 'empresa' => $empresa]);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        //$iva_param  = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        $id_plan_confg = LogConfig::busqueda('4.1.01.02');
        $iva_param  = Ct_Configuraciones::where('id_plan', $id_plan_confg)->first();

        $sucursales = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();
        return view('contable/nota_credito_cliente/create', ['iva_param' => $iva_param, 'empresa' => $empresa, 'sucursales' => $sucursales]);
    }

    /************************************************************/
    /***********BUSQUEDA DE CLIENTE POR IDENTIFICACION***********
    /***********************************************************/
    public function buscarClientexId(Request $request)
    {
        $nombre     = $request['term'];
        $data       = array();
        $id_empresa = $request->session()->get('id_empresa');
        $clientes   = Ct_Clientes::where('identificacion', 'like', "%{$nombre}%")->orWhere("nombre", "LIKE", "%{$nombre}%")->get();
        foreach ($clientes as $cliente) {
            $data[] = array('value' => $cliente->identificacion, 'nombre' => $cliente->nombre, 'direccion' => $cliente->direccion_representante, 'ciudad' => $cliente->ciudad_representante, 'mail' => $cliente->email_representante, 'telefono' => $cliente->telefono1_representante, 'tipo' => $cliente->clase);
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    /************************************************************/
    /*************BUSQUEDA DE CLIENTE POR NOMBRE*****************
    /***********************************************************/
    public function buscarCliente(Request $request)
    {
        $nombre = $request['term'];
        $data   = array();

        $id_empresa = $request->session()->get('id_empresa');
        $clientes   = Ct_Clientes::where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($clientes as $cliente) {
            $data[] = array('value' => $cliente->nombre, 'id' => $cliente->identificacion, 'direccion' => $cliente->direccion_representante, 'ciudad' => $cliente->ciudad_representante, 'mail' => $cliente->email_representante, 'telefono' => $cliente->telefono1_representante, 'tipo' => $cliente->clase);
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    /************************************************************/
    /************ BUSQUEDA DE RUBRO DE CLIENTE POR CODIGO********
    /***********************************************************/
    public function search_rubro_codigo(Request $request)
    {

        $codigo_rubro = $request['term'];
        $data         = array();
        //$data = null;

        $rubros = DB::table('ct_rubros_cliente')->where('estado', '1')->where('codigo', 'like', '%' . $codigo_rubro . '%')->get();
        foreach ($rubros as $rubr) {
            $data[] = array('value' => $rubr->codigo);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    /************************************************************/
    /************GUARDADO DE NOTA DE CREDITO CLIENTE*************/
    /***********************************************************/
    public function store_nota_credito(Request $request)
    {

        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $id_empresa        = $request->session()->get('id_empresa');
        $objeto_validar    = new Validate_Decimals();
        $fecha_nota        = $request['fecha_hoy'];
        //$iva_param         = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        //$iva_param         = \Sis_medico\Ct_Configuraciones::obtener_cuenta('VENTA_TARIFA_12 -4.1.01.02-');
        $id_plan_config = LogConfig::busqueda('4.1.01.02');
        $iva_param = Plan_Cuentas::where('id', $id_plan_config)->first();
        $ivaf              = $iva_param->iva;
        $num_fact_cancelar = $request['nro_factura'];
        $total             = 0;
        $nuevo_saldo       = 0;
        $total_final       = $objeto_validar->set_round($request['total']);
        DB::beginTransaction();
        $msj = "no";
      
        try {
            $ventas = Ct_ventas::where('estado', '1')
                ->where('id_cliente', $request['id_cliente'])
                ->where('id_empresa', $id_empresa)
                ->where('nro_comprobante', $num_fact_cancelar)->first();
            if ($request['contador'] > 0) {
                /************************************************************/
                /******Validacion a Insertar Sucursales y Punto de Emision**/
                /***********************************************************/
                if ($request['sucursal'] != 0) {
                    $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
                    $c_sucursal = $cod_sucurs->codigo_sucursal;
                    $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
                    $c_caja     = $cod_caj->codigo_caja;
                    $nfactura   = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                    if (!is_null($request['secuencial'])) {
                        $nfactura = $request['secuencial'];
                    }
                    $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $nfactura;
                } else {
                    $c_sucursal      = 0;
                    $c_caja          = 0;
                    $num_comprobante = 0;
                    $nfactura        = 0;
                }

                /************************************************************/
                /****************Guardado Ct_Asientos_Cabecera***************/
                /***********************************************************/
                $input_cabecera = [
                    'observacion'     => $request['concepto'],
                    'fecha_asiento'   => $fecha_nota,
                    'fact_numero'     => $num_comprobante,
                    'valor'           => $total_final,
                    'id_empresa'      => $id_empresa,
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];

                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                /************************************************************/
                /************Guardado Ct_Nota_Credito_Clientes***************/
                /***********************************************************/
                $input = [
                    'id_empresa'          => $id_empresa,
                    'id_cliente'          => $request['id_cliente'],
                    'check_sri'           => $request['check_archivo_sri'],
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'sucursal'            => $c_sucursal,
                    'punto_emision'       => $c_caja,
                    'numero_factura'      => $request['nro_factura'],
                    'nro_comprobante'     => $num_comprobante,
                    'secuencia'           => $nfactura,
                    'fecha'               => $request['fecha_hoy'],
                    'tipo'                => $request['tipo'],
                    'concepto'            => $request['concepto'],
                    'subtotal'            => $request['subtotal'],
                    'impuesto'            => $request['impuesto'],
                    'sub_sin_imp'         => $request['sub_sin_imp'],
                    'tar_iva_12'          => $request['tar_iva_12'],
                    'total_credito'       => $request['total'],
                    'total_deudas'        => $request['total_deudas'],
                    'total_abonos'        => $request['total_abonos'],
                    'total_nuevo_saldo'   => $request['total_nuevo_saldo'],
                    'observacion'         => $request['observaciones'],
                    'electronica'         => $request['electronica'],
                    'estado'              => '1',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];

                $id_credito = Ct_Nota_Credito_Clientes::insertGetId($input);
               
                /************************************************************/
                /************Guardado Ct_Detalle_Rubro_Credito**************/
                /***********************************************************/
                $iva = 0;
                if ($request['impuesto'] > 0) {
                    $iva = 1;
                }
                $valor       = 0;
                $primerarray = array();
                for ($i = 0; $i <= $request['contador']; $i++) {
                    if ($request['visibilidad' . $i] == '1') {
                        if (!is_null($request['codigo' . $i])) {
                            $valor += $request['valor' . $i];

                            if ($iva == 1) {
                                $ivasx = $valor * $ivaf;
                                $valor = $ivasx;
                            }

                            $consul_rub_client = Ct_Rubros_Cliente::where('codigo', $request['codigo' . $i])->first();
                            if (!is_null($consul_rub_client)) {

                                $segundoarray = [$consul_rub_client->haber, $request['valor' . $i]];
                                $key          = array_search($consul_rub_client->haber, array_column($primerarray, '0'));

                                if ($key !== false) {
                                    $valor2               = $primerarray[$key][1];
                                    $valor2               = $valor2 + $request['valor' . $i];
                                    $primerarray[$key][0] = $consul_rub_client->haber;
                                    $primerarray[$key][1] = $valor2;
                                } else {
                                    array_push($primerarray, $segundoarray);
                                }

                                Ct_Detalle_Rubro_Credito::create([
                                    'id_nt_cred_client' => $id_credito,
                                    'codigo'            => $request['codigo' . $i],
                                    'nombre_rubro'      => $request['rubro' . $i],
                                    'detalle'           => $request['detalle' . $i],
                                    'valor'             => $request['valor' . $i],
                                    'total_base'        => $request['total_base' . $i],
                                    'id_usuariocrea'    => $idusuario,
                                    'id_usuariomod'     => $idusuario,
                                    'ip_creacion'       => $ip_cliente,
                                    'ip_modificacion'   => $ip_cliente,
                                ]);
                            }
                        }
                    }
                }

                /*****************************************************************/
                /*****************Guardado de Asiento Detalle*********************/
                /*****************************************************************/

                //$plan_nota_credito = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                //$plan_nota_credito = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/C_CLIENTE_CXC_CLIENTES_COMERCIALES');
               
                $id_plan_config = LogConfig::busqueda('1.01.02.01.01');
                
            
                $desc_cuenta         = Plan_Cuentas::where('id', $id_plan_config)->first();

                if(is_null($desc_cuenta)){
                    DB::rollback();
                    return ['status'=>'error', 'msj'=>"No se ha configurado la cuenta"];
                }
                // if(Auth::user()->id == "0957258056"){
                //     dd($desc_cuenta);
                // }
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $desc_cuenta->id,
                    'descripcion'         => $desc_cuenta->nombre,
                    'fecha'               => $fecha_nota,
                    'haber'               => $request['total'],
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
                $id_plan_config = LogConfig::busqueda('4.1.07.01');
                $plan_nota_credito2 = Plan_Cuentas::where('id', $id_plan_config)->first(); //DEVOLUCION EN VENTAS
                //$plan_nota_credito2 = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/C_CLIENTE_DEVOLUCION_VENTAS');

                // $id_plan_config = LogConfig::busqueda('1370');
                // $plan_nota_credito2 = Plan_Cuentas::where('id', $id_plan_config)->first();

                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_nota_credito2->id,
                    'descripcion'         => $plan_nota_credito2->nombre,
                    'fecha'               => $fecha_nota,
                    'debe'                => $request['total'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
                /*
                for ($file = 0; $file < count($primerarray); $file++) {
                $cuent_descrip = Plan_Cuentas::where('id', $primerarray[$file][0])->first();
                $cuenta = $primerarray[$file][0];
                $debe =  number_format($primerarray[$file][1], 2, '.', '');
                $debes = 0;
                if ($iva == 1) {
                $debes = $debe * $ivaf;
                $debe = $debe + $debes;
                }

                } */
               
                for ($i = 0; $i <= $request['contador_a']; $i++) {
                    if (!is_null($request['abono_a' . $i]) && $request['abono_a' . $i] > 0) {
                        $consulta_venta    = null;
                        $input_comprobante = null;
                        if (!is_null($request['id_actualiza' . $i])) {
                            //dd($request['id_actualiza'.$i]);
                            $consulta_venta = Ct_Ventas::where('id', $request['id_actualiza' . $i])->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->first();
                            if (!is_null($consulta_venta)) {
                                Ct_Detalle_Credito_Clientes::create([
                                    'id_not_cred'       => $id_credito,
                                    'id_factura'        => $consulta_venta->id,
                                    'fecha_emision'     => $request['emision' . $i],
                                    'fecha_vence'       => $request['vence' . $i],
                                    'tipo'              => $request['tipo_a' . $i],
                                    'secuencia_factura' => $request['numero' . $i],
                                    'concepto'          => $request['observacion' . $i],
                                    'saldo'             => $request['saldo_a' . $i],
                                    'abono'             => $request['abono_a' . $i],
                                    'nuevo_saldo'       => $request['nuevo_saldo' . $i],
                                    'estado'            => '1',
                                    'ip_creacion'       => $ip_cliente,
                                    'ip_modificacion'   => $ip_cliente,
                                    'id_usuariocrea'    => $idusuario,
                                    'id_usuariomod'     => $idusuario,
                                ]);
                            }
                        }
                    }
                    $consulta_venta = Ct_ventas::where('id', $request['id_actualiza' . $i])
                        ->where('id_empresa', $id_empresa)
                        ->where('estado', '<>', '0')
                        ->first();
                    //dd($consulta_venta);
                    if ($request['abono_a' . $i] > 0) {
                        if (!is_null($consulta_venta)) {
                            if ($request['abono_a' . $i] > ($consulta_venta->valor_contable)) {
                                $nuevo_saldo = $request['abono_a' . $i] - $consulta_venta->valor_contable;
                            } else {
                                $nuevo_saldo = $consulta_venta->valor_contable - $request['abono_a' . $i];
                            }

                            $nuevo_saldof    = $objeto_validar->set_round($nuevo_saldo);
                            $input_actualiza = null;

                            if ($nuevo_saldof != 0) {
                                $input_actualiza = [
                                    'estado_pago'     => '2', //Aun en pago
                                    'valor_contable'  => $nuevo_saldof,
                                    'ip_creacion'     => $ip_cliente,
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariocrea'  => $idusuario,
                                    'id_usuariomod'   => $idusuario,
                                ];
                            } else {
                                $input_actualiza = [
                                    'estado_pago'     => '3', //Pagado
                                    'valor_contable'  => $nuevo_saldof,
                                    'ip_creacion'     => $ip_cliente,
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariocrea'  => $idusuario,
                                    'id_usuariomod'   => $idusuario,
                                ];
                            }

                            $consulta_venta->update($input_actualiza);
                        }
                    }
                }

                $empresa = Empresa::find($id_empresa);
                $sri     = "";
                if ($empresa->electronica == 1 && $request['electronica'] == 1) {
                    $sri = $this->getSRI($id_credito);
                }

                

                DB::commit();
                return ['status'=>'success', "msj"=>"Guardado con exito"];
                return $id_credito;
            } else {
                DB::commit();
                return 'false';
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['status'=>"error", 'msj'=>"Error al guardar", 'error' => $e->getMessage()];
        }
    }

    /***************************************************/
    /***OBTENEMOS SECUENCIA DE LA NOTA CREDITO CLIENTE**/
    /***************************************************/
    public function obtener_numero_factura($idempresa, $sucursal, $punto_emision)
    {

        $contador_ntc = Ct_Nota_Credito_Clientes::where('id_empresa', $idempresa)
            ->where('sucursal', $sucursal)
            ->where('punto_emision', $punto_emision)->get()->count();

        if ($contador_ntc == 0) {

            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            return $numero_factura;
        } else {

            $max_id = Ct_Nota_Credito_Clientes::where('id_empresa', $idempresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)->latest()->first();
            $max_id = intval($max_id->secuencia);

            if (($max_id >= 1) && ($max_id < 10)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if (($max_id >= 10) && ($max_id < 100)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
                return $numero_factura;
            }
        }
    }

    /************************************************************/
    /***************CREACION DE PDF NOTA CREDITO CLIENTE*********
    /***********************************************************/
    public function crear_pdf_nota_credito(Request $request, $id)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $nota_cred  = Ct_Nota_Credito_Clientes::where('id', $id)->first();
        //   dd($nota_cred->nro_autorizacion);
        $asiento_cabecera = null;
        $empresa          = null;

        if (!is_null($nota_cred)) {

            $det_rub = Ct_Detalle_Rubro_Credito::where('estado', '1')->where('id_nt_cred_client', $nota_cred->id)->get();
            $empresa = Empresa::where('id', $nota_cred->id_empresa)->first();
            $cliente = Ct_Clientes::where('estado', '1')->where('identificacion', $nota_cred->id_cliente)->first();

            $detalle_credito = Ct_Detalle_Credito_Clientes::where('id_not_cred', $nota_cred->id)->get();
            $ventas          = Ct_Ventas::where('estado', '1')->where('id', $detalle_credito[0]->id_factura)->where('id_empresa', $id_empresa)->first();
            $letras          = new Numeros_Letras();
            $total_str       = $letras->convertir(number_format($nota_cred->total_credito, 2, '.', ''), "DOLARES", "CTVS");

            $asiento_cabecera = Ct_Asientos_Cabecera::where('estado', '1')->where('id', $nota_cred->id_asiento_cabecera)->first();
        }

        $asiento_detalle = null;

        if ($asiento_cabecera != null) {
            $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        }

        //if (!is_null($nota_cred)){

        $vistaurl = "contable.nota_credito_cliente.pdf_nota_credito2";
        $view     = \View::make($vistaurl, compact('nota_cred', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'cliente', 'ventas', 'det_rub'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Nota de Crédito Clientes' . $id . '.pdf');
        //}

        //return 'error';

    }

    /************************************************************/
    /*****************BUZQUEDA DE FACTURAS DE VENTAS*************
    /***********************************************************/
    public function obtener_num_fact(Request $request)
    {

        $num  = $request['term'];
        $data = array();

        $id_empresa = $request->session()->get('id_empresa');

        $fact_venta = Ct_Ventas::where('nro_comprobante', 'like', '%' . $num . '%')
            ->where('estado', '1')
            ->where('valor_contable', '>', '0')
            ->where('id_empresa', $id_empresa)
            ->get();

        foreach ($fact_venta as $fact_venta) {
            $data[] = array('value' => $fact_venta->nro_comprobante, 'num_asiento' => $fact_venta->id_asiento, 'cliente' => $fact_venta->id_cliente, 'nomb_client' => $fact_venta->cliente->nombre);
        }

        //dd($fact_venta);

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    /************************************************************/
    /**************ANULACION ASIENTO NOTA DE CREDITO CLIENTE*****
    /***********************************************************/
    public function anular_asiento($id)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $asiento         = Ct_Asientos_Cabecera::findorfail($id);
        $asiento->estado = 1;
        $asiento->save();
        $detalles = $asiento->detalles;

        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => 'ANULACION ' . $asiento->observacion,
            'fecha_asiento'   => $asiento->fecha_asiento,
            'fact_numero'     => $asiento->fact_numero,
            'id_empresa'      => $asiento->id_empresa,
            'valor'           => $asiento->valor,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        foreach ($detalles as $value) {
            $value->estado = 1;
            $value->save();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento,
                'id_plan_cuenta'      => $value->id_plan_cuenta,
                'debe'                => $value->haber,
                'haber'               => $value->debe,
                'descripcion'         => $value->descripcion,
                'fecha'               => $asiento->fecha_asiento,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        return "ok";
    }

    /************************************************************/
    /**************ANULAR NOTA DE CREDITO CLIENTE****************
    /***********************************************************/
    public function anular($id, Request $request)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $fechahoy     = Date('Y-m-d H:i:s');
        $id_empresa   = $request->session()->get('id_empresa');
        $nota_credito = Ct_Nota_Credito_Clientes::where('id', $id)->where('id_empresa', $id_empresa)->first();

        if (!is_null($nota_credito)) {

            $input = [
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];

            $nota_credito->update($input);

            $consulta_cabecera_nc_client = Ct_Asientos_Cabecera::where('estado', '1')->where('id', $nota_credito->id_asiento_cabecera)->first();

            /*    $input_asiento = [
            'estado'  => '0',
            ];

            $consulta_cabecera_nc_client->update($input_asiento); */

            //Creamos un nuevo Registro de Anulacion Nota Credito Cliente
            $input_cabecera = [
                'observacion'     => 'ANULACIÓN NOTA DE CRÉDITO :' . $nota_credito->secuencia,
                'fecha_asiento'   => $fechahoy,
                'id_empresa'      => $consulta_cabecera_nc_client->id_empresa,
                'valor'           => $consulta_cabecera_nc_client->valor,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cab   = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $consulta_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $consulta_cabecera_nc_client->id)->get();
            if ($consulta_detalle != '[]') {
                foreach ($consulta_detalle as $value) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cab,
                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $fechahoy,
                        'haber'               => $value->debe,
                        'debe'                => $value->haber,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            }
            LogAsiento::anulacion("NC-C", $id_asiento_cab, $consulta_cabecera_nc_client->id);
            // Log_Contable::create([
            //     'tipo'           => 'NCC',
            //     'valor_ant'      => $consulta_cabecera_nc_client->valor,
            //     'valor'          => $consulta_cabecera_nc_client->valor,
            //     'id_usuariocrea' => $idusuario,
            //     'id_usuariomod'  => $idusuario,
            //     'observacion'    => $consulta_cabecera_nc_client->concepto,
            //     'id_ant'         => $consulta_cabecera_nc_client->id,
            //     'id_referencia'  => $id_asiento_cab,
            // ]);
            $detalle_credito = Ct_Detalle_Credito_Clientes::where('id_not_cred', $nota_credito->id)->get();
            if (count($detalle_credito) > 0) {
                foreach ($detalle_credito as $credito) {
                    $x = Ct_ventas::find($credito->id_factura);
                    if (!is_null($x)) {
                        $valor             = $x->valor_contable;
                        $x->valor_contable = $valor + $credito->abono;
                        $x->id_usuariomod  = $idusuario;
                        $x->save();
                    }
                }
            }
            return redirect()->route('nota_credito_cliente.index');
        }
    }

    /************************************************************/
    /***************BUSCAR DATA CT NOTA CREDITO******************
    /***********************************************************/
    public function buscar_parametros(Request $request)
    {

        if (!is_null($request['id_nota'])) {

            $nota_credito = Ct_Nota_Credito_Clientes::where('estado', '1')->where('id', $request['id_nota'])->first();
            $id_nota      = $nota_credito->id;
            $secuencia    = $nota_credito->secuencia;
            $id_asiento   = $nota_credito->id_asiento_cabecera;

            return ['id_nota' => $id_nota, 'secuencia' => $secuencia, 'id_asiento' => $id_asiento];
        } else {

            return "false";
        }
    }

    /************************************************************/
    /**********OBTENER DETALLE DE DEUDAS DEL CLIENTE*************
    /***********************************************************/
    public function obtener_deudas_cliente(Request $request)
    {

        $id_cliente      = $request['id_cliente'];
        $num_comprobante = $request['num_fact'];
        $id_empresa      = $request->session()->get('id_empresa');

        $detalle_deuda = '[]';

        $detalle_deuda = DB::table('ct_clientes as p')
            ->join('ct_ventas as cv', 'cv.id_cliente', 'p.identificacion')
            ->where('cv.id_cliente', $id_cliente)
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.nro_comprobante', $num_comprobante)
            ->where('cv.estado_pago', '<', '3')
            ->where('cv.estado', '>', '0')
            ->Where('cv.estado_pago', '>', '0')
            ->where('cv.valor_contable', '>', '0')
            //->where('cv.total_final','>','0')
            ->select('cv.id', 'cv.numero', 'cv.nro_comprobante', 'cv.fecha', 'cv.valor_contable')
            ->get();
        //->select('cv.id','cv.numero','cv.nro_comprobante','cv.fecha','cv.valor_contable')

        if ($detalle_deuda != '[]') {
            //$data = [$detalle_deuda[0]->id,$detalle_deuda[0]->numero,$detalle_deuda[0]->fecha,$detalle_deuda[0]->valor_contable,$detalle_deuda];
            $data = [$detalle_deuda[0]->id, $detalle_deuda[0]->numero, $detalle_deuda[0]->fecha, $detalle_deuda[0]->valor_contable, $detalle_deuda];
            return $data;
        } else {
            return ['value' => 'false'];
        }
    }

    /*******************************************************************************/
    /**********OBTENER DETALLE DE DEUDAS DEL CLIENTE SIN NUMERO FACTURA*************
    /*******************************************************************************/
    public function buscar_deudas_cliente(Request $request)
    {
        $id_cliente = $request['id_cliente'];
        $id_empresa = $request->session()->get('id_empresa');

        $detalle_deuda = '[]';

        $detalle_deuda = DB::table('ct_clientes as p')
            ->join('ct_ventas as cv', 'cv.id_cliente', 'p.identificacion')
            ->where('cv.id_cliente', $id_cliente)
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado', '>', '0')
            ->where('cv.valor_contable', '>', '0')
            ->select('cv.id', 'cv.numero', 'cv.nro_comprobante', 'cv.fecha', 'cv.valor_contable')
            ->get();

        if ($detalle_deuda != '[]') {
            $data = [$detalle_deuda[0]->id, $detalle_deuda[0]->numero, $detalle_deuda[0]->fecha, $detalle_deuda[0]->valor_contable, $detalle_deuda];
            return $data;
        } else {
            return ['value' => 'false'];
        }
    }

    /************************************************************/
    /**************OBTENER SUMA TOTAL DEUDAS CLIENTES************
    /***********************************************************/
    public function obtener_total_deudas(Request $request)
    {

        $id_cliente      = $request['id_cliente'];
        $num_comprobante = $request['num_fact'];
        $id_empresa      = $request->session()->get('id_empresa');

        $total_deuda_cliente = '[]';

        $total_deuda_cliente = DB::table('ct_clientes as p')
            ->join('ct_ventas as cv', 'cv.id_cliente', 'p.identificacion')
            ->where('cv.id_cliente', $id_cliente)
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.nro_comprobante', $num_comprobante)
            ->where('cv.estado_pago', '<', '3')
            ->Where('cv.estado_pago', '>', '0')
            ->where('cv.total_final', '>', '0')
            ->select(DB::raw("SUM(cv.valor_contable) as total"))
            ->first();

        //if(!is_null($total_deuda_cliente)){
        if ($total_deuda_cliente != '[]') {

            //return ['total_deuda' => $total_deuda_cliente->total];
            $data = ['total_deuda' => $total_deuda_cliente->total];
            return $data;
        } else {
            //return "false";
            return ['value' => 'false'];
        }
    }

    /************************************************************/
    /**************OBTENER SUMA TOTAL DEUDAS CLIENTES************
    /***********************************************************/
    public function suma_deudas_clientes(Request $request)
    {

        $id_cliente = $request['id_cliente'];
        $id_empresa = $request->session()->get('id_empresa');

        $total_deuda_cliente = '[]';

        $total_deuda_cliente = DB::table('ct_clientes as p')
            ->join('ct_ventas as cv', 'cv.id_cliente', 'p.identificacion')
            ->where('cv.id_cliente', $id_cliente)
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado_pago', '<', '3')
            ->Where('cv.estado_pago', '>', '0')
            ->where('cv.total_final', '>', '0')
            ->select(DB::raw("SUM(cv.total_final) as total"))
            ->first();

        if ($total_deuda_cliente != '[]') {

            $data = ['total_deuda' => $total_deuda_cliente->total];
            return $data;
        } else {

            return ['value' => 'false'];
        }
    }
    public function edit_nota_credito($id, Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $nota_cred_client = Ct_Nota_Credito_Clientes::where('id', $id)->where('id_empresa', $id_empresa)->first();
        $sucursales = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();

        if (!is_null($nota_cred_client)) {

            $det_rub_cred    = Ct_Detalle_Rubro_Credito::where('id_nt_cred_client', $nota_cred_client->id)->get();
            $det_cred_client = Ct_Detalle_Credito_Clientes::where('id_not_cred', $nota_cred_client->id)->get();
            //Obtenemos las Deudas del Cliente
            $id_cliente = $nota_cred_client->id_cliente;
            if ($nota_cred_client->tipo_nota == 1) {
                return view('contable/nota_credito_cliente/edit', ['nota_cred_client' => $nota_cred_client, 'det_cred_client' => $det_cred_client, 'det_rub_cred' => $det_rub_cred, 'empresa' => $empresa]);
            } else {
          
                $productosdev = Ct_Devolucion_Productos::where("id_nota_credito", $nota_cred_client->id)->get();
               // dd($productosdev);
                return view('contable/nota_credito_cliente/edit2', ['nota_cred_client' => $nota_cred_client, 'det_cred_client' => $det_cred_client, 'det_rub_cred' => $det_rub_cred, 'empresa' => $empresa, 'productosdev'=>$productosdev]);
               // return view('contable/nota_credito_cliente/show', ['nota_cred_client' => $nota_cred_client, 'det_cred_client' => $det_cred_client, 'det_rub_cred' => $det_rub_cred, 'empresa' => $empresa]);
            }
        }
    }

    /************************************************************/
    /*****************BUSCAR NOTA DE CREDITO*********************
    /************************************************************/

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $constraints = [
            'id_asiento_cabecera' => $request['buscar_asiento'],
            'id'                  => $request['numero'],
            'id_cliente'          => $request['id_cliente'],
            'concepto'            => $request['concepto'],
            'id_empresa'          => $id_empresa,
        ];

        //$id_empresa = $request->session()->get('id_empresa');
        $clientes     = Ct_Clientes::where('estado', '1')->get();
        $nota_credito = $this->doSearchingQuery($constraints);

        return view('contable/nota_credito_cliente/index', ['nota_credito' => $nota_credito, 'clientes' => $clientes, 'empresa' => $empresa]);
    }
    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Nota_Credito_Clientes::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->orderby('id', 'asc')->paginate(10);
    }
    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $consulta       = null;
        $fecha_creacion = date('Y/m/d');
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'id_asiento_cabecera' => $request['buscar_asiento2'],
            'id'                  => $request['numero2'],
            'id_cliente'          => $request['id_cliente2'],
            'concepto'            => $request['concepto2'],
            'id_empresa'          => $id_empresa,
        ];

        $consulta = Ct_Nota_Credito_Clientes::query();
        $fields   = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $consulta = $consulta->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        $consulta = $consulta->get();

        Excel::create('Nota de Credito Clientes' . ' ' . $fecha_creacion, function ($excel) use ($empresa, $consulta) {

            $excel->sheet('Nota de Credito Clientes', function ($sheet) use ($empresa, $consulta) {

                $sheet->mergeCells('C1:R1');
                $sheet->cell('C1', function ($cell) use ($empresa) {
                    if (!is_null($empresa)) {
                        $cell->setValue($empresa->nombrecomercial . ':' . $empresa->id);
                    }
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:B1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(220);
                    $objDrawing->setWidth(120);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C2:R2');
                $sheet->cell('C2', function ($cell) use ($empresa) {
                    if (!is_null($empresa)) {
                        $cell->setValue('INFORME NOTA DE CREDITO');
                    }
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A3:B3');
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue('# Nota Crédito');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:D3');
                $sheet->cell('C3', function ($cell) {

                    $cell->setValue('# de Asiento');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E3', function ($cell) {

                    $cell->setValue('Cliente');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F3', function ($cell) {

                    $cell->setValue('Fecha');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G3', function ($cell) {

                    $cell->setValue('Tipo');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('H3:I3');
                $sheet->cell('H3', function ($cell) {

                    $cell->setValue('Detalle');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('J3:K3');
                $sheet->cell('J3', function ($cell) {

                    $cell->setValue('Total Crédito');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('L3:M3');
                $sheet->cell('L3', function ($cell) {

                    $cell->setValue('Total Deudas');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('N3:O3');
                $sheet->cell('N3', function ($cell) {

                    $cell->setValue('Total Abono');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P3', function ($cell) {

                    $cell->setValue('Estado');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Q3', function ($cell) {

                    $cell->setValue('Creado Por');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('R3', function ($cell) {

                    $cell->setValue('Factura Aplica');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#D1D1D1');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'J' => '0.00',
                    'L' => '0.00',
                    'N' => '0.00',
                ));

                $i     = 4;
                $total = 0;
                foreach ($consulta as $value) {
                    $nueva = ct_detalle_credito_clientes::where('id', $value->id)->first();
                    //dd($nueva);
                    $total += $value->total_credito;

                    $sheet->mergeCells('A' . $i . ':B' . $i);
                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':D' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->id_asiento_cabecera);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->cliente->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        $fech_inver = date("d/m/Y", strtotime($value->fecha));
                        $cell->setValue($fech_inver);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->tipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('H' . $i . ':I' . $i);
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->concepto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('J' . $i . ':K' . $i);
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_credito);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('L' . $i . ':M' . $i);
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_deudas);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('N' . $i . ':O' . $i);
                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_abonos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        if (($value->estado) != 0) {
                            $cell->setValue('ACTIVO');
                        } else {
                            $cell->setValue('ANULADO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->usercrea->nombre1 . $value->usercrea->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('R' . $i, function ($cell) use ($nueva) {
                        if (!empty($nueva->secuencia_factura)) {
                            $cell->setValue($nueva->secuencia_factura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cell->setValue('No tiene secuencia');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $i++;
                }

                $sheet->mergeCells('H' . $i . ':I' . $i);
                $sheet->cell('H' . $i, function ($cell) {
                    $cell->setValue('Total');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J' . $i . ':K' . $i);
                $sheet->cell('J' . $i, function ($cell) use ($total) {
                    $cell->setValue($total);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('L' . $i . ':M' . $i);
                $sheet->cell('L' . $i, function ($cell) use ($total) {
                    $cell->setValue($total);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('N' . $i . ':O' . $i);
                $sheet->cell('N' . $i, function ($cell) use ($total) {
                    $cell->setValue($total);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }
    public function getSRI($id)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $nota_credito = Ct_Nota_Credito_Clientes::find($id);
        $getType      = $nota_credito->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $data['empresa']     = $nota_credito->id_empresa;
        $cliente['cedula']   = $nota_credito->id_cliente;
        $cliente['tipo']     = $getType;
        $cliente['nombre']   = $nota_credito->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $nota_credito->cliente->nombre);
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
        $cliente['email']     = $nota_credito->cliente->email;
        $cliente['telefono']  = $nota_credito->cliente->telefono;
        $direccion['calle']   = $nota_credito->cliente->direccion_representante;
        $direccion['ciudad']  = $nota_credito->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $id)->first();
        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $details->id_factura)->get();
        $ventas        = Ct_ventas::find($details->id_compra);
        $tax           = 0;

        $ventas                  = Ct_ventas::find($details->id_factura);
        $factura['comprobante']  = $ventas->nro_comprobante;
        $factura['fechaemision'] = date("d/m/Y", strtotime($nota_credito->fecha));
        $factura['motivo']       = $nota_credito->concepto;
        $data['factura']         = $factura;

        //se envian los productos
        $cant = 0;

        foreach ($venta_detalle as $value) {
            //se envian los productos
            $producto['sku']       = $value->id_ct_productos; //ID EXAMEN
            $producto['nombre']    = $value->nombre; // NOMBRE DEL EXAMEN
            $producto['cantidad']  = $value->cantidad;
            $producto['precio']    = $value->precio; //DETALLE
            $pricetot              = $value->cantidad * $value->precio;
            $producto['descuento'] = $value->descuento;
            $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
            $tax                   = "0";
            if ($value->check_iva == 1) {
                $tax = $pricetot * $value->porcentaje;
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

        //$info_adicional['nombre']      = $nota_credito->cliente->nombre;
        $info_adicional['nombre']      = "EMAIL";
        $info_adicional['valor']       = $nota_credito->cliente->email_representante;
        $info[0]                       = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $pago['forma_pago']            = '01';
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;

      

        $envio = ApiFacturacionController::crearNotasCredito($data);
        $nota_credito->update([
            'nro_comprobante' => $envio->comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);

        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "NOTA DE CREDITO ",
            'dato_ant1'   => $nota_credito->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);
    }
    public function informe_notacrecliente(Request $request)
    {
        //5000 id_venta revisar OJOOOOOOO

        $id_empresa           = $request->session()->get('id_empresa');
        $empresa              = Empresa::where('id', $id_empresa)->first();
        $fecha_desde          = $request['fecha_desde'];
        $gastos               = $request['esfac_contable'];
        $variable             = 0;
        $variable2            = 0;
        $totales              = 0;
        $subtotal12           = 0;
        $subtotal0            = 0;
        $subtotal             = 0;
        $descuento            = 0;
        $impuesto             = 0;
        $total_base_retencion = 0;
        if ($gastos == null) {
            $gastos = 0;
        }
        $tipo = $request['tipo'];
        if ($tipo == null) {
            $tipo = 0;
        }
        if ($request['excelF'] == 1) {
            $this->excel_ncc($request);
        }
        $fecha_hasta = $request['fecha_hasta'];
        $proveedor   = $request['id_cliente'];
        $concepto    = $request['concepto'];
        $secuencia   = $request['secuencia'];

        $deudas      = [];
        $deudas2     = [];
        $proveedores = Ct_Clientes::where('estado', '<>', '0')->paginate(50);
        if (!is_null($fecha_hasta)) {
            $deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia);

            $deudas2 = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable2, $tipo, 0, $secuencia);

            foreach ($deudas2 as $value) {
                if ($value != null) {
                    if ($value->estado != 0) {
                        $totales += $value->total_credito;
                        $subtotal12 += $value->tar_iva_12;
                        $subtotal0 += $value->sub_sin_imp;
                        $subtotal += $value->sub_sin_imp + $value->tar_iva_12;
                        $descuento += $value->descuento;
                        $impuesto += $value->impuesto;
                        $total_base_retencion += $value->sub_sin_imp + $value->tar_iva_12;
                    }
                }
            }
        }

        //dd($deudas);
        return view('contable/nota_credito_cliente/informe_notacreditoclientes', ['informe' => $deudas, 'secuencia' => $secuencia, 'empresa' => $empresa, 'totales' => $totales, 'subtotal0' => $subtotal0, 'subtotal' => $subtotal, 'subtotal12' => $subtotal12, 'impuesto' => $impuesto, 'proveedores' => $proveedores, 'descuento' => $descuento, 'fecha_hasta' => $fecha_hasta, 'fecha_desde' => $fecha_desde, 'id_proveedor' => $proveedor, 'tipo' => $tipo, 'total_base_retencion' => $total_base_retencion]);
    }

    public function autocomplete(Request $request)
    {
        $codigo = $request['term'];
        $data   = null;

        $productos = Ct_Clientes::where('identificacion', 'like', $codigo . '%')
            ->orwhere('nombre', 'like', '%' . $codigo . '%')
            ->paginate(20);

        $data = array();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->identificacion);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $referencia, $r, $secuencia)
    {
        //$deudas = $this->informe_final($proveedor, $fecha_desde, $fecha_hasta, $id_empresa, $variable, $tipo, 0, $secuencia);
        $deudas = null;
        // no modificar por favor
        $deudas = Ct_Nota_Credito_Clientes::where('id_empresa', $id_empresa);

        if (!is_null($fecha_desde)) {
            $deudas = $deudas->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
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

            if (Auth::user()->id == '0922729587') {
                //dd($deudas);

            }
        }

        return $deudas;
    }
    public function excel_ncc(Request $request)
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
        Excel::create('Informe Nota de Credito Cliente ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $consulta, $gastos, $fecha_hasta, $fecha_desde) {
            $excel->sheet('Informe Nota de Credito Cliente', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $consulta, $gastos) {
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

                    $cell->setValue($empresa->nombrecomercial . ' ' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C3:Q3');
                $sheet->cell('C3', function ($cell) {

                    $cell->setValue("INFORME NOTA DE CREDITO DE CLIENTES ");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:Q4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {

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

                    $cell->setValue('FECHA');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B5', function ($cell) {

                    $cell->setValue('NUMERO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C5', function ($cell) {

                    $cell->setValue('RUC');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D5', function ($cell) {

                    $cell->setValue('CLIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E5', function ($cell) {

                    $cell->setValue('PACIENTE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F5', function ($cell) {
                    $cell->setFontColor('#FFFFFF');

                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });;
                $sheet->cell('G5', function ($cell) {

                    $cell->setValue('DETALLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H5', function ($cell) {

                    $cell->setValue('BASE IMPONIBLE');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I5', function ($cell) {

                    $cell->setValue('SUBTOTAL 12');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J5', function ($cell) {

                    $cell->setValue('SUBTOTAL 0');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K5', function ($cell) {

                    $cell->setValue('SUBTOTAL ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L5', function ($cell) {

                    $cell->setValue('DESCUENTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M5', function ($cell) {

                    $cell->setValue('IMPUESTO ');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N5', function ($cell) {

                    $cell->setValue('TOTAL');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O5', function ($cell) {

                    $cell->setValue('ESTADO');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P5', function ($cell) {

                    $cell->setValue('CREADO POR');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q5', function ($cell) {

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
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(60)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(20)->setAutosize(false);
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
        $x              = 0;
        $valor          = 0;
        $resta          = 0;
        $totales        = 0;
        $subtotal12     = 0;
        $subtotal0      = 0;
        $subtotal       = 0;
        $descuento      = 0;
        $impuesto       = 0;
        $finalsub       = 0;
        $base_imponible = 0;
        $detalle_base   = 0;
        foreach ($consulta as $value) {
            if ($value != null) {
                if ($value->estado != 0) {
                    if ($value->electronica == 1) {
                        $subtotal += $value->sub_sin_imp + $value->tar_iva_12 + $value->descuento;
                    } else {
                        $subtotal += $value->sub_sin_imp + $value->tar_iva_12;
                    }

                    $detalle_base = 0;

                    $detalle_base = $value->tar_iva_12 + $value->sub_sin_imp;

                    $base_imponible = $value->tar_iva_12 + $value->sub_sin_imp + $base_imponible;

                    $subtotal12 += $value->tar_iva_12;

                    $subtotal0 += $value->sub_sin_imp;

                    $descuento += $value->descuento;
                    $impuesto += $value->impuesto;
                    $finalsub = $value->tar_iva_12 + $value->sub_sin_imp;
                    $totales += $value->total_credito;
                }

                $sheet->cell('A' . $i, function ($cell) use ($value) {

                    $cell->setValue(date("d-m-Y", strtotime($value->fecha)));
                    $cell->setFontWeight('bold');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('B' . $i, function ($cell) use ($value) {

                    $cell->setValue($value->nro_comprobante);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell,1);
                });

                $sheet->cell('C' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);

                    $cell->setValue(' ' . $value->id_cliente);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('G'.$i.':H'.$i);
                $sheet->cell('D' . $i, function ($cell) use ($value) {

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

                    // $this->setSangria($cont, $cell);
                    $cell->setValue($value->tipo);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');
                    $cell->setValue($value->concepto . "# Asiento " . $value->id_asiento);
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H' . $i, function ($cell) use ($value, $detalle_base) {

                    // $this->setSangria($cont, $cell);
                    $cell->setValignment('left');

                    //$cell->setValue($value->subtotal);

                    $cell->setValue(number_format($detalle_base, 2, '.', ','));
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);
                    if ($value->tar_iva_12 == null) {
                        $cell->setValue('0,00');
                    } else {
                        $cell->setValue($value->tar_iva_12);
                    }
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);
                    if ($value->sub_sin_imp == null) {
                        $cell->setValue('0,00');
                    } else {
                        $cell->setValue($value->sub_sin_imp);
                    }

                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K' . $i, function ($cell) use ($finalsub, $value) {

                    // $this->setSangria($cont, $cell);

                    $cell->setValue($finalsub);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);
                    if ($value->descuento == null) {
                        $cell->setValue('0,00');
                    } else {
                        $cell->setValue($value->descuento);
                    }

                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->impuesto);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N' . $i, function ($cell) use ($value) {

                    // $this->setSangria($cont, $cell);
                    if ($value->electronica == 1) {
                        $get = $value->total_credito - $value->descuento;
                        if ($value->estado == 0) {
                            $cell->setBackground('#E64725');
                        }
                        $cell->setValue($get);
                    } else {
                        if ($value->estado == 0) {
                            $cell->setBackground('#E64725');
                        }
                        $cell->setValue($value->total_credito);
                    }
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $i, function ($cell) use ($value) {

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

                    // $this->setSangria($cont, $cell);

                    $cell->setValue($value->usuario->nombre1 . " " . $value->usuario->apellido1);
                    $cell->setValignment('center');
                    if ($value->estado == 0) {
                        $cell->setBackground('#E64725');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($value) {

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
        $sheet->cell('G' . $i, function ($cell) {

            // $this->setSangria($cont, $cell);

            $cell->setValue("TOTAL :");
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
        });
        $sheet->cell('H' . $i, function ($cell) use ($base_imponible) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($base_imponible);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell('I' . $i, function ($cell) use ($subtotal12) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal12);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('J' . $i, function ($cell) use ($subtotal0) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal0);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($subtotal) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($subtotal);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($descuento) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($descuento);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('M' . $i, function ($cell) use ($impuesto) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($impuesto);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('N' . $i, function ($cell) use ($totales) {

            // $this->setSangria($cont, $cell);

            $cell->setValue($totales);
            $cell->setValignment('center');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        return $i;
    }
    public function index_parcial(Request $request)
    {

        return view('contable.nota_credito_cliente.index_parcial');
    }
    public function create2(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $clientes   = Ct_Clientes::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();
        return view('contable.nota_credito_cliente.create2', ['empresa' => $empresa, 'clientes' => $clientes, 'sucursales' => $sucursales]);
    }
    public function getcomprobante(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos  = [];
        if ($request['search'] != null) {
            $productos = Ct_ventas::where('nro_comprobante', 'LIKE', "%{$request['search']}%")
                        ->where('id_empresa', $id_empresa)
                        ->select('ct_ventas.id as id', DB::raw('CONCAT(nombre_cliente," | ",nro_comprobante) as text'))
                        ->where('estado', '<>', 0)
                        ->where('valor_contable', '>', '0')->get();
        }
        // /DB::raw('CONCAT(codigo," | ",nombre) as text')
        return response()->json($productos);
    }
    public function newData(Request $request)
    {
        // dd("aquiii");
        $id_factura = $request['id_factura'];
        $venta      = Ct_ventas::find($id_factura);
        $tabla = NotaCreditoClienteController::tableNotaCredito($venta);
        return $tabla;
    }

    public static function tableNotaCredito($ventas)
    {
        $table = "";
        $total = 0;

        foreach ($ventas->detalles as $value) {

            $cantidad = 0;
            $codigoProduto = "";

            $validate = Ct_Devolucion_Productos::where('id_factura', $ventas->id)->where('codigo', $value->id_ct_productos)->where('estado', 1)
                ->groupBy('codigo')->select(DB::raw('COUNT(cantidad) as cantidad'), 'codigo')->first();
            if (!is_null($validate)) {
                $cantidad = $validate->cantidad;
                $codigoProduto = $validate->codigo;
            }

            for ($i = 0; $i < $value->cantidad; $i++) {

                if ($value->id_ct_productos == $codigoProduto and $cantidad > 0) {
                    $cantidad--;
                } else {
                    $iva_producto = 0;
                    if ($value->check_iva == 1) {
                        //$iva_producto = $value->precio * $value->porcentaje;
                        $iva_producto = ($value->extendido / $value->cantidad) * $value->porcentaje;      
                    }
                    //$totalProducto = ($value->precio + $iva_producto);
                    $totalProducto = (($value->extendido / $value->cantidad) + $iva_producto);
                    $precio = $value->extendido / $value->cantidad;
                    $descuento = $value->descuento / $value->cantidad;
                    $table .= "<tr class='datos'>
                                    <td>#</td>
                                    <td> 
                                        <p>{$value->id_ct_productos}</p> 
                                        <input class='fe' type='hidden' name='id[]' value='{$value->id}'> 
                                        <input type='hidden' name='codigo[]' value='{$value->id_ct_productos}'>
                                        <input class='cantidades' type='hidden' name='cantidad[]' value='1'>
                                        <!-- <input class='precios precioProducto' name='precio[]' type='hidden' value='{$precio}' > -->
                                        <textarea class='form-control  ocultosp' name='descripcion[]' placeholder='Ingrese descripcion' ></textarea> 
                                        <input type='hidden' name='nombre[]' value='{$value->nombre}'> 
                                        <input type='hidden' class='iva_producto' id='iva_producto{$i}' name='iva_producto[]' value='{$iva_producto}'>
                                        <input type='hidden' id='porct_iva_{$i}' class='iva_porct' name='iva_porct[]' value='{$value->porcentaje}'>
                                        <input type='hidden' id='total_producto{$i}' class='totalProducto' name='totalProducto[]' value='{$totalProducto}'>
                                        <input type='hidden'  class='descuento' name='descuento[]' value='{$descuento}'>
                                    </td>
                                    <td>1</td>
                                    <td><p>{$value->nombre}</p></td>
                                    <td><input class='precios precioProducto' id='precio_{$i}' onchange='calcIva($i)' type='text' name='precio[]' value='{$precio}'></td>
                                    <td>
                                        <input class='verificar' onclick='adder(this); sumaGlobal();' type='checkbox' name='verificarx[]' checked value='0'> 
                                        <input class='vercheckbox checkProducto' type='hidden' name='verificar[]' value='1'> 
                                    </td>
                            </tr>";

                    $total += $totalProducto;
                }
            }
        }
        return ['table' => $table, 'total' => $ventas->total_final, "saldo" => $total, "valor_contable" => $ventas->valor_contable];
    }

    public static function validarFactura(Request $request)
    {
        $id_factura = $request->id_factura;
        $information = Contable::recovery_by_model('O', 'V', $id_factura);
        $data = json_encode($information);
        $data = json_decode($data, true);
        $msj = "";
        $status = "success";
        //dd($data["original"]["ingreso"]);


        if (isset($data["original"]['ingreso'])) {
            if (count($data["original"]['ingreso']) > 0) {
                $msj = "Ingreso: ";
                for ($i = 0; $i < count($data["original"]['ingreso']); $i++) {
                    $msj .= "{$data["original"]['ingreso'][$i]}";
                }
            }
        }

        if (isset($data["original"]['retencion'])) {
            if (count($data["original"]['retencion']) > 0) {
                $msj = "<br>Retencion: ";
                for ($i = 0; $i < count($data["original"]['retencion']); $i++) {
                    $msj .= "{$data["original"]['retencion'][$i]}";
                }
            }
        }

        if (isset($data["original"]['cruce'])) {
            if (count($data["original"]['cruce']) > 0) {
                $msj = "<br>Cruce: ";
                for ($i = 0; $i < count($data["original"]['cruce']); $i++) {
                    $msj .= "{$data["original"]['cruce'][$i]}";
                }
            }
        }

        if (isset($data["original"]['chequepost'])) {
            if (count($data["original"]['chequepost']) > 0) {
                $msj = "<br>Cheque Post: ";
                for ($i = 0; $i < count($data["original"]['chequepost']); $i++) {
                    $msj .= "{$data["original"]['chequepost'][$i]}";
                }
            }
        }

        if (isset($data["original"]['cruce_cuentas'])) {
            if (count($data["original"]['cruce_cuentas']) > 0) {
                $msj = "<br>Cruce Cuentas: ";
                for ($i = 0; $i < count($data["original"]['cruce_cuentas']); $i++) {
                    $msj .= "{$data["original"]['cruce_cuentas'][$i]}";
                }
            }
        }

        if (isset($data["original"]['credito'])) {
            if (count($data["original"]['credito']) > 0) {
                $msj = "<br>Credito: ";
                for ($i = 0; $i < count($data["original"]['credito']); $i++) {
                    $msj .= "{$data["original"]['credito'][$i]}";
                }
            }
        }

        if ($msj != "") {
            $status = "error";
        }

        return ["status" => $status, "msj" => $msj];
    }

    public function newstore(Request $request)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');
        $objeto_validar = new Validate_Decimals();
        $fecha_nota     = $request['fecha_hoy'];
        //$iva_param      = Ct_Configuraciones::where('id_plan', '4.1.01.02')->first();
        $iva_cuenta      = LogConfig::busqueda('4.1.01.02');
        $iva_param = Ct_Configuraciones::where('id_plan', $iva_cuenta)->first();

        $ivaf           = $iva_param->iva;
        $nuevo_saldo    = 0;
        $total_final    = $objeto_validar->set_round($request['total1']);
        //dd($request->all());
        DB::beginTransaction();
        $msj = "no";

        try {
            //Se busca la factura d eventa que se le hara la mota de credito parcial
            $venta      = Ct_ventas::find($request['factura']);

            //Buscar el punto de emision y sucursal 
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            $c_caja     = $cod_caj->codigo_caja;

            //Obtiene la  secuencia de la nota de credito Parcial
            $nfactura   = $this->obtener_numero_factura($id_empresa, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);

            if (!is_null($request['secuencial'])) {
                $nfactura = $request['secuencial'];
            }

            //Concatena la sucursal y el punto de emision
            $num_comprobante = "{$cod_sucurs->codigo_sucursal}-{$cod_caj->codigo_caja}-{$nfactura}";

            //Crear la cabecera del asiento de la nota de credito
            $input_cabecera  = [
                'observacion'     => $request['concepto'],
                'fecha_asiento'   => $fecha_nota,
                'sucursal'        => $c_sucursal,
                'punto_emision'   => $c_caja,
                'fact_numero'     => $num_comprobante,
                'valor'           => $total_final,
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            //Se crea la cabecera de la nota de credito parcial
            $venta = Ct_detalle_venta::find($request['id'][0]);

            /*$input               = [
                'id_empresa'          => $id_empresa,
                'id_cliente'          => $venta->id_cliente,
                'check_sri'           => $request['check_archivo_sri'],
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'sucursal'            => $c_sucursal,
                'punto_emision'       => $c_caja,
                //'numero_factura'      => $request['factura'],
                'numero_factura'      => $venta->id_ct_ventas,
                'nro_comprobante'     => $num_comprobante,
                'secuencia'           => $nfactura,
                'fecha'               => $request['fecha_hoy'],
                'tipo'                => $request['tipo'],
                'concepto'            => $request['concepto'],
                'subtotal'            => $request['subtotal'],
                'impuesto'            => $request['impuesto'],
                'sub_sin_imp'         => $request['sub_sin_imp'],
                'tar_iva_12'          => $request['tar_iva_12'],
                'total_credito'       => $request['total'],
                'total_deudas'        => $request['total'],
                'total_abonos'        => $request['total'],
                'total_nuevo_saldo'   => $request['total'],
                'observacion'         => $request['observaciones'],
                'electronica'         => $request['electronica'],
                'estado'              => '1',
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];*/
            // dd($venta->cabecera);
            $input               = [
                'id_empresa'          => $id_empresa,
                'id_cliente'          => $venta->cabecera->id_cliente,
                'check_sri'           => $request['check_archivo_sri'],
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'sucursal'            => $c_sucursal,
                'punto_emision'       => $c_caja,
                //'numero_factura'      => $request['factura'],
                'numero_factura'      => $venta->id_ct_ventas,
                'id_factura'          => $venta->id_ct_ventas,
                'nro_comprobante'     => $num_comprobante,
                'secuencia'           => $nfactura,
                'fecha'               => $request['fecha_hoy'],
                'tipo'                => $request['tipo'],
                'concepto'            => $request['concepto'],
                'subtotal'            => $request['subtotal1'],
                'descuento'           => $request['descuento1'],
                'impuesto'            => $request['impuesto1'],
                'sub_sin_imp'         => $request['subtotal1'],
                'subtotal0'           => $request['subtotal01'],
                'subtotal0'           => $request['subtotal121'],
                'tar_iva_12'          => $request['impuesto1'],
                'total_credito'       => $request['total1'],
                'total_deudas'        => $request['total1'],
                'total_abonos'        => $request['total1'],
                'total_nuevo_saldo'   => $request['total1'],
                'observacion'         => $request['observaciones'],
                'electronica'         => $request['electronica'],
                'estado'              => '1',
                'tipo_nota'           => '2',
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_credito = Ct_Nota_Credito_Clientes::insertGetId($input);
            $iva        = 0;

            //$detalle = NotaCreditoClienteController::guardarNotaCreditoDetalle($id_credito, $request);

            //$plan_nota_credito = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $plan_nota_credito = Plan_Cuentas::find($id_plan_confg);

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_nota_credito->id,
                'descripcion'         => $plan_nota_credito->nombre,
                'fecha'               => $fecha_nota,
                'haber'               => $request['total1'],
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            //$plan_nota_credito2 = Plan_Cuentas::where('id', '4.1.07.01')->first();.
            $id_plan_confg = LogConfig::busqueda('4.1.07.01');
            $plan_nota_credito2 = Plan_Cuentas::find($id_plan_confg);

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_nota_credito2->id,
                'descripcion'         => $plan_nota_credito2->nombre,
                'fecha'               => $fecha_nota,
                'debe'                => $request['total1'],
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            //cuentas por cobraer clientes y
            for ($i = 0; $i < count($request['codigo']); $i++) {
                // if ($request['id'][$i] != 0) {
                //     $s         = Ct_Devolucion_Productos::find($request['id'][$i]);
                //     $s->estado = 1;
                //     $s->save();
                // } else {
                for ($i = 0; $i < count($request['verificar']); $i++) {
                    if ($request['verificar'][$i] == 1) {
                        Ct_Devolucion_Productos::create([
                            'id_nota_credito' => $id_credito,
                            'descripcion'     => $request['descripcion'][$i],
                            'id_factura'      => $venta->id_ct_ventas,
                            'codigo'          => $request['codigo'][$i],
                            'cantidad'        => 1, //$request['cantidad'][$i],
                            'precio'          => $request['precio'][$i],
                            'valor_iva'       => $request["iva_producto"][$i],
                            'iva'             => $request["iva_producto"][$i] > 0 ? 1 : 0,
                            'descuento'       => $request['descuento'][$i],
                            'total'           => $request['totalProducto'][$i],
                            'estado'          => $request['verificar'][$i],
                            'nombre'          => $request['nombre'][$i],
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    }
                }
                //}
            }
            $cab_venta = Ct_ventas::find($venta->id_ct_ventas);
            $valor_contable = $cab_venta->valor_contable - $request['total1'];

            if ($valor_contable < 0) {
                DB::rollBack();
                return ["status" => "error",  'msj' => strtoupper("El total de la nota de credito no debe ser mayor al saldo de la factura")];
            }

            $cab_venta->valor_contable = $valor_contable;
            $cab_venta->save();

            DB::commit();
            $empresa     = Empresa::find($id_empresa);
            $sri         = "";
            $comprobante = "";
            $status_sri = "";
            $msj_sri = "";
            if ($empresa->electronica == 1 && $request['electronica'] == 1) {
                $sri = NotaCreditoClienteController::newGetSriParcial($id_credito);
                // if(Auth::user()->id == "0957258056"){
                //     dd($sri->status->status);
                // }
                $status_sri =  $sri->status->status;
                $msj_sri = $sri->status->reason;
                // $sri = $this->getSRIParcial($id_credito);
                // if (isset($sri->comprobante)) {
                //     if ($sri->comprobante != "") {
                //         $comprobante = $sri->comprobante;
                //     }
                // }
            }

            return response()->json([
                'status' => "success", "msj" => "Guardado Correctamente", 'sri' => $sri, 'comprobante' => $comprobante,
                'id' => $id_credito, "id_asiento" => $id_asiento_cabecera, "numero" => $nfactura, "new_valor" => $valor_contable, "status_sri" => $status_sri,
                'msj_sri' => $msj_sri
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return ["status" => "error",  'msj' => "Ocurrio un error..", 'error' => $e->getMessage()];
        }
    }

    public static function newGetSriParcial($id_credito){
        $credito = Ct_Nota_Credito_Clientes::find($id_credito);
        $detalle = Ct_Devolucion_Productos::where('id_nota_credito', $id_credito)->get();
        $id_usuario = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $cliente = [];
        $nombreCliente = "";
        $nombres = "";
        $apellidos = "";
        if(isset($credito->cliente)){
            $cliente = $credito->cliente;
            $nombreCliente = explode(" ", $cliente->nombre);
            if(count($nombreCliente) == 4){
                $nombres = "{$nombreCliente[0]} {$nombreCliente[1]}"; $apellidos ="{$nombreCliente[2]} {$nombreCliente[3]}";
            }else if(count($nombreCliente) == 3){
                $nombres = "{$nombreCliente[0]}"; $apellidos ="{$nombreCliente[2]} {$nombreCliente[3]}";
            }else if(count($nombreCliente) ==2){
                $nombres = "{$nombreCliente[0]}"; $apellidos ="{$nombreCliente[2]}";
            }
        }else{
            return ["status" => "error" , "msj" => "Error al buscar el cliente"];
        }
        $venta = [];
        if(isset($credito->valorf)){
            $venta = $credito->valorf;
        }else{
            return ['status' => 'error', 'msj' =>"Error al encontrar la factura"];
        }
        

        $data["company"] = $credito->id_empresa;
        //Datos del Cliente
        $data["person"]["document"]= isset($credito->valorf) ? $credito->valorf->id_cliente : "";
        $data["person"]["documentType"] = "0".intval($cliente->tipo);
        $data["person"]["name"] = $nombres;
        $data["person"]["surname"] = $apellidos;
        $data["person"]["email"] = $cliente->email_representante;
        $data["person"]["mobile"] = $cliente->telefono1_representante;

        $data["person"]["address"]["street"] = $cliente->ciudad_representante; 
        $data["person"]["address"]["city"] = $cliente->ciudad_representante; 
        $data["person"]["address"]["country"] = is_null($cliente->pais) || trim($cliente->pais) != "" ? $cliente->pais : "EC";

        //Datos de la factura
        $data["facturamodificar"]["numerocomprobante"] = $venta->nro_comprobante;
        $data["facturamodificar"]["fechaemision"] = date("d/m/Y", strtotime($credito->fecha));
        $data["facturamodificar"]["motivo"] = $credito->concepto;

        //items
        $data["item"] = [];
       
        foreach($detalle as $value){
            $nombre = $value->nombre;
            if(is_null($value->nombre) or trim($value->nombre) == ""){
                $ct_producto = Ct_productos::where('codigo', $value->codigo)->where('id_empresa', $credito->id_empresa)->first();
                if(!is_null($ct_producto)){
                    $nombre = $ct_producto->nombre;
                }else{
                    return ["status" => "error", "msj" =>"No se pudo encontrar un producto"];
                }
            }
            $item = [
                "sku" => $value->codigo,
                "name" => $nombre,
                "qty" => $value->cantidad,
                "price" => floatval(number_format($value->precio, 2, '.', '')),
                "discount" => floatval(number_format($value->descuento, 2, '.', '')) ,
                "subtotal" => floatval(number_format($value->total - $value->valor_iva, 2, '.', '')),
                "tax" => floatval(number_format($value->valor_iva, 2, '.', '')),
                "total" => floatval(number_format($value->total, 2, '.', '')),
            ];
            
            array_push($data["item"], $item);
        }
        $empresa = Empresa::find($credito->id_empresa);
        if(is_null($empresa)){
            return ["status" =>"error", "msj" =>"Error al buscar la empresa"];
        }
        $data["billingParameters"]["establecimiento"] = $empresa->establecimiento;
        $data["billingParameters"]["ptoEmision"] = $empresa->punto_emision;
        
        $data["billingParameters"]["infoAdicional"] = [];

        $infoAdi = ["key"=> "AGENTES_RETENCION","value" => "Resolucion 1"];
        // $infoAdi2 = ["key"=> "email","value" => "aaa@hotmail.com"];

        array_push( $data["billingParameters"]["infoAdicional"], $infoAdi);

        $data["billingParameters"]["formaPago"] ="20";
        $data["billingParameters"]["plazoDias"] ="30";
        $data["userAgent"] = "NC-P";
        
        //dd($data);
        $respuesta = ApiFacturacionController::newCrearNotasCredito($data);

        $resp = json_encode($respuesta);

        //dd($respuesta->status->status,json_decode($resp, true), json_encode($data));
        //dd($respuesta->status->status);

        Log_usuario::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "NOTA DE CREDITO PARCIAL",
            'dato_ant1'   => $credito->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => "NC-P {$credito->id} {$data['facturamodificar']['numerocomprobante']} {$respuesta->status->status}",
        ]);
        return $respuesta;
    }

  
    //dicloxicilina
    public function getSRIParcial($id)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $nota_credito = Ct_Nota_Credito_Clientes::find($id);
        $getType      = $nota_credito->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $data['empresa']     = $nota_credito->id_empresa;
        $cliente['cedula']   = $nota_credito->id_cliente;
        $cliente['tipo']     = $getType;
        $cliente['nombre']   = $nota_credito->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $nota_credito->cliente->nombre);
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
        $cliente['email']     = $nota_credito->cliente->email_representante;
        $cliente['telefono']  = $nota_credito->cliente->telefono1_representante;
        $direccion['calle']   = $nota_credito->cliente->direccion_representante;
        $direccion['ciudad']  = $nota_credito->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        //dd($nota_credito);
        $details = Ct_Detalle_Credito_Clientes::where('id_not_cred', $id)->first();

        $ventas                  = Ct_ventas::where('id', $nota_credito->numero_factura)->first();
        $venta_detalle           = Ct_detalle_venta::where('id_ct_ventas', $ventas->id)->where('estado', '1')->get();
        $factura['comprobante']  = $ventas->nro_comprobante;
        $factura['fechaemision'] = date('d/m/Y', strtotime($ventas->fecha));
        $factura['motivo']       = $nota_credito->concepto;
        $data['factura']         = $factura;
        $cant                    = 0;
        //dd($venta_detalle);
        foreach ($venta_detalle as $value) {
            //se envian los productos
            $detalle               = trim(preg_replace("/\s+/", " ", substr($value->nombre, 0, 30)));
            $detalle               = str_replace("(", " ", $detalle);
            $detalle               = str_replace(")", " ", $detalle);
            $codigo                = str_replace("-", " ", $value->id_ct_productos);
            $producto['sku']       = ($codigo); //ID EXAMEN
            $producto['nombre']    = $detalle; // NOMBRE DEL EXAMEN
            $producto['cantidad']  = $value->cantidad;
            $producto['precio']    = $value->precio; //DETALLE
            $pricetot              = $value->cantidad * $value->precio;
            $producto['descuento'] = $value->descuento;
            $producto['subtotal']  = $pricetot - $value->descuento; //precio-descuento
            $tax                   = "0";
            if ($value->check_iva == 1) {
                $tax = $pricetot * $value->porcentaje;
            }
            $producto['tax']    = $tax;
            $producto['total']  = $pricetot - $value->valor_descuento; //SUBTOTAL
            $producto['copago'] = "0";
            $productos[$cant]   = $producto;
            $cant++;
        }
        $data['productos'] = $productos;
        if ($idusuario == '1316262193') {
            //dd($data);
        }
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
         */

        $info_adicional['nombre']      = trim(str_replace("_", " ", $nota_credito->cliente->nombre)); //NO TIENE QUE TENER ESPACIOS
        $info_adicional['valor']       = trim($nota_credito->cliente->email_representante); //NO TIENE QUE TENER ESPACIOS 
        $info[0]                       = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $pago['forma_pago']            = '01';
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;
        //dd($data);
        $envio = ApiFacturacionController::crearNotasCredito($data);
        //dd($envio);
        $nota_credito->update([
            'nro_comprobante' => $envio->comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);
        //dd($envio);
        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "NOTA DE CREDITO PACIAL",
            'dato_ant1'   => $nota_credito->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);
        return response()->json($envio);
    }
    public function getSRIParcialReenviar(Request $request)
    {
        $id           = $request['id'];
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $nota_credito = Ct_Nota_Credito_Clientes::find($id);
        $getType      = $nota_credito->cliente->tipo;
        if (strlen($getType) == 1) {
            $getType = '0' . $getType;
        }
        $data['empresa']     = $nota_credito->id_empresa;
        $cliente['cedula']   = $nota_credito->id_cliente;
        $cliente['tipo']     = $getType;
        $cliente['nombre']   = $nota_credito->cliente->nombre;
        $cliente['apellido'] = '';
        $explode             = explode(" ", $nota_credito->cliente->nombre);
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
        $cliente['email']     = $nota_credito->cliente->email_representante;
        $cliente['telefono']  = $nota_credito->cliente->telefono1_representante;
        $direccion['calle']   = $nota_credito->cliente->direccion_representante;
        $direccion['ciudad']  = $nota_credito->cliente->ciudad_representante;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;
        //dd($nota_credito);
        $details                 = Ct_Detalle_Credito_Clientes::where('id_not_cred', $id)->first();
        $venta_detalle           = Ct_Devolucion_Productos::where('id_nota_credito', $id)->where('estado', '1')->get();
        $ventas                  = Ct_ventas::where('id', $nota_credito->numero_factura)->first();
        $factura['comprobante']  = $ventas->nro_comprobante;
        $factura['fechaemision'] = date('d/m/Y', strtotime($ventas->fecha));
        $factura['motivo']       = substr($nota_credito->concepto, 0, 50);
        $data['factura']         = $factura;
        $cant                    = 0;
        //dd(strlen($nota_credito->concepto));
        //dd($venta_detalle);
        foreach ($venta_detalle as $value) {
            //se envian los productos
            $producto['sku'] = $value->codigo; //ID EXAMEN
            if ($value->nombre != null) {
                $producto['nombre'] = $value->nombre; // NOMBRE DEL EXAMEN
            } else {
                $prx = Ct_productos::where('codigo', $value->codigo)->where('id_empresa', $nota_credito->id_empresa)->first();
                if ($prx != null) {
                    $producto['nombre'] = $prx->nombre; // NOMBRE DEL EXAMEN
                } else {
                    $producto['nombre'] = $prx->codigo; // NOMBRE DEL EXAMEN
                }
            }

            $producto['cantidad']  = floatval($value->cantidad);
            $producto['precio']    = floatval($value->precio); //DETALLE
            $pricetot              = $value->cantidad * $value->precio;
            $producto['descuento'] = floatval($value->descuento);
            $producto['subtotal']  = floatval($pricetot) - floatval($value->descuento); //precio-descuento
            $tax                   = "0";
            if ($value->iva == 1) {
                $tax = $pricetot * $value->porcentaje;
            }
            $producto['tax']    = $tax;
            $producto['total']  = floatval($pricetot) - floatval($value->valor_descuento); //SUBTOTAL
            $producto['copago'] = 0;
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

        $info_adicional['nombre']      = $nota_credito->cliente->nombre;
        $info_adicional['valor']       = $nota_credito->cliente->email_representante;
        $info[0]                       = $info_adicional;
        $pago['informacion_adicional'] = $info;
        $pago['forma_pago']            = '01';
        $pago['dias_plazo']            = '30';
        $data['pago']                  = $pago;
        // dd($data);

        //dd($data);
        $envio = ApiFacturacionController::crearNotasCredito($data);
        //dd($envio);
        $nota_credito->update([
            'nro_comprobante' => $envio->comprobante,
            'fecha_envio'     => date('Y-m-d H:i:s'),
        ]);
        //dd($envio);
        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;
        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "NOTA DE CREDITO ",
            'dato_ant1'   => $nota_credito->id,
            'dato1'       => 'envio',
            'dato_ant2'   => "ENVIAR AL SRI",
            'dato_ant4'   => $manage,
        ]);

        return response()->json($envio);
    }
}
