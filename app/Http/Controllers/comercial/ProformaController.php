<?php

namespace Sis_medico\Http\Controllers\comercial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;
use Sis_medico\Http\Controllers\Controller;
//use Sis_medico\Http\Controllers\contable\NuevoReciboCobroController;
use Response;
use Svg\Tag\Rect;
use Session;
use Excel;
use Sis_medico\User;
use Sis_medico\ProformaAgrupador;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Protocolo;
use Sis_medico\Paciente;
use Sis_medico\Proforma_Cabecera;
use Sis_medico\Labs_doc_externos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\PrecioProducto;
use Sis_medico\Ct_productos;
use Sis_medico\Proforma_Detalle;
use Sis_medico\Agenda;
use Sis_medico\Ct_Clientes;
use Sis_medico\Empresa;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Http\Controllers\contable\NuevoReciboCobroController;
use Sis_medico\ProformaAgrupadorDetalles;
use Sis_medico\Seguro;

class ProformaController extends Controller
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
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 6, 7,5)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        //  dd("HOLA");

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();se mmuestran todos
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros1    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '1')->select('s.nombre', 's.id')->get();
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.nombre', 's.id')->groupBy('s.id', 's.nombre')->orderBy('s.nombre')->get();
        //dd($seguros2);
        //$seguros = $seguros1->union($seguros2)->get();
        //dd($seguros);
        //$seguros    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '0')->orderBy('s.nombre')->get();

        $protocolos = Protocolo::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();


        return view('comercial/proforma/index', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros1' => $seguros1, 'seguros2' => $seguros2, 'protocolos' => $protocolos, 'empresas' => $empresas]);
    }

    public function buscarPaciente(Request $request)
    {
        //dd($request->all());
        $paciente = Paciente::where('paciente.id', $request->id)->join('users as u', 'paciente.id_usuario', 'u.id')->select('paciente.*', 'u.email as pemail')->first();

        $proforma = Proforma_Cabecera::where('id_paciente', $request->id)->where('pagado', 0)->where('estado','-1')->get();

        //dd(count($proforma));
        $urls = url("comercial/proforma/editar");
        $enlaces = "";

        if (count($proforma) > 0) {
            foreach ($proforma as $value) {
                $href = "<a target='_blank' class='label label-warning' href='{$urls}/{$value->id}'> Click Aqui </a><br>";
                $enlaces = $enlaces . $href;
            }
            $enlaces = "<div id='enlaces_grupo'><p style='font-weight: bold;'>Tiene Proforma Creada</p> {$enlaces} </div>";
        }
        if (!is_null($paciente)) {
            return ["status" => "success", "paciente" => $paciente, "enlaces" => $enlaces];
        } else {
            return ["status" => "error"];
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $id_empresa = $request->session()->get('id_empresa');
        $fecha = date("Y/m/d");
        DB::beginTransaction();
        try {
            $paciente = Paciente::where('id', $request->id)->first();
            $datos = [
                'observacion'       => "Proforma Cliente: {$fecha}",
                'id_paciente'       => $request->id,
                'id_seguro'         => $request->id_seguro,
                'estado'            => -1,
                'fecha_emision'     => date('Y-m-d'),
                'total_final'       => 0,
                'oda'               => 0,
                'valor_contable'    => 0,
                'subtotal'          => 0,
                'subtotal_12'       => 0,
                'subtotal_0'        => 0,
                'descuento'         => 0,
                'base_imponible'    => 0,
                'numero_oda'        => '',
                'iva_total'         => 0,
                'pagado'                => 0,
                'tipo_identificacion'   => 5,
                'razon_social'      => "{$paciente->nombre1} {$paciente->nombre2} {$paciente->apellido1} {$paciente->apellido2}",
                'id_empresa'        => $id_empresa,
                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];
            $id_proforma = Proforma_Cabecera::insertGetId($datos);
            DB::commit();
            return ["status" => "success", "id_proforma" => $id_proforma];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "No se creo la cotizacion", "exp" => $e->getMessage()];
        }
    }

    public function eliminar_proforma($id)
    {
   
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //$orden    = Ct_Orden_Venta::find($id);
        $orden    = Proforma_Cabecera::find($id);
        //  dd($id, $orden);
        

        $arr_cabecera = [
           
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'estado'                => 0,

        ];

        $orden->update($arr_cabecera);

        return redirect(route('comercial.proforma.index_proforma'));
       
    }

    public function crearPaciente(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        //dd($request->all());
        DB::beginTransaction();

        try {
            $user = User::find($request['id']);
           
            if (is_null($user)) {
                //dd("create user");
                User::create([
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['telefono1'],
                    // 'telefono2'        => $request['telefono2'],
                    //'id_pais'          => $request['id_pais'],
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $request['email'],
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => $idusuario,
                    'id_usuariomod'    => $idusuario,
                ]);
            }
            $paciente_creado = paciente::find($request['id']);
            //dd($paciente_creado);
            if (is_null($paciente_creado)) {

                paciente::create([
                    'id'                    => $request['id'],
                    'nombre1'               => strtoupper($request['nombre1']),
                    'nombre2'               => strtoupper($request['nombre2']),
                    'apellido1'             => strtoupper($request['apellido1']),
                    'apellido2'             => strtoupper($request['apellido2']),
                    'telefono1'             => $request['telefono1'],
                    // 'telefono2'             => $request['telefono22'],
                    'nombre1familiar'       => strtoupper($request['nombre1']),
                    'nombre2familiar'       => strtoupper($request['nombre2']),
                    'apellido1familiar'     => strtoupper($request['apellido1']),
                    'apellido2familiar'     => strtoupper($request['apellido2']),
                    // 'parentesco'            => $request['parentesco'],
                    // 'parentescofamiliar'    => $request['parentesco'],
                    // 'id_pais'               => $request['id_pais2'],
                    // 'fecha_val'             => $request['fecha_val'],
                    // 'cod_val'               => $request['cod_val'],
                    // 'validacion_cv_msp'     => $request['validacion_cv_msp'],
                    // 'validacion_nc_msp'     => $request['validacion_nc_msp'],
                    // 'validacion_sec_msp'    => $request['validacion_sec_msp'],
                    // 'codigo_validacion_msp' => $codigo_validacion_msp,
                    'fecha_nacimiento'      => $request['fecha_nacimiento'],
                    //'telefono3'             => $request['telefono2'],
                    'tipo_documento'        => 1,
                    'imagen_url'            => ' ',
                    //  'menoredad'             => $request['menoredad'],
                    'id_seguro'             => $request['id_seguro'],
                    'id_usuario'            => $request['id'],
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    // 'origen'                => $request['origen'],
                    // 'origen2'               => $origen2,
                    // 'otro'                  => $otro,
                    // 'referido'              => $request['referido'],
                    // 'mail_opcional'         => $request['email2'],
                    // 'papa_mama'             => $request['papa_mama'],
                ]);
            }
            DB::commit();
            return ["status" => "success", "msj" => "Se creo el paciente"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "No se pudo crear el paciente"];
        }
    }

    public function editar($id)
    {

        $orden = Proforma_Cabecera::find($id);

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();


        $convenios = DB::table('convenio as c')
            ->where('c.id_seguro', $orden->id_seguro)
            ->join('nivel as n', 'n.id', 'c.id_nivel')
            ->select('c.*', 'n.nombre', 'n.id as id_nivel')
            ->get();

        $seguros1    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '1')->orderBy('s.nombre');
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.*')->orderBy('s.nombre');
        $seguros     = $seguros1->union($seguros2)->get();


        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();

        return view('comercial/proforma/editar', ['usuarios' => $usuarios, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'convenios' => $convenios]);
    }

    public function detalles($id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $orden      = Proforma_Cabecera::find($id_orden);
        $detalles   = $orden->detalles;

        return view('contable/nuevo_recibo/detalles', ['orden' => $orden, 'detalles' => $detalles]);
    }

    public function guardar_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_orden = $request->id;
        $id_producto = $request->producto_nuevo;

        //RUTINA PARA OBTENER EL PRECIO DE LOS PRODUCTOS
        $orden    = Proforma_Cabecera::find($id_orden);
        $producto = Ct_productos::find($id_producto);
        $precio   = $producto->valor_total_paq;

        if ($precio == null || $precio == 0) {
            $precio_producto = PrecioProducto::where('codigo_producto', $producto->codigo)->orderBy('nivel', 'asc')->get()->first();
            $precio = $precio_producto != null ? $precio_producto->precio : 0;
        }

        if ($orden->nivel != null or $orden->nivel >= 0) {
            $tarifario = $producto->tarifarios->where('nivel', $orden->nivel)->where('estado', 1)->first();
            //dd($tarifario);
            if (!is_null($tarifario)) {
                $precio = $tarifario->precio_producto;
            }
        }

        $iva = 0;
        if ($producto->iva == 1) {
            $iva = Ct_Configuraciones::find('3');
            if (!is_null($iva)) {
                $iva = $iva->iva;
            } //TERRIBLE EL TEMA DEL IVA
        }

        $valor_iva = $precio * $iva;
        $valor_iva = round($valor_iva, 2);

        $arr_producto = [
            'id_proforma'           => $id_orden,
            'cod_prod'              => $producto->codigo,
            'nombre_producto'       => $producto->nombre,
            //'descripcion'         => $producto->nombre,
            'id_producto'           => $id_producto,
            'precio'                => $precio,
            'cantidad'              => 1,
            'total'                 => $precio,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'p_oda'                 => 0,
            'valor_oda'             => 0,
            'tipo_dcto'             => '%',
            'p_dcto'                => 0,
            'descuento'             => 0,
            'iva'                   => $iva,
            'valor_iva'             => $valor_iva,
            'cobrar_paciente'       => $precio,
            'cobrar_seguro'         => 0,
        ];
        Proforma_Detalle::create($arr_producto);

        ProformaController::recalcular($id_orden);

        return "ok";
    }
    public function actualizar_descripcion(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_detalle = $request->id;
        $id_producto = $request->id_producto;
        $descripcion = $request->descripcion;

        //$detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);
        $detalle  = Proforma_Detalle::find($id_detalle);
        $arr_producto = [

            'descripcion'            => $descripcion,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $detalle->update($arr_producto);


        return "ok";
    }


    public function actualizar_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_detalle = $request->id;
        $id_producto = $request->id_producto;
        $cantidad = $request->cantidad;
        $precio   = $request->precio;
        $p_cpac   = $request->p_cpac;
        $deducible= $request->deducible;
        //$p_oda    = $request->p_oda;
        //$cobrar_paciente = $request->cobrar_paciente;
        $p_dcto    = $request->p_dcto;
        //$descuento = $request->descuento;
        $p_oda     = 100 - $p_cpac;

        //$detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);
        $detalle  = Proforma_Detalle::find($id_detalle);
        $cabecera = $detalle->cabecera;
        $producto = Ct_productos::find($id_producto);
        //  dd($detalle);

        $iva = 0;
        if ($producto->iva == 1) {
            $iva = Ct_Configuraciones::find('3');
            if (!is_null($iva)) {
                $iva = $iva->iva;
            } //TERRIBLE EL TEMA DEL IVA
        }

        $subtotal  = $cantidad * $precio;
        if($cabecera->id_seguro == '4'){
            $subtotal  = $subtotal - $deducible;
        }
        $descuento = $subtotal * $p_dcto / 100;
        $descuento = round($descuento, 2);
        //$subtotal  = $subtotal - $descuento;

        $cobrar_paciente = $subtotal * $p_cpac / 100;
        $cobrar_paciente = round($cobrar_paciente, 2);
        $valor_oda       = $subtotal - $cobrar_paciente;
        $subtotal        = $subtotal - $valor_oda;
        if($cabecera->id_seguro != '4'){
            $valor_oda   = $valor_oda - $deducible;
        }

        $valor_iva = ($subtotal - $descuento) * $iva;
        $valor_iva = round($valor_iva, 2);
        $total     = $subtotal - $descuento + $valor_iva;
        //dd($id_orden);

        $arr_producto = [

            'precio'            => $precio,
            'cantidad'          => $cantidad,
            'total'             => $subtotal - $descuento,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'p_oda'             => $p_oda,
            'valor_oda'         => $valor_oda,
            'p_dcto'            => $p_dcto,
            'descuento'         => $descuento,
            'iva'               => $iva,
            'valor_iva'         => $valor_iva,
            'cobrar_paciente'   => $cobrar_paciente,
            'cobrar_seguro'     => $valor_oda,
            'valor_deducible'   => $deducible
        
        ];

        $detalle->update($arr_producto);

        ProformaController::recalcular($detalle->id_proforma);

        return "ok";
    }

    public function recalcular($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //$orden    = Ct_Orden_Venta::find($id);
        $orden    = Proforma_Cabecera::find($id);
        //  dd($id, $orden);
        $total_con_descuento = $orden->detalles->sum('total');
        $total_iva           = $orden->detalles->sum('valor_iva');
        $total               = $total_con_descuento + $total_iva;
        $subtotal_0          = $orden->detalles->where('iva', 0)->sum('cobrar_paciente');
        $subtotal_12         = $orden->detalles->where('iva', '>', 0)->sum('cobrar_paciente');
        $oda                 = $orden->detalles->sum('cobrar_seguro');
        $descuento           = $orden->detalles->sum('descuento');

        $arr_cabecera = [
            'total'                 => $total,
            'iva'                   => $total_iva,
            'subtotal_0'            => $subtotal_0,
            'subtotal_12'           => $subtotal_12,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'valor_oda'             => $oda,
            'total_sin_tarjeta'     => $total_con_descuento,
            'descuento'             => $descuento,
            'total_cobrar_seguro'   => $oda,
            'total_final'           => $total,
            'valor_contable'        => $total,
            'subtotal'        => $subtotal_0 + $subtotal_12,
        ];

        $orden->update($arr_cabecera);
    }


    public function proformaModal($id_paciente)
    {
        //ProformaController::calcularFechaCaducidad();

        $orden = Proforma_Cabecera::where('id_paciente', $id_paciente)->where('pagado', 0)->where('estado', 1)->get();
      

        return view('comercial/proforma/modal_proforma',  ['orden' => $orden]);
    }

    public function updatePaciente(Request $request)
    {
        $nro_oda = $request->numero_oda;

        $data = [
            'numero_oda'    => $nro_oda,
            'id_nivel'      => $request->id_nivel,
        ];
    }
    public function nivel(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $cont = 0;

        $convenios = DB::table('convenio as c')
            ->where('c.id_seguro', $request->id_seguro)
            ->join('nivel as n', 'n.id', 'c.id_nivel')
            ->select('c.*', 'n.nombre', 'n.id as id_nivel')
            ->get();

        // dd($convenios);
        if (count($convenios) > 0) {
            $selectes = "";
            foreach ($convenios as $value) {
                $cont++;
                $select = "<option value='{$value->id_nivel}'> {$value->nombre} </option>";
                $selectes = $selectes . $select;
            }
            if (count($convenios) > 1) {
                $selectes = "<option value=''>Seleccione...</option> {$selectes}";
            }
            $select = "<select onchange='alertaNivel()'  class='form-control input-sm' id='id_nivel' name='id_nivel'> {$selectes} </select>";
        } else {
            $select = "<option value=''>No tiene Nivel..</option>";
            $select = "<select onchange='alertaNivel()' class='form-control input-sm' id='id_nivel' name='id_nivel'> {$select} </select>";
        }
        return  ['selects' => $select, 'cont' => $cont];
    }

    public function eliminar_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_detalle = $request->id;


        //$detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);
        $detalle  = Proforma_Detalle::find($id_detalle);

        $id_orden = $detalle->id_proforma;

        $detalle->delete();

        $this->recalcular($id_orden);

        return "ok";
    }

    public function updateCabecera(Request $request)
    {

        $numero_oda = $request->numero_oda;
        $id_orden = $request->id_orden;
        $observacion = $request->observacion_cab;
        //dd($request->all());
        $oda = 0;
        if (!is_null($numero_oda)) {
            //dd("aqui");
            if (trim($numero_oda) != "") {
                $oda = 1;
            }
        }

        //ProformaController::calcularFechaCaducidad($id_orden);

        //dd($verificar);

        $proforma = Proforma_Cabecera::find($id_orden);

        $proforma->numero_oda = $numero_oda;
        $proforma->observacion = $observacion;
        $proforma->oda = $oda;
        $proforma->fecha_caducidad = $request->fecha_caducidad;

        $proforma->save();

        

        return "ok";
    }

    public function actualizarPrecio(Request $request)
    {
        $id_nivel = $request->id_nivel;
        $id_orden = $request->id_orden;

        $cab = Proforma_Cabecera::find($id_orden);
        $cab->nivel = $id_nivel;
        $cab->id_seguro = $request->id_seguro;
        $cab->save();


        foreach ($cab->detalles as $det) {
            // $precio = "";
            $producto = Ct_productos::find($det->id_producto);

            $precio   = $producto->valor_total_paq;
            if ($precio == null || $precio == 0) {
                $precio_producto = PrecioProducto::where('codigo_producto', $det->producto->codigo)->orderBy('nivel', 'asc')->get()->first();
                if (!is_null($precio_producto)) {
                    $precio = $precio_producto->precio;
                } else {
                    $precio = 0;
                }
            }




            if ($id_nivel != null or $id_nivel != '') {
                $tarifario = $producto->tarifarios->where('nivel', $id_nivel)->where('estado', 1)->first();
                if (!is_null($tarifario)) {
                    $precio = $tarifario->precio_producto;
                }
            }


            $data =  $det["attributes"];

            $data['precio'] = $precio;
            $data['p_cpac'] = 100 - $data['p_oda'];

            unset($data['updated_at'], $data['cobrar_paciente']);


            $resp = ProformaController::actualizar_producto_nivel($data);
        }
        return "ok";
    }

    public function actualizar_producto_nivel($request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd($request);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_detalle = $request['id'];
        $id_producto = $request['id_producto'];
        $cantidad = $request['cantidad'];
        $precio   = $request['precio'];
        $p_cpac   = $request['p_cpac'];
        //$p_oda    = $request['p_oda'];
        //$cobrar_paciente = $request['cobrar_paciente'];
        $p_dcto    = $request['p_dcto'];
        //$descuento = $request['descuento'];
        $p_oda     = 100 - $p_cpac;

        //

        //$detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);
        $detalle  = Proforma_Detalle::find($id_detalle);
        $producto = Ct_productos::find($id_producto);
        //  dd($detalle);

        $iva = 0;
        if ($producto->iva == 1) {
            $iva = Ct_Configuraciones::find('3');
            if (!is_null($iva)) {
                $iva = $iva->iva;
            } //TERRIBLE EL TEMA DEL IVA
        }

        $subtotal  = $cantidad * $precio;
        $descuento = $subtotal * $p_dcto / 100;
        $descuento = round($descuento, 2);
        //$subtotal  = $subtotal - $descuento;

        $cobrar_paciente = $subtotal * $p_cpac / 100;
        $cobrar_paciente = round($cobrar_paciente, 2);
        $valor_oda       = $subtotal - $cobrar_paciente;
        $subtotal        = $subtotal - $valor_oda;

        $valor_iva = ($subtotal - $descuento) * $iva;
        $valor_iva = round($valor_iva, 2);
        $total     = $subtotal - $descuento + $valor_iva;
        //dd($id_orden);

        $arr_producto = [

            'precio'            => $precio,
            'cantidad'          => $cantidad,
            'total'             => $subtotal - $descuento,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'p_oda'             => $p_oda,
            'valor_oda'         => $valor_oda,
            'p_dcto'            => $p_dcto,
            'descuento'         => $descuento,
            'iva'               => $iva,
            'valor_iva'         => $valor_iva,
            'cobrar_paciente'   => $cobrar_paciente,
            'cobrar_seguro'     => $valor_oda,
        ];

        $detalle->update($arr_producto);

        ProformaController::recalcular($detalle->id_proforma);

        return "ok";
    }

    public function pasarNuevoRecibo(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario  = Auth::user()->id;

        $proforma   = Proforma_Cabecera::find($request->id);
        $agenda  = Agenda::find($request->id_agenda);
        $paciente = $agenda->paciente;
        $cliente = Ct_Clientes::find($proforma->id_paciente);

        $tipo_identificacion    = null;
        $telefono   = null;
        $razon_social           = null;
        $email      = null;
        $ciudad                 = null;
        $direccion              = null;
        if (!is_null($cliente)) {
            $tipo_identificacion    = $cliente->tipo;
            $razon_social           = $cliente->nombre;
            $ciudad                 = $cliente->ciudad_representante;
            $direccion              = $cliente->direccion_representante;
            $telefono               = $cliente->telefono1_representante;
            $email                  = $cliente->email_representante;
        } else {

            $cl_nombre = $paciente->apellido1;
            if ($paciente->apellido2 != '(N/A)' && $paciente->apellido2 != 'N/A' && $paciente->apellido2 != '.') {
                $cl_nombre = $cl_nombre . ' ' . $paciente->apellido2;
            }
            $cl_nombre = $cl_nombre . ' ' . $paciente->nombre1;
            if ($paciente->nombre2 != '(N/A)' && $paciente->nombre2 != 'N/A' && $paciente->nombre2 != '.') {
                $cl_nombre = $cl_nombre . ' ' . $paciente->nombre2;
            }


            ct_clientes::create([
                'identificacion'          => $proforma->id_paciente,
                'nombre'                  => $cl_nombre,
                'tipo'                    => '5',
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $paciente->ciudad,
                'direccion_representante' => $paciente->direccion,
                'telefono1_representante' => $paciente->telefono1,
                'email_representante'     => $paciente->usuario->email,
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
            ]);

            $cliente = Ct_Clientes::find($proforma->id_paciente);

            $tipo_identificacion    = $cliente->tipo;
            $razon_social           = $cliente->nombre;
            $ciudad                 = $cliente->ciudad_representante;
            $direccion              = $cliente->direccion_representante;
            $telefono               = $cliente->telefono1_representante;
            $email                  = $cliente->email_representante;
        }


        $id_empresa = $agenda->id_empresa;
        if ($id_empresa == null) {
            $empresa = Empresa::where('prioridad', '1')->get()->first();
            $id_empresa = $empresa->id;
        }

        $numero_oda = 0;
        if($proforma->numero_oda != null){
            $numero_oda = $proforma->numero_oda;
        }    

        $arr_orden_venta = [
            'id_agenda'     => $agenda->id,
            'id_empresa'    => $id_empresa,
            'fecha_emision' => date('Y-m-d'),
            'nueva_fecha'   => date('Y-m-d H:i:s'),
            'oda'           => $proforma->oda,
            'id_seguro'     => $proforma->id_seguro,
            'id_nivel'      => $proforma->nivel,
            'tipo_identificacion'   => $tipo_identificacion,
            'identificacion'        => $agenda->id_paciente,
            'razon_social'          => $razon_social,
            'ciudad'                => $ciudad,
            'direccion'             => $direccion,
            'telefono'              => $telefono,
            'email'                 => $email,
            'total'                 => $proforma->total,
            'iva'                   => $proforma->iva,
            'subtotal_0'            => $proforma->subtotal_0,
            'subtotal_12'           => $proforma->subtotal_12,
            'id_usuariocrea'        => $id_usuario,
            'id_usuariomod'         => $id_usuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'valor_oda'             => $proforma->valor_oda,
            'total_sin_tarjeta'     => $proforma->total_sin_tarjeta,
            'observacion'           => $proforma->observacion,
            //'caja VARCHAR(255) DEFAULT 'NULL',
            'estado'                => -1,
            'numero_oda'            => $numero_oda,
            'descuento'             => $proforma->descuento,
            'total_cobrar_seguro'   => $proforma->total_cobrar_seguro,
        ];

        $id_orden = Ct_Orden_Venta::insertGetId($arr_orden_venta);

        $detalles = $proforma->detalles;

        foreach ($detalles as $detalle) {

            $arr_producto = [
                'id_orden'          => $id_orden,
                'cod_prod'          => $detalle->cod_prod,
                'descripcion'       => $detalle->descripcion,
                'nombre_producto'   => $detalle->nombre_producto,
                'id_producto'       => $detalle->id_producto,
                'precio'            => $detalle->precio,
                'cantidad'          => $detalle->cantidad,
                'total'             => $detalle->total,
                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'p_oda'             => $detalle->p_oda,
                'valor_oda'         => $detalle->valor_oda,
                'tipo_dcto'         => '%',
                'p_dcto'            => $detalle->p_dcto,
                'descuento'         => $detalle->descuento,
                'iva'               => $detalle->iva,
                'valor_iva'         => $detalle->valor_iva,
                //valor_deducible FLOAT DEFAULT 0,
                //valor_oda_hum FLOAT DEFAULT 0,
                //porc_dedu_segu FLOAT DEFAULT NULL,
                //porc_dedu_paci FLOAT DEFAULT NULL,
                //fee FLOAT DEFAULT NULL,
                'cobrar_paciente' => $detalle->cobrar_paciente,
                'cobrar_seguro'   => $detalle->cobrar_seguro,
            ];

            Ct_Orden_Venta_Detalle::create($arr_producto);
        }

        NuevoReciboCobroController::recalcular($id_orden);

        $proforma->update([
            'id_agenda'         => $agenda->id,
            'id_empresa'        => $id_empresa,
            'id_usuariomod'     => $id_usuario,
            'ip_modificacion'   => $ip_cliente,
            'id_orden'          => $id_orden,
            'pagado'            => 1,
        ]);

        return ['id_orden' => $id_orden];
    }

    public function index_proforma(Request $request)
    {
       // ProformaController::calcularFechaCaducidad();

        $proformas = [];
        $seguro = $request['seguro'];
        $paciente = $request['paciente'];

        $proformas = ProformaController::buscador_proforma($request)->orderBy('id', 'desc')->paginate(20);
        
        //$proforma_cab = Proforma_Cabecera::where('estado', '<>', '0')->orderBy('id', 'desc')->paginate(20);
        $seguros = Seguro::where('inactivo','1')->get();

  
        return view('comercial/proforma/index_proforma', ['proformas' => $proformas, 'seguros'=>$seguros, 'seguro'=> $seguro, 'paciente'=>$paciente]);
    }
    public static function buscador_proforma($request){
       $proformas = DB::table('proforma_cabecera as pro_cab')
       //->join('proforma_detalle as pro_det', 'pro_det.id', 'pro_cab.id_proforma')}
       ->join('seguros as seg','seg.id', 'pro_cab.id_seguro')
       ->join('paciente as pac', 'pac.id', 'pro_cab.id_paciente')
       ->where('pro_cab.estado', '!=', '0')

       ->select('pro_cab.id', 'pro_cab.id_orden' ,'pac.nombre1', 'pac.nombre2', 'pac.apellido1', 'pac.apellido2', 'seg.nombre as nombre_seguro','pro_cab.observacion','pro_cab.id_seguro','pro_cab.estado', 'pro_cab.total', 'pro_cab.pagado' );

       if(count($request->all()) > 0){
            if(!is_null($request->seguro)){
                $proformas = $proformas->where('pro_cab.id_seguro', $request->seguro);
            }
            if(!is_null($request->paciente)){
                $paciente = $request->paciente;
                $proformas = $proformas->where(function ($jq1) use ($paciente) {
                    $jq1->orwhereraw('CONCAT(pac.nombre1," ",pac.nombre2," ",pac.apellido1," ",pac.apellido2) LIKE ?', ['%' . $paciente . '%'])
                    ->orwhereraw('CONCAT(pac.nombre1," ",pac.apellido1," ",pac.apellido2) LIKE ?', ['%' . $paciente . '%']);
                });
            }

        }
        return $proformas;

    }
    
    public function proforma_paciente ($id_paciente){
       $paciente = Paciente::find($id_paciente);
       $proformas_paciente = Proforma_Cabecera::where('estado', '1')->where('id_paciente', $id_paciente)->orderBy('id', 'desc')->paginate(10);  
       return view('comercial/proforma/index_proforma_paciente', ['proformas_paciente' => $proformas_paciente, 'paciente'=>$paciente]);
    }


    public function excel_proforma()
    {

        $titulos = array("FECHA CREACIÓN", "PACIENTE", "EFECTIVO", "T.CREDITO", "7% T/C", "2% T/D", "TRANSF/DEP", "CHEQUE", "PEND FC SEG", "TOTAL VTA", "HONOR. MEDICOS", "TIPO TARJETA", "BANCO", "COMPROBANTE", "OBSERVACION", "ORIGE", "DETALLE");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        $proCabecera = DB::table('proforma_cabecera')
            ->join('proforma_detalle', 'proforma_cabecera.id', 'proforma_detalle.id_proforma')
            ->join('seguros', 'proforma_cabecera.id_seguro', 'seguros.id')
            ->join('agenda', 'proforma_cabecera.id_agenda', 'agenda.id')
            ->join('paciente', 'agenda.id_paciente', 'paciente.id')
            ->join('empresa', 'proforma_cabecera.id_empresa', 'empresa.id')
            ->select('proforma_cabecera.created_at','paciente.nombre1','paciente.apellido1','')
            ->get();

        dd($proCabecera);
        Excel::create('Preformas', function ($excel) use ($titulos, $posicion, $proCabecera) {
            $excel->sheet('Preformas', function ($sheet) use ($titulos, $posicion, $proCabecera) {

                $sheet->mergeCells('A1:R1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('PREFORMAS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 2;
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


                foreach ($proCabecera as $value) {
                    $datos_excel = array();

                    //array_push($datos_excel, $value->);
                }
            });
        })->export('xlsx');
    }
    public function calcularFechaCaducidad(){
        $fecha_actual = date('Y-m-d');
        $verificar = false;
        $id_usuario  = Auth::user()->id;
        //$proforma = Proforma_Cabecera::find($id_orden);
        $proforma = Proforma_Cabecera::where('pagado', 0)->where('estado', 1)->get();

        foreach($proforma as $value){
            if($value->estado == 1){
                /*if($fecha_actual < $value->fecha_caducidad ){
                    $value->estado = 0;
                    $value->id_usuariomod = $id_usuario;
                    $value->save();
                    $verificar = true;
                }*/
            }
        }
        //return $verificar;
    }

    public function proformaLista(Request $request){
       $id_usuario = Auth::user()->id;
     //  dd($request->all());
        DB::beginTransaction();

       try{
            $proforma = Proforma_Cabecera::find($request->id_orden);
            $proforma->estado = 1;
            $proforma->id_usuariomod = $id_usuario;
            $proforma->save();
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado con Exito'];
       }catch(\Exception $e){
            DB::rollback();
            return ['status' => 'error', 'msj' => 'Error al guardar', 'exp'=>$e->getMessage()];
       }
        
    }

    public function pdf_proforma($id )
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $proforma_pdf = Proforma_Cabecera::find($id);
        $ct_for_pag = Proforma_Detalle::where('id_proforma', $id)->get();

        $agenda = $proforma_pdf->agenda;
        
        $vistaurl = "comercial.proforma.pdf_proforma_proc";
        
        $view = \View::make($vistaurl, compact('proforma_pdf', 'ct_for_pag'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante de Proforma-' . $id . '.pdf');
    }

    public function crear_deducible($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $detalle    = Proforma_Detalle::find($id);
        
        $cabecera   = $detalle->cabecera;

        $producto   = Ct_productos::where('nombre', 'like', '%DEDUCIBLE%')->get()->first();

        if(!is_null($producto)){

            $arr_producto = [
                'id_proforma'           => $cabecera->id,
                'cod_prod'              => $producto->codigo,
                'descripcion'           => 'DEDUCIBLE DE '.$detalle->nombre_producto,
                'nombre_producto'       => $producto->nombre,
                'id_producto'           => $producto->id,
                'precio'                => $detalle->valor_deducible,
                'cantidad'              => 1,
                'total'                 => $detalle->valor_deducible,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'p_oda'                 => 0,
                'valor_oda'             => 0,
                'tipo_dcto'             => '%',
                'p_dcto'                => 0,
                'descuento'             => 0,
                'iva'                   => 0,
                'valor_iva'             => 0,
                'cobrar_paciente'       => $detalle->valor_deducible,
                'cobrar_seguro'         => 0,
            ];

            Proforma_Detalle::create($arr_producto);

            $this->recalcular($cabecera->id);

            return "ok";

        }

        return "error";


    }

    public function mostrar_agrupador_proforma(Request $request){

        $id_empresa = $request->session()->get('id_empresa');

        $agrupador = [];

        if(!is_null($request["search"])){
            $agrupador = ProformaAgrupador::where('nombre',"LIKE","%{$request['search']}%")->where('id_empresa', $id_empresa)->select('id', 'nombre as text' )->where('estado',1)->take(20)->get();
        }
        return response()->json($agrupador);
    }

    public function guardar_agrupador(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        $agrupador  = $request->agrupador;
        $id         = $request->id;
        //dd($request->all());

        $orden              = Proforma_Cabecera::find($id);
        $proforma_agrupador = ProformaAgrupador::find($agrupador);
        $detalles           = $proforma_agrupador->detalles;

        $detalles_plantilla = null;
        if($orden->seguro->tipo == 2){
            if($proforma_agrupador->id_proforma != null){
                $proforma_plantilla = Proforma_Cabecera::find($proforma_agrupador->id_proforma);
                if(!is_null($proforma_plantilla)){
                    $detalles_plantilla = $proforma_plantilla->detalles;
                    //dd($detalles_plantilla);
                }
            }
        }    

        foreach ($detalles as $detalle) {
            //////////////////////
            $id_orden    = $id;
            $id_producto = $detalle->id_producto;
            $precio = 0;$cantidad = '1';
            //RUTINA PARA OBTENER EL PRECIO DE LOS PRODUCTOS
            $producto = Ct_productos::find($id_producto);
            if($detalles_plantilla != null){
                $precio   = $detalles_plantilla->where('id_producto',$id_producto)->first()->precio;
                $cantidad = $detalles_plantilla->where('id_producto',$id_producto)->first()->cantidad;
                //dd($precio);
            }

            if($precio == 0){
                $precio   = $producto->valor_total_paq;

                if ($precio == null || $precio == 0) {
                    $precio_producto = PrecioProducto::where('codigo_producto', $producto->codigo)->orderBy('nivel', 'asc')->get()->first();
                    $precio = $precio_producto != null ? $precio_producto->precio : 0;
                }

                if ($orden->nivel != null or $orden->nivel >= 0) {
                    $tarifario = $producto->tarifarios->where('nivel', $orden->nivel)->where('estado', 1)->first();
                    //dd($tarifario);
                    if (!is_null($tarifario)) {
                        $precio = $tarifario->precio_producto;
                    }
                }
            }    

            $iva = 0;
            if ($producto->iva == 1) {
                $iva = Ct_Configuraciones::find('3');
                if (!is_null($iva)) {
                    $iva = $iva->iva;
                } //TERRIBLE EL TEMA DEL IVA
            }

            $valor_iva = $precio * $iva;
            $valor_iva = round($valor_iva, 2);

            $arr_producto = [
                'id_proforma'           => $id_orden,
                'cod_prod'              => $producto->codigo,
                'nombre_producto'       => $producto->nombre,
                //'descripcion'         => $producto->nombre,
                'id_producto'           => $id_producto,
                'precio'                => $precio,
                'cantidad'              => $cantidad,
                'total'                 => $precio,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'p_oda'                 => 0,
                'valor_oda'             => 0,
                'tipo_dcto'             => '%',
                'p_dcto'                => 0,
                'descuento'             => 0,
                'iva'                   => $iva,
                'valor_iva'             => $valor_iva,
                'cobrar_paciente'       => $precio,
                'cobrar_seguro'         => 0,
            ];
            Proforma_Detalle::create($arr_producto);

            
            /////////////////////
        }

        ProformaController::recalcular($id_orden);

        return "ok";

    }

    public function index_plantilla (){
        $pro_agrupadores = ProformaAgrupador::where('estado', '1')->get();
        return view ('comercial/plantilla/index', ['pro_agrupadores' => $pro_agrupadores]);
    } 

    public function index_plantilla_detalle ($id){
        $agrupador = ProformaAgrupador::find($id);
        $agrup_detalles = $agrupador->detalles;
        return view ('comercial/plantilla/index_detalles', ['agrup_detalles' => $agrup_detalles , 'agrupador' => $agrupador]);
    } 

    public function guadar_producto_plantilla(Request $request){
    
     $ip_cliente = $_SERVER["REMOTE_ADDR"];
     $idusuario  = Auth::user()->id;
    
     $id          = $request->id;
     $id_producto = $request->producto_nuevo;
     
     $arr_plantilla_detalle = [
       'id_proforma_agrupador' => $id,
       'id_producto'           => $id_producto,
       'estado'                => 1,

       'id_usuariocrea'       => $idusuario,
       'id_usuariomod'        => $idusuario,
       'ip_creacion'          => $ip_cliente,
       'ip_modificacion'      => $ip_cliente,
     
     ];

     ProformaAgrupadorDetalles::create($arr_plantilla_detalle);

     return "oki";


    }

    public function crear_plantilla(){
               
     return view('comercial/plantilla/crear');
    }

    public function store_plantilla(Request $request){
       
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       
        $id_empresa = $request->session()->get('id_empresa');
              
        $arr_plantilla = [
          'codigo'                => $request['codigo_plantilla'],
          'nombre'                => $request['nombre_plantilla'],
          'id_empresa'            => $id_empresa,
          'estado'                => 1,
   
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,
        
        ];
   
        ProformaAgrupador::create($arr_plantilla);
   
        return redirect(route('proforma.index_plantilla'));
      
    }

     public function editar_plantilla($id){
       
        $agrupador = ProformaAgrupador::find($id);
              
      return view ('comercial/plantilla/editar' , ['agrupador' => $agrupador]);
    } 

    public function update_plantilla(Request $request){

        $agrupador = ProformaAgrupador::find($request['id']);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       
        $id_empresa = $request->session()->get('id_empresa');
              
        $arr_plantilla = [
          'codigo'                => $request['codigo_plantilla'],
          'nombre'                => $request['nombre_plantilla'],
          'id_empresa'            => $id_empresa,
          'estado'                => $request['estado_plantilla'],
   
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,
        
        ];
   
        $agrupador->update($arr_plantilla);
   
        return redirect(route('proforma.index_plantilla'));
      
    }

    public function eliminar_producto_plantilla($id)
    {
       
        $producto = ProformaAgrupadorDetalles::find($id);

        $id_grupo = $producto->id_proforma_agrupador;
        $producto->delete();
    
        return redirect()->route('proforma.index_plantilla_detalle', ['id' => $id_grupo]);
      
       }

        
}
