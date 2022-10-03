<?php

namespace Sis_medico\Http\Controllers\archivo_plano;


use Sis_medico\Http\Controllers\Controller;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Convenio;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_ventas;
use Sis_medico\Examen;
use Sis_medico\Examenes;
use Sis_medico\Examen_Agrupador_Sabana;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Resultado;
use Sis_medico\Log_usuario;
use Sis_medico\Medicina;
use Sis_medico\Medicina_Principio;
use Sis_medico\Paciente;
use Sis_medico\PrecioProducto;
use Sis_medico\Principio_Activo;
use Sis_medico\SCI_Pacientes;
use Sis_medico\User;
use Sis_medico\ApProcedimiento;
use Sis_medico\Seguro;
use Sis_medico\Nivel;
use Sis_medico\Inv_Carga_Inventario;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Insumo_Plantilla_Item_Control;
use Sis_medico\Planilla_Procedimiento;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvInventario;
use Sis_medico\InvKardex;
use Sis_medico\InvCosto;
use Sis_medico\Producto_Respaldo;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Ct_compras;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\ApPlantillaItem;
use Sis_medico\hc_procedimientos;
use Sis_medico\Planilla;

class Ap_PlanillaDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }

    public function planilla_detalle_pdf($id_procedimiento)
    {

        $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
        
        if(!is_null($hc_procedimiento)){
            $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos',$hc_procedimiento->id)->where('estado','1')->first();
            
            if(!is_null($archivo_plano)){

                $txt_cie10 = null;

                $cie10 = Cie_10_3::find($archivo_plano->cie10);
                if (is_null($cie10)) {
                    $cie10 = Cie_10_4::find($archivo_plano->cie10);
                    if (!is_null($cie10)) {
                        $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                    }
                } else {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $archivo_plano->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    //->orWhere('apt.tipo_ex','P')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                    ->get();


                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();

                
                $view = \View::make('archivo_plano.planilla.planilla_pdf', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos'))->render();

                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                
                return $pdf->stream('planilla_pdf_'.$hc_procedimiento->historia->paciente->apellido1.'_'.$hc_procedimiento->historia->paciente->nombre1.'_.pdf');
            
            }

            return "no existe";
        }

        return "no existe";
        
        /*$pdf->setOptions(['dpi' => 96]);
        $paper_size = array(0, 0, 1100, 1650);
        $pdf->setpaper($paper_size);
        $pdf->loadHTML($view);*/
        /*$age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;*/
        /*->setPaper($paper_size, 'portrait')*/;
        
    }

    public function planilla_detalle_contab_pdf($id_procedimiento){

        $hc_procedimiento = hc_procedimientos::find($id_procedimiento);

        $medicinas = array();       $insumos = array();     $laboratorio = array();
        $servicios_ins = array();   $imagen = array();      $equipos = array();
        $archivo_plano = array();
        $mensaje = "";
        
        if(!is_null($hc_procedimiento)){
            $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos',$hc_procedimiento->id)->where('estado','1')->first();
            
            if(!is_null($archivo_plano)){

                $txt_cie10 = null;

                $cie10 = Cie_10_3::find($archivo_plano->cie10);
                if (is_null($cie10)) {
                    $cie10 = Cie_10_4::find($archivo_plano->cie10);
                    if (!is_null($cie10)) {
                        $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                    }
                } else {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $archivo_plano->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    //->orWhere('apt.tipo_ex','P')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                    ->get();


                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();

                
                $mensaje = "";
                $view = \View::make('archivo_plano.planilla.planilla_pdf_contab', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos','mensaje'))->render();
                $nfile = 'planilla_pdf_'.$hc_procedimiento->historia->paciente->apellido1.'_'.$hc_procedimiento->historia->paciente->nombre1.'_.pdf';
            
            } else {
                $mensaje = "no encontro la planilla";
                $view = \View::make('archivo_plano.planilla.planilla_pdf_contab', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos','mensaje'))->render();
                $nfile = 'planilla_pdf_NO_NAME_.pdf';
            }

        } else {       
            $mensaje = "no se encontro el procedimiento";
            $view = \View::make('archivo_plano.planilla.planilla_pdf_contab', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos','mensaje'))->render();
            $nfile = 'planilla_pdf_NO_NAME_.pdf';
        }



        
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        
        return $pdf->stream($nfile);
        
        /*$pdf->setOptions(['dpi' => 96]);
        $paper_size = array(0, 0, 1100, 1650);
        $pdf->setpaper($paper_size);
        $pdf->loadHTML($view);*/
        /*$age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;*/
        /*->setPaper($paper_size, 'portrait')*/;
        
    }

    public function planilla_detalle_contab_pdf_vs($id,$id_procedimiento){

        $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
        
        if(!is_null($hc_procedimiento)){
            $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos',$hc_procedimiento->id)->where('estado','1')->first();
            
            if(!is_null($archivo_plano)){
                /////////////////////////INICIO DE VENTAS //////////////////////////////////////
                $txt_cie10 = null;

                $cie10 = Cie_10_3::find($archivo_plano->cie10);
                if (is_null($cie10)) {
                    $cie10 = Cie_10_4::find($archivo_plano->cie10);
                    if (!is_null($cie10)) {
                        $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                    }
                } else {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $archivo_plano->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    //->orWhere('apt.tipo_ex','P')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                    ->get();


                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
                /******************************************FIN DE VENTAS *********************************** */



                /***********************************COSTOS************************************* */ 
                $planillac = array();    $fact_ventac = array();      $ordenc = array();   $detallesc = array(); 
                $hcpc = hc_procedimientos::find($id);
                $hcc = $hcpc->historia;
                $agenda = $hcc->agenda;
                $paciente = $agenda->paciente; 
                //$empresa = Empresa::find($agenda->id_empresa);
                $planillac = $this->planillaProcedimiento($id_procedimiento);
                if (!isset($planillac->id)) {
                    $detallesc = '[]';
                } else {
                    $detallesc = $planillac->detalles_validos;
                }

                //compact('fact_ventac', 'hcc', 'hcpc', 'detallesc', 'ordenc',  'pacientec'))->render();
                /*************************************FIN DE COSTO********************************************/
                
                $view = \View::make('archivo_plano.planilla.planilla_pdf_contab_vs', compact('fact_ventac', 'hcc', 'hcpc', 'detallesc', 'ordenc',  'pacientec','archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos'))->render();

                $pdf  = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                
                return $pdf->stream('planilla_pdf_'.$hc_procedimiento->historia->paciente->apellido1.'_'.$hc_procedimiento->historia->paciente->nombre1.'_.pdf');
            
            }

            return "no existe";
        }

        return "no existe";
        
        /*$pdf->setOptions(['dpi' => 96]);
        $paper_size = array(0, 0, 1100, 1650);
        $pdf->setpaper($paper_size);
        $pdf->loadHTML($view);*/
        /*$age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;*/
        /*->setPaper($paper_size, 'portrait')*/;
        
    }

    public function planillaProcedimiento($id_hc_procedimiento) //id_procedimiento//
    {
        $planilla = Planilla::where('id_hc_procedimiento',$id_hc_procedimiento)
                                ->where('estado','!=', 0)                    
                                ->where('aprobado','!=', 0)    
                                ->orderBy('id','desc')             
                                ->first(); 
        if (!isset($planilla->id)) {
            $planilla = '[]';
        } 
        return $planilla;
    }

}
