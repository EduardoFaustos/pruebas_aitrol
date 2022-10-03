<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;
use Sis_medico\User;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_Detalle_Rol;
use Sis_medico\Ct_Nomina;
use Excel;

class NominaReporteRolController extends Controller
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
        
        $empresas = Empresa::all();

        return view('contable.rh_reporte_roles.index',['empresas' => $empresas]);
    }
    
    public function reporte_datos_rol_pago(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];
        
        $mes_rol = null;

        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //dd($empresa);

        if($id_mes =='1'){
            $mes_rol = 'ENERO';    
        }elseif($id_mes =='2'){
            $mes_rol = 'FEBRERO';
        }elseif($id_mes =='3'){
            $mes_rol = 'MARZO';
        }elseif($id_mes =='4'){
            $mes_rol = 'ABRIL';
        }elseif($id_mes =='5'){
            $mes_rol = 'MAYO';
        }elseif($id_mes =='6'){
            $mes_rol = 'JUNIO';
        }elseif($id_mes =='7'){
            $mes_rol = 'JULIO';
        }elseif($id_mes =='8'){
            $mes_rol = 'AGOSTO';
        }elseif($id_mes =='9'){
            $mes_rol = 'SEPTIEMBRE';
        }elseif($id_mes =='10'){
            $mes_rol = 'OCTUBRE';
        }elseif($id_mes =='11'){
            $mes_rol = 'NOVIEMBRE';
        }elseif($id_mes =='12'){
            $mes_rol = 'DICIEMBRE';
        }
        
        $rol_det_consulta = DB::table('ct_rol_pagos as rp')
                            ->where('rp.id_empresa', $id_empresa)
                            ->where('rp.anio', $id_anio)
                            ->where('rp.mes', $id_mes)
                            ->where('rp.estado', 1)
                            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
                            ->select('rp.id_empresa as idempresa'
                                     ,'rp.anio as anio'
                                     ,'rp.mes as mes'
                                     ,'rp.id_user as usuario'
                                     ,'rp.id_nomina as id_nomina'
                                     ,'drol.sueldo_mensual as sueldo'
                                     ,'drol.cantidad_horas50 as cantidad_horas_50'
                                     ,'drol.sobre_tiempo50 as valor_horas_50'
                                     ,'drol.cantidad_horas100 as cantidad_horas_100'
                                     ,'drol.sobre_tiempo100 as valor_horas_100'
                                     ,'drol.bonificacion as bonificacion'
                                     ,'drol.alimentacion as alimentacion'
                                     ,'drol.bono_imputable as bono_imputable'
                                     ,'drol.transporte as transporte'
                                     ,'drol.exam_laboratorio as exam_laboratorio'
                                     ,'drol.fondo_reserva as fondo_reserva'
                                     ,'drol.decimo_tercero as decimo_tercero'
                                     ,'drol.decimo_cuarto as decimo_cuarto'
                                     ,'drol.porcentaje_iess as porcentaje_iess'
                                     ,'drol.seguro_privado as seguro_privado'
                                     ,'drol.impuesto_renta as impuesto_renta'
                                     ,'drol.multa as multa'
                                     ,'drol.fond_reserv_cobrar as fond_res_cobr' //Nuevo
                                     ,'drol.otros_egresos as otro_egres' //Nuevo
                                     ,'drol.prestamos_empleado as prestamo_empleado'
                                     ,'drol.saldo_inicial_prestamo as sal_ini_prest' //Nuevo
                                     ,'drol.anticipo_quincena as anticipo_quincena'
                                     ,'drol.otro_anticipo as otr_anticip' //Nuevo
                                     ,'drol.total_ingresos as total_ingreso'
                                     ,'drol.total_egresos as total_egreso'
                                     ,'drol.neto_recibido as neto_recibido'
                                     ,'drol.total_quota_quirog as total_cuot_quir'
                                     ,'drol.parqueo'
                                     ,'drol.total_quota_hipot as total_cuot_hipot')->get();

        //$fecha_d = date('Y/m/d');
        Excel::create('Reporte Rol Pago Empleados RRHH'.' : '.$mes_rol.' DEL '.$id_anio, function($excel) use($rol_det_consulta,$empresa,$id_anio,$mes_rol){
            $excel->sheet('Datos Rol Pago', function($sheet) use($rol_det_consulta,$empresa,$id_anio,$mes_rol){
                $fecha_d = date('Y/m/d');
                $i = 5;
                $sum_salario = 0;
                $sum_cantid_horas_50 = 0;
                $sum_cantid_horas_100 = 0;
                $sum_horas_50 = 0;
                $sum_horas_100 = 0;
                $sum_bono = 0;
                $sum_bonoimp = 0;
                $sum_alimentacion = 0;
                $sum_transporte = 0;
                $sum_fondo_reserva = 0;
                $sum_decimo_tercero = 0;
                $sum_decimo_cuarto = 0;
                $sum_total_ingreso = 0;
                $sum_iess = 0;
                $sum_seg_priv = 0;
                $sum_imp_renta = 0;
                $sum_multa = 0;
                $sum_fond_resev_cob = 0;
                $sum_otro_egre = 0;
                $sum_sald_inicial = 0;
                $sum_otr_anticipo = 0;
                $sum_parqueo = 0;

                $sum_examlab = 0;
                $sum_prestamo = 0;
                $sum_anticipo_quincena = 0;
                $sum_total_egreso = 0;
                $sum_total_hip = 0;
                $sum_total_quir = 0;
                $sum_neto_recibido = 0;

                $sheet->mergeCells('A1:AJ1');
                $mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                $sheet->cell('A1', function($cell) use($fecha2,$id_anio,$mes_rol){
                    // manipulate the cel
                    $cell->setValue('DETALLE ROL PAGOS EMPLEADOS'.' : '.$mes_rol.' DEL '.$id_anio);
                    $cell->setFontSize('15');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->mergeCells('A2:AJ2');
                $sheet->cell('A2', function($cell) use ($empresa){
                    // manipulate the cel
                    if(!is_null($empresa)){
                     $cell->setValue($empresa->nombrecomercial);
                    }
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->cells('A2', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A3:AJ3');
                $sheet->cell('A3', function($cell) use ($empresa){
                    // manipulate the cel
                    if(!is_null($empresa)){
                     $cell->setValue($empresa->id);
                    }
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->cells('A3', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('A1:K3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES DE EMPLEADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IDENTIFICACIÒN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUELDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AÑO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD HR AL 50%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue(' VALOR HORAS AL 50%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD HR AL 100%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR HORAS AL 100%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO IMPUTABLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ALIMENTACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSPORTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FONDO RESERVA M/A');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR FONDO RESERVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DECIMO TERCERO M/A');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR DECIMO III');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DECIMO CUARTO M/A');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR DECIMO IV');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL INGRESOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('9.45% IESS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO PRIVADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO A LA RENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MULTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FONDO RESERVA COBRAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Z4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS EGRESOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EXAMEN DE LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO INICIAL PRESTAMO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ANTICIPO QUINCENA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OTRO ANTICIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AF4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS HIPOTECARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //Prestamos Hipotecarios
                $sheet->cell('AG4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS QUIROGRAFARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARQUEO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //Prestamos Quirografarios
                $sheet->cell('AI4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL EGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });                
                
                $sheet->cell('AJ4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NETO RECIBIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A4:AJ4', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                // DETALLES
                $sheet->setColumnFormat(array(
                    'C' => '0.00','G' => '0.00','I' => '0.00', 
                    'J' => '0.00','K' => '0.00','M' => '0.00',
                    'O' => '0.00','Q' => '0.00','R' => '0.00',
                    'S' => '0.00','T' => '0.00','U' => '0.00',
                    'V' => '0.00','W' => '0.00','X' => '0.00',
                    'Y' => '0.00','Z' => '0.00','AA' => '0.00',
                    'AB' => '0.00', 'AC' => '0.00', 'AD' => '0.00', 'AE' => '0.00','AF' => '0.00','AG' => '0.00','AH' => '0.00','AJ' => '0.00','AI' => '0.00','P' => '0.00'
                ));

                foreach($rol_det_consulta as $value){
                    $txtcolor='#000000';

                    $empresa = null;
                    $usuario = null;
                    $dat_nomina = null;

                    if($value->id_nomina!=null){
                        $dat_nomina = Ct_Nomina::find($value->id_nomina);
                    }
                    
                    if($value->usuario!=null){
                        $usuario = User::find($value->usuario);
                        $nombre_paciente = $usuario->apellido1 . " ";

                        if ($usuario->apellido2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->apellido2 . " ";
                        }

                        $nombre_paciente = $nombre_paciente . $usuario->nombre1 . " ";
                        if ($usuario->nombre2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->nombre2 . " ";
                        }
                    }

                    $sheet->cell('A'.$i, function($cell) use($nombre_paciente, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($nombre_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->usuario);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('C'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->sueldo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->anio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E'.$i, function($cell) use($value, $txtcolor){
                        $mes='';
                        
                        if($value->mes=='1'){
                            $mes = 'ENERO';    
                        }elseif($value->mes=='2'){
                            $mes = 'FEBRERO';
                        }elseif($value->mes=='3'){
                            $mes = 'MARZO';
                        }elseif($value->mes=='4'){
                            $mes = 'ABRIL';
                        }elseif($value->mes=='5'){
                            $mes = 'MAYO';
                        }elseif($value->mes=='6'){
                            $mes = 'JUNIO';
                        }elseif($value->mes=='7'){
                            $mes = 'JULIO';
                        }elseif($value->mes=='8'){
                            $mes = 'AGOSTO';
                        }elseif($value->mes=='9'){
                            $mes = 'SEPTIEMBRE';
                        }elseif($value->mes=='10'){
                            $mes = 'OCTUBRE';
                        }elseif($value->mes=='11'){
                            $mes = 'NOVIEMBRE';
                        }elseif($value->mes=='12'){
                            $mes = 'DICIEMBRE';
                        }
                        
                        // manipulate the cel
                        $cell->setValue($mes);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F'.$i, function($cell) use($value, $txtcolor){
                        
                        // manipulate the cel
                        $cell->setValue($value->cantidad_horas_50);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->valor_horas_50);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->cantidad_horas_100);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('I'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->valor_horas_100);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->bonificacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->bono_imputable);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('L'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->alimentacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('M'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->transporte);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('N'.$i, function($cell) use($dat_nomina, $txtcolor){
                        $men_acum ='';
                        
                        if($dat_nomina->pago_fondo_reserva =='1'){
                            $men_acum = 'ACUMULA';    
                        }elseif($dat_nomina->pago_fondo_reserva =='2'){
                            $men_acum = 'MENSUALIZA';
                        }
                        // manipulate the cel
                        $cell->setValue($men_acum);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('O'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->fondo_reserva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('P'.$i, function($cell) use($dat_nomina, $txtcolor){
                        $men_acum ='';
                        
                        if($dat_nomina->decimo_tercero =='1'){
                            $men_acum = 'ACUMULA';    
                        }elseif($dat_nomina->decimo_tercero =='2'){
                            $men_acum = 'MENSUALIZA';
                        }
                        // manipulate the cel
                        $cell->setValue($men_acum);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Q'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->decimo_tercero);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('R'.$i, function($cell) use($dat_nomina, $txtcolor){
                        $men_acum ='';
                        
                        if($dat_nomina->decimo_cuarto =='1'){
                            $men_acum = 'ACUMULA';    
                        }elseif($dat_nomina->decimo_cuarto =='2'){
                            $men_acum = 'MENSUALIZA';
                        }
                        // manipulate the cel
                        $cell->setValue($men_acum);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('S'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->decimo_cuarto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('T'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->total_ingreso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('U'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->porcentaje_iess);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('V'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->seguro_privado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('W'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->impuesto_renta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->multa);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('Y'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->fond_res_cobr);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('Z'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->otro_egres);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
   
                    $sheet->cell('AA'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->exam_laboratorio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AB'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->sal_ini_prest);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AC'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->prestamo_empleado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AD'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->anticipo_quincena);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AE'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->otr_anticip);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AF'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->total_cuot_hipot);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AG'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->total_cuot_quir);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AH'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->parqueo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AI'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->total_egreso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AJ'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->neto_recibido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sum_salario = $sum_salario+$value->sueldo;
                    $sum_cantid_horas_50 = $sum_cantid_horas_50+$value->cantidad_horas_50;
                    $sum_cantid_horas_100 = $sum_cantid_horas_100+$value->cantidad_horas_100;
                    $sum_horas_50 = $sum_horas_50+$value->valor_horas_50;
                    $sum_horas_100 = $sum_horas_100+$value->valor_horas_100;
                    $sum_bono = $sum_bono+$value->bonificacion;
                    $sum_bonoimp = $sum_bonoimp+$value->bono_imputable;
                    $sum_alimentacion = $sum_alimentacion+$value->alimentacion;
                    $sum_transporte = $sum_transporte+$value->transporte;
                    $sum_fondo_reserva = $sum_fondo_reserva+$value->fondo_reserva;
                    $sum_decimo_tercero = $sum_decimo_tercero+$value->decimo_tercero;
                    $sum_decimo_cuarto = $sum_decimo_cuarto+$value->decimo_cuarto;
                    $sum_total_ingreso = $sum_total_ingreso+$value->total_ingreso;
                    $sum_iess = $sum_iess+$value->porcentaje_iess;
                    $sum_seg_priv = $sum_seg_priv+$value->seguro_privado;
                    $sum_imp_renta = $sum_imp_renta+$value->impuesto_renta;
                    $sum_multa = $sum_multa+$value->multa;
                    $sum_parqueo = $sum_parqueo + $value->parqueo;
                    $sum_fond_resev_cob = $sum_fond_resev_cob+$value->fond_res_cobr;

                    $sum_otro_egre = $sum_otro_egre+$value->otro_egres;

                    $sum_examlab =   $sum_examlab+$value->exam_laboratorio;

                    $sum_sald_inicial = $sum_sald_inicial+$value->sal_ini_prest;

                    $sum_otr_anticipo = $sum_otr_anticipo+$value->otr_anticip;

                    $sum_prestamo = $sum_prestamo+$value->prestamo_empleado;
                    $sum_anticipo_quincena = $sum_anticipo_quincena+$value->anticipo_quincena;
                    $sum_total_egreso = $sum_total_egreso+$value->total_egreso;
                    $sum_total_hip = $sum_total_hip+$value->total_cuot_hipot;
                    $sum_total_quir = $sum_total_quir+$value->total_cuot_quir;
                    $sum_neto_recibido = $sum_neto_recibido+$value->neto_recibido;
                    
                    $i= $i+1;
                }

                $txtcolor='#000000';
                $sheet->cell('A'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('TOTALES');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('A'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('B'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('B'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('C'.$i, function($cell) use($sum_salario,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_salario);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('D'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F'.$i, function($cell) use($sum_cantid_horas_50,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_cantid_horas_50);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('F'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('G'.$i, function($cell) use($sum_horas_50,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_horas_50);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('G'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('H'.$i, function($cell) use($sum_cantid_horas_100,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_cantid_horas_100);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('H'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('I'.$i, function($cell) use($sum_horas_100,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_horas_100);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('I'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('J'.$i, function($cell) use($sum_bono,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_bono);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('J'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('K'.$i, function($cell) use($sum_bonoimp,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_bonoimp);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('K'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('L'.$i, function($cell) use($sum_alimentacion,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_alimentacion);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('L'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('M'.$i, function($cell) use($sum_transporte,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_transporte);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('M'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                
                $sheet->cell('N'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('N'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('O'.$i, function($cell) use($sum_fondo_reserva,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_fondo_reserva);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('O'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('P'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('P'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('Q'.$i, function($cell) use($sum_decimo_tercero,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_decimo_tercero);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('Q'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('R'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('R'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('S'.$i, function($cell) use($sum_decimo_cuarto,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_decimo_cuarto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('S'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('T'.$i, function($cell) use($sum_total_ingreso,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_total_ingreso);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('T'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('U'.$i, function($cell) use($sum_iess,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_iess);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('U'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('V'.$i, function($cell) use($sum_seg_priv,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_seg_priv);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('V'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('W'.$i, function($cell) use($sum_imp_renta,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_imp_renta);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('W'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('X'.$i, function($cell) use($sum_multa,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_multa);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('X'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                
                //Nuevo
                $sheet->cell('Y'.$i, function($cell) use($sum_fond_resev_cob,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_fond_resev_cob);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('Y'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });


                $sheet->cell('Z'.$i, function($cell) use($sum_otro_egre,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_otro_egre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('Z'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                

                $sheet->cell('AA'.$i, function($cell) use($sum_examlab,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_examlab);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AA'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });


                $sheet->cell('AB'.$i, function($cell) use($sum_sald_inicial,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_sald_inicial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AB'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                
                $sheet->cell('AC'.$i, function($cell) use($sum_prestamo,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_prestamo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AC'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AD'.$i, function($cell) use($sum_anticipo_quincena,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_anticipo_quincena);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AD'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('AE'.$i, function($cell) use($sum_otr_anticipo,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_otr_anticipo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AE'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                //Suma Total Hipotecario
                $sheet->cell('AF'.$i, function($cell) use($sum_total_hip,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_total_hip);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AF'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                //Suma Total Quirografario
                $sheet->cell('AG'.$i, function($cell) use($sum_total_quir,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_total_quir);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AG'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AH'.$i, function($cell) use($sum_parqueo,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_parqueo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AH'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AI'.$i, function($cell) use($sum_total_egreso,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_total_egreso);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AI'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AJ'.$i, function($cell) use($sum_neto_recibido,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_neto_recibido);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AJ'.$i, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
            });
            
            //$excel->getActiveSheet()->getColumnDimension("AC")->setWidth(15)->setAutosize(false);
            //$excel->getActiveSheet()->getColumnDimension("AD")->setWidth(15)->setAutosize(false);
            
        })->export('xlsx');
       

    }



    
    
}
