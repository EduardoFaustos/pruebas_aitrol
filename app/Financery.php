<?php

namespace Sis_medico;

use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;

class Financery{
    public static function detalle_precio($desde, $hasta,$tipo,$afecha=null){
       
        $balance    = array();
        if ($tipo == 'I') {
            $condicion = '4';
        } elseif ($tipo == 'C') {
            $condicion = '5';
        } else {
            $condicion = '6';
        }
        
        $plans = Plan_Cuentas::where('estado', '<>', 0)
            
            ->where('id', 'like', "$condicion")
            ->select('id', 'nombre')
            ->orderBy('id', 'asc')
            ->get();
        $i       = 0;
        $saldo   = 0;
        $lastDay = date('t', strtotime("$hasta-12"));
        //dd("$hasta-$lastDay 23:59:59");
        
        if ($afecha == '2') {
            $desde = "$desde-01-01";
            $hasta = "$hasta-12-31";
        }
        $saldo=0;

        foreach ($plans as $plan) {
            
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like','%'. $plan->id . '%')
                    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                    ->where('ct_asientos_detalle.estado', '<>', 0);
                
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                    $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like','%'. $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')

                        ->where('p.nombre', 'like', '%-)%')
                        ->where('ct_asientos_detalle.estado', '<>', 0)
                        ->whereBetween('ct_asientos_detalle.fecha', ["$desde 00:00:00", "$hasta 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }
                

                $asiento = $asiento->whereBetween('ct_asientos_detalle.fecha', ["$desde 00:00:00", "$hasta 23:59:59"])
                    ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                    ->get();
                
                foreach ($asiento as $row) {
                    $saldo = $row->saldo - $saldo2;
                }

            }

        }

        return $saldo;
    }
    public static function trabajadores($desde,$hasta,$afecha=null){
        $totpyg = 0;
        $toting = Financery::detalle_precio($desde, $hasta, 'I', $afecha);
        $totcos = Financery::detalle_precio($desde, $hasta, 'C', $afecha);
        $totgas = Financery::detalle_precio($desde, $hasta, 'G', $afecha);
        $totpyg = round(($toting - ($totcos + $totgas)) * 0.15, 2);
        return $totpyg;
    }
}