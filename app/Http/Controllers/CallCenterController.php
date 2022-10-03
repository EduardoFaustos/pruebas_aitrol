<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\Agenda;
use Sis_medico\Log_Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Historiaclinica;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Pentax;
use Sis_medico\Pentax_log;
use Sis_medico\PentaxProc;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\hc_child_pugh;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_cpre_eco;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Hc_protocolo_training;
use Response;
use Sis_medico\Examen_Orden;
use Sis_medico\Log_usuario;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Horario_Doctor;
use Sis_medico\Excepcion_Horario;
use Excel;

class CallCenterController extends Controller
{
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

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
   public function index($fecha, $fechafin){
        $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        

        if($fecha == 0){
            $fecha_2 = date('Y-m-d');   
        }else{
            $fecha_2 = date('Y-m-d', $fecha);
        }
        if($fechafin ==0){
            $fechafin1= date('Y-m-d');
        }else{
            $fechafin1= date('Y-m-d', $fechafin);
        }

        
        $variable1 = Agenda::where("estado_cita","0")->where('proc_consul', '<', 2)->whereBetween('fechaini', [$fecha_2.' 00:00:00', $fechafin1.' 23:59:59'])->get();
        

            
         
        return view('callcenter/index', ['variable1' => $variable1,'fecha'=>$fecha,'fechafin'=>$fechafin]);
   }
   public function actualizarpaciente(Request $request){
    $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        } 

        $input = [
            'telefono_llamar' => $request['telefono_call'],            

        ];

        paciente::find($request['id_paciente'])->update($input);
        
   }
   public function descargar_reporte(Request $request){
        //dd($request);
        $fecha = $request['fecha'];
        $fechafin= $request['fechafin'];
        $variable1 = Agenda::where("estado_cita","0")->where('proc_consul', '<', 2)->whereBetween('fechaini', [$fecha.' 00:00:00', $fechafin.' 23:59:59'])->get();
            
        Excel::create('Archivo CSV-'.$fecha, function($excel) use($variable1) {
            $excel->sheet('Llamadas por confirmar', function($sheet) use($variable1) {

                $sheet->cell('A1', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO');
                });
                $sheet->cell('B1', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                });
                $sheet->cell('C1', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                });
                $sheet->cell('D1', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                });
                $contador=2;
                foreach ($variable1 as $value) {
                    if(!is_null($value->paciente->telefono_llamar) && (strlen($value->paciente->telefono_llamar)>=9)){

                       $sheet->cell('A'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->paciente->telefono_llamar);
                        });
                        $sheet->cell('B'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 0,10));
                        });
                        $sheet->cell('C'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 11,17));
                        });
                        $sheet->cell('D'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->id);
                        });
                        $contador++;
                    }elseif(is_numeric($value->paciente->telefono1)&& (strlen($value->paciente->telefono1)>=9)){
                        $sheet->cell('A'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->paciente->telefono1);
                        });
                        $sheet->cell('B'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 0,10));
                        });
                        $sheet->cell('C'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 11,17));
                        });
                        $sheet->cell('D'.$contador, function($cell) use($value){
                            // manipulate the cel
                            $cell->setValue($value->id);
                        });
                        $contador++;
                    }
                    
                }

            });
        })->export('csv');
    }

}