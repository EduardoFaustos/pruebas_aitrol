<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\UsuarioEspecial;

class EstadoResultadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22,26)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $estadopyg  = array();
        $ingresos   = array();
        $costos     = array();
        $gastos     = array();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];

        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }
        $tipo_dato = 0;
        if (isset($request['tipo'])) {
            $id_auth  = Auth::user()->id;
            $especial = UsuarioEspecial::where('id_usuario', $id_auth)->where('id_empresa', $id_empresa)->first();
            if (!is_null($especial)) {
                if ($request['tipo'] == 1) {
                    $tipo_dato = 1;
                }
            }

        }

        // $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(20);

        return view('contable/estado_resultados/index', ['empresa' => $empresa, 'estadopyg' => $estadopyg, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'ingresos' => $ingresos, 'costos' => $costos, 'gastos' => $gastos, 'tipo_dato' => $tipo_dato]);

    }

    public function show(Request $request)
    {
        //$estadoresultado = new EstadoResultado();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        if (!isset($request['imprimir'])) {
            //dd($request);
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
                $tipo_dato   = 0;

                $rfecha_desde           = $request['fecha_desde'];
                $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
                $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
                $fecha_desde            = date('Y-m-d', $timestamp);

                $rfecha_hasta           = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta            = date('Y-m-d', $timestamp);

            } elseif (is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde  = '2010-01-01';
                $rfecha_desde = '01/01/2010';
                $tipo_dato    = 1;

                $fecha_hasta = $request['fecha_hasta'];

                $rfecha_hasta           = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta            = date('Y-m-d', $timestamp);

            } else {
                $fecha_desde  = date('Y-m-d');
                $rfecha_desde = date('d/m/Y');
                $fecha_hasta  = date('Y-m-d');
                $rfecha_hasta = date('d/m/Y');
                $tipo_dato    = 0;
            }
            $cuentas_detalle = "";
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1;
            }
            $mostrar_detalle = "";
            if (isset($request['mostrar_detalle'])) {
                $mostrar_detalle = $request['mostrar_detalle'];
            }
            $mostrar_cierre = 0;
            if (isset($request['mostrar_cierre'])) {
                $mostrar_cierre = $request['mostrar_cierre'];
            }
            $mostrar_acumulado = 0;
            if (isset($request['mostrar_acumulado'])) {
                $mostrar_acumulado = $request['mostrar_acumulado'];
            }

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, $mostrar_cierre, $mostrar_acumulado);
            $toting   = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I', null, 0, $mostrar_cierre, $mostrar_acumulado);

            $costos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle, 0, $mostrar_cierre, $mostrar_acumulado);
            $totcos = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C', null, 0, $mostrar_cierre, $mostrar_acumulado);
            //dd($costos);

            $gastos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle, 0, $mostrar_cierre, $mostrar_acumulado);
            $totgas = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G', null, 0, $mostrar_cierre, $mostrar_acumulado);

            $totpyg       = EstadoResultado::detalle_total_pyg($fecha_desde, $fecha_hasta, null, $mostrar_cierre, $mostrar_acumulado);
            $trabajadores = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, null, array(), $mostrar_cierre, $mostrar_acumulado);
            /*if ('0922729587' == Auth::user()->id) {
            dd($trabajadores);
            }*/
            $total          = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, null, null, $mostrar_cierre, $mostrar_acumulado);
            $total_gravable = EstadoResultado::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre, $mostrar_acumulado);

            //dd($total_gravable);
            $renta_acumulada = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre, $mostrar_acumulado);
            //dd($renta_acumulada);
            $links      = array();
            $fechadesde = $fecha_desde . " " . "00:00:00";
            $fechahasta = $fecha_hasta . " " . "00:00:00";
            //dd($fecha_desde);
            /*foreach ($costos as $costo) {
                

                if(Auth::user()->id == "0922729587" and ($costo['cuenta'] == '1427' or $costo['cuenta'] == '5.2.03.02.04')){
                    //dd($busqueda);
                }

                if (count($busqueda) > 0) {
                    array_push($links, $busqueda[0]->plan);
                    //echo("--".$busqueda[0]->id_plan_cuenta);
                }
            }*/


            // dd($links);
            /*for($li = 0 ; $li < count($links); $li++){
            echo ($links[$li]);
            }*/

            // dd($links);

            return view('contable/estado_resultados/index', ['fecha_desde' => $rfecha_desde, 'fecha_hasta' => $rfecha_hasta, 'empresa' => $empresa, 'ingresos' => $ingresos, 'totpyg' => $totpyg, 'costos' => $costos, 'gastos' => $gastos, 'cuentas_detalle' => $cuentas_detalle, 'mostrar_detalle' => $mostrar_detalle, 'mostrar_cierre' => $mostrar_cierre, 'mostrar_acumulado' => $mostrar_acumulado, 'trabajadores' => $trabajadores, 'total' => $total, 'renta_acumulada' => $renta_acumulada, 'tipo_dato' => $tipo_dato, 'total_gravable' => $total_gravable, 'links' => $links, 'r_espe' => 0, 'id_empresa' => $id_empresa]);
        } else {

            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];

                $rfecha_desde              = $request['filfecha_desde'];
                $request['filfecha_desde'] = str_replace('/', '-', $request['filfecha_desde']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_desde'])->timestamp;
                $fecha_desde               = date('Y-m-d', $timestamp);

                $rfecha_hasta              = $request['filfecha_hasta'];
                $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
                $timestamp                 = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
                $fecha_hasta               = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde  = date('Y-m-d');
                $rfecha_desde = date('d/m/Y');
                $fecha_hasta  = date('Y-m-d');
                $rfecha_hasta = date('d/m/Y');
            }
            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle'];
            }
            $mostrar_detalle = "";
            if (isset($request['filmostrar_detalle'])) {
                $mostrar_detalle = 1;
            }
            $mostrar_cierre = 0;
            if (isset($request['mostrar_cierre'])) {
                $mostrar_cierre = $request['mostrar_cierre'];
            }

            // $estadopyg = array();
            // $estadopyg = $this->detalle_estadopg($fecha_desde, $fecha_hasta, $tipo);

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, $mostrar_cierre);
            $toting   = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I', null, 0, $mostrar_cierre);
            $id_auth  = Auth::user()->id;
            /*if ($id_auth == '0922729587') {
            dd($ingresos);
            }*/

            $costos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle, 0, $mostrar_cierre);
            $totcos = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C', null, 0, $mostrar_cierre);
            //dd($costos);

            $gastos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle, 0, $mostrar_cierre);
            $totgas = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G', null, 0, $mostrar_cierre);

            $totpyg       = EstadoResultado::detalle_total_pyg($fecha_desde, $fecha_hasta, null, $mostrar_cierre);
            $trabajadores = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, null, array(), $mostrar_cierre);

            $participacion   = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, null, array(), $mostrar_cierre);
            $totalf          = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, null, null, $mostrar_cierre);
            $renta_acumulada = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre);

            $total_gravable = EstadoResultado::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre);

            $total = $totalf - $renta_acumulada;

            $periodo_desde = "";
            $periodo_hasta = "";
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            if ($request['exportar'] == 0) {
                $vistaurl = "contable/estado_resultados/print";
                $view     = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg', 'participacion', 'totalf', 'renta_acumulada', 'total_gravable'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('EstadoResultado-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {
                Excel::create('EstadoSituacionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg, $participacion, $totalf, $renta_acumulada, $total, $total_gravable) {
                    $excel->sheet('EstadoSituacionFinanciera', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg, $participacion, $totalf, $renta_acumulada, $total, $total_gravable) {
                        $sheet->mergeCells('A1:F1');
                        $sheet->cell('A1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:F2');
                        $sheet->cell('A2', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("ESTADO DE RESULTADO INTEGRAL");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function ($cell) use ($periodo_hasta) {
                            // manipulate the cel
                            $cell->setValue("del 01 de enero al $periodo_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('B4:E4');
                        $sheet->cell('B4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->cell('F4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SALDO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        // DETALLES

                        $sheet->setColumnFormat(array(
                            'F' => '0.00',
                        ));

                        $i = $this->setDetalles($ingresos, $sheet, 5);
                        $i = $this->setDetalles($costos, $sheet, $i);
                        $i = $this->setDetalles($gastos, $sheet, $i);
                        // $i = $this->setDetalles($pasivos, $sheet, $i);
                        // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" Utilidad / Perdida del Periodo ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $i . ':E' . $i);

                        $sheet->cell('F' . $i, function ($cell) use ($totpyg) {
                            $cell->setValue(number_format($totpyg, 2));
                            if ($totpyg < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $par  = $i + 1;
                        $tf   = $i + 2;
                        $tfg  = $i + 3;
                        $ra   = $i + 4;
                        $totf = $i + 5;
                        $sheet->cell('A' . $par, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" 15% PARTICIPACION A TRABAJADORES: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $par . ':E' . $par);

                        $sheet->cell('F' . $par, function ($cell) use ($participacion) {
                            $cell->setValue(number_format($participacion, 2));
                            if ($participacion < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $tf, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" UTILIDAD CONTABLE: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $tf . ':E' . $tf);

                        $sheet->cell('F' . $tf, function ($cell) use ($totalf) {
                            $cell->setValue(number_format($totalf, 2));
                            if ($totalf < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $tfg, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" UTILIDAD GRAVABLE: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $tfg . ':E' . $tfg);

                        $sheet->cell('F' . $tfg, function ($cell) use ($total_gravable) {
                            $cell->setValue(number_format($total_gravable, 2));
                            if ($total_gravable < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $ra, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" IMPUESTO GENERADO: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $ra . ':E' . $ra);

                        $sheet->cell('F' . $ra, function ($cell) use ($renta_acumulada) {
                            $cell->setValue(number_format($renta_acumulada, 2));
                            if ($renta_acumulada < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $totf, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" UTILIDAD GRAVABLE: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $totf . ':E' . $totf);

                        $sheet->cell('F' . $totf, function ($cell) use ($total) {
                            $cell->setValue(number_format($total, 2));
                            if ($total < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        //  CONFIGURACION FINAL
                        $sheet->cells('A2:F2', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A4:F4', function ($cells) {
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
                        ));

                    });
                })->export('xlsx');
            }
        }
    }

    public function setDetalles($data, $sheet, $i, $totpyg = "")
    {
        foreach ($data as $value) {
            $cont = substr_count($value['cuenta'], ".");
            if ($cont == 0) {$valor100 = $value['saldo'];}
            if ($value['saldo'] != 0) {
                $sheet->cell('A' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $cell->setValue(" " . $value['cuenta'] . " ");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell);
                });

                $sheet->mergeCells('B' . $i . ':E' . $i);
                $sheet->cell('B' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $cell->setValue($value['nombre']);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell, 1);
                });

                $sheet->cell('F' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $this->setSangria($cont, $cell);
                    $cell->setValue(number_format($value['saldo'], 2));
                    if ($value['saldo'] < 0) {
                        $cell->setFontColor('#ff0000');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;
            }

        }
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }

    public function setSangria($cont, $cell, $indent = "")
    {
        switch ($cont) {
            case 0:
                $cell->setFontSize('12');
                $cell->setFontWeight('bold');
                break;
            case 1:
                $cell->setFontSize('11');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(2);
                }
                break;
            case 2:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(4);
                }
                break;
            case 3:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(6);
                }
                break;
            case 4:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(8);
                }
                break;
            default:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(10);
                }
                break;
        }
    }

    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Asientos_Cabecera::findorfail($id);
        return view('contable/diario/asiento', ['registro' => $registro]);
    }

    public function crear()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cuentas = plan_cuentas::where('estado', '2')->get();
        return view('contable/diario/create', ['cuentas' => $cuentas]);
    }

    public function buscar(Request $request)
    {
        $cuenta = plan_cuentas::find($request['nombre']);
        return view('contable/diario/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
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
                'fecha'               => $request['fecha_asiento'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        return redirect()->route('librodiario.index');

    }

    public function fechaTexto($fecha)
    {
        $fecha     = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia       = date('l', strtotime($fecha));
        $mes       = date('F', strtotime($fecha));
        $anio      = date('Y', strtotime($fecha));
        $dias_EN   = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $dias_ES   = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES  = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN  = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        // return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
        return $numeroDia . " de " . $nombreMes . " de " . $anio;
    }
}
