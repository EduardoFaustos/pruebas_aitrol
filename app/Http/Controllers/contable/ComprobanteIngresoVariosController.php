<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_acreedores;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Comprobante_Ingreso_Varios;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Empresa;
use Sis_medico\Ct_Ventas;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso_Varios;
use Sis_medico\Ct_Detalle_Pago_Ingreso_Varios;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\EstadoResultado;
use Sis_medico\Financery;
use Sis_medico\Validate_Decimals;
use Sis_medico\Numeros_Letras;
use Sis_medico\ProyeccionFinanciera2;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Illuminate\Support\Facades\Session;


class ComprobanteIngresoVariosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $bancos= Ct_Bancos::where('estado','1')->get();
        $comp_ingreso = Ct_Comprobante_Ingreso_Varios::where('id_empresa', $id_empresa)->orderBy('id','desc')->paginate(20);
        $empresa= Empresa::where('id',$id_empresa)->first();

        return view('contable/comprobante_ingreso_varios/index', ['comp_ingreso' => $comp_ingreso,'bancos'=>$bancos,'empresa'=>$empresa]);
    }

    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $lista_banco = Ct_Bancos::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $plan_cuentas= Plan_Cuentas::join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.estado', '2')->where('p.id_empresa', $id_empresa)->select('plan_cuentas.id', 'p.plan as plan', 'p.nombre as nombre')->get();
        $divisas = Ct_Divisas::where('estado', '1')->get();
        
        $banco = Ct_Caja_Banco::where('estado', '1')->where('id_empresa',$id_empresa)->get();
        $empresa= Empresa::where('id',$id_empresa)->first();
        return view('contable/comprobante_ingreso_varios/create', ['tipos_pagos' => $tipo_pago,'plan_cuentas'=>$plan_cuentas, 'banco' => $banco, 'divisas' => $divisas, 'bancos' => $lista_banco,'empresa'=>$empresa]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("sds");
        $constraints = [
            'id' => $request['id'],
            'id_banco' => $request['id_banco'],
            'secuencia' => $request['secuencia'],
            'concepto' => $request['concepto'],
            'fecha'    => $request['fecha'],
            'id_asiento_cabecera' =>$request['id_asiento'],
        ];

        //dd($constraints);
        $acreedores = $this->doSearchingQuery($constraints);
        $bancos= Ct_Bancos::where('estado','1')->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa= Empresa::where('id',$id_empresa)->first();
        return view('contable/comprobante_ingreso_varios/index', ['comp_ingreso' => $acreedores, 'searchingVals' => $constraints,'bancos'=>$bancos,'empresa'=>$empresa]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Comprobante_Ingreso_Varios::query();
        $fields = array_keys($constraints);
        $id_empresa = Session::get('id_empresa');
        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('id_empresa', $id_empresa)->orderBy('id','desc')->paginate(20);
    }
    public function anular(Request $request,$id){
        if(!is_null($id)){
            $comp_ingreso = Ct_Comprobante_Ingreso_Varios::where('estado', '1')->where('id', $id)->first();
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_empresa = $request->session()->get('id_empresa');
            $concepto = $request['concepto'];
            $idusuario  = Auth::user()->id;
            if(!is_null($comp_ingreso)){
                $input = [
                    'estado' => '0',
                    'nota'   => $concepto,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariomod'                 => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento= Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => $concepto,
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
               
            }
        }
        LogAsiento::anulacion('CIV', $id_asiento, $asiento->id);
       
        return redirect()->route('comprobante_ingreso_v.index');
    }

    public function store(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $contador_ctv = DB::table('ct_comprobante_ingreso_varios')->where('id_empresa', $id_empresa)->get()->count();
        $objeto_validar = new Validate_Decimals();
        $numero_factura = 0;
        
        if ($contador_ctv == 0) {

            //return 'No Retorno nada';
            $num = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
        } else {
            //Obtener Ultimo Registro de la Tabla ct_compras
            $max_id = DB::table('ct_comprobante_ingreso_varios')->max('id');
            if (($max_id >= 1) && ($max_id < 10)) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
            if (($max_id >= 10) && ($max_id < 100)) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
            }
        }

        $input_cabecera = [
            'observacion' => $request['concepto'] . ' por la cantidad de ' . $objeto_validar->set_round($request['total_ingresos']),
            'fecha_asiento' => $request['fecha'],
            'fact_numero' => $numero_factura,
            'valor' => $objeto_validar->set_round($request['total_ingresos']),
            'id_empresa'                    => $id_empresa,
            'estado'                        => '1',
            'ip_creacion'                   => $ip_cliente,
            'ip_modificacion'               => $ip_cliente,
            'id_usuariocrea'                => $idusuario,
            'id_usuariomod'                 => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        $banco= Ct_Caja_Banco::find($request['id_banco']);
        $desc_cuenta = Plan_Cuentas::where('id', $banco->cuenta_mayor)->first();
        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera'           => $id_asiento_cabecera,
            'id_plan_cuenta'                => $banco->cuenta_mayor,
            'descripcion'                   => $desc_cuenta->nombre,
            'fecha'                         => $request['fecha'],
            'debe'                          => $objeto_validar->set_round($request['total_ingresos']),
            'haber'                         => '0',
            'estado'                        => '1',
            'id_usuariocrea'                => $idusuario,
            'id_usuariomod'                 => $idusuario,
            'ip_creacion'                   => $ip_cliente,
            'ip_modificacion'               => $ip_cliente,
        ]);
        $input_comprobante = [
            'observaciones'   =>  $request['concepto'] . ' por la cantidad de ' . $request['total_ingresos'],
            'estado'          => '1',
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'fecha'           => $request['fecha'],
            'id_banco'        => $request['id_banco'],
            'secuencia'       => $numero_factura,
            'divisas'         => $request['divisas'],
            'id_empresa'      => $id_empresa,
            'total_ingreso'   => $objeto_validar->set_round($request['total_ingresos']),
            'concepto'        => $request['concepto'],
            'comentarios'     => $request['nota'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_comprobante = Ct_Comprobante_Ingreso_Varios::insertGetId($input_comprobante);
        for ($i = 0; $i <= $request['contador']; $i++) {
            
            if (!is_null($request['valor' . $i])) {
                if($request['visibilidad'.$i]=='1'){
                    if ($request['tipo' . $i] == '4') {
                        Ct_Detalle_Pago_Ingreso_Varios::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo_tarjeta'                => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo_tarjeta'                => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'new'                            => '1', 
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                            'tipo_c'                         => 'CIV',      
                        ]);
                    } else if ($request['tipo' . $i] == '6') {
                        Ct_Detalle_Pago_Ingreso_Varios::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo_tarjeta'                => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo_tarjeta'                => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'new'                            => '1', 
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                            'tipo_c'                         => 'CIV',
                        ]);
                    } else if ($request['tipo' . $i] == '1') {
                        Ct_Detalle_Pago_Ingreso_Varios::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'new'                            => '1', 
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                            'tipo_c'                         => 'CIV',
                        ]);
                    } else {
                        Ct_Detalle_Pago_Ingreso_Varios::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_banco'                       => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                        ]);
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'                 => $id_comprobante,
                            'fecha'                          => $request['fecha' . $i],
                            'numero'                         => $request['numero_a' . $i],
                            'id_banco'                       => $request['banco' . $i],
                            'id_tipo'                        => $request['tipo' . $i],
                            'total'                          => $request['valor' . $i],
                            'new'                            => '1', 
                            'cuenta'                         => $request['cuenta' . $i],
                            'girador'                        => $request['girador' . $i],
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $idusuario,
                            'id_usuariomod'                  => $idusuario,
                            'tipo_c'                         => 'CIV',
                        ]);
                    }
                }              
            }
        }
        for ($i = 0; $i <=$request['contador_a']; $i++) {
            if (!is_null($request['haber' . $i])) {
                if($request['visibilidads'.$i]=='1'){
                    //new changes at 1 December 2020
                    
                    $consul= Plan_Cuentas::find($request['codigo'.$i]);
                    Ct_Detalle_Comprobante_Ingreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'fecha'                          => $request['fecha'],
                        'codigo'                         => $request['codigo' . $i],
                        'nombre'                         => $consul->nombre,
                        'haber'                          => $request['haber' . $i],
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                    $plan_descripcion= Plan_Cuentas::where('id',$request['codigo'.$i])->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $request['codigo' . $i],
                        'debe'                => '0',
                        'haber'               => $request['haber' . $i],
                        'descripcion'         => $plan_descripcion->nombre,
                        'fecha'               => $request['fecha'],
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                    ]);
                }               
            }
        }
        return $id_comprobante;
    }
    public function editar($identificacion)
    {


        return view('contable/acreedores/edit');
    }
    public function edit($id,Request $request){
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $comprobante_ingreso= Ct_Comprobante_Ingreso_Varios::where('id',$id)->where('id_empresa',$id_empresa)->first();
        $detalle_ingreso= Ct_Detalle_Comprobante_Ingreso_Varios::where('id_comprobante_varios',$comprobante_ingreso->id)->get();
        $clientes= Ct_Clientes::where('estado','1')->get();        
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $tipos      = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $lista_banco = Ct_Caja_Banco::where('estado', '1')->get();
        return view('contable/comprobante_ingreso_varios/edit',['id_empresa'=>$id_empresa,'tipos'=>$tipos,'detalle_ingreso'=>$detalle_ingreso,'comprobante_ingreso'=>$comprobante_ingreso,'tipo_pago'=>$tipo_pago,'bancos'=>$lista_banco,'lista_banco'=>$lista_banco]);
    }


    public function update(Request $request, $id)
    {


        return redirect()->intended('/contable/acreedor');
    }
    public function banco(Request $request)
    {
        if (!is_null($request['validacion'])) {
            $banco = Ct_Caja_Banco::where('estado', '1')->where('id', $request['validacion'])->first();
            if (!is_null($banco)) {
                return $banco;
            } else {
                return 'error banco';
            }
        } else {
            return 'error vacio';
        }
    }
    
    public function pdf_comprobante(Request $request, $id)
    {
        $comp_ingreso = Ct_Comprobante_Ingreso_Varios::where('id', $id)->first();
        $empresa = Empresa::where('id', $comp_ingreso->id_empresa)->first();
        $letras = new Numeros_Letras();
        $detalle_ingreso = Ct_Detalle_Comprobante_Ingreso::where('id_comprobante', $comp_ingreso->id)->first();
        //la variable convertir con la clase Numeros Letras
        $total_str = $letras->convertir(number_format($comp_ingreso->total_ingreso, 2, '.', ''), "DOLARES", "CTVS");
        //dd($factura_contable);
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_ingreso->id_asiento_cabecera)->first();
        $asiento_detalle = null;
        if ($asiento_cabecera != null) {
            $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        }

        if ($comp_ingreso != '[]') {

            $vistaurl = "contable.comprobante_ingreso_varios.pdf_comprobante_ingreso_varios";
            $view     = \View::make($vistaurl, compact('comp_ingreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('Comprobante de Ingreso' . $id . '.pdf');
        }

        return 'error';
    }
}