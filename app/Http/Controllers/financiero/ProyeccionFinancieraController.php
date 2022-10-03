<?php

namespace Sis_medico\Http\Controllers\financiero;

use Excel;
use Illuminate\Http\Request;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\ProyeccionFinanciera;

class ProyeccionFinancieraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $balance    = array();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
        }
        return view('financiero/estado_esi', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'balance' => $balance]);
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
    public function proyeccionfinanciera(Request $request)
    {
        //$estadoresultado = new EstadoResultado();
        //dd('hola');
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1;
            }
            $mostrar_detalle = "";
            if (isset($request['mostrar_detalle'])) {
                $mostrar_detalle = $request['mostrar_detalle'];
            }
            $date = date_create(str_replace("/", "-", $request['fecha_hasta'] . '-01'));
            $date = date_format($date, "Y");
            //dd($gastos);

            $fechagrupo = array();
            for ($i = $fecha_desde; $i <= $date; $i++) {
                array_push($fechagrupo, (int) $i);
            }

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            //dd($ingresos);
            $liquidez     = ProyeccionFinanciera::detalle_principal($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $solvencia    = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $gestion      = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $rentabilidad = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);

            $costos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle);
            $totcos = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C');

            $gastos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle);
            $totgas = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G');

            $totpyg = ProyeccionFinanciera::detalle_total_pyg($fecha_desde, $fecha_hasta);

            //dd($liquidez);

            return view('financiero/proyeccionfinanciera', ['fechagrupo' => $fechagrupo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa'  => $empresa, 'ingresos' => $ingresos
                , 'totpyg' => $totpyg, 'costos' => $costos, 'gastos' => $gastos, 'cuentas_detalle' => $cuentas_detalle, 'mostrar_detalle' => $mostrar_detalle,
                'liquidez'                                                   => $liquidez, 'solvencia'     => $solvencia, 'gestion'       => $gestion, 'rentabilidad' => $rentabilidad, 'i'   => $i]);
        } else {
            if (!is_null($request['fecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle'];
            }
            $mostrar_detalle = "";
            if (isset($request['filmostrar_detalle'])) {
                $mostrar_detalle = 1;
            }

            // $estadopyg = array();
            // $estadopyg = $this->detalle_estadopg($fecha_desde, $fecha_hasta, $tipo);

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $toting   = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I');

            $costos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle);
            $totcos = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C');

            $gastos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle);
            $totgas = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G');

            $totpyg = ProyeccionFinanciera::detalle_total_pyg($fecha_desde, $fecha_hasta);

            $periodo_desde = "";
            $periodo_hasta = "";
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            if ($request['exportar'] == 0) {
                $vistaurl = "contable/estado_resultados/print";
                $view     = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg', 'cuenta'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('ProyeccionFinanciera-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {
                Excel::create('ProyeccionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                    $excel->sheet('ProyeccionFinanciera', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
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

                        //$i = $this->setDetalles($ingresos, $sheet, 5);
                        //$i = $this->setDetalles($costos, $sheet, $i);
                        //$i = $this->setDetalles($gastos, $sheet, $i);
                        // $i = $this->setDetalles($pasivos, $sheet, $i);
                        // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

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

    public function resultado(Request $request)
    {
        //$estadoresultado = new EstadoResultado();
        //dd('hola');
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1;
            }
            $mostrar_detalle = "";
            if (isset($request['mostrar_detalle'])) {
                $mostrar_detalle = $request['mostrar_detalle'];
            }
            $date = date_create(str_replace("/", "-", $request['fecha_hasta'] . '-01'));
            $date = date_format($date, "Y");
            //dd($gastos);

            $fechagrupo = array();
            for ($i = $fecha_desde; $i <= $date; $i++) {
                array_push($fechagrupo, (int) $i);
            }

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos     = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $liquidez     = ProyeccionFinanciera::detalle_principal($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $solvencia    = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $gestion      = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $rentabilidad = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);

            $costos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle);
            $totcos = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C');

            $gastos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle);
            $totgas = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G');

            $totpyg = ProyeccionFinanciera::detalle_total_pyg($fecha_desde, $fecha_hasta);

            //dd($liquidez);

            return view('financiero/proyeccionfinanciera', ['fechagrupo' => $fechagrupo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'empresa'  => $empresa, 'ingresos' => $ingresos
                , 'totpyg' => $totpyg, 'costos' => $costos, 'gastos' => $gastos, 'cuentas_detalle' => $cuentas_detalle, 'mostrar_detalle' => $mostrar_detalle,
                'liquidez'                                                   => $liquidez, 'solvencia'     => $solvencia, 'gestion'       => $gestion, 'rentabilidad' => $rentabilidad]);
        } else {
            if (!is_null($request['fecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y-m');
            }
            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle'];
            }
            $mostrar_detalle = "";
            if (isset($request['filmostrar_detalle'])) {
                $mostrar_detalle = 1;
            }

            // $estadopyg = array();
            // $estadopyg = $this->detalle_estadopg($fecha_desde, $fecha_hasta, $tipo);

            $ingresos = array();
            $gresos   = array();
            $gastos   = array();

            $ingresos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle);
            $toting   = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I');

            $costos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle);
            $totcos = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C');

            $gastos = ProyeccionFinanciera::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle);
            $totgas = ProyeccionFinanciera::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G');

            $totpyg = ProyeccionFinanciera::detalle_total_pyg($fecha_desde, $fecha_hasta);

            $periodo_desde = "";
            $periodo_hasta = "";
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            if ($request['exportar'] == 0) {
                $vistaurl = "contable/estado_resultados/print";
                $view     = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg', 'cuenta'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('EstadoResultado-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {

                Excel::create('ProyeccionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                    $excel->sheet('ProyeccionFinanciera', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg) {
                        $sheet->mergeCells('A1:T1');
                        $sheet->cell('A1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');

                        });
                        $sheet->mergeCells('A2:T2');
                        $sheet->cell('A2', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("PROYECCION FINANCIERA");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');

                        });
                        $sheet->mergeCells('A3:T3');
                        $sheet->cell('A3', function ($cell) use ($periodo_hasta) {
                            // manipulate the cel
                            $cell->setValue("del 01 de enero al $periodo_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');

                        });
                        //PRIMERA PARTE
                        //$sheet->mergeCells('A6:6');
                        $sheet->mergeCells('A6:B6');
                        $sheet->cell('A6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("Periodo");
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('C6:D6');
                        $sheet->cell('C6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Ingresos');
                            $cell->setAlignment('center');

                        });
                        $sheet->mergeCells('E6:F6');
                        $sheet->cell('E6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Egresos');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('G6:H6');
                        $sheet->cell('G6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Var. Ingresos');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('I6:J6');
                        $sheet->cell('I6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Var. Egresos');
                            $cell->setAlignment('center');
                        });

                        // SEGUNDA PARTE///////////////////
                        $sheet->mergeCells('M6:N6');
                        $sheet->cell('M6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Periodo');
                            $cell->setAlignment('center');

                        });
                        // $sheet->mergeCells('B4:E4');
                        $sheet->mergeCells('O6:P6');
                        $sheet->cell('O6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Ingresos');
                            $cell->setAlignment('center');

                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->mergeCells('Q6:R6');
                        $sheet->cell('Q6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XY');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('S6:T6');
                        $sheet->cell('S6', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X2');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('A17:D17');
                        $sheet->cell('A17', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("Resultados");
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('F17:J17');
                        $sheet->cell('F17', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("Resultados");
                            $cell->setAlignment('center');
                        });

                        $sheet->mergeCells('A22:J22');
                        $sheet->cell('A22', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("AJUSTES");
                            $cell->setAlignment('center');
                        });
                        $sheet->cells('A22:J22', function ($cells) {
                            // manipulate the range of cells
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('', '', 'thin', '');
                            $cells->setValignment('center');
                        });
                        $sheet->cell('A23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Periodo');
                            $cell->setAlignment('center');

                        });
                        // $sheet->mergeCells('B4:E4');
                        $sheet->mergeCells('B23:C23');
                        $sheet->cell('B23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Ingresos');
                            $cell->setAlignment('center');
                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->mergeCells('D23:E23');
                        $sheet->cell('D23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('%');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('F23:G23');
                        $sheet->cell('F23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Regresion');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('H23:I23');
                        $sheet->cell('H23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Egresos');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('J23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Error');
                            $cell->setAlignment('center');
                        });
                        ///////////////////////////////////////////////////
                        $sheet->mergeCells('M22:T22');
                        $sheet->cell('M22', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("PRONOSTICO");
                            $cell->setAlignment('center');

                        });
                        $sheet->cells('M22:T22', function ($cells) {
                            // manipulate the range of cells
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('', '', 'thin', '');
                            $cells->setValignment('center');
                        });
                        // $sheet->mergeCells('B4:E4');
                        $sheet->mergeCells('M23:N23');
                        $sheet->cell('M23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Periodo');
                            $cell->setAlignment('center');
                        });
                        // $sheet->mergeCells('B4:E4');
                        $sheet->mergeCells('O23:P23');
                        $sheet->cell('O23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Ingresos');
                            $cell->setAlignment('center');
                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->mergeCells('Q23:R23');
                        $sheet->cell('Q23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Egresos');
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('S23:T23');
                        $sheet->cell('S23', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('Ajustes');
                            $cell->setAlignment('center');
                        });

                        //  CONFIGURACION FINAL
                        $sheet->cells('A2:J2', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('A22:J22', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('A23:J23', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('M22:T22', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('M23:T23', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A6:J6', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('M6:T6', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('A17:D17', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                            $cells->setValignment('center');
                        });
                        $sheet->cells('F17:J17', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                            $cells->setValignment('center');
                        });

                        $sheet->setColumnFormat(array(

                            'A' => '0.00', 'B' => '0.00',
                            'C' => '0.00', 'D' => '0.00',
                            'E' => '0.00', 'F' => '0.00',
                            'G' => '0.00', 'H' => '0.00',
                            'I' => '0.00', 'J' => '0.00',
                            'K' => '0.00', 'L' => '0.00',
                            'M' => '0.00', 'N' => '0.00',
                            'O' => '0.00', 'P' => '0.00',
                            'Q' => '0.00', 'R' => '0.00',
                            'S' => '0.00', 'T' => '0.00',

                        ));

                        $sheet->setWidth(array(
                            'A' => 12,
                            'B' => 12,
                            'C' => 12,
                            'D' => 12,
                            'E' => 12,
                            'F' => 12,
                        ));
                        $i = $this->setDetalles($ingresos, $sheet, 5);
                        $i = $this->setDetalles($costos, $sheet, $i);
                        $i = $this->setDetalles($gastos, $sheet, $i);

                        $sheet->mergeCells('A' . $i . ':B' . $i);

                        $sheet->cell('A' . $i, function ($cell) use ($totpyg) {
                            $cell->setValue(number_format($totpyg, 2));
                            if ($totpyg < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        //$i=6
                        //dd($ingresos);

                        /*$sheet->cell('A7', function($cell) use($ingresos){
                    // manipulate the cel
                    $cell->setValue($ingresos);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('B7', function($cell) use($costos){
                    // manipulate the cel
                    $cell->setValue($costos);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('C7', function($cell) use($gastos){
                    // manipulate the cel
                    $cell->setValue($gastos);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('D7', function($cell) use($vingresos){
                    // manipulate the cel
                    $cell->setValue($vingresos);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('E7', function($cell) use($vegresos){
                    // manipulate the cel
                    $cell->setValue($vegresos);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });*/

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

}
