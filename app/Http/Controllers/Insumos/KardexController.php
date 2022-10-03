<?php

namespace Sis_medico\Http\Controllers\Insumos;


use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Bodega;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvKardex;
use Sis_medico\Producto;
use Sis_medico\Ct_ventas;

class KardexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 7)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y-m-d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }

        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        // dd($fecha_desde);
        $productos = Producto::orderby('id', 'desc')->get();
        $bodegas   = Bodega::where('estado', 1)->get();
        return view('insumos/kardex/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto,
            'bodegas'   => $bodegas, 'id_bodega' => $id_bodega,
        ]);
    }

    public function show(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = Session::get('id_empresa');
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $rfecha_desde           = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $codigo_producto = 0;
        if (isset($request['codigo_producto'])) {
            $codigo_producto = $request['codigo_producto'];
        }
        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }

        #   KARDEX ANTERIOR    suma #
        $getAnterior = InvKardex::where('inv_kardex.estado', '1');
        if (isset($request['codigo_producto']) and $request['codigo_producto'] != "") {
            $prod = Producto::where('codigo', $request['codigo_producto'])->first();
            if (isset($prod->id)) {
                $getAnterior = $getAnterior->where('id_producto', $prod->id);
            }
        }
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $getAnterior = $getAnterior->where('inv_kardex.id_producto', $request['id_producto']);
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {

            $getAnterior = $getAnterior->where('inv_kardex.id_bodega', $request['id_bodega']);
        }
        $getAnterior = $getAnterior->where('inv_kardex.fecha', '<', $fecha_desde)
            ->where('inv_kardex.id_empresa', $id_empresa)
            ->where('inv_kardex.tipo', 'I')
            ->where('inv_kardex.estado', '1')
            ->leftJoin('inv_det_movimientos', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos')
            ->orderBy('inv_kardex.fecha', 'ASC')
            ->orderBy('inv_kardex.id', 'ASC')
            ->orderBy('inv_kardex.tipo', 'I')
            ->select(DB::raw('SUM(inv_kardex.cantidad) as cantidad'), DB::raw('inv_kardex.valor_unitario'), DB::raw('SUM(inv_kardex.total) as total'), 'fecha', DB::raw('SUM(inv_det_movimientos.iva) as iva'))->first();
        #   KARDEX ANTERIOR    resta #
        $getAnterior_2 = InvKardex::where('inv_kardex.estado', '1');
        if (isset($request['codigo_producto']) and $request['codigo_producto'] != "") {
            $prod = Producto::where('codigo', $request['codigo_producto'])->first();
            if (isset($prod->id)) {
                $getAnterior_2 = $getAnterior_2->where('id_producto', $prod->id);
            }
        }
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $getAnterior_2 = $getAnterior_2->where('inv_kardex.id_producto', $request['id_producto']);
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {

            $getAnterior_2 = $getAnterior_2->where('inv_kardex.id_bodega', $request['id_bodega']);
        }
        $getAnterior_2 = $getAnterior_2->where('inv_kardex.fecha', '<', $fecha_desde)
            ->where('inv_kardex.id_empresa', $id_empresa)
            ->where('inv_kardex.tipo', '<>', 'I')
            ->leftJoin('inv_det_movimientos', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos')
            ->orderBy('inv_kardex.fecha', 'ASC')
            ->where('inv_det_movimientos.estado', '1')
            ->orderBy('inv_kardex.id', 'ASC')
            ->select(DB::raw('SUM(inv_kardex.cantidad) as cantidad'), DB::raw('inv_kardex.valor_unitario'), DB::raw('SUM(inv_kardex.total) as total'), 'fecha', DB::raw('SUM(inv_det_movimientos.iva) as iva'))->first();
        //dd($getAnterior);

        $kardex = array();
        $kardex = InvKardex::where('estado', '!=', '2')
            ->where('inv_kardex.id_empresa', $id_empresa)
            ->where('inv_kardex.estado', '1')
            ->where('fecha', '>=', $fecha_desde . " 00:00:00")
            ->where('fecha', '<=', $fecha_hasta . " 23:59:59");
        //->where('tipo', '!=', 'T');
        if (isset($request['codigo_producto']) and $request['codigo_producto'] != "") {
            $prod = Producto::where('codigo', $request['codigo_producto'])->first();
            if (isset($prod->id)) {
                $kardex = $kardex->where('id_producto', $prod->id);
            }
        }
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $kardex = $kardex->where('id_producto', $request['id_producto']);
        }
        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {
            $kardex = $kardex->where('id_bodega', $request['id_bodega']);
        }
        $kardex = $kardex->orderBy('fecha', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        //dd($kardex);

        $id_empresa = Session::get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos  = Producto::orderby('id', 'desc')->get();
        $bodegas    = Bodega::all();
        return view('insumos/kardex/table', ['productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'getAnterior' => $getAnterior, 'fecha_hasta' => $fecha_hasta, 'kardex' => $kardex, 'id_producto' => $id_producto, 'detalles' => "", 'bodegas' => $bodegas, 'id_bodega' => $id_bodega, 'getAnterior_2' => $getAnterior_2]);

    }
    public function exportar(Request $request)
    {
        //dd($request->all());
        // dd("Exportar");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
            $rfecha_desde              = $request['filfecha_desde'];
            $request['filfecha_desde'] = str_replace('/', '-', $request['filfecha_desde']);
            $timestamp                 = \Carbon\Carbon::parse($request['filfecha_desde'])->timestamp;
            $fecha_desde               = date('Y-m-d', $timestamp);

            $rfecha_hasta              = $request['filfecha_hasta'];
            $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
            $timestamp                 = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
            $fecha_hasta               = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $kardex   = '[]';
        $producto = "[]";

        if (isset($request['filid_producto']) and $request['filid_producto'] != "") {
            $kardex = InvKardex::where('id_producto', $request['filid_producto'])
                ->where('tipo', 'NOT LIKE', '%ANU%')
                ->whereBetween('fecha', [$fecha_desde . " 00:00:00", $fecha_hasta . " 23:59:59"])
                ->orderBy('fecha', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();
            $producto = Producto::where('id', $request['filid_producto'])->first();

            // dd($fecha_desde);
            $id_empresa  = Session::get('id_empresa');
            $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $dateFin     = "2020/12/31";
            $dateIni     = "2020/12/31";
            $getAnterior = InvKardex::where('id_producto', $request['filid_producto'])
                ->where('id_empresa', $id_empresa)
                ->where('tipo', 'I')
                ->leftJoin('inv_det_movimientos', 'inv_det_movimientos.id', '=', 'inv_kardex.id_inv_det_movimientos' )
                ->whereBetween('fecha', [$dateIni . " 00:00:00", $dateFin . " 23:59:59"])
                ->orderBy('fecha', 'ASC')
                ->orderBy('id', 'ASC')
                ->select(DB::raw('SUM(cantidad) as cantidad'), DB::raw('SUM(valor_unitario) as valor_unitario'), DB::raw('SUM(total) as total'), 'fecha', DB::raw('SUM(inv_det_movimientos.iva) as iva'))->first();
            //dd($getAnterior);
            $periodo_desde = $this->fechaTexto($fecha_desde);
            $periodo_hasta = $this->fechaTexto($fecha_hasta);

            //  DOCUMENTACION
            // https://docs.laravel-excel.com/2.1/export/cells.html
            Excel::create('Kardex-' . $producto->nombre . ' ' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $getAnterior, $periodo_desde, $periodo_hasta, $producto, $kardex) {
                $excel->sheet('Kardex', function ($sheet) use ($empresa, $periodo_desde, $periodo_hasta, $producto, $kardex, $getAnterior) {
                    $sheet->mergeCells('A1:L1');
                    $sheet->cell('A1', function ($cell) use ($empresa) {
                        $cell->setValue($empresa->nombrecomercial);
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', '', 'thin');
                    });
                    $sheet->mergeCells('A2:L2');
                    $sheet->cell('A2', function ($cell) {
                        $cell->setValue("KARDEX");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A3:L3');
                    $sheet->cell('A3', function ($cell) use ($periodo_desde, $periodo_hasta) {
                        $cell->setValue("$periodo_desde al $periodo_hasta");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A4:F4');
                    $sheet->cell('A4', function ($cell) {
                        $cell->setValue("PRODUCTO");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G4:L4');
                    $sheet->cell('G4', function ($cell) {
                        $cell->setValue("MÉTODO");
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A5:F5');
                    $sheet->cell('A5', function ($cell) use ($producto) {
                        $cell->setValue($producto->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G5:L5');
                    $sheet->cell('G5', function ($cell) {
                        $cell->setValue('');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A6:A7');
                    $sheet->cell('A6', function ($cell) {
                        $cell->setValue('FECHA');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('B6:C6');
                    $sheet->cell('B6', function ($cell) {
                        $cell->setValue('DETALLE');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D6:F6');
                    $sheet->cell('D6', function ($cell) {
                        $cell->setValue('ENTRADA');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('G6:I6');
                    $sheet->cell('G6', function ($cell) {
                        $cell->setValue('SALIDA');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('J6:L6');
                    $sheet->cell('J6', function ($cell) {
                        $cell->setValue('SALDOS');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('B7:C7');
                    $sheet->cell('B7', function ($cell) {
                        $cell->setValue('CONCEPTO');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //ENTRADA
                    // $sheet->mergeCells('D7:F7');
                    $sheet->cell('D7', function ($cell) {
                        $cell->setValue('CANTIDAD');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E7', function ($cell) {
                        $cell->setValue('VAL. UNIT');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F7', function ($cell) {
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //SALDIDA
                    // $sheet->mergeCells('D7:F7');
                    $sheet->cell('G7', function ($cell) {
                        $cell->setValue('CANTIDAD');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H7', function ($cell) {
                        $cell->setValue('VAL. UNIT');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I7', function ($cell) {
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //SALDO
                    // $sheet->mergeCells('D7:F7');
                    $sheet->cell('J7', function ($cell) {
                        $cell->setValue('CANTIDAD');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('K7', function ($cell) {
                        $cell->setValue('VAL. UNIT');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L7', function ($cell) {
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M7', function ($cell) {
                        $cell->setValue('OBSERVACION');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    // DETALLES

                    $sheet->setColumnFormat(array(
                        'D' => '0.00',
                        'E' => '0.00',
                        'F' => '0.00',
                        'G' => '0.00',
                        'H' => '0.00',
                        'I' => '0.00',
                        'J' => '0.00',
                        'K' => '0.00',
                        'L' => '0.00',
                    ));

                    $i = $this->setDetalles($kardex, $sheet, 8, $getAnterior);
                    // $i = $this->setDetalles($pasivos, $sheet, $i, '',$participacion);
                    // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                    //  CONFIGURACION FINAL
                    $sheet->cells('A2:M2', function ($cells) {
                        // manipulate the range of cells
                        $cells->setBackground('#0070C0');
                        // $cells->setFontSize('10');
                        $cells->setFontWeight('bold');
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        $cells->setValignment('center');
                    });

                    $sheet->cells('A4:M4', function ($cells) {
                        // manipulate the range of cells
                        $cells->setBackground('#cdcdcd');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize('12');
                    });

                    $sheet->setWidth(array(
                        'A' => 12,
                        'B' => 20,
                        'C' => 12,
                        'D' => 12,
                        'E' => 12,
                        'F' => 12,
                        'G' => 12,
                        'H' => 12,
                        'I' => 12,
                        'J' => 12,
                        'K' => 12,
                        'L' => 12,
                    ));
                });
                $excel->getActiveSheet()->getColumnDimension("M")->setWidth(45)->setAutosize(false);
            })->export('xlsx');
        }
    }

    public function setDetalles($kardex, $sheet, $i, $getAnterior)
    {
        $sheet->cell('I' . $i, function ($cell) {
            $cell->setValue('SALDOS ANT:');
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        /*$sheet->cell('G' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue(date('d/m/Y', strtotime($getAnterior->fecha)));
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });*/
        $sheet->cell('J' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue($getAnterior->cantidad);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('K' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue($getAnterior->valor_unitario);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell('L' . $i, function ($cell) use ($getAnterior) {
            $cell->setValue($getAnterior->total);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $cant_ant   = (is_null($getAnterior->cantidad))?$getAnterior->cantidad:0;
        $getPrice    = 0;
        $getPriceant = 0;
        $getCount    = 0;
        $getTotal    = 0;
        $total_ant = 0;
        $vu_ant = 0;
        $cantidadant = $cant_ant;
        $anterior    = $getAnterior->cantidad;
        $total_ant  = $getAnterior->total - $iva_ant;
        if (is_null($total_ant>0)) {
            $vu_ant = $total_ant /$cant_ant;
         }
        if (is_null($anterior)) {
            $anterior = $cant_ant;
        }
        $cantidad       = $anterior;
        $anteriorprecio = $getAnterior->valor_unitario;
        if (is_null($anteriorprecio)) {
            $anteriorprecio = 0;
        }
        $anteriortotal = $getAnterior->total;
        if (is_null($anteriortotal)) {
            $anteriortotal = 0;
        }
        //dd($anteriorprecio);
        //$totalCosto  = $anteriortotal;
        //$precioCosto = $anteriorprecio;
        $totalCosto=$total_ant;
        $precioCosto=$vu_ant;
        $contador    = 0;
        $i++;
        foreach ($kardex as $value) {
            if ($value->documento_bodega->tipo_movimiento->tipo=='I') {

                $cantidad += $value->cantidad;
            } else {

                $cantidad = $cantidad - $value->cantidad;
            }
            $getPrice += $value->valor_unitario;
            $getTotal += $value->total;
            $sheet->cell('A' . $i, function ($cell) use ($value) {
                $cell->setValue(date('d/m/Y', strtotime($value->fecha)));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->mergeCells('B' . $i . ':C' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($value) {
                $cell->setValue($value->descripcion);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $tcosto_ed = 0;
            $tcosto_ed =  $value->valor_unitario*$value->cantidad;
            if ($value->documento_bodega->tipo_movimiento->tipo=='I') {
                $observ = DB::table('ct_compras')->find($value->id_movimiento);
                $totalCosto += $value->total;
                if ($cantidad > 0) {
                    //$precioCosto = $totalCosto / $cantidad;
                    $tunitario = $totalCosto/$cantidad;
                } else {
                    //$precioCosto = 0;
                    $tunitario = 0;
                }
                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    $cell->setValue($value->cantidad);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($value) {
                    $cell->setValue($value->valor_unitario);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($value) {
                    $cell->setValue($value->total);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            } else {
                $observ     = Ct_ventas::find($value->id_movimiento);
                //$totalCosto = $precioCosto * $cantidad;
                //$tots       = $precioCosto * $value->cantidad;
                $tots=$precioCosto*$value->cantidad;
                $sheet->cell('D' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) {
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G' . $i, function ($cell) use ($cantidad) {
                    $cell->setValue($cantidad);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H' . $i, function ($cell) use ($precioCosto) {
                    $cell->setValue($precioCosto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I' . $i, function ($cell) use ($tots) {
                    $cell->setValue($tots);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            }

            $sheet->cell('J' . $i, function ($cell) use ($cantidad) {
                $cell->setValue($cantidad);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('K' . $i, function ($cell) use ($precioCosto) {
                $cell->setValue($precioCosto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('L' . $i, function ($cell) use ($totalCosto) {
                $cell->setValue($totalCosto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $sheet->cell('M' . $i, function ($cell) use ($observ, $value) {
                if ($observ != null) {
                    if ($value->movimiento == 1) {
                        $cell->setValue($observ->observacion);
                    } else {
                        $observs = Movimiento::find($value->id_movimiento);
                        $cell->setValue($observs->nombres_paciente . ' P: ' . $observs->procedimientos);
                    }
                }

                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $i++;
            $observ = "";
            //$cantidad=$value->cantidad;
            if ($value->documento_bodega->tipo_movimiento->tipo=='I') {

                $cantidadant += $value->cantidad;
            } else {

                $cantidadant = $cantidad - $value->cantidad;
            }
            $getTotalant = 0;
            $getPriceant += $value->valor_unitario;
            $getTotalant += $value->total;
            $contador++;
            //dd($getTotal);
        }
        // $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        // $sheet->cells('F5:F' . $i, function ($cells) {
        //     $cells->setAlignment('right');
        // });
        // $sheet->cells('G5:G' . $i, function ($cells) {
        //     $cells->setAlignment('right');
        // });
        return $i;
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
