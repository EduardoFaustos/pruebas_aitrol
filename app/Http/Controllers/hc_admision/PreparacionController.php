<?php

namespace Sis_medico\Http\Controllers\hc_admision;

 
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Log_usuario;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Indicacion;
use Excel;


use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;






use Response;



class PreparacionController extends Controller
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
        if(in_array($rolUsuario, array(1, 3, 6,11,7)) == false){
          return true;
        }
    }

    private function rol_admi(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4, 5)) == false){
          return true;
        }
    }

    public function mostrar($id,$url_doctor){
    
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol_admi()){
            return response()->view('errors.404');
        }    

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('historiaclinica as h','h.id_agenda','agenda.id')
            ->join('seguros as s', 'h.id_seguro', '=', 's.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'paciente.sexo', 'paciente.fecha_nacimiento','h.hcid','s.nombre as hsnombre','paciente.gruposanguineo','h.presion','h.pulso','h.temperatura','h.altura','h.peso')
            ->where('agenda.id', '=', $id)
            ->first();

                   

        return view('hc_admision/preparacion/preparacion', ['agenda' => $agenda, 'url_doctor' => $url_doctor]);  
    }

    public function pdfPreparaciones(){
        // $miNombre = "Adriana";
        // $usuario = Auth::user();
        $vistaurl = "preparaciones.pdfPrepariones";
        $procedimiento = "Endoscopia";
        $nombre_secundario = "(EDA)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
        //pdfPrepariones
    }

    public function pdfPreparacionesColonoscopiaTO(){

        $vistaurl = "preparaciones.pdfPreparacionesColonoscopia";
        $procedimiento = "Colonoscopía";
        $nombre_secundario = "(Travadpik Oral)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesColonoscopiaTO12(){

        $vistaurl = "preparaciones.pdfPreparacionesColonoscopiaTO12";
        $procedimiento = "Colonoscopía";
        $nombre_secundario = "<br>(Travadpik Oral - hasta las 12:00)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }
    
    public function pdfPreparacionesColonoscopiaN(){

        $vistaurl = "preparaciones.pdfPreparacionesColonoscopiaN";
        $procedimiento = "Colonoscopía";
        $nombre_secundario = "(Nulytely)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesColonoscopiaI(){

        $vistaurl = "preparaciones.pdfPreparacionesColonoscopiaI";
        $procedimiento = "Colonoscopía";
        $nombre_secundario = "(Izinova)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesColonoscopiaI12(){

        $vistaurl = "preparaciones.pdfPreparacionesColonoscopiaI12";
        $procedimiento = "Colonoscopía";
        $nombre_secundario = "(Izinova - hasta 12h00)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesCapsulaE(){

        $vistaurl = "preparaciones.pdfPreparacionesCapsulaE";
        $procedimiento = "Cápsula Endoscópica";
        $nombre_secundario = "<br>(Se va a casa y regresa al día siguiente)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesBroncoscopia(){

        $vistaurl = "preparaciones.pdfPreparacionesBroncoscopia";
        $procedimiento = "Broncoscopía";
        $nombre_secundario = "<br>(Ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesColonoscopia_NO(){

        $vistaurl = "preparaciones.pdfPreparacionesColonoscopia_NO";
        $procedimiento = "Colonoscopía";
        $nombre_secundario = "(Neolax Oral)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }
    
    public function pdfPreparacionesEndoscopia(){

        $vistaurl = "preparaciones.pdfPreparacionesEndoscopia";
        $procedimiento = "Endoscopía";
        $nombre_secundario = "<br>(Procedimiento ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesEcoendoscopiaDCP(){

        $vistaurl = "preparaciones.pdfPreparacionesEcoendoscopiaDCP";
        $procedimiento = "Ecoendoscopia Diagnóstica y/o Con Punción";
        $nombre_secundario = "<br>(Procedimiento ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesRetiroBI(){

        $vistaurl = "preparaciones.pdfPreparacionesRetiroBI";
        $procedimiento = "Retiro de balón intragástrico";
        $nombre_secundario = "<br>(Procedimiento ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesPOEM(){

        $vistaurl = "preparaciones.pdfPreparacionesPOEM";
        $procedimiento = "POEM";
        $nombre_secundario = "<br>(Procedimiento ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }
    
    public function pdfPreparacionesManometriaAR(){

        $vistaurl = "preparaciones.pdfPreparacionesManometriaAR";
        $procedimiento = "Manometría Ano Rectal";
        $nombre_secundario = "<br>(Ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

    public function pdfPreparacionesManometriaE(){

        $vistaurl = "preparaciones.pdfPreparacionesManometriaE";
        $procedimiento = "Manometría Esofágica";
        $nombre_secundario = "<br>(Ambulatorio - se va a casa el mismo día)";
        $view     = \View::make($vistaurl, compact('procedimiento', "nombre_secundario"))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('preparaciones.pdf');
    }

}
