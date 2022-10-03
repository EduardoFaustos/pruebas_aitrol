<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\User;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Excel;

class NominaReporteBancoController extends Controller
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

        return view('contable.rh_reporte_banco.index',['empresas' => $empresas]);
    }

    public function reporte_datos_banco(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];
        $fecha_proc = date('d/m/Y');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $rol_form_pag = [];
        if($request['tipo']== '1'){
        $rol_form_pag = DB::table('ct_nomina')
            ->leftjoin('ct_rh_valor_anticipos','ct_rh_valor_anticipos.id_user','ct_nomina.id_user')
            ->join('ct_rol_forma_pago', 'ct_rol_forma_pago.id_rol_pago', 'ct_rh_valor_anticipos.id_tipo_pago')
            ->where('ct_rh_valor_anticipos.anio', $id_anio)
            ->where('ct_rh_valor_anticipos.mes', $id_mes)
            ->where('ct_rh_valor_anticipos.id_empresa', $id_empresa)
            ->select('ct_nomina.*','ct_rh_valor_anticipos.*','ct_rol_forma_pago.*')->get();
        }
        if($request['tipo']== '2'){
            $rol_form_pag = DB::table('ct_nomina')
                ->leftjoin('ct_rol_pagos','ct_rol_pagos.id_user','ct_nomina.id_user')
                ->join('ct_rol_forma_pago', 'ct_rol_forma_pago.id_rol_pago', 'ct_rol_pagos.id_tipo_rol')
                ->where('ct_rol_pagos.id_empresa', $id_empresa)
                ->where('ct_rol_pagos.anio', $id_anio)
                ->where('ct_rol_pagos.mes', $id_mes)
                ->where('ct_rol_pagos.estado', 1)
                ->groupBy('ct_nomina.id_user')
                ->select('ct_nomina.*','ct_rol_pagos.*','ct_rol_forma_pago.*')
                ->get();
         }
         
        Excel::create('REPORTE BANCO - EMPLEADO POR EMPRESA', function($excel) use($empresa,$fecha_proc,$rol_form_pag){
            $excel->sheet('Informacion Roles de Pago', function($sheet) use($empresa,$fecha_proc,$rol_form_pag){

                //$fecha_d = date('Y/m/d');
                $i = 3;
                $j = 0;

                $sum_valor = 0;
                $cont_empl = 0;

                $sheet->mergeCells('A1:K1');
                /*$mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} SS
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);*/

                $sheet->cell('A1', function($cell) use($empresa){
                    // manipulate the cel
                    $cell->setValue('REPORTE BANCO');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K1', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->cell('A2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Forma Pag/Cob');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Banco');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Num.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Identificacion');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Doc.');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NUC');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Telefono');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Referencia');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A2:K2', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                // DETALLES
                $sheet->setColumnFormat(array(
                    'E' => '0.00', 
                ));
                foreach($rol_form_pag as $value){
                    //dd($value);
                    $txtcolor='#000000';

                    $usuario = null;

                    if($value->id_user != null){
                        $usuario = User::find($value->id_user);
                        
                        $nombre_paciente = $usuario->apellido1 . " ";

                        if ($usuario->apellido2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->apellido2 . " ";
                        }
                        
                        $nombre_paciente = $nombre_paciente . $usuario->nombre1 . " ";
                        if ($usuario->nombre2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->nombre2 . " ";
                        }
                    }

                    $sheet->cell('A'.$i, function($cell) use($value, $txtcolor){
                        $tipo_pago ='';
                        // CU:ACREDITACION,EF:EFECTIVO,CH:CHEQUE 
                        if($value->id_tipo_pago=='1'){
                            $tipo_pago = 'CU';    
                        }elseif($value->id_tipo_pago =='2'){
                            $tipo_pago = 'EF';
                        }elseif($value->id_tipo_pago =='2'){
                            $tipo_pago = 'CH';
                        }
                        
                        // manipulate the cel
                        $cell->setValue($tipo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B'.$i, function($cell) use($value, $txtcolor){
                        $banco_dep ='';
                        // Bancos
                        if($value->banco=='2'){
                            $banco_dep = '30';    
                        }elseif($value->banco =='1'){
                            $banco_dep = '10';
                        }elseif($value->banco =='4'){
                            $banco_dep = '32';
                        }elseif($value->banco =='7'){
                            $banco_dep = '35';
                        }elseif($value->banco =='5'){
                            $banco_dep = '37';
                        }elseif($value->banco =='9'){
                            $banco_dep = '42';
                        }
                        
                        // manipulate the cel
                        $cell->setValue($banco_dep);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C'.$i, function($cell) use($value, $txtcolor){
                        $tip_cuent ='';
                        // Tipos de Cuenta 
                        //10: AHORRO 00:CORRIENTE 
                        if($value->id_tipo_cuenta=='1'){
                            $tip_cuent = '10';    
                        }elseif($value->id_tipo_cuenta =='2'){
                            $tip_cuent = '00';
                        }
                        
                        // manipulate the cel
                        $cell->setValue($tip_cuent);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->numero_cuenta);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->id_user);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        //C: CEDULA, R:RUC, P:PASAPORTE, X:NINGUNO
                        $cell->setValue('C');
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->id_user);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I'.$i, function($cell) use($nombre_paciente, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($nombre_paciente);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J'.$i, function($cell) use($usuario, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($usuario->telefono1);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K'.$i, function($cell) use($txtcolor){
                        // manipulate the cel
                        $cell->setValue('RP');
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sum_valor = $sum_valor+$value->valor;
                    
                    $i= $i+1;
                    $cont_empl= $cont_empl+1;
                }

                $j = $i+1;
                $k = $j+1;
                $l = $k+1;
                $txtcolor='#000000';

                //Subtotales
                $sheet->cell('A'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('SUBTOTALES');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K'.$i, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Termina Sub Total
                //Total
                $sheet->cell('A'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('B'.$j, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('C'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('FORMA');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C'.$j, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('CANT.');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                
                $sheet->cells('D'.$j, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E'.$j, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K'.$j, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Fin TOtal
                //USD
                $sheet->cell('A'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('USD');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('CU');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D'.$k, function($cell) use($cont_empl,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E'.$k, function($cell) use($sum_valor,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K'.$k, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //FIN USD
                //TOTAL GENERAL
                $sheet->cell('A'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('TOTAL GENERAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('DOLARES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C'.$l, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D'.$l, function($cell) use($cont_empl,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('D'.$l, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E'.$l, function($cell) use($sum_valor,$txtcolor){
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E'.$l, function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K'.$l, function($cell) use($txtcolor){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                //FIN TOTAL GENERAL


                



               
            });
            
        })->export('xlsx');
        
    }

}
