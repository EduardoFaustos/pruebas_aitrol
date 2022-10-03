<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\AfActivo;
use Sis_medico\AfTipo;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Marca;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Producto;
use Sis_medico\User;
use Sis_medico\Ct_Nomina;
use Sis_medico\AfGrupo;
use Sis_medico\Proveedor;
use Sis_medico\Af_Bodega_Serie_Color;
use Sis_medico\AfSubTipo;
use Excel;
use Sis_medico\AfDepreciacionCabecera;
use Sis_medico\AfActivo_Accesorios;
use DateTime;

class ActivoFijoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $desde   = null;
        $hasta   = null;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $activos    = AfActivo::where('estado', '!=', 0)->where('empresa', $id_empresa)->orderBy('id','DESC')->paginate('20');
        $registros  = array();
        
      
        $tipos = AfTipo::where('estado', '1')->get();

        //dd($activos);
        return view('activosfijos/mantenimientos/activofijo/index', ['activos' => $activos, 'registros' => $registros, 'empresa' => $empresa, 'tipos' => $tipos, 'activo' => '', 'tipo' => '', 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function buscar_activo(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $activo = $request['nombre_activo'];
        $tipo = $request['id_tipo'];
        $desde = $request['desde'];
        $hasta = $request['hasta'];


        $activos = AfActivo::where('estado', '1');
        $tipos = AfTipo::where('estado', '1')->get();

        if (!is_null($tipo)) {
            $activos = $activos->where('tipo_id', $tipo)->where('empresa', $id_empresa);
        }

        if (!is_null($activo)) {
            $activos = $activos->where('nombre', 'like', '%' . $activo . '%');
        }
        if (!is_null($desde)){
            $activos = $activos->where('fecha_compra', '>', $desde . ' 00:00:00');
        }

        if (!is_null($hasta)){
            $activos = $activos->where('fecha_compra', '<', $hasta . ' 00:00:00');
        }

        $activos = $activos->paginate('20');

        return view('activosfijos/mantenimientos/activofijo/index', ['activos' => $activos, 'empresa' => $empresa, 'tipos' => $tipos, 'activo' => $activo, 'tipo' => $tipo, 'desde'=>$desde, 'hasta'=>$hasta]);
    }

    public function search(Request $request)
    {
        //return $request->all();
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');

        $constraints = ['nombre' => $request['nombre'], 'empresa' => $id_empresa];

        $activos = $this->doSearchingQuery($constraints);
        $empresa = Empresa::find($id_empresa);

        return view('activosfijos/mantenimientos/activofijo/index', ['activos' => $activos, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = AfActivo::query()->select('af_activo.*');
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where('af_activo.' . $fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }

    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $activo = AfActivo::find($id);
        $accesorios = AfActivo_Accesorios::where('id_activo', $activo->id)->get();
        $plan         = array();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', '!=', 2)->get();
        // $responsables   = array();
        $productos = Producto::where('estado', '!=', 0)->get();
        $marcas    = Marca::where('estado', '!=', 0)->get();
        $empleados = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $proveedor = Proveedor::where('estado', '1')->get();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $grupos       = AfGrupo::where('estado', '1')->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        //dd($activo);
        // Redirect to user list if updating user wasn't existed
        if ($activo == null || count($activo) == 0) {
            return redirect()->intended('/dashboard');
        }
        return view('activosfijos/mantenimientos/activofijo/edit', ['activo' => $activo, 'plan' => $plan, 'tipos' => $tipos, 'responsables' => $responsables, 'productos' => $productos, 'marcas' => $marcas, 'empleados' => $empleados, 'proveedor' => $proveedor, 'grupos' => $grupos, 'af_colores' => $af_colores, 'af_series' => $af_series, 'sub_tipos' => $sub_tipos, 'accesorios' => $accesorios, 'af_responsables' => $af_responsables]);
    }

    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::find($id_empresa);
        $plan         = Plan_Cuentas::all();
        $tipos        = AfTipo::where('estado', '!=', 0)->get();
        $grupos       = AfGrupo::where('estado', '1')->get();
        $responsables = User::where('estado', '!=', 0)->where('id_tipo_usuario', '!=', 2)->get();
        $productos    = Producto::where('estado', '!=', 0)->get();
        $marcas       = Marca::where('estado', '!=', 0)->get();
        $proveedor    = Proveedor::where('estado', '1')->get();
        $empleados    = Ct_Nomina::where('estado', '1')->where('id_empresa', $empresa->id)->get();
        $af_colores   = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '1')->get();
        $af_series    = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '2')->get();
        $af_responsables = Af_Bodega_Serie_Color::where('estado', '1')->where('tipo', '3')->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();

        /*$codigo = "";
        $codigo = (AfActivo::max('id') + 1);
        $codigo = str_pad($codigo, 2, "0", STR_PAD_LEFT);*/

        return view('activosfijos/mantenimientos/activofijo/create', ['plan' => $plan, 'tipos' => $tipos, 'responsables' => $responsables, 'productos' => $productos, 'marcas' => $marcas, 'empleados' => $empleados, 'grupos' => $grupos, 'proveedor' => $proveedor, 'af_colores' => $af_colores, 'af_series' => $af_series, 'sub_tipos' => $sub_tipos, 'af_responsables' => $af_responsables]);
    }

    private function validateInput($request)
    {
        $this->validate($request, []);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');

        //$activo_id = AfActivo::where('id', $request['id'])->first();

        $id_activo = AfActivo::insertGetId([
            'codigo'             => $request['codigo'] . '-' . $request['codigo_num'],
            'nombre'             => strtoupper($request['nombre']),
            'descripcion'        => strtoupper($request['descripcion']),
            'tipo_id'            => $request['tipo_id'],
            'subtipo_id'         => $request['subtipo_id'],
            'responsable'        => $request['responsable'],
            'org_funcional'      => $request['org_funcional'],
            'acreedor'           => $request['acreedor'],
            'producto'           => $request['producto'],
            'marca'              => $request['marca'],
            'factura'            => $request['factura'],
            'color'              => $request['color'],
            'modelo'             => $request['modelo'],
            'procedencia'        => $request['procedencia'],
            'estado_activo'      => $request['estado_activo'],
            'costo'              => $request['costo'],
            'fecha_compra'       => $request['fecha_compra'],
            'depreciacion_acum'  => $request['depreciacion_acum'],
            'tasa'               => $request['tasa'],
            'fecha_depreciacion' => $request['fecha_depreciacion'],
            'vida_util'          => $request['vida_util'],
            'nota'               => $request['nota'],
            'valor_residual'     => $request['valor_residual'],
            'estado'             => $request['estado'],
            'estado_activo'      => $request['estado_activo'],
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,
            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
            'empresa'            => $empresa->id,
            'serie'              => $request['serie'],
            'tipo_tasa'          => $request['tipo_tasa'],
            'ubicacion'          => $request['ubicacion'],
            'dias_depreciacion'  => $request['dias_depreciacion'],
            'accesorios'         => $request['accesorios'],
            'codigo_text'        => $request['codigo'],
            'codigo_num'         => $request['codigo_num'],
        ]);

        if (count($request->nombre_ac) > 0) {
            foreach ($request->nombre_ac as $item => $v) {
                $arr_accesorios = [
                    'id_activo'         => $id_activo,
                    'nombre'            => $request->nombre_ac[$item],
                    'marca'             => $request->marca_ac[$item],
                    'modelo'            => $request->modelo_ac[$item],
                    'serie'             => $request->serie_ac[$item],
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                AfActivo_Accesorios::insert($arr_accesorios);
            }
        }

        return redirect()->intended('/afactivo/index');
    }

    public function update_activo(Request $request, $id_activo)
    {
        // dd($id);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $activo = AfActivo::where('id', $id_activo)->first();
       // dd($request->all());



        $input  = [
            'codigo'             => $request['codigo'] . '-' . $request['codigo_num'],
            'nombre'             => strtoupper($request['nombre']),
            'descripcion'        => strtoupper($request['descripcion']),
            'tipo_id'            => $request['tipo_id'],
            'subtipo_id'         => $request['subtipo_id'],
            'responsable'        => $request['responsable'],
            'org_funcional'      => $request['org_funcional'],
            'acreedor'           => $request['acreedor'],
            'producto'           => $request['producto'],
            'marca'              => $request['marca'],
            'factura'            => $request['factura'],
            'color'              => $request['color'],
            'modelo'             => $request['modelo'],
            'procedencia'        => $request['procedencia'],
            'estado_activo'      => $request['estado_activo'],
            'costo'              => $request['costo'],
            'fecha_compra'       => $request['fecha_compra'],
            'tasa'               => $request['tasa'],
            'fecha_depreciacion' => $request['fecha_depreciacion'],
            'vida_util'          => $request['vida_util'],
            'nota'               => $request['nota'],
            'estado'             => $request['estado'],
            'id_usuariomod'      => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'empresa'            => $empresa->id,
            'serie'              => $request['serie'],
            'tipo_tasa'          => $request['tipo_tasa'],
            'valor_residual'     => $request['valor_residual'],
            'ubicacion'          => $request['ubicacion'],
            'dias_depreciacion'  => $request['dias_depreciacion'],
            'accesorios'         => $request['accesorios'],
            'codigo_text'        => $request['codigo'],
            'codigo_num'         => $request['codigo_num'],
            'depreciacion_acum'  => $request['depreciacion_acum'],

        ];

        $activo->update($input);

        if ($request->has("nombre_ac")) {
            $variable = AfActivo_Accesorios::where('id_activo', $activo->id);
            //dd($eliminar);
            if (!is_null($variable)) {
                $variable->delete();
            }
            if ('nombre_ac' != $variable) {
                if (count($request->nombre_ac) > 0) {
                    foreach ($request->nombre_ac as $item => $v) {
                        $arr_accesorios = [
                            'id_activo'         => $id_activo,
                            'nombre'            => $request->nombre_ac[$item],
                            'marca'             => $request->marca_ac[$item],
                            'modelo'            => $request->modelo_ac[$item],
                            'serie'             => $request->serie_ac[$item],
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                        ];
                        AfActivo_Accesorios::insert($arr_accesorios);
                    }
                }
            }
        }

        //dd($activo);
        return "ok";
    }

    public function destroy(Request $request, $id)
    {
        // dd($request);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input = [
            'estado'          => 0,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        AfTipo::where('id', $id)->update($input);

        return redirect()->intended('/afTipo');
    }


    public function eliminar_activo($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $activo = AfActivo::find($id);

        $arr_ac = [
            'estado'          => "0",
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        $activo->update($arr_ac);

        return "ok";
    }

    public function guardar_color(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_color = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['color'] . '%')->where('tipo', '1')->first();

        if (is_null($af_color)) {
            $arr_color = [
                'nombre'            => strtoupper($request['color']),
                'tipo'              => 1,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_color);
        }

        return "ok";
    }

    public function guardar_serie(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_serie = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['serie'] . '%')->where('tipo', '2')->first();

        if (is_null($af_serie)) {
            $arr_serie = [
                'nombre'            => $request['serie'],
                'tipo'              => 2,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_serie);
        }

        return "ok";
    }

    public function serie_ac(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       //dd($request['serie_ac'][$id]);
        $af_serie = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['serie_ac'][$id] . '%')->where('tipo', '2')->first();

        if (is_null($af_serie)) {
            $arr_serie = [
                'nombre'            => $request['serie_ac'][$id],
                'tipo'              => 2,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_serie);
        }

        return "ok";
    }

    public function marca_ac(Request $request, $id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       //dd($request['serie_ac'][$id]);
       $af_marca = Marca::where('nombre', 'like', '%' . $request['marca_ac'][$id] . '%')->where('estado', '1')->first();


       if (is_null($af_marca)) {
           $arr_marca = [
               'nombre'            => strtoupper($request['marca_ac'][$id]),
               'descripcion'       => strtoupper($request['marca_ac'][$id]),
               'id_usuariocrea'    => $idusuario,
               'id_usuariomod'     => $idusuario,
               'ip_creacion'       => $ip_cliente,
               'ip_modificacion'   => $ip_cliente,
           ];

           Marca::create($arr_marca);
       }

       return "ok";
    }

    public function guardar_marca(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_marca = Marca::where('nombre', 'like', '%' . $request['marca'] . '%')->where('estado', '1')->first();

        if (is_null($af_marca)) {
            $arr_marca = [
                'nombre'            => strtoupper($request['marca']),
                'descripcion'       => strtoupper($request['marca']),
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Marca::create($arr_marca);
        }

        return "ok";
    }

    public function guardar_responsable(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $af_responsable = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $request['responsable'] . '%')->where('tipo', '3')->first();

        if (is_null($af_responsable)) {
            $arr_resp = [
                'nombre'            => strtoupper($request['responsable']),
                'tipo'              => 3,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            Af_Bodega_Serie_Color::create($arr_resp);
        }

        return "ok";
    }

    public function index_listado(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $tipos = AfTipo::where('estado', '1')->get();
        $desde = date('Y-m-d');
        $hasta = date('Y-m-d');
        $activos_fijos = AfActivo::where('estado', '1')->where('empresa', $empresa->id)->paginate('20');

       /* if (!is_null($tipos)) {
            $activos_fijos = $activos_fijos->where('tipo_id', $tipos);
        }

        if ($desde != null || $hasta != null) {
            $activos_fijos = $activos_fijos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
        }

        $activos_fijos = $activos_fijos->paginate('20');*/

        return view('activosfijos/informes/index_listado', ['activos_fijos' => $activos_fijos,'tipos' => $tipos, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function buscar_listado_activos(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $tipo = $request['id_tipo'];
        $desde = $request['desde'];
        $hasta = $request['hasta'];

        $activos_fijos = AfActivo::where('estado', '1')->where('empresa', $empresa->id);
         $tipos  = AfTipo::where('estado', '1')->get();

        if (!is_null($tipo)) {
            $activos_fijos = $activos_fijos->where('tipo_id', $tipo);
        }

        if (!is_null($desde)){
            $activos_fijos = $activos_fijos->where('fecha_compra', '>', $desde . ' 00:00:00');
        }

        if (!is_null($hasta)){
            $activos_fijos = $activos_fijos->where('fecha_compra', '<', $hasta . ' 00:00:00');
        }

        $activos_fijos = $activos_fijos->paginate('20');

        //dd($activos_fijos);
        return view('activosfijos/informes/index_listado', ['activos_fijos' => $activos_fijos, 'empresa' => $empresa, 'tipos' => $tipos, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function excel_listado_general(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $tipo  = $request['id_tipo'];
        $desde = $request['desde'];
        $hasta = $request['hasta'];


        $titulos = array("FECHA ADQ.", "TIPO ACTIVO", "CATEGORIA", "CODIGO", "NOMBRE", "DESCRIPCION", "FACTURA", "MARCA", "MODELO", "SERIE", "COLOR", "UBICACIÓN", "TASA %", "VIDA UTIL", "V. ORIGEN", "V. SALVA.", "TOT. DEPRE", "VAL. ACTUAL");


        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");

        $activos = AfActivo::where('estado', '1')->where('empresa', $empresa->id);

        if (!is_null($tipo)) {
            $activos = $activos->where('tipo_id', $tipo);
        }

        if ($desde != null || $hasta != null) {
            $activos = $activos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
        }

        $activos = $activos->get();

        Excel::create('Listado Activos', function ($excel) use ($titulos, $posicion, $activos) {
            $excel->sheet('Listado Activos', function ($sheet) use ($titulos, $posicion, $activos) {
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO GENERAL DE ACTIVOS FIJOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 5; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/
                $cont1 = 0;
                $cont2 = 0;
                $cont3 = 0;
                $cont4 = 0;
                foreach ($activos as $activo) {
                    $cont1 += $activo->costo;
                    $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $activo->id)->get();
                    $tot_activo = 0;

                    if (!is_null($depreciacion)) {
                        foreach ($depreciacion as $dep) {
                            $tot_activo += $dep->valordepreciacion;
                        }
                    }

                    $val_actual = $activo->costo - $tot_activo;
                    $cont2 += $tot_activo;
                    $cont3 += $val_actual;
                    $datos_excel = array();

                    array_push($datos_excel, substr($activo->fecha_compra, 0, 10), $activo->tipo->nombre, $activo->sub_tipo->nombre, $activo->codigo, $activo->nombre, $activo->descripcion, $activo->factura, $activo->marca, $activo->modelo, $activo->serie, $activo->color, $activo->ubicacion, $activo->tasa, $activo->vida_util, '$' . number_format($activo->costo, 2, '.', ','), '$0.00', '$' . number_format($tot_activo, 2, '.', ','), '$' . number_format($val_actual, 2, '.', ','));

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }

                    $comienzo++;

                    $sheet->cell('A'  . $comienzo, function ($cell) use ($datos_excel, $i) {
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('O'  . $comienzo, function ($cell) use ($i, $cont1) {
                        $cell->setValue('$' . number_format($cont1, 2, '.', ','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('P'  . $comienzo, function ($cell) use ($i, $cont1) {
                        $cell->setValue('$0.00');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('Q'  . $comienzo, function ($cell) use ($i, $cont2) {
                        $cell->setValue('$' . number_format($cont2, 2, '.', ','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('R'  . $comienzo, function ($cell) use ($i, $cont3) {
                        $cell->setValue('$' . number_format($cont3, 2, '.', ','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                }
            });
        })->export('xlsx');
    }

    public function excel_depreciacion_acumulada(Request $request, $id_activo)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $titulos = array("AÑO", "MES", "FECHA", "MONTO");
        //Posiciones en el excel
        $posicion = array("C", "D", "E", "F");

        $activo = AfActivo::find($id_activo);

        if(isset($activo->ultima_depreciacion()->saldo)){
            $saldo = $activo->ultima_depreciacion()->saldo - $activo->depreciacion_acum;
        }else{
       
            $saldo = $activo->costo - $activo->depreciacion_acum;
        }


        $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $id_activo)->get();
        // dd($depreciacion);

        $tot_activo = 0;
        if (!is_null($depreciacion)) {
            foreach ($depreciacion as $dep) {
                $tot_activo += $dep->valordepreciacion;
            }

            $tot_activo = $tot_activo+$activo->depreciacion_acum;
        }

        Excel::create('Depreciacion', function ($excel) use ($titulos, $posicion, $activo, $depreciacion, $saldo, $tot_activo) {
            $excel->sheet('Listado Activos', function ($sheet) use ($titulos, $posicion, $activo, $depreciacion, $saldo, $tot_activo) {
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEPRECIACION ACUMULADA DE ACTIVOS FIJOS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ACTIVO FIJO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B3:E3');
                $sheet->cell('B3', function ($cell) use ($activo) {
                    // manipulate the cel
                    $cell->setValue($activo->codigo . ' ' . $activo->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO ACTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('G3:H3');
                $sheet->cell('G3', function ($cell) use ($activo) {
                    // manipulate the cel
                    $cell->setValue($activo->tipo->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CATEGORIA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B4', function ($cell) use ($activo) {
                    // manipulate the cel
                    $cell->setValue($activo->sub_tipo->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ADQ.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) use ($activo) {
                    // manipulate the cel
                    $cell->setValue(substr($activo->fecha_compra, 0, 10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('V. ORIGINAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F4', function ($cell) use ($activo) {
                    // manipulate the cel
                    $cell->setValue('$' . number_format($activo->costo,2,'.',','));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('V. SALVAMENTO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) use ($activo) {
                    // manipulate the cel
                    $cell->setValue('$0.0000');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEPRECIACION ACUMULADA');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B5', function ($cell) use ($tot_activo) {
                    // manipulate the cel
                    $cell->setValue('$'.$tot_activo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO ACTUAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D5', function ($cell) use ($saldo) {
                    // manipulate the cel
                    $cell->setValue(round($saldo,2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 8; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $tot_depreciado = 0;

                foreach ($depreciacion as $dep) {

                    $tot_depreciado += $dep->valordepreciacion;

                    $datos_excel = array();
                    array_push($datos_excel, substr($dep->fecha, 0, 4), date("m", strtotime($dep->fecha)), substr($dep->fecha, 0, 10), '$' . number_format($dep->valordepreciacion,2,'.',','));

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }

                $sheet->mergeCells('C' . $comienzo . ':E' . $comienzo);
                $sheet->cell('C' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL DEPRECIADO POR ACTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F' . $comienzo, function ($cell) use ($tot_depreciado) {
                    // manipulate the cel
                    $cell->setValue('$' . number_format($tot_depreciado,2,'.',','));

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }

    public function crear_activo($archivo)
    {
        $idusuario       = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];

        Excel::filter('chunk')->load($archivo . '.xlsx')->chunk(600, function ($reader) use ($idusuario, $ip_cliente) {
            // dd($reader);
            foreach ($reader as $book) {
                if (!is_null($book)) {

                    $af_color = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $book->color . '%')->where('tipo', '1')->first();

                    if (is_null($af_color)) {
                        $arr_color = [
                            'nombre'            => $book->color,
                            'tipo'              => 1,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                        ];

                        Af_Bodega_Serie_Color::create($arr_color);
                    }

                    $af_serie = Af_Bodega_Serie_Color::where('nombre', 'like', '%' . $book->serie . '%')->where('tipo', '2')->first();

                    if (is_null($af_serie)) {
                        $arr_serie = [
                            'nombre'            => $book->serie,
                            'tipo'              => 2,
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                        ];

                        Af_Bodega_Serie_Color::create($arr_serie);
                    }

                    $af_marca = Marca::where('nombre', 'like', '%' . $book->marca . '%')->where('estado', '1')->first();

                    if (is_null($af_marca)) {
                        $arr_marca = [
                            'nombre'            => strtoupper($book->marca),
                            'descripcion'       => strtoupper($book->marca),
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                        ];

                        Marca::create($arr_marca);
                    }

                    $arr_activo = [
                        'codigo'             => $book->codigo,
                        'nombre'             => strtoupper($book->nombre),
                        'descripcion'        => strtoupper($book->descripcion),
                        'tipo_id'            => $book->tipo,
                        'subtipo_id'         => $book->categoria,
                        'responsable'        => $book->responsable,
                        'marca'              => strtoupper($book->marca),
                        'color'              => strtoupper($book->color),
                        'modelo'             => $book->modelo,
                        'procedencia'        => $book->procedencia,
                        'estado_activo'      => $book->estado_activo,
                        'costo'              => $book->costo,
                        'fecha_compra'       => $book->fecha_compra,
                        'estado'             => $book->estado,
                        'ubicacion'          => $book->ubicacion,
                        'id_usuariocrea'     => $idusuario,
                        'id_usuariomod'      => $idusuario,
                        'ip_creacion'        => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                        'empresa'            => $book->empresa,
                        'serie'              => $book->serie,
                    ];

                    AfActivo::create($arr_activo);
                }
            }
        });
        return "ok";
    }

    public function index_listado_tipo(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde = $request['desde'];
        $hasta = $request['hasta'];

        $titulos = array("FECHA ADQ.", "TIPO ACTIVO", "CATEGORIA", "CODIGO", "NOMBRE", "DESCRIPCION", "FACTURA", "MARCA", "MODELO", "SERIE", "COLOR", "UBICACIÓN", "TASA %", "VIDA UTIL", "V. ORIGEN", "V. SALVA.", "TOT. DEPRE", "VAL. ACTUAL");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

        $tipos = AfTipo::where('estado', '1')->get();

        Excel::create('LISTADO GENERAL DE ACTIVOS FIJOS', function ($excel) use ($titulos, $posicion, $tipos, $empresa, $desde, $hasta) {
            $excel->sheet('LISTADO GENERAL', function ($sheet) use ($titulos, $posicion, $tipos, $empresa, $desde, $hasta) {
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO GENERAL DE ACTIVOS FIJOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 5; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo = $comienzo + 2;
                /*****FIN DE TITULOS DEL EXCEL***********/


                foreach ($tipos as $tipo) {

                    $sheet->mergeCells('A' . $comienzo . ':R' . $comienzo);
                    $sheet->cell('A' . $comienzo, function ($cell) use ($tipo) {
                        // manipulate the cel
                        $cell->setValue($tipo->nombre);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo++;

                    $activos = AfActivo::where('estado', '1')->where('empresa', $empresa->id)->where('tipo_id', $tipo->id);

                    if ($desde != null || $hasta != null) {
                        $activos = $activos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
                    }

                    $activos = $activos->get();

                    $cont1 = 0;
                    $cont2 = 0;
                    $cont3 = 0;
                    $cont4 = 0;
                    foreach ($activos as $ac) {
                        $cont1 += $ac->costo;
                        $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $ac->id)->get();
                        $tot_activo = 0;

                        if (!is_null($depreciacion)) {
                            foreach ($depreciacion as $dep) {
                                $tot_activo += $dep->valordepreciacion;
                            }
                        }

                        $val_actual  = $ac->costo - $tot_activo;
                        $cont2      += $tot_activo;
                        $cont3      += $val_actual;
                        $datos_excel = array();

                        array_push($datos_excel, substr($ac->fecha_compra, 0, 10), $ac->tipo->nombre, $ac->sub_tipo->nombre, $ac->codigo, $ac->nombre, $ac->descripcion, $ac->factura, $ac->marca, $ac->modelo, $ac->serie, $ac->color, $ac->ubicacion, $ac->tasa, $ac->vida_util, '$' . number_format($ac->costo, 2, '.', ','), '$0.00', '$' . number_format($tot_activo, 2, '.', ','), '$' . number_format($val_actual, 2, '.', ','));

                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }

                        $comienzo++;
                        $sheet->cell('A'  . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue('Total');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('O'  . $comienzo, function ($cell) use ($i, $cont1) {
                            $cell->setValue('$' . number_format($cont1, 2, '.', ','));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('P'  . $comienzo, function ($cell) use ($i, $cont1) {
                            $cell->setValue('$0.00');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('Q'  . $comienzo, function ($cell) use ($i, $cont2) {
                            $cell->setValue('$' . number_format($cont2, 2, '.', ','));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('R'  . $comienzo, function ($cell) use ($i, $cont3) {
                            $cell->setValue('$' . number_format($cont3, 2, '.', ','));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }

                    $comienzo = $comienzo + 3;
                }
            });
        })->export('xlsx');
    }

    public function pdf_listado_general(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $fecha = date('Y-m-d');
        $tipo  = $request['id_tipo'];
        $desde = $request['desde'];
        $hasta = $request['hasta'];

        $activos = AfActivo::where('estado', '1')->where('empresa', $empresa->id);

        if (!is_null($tipo)) {
            $activos = $activos->where('tipo_id', $tipo);
        }

        if ($desde != null || $hasta != null) {
            $activos = $activos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
        }

        $activos = $activos->get();

        $view = \View::make('activosfijos.informes.pdf_listado_general', compact('fecha', 'activos', 'empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Listado_General_Activos.pdf');
    }

    public function pdf_listado_tipo(Request $request)
    {

        $fecha = date('Y-m-d');
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $tipos = AfTipo::where('estado', '1')->get();

        $view = \View::make('activosfijos.informes.pdf_listado_tipo', compact('fecha', 'tipos', 'empresa', 'desde', 'hasta'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Listado_Activos_Tipo.pdf');
    }

    public function pdf_depreciacion(Request $request, $id_activo)
    {

        $fecha = date('Y-m-d');
        $activo = AfActivo::find($id_activo);

        $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $id_activo)->get();

        $view = \View::make('activosfijos.informes.pdf_depreciacion', compact('fecha', 'activo', 'depreciacion'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Depreciacion_acumulada.pdf');
    }

    public function excel_depreciacion(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $tipo  = $request['tipo'];

        // revisar los dias 

        $titulos = array("FECHA INSC REG PROP", "CODIGO", "NOMBRE", "DESCRIPCION", "MARCA", "MODELO", "SERIE", "COLOR", "COSTO", "20%", "DEP DIARIO", "DIAS DE DEPRECIACION","GTO. DEP. MENSUAL", "DEPRECIACION", "SALDO ACTUAL");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T","U","V","W");

        $activos = AfActivo::where('estado', '1')->where('empresa', $empresa->id);

        if (!is_null($tipo)) {
            $activos = $activos->where('tipo_id', $tipo);
        }

        if ($desde != null || $hasta != null) {
            $activos = $activos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
        }

        $activos = $activos->get();

        Excel::create('Gastos Depreciacion Mensual', function ($excel) use ($titulos, $posicion, $activos, $request) {
            $excel->sheet('Gastos Depreciacion', function ($sheet) use ($titulos, $posicion, $activos, $request) {
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GASTOS DEPRECIACION MENSUAL');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 5; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

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
                $cont1 = 0;
                $cont2 = 0;
                $cont3 = 0;
                $cont4 = 0;
               // $cont5 = 0;
                $cont6=  0;
              
                $fhasta = new DateTime($request->hasta);

                foreach ($activos as $activo) {
                    $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $activo->id)->get();
                    $tot_activo = 0;
                    if (!is_null($depreciacion)) {
                        foreach ($depreciacion as $dep) {
                            $tot_activo += $dep->valordepreciacion;
                        }

                        $tot_activo = $tot_activo+$activo->depreciacion_acum;
                    }

                    if(isset($activo->ultima_depreciacion()->saldo)){
                        $saldo = $activo->ultima_depreciacion()->saldo - $activo->depreciacion_acum;
                    }else{
                   
                        $saldo = $activo->costo - $activo->depreciacion_acum;
                    }


                 
                    $cont1 += round($activo->costo, 2);
                    $cont2 += round(($activo->costo * 0.2), 2);
                    $cont3 += round((($activo->costo * 0.2) / 360),2);
                   // $cont5 +=30;
                    $cont6 += $tot_activo;
            
                    $fecha_compra = new DateTime($activo->fecha_compra);

                    $diff = $fecha_compra->diff($fhasta);

                    $days= $diff->days;

                    if($days >= 30){
                    $days = 30;
                    }
                    $cont4 += round(((($activo->costo *($activo->tasa/100)) / 360) * $days),2);
                   
                    //dd($days);
                    $datos_excel = array();
                    array_push($datos_excel, substr($activo->fecha_compra, 0, 10), $activo->codigo, $activo->nombre, $activo->descripcion, $activo->marca, $activo->modelo, $activo->serie, $activo->color, '$' . number_format($activo->costo, 2, '.', ','), round(($activo->costo * 0.2), 2), round((($activo->costo * 0.2) / 360),2), $days, round(((($activo->costo *($activo->tasa/100)) / 360) * $days),2), '$' . number_format($tot_activo, 2, '.', ',') , number_format($saldo,2,'.',','));

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;


                    $sheet->cell('A'. $comienzo, function ($cell) use ($datos_excel, $i) {
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('I'. $comienzo, function ($cell) use ($cont1) {
                        $cell->setValue(number_format($cont1,2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('J'. $comienzo, function ($cell) use ($cont2) {
                        $cell->setValue(number_format($cont2,2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('K'. $comienzo, function ($cell) use ($cont3) {
                        $cell->setValue(number_format($cont3,2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('L'. $comienzo, function ($cell)  {
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('M'. $comienzo, function ($cell) use ($cont4) {
                        $cell->setValue(number_format($cont4, 2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('N'. $comienzo, function ($cell) use ($cont6) {
                        $cell->setValue(number_format($cont6 , 2,'.',','));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    
                }
            });
        })->export('xlsx');
    }
    public function pdf_activo(Request $request, $id_activo)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $fecha = date('Y-m-d');
        $activo = AfActivo::find($id_activo);
        $tipos = AfTipo::where('estado', '1')->get();
        $sub_tipos    = AfSubTipo::where('estado', '1')->get();
        $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $id_activo)->get();

        $view = \View::make('activosfijos.mantenimientos.activofijo.pdf_activo', compact('fecha', 'activo', 'depreciacion', 'sub_tipos', 'tipos', 'empresa'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Depreciacion_activo_fijo.pdf');
    }

    
    
}
