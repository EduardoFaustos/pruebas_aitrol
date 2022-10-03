<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Valida_Anticipo;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use Sis_medico\LogConfig;

class NominaOtrosAnticiposController extends Controller
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

        $fecha_actual = date('Y-m-d');
        $anio_actual  = date('Y');
        $mes_actual   = date('m');

        //dd($mes_actual);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        //Obtenemos los empleados ingresados por empresa para calculo de Anticipo
        // $empl_nomina = Ct_Nomina::where('estado', '1')
        //     ->where('id_empresa', $id_empresa)
        //     ->orderby('nombres', 'asc')->get();
        //->get();

        $empl_nomina = Ct_Nomina::where('ct_nomina.estado', '1')
            ->where('id_empresa', $id_empresa)
            ->join('users as u', 'u.id', 'ct_nomina.id_user')
            ->select('u.*','ct_nomina.*')
            ->orderby('u.apellido1', 'asc')->get();

        //Obtenemos el Numero de Empleados por empresa
        $cont_empl = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')
            ->get()->count();

        //dd($empl_nomina);

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio_actual)
            ->where('mes', $mes_actual)
            ->where('estado', '1')
            ->first();

        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)
            ->where('anio', $anio_actual)
            ->where('mes', $mes_actual)
            ->where('estado', '1')
            ->first();

        $valida_anticipo = 0;
        if (!is_null($inf_val_anticip)) {
            $valida_anticipo = 1;
        }

        if (!is_null($anticipo_valida)) {
            $valida_anticipo = 1;
        }
       

        return view('contable.rol_otro_anticipo.index_otros_anticipo', ['empresa' => $empresa, 'empl_nomina' => $empl_nomina, 'cont_empl' => $cont_empl, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'fecha_actual' => $fecha_actual, 'mes_actual' => $mes_actual, 'anio_actual' => $anio_actual, 'valida_anticipo' => $valida_anticipo]);
    }

    public function obtener_anticipo_quincena(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $valor_porcentaje = $request['valor_porcent'];

        $id_empresa = $request->session()->get('id_empresa');

        $empl_rol = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')->get();

        if (!is_null($empl_rol)) {

            foreach ($empl_rol as $value) {

                $valor_anticip = (($value->sueldo_neto) * ($valor_porcentaje)) / 100;

                $input = [
                    'id_user'         => $value->id_user,
                    'id_empresa'      => $value->id_empresa,
                    'anio'            => $id_anio,
                    'mes'             => $id_mes,
                    'sueldo'          => $value->sueldo_neto,
                    'porcentaje'      => $valor_porcentaje,
                    'valor_anticipo'  => $valor_anticip,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ];

                Ct_Rh_Valor_Anticipos::create($input);
            }

        }

        //return view('contable.rol_anticipo_quincena.resultado_anticipo',['empl_rol' => $empl_rol,'valor_porcentaje' => $valor_porcentaje,'id_empresa' => $id_empresa,'id_anio' => $id_anio,'id_mes' => $id_mes]);

    }

    //Calculo de Anticipos Individual (Empleados)
    public function obtener_anticipo_individual($id_nom, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $valor_porcentaje = $request['valor_porcent'];

        $id_empresa = $request->session()->get('id_empresa');

        $info_empleado = Ct_Nomina::where('id', $id_nom)
            ->where('id_empresa', $id_empresa)
            ->where('estado', '1')
            ->first();

        if (!is_null($info_empleado)) {

            $valor_anticip = (($info_empleado->sueldo_neto) * ($valor_porcentaje)) / 100;

            $input = [
                'id_user'         => $info_empleado->id_user,
                'id_empresa'      => $info_empleado->id_empresa,
                'anio'            => $id_anio,
                'mes'             => $id_mes,
                'sueldo'          => $info_empleado->sueldo_neto,
                'porcentaje'      => $valor_porcentaje,
                'valor_anticipo'  => $valor_anticip,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ];

            Ct_Rh_Valor_Anticipos::create($input);
        }

    }

    //Calculo de Anticipos Individual (Empleados)
    public function registrar_anticipo_quincena(Request $request)
    {

        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $id_usuario    = Auth::user()->id;
        $id_nom        = $request['id_nom'];
        $anio          = $request['anio'];
        $id_mes        = $request['mes'];
        $valor_anticip = $request['val_anticip'];
        $id_empresa    = $request->session()->get('id_empresa');

        $info_empleado = Ct_Nomina::where('id', $id_nom)
            ->where('id_empresa', $id_empresa)
            ->where('estado', '1')
            ->first();

        if (!is_null($info_empleado)) {

            $input = [
                'id_user'         => $info_empleado->id_user,
                'id_empresa'      => $id_empresa,
                'anio'            => $anio,
                'mes'             => $id_mes,
                'fecha_creacion'  => $request['fech_crea'],
                'id_tipo_pago'    => $request['tip_pag'],
                'numero_cuenta'   => $request['num_cuent'],
                'banco'           => $request['banco'],
                'cuenta_saliente' => $request['cuent_salient'],
                'num_cheque'      => $request['num_cheq'],
                'fecha_cheque'    => $request['fecha_cheq'],
                'sueldo'          => $info_empleado->sueldo_neto,
                'valor_anticipo'  => $valor_anticip,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'estado'          => '1',
            ];
            Ct_Rh_Valor_Anticipos::create($input);
        }

    }

    public function registrar_asiento_anticipo_quincena(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');

        $anio_cobro   = $request['anio'];
        $mes_cobro    = $request['mes'];
        $sum_anticipo = $request['sum_anticip'];
        //$fech_creacion = Date('Y-m-d H:i:s');
        $fech_creacion = $request['fech_crea'];
        $cuent_sal     = $request['cuent_sal'];

        //Obtenemos el Mes de Inicio
        $txt_mes_cobro = '';
        if ($mes_cobro == '12') {
            $txt_mes_cobro = 'DICIEMBRE';
        } elseif ($mes_cobro == '11') {
            $txt_mes_cobro = 'NOVIEMBRE';
        } elseif ($mes_cobro == '10') {
            $txt_mes_cobro = 'OCTUBRE';
        } elseif ($mes_cobro == '9') {
            $txt_mes_cobro = 'SEPTIEMBRE';
        } elseif ($mes_cobro == '8') {
            $txt_mes_cobro = 'AGOSTO';
        } elseif ($mes_cobro == '7') {
            $txt_mes_cobro = 'JULIO';
        } elseif ($mes_cobro == '6') {
            $txt_mes_cobro = 'JUNIO';
        } elseif ($mes_cobro == '5') {
            $txt_mes_cobro = 'MAYO';
        } elseif ($mes_cobro == '4') {
            $txt_mes_cobro = 'ABRIL';
        } elseif ($mes_cobro == '3') {
            $txt_mes_cobro = 'MARZO';
        } elseif ($mes_cobro == '2') {
            $txt_mes_cobro = 'FEBRERO';
        } elseif ($mes_cobro == '1') {
            $txt_mes_cobro = 'ENERO';
        }

        $text = 'Anticipo 1ERA Quincena' . ':' . ' ' . 'Año Cobro Anticipo' . ':' . $anio_cobro . ' ' . 'Mes Cobro Anticipo' . ':' . $txt_mes_cobro . ' ' . 'Valor' . ':' . $sum_anticipo;

        /************************************
         *****Inserta Ct_Asientos_Cabecera***
        /************************************/
        $input_cabecera = [
            'observacion'     => 'ANTICIPOS EMPLEADOS 1ERA QUINCENA:' . ' ' . 'Año Cobro Anticipo' . ':' . $anio_cobro . ' ' . 'Mes Cobro Anticipo' . ':' . $txt_mes_cobro . ' ' . 'Por la Cantidad de' . ':' . $sum_anticipo,
            'fecha_asiento'   => $fech_creacion,
            //'fact_numero'     => $numero_anticipo,
            'id_empresa'      => $id_empresa,
            //'observacion'     => $text,
            'valor'           => $sum_anticipo,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        //dd($input_cabecera);

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        //dd($id_asiento_cabecera);

        /************************************
         *****Inserta Ct_Asientos_Detalle*****
        /************************************/

        if ($sum_anticipo > 0) {

            $plan_cuentas = Plan_Cuentas::where('id', $cuent_sal)->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuent_sal,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fech_creacion,
                'debe'                => '0',
                'haber'               => $sum_anticipo,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

        }

        if ($sum_anticipo > 0) {

            //$cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPO_ANT_SUELDOS');

            $id_plan_config = LogConfig::busqueda('1.01.02.03.01');
            $desc_cuenta  = Plan_Cuentas::where('id', $id_plan_config)->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $desc_cuenta->id,
                'descripcion'         => $desc_cuenta->nombre,
                'fecha'               => $fech_creacion,
                'debe'                => $sum_anticipo,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

        }

        Ct_Valida_Anticipo::create([

            'id_empresa'      => $id_empresa,
            'asiento'         => $id_asiento_cabecera,
            'anio'            => $anio_cobro,
            'mes'             => $mes_cobro,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);

    }

    //Actualiza Valor Anticipo Primera Quincena
    public function actualiza_valor_anticipos($id_nomina)
    {

        $inf_nomina = Ct_Nomina::where('id', $id_nomina)->first();
        //->where('estado','1')
        //->first();

        return view('contable/rol_otro_anticipo/mod_update_val_anticipo', ['id_nomina' => $id_nomina, 'inf_nomina' => $inf_nomina]);

    }

    //Guarda Valor Anticipo Actualizados
    public function store_valor_anticipo(Request $request)
    {

        $val_anticipo = $request['val_anticipo'];

        //dd($val_anticipo);

        Ct_Nomina::where('id', $request['id_nomina'])
            ->where('estado', '1')
            ->update(['val_anticip_quince' => $request['val_anticipo']]);

        $msj = "ok";

        return ['msj' => $msj];

    }

    //Valida Que Exista Anticipo
    public function imprime_anticipos_quincena(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $anio            = $request['year'];
        $mes             = $request['mes'];
        $inf_val_anticip = 0;
        $anticipo_valida = 0;

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->get()->count();

        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->get()->count();

        if ($inf_val_anticip > 0 or $anticipo_valida > 0) {

            $msj = "si";
            return ['msj' => $msj];

        } else {

            $msj = "no";
            return ['msj' => $msj];

        }

    }

    public function comprobar_empresa(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
         return ['nombre_empresa' => $empresa->nombrecomercial, 'id_empresa' => $empresa->id];

    }

    //Descarga Pdf de Anticipos de Quincena
    public function obtener_pdf_anticipo_quincena2($mes, $anio, Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $id_mes  = $mes;
        $id_anio = $anio;

        // $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
        //     ->where('anio', $anio)
        //     ->where('mes', $mes)
        //     ->where('estado', '1')
        //     ->groupBy('id_user')
        //     ->get();

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->join('users', 'users.id', 'ct_rh_valor_anticipos.id_user')
            ->where('ct_rh_valor_anticipos.anio', $anio)
            ->where('ct_rh_valor_anticipos.mes', $mes)
            ->where('ct_rh_valor_anticipos.estado', '1')
            ->orderBy('users.apellido1')
            ->groupBy('users.id')
            ->get();


        $ult_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->get()->first();

        //dd($inf_val_anticip);

        $vistaurl = "contable.rol_otro_anticipo.pdf_anticipo_quincena";
        $view     = \View::make($vistaurl, compact('inf_val_anticip', 'empresa', 'ult_val_anticip'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Anticipo 1era Quincena Mensual.pdf');

    }

    
    public function obtener_pdf_anticipo_quincena2($mes, $anio, Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $id_mes  = $mes;
        $id_anio = $anio;

        // $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
        //     ->where('anio', $anio)
        //     ->where('mes', $mes)
        //     ->where('estado', '1')
        //     ->groupBy('id_user')
        //     ->get();

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->join('users', 'users.id', 'ct_rh_valor_anticipos.id_user')
            ->where('ct_rh_valor_anticipos.anio', $anio)
            ->where('ct_rh_valor_anticipos.mes', $mes)
            ->where('ct_rh_valor_anticipos.estado', '1')
            ->orderBy('users.apellido1')
            ->groupBy('users.id')
            ->get();


        $ult_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->get()->first();

        //dd($inf_val_anticip);

        $vistaurl = "contable.rol_otro_anticipo.pdf_anticipo_quincena";
        $view     = \View::make($vistaurl, compact('inf_val_anticip', 'empresa', 'ult_val_anticip'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Anticipo 1era Quincena Mensual.pdf');

    }


    public function busca_quincena(Request $request)
    {
        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->first();
        $anticipo_qui    = 0;
        $anticipo_valida = 0;
        $anticipo_qui    = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)->where('anio', $request['anio'])->where('mes', $request['mes'])->where('estado', '1')->get()->count();
        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)->where('anio', $request['anio'])->where('mes', $request['mes'])->where('estado', '1')->get()->count();
        //dd($anticipo_qui);
        if ($anticipo_qui > 0 or $anticipo_valida > 0) {
            return json_encode("existe");
        } else {
            return json_encode("no");
        }
    }

}
