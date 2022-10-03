<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Bodega;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Marca;
use Sis_medico\Movimiento;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Tipo;
use Sis_medico\User;
use PHPExcel_Style_NumberFormat;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvKardex;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\Pedido;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Empresa;
use Sis_medico\Session;


class ReportesController extends Controller
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
        if (in_array($rolUsuario, array(1, 7, 20)) == false) {
            return true;
        }
    }

    public function reporte_bodega(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $nombres = $request['nombres'];
        $bodega= $request['bodega'];
        $productos = DB::table('inv_det_movimientos as m')
            ->leftjoin('producto as p1', 'p1.id', 'm.id_producto')
            ->leftjoin('inv_inventario as inv', 'inv.id', 'm.id_inv_inventario')
            ->leftjoin('bodega as b', 'b.id', 'inv.id_bodega')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->join('marca as marc', 'marc.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->whereNull('p.deleted_at')
            ->where('m.estado', '1')
            //->where('m.tipo', '1')
            //->whereNull('ped.consecion')
            //->where('ped.tipo','<>','3')
            //->groupBy('m.serie')
            ->select( 'p1.nombre as nombre_producto', 'p1.codigo', 'p1.descripcion', 'b.nombre as nombre_bodega', 'marc.nombre as nombre_marca', 'm.cantidad' ,'m.updated_at as fecha', 'm.fecha_vence', 'm.serie', 'p.pedido', 'prov.nombrecomercial', 'tip.nombre as nombre_tipo','m.lote');

        if ($nombres != null) {
            $productos = $productos->where('p1.nombre', 'like', '%' . $nombres . '%');
        }
        if($request['codigo']!=null){
            $productos = $productos->where('p1.codigo', 'like', '%' . $request['codigo'] . '%');
        }
        if($bodega!=null){
            $productos= $productos->where('inv.id_bodega',$bodega);
        }
        $bodegas= Bodega::where('estado','1')->get();
        $productos = $productos->paginate(15);

        return view('insumos/reporte/bodega/index', ['productos' => $productos,'bodegas'=>$bodegas,'codigo'=>$request['codigo'],'bodega'=>$bodega, 'nombres' => $nombres]);
    }

    public function reporte_producto_bodega(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        
        $id_empresa  = $request->session()->get('id_empresa');
        $nombres = $request['nombres'];
        $bodega= $request['bodega'];
        $productos = DB::table('inv_det_movimientos as m')
            ->leftjoin('producto as p1', 'p1.id', 'm.id_producto')
            ->leftjoin('inv_inventario as inv', 'inv.id', 'm.id_inv_inventario')
            ->leftjoin('bodega as b', 'b.id', 'inv.id_bodega')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->join('marca as marc', 'marc.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->where('m.estado', '1')
            ->whereNull('p.deleted_at')
            //->where('m.tipo', '1')
            //->whereNull('ped.consecion')
            //->where('ped.tipo','<>','3')
            //->groupBy('m.serie')
            ->select( 'p1.nombre as nombre_producto', 'p1.codigo', 'p1.descripcion', 'b.nombre as nombre_bodega', 'marc.nombre as nombre_marca', 'm.cantidad' ,'m.updated_at as fecha', 'm.fecha_vence', 'm.serie', 'p.pedido', 'prov.nombrecomercial', 'tip.nombre as nombre_tipo','m.lote');

        if ($nombres != null) {
            $productos = $productos->where('p1.nombre', 'like', '%' . $nombres . '%');
        }
        if($request['codigo']!=null){
            $productos = $productos->where('p1.codigo', 'like', '%' . $request['codigo'] . '%');
        }
        if($bodega!=null){
            $productos= $productos->where('inv.id_bodega','like', '%' . $bodega . '%');
        }
        $productos = $productos->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Existentes_Bodega'. $fecha_d , function ($excel) use ($productos){

          $excel->sheet('Reporte Existentes en Bodega', function($sheet) use($productos){
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

          $fecha_d = date('Y/m/d');
           $sheet->mergeCells('A1:M2');
                     $sheet->cell('A1', function ($cell){
                         $cell->setValue('Reporte de Productos Existentes en Bodega');
                         $cell->setFontColor('#010101');
                         $cell->setBackground('#D1E9F2');
                         $cell->setFontWeight('bold');
                         $cell->setFontSize('20');
                         $cell->setAlignment('center');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                     });
           $i = 3;

          $arrTitulos= ["Codigo", "Nombre", "Descripción", "Serie", "Pedidos", "Bodega" ,"Proveedores", "Marca", "Tipo de Producto", "Cantidad en Bodega", "Lote", "Fecha", "Fecha de Vencimiento"];

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
          $fecha_hoy = date('Y/m/d');
          foreach($productos as $value){
             $arrValue = [];

             $arrValue = [$value->codigo, $value->nombre_producto, $value->descripcion,  $value->serie."-",  $value->pedido ,$value->nombre_bodega, $value->nombrecomercial, $value->nombre_marca, $value->nombre_tipo, $value->cantidad, $value->lote, $value->fecha, $value->fecha_vence];

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

    public function buscador_master(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = [];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $codigo      = $request['codigo'];
        $nombre      = $request['nombres'];
        $bodega      = $request['bodega'];
        $tipo        = $request['tipo'];
        $marca       = $request['marca'];
        $tipo_prod   = $request['tipo_producto'];
        $proveedores = $request['proveedor'];
        $caducados =  $request['caducados'];
        $fecha_hoy     = date('Y-m-d');


        $productos = ReportesController::busqueda_master($request)->paginate(20);
        //dd($productos);

        $bodega_nombre = bodega::where('estado', '1')->get();
        $marca_nombre  = marca::where('estado', '1')->get();
        $tipo_producto = tipo::where('estado', '1')->get();
        $proveedor     = proveedor::where('estado', '1')->get();
       // dd($productos);
        return view('insumos/reporte/buscador_master/index', ['productos' => $productos,'bodega'=>$bodega,'tipo'=>$tipo, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'bodega_nombre' => $bodega_nombre, 'marca_nombre' => $marca_nombre, 'nombre' => $nombre, 'codigo' => $codigo, 'marca' => $marca, 'bodega' => $bodega, 'fecha_hoy' => $fecha_hoy, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'tipo_producto' => $tipo_producto, 'tipo_prod' => $tipo_prod, 'proveedor' => $proveedor, 'proveedores' => $proveedores]);

    }

    public static function busqueda_master($request){
        $id_empresa  = $request->session()->get('id_empresa');
        $productos = DB::table('inv_kardex as ik')
            ->leftjoin('inv_det_movimientos as idm', 'idm.id','ik.id_inv_det_movimientos')
            ->leftjoin('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('producto as p1', 'p1.id', 'ik.id_producto')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->leftjoin('marca as m', 'm.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->where('idm.cantidad', '>=', '1')
            ->whereNull('p.deleted_at')
            ->select('p1.codigo', 'p1.nombre as nombre_producto','p1.descripcion', 'b.id','b.nombre as nombre_bodega', 'ik.cantidad', 'idm.serie', 'p.pedido', 'prov.nombrecomercial', 'm.nombre as nombre_marca', 'tip.nombre as nombre_tipop', 'p.vencimiento', 'ik.tipo');
  
        if (count($request->all()) > 0){
            if(!is_null($request->codigo)){
                $productos = $productos->where('p1.codigo', 'like', '%' . $request->codigo . '%');
            }

            if(!is_null($request->nombres)){
                $productos = $productos->where('p1.nombre', 'like', '%' . $request->nombres . '%');
            }
            if (!is_null($request->bodega)){
                $productos = $productos->where('b.id', $request->bodega);
            }
            if(!is_null($request->tipo_producto)){
                $productos = $productos->where('tip.id', $request->tipo_producto);
            }
            if(!is_null($request->proveedor)){
                $productos = $productos->where('prov.id', $request->proveedor);
            }
            if(!is_null($request->marca)){
                $productos = $productos->where('m.id', $request->marca);
            }
            if(!is_null($request->caducados)){
                if($request->caducados == 1){
                    $productos = $productos->whereDate('p.vencimiento','<=',date('Y-m-d'));
                }elseif($request->caducados == 2){
                    $productos = $productos->whereDate('p.vencimiento','>=',date('Y-m-d'));
                }elseif($request->caducados == null){
                    $productos = $productos->whereDate('p.vencimiento','>=',date('Y-m-d'));
                }
            }
            if(!is_null($request->tipo)){
                $productos = $productos->where("p.tipo", $request->tipo);
            }
            if(!is_null($request->fecha)){
                $productos = $productos = $productos->where('idm.fecha_vence', ">=" ,"{$request->fecha} 00:00:00");
            }

            if(!is_null($request->fecha_hasta)){
                $productos = $productos = $productos->where('idm.fecha_vence', "<=" ,"{$request->fecha_hasta} 23:59:59");
            }
        }
        return $productos;
        //dd($productos);
    }

    public function reporte_master(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $productos = ReportesController::busqueda_master($request)->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Master_Insumos'. $fecha_d , function ($excel) use ($productos){

          $excel->sheet('Reporte Master de Insumos', function($sheet) use($productos){
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

          $fecha_d = date('Y/m/d');
           $sheet->mergeCells('A1:L2');
                     $sheet->cell('A1', function ($cell){
                         $cell->setValue('Reporte Master de Insumos Médicos');
                         $cell->setFontColor('#010101');
                         $cell->setBackground('#D1E9F2');
                         $cell->setFontWeight('bold');
                         $cell->setFontSize('20');
                         $cell->setAlignment('center');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                     });


          $i = 3;

          $arrTitulos= ["Codigo", "Nombre", "Descripción", "Bodega" ,"Cantidad","Tipo","Serie", "Pedidos" , "Proveedores","Marca", "Tipo de Producto","Fecha de Vencimiento", "Estado"];

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
          $fecha_hoy = date('Y/m/d');
          foreach($productos as $value){
             $arrValue = [];

             $estado = [];
                if ($value->vencimiento < date('Y-m-d')) {
                    $estado ='Producto caducado';
                } else {
                    $estado ='Producto Proximo a Caducado';
                }
            $tipo=[];
             if($value->tipo == 'I'){
                $tipo = "Ingreso";
             } else if($value->tipo == 'E'){
                $tipo = "Egreso";
             }elseif($value->tipo == 'T'){
                $tipo = "Traslado";
             }

             $arrValue = [$value->codigo, $value->nombre_producto, $value->descripcion, $value->nombre_bodega,$value->cantidad, $tipo ,$value->serie."-", $value->pedido, $value->nombrecomercial , $value->nombre_marca, $value->nombre_tipop, $value->vencimiento, $estado];

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

    public function reporte_caducado(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $nombres_prod = $request['nombres'];
        $fecha        = $request['fecha'];
        $fecha_hasta  = $request['fecha_hasta'];
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_hoy = date('Y-m-d');
        //BUSCA POR NOMBRE

        $fecha_cadu = DB::table('inv_det_movimientos as m')
            ->leftjoin('producto as p1', 'm.id_producto', 'p1.id')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('bodega as b', 'b.id', 'p.id_bodega')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('marca as mar', 'mar.id', 'p1.id_marca')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->where('p.id_empresa', $id_empresa)
            ->where('m.cantidad', '>=', '1')
            //->whereNull('ped.consecion')
           /* ->where('p.tipo','<>','3')
            ->where('m.estado', '1')
            ->where(function ($query) {
                $query->where('p1.tipo', '2')
                    ->orWhere('p1.tipo', '1');})
            ->where(function ($query) {
                $query->where('m.fecha_vence', '<', 'now()')
            ->orWhereRaw('m.fecha_vence < DATE_ADD(NOW(), INTERVAL 3 MONTH)');})*/

            ->select('p1.codigo', 'p1.nombre', 'p1.descripcion', 'p.pedido', 'm.serie', 'm.fecha_vence', 'b.nombre as nombre_bodega', 'prov.nombrecomercial', 'mar.nombre as nombre_marca', 'm.cantidad' , 'tip.nombre as nombre_tipoproducto');

        if ($nombres_prod != null) {
            //busca si el nombre del producto esta lleno
            $fecha_cadu = $fecha_cadu->where('p1.nombre', 'like', '%' . $nombres_prod . '%');

        } elseif ($fecha != null && $fecha_hasta != null) {

            $fecha_cadu = $fecha_cadu->whereBetween('m.fecha_vence', [$fecha, $fecha_hasta]);
        }

        $fecha_cadu = $fecha_cadu->paginate(15);

        return view('insumos/reporte/caducado/index', ['fecha_cadu' => $fecha_cadu, 'fecha_hoy' => $fecha_hoy, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres_prod' => $nombres_prod]);
    }

    public function reporte_producto_caducado(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
         
        $id_empresa  = $request->session()->get('id_empresa');
        $nombres_prod = $request['nombres'];
        $fecha        = $request['fecha'];
        $fecha_hasta  = $request['fecha_hasta'];

        $fecha_hoy = date('Y-m-d');

        $fecha_cadu = DB::table('inv_det_movimientos as m')
            ->leftjoin('producto as p1', 'm.id_producto', 'p1.id')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('bodega as b', 'b.id', 'p.id_bodega')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('marca as mar', 'mar.id', 'p1.id_marca')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->where('m.cantidad', '>=', '1')
            ->where('p.id_empresa', $id_empresa)
            //->whereNull('ped.consecion')
           /* ->where('p.tipo','<>','3')
            ->where('m.estado', '1')
            ->where(function ($query) {
                $query->where('p1.tipo', '2')
                    ->orWhere('p1.tipo', '1');})
            ->where(function ($query) {
                $query->where('m.fecha_vence', '<', 'now()')
            ->orWhereRaw('m.fecha_vence < DATE_ADD(NOW(), INTERVAL 3 MONTH)');})*/

            ->select('p1.codigo', 'p1.nombre', 'p1.descripcion', 'p.pedido', 'm.serie', 'm.fecha_vence', 'b.nombre as nombre_bodega', 'prov.nombrecomercial', 'mar.nombre as nombre_marca', 'm.cantidad' , 'tip.nombre as nombre_tipoproducto');

        if ($nombres_prod != null) {
            //busca si el nombre del producto esta lleno
            $fecha_cadu = $fecha_cadu->where('p1.nombre', 'like', '%' . $nombres_prod . '%');

        } elseif ($fecha != null && $fecha_hasta != null) {

            $fecha_cadu = $fecha_cadu->whereBetween('m.fecha_vence', [$fecha, $fecha_hasta]);
        }

        $fecha_cadu = $fecha_cadu->get();

        Excel::create('Reporte_de_Insumos_caducados'. $fecha_hoy , function ($excel) use ($fecha_cadu){

            $excel->sheet('Reporte de Insumos caducados', function($sheet) use($fecha_cadu){
            $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

            $fecha_hoy = date('Y/m/d');
             $sheet->mergeCells('A1:L2');
                       $sheet->cell('A1', function ($cell){
                           $cell->setValue('REPORTE DE INSUMOS CADUCADOS / CADUCAR');
                           $cell->setFontColor('#010101');
                           $cell->setBackground('#D1E9F2');
                           $cell->setFontWeight('bold');
                           $cell->setFontSize('20');
                           $cell->setAlignment('center');
                           $cell->setBorder('thin', 'thin', 'thin', 'thin');
                       });


            $i = 3;

            $arrTitulos= ["Codigo", "Nombre", "Descripción", "Serie", "Pedido", "Bodega", "Proveedores", "Marca", "Tipo de Producto", "Cantidad",  "Fecha de Vencimiento", "Estado"];

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
            $fecha_hoy = date('Y/m/d');
            foreach($fecha_cadu as $value){
               $arrValue = [];
               $estado = [];
                if ($value->fecha_vence < date('Y-m-d')) {
                    $estado ='Producto caducado';
                } else {
                    $estado ='Producto Proximo a Caducado';
                }

               $arrValue = [$value->codigo, $value->nombre, $value->descripcion, $value->serie."-", $value->pedido, $value->nombre_bodega, $value->nombrecomercial , $value->nombre_marca, $value->nombre_tipoproducto, $value->cantidad, $value->fecha_vence, $estado];

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

    public function buscador_usos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = [];
        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $eq= $request['pciente'];
        $marca       = $request['marca'];
        $codigo      = $request['codigo'];
        $nombres    = $request['nombres'];
        $proveedores = $request['proveedor'];

        //dd($request->all());

        $productos = $this->busqueda($request)->paginate(20);

        $doc_bodega =InvDocumentosBodegas::all();
        $marca_nombre  = marca::where('estado', '1')->get();
        $proveedor     = proveedor::where('estado', '1')->get();


        return view('insumos/reporte/buscador_usos/index', ['productos' => $productos, 'nombres' => $nombres, 'proveedores'=>$proveedores, 'proveedor'=> $proveedor , 'codigo'=>$codigo, 'marca' => $marca ,'marca'=>$marca, 'marca_nombre'=> $marca_nombre , 'fecha' => $fecha,'paciente'=>$eq, 'fecha_hasta' => $fecha_hasta, 'doc_bodega' => $doc_bodega, "data"=>$request->all()]);
    }

    ///
<<<<<<< HEAD
  
=======

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b

    ///
    public function descargar_dar_baja(Request $request)
    {
    $dar_baja=[];
    $fecha = ['fecha'];
    $fecha_hasta = ['fecha_hasta'];

    $dar_baja = ReportesController::busqueda_dar_baja($request)->paginate(20);
    //dd($dar_baja);
<<<<<<< HEAD
    
=======

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
    return view('insumos/reporte/dar_baja/index', ['fecha'=>$fecha, 'fecha_hasta'=>$fecha_hasta, 'dar_baja'=>$dar_baja]);
    }

    public static function busqueda_dar_baja(Request $request){
<<<<<<< HEAD
=======
        $id_empresa  = $request->session()->get('id_empresa');
>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
        $dar_baja = DB::table('inv_kardex as ik')
        ->join('producto as p','p.id', 'ik.id_producto')
        ->join('inv_det_movimientos as idm','idm.id', 'ik.id_inv_det_movimientos')
        ->join('marca as m', 'm.id', 'p.id_marca')
        ->where('ik.dar_baja', '1')
<<<<<<< HEAD
=======
        ->where('p.id_empresa', $id_empresa)
>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
        ->select('ik.updated_at', 'p.nombre', 'idm.serie', 'ik.cantidad', 'ik.referencia', 'm.nombre as nombre_marca');

        if(count($request->all()) > 0){
            if(!is_null($request->fecha)){
                  $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', ">=" ,"{$request->fecha} 00:00:00");
                }
<<<<<<< HEAD
           
=======

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
                if(!is_null($request->fecha_hasta)){
                   $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', "<=" ,"{$request->fecha_hasta} 23:59:59");
                 }
        }
        return $dar_baja;

    }

    public function reporte_dar_baja(Request $request)
    {
<<<<<<< HEAD
=======
        
        $id_empresa  = $request->session()->get('id_empresa');

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
        $dar_baja = DB::table('inv_kardex as ik')
        ->join('producto as p','p.id', 'ik.id_producto')
        ->join('inv_det_movimientos as idm','idm.id', 'ik.id_inv_det_movimientos')
        ->join('marca as m', 'm.id', 'p.id_marca')
        ->where('ik.dar_baja', '1')
<<<<<<< HEAD
=======
        ->where('p.id_empresa', $id_empresa)
>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
        ->select('ik.updated_at', 'p.nombre', 'idm.serie', 'ik.cantidad', 'ik.referencia', 'm.nombre as nombre_marca');

        if(count($request->all()) > 0){
            if(!is_null($request->fecha)){
                  $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', ">=" ,"{$request->fecha} 00:00:00");
                }
<<<<<<< HEAD
           
=======

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
                if(!is_null($request->fecha_hasta)){
                   $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', "<=" ,"{$request->fecha_hasta} 23:59:59");
                 }
           }


        $dar_baja=$dar_baja->get();
        //dd($dar_baja);

        $fecha_hoy = date('Y/m/d');
<<<<<<< HEAD
        
        Excel::create('Reporte_Dar_Baja'. $fecha_hoy , function ($excel) use ($dar_baja){
          
            $excel->sheet('Reporte Dar de Baja', function($sheet) use($dar_baja){
            $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
          
=======

        Excel::create('Reporte_Dar_Baja'. $fecha_hoy , function ($excel) use ($dar_baja){

            $excel->sheet('Reporte Dar de Baja', function($sheet) use($dar_baja){
            $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
            $fecha_hoy = date('Y/m/d');
             $sheet->mergeCells('A1:F2');
                       $sheet->cell('A1', function ($cell){
                           $cell->setValue('Reporte Dar de Baja');
                           $cell->setFontColor('#010101');
                           $cell->setBackground('#D1E9F2');
                           $cell->setFontWeight('bold');
                           $cell->setFontSize('20');
                           $cell->setAlignment('center');
                           $cell->setBorder('thin', 'thin', 'thin', 'thin');
                       });
<<<<<<< HEAD
   
   
            $i = 3;

            $arrTitulos1= ["Fecha", "Insumo", "Serie", "Cantidad", "Observación", "Marca"];
   
            $comienzo = 3;
   
=======


            $i = 3;

            $arrTitulos1= ["Fecha", "Insumo", "Serie", "Cantidad", "Observación", "Marca"];

            $comienzo = 3;

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
            for ($i=0; $i < count($arrTitulos1) ; $i++) {
               $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrTitulos1, $i) {
                       // manipulate the cel
                       $cell->setValue($arrTitulos1[$i]);
                       $cell->setBackground('#D1E9F2');
                       $cell->setFontWeight('bold');
                       $cell->setBorder('thin', 'thin', 'thin', 'thin');
               });
            }
            $comienzo++;
            //dd($biopsias->get());
            //dd($productos);
            foreach($dar_baja as $value){
<<<<<<< HEAD
              
=======

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
               $arrValue = [];
              // $nombre_proc = "";
               //$nombre_doc = "";
               //$procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->first();
               //if(!is_null($procedimiento_final)){
                //  $nombre_proc = $procedimiento_final->procedimiento;
              // }
<<<<<<< HEAD
  
 //              if (!is_null($value->id_doctor_examinador)){
   //               $nombre_doc = User::where('id', $value->id_doctor_examinador)->first();
     //          }
               
               $arrValue = [date('Y-m-d', strtotime($value->updated_at)), $value->nombre, $value->serie, $value->cantidad, $value->referencia, $value->nombre_marca];
   
=======

 //              if (!is_null($value->id_doctor_examinador)){
   //               $nombre_doc = User::where('id', $value->id_doctor_examinador)->first();
     //          }

               $arrValue = [date('Y-m-d', strtotime($value->updated_at)), $value->nombre, $value->serie, $value->cantidad, $value->referencia, $value->nombre_marca];

>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
               for ($i=0; $i < count($arrValue) ; $i++) {
                   $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i) {
                           // manipulate the cel
                           $cell->setValue($arrValue[$i]);
                           $cell->setBorder('thin', 'thin', 'thin', 'thin');
                   });
                }
                $comienzo++;
            }
<<<<<<< HEAD
              
          });   
         
   
         })->export('xlsx');
   
          
=======

          });


         })->export('xlsx');


>>>>>>> 8b9ae7ebfe9eca520ad417c10063595cb271612b
    }





    public static function busqueda($request){
       // if (!is_null($doc_bodega)){
           // if(!is_null($eq)){
                
                $id_empresa  = $request->session()->get('id_empresa');
                $productos = DB::table('inv_kardex as ik')
                ->join('inv_det_movimientos as idm','idm.id', 'ik.id_inv_det_movimientos')
                ->join('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
                ->join('pedido as p', 'p.id', 'idm.id_pedido' )
                ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
                ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
                ->join('producto as p1', 'p1.id', 'ik.id_producto')
                ->join('hc_procedimientos as hp', 'hp.id','idm.id_procedimiento')
                ->join('historiaclinica as h', 'h.hcid','hp.id_hc')
                ->join('marca as m', 'm.id','p1.id_marca')
                ->join('agenda as a','a.id','h.id_agenda')
                //->join('paciente as p2', 'p2.id','h.id_paciente')
                //->join('procedimiento as proc', 'proc.id', 'hp.id')
                ->leftjoin('paciente as u', 'u.id', 'h.id_paciente')
                ->where('p.id_empresa', $id_empresa)
                //->leftjoin('tipo as t', 't.id', 'p.tipo_producto')
                //->where('m.tipo', 0)
                //->select('p.codigo', 'p.id as producto_id', 'p.nombre as nombre_producto', 'm.serie', 'ped.pedido', 'mar.nombre as nombre_marca', 'b.nombre as nombre_bodega', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id','t.nombre as nombre_tipo','m.fecha_vencimiento');
                ->select('ik.total as precio_k','p1.codigo', 'p1.nombre as nombre_producto', 'prov.nombrecomercial' ,'a.fechaini', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'm.nombre as nombre_m', 'ik.descripcion', 'ik.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento' ,'hp.id as hc_id_procedimiento', 'hp.id_doctor_examinador' );

               //dd($productos->get());
                if(count($request->all()) > 0){
                    if(!is_null($request->codigo)){
                        $productos = $productos->where('p1.codigo', $request->codigo);
                    }
                    if (!is_null($request->nombres)) {
                        $productos = $productos->where('p1.nombre', 'like', '%' . $request->nombres . '%');
                    }
                    if(!is_null($request->tipo_)){
                        $productos = $productos->where("p.tipo", $request->tipo_);
                    }
                    if(!is_null($request->proveedor)){
                        $productos = $productos->where('prov.id', $request->proveedor);
                    }

                    if(!is_null($request->pciente)){
                        $eq = $request->pciente;
                        $productos = $productos->where(function ($jq1) use ($eq) {
                            $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
                            ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
                        });
                    }

                    if(!is_null($request->fecha)){
                        $productos = $productos = $productos->where('a.fechaini', ">=" ,"{$request->fecha} 00:00:00");
                    }

                    if(!is_null($request->fecha_hasta)){
                        $productos = $productos = $productos->where('a.fechaini', "<=" ,"{$request->fecha_hasta} 23:59:59");
                    }
                    if(!is_null($request->marca)){
                        $productos = $productos = $productos->where('m.id', $request->marca);
                    }
                }

                return $productos;
    }

    public function reporte_usos_productos (Request $request){
        //dd($request->all());
        $productos = ReportesController::busqueda($request)->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Uso_Productos'. $fecha_d , function ($excel) use ($productos){

          $excel->sheet('Reporte Uso de Productos', function($sheet) use($productos){
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

          $fecha_d = date('Y/m/d');
           $sheet->mergeCells('A1:M2');
                     $sheet->cell('A1', function ($cell){
                         $cell->setValue('Reporte Uso de Productos');
                         $cell->setFontColor('#010101');
                         $cell->setBackground('#D1E9F2');
                         $cell->setFontWeight('bold');
                         $cell->setFontSize('20');
                         $cell->setAlignment('center');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                     });
 
 
          $i = 3;
          
          $arrTitulos= ["Fecha de Utilización", "Paciente", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido" , "Serie", "Precio", "Total" ,"Factura/Consigna",  "Procedimiento", "Médico que realizo el procedimiento"];
 
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
          //dd($productos);
          foreach($productos as $value){
            
             $arrValue = [];
             $nombre_proc = "";
             $nombre_doc = "";
             $precio_total= [];
             $procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->first();
             if(!is_null($procedimiento_final)){
                $nombre_proc = $procedimiento_final->procedimiento;
             }

             if (!is_null($value->id_doctor_examinador)){
                $nombre_doc = User::where('id', $value->id_doctor_examinador)->first();
             }

             $precio_total = $value->cantidad *  $value->total;
             
             $arrValue = [$value->fechaini, " {$value->apellido1} {$value->apellido2} {$value->nombre1} {$value->nombre2}", $value->nombre_m, $value->nombre_producto, $value->cantidad, $value->lote, $value->vencimiento, $value->pedido, $value->serie."-", $value->total, $precio_total , $value->documento,  $nombre_proc->nombre, "{$nombre_doc->nombre1} {$nombre_doc->apellido1}"];
 
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


    public function buscador_usos_equipo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y/m/d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y/m/d');
        }
        $productos = DB::table('equipo as e')
            ->join('equipo_historia as eh', 'eh.id_equipo', 'e.id')
            ->join('historiaclinica as hc', 'hc.hcid', 'eh.hcid')
            ->join('users as u', 'u.id', 'hc.id_paciente')
            ->where('e.nombre', 'like', '%' . $request['nombre'] . '%')
            ->where('e.tipo', 'like', '%' . $request['tipo'] . '%')
            ->where('e.modelo', 'like', '%' . $request['modelo'] . '%')
            ->where('e.marca', 'like', '%' . $request['marca'] . '%')
            ->where('e.serie', 'like', '%' . $request['serie'] . '%');
        //dd($productos->get());
        if ($fecha != null && $fecha_hasta != null) {
            $productos = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        }

        $productos = $productos->paginate(20);

        return view('insumos/reporte/buscador_equipo/index', ['productos' => $productos, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombre' => $request['nombre'], 'tipo' => $request['tipo'], 'modelo' => $request['modelo'], 'marca' => $request['marca'], 'serie' => $request['serie']]);
    }

    public function buscador_equipo_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $productos = DB::table('equipo as e')
            ->join('equipo_historia as eh', 'eh.id_equipo', 'e.id')
            ->join('historiaclinica as hc', 'hc.hcid', 'eh.hcid')
            ->join('users as u', 'u.id', 'hc.id_paciente')
            ->where('e.nombre', 'like', '%' . $request['nombre'] . '%')
            ->where('e.tipo', 'like', '%' . $request['tipo'] . '%')
            ->where('e.modelo', 'like', '%' . $request['modelo'] . '%')
            ->where('e.marca', 'like', '%' . $request['marca'] . '%')
            ->where('e.serie', 'like', '%' . $request['serie'] . '%');
        //dd($productos->get());
        if ($fecha != null && $fecha_hasta != null) {
            $productos = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        }

        $productos = $productos->get();

        Excel::create('Reporte de Equipos Usados ', function ($excel) use ($productos) {

            $excel->sheet(date('Y-m-d'), function ($sheet) use ($productos) {

                $sheet->mergeCells('A1:J1');

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE EQUIPOS USADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERIE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');

                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MARCA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });

                $i = 3;
                foreach ($productos as $value) {
                    //dd($value);

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue("  ".$value->serie);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->tipo);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //$cell->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                        //$cell->getNumberFormat();
                        $cell->setValue($value->marca);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->prestamo == 1) {
                            $cell->setValue('Si');
                        } else {
                            $cell->setValue('No');
                        }
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_atencion);
                    });

                    $i++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(100)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            ob_end_clean();
        })->export('xlsx');
    }

    public function buscador_usos_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $eq= $request['pciente'];
        if(!is_null($eq)){
            $id_empresa  = $request->session()->get('id_empresa');

            $productos = DB::table('movimiento as m')
            ->leftjoin('producto as p', 'p.id', 'm.id_producto')
            ->join('pedido as ped', 'ped.id', 'm.id_pedido')
            ->join('marca as mar', 'mar.id', 'p.id_marca')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->leftjoin('movimiento_paciente as mp', 'mp.id_movimiento', 'm.id')
            ->leftjoin('hc_procedimientos as hc_prod', 'hc_prod.id', 'mp.id_hc_procedimientos')
            ->leftjoin('historiaclinica as hc', 'hc.hcid', 'hc_prod.id_hc')
            ->leftjoin('users as u', 'u.id', 'hc.id_paciente')
            ->leftjoin('tipo as t', 't.id', 'p.tipo_producto')
            ->where('m.tipo', 0)
            ->where('p.id_empresa', $id_empresa)
            ->select('p.codigo', 'p.id as producto_id', 'p.nombre as nombre_producto', 'm.serie', 'ped.pedido', 'mar.nombre as nombre_marca', 'b.nombre as nombre_bodega', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id','t.nombre as nombre_tipo','m.fecha_vencimiento','m.lote');
            $nombres2 = explode(" ", $eq);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $productos = $productos->where(function ($jq1) use ($eq) {
                    $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
                });

            } else {

                $productos = $productos->whereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
            }
        }else{
            $id_empresa  = $request->session()->get('id_empresa');

            $productos = DB::table('movimiento as m')
            ->leftjoin('producto as p', 'p.id', 'm.id_producto')
            ->join('pedido as ped', 'ped.id', 'm.id_pedido')
            ->join('marca as mar', 'mar.id', 'p.id_marca')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->leftjoin('movimiento_paciente as mp', 'mp.id_movimiento', 'm.id')
            ->leftjoin('hc_procedimientos as hc_prod', 'hc_prod.id', 'mp.id_hc_procedimientos')
            ->leftjoin('historiaclinica as hc', 'hc.hcid', 'hc_prod.id_hc')
            ->leftjoin('users as u', 'u.id', 'hc.id_paciente')
            ->leftjoin('tipo as t', 't.id', 'p.tipo_producto')
            ->where('m.tipo', 0)
            ->where('p.id_empresa', $id_empresa)
            ->select('p.codigo', 'p.id as producto_id', 'p.nombre as nombre_producto', 'm.serie', 'ped.pedido', 'mar.nombre as nombre_marca', 'b.nombre as nombre_bodega', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id','t.nombre as nombre_tipo','m.fecha_vencimiento','m.lote');
        }
        if ($fecha != null && $fecha_hasta != null) {
            $productos = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        } else {
            $fecha       = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
            $productos   = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        }

        $productos = $productos->get();

        $texto = "";
        foreach ($productos as $value) {
            $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
            $mas         = true;
        }

        foreach ($adicionales as $value2) {
            if ($mas == true) {
                $texto = $texto . $value2->procedimiento->nombre;
                $mas   = false;
            } else {
                $texto = $texto . ' + ' . $value2->procedimiento->nombre;
            }
        }

        //dd($productos);

        Excel::create('Reporte de Productos Usados ', function ($excel) use ($productos, $texto) {

            $excel->sheet(date('Y-m-d'), function ($sheet) use ($productos, $texto) {

                $sheet->mergeCells('A1:L1');

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE PRODUCTOS USADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO DE PRODUCTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');

                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERIE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');

                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PEDIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BODEGA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MARCA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE CADUCIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LOTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });

                $i = 3;
                foreach ($productos as $value) {
                    //dd($value);

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->codigo);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_producto);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_tipo);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue("  ".$value->serie);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //$cell->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                        //$cell->getNumberFormat();
                        $cell->setValue($value->pedido);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_bodega);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_marca);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value, $texto) {
                        // manipulate the cel
                        $cell->setValue($texto);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_atencion);
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_vencimiento);
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if(isset($value->lote)){
                            $cell->setValue($value->lote);
                        }

                    });

                    $i++;
                }

            });
            $cantidad_i=count($productos);
            $excel->getActiveSheet()->getStyle('D3:D'.$cantidad_i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $excel->getActiveSheet()->getStyle('A3:A'.$cantidad_i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
            ob_end_clean();
        })->export('xlsx');
    }

    public function modal_detalle($id)
    {
        $producto    = producto::find($id);
        $movimientos = movimiento::where('movimiento.id_producto', $id)
            ->leftjoin('movimiento_paciente as mp', 'mp.id_movimiento', 'movimiento.id')
            ->leftjoin('hc_procedimientos as hc_prod', 'hc_prod.id', 'mp.id_hc_procedimientos')
            ->leftjoin('historiaclinica as hc', 'hc.hcid', 'hc_prod.id_hc')
            ->leftjoin('users as u', 'u.id', 'hc.id_paciente')
            ->where('movimiento.tipo', 0)
            ->select('movimiento.serie', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id')
            ->get();
        return view('insumos/reporte/buscador_usos/modal', ['movimientos' => $movimientos, 'producto' => $producto]);
    }
}
