<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Seguro;
use Sis_medico\Paciente;
use Response;
use Excel;
use PHPExcel_Style_NumberFormat;
use Carbon\Carbon;

class Ap_ArchivoMspController extends Controller
{
	protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4, 5, 11,22)) == false){
          return true;
        }
    }

    /**********************************/
    /*****AUTOCOMPLETA MES PLANO*******/
    /**********************************/
    public function search_mes_plano(Request $request){

        $mes_plano = $request['term'];

        $data         = null;

        $seteo = '%' . $mes_plano . '%';

       
        $query1 = Archivo_Plano_Cabecera::select('mes_plano')
                                          ->orderBy('mes_plano')
                                          ->groupBy('mes_plano')
                                          ->where('estado','1')
                                          ->get();
        
        foreach ($query1 as $value) {
            $data[] = array('value' => $value->mes_plano);
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }
    
    
    
    /**********************************/
    /*****CREA ARCHIVO PLANO MSP****/
    /**********************************/
    public function crear_ap_msp(Request $request){

        $mes_plan = Archivo_Plano_Cabecera::select('mes_plano')
                                            ->orderBy('mes_plano')
                                            ->groupBy('mes_plano')
                                            ->where('estado','1')
                                            ->get();

        $empresas = Empresa::where('admision','1')->get();

        //dd($empresas);
        
        return view('archivo_plano/archivo/archivo_plano_msp',['empresas' =>$empresas,'mes_plan'=>$mes_plan]);

    }

    /**********************************/
    /*****BUSCAR MES PLANO Y EMPRESA***/
    /**********************************/
    public function search_mes_empresa(Request $request){

        $mes_pla =$request->mes_plano;
        $id_empresa =$request->id_empresa;

                $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_pla)
                    ->where('archivo_plano_cabecera.id_seguro',5)
                    ->where('archivo_plano_cabecera.id_empresa',$id_empresa)
                    ->where('archivo_plano_cabecera.estado','1')
                    ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
                    ->where('apd.estado','1')
                    ->select('archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario'
                    ,'archivo_plano_cabecera.fecha_ing','apd.descripcion'
                    ,'apd.codigo','apd.cantidad','apd.valor'
                    ,'apd.porcentaje10','archivo_plano_cabecera.presuntivo_def'
                    ,'apd.iva','apd.clasificador','archivo_plano_cabecera.cie10'
                    ,'apd.porcentaje_iva','archivo_plano_cabecera.id_hc'
                    ,'archivo_plano_cabecera.cod_deriva_msp','apd.valor_unitario','apd.tipo'
                    ,'apd.subtotal','apd.total_solicitado_usd','apd.valor_porcent_clasifi')->get();

        return view('archivo_plano/archivo/busqueda_mes_empresa',['archivo_plano' => $archivo_plano]);

    }


    /*******************************************/
    /**********CREAR ARCHIVO PLANO MSP**********/
    /*******************************************/
    public function crear_ap_msp_excel(Request $request){

        $total_inicio = 0;
        $numero_expediente = 0;
        $numero_expediente_final = 0;
        $mes_plan = $request['mes_plano'];
        $id_empresa = $request['id_empresa'];
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $fecha_d = date('Y/m/d');

        //Mes Servicio
        $mes_servicio = substr($mes_plan,0,2);
        
        //Anio servicio
        $anio_servicio = substr($mes_plan,2, 6);
    
    
        
        //Obtenemos el mes
        $mes_letra ='';
        if($mes_servicio == '01'){ $mes_letra = "ENERO";} 
        if($mes_servicio == '02'){ $mes_letra = "FEBRERO";} 
        if($mes_servicio == '03'){ $mes_letra = "MARZO";} 
        if($mes_servicio == '04'){ $mes_letra = "ABRIL";} 
        if($mes_servicio == '05'){ $mes_letra = "MAYO";} 
        if($mes_servicio == '06'){ $mes_letra = "JUNIO";} 
        if($mes_servicio == '07'){ $mes_letra = "JULIO";} 
        if($mes_servicio == '08'){ $mes_letra = "AGOSTO";}  
        if($mes_servicio == '09'){ $mes_letra = "SEPTIEMBRE";} 
        if($mes_servicio == '10'){ $mes_letra = "OCTUBRE";} 
        if($mes_servicio == '11'){ $mes_letra = "NOVIEMBRE";} 
        if($mes_servicio == '12'){ $mes_letra = "DICIEMBRE";}

        $mes_anio_servicio = $mes_letra."-".$anio_servicio;

            $expediente_monto = Archivo_Plano_Cabecera::where('mes_plano', $mes_plan)
                                                        ->where('id_seguro',5)
                                                        ->where('estado','1')
                                                        ->where('id_empresa',$id_empresa)
                                                        ->select('id')
                                                        ->get();
            
            foreach ($expediente_monto as $value){

                $plano_det = DB::table('archivo_plano_detalle as apd')
                            ->where('apd.id_ap_cabecera', $value->id)
                            ->where('apd.estado','1')
                            ->groupBy('apd.id_ap_cabecera')
                            ->select(DB::raw("SUM(apd.total_solicitado_usd) as valor_solicitud"))
                            ->first();
                
                
                if(!is_null($plano_det)){
                    $total_inicio+=$plano_det->valor_solicitud;
                }
    
                $numero_expediente = $numero_expediente+1;

            }

            $numero_expediente_final = $numero_expediente-1;
            
            $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plan)
                    ->where('archivo_plano_cabecera.id_seguro',5)
                    ->where('archivo_plano_cabecera.id_empresa',$id_empresa)
                    ->where('archivo_plano_cabecera.estado','1')
                    ->join('paciente as pac','pac.id','archivo_plano_cabecera.id_paciente')
                    ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
                    ->where('apd.estado','1')
                    ->select(DB::raw('CONCAT(pac.apellido1, pac.apellido2, pac.nombre1, pac.nombre2) AS full_name'),'pac.*','archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario'
                    ,'archivo_plano_cabecera.fecha_ing','apd.descripcion'
                    ,'apd.codigo','apd.cantidad','apd.valor'
                    ,'apd.porcentaje10','archivo_plano_cabecera.presuntivo_def'
                    ,'apd.iva','apd.clasificador','archivo_plano_cabecera.cie10'
                    ,'apd.porcentaje_iva','archivo_plano_cabecera.id_hc'
                    ,'archivo_plano_cabecera.cod_deriva_msp','apd.valor_unitario','apd.tipo'
                    ,'apd.subtotal','apd.total_solicitado_usd','apd.valor_porcent_clasifi','apd.clasif_porcentaje_msp')
                    ->orderby('full_name')
                    ->get();
       
            $fecha_d = date('Y/m/d');
            Excel::create('Archivo Plano Msp'.$fecha_d, function($excel) use($archivo_plano,$empresa,$mes_anio_servicio,$numero_expediente_final,$total_inicio){
    
                $excel->sheet('Archivo Plano Msp', function($sheet) use($archivo_plano,$empresa,$mes_anio_servicio,$numero_expediente_final,$total_inicio){

                    $fecha_d = date('Y/m/d');
                    $i = 6;

                    $sheet->mergeCells('A1:M1');
               
                    $mes = substr($fecha_d, 5, 2); 
                    if($mes == '01'){ $mes_letra = "ENERO";} 
                    if($mes == '02'){ $mes_letra = "FEBRERO";} 
                    if($mes == '03'){ $mes_letra = "MARZO";} 
                    if($mes == '04'){ $mes_letra = "ABRIL";} 
                    if($mes == '05'){ $mes_letra = "MAYO";} 
                    if($mes == '06'){ $mes_letra = "JUNIO";} 
                    if($mes == '07'){ $mes_letra = "JULIO";} 
                    if($mes == '08'){ $mes_letra = "AGOSTO";}  
                    if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                    if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                    if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                    if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                    $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' del '.substr($fecha_d, 0, 4);
                    
                    $sheet->cell('A1', function($cell) use($fecha2){
                        $cell->setValue('ARCHIVO PLANO MSP'.' - '.$fecha2);
                        $cell->setFontSize('14');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A2:M2');
                    $sheet->cell('A2', function($cell) use ($empresa){
                        // manipulate the cel
                        if(!is_null($empresa)){
                        $cell->setValue($empresa->razonsocial);
                        }
                        $cell->setFontWeight('bold'); 
                        $cell->setFontSize('15');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A3:B3');
                    $sheet->cell('A3', function($cell) {
                        $cell->setValue('Mes y Año del Servicio:');
                        $cell->setBorder('thin', '', '', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('C3', function($cell) use($mes_anio_servicio){
                        // manipulate the cell
                        $cell->setValue($mes_anio_servicio);
                        $cell->setBorder('', '', '', '');
                    });
                    $sheet->mergeCells('A4:B4');
                    $sheet->cell('A4', function($cell) {
                        $cell->setValue('Tipo Servicio:');
                        $cell->setBorder('', '', '', 'thin');
                        $cell->setFontWeight('bold'); 
                    });
                    $sheet->cell('C4', function($cell) {
                        // manipulate the cell
                        $cell->setValue('AMBULATORIO');
                        $cell->setBorder('', '', '', '');
                    });
                    $sheet->cell('G3', function($cell) {
                        $cell->setValue('N° Expedientes:');
                        $cell->setBorder('', '', '', ''); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('H3', function($cell) use($numero_expediente_final){
                        // manipulate the cell
                        $cell->setValue($numero_expediente_final);
                        $cell->setBorder('', '', '', '');
                    });
                    $sheet->cell('G4', function($cell) {
                        $cell->setValue('Monto Solicitado:');
                        $cell->setBorder('', '', '', ''); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('H4', function($cell) use($total_inicio){
                        // manipulate the cell
                        $valor_inicio = number_format($total_inicio, 2);
                        $cell->setValue($valor_inicio);
                        $cell->setBorder('', '', '', '');
                        $cell->setAlignment('right');
                    });

                    $sheet->cell('M3', function($cell) {
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', '', ''); 
                    });
                    $sheet->cell('M4', function($cell) {
                        $cell->setValue('');
                        $cell->setBorder('', 'thin', '', ''); 
                    });
                    $sheet->cell('I3', function($cell) {
                        $cell->setValue('');
                        $cell->setBorder('', '', '', ''); 
                    });
                    $sheet->cell('I4', function($cell) {
                        $cell->setValue('');
                        $cell->setBorder('', '', '', ''); 
                    });

                    $sheet->cell('A5', function($cell) {
                        $cell->setValue('N°');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B5', function($cell) {
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('C5', function($cell) {
                        $cell->setValue('Codigo de Validacion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('D5', function($cell) {
                        $cell->setValue('Beneficiario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('E5', function($cell) {
                        $cell->setValue('Código TSNS');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('F5', function($cell) {
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('G5', function($cell) {
                        $cell->setValue('Clasificador');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold'); 
                    });
                   
                    $sheet->cell('H5', function($cell) {
                        $cell->setValue('Cantidad Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold'); 
                    });
                    $sheet->cell('I5', function($cell) {
                        $cell->setValue('Precio Unitario ');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold'); 
                    });
                    
                    $sheet->cell('J5', function($cell) {
                        $cell->setValue('Clasificador %');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K5', function($cell) {
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L5', function($cell) {
                        $cell->setValue('Valor por Modificador');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');

                    });
                    $sheet->cell('M5', function($cell) {
                        $cell->setValue('Valor Solicitado');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                        $cell->setFontWeight('bold');
                    });
                    

                    $j=1;
                    $x=0;
                    $id_temporal=0; $tota_sol=0;
                    foreach($archivo_plano as $value){
                       
                        if($value->id_paciente != $id_temporal) {
                            $id_temporal=$value->id_paciente;
                            $x++;
                        } 
                        $sheet->cell('A'.$i, function ($cell) use($x){
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue(substr($value->fecha_ing,0,10));
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->cod_deriva_msp);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E'.$i, function ($cell) use($value){
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                               $cell->setValue(' ');
                            }
                            else{
                                $cell->setValue($value->codigo);
                            }
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('F'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->clasificador);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                       
                        $sheet->cell('H'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('I'.$i, function ($cell) use($value){
                            // manipulate the cel
                            if($value->clasif_porcentaje_msp == 50){

                                $cell->setValue($value->valor*2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            }else{
                              
                                $cell->setValue($value->valor);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            }

                        });
                       
                        $sheet->cell('J'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->clasif_porcentaje_msp);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('K'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->subtotal);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->valor_porcent_clasifi);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('M'.$i, function ($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->total_solicitado_usd);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                         
                        $tota_sol+=$value->total_solicitado_usd; 
                        
                        $j=$j+1;
                        $i= $i+1;

                    }

                    $i=$i;
                    
                    $sheet->cell('L'.$i, function ($cell){
                        // manipulate the cel
                        $cell->setValue('Total:');
                        $cell->setAlignment('right');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });
    
                    $sheet->cell('M'.$i, function($cell) use($tota_sol){
                        // manipulate the cel
                        $valor_total = number_format($tota_sol, 2);
                        $cell->setValue($valor_total);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });
    


                });
                
                $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("F")->setWidth(23)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
                $excel->getActiveSheet()->getStyle("A3:M8000")->getFont()->setSize(8);
                $excel->getActiveSheet()->getStyle('A5:M8000')->getAlignment()->setWrapText(true);

            })->export('xlsx');

    }


    /***********************************************************/
    /**********CREAR PLANILLA DE CARGO CONSOLIDADO MSP**********/
    /***********************************************************/
    public function crear_reporte_consolidado(Request $request){

        $mes_plan = $request['mes_plan'];
        $id_empresa = $request['id_empr'];
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $fecha_elaboracion = date('d/m/Y');
        
        $fecha_d = date('Y/m/d');


        //Anio servicio
        $anio_servicio = substr($fecha_d, 0, 4);
     
        //Mes Servicio
        $mes_servicio = substr($fecha_d, 5, 2); 

        //Obtenemos el mes
        $mes_letra ='';
        if($mes_servicio == 01){ $mes_letra = "ENERO";} 
        if($mes_servicio == 02){ $mes_letra = "FEBRERO";} 
        if($mes_servicio == 03){ $mes_letra = "MARZO";} 
        if($mes_servicio == 04){ $mes_letra = "ABRIL";} 
        if($mes_servicio == 05){ $mes_letra = "MAYO";} 
        if($mes_servicio == 06){ $mes_letra = "JUNIO";} 
        if($mes_servicio == 07){ $mes_letra = "JULIO";} 
        if($mes_servicio == '08'){ $mes_letra = "AGOSTO";}  
        if($mes_servicio == '09'){ $mes_letra = "SEPTIEMBRE";} 
        if($mes_servicio == '10'){ $mes_letra = "OCTUBRE";} 
        if($mes_servicio == '11'){ $mes_letra = "NOVIEMBRE";} 
        if($mes_servicio == '12'){ $mes_letra = "DICIEMBRE";}

        $mes_anio_servicio = $mes_letra."-".$anio_servicio;

        /*$archivo_plano = Archivo_Plano_Cabecera::where('mes_plano', $mes_plan)
                                                ->where('id_seguro',5)
                                                ->where('estado','1')
                                                ->where('id_empresa',$id_empresa)
                                                ->select('id','derivacion_nc_msp','cod_deriva_msp','id_paciente')
                                                ->get();*/

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plan)
                                                ->where('archivo_plano_cabecera.id_seguro',5)
                                                ->where('archivo_plano_cabecera.estado','1')
                                                ->join('paciente as pac','pac.id','archivo_plano_cabecera.id_paciente')
                                                ->where('archivo_plano_cabecera.id_empresa',$id_empresa)
                                                ->select(DB::raw('CONCAT(pac.apellido1, pac.apellido2, pac.nombre1, pac.nombre2) AS full_name'),'pac.*','archivo_plano_cabecera.id','archivo_plano_cabecera.derivacion_nc_msp','archivo_plano_cabecera.cod_deriva_msp','archivo_plano_cabecera.id_paciente')
                                                ->orderby('full_name')
                                                ->get();

        $total_inicio = 0;
        $numero_expediente = 0;
        
        foreach ($archivo_plano as $value){

            /*$plano_det = DB::table('archivo_plano_detalle as apd')
                        ->where('apd.id_ap_cabecera', $value->id)
                        ->where('apd.estado','1')
                        ->groupBy('apd.id_ap_cabecera')
                        ->select(DB::raw("SUM(apd.total_solicitado_usd) as valor_solicitud"))
                        ->first();*/
            
            $plano_det = $value->detalles->where('estado','1')->sum('total_solicitado_usd');
            //dd($plano_det);


            if(!is_null($plano_det)){
                $total_inicio+=$plano_det;
            }

            $numero_expediente = $numero_expediente+1;

        }


        Excel::create('Reporte Planilla Consolidado MSP ', function($excel) use($archivo_plano,$empresa,$fecha_elaboracion,$mes_anio_servicio,$numero_expediente,$total_inicio){
            $excel->sheet('Planilla ', function($sheet) use($archivo_plano,$empresa,$fecha_elaboracion,$mes_anio_servicio,$numero_expediente,$total_inicio){

                $sheet->mergeCells('B2:G2');
                $sheet->cell('B2', function($cell) use($empresa){
                    if(!is_null($empresa)){
                      $cell->setValue($empresa->razonsocial);
                    }
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                });

                $sheet->mergeCells('B3:G3');
                    $sheet->cell('B3', function($cell) {
                    
                    $cell->setValue('PLANILLA DE CARGOS CONSOLIDADO');
                    $cell->setAlignment('center');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B5:D5');
                $sheet->cell('B5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo Servicio:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', 'thin');
                });

                $sheet->cell('E5', function($cell){
                    // manipulate the cel
                    $tipo_servicio ='AMBULATORIO';
                    $cell->setValue($tipo_servicio);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', '', '', '');
                });

                $sheet->cell('F5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha de Elaboración:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('G5', function($cell) use($fecha_elaboracion) {
                    // manipulate the cel
                    $cell->setValue($fecha_elaboracion);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', '', '');
                });
                $sheet->cell('G5', function($cell) use($fecha_elaboracion) {
                    // manipulate the cel
                    $cell->setValue($fecha_elaboracion);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', '', '');
                });

                $sheet->mergeCells('B6:D6');
                $sheet->cell('B6', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Monto Solicitado:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                
                $sheet->cell('E6', function($cell) use($total_inicio){
                    // manipulate the cel
                    $valor_inicio = number_format($total_inicio, 2);
                    $cell->setValue($valor_inicio);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->cell('F6', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->cell('G6', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', '');
                });
                $sheet->mergeCells('B7:D7');
                $sheet->cell('B7', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes y Año de Presentacion:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                $sheet->cell('E7', function($cell) use($mes_anio_servicio){
                    // manipulate the cel
                    $cell->setValue($mes_anio_servicio);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->cell('F7', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->cell('G7', function($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', '');
                });
                $sheet->mergeCells('B8:D8');
                $sheet->cell('B8', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Nro. Expedientes:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', 'thin', 'thin');
                });
                
                /*$sheet->cell('E8', function($cell) use($numero_expediente){
                    // manipulate the cel
                    $cell->setValue($numero_expediente);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', 'thin', '');
                });*/
                $sheet->cell('F8', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('G8', function($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', '');
                });
                $sheet->cell('B10', function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('C10', function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.Caso');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('D10', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Código Validación');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('E10', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CC No.');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('F10', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('G10', function($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor Solicitad');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->setColumnFormat(array(
                   'G' => '0.00',
                ));

                $i=11;$j=1;$k=0;$l=0;$m=0;$n=0;$p=0;$q=0;$r=0;$s=0;$t=0;$u=0;

                $total=0;
                $id_temporal = '';
                $numero = 0;
                $numero_caso = 0;
                $codigo_validacion = 0;
                $cc_num = 0;
                $beneficiario = 0;
                $acum_paciente=0;

                foreach ($archivo_plano as $value){

                    if($value->derivacion_nc_msp != $id_temporal){

                        //dd($id_temporal,$value->derivacion_nc_msp);
                        
                        if($id_temporal != ''){
                            

                            $sheet->cell('B'.$i, function ($cell) use($numero){
                                // manipulate the cel
                                $cell->setValue($numero);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontSize('8');
                            });
                            $sheet->cell('C'.$i, function ($cell) use($numero_caso){
                                // manipulate the cel
                                $cell->setValue($numero_caso);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontSize('8');
                            });
                            $sheet->cell('D'.$i, function ($cell) use($codigo_validacion){
                                // manipulate the cel
                                $cell->setValue($codigo_validacion);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontSize('8');
                            });
                            $sheet->cell('E'.$i, function ($cell) use($cc_num){
                                // manipulate the cel
                                $cell->setValue($cc_num);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontSize('8');
                            });

                            $sheet->cell('F'.$i, function ($cell) use($beneficiario){
                                // manipulate the cel
                                $cell->setValue($beneficiario);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontSize('8');
                            });

                            $sheet->cell('G'.$i, function ($cell) use($acum_paciente){
                                // manipulate the cel
                                $cell->setValue(number_format($acum_paciente, 2));
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontSize('8');
                            
                            });

                            $j++;
                            $i++;
                            
                            $numero = 0;
                            $numero_caso = 0;
                            $codigo_validacion = 0;
                            $cc_num = 0;
                            $beneficiario = 0;
                            $acum_paciente=0;

                        }

                        $id_temporal = $value->derivacion_nc_msp;
                    
                        
                    
                    }

                    $datospaciente = $value->paciente;

                    /*$plano_det = DB::table('archivo_plano_detalle as apd')
                    ->where('apd.id_ap_cabecera', $value->id)
                    ->where('apd.estado','1')
                    ->groupBy('apd.id_ap_cabecera')
                    ->select(DB::raw("SUM(apd.total_solicitado_usd) as valor_solicitud"))
                    ->first();*/
                    $plano_det = $value->detalles->where('estado','1')->sum('total_solicitado_usd');
                    //$plano_det = $value->detalles->where('estado','1')->sum('total_solicitado_usd');
                    $acum_paciente+=$plano_det;

                    $numero = $j;
                    $numero_caso = $value->derivacion_nc_msp;
                    $codigo_validacion = $value->cod_deriva_msp;
                    $cc_num = $value->id_paciente;
                    $beneficiario = $datospaciente->apellido1.' '.$datospaciente->apellido2.' '.$datospaciente->nombre1.' '.$datospaciente->nombre2;

                    $total+=$plano_det;
                    /*if(!is_null($plano_det)){
                        $total_inicio+=$plano_det->valor_solicitud;
                    }*/
                    //dd($id_temporal);
                   

                    /*if(!is_null($plano_det)){
                        $total+=$plano_det->valor_solicitud;
                    }*/

                    //$j=$j+1;
                    //$i++;
                }

                $sheet->cell('B'.$i, function ($cell) use($numero){
                    // manipulate the cel
                    $cell->setValue($numero);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('C'.$i, function ($cell) use($numero_caso){
                    // manipulate the cel
                    $cell->setValue($numero_caso);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('D'.$i, function ($cell) use($codigo_validacion){
                    // manipulate the cel
                    $cell->setValue($codigo_validacion);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('E'.$i, function ($cell) use($cc_num){
                    // manipulate the cel
                    $cell->setValue($cc_num);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->cell('F'.$i, function ($cell) use($beneficiario){
                    // manipulate the cel
                    $cell->setValue($beneficiario);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->cell('G'.$i, function ($cell) use($acum_paciente){
                    // manipulate the cel
                    $cell->setValue(number_format($acum_paciente, 2));
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                
                });

                $j++;
                $i++;
                
                //$numero = 0;
                $numero_caso = 0;
                $codigo_validacion = 0;
                $cc_num = 0;
                $beneficiario = 0;
                $acum_paciente=0;

                //$i++;
                $sheet->mergeCells('B'.$i.':F'.$i);
                $sheet->cell('B'.$i, function ($cell){
                    // manipulate the cel
                    $cell->setValue('Total Valor Solicitado:');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->cell('G'.$i, function($cell) use($total){
                    // manipulate the cel
                    $valor_total = number_format($total, 2);
                    $cell->setValue($valor_total);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $k=$i+2;

                $sheet->mergeCells('B'.$k.':G'.$k);
                $sheet->cell('B'.$k, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $l=$k+1;

                $sheet->mergeCells('B'.$l.':G'.$l);
                $sheet->cell('B'.$l, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $m=$l+1;
                $sheet->mergeCells('B'.$m.':G'.$m);
                $sheet->cell('B'.$m, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $n=$m+1;
                $sheet->mergeCells('B'.$n.':G'.$n);
                $sheet->cell('B'.$n, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $p=$n+1;
                $sheet->mergeCells('B'.$p.':G'.$p);
                $sheet->cell('B'.$p, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $q=$p+1;
                $sheet->mergeCells('B'.$q.':G'.$q);
                $sheet->cell('B'.$q, function ($cell){
                    // manipulate the cel
                    $cell->setValue('Firma:');
                    $cell->setBorder('', 'thin', '', 'thin');
                    $cell->setFontSize('8');
                });
                $r=$q+1;
                $sheet->cell('B'.$r, function ($cell){
                    // manipulate the cel
                    $cell->setValue('Nombre:');
                    $cell->setBorder('', '', '', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->mergeCells('C'.$r.':E'.$r);
                $sheet->cell('C'.$r, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', 'hair', '');
                });
                $sheet->mergeCells('F'.$r.':G'.$r);
                $sheet->cell('F'.$r, function ($cell){
                    // manipulate the cel
                    $cell->setBorder('', 'thin', '', '');
                });

                $s=$r+1;
                $sheet->cell('B'.$s, function ($cell){
                    // manipulate the cel
                    $cell->setValue('N° CC:');
                    $cell->setBorder('', '', '', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->mergeCells('C'.$s.':E'.$s);
                $sheet->cell('C'.$s, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', 'hair', '');
                });
                $sheet->cell('F'.$s, function ($cell){
                    // manipulate the cel
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('G'.$s, function ($cell){
                    // manipulate the cel
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $t=$s+1;
                $sheet->cell('B'.$t, function ($cell){
                    // manipulate the cel
                    $cell->setValue('Cargo:');
                    $cell->setBorder('', '', '', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->mergeCells('C'.$t.':E'.$t);
                $sheet->cell('C'.$t, function ($cell){
                    // manipulate the cel
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->cell('F'.$t, function ($cell){
                    // manipulate the cel
                    $cell->setValue('Sello');
                    $cell->setAlignment('right');
                    $cell->setBorder('', '', '', '');
                    $cell->setFontSize('8');
                });

                $sheet->cell('G'.$t, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', '', '');
                });

                $u=$t+1;
                $sheet->mergeCells('B'.$u.':G'.$u);
                $sheet->cell('B'.$u, function ($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });

                //$numero = $numero-1;
                $sheet->cell('E8', function($cell) use($numero){
                    // manipulate the cel
                    $cell->setValue($numero);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', 'thin', '');
                });
            
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(2)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(13)->setAutosize(false);

        })->export('xlsx');
    
    }

    public function reporte_cuenta_iess(Request $request){
        
        $mes_plano =$request->mes_plano;
        //dd($mes_plano);
        $seg =$request->seguro;
        $tipo_seg =$request->id_tipo_seguro;       
        $empresa =$request->id_empresa;
        $arr_base_0=[];
        $arr_base_iva=[];
        $arr_v_iva=[];
        $arr_amd_10=[];
        //dd($empresa);
        $nombre_seguro = Seguro::find($seg)->nombre;

        /*$archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano',$mes_plano)
            ->where('archivo_plano_cabecera.id_seguro',$seg)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','archivo_plano_cabecera.id','apd.id_ap_cabecera')
            ->where('apd.estado','1')->get();
        dd($archivo_plano);*/

        $cant_pac = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano',$mes_plano)
            ->where('archivo_plano_cabecera.id_seguro',$seg)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->select('tseg.id','tseg.nombre','tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->groupBy('tseg.id','tseg.nombre','tseg.tipo')
            ->orderBy('tseg.id','tseg.nombre','tseg.tipo')->get();
        //dd($cant_pac->where('tseg.id', '1')->get(),$cant_pac->where('tseg.id', '2')->get());
        $base_0 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano',$mes_plano)
            ->where('archivo_plano_cabecera.id_seguro',$seg)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','archivo_plano_cabecera.id','apd.id_ap_cabecera')
            ->where('apd.porcentaje_iva','=','0')
            ->where('apd.estado','1')
            ->select('tseg.id','tseg.nombre','tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.subtotal) as valor')
            ->groupBy('tseg.id','tseg.nombre','tseg.tipo')
            ->orderBy('tseg.id','tseg.nombre','tseg.tipo')->get();
            //dd($base_0);

            foreach ($base_0 as $value) {
                $arr_base_0[$value->id] = $value->valor;
            }
            //dd($arr_base_0);

        $base_iva = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano',$mes_plano)
            ->where('archivo_plano_cabecera.id_seguro',$seg)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','archivo_plano_cabecera.id','apd.id_ap_cabecera')
            ->where('apd.porcentaje_iva','!=','0')
            ->where('apd.estado','1')
            ->select('tseg.id','tseg.nombre','tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.subtotal) as valor')
            ->groupBy('tseg.id','tseg.nombre','tseg.tipo')
            ->orderBy('tseg.id','tseg.nombre','tseg.tipo')->get();
            //dd($base_0);

            foreach ($base_iva as $value) {
                $arr_base_iva[$value->id] = $value->valor;
            }

        $v_iva = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano',$mes_plano)
            ->where('archivo_plano_cabecera.id_seguro',$seg)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','archivo_plano_cabecera.id','apd.id_ap_cabecera')
            ->where('apd.porcentaje_iva','!=','0')
            ->where('apd.estado','1')
            ->select('tseg.id','tseg.nombre','tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.iva) as valor')
            ->groupBy('tseg.id','tseg.nombre','tseg.tipo')
            ->orderBy('tseg.id','tseg.nombre','tseg.tipo')->get();
            //dd($base_0);

            foreach ($v_iva as $value) {
                $arr_v_iva[$value->id] = $value->valor;
            }

        $amd_10 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano',$mes_plano)
            ->where('archivo_plano_cabecera.id_seguro',$seg)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','archivo_plano_cabecera.id','apd.id_ap_cabecera')
            ->where('apd.estado','1')
            ->where('apd.porcent_10','!=','0')
            ->select('tseg.id','tseg.nombre','tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.porcentaje10) as valor')
            ->groupBy('tseg.id','tseg.nombre','tseg.tipo')
            ->orderBy('tseg.id','tseg.nombre','tseg.tipo')->get();
            //dd($base_0);

            foreach ($amd_10 as $value) {
                $arr_amd_10[$value->id] = $value->valor;
            }

        Excel::create('ReporteDetallado'.$nombre_seguro, function ($excel) use($cant_pac,$arr_base_0,$arr_base_iva,$arr_v_iva,$arr_amd_10,$nombre_seguro){
            $excel->sheet('ReporteDetallado'.$nombre_seguro, function ($sheet) use($cant_pac,$arr_base_0,$arr_base_iva,$arr_v_iva,$arr_amd_10,$nombre_seguro){

                $sheet->setColumnFormat(array(
                    'D' => '$ 0.00',
                    'E' => '$ 0.00',
                    'F' => '$ 0.00',
                    'G' => '$ 0.00',
                    'H' => '$ 0.00',
                ));
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPOSEG');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO_SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N_EXP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE_0');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE_IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('V_IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GAST_AMD_10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL_M_IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i=2;$total_0=0;$total_iva=0;$total_v_iva=0;$total_10=0;
                foreach ($cant_pac as $value) {
                    $sheet->cell('A'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->tipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    if (isset($arr_base_0[$value->id])) {
                        $sheet->cell('D'.$i, function ($cell) use($arr_base_0, $value){
                            // manipulate the cel
                            $cell->setValue(round($arr_base_0[$value->id],2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $total_0=$arr_base_0[$value->id];
                    }
                    else{
                        $sheet->cell('D'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("0");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    if (isset($arr_base_iva[$value->id])) {
                        $sheet->cell('E'.$i, function ($cell) use($arr_base_iva, $value){
                        // manipulate the cel
                        $cell->setValue(round($arr_base_iva[$value->id],2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $total_iva=$arr_base_iva[$value->id];
                    }
                    else{
                        $sheet->cell('E'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("0");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    if (isset($arr_v_iva[$value->id])) {
                        $sheet->cell('F'.$i, function ($cell) use($arr_v_iva, $value){
                        // manipulate the cel
                        $cell->setValue(round($arr_v_iva[$value->id],2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $total_v_iva=$arr_v_iva[$value->id];
                    }
                    else{
                        $sheet->cell('F'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("0");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    if (isset($arr_amd_10[$value->id])) {
                        $sheet->cell('G'.$i, function ($cell) use($arr_amd_10, $value){
                        // manipulate the cel
                        $cell->setValue(round($arr_amd_10[$value->id],2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $total_10=$arr_amd_10[$value->id];
                    }
                    else{
                        $sheet->cell('G'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("0");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $total_m_iva=$total_0+$total_iva+$total_v_iva+$total_10;

                        $sheet->cell('H'.$i, function ($cell) use($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                    $i++;
                }
            });

        })->export('xlsx');
        

    }



}
