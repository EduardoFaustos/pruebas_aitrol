<?php

namespace Sis_medico\Http\Controllers\contable;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Tipo_Rol;
use Sis_medico\Ct_Rh_Saldos_Iniciales_Detalle;
use Sis_medico\Ct_Rh_Saldos_Iniciales;
use Sis_medico\Ct_Rh_Valores;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_Detalle_Rol;
use Sis_medico\Ct_Rh_Prestamos;
use Sis_medico\Ct_Rh_Otros_Anticipos;
use Sis_medico\Ct_Rh_Prestamos_Detalle;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Rh_Cuotas_Quirografario;
use Sis_medico\Ct_Rh_Cuotas_Hipotecarios;
use Sis_medico\Nota_Debito;
use Sis_medico\Nota_Debito_Detalle;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rol_Proceso;
use Sis_medico\LogAsiento;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Rol_Forma_Pago;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Ct_Rh_Detalle_Horas_Extras;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Rh_Detalle_Prestamos_Subidos;
//Nueva Forma de Pago
use Sis_medico\User;
use Sis_medico\RolAsiento;
use Sis_medico\RolAsientoCuentas;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Excel;

class Nuevo_RolController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $anio = $request->year;
        $mes  = $request->mes;
        if ($anio == null) {
            $anio = date('Y');
        }
        if ($mes == null) {
            $mes = 0;
        }

        $empleado = Ct_Nomina::find($id);

        $roles = $empleado->roles->where('estado', 1)->where('anio', $anio)->sortByDesc('fecha_elaboracion');

        if ($mes > 0) {
            $roles = $roles->where('mes', $mes);
        }

        return view('contable/nuevo_rol_pago/index', ['empleado' => $empleado, 'roles' => $roles, 'anio' => $anio, 'mes' => $mes]);
    }

    public function crear($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $nomina = Ct_Nomina::findorfail($id);
        /*$val_aport_pers = Ct_Rh_Valores::where('id_empresa', $registro->id_empresa)
        ->where('tipo', 1)->where('id', $registro->aporte_personal)->first();*/

        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first();//dd($val_fond_reserv);
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();
        $anio = date('Y');
        $mes  = date('m');
       
        $fecha = date('Y-m-d H:i:s');
        $rol = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_nomina', $nomina->id)
            ->first();       
        $empresa = Empresa::find($nomina->id_empresa);    
        $parqueo = $nomina->parqueo;  

        if (is_null($rol)) {
           
            $id_rol = Ct_Rol_Pagos::insertGetId([
                'id_nomina'         => $nomina->id,
                'id_user'           => $nomina->id_user,
                'id_empresa'        => $nomina->id_empresa,
                'anio'              => $anio,
                'mes'               => $mes,
                'id_tipo_rol'       => '1',
                //'neto_recibido'     => ,
                'fecha_elaboracion' => $fecha,
                'estado'            => '1',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,

            ]);
            
            //PRESTAMOS
            $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)
                ->where('id_empresa', $nomina->id_empresa)
                ->where('estado','1')
                ->where('prest_cobrad','0')
                //->where('mes_inicio_cobro','<=',$mes)
                //->where('anio_inicio_cobro','<=',$anio)
                ->where(function ($query) use($anio,$mes){
                    $query->where('anio_inicio_cobro', '<', $anio)//2021 < 2022
                        ->orwhere(function ($query2) use($anio,$mes){
                            $query2->where('anio_inicio_cobro', '=', $anio)//2022 = 2022
                                ->Where('mes_inicio_cobro', '<=', $mes);// ene <= ene   
                    });  
                })
                ->get();   

            $total_cuota = 0;    
            foreach($prestamos as $prestamo){
                $total_cuota += $prestamo->valor_cuota;
                $cuotas = $prestamo->detalles()->where('estado',1)->where('estado_pago',1)->count();//dd($cuotas);
                $xanio   = $prestamo->anio_inicio_cobro;
                $xmes    = $prestamo->mes_inicio_cobro;
                $xmes    = $xmes + $cuotas;
                if($xmes > 12){
                    $xmes = 1;
                    $xanio ++;
                }
                $cuotas++;
                if($cuotas <= $prestamo->num_cuotas){
                    Ct_Rh_Prestamos_Detalle::create([
                        'id_ct_rh_prestamos' => $prestamo->id,
                        'anio'               => $anio,
                        'mes'                => $mes,
                        'fecha'              => date('Y-m-d H:i:s'),
                        'cuota'              => $cuotas,
                        'valor_cuota'        => $prestamo->valor_cuota,
                        'id_ct_rol_pagos'    => $id_rol,
                        'estado'             => '1',
                        'estado_pago'        => '1',
                        'fecha_pago'         => date('Y-m-d H:i:s'),
                        'id_usuariocrea'     => $idusuario,
                        'id_usuariomod'      => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                    ]);
                    $saldo_prestamo = $prestamo->saldo_total;
                    $saldo_prestamo -= $prestamo->valor_cuota;
                    $prestamo->update([
                        'saldo_total' => $saldo_prestamo, 
                    ]);
                }    
                if($cuotas >= $prestamo->num_cuotas){
                    $prestamo->update([
                        'prest_cobrad' => '1', 
                    ]);    
                }

            } 
            //SALDOS
            $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $nomina->id_user)
                ->where('id_empresa', $nomina->id_empresa)
                ->where('estado','1')
                ->where('saldo_cobrad','0')
                ->get(); //dd($saldos);  

            $total_cuota_ini = 0;    
            foreach($saldos as $saldo){
                $total_cuota_ini += $saldo->valor_cuota;
                $cuotas = $saldo->detalles()->where('estado',1)->where('estado_pago',1)->count();//dd($cuotas);
                $anio   = $saldo->anio_inicio_cobro;
                $mes    = $saldo->mes_inicio_cobro;
                $mes    = $mes + $cuotas;
                if($mes > 12){
                    $mes = 1;
                    $anio ++;
                }
                $cuotas++;
                if($cuotas < $saldo->num_cuotas){
                    Ct_Rh_Saldos_Iniciales_Detalle::create([
                        'id_ct_rh_saldos_iniciales' => $saldo->id,
                        'anio'               => $anio,
                        'mes'                => $mes,
                        'fecha'              => date('Y-m-d H:i:s'),
                        'cuota'              => $cuotas,
                        'valor_cuota'        => $saldo->valor_cuota,
                        'id_ct_rol_pagos'    => $id_rol,
                        'estado'             => '1',
                        'estado_pago'        => '1',
                        'fecha_pago'         => date('Y-m-d H:i:s'),
                        'id_usuariocrea'     => $idusuario,
                        'id_usuariomod'      => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                    ]);
                    $saldo_prestamo = $saldo->saldo_res;
                    $saldo_prestamo -= $saldo->valor_cuota;
                    $saldo->update([
                        'saldo_res' => $saldo_prestamo, 
                    ]);
                }    
                if($cuotas >= $saldo->num_cuotas){
                    $saldo->update([
                        'saldo_cobrad' => '1', 
                    ]);    
                }

            } 

            //OTROS ANTICIPOS
            $anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $nomina->id_user)
            ->where('id_empresa', $nomina->id_empresa)
            ->where('mes_cobro_anticipo', $mes)
            ->where('anio_cobro_anticipo', $anio)
            ->where('estado', '1')
            ->get();
            $total_otros_anticipos = 0;    
            foreach($anticipos as $anticipo){
                $total_otros_anticipos += $anticipo->monto_anticipo;
                $anticipo->update(['id_ct_rol' => $id_rol]);

            }   

            //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
            $dias_laborados = 30;$sobre_tiempo_50 = 0;$sobre_tiempo_100 = 0;$cantidad_horas50 = 0;$cantidad_horas100 = 0;
            $bonificacion = 0;$transporte = 0;$exam_laboratorio = 0;$fondo_reserva = 0;$decimo_tercero = 0;$decimo_cuarto = 0;
            $multa = 0;$impuesto_renta = 0;$total_quota_quirog = 0; $total_quota_hipot = 0;$fond_reserv_cobrar = 0;$fondo_reserva_acumulado = 0;
            $sueldo_mensual     = $nomina->sueldo_neto;
            $impuesto_renta     = $nomina->impuesto_renta;
            $bono_imputable     = $nomina->bono_imputable;
            $alimentacion       = $nomina->alimentacion;
            $fecha_ingreso      = $nomina->fecha_ingreso;
            $bonificacion       = $nomina->bono;
            $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
            $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
            $tipo_decimo_tercero     = $nomina->decimo_tercero;
            $aporte_personal         = $nomina->aportepersonal->valor;
            $seguro_privado          = $nomina->seguro_privado;
            $anticipo_quincena       = $nomina->val_anticip_quince;
            $a15 = Ct_Rh_Valor_Anticipos::where('id_user',$nomina->id_user)->where('id_empresa',$nomina->id_empresa)->where('anio',$anio)->where('mes',$mes)->first();
            if(!is_null($a15)){
                $anticipo_quincena = $a15->valor_anticipo;
            }
            $pct_fondo_reserva = 0;
            if(!is_null($val_fond_reserv)){
                $pct_fondo_reserva   = $val_fond_reserv->valor;
            }
            
            $valor_sueldo_basico = 0;
            if(!is_null($val_sal_basico)){
                $valor_sueldo_basico = $val_sal_basico->valor;
            }    
                 


            //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
            $sueldo_mensual = $sueldo_mensual / 30;
            $sueldo_mensual = $sueldo_mensual * $dias_laborados;
            $sueldo_mensual = round($sueldo_mensual,2);
            
            //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
            $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

            //4:FONDO DE RESERVA
            $fecha         = date("Y-m-d");
            $fec           = new DateTime($fecha);
            $fec2          = new DateTime($fecha_ingreso);
            $diff          = $fec->diff($fec2); //dd($diff);
            $intervalMeses = $diff->format("%m");
            $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
            $añosAhora    = $diff->format("%y"); //dd($añosAhora);
            $intervalDias  = $diff->format("%d");
            $diaActual     = $fec->format("d");
            $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
            if ($añosAhora > 0) {
                if ($tipo_fondo_reserva == 2) { //mensualiza
                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                } 
                ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (1)///////////////////////////////////////
                else{
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);
                }
                //////////////////////////////////////////////////////////////////////////////////////////////////
            }
            $fondo_reserva = round($fondo_reserva,2);

            //5:DECIMO TERCER
            if($tipo_decimo_tercero == 2){
                $decimo_tercero = $base_iess / 12;    
            }
            $decimo_tercero = round($decimo_tercero,2);

            //6:DECIMO CUARTO
            if($tipo_decimo_cuarto == 2){
                $decimo_cuarto = $valor_sueldo_basico / 12;    
            }
            $decimo_cuarto = round($decimo_cuarto,2);

            //7:PORCENTAJE IESS
            $porcentaje_iess = $base_iess * $aporte_personal / 100;
            $porcentaje_iess = round($porcentaje_iess,2);

            $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto +$alimentacion + $transporte;
            $total_ingresos = round($total_ingresos,2);

            $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $parqueo;
            $total_egresos = round($total_egresos,2);

            $neto_recibir = $total_ingresos - $total_egresos;

            //Guardado en la Tabla Ct_Detalle_Rol
            Ct_Detalle_Rol::create([
                'id_rol'                      => $id_rol,
                'dias_laborados'              => $dias_laborados,
                'base_iess'                   => $base_iess,
                'sueldo_mensual'              => $sueldo_mensual,
                'cantidad_horas50'            => $cantidad_horas50,
                'sobre_tiempo50'              => $sobre_tiempo_50,
                'cantidad_horas100'           => $cantidad_horas100,
                'sobre_tiempo100'             => $sobre_tiempo_100,
                'bonificacion'                => $bonificacion,
                'alimentacion'                => $alimentacion,
                'transporte'                  => $transporte,
                'bono_imputable'              => $bono_imputable,
                'exam_laboratorio'            => $exam_laboratorio,
                'fondo_reserva'               => $fondo_reserva,
                'decimo_tercero'              => $decimo_tercero,
                'decimo_cuarto'               => $decimo_cuarto,
                'porcentaje_iess'             => $porcentaje_iess,
                'seguro_privado'              => $seguro_privado,
                'impuesto_renta'              => $impuesto_renta,
                'multa'                       => $multa,
                'fond_reserv_cobrar'          => $fond_reserv_cobrar,
                'otros_egresos'               => 0,
                'prestamos_empleado'          => $total_cuota,
                'saldo_inicial_prestamo'      => $total_cuota_ini,
                'anticipo_quincena'           => $anticipo_quincena,
                //'observacion_bono'          => $request['observacion_bono'],
                //'observacion_alimentacion'  => $request['observacion_alimentacion'],
                //'observ_seg_privado'        => $request['observacion_seg_priv'],
                //'observ_imp_renta'          => $request['observacion_imp_rent'],
                'otro_anticipo'               => $total_otros_anticipos,
                //'observacion_multa'         => $request['observ_multa'],
                //'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
                //'observacion_prestamo'      => $request['concepto_prestamo'],
                //'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
                //'observacion_anticip_quinc' => $request['concepto_quincena'],
                //'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
                //'observacion_transporte'    => $request['observacion_transporte'],
                //'observacion_bonoimp'       => $request['observacion_bonoimp'],
                //'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
                //'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
                'total_quota_quirog'          => $total_quota_quirog,
                'total_quota_hipot'           => $total_quota_hipot,
                'total_ingresos'              => $total_ingresos,
                'total_egresos'               => $total_egresos,
                'neto_recibido'               => $neto_recibir,
                
                'id_usuariocrea'            => $idusuario,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
                'parqueo'                   => $parqueo,
                'fondo_reserva_acumulado'   => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (1)
            ]);

            $rol = Ct_Rol_Pagos::find($id_rol);

            

        }

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        $detalle_rol = $rol->detalle->first();

        $prestamos_rol = Ct_Rh_Prestamos_Detalle::where('id_ct_rol_pagos',$rol->id)->get();

        $saldos_ini = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rol_pagos',$rol->id)->get();

        $otros_anticipos = Ct_Rh_Otros_Anticipos::where('id_ct_rol',$rol->id)->get();

        $cuotas_quiro = Ct_Rh_Cuotas_Quirografario::where('id_rol',$rol->id)->get();

        $cuotas_hipo = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $rol->id)->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all(); 

        $lista_banco = Ct_Bancos::all();

        $forma_pago = $rol->formas_de_pago->where('estado','1');

        //dd($detalle_rol);
        return view('contable.rol_pago.editnew',['rol' => $rol, 'nomina' => $nomina, 'val_fond_reserv' => $val_fond_reserv, 'val_sal_basico' => $val_sal_basico, 'form_pago' => null, 'ct_tipo_rol' => $ct_tipo_rol, 'detalle_rol' => $detalle_rol, 'forma_pago' => $forma_pago, 'prestamos_rol' => $prestamos_rol, 'saldos_ini' => $saldos_ini, 'otros_anticipos' => $otros_anticipos, 'cuotas_hipo' => $cuotas_hipo, 'cuotas_quiro' => $cuotas_quiro, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'empresa' => $empresa]);

    }

    public function update(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_rol = $request->id_rol;
        $id_detalle_rol = $request->id_detalle_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $detalle_rol = Ct_Detalle_Rol::find($id_detalle_rol);
        $nomina = $rol->ct_nomina;
        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first();
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();

        $total_cuota = 0;$total_cuota_ini = 0; $total_otros_anticipos = 0; 
        $total_quota_quirog = 0; $total_quota_hipot = 0; 

        $total_cuota = $rol->prestamo_detalle->sum('valor_cuota'); 
        $total_cuota_ini = $rol->saldo_detalle->sum('valor_cuota');  
        $total_otros_anticipos = $rol->otros_anticipos->sum('monto_anticipo'); 
        $total_quota_hipot = $rol->cuotas_hipotecarios->sum('valor_cuota');
        $total_quota_quirog = $rol->cuotas_quirografario->sum('valor_cuota');


        //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
        $dias_laborados = $request->dias_laborados;$sobre_tiempo_50 = 0;$sobre_tiempo_100 = 0;$cantidad_horas50 = $request->cant_horas_50;
        $cantidad_horas100 = $request->cant_horas_100;$bonificacion = $request->valor_bono;$transporte = $request->transporte;
        $exam_laboratorio = $request->exam_laboratorio;$fondo_reserva = 0;$decimo_tercero = 0;$decimo_cuarto = 0;$fondo_reserva_acumulado = 0;
        $multa = $request->valor_multa;$impuesto_renta = $request->impuesto_renta;
        $sueldo_mensual     = $nomina->sueldo_neto;

        $bono_imputable     = $request->bono_imputable;
        $alimentacion       = $request->alimentacion;
        $fecha_ingreso      = $nomina->fecha_ingreso;
        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
        $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
        $tipo_decimo_tercero     = $nomina->decimo_tercero;
        $aporte_personal         = $nomina->aportepersonal->valor;
        $seguro_privado          = $request->seguro_privado;
        $anticipo_quincena       = $request->anticipo_quincena;
        $parqueo                 = $request->parqueo;
        $pct_fondo_reserva = 0; $valor_sueldo_basico = 0;
        if(!is_null($val_fond_reserv)){
            $pct_fondo_reserva   = $val_fond_reserv->valor;
        }
        if(!is_null($val_sal_basico)){
            $valor_sueldo_basico = $val_sal_basico->valor;
        }    
            
        $fond_reserv_cobrar  = $request->fond_res_cobrar_trab;
        $otros_egresos = $request->otros_egresos_trab;

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        $sueldo_mensual = $sueldo_mensual / 30;

        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        //dd($sueldo_mensual);
        $sueldo_mensual = round($sueldo_mensual,2);
        //CALCULO SOBRETIEMPO 50
        //$sobre_tiempo_50 = ((($sueldo_mensual/30)/8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = ((($nomina->sueldo_neto/30)/8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = round($sobre_tiempo_50,2);
        //CALCULO SOBRETIEMPO 100
        //$sobre_tiempo_100 = ((($sueldo_mensual/30)/8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = ((($nomina->sueldo_neto/30)/8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = round($sobre_tiempo_100,2);

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        /*$sueldo_mensual = $sueldo_mensual / 30;
        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        $sueldo_mensual = round($sueldo_mensual,2);*/
        
        //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
        $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

        //4:FONDO DE RESERVA
        $fecha         = date("Y-m-d");
        $fec           = new DateTime($fecha);
        $vt_dia        = date("d",strtotime($fecha_ingreso));
        $fec2          = new DateTime($fecha_ingreso);
        $diff          = $fec->diff($fec2); //dd($diff);
        $intervalMeses = $diff->format("%m");
        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
        $añosAhora     = $diff->format("%y"); //dd($añosAhora);
        $intervalDias  = $diff->format("%d");
        $diaActual     = $fec->format("d");
        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
        if ($añosAhora > 0) {
            if ($tipo_fondo_reserva == 2) { //mensualiza
                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
            } 
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
            else{
                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);  
                $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);  
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////
        }else{
            if($intervalMeses == '11' && $tipo_fondo_reserva == 2){
                $vt_dia = $dias_laborados - $vt_dia; 
                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                //dd($fondo_reserva,$pct_fondo_reserva);
            }
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
            else{
                if($intervalMeses == '11'){
                    $vt_dia = $dias_laborados - $vt_dia; 
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);
                }    
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////// 
        }
        $fondo_reserva = round($fondo_reserva,2);

        //5:DECIMO TERCER
        if($tipo_decimo_tercero == 2){
            $decimo_tercero = $base_iess / 12;    
        }
        $decimo_tercero = round($decimo_tercero,2);

        //6:DECIMO CUARTO
        if($tipo_decimo_cuarto == 2){
            $decimo_cuarto = $valor_sueldo_basico / 12;    
        }
        $decimo_cuarto = round($decimo_cuarto,2);

        //7:PORCENTAJE IESS
        $porcentaje_iess = $base_iess * $aporte_personal / 100;
        $porcentaje_iess = round($porcentaje_iess,2);

        $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto +$alimentacion + $transporte;
        $total_ingresos = round($total_ingresos,2);

        $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $otros_egresos + $exam_laboratorio + $parqueo + $fond_reserv_cobrar;
        $total_egresos = round($total_egresos,2);

        $neto_recibir = $total_ingresos - $total_egresos;
        

        $detalle_rol->update([
            'dias_laborados'              => $dias_laborados,
            'base_iess'                   => $base_iess,
            'sueldo_mensual'              => $sueldo_mensual,
            'cantidad_horas50'            => $cantidad_horas50,
            'sobre_tiempo50'              => $sobre_tiempo_50,
            'cantidad_horas100'           => $cantidad_horas100,
            'sobre_tiempo100'             => $sobre_tiempo_100,
            'bonificacion'                => $bonificacion,
            'alimentacion'                => $alimentacion,
            'transporte'                  => $transporte,
            'bono_imputable'              => $bono_imputable,
            'exam_laboratorio'            => $exam_laboratorio,
            'fondo_reserva'               => $fondo_reserva,
            'decimo_tercero'              => $decimo_tercero,
            'decimo_cuarto'               => $decimo_cuarto,
            'porcentaje_iess'             => $porcentaje_iess,
            'seguro_privado'              => $seguro_privado,
            'impuesto_renta'              => $impuesto_renta,
            'multa'                       => $multa,
            'fond_reserv_cobrar'          => $fond_reserv_cobrar,
            'otros_egresos'               => $otros_egresos,
            'prestamos_empleado'          => $total_cuota,
            'saldo_inicial_prestamo'      => $total_cuota_ini,
            'anticipo_quincena'           => $anticipo_quincena,
            'observacion_bono'          => $request['observacion_bono'],
            'observacion_alimentacion'  => $request['observacion_alimentacion'],
            'observ_seg_privado'        => $request['observacion_seg_priv'],
            'observ_imp_renta'          => $request['observacion_imp_rent'],
            'otro_anticipo'               => $total_otros_anticipos,
            'observacion_multa'         => $request['observ_multa'],
            'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
            'observacion_prestamo'      => $request['concepto_prestamo'],
            'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
            'observacion_anticip_quinc' => $request['concepto_quincena'],
            'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
            'observacion_transporte'    => $request['observacion_transporte'],
            'observacion_bonoimp'       => $request['observacion_bonoimp'],
            'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
            'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
            'total_quota_quirog'          => $total_quota_quirog,
            'total_quota_hipot'           => $total_quota_hipot,
            'total_ingresos'              => $total_ingresos,
            'total_egresos'               => $total_egresos,
            'neto_recibido'               => $neto_recibir,
            'id_usuariomod'               => $idusuario,
            'ip_modificacion'             => $ip_cliente,
            'parqueo'                     => $parqueo,
            'fondo_reserva_acumulado'     => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)
        ]);

        return "ok";

    }

    public function update_observaciones(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_rol = $request->id_rol;
        $id_detalle_rol = $request->id_detalle_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $detalle_rol = Ct_Detalle_Rol::find($id_detalle_rol);
        

        $observacion_bono = $request->observacion_bono; 
        $observacion_bonoimp = $request->observacion_bonoimp; 
        $observacion_alimentacion =$request->observacion_alimentacion; 
        $observacion_transporte = $request->observacion_transporte; 
        $observacion_seg_priv = $request->observacion_seg_priv; 
        $observacion_imp_rent = $request->observacion_imp_rent; 
        $observ_multa = $request->observ_multa; 
        $obs_fond_cob_trab = $request->obs_fond_cob_trab; 
        $obs_otros_egres_trab = $request->obs_otros_egres_trab; 
        $observ_examlaboratorio = $request->observ_examlaboratorio; 
        $obser_saldo_inicial = $request->obser_saldo_inicial; 
        $concepto_quincena = $request->concepto_quincena;
        $concepto_prestamo = $request->concepto_prestamo;
        $concep_otros_anticipos = $request->concep_otros_anticipos;

        $detalle_rol->update([
            'observacion_bono'          => $observacion_bono,
            'observacion_alimentacion'  => $observacion_alimentacion,
            'observ_seg_privado'        => $observacion_seg_priv,
            'observ_imp_renta'          => $observacion_imp_rent,
            'observacion_multa'         => $observ_multa,
            'observacion_fondo_cobrar'  => $obs_fond_cob_trab,
            'observacion_prestamo'      => $concepto_prestamo,
            'observacion_saldo_inicial' => $obser_saldo_inicial,
            'observacion_anticip_quinc' => $concepto_quincena,
            'observacion_otro_anticip'  => $concep_otros_anticipos,
            'observacion_transporte'    => $observacion_transporte,
            'observacion_bonoimp'       => $observacion_bonoimp,
            'observ_examlaboratorio'    => $observ_examlaboratorio,
            'observacion_otro_egreso'   => $obs_otros_egres_trab,
            'id_usuariomod'             => $idusuario,
            'ip_modificacion'           => $ip_cliente,
        ]);


        return "ok";    
    }

    public function eliminar_prestammo_rol($id){

        $detalle_prestamo_rol = Ct_Rh_Prestamos_Detalle::find($id);

        $prestamo_rol = $detalle_prestamo_rol->prestamos;
        $prestamo_rol->update([
            'saldo_total' => $prestamo_rol->saldo_total + $detalle_prestamo_rol->valor_cuota,
            'prest_cobrad' => 0
        ]);
        $detalle_prestamo_rol->delete();

    }

    public function recargar_prestammo_rol($id_nomina, $id_rol){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nomina = Ct_Nomina::find($id_nomina);
        $rol = Ct_Rol_Pagos::find($id_rol);
        $mes = $rol->mes;
        $anio = $rol->anio;

        //PRESTAMOS
        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)
            ->where('id_empresa', $nomina->id_empresa)
            ->where('estado','1')
            //->where('mes_inicio_cobro','<=',$mes)
            //->where('anio_inicio_cobro','<=',$anio)
            ->where(function ($query) use($anio,$mes){
                $query->where('anio_inicio_cobro', '<', $anio)//2021 < 2022
                    ->orwhere(function ($query2) use($anio,$mes){
                        $query2->where('anio_inicio_cobro', '=', $anio)//2022 = 2022
                            ->Where('mes_inicio_cobro', '<=', $mes);// ene <= ene   
                });  
            })
            ->where('prest_cobrad','0')
            ->get(); // dd($prestamos->toSql(),$mes,$anio);  

        $total_cuota = 0;    
        foreach($prestamos as $prestamo){
            $total_cuota += $prestamo->valor_cuota;
            $cuotas = $prestamo->detalles()->where('estado',1)->where('estado_pago',1)->count();//dd($cuotas);
            $xanio   = $prestamo->anio_inicio_cobro;
            $xmes    = $prestamo->mes_inicio_cobro;
            $xmes    = $xmes + $cuotas;
            if($xmes > 12){
                $xmes = 1;
                $xanio ++;
            }
            $cuotas++;
            if($cuotas <= $prestamo->num_cuotas){
                Ct_Rh_Prestamos_Detalle::create([
                    'id_ct_rh_prestamos' => $prestamo->id,
                    'anio'               => $anio,
                    'mes'                => $mes,
                    'fecha'              => date('Y-m-d H:i:s'),
                    'cuota'              => $cuotas,
                    'valor_cuota'        => $prestamo->valor_cuota,
                    'id_ct_rol_pagos'    => $id_rol,
                    'estado'             => '1',
                    'estado_pago'        => '1',
                    'fecha_pago'         => date('Y-m-d H:i:s'),
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                ]);
                $saldo_prestamo = $prestamo->saldo_total;
                $saldo_prestamo -= $prestamo->valor_cuota;
                $prestamo->update([
                    'saldo_total' => $saldo_prestamo, 
                ]);
            }    
            if($cuotas >= $prestamo->num_cuotas){
                $prestamo->update([
                    'prest_cobrad' => '1', 
                ]);    
            }

        } 
    }   

    public function eliminar_saldo_rol($id){

        $detalle_saldo_rol = Ct_Rh_Saldos_Iniciales_Detalle::find($id);

        $saldo_rol = $detalle_saldo_rol->saldos;
        $saldo_rol->update([
            'saldo_res' => $saldo_rol->saldo_res + $detalle_saldo_rol->valor_cuota,
            'saldo_cobrad' => 0
        ]);
        $detalle_saldo_rol->delete();

    }

    public function recargar_saldo_rol($id_nomina, $id_rol){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nomina = Ct_Nomina::find($id_nomina); 

        $rol = Ct_Rol_Pagos::find($id_rol);
        $mes = $rol->mes;
        $anio = $rol->anio;

        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $nomina->id_user)
                ->where('id_empresa', $nomina->id_empresa)
                ->where('estado','1')
                ->where('saldo_cobrad','0')
                ->get(); //dd($saldos);  

        $total_cuota_ini = 0;    
        foreach($saldos as $saldo){
            $total_cuota_ini += $saldo->valor_cuota;
            $cuotas = $saldo->detalles()->where('estado',1)->where('estado_pago',1)->count();//dd($cuotas);
            $xanio   = $saldo->anio_inicio_cobro;
            $xmes    = $saldo->mes_inicio_cobro;
            $xmes    = $xmes + $cuotas;
            if($mes > 12){
                $xmes = 1;
                $xanio ++;
            }
            $cuotas++;
            if($cuotas < $saldo->num_cuotas){
                Ct_Rh_Saldos_Iniciales_Detalle::create([
                    'id_ct_rh_saldos_iniciales' => $saldo->id,
                    'anio'               => $anio,
                    'mes'                => $mes,
                    'fecha'              => date('Y-m-d H:i:s'),
                    'cuota'              => $cuotas,
                    'valor_cuota'        => $saldo->valor_cuota,
                    'id_ct_rol_pagos'    => $id_rol,
                    'estado'             => '1',
                    'estado_pago'        => '1',
                    'fecha_pago'         => date('Y-m-d H:i:s'),
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                ]);
                $saldo_prestamo = $saldo->saldo_res;
                $saldo_prestamo -= $saldo->valor_cuota;
                $saldo->update([
                    'saldo_res' => $saldo_prestamo, 
                ]);
            }    
            if($cuotas >= $saldo->num_cuotas){
                $saldo->update([
                    'saldo_cobrad' => '1', 
                ]);    
            }

        }       

    } 

    public function eliminar_anticipo_rol($id){

        $anticipo_rol = Ct_Rh_Otros_Anticipos::find($id);
        $anticipo_rol->update([
            'id_ct_rol' => null
        ]);

    }

    public function recargar_anticipo_rol($id_nomina, $id_rol){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nomina = Ct_Nomina::find($id_nomina); 
        $rol = Ct_Rol_Pagos::find($id_rol);
        //OTROS ANTICIPOS
        $anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $nomina->id_user)
        ->where('id_empresa', $nomina->id_empresa)
        ->where('mes_cobro_anticipo', $rol->mes)
        ->where('anio_cobro_anticipo', $rol->anio)
        ->where('estado', '1')
        ->whereNull('id_ct_rol')
        ->get();
        $total_otros_anticipos = 0;    
        foreach($anticipos as $anticipo){
            $total_otros_anticipos += $anticipo->monto_anticipo;
            $anticipo->update(['id_ct_rol' => $id_rol]);

        }        

    }   

    public function cargar_cuota_quirografario($id_rol, Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       
        $arr_quiro = [

            'id_rol'          => $id_rol,
            'valor_cuota'     => $request->cuota_quiro,
            'detalle_cuota'   => $request->detalle_quiro,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        Ct_Rh_Cuotas_Quirografario::insert($arr_quiro);

    } 

    public function cargar_cuota_hipotecario($id_rol, Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       
        $arr_quiro = [

            'id_rol'          => $id_rol,
            'valor_cuota'     => $request->cuota_hipo,
            'detalle_cuota'   => $request->detalle_hipo,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        Ct_Rh_Cuotas_Hipotecarios::insert($arr_quiro);

    } 

    public function eliminar_cuota_quiro($id){

        $cuota_quiro = Ct_Rh_Cuotas_Quirografario::find($id);
        $cuota_quiro->delete();

    }

    public function eliminar_cuota_hipo($id){

        $cuota_hipo = Ct_Rh_Cuotas_Hipotecarios::find($id);
        $cuota_hipo->delete();

    }

    public function forma_pago_store(Request $request){
    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        Ct_Rol_Forma_Pago::create([

            'id_rol_pago'     => $request->id_rol,
            'id_tipo_pago'    => $request->tipo_pago,
            'fecha'           => date('Y-m-d H:i:s'),
            'banco'           => $request->banco,
            'numero_cuenta'   => $request->numero_cuenta,
            'num_cheque'      => $request->num_cheque,
            'estado'          => 1,  
            'valor'           => $request->valor_forma_pago,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ]);

    }

    public function editar_nuevo_rol($id_rol){

        $rol = Ct_Rol_Pagos::find($id_rol); 

        $nomina = $rol->ct_nomina;  

        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first();//dd($val_fond_reserv);
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        $detalle_rol = $rol->detalle->first();

        $prestamos_rol = Ct_Rh_Prestamos_Detalle::where('id_ct_rol_pagos',$rol->id)->get();

        $saldos_ini = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rol_pagos',$rol->id)->get();

        $otros_anticipos = Ct_Rh_Otros_Anticipos::where('id_ct_rol',$rol->id)->get();

        $cuotas_quiro = Ct_Rh_Cuotas_Quirografario::where('id_rol',$rol->id)->get();

        $cuotas_hipo = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $rol->id)->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all(); 

        $lista_banco = Ct_Bancos::all();

        $forma_pago = $rol->formas_de_pago->where('estado','1');

        return view('contable.rol_pago.editnew',['rol' => $rol, 'nomina' => $nomina, 'val_fond_reserv' => $val_fond_reserv, 'val_sal_basico' => $val_sal_basico, 'form_pago' => null, 'ct_tipo_rol' => $ct_tipo_rol, 'detalle_rol' => $detalle_rol, 'forma_pago' => $forma_pago, 'prestamos_rol' => $prestamos_rol, 'saldos_ini' => $saldos_ini, 'otros_anticipos' => $otros_anticipos, 'cuotas_hipo' => $cuotas_hipo, 'cuotas_quiro' => $cuotas_quiro, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco]); 
    }


    public function eliminar_rol($id)
    {
        
        //Obtenemos la fecha de Hoy
        $fecha_actual = Date('Y-m-d H:i:s');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $rol = Ct_Rol_Pagos::find($id);

        $rol->update($act_estado = [
                'estado' => '0',
            ]);
        

        $detalle_prestamo_rol = Ct_Rh_Prestamos_Detalle::where('id_ct_rol_pagos',$id)->get();

        foreach ($detalle_prestamo_rol as $det_prestamo) {
            $prestamo_rol = $det_prestamo->prestamos;
            $prestamo_rol->update([
                'saldo_total' => $prestamo_rol->saldo_total + $det_prestamo->valor_cuota,
                'prest_cobrad' => 0
            ]);
            $det_prestamo->delete();
        }


        $detalle_saldo_rol = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rol_pagos',$id)->get();

        foreach ($detalle_saldo_rol as $det_saldo) {
            $saldo_rol = $det_saldo->saldos;
            $saldo_rol->update([
                'saldo_res' => $saldo_rol->saldo_inicial + $det_saldo->valor_cuota,
                'saldo_cobrad' => 0
            ]);
            $det_saldo->delete();
        }

        return "ok";
        
    }

    public function masivo_prestamo_saldo(){

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;


        //$prestamos = Ct_Rh_Prestamos::where('anio_inicio_cobro', '2021')->whereNull('leido')->get();

        $prestamos = Ct_Rh_Prestamos::where('anio_inicio_cobro', '2021')
            ->where('leido','1')
            ->where('estado','1')
            ->where('prest_cobrad','1')
            ->get();

        //$saldos = Ct_Rh_Saldos_Iniciales::where('anio_inicio_cobro', '2021')->whereNull('leido')->get();

        $saldos = Ct_Rh_Saldos_Iniciales::where('anio_inicio_cobro', '2021')
                ->where('leido','1')
                ->where('estado','1')
                ->where('saldo_cobrad','1')
                ->get();  

        foreach($prestamos as $p){
            
                $xmes = $p->mes_inicio_cobro;
                $cont = 0;
                $tot_cuota = 0;
                while($xmes <= 9){  
                    $cont++; 
                    $tot_cuota += $p->valor_cuota;

                        $rol = Ct_Rol_Pagos::where('mes', $xmes)->where('anio','2021')->where('id_user',$p->id_empl)->first();
                        if (!is_null($rol)) {
                            Ct_Rh_Prestamos_Detalle::create([
                                'id_ct_rh_prestamos'    => $p->id,
                                'anio'                  => 2021,
                                'mes'                   => $xmes,
                                'fecha'                 => date('Y-m-d H:i:s'),
                                'valor_cuota'           => $p->valor_cuota,
                                'id_ct_rol_pagos'       => $rol->id,
                                'cuota'                 => $cont,
                                'estado'                => '1',
                                'estado_pago'           => '1',
                                'fecha_pago'            => date('Y-m-d H:i:s'),
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                                'ip_modificacion'       => $ip_cliente,
                            ]);
                        }

                    if ($tot_cuota >= $p->monto_prestamo) {
                        // code...
                       break;
                    }

                    $xmes++;            
            }

            $arr_prestamo = [
                'leido'             => 2,
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
                'saldo_sistema'     => $p->monto_prestamo - $tot_cuota,
            ];

            $p->update($arr_prestamo);

           
        }

        foreach($saldos as $s){
         
                $mes_saldo = $s->mes_inicio_cobro;
                $aux = 0;
                $total_cuota = 0;
                while ($mes_saldo <= 9) {
                    $aux++;
                    $total_cuota += $s->valor_cuota;
                    $rol = Ct_Rol_Pagos::where('mes', $mes_saldo)->where('anio','2021')->where('id_user',$s->id_empl)->first();
                    if (!is_null($rol)) {
                        Ct_Rh_Saldos_Iniciales_Detalle::create([
                            'id_ct_rh_saldos_iniciales' => $s->id,
                            'anio'               => 2021,
                            'mes'                => $mes_saldo,
                            'fecha'              => date('Y-m-d H:i:s'),
                            'cuota'              => $aux++,
                            'valor_cuota'        => $s->valor_cuota,
                            'id_ct_rol_pagos'    => $rol->id,
                            'estado'             => '1',
                            'estado_pago'        => '1',
                            'fecha_pago'         => date('Y-m-d H:i:s'),
                            'id_usuariocrea'     => $idusuario,
                            'id_usuariomod'      => $idusuario,
                            'ip_crea'            => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,
                        ]);
                    }
                    

                    if ($total_cuota >= $s->saldo_inicial) {
                        // code...
                        break;
                    }

                    $mes_saldo++; 

                }

                $arr_saldo = [
                    'leido'             => 2,
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                    'saldo_sistema'     => $s->saldo_inicial - $total_cuota,
                ];

                $s->update($arr_saldo);
            

        }
    } 

    public function masivo_anticipos(){

        $anticipos = Ct_Rh_Otros_Anticipos::where('estado','1')->get();
        foreach($anticipos as $anticipo){
            $rol = Ct_Rol_Pagos::where('mes', $anticipo->mes_cobro_anticipo)->where('anio','2021')->where('id_user',$anticipo->id_empl)->first();
            if(!is_null($rol)){
                $anticipo->update([
                    'id_ct_rol' => $rol->id,
                ]);
            }
                
        }

    }

    public function index_prestamos_saldos($id_user){

        $user = User::find($id_user);

        $prestamos = Ct_Rh_Prestamos::where('id_empl',$id_user)->get();

        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl',$id_user)->get();


        return view('contable/nuevo_prestamo_saldos/index',['prestamos' =>$prestamos, 'saldos' => $saldos, 'user' => $user]);

    }

    public function masivo_search(Request $request){

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $empleado   = $request->empleado;

        if($id_empresa==null){
            $empresa    = Empresa::where('prioridad','1')->first();
            $id_empresa = $empresa->id;
        }
        if($anio==null){
            $anio = date('Y');    
        }
        if($mes==null){
            $mes = date('m');
        }
        
        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado','1')->where('ct_rol_pagos.anio',$anio)->where('ct_rol_pagos.mes',$mes)->where('ct_rol_pagos.id_empresa',$id_empresa)->join('ct_nomina as nom','nom.id','ct_rol_pagos.id_nomina')->select('ct_rol_pagos.*','nom.nombres');

        if($empleado!=null){
            $roles = $roles->where('nom.nombres','like','%'.$empleado.'%');
        }

        $roles = $roles->get();

        //dd($roles);

        $empresas = Empresa::where('estado','1')->get();

        $procesos = Ct_Rol_Proceso::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->get();

        return view('contable.nuevo_rol_pago.masivo_rol',[ 'roles' => $roles, 'anio' => $anio, 'mes' => $mes, 'id_empresa' => $id_empresa, 'empresas' => $empresas, 'empleado' => $empleado, 'err' => null, 'procesos' => $procesos ]);

    }

    public function masivo_genera_roles(Request $request){

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;

        //dd($request->all());
        $fecha      = date('Y-m-d H:i:s');

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nominas = Ct_Nomina::where('estado','1')->where('id_empresa',$id_empresa)->get();
        $err = null;
        //quite x crear roles pasados en gastroquito habilitar luego de que este todo ok
        // if($anio < date('Y')){
        //     $err = "El año seleccionado es menor al actual";
        // }else{
        //     if($anio == date('Y')){
        //         if($mes < date('m')){
        //             $err = "El mes seleccionado es menor al actual";
        //         }
        //     }
        // }

        if($err == null){
            $procesadas = 0; $no_procesadas = 0;

            $id_proceso = Ct_Rol_Proceso::insertGetId([
                'id_empresa'    => $id_empresa,
                'anio'          => $anio,
                'mes'           => $mes,
                'tipo_proceso'  => 'ROL_POR_EMPRESA',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ]);

            foreach($nominas as $nomina){

                $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)->where('tipo', 4)->first();//dd($val_fond_reserv);
                //NO SIEMPRE EXISTE VALIDAR
                $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)->where('tipo', 3)->first();
                $rol = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                    ->where('ct_rol_pagos.anio', $anio)
                    ->where('ct_rol_pagos.mes', $mes)
                    ->where('ct_rol_pagos.id_nomina', $nomina->id)
                    ->first(); 
                if($nomina->aportepersonal == null){
                    $no_procesadas ++;    
                }else{          

                    if (is_null($rol)) {
                   
                        $procesadas ++;
                        $id_rol = Ct_Rol_Pagos::insertGetId([
                            'id_nomina'         => $nomina->id,
                            'id_user'           => $nomina->id_user,
                            'id_empresa'        => $nomina->id_empresa,
                            'anio'              => $anio,
                            'mes'               => $mes,
                            'id_tipo_rol'       => '1',
                            'fecha_elaboracion' => $fecha,
                            'estado'            => '1',
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'certificado'       => 0,
                            'id_proceso'        => $id_proceso,
                        ]);
                    
                        //PRESTAMOS
                        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)
                            ->where('id_empresa', $nomina->id_empresa)
                            ->where('estado','1')
                            ->where('prest_cobrad','0')
                            //->where('anio_inicio_cobro','<=',$anio)
                            //->where('mes_inicio_cobro','<=',$mes)
                            ->where(function ($query) use($anio,$mes){
                                $query->where('anio_inicio_cobro', '<', $anio)//2021 < 2022
                                    ->orwhere(function ($query2) use($anio,$mes){
                                        $query2->where('anio_inicio_cobro', '=', $anio)//2022 = 2022
                                            ->Where('mes_inicio_cobro', '<=', $mes);// ene <= ene   
                                });  
                            })
                            ->get();   

                        $total_cuota = 0;    
                        foreach($prestamos as $prestamo){
                            $total_cuota += $prestamo->valor_cuota;
                            $cuotas = $prestamo->detalles()->where('estado',1)->where('estado_pago',1)->count();//dd($cuotas);
                            $xanio   = $prestamo->anio_inicio_cobro;
                            $xmes    = $prestamo->mes_inicio_cobro;
                            $xmes    = $xmes + $cuotas;
                            if($xmes > 12){
                                $xmes = 1;
                                $xanio ++;
                            }
                            $cuotas++;
                            if($cuotas <= $prestamo->num_cuotas){
                                Ct_Rh_Prestamos_Detalle::create([
                                    'id_ct_rh_prestamos' => $prestamo->id,
                                    //'anio'               => $xanio,
                                    //'mes'                => $xmes,
                                    'anio'               => $anio,
                                    'mes'                => $mes,
                                    'fecha'              => date('Y-m-d H:i:s'),
                                    'cuota'              => $cuotas,
                                    'valor_cuota'        => $prestamo->valor_cuota,
                                    'id_ct_rol_pagos'    => $id_rol,
                                    'estado'             => '1',
                                    'estado_pago'        => '1',
                                    'fecha_pago'         => date('Y-m-d H:i:s'),
                                    'id_usuariocrea'     => $idusuario,
                                    'id_usuariomod'      => $idusuario,
                                    'ip_creacion'            => $ip_cliente,
                                    'ip_modificacion'    => $ip_cliente,
                                ]);
                                $saldo_prestamo = $prestamo->saldo_total;
                                $saldo_prestamo -= $prestamo->valor_cuota;
                                $prestamo->update([
                                    'saldo_total' => $saldo_prestamo, 
                                ]);
                            }    
                            if($cuotas >= $prestamo->num_cuotas){
                                $prestamo->update([
                                    'prest_cobrad' => '1', 
                                ]);    
                            }

                        } 
                        //SALDOS
                        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $nomina->id_user)
                            ->where('id_empresa', $nomina->id_empresa)
                            ->where('estado','1')
                            ->where('saldo_cobrad','0')
                            //->where('anio_inicio_cobro','<=',$anio)
                            //->where('mes_inicio_cobro','<=',$mes)
                            ->where(function ($query) use($anio,$mes){
                                $query->where('anio_inicio_cobro', '<', $anio)//2021 < 2022
                                    ->orwhere(function ($query2) use($anio,$mes){
                                        $query2->where('anio_inicio_cobro', '=', $anio)//2022 = 2022
                                            ->orWhere('mes_inicio_cobro', '<=', $mes);// ene <= ene   
                                });  
                            })
                            ->get(); //dd($saldos);  

                        $total_cuota_ini = 0;    
                        foreach($saldos as $saldo){
                            $total_cuota_ini += $saldo->valor_cuota;
                            $cuotas = $saldo->detalles()->where('estado',1)->where('estado_pago',1)->count();//dd($cuotas);
                            $xanio   = $saldo->anio_inicio_cobro;
                            $xmes    = $saldo->mes_inicio_cobro;
                            $xmes    = $xmes + $cuotas;
                            if($xmes > 12){
                                $xmes = 1;
                                $xanio ++;
                            }
                            $cuotas++;
                            if($cuotas < $saldo->num_cuotas){
                                Ct_Rh_Saldos_Iniciales_Detalle::create([
                                    'id_ct_rh_saldos_iniciales' => $saldo->id,
                                    //'anio'               => $xanio,
                                    //'mes'                => $xmes,
                                    'anio'               => $anio,
                                    'mes'                => $mes,
                                    'fecha'              => date('Y-m-d H:i:s'),
                                    'cuota'              => $cuotas,
                                    'valor_cuota'        => $saldo->valor_cuota,
                                    'id_ct_rol_pagos'    => $id_rol,
                                    'estado'             => '1',
                                    'estado_pago'        => '1',
                                    'fecha_pago'         => date('Y-m-d H:i:s'),
                                    'id_usuariocrea'     => $idusuario,
                                    'id_usuariomod'      => $idusuario,
                                    'ip_creacion'            => $ip_cliente,
                                    'ip_modificacion'    => $ip_cliente,
                                ]);
                                $saldo_prestamo = $saldo->saldo_res;
                                $saldo_prestamo -= $saldo->valor_cuota;
                                $saldo->update([
                                    'saldo_res' => $saldo_prestamo, 
                                ]);
                            }    
                            if($cuotas >= $saldo->num_cuotas){
                                $saldo->update([
                                    'saldo_cobrad' => '1', 
                                ]);    
                            }

                        } 

                        //OTROS ANTICIPOS
                        $anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $nomina->id_user)
                        ->where('id_empresa', $nomina->id_empresa)
                        ->where('mes_cobro_anticipo', $mes)
                        ->where('anio_cobro_anticipo', $anio)
                        ->where('estado', '1')
                        ->get();
                        $total_otros_anticipos = 0;    
                        foreach($anticipos as $anticipo){
                            $total_otros_anticipos += $anticipo->monto_anticipo;
                            $anticipo->update(['id_ct_rol' => $id_rol]);

                        }   

                        //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
                        $dias_laborados = 30;$sobre_tiempo_50 = 0;$sobre_tiempo_100 = 0;$cantidad_horas50 = 0;$cantidad_horas100 = 0;
                        $bonificacion = 0;$transporte = 0;$exam_laboratorio = 0;$fondo_reserva = 0;$decimo_tercero = 0;$decimo_cuarto = 0;
                        $multa = 0;$impuesto_renta = 0;$total_quota_quirog = 0; $total_quota_hipot = 0;$fond_reserv_cobrar = 0;$fondo_reserva_acumulado = 0;
                        $sueldo_mensual     = $nomina->sueldo_neto;
                        $impuesto_renta     = $nomina->impuesto_renta;
                        $bono_imputable     = $nomina->bono_imputable;
                        $alimentacion       = $nomina->alimentacion;
                        $fecha_ingreso      = $nomina->fecha_ingreso;
                        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
                        $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
                        $tipo_decimo_tercero     = $nomina->decimo_tercero;
                        $aporte_personal         = $nomina->aportepersonal->valor;
                        $seguro_privado          = $nomina->seguro_privado;
                        $anticipo_quincena       = $nomina->val_anticip_quince;
                        $parqueo                 = $nomina->parqueo;
                        $bonificacion            = $nomina->bono;

                        $a15 = Ct_Rh_Valor_Anticipos::where('id_user',$nomina->id_user)->where('id_empresa',$nomina->id_empresa)->where('anio',$anio)->where('mes',$mes)->first();
                        if(!is_null($a15)){
                            $anticipo_quincena = $a15->valor_anticipo;
                        }
                        $pct_fondo_reserva = 0; $valor_sueldo_basico = 0;
                        if(!is_null($val_fond_reserv)){
                            $pct_fondo_reserva   = $val_fond_reserv->valor;
                        }
                        if(!is_null($val_sal_basico)){
                            $valor_sueldo_basico = $val_sal_basico->valor;
                        }    
                         
                        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
                        $sueldo_mensual = $sueldo_mensual / 30;
                        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
                        $sueldo_mensual = round($sueldo_mensual,2);
                    
                        //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
                        $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

                        //4:FONDO DE RESERVA
                        $fecha         = date("Y-m-d");
                        $fec           = new DateTime($fecha);
                        $fec2          = new DateTime($fecha_ingreso);
                        $diff          = $fec->diff($fec2); //dd($diff);
                        $intervalMeses = $diff->format("%m");
                        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
                        $añosAhora    = $diff->format("%y"); //dd($añosAhora);
                        $intervalDias  = $diff->format("%d");
                        $diaActual     = $fec->format("d");
                        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
                        if ($añosAhora > 0) {
                            if ($tipo_fondo_reserva == 2) { //mensualiza
                                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                            } 
                            ////////////////////////ROL DE PAGO ACUMULADO PARA ASIENTOS (3)////////////////
                            else{
                                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                                $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);    
                            }
                            ///////////////////////////////////////////////////////////////////////////////
                        }
                        $fondo_reserva = round($fondo_reserva,2);

                        //5:DECIMO TERCER
                        if($tipo_decimo_tercero == 2){
                            $decimo_tercero = $base_iess / 12;    
                        }
                        $decimo_tercero = round($decimo_tercero,2);

                        //6:DECIMO CUARTO
                        if($tipo_decimo_cuarto == 2){
                            $decimo_cuarto = $valor_sueldo_basico / 12;    
                        }
                        $decimo_cuarto = round($decimo_cuarto,2);

                        //7:PORCENTAJE IESS
                        $porcentaje_iess = $base_iess * $aporte_personal / 100;
                        $porcentaje_iess = round($porcentaje_iess,2);

                        $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto +$alimentacion + $transporte;
                        $total_ingresos = round($total_ingresos,2);


                        $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $parqueo;
                        $total_egresos = round($total_egresos,2);

                        $neto_recibir = $total_ingresos - $total_egresos;

                        //Guardado en la Tabla Ct_Detalle_Rol
                        Ct_Detalle_Rol::create([
                            'id_rol'                      => $id_rol,
                            'dias_laborados'              => $dias_laborados,
                            'base_iess'                   => $base_iess,
                            'sueldo_mensual'              => $sueldo_mensual,
                            'cantidad_horas50'            => $cantidad_horas50,
                            'sobre_tiempo50'              => $sobre_tiempo_50,
                            'cantidad_horas100'           => $cantidad_horas100,
                            'sobre_tiempo100'             => $sobre_tiempo_100,
                            'bonificacion'                => $bonificacion,
                            'alimentacion'                => $alimentacion,
                            'transporte'                  => $transporte,
                            'bono_imputable'              => $bono_imputable,
                            'exam_laboratorio'            => $exam_laboratorio,
                            'fondo_reserva'               => $fondo_reserva,
                            'decimo_tercero'              => $decimo_tercero,
                            'decimo_cuarto'               => $decimo_cuarto,
                            'porcentaje_iess'             => $porcentaje_iess,
                            'seguro_privado'              => $seguro_privado,
                            'impuesto_renta'              => $impuesto_renta,
                            'multa'                       => $multa,
                            'fond_reserv_cobrar'          => $fond_reserv_cobrar,
                            'otros_egresos'               => 0,
                            'prestamos_empleado'          => $total_cuota,
                            'saldo_inicial_prestamo'      => $total_cuota_ini,
                            'anticipo_quincena'           => $anticipo_quincena,
                            'otro_anticipo'               => $total_otros_anticipos,
                            'total_quota_quirog'          => $total_quota_quirog,
                            'total_quota_hipot'           => $total_quota_hipot,
                            'total_ingresos'              => $total_ingresos,
                            'total_egresos'               => $total_egresos,
                            'neto_recibido'               => $neto_recibir,
                            'id_usuariocrea'            => $idusuario,
                            'id_usuariocrea'            => $idusuario,
                            'id_usuariomod'             => $idusuario,
                            'ip_creacion'               => $ip_cliente,
                            'ip_modificacion'           => $ip_cliente,
                            'parqueo'                   => $parqueo,
                            'fondo_reserva_acumulado'   => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (3)
                        ]);

                        $rol = Ct_Rol_Pagos::find($id_rol);

                    }
                    else{
                        $no_procesadas ++;
                    }
                }    
            }

            $proceso = Ct_Rol_Proceso::find($id_proceso);

            $proceso->update([
                'procesados' => $procesadas,
                'no_procesados' => $no_procesadas,
            ]);
        }else{
            $err = "No se puede procesar Roles anteriores al mes actual";
        }     

        return "ok";   

    }

    public function excel_prestamos_saldos($id_user){

        $user = User::find($id_user);
        $prestamos = Ct_Rh_Prestamos::where('id_empl',$id_user)->get();
        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl',$id_user)->get();

        $titulos = array("FECHA CREACION", "CONCEPTO", "NUMERO CUOTA", "VALOR CUOTA", "MES/AÑO INICIO COBRO", "MES/AÑO FIN COBRO", "MONTO PRESTAMO", "VALOR A PAGAR", "ESTADO");

        $titulos2 = array("FECHA CREACION", "CONCEPTO", "NUMERO CUOTA", "VALOR CUOTA", "MES/AÑO INICIO COBRO", "MES/AÑO FIN COBRO", "SALDO INICIAL", "VALOR A PAGAR", "ESTADO");

        
        //Posiciones en el excel
        $posicion = array("A","B","C","D","E","F","G","H", "I");

        Excel::create('Reporte_'.$user->apellido1.' '.$user->nombre1, function ($excel) use ($titulos, $posicion, $prestamos, $saldos, $user, $titulos2) {
            $excel->sheet('Reporte_'.$user->apellido1.' '.$user->nombre1, function ($sheet) use ($titulos, $posicion, $prestamos, $saldos, $user, $titulos2) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS - SALDOS DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B2', function ($cell) use($user) {
                    // manipulate the cel
                    $cell->setValue($user->id);
                    $cell->setAlignment('right');
                    
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });


                $sheet->mergeCells('D2:E2');
                $sheet->cell('D2', function ($cell) use($user) {
                    // manipulate the cel
                    $cell->setValue($user->apellido1.' '.$user->apellido2.' '.$user->nombre1.' '.$user->nombre2);
                    $cell->setAlignment('right');
               
                });

                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });


                $comienzo =5; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for($i = 0 ; $i<count($titulos); $i++){
                    $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo ++;
                /*****FIN DE TITULOS DEL EXCEL***********/

                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $titulo_detallep = array("AÑO","MES", "CUOTA", "VALOR CUOTA");
                $acum_prestamos = 0;
                foreach ($prestamos as $prestamo) {

                    $detalle_prestamo = Ct_Rh_Prestamos_Detalle::where('id_ct_rh_prestamos', $prestamo->id)->get();
                    $datos_excel = array();

                    if ($prestamo->estado) {
                        $acum_prestamos += $prestamo->saldo_total;
                        if($prestamo->prest_cobrad){
                            $estado = 'PAGADO';
                        }else{
                            $estado = 'Activo';
                        }    
                    }
                    else{
                        $estado = 'Inactivo';
                    }


                    $ms = intval($prestamo->mes_inicio_cobro)-1;
                    $m = intval($prestamo->mes_fin_cobro)-1;

                    array_push($datos_excel, $prestamo->fecha_creacion, $prestamo->concepto, $prestamo->num_cuotas, '$'.$prestamo->valor_cuota, $meses[$ms].'-'.$prestamo->anio_inicio_cobro, $meses[$m].'-'.$prestamo->anio_fin_cobro, '$'.$prestamo->monto_prestamo, '$'.$prestamo->saldo_total,$estado);

                    for($i = 0 ; $i<count($datos_excel); $i++){
                            $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }

                    $comienzo++;

                    if($detalle_prestamo->count() > 0){
                        for($i = 0 ; $i<count($titulo_detallep); $i++){
                            $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use ($titulo_detallep, $i) {
                                $cell->setValue($titulo_detallep[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;

                        $tot_det_prest = 0;
                        foreach($detalle_prestamo as $det_prest){

                            $datos_detalle = array();
                            $mesd = intval($det_prest->mes)-1;
                            $tot_det_prest += $det_prest->valor_cuota;
                        
                            array_push($datos_detalle, $det_prest->anio, $meses[$mesd], $det_prest->cuota, '$'.$det_prest->valor_cuota);

                            for($i = 0 ; $i<count($datos_detalle); $i++){
                                $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use($datos_detalle, $i) {
                                $cell->setValue($datos_detalle[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                                });
                            
                            }
                            $comienzo++;

                        }
                        $sheet->cell('C'.$comienzo, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL');
                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                           
                        });

                        $sheet->cell('D'.$comienzo, function ($cell) use($tot_det_prest) {
                            // manipulate the cel
                            $cell->setValue('$'.$tot_det_prest);
                            $cell->setAlignment('center');
                            
                        });
                        $comienzo++;
                    }    

                }

                $sheet->cell('G'.$comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PRESTAMOS');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });
                $sheet->cell('H'.$comienzo, function ($cell) use($acum_prestamos) {
                    // manipulate the cel
                    $cell->setValue('$ '.$acum_prestamos);
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });

                $comienzo++;

                $sheet->cell('A'.$comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDOS INICIALES');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });
                $comienzo++;


                for($i = 0 ; $i<count($titulos2); $i++){
                    $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use ($titulos2, $i) {
                        $cell->setValue($titulos2[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo ++;

                $acum_saldo = 0;
                foreach ($saldos as $saldo) {

                    $detalle_saldos = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rh_saldos_iniciales',$saldo->id)->get();
                    $datos_saldo = array();

                    if ($saldo->estado) {
                        $acum_saldo += $saldo->saldo_res;
                        if($saldo->saldo_cobrad){
                            $estado = 'PAGADO';
                        }else{
                            $estado = 'Activo';
                        }    
                    }
                    else{
                        $estado = 'Inactivo';
                    }
                    

                    $msi = intval($saldo->mes_inicio_cobro) - 1;
                    $mi = intval($saldo->mes_fin_cobro) - 1 ;


                    array_push($datos_saldo, $saldo->fecha_creacion, $saldo->observacion, $saldo->num_cuotas, '$'.$saldo->valor_cuota, $meses[$msi].'-'.$saldo->anio_inicio_cobro, $meses[$mi].'-'.$saldo->anio_fin_cobro, '$'.$saldo->saldo_inicial, '$'.$saldo->saldo_res, $estado);

                    

                    for($i = 0 ; $i<count($datos_saldo); $i++){
                            $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use($datos_saldo, $i) {
                            $cell->setValue($datos_saldo[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                    if($detalle_saldos->count() > 0){
                        for($i = 0 ; $i<count($titulo_detallep); $i++){
                            $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use ($titulo_detallep, $i) {
                                $cell->setValue($titulo_detallep[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;

                        $tot_det_saldo = 0;
                        foreach($detalle_saldos as $det_saldo){

                            $datos_detalle_s = array();
                            $tot_det_saldo += $det_saldo->valor_cuota;
                            $mss = intval($det_saldo->mes)-1;
                        
                            array_push($datos_detalle_s, $det_saldo->anio, $meses[$mss], $det_saldo->cuota, '$'.$det_saldo->valor_cuota);

                            for($i = 0 ; $i<count($datos_detalle_s); $i++){
                                $sheet->cell(''.$posicion[$i].''.$comienzo, function ($cell) use($datos_detalle_s, $i) {
                                $cell->setValue($datos_detalle_s[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                                });
                            
                            }
                            $comienzo++;                      

                        }
                        $sheet->cell('C'.$comienzo, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL');
                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                           
                        });

                        $sheet->cell('D'.$comienzo, function ($cell) use($tot_det_saldo) {
                            // manipulate the cel
                            $cell->setValue('$'.round($tot_det_saldo,2));
                            $cell->setAlignment('center');
                            
                           
                        });
                    }    
                    $comienzo++;

                }
                $comienzo++;
                $sheet->cell('G'.$comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL SALDOS');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });
                $sheet->cell('H'.$comienzo, function ($cell) use($acum_saldo) {
                    // manipulate the cel
                    $cell->setValue('$ '.$acum_saldo);
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });
                $total_pago = $acum_saldo + $acum_prestamos; 

                $comienzo++;
                $sheet->cell('G'.$comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL A PAGAR');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });
                $sheet->cell('H'.$comienzo, function ($cell) use($total_pago) {
                    // manipulate the cel
                    $cell->setValue('$ '.number_format($total_pago, 2, '.', ' '));
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                   
                });

            });
        })->export('xlsx');
    }

    public function masivo_certificar($id, $cert){

        $rol = Ct_Rol_Pagos::find($id);

        $rol->update([
            'certificado' => $cert,
        ]);

        return "ok";
    }

    public function masivo_certificar_mes(Request $request){

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;

        $roles = Ct_Rol_Pagos::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->where('estado','1')->where('certificado','0')->get();
        
        foreach($roles as $rol){
            $rol->update([
                'certificado' => '1',
            ]);
        } 

        return "ok";

    }

    public function masivos_horario_extra(Request $request){
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];

        $proceso = Ct_Rol_Proceso::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->where('tipo_proceso','ROL_POR_EMPRESA')->first();

        if(is_null($proceso)){
            return ['tipo' => 'err', 'mensaje' => 'Debe generar los roles primero'];
        }

        $fecha_actual    = Date('Y-m-d H:i:s');
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "he_".$id_empresa.'_'.$anio.'_'.$mes.'_'.date('YmdHis').".".$extension;

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
        $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
        $prestamo = null;
        $id_proceso = Ct_Rol_Proceso::insertGetId([
            'id_empresa'    => $id_empresa,
            'anio'          => $anio,
            'mes'           => $mes,
            'tipo_proceso'  => 'HORAS_EXTRAS_POR_EMPRESA',
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ]);
        $procesadas=0;$no_procesadas=0;

        if ($r1) {
            Excel::filter('chunk')
                ->formatDates(true, 'Y-m-d')
                ->load($rutadelaimagen)
                ->chunk(250, function ($reader) use ($idusuario, $anio, $mes, $id_empresa, $fecha_actual, $ip_cliente, $nombre_original, $id_proceso, $procesadas, $no_procesadas) {

                    foreach ($reader as $book) {
                        //dd($book);
                        if (!is_null($book)) {
                            
                            $continuar = true;

                            if (is_null($book->cedula)) {
                                $arr_det_log =[
                                    'id_empleado'           => $book->cedula,
                                    'nombre'                => $book->colaborador,
                                    'sueldo'                => $book->sueldo,
                                    'valor_horas50'         => $book->valor_horas50,
                                    'valor_horas100'        => $book->valor_horas100,
                                    'num_horas50'           => $book->num_horas50,
                                    'num_horas100'          => $book->num_horas100,
                                    'total50'               => $book->total50,
                                    'total100'              => $book->total100,
                                    'total'                 => $book->total,
                                    'id_usuariocrea'        => $idusuario,
                                    'id_usuariomod'         => $idusuario,
                                    'ip_creacion'           => $ip_cliente,
                                    'ip_modificacion'       => $ip_cliente,
                                    'id_proceso'            => $id_proceso,
                                    'detalle_resultado'     => 'REGISTRO SIN NUMERO DE CEDULA'
                                ];

                                $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                $no_procesadas++;$continuar=false;    
                            }
                             
                            if($continuar){    
                                $nomina = Ct_Nomina::where('id_user',$book->cedula)->where('id_empresa',$id_empresa)->first();
                                if(is_null($nomina)){

                                    $arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO NO EXISTE EN LA NOMINA'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;

                                }
                            }    
                            //dd("h",$book);
                            if($continuar){

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

                                if(is_null($rol)){
                                    $arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO SIN ROL GENERADO'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;
                                }


                            }

                            if($continuar){

                                if(is_null($book->num_horas50)){
                                    $arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO SIN VALOR DE HORAS EXTRAS AL 50%'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if($continuar){

                                if(!is_numeric($book->num_horas50)){
                                    $arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'ERROR EN FORMATO DEL VALOR DE HORAS EXTRAS AL 50%'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if($continuar){

                                if(is_null($book->num_horas100)){
                                    $arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO SIN VALOR DE HORAS EXTRAS AL 100%'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if($continuar){

                                if(!is_numeric($book->num_horas100)){
                                    $arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'ERROR EN FORMATO DEL VALOR DE HORAS EXTRAS AL 100%'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if($continuar){

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

                                if (!is_null($rol)) {

                                    $detalle_rol = $rol->detalle->first();

                                    if (!is_null($detalle_rol)) {

                                        $arr_det = [
                                            'cantidad_horas50'  => $book->num_horas50,
                                            'cantidad_horas100' => $book->num_horas100,
                                        ];

                                        $detalle_rol->update($arr_det);

                                        $this->recalcular_db($rol->id);
                                        $arr_det_log =[
                                            'id_empleado'           => $book->cedula,
                                            'nombre'                => $book->colaborador,
                                            'sueldo'                => $book->sueldo,
                                            'valor_horas50'         => $book->valor_horas50,
                                            'valor_horas100'        => $book->valor_horas100,
                                            'num_horas50'           => $book->num_horas50,
                                            'num_horas100'          => $book->num_horas100,
                                            'total50'               => $book->total50,
                                            'total100'              => $book->total100,
                                            'total'                 => $book->total,
                                            'id_usuariocrea'        => $idusuario,
                                            'id_usuariomod'         => $idusuario,
                                            'ip_creacion'           => $ip_cliente,
                                            'ip_modificacion'       => $ip_cliente,
                                            'id_proceso'            => $id_proceso,
                                            'detalle_resultado'     => 'PROCESADO OK'
                                        ];

                                        $detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                        $procesadas++;
                                        
                                    }
                                } 
                            }
                        }
                    }
                    $proceso = Ct_Rol_Proceso::find($id_proceso);
                    $proceso->update([
                        'procesados'    =>  $procesadas,
                        'no_procesados'  =>  $no_procesadas,
                    ]);
                });
        }
        
        $proceso = Ct_Rol_Proceso::find($id_proceso);

        if(is_null($proceso->procesados)){
            //dd($proceso);
            $proceso->update([
                'observacion' => 'Archivo sin registros válidos'
            ]);
            return ['tipo' => 'err', 'mensaje' => 'Archivo sin registros'];
        }
        //dd("fin");
        return ['tipo' => 'ok', 'mensaje' => 'Procesados: '.$proceso->procesados.' No Procesados: '.$proceso->no_procesados];
    }

    public function recalcular_db($id_rol){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $rol         = Ct_Rol_Pagos::find($id_rol);
        $detalle_rol = $rol->detalle->first();
        
        $nomina = $rol->ct_nomina;
        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first();
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();

        $total_cuota = 0;$total_cuota_ini = 0; $total_otros_anticipos = 0; 
        $total_quota_quirog = 0; $total_quota_hipot = 0; 

        $sobre_tiempo_50 = 0;$sobre_tiempo_100 = 0;
        $fondo_reserva = 0;$decimo_tercero = 0;$decimo_cuarto = 0;$fondo_reserva_acumulado = 0;
            
        $total_cuota = $rol->prestamo_detalle->sum('valor_cuota'); 
        $total_cuota_ini = $rol->saldo_detalle->sum('valor_cuota');  
        $total_otros_anticipos = $rol->otros_anticipos->sum('monto_anticipo'); 
        $total_quota_hipot = $rol->cuotas_hipotecarios->sum('valor_cuota');
        $total_quota_quirog = $rol->cuotas_quirografario->sum('valor_cuota');

        //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
        //dd($detalle_rol);
        $dias_laborados    = $detalle_rol->dias_laborados;
        $cantidad_horas50  = $detalle_rol->cantidad_horas50;
        $cantidad_horas100 = $detalle_rol->cantidad_horas100;
        $bonificacion      = $detalle_rol->bonificacion;
        $transporte        = $detalle_rol->transporte;
        $exam_laboratorio  = $detalle_rol->exam_laboratorio;

        $multa             = $detalle_rol->valor_multa;
        $impuesto_renta    = $detalle_rol->impuesto_renta;
        $sueldo_mensual    = $nomina->sueldo_neto;

        $bono_imputable     = $detalle_rol->bono_imputable;
        $alimentacion       = $detalle_rol->alimentacion;
        $fecha_ingreso      = $nomina->fecha_ingreso;
        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
        $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
        $tipo_decimo_tercero     = $nomina->decimo_tercero;
        $aporte_personal         = $nomina->aportepersonal->valor;
        $seguro_privado          = $detalle_rol->seguro_privado;
        $anticipo_quincena       = $detalle_rol->anticipo_quincena;
        $pct_fondo_reserva   = $val_fond_reserv->valor;
        $valor_sueldo_basico = $val_sal_basico->valor;
        $fond_reserv_cobrar  = $detalle_rol->fond_reserv_cobrar;
        $otros_egresos       = $detalle_rol->otros_egresos;
        $parqueo             = $detalle_rol->parqueo;

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        $sueldo_mensual = $sueldo_mensual / 30;

        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        //dd($sueldo_mensual);
        $sueldo_mensual = round($sueldo_mensual,2);
        //CALCULO SOBRETIEMPO 50
        //$sobre_tiempo_50 = ((($sueldo_mensual/30)/8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = ((($nomina->sueldo_neto/30)/8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = round($sobre_tiempo_50,2);
        //CALCULO SOBRETIEMPO 100
        //$sobre_tiempo_100 = ((($sueldo_mensual/30)/8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = ((($nomina->sueldo_neto/30)/8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = round($sobre_tiempo_100,2);

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        /*$sueldo_mensual = $sueldo_mensual / 30;
        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        $sueldo_mensual = round($sueldo_mensual,2);*/
        
        //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
        $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

        //4:FONDO DE RESERVA
        $fecha         = date("Y-m-d");
        $fec           = new DateTime($fecha);
        $vt_dia        = date("d",strtotime($fecha_ingreso));
        $fec2          = new DateTime($fecha_ingreso);
        $diff          = $fec->diff($fec2); //dd($diff);
        $intervalMeses = $diff->format("%m");
        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
        $añosAhora     = $diff->format("%y"); //dd($añosAhora);
        $intervalDias  = $diff->format("%d");
        $diaActual     = $fec->format("d");
        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
        if ($añosAhora > 0) {
            if ($tipo_fondo_reserva == 2) { //mensualiza
                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);//dd($base_iess,$pct_fondo_reserva,$fondo_reserva);
            } 
            ////////////////////FONDO DE RESERVA ACUMULADO (4)////////////////////////////////////
            else{
                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
            }
            //////////////////////////////////////////////////////////////////////////////////////
        }else{
            if($intervalMeses == '11' && $tipo_fondo_reserva == 2){
                $vt_dia = $dias_laborados - $vt_dia; 
                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                //dd($fondo_reserva,$pct_fondo_reserva);
                //dd($fondo_reserva);
            }
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (4)///////////////////////////////////////
            else{
                if($intervalMeses == '11'){
                    $vt_dia = $dias_laborados - $vt_dia; 
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);
                }    
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////// 
               
        }
        $fondo_reserva = round($fondo_reserva,2);

        //5:DECIMO TERCER
        if($tipo_decimo_tercero == 2){
            $decimo_tercero = $base_iess / 12;    
        }
        $decimo_tercero = round($decimo_tercero,2);

        //6:DECIMO CUARTO
        if($tipo_decimo_cuarto == 2){
            $decimo_cuarto = $valor_sueldo_basico / 12;    
        }
        $decimo_cuarto = round($decimo_cuarto,2);

        //7:PORCENTAJE IESS
        $porcentaje_iess = $base_iess * $aporte_personal / 100;
        $porcentaje_iess = round($porcentaje_iess,2);

        $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto +$alimentacion + $transporte;

        $total_ingresos = round($total_ingresos,2);

        $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $otros_egresos + $exam_laboratorio + $parqueo + $fond_reserv_cobrar;
        $total_egresos = round($total_egresos,2);


        $neto_recibir = $total_ingresos - $total_egresos;

        $detalle_rol->update([
            'dias_laborados'              => $dias_laborados,
            'base_iess'                   => $base_iess,
            'sueldo_mensual'              => $sueldo_mensual,
            'cantidad_horas50'            => $cantidad_horas50,
            'sobre_tiempo50'              => $sobre_tiempo_50,
            'cantidad_horas100'           => $cantidad_horas100,
            'sobre_tiempo100'             => $sobre_tiempo_100,
            'bonificacion'                => $bonificacion,
            'alimentacion'                => $alimentacion,
            'transporte'                  => $transporte,
            'bono_imputable'              => $bono_imputable,
            'exam_laboratorio'            => $exam_laboratorio,
            'fondo_reserva'               => $fondo_reserva,
            'decimo_tercero'              => $decimo_tercero,
            'decimo_cuarto'               => $decimo_cuarto,
            'porcentaje_iess'             => $porcentaje_iess,
            'seguro_privado'              => $seguro_privado,
            'impuesto_renta'              => $impuesto_renta,
            'multa'                       => $multa,
            'fond_reserv_cobrar'          => $fond_reserv_cobrar,
            'otros_egresos'               => $otros_egresos,
            'prestamos_empleado'          => $total_cuota,
            'saldo_inicial_prestamo'      => $total_cuota_ini,
            'anticipo_quincena'           => $anticipo_quincena,
            //'observacion_bono'          => $request['observacion_bono'],
            //'observacion_alimentacion'  => $request['observacion_alimentacion'],
            //'observ_seg_privado'        => $request['observacion_seg_priv'],
            //'observ_imp_renta'          => $request['observacion_imp_rent'],
            'otro_anticipo'               => $total_otros_anticipos,
            //'observacion_multa'         => $request['observ_multa'],
            //'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
            //'observacion_prestamo'      => $request['concepto_prestamo'],
            //'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
            //'observacion_anticip_quinc' => $request['concepto_quincena'],
            //'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
            //'observacion_transporte'    => $request['observacion_transporte'],
            //'observacion_bonoimp'       => $request['observacion_bonoimp'],
            //'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
            //'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
            'total_quota_quirog'          => $total_quota_quirog,
            'total_quota_hipot'           => $total_quota_hipot,
            'total_ingresos'              => $total_ingresos,
            'total_egresos'               => $total_egresos,
            'neto_recibido'               => $neto_recibir,
            'id_usuariomod'               => $idusuario,
            'ip_modificacion'             => $ip_cliente,
            'parqueo'                     => $parqueo, 
            'fondo_reserva_acumulado'     => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (4) 
        ]);

        return "ok";

    }

    public function detalle_he_rol($id_proceso){

        $detalles = Ct_Rh_Detalle_Horas_Extras::where('id_proceso',$id_proceso)->get();

        return view('contable.nuevo_rol_pago.detalle_proceso',['detalles' => $detalles]);

    }

    public function detalle_iess_rol($id_proceso){

        $detalles = Ct_Rh_Detalle_Prestamos_Subidos::where('id_proceso',$id_proceso)->get();
        //dd($detalles);
        return view('contable.nuevo_rol_pago.detalle_proceso_iess',['detalles' => $detalles]);

    }

    public function masivos_prestamos(Request $request){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $prestamo   = $request->prestamos;
        if($prestamo=='1'){
            $tipo_pres  = 'QUIRO';
        }else{
            $tipo_pres  = 'HIPOT';
        }
        
        //dd($prestamo);
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];

        $proceso = Ct_Rol_Proceso::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->where('tipo_proceso','ROL_POR_EMPRESA')->first();

        if(is_null($proceso)){
            return ['tipo' => 'err', 'mensaje' => 'Debe generar los roles primero'];
        }

        $fecha_actual    = Date('Y-m-d H:i:s');
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "hp_".$tipo_pres.'_'.$id_empresa.'_'.$anio.'_'.$mes.'_'.date('YmdHis').".".$extension;
        //dd($nuevo_nombre);

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
        $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
        
        $id_proceso = Ct_Rol_Proceso::insertGetId([
            'id_empresa'    => $id_empresa,
            'anio'          => $anio,
            'mes'           => $mes,
            'tipo_proceso'  => 'PRESTAMOS_'.$tipo_pres.'_POR_EMPRESA',
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ]);
        $procesadas=0;$no_procesadas=0;

        if ($r1) {
            Excel::filter('chunk')
                ->formatDates(true, 'Y-m-d')
                ->load($rutadelaimagen)
                ->chunk(250, function ($reader) use ($idusuario, $anio, $mes, $prestamo, $id_empresa, $fecha_actual, $ip_cliente, $nombre_original, $id_proceso, $procesadas, $no_procesadas) {
                    //dd($reader);
                    $cant = 0;
                    foreach ($reader as $book) {

                        if (!is_null($book)) {

                            $continuar = true;

                            if (is_null($book->cedula)) {
                                $arr_det_log =[

                                    'id_proceso'            => $id_proceso,
                                    'numero'                => $book->no,
                                    'cedula'                => $book->cedula,
                                    'nombres'               => $book->nombres,                                  
                                    'detalle'               => $book->detalle, 
                                    'nut'                   => $book->nut,
                                    'valor'                 => $book->valor,
                                    'id_usuariocrea'        => $idusuario,
                                    'id_usuariomod'         => $idusuario,
                                    'ip_creacion'           => $ip_cliente,
                                    'ip_modificacion'       => $ip_cliente,
                                    'detalle_resultado'     => 'REGISTRO SIN NUMERO DE CEDULA'
                                ];

                                $detalle_log =Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                $no_procesadas++;$continuar=false;    
                            }

                            if($continuar){    
                                $nomina = Ct_Nomina::where('id_user',$book->cedula)->where('id_empresa',$id_empresa)->first();
                                if(is_null($nomina)){

                                    $arr_det_log =[

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,                                  
                                        'detalle'               => $book->detalle, 
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'EMPLEADO NO EXISTE EN LA NOMINA'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;

                                }
                            }

                            if($continuar){

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

                                if(is_null($rol)){
                                    $arr_det_log =[

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,                                  
                                        'detalle'               => $book->detalle, 
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'EMPLEADO SIN ROL GENERADO'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;
                                }


                            }

                            if($continuar){

                                if(!is_numeric($book->valor)){
                                    $arr_det_log =[

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,                                  
                                        'detalle'               => $book->detalle, 
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'ERROR EN FORMATO DE VALOR DEL PRESTAMO'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if($continuar){

                                if(is_null($book->valor)){
                                    $arr_det_log =[

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,                                  
                                        'detalle'               => $book->detalle, 
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'VALOR DEL PRESTAMO VACIO'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if($continuar){

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();
                                

                                if (!is_null($rol)) {

                                    if ($prestamo == 1) {

                                        $arr_qui = [
                                            'id_rol'            => $rol->id,
                                            'valor_cuota'       => $book->valor,
                                            'detalle_cuota'     => $book->detalle,
                                            'id_usuariocrea'    => $idusuario,
                                            'id_usuariomod'     => $idusuario,
                                            'ip_creacion'       => $ip_cliente,
                                            'ip_modificacion'   => $ip_cliente,
                                        ];

                                        Ct_Rh_Cuotas_Quirografario::create($arr_qui);

                                        
                                    } else {
                                        $arr_hip = [
                                            'id_rol'            => $rol->id,
                                            'valor_cuota'       => $book->valor,
                                            'detalle_cuota'     => $book->detalle,
                                            'id_usuariocrea'    => $idusuario,
                                            'id_usuariomod'     => $idusuario,
                                            'ip_creacion'       => $ip_cliente,
                                            'ip_modificacion'   => $ip_cliente,
                                        ];

                                        Ct_Rh_Cuotas_Hipotecarios::create($arr_hip);

                                    }

                                    $this->recalcular_db($rol->id);

                                    $arr_det_log =[

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,                                  
                                        'detalle'               => $book->detalle, 
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'PROCESADO'
                                    ];

                                    $detalle_log =Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $procesadas++;

                                } 
                            }
                        }


                        $cant++;
                    }
                    $proceso = Ct_Rol_Proceso::find($id_proceso);
                    $proceso->update([
                        'procesados'    =>  $procesadas,
                        'no_procesados'  =>  $no_procesadas,
                    ]);
                });
        }

        $proceso = Ct_Rol_Proceso::find($id_proceso);

        if(is_null($proceso->procesados)){
            //dd($proceso);
            $proceso->update([
                'observacion' => 'Archivo sin registros válidos'
            ]);
            return ['tipo' => 'err', 'mensaje' => 'Archivo sin registros'];
        }
        //dd("fin");
        return ['tipo' => 'ok', 'mensaje' => 'Procesados: '.$proceso->procesados.' No Procesados: '.$proceso->no_procesados];
              

    }

    public function he_valida_ejecutado(Request $request){

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $prestamo   = $request->prestamos;

        $proceso = Ct_Rol_Proceso::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->where('tipo_proceso','HORAS_EXTRAS_POR_EMPRESA')->first();
        
        if(is_null($proceso)){
            return ['estado' => 'ok'];
        }

        return ['estado' => 'wrn', 'mensaje' => 'Ya existe un proceso ingresado, Desea Continuar'];

    }

    public function p_valida_ejecutado(Request $request){

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $prestamo   = $request->prestamos;

        if($prestamo == '1'){ //QUIROGRAFARIO
            $proceso = Ct_Rol_Proceso::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->where('tipo_proceso','PRESTAMOS_QUIRO_POR_EMPRESA')->first();
        }else{ //HIPOTECARIO
            $proceso = Ct_Rol_Proceso::where('id_empresa',$id_empresa)->where('anio',$anio)->where('mes',$mes)->where('tipo_proceso','PRESTAMOS_HIPOT_POR_EMPRESA')->first();
        }

        
        if(is_null($proceso)){
            return ['estado' => 'ok'];
        }

        return ['estado' => 'wrn', 'mensaje' => 'Ya existe un proceso ingresado, Desea Continuar'];

    }

    public function asientos_por_generar($anio, $mes, Request $request){

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');
        $anio           = $anio;
        $mes            = $mes;
        $txt_mes        = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->get();
           
        if($roles->count() == 0){
            return [ 'msj'=> "error", 'mensaje' => "No existe roles de pago en el Año : {$anio} Mes : {$txt_mes}, verifique la fecha de la creación del asiento"];
        }

        $rol_asiento    = RolAsiento::where('id_empresa', $id_empresa)
                            ->where('anio', $anio)
                            ->where('mes', $mes)
                            ->where('estado', '1')
                            ->first();
        
        if (!is_null($rol_asiento)) {
            
            return [ 'msj'=> "ok", 'id_rol_asiento' => $rol_asiento->id ];

        } else {
                
        
            $input_rol_asiento = [
                //'id_asiento'      => $id_asiento_cabecera,
                //'fecha_asiento'   => $fecha_actual,
                'id_empresa'      => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'id_empresa'      => $id_empresa,
                'anio'            => $anio,
                'mes'             => $mes,
                'estado'          =>  1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_rol_asiento = RolAsiento::insertGetId($input_rol_asiento);

            $rol_acum = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                ->where('ct_rol_pagos.anio', $anio)
                ->where('ct_rol_pagos.mes', $mes)
                ->where('ct_rol_pagos.id_empresa', $id_empresa)
                ->join('ct_detalle_rol as drol', 'drol.id_rol', 'ct_rol_pagos.id')
                ->select(
                    DB::raw("SUM(drol.sueldo_mensual) as total_sueldo"),
                    DB::raw("SUM(drol.sobre_tiempo50) as total_horas_50"),
                    DB::raw("SUM(drol.sobre_tiempo100) as total_horas_100"),
                    DB::raw("SUM(drol.bonificacion) as total_bono"),
                    DB::raw("SUM(drol.alimentacion) as total_alimentacion"),
                    DB::raw("SUM(drol.transporte) as total_transporte"),
                    DB::raw("SUM(drol.bono_imputable) as total_bonoimp"),
                    DB::raw("SUM(drol.fondo_reserva) as total_fond_reserva"),
                    DB::raw("SUM(drol.decimo_tercero) as total_decimo_tercero"),
                    DB::raw("SUM(drol.decimo_cuarto) as total_decimo_cuarto"),
                    DB::raw("SUM(drol.porcentaje_iess) as total_iess"),
                    DB::raw("SUM(drol.multa) as total_multa"),
                    DB::raw("SUM(drol.fond_reserv_cobrar) as fond_reser_cob"),
                    DB::raw("SUM(drol.otros_egresos) as otro_egres"),
                    DB::raw("SUM(drol.impuesto_renta) as total_imp_renta"),
                    DB::raw("SUM(drol.seguro_privado) as total_seguro_privado"),
                    DB::raw("SUM(drol.exam_laboratorio) as total_exlaboratorio"),
                    DB::raw("SUM(drol.saldo_inicial_prestamo) as total_sald_inicial"),
                    DB::raw("SUM(drol.prestamos_empleado) as total_prestamos"),
                    DB::raw("SUM(drol.anticipo_quincena) as total_anticipos"),
                    DB::raw("SUM(drol.otro_anticipo) as total_otro_anticipo"),
                    DB::raw("SUM(drol.total_ingresos) as total_ingreso"),
                    DB::raw("SUM(drol.total_egresos) as total_egresos"),
                    DB::raw("SUM(drol.neto_recibido) as total_neto_recibido"),
                    DB::raw("SUM(drol.total_quota_quirog) as total_quot_quirog"),
                    DB::raw("SUM(drol.total_quota_hipot) as total_quot_hipot"),
                    DB::raw("SUM(drol.parqueo) as parqueo"),
                    DB::raw("SUM(drol.fondo_reserva_acumulado) as fondo_reserva_acumulado")
                )
                ->first();

            /* PRESTAMOS A EMPLEADOS MAS SALDO INICIAL ACTIVO */
            $val_prestamo = $rol_acum->total_prestamos;
            $val_prestamo += $rol_acum->total_sald_inicial;
            if ($val_prestamo > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first(); 1.01.02.03.02
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS A EMPLEADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_prestamo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,     
                ]);
            }    

            /* ANTICIPO SUELDOS EMPLEADOS ACTIV */
            $anticipo_sueldo = $rol_acum->total_anticipos;
            if ($anticipo_sueldo > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();1.01.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ANTICIPOS QUINCENA',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $anticipo_sueldo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* OTROS ANTICIPO */
            $otros_anticipo = $rol_acum->total_otro_anticipo;
            if ($otros_anticipo > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();1.01.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS ANTICIPOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $otros_anticipo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    //'doc_pendiente'     => 'E/V',  
                ]);
            }

            /* IMPUESTO A LA RENTA POR PAGAR PASIVO */
            $val_imp_renta = $rol_acum->total_imp_renta;
            if ($val_imp_renta > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.11')->first();2.01.04.01.10
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_IMPUESTO_RENTA_PAGAR');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => '302 RET FTE RELACION DEPENDENCIA',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_imp_renta,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* APORTE INDIVIDUAL 9.45% PASIVO */
            $aporte_individual = $rol_acum->total_iess;
            if ($aporte_individual > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.01')->first();2.01.04.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_APORTE_INDIVIDUAL_9.4');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'APORTE INDIVIDUAL 9.45 %',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $aporte_individual,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /* CON EL IESS PRESTAMOS QUIROGRAFARIO PASIVO */
            $val_prest_quirografario = $rol_acum->total_quot_quirog;
            if ($val_prest_quirografario > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.05')->first();2.01.04.03.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_QUIROGRAFARIOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS QUIROGRAFARIOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_prest_quirografario,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /* CON EL IESS PRESTAMOS HIPOTECARIOS PASIVO */
            $val_prest_hipotecario = $rol_acum->total_quot_hipot;
            if ($val_prest_hipotecario > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.06')->first();2.01.04.03.06
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_HIPOTECARIOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS HIPOTECARIOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_prest_hipotecario,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /**********NETO A RECIBIR (CUENTA DESTINO)*******/
            $net_recibir = $rol_acum->total_neto_recibido;
            if ($net_recibir > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_SUELDOS_SALARIOS_PAGAR');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'SUELDOS SALARIOS PAGAR',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $net_recibir,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* MULTAS EMPLEADOS Y FUNCIONARIO INGRESO */
            $val_multa = $rol_acum->total_multa;
            if ($val_multa > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.01')->first();4.1.04.01.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MULTAS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'MULTAS EMPLEADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_multa,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* PARQUEO */
            $val_parqueo = $rol_acum->parqueo;
            //$val_otro_egreso = $val_otro_egreso + $total_sum->parqueo;
            if ($val_parqueo > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();4.1.04.01.02
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS_PARQUEO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS INGRESOS PARQUEOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_parqueo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* MODIFICADA OTROS INGRESOS */
            $val_otro_egreso = $rol_acum->otro_egres;
            if ($val_otro_egreso > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();4.1.04.01.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS INGRESOS (INTERESES)',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_otro_egreso,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* PAGO DE SUELDOS Y SALARIOS HORAS EXTRAS AL 50 Y AL 100 BONO IMPUTABLE SUELDO GASTO */
            $sueldo_salario = $rol_acum->total_sueldo;
            if ($sueldo_salario > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.01')->first(); //ROLPAGO_SUELDO 5.2.02.01.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROLPAGO_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'SUELDOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $sueldo_salario,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                    'doc_pendiente'     => 'IESS', 
                ]);
            }

            /*Suma de Horas extras*/
            $sobre_tiempo_50  = $rol_acum->total_horas_50;
            $sobre_tiempo_100 = $rol_acum->total_horas_100;
            $total_sobre_tiempo = $sobre_tiempo_50 + $sobre_tiempo_100;
            if ($total_sobre_tiempo > 0) {
                //$plan_cuentas       = Plan_Cuentas::where('id', '5.2.02.01.02')->first();
                //5.2.02.01.02  ROL_PAGOS_HORAS_EXTRAS
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_HORAS_EXTRAS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'HORAS EXTRAS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $total_sobre_tiempo,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                    'doc_pendiente'     => 'IESS', 
                ]);
            }

            /* FONDO RESERVA MENSUALES GASTO */
            $val_fond_reserv = $rol_acum->total_fond_reserva;
            if ($val_fond_reserv > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();5.2.02.02.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA MENSUALES',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_fond_reserv,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* FONDO RESERVA ACUMULADOS GASTO */
            $val_fond_reserv_acum = $rol_acum->fondo_reserva_acumulado;
            if ($val_fond_reserv_acum > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();5.2.02.02.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA ACUMULADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_fond_reserv_acum,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* ALIMENTACION GASTO */
            $val_alimentacion = $rol_acum->total_alimentacion;
            if ($val_alimentacion > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.01')->first();5.2.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_ALIMENTACION');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ALIMENTACION',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_alimentacion,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* BONIFICACION ESPECIAL GASTO */
            $val_bonif = $rol_acum->total_bono;
            if ($val_bonif > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.03')->first();
                // 5.2.02.03.03   ROL_PAGOS_BONIFICACION_ESPECIAL 5.2.02.03.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_ESPECIAL');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'BONIFICACION ESPECIAL',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_bonif,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* BONO IMPUTABLE GASTO */
            $val_bono_imput = $rol_acum->total_bonoimp;
            if ($val_bono_imput > 0) {   
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.04')->first();5.2.02.03.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_IMPUTABLE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'BONIFICACION INPUTABLE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_bono_imput,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',  
                ]);
            }

            /* TRANSPORTE Movilizacion y Transporte */
            $val_transporte = $rol_acum->total_transporte;
            if ($val_transporte > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.06')->first();5.2.02.03.06
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MOVILIZACION_TRANSPORTE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'MOVILIZACIONES Y TRANSPORTE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_transporte,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* FONDO RESERVA ACUMULADOS GASTO */
            $val_fond_reserv_acum = $rol_acum->fondo_reserva_acumulado;
            if ($val_fond_reserv_acum > 0) {
                //2.01.04.03.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA_SALE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA ACUMULADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_fond_reserv_acum,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                    'doc_pendiente'     => 'IESS2', 
                ]);
            }

            /* DECIMO TERCERO GASTO */
            $val_decim_terc = $rol_acum->total_decimo_tercero;
            if ($val_decim_terc > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.04')->first();5.2.02.01.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'DECIMO TERCER SUELDO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_decim_terc,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /* DECIMO CUARTO SUELDO GASTO */
            $val_decim_cuart = $rol_acum->total_decimo_cuarto;
            if ($val_decim_cuart > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.05')->first();5.2.02.01.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'DECIMO CUARTO SUELDO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_decim_cuart,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente, 
                ]);
            }

            /*  SEGURO PRIVADO DEBEN REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT  */
            /*  FONDO RESERVA COBRAR REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT  */
            /*  EXAMEN DE LABORATORIO REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT */

            /*  MODIFICADO CORRECTO SEGURO ASISTENCIA MEDICA CXC SEGURO CUENTA X COBRAR SALUD GASTO */
            /*$val_seg_privado = $total_sum->total_seguro_privado;

            if ($val_seg_privado > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.02')->first();
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.11')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_CUENTAS_POR_COBRAR_SEGURO');


                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.11',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_seg_privado,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }*/

            /* FONDO DE RESERVA COBRAR TRABAJADORES MODIFICADO INGRESO */

            /*$val_res_cob = $total_sum->fond_reser_cob;

            if ($val_res_cob > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.02.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'haber'                => $val_res_cob,
                    'debe'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }*/

            /* EXAMENES DE LABORATORIO */
            /*$val_exa_lab = $total_sum->total_exlaboratorio;

            if ($val_exa_lab > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_exa_lab,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }*/

            return [ 'msj'=> "ok", 'id_rol_asiento' => $id_rol_asiento];
        }    

    }

    public function detalle_asientos_rol($id){

        ///COLOCAR EN UN ARCHIVO DE CONFIGURACION DE NOMINA
        $aporte_patronal = 11.15;
        $secap           = 1;

        $cuentas_rol = RolAsiento::find($id);

        $cuentas = $cuentas_rol->cuentas;
        $cuentas_iess = [];$total_aporte = 0;$valor_aporte = 0; $valor_secap = 0; $cuentas_iess2 = [];
        if($cuentas_rol->id_asiento != null){
            $cuentas_iess = $cuentas_rol->cuentas->where('doc_pendiente','IESS');
            $total_aporte = $cuentas_rol->cuentas->where('doc_pendiente','IESS')->sum('debe');
            $valor_aporte = $total_aporte * $aporte_patronal /100;
            $valor_aporte = round($valor_aporte,2);
            $valor_secap = $total_aporte * $secap /100;
            $valor_secap = round($valor_secap,2);
            $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente','IESS2');

        }

        

        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $cuentas_rol->id_empresa)->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $cuentas_rol->anio)
            ->where('ct_rol_pagos.mes', $cuentas_rol->mes)
            ->where('ct_rol_pagos.id_empresa', $cuentas_rol->id_empresa)
            ->get();

        return view('contable.nuevo_rol_pago.cuentas',['cuentas_rol' => $cuentas_rol, 'cuentas' => $cuentas, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'tipo_pago_rol' => $tipo_pago_rol, 'roles' => $roles, 'cuentas_iess' => $cuentas_iess, 'aporte_patronal' => $aporte_patronal, 'secap' => $secap, 'valor_aporte' => $valor_aporte, 'valor_secap' => $valor_secap, 'total_aporte' => $total_aporte, 'cuentas_iess2' => $cuentas_iess2 ]);

    }

    public function generar_asientos($id, Request $request){

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;

        $fecha_actual = date('Y-m-d H:i:s');
        $id_empresa   = $request->session()->get('id_empresa');
        $cuentas_rol  = RolAsiento::find($id);


        if($cuentas_rol->id_asiento == null){
            //dd($cuentas_rol);
            $txt_mes  = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
            $text     = 'Total Roles de Pago' . ':' . ' ' . 'Id_Empresa' . ':' . $id_empresa . ' ' . 'Año' . ':' . $cuentas_rol->anio . ' ' . 'Mes' . ':' . $cuentas_rol->mes;

            $cuentas = $cuentas_rol->cuentas();
            $debe    = $cuentas_rol->cuentas()->sum('debe');
            $haber   = $cuentas_rol->cuentas()->sum('haber');

            if($debe != $haber){
                return [ 'msj'=> "error", 'mensaje' => "No se puede generar, se encuentra descuadrado"];    
            }

            $input_cabecera = [
                'fecha_asiento'   => $fecha_actual,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $debe,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $input_rol_asiento = [
                'id_asiento'      => $id_asiento_cabecera,
                'fecha_asiento'   => $fecha_actual,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $cuentas_rol->update($input_rol_asiento);

            $cuentas = $cuentas_rol->cuentas;

            foreach ($cuentas as $value) {
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->id_plan_cuentas,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $fecha_actual,
                    'debe'                => $value->debe,
                    'haber'               => $value->haber,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);    
            }

            return [ 'msj'=> "ok", 'id_rol_asiento' => $cuentas_rol->id];
                
                
        }else{

            return [ 'msj'=> "error", 'mensaje' => "Ya se encuentran Generados los Asientos"];  

        }

        

    }
    
    public function pago_de_roles(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);
        $roles = $request->roles;
        if(count($roles) < 1){
            return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN ROL PARA GENERAR', 'titulos' => 'Error'];
        }

        $caja_banco = Ct_Caja_Banco::where('id_empresa',$id_empresa)->where('cuenta_mayor',$request->cuenta_saliente)->where('estado',1)->first();
        if(is_null($caja_banco)){
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $cuentas_rol  = RolAsiento::find($request->rol_asientos);
        
        DB::beginTransaction();
        try {

            $sum_pagar = 0;
            foreach($roles as $rol){

                $ct_rol  = Ct_Rol_Pagos::find($rol);
                $detalle = $ct_rol->detalle->first();
                if( !is_null($ct_rol) ){  
                    if( $ct_rol->id_asiento_pago == null ){
                        if( !is_null($detalle)){
                            $sum_pagar   += $detalle->neto_recibido;
                        }
                        
                    }
                }
            }

            //dd($sum_pagar);
            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            $concepto  = 'PAGOS DE SUELDOS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto2 = 'SUELDOS Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_creacion,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                
            foreach($roles as $rol){
                $ct_rol  = Ct_Rol_Pagos::find($rol);

                if( $ct_rol->id_asiento_pago == null ){

                    $arr_valor = [
                        'id_tipo_pago'          => $request->tipo_pago,
                        'numero_cuenta'         => $request->numero_cuenta,
                        'banco'                 => $request->banco,
                        'cuenta_saliente'       => $request->cuenta_saliente,
                        'num_cheque'            => $request->numero_cheque,
                        'fecha_cheque'          => $request->fecha_cheque,
                        'id_asiento_pago'       => $id_asiento_cabecera,
                    ];

                    $ct_rol->update($arr_valor);
                }    

            }

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_SUELDOS_SALARIOS_PAGAR');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->cuenta_saliente,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                if($ct_tipo_pago->tipo == 'ACREDITACION'){ //DEBITO

                    $nota_debito         = [
                        'concepto'        => $concepto,
                        'fecha'           => $request->fecha_creacion,
                        'valor'           => $sum_pagar,
                        'empresa'         => $id_empresa,
                        'tipo'            => "BAN-ND",
                        'id_asiento'      => $id_asiento_cabecera,
                        'id_banco'        => $caja_banco->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'modulo'          => 'PAGO DE SUELDOS', 
                    ];
                    //$id_nota = 0;
                    $id_nota = Nota_Debito::insertGetId($nota_debito);
                   
                    $nota_deb_detalle = [
                        'id_nota_debito'  => $id_nota,
                        'codigo'          => $plan_cuentas->cuenta_guardar,
                        'cuenta'          => $plan_cuentas->nombre_mostrar,
                        'debe'            => $sum_pagar,
                        'haber'           => number_format(0, 2),
                        'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                    ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);

                }else{ //COMPROBANTE DE EGRESO
                    $numero_factura = LogAsiento::getSecuencia(2);

                    $input_comprobante = [
                        'descripcion'     => $concepto,
                        'estado'          => '1',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => 'null',
                        'nota'            => $concepto,
                        'fecha_comprobante' => $request->fecha_creacion,
                        'beneficiario'    => $concepto2,
                        'check'           => 0,
                        'girado'          => $concepto2,
                        'id_caja_banco'   => $caja_banco->id,
                        'nro_cheque'      => $request->numero_cheque,
                        'valor'           => $sum_pagar,
                        'fecha_cheque'    => $request->fecha_cheque,
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'modulo'          => 'PAGO DE SUELDOS', 
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $plan_cuentas->cuenta_guardar,
                        'cuenta'                         => $plan_cuentas->nombre_mostrar,
                        'descripcion'                    => $concepto,
                        'debe'                           => $sum_pagar,
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $id_usuario,
                        'id_usuariomod'                  => $id_usuario,
                    ]);
                }
            }
        

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function aportes_patronales(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $rol_asientos_aporte  = $request->rol_asientos_aporte;
        $total_aporte         = $request->total_aporte;
        $p_patronal           = $request->p_patronal;
        $p_secap              = $request->p_secap;
        $aporte_patronal      = $request->aporte_patronal;
        $aporte_secap         = $request->aporte_secap;
        $fecha_asiento_aporte = $request->fecha_asiento_aporte;

        $cuentas_rol  = RolAsiento::find($rol_asientos_aporte);

        
        if($total_aporte <= 0 ){
            return ['respuesta' => 'error', 'msj' => 'SIN VALOR A APORTAR', 'titulos' => 'Error'];
        }

        if($p_patronal <= 0 ){
            return ['respuesta' => 'error', 'msj' => 'CONFIGURE EL PORCENTAJE DE APORTACION PATRONAL', 'titulos' => 'Error'];
        }

        if($p_secap <= 0 ){
            return ['respuesta' => 'error', 'msj' => 'CONFIGURE EL PORCENTAJE DE APORTACION SECAP', 'titulos' => 'Error'];
        }

        if( is_null($cuentas_rol) ){
            return ['respuesta' => 'error', 'msj' => 'PERDIODO NO DEFINIDO', 'titulos' => 'Error'];
        }

        $sum_pagar = $aporte_patronal + $aporte_secap;

        
        DB::beginTransaction();
        try {

            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            $concepto  = 'APORTES PATRONALES Y DEL SECAP: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto2 = 'PATRONAL Y SECAP Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $fecha_asiento_aporte,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $cuentas_rol->update([
                'id_asiento_aporte'    => $id_asiento_cabecera,
                'fecha_asiento_aporte' => $fecha_asiento_aporte,
                'aporte_patronal'      => $aporte_patronal,
                'aporte_secap'         => $aporte_secap,
                'total_aporte'         => $total_aporte ,
                'p_patronal'           => $p_patronal,
                'p_secap'              => $p_secap,         
                 
            ]);    

            if ($aporte_patronal > 0) {


                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => 0,
                    'haber'               => $aporte_patronal,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(5)');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => $aporte_patronal,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);


            }

            if ($aporte_secap > 0) {

                

                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => 0,
                    'haber'               => $aporte_secap,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(5)');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => $aporte_secap,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);


            }
        

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function pago_aportes_patronales(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_usuario = Auth::user()->id;
        $idusuario  = $id_usuario;
        $id_empresa = $request->session()->get('id_empresa');

        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->id_tipo_pago_planilla);
        

        $caja_banco = Ct_Caja_Banco::where('id_empresa',$id_empresa)->where('cuenta_mayor',$request->id_cuenta_planilla)->where('estado',1)->first();
        if(is_null($caja_banco)){
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $cuentas_rol  = RolAsiento::find($request->rol_planillas);

        if($cuentas_rol->id_asiento_planilla != null){
            return ['respuesta' => 'error', 'msj' => 'YA SE GENERO EL ASIENTO PARA EL PAGO DE LA PLANILLA', 'titulos' => 'Error'];
        }

        $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente','IESS2');

        $sum_pagar = $request->total_planilla;
        if($sum_pagar <= 0){
            return ['respuesta' => 'error', 'msj' => 'SIN VALOR A PAGAR', 'titulos' => 'Error'];
        }
        
        DB::beginTransaction();
        try {

            
            
            //dd($sum_pagar);
            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            $concepto  = 'PAGOS DE PLANILLAS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto2 = 'PLANILLAS Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_planilla,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $arr_valor = [
                'id_tipo_pago_planilla'    => $request->id_tipo_pago_planilla,
                'numero_cuenta_planilla'   => $request->numero_cuenta_planilla,
                'id_banco_planilla'        => $request->id_banco_planilla,
                'id_cuenta_planilla'       => $request->id_cuenta_planilla,
                'numero_cheque_planilla'   => $request->numero_cheque_planilla,
                'fecha_cheque_planilla'    => $request->fecha_cheque_planilla,
                'id_asiento_planilla'      => $id_asiento_cabecera,
                'fecha_planilla'           => $request->fecha_planilla,
            ];

            $cuentas_rol->update($arr_valor);

            foreach($cuentas_iess2 as $cuentas){
    
                if($cuentas->haber > 0){

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuentas->id_plan_cuentas,
                        'descripcion'         => $cuentas->descripcion,
                        'fecha'               => $request->fecha_planilla,
                        'debe'                => $cuentas->haber,
                        'haber'               => 0,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]); 

                }   

            }

            if($cuentas_rol->aporte_patronal > 0){
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_planilla,
                    'debe'                => $cuentas_rol->aporte_patronal,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            } 
            

            if($cuentas_rol->aporte_secap > 0){
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');

                Ct_Asientos_Detalle::create([//////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_planilla,
                    'debe'                => $cuentas_rol->aporte_secap,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            } 

            $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->id_cuenta_planilla)->orwhere('plan', $request->id_cuenta_planilla)->first();
            $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

            if($sum_pagar > 0){

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->id_cuenta_planilla,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_planilla,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);  
            }     

            if($ct_tipo_pago->tipo == 'ACREDITACION'){ //DEBITO

                $nota_debito         = [
                    'concepto'        => $concepto,
                    'fecha'           => $request->fecha_planilla,
                    'valor'           => $sum_pagar,
                    'empresa'         => $id_empresa,
                    'tipo'            => "BAN-ND",
                    'id_asiento'      => $id_asiento_cabecera,
                    'id_banco'        => $caja_banco->id,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'modulo'          => 'PAGO DE PLANILLAS', 
                ];
                //$id_nota = 0;
                $id_nota = Nota_Debito::insertGetId($nota_debito);
               
                $nota_deb_detalle = [
                    'id_nota_debito'  => $id_nota,
                    'codigo'          => $request->id_cuenta_planilla,
                    'cuenta'          => $plan_cuentas2->nombre,
                    'debe'            => $sum_pagar,
                    'haber'           => number_format(0, 2),
                    'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ];
                Nota_Debito_Detalle::create($nota_deb_detalle);

            }else{ //COMPROBANTE DE EGRESO
                $numero_factura = LogAsiento::getSecuencia(2);

                $input_comprobante = [
                    'descripcion'     => $concepto,
                    'estado'          => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'    => 'null',
                    'nota'            => $concepto,
                    'fecha_comprobante' => $request->fecha_planilla,
                    'beneficiario'    => $concepto2,
                    'check'           => 0,
                    'girado'          => $concepto2,
                    'id_caja_banco'   => $caja_banco->id,
                    'nro_cheque'      => $request->numero_cheque_planilla,
                    'valor'           => $sum_pagar,
                    'fecha_cheque'    => $request->fecha_cheque_planilla,
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'modulo'          => 'PAGO DE PLANILLAS', 
                ];
                $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                Ct_Detalle_Comprobante_Egreso_Varios::create([
                    'id_comprobante_varios'          => $id_comprobante,
                    'codigo'                         => $request->id_cuenta_planilla,
                    'cuenta'                         => $plan_cuentas2->nombre,
                    'descripcion'                    => $concepto,
                    'debe'                           => $sum_pagar,
                    'id_secuencia'                   => $numero_factura,
                    'estado'                         => '1',
                    'ip_creacion'                    => $ip_cliente,
                    'ip_modificacion'                => $ip_cliente,
                    'id_usuariocrea'                 => $id_usuario,
                    'id_usuariomod'                  => $id_usuario,
                ]);
            }
            
        
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    
}
