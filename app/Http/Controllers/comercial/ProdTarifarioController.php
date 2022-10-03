<?php

namespace Sis_medico\Http\Controllers\comercial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Seguro;
use Sis_medico\PrecioProducto;
use Excel;
use PHPExcel_Worksheet_Drawing;

class ProdTarifarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $productos = [];

        return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);
    }
    public function buscarproductos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $producto = [];
        if (!is_null($request["search"])) {
            /*$producto = Ct_productos::where('id_empresa', $id_empresa)->where('codigo', "LIKE", "%{$request['search']}%")
                ->orWhere('nombre', 'LIKE', "%{$request['search']}%")
                ->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'))->take(20)->get();*/
            $producto = Ct_productos::where('estado_tabla', 1)
            //->where('codigo', "LIKE", "%{$request['term']}%")
            ->whereRaw('CONCAT(codigo," | ",nombre) like ?',"%{$request['search']}%")
            ->where('id_empresa', $id_empresa)->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as value'))
            ->take(50)->get();
        }
        //dd($producto);
        return response()->json($producto);
    }

    public function buscar_todos_productos(Request $request)
    {
        
        $id_empresa = $request->session()->get('id_empresa');//dd($id_empresa);
        /*$productos = Ct_productos::where('nombre','like','%'.$request->term.'%')
        ->where('estado_tabla','1')
        ->where('id_empresa',$id_empresa)->get();*/

        $producto1 = Ct_productos::where('estado_tabla', 1)->where('codigo', "LIKE", "%{$request['term']}%")->where('id_empresa', $id_empresa)->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as value'));
        $producto2 = Ct_productos::where('estado_tabla', 1)->where('nombre', 'LIKE', "%{$request['term']}%")->where('id_empresa', $id_empresa)->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as value'));
        $productos = $producto1->union($producto2)->get();

        $arr=null;
        $i = 0;
        foreach ($productos as $prod) {
            $arr[] = array('value' => $prod->value , 'id' => $prod->id );
        }
        //dd($arr);
        return $arr;
        //return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);
    }

    public function buscar_productos_contenido(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        //$productos = Ct_productos::where('estado_tabla', '1')->where('nombre','like', '%'.$request->producto.'%')->get();
        $productos = Ct_productos::where('estado_tabla', 1)
            //->where('codigo', "LIKE", "%{$request['term']}%")
            ->whereRaw('CONCAT(codigo," | ",nombre) like ?',"%{$request['producto']}%")
            ->where('id_empresa', $id_empresa)->take(50)->get();

        if ($request['excel'] == 1) {
            $this->excel1($request);
        }
        return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);
    }

    public function buscar(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $productos = Ct_productos::where('estado_tabla', '1')->where('id', $request->producto)->get();

        if ($request['excel'] == 1) {
            $this->excel();
        }
        return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);
    }

    /*public function excel()
    {
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('Productos Tarifarios', function ($excel) use ($posicion) {
            $excel->sheet('Productos Tarifarios', function ($sheet) use ($posicion) {
                //$sheet->mergeCells('A1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Lista de Productos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;
                $productos = Ct_productos::where('estado_tabla', '1')->get();
                for ($i = 0; $i < count($productos); $i++) {
                    $sheet->cell('A' . '' . $comienzo, function ($cell) use ($productos, $i) {
                        $cell->setValue($productos[$i]->nombre);
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $comienzo++;
                    
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(45)->setAutosize(false);
        })->export('xlsx');
    }*/

    public function index_tarifario(Request $request, $id_producto)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $producto = Ct_productos::where('estado_tabla', '1')->where('id', $id_producto)->where('id_empresa', $id_empresa)->first();
        $prod_tarifario = Ct_Productos_Tarifario::where('id_producto', $producto->id)->where('estado', '1')->get();

        return view('comercial/producto_tarifario/index_tarifario', ['empresa' => $empresa, 'producto' => $producto, 'prod_tarifario' => $prod_tarifario, 'id_producto' => $id_producto]);
    }

    public function crear_tarifario($id_producto)
    {

        $producto = Ct_productos::where('id', $id_producto)->first();

        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('seguros.promo_seguro', '<>', '1')
            ->orderBy('seguros.nombre', 'asc')
            ->get();


        return view('comercial/producto_tarifario/modal_crea_tarifario', ['producto' => $producto, 'seguros' => $seguros, 'id_producto' => $id_producto]);
    }

    public function guardar_tarifario(Request $request)
    {

        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        DB::beginTransaction();
        try {

            $prod_tarifario = Ct_Productos_Tarifario::where('nivel', $request->id_nivel)->where('id_producto', $request->id_producto)->where('estado', '1')->first();


            if (is_null($prod_tarifario)) {

                if ($request->id_seguro == 1) {
                    $ct_producto = Ct_productos::where('id', $request->id_producto)->first();
                    $arr_prod = [
                        'valor_total_paq'   => $request->precio,
                        'id_usuariomod'     => $id_usuario,
                        'ip_modificacion'   => $ip_cliente,
                    ];
                    $ct_producto->update($arr_prod);
                } else {
                    $arr = [
                        'id_seguro'         => $request->id_seguro,
                        'nivel'             => $request->id_nivel,
                        'id_producto'       => $request->id_producto,
                        'precio_producto'   => $request->precio,
                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];

                    Ct_Productos_Tarifario::create($arr);
                }
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'Ya existe el nivel', 'titulos' => 'Error'];
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function edit_tarifario($id)
    {
        $prod_tarifario = Ct_Productos_Tarifario::find($id);

        return view('comercial/producto_tarifario/modal_edit_tarifario', ['prod_tarifario' => $prod_tarifario,  'id' => $id]);
    }

    public function edit_particular($id)
    {
        $producto = Ct_productos::find($id);

        return view('comercial/producto_tarifario/modal_edit_particular', ['producto' => $producto,  'id' => $id]);
    }

    public function update_tarifario(Request $request)
    {
        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_prod_tar = $request->id_prod_tar;
        DB::beginTransaction();
        try {
            $prod_tarifario = Ct_Productos_Tarifario::find($id_prod_tar);
            $arr_up = [
                'precio_producto'   => $request->precio,
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $prod_tarifario->update($arr_up);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function update_particular(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $id_producto = $request->id_producto;
            $ct_producto = Ct_productos::where('id', $id_producto)->first();
            $arr_prod = [
                'valor_total_paq'   => $request->precio,
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];
            $ct_producto->update($arr_prod);
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function eliminar_tarifario($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $prod_tarifario = Ct_Productos_Tarifario::find($id);

        $arr_p = [
            'estado'            => 0,
            'id_usuariomod'     => $id_usuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $prod_tarifario->update($arr_p);
    }

   /* public function excel1(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        Excel::create('Producto Tarifario', function ($excel) use ($id_empresa) {
            $excel->sheet('Producto Tarifario', function ($sheet) use ($id_empresa) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Productos');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;



                $ct_productos_tarifario = Ct_Productos_Tarifario::where('estado', 1)->groupBy('id_producto')->select('id_producto')->get();
                $count = Ct_Productos_Tarifario::where('estado', 1)->groupBy('id_producto')->select('id_producto')->count();
                foreach ($ct_productos_tarifario as $key => $val) {

                    $ct_produ = Ct_productos::where('id', $val->id_producto)->where('estado_tabla', 1)->where('id_empresa', $id_empresa)->first();
                    if (!is_null($ct_produ)) {
                        $sheet->cell('A' . $comienzo, function ($cell) use ($ct_produ) {
                            $cell->setValue($ct_produ->nombre);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#EBE4E4');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $comienzo++;

                        foreach ($ct_produ->tarifarios as  $key => $tarifario) {

                            if ($key === 0) {
                                $sheet->cell('B' . $comienzo, function ($cell) {
                                    $cell->setValue('Nivel');
                                    $cell->setBackground('#EBE4E4');
                                    $cell->setFontWeight('bold');
                                    $cell->setAlignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('C' . $comienzo, function ($cell) {
                                    $cell->setValue('Nombre del producto');
                                    $cell->setFontWeight('bold');
                                    $cell->setBackground('#EBE4E4');
                                    $cell->setAlignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->cell('D' . $comienzo, function ($cell) {
                                    $cell->setValue('Precio del producto');
                                    $cell->setFontWeight('bold');
                                    $cell->setBackground('#EBE4E4');
                                    $cell->setAlignment('center');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $comienzo++;
                            }
                            $sheet->cell('B' . $comienzo, function ($cell) use ($tarifario) {
                                $cell->setValue($tarifario->nivel);
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('C' . $comienzo, function ($cell) use ($tarifario) {
                                $cell->setValue($tarifario->producto->nombre);
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('D' . $comienzo, function ($cell) use ($tarifario) {
                                $cell->setValue(number_format($tarifario->precio_producto, 2, ',', ''));
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $comienzo++;
                        }
                    }
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getStyle("A1:A3000")->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }*/
    public function excel(Request $request){
        $id_empresa = $request->session()->get('id_empresa');

        $productos = Ct_Productos::where('estado_tabla','1')->where("id_empresa",$id_empresa)->get();
         
        //$insumos_baja = $insumos_baja->get();
        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Tarifario_Pro'. $fecha_d , function ($excel) use ($productos){
          
          $excel->sheet('Reporte Tarifario Productos', function($sheet) use($productos){
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
        
          $fecha_d = date('Y/m/d');
           $sheet->mergeCells('A1:F2');
                     $sheet->cell('A1', function ($cell){
                         $cell->setValue('Reporte Productos Tarifario');
                         $cell->setFontColor('#010101');
                         $cell->setBackground('#D1E9F2');
                         $cell->setFontWeight('bold');
                         $cell->setFontSize('20');
                         $cell->setAlignment('center');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                     });
 
 
          $i = 3;
          
          $arrTitulos= ["Id", 'Codigo' ,"Nombre", "Iva", "Valor PVP", 'Grupo'];

          $arrTitulos2= ["","Seguro", 'Nivel', "valor"];
 
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
          foreach($productos as $value){
            
             $arrValue = [];
           
             $precio   = $value->valor_total_paq;
                if($precio == null || $precio == 0){
                    $precio_producto = PrecioProducto::where('codigo_producto',$value->codigo)->orderBy('nivel','asc')->get()->first();
                    if(!is_null($precio_producto)){
                        $precio = $precio_producto->precio;    
                    }else{
                        $precio = 0;
                    }
                }
              $iva = 'NO';
              if ($value->iva==1){
                  $iva='Si';
              }
              $tipo_grupo = "";
              if($value->grupo==1){
                 $tipo_grupo = 'Insumo';
              }elseif($value->grupo==2){
                $tipo_grupo = 'Medicamentos';
             }elseif($value->grupo==3){
                $tipo_grupo = 'Servicios';
             }elseif($value->grupo==4){
                $tipo_grupo = 'Procedimientos';
             }elseif($value->grupo==5){
                $tipo_grupo = 'Otros';
             }elseif($value->grupo==6){
                $tipo_grupo = 'Honorario Medico';
             }elseif($value->grupo==7){
                $tipo_grupo = 'Equipo';
             }elseif($value->grupo==8){
                $tipo_grupo = 'Honorario Anestesiologo';
             }elseif($value->grupo==9){
                $tipo_grupo = 'Membresias';
             }
             
             $arrValue = [$value->id, $value->codigo, $value->nombre, $iva, $precio, $tipo_grupo];
 
             for ($i=0; $i < count($arrValue) ; $i++) {
                 $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i,$abec){
                         // manipulate the cel
                         $cell->setValue($abec[$i] == "E" ? "$ ".  $arrValue[$i] : $arrValue[$i]);
                         $cell->setFontWeight('bold');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                 });
              }
              $comienzo++;
              $prod_tarifario = $value->tarifarios->where("estado", '1');
              if($prod_tarifario->count() > 0) {
                for ($i=1; $i < count($arrTitulos2) ; $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrTitulos2, $i) {
                            // manipulate the cel
                            $cell->setValue($arrTitulos2[$i]);
                            //$cell->setBackground('#D1E9F2');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                 }
                 $comienzo++;
              }
            foreach($prod_tarifario as $prd_tarif){
            // $precio_tari = $prd_tarif->precio_producto;
            // $precio_tari1 = $precio_tari
             $arrValue = ["",$prd_tarif->seguro->nombre, $prd_tarif->fxnivel->nombre, $prd_tarif->precio_producto];
             
             for ($i=1; $i < count($arrValue) ; $i++) {
                 $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i, $abec) {
                         // manipulate the cel
                         $cell->setValue($abec[$i] == "D" ? "$ ".  $arrValue[$i] : $arrValue[$i] );
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                         //$cell->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');   
                         
                 });
              } 
             $comienzo++;
            }
            $comienzo++;

          }
        
        });   
        $excel->getActiveSheet()->getColumnDimension("B")->setWidth(25)->setAutosize(false);
        $excel->getActiveSheet()->getColumnDimension("C")->setWidth(57)->setAutosize(false);
 
       })->export('xlsx');
 


    }
}
