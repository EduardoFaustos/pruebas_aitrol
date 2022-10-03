<?php

namespace Sis_medico;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\EstructuraFlujoEfectivo; 
use Sis_medico\Plan_cuentas;
use Session;

class ValoresCuentas 
{

    public static function activoCorriente($desde, $hasta)
    {
        $data = array();
        $f = new ValoresCuentas();
        $est = $f->getActivos();
        //dd(var_dump($est));
        foreach($est as $e){
            $saldo = 0;
            $saldo = $f->getAsientos($e->id_plan, $e->estado, $desde, $hasta);
          
            //dd(($saldo));
            $det['id_plan'] = $e->id_plan;      $det['nombre'] = $e->nombre; 
            $det['estado'] = $e->estado;        $det['signo'] = $e->signo; 
            $det['saldo'] = $saldo; 
            $data[] = $det;
        }

          
        return $data;
    }

    public static function activonoCorriente($desde, $hasta)
    {
        $data = array();
        $f = new ValoresCuentas();
        $est = $f->getActivosnoc();
        //dd(var_dump($est));
        foreach($est as $e){
            $saldo = 0;
            $saldo = $f->getAsientos($e->id_plan, $e->estado, $desde, $hasta);
          
            //dd(($saldo));
            $det['id_plan'] = $e->id_plan;      $det['nombre'] = $e->nombre; 
            $det['estado'] = $e->estado;        $det['signo'] = $e->signo; 
            $det['saldo'] = $saldo; 
            $data[] = $det;
        }

          
        return $data;
    }

    public static function pasivoCorriente($desde, $hasta)
    {
        $data = array();
        $f = new ValoresCuentas();
        $est = $f->getPasivos();
        //dd(var_dump($est));
        foreach($est as $e){
            $saldo = 0;
            $saldo = $f->getAsientos($e->id_plan, $e->estado, $desde, $hasta);
          
            //dd(($saldo));
            $det['id_plan'] = $e->id_plan;      $det['nombre'] = $e->nombre; 
            $det['estado'] = $e->estado;        $det['signo'] = $e->signo; 
            $det['saldo'] = $saldo; 
            $data[] = $det;
        }

          
        return $data;
    }

    public static function pasivonoCorriente($desde, $hasta)
    {
        $data = array();
        $f = new ValoresCuentas();
        $est = $f->getPasivosnoc();
        //dd(var_dump($est));
        foreach($est as $e){
            $saldo = 0;
            $saldo = $f->getAsientos($e->id_plan, $e->estado, $desde, $hasta);
          
            //dd(($saldo));
            $det['id_plan'] = $e->id_plan;      $det['nombre'] = $e->nombre; 
            $det['estado'] = $e->estado;        $det['signo'] = $e->signo; 
            $det['saldo'] = $saldo; 
            $data[] = $det;
        }

          
        return $data;
    }

    public static function Patrimonio($desde, $hasta)
    {
        $data = array();
        $f = new ValoresCuentas();
        $est = $f->getPatrimoniototal();
        //dd(var_dump($est));
        foreach($est as $e){
            $saldo = 0;
            $saldo = $f->getAsientos($e->id_plan, $e->estado, $desde, $hasta);
          
            //dd(($saldo));
            $det['id_plan'] = $e->id_plan;      $det['nombre'] = $e->nombre; 
            $det['estado'] = $e->estado;        $det['signo'] = $e->signo; 
            $det['saldo'] = $saldo; 
            $data[] = $det;
        }

          
        return $data;
    }


    public static function getEstructura()
    {
        $estructura = EstructuraFlujoEfectivo::where('estructura_flujo_efectivo.estado', '!=', 0)  
                        ->join('plan', 'id_plan', 'plan.id') 
                        ->select('id_plan', 'nombre', 'plan.estado', 'signo')
                        ->orderBy('signo') 
                        ->get();
        return $estructura;
    }

    public static function getPatrimoniototal()
    {
        //$estructura = EstructuraReportes::whereIn('estructura_reportes.estado', [9])  
        $estructura = EstructuraReportes::where('estructura_reportes.id_grupo', '=', 5)  
                        ->join('plan', 'id_plan', 'plan.id') 
                        ->select('id_plan', 'nombre', 'plan.estado', 'signo')
                        ->orderBy('signo') 
                        ->get();
        return $estructura;
    }

    public static function getActivos()
    {
        //$estructura = EstructuraReportes::whereIn('estructura_reportes.estado', [9])  
        $estructura = EstructuraReportes::where('estructura_reportes.id_grupo', '=', 1)  
                        ->join('plan', 'id_plan', 'plan.id') 
                        ->select('id_plan', 'nombre', 'plan.estado', 'signo')
                        ->orderBy('signo') 
                        ->get();
        return $estructura;
    }

    public static function getActivosnoc()
    {
        //$estructura = EstructuraReportes::whereIn('estructura_reportes.estado', [9])  
        $estructura = EstructuraReportes::where('estructura_reportes.id_grupo', '=', 3)  
                        ->join('plan', 'id_plan', 'plan.id') 
                        ->select('id_plan', 'nombre', 'plan.estado', 'signo')
                        ->orderBy('signo') 
                        ->get();
        return $estructura;
    }

    public static function getPasivos()
    {
        //$estructura = EstructuraReportes::whereIn('estructura_reportes.estado', [9])  
        $estructura = EstructuraReportes::where('estructura_reportes.id_grupo', '=', 2)  
                        ->join('plan', 'id_plan', 'plan.id') 
                        ->select('id_plan', 'nombre', 'plan.estado', 'signo')
                        ->orderBy('signo') 
                        ->get();
        return $estructura;
    }

    public static function getPasivosnoc()
    {
        //$estructura = EstructuraReportes::whereIn('estructura_reportes.estado', [9])  
        $estructura = EstructuraReportes::where('estructura_reportes.id_grupo', '=', 4)  
                        ->join('plan', 'id_plan', 'plan.id') 
                        ->select('id_plan', 'nombre', 'plan.estado', 'signo')
                        ->orderBy('signo') 
                        ->get();
        return $estructura;
    }

    public static function getAsientos($cuenta, $detalle, $desde, $hasta)
    {
        $id_empresa = Session::get('id_empresa');
        $data = array();    $saldo = 0;
        if($detalle==1){ 
            $operador = '='; $valor = $cuenta;
        }else{
            $operador = 'like'; $valor = $cuenta.'.%';
        }
        $operador = 'like'; $valor = $cuenta.'%';
        //dd($valor);

        $asiento = Ct_Asientos_Detalle::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                ->where('id_plan_cuenta', $operador, $valor)
                ->where('ct_asientos_detalle.estado', '<>', 0) 
                ->where('c.id_empresa', $id_empresa)
                ->whereBetween('fecha', ["2020-01-01 00:00:00", "2020-04-16 23:59:59"])
                ->select(DB::raw('ifnull(SUM(ct_asientos_detalle.debe - ct_asientos_detalle.haber),0) as saldo'),DB::raw('MONTH(ct_asientos_detalle.fecha) as mes')) 
                //->select(DB::raw('MONTH(ct_asientos_detalle.fecha) as mes'), DB::raw('ifnull(SUM(ct_asientos_detalle.debe - ct_asientos_detalle.haber),0) as saldo')) 
                ->groupBy('mes')
                ->get();

        foreach ($asiento as $row) { 
            $data['saldo'] = $row->saldo;
            $data['mes'] = $row->mes;
            $saldo+=$row->saldo;
        }
        //dd($data);
        
        return $saldo;
    }

}