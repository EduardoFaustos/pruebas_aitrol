<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use DateTime;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Excel;
use PDF;
use Storage;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

class MuestraBiopiasController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 5, 20)) == false) {
            return true;
        }
    }

    public function index_muestras(Request $request){
        
         if ($this->rol()) {
            return response()->view('errors.404');
        }

        $biopsias    = $request->all();
        $fecha       = $request['fecha'];
        $fechafin    = $request['fechafin'];
        $examen_aqui = $request['examen_aqui'];
        $pacientes_omni = $request['pacientes_omni'];
        $tipo_seguro = $request['tipo_seguro'];

        
        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }

        $hc_biopsias = Hc4_Biopsias:: where('hc4_biopsias.estado', '1')->where('muestra_biopsia', $examen_aqui)->join('seguros as seg', 'seg.id' , 'hc4_biopsias.id_seguro')->where('seg.tipo', $tipo_seguro)->join('historiaclinica as hc', 'hc.hcid', 'hc4_biopsias.hcid')->join('agenda as ag', 'ag.id', 'hc.id_agenda')->where('ag.omni',$pacientes_omni)->whereBetween('hc4_biopsias.created_at', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])->groupBy('hc_id_procedimiento');

        if ($pacientes_omni == 0){
            if ($tipo_seguro == 0){
                if ($examen_aqui==0){
                    $cedula            = $request['cedula'];
                    $biopsias          =  $hc_biopsias->where('muestra_biopsia','0')->where('seg.tipo', '0')->where('ag.omni','=', 'null')->orwhere('ag.omni','=','NO');
                }
            
                if ($examen_aqui==1){ 
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '1')->where('seg.tipo', '0')->where('ag.omni','=', 'null')->orWhere('ag.omni','=','NO');
                }
            }

            if ($tipo_seguro == 1) {

                if ($examen_aqui==0){    
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '0')->where('seg.tipo', '>=', '1')->where('ag.omni','=', 'null')->orWhere('ag.omni','=','NO');
                }   
                if ($examen_aqui==1){ 
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '1')->where('seg.tipo', '>=', '1')->where('ag.omni','=', 'null')->orwhere('ag.omni','=','NO');
                }              
            }   

        }

        if ($pacientes_omni==1){

            if ($tipo_seguro == 0){
                if ($examen_aqui==0){
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '0')->where('seg.tipo', '0')->where('ag.omni', '=' , 'OM')->orwhere('ag.omni', '=' ,'SI');
                }
            
                if ($examen_aqui==1){ 
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '1')->where('seg.tipo', '0')->where('ag.omni', '=' , 'OM')->orwhere('ag.omni', '=' ,'SI');
                }   
            }

            if ($tipo_seguro == 1) {

                if ($examen_aqui==0){    
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '0')->where('seg.tipo', '>=', '1')->where('ag.omni', '=' , 'OM')->orwhere('ag.omni', '=' ,'SI');
                }

                if ($examen_aqui==1){ 
                    $cedula            = $request['cedula'];
                    $biopsias          = $hc_biopsias->where('muestra_biopsia', '1')->where('seg.tipo', '>=', '1')->where('ag.omni', '=' , 'OM')->orwhere('ag.omni', '=' ,'SI');
                }              
            }   
        }

        if (!is_null($cedula)) {
                $biopsias->where('hc4_biopsias.id_paciente', $cedula);
            }

              //dd($biopsias->get());
        $biopsias   = $biopsias->select('hc4_biopsias.*')->paginate(15);
           //dd($biopsias);

     return view ('hc_admision/biopsias/index', ['fecha' => $fecha , 'fechafin' => $fechafin ,'pacientes_omni' => $pacientes_omni,'examen_aqui' => $examen_aqui,'cedula' => $cedula , 'biopsias' => $biopsias, 'tipo_seguro' => $tipo_seguro]);
    }

    public function update(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());

        $id = $request->id;
        //dd($request->all());
        
        $biopsias = Hc4_Biopsias::find($id);
        $biopsias->muestra_biopsia = $request['estado'];
        $biopsias->id_usuariomod  = $idusuario;
        $biopsias->ip_modificacion = $ip_cliente;

        $biopsias->save();

        return  ["status"=>"success", 'msj'=> 'Datos correctamente actualizados'];
    }

    public function reporte_muestras_biopsias(Request $request){

        $fecha       = $request['fecha'];
        $fechafin    = $request['fechafin'];
        $cedula      = $request['cedula'];
        $examen_aqui = $request['examen_aqui'];

       // dd($request->all());

       if ($examen_aqui == 0) {
        $biopsias          = Hc4_Biopsias:: where('estado', '1')->where('muestra_biopsia','0')->groupBy('hc_id_procedimiento')->whereBetween('hc4_biopsias.created_at', [$fecha . ' 00:00:00', $fechafin . ' 23:59:59'])->get();

       }
       if ($examen_aqui == 1) {
        $biopsias          = Hc4_Biopsias:: where('estado', '1')->where('muestra_biopsia','1')->groupBy('hc_id_procedimiento')->whereBetween('hc4_biopsias.created_at', [$fecha . ' 00:00:00', $fechafin . ' 23:59:59'])->get();

       }

       $fecha_d = date('Y/m/d');

       Excel::create('Reporte_Muestras_Biopsias'. $fecha_d , function ($excel) use ($biopsias , $fecha){
         
         $excel->sheet('Reporte Muestras Biopsias', function($sheet) use($biopsias, $fecha){
         $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
       
         $fecha_d = date('Y/m/d');
          $sheet->mergeCells('A1:F2');
                    $sheet->cell('A1', function ($cell){
                        $cell->setValue('Reporte Muestras Biopsias');
                        $cell->setFontColor('#010101');
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setFontSize('20');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


         $i = 3;
         
         $arrTitulos= ["Nombre del Paciente", "Cedula", "Tipo Seguro", "Descripcion" ,"# de Frascos", "Se realizo examenes aqui"];

         $comienzo = 3;

         for ($i=0; $i < count($arrTitulos) ; $i++) {
            $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrTitulos, $i) {
                    // manipulate the cel
                    $cell->setValue($arrTitulos[$i]);
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
         }
         $comienzo++;
         //dd($biopsias->get());
         foreach($biopsias as $value){
            $arrValue = [];
            if ($value->muestra_biopsia ==0){
                $value->muestra_biopsia = "NO";
            } 
            else{
                $value->muestra_biopsia = "SI";
            }
            $arrValue = ["{$value->pacientes->nombre1} {$value->pacientes->nombre2} {$value->pacientes->apellido1} {$value->pacientes->apellido2}", $value->id_paciente, $value->seguros->nombre, $value->descripcion_frasco, $value->numero_frasco, $value->muestra_biopsia];

            for ($i=0; $i < count($arrValue) ; $i++) {
                $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i) {
                        // manipulate the cel
                        $cell->setValue($arrValue[$i]);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
             }
             $comienzo++;
         }
           
       });   
      

      })->export('xlsx');

    }

    public function pdf_muestras_biopsias(Request $request)
    {

        $view = \View::make('hc_admision/biopsias/pdf_muestras_biopsias')->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('muestra_biopsia.pdf');
    }
}