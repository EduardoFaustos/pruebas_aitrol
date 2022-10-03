<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Style_NumberFormat;
use Response;
use Session;
use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Empresa;
use Sis_medico\Equipo;
use Sis_medico\Equipo_Historia;
use Sis_medico\hc_procedimientos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Insumo_Plantilla;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Insumo_Plantilla_Item;
use Sis_medico\Insumo_Plantilla_Item_Control;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvCosto;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvTrasladosBodegas;
use Sis_medico\Log_movimiento;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Pedido;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Planilla_Procedimiento;
use Sis_medico\Producto;
//use Sis_medico\InvDetMovimientos;

class TransitoController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->conf_fc = 0;
        // $this->id_procedimiento_generico = 68;// poltoviejo
        $this->id_procedimiento_generico = 8; //gye
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 6, 7)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $productos = DB::table('producto as p')->join('movimiento as m', 'm.id_producto', 'p.id')->where('p.estado', '1')->groupBy('m.id_producto')->select('m.id_producto', 'm.tipo as tipo', 'm.cantidad', 'p.nombre', 'm.id_producto', 'p.estado', 'p.codigo', 'm.created_at as created_at', DB::raw('COUNT(CASE WHEN m.tipo=2 THEN 0 ELSE null END) as transito'), DB::raw('COUNT(CASE WHEN m.tipo=1 THEN 0 ELSE null END) as ingreso'), DB::raw('count(*) as cantidad_total, m.tipo'))->get();
        return view('insumos/transito/index', ['productos' => $productos, 'request' => $request]);
    }
    public function modal($id_producto, Request $request)
    {
        $productos = DB::table('log_movimiento as lm')
            ->join('producto as p', 'p.id', 'lm.id_producto')
            ->join('movimiento as m', 'm.id', 'lm.id_movimiento')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->join('users as u', 'u.id', 'lm.id_encargado')
            ->where('lm.id_producto', $id_producto)
            ->select('lm.cantidad', 'lm.motivo', 'lm.observacion', 'm.id', 'lm.tipo', 'm.serie', 'p.nombre as producto_nombre', 'u.nombre1 as nombre', 'u.apellido1 as apellido1', 'u.apellido2 as apellido2', 'lm.created_at as fecha')
            ->OrderBy('lm.created_at', 'desc')->get();
        return view('insumos/transito/modal', ['productos' => $productos, 'id' => $id_producto]);
    }
    public function toLoad($id_producto)
    {
        $productos = DB::table('log_movimiento as lm')
            ->join('producto as p', 'p.id', 'lm.id_producto')
            ->join('movimiento as m', 'm.id', 'lm.id_movimiento')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->join('users as u', 'u.id', 'lm.id_encargado')
            ->where('lm.id_producto', $id_producto)
            ->select('lm.cantidad', 'lm.motivo', 'lm.observacion', 'm.id', 'lm.tipo', 'm.serie', 'p.nombre as producto_nombre', 'u.nombre1 as nombre', 'u.apellido1 as apellido1', 'u.apellido2 as apellido2', 'lm.created_at as fecha');
        return $productos;
    }
    public function toLoadWithout($id_producto)
    {
        $productos = DB::table('log_movimiento as lm')
            ->join('producto as p', 'p.id', 'lm.id_producto')
            ->join('movimiento as m', 'm.id', 'lm.id_movimiento')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->join('users as u', 'u.id', 'lm.id_encargado')
            ->where('lm.id_producto', $id_producto);
        return $productos;
    }

    public function getData(Request $request, $id_producto)
    {

        $draw       = $request->get('draw');
        $start      = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column']; // Column index
        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value

        // Total records
        $totalRecords           = $this->toLoadWithout($id_producto)->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $this->toLoadWithout($id_producto)->select('count(*) as allcount')->where('serie', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = $this->toLoadWithout($id_producto)->orderBy($columnName, $columnSortOrder)
            ->where('m.serie', 'like', '%' . $searchValue . '%')
            ->select('lm.cantidad', 'lm.motivo', 'lm.observacion', 'm.id', 'lm.tipo', 'm.serie', 'p.nombre as producto_nombre', 'u.nombre1 as nombre', 'u.apellido1 as apellido1', 'u.apellido2 as apellido2', 'lm.created_at as fecha')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno      = $start + 1;
        foreach ($records as $record) {
            $id              = date('d/m/Y H:i:s', strtotime($record->fecha));
            $producto_nombre = $record->producto_nombre;
            $name            = $record->nombre . ' ' . $record->apellido1 . ' ' . $record->apellido2;
            //$email = $record->email;
            $er     = $record->tipo;
            $bodega = "";
            if ($record->tipo == 2) {
                $bodega = "Bodega Pentax";
            }
            $xdata = "";
            $fo    = "";
            if ($er == 1) {
                $fo    = "colorB1";
                $xdata = "Ingreso";
            } elseif ($er == 0) {
                $fo    = "colorA1";
                $xdata = "Salida";
            } elseif ($er == 2) {
                $fo    = "colorC1";
                $xdata = "Transito";
            } elseif ($er == -1) {
                $fo    = "colorD1";
                $xdata = "Reingreso";
            } elseif ($er == 4) {
                $fo    = "colorE1";
                $xdata = "De baja";
            }
            $data_arr[] = array(
                "fecha"           => $id,
                "serie"           => $record->serie,
                "producto_nombre" => $producto_nombre,
                "tipo"            => $xdata,
                "classf"          => $fo,
                "nombre"          => $name,
                "cantidad"        => $record->cantidad,
                "bodega"          => $bodega,

            );
        }

        $response = array(
            "draw"                 => intval($draw),
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData"               => $data_arr,
        );

        echo json_encode($response);
        exit;
    }
    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('insumos/transito/create');
    }
    public function nombre(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM users
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' AND
                  id_tipo_usuario != 2
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }

    public function nombre2(Request $request)
    {

        $nombre_encargado = $request['nombre_encargado'];

        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM users
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' AND
                  id_tipo_usuario != 2;
                  ";
        $nombres = DB::select($query);
        if ($nombres != array()) {
            $data = $nombres[0]->id;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }

    public function codigo(Request $request)
    {
        $nombre = $request['serie'];
        $data   = null;
        $query  = "SELECT m.*, p.*
              FROM movimiento m, producto p
              WHERE m.serie LIKE '" . $nombre . "' AND
              m.tipo = '1' AND
              m.id_producto = p.id AND m.usos >= 1 AND m.cantidad >= 1";

        //return $query;
        $productos = DB::select($query);

        //return $productos;

        if ($productos != array()) {
            $data = $productos[0]->nombre;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = DB::table('log_movimiento as lm')
            ->join('producto as p', 'p.id', 'lm.id_producto')
            ->join('movimiento as m', 'm.id', 'lm.id_movimiento')
            ->join('bodega as b', 'b.id', 'm.id_bodega')
            ->join('users as u', 'u.id', 'lm.id_encargado');
        //dd($request->all());
        if (!is_null($request['codigo'])) {
            $productos = $productos->where('m.serie', 'like', '%' . $request['codigo'] . '%');
        }
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $productos = $productos->whereBetween('lm.created_at', [date('Y/m/d', strtotime($request['fecha_desde'])) . ' 00:00:00', date('Y/m/d', strtotime($request['fecha_hasta'])) . ' 23:59:59']);
        }
        if (!is_null($request['fecha_hasta'])) {
            $productos = $productos->whereDate('lm.created_at', '<', $request['fecha_hasta']);
        }
        if (!is_null($request['tipo'])) {
            if ($request['tipo'] == '3') {
                $request['tipo'] = '0';
            }
            $productos = $productos->where('lm.tipo', $request['tipo']);
            if ($request['tipo'] == '0') {
                $request['tipo'] = '3';
            }
        }
        $productos = $productos->select('lm.cantidad', 'lm.motivo', 'lm.observacion', 'm.id', 'lm.tipo', 'm.serie', 'p.nombre as producto_nombre', 'u.nombre1 as nombre', 'u.apellido1 as apellido1', 'u.apellido2 as apellido2', 'lm.created_at as fecha')
            ->OrderBy('lm.created_at', 'desc')->paginate(15);
        $productos = DB::table('producto as p')->join('movimiento as m', 'm.id_producto', 'p.id')->where('p.estado', '1')->groupBy('m.id_producto')->select('m.id_producto', 'm.tipo as tipo', 'm.cantidad', 'p.nombre', 'p.estado', 'm.created_at as created_at', DB::raw('COUNT(CASE WHEN m.tipo=2 THEN 0 ELSE null END) as transito'), DB::raw('COUNT(CASE WHEN m.tipo=1 THEN 0 ELSE null END) as ingreso'))->get();
        //dd($productos);
        return view('insumos/transito/index', ['productos' => $productos, 'request' => $request]);
    }

    public function _serie_enfermero(Request $request)
    {

        $nombre               = $request['codigo'];
        $data                 = null;
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;

        $inv_serie = InvInventarioSerie::where('serie', $nombre)
            ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
            ->where('existencia', '!=', 0)
            ->where('estado', '!=', 0)
            ->first();

        $inv_serie_e = InvInventarioSerie::where('serie', $nombre)
            ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
            ->where('existencia', '!=', 0)
            ->where('estado', '!=', 0)
            ->sum('existencia');

        $p = Producto::where('codigo', $nombre)->first();

        if (is_null($inv_serie) and is_null($p)) {

            return 0;
        }
        if (isset($inv_serie->producto)) {
            $producto = $inv_serie->producto;
        } else {
            $producto = $p;
        }

        //dd($producto);
        if (isset($producto->id) and isset($inv_serie_e->existencia)) {
            if ($inv_serie_e->existencia == 0 and $producto->usos != 0) {
                return 0;
            }
        }

        // if ( $idusuario == '0924383631') {
        //     dd($inv_serie_e);
        // }

        $movimiento = Movimiento::where('serie', $nombre)->first();
        if (!isset($movimiento->id)) {
            $movimiento = Movimiento::where('id_producto', $producto->id)->first();
        }

        if (isset($producto->id) and isset($movimiento->id)) {
            $mov_pac                       = new Movimiento_Paciente;
            $mov_pac->id_movimiento        = $movimiento->id;
            $mov_pac->id_hc_procedimientos = $id_hc_procedimientos;
            $mov_pac->id_usuariocrea       = $idusuario;
            $mov_pac->id_usuariomod        = $idusuario;
            $mov_pac->ip_modificacion      = $ip_cliente;
            $mov_pac->ip_creacion          = $ip_cliente;
            $mov_pac->save();

            $log                  = new Log_movimiento;
            $log->id_producto     = $producto->id;
            $log->id_encargado    = $idusuario;
            $log->id_movimiento   = $movimiento->id;
            $log->observacion     = "Producto entregado a paciente";
            $log->tipo            = 0;
            $log->id_usuariocrea  = $idusuario;
            $log->id_usuariomod   = $idusuario;
            $log->ip_modificacion = $ip_cliente;
            $log->ip_creacion     = $ip_cliente;
            $log->save();
            return $mov_pac->id;
        } else {
            return 0;
        }
    }

    public function __serie_enfermero(Request $request)
    {
        // dd ($request);
        # egreso insumos
        //copia en EnfermeriaController
        $nombre               = $request['codigo'];
        $data                 = null;
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        //return $query;
        $producto = Movimiento::where('serie', $nombre)->where('usos', '>=', 1)->where('cantidad', '>=', 0)->where('tipo', '=', 2)->first();
        //dd($producto);
        $producto_2 = Movimiento::where('serie', $nombre)->where('usos', '>=', 1)->where('cantidad', '>=', 0)->where('tipo', '=', 1)->first();
        $producto_3 = Movimiento::join('producto', 'movimiento.id_producto', '=', 'producto.id')
            ->where('producto.codigo_siempre', 1)
            ->where('producto.codigo', $nombre)
            //->where('movimiento.usos', '>=', 1)
            //->where('movimiento.fecha_vencimiento', '>=', date('Y-m-d'))
            //->where('movimiento.cantidad', '>=', 1)
            //->where('movimiento.tipo', '=', 1)
            ->select('movimiento.*')->first();

        /*///////////////////////////////////////////////////////////////////
        // dd($request);
        $serie = InvInventarioSerie::where('serie',$request['codigo'])->first();
        dd($serie);
        $producto = $serie->producto;
        $input_movimiento_paciente = [
        'id_movimiento'        => $producto->id,
        'id_hc_procedimientos' => $id_hc_procedimientos,
        'id_usuariocrea'       => $idusuario,
        'id_usuariomod'        => $idusuario,
        'ip_modificacion'      => $ip_cliente,
        'ip_creacion'          => $ip_cliente,
        ];
        $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);
        return $id;
        ///////////////////////////////////////////////////////////////////*/
        DB::beginTransaction();
        try {
            if ($producto != '') {
                //producto que esta en transito
                if ($producto->fecha_vencimiento >= date('Y-m-d')) {
                    $uso = $producto->usos - 1;
                    //return $producto->usos;
                    if ($uso > 0) {
                        $tipo       = '2';
                        $cantidad_2 = 1;
                    } else {
                        $tipo       = '0';
                        $cantidad_2 = 0;
                    }
                    $input_movimiento = [
                        // 'cantidad'        => $cantidad_2,
                        'usos'            => $uso,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => $tipo,
                    ];
                    $producto_ingreso = $producto;
                    $producto_ingreso->update($input_movimiento);

                    $input_movimiento_paciente = [
                        'id_movimiento'        => $producto->id,
                        'id_hc_procedimientos' => $id_hc_procedimientos,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                    ];
                    $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);
                    Log_movimiento::create([
                        'id_producto'     => $producto->producto->id,
                        'id_encargado'    => $idusuario,
                        'id_movimiento'   => $producto->id,
                        'observacion'     => "Producto entregado a paciente",
                        'tipo'            => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    ## creo el documento de egreso
                    // $this->__serie_enfermeroget($nombre, $id_hc_procedimientos);
                    DB::commit();
                    return $id;
                } else {
                    return "caducado";
                }
            } elseif ($producto_2 != '') {
                //producto que esta en bodega
                if ($producto_2->fecha_vencimiento >= date('Y-m-d')) {
                    $uso = $producto_2->usos - 1;
                    if ($uso > 0) {
                        $cantidad_2 = 1;
                        $tipo       = '2';
                        $producto   = Producto::find($producto_2->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    } else {
                        $tipo       = '0';
                        $cantidad_2 = 0;
                        $producto   = Producto::find($producto_2->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    }
                    $input_movimiento = [
                        // 'cantidad'        => $cantidad_2,
                        'usos'            => $uso,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => $tipo,
                    ];
                    $producto_ingreso = $producto_2;
                    $producto_ingreso->update($input_movimiento);

                    $input_movimiento_paciente = [
                        'id_movimiento'        => $producto_2->id,
                        'id_hc_procedimientos' => $id_hc_procedimientos,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                    ];
                    $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);

                    Log_movimiento::create([
                        'id_producto'     => $producto_2->producto->id,
                        'id_encargado'    => $idusuario,
                        'id_movimiento'   => $producto_2->id,
                        'observacion'     => "Producto entregado a paciente",
                        'tipo'            => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    ## creo el documento de egreso
                    // $this->__serie_enfermeroget($nombre, $id_hc_procedimientos);
                    DB::commit();
                    return $id;
                } else {
                    return "caducado";
                }
            } elseif ($producto_3 != '') {
                //return $producto_3;
                //producto que esta en bodega
                if ($producto_3->fecha_vencimiento >= date('Y-m-d')) {
                    $uso = $producto_3->usos - 1;
                    if ($uso > 0) {
                        $cantidad_2 = 1;
                        $tipo       = '2';
                        $producto   = Producto::find($producto_3->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    } else {
                        $tipo       = '0';
                        $cantidad_2 = 0;
                        $producto   = Producto::find($producto_3->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    }
                    $input_movimiento = [
                        // 'cantidad'        => $cantidad_2,
                        'usos'            => $uso,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => $tipo,
                    ];
                    $producto_ingreso = $producto_3;
                    $producto_ingreso->update($input_movimiento);

                    $input_movimiento_paciente = [
                        'id_movimiento'        => $producto_3->id,
                        'id_hc_procedimientos' => $id_hc_procedimientos,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                    ];
                    $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);

                    Log_movimiento::create([
                        'id_producto'     => $producto_3->producto->id,
                        'id_encargado'    => $idusuario,
                        'id_movimiento'   => $producto_3->id,
                        'observacion'     => "Producto entregado a paciente",
                        'tipo'            => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    ## creo el documento de egreso
                    // $this->__serie_enfermeroget($nombre, $id_hc_procedimientos);
                    DB::commit();
                    return $id;
                } else {
                    return "caducado";
                }
            } else {
                return 'No se encontraron resultados';
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function serie_enfermero(Request $request)
    {

        $idusuario = Auth::user()->id;
        $mensaje   = "";
        $serie     = $request['codigo'];

        $data                   = null;
        $id_hc_procedimientos   = $request['id_hc_procedimientos'];
        $id_agenda              = $request['id_agenda'];
        $agenda                 = Agenda::find($id_agenda);
        $paciente               = $agenda->paciente;
        $ip_cliente             = $_SERVER["REMOTE_ADDR"];
        $idusuario              = Auth::user()->id;
        $id_movimiento_paciente = "";

        # 1. POR NUMERO DE SERIE
        // VERIFICO LA EXISTENCIA DEL INSUMO CONSIDERANDO LOS NUMEROS DE USOS TAMB
        $inv_serie = InvInventarioSerie::where('serie', $serie)
            ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
            ->where('existencia', '<>', 0)
            ->where('estado', '<>', 0)
            ->first();

        if (!isset($inv_serie->id)) {
            if (Auth::user()->id == '0922729587') {
                //dd("entra");
                //dd($inv_serie);
            }
            $inv_serie = InvInventarioSerie::where('serie', $serie)
                ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
                ->where('estado', '<>', 0)
                ->first();
        }
        $p = Producto::where('codigo', $serie)->first();

        if (is_null($inv_serie) and is_null($p)) {
            return "NO EXISTE EN INVENTARIO SERIE";
        }
        if (isset($inv_serie->producto)) {
            $producto = $inv_serie->producto;
        } else {
            $producto = $p;
        }

        if (isset($producto->id) or isset($inv_serie->existencia)) {
            if (Auth::user()->id == '0922729587') {
                //dd($inv_serie);
            }
            if ((isset($inv_serie->existencia) and $inv_serie->existencia == 0)) {
                return "NO HAY STOCK";
            }
            /*if (isset($producto->existencia) and $producto->usos != 0) {
                return "NO HAY STOCK";
            }*/
        }

        $codigo            = $producto->codigo;
        $observacion       = 'DESCARGO DE INSUMO';
        $precio            = 0;
        $lote              = "";
        $fecha_vencimiento = "";
        if (isset($inv_serie->id)) {
            $lote              = $inv_serie->lote;
            $fecha_vencimiento = $inv_serie->fecha_vence;
        }

        $invcosto = InvCosto::where('id_producto', $producto->id)->first();

        if (!is_null($invcosto)) {
            $precio = $invcosto->costo_promedio;
        }
        //PLANILLA INGRESADO POR VH
        $hc_procedimiento = hc_procedimientos::find($id_hc_procedimientos);

        $vh_procedimiento = null;
        foreach ($hc_procedimiento->hc_procedimiento_f as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $vh_procedimiento = $px->procedimiento->id; //dd($vh_procedimiento);
                break;
            }
        }
        $cabecera = null;
        if ($vh_procedimiento != null) {
            $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $vh_procedimiento)->first();
            if (is_null($planilla_procedimiento)) {
                $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $this->id_procedimiento_generico)->first();
            }
            if (!is_null($planilla_procedimiento)) {
                $id_plantilla = $planilla_procedimiento->id_planilla;
                $la_planilla  = Planilla::where('id_hc_procedimiento', $id_hc_procedimientos)->where('estado', 1)->first();
                if (is_null($la_planilla)) {

                    $a_proc = [
                        'fecha'               => date('Y-m-d H:i:s'),
                        'id_planilla'         => $id_plantilla,
                        'id_agenda'           => $id_agenda,
                        'id_movimiento'       => null,
                        'id_hc_procedimiento' => $id_hc_procedimientos,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        //'codigo' => ,
                        'estado'              => '1',
                        'observacion'         => 'Paciente: ' . $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2,
                    ];
                    $cabecera = Planilla::insertGetId($a_proc);
                } else {
                    $a_proc = [
                        'id_usuariomod'   => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                    ];

                    $la_planilla->update($a_proc);
                    $cabecera = $la_planilla->id;
                }
            } else {
                return "NO TIENE PLANTILLA";
            }
        } else {
            return "NO TIENE PROCEDIMIENTO PRINCIPAL";
        }
        //
        if ($cabecera == null) {
            return "NO EXISTE LA PLANILLA";
        }

        $tipo_plantilla = null;
        //dd($id_plantilla,$id_producto);
        $ins_plantilla_item_control = Insumo_Plantilla_Item_Control::where('id_plantilla', $id_plantilla)->where('id_producto', $producto->id)->first();

        if (!is_null($ins_plantilla_item_control)) {

            $tipo_plantilla = $ins_plantilla_item_control->tipo_plantilla;
        }

        if (isset($inv_serie->id) and $inv_serie->inventario->producto->usos == 0) {

            if ($inv_serie->fecha_vence >= date('Y-m-d') || $this->conf_fc == 0) {
                # GENERO EL DESCARGO
                $id_movimiento_paciente = $this->_serie_enfermero($request);
                // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                if (isset($inv_serie->id)) {
                    InvInventarioSerie::comprometer($inv_serie, 1);
                }
                // $this->documentoTrasladoCompra($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                // $id = $this->documentoEgreso($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                $vh_movimiento_pac = Movimiento_Paciente::find($id_movimiento_paciente);
                $a_detalle         = [
                    'codigo'                 => $codigo,
                    'id_planilla_cabecera'   => $cabecera,
                    //'procedimiento' => ,
                    'precio'                 => $precio,
                    'check'                  => '1',
                    'estado'                 => '1',
                    'id_usuariocrea'         => $idusuario,
                    'id_usuariomod'          => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente,
                    'movimiento'             => $vh_movimiento_pac->id_movimiento,
                    'cantidad'               => 1,
                    'serie'                  => $serie,
                    'lote'                   => $lote,
                    'fecha_vencimiento'      => $fecha_vencimiento,
                    'observacion'            => $observacion,
                    'tipo_plantilla'         => $tipo_plantilla,
                    'id_movimiento_paciente' => $id_movimiento_paciente,
                ];

                $detalle = Planilla_Detalle::insertGetId($a_detalle);
                return "ok";
            } else {
                return "caducado";
            }
        } elseif (isset($inv_serie->id) and $inv_serie->existencia_uso > 0) {
            // VERIFICO EL SI TIENE EXISTENCIA EN USOS //
            if ($inv_serie->fecha_vence >= date('Y-m-d') || $this->conf_fc == 0) {

                $id_movimiento_paciente = $this->_serie_enfermero($request); //dd($id_movimiento_paciente);
                // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                if (isset($inv_serie->id)) {
                    InvInventarioSerie::comprometer($inv_serie, 1);
                }
                // $this->documentoTrasladoCompra($inv_serie, $id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                // $id = $this->documentoEgreso($inv_serie, $id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                $vh_movimiento_pac = Movimiento_Paciente::find($id_movimiento_paciente);

                if (!is_null($vh_movimiento_pac)) {
                    $a_detalle = [
                        'codigo'                 => $codigo,
                        'id_planilla_cabecera'   => $cabecera,
                        //'procedimiento' => ,
                        'precio'                 => $precio,
                        'check'                  => '1',
                        'estado'                 => '1',
                        'id_usuariocrea'         => $idusuario,
                        'id_usuariomod'          => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'        => $ip_cliente,
                        'movimiento'             => $vh_movimiento_pac->id_movimiento,
                        'cantidad'               => '1',
                        'serie'                  => $serie,
                        'lote'                   => $lote,
                        'fecha_vencimiento'      => $fecha_vencimiento,
                        'observacion'            => $observacion,
                        'tipo_plantilla'         => $tipo_plantilla,
                        'id_movimiento_paciente' => $id_movimiento_paciente,
                    ];

                    $detalle = Planilla_Detalle::insertGetId($a_detalle);
                }

                return "ok";
            } else {

                //dd("no entra",$inv_serie);
                return "caducado";
            }
        }

        # 2. POR CODIGO DEL PRODUCTO
        // SI NO ENCUENTRA DATOS POR NUMERO DE SERIE VERIFICA POR CODIGO DEL PRODUCTO  Y BODEGA PARA OBTENER EL INVENTARIO
        $producto  = Producto::where('codigo', $serie)->first();
        $id_bodega = env('BODEGA_EGR_PACI1', 2);
        if (isset($producto->id)) {
            #inventario
            $inventario = InvInventario::getInventario($producto->id, $id_bodega);

            if (isset($inventario->id)) {
                #inventario serie
                $inv_serie = InvInventarioSerie::where('id_producto', $producto->id)
                    ->where('id_bodega', $id_bodega)
                    ->where('existencia', '!=', 0)
                    ->where('estado', '!=', 0);
                if ($this->conf_fc != 0) {
                    $inv_serie = $inv_serie->where('fecha_vence', '>=', date('Y-m-d'));
                }
                $inv_serie = $inv_serie->orderBy('id', 'DESC')->first();

                // if (Auth::user()->id=='0924383631') {
                //     dd($inv_serie);
                // }

                if (isset($inv_serie->id) or $producto->usos == 0) {
                    $id_movimiento_paciente = $this->_serie_enfermero($request);
                    // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                    if (isset($inv_serie->id)) {
                        InvInventarioSerie::comprometer($inv_serie, 1);
                    }
                    // $this->documentoTrasladoCompra($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                    // $id = $this->documentoEgreso($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                    $item_cant         = 1;
                    $vh_movimiento_pac = Movimiento_Paciente::find($id_movimiento_paciente);

                    $a_detalle = [
                        'codigo'                 => $codigo,
                        'id_planilla_cabecera'   => $cabecera,
                        //'procedimiento' => ,
                        'precio'                 => $precio,
                        'check'                  => '1',
                        'estado'                 => '1',
                        'id_usuariocrea'         => $idusuario,
                        'id_usuariomod'          => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'        => $ip_cliente,
                        'movimiento'             => $vh_movimiento_pac->id_movimiento,
                        'cantidad'               => $item_cant,
                        'serie'                  => $serie,
                        'lote'                   => $lote,
                        'fecha_vencimiento'      => $fecha_vencimiento,
                        'observacion'            => $observacion,
                        'tipo_plantilla'         => $tipo_plantilla,
                        'id_movimiento_paciente' => $id_movimiento_paciente,
                    ];

                    $detalle = Planilla_Detalle::insertGetId($a_detalle);
                    return "ok";
                } else {
                    return "NO HAY STOCK";
                }
            }
        } else {
            return "inconsistencia";
        }
    }

    public function __documentoEgreso__($inv_serie, $id_agenda, $id_hc_procedimientos, $cantidad, $cantidad_uso)
    {
        $id_bodega  = env('BODEGA_EGR_PACI1', 2); // bodega de descargo de medicina pentax
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            $cab_mov_inv = InvCabMovimientos::where('id_agenda', $id_agenda)->first();
            if (!isset($cab_mov_inv->id)) {
                # creo la cabecera del traslado #
                $documento = invDocumentosBodegas::where('abreviatura_documento', 'EGP')->first();
                $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum(env('BODEGA_EGR_PACI1', 2), 'EGP');
                if ($secuencia != 0) {
                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                        ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
                        ->first();
                    $cab_mov_inv                        = new InvCabMovimientos;
                    $cab_mov_inv->id_documento_bodega   = $documento->id;
                    $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                    $cab_mov_inv->id_bodega_origen      = $id_bodega;
                    $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                    $cab_mov_inv->observacion           = $documento->abreviatura_documento . " " . strtoupper($documento->documento) . " AGENDA: " . $id_agenda;
                    $cab_mov_inv->fecha                 = date('Y-m-d');
                    /* $cab_mov_inv->descuento             = $request['descuentx'];
                    $cab_mov_inv->subtotal              = $request['subtotal_12'];
                    $cab_mov_inv->subtotal_0            = $request['subtotal_0'];
                    $cab_mov_inv->iva                   = $request['iva'];
                    $cab_mov_inv->total                 = $request['total'];*/
                    $cab_mov_inv->id_empresa      = Session::get('id_empresa');
                    $cab_mov_inv->id_agenda       = $id_agenda;
                    $cab_mov_inv->ip_creacion     = $ip_cliente;
                    $cab_mov_inv->ip_modificacion = $ip_cliente;
                    $cab_mov_inv->id_usuariocrea  = $idusuario;
                    $cab_mov_inv->id_usuariomod   = $idusuario;
                    $cab_mov_inv->save();
                }
            }
            # I V A
            $iva = 0;
            if (isset($inv_serie->inventario->producto->iva) && $inv_serie->inventario->producto->iva == 1) {
                $conf = Ct_Configuraciones::find(3);
                $iva  = ($inv_serie->inventario->costo_promedio) * $conf->iva;
            }
            # TRAIGO EL DETALLE DEL INGRESO DEL ITEM
            $inicial = InvDetMovimientos::where('serie', $inv_serie->serie)
                ->where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();
            # DETALLES
            $det_mov_inv                         = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
            $det_mov_inv->id_producto            = $inv_serie->inventario->producto->id;
            $det_mov_inv->id_inv_inventario      = $inv_serie->id_inv_inventario;
            $det_mov_inv->id_procedimiento       = $id_hc_procedimientos;
            $det_mov_inv->cantidad               = $cantidad;
            $det_mov_inv->cant_uso               = $cantidad_uso;
            $det_mov_inv->serie                  = $inv_serie->serie;
            $det_mov_inv->lote                   = $inv_serie->lote;
            $det_mov_inv->fecha_vence            = $inv_serie->fecha_vence;
            $det_mov_inv->valor_unitario         = $inv_serie->inventario->costo_promedio;
            $det_mov_inv->subtotal               = $inv_serie->inventario->costo_promedio;
            $det_mov_inv->descuento              = 0;
            $det_mov_inv->iva                    = $iva;
            $det_mov_inv->total                  = $inv_serie->inventario->costo_promedio + $iva;
            $det_mov_inv->motivo                 = $cab_mov_inv->observacion;
            $det_mov_inv->id_pedido              = $inicial->cabecera->id_pedido;
            $det_mov_inv->ip_creacion            = $ip_cliente;
            $det_mov_inv->ip_modificacion        = $ip_cliente;
            $det_mov_inv->id_usuariocrea         = $idusuario;
            $det_mov_inv->id_usuariomod          = $idusuario;
            $det_mov_inv->save();

            // CALCULAR TOTALES

            // MOVIMIENTO EN KARDEX
            $kardex = InvKardex::setKardex($cab_mov_inv->id);
            DB::commit();
            return $cab_mov_inv->id;
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function serie_enfermero_equipo(Request $request)
    {
        //dd("EPA"); return;
        $nombre      = $request['codigo'];
        $data        = null;
        $id_historia = $request['id_historia'];
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $equipo      = Equipo::where('serie', $nombre)->where('estado', 1)->first();
        if (!is_null($equipo)) {
            Equipo_Historia::create([
                'id_equipo'       => $equipo->id,
                'hcid'            => $id_historia,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
            return 'ok';
        } else {
            return 'No se encontraron resultados';
        }
    }

    public function eliminar_equipo($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $equipo_historia = Equipo_Historia::findorfail($id);
        $equipo_historia->delete();
        return 'ok';
    }

    public function store(Request $request)
    {

        $serie = $request['serie'];
        // dd($serie);

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_encargado = $request['id_encargado'];

        $query = "SELECT m.*, p.*, m.usos as usos_producto, p.cantidad as cantidad1, p.id as producto_id
              FROM movimiento m, producto p
              WHERE m.serie LIKE '" . $serie . "' AND
               m.tipo = '1' AND
             m.id_producto = p.id AND m.usos >= 1 AND m.cantidad >= 1";

        $productos = DB::select($query);

        if ($productos != array()) {

            $calculo = $productos[0]->usos_producto - 1;
            //dd($calculo);
            //$cantidad  = 0;
            if ($calculo >= 1) {
                $cantidad_productos = $productos[0]->cantidad;
            } else {
                $anterior = movimiento::where('serie', $serie)->first();
                //$nueva_cantidad  = $anterior->cantidad -1;
                $cantidad_productos = $productos[0]->cantidad1 - 1;
                if ($cantidad_productos > 0) {
                    $calculo = $productos[0]->usos;
                }
            }

            $id          = $productos[0]->id;
            $producto_id = $productos[0]->producto_id;
            $movimiento  = Movimiento::where('serie', $serie)->get();
            // dd($cantidad);
            $id_movimiento = $movimiento[0]->id;

            $input = [
                'cantidad'        => '1',
                'id_encargado'    => $id_encargado,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'tipo'            => '2',
            ];
            $movimiento_cambio = Movimiento::where('serie', $serie)->where('tipo', '1')->where('usos', '>=', '1')->first();
            $movimiento_cambio->update($input);

            $input2 = [
                'cantidad'        => $cantidad_productos,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Producto::where('id', $producto_id)->update($input2);

            Log_movimiento::create([
                'id_producto'     => $id,
                'id_encargado'    => $id_encargado,
                'id_movimiento'   => $id_movimiento,
                'observacion'     => "Producto en Transito",
                'tipo'            => '2',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }
        return redirect()->intended('/transito');
    }
    public function transito(Request $request)
    {
        // $f_v = config('app.fecv');
        // dd($f_v);
        // $resp = InvCabMovimientos::calcularTotalCabMovimiento(14);
        // dd($resp);
        $transito = InvTrasladosBodegas::where('estado', '1')->orderBy('id', 'DESC')->get();

        $mes         = date('m');
        $fecha_desde = "";
        $fecha_hasta = "";
        $busq        = array();

        if (isset($request->fecha_desde) || !is_null($request->fecha_desde)) {
            $fecha_desde = date("Y-m-d", strtotime($request->fecha_desde));
        }

        if (isset($request->fecha_hasta) || !is_null($request->fecha_hasta)) {
            $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta));
        }

        $busq = [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
        ];

        if ($request['excel'] == 1) {
            $this->excel_transproducc($request);
        }

        $traslados = InvCabMovimientos::traslados($fecha_desde, $fecha_hasta);

        return view('insumos/transito/index_transito', compact('transito', 'traslados', 'busq'));
    }

    public function detalleTransito(Request $request)
    {
        $rec = InvDetMovimientos::where('id_inv_cab_movimientos', $request->id)
            ->join('producto as p', 'inv_det_movimientos.id_producto', 'p.id')
            ->select('inv_det_movimientos.serie', 'p.nombre', 'inv_det_movimientos.lote', 'inv_det_movimientos.cantidad', 'inv_det_movimientos.cant_uso', 'inv_det_movimientos.total')
            ->get();

        $data = array();
        foreach ($rec as $value) {
            $arreglo = [

                'serie'    => $value->serie,
                'producto' => $value->nombre,
                'lote'     => $value->lote,
                'cantidad' => $value->cantidad,
                'uso'      => $value->uso,
                'total'    => $value->total,
            ];

            array_push($data, $arreglo);
        }

        return $rec;
    }

    public function showSource(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $bodega     = Bodega::where('estado', '1')->whereNull('deleted_at')->get();
        $plantilla  = Insumo_Plantilla::where('estado', 1)->get();
        return view('insumos/transito/details', ['empresa' => $empresa, 'bodega' => $bodega, 'plantilla' => $plantilla]);
    }
    public function htmlSource(Request $request)
    {
        $id = $request['id'];
        // dd($request->all());
        if ($request['plantilla'] == '0') {
            $serie = InvInventarioSerie::where('serie', $id)
                ->where('id_bodega', $request['bodega'])
                ->where('existencia', '>', '0')
                // ->where('fecha_vence','>=',date('Y-m-d'))
                ->where('estado', '1')->first();

            //dd($serie);
            return view('insumos.transito.heading', ['serie' => $serie, 'id' => $id]);
        } else {
            $detalles = Insumo_Plantilla_Item::where('id_plantilla', $request['id'])
                ->select('id_producto')
                ->get();
            $productos = array();
            foreach ($detalles as $value) {
                # code...
                array_push($productos, $value->id_producto);
            }
            $serie = InvInventarioSerie::where('id_bodega', $request['bodega'])
                ->where('existencia', '>', '0')
                ->whereIn('id_producto', $productos)
                // ->where('fecha_vence','>=',date('Y-m-d'))
                ->where('estado', '1')->groupBy('id_producto')->get();
            //  dd($serie);
            return view('insumos.transito.heading', ['serie' => $serie, 'id' => $id, 'plantilla' => 1, 'pedido' => 0]);
        }
    }
    public function htmlSourcePedido(Request $request)
    {
        $detalles = '[]';
        $id       = $request['id'];
        $pedido   = Pedido::where('pedido', $request->pedido)->first();
        if (isset($pedido->id)) {
            $detalles = $pedido->movimientos;
        }
        return view('insumos.transito.heading', ['serie' => $detalles, 'id' => $id, 'id_bodega' => $request['bodega'], 'pedido' => 1, 'data' => $pedido]);
    }
    public function getPlantilla(Request $request)
    {
        $plantilla = Insumo_Plantilla_Item::all();
        return response()->json($plantilla);
    }
    public function modal_new(Request $request)
    {
        $query  = InvTrasladosBodegas::where('estado', '1')->get();
        $bodega = Bodega::where('estado', '1')->get();
        return view('insumos.transito.modal_transito', ['transito' => $query, 'bodega' => $bodega]);
    }
    public function storenew(Request $request)
    {
        //dd($request->all());
        //transaccion de bodega
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            $documento = InvDocumentosBodegas::where('abreviatura_documento', 'TRA')->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipo($request['bodega_saliente'], 'T');
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                    ->where('id_bodega', $request['bodega_saliente'])
                    ->first();
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $request['bodega_saliente'];
                $cab_mov_inv->id_bodega_destino     = $request['bodega_entrante'];
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $request['observaciones'];
                $cab_mov_inv->fecha                 = date('Y-m-d');
                //$cab_mov_inv->observacion           = 'TRASLADO '.str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->id_empresa      = Session::get('id_empresa');
                $cab_mov_inv->ip_creacion     = $ip_cliente;
                $cab_mov_inv->ip_modificacion = $ip_cliente;
                $cab_mov_inv->id_usuariocrea  = $idusuario;
                $cab_mov_inv->id_usuariomod   = $idusuario;
                $cab_mov_inv->save();
                $acum_subt  = 0;
                $acum_desc  = 0;
                $acum_iva   = 0;
                $acum_total = 0;

                for ($i = 0; $i < count($request['cantidad']); $i++) {
                    $iva        = 0;
                    $inventario = InvInventario::getInventario($request['id'][$i], $request['bodega_saliente']);
                    // dd($inventario);
                    // if (!isset($inventario->id)) {
                    //     $inventario = InvInventario::setNeoInventario($request['id'][$i], $request['bodega_entrante'], 0, 0);
                    // }
                    $producto = Producto::find($request['id'][$i]);
                    if (isset($producto->iva) && $producto->iva == 1) {
                        $conf = Ct_Configuraciones::find(3);
                        $iva  = ($request['cantidad'][$i] * $request['precio'][$i]) * $conf->iva;
                    }
                    /*else{
                    return ["respuesta"=>"error", "msj"=>"No se completo la transferencia"];
                    }*/
                    $cant_uso = 0;
                    if ($producto->usos != null) {
                        $cant_uso = $producto->usos;
                    }
                    if ($cant_uso == null or $cant_uso < 0) {
                        $cant_uso = 0;
                    }

                    $det_mov_inv                         = new InvDetMovimientos;
                    $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                    $det_mov_inv->id_producto            = $request['id'][$i];
                    $det_mov_inv->id_inv_inventario      = $inventario->id;
                    $det_mov_inv->cantidad               = $request['cantidad'][$i];
                    $det_mov_inv->cant_uso               = $request['cantidad'][$i] * $cant_uso;
                    $det_mov_inv->serie                  = $request['serie'][$i];
                    $det_mov_inv->lote                   = $request['lote'][$i];
                    $det_mov_inv->fecha_vence            = $request['vence'][$i];
                    $det_mov_inv->valor_unitario         = $request['precio'][$i];
                    $det_mov_inv->subtotal               = $request['cantidad'][$i] * $request['precio'][$i];
                    $det_mov_inv->descuento              = 0;
                    $det_mov_inv->iva                    = $iva;
                    $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                    $det_mov_inv->motivo                 = 'TRASLADO ' . str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                    //  $det_mov_inv->id_detalle_origen         = $id_movimiento;
                    //  $det_mov_inv->id_detalle_pedido         = $id_movimiento;
                    $det_mov_inv->ip_creacion     = $ip_cliente;
                    $det_mov_inv->ip_modificacion = $ip_cliente;
                    $det_mov_inv->id_usuariocrea  = $idusuario;
                    $det_mov_inv->id_usuariomod   = $idusuario;
                    $det_mov_inv->save();
                    # acumular valores
                    $acum_subt += $det_mov_inv->subtotal;
                    $acum_desc += $det_mov_inv->descuento;
                    $acum_iva += $det_mov_inv->iva;
                    $acum_total += $det_mov_inv->total;
                }
                $cab_mov_inv->descuento  = $acum_desc;
                $cab_mov_inv->subtotal   = $acum_subt;
                $cab_mov_inv->subtotal_0 = 0;
                $cab_mov_inv->iva        = $acum_iva;
                $cab_mov_inv->total      = $acum_total;
                $cab_mov_inv->save();
                // CALCULAR TOTALES
                InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
                $kardex = InvKardex::setKardex($cab_mov_inv->id);
                # crear los ingresos de traslados
                //DB::rollback();
                InvCabMovimientos::ingresoTrasladoPedido($cab_mov_inv->id, $cab_mov_inv->id);
                // DB::rollback();

                DB::commit();

                return ["respusta" => "success", "msj" => "Guardado correctamente"];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    public function editnew($id, Request $request)
    {
        $cabecera   = InvCabMovimientos::find($id);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $bodega     = Bodega::where('estado', '1')->get();
        $plantilla  = Insumo_Plantilla::where('estado', 1)->get();
        return view('insumos.transito.editnew', ['cabecera' => $cabecera, 'empresa' => $empresa, 'bodega' => $bodega, 'plantilla' => $plantilla]);
    }
    public function updatenew(Request $request)
    {
        //transaccion de bodega
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            $cab_mov_inv                  = InvCabMovimientos::find($request->id_inv_cab_movimiento);
            $cab_mov_inv->observacion     = $request['observaciones'];
            $cab_mov_inv->ip_modificacion = $ip_cliente;
            $cab_mov_inv->id_usuariomod   = $idusuario;
            // $cab_mov_inv->save();
            $acum_subt  = 0;
            $acum_desc  = 0;
            $acum_iva   = 0;
            $acum_total = 0;

            # cabecera del ingreso de traslado
            $cab_ing_tras = InvCabMovimientos::where('id_docum_origen', $cab_mov_inv->id)
                ->where('estado', 1)
                ->first();

            #   anulo los detalles del traslado
            InvDetMovimientos::where('estado', 1)
                ->where('id_inv_cab_movimientos', $cab_mov_inv->id)
                ->update(['estado' => 0]);
            for ($i = 0; $i < count($request['cantidad']); $i++) {
                // if ($i==1) {dd($request['serie'][$i]);}
                #   D   E   T   A   L   L   E   S   #
                $det_mov_inv = InvDetMovimientos::where('id_inv_cab_movimientos', $cab_mov_inv->id)
                    ->where('serie', $request['serie'][$i])
                    ->first();
                $producto = Producto::find($request['id'][$i]);
                $iva      = 0;
                if (isset($producto->iva) && $producto->iva == 1) {
                    $conf = Ct_Configuraciones::find(3);
                    $iva  = ($request['cantidad'][$i] * $request['precio'][$i]) * $conf->iva;
                }
                $cant_uso = 0;
                if ($producto->usos != null) {
                    $cant_uso = $producto->usos;
                }
                if ($cant_uso == null or $cant_uso < 0) {
                    $cant_uso = 0;
                }
                if (isset($det_mov_inv->id)) {
                    $det_mov_inv->cantidad = $request['cantidad'][$i];
                    $det_mov_inv->cant_uso = $request['cantidad'][$i] * $cant_uso;
                    // $det_mov_inv->lote                      = $request['lote'][$i];
                    // $det_mov_inv->fecha_vence               = $request['vence'][$i];
                    // $det_mov_inv->valor_unitario            = $request['precio' ][$i];
                    $det_mov_inv->subtotal = $request['cantidad'][$i] * $request['precio'][$i];
                    //$det_mov_inv->descuento                 = 0;
                    $det_mov_inv->iva             = $iva;
                    $det_mov_inv->total           = $det_mov_inv->subtotal + $det_mov_inv->iva;
                    $det_mov_inv->estado          = 1;
                    $det_mov_inv->ip_modificacion = $ip_cliente;
                    $det_mov_inv->id_usuariomod   = $idusuario;
                    // $det_mov_inv->save();
                    $det_mov_inv->actualizarDetalleMovimiento($det_mov_inv);
                    $det_mov_inv->kardex = 1;
                    $det_mov_inv->save();
                } else {
                    $inventario = InvInventario::getInventario($request['id'][$i], $request['bodega_saliente']);
                    if ($inventario == '[]') {
                        $inventario = InvInventario::setNeoInventario($request['id'][$i], $request['bodega_saliente'], 0, 0);
                    }
                    $det_mov_inv                         = new InvDetMovimientos;
                    $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
                    $det_mov_inv->id_producto            = $request['id'][$i];
                    $det_mov_inv->id_inv_inventario      = $inventario->id;
                    $det_mov_inv->cantidad               = $request['cantidad'][$i];
                    $det_mov_inv->cant_uso               = $request['cantidad'][$i] * $cant_uso;
                    $det_mov_inv->serie                  = $request['serie'][$i];
                    $det_mov_inv->lote                   = $request['lote'][$i];
                    $det_mov_inv->fecha_vence            = $request['vence'][$i];
                    $det_mov_inv->valor_unitario         = $request['precio'][$i];
                    $det_mov_inv->subtotal               = $request['cantidad'][$i] * $request['precio'][$i];
                    $det_mov_inv->descuento              = 0;
                    $det_mov_inv->iva                    = $iva;
                    $det_mov_inv->total                  = $det_mov_inv->subtotal + $det_mov_inv->iva;
                    $det_mov_inv->motivo                 = 'TRASLADO ' . $cab_mov_inv->numero_documento;
                    $det_mov_inv->ip_creacion            = $ip_cliente;
                    $det_mov_inv->ip_modificacion        = $ip_cliente;
                    $det_mov_inv->id_usuariocrea         = $idusuario;
                    $det_mov_inv->id_usuariomod          = $idusuario;
                    $det_mov_inv->save();
                    # ingresar a kardex  eltraslado
                    InvKardex::setKardex($cab_mov_inv->id);
                    # registar el ingreso a bodega por traslado
                    $det_mov_inv->ingresarDetalleMovimientoTransito($cab_ing_tras, $det_mov_inv, $cab_mov_inv);
                }
                # acumular valores
                $acum_subt += $det_mov_inv->subtotal;
                $acum_desc += $det_mov_inv->descuento;
                $acum_iva += $det_mov_inv->iva;
                $acum_total += $det_mov_inv->total;
            }
            $cab_mov_inv->descuento  = $acum_desc;
            $cab_mov_inv->subtotal   = $acum_subt;
            $cab_mov_inv->subtotal_0 = 0;
            $cab_mov_inv->iva        = $acum_iva;
            $cab_mov_inv->total      = $acum_total;
            $cab_mov_inv->save();
            // dd($cab_mov_inv);
            # actualizo cabecera del ingreso de traslado
            if (isset($cab_ing_tras->id)) {
                $cab_ing_tras->descuento  = $cab_mov_inv->descuento;
                $cab_ing_tras->subtotal   = $cab_mov_inv->subtotal;
                $cab_ing_tras->subtotal_0 = $cab_mov_inv->subtotal_0;
                $cab_ing_tras->iva        = $cab_mov_inv->iva;
                $cab_ing_tras->total      = $cab_mov_inv->total;
                $cab_ing_tras->save();
            }
            // DB::rollBack();
            DB::commit();
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
        return response()->json('ok');
    }
    public function eliminarTransito(Request $request)
    {
        // dd("Id:" . $request->id);
        InvCabMovimientos::anulacionIngresoTrasladoPedido($request->id);
        return response()->json('ok');
    }

    public function agregarMedicamentoPacienteDia(Request $request)
    {
        $data       = array();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $hc_procedimiento = hc_procedimientos::find($request->md_hc_procedimientos);
        if (!isset($hc_procedimiento->id)) {
            $data['status']  = "error";
            $data['message'] = "Sin hc procedimiento procedimiento";
            return response()->json($data);
        }
        $vh_procedimiento = 0;
        foreach ($hc_procedimiento->hc_procedimiento_f as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $vh_procedimiento = $px->procedimiento->id;
                break;
            }
        }

        $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $vh_procedimiento)->first();
        if (is_null($planilla_procedimiento)) {
            // $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $this->id_procedimiento_generico)->first();
            $planilla_procedimiento = $this->crearPlantillaBasicaProcedimiento($vh_procedimiento);
        }

        if (!isset($planilla_procedimiento->id)) {
            $data['status']  = "error";
            $data['message'] = "Sin planilla procedimiento";
            return response()->json($data);
        }
        $id_plantilla = $planilla_procedimiento->id_planilla;
        $cabecera     = Planilla::where('id_planilla', $id_plantilla)
            ->where('id_hc_procedimiento', $request->md_hc_procedimientos)
            ->first();
        if (!isset($cabecera->id)) {
            $observacion = 'Paciente: ' . $hc_procedimiento->historia->paciente->apellido1 . ' ' . $hc_procedimiento->historia->paciente->apellido2 . ' ' . $hc_procedimiento->historia->paciente->nombre1 . ' ' . $hc_procedimiento->historia->paciente->nombre2;
            $id_planilla = Planilla::insertGetId([
                'fecha'               => date('Y-m-d H:i:s'),
                'id_planilla'        => $id_plantilla,
                'id_agenda'           => $hc_procedimiento->historia->id_agenda,
                'id_hc_procedimiento' => $request->md_hc_procedimientos,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'observacion'         => $observacion,
                'estado'              => 1,
                'aprobado'            => 0,
            ]);
            $cabecera = Planilla::find($id_planilla);
        }

        $inv_serie = InvInventarioSerie::where('id_producto', $request->id_insumo)
            ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
            ->where('existencia', '>', 0)
            ->first();
        if (isset($inv_serie->id)) {
            $movimiento = Movimiento::where('serie', $inv_serie->serie)->first();
            if (isset($movimiento->id)) {
                $arr_mov = [
                    'id_producto'       => $movimiento->id_producto,
                    'cantidad'          => $request->cantidad,
                    'serie'             => $movimiento->serie,
                    'id_bodega'         => env('BODEGA_EGR_PACI1', 2),
                    'id_pedido'         => $movimiento->pedido->id,
                    'estado'            => '1',
                    'tipo'              => '0',
                    'fecha_vencimiento' => $movimiento->fecha_vencimiento,
                    'id_encargado'      => $idusuario,
                    'usos'              => 0,
                    'lote'              => $movimiento->lote,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'precio'            => $movimiento->precio,
                    'descuento'         => 0,
                    'descuentop'        => 0,
                    'consecion_det'     => 0,
                ];
                $id_movimiento = Movimiento::insertGetId($arr_mov);

                $movimiento_paciente                       = new Movimiento_Paciente;
                $movimiento_paciente->id_movimiento        = $id_movimiento;
                $movimiento_paciente->cantidad             = $request->cantidad;
                $movimiento_paciente->id_hc_procedimientos = $request->md_hc_procedimientos;
                $movimiento_paciente->ip_creacion          = $ip_cliente;
                $movimiento_paciente->ip_modificacion      = $ip_cliente;
                $movimiento_paciente->id_usuariocrea       = $idusuario;
                $movimiento_paciente->id_usuariomod        = $idusuario;
                // dd($movimiento_paciente);
                $movimiento_paciente->save();

                $a_detalle = [
                    'codigo'                 => $movimiento->producto->codigo,
                    'id_planilla_cabecera'   => $cabecera->id,
                    'precio'                 => $movimiento->precio,
                    'check'                  => 1,
                    'estado'                 => 1,
                    'id_usuariocrea'         => $idusuario,
                    'id_usuariomod'          => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente,
                    'movimiento'             => $id_movimiento,
                    'id_movimiento_paciente' => $movimiento_paciente->id,
                    'cantidad'               => $request->cantidad,
                    'serie'                  => $movimiento->serie,
                    'lote'                   => $movimiento->lote,
                    'fecha_vencimiento'      => $movimiento->fecha_vencimiento,
                ];

                $detalle = Planilla_Detalle::insertGetId($a_detalle);

                $data['status']  = "ok";
                $data['message'] = "Registro Se agrego a la planilla " . $cabecera->id . " con éxito";
            }
        } else {
            $data['status']  = "error";
            $data['message'] = "No se encontro existencia en inventario";
        }
        return response()->json($data);
    }

    public function crearPlantillaBasicaProcedimiento($id_procedimientos)
    {
        $ip_cliente             = $_SERVER["REMOTE_ADDR"];
        $idusuario              = Auth::user()->id;
        $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $this->id_procedimiento_generico)->first();
        // insumo_planilla_procedimiento
        $cab_planilla = Insumo_Plantilla_Control::find($planilla_procedimiento->id_planilla);
        if (isset($cab_planilla->id)) {
            $cabecera                        = $cab_planilla;
            $detalles                        = $cab_planilla->detalles;
            $n_cab_planilla                  = new Insumo_Plantilla_Control;
            $n_cab_planilla->codigo          = rand(0, 9) . rand(0, 9) . rand(0, 9);
            $n_cab_planilla->nombre          = $cabecera->nombre;
            $n_cab_planilla->estado          = 1;
            $n_cab_planilla->id_usuariocrea  = $idusuario;
            $n_cab_planilla->id_usuariomod   = $idusuario;
            $n_cab_planilla->ip_creacion     = $ip_cliente;
            $n_cab_planilla->ip_modificacion = $ip_cliente;
            $n_cab_planilla->nombre_oculto   = "Planilla creada automaticamente";
            $n_cab_planilla->save();

            foreach ($detalles as $row) {
                $n_det_planilla                  = new Insumo_Plantilla_Item_Control;
                $n_det_planilla->id_plantilla    = $n_cab_planilla->id;
                $n_det_planilla->id_producto     = $row->id_producto;
                $n_det_planilla->cantidad        = $row->cantidad;
                $n_det_planilla->estado          = $row->estado;
                $n_det_planilla->id_usuariocrea  = $idusuario;
                $n_det_planilla->id_usuariomod   = $idusuario;
                $n_det_planilla->ip_creacion     = $ip_cliente;
                $n_det_planilla->ip_modificacion = $ip_cliente;
                $n_det_planilla->save();
            }
            // asociar la plantilla al procedimiento
            $pp                   = new Planilla_Procedimiento;
            $pp->id_planilla      = $n_cab_planilla->id;
            $pp->id_procedimiento = $id_procedimientos;
            $pp->id_usuariocrea   = $idusuario;
            $pp->id_usuariomod    = $idusuario;
            $pp->ip_creacion      = $ip_cliente;
            $pp->ip_modificacion  = $ip_cliente;
            $pp->save();
            return $pp;
        }
        return null;
    }
    public function excel_transproducc(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $mes        = date('m');

        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];

        $titulos    = array("N°", "FECHA/HORA", "BODEGA ORIGEN", "BODEGA DESTINO", "OBSERVACION", "USUARIO  CREA");
        $subtitulos = array("Serie", "Nombre", "Lote", "Cantidad", "Cantidad de Uso", "Total");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        if (!is_null($request->fecha_desde) || !is_null($request->fecha_desde)) {
            $fecha_desde = date("Y-m-d", strtotime($request->fecha_desde));
        }

        if (!is_null($request->fecha_hasta) || !is_null($request->fecha_hasta)) {
            $fecha_hasta = date("Y-m-d", strtotime($request->fecha_hasta));
        }

        $busq = [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
        ];
        //dd($busq);
        $traslados = InvCabMovimientos::traslados($fecha_desde, $fecha_hasta);

        Excel::create('Administración de Productos- Transito', function ($excel) use ($titulos, $posicion, $traslados, $subtitulos) {
            $excel->sheet('Admin.Productos-Transito', function ($sheet) use ($titulos, $posicion, $traslados, $subtitulos) {
                $sheet->mergeCells('A1:F1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Administración de Productos- Transito');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 2; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#92CFEF');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/

                foreach ($traslados as $tp) {
                    //dd($tp);
                    $datos_excel = array();
                    array_push($datos_excel, $tp->numero_documento, $tp->created_at, "{$tp->bodega_origen->nombre}", "{$tp->bodega_destino->nombre}", "{$tp->observacion}", "{$tp->usuariocrea->nombre1} {$tp->usuariocrea->apellido1}");

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBackground('#D8E0E4');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                    $rec = InvDetMovimientos::where('id_inv_cab_movimientos', $tp->id)
                        ->join('producto as p', 'inv_det_movimientos.id_producto', 'p.id')
                        ->select(
                            'inv_det_movimientos.serie',
                            'p.nombre',
                            'inv_det_movimientos.lote',
                            'inv_det_movimientos.cantidad',
                            'inv_det_movimientos.cant_uso',
                            'inv_det_movimientos.total'
                        )
                        ->get();
                    //dd($rec);
                    if (count($rec) > 0) {
                        // dd($rec);

                        for ($i = 0; $i < count($subtitulos); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($subtitulos, $i) {
                                $cell->setValue($subtitulos[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#FC979A');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;

                        foreach ($rec as $r) {
                            //dd($r);
                            $arreglo = [
                                "{$r->serie}-",
                                $r->nombre,
                                $r->lote,
                                $r->cantidad,
                                $r->cant_uso,
                                $r->total,
                            ];

                            // dd($data);
                            for ($l = 0; $l < count($arreglo); $l++) {
                                $sheet->cell('' . $posicion[$l] . '' . $comienzo, function ($cell) use ($arreglo, $l) {
                                    $cell->setValue($arreglo[$l]);
                                    $cell->setBackground('#EBE4E4');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            }

                            $comienzo++;
                        }
                    }
                }
            });
            $excel->getActiveSheet()->getStyle("A1:A4000")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        })->export('xlsx');
    }

    public function editar_pedido_new_update(Request $request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
            //$movimiento = Movimiento::find($request->id);

            $inv_cab_movimiento = InvCabMovimientos::where('id_pedido', $movimiento->id_pedido)->first();
            $inv_det_movimientos = InvDetMovimientos::find($request->id);
            // $inv_det_movimientos = InvDetMovimientos::where('id_inv_cab_movimientos', $inv_cab_movimiento->id)
            //                         ->where('serie', $movimiento->serie)->where('id_producto', $movimiento->id_producto)->first();
            if ($request->type == 'lote') {

                //$movimiento->lote = $request->value;
                $inv_det_movimientos->lote = $request->value;

            } else if ($request->type == 'fecha_exp') {

                //$movimiento->fecha_vencimiento = $request->value;
                $inv_det_movimientos->fecha_vence = $request->value;
            }
            //$movimiento->save();
            $inv_det_movimientos->save();

            DB::commit();
            return ['status' => 'success', 'msj' => 'Editado Correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => 'No se Pudo Modificar', $e->getMessage()];
        }
    }
}
