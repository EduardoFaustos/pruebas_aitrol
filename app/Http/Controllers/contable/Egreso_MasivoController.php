<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\LogAsiento;
use Sis_medico\Numeros_Letras;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\User;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Cruce;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Detalle_Debito_Acreedores;
use Sis_medico\Ct_Cruce_Cuentas;

class Egreso_MasivoController extends Controller
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
        $id_empresa           = $request->session()->get('id_empresa');
        $empresa              = Empresa::where('id', $id_empresa)->first();
        $comprobante_egreso_m = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->orderBy('id', 'DESC')->get();
        //     dd($comprobante_egreso_m);

        return view('contable/comp_egreso_masivo/index', ['empresa' => $empresa, 'comprobante_egreso_m' => $comprobante_egreso_m]);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa    = $request->session()->get('id_empresa');
        $divisas       = Ct_divisas::where('estado', '1')->get();
        $banco         = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $tipo_pago     = Ct_Tipo_Pago::where('estado', '1')->get();
        $formas_pago   = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();

        $tipos   = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $compras = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('f_autorizacion', 'desc')->take(400)->get();
        if ($id_empresa == '0992704152001' || $id_empresa == '1314490929001' || $id_empresa == '1307189140001' || $id_empresa == '0993170887001') {
            return view('contable/comp_egreso_masivo/create', ['divisas' => $divisas, 'tipos_pagos' => $tipo_pago, 'tipos' => $tipos, 'bancos' => $banco, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'compras' => $compras, 'formas_pago' => $formas_pago]);
        } else {
            return view('contable/comp_egreso_masivo/create2', ['divisas' => $divisas, 'tipos_pagos' => $tipo_pago, 'tipos' => $tipos, 'bancos' => $banco, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'compras' => $compras, 'formas_pago' => $formas_pago]);
        }

    }

    public function edit($id, Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa            = $request->session()->get('id_empresa');
        $empresa               = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $divisas               = Ct_divisas::where('estado', '1')->get();
        $formas_pago           = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $banco                 = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $comprobante_egreso_m  = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_comprobante_m = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $comprobante_egreso_m->id)->get();
        return view('contable/comp_egreso_masivo/edit', ['divisas' => $divisas, 'formas_pago' => $formas_pago, 'empresa' => $empresa, 'banco' => $banco, 'detalle_egreso_m' => $detalle_comprobante_m, 'comprobante_egreso_m' => $comprobante_egreso_m]);
    }
    public function store2(Request $request)
    {
        if(Auth::user()->id == "0957258056"){
            //dd("store 2", $request->all());
        }
        $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->get()->count();
        $numero_factura    = 0;
        $superavit         = (int) $request['superavit'];
        $secuencia_factura = (int) $request['asiento'];
        $secuencia         = 0;
        $id_proveedor      = $request['id_proveedor'];
        $id_empresa        = $request->session()->get('id_empresa');
        $fechahoy          = $request['fecha_hoy'];
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura    = 0;
        $banco             = $request['banco'];
        //dd($request->abono0);
        $objeto_validar = new Validate_Decimals();

        if ($contador_ctv == 0) {

            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {

            $max_id = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->latest()->first();
            $max_id = intval($max_id->secuencia);
            if (strlen($max_id) < 10) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }

        $input_cabecera = [
            'observacion'     => $request['aaa'],
            'fecha_asiento'   => $request['fecha_hoy'],
            'fact_numero'     => $numero_factura,
            'valor'           => $request['valor_cheque'],
            'id_empresa'      => $id_empresa,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        if (($banco) != null) {
            $nuevo_saldof      = $objeto_validar->set_round($request['valor_cheque']);
            $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();

            $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $consulta_db_cajab->cuenta_mayor,
                'descripcion'         => $consulta_db_cajab->nombre,
                'fecha'               => $request['fecha_hoy'],
                'haber'               => $nuevo_saldof,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        }

        $input_comprobante = [
            'descripcion'         => $request['aaa'],
            'estado'              => '1',
            'beneficiario'        => $request['nombre_proveedor'],
            'fecha_cheque'        => $request['fecha_cheque'],
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'check'               => $request['verificar_cheque'],
            'id_secuencia'        => $request['numero'],
            'id_pago'             => $request['formas_pago'],
            'id_caja_banco'       => $request['banco'],
            'no_cheque'           => $request['numero_cheque'],
            'fecha_comprobante'   => $request['fecha_hoy'],
            'secuencia'           => $numero_factura,
            'girado_a'            => $request['giradoa'],
            'id_empresa'          => $id_empresa,
            'valor_pago'          => $request['valor_cheque'],
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
        ];

        $id_comprobante = Ct_Comprobante_Egreso_Masivo::insertGetId($input_comprobante);
        //dd($request->all());
        $primerarray = [];
        for ($i = 0; $i <= $request['contador']; $i++) {

            if (!is_null($request['abono' . $i])) {

                if (($request['abono' . $i]) > 0) {
                    //$nuevo_saldof = $objeto_validar->set_round($request['abono' . $i]);
                    $consulta_proveedor = Proveedor::where('id', $request['proveedor' . $i])->first();
                    $nuevo_saldof       = $objeto_validar->set_round($request['abono' . $i]);
                    $c                  = $consulta_proveedor->id_cuentas;
                    if ($c == null) {
                        $c = "2.01.03.01.01";
                    }
                    $cuent_descrip   = Plan_Cuentas::find($c);
                    if(Auth::user()->id == "0957258056"){
                        //dd($request['id_actualiza' . $i]);
                    }
                    $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                    Ct_Detalle_Comprobante_Egreso_Masivo::create([
                        'id_comprobante'  => $id_comprobante,
                        'observacion'     => $request['aaa'],
                        'id_compra'       => $consulta_compra->id,
                        'id_secuencia'    => $request['numero' . $i],
                        'id_proveedor'    => $request['proveedor' . $i],
                        'saldo_base'      => $request['saldo' . $i],
                        'abono'           => $request['abono' . $i],
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuent_descrip->id,
                        'descripcion'         => $cuent_descrip->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $nuevo_saldof,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
                //dd($request['abono'.$i]);

            }
        }

        $consulta_compra = null;
        $input_actualiza = null;

        for ($i = 0; $i <= $request['contador']; $i++) {
            if (!is_null($request['abono' . $i]) && $request['abono' . $i] > 0) {
                $nuevo_saldo = 0;
                //actualizar valor contable de cada tabla
                $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                //dd($consulta_compra);
                if ($consulta_compra != null || $consulta_compra != '[]') {
                    if ($request['abono' . $i] > 0) {
                        if ($request['abono' . $i] > ($consulta_compra->valor_contable)) {
                            $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                        } else {
                            $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                        }

                        $nuevo_saldof    = $objeto_validar->set_round($nuevo_saldo);
                        $input_actualiza = null;
                        if ($nuevo_saldof != 0) {
                            $input_actualiza = [
                                'estado'          => '2', //poner otro estado para que no salga en las consultas
                                'valor_contable'  => $nuevo_saldof,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                        } else {
                            $input_actualiza = [
                                'estado'          => '3', //poner otro estado para que no salga en las consultas
                                'valor_contable'  => $nuevo_saldof,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                        }
                        $consulta_compra->update($input_actualiza);
                    }
                }
            }
        }
        return $id_comprobante;
    }
    public function store(Request $request)
    {
        if(Auth::user()->id == "0957258056"){
          //  dd("store 1", $request->all());
        }
        $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->get()->count();
        $numero_factura    = 0;
        $superavit         = (int) $request['superavit'];
        $secuencia_factura = (int) $request['asiento'];
        $secuencia         = 0;
        $id_proveedor      = $request['id_proveedor'];
        $id_empresa        = $request->session()->get('id_empresa');
        $fechahoy          = $request['fecha_hoy'];
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura    = 0;
        $banco             = $request['banco'];
        //dd($request->abono0);
        $objeto_validar = new Validate_Decimals();

        if ($contador_ctv == 0) {

            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {

            $max_id = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->latest()->first();
            $max_id = intval($max_id->secuencia);
            if (strlen($max_id) < 10) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }

        $input_cabecera = [
            'observacion'     => $request['aaa'],
            'fecha_asiento'   => $request['fecha_hoy'],
            'fact_numero'     => $numero_factura,
            'valor'           => $request['valor_cheque'],
            'id_empresa'      => $id_empresa,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        if (($banco) != null) {
            $nuevo_saldof      = $objeto_validar->set_round($request['valor_cheque']);
            $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();

            $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $consulta_db_cajab->cuenta_mayor,
                'descripcion'         => $consulta_db_cajab->nombre,
                'fecha'               => $request['fecha_hoy'],
                'haber'               => $nuevo_saldof,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        }

        $input_comprobante = [
            'descripcion'         => $request['aaa'],
            'estado'              => '1',
            'beneficiario'        => $request['nombre_proveedor'],
            'fecha_cheque'        => $request['fecha_cheque'],
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'check'               => $request['verificar_cheque'],
            'id_secuencia'        => $request['numero'],
            'id_pago'             => $request['formas_pago'],
            'id_caja_banco'       => $request['banco'],
            'no_cheque'           => $request['numero_cheque'],
            'fecha_comprobante'   => $request['fecha_hoy'],
            'secuencia'           => $numero_factura,
            'girado_a'            => $request['giradoa'],
            'id_empresa'          => $id_empresa,
            'valor_pago'          => $request['valor_cheque'],
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
        ];

        $id_comprobante = Ct_Comprobante_Egreso_Masivo::insertGetId($input_comprobante);
        //dd($request->all());
        $primerarray = [];
        for ($i = 0; $i <= $request['contador']; $i++) {

            if (!is_null($request['abono' . $i])) {

                if (($request['abono' . $i]) > 0) {

                    $nuevo_saldof       = $objeto_validar->set_round($request['abono' . $i]);
                    $consulta_proveedor = Proveedor::where('id', $request['proveedor' . $i])->first();
                    if (!is_null($consulta_proveedor)) {
                        $segundoarray = [$consulta_proveedor->id_cuentas, $request['abono' . $i]];
                        $key          = array_search($consulta_proveedor->id_cuentas, array_column($primerarray, '0'));
                        //dd($segundoarray);

                        if ($key !== false) {

                            $valor                = $primerarray[$key][1];
                            $valor                = $valor + $request['abono' . $i];
                            $primerarray[$key][0] = $consulta_proveedor->id_cuentas;
                            $primerarray[$key][1] = $valor;
                        } else {
                            array_push($primerarray, $segundoarray);
                        }
                    }

                    $cuenta_provloc = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EGRESOMASIV_PROV_LOC');

                    //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_provloc->cuenta_guardar,
                        'descripcion'         => $cuenta_provloc->nombre_mostrar,
                        'fecha'               => $request['fecha_hoy'],
                        'debe'                => $nuevo_saldof,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);

                    $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                    Ct_Detalle_Comprobante_Egreso_Masivo::create([
                        'id_comprobante'  => $id_comprobante,
                        'observacion'     => $request['aaa'],
                        'id_compra'       => $consulta_compra->id,
                        'id_secuencia'    => $request['numero' . $i],
                        'id_proveedor'    => $request['proveedor' . $i],
                        'saldo_base'      => $request['saldo' . $i],
                        'abono'           => $request['abono' . $i],
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
                //dd($request['abono'.$i]);

            }
        }
        //dd($primerarray);
        for ($file = 0; $file < count($primerarray); $file++) {

            $cuent_descrip = Plan_Cuentas::where('id', $primerarray[$file][0])->first();
            //dd($cuent_descrip);
            $cuentapadre = $cuent_descrip;
            $cuenta      = $primerarray[$file][0];
            //$debe =   $arr_primario[$file][1];
            $debe = number_format($primerarray[$file][1], 2, '.', '');

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuent_descrip->id,
                'descripcion'         => $cuent_descrip->nombre,
                'fecha'               => $fechahoy,
                'debe'                => $objeto_validar->set_round($debe),
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        }

        $consulta_compra = null;
        $input_actualiza = null;

        for ($i = 0; $i <= $request['contador']; $i++) {
            if (!is_null($request['abono' . $i]) && $request['abono' . $i] > 0) {
                $nuevo_saldo = 0;
                //actualizar valor contable de cada tabla
                $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                //dd($consulta_compra);
                if ($consulta_compra != null || $consulta_compra != '[]') {
                    if ($request['abono' . $i] > 0) {
                        if ($request['abono' . $i] > ($consulta_compra->valor_contable)) {
                            $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                        } else {
                            $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                        }

                        $nuevo_saldof    = $objeto_validar->set_round($nuevo_saldo);
                        $input_actualiza = null;
                        if ($nuevo_saldof != 0) {
                            $input_actualiza = [
                                'estado'          => '2', //poner otro estado para que no salga en las consultas
                                'valor_contable'  => $nuevo_saldof,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                        } else {
                            $input_actualiza = [
                                'estado'          => '3', //poner otro estado para que no salga en las consultas
                                'valor_contable'  => $nuevo_saldof,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                        }
                        $consulta_compra->update($input_actualiza);
                    }

                }
            }
        }
        return $id_comprobante;
    }
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $constraints = [
            'id'                  => $request['id'],
            'girado_a'            => $request['girado_a'],
            'descripcion'         => $request['descripcion'],
            'no_cheque'           => $request['cheque'],
            'fecha_cheque'        => $request['fecha'],
            'id_asiento_cabecera' => $request['id_asiento_cabecera'],

        ];

        $comprobante_egreso_m = $this->doSearchingQuery($constraints, $id_empresa);
        $empresa              = Empresa::where('id', $id_empresa)->first();
        return view('contable/comp_egreso_masivo/index', ['comprobante_egreso_m' => $comprobante_egreso_m, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Comprobante_Egreso_Masivo::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }

    public function pdf_egreso($id, Request $request)
    {

        //dd($asiento_detalle);
        $id_empresa                 = $request->session()->get('id_empresa');
        $comprobante_egreso_m       = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $comprobante_egreso_detalle = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $id)->get();
        $total_abono                = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $id)->sum('abono');

        //dd($total_abono);

        $empresa          = Empresa::where('id', $comprobante_egreso_m->id_empresa)->first();
        $letras           = new Numeros_Letras();
        $total_str        = $letras->convertir(number_format($comprobante_egreso_m->valor_pago, 2, '.', ''), "DOLARES", "CTVS");
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comprobante_egreso_m->id_asiento_cabecera)->first();
        $compras          = Ct_compras::where('secuencia_f', $comprobante_egreso_m->id_secuencia)->first();
        $asiento_detalle  = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        $vistaurl         = "contable.comp_egreso_masivo.pdf_egreso_masivo";
        $view             = \View::make($vistaurl, compact('comprobante_egreso_m', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'comprobante_egreso_detalle'))->render();
        $pdf              = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Comprobante de Egreso Masivo' . '.pdf');
    }
    public function reporte_compegresoma(Request $request, $id, $tipo)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        //$fecha_proc = date('d/m/Y');
        $empresa   = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();

        $reporte_datos = [];
        //$rol_form_pag = [];
        //dd($tipo);
        //dd($reporte_datos);
        if ($tipo == '1') {
            $reporte_datos = DB::table('ct_comprobante_egreso_masivo as ctcm')
                ->join('ct_detalle_comprobante_egreso_masivo as ctem', 'ctem.id_comprobante', 'ctcm.id')
                ->join('ct_tipo_pago as ctp', 'ctcm.id_pago', 'ctp.id')
                ->join('proveedor as p', 'ctem.id_proveedor', 'p.id')
                ->join('ct_configuracion_bancos as ctcb', 'ctcb.id', 'p.id_configuracion')
                ->groupBy('ctem.id_proveedor')
                ->where('ctcm.id', $id)
                ->select(DB::raw('SUM(ctem.abono) as suma_abono'), 'ctcm.*', 'ctem.id_proveedor', 'ctp.nombre as ntipoc', 'ctcm.*', 'ctcb.submotivo_pago')
                ->get();

            //dd($reporte_datos);
        }
        //dd($reporte_datos);
        Excel::create('ORDEN COMPROBANTE MASIVO', function ($excel) use ($empresa, $reporte_datos) {
            $excel->sheet('Comprobante Masivo', function ($sheet) use ($empresa, $reporte_datos) {

                //$fecha_d = date('Y/m/d');
                $i = 3;
                $j = 0;

                $sum_valor = 0;
                $cont_empl = 0;

                $sheet->mergeCells('A1:K1');

                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue('ORDEN COMPROBANTE MASIVO');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K1', function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Forma Pag/Cob');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Banco');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Num.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Identificacion');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Doc.');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUC');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Telefono');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Referencia');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A2:K2', function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                });
                // DETALLES
                $sheet->setColumnFormat(array(
                    'E' => '0.00',
                ));

                foreach ($reporte_datos as $value) {
                    //dd($value);
                    $txtcolor = '#000000';

                    $id_proveedor = Proveedor::find($value->id_proveedor);

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        $tipo_pago = '';
                        // CU:CUENTA,EF:EFECTIVO,CH:CHEQUE

                        if (is_null($value->id_pago)) {
                            $tipo_pago = "NO TIENE";
                        } else {
                            if ($value->tipo == '1') {
                                $tipo_pago = 'CU';
                            } elseif ($value->tipo == '2') {
                                $tipo_pago = 'EF';
                            } elseif ($value->tipo == '2') {
                                $tipo_pago = 'CH';
                            }
                        }

                        // manipulate the cel
                        $cell->setValue($tipo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->submotivo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // dd($value->nombre);
                        $tip_cuent = '';

                        // Tipos de Cuenta
                        //10: AHORRO 00:CORRIENTE
                        // manipulate the cel
                        if ($id_proveedor->tipo_cuenta == 1) {

                            $cell->setValue('10');
                        } else {
                            $cell->setValue('00');
                        }
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->cuenta);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->suma_abono);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(' ' . $id_proveedor->identificacion);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        //C: CEDULA, R:RUC, P:PASAPORTE, X:NINGUNO
                        if (strlen($id_proveedor->identificacion) == 10) {
                            $cell->setValue('C');
                        } elseif (strlen($id_proveedor->identificacion) > 10) {
                            $cell->setValue('R');
                        } else {
                            $cell->setValue('P');
                        }
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel

                        $cell->setValue(' ' . $id_proveedor->identificacion);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->beneficiario);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->telefono1);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->submotivo_pago == "30") {
                            $cell->setValue('PR');
                        } else {
                            $cell->setValue('RU');
                        }
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sum_valor = $sum_valor + $value->suma_abono;

                    $i         = $i + 1;
                    $cont_empl = $cont_empl + 1;
                }

                $j        = $i + 1;
                $k        = $j + 1;
                $l        = $k + 1;
                $txtcolor = '#000000';

                //Subtotales
                $sheet->cell('A' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTALES');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Termina Sub Total
                //Total
                $sheet->cell('A' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('B' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('C' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('FORMA');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('CANT.');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                $sheet->cells('D' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Fin TOtal
                //USD
                $sheet->cell('A' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('USD');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('CU');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D' . $k, function ($cell) use ($cont_empl, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E' . $k, function ($cell) use ($sum_valor, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //FIN USD
                //TOTAL GENERAL
                $sheet->cell('A' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTAL GENERAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('DOLARES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D' . $l, function ($cell) use ($cont_empl, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('D' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E' . $l, function ($cell) use ($sum_valor, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                //FIN TOTAL GENERAL

            });
        })->export('xlsx');
    }
    public function anular($id, Request $request)
    {
        if (!is_null($id)) {
            $comp_ingreso = Ct_Comprobante_Egreso_Masivo::where('id', $id)->where('estado', '1')->first();
            $ip_cliente   = $_SERVER["REMOTE_ADDR"];
            $concepto     = $request['concepto'];
            $id_empresa   = $request->session()->get('id_empresa');

            $idusuario = Auth::user()->id;
            if (!is_null($comp_ingreso)) {

                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12
                if (!is_null($comp_ingreso->detalles)) {
                    foreach ($comp_ingreso->detalles as $value) {
                        $consulta_venta = Ct_compras::where('id', $value->id_compra)->where('estado', '>', '0')->where('id_empresa', $id_empresa)->first();

                        if (!is_null($consulta_venta)) {
                            $valor           = $consulta_venta->valor_contable;
                            $suma            = ($value->abono) + $valor;
                            $input_actualiza = [
                                'valor_contable'  => $suma,
                                'estado'          => '1',
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                        }
                    }
                }
                $input = [
                    'estado'          => '0',
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento                = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado        = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles   = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => strtoupper($concepto),
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
                    'valor'           => $asiento->valor,
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
                        'fecha'               => $asiento->fecha_asiento,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }
                LogAsiento::anulacion("EG-M", $id_asiento, $asiento->id);
                // Log_Contable::create([
                //     'tipo'           => 'EM',
                //     'valor_ant'      => $asiento->valor,
                //     'valor'          => $asiento->valor,
                //     'id_usuariocrea' => $idusuario,
                //     'id_usuariomod'  => $idusuario,
                //     'observacion'    => $asiento->concepto,
                //     'id_ant'         => $asiento->id,
                //     'id_referencia'  => $id_asiento,
                // ]);
                return redirect()->route('acreedores_cegreso');
            }
        } else {
            return 'error';
        }
    }
    public function repararEgresoMasivo($id_empresa){
        $compras = Ct_Compras::where('id_empresa', $id_empresa)->orderBy('id', 'Desc')->get();

        foreach($compras as $c){
            $id = $c->id;
            //egresos
            $v_egreso=0;//---
            $egreso = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id)->get();
            foreach ($egreso as $x) {
                if ($x->comp_egreso->estado == 1) {
                    $v_egreso += $x->abono;
                }
            }

            //cruce
            $v_cruce = 0;//---
            //dd($id);
            if ($id != "") {
                $cruce = Ct_Detalle_Cruce::where('id_factura', $id)->get();
                foreach ($cruce as $x) {
                    if ($x->cabecera->estado == 1) {
                        $v_cruce += $x->total;
                    }
                }
            }

           
            //bancario
            $v_bancario = 0;//---
            if ($id != "") {
                $debito = Ct_Debito_Bancario_Detalle::where('id_compra', $id)->get();
                foreach ($debito as $x) {
                    if ($x->cabecera->estado == 1) {
                        $v_bancario += $x->abono;
                    }
                }
            }
            //retenciones
            $v_retencion=0;//---
            if ($id != "") {
                $retenciones = Ct_Retenciones::where('id_compra', $id)->where('estado', '1')->first();
                if (!is_null($retenciones)) {
                    $v_retencion = $retenciones->valor_fuente + $retenciones->valor_iva;
                }
            }

            $v_credito = 0;//---
            if ($id != "") {
                $credito = Ct_Credito_Acreedores::where('id_compra', $id)->where('estado', '1')->get();
                if (!is_null($credito)) {
                    foreach ($credito as $x) {
                        $v_credito += $x->valor_contable;
                    }
                }
            }

            $v_debito = 0;//---
            if ($id != "") {
                $debito = Ct_Detalle_Debito_Acreedores::where('id_factura', $id)->where('estado', '1')->get();
                foreach ($debito as $x) {
                    if ($x->cabecera->estado == 1) {
                        $v_debito += $x->total;
                    }
                }
            }

            $v_cruce_cuentas = 0;//---
            $cruce = Ct_Cruce_Cuentas::where('id_factura', $id)->where('estado', '1')->get();
            if (!is_null($cruce)) {
                foreach ($cruce as $x) {
                    $v_cruce += $x->total;
                }
            }

            $v_egreso_masivo = 0;//---
            if ($id != "") {
                $egreso = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_compra', $id)->get();
                foreach ($egreso as $x) {
                    if ($x->comp_egreso->estado == 1) {
                        $v_egreso_masivo += $x->abono;
                    }
                }
            }
            //dd("aqi",$v_cruce);
            $v_total = $v_egreso + $v_cruce + $v_bancario + $v_retencion + $v_credito + $v_debito + $v_cruce_cuentas + $v_egreso_masivo;
            if($c->total_final>= $v_total){
                $saldo =  $c->total_final - $v_total ;
                echo "<p>Compra:{$c->id} [{$c->total_final}] -> retencion:{$v_retencion} -> cruce:{$v_cruce} -> bancario:{$v_bancario} -> credito:{$v_credito} -> debitoo: {$v_debito} -> cruce cuentas:{$v_cruce_cuentas} -> egreso_masivo {$v_egreso_masivo} -> total:{$v_total} -> saldo:{$saldo}<br> </p>";
                
            }
            
        }

    }
}
