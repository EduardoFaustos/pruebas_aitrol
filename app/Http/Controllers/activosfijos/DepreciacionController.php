<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Session;
use Sis_medico\AfActivo;
use Sis_medico\AfDepreciacionCabecera;
use Sis_medico\AfDepreciacionDetalle;
use Sis_medico\AfTipo;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use DateTime;


class DepreciacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }
    public function buscar(Request $request)
    {
       //0dd("buscar");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        
        if (!is_null($request['fecha']) && !is_null($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
        if (!is_null($request['tipo']) && !is_null($request['tipo'])) {
            $tipo = $request['tipo'];
        } else {
            $tipo = null;
        }

        $fecha_desde = $request['desde'];
        $fecha_hasta = $request['hasta'];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //$generados  = $this->getGenerados($fecha, $id_empresa);
        $activos = $this->getActivos($tipo, $id_empresa, $fecha_desde, $fecha_hasta);

        $tipos   = AfTipo::where('estado', '!=', 0)->get();
        return view('activosfijos/documentos/depreciacion/index', ['empresa' => $empresa, 'fecha' => $fecha, 'tipos' => $tipos, 'activos' => $activos, 'tipo' => $tipo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    public function index(Request $request)
    {
        //dd("index");
        //dd("holis");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['fecha']) && !is_null($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
        if (!is_null($request['tipo']) && !is_null($request['tipo'])) {
            $tipo = $request['tipo'];
        } else {
            $tipo = null;
        }

        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //$generados  = $this->getGenerados($fecha, $id_empresa);
        //dd($generados);
        $activos = $this->getActivos($tipo, $id_empresa, $fecha_desde, $fecha_hasta);
        $tipos   = AfTipo::where('estado', '!=', 0)->get();

        $afdepreciados = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', 1)
        ->where('id_empresa', $id_empresa)
        ->join('af_depreciacion_detalle as dep_det','dep_det.depreciacion_cabecera_id','af_depreciacion_cabecera.id')
        ->join('af_activo as af','af.id','dep_det.activo_id')
        ->select('af.*')
        ->get();
        return view('activosfijos/documentos/depreciacion/index', ['empresa' => $empresa, 'fecha' => $fecha, 'tipos' => $tipos, 'activos' => $activos, 'tipo' => $tipo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);

    }

    public function getGenerados($fecha, $id_empresa)
    {
        $fecha     = str_replace('/', '-', $fecha);
        $timestamp = \Carbon\Carbon::parse($fecha)->timestamp;
        $fecha     = date('Y-m-d', $timestamp);

        $datos = '[]';
        if ($fecha != null) {
            $datos = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', 1)
                ->where('id_empresa', $id_empresa)
                ->join('af_depreciacion_detalle as dep_det','dep_det.depreciacion_cabecera_id','af_depreciacion_cabecera.id')
                ->join('af_activo as af','af.id','dep_det.activo_id')
                ->select('af.*')
                ->get();
        }
        // dd($facturas);
        return $datos;
    }

    public function getActivos($tipo, $id_empresa, $fecha_desde, $fecha_hasta)
    {
        $activo = AfActivo::where('estado', 1)->where('empresa', $id_empresa)->wherebetween('fecha_compra', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59']);
        if ($tipo != null) {
            $activo = $activo->where('tipo_id', $tipo);
        }
        $activo = $activo->get();

        return $activo;
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        if (!is_null($request['filperiodo'])) {
            $periodo = $request['filperiodo'];
        } else {
            $periodo = date('Y-m');
        }
        $request['periodo'] = $request['filperiodo'];
        
        $this->guardarDepreciacion($request);


        return redirect()->route('afDepreciacion.index', [$request]);

        // return Redirect::route('ats.index')->with($request);
        // $this->index($request);

    }

    public function guardarDepreciacion($data)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        //dd($data);
        $input = [
            'fecha'           => $data->fecha_asiento,
            'id_empresa'      => $id_empresa,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_cabecera = AfDepreciacionCabecera::insertGetId($input);
        $days = $data->days;
        $i = 0;

        foreach ($data->id_activo as $value) {
            $activo = AfActivo::where('estado', 1)->where('id', $value)->first();
            if (($activo->costo != null and $activo->costo != 0) and ($activo->tasa != null and $activo->tasa != 0)) {
                //dd($activo);

                /*if (isset($activo->ultima_depreciacion()->totaldepreciado)) {
                    $valordepreciacion = (($activo->ultima_depreciacion()->saldo * $activo->tasa) / 100);
                } else { 
                    $valordepreciacion = (($activo->costo * $activo->tasa) / 100);
                }*/

                if ($days[$i] >= 30) {
                    $days[$i] = 30;
                }
                
                $valordepreciacion = ((($activo->costo * ($activo->tasa/100))/360)*$days[$i]);  

                if (isset($activo->ultima_depreciacion()->totaldepreciado)) {
                    $totaldepreciado = $activo->ultima_depreciacion()->totaldepreciado;
                } else {
                 $totaldepreciado = 0;
                }
                
                $totaldepreciado += $valordepreciacion;
                $saldo = $activo->costo - $totaldepreciado;

                AfDepreciacionDetalle::create([
                    'depreciacion_cabecera_id' => $id_cabecera,
                    'activo_id'                => $activo->id,
                    'costo'                    => $activo->costo, //aqui puse el nombre de la cuenta del acreedor
                    'porcentaje'               => $activo->tasa,
                    'vidautil'                 => $activo->vida_util,
                    'valordepreciacion'        => round($valordepreciacion, 2),
                    'totaldepreciado'          => round($totaldepreciado, 2),
                    'saldo'                    => $saldo,
                    'estado'                   => '1',
                    'id_usuariocrea'           => $idusuario,
                    'id_usuariomod'            => $idusuario,
                    'ip_creacion'              => $idusuario,
                    'ip_modificacion'          => $idusuario,
                ]); 
            }

        }

        $i++;

        $this->asientoDepreciacion($id_cabecera, $data->fecha_asiento, $data->det_asiento);
        
        return; 
    }

    public function asientoDepreciacion($id_cab_depreciacion, $fecha_asiento, $det_asiento)
    {
        $cabecera   = AfDepreciacionCabecera::where('id', $id_cab_depreciacion)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        $cabecera   = [
            'observacion'     => $det_asiento,
            //'observacion'     => 'DEPRECIACIÓN ACUMULADA DE ACTIVOS FIJOS A FECHA : ' . date('d/m/Y'),
            'fecha_asiento'   => $fecha_asiento,
            // 'fact_numero'       => $request['serie_factura'] . $request['secuencia'],
            'valor'           => 0,
            'estado'          => '2',
            'id_empresa'      => $id_empresa,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabecera);

        $detalles = AfDepreciacionDetalle::where('depreciacion_cabecera_id', $id_cab_depreciacion)
            ->join('af_activo as a', 'af_depreciacion_detalle.activo_id', 'a.id')
            ->join('af_tipo as t', 'a.tipo_id', 't.id')
            ->select('af_depreciacion_detalle.depreciacion_cabecera_id','t.cuantadepreciacion', 't.cuentagastos','t.id as id_tipo', DB::raw('SUM(valordepreciacion) as valordepreciacion'))
            ->groupBy('t.id')
            ->get();
        //dd($detalles);

            
        $acumvalor = 0;

        //dd($detalles);
        foreach ($detalles as $value) {
            $cuenta = Plan_Cuentas::where('id', $value->cuantadepreciacion)->first();
            if (!is_null($cuenta)) {
                $acumvalor += $value->valordepreciacion;
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->cuantadepreciacion,
                    'descripcion'         => $cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => date('Y-m-d H:i:s'),
                    'debe'                => '0',
                    'haber'               => $value->valordepreciacion,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }

        }

        foreach ($detalles as $value) {

            $cuenta = Plan_Cuentas::where('id', $value->cuentagastos)->first();
            if (!is_null($cuenta)) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->cuentagastos,
                    'descripcion'         => $cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => date('Y-m-d H:i:s'),
                    'debe'                => $value->valordepreciacion,
                    'haber'               => 0,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }

        }

        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $id_asiento_cabecera)->update(['valor' => $acumvalor]);

    }
    public function pdf_depreciacionesmen(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $tipo  = $request['tipo'];


        $activos = AfActivo::where('estado', '1')->where('empresa', $empresa->id);

        if (!is_null($tipo)) {
            $activos = $activos->where('tipo_id', $tipo);
        }

        if ($desde != null || $hasta != null) {
            $activos = $activos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
        }

        $activos = $activos->get();
      
        $view = \View::make('activosfijos.documentos.depreciacion.pdf_depreciacionesmen', compact('empresa','desde','tipo','activos','tipo', 'hasta'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Depreciacion_acumulada.pdf');
    }

}
