<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Session;
use Sis_medico\Http\Controllers\Controller;
use Excel;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_NumberFormat;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Ct_Flujo_Cajap;
use Sis_medico\Http\Controllers\ImportacionesController;


class ReporteFlujoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index_flujo()
    {
        $id_empresa     = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $anio = date('Y');
        $mes = date('m');


        return view('contable/flujo_proyectado/index', ['anio' => $anio, 'mes' => $mes, 'empresa' => $empresa]);
    }

    public function excel_flujo(Request $request)
    {
        Excel::create('FLUJO DE CAJA PROYECTADO', function ($excel) use ($request) {
            $excel->sheet('FLUJO', function ($sheet) use ($request) {
                $anio = $request['anio'];
                $mes = $request['mes'];
                
                
                $inicio = 1;
                $fin = date("t", strtotime($inicio));

                $fechasMes = [];

                for ($i = 1; $i <= $fin; $i++) {
                    array_push($fechasMes, "{$anio}-{$mes}-{$i}");
                }
                array_push($fechasMes, " ");
                $dias["data"] = $fechasMes;
                $dias["comienzo"] = 1;
                $dias["letra"] = "B";

                excelCreate::details($sheet, $dias);

                $comienzo2 = 2;
                $array = array('Efectivo inicial', 'Ingresos de efectivo (ganancias):');
                for ($i=0; $i < count($array); $i++) {
                    ImportacionesController::excelDetalles($sheet, $comienzo2, ["A"], [$array[$i]]);
                    $comienzo2++;
                }

                $cuentas_ing = Ct_Flujo_Cajap::where('estado', 1)->where('tipo', 1)->get();
                $datos["comienzo"] = 4;
                foreach ($cuentas_ing as $ingreso) {
                    $datos["data"] = [$ingreso->nombre];
                    excelCreate::details($sheet, $datos);
                    $datos["comienzo"]++;
                }

                $array2 = array('Total de ingresos de efectivo', 'Saldo disponible de efectivo','Egresos de efectivo (gastos):');
                for ($i=0; $i < count($array2); $i++) {
                    ImportacionesController::excelDetalles($sheet, $datos["comienzo"]++, ["A"], [$array2[$i]]);
                }
                                  
                $cuentas_egr = Ct_Flujo_Cajap::where('estado', 1)->where('tipo', 2)->get();
               // $datos["comienzo"] = $datos["comienzo"] + 3;
                foreach ($cuentas_egr as $egreso) {
                    $datos["data"] = [$egreso->nombre];
                    excelCreate::details($sheet, $datos);
                    $datos["comienzo"]++;
                }

                $array3 = array('Subtotal', 'Otros egresos de efectivo: Proveedores');
                for ($i=0; $i < count($array3); $i++) {
                    ImportacionesController::excelDetalles($sheet, $datos["comienzo"]++, ["A"], [$array3[$i]]);         
                }
                $datos["comienzo"]++;

                $cuentas_otros = Ct_Flujo_Cajap::where('estado', 1)->where('tipo', 3)->get();
                //$datos["comienzo"] = $datos["comienzo"] + 3;
                foreach ($cuentas_otros as $otros) {
                    $datos["data"] = [$otros->nombre];
                    excelCreate::details($sheet, $datos);
                    $datos["comienzo"]++;
                }

                $array4 = array('Subtotal', 'Total de egresos de efectivo', 'Efectivo al final del período');
                for ($i=0; $i < count($array4); $i++) {
                    ImportacionesController::excelDetalles($sheet, $datos["comienzo"]++, ["A"], [$array4[$i]]);         
                }
            });
        })->export('xlsx');
    }
}
