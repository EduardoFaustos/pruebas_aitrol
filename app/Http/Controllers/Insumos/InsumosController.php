<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Session;

use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\hc_procedimientos;
use Sis_medico\Equipo_Historia;
use Sis_medico\Empresa;
use Sis_medico\Producto;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Marca;
use Excel;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_NumberFormat;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Pedido;

class InsumosController extends Controller
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

    //
    public function descargo_insumos($id_agenda)
    {
        // dd("EPA");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $agenda = Agenda::find($id_agenda);
        $bodegas = Bodega::all();

        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $id_agenda)->get();

        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
        } else {
            $hcid = "";
        }
        $equipos = Equipo_Historia::where('hcid', $hcid)->get();

        return view('enfermeria/insumos', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'bodegas' => $bodegas]);
    }

    /* REPORTE DE EGRESO DE PACIENTES */

    public function egresoprocedimiento(Request $request)
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

        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        // dd($fecha_desde);
        $productos  = Producto::orderby('id', 'desc')->get();
        $bodegas    = Bodega::all();
        $marca      = Marca::where('estado', 1)->get();
        return view('insumos/reporte/egreso_procedimiento/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto,
            'bodegas' => $bodegas, 'id_bodega' => $id_bodega, 'marca' => $marca
        ]);
    }

    public function show_egresoprocedimiento(Request $request)
    {
        //dd($request->all());
        if ($request['excel'] == '1') {
            $this->materiales_utilizados($request->all());
        }

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $marca      = Marca::where('estado', 1)->get();
        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
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
        $movimiento = Movimiento_Paciente::select('movimiento_paciente.*', 'producto.id_marca')
            ->join('movimiento', 'movimiento.id', 'movimiento_paciente.id_movimiento')
            ->join('producto', 'producto.id', 'movimiento.id_producto')
            ->where('movimiento_paciente.created_at', '>=', $fecha_desde . " 00:00:00")
            ->where('movimiento_paciente.created_at', '<=', $fecha_hasta . " 23:59:59")
            ->where('producto.id_marca', $request['marca'])
            ->where('producto.id', $request['producto'])
            ->get();

        $equipo = Equipo_Historia::where('created_at', '>=', $fecha_desde . " 00:00:00")
            ->where('created_at', '<=', $fecha_hasta . " 23:59:59")
            ->get();
        return view('insumos/reporte/egreso_procedimiento/show', [
            'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'movimiento' => $movimiento,
            'detalles' => "", "equipo" => $equipo, 'marca' => $marca
        ]);
    }

    public function materiales_utilizados($valor)
    {
        $marca = $valor['marca'];
        $producto = $valor['producto'];
        $query = DB::table('hc_procedimientos as hp')
            ->join('historiaclinica as hc', 'hp.id_hc', 'hc.hcid')
            ->join('movimiento_paciente as mp', 'hp.id', 'mp.id_hc_procedimientos')
            ->join('movimiento as m', 'mp.id_movimiento', 'm.id')
            ->join('producto as pr', 'm.id_producto', 'pr.id')
            ->join('marca', 'pr.id_marca', 'marca.id')
            ->join('paciente as pac', 'hc.id_paciente', 'pac.id')
            ->join('pedido as ped', 'm.id_pedido', 'ped.id')
            ->join('hc_procedimiento_final as hcf', 'hp.id', 'hcf.id_hc_procedimientos')
            ->join('procedimiento as proc', 'hcf.id_procedimiento', 'proc.id')
            ->join('agenda as a', 'hc.id_agenda', 'a.id')
            ->where('a.fechaini', '>=',  $valor['fecha_desde'])
            ->where('marca.id', $marca)
            ->where('pr.id', $producto)
            ->select(
                'marca.nombre as marcanombre',
                'ped.pedido',
                'a.fechaini',
                'pr.descripcion',
                'hc.id_paciente',
                DB::raw("CONCAT(pac.nombre1,' ',pac.apellido1,' ',pac.apellido2) AS Nombre"),
                'mp.cantidad',
                'proc.nombre',
                'pr.codigo',
                'm.fecha_vencimiento',
                'm.lote',
                'm.serie',
                'ped.tipo',
                'pr.precio_venta'
            )->get();
        $titulos = array("FECHA DE UTILIZACION", "PACIENTE", "MARCA", "DETALLE", "REFERENCIA", "CANTIDAD", "LOTE", "FECHA DE VENCIMIENTO", "NUMERO DE PEDIDO", "SERIE", "FACTURADO /CONSIGNA", "PRECIO");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('Modelo de registro de materiales utilizados', function ($excel) use ($titulos, $posicion, $query) {
            $excel->sheet('Admin.Productos-Transito', function ($sheet) use ($titulos, $posicion, $query) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Modelo de registro de materiales utilizados');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#000000');
                        $cell->setFontColor('#FFFFFF');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $tipo = '';
                $precio = 0;
                foreach ($query as $r) {
                    if ($r->tipo == 1) {
                        $tipo = 'Guia de Remisión';
                    } else if ($r->tipo == 2) {
                        $tipo = 'Factura contra entrega';
                    } else if ($r->tipo == 3) {
                        $tipo = 'Factura';
                    }

                    if (!is_null($r->precio_venta)) {
                        $precio = $r->precio_venta;
                    }
                    $arreglo = [
                        $r->fechaini,
                        $r->Nombre,
                        $r->marcanombre,
                        $r->descripcion,
                        $r->codigo,
                        $r->cantidad,
                        $r->lote,
                        $r->fecha_vencimiento,
                        $r->pedido,
                        $r->serie,
                        $tipo,
                        $precio
                    ];


                    for ($i = 0; $i < count($arreglo); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($arreglo, $i) {
                            $cell->setValue(strval($arreglo[$i]));
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#FC979A');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
            });
            $excel->getActiveSheet()->getStyle("J1:J100")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        })->export('xlsx');
    }

    public static function busquedaData($request)
    {


        $consulta2 = Hc_Procedimientos::join("historiaclinica as hc", "hc.hcid", "hc_procedimientos.id_hc")
            ->join("movimiento_paciente as mp", "hc_procedimientos.id", "mp.id_hc_procedimientos")
            ->join("movimiento as m", "m.id", "mp.id_movimiento")
            ->join("producto as pr", "pr.id", "m.id_producto")
            ->join("paciente as pac", "pac.id", "hc.id_paciente")
            ->join("hc_procedimiento_final as hcf", "hcf.id_hc_procedimientos", "hc_procedimientos.id")
            ->join("procedimiento as proc", "proc.id", "hcf.id_procedimiento")
            ->join("agenda as a", "a.id", "hc.id_agenda")
            ->join("marca as mar", "pr.id_marca", "mar.id");

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

        $movimiento =  $consulta2->where('a.fechaini', '>=', $fecha_desde . " 00:00:00")
            ->where('a.fechaini', '<=', $fecha_hasta . " 23:59:59")
            ->select("a.fechaini", 
                DB::Raw('CONCAT( pac.nombre1," ",pac.apellido1," ",pac.apellido2) as nombrePaciente'), 
                "mar.nombre as nombreMarca", "m.cantidad as cantidadProd",
                "m.serie as serieProd", "m.id_pedido", "m.*")->groupBy('m.serie')->get();
        //     ->where('producto.id_marca', $request['marca'])
        //     ->where('producto.id',$request['producto'])
        //     ->get();
        $equipo =  $consulta2->join('equipo_historia as eqh', 'eqh.hcid', 'hc.hcid')
            ->where('a.fechaini', '>=', $fecha_desde . " 00:00:00")
            ->where('a.fechaini', '<=', $fecha_hasta . " 23:59:59")
            ->select(
                "a.fechaini",
                DB::Raw('CONCAT( pac.nombre1," ",pac.apellido1," ",pac.apellido2) as nombrePaciente'),
                "mar.nombre as nombreMarca",
                DB::Raw('SUM(m.cantidad) as cantidadProd'),
                "m.serie as serieProd",
                "m.id_pedido",
                "m.*",
                "proc.nombre as nombreProcedimiento",
                'pr.descripcion as productoDescripcion',
                'pr.codigo as codigoProducto'
            )->groupBy('m.serie')->get();


        return ["movimiento" => $movimiento, "equipo" => $equipo];
    }


    public function excelMaterialesUtilizados(Request $request)
    {
        //  /  dd($request->all());
        Excel::create('Modelo de registro de materiales utilizados', function ($excel) use ($request) {
            $excel->sheet('Materiales Utilizados', function ($sheet) use ($request) {
                $data["data"] = ["FECHA DE UTILIZACION", "PACIENTE", "MARCA", "DETALLE", "REFERENCIA", "CANTIDAD", "LOTE",    "FECHA DE VENCIMIENTO", "NUMERO DE PEDIDO", "SERIE", "FACTURADO /CONSIGNA", "PRECIO"];
            //    / dd(count($data["data"]));
                $data["background-color"] = "#000000";
                $data["color"] = "#ffffff";
                excelCreate::details($sheet, $data);

                $busqueda = InsumosController::busquedaData($request);
                $movimiento = $busqueda["movimiento"];

                if (count($movimiento) > 0) {
                    $movimientos["comienzo"] = 2;
                    foreach ($movimiento as $value) {
                        $pedido = Pedido::find($value->id_pedido);
                        //$product = Producto::find($value->id_producto);
                        $movimientos["data"] = [
                            date('d/m/Y H:i', strtotime($value->fechaini)), $value->nombrePaciente,
                            $value->nombreMarca, "Detalle", "Referencia", $value->cantidadProd, "Lote", "Fecha Vence", !is_null($pedido) ? $pedido->pedido: '',
                            $value->serieProd."-", "Factura/consigna", $value->precio 

                        ];
                        excelCreate::details($sheet, $movimientos);
                        $movimientos["comienzo"]++;
                    }
                }

                $equipo = $busqueda["equipo"];

                $mergue["comienzo"] = $movimientos["comienzo"];
                $mergue["data"] = ["EQUIPOS"];
                $mergue["background-color"] = "#6FB539";
                $mergue["color"] = "#010101";
                $mergue["mergue"] = 1;
                $mergue["columna"] = "A{$mergue['comienzo']}:L{$mergue['comienzo']}";
                //dd($mergue);
                excelCreate::details($sheet, $mergue);
                $mergue["comienzo"] ++;
                // "A:{$comienzo}:L:{$comienzo}"

                $equipos["comienzo"] =  $mergue["comienzo"];
                if (count($equipo) > 0) {
                    foreach ($equipo as $value) {
                        $pedido = Pedido::find($value->id_pedido);
                        $equipos["data"] =  [
                            date('d/m/Y H:i', strtotime($value->fechaini)),
                            $value->nombrePaciente,
                            $value->nombreMarca,
                            $value->productoDescripcion,
                            $value->codigoProducto,
                            $value->cantidadProd,
                            $value->lote,
                            date('d/m/Y H:i', strtotime($value->fecha_vencimiento)),
                            !is_null($pedido) ? $pedido->pedido : '',
                            "{$value->serieProd}-",
                            $value->consecion_det,
                            $value->precio
                        ];

                        excelCreate::details($sheet, $equipos);
                        $equipos["comienzo"]++;
                    }
                }

                $movimiento = $busqueda["movimiento"];
            });
        })->export('xlsx');
    }
}
