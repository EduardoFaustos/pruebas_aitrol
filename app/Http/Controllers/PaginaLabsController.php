<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Http\Controllers\ApiFacturacionController;

class PaginaLabsController extends Controller
{
    public function examenes(Request $request)
    {
        $token = $request['token'];
        if ($token != 'dd96a4a6c3dd2119f27e3414a06a8ad8') {
            return response()->json([
                'error'   => 'TOKEN INCORRECTO',
                'success' => 'NO',
            ]);
        }
        $examenes = Examen::join('examen_agrupador_sabana', 'examen_agrupador_sabana.id_examen', '=', 'examen.id')
            ->where('examen.estado', 1)->where('examen_agrupador_sabana.estado', '1')->orderBy('nombre')->get();

        return response()->json($examenes);
    }

    public function comprobante_externo($comprobante)
    {
        $comprobante         = base64_decode(base64_decode($comprobante));
        $data['empresa']     = '0993075000001';
        $data['comprobante'] = $comprobante;
        $data['tipo']        = 'pdf';
        return ApiFacturacionController::estado_comprobante_externo($data);
    }

    public function cotizacion_externo($id)
    {
        $cotizador = base64_decode(base64_decode($id));
        $id        = $cotizador;

        $orden      = Examen_Orden::find($id);
        $detalles   = DB::table('examen_detalle as ed')->where('ed.id_examen_orden', $orden->id)->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')->select('ed.*', 'e.descripcion', 'e.nombre')->join('examen as e', 'e.id', 'ed.id_examen')->orderBy('es.id_examen_agrupador_labs')->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        //dd($detalles);
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;

        $view = \View::make('laboratorio.orden.cotizacion_pdf', compact('orden', 'detalles', 'age', 'forma_pago'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('cotizador-' . $id . '.pdf');

    }

    public function resultados_externos($id)
    {
        $orden    = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        //$detalle = $orden->detalles;
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        //dd($detalle);
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();

        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
                //$cant_par++;
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3');

                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);

                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }

        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;

            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }
        //dd($pct);
        //dd($detalle);
        // Fin recalcula Porcentaje

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();

        } else {
            //$agrupador = Examen_Agrupador_labs::all();
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();

        }

        $ucreador = $orden->crea;
        $age      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        //dd($age,$orden->paciente->fecha_nacimiento);

        $vistaurl = "laboratorio.orden.resultados_pdf";
        $view     = \View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador', 'user'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        //return $view;
        return $pdf->stream('resultado-' . $id . '.pdf', array("Attachment" => false));
    }

    public function facturacion_externo(Request $request){
        if($request->header('appid') !== null){
            if($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA='){
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $data = json_decode($elemento, true);
                $data['externo'] = 'recibido';
                //dd($request);
                //print_r($data);exit();
                $envio = ApiFacturacionController::envio_factura_externo($data);
                //dd($envio);
                return $envio;
            }else{
                return "CREDENCIALES INVALIDAS";
            }
        }else{
            return "CREDENCIALES INVALIDAS";
        }
    }

    public function ride_externo(Request $request, $recurso){
        if($request->header('appid') !== null){
            if($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA='){
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $data = json_decode($elemento, true);
                $data['externo'] = 'recibido';
                //dd($request);
                //print_r($data);exit();
                $porciones = explode("_", $recurso);
                $data['empresa']     = $porciones[1];
                $data['comprobante'] = $porciones[2];
                $data['tipo']        = 'pdf';
                $data['externo']     = '1';
                //dd($envio);
                return ApiFacturacionController::estado_comprobante_externo($data);
            }else{
                return "CREDENCIALES INVALIDAS";
            }
        }else{
            return "CREDENCIALES INVALIDAS";
        }
    }
    

}
