<?php

namespace Sis_medico;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Comprobante_Secuencia;
use Sis_medico\Empresa;
use Sis_medico\LogAnularDeposito;
use Illuminate\Support\Facades\DB;
use Sis_medico\LogCierreAnio;
use Sis_medico\Ct_Configuraciones;

class LogConfig
{
    public static function busqueda($cuenta, $modulo = "")
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        //$id_plan = $cuenta;
        $nuevo = 0;
        $endoscopy = 0;
        // if (Auth::user()->id == "0957258056" or Auth::user()->id == "0953905999" and $endoscopy == 0 or Auth::user()->id == "0954127262") {
        if($nuevo == 0 and $endoscopy == 0){
            $id_plan = LogConfig::obtener_cuenta2($cuenta, $modulo="");
            // return $config2;
        } else {
            if ($endoscopy == 1) {
                $id_plan = LogConfig::obtener_cuenta($cuenta);
            } else {
                $plan_empresa = Plan_Cuentas_Empresa::where('plan', $cuenta)->where('id_empresa', $id_empresa)->first();
                if (!is_null($plan_empresa)) {
                    $id_plan = $plan_empresa->id_plan;
                } else {
                    $id_plan = $cuenta;
                }
            }
        }
        return $id_plan;
    }

    public static function obtener_cuenta($tipo)
    {
        $id_empresa = Session::get('id_empresa');
        //dd($tipo);
        $endoscopy = 0;

        $cuenta = Ct_Configuraciones::leftjoin('plan_cuentas_empresa', 'ct_configuraciones.id_plan', 'plan_cuentas_empresa.id_plan')
            ->where('plan_cuentas_empresa.id_empresa', $id_empresa)
            ->where('ct_configuraciones.id_empresa', $id_empresa)
            ->where('tipo', 'like', "%-{$tipo}-%")
            ->select('plan_cuentas_empresa.id_plan as cuenta_guardar', 'plan_cuentas_empresa.plan as secuencial_mostrar', 'plan_cuentas_empresa.nombre as nombre_mostrar')
            ->first();
        if (!is_null($cuenta)) {
            return $cuenta->cuenta_guardar;
        } else {
            return $tipo;
        }
    }

    public static function masivoConfiguraciones()
    {
        $id_empresa = "1793135579001";
        $empresas = Empresa::all();
        $configuraciones = Ct_Configuraciones::where('id_empresa', '<>', "NULL")->get();
        //   dd($configuraciones);
        foreach ($empresas as $emp) {
            foreach ($configuraciones as $config) {
                if ($config->id_empresa != $emp->id) {
                    $data = $config["original"];
                    unset($data["id"]);
                    $data["id_empresa"] = $emp->id;
                    //dd($data);
                    Ct_Configuraciones::create($data);
                }
            }
        }


        return $configuraciones;
    }

    public static function obtener_cuenta2($busq, $modulo="")
    {
        $id_empresa = Session::get('id_empresa');
        $nro_cuenta = explode('.', $busq);

        $cuenta = Ct_Configuraciones::leftjoin('plan_cuentas_empresa', 'ct_configuraciones.id_plan', 'plan_cuentas_empresa.id_plan')
            ->where('plan_cuentas_empresa.id_empresa', $id_empresa)
            ->where('ct_configuraciones.id_empresa', $id_empresa);

        if ($nro_cuenta[0] > 0) {
            // dd("si es nro. cuenta");
            $cuenta = $cuenta->where('tipo', 'like', "%-{$busq}-%");
        } else {
            //dd("es nombre modulo");
            $cuenta = $cuenta->where('modulo', 'like', "{$busq}");
        }
        $cuenta = $cuenta->select('plan_cuentas_empresa.id_plan as cuenta_guardar', 'plan_cuentas_empresa.plan as secuencial_mostrar', 'plan_cuentas_empresa.nombre as nombre_mostrar')->first();
        if (!is_null($cuenta)) {
            return $cuenta->cuenta_guardar;
        } else {
            return $busq;
        }

    }

    public static function getDia($id){    
        $day = ['N/A','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
        return $day[$id];
    }
}
