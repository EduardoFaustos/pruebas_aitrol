<?php

namespace Sis_medico\Http\Controllers\msp;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_agenda;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\hc_receta;
use Sis_medico\Hc_Cie10;
use Sis_medico\Paciente;

class MspPdfController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pdf ($id_agenda){


       //aqui va la consulta
       $agenda = Agenda::find($id_agenda);
       $historia_cli = Historiaclinica::where('id_agenda', $agenda->id)->first();
       $receta = hc_receta::where('id_hc', $historia_cli->id_hc)->first();
       //$date = date('Y-m-d');
       // ruta del pdf
       $vistaurl = "msp.pdf_msp";
       $view     = \View::make($vistaurl, compact('agenda', 'historia_cli','receta'))->render();
       $pdf      = \App::make('dompdf.wrapper');
       $pdf->loadHTML($view);
       $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
       $pdf->setPaper('A4', 'portrait');

       return $pdf->stream('Pdf MSP' . '.pdf');
      
    }
}
