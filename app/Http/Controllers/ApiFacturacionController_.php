<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Api;
use Sis_medico\LogConfig;
use Sis_medico\Paciente;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use Sis_medico\Validate_Decimals;
//use Sis_medico\LogConfig;

class ApiFacturacionController extends Controller
{
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

    public function llamarValidarCedula($cedula){
        $respuesta = $this->validarCedula($cedula);
        return $respuesta;
    }

    private static function validarCedula($cedula)
    {
        if ((strlen($cedula) == 10) || (strlen($cedula) == 13)) {
            $numero           = $cedula;
            $suma             = 0;
            $residuo          = 0;
            $pri              = false;
            $pub              = false;
            $nat              = false;
            $numeroProvincias = 24;
            $modulo           = 11;

            /* Verifico que el campo no contenga letras */
            $ok = 1;
            $i  = substr($numero, 0, 2);
            if ($i > $numeroProvincias) {
                return false;
            }

            /* Aqui almacenamos los digitos de la cedula en variables. */
            $d1  = substr($numero, 0, 1);
            $d2  = substr($numero, 1, 1);
            $d3  = substr($numero, 2, 1);
            $d4  = substr($numero, 3, 1);
            $d5  = substr($numero, 4, 1);
            $d6  = substr($numero, 5, 1);
            $d7  = substr($numero, 6, 1);
            $d8  = substr($numero, 7, 1);
            $d9  = substr($numero, 8, 1);
            $d10 = substr($numero, 9, 1);

            /* El tercer digito es: */
            /* 9 para sociedades privadas y extranjeros */
            /* 6 para sociedades $publicas */
            /* menor que 6 (0,1,2,3,4,5) para personas $naturales */

            if ($d3 == 7 || $d3 == 8) {
                return false;
            }

            /* Solo para personas $naturales ($modulo 10) */
            if ($d3 < 6) {
                $nat = true;
                $p1  = $d1 * 2;
                if ($p1 >= 10) {
                    $p1 -= 9;
                }

                $p2 = $d2 * 1;
                if ($p2 >= 10) {
                    $p2 -= 9;
                }
                $p3 = $d3 * 2;
                if ($p3 >= 10) {
                    $p3 -= 9;
                }
                $p4 = $d4 * 1;
                if ($p4 >= 10) {
                    $p4 -= 9;
                }
                $p5 = $d5 * 2;
                if ($p5 >= 10) {
                    $p5 -= 9;
                }
                $p6 = $d6 * 1;
                if ($p6 >= 10) {
                    $p6 -= 9;
                }
                $p7 = $d7 * 2;
                if ($p7 >= 10) {
                    $p7 -= 9;
                }

                $p8 = $d8 * 1;
                if ($p8 >= 10) {
                    $p8 -= 9;
                }

                $p9 = $d9 * 2;
                if ($p9 >= 10) {
                    $p9 -= 9;
                }

                $modulo = 10;
            }

            /* Solo para sociedades $publicas ($modulo 11) */
            /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */
            else if ($d3 == 6) {
                $pub = true;
                $p1  = $d1 * 3;
                $p2  = $d2 * 2;
                $p3  = $d3 * 7;
                $p4  = $d4 * 6;
                $p5  = $d5 * 5;
                $p6  = $d6 * 4;
                $p7  = $d7 * 3;
                $p8  = $d8 * 2;
                $p9  = 0;
            }

            /* Solo para entidades privadas ($modulo 11) */
            else if ($d3 == 9) {
                $pri = true;
                $p1  = $d1 * 4;
                $p2  = $d2 * 3;
                $p3  = $d3 * 2;
                $p4  = $d4 * 7;
                $p5  = $d5 * 6;
                $p6  = $d6 * 5;
                $p7  = $d7 * 4;
                $p8  = $d8 * 3;
                $p9  = $d9 * 2;
            }

            $suma    = $p1 + $p2 + $p3 + $p4 + $p5 + $p6 + $p7 + $p8 + $p9;
            $residuo = $suma % $modulo;

            /* Si $residuo=0, dig.ver.=0, caso contrario 10 - $residuo*/
            $digitoVerificador = $residuo == 0 ? 0 : $modulo - $residuo;

            /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
            if ($pub == true) {
                if ($digitoVerificador != $d9) {
                    return false;
                }
                /* El ruc de las empresas del sector $publico terminan con 0001*/
                if (substr($numero, 9, 4) != '0001') {
                    return false;
                }
            } else if ($pri == true) {
                if ($digitoVerificador != $d10) {
                    return false;
                }
                if (substr($numero, 10, 3) != '001') {
                    return false;
                }
            } else if ($nat == true) {
                if ($digitoVerificador != $d10) {
                    return false;
                }
                if (strlen($numero) > 10 && substr($numero, 10, 3) != '001') {
                    return false;
                }
            }
        }
        return true;
    }

