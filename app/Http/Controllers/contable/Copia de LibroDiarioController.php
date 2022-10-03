<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;

class LibroDiarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if (!is_null($request['fecha']) && !is_null($request['fecha_hasta'])) {
            $fecha       = $request['fecha'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha       = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $registros  = DB::table('ct_asientos_cabecera as ct_c')
            ->leftjoin('empresa as p', 'p.id', 'ct_c.id_empresa')
            ->select('ct_c.id', 'ct_c.fecha_asiento', 'ct_c.observacion', 'ct_c.valor', 'p.nombrecomercial', 'ct_c.estado')
            ->where('ct_c.id_empresa', $id_empresa)
            ->orderby('id', 'desc')
            ->get();

        return view('contable/diario/index', ['registros' => $registros, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa]);

    }

    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Asientos_Cabecera::findorfail($id);
        $empresa  = Empresa::where('id', '0992704152001')->first();
        return view('contable/diario/asiento', ['registro' => $registro, 'empresa' => $empresa]);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $cuentas    = plan_cuentas::where('estado', '2')->get();
        return view('contable/diario/create', ['cuentas' => $cuentas, 'empresa' => $empresa]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'ct_c.id'             => $request['id'],
            'nombrecomercial'     => $request['empresa'],
            'observacion'         => $request['detalle'],
            'ct_c.fact_numero'    => $request['secuencia_f'],
            'ct_c.fecha_asiento'  => $request['fecha'],
            'ct_c.id_usuariocrea' => $request['fac_crea'],
        ];
        //dd($constraints);
        $registros = $this->doSearchingQuery($constraints, $request);
        return view('contable/diario/index', ['request' => $request, 'registros' => $registros, 'searchingVals' => $constraints]);

    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query      = DB::table('ct_asientos_cabecera as ct_c')
            ->join('empresa as p', 'p.id', 'ct_c.id_empresa')
            ->where('ct_c.id_empresa', $id_empresa)
            ->select('ct_c.id', 'p.nombrecomercial', 'ct_c.fecha_asiento', 'ct_c.observacion', 'ct_c.valor', 'ct_c.estado', 'ct_c.fact_numero');
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->get();
    }
    public function buscar_empresa(Request $request)
    {

        $codigo       = $request['term'];
        $data         = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombrecomercial) as completo
                  FROM `empresa`
                  WHERE CONCAT_WS(' ',nombrecomercial) like '" . $seteo . "'
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => $request['observacion'],
            'fecha_asiento'   => $request['fecha_asiento'] . ' ' . date('H:i:s'),
            'valor'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        for ($i = 1; $i <= $request['contador']; $i++) {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento,
                'id_plan_cuenta'      => $request['id_plan' . $i],
                'debe'                => $request['debe' . $i],
                'haber'               => $request['haber' . $i],
                'fecha'               => date('Y-m-d H:i:s'),
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        return redirect()->route('librodiario.index');

    }

    public function libro_mayor(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        if (isset($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('Y-m-d');
        }

        if (isset($request['fecha_hasta'])) {
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_hasta = date('Y-m-d');
        }

        if (isset($request['cuenta'])) {
            $filcuenta = $request['cuenta'];
        } else {
            $filcuenta = '[]';
        }

        $conditions = array();
        if ($filcuenta != '[]') {
            foreach ($filcuenta as $field) {
                // $conditions[] = ['id', 'like', '%' . $field . '%'];
                $conditions[] = $field;
            }
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $scuentas   = plan_cuentas::all();

        //$cuentas = Plan_Cuentas::where('id', 'like', '_.__.__')->orwhere('id', 'like', '_._.__')->orwhere('id', 'like', '_.__.___')->get();
        if (count($conditions) > 0) {
            // $cuentas = Plan_Cuentas::where('id', 'like', $filcuenta)->get();
            $cuentas = Plan_Cuentas::whereIn('id', $conditions)->get();
        } else {
            //$cuentas = Plan_Cuentas::where('id', 'like', '_.__.__')->orwhere('id', 'like', '_._.__')->orwhere('id', 'like', '_.__.___')->get();
            $cuentas = array();
        }

        if (!isset($request['imprimir'])) {
            // return view('contable/libro_mayor/index', ['cuentas' => $cuentas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta,
            // 'empresa' => $empresa, 'scuentas' => $scuentas, 'filcuenta' => $filcuenta, 'id_empresa'=>$id_empresa]);

            return view('contable/libro_mayor/index', compact('cuentas', 'fecha', 'fecha_hasta', 'empresa', 'scuentas', 'filcuenta', 'id_empresa'));
        } else {
            if (isset($request['filfecha'])) {
                $fecha = $request['filfecha'];
            } else {
                $fecha = date('Y-m-d');
            }

            if (isset($request['filfecha_hasta'])) {
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_hasta = date('Y-m-d');
            }

            if (isset($request['filcuenta'])) {
                $filcuenta = $request['filcuenta'];
            } else {
                $filcuenta = '[]';
            }

            $conditions = array();
            if ($filcuenta != '[]' and $filcuenta != null) {
                $filcuenta = explode(",", $filcuenta);
                foreach ($filcuenta as $field) {
                    $conditions[] = ['id', 'like', '%' . $field . '%'];
                }
            }

            if (count($conditions) > 0) {
                // $cuentas = Plan_Cuentas::where('id', 'like', $filcuenta)->get();
                $cuentas = Plan_Cuentas::where($conditions)->get();
            } else {
                //$cuentas = Plan_Cuentas::where('id', 'like', '_.__.__')->orwhere('id', 'like', '_._.__')->orwhere('id', 'like', '_.__.___')->get();
                $cuentas = array();
            }

            if ($request['exportar'] == "") {
                $vistaurl = "contable/libro_mayor/print";
                $view     = \View::make($vistaurl, compact('cuentas', 'fecha', 'fecha_hasta', 'empresa', 'scuentas', 'filcuenta', 'id_empresa'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('Mayor-' . $filcuenta . '.pdf');
            } else {
                $this->libro_mayor_excel($fecha, $fecha_hasta, $empresa, $cuentas);
            }

            /*  return view('contable/libro_mayor/print', ['cuentas' => $cuentas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'empresa'=>$empresa, 'scuentas'=>$scuentas, 'filcuenta'=>$filcuenta]);*/
        }

    }
    public function libro_mayor_excel($fecha_desde, $fecha_hasta, $empresa, $cuentas)
    {

        Excel::create('LibroMayor-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $cuentas) {
            $excel->sheet('LibroMayor', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $cuentas) {
                $sheet->mergeCells('A1:G1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:G2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:G3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("LIBRO MAYOR");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:G4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
                    $cell->setValue("$fecha_desde al $fecha_hasta");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$this->cab_detalle($sheet, 5);
                // dd('Detalle');
                $i = $this->setDetalles($sheet, 5, $cuentas, $fecha_desde, $fecha_hasta, $empresa->id);
                $sheet->setColumnFormat(array(
                    'E' => '0.00',
                    'F' => '0.00',
                    'G' => '0.00',
                ));

                //  CONFIGURACION FINAL
                $sheet->cells('A3:G3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A5:G5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                ));

            });
        })->export('xlsx');
    }

    public function cab_detalle($sheet, $i)
    {
        //$sheet->mergeCells('A4:A5');
        $sheet->cell("A$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('FECHA');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        // $sheet->mergeCells('B5:E5');
        $sheet->cell("B$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('ASIENTO');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');

        });
        $sheet->cell("C$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('CUENTA');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("D$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('DETALLE');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("E$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('DEBE');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("F$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('HABER');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("G$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('SALDO');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $i++;
        return $i;
    }

    public function setDetalles($sheet, $n, $cuentas, $fecha_desde, $fecha_hasta, $id_empresa)
    {
        //dd($fecha_desde);
        foreach ($cuentas as $cuenta) {
            $registros = \Sis_medico\Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $cuenta->id . '%')
                ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                ->whereBetween('ct_asientos_detalle.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
                ->where('c.id_empresa', $id_empresa)
                ->get();

            // dd($registrols);

            $n     = $this->cab_detalle($sheet, $n);
            $saldo = 0;
            foreach ($registros as $value) {
                $sheet->cell("A$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->fecha);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // $sheet->mergeCells('B5:E5');
                $sheet->cell("B$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->cabecera->id);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $sheet->cell("C$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->id_plan_cuenta);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell("D$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->descripcion);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell("E$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->debe);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell("F$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->haber);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $saldo = ($saldo + $value->debe) - $value->haber;

                $sheet->cell("G$n", function ($cell) use ($saldo) {
                    // manipulate the cel
                    $cell->setValue($saldo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $n++;
            }

        }
    }

    public function buscador_secuencia(Request $request)
    {
        //dd($request['nombre']);
        if (($request['nombre']) != "") {

            $id_empresa = $request->session()->get('id_empresa');
            $registros  = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa)->where('fact_numero', $request['nombre'])->get();
            //dd($registros);
            return view('contable/diario/resultados_tabla', ['registros' => $registros]);
        }

        return 'no datos';

    }
    public function buscar_proveedor(Request $request)
    {
        if (!is_null($request['id_proveedor'])) {
            $proveedor  = $request['id_proveedor'];
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id', 'c.id_ct_compras')
                //->join('proveedor as p', 'p.id', 'co.proveedor')
                ->join('ct_acreedores as p', 'p.id', 'co.proveedor')
                ->where('a.id_empresa', $id_empresa)
                ->where('p.id_empresa', $id_empresa)
                ->where('p.nombrecomercial', $proveedor)
                ->select('c.*')->orderby('id', 'desc')
                ->get();
            if ($registros != '[]') {
                return view('contable/diario/resultados_tabla', ['registros' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        } elseif (isset($request['concepto'])) {
            $concepto   = '%' . $request['concepto'] . '%';
            $id_empresa = $request->session()->get('id_empresa');
            $registros  = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa)
                ->where('observacion', 'like', $concepto)->orderby('id', 'desc')
                ->get();
            if ($registros != '[]') {
                return view('contable/diario/resultados_tabla', ['registros' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        }
        return ['value' => 'no resultados'];

    }

    public function buscador_fecha(Request $request)
    {
        $fecha       = $request['fechaini'];
        $fecha_hasta = $request['fecha_hasta'];
        if ($fecha != "" && $fecha_hasta != "") {
            $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(20);
            return view('contable/diario/resultados_tabla', ['registros' => $registros]);
        }
        return 'no data';

    }

    public function buscar_cuenta_diario(Request $request)
    {
        $cuenta = plan_cuentas::find($request['nombre']);
        return view('contable/libro_mayor/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
    }

    public function anular_asiento($id)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $asiento         = Ct_Asientos_Cabecera::findorfail($id);
        $asiento->estado = 1;
        $asiento->id_usuariomod = $idusuario;
        $asiento->save();
        $detalles = $asiento->detalles;
        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => 'ANULACION ' . $asiento->observacion,
            'fecha_asiento'   => $asiento->fecha_asiento,
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
        return "ok";
    }

}