    private function cleanNames($valor)
    {
        $vowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "'", '"', "|", "^");
        return str_replace($vowels, "", $valor);
    }

    public static function envio_factura($data)
    {
        //dd($data);
        if (Auth::check()) {
            $id_usuario = Auth::user()->id;
        }else{
            $id_usuario       = 'FACELECTRO';
        }
        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = ApiFacturacionController::getNonce(12);
        $empresa    = Empresa::findorfail($data['empresa']);
        //dd($empresa);

        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        } elseif ($empresa->electronica != 1) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));

        $envio['company'] = $data['empresa'];
        //VALIDO SI LA CEDULA O RUC ES VALIDO
        $valida_cedula = true;
        if ($data['cliente']['tipo'] != 6) {
            $valida_cedula = ApiFacturacionController::validarCedula($data['cliente']['cedula']);
        }

        if (!$valida_cedula && ($data['cliente']['tipo'] == 4 || $data['cliente']['tipo'] == 5)) {
            return "numero de cedula incorrecto";
        }

        //INGRESO LOS DATOS DE LA PERSONA
        $person['document'] = $data['cliente']['cedula'];
        if ($data['cliente']['tipo'] == 6) {
            $tipo = "06";
        } elseif ($data['cliente']['tipo'] == 8) {
            $tipo = "08";
        } elseif (strlen($data['cliente']['cedula']) == 13 && substr($data['cliente']['cedula'], -3) == '001') {
            $tipo = "04";
        } else {
            $tipo = "05";
        }

        $person['documentType']       = $tipo;
        $person['name']               = $data['cliente']['nombre'];
        $person['surname']            = $data['cliente']['apellido'];
        $person['email']              = $data['cliente']['email'];
        $person['mobile']             = $data['cliente']['telefono'];
        $person['address']['street']  = $data['cliente']['direccion']['calle'];
        $person['address']['city']    = $data['cliente']['direccion']['ciudad'];
        $person['address']['country'] = 'EC';
        $envio['person']              = $person;

        if (count($data['productos']) <= 0) {
            return "no existen productos";
        }
        //ingreso y creo los productos
        $productos = array();
        foreach ($data['productos'] as $key => $value) {
            $nombre_p            = "";
            $nombre_p            = substr($value['sku'], 0, 20);
            $nombre_p            = str_replace(" ", "_", $nombre_p);
            $arreglo             = array();
            $arreglo['sku']      = $nombre_p;
            $arreglo['name']     = $value['nombre'];
            $arreglo['qty']      = intval($value['cantidad']);
            $arreglo['price']    = floatval(number_format($value['precio'], 2, '.', ''));
            $arreglo['discount'] = floatval(number_format($value['descuento'], 2, '.', ''));
            $arreglo['subtotal'] = floatval(number_format($value['subtotal'], 2, '.', ''));
            $arreglo['tax']      = floatval(number_format($value['tax'], 2, '.', ''));
            $arreglo['total']    = floatval(number_format($value['total'], 2, '.', ''));
            array_push($productos, $arreglo);
        }
        $envio['items'] = $productos;

        //parametros de pago
        if(isset($data['externo'])){
            $pago['establecimiento'] = $data['establecimiento'];
            $pago['ptoEmision']      = $data['ptoEmision'];
        }else{
            $pago['establecimiento'] = $empresa->establecimiento;
            $pago['ptoEmision']      = $empresa->punto_emision;
        }
        if ($empresa->externo == 1) {
            $data['establecimiento'] = $empresa->establecimiento;
            $data['ptoEmision']      = $empresa->punto_emision;
        }

        $info_adicional          = array();
        foreach ($data['pago']['informacion_adicional'] as $key => $value) {
            $nombre_p         = "";
            $nombre_p         = substr($value['nombre'], 0, 20);
            $nombre_p         = str_replace(" ", "_", $nombre_p);
            $arreglo          = array();
            $arreglo['key']   = $nombre_p;
            $arreglo['value'] = $value['valor'];
            array_push($info_adicional, $arreglo);
        }
        $pago['infoAdicional']      = $info_adicional;
        $pago['formaPago']          = $data['pago']['forma_pago'];
        $pago['plazoDias']          = $data['pago']['dias_plazo'];
        $envio['billingParameters'] = $pago;
        $envio['userAgent']         = "SIAAM SOFTWARE/1";

        $envio_2 = json_encode($envio);
        if( $empresa->externo == 1){
            $url     = $empresa->url ;
        }else{
            $url     = $empresa->url . 'billing/create/';
        }
        
        //dd($url);

        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $envio_2,
            'dato2'       => "Inicia api",
        ]);
        if ($empresa->externo == 0) {
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'POST',
                    'content' => $envio_2,
                ),
            );
        }else{
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );
        }
        //dd($url);
        $context = stream_context_create($options);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        if(Auth::user()->id == '0922729587'){
            //dd($response, $url, $context);
        }
        //dd($response);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API",
        ]);
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-000000013"}';*/
        $respuesta_array = json_decode($response);
        if ($data['contable'] == 1 && $empresa->externo == 0) {
            ApiFacturacionController::crea_factura($data, $respuesta_array);
        }
        return $respuesta_array;
    }

    public static function envio_factura_externo($data)
    {
        $id_usuario       = 'FACELECTRO';
        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = ApiFacturacionController::getNonce(12);
        
        $empresa    = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        } elseif ($empresa->electronica != 1) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));

        $envio['company'] = $data['empresa'];
        //VALIDO SI LA CEDULA O RUC ES VALIDO
        $valida_cedula = true;
        if ($data['cliente']['tipo'] != 6) {
            $valida_cedula = ApiFacturacionController::validarCedula($data['cliente']['cedula']);
        }

        if (!$valida_cedula && ($data['cliente']['tipo'] == 4 || $data['cliente']['tipo'] == 5)) {
            return "numero de cedula incorrecto";
        }

        //INGRESO LOS DATOS DE LA PERSONA
        $person['document'] = $data['cliente']['cedula'];
        if ($data['cliente']['tipo'] == 6) {
            $tipo = "06";
        } elseif ($data['cliente']['tipo'] == 8) {
            $tipo = "08";
        } elseif (strlen($data['cliente']['cedula']) == 13 && substr($data['cliente']['cedula'], -3) == '001') {
            $tipo = "04";
        } else {
            $tipo = "05";
        }

        $person['documentType']       = $tipo;
        $person['name']               = $data['cliente']['nombre'];
        $person['surname']            = $data['cliente']['apellido'];
        $person['email']              = $data['cliente']['email'];
        $person['mobile']             = $data['cliente']['telefono'];
        $person['address']['street']  = $data['cliente']['direccion']['calle'];
        $person['address']['city']    = $data['cliente']['direccion']['ciudad'];
        $person['address']['country'] = 'EC';
        $envio['person']              = $person;

        if (count($data['productos']) <= 0) {
            return "no existen productos";
        }
        //ingreso y creo los productos
        $productos = array();
        foreach ($data['productos'] as $key => $value) {
            $nombre_p            = "";
            $nombre_p            = substr($value['sku'], 0, 20);
            $nombre_p            = str_replace(" ", "_", $nombre_p);
            $arreglo             = array();
            $arreglo['sku']      = $nombre_p;
            $arreglo['name']     = $value['nombre'];
            $arreglo['qty']      = intval($value['cantidad']);
            $arreglo['price']    = floatval(number_format($value['precio'], 2, '.', ''));
            $arreglo['discount'] = floatval(number_format($value['descuento'], 2, '.', ''));
            $arreglo['subtotal'] = floatval(number_format($value['subtotal'], 2, '.', ''));
            $arreglo['tax']      = floatval(number_format($value['tax'], 2, '.', ''));
            $arreglo['total']    = floatval(number_format($value['total'], 2, '.', ''));
            array_push($productos, $arreglo);
        }
        $envio['items'] = $productos;

        //parametros de pago
        if(isset($data['externo'])){
            $pago['establecimiento'] = $data['establecimiento'];
            $pago['ptoEmision']      = $data['ptoEmision'];
        }else{
            $pago['establecimiento'] = $empresa->establecimiento;
            $pago['ptoEmision']      = $empresa->punto_emision;
        }
        
        $info_adicional          = array();
        foreach ($data['pago']['informacion_adicional'] as $key => $value) {
            $nombre_p         = "";
            $nombre_p         = substr($value['nombre'], 0, 20);
            $nombre_p         = str_replace(" ", "_", $nombre_p);
            $arreglo          = array();
            $arreglo['key']   = $nombre_p;
            $arreglo['value'] = $value['valor'];
            array_push($info_adicional, $arreglo);
        }
        $pago['infoAdicional']      = $info_adicional;
        $pago['formaPago']          = $data['pago']['forma_pago'];
        $pago['plazoDias']          = $data['pago']['dias_plazo'];
        $envio['billingParameters'] = $pago;
        $envio['userAgent']         = "SIAAM SOFTWARE/1";

        $envio_2 = json_encode($envio);
        $url     = $empresa->url . 'billing/create/';
        
        //dd($url);

        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $envio_2,
            'dato2'       => "Inicia api externo",
        ]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $envio_2,
            ),
        );
        //dd($url);
        $context = stream_context_create($options);
        
        //dd($url, $context);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API externo",
        ]);
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-000000013"}';*/
        $respuesta_array = json_decode($response);
        if ($data['contable'] == 1 && $empresa->externo == 0) {
            ApiFacturacionController::crea_factura($data, $respuesta_array);
        }
        return $response;
    }

    public static function crea_factura($data, $info_comprobante)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = 'FACELECTRO';
        $fecha_as        = date('Y-m-d');
        $id_empresa      = $data['empresa'];
        $llevaOrden      = false;
        $numero          = $info_comprobante->comprobante;
        $numero1         = $numero;
        $num_comprobante = 0;
        //dd($data);
        //dd($request->all());
        $cliente = Ct_Clientes::where('identificacion', '=', $data['cliente']['cedula'])->first();
        //return $cliente. " - ". $request['identificacion_cliente'];

        $cliente_datos = $data['cliente'];

        if (is_null($cliente)) {
            // cliente
            Ct_Clientes::create([
                'nombre'                  => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'tipo'                    => $cliente_datos['tipo'],
                'identificacion'          => $cliente_datos['cedula'],
                'clase'                   => '1',
                'nombre_representante'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'cedula_representante'    => $cliente_datos['cedula'],
                'ciudad_representante'    => $cliente_datos['direccion']['ciudad'],
                'direccion_representante' => $cliente_datos['direccion']['calle'],
                'telefono1_representante' => $cliente_datos['telefono'],
                'email_representante'     => $cliente_datos['email'],
                'estado'                  => '1',
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ]);
        }
        $partes          = explode("-", $info_comprobante->comprobante);
        $c_sucursal      = $partes['0'];
        $c_caja          = $partes['1'];
        $num_comprobante = $info_comprobante->comprobante;
        $nfactura        = $partes['2'];
        $proced          = ' ';
        if ($data['laboratorio'] == 1) {
            $proced = 'Examenes de Laboratorio';
        }
        $pacis = Paciente::find($data['paciente']);

        $pac = "";
        if (!is_null($pacis)) {
            $pac = $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
        }

        $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . ' | ' . $pac;
        $id_paciente = $data['paciente'];

        if (is_null($id_paciente)) {
            $id_paciente = '9999999999';
        }
        //fix contranstrain
        if (is_null($pacis)) {
            $id_paciente = '9999999999';
        }
        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //7******GUARDAdo TABLA ASIENTO CABECERA********

        $total1       = 0;
        $subtotal0    = 0;
        $subtotal12   = 0;
        $descuento    = 0;
        $descuento_0  = 0;
        $descuento_12 = 0;
        $iva          = 0;
        foreach ($data['productos'] as $value) {
            $total1 += $value['total'];
            $descuento += $value['descuento'];
            $iva += $value['tax'];
            if ($value['tax'] == 0) {
                $subtotal0 += $value['subtotal'];
                $descuento_0 += $value['descuento'];
            } else {
                $subtotal12 += $value['subtotal'];
                $descuento_12 += $value['descuento'];
            }
        }
        $base_imponible = $subtotal0 + $subtotal12;

        $input_cabecera = [
            'fecha_asiento'   => $fecha_as,
            'fact_numero'     => $nfactura,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text,
            'valor'           => $total1,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        //$id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'          => $c_sucursal,
            'punto_emision'     => $c_caja,
            'numero'            => $nfactura,
            'nro_comprobante'   => $num_comprobante,
            'id_asiento'        => $id_asiento_cabecera,
            'id_empresa'        => $id_empresa,
            'tipo'              => 'VEN-FA',
            'fecha'             => $fecha_as,
            'fecha_envio'       => $fecha_as,
            'divisas'           => 1,
            'nombre_cliente'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
            'id_cliente'        => $cliente_datos['cedula'], //nombre_cliente
            'direccion_cliente' => $cliente_datos['direccion']['calle'],
            'ruc_id_cliente'    => $cliente_datos['cedula'],
            'telefono_cliente'  => $cliente_datos['telefono'],
            'email_cliente'     => $cliente_datos['email'],
            'id_paciente'       => $id_paciente,
            'nombres_paciente'  => $pac,
            'seguro_paciente'   => $data['id_seguro'],
            'concepto'          => $data['concepto'],
            'copago'            => $data['copago'],
            'subtotal_0'        => $subtotal0,
            'subtotal_12'       => $subtotal12,
            'descuento'         => $descuento,
            'base_imponible'    => $base_imponible,
            'impuesto'          => $iva,
            'total_final'       => $total1,
            'valor_contable'    => $total1,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'electronica'       => 1,
        ];

        // return $factura_venta;

        $id_venta = Ct_ventas::insertGetId($factura_venta);
        //dd($id_venta);
        //$id_venta = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;

        //kardex
        foreach ($data['productos'] as $valor) {
            $datos_iva = 0;
            if ($valor['tax'] > 0) {
                $datos_iva = 1;
            }
            $detalle = [
                'id_ct_ventas'    => $id_venta,
                'id_ct_productos' => $valor['sku'],
                'nombre'          => $valor['nombre'],
                'cantidad'        => $valor['cantidad'],
                'precio'          => $valor['precio'],
                'descuento'       => $valor['descuento'],
                'extendido'       => $valor['subtotal'],
                'detalle'         => '',
                'copago'          => $valor['copago'],
                'check_iva'       => $datos_iva,
                'porcentaje'      => '0.12',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }

        //***MODULO CUENTA POR COBRAR***

        //cUENTAS X COBRAR CLIENTES

        $val_tol = $total1;
        

        if ($val_tol > 0) {
            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);

           // $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => $val_tol,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        //    2.01.07.01.01 iva sobre ventas
        if ($iva > 0) {
            $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
            // $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $iva,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }
        // 4.1.01.02    Ventas Mercaderia Tarifa 12%
        if ($subtotal12 > 0) {
            $id_plan_confg = LogConfig::busqueda('4.1.01.02');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
            // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $subtotal12 + $descuento_12,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        // 4.1.01.01    Ventas Mercaderia Tarifa 0%
        if ($subtotal0 > 0) {
            if ($data['empresa'] == '1391914857001') {
                $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '4.1.01.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal0 + $descuento_0,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            } else {
                $id_plan_confg = LogConfig::busqueda('4.1.01.01');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
                // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal0 + $descuento_0,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
        }

        if ($descuento > 0) {
            $id_plan_confg = LogConfig::busqueda('4.1.06.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
       
            // $plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '4.1.06.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'haber'               => '0',
                'debe'                => $descuento,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        $variable    = $data['formas_pago'];
        $arr_p       = [];
        $total_pagos = 0;

        foreach ($variable as $value) {

            Ct_Forma_Pago::create([

                'id_ct_ventas'    => $id_venta,
                'tipo'            => $value['id_tipo'],
                'fecha'           => $value['fecha'],
                'tipo_tarjeta'    => $value['tipo_tarjeta'],
                'numero'          => $value['numero_transaccion'],
                'banco'           => $value['id_banco'],
                'cuenta'          => $value['cuenta'],
                'giradoa'         => $value['giradoa'],
                'valor'           => $value['valor'],
                'valor_base'      => $value['valor_base'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);

            if ($value['id_tipo'] != 7) {
                $arr_pagos = [
                    'id_tip_pago'    => $value['id_tipo'],
                    'fecha_pago'     => $value['fecha'],
                    'tipo_tarjeta'   => $value['tipo_tarjeta'],
                    'numero_pago'    => $value['numero_transaccion'],
                    'id_banco_pago'  => $value['id_banco'],
                    'id_cuenta_pago' => $value['cuenta'],
                    'giradoa'        => $value['giradoa'],
                    'valor'          => $value['valor'],
                    'valor_base'     => $value['valor_base'],
                ];
                $total_pagos += $value['valor'];

                array_push($arr_p, $arr_pagos);
            }

        }

        //agregar comprobantes de ingreso
        $erf = ApiFacturacionController::crearComprobante($nfactura, $data, $arr_p, $id_venta, $id_empresa, $total_pagos);

        return true;
    }

    public static function crea_factura_noelec($data, $info_comprobante)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = 'FACELECTRO';
        $fecha_as   = $data['fecha'];
        $id_empresa = $data['empresa'];

        if (isset($data['electronica'])) {
            $electronica = $data['electronica'];
        } else {
            $electronica = 0;
        }
        $llevaOrden      = false;
        $numero          = $info_comprobante;
        $numero1         = $numero;
        $num_comprobante = 0;
        //dd($data);
        //dd($request->all());
        $cliente = Ct_Clientes::where('identificacion', '=', $data['cliente']['cedula'])->first();
        //return $cliente. " - ". $request['identificacion_cliente'];

        $cliente_datos = $data['cliente'];

        if (is_null($cliente)) {
            // cliente
            Ct_Clientes::create([
                'nombre'                  => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'tipo'                    => $cliente_datos['tipo'],
                'identificacion'          => $cliente_datos['cedula'],
                'clase'                   => '1',
                'nombre_representante'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'cedula_representante'    => $cliente_datos['cedula'],
                'ciudad_representante'    => $cliente_datos['direccion']['ciudad'],
                'direccion_representante' => $cliente_datos['direccion']['calle'],
                'telefono1_representante' => $cliente_datos['telefono'],
                'email_representante'     => $cliente_datos['email'],
                'estado'                  => '1',
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ]);
        }
        $partes          = explode("-", $info_comprobante);
        $c_sucursal      = $partes['0'];
        $c_caja          = $partes['1'];
        $num_comprobante = $info_comprobante;
        $nfactura        = $partes['2'];

        $proced = ' ';
        if ($data['laboratorio'] == 1) {
            $proced = 'Examenes de Laboratorio';
        }
        $pacis = Paciente::find($data['paciente']);

        $pac = "";
        if (!is_null($pacis)) {
            $pac = " | " . $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
        }
        $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
        $id_paciente = $data['paciente'];

        if (is_null($id_paciente) or is_null($pacis)) {
            $id_paciente     = '9999999999';
            $nombre_paciente = '';
        } else {
            $nombre_paciente = $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
        }
        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //7******GUARDAdo TABLA ASIENTO CABECERA********

        $total1       = 0;
        $subtotal0    = 0;
        $subtotal12   = 0;
        $descuento    = 0;
        $descuento_0  = 0;
        $descuento_12 = 0;
        $iva          = 0;
        foreach ($data['productos'] as $value) {
            $total1 += $value['total'];
            $descuento += $value['descuento'];
            $iva += $value['tax'];
            if ($value['tax'] == 0) {
                $subtotal0 += $value['subtotal'];
                $descuento_0 += $value['descuento'];
            } else {
                $subtotal12 += $value['subtotal'];
                $descuento_12 += $value['descuento'];
            }
        }
        $base_imponible = $subtotal0 + $subtotal12;

        $input_cabecera = [
            'fecha_asiento'   => $fecha_as,
            'fact_numero'     => $nfactura,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text,
            'valor'           => $total1,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        //$id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'          => $c_sucursal,
            'punto_emision'     => $c_caja,
            'numero'            => $nfactura,
            'nro_comprobante'   => $num_comprobante,
            'id_asiento'        => $id_asiento_cabecera,
            'id_empresa'        => $id_empresa,
            'tipo'              => 'VEN-FA',
            'fecha'             => $fecha_as,
            'divisas'           => 1,
            'nombre_cliente'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
            'id_cliente'        => $cliente_datos['cedula'], //nombre_cliente
            'direccion_cliente' => $cliente_datos['direccion']['calle'],
            'ruc_id_cliente'    => $cliente_datos['cedula'],
            'telefono_cliente'  => $cliente_datos['telefono'],
            'nro_autorizacion'  => $cliente_datos['nro_autorizacion'],
            'email_cliente'     => $cliente_datos['email'],
            'id_paciente'       => $id_paciente,
            'nombres_paciente'  => $nombre_paciente,
            'seguro_paciente'   => $data['id_seguro'],
            'concepto'          => $data['concepto'],
            'copago'            => $data['copago'],
            'subtotal_0'        => $subtotal0,
            'subtotal_12'       => $subtotal12,
            'descuento'         => $descuento,
            'base_imponible'    => $base_imponible,
            'impuesto'          => $iva,
            'total_final'       => $total1,
            'valor_contable'    => $total1,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'electronica'       => $electronica,
        ];

        // return $factura_venta;

        $id_venta = Ct_ventas::insertGetId($factura_venta);
        //dd($id_venta);
        //$id_venta = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;

        //kardex
        foreach ($data['productos'] as $valor) {
            $datos_iva = 0;
            if ($valor['tax'] > 0) {
                $datos_iva = 1;
            }
            $detalle = [
                'id_ct_ventas'    => $id_venta,
                'id_ct_productos' => $valor['sku'],
                'nombre'          => $valor['nombre'],
                'cantidad'        => $valor['cantidad'],
                'precio'          => $valor['precio'],
                'descuento'       => $valor['descuento'],
                'extendido'       => $valor['subtotal'],
                'detalle'         => '',
                'copago'          => $valor['copago'],
                'check_iva'       => $datos_iva,
                'porcentaje'      => '0.12',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }

        //***MODULO CUENTA POR COBRAR***

        //cUENTAS X COBRAR CLIENTES

        $val_tol = $total1;

        if ($val_tol > 0) {
            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
       
            // $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => $val_tol,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        //    2.01.07.01.01 iva sobre ventas
        if ($iva > 0) {
            $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
       
            // $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '2.01.07.01.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $iva,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }
        // 4.1.01.02    Ventas Mercaderia Tarifa 12%
        if ($subtotal12 > 0) {
            $id_plan_confg = LogConfig::busqueda('4.1.01.02');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
       
           // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '4.1.01.02',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $subtotal12 + $descuento_12,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        // 4.1.01.01    Ventas Mercaderia Tarifa 0%
        if ($subtotal0 > 0) {
            $id_plan_confg = LogConfig::busqueda('4.1.01.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
       
            // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '4.1.01.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $subtotal0 + $descuento_0,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        if ($descuento > 0) {
            $id_plan_confg = LogConfig::busqueda('4.1.06.01');
            $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
       
            // $plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '4.1.06.01',
                'id_plan_cuenta'      => $plan_cuentas->id,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'haber'               => '0',
                'debe'                => $descuento,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        $variable    = $data['formas_pago'];
        $arr_p       = [];
        $total_pagos = 0;

        foreach ($variable as $value) {

            Ct_Forma_Pago::create([

                'id_ct_ventas'    => $id_venta,
                'tipo'            => $value['id_tipo'],
                'fecha'           => $fecha_as,
                'tipo_tarjeta'    => $value['tipo_tarjeta'],
                'numero'          => $value['numero_transaccion'],
                'banco'           => $value['id_banco'],
                'cuenta'          => $value['cuenta'],
                'giradoa'         => $value['giradoa'],
                'valor'           => $value['valor'],
                'valor_base'      => $value['valor_base'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);

            $arr_pagos = [
                'id_tip_pago'    => $value['id_tipo'],
                'fecha_pago'     => $fecha_as,
                'tipo_tarjeta'   => $value['tipo_tarjeta'],
                'numero_pago'    => $value['numero_transaccion'],
                'id_banco_pago'  => $value['id_banco'],
                'id_cuenta_pago' => $value['cuenta'],
                'giradoa'        => $value['giradoa'],
                'valor'          => $value['valor'],
                'valor_base'     => $value['valor_base'],
            ];
            $total_pagos += $value['valor'];

            array_push($arr_p, $arr_pagos);
        }

        //agregar comprobantes de ingreso
        $erf = ApiFacturacionController::crearComprobante_noelect($nfactura, $data, $arr_p, $id_venta, $id_empresa, $total_pagos, $fecha_as);

        return true;
    }

    public static function reproceso_factura_noelec($data, $info_comprobante)
    {
        $ip_cliente      = 'FACTURACION MASIVO';
        $idusuario       = 'FACELECTRO';
        $fecha_as        = $data['fecha'];
        $id_empresa      = $data['empresa'];
        $llevaOrden      = false;
        $numero          = $info_comprobante;
        $numero1         = $numero;
        $num_comprobante = 0;
        if (isset($data['electronica'])) {
            $electronica = $data['electronica'];
        } else {
            $electronica = 0;
        }
        //dd($data);
        //dd($request->all());
        $cliente = Ct_Clientes::where('identificacion', '=', $data['cliente']['cedula'])->first();
        //return $cliente. " - ". $request['identificacion_cliente'];

        $cliente_datos = $data['cliente'];

        if (is_null($cliente)) {
            // cliente
            Ct_Clientes::create([
                'nombre'                  => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'tipo'                    => $cliente_datos['tipo'],
                'identificacion'          => $cliente_datos['cedula'],
                'clase'                   => '1',
                'nombre_representante'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'cedula_representante'    => $cliente_datos['cedula'],
                'ciudad_representante'    => $cliente_datos['direccion']['ciudad'],
                'direccion_representante' => $cliente_datos['direccion']['calle'],
                'telefono1_representante' => $cliente_datos['telefono'],
                'email_representante'     => $cliente_datos['email'],
                'estado'                  => '1',
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ]);
        }
        $partes          = explode("-", $info_comprobante);
        $c_sucursal      = $partes['0'];
        $c_caja          = $partes['1'];
        $num_comprobante = $info_comprobante;
        $nfactura        = $partes['2'];

        $proced = ' ';
        if ($data['laboratorio'] == 1) {
            $proced = 'Examenes de Laboratorio';
        }
        $pacis = Paciente::find($data['paciente']);

        $pac = "";
        if (!is_null($pacis)) {
            $pac = " | " . $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
        }
        $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . $pac;
        $id_paciente = $data['paciente'];

        if (is_null($id_paciente) or is_null($pacis)) {
            $id_paciente     = '9999999999';
            $nombre_paciente = '';
        } else {
            $nombre_paciente = $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
        }
        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //7******GUARDAdo TABLA ASIENTO CABECERA********

        $total1       = 0;
        $subtotal0    = 0;
        $subtotal12   = 0;
        $descuento    = 0;
        $descuento_0  = 0;
        $descuento_12 = 0;
        $iva          = 0;
        foreach ($data['productos'] as $value) {
            $total1 += $value['total'];
            $descuento += $value['descuento'];
            $iva += $value['tax'];
            if ($value['tax'] == 0) {
                $subtotal0 += $value['subtotal'];
                $descuento_0 += $value['descuento'];
            } else {
                $subtotal12 += $value['subtotal'];
                $descuento_12 += $value['descuento'];
            }
        }
        $base_imponible = $subtotal0 + $subtotal12;

        $ct_venta = ct_ventas::where('nro_comprobante', $info_comprobante)->where('id_empresa', $id_empresa)->where('valor_contable', '>', 0)->first();
        if (!is_null($ct_venta)) {
            //dd('cabecera asiento');
            $input_cabecera = [
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $total1,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            Ct_Asientos_Cabecera::where('id', $ct_venta->id_asiento)->update($input_cabecera);
            $id_asiento_cabecera = $ct_venta->id_asiento;

            //$id_asiento_cabecera = 0;
            //GUARDAdo TABLA CT_VENTA.
            $factura_venta = [
                'id_empresa'        => $id_empresa,
                'tipo'              => 'VEN-FA',
                'fecha'             => $fecha_as,
                'divisas'           => 1,
                'nombre_cliente'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'id_cliente'        => $cliente_datos['cedula'], //nombre_cliente
                'direccion_cliente' => $cliente_datos['direccion']['calle'],
                'ruc_id_cliente'    => $cliente_datos['cedula'],
                'telefono_cliente'  => $cliente_datos['telefono'],
                'nro_autorizacion'  => $cliente_datos['nro_autorizacion'],
                'email_cliente'     => $cliente_datos['email'],
                'id_paciente'       => $id_paciente,
                'nombres_paciente'  => $nombre_paciente,
                'seguro_paciente'   => $data['id_seguro'],
                'concepto'          => $data['concepto'],
                'copago'            => $data['copago'],
                'subtotal_0'        => $subtotal0,
                'subtotal_12'       => $subtotal12,
                'descuento'         => $descuento,
                'base_imponible'    => $base_imponible,
                'impuesto'          => $iva,
                'total_final'       => $total1,
                'valor_contable'    => $total1,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'electronica'       => $electronica,
            ];

            // return $factura_venta;
            $ct_venta->update($factura_venta);
            $id_venta = $ct_venta->id;
            //dd($id_venta);
            //$id_venta = 0;
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;

            //kardex
            $deletedRows = Ct_detalle_venta::where('id_ct_ventas', $id_venta)->delete();
            foreach ($data['productos'] as $valor) {
                $datos_iva = 0;
                if ($valor['tax'] > 0) {
                    $datos_iva = 1;
                }
                $detalle = [
                    'id_ct_ventas'    => $id_venta,
                    'id_ct_productos' => $valor['sku'],
                    'nombre'          => $valor['nombre'],
                    'cantidad'        => $valor['cantidad'],
                    'precio'          => $valor['precio'],
                    'descuento'       => $valor['descuento'],
                    'extendido'       => $valor['subtotal'],
                    'detalle'         => '',
                    'copago'          => $valor['copago'],
                    'check_iva'       => $datos_iva,
                    'porcentaje'      => '0.12',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];

                Ct_detalle_venta::create($detalle);
            }

            //***MODULO CUENTA POR COBRAR***

            //cUENTAS X COBRAR CLIENTES

            $val_tol = $total1;

            $deletedRows = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id_asiento_cabecera)->delete();

            if ($val_tol > 0) {
                $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
                // $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    // 'id_plan_cuenta'      => '1.01.02.05.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => $val_tol,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            //    2.01.07.01.01 iva sobre ventas
            if ($iva > 0) {
                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
                // $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    // 'id_plan_cuenta'      => '2.01.07.01.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $iva,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }
            // 4.1.01.02    Ventas Mercaderia Tarifa 12%
            if ($subtotal12 > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.02');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
                // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    // 'id_plan_cuenta'      => '4.1.01.02',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal12 + $descuento_12,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            // 4.1.01.01    Ventas Mercaderia Tarifa 0%
            if ($subtotal0 > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.01.01');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
                // $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    // 'id_plan_cuenta'      => '4.1.01.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal0 + $descuento_0,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            if ($descuento > 0) {
                $id_plan_confg = LogConfig::busqueda('4.1.06.01');
                $plan_cuentas = Plan_Cuentas::find($id_plan_confg);
           
                // $plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    // 'id_plan_cuenta'      => '4.1.06.01',
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'haber'               => '0',
                    'debe'                => $descuento,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            $valor_actual = Ct_ventas::where('id', $id_venta)->first();
            $id__ventas   = [

                'valor_contable' => '0',
                'estado_pago'    => 2,
            ];

            Ct_ventas::where('id', $id_venta)->update($id__ventas);
            //dd('entra');
        }
        //agregar comprobantes de ingreso

        return true;
    }

    public static function estado_comprobante($data)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = ApiFacturacionController::getNonce(12);
        $empresa    = Empresa::find($data['empresa']);
        //dd($empresa);
        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        if ($data['tipo'] == "comprobante") {
            $url = $empresa->url."billing/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "pdf") {
            $url = $empresa->url."billing/ride/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "xml") {
            $url = $empresa->url."billing/xml/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } else {
            return "no exite el tipo";
        }

        //dd($url);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API INFORMACION " . $data['tipo'],
            'url'         => $url,
            'dato1'       => 'SIN DATOS BODY',
            'dato2'       => "INICIA API COMPROBANTE",
        ]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => 'prueba',
            ),
            "ssl"  => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        //dd($options);
        $context = stream_context_create($options);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        //dd($response);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API INFORMACION " . $data['tipo'],
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API COMPROBANTE",
        ]);

        if ($data['tipo'] == "comprobante") {
            $respuesta = json_decode($response);
            //dd($respuesta);
            return $respuesta;
        } elseif ($data['tipo'] == "pdf") {
            $name    = $data['comprobante'] . '.pdf';
            $content = $response;

            //Save PDF file on the server (temp files).
            $pdf  = Storage::disk('local')->put('/ride/' . $name, $content);
            $path = storage_path() . '/app/ride/' . $name;
            if (file_exists($path)) {
                return Response::download($path);
            }
        } else {
            header('Content-type: text/xml');
            header("Content-Disposition: attachment; filename=" . $data['comprobante'] . ".xml");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $response;exit;
        }
    }

    public static function estado_comprobante_general($data)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = ApiFacturacionController::getNonce(12);
        $empresa    = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        if ($data['tipo_comprobante'] == "retencion") {
            if ($data['tipo'] == "comprobante") {
                $url = $empresa->url."billing/RETENCION_" . $data['empresa'] . "_" . $data['comprobante'];
            } elseif ($data['tipo'] == "pdf") {
                $url = $empresa->url."billing/ride/RETENCION_" . $data['empresa'] . "_" . $data['comprobante'];
            } elseif ($data['tipo'] == "xml") {
                $url = $empresa->url."billing/xml/RETENCION_" . $data['empresa'] . "_" . $data['comprobante'];
            } else {
                return "no exite el tipo";
            }
        } elseif ($data['tipo_comprobante'] == "nota_credito") {
            if ($data['tipo'] == "comprobante") {
                $url = $empresa->url."billing/NOTACREDITO_" . $data['empresa'] . "_" . $data['comprobante'];
            } elseif ($data['tipo'] == "pdf") {
                $url = $empresa->url."billing/ride/NOTACREDITO_" . $data['empresa'] . "_" . $data['comprobante'];
            } elseif ($data['tipo'] == "xml") {
                $url = $empresa->url."billing/xml/NOTACREDITO_" . $data['empresa'] . "_" . $data['comprobante'];
            } else {
                return "no exite el tipo";
            }
        } elseif ($data['tipo_comprobante'] == "guia") {
            if ($data['tipo'] == "comprobante") {
                $url = $empresa->url."billing/GUIA_" . $data['empresa'] . "_" . $data['comprobante'];
            } elseif ($data['tipo'] == "pdf") {
                $url = $empresa->url."billing/ride/GUIA_" . $data['empresa'] . "_" . $data['comprobante'];
            } elseif ($data['tipo'] == "xml") {
                $url = $empresa->url."billing/xml/GUIA_" . $data['empresa'] . "_" . $data['comprobante'];
            } else {
                return "no exite el tipo";
            }
        } else {
            return "no exite el tipo";
        }

        //dd($url);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API INFORMACION " . $data['tipo'],
            'url'         => $url,
            'dato1'       => 'SIN DATOS BODY',
            'dato2'       => "INICIA API COMPROBANTE",
        ]);
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
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API INFORMACION " . $data['tipo'],
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API COMPROBANTE",
        ]);

        if ($data['tipo'] == "comprobante") {
            $respuesta = json_decode($response);
            //dd($respuesta);
            return $respuesta;
        } elseif ($data['tipo'] == "pdf") {
            $name    = $data['comprobante'] . '.pdf';
            $content = $response;

            //Save PDF file on the server (temp files).
            $pdf  = Storage::disk('local')->put('/ride/' . $name, $content);
            $path = storage_path() . '/app/ride/' . $name;
            if (file_exists($path)) {
                return Response::download($path);
            }
        } else {
            header('Content-type: text/xml');
            header("Content-Disposition: attachment; filename=" . $data['comprobante'] . ".xml");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $response;exit;
        }
    }

    public static function estado_comprobante_externo($data)
    {

        $nonce   = ApiFacturacionController::getNonce(12);
        $empresa = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        if ($data['tipo'] == "comprobante") { 
            $url = $empresa->url."billing/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "pdf") {
            $url = $empresa->url."billing/ride/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } elseif ($data['tipo'] == "xml") {
            $url = $empresa->url."billing/xml/FACTURA_" . $data['empresa'] . "_" . $data['comprobante'];
        } else {
            return "no exite el tipo";
        }
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
        if(isset($data['externo'])){
            return $response;
        }
        if ($data['tipo'] == "comprobante") {
            $respuesta = json_decode($response);
            //dd($respuesta);
            return $respuesta;
        } elseif ($data['tipo'] == "pdf") {
            $name    = $data['comprobante'] . '.pdf';
            $content = $response;

            //Save PDF file on the server (temp files).
            $pdf  = Storage::disk('local')->put('/ride/' . $name, $content);
            $path = storage_path() . '/app/ride/' . $name;
            if (file_exists($path)) {
                return Response::download($path);
            }
        } else {
            header('Content-type: text/xml');
            header("Content-Disposition: attachment; filename=" . $data['comprobante'] . ".xml");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $response;exit;
        }
    }

    public function comprobante_publico($comprobante, $id_empresa, $tipo)
    {
        $data['empresa']     = $id_empresa;
        $data['comprobante'] = $comprobante;
        $data['tipo']        = $tipo;

        $envio = ApiFacturacionController::estado_comprobante($data);
        if ($tipo == 'comprobante') {
            return view('log_comprobante', ['envio' => $envio]);
        }
        return $envio;
    }

    public function comprobante_publico_general($comprobante, $id_empresa, $tipo, $tipo_comprobante)
    {
        $data['empresa']          = $id_empresa;
        $data['comprobante']      = $comprobante;
        $data['tipo']             = $tipo;
        $data['tipo_comprobante'] = $tipo_comprobante;

        $envio = ApiFacturacionController::estado_comprobante_general($data);
        if ($tipo == 'comprobante') {
            return view('log_comprobante', ['envio' => $envio]);
        }
        return $envio;
    }

    public static function crearComprobante($nfactura, $data, $array_pagos, $factura_id, $id_empresa, $total_pagos)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = 'FACELECTRO';
        $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        if (is_null($nfactura)) {
            $nfactura = $contador_ctv;
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num            = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id    = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->lastest()->first();
                $secuencia = intval($max_id->secuencia);
                if (strlen($secuencia) < 10) {
                    $nu             = $secuencia + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $nfactura = $numero_factura;
        }
        $objeto_validar = new Validate_Decimals();
        $id_comprobante = 0;
        if (sizeOf($array_pagos) > 0) {

            // yo ya tengo el numero de factura
            //falta el total del metodo de pago

            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($total_pagos),
                'fecha_asiento'   => date('Y-m-d'),
                'fact_numero'     => $nfactura,
                'valor'           => $objeto_validar->set_round($total_pagos),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            //1.01.02.05.01
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $desc_cuenta = Plan_Cuentas::find($id_plan_confg);

            // $desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => date('Y-m-d'),
                'haber'               => $objeto_validar->set_round($total_pagos),
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $total_pagos,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => date('Y-m-d'),
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($total_pagos),
                'id_cliente'          => $data['cliente']['cedula'],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                $val = "";
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : date('Y-m-d');
                Ct_Detalle_Pago_Ingreso::create([
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
                ]);
            }
            //contador_a son las faturas en este caso es 1
            //for($i=0;$i<$request['contador_a'];$i++){

            //   if (!is_null($request['abono_a'.$i])) {
            //if($request['id_cliente']!=null){
            //$nuevo_saldof= $objeto_validar->set_round($request['abono_a'.$i]);
            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $id_plan_confg,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => date('Y-m-d'),
                'debe'                => $total_pagos, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            //}
            $consulta_venta    = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => date('Y-m-d'),
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $data['total_factura'], //$request['saldo_a'.$i],
                    'total'             => $total_pagos, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            if ($total_pagos < $data['total_factura']) {
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

    public static function crearComprobante_noelect($nfactura, $data, $array_pagos, $factura_id, $id_empresa, $total_pagos, $fecha_as)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = 'FACELECTRO';
        $contador_ctv   = DB::table('ct_comprobante_ingreso')->get()->count();
        $numero_factura = 0;
        if (is_null($nfactura)) {
            $nfactura = $contador_ctv;
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num            = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_comprobante_ingreso')->max('id');
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $nfactura = $numero_factura;
        }
        $objeto_validar = new Validate_Decimals();
        $id_comprobante = 0;
        if (sizeOf($array_pagos) > 0) {

            // yo ya tengo el numero de factura
            //falta el total del metodo de pago

            $input_cabecera = [
                'observacion'     => 'COMPROBANTE DE INGRESO FACT:' . $nfactura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($total_pagos),
                'fecha_asiento'   => $fecha_as,
                'fact_numero'     => $nfactura,
                'valor'           => $objeto_validar->set_round($total_pagos),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            //1.01.02.05.01
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $desc_cuenta = Plan_Cuentas::find($id_plan_confg);

            // $desc_cuenta = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                // 'id_plan_cuenta'      => '1.01.02.05.01',
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $fecha_as,
                'haber'               => $objeto_validar->set_round($total_pagos),
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO REF: ' . $nfactura . ' POR LA CANTIDAD DE ' . $total_pagos,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $fecha_as,
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($total_pagos),
                'id_cliente'          => $data['cliente']['cedula'],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            //formas de pago detalle
            foreach ($array_pagos as $valor) {
                $val = "";
                if (!is_null($valor['valor'])) {
                    $val = $valor['valor'];
                } else {
                    $val = $valor['valor_base'];
                }
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : date('Y-m-d');
                Ct_Detalle_Pago_Ingreso::create([
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
                ]);
            }
            //contador_a son las faturas en este caso es 1
            //for($i=0;$i<$request['contador_a'];$i++){

            //   if (!is_null($request['abono_a'.$i])) {
            //if($request['id_cliente']!=null){
            //$nuevo_saldof= $objeto_validar->set_round($request['abono_a'.$i]);
            $id_plan_confg = LogConfig::busqueda('1.01.01.1.01');
            $desc_cuenta = Plan_Cuentas::where('id', $id_plan_confg)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $id_plan_confg,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $fecha_as,
                'debe'                => $total_pagos, //$nuevo_saldof,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);

            //}
            $consulta_venta    = null;
            $input_comprobante = null;

            if (floatval($total_pagos) > 0) {
                Ct_Detalle_Comprobante_Ingreso::create([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $fecha_as,
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id, ////$consulta_venta->id,
                    'secuencia_factura' => $nfactura, //$request['numero'.$i],
                    'total_factura'     => $data['total_factura'], //$request['saldo_a'.$i],
                    'total'             => $total_pagos, //$request['abono_a'.$i],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            if ($total_pagos < $data['total_factura']) {
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

    public static function crearRetencion($data)
    {
        //dd($data);
        //dd($data);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = ApiFacturacionController::getNonce(12);
        $empresa    = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        } elseif ($empresa->electronica != 1) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));

        $envio['company'] = $data['empresa'];
        //VALIDO SI LA CEDULA O RUC ES VALIDO
        $valida_cedula = ApiFacturacionController::validarCedula($data['proveedor']['cedula']);

        if (!$valida_cedula && ($data['proveedor']['tipo'] == 4 || $data['proveedor']['tipo'] == 5)) {
            return "numero de cedula incorrecto";
        }

        //INGRESO LOS DATOS DE LA PERSONA
        $proveedor['document'] = $data['proveedor']['cedula'];
        if ($data['proveedor']['tipo'] == 6) {
            $tipo = "06";
        }if ($data['proveedor']['tipo'] == 8) {
            $tipo = "08";
        } elseif (strlen($data['proveedor']['cedula']) == 13 && substr($data['proveedor']['cedula'], -3) == '001') {
            $tipo = "04";
        } else {
            $tipo = "05";
        }

        $proveedor['documentType']        = $tipo;
        $proveedor['name']                = $data['proveedor']['nombre'];
        $proveedor['surname']             = $data['proveedor']['apellido'];
        $proveedor['email']               = $data['proveedor']['email'];
        $envio['proveedor']               = $proveedor;
        $comprobante['fechaemision']      = $data['comprobante']['fecha'];
        $comprobante['tipocomprobante']   = $data['comprobante']['tipo'];
        $comprobante['periodofiscal']     = $data['comprobante']['periodo'];
        $comprobante['numerocomprobante'] = $data['comprobante']['comprobante'];
        $envio['comprobantearetener']     = $comprobante;

        if (count($data['impuesto']) <= 0) {
            return "no existen impuestos";
        }
        //ingreso y creo los productos
        $impuesto = array();
        foreach ($data['impuesto'] as $key => $value) {
            $arreglo                  = array();
            $arreglo['tipoimpuesto']  = $value['tipo'];
            $arreglo['impuesto']      = $value['impuesto'];
            $arreglo['baseimponible'] = floatval(number_format($value['baseimponible'], 2, '.', ''));
            $arreglo['porcentaje']    = floatval(number_format($value['porcentaje'], 2, '.', ''));
            $arreglo['valorretenido'] = floatval(number_format($value['valorretenido'], 2, '.', ''));
            array_push($impuesto, $arreglo);
        }
        $envio['detallesretencion'] = $impuesto;

        //parametros de pago
        $pago['establecimiento'] = $empresa->establecimiento;
        $pago['ptoEmision']      = $empresa->punto_emision;
        $info_adicional          = array();

        foreach ($data['pago']['informacion_adicional'] as $key => $value) {

            $arreglo          = array();
            $arreglo['key']   = $value['nombre'];
            $arreglo['value'] = $value['valor'];
            array_push($info_adicional, $arreglo);
        }
        $pago['infoAdicional']      = $info_adicional;
        $envio['billingParameters'] = $pago;
        $envio['userAgent']         = "SIAAM SOFTWARE/1";
        //dd($envio);

        $envio_2 = json_encode($envio);
        $url     = $empresa->url . 'billing/createretencion/';
        //dd($url);

        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $envio_2,
            'dato2'       => "Inicia api RETENCION",
        ]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $envio_2,
            ),
        );
        //dd($url);
        $context = stream_context_create($options);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API RETENCION",
        ]);
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-000000013"}';*/
        $respuesta_array = json_decode($response);
        return $respuesta_array;
    }

    public static function crearNotasCredito($data)
    {
        //dd($data);
        //dd('entra');
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = ApiFacturacionController::getNonce(12);
        $empresa    = Empresa::find($data['empresa']);

        //compruebo si la empresa esta habilitada como facturacion electronica
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        } elseif ($empresa->electronica != 1) {
            return "empresa no permite facturacion electronica";
        }

        //extraigo datos de la empresas y creo el token
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));

        $envio['company'] = $data['empresa'];
        //VALIDO SI LA CEDULA O RUC ES VALIDO
        $valida_cedula = ApiFacturacionController::validarCedula($data['cliente']['cedula']);
        if (!$valida_cedula && ($data['cliente']['tipo'] == 4 || $data['cliente']['tipo'] == 5)) {
            return "numero de cedula incorrecto";
        }

        //INGRESO LOS DATOS DE LA PERSONA
        $proveedor['document'] = $data['cliente']['cedula'];
        if ($data['cliente']['tipo'] == 6) {
            $tipo = "06";
        }if ($data['cliente']['tipo'] == 8) {
            $tipo = "08";
        } elseif (strlen($data['cliente']['cedula']) == 13 && substr($data['cliente']['cedula'], -3) == '001') {
            $tipo = "04";
        } else {
            $tipo = "05";
        }

        $proveedor['documentType']       = $tipo;
        $proveedor['name']               = $data['cliente']['nombre'];
        $proveedor['surname']            = $data['cliente']['apellido'];
        $proveedor['email']              = $data['cliente']['email'];
        $proveedor['mobile']             = $data['cliente']['telefono'];
        $proveedor['address']['street']  = $data['cliente']['direccion']['calle'];
        $proveedor['address']['city']    = $data['cliente']['direccion']['ciudad'];
        $proveedor['address']['country'] = "EC";
        $envio['person']                 = $proveedor;
        //dd($data);
        $comprobante['numerocomprobante'] = $data['factura']['comprobante'];
        $comprobante['fechaemision']      = $data['factura']['fechaemision'];
        $comprobante['motivo']            = $data['factura']['motivo'];
        $envio['facturamodificar']        = $comprobante;

        if (count($data['productos']) <= 0) {
            return "no existen productos";
        }
        //ingreso y creo los productos
        $productos = array();
        foreach ($data['productos'] as $key => $value) {
            $arreglo             = array();
            $arreglo['sku']      = $value['sku'];
            $arreglo['name']     = $value['nombre'];
            $arreglo['qty']      = $value['cantidad'];
            $arreglo['price']    = floatval(number_format($value['precio'], 2, '.', ''));
            $arreglo['discount'] = floatval(number_format($value['descuento'], 2, '.', ''));
            $arreglo['subtotal'] = floatval(number_format($value['subtotal'], 2, '.', ''));
            $arreglo['tax']      = floatval(number_format($value['tax'], 2, '.', ''));
            $arreglo['total']    = floatval(number_format($value['total'], 2, '.', ''));
            array_push($productos, $arreglo);
        }
        $envio['items'] = $productos;

        //parametros de pago
        $pago['establecimiento'] = $empresa->establecimiento;
        $pago['ptoEmision']      = $empresa->punto_emision;
        $info_adicional          = array();

        foreach ($data['pago']['informacion_adicional'] as $key => $value) {

            $arreglo          = array();
            $arreglo['key']   = $value['nombre'];
            $arreglo['value'] = $value['valor'];
            array_push($info_adicional, $arreglo);
        }
        $pago['infoAdicional']      = $info_adicional;
        $pago['formaPago']          = $data['pago']['forma_pago'];
        $pago['plazoDias']          = $data['pago']['dias_plazo'];
        $envio['billingParameters'] = $pago;
        $envio['userAgent']         = "SIAAM SOFTWARE/1";
        //dd($envio);

        $envio_2 = json_encode($envio);
        $url     = $empresa->url . 'billing/createnotacredito/';
        //dd($url);

        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $envio_2,
            'dato2'       => "Inicia api NOTA DE CREDITO",
        ]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $envio_2,
            ),
        );

        //dd($options);
        //dd($url);
        $context = stream_context_create($options);
        //dd($context, $url);
        $response = file_get_contents($url, false, $context);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API NOTA DE CREDITO",
        ]);
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-000000013"}';*/
        $respuesta_array = json_decode($response);
        return $respuesta_array;
    }

    public static function newCrearNotasCredito($data){
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $empresa = Empresa::find($data["company"]);

        

        $envio_2 = json_encode($data);
        $url     = $empresa->url . 'billing/createnotacredito/';

        $nonce      = ApiFacturacionController::getNonce(12);
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));

        $log_api = [
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato4'       => "NC-P {$data['facturamodificar']['numerocomprobante']}"
        ];

        $log_api['dato1'] = $envio_2;
        $log_api['dato2'] = "Inicia api NOTA DE CREDITO";

        //Inicia el Api
        Log_Api::create($log_api);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $envio_2,
            ),
        );

     
        $context = stream_context_create($options);
        
        $response = file_get_contents($url, false, $context);

        $log_api['dato1'] = $response;
        $log_api['dato2'] = "FIN API NOTA DE CREDITO";

        Log_Api::create($log_api);
        
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-."}';*/
        $respuesta_array = json_decode($response);
        return $respuesta_array;

    }

    public function mod_venta($id)
    {
        $venta                       = Ct_ventas::find($id);
        $venta->revision_electronica = 1;
        $venta->save();
    }

    public static function reproceso_fecha($data, $info_comprobante)
    {
        $id_empresa = $data['empresa'];
        if (is_numeric($data['fecha'])) {
            $timestamp = ($data['fecha'] - 25568) * 86400;
            $fecha     = date("Y-m-d", $timestamp);
            //dd($fecha);
        } else {
            $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $data['fecha'])));
        }

        $ct_venta = ct_ventas::where('nro_comprobante', $info_comprobante)->where('id_empresa', $id_empresa)->first();
        if (!is_null($ct_venta)) {
            $ct_venta->fecha = $fecha;
            $ct_venta->save();
            $asiento_venta                = Ct_Asientos_Cabecera::find($ct_venta->id_asiento);
            $asiento_venta->fecha_asiento = $fecha;
            $asiento_detalle              = Ct_Asientos_Detalle::where('id_asiento_cabecera', $asiento_venta->id)->update(['fecha' => $fecha]);
            $asiento_venta->save();
            $detalle_comp = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $ct_venta->id)->first();
            if (!is_null($detalle_comp)) {
                $comprobante        = Ct_Comprobante_Ingreso::find($detalle_comp->id_comprobante);
                $comprobante->fecha = $fecha;
                $comprobante->save();

                $pagos = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $detalle_comp->id_comprobante)->get();
                foreach ($pagos as $key => $value) {
                    $pago        = Ct_Detalle_Pago_Ingreso::find($value->id);
                    $pago->fecha = $fecha;
                    $pago->save();
                }
                $asiento_comprobante                = Ct_Asientos_Cabecera::find($comprobante->id_asiento_cabecera);
                $asiento_detalle_comprobante        = Ct_Asientos_Detalle::where('id_asiento_cabecera', $asiento_comprobante->id)->update(['fecha' => $fecha]);
                $asiento_comprobante->fecha_asiento = $fecha;
                $asiento_comprobante->save();
            }

        }

        return true;
    }

}
