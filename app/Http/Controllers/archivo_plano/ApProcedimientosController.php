<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\ApProcedimiento;
use Sis_medico\ApProcedimientoNivel;
use Sis_medico\ApPlantilla;
use Sis_medico\Ap_Tipo_Examen;

class ApProcedimientosController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1,11,22)) == false) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {
        $tipo= Ap_Tipo_Examen::where('estado','1')->get();
        return view('archivo_plano/procedimientos/frmcrear',['tipo' =>$tipo]);
    }

    public function index(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        } 
        
        //$procedimiento = $request['descripcion'];
        $procedimientos = ApProcedimiento::where('estado','1')->paginate(20);
        //dd($procedimientos);

        /*$procedimientos = ApProcedimiento::select('tipo', 'codigo', 'descripcion', 'valor', 'iva', 'id')
            ->where('descripcion', 'LIKE', '%' . $procedimiento . '%')
            ->paginate(20);*/ 

        //$procedimientos->appends(['search' => $procedimiento]);
        return view('archivo_plano/procedimientos/index', ['procedimientos' =>$procedimientos,'procedimiento' => '']);
    }

    public function buscar_procedimiento(Request $request)
    {
        $procedimiento = $request['descripcion'];

        $procedimientos = ApProcedimiento::where('estado','1');

        //dd($procedimientos);
        if($procedimiento!=null)
        {
            $procedimientos = $procedimientos->where(function($jq1) use($procedimiento){
                $jq1->orwhereraw('descripcion LIKE ?', ['%'.$procedimiento.'%']);
            });
        }

        $procedimientos=$procedimientos->paginate(20);

        return view('archivo_plano/procedimientos/index',['procedimiento' =>$procedimiento,'procedimientos' =>$procedimientos]);
    }

    public function lista(Request $request)
    {
        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();
        $procedimiento = $request->input('descripcion');

        $procedimientos = "SELECT b.tipo, b.codigo, b.descripcion, a.cantidad, b.valor, b.iva from ap_plantilla_items a, ap_procedimiento b
        where cod_plantilla='$procedimiento' and a.id_procedimiento=b.codigo order by b.descripcion ASC";
        $procedimientos = DB::select($procedimientos);
        //dd($procedimientos);

        return view('archivo_plano/procedimientos/lista', compact('procedimientos', 'lista'));
    }


    public function editar(Request $request, $id)
    {

        $editar_procedimiento = ApProcedimiento::find($id);


        return view('archivo_plano/procedimientos/editar', ['editar_procedimiento' => $editar_procedimiento]);
    }

    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request['id_procedimiento'];
        //dd($id);
        $tipo_procedimiento = ApProcedimiento::findOrFail($id);

        $input = [

            'tipo'                  => $request['txttipo'],
            'codigo'                => $request['txtcodigo'],
            'descripcion'           => $request['txtdescripcion'],
            'cantidad'              => $request['txtcantidad'],
            'valor'                 => $request['txtvalor'],
            'iva'                   => $request['txtiva'],
            'estado'                => $request['txtestado'],
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,

        ];

        //dd($input);
        $tipo_procedimiento->update($input);

        return redirect('administracion/procedimientos');
    }




    public function store(Request $request)
    {
        /*
        $pro = new ApProcedimiento();
        $pro->tipo = $request->txttipo;
        $pro->codigo = $request->txtcodigo;
        $pro->descripcion = $request->txtdescripcion;
        $pro->cantidad = $request->txtcantidad;
        $pro->valor = $request->txtvalor;
        $pro->iva = $request->txtiva;
        $pro->estado = $request->txtestado;
        $pro->id_usuariocrea = $idusuario;
        $pro->id_usuariomod = $idusuario;
        $pro->ip_creacion = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();
        */
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $arr=[
            'tipo' =>$request['txttipo'],
            'codigo' =>$request['txtcodigo'],
            'descripcion' =>$request['txtdescripcion'],
            'cantidad' =>$request['txtcantidad'],
            'valor' =>$request['txtvalor'],
            'iva' =>$request['txtiva'],
            'estado' =>$request['txtestado'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        ApProcedimiento::create($arr);
        return redirect('administracion/procedimientos');
    }




    //create nivel
    public function store2(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $arr=[
            'id_procedimiento'=>$request['id_procedimiento'],
            'codigo' =>$request['codigo'],
            'cod_conv' =>$request['txtnivel'],
            'desc_conv' =>$request['txtdes'],
            'anexo' =>$request['txtanexo'],
            'uvr1' =>$request['uvr1'],
            'uvr2' =>$request['uvr2'],
            'uvr3' =>$request['uvr3'],
            'prc1' =>$request['prc1'],
            'prc2' =>$request['prc2'],
            'prc3' =>$request['prc3'],
            'uvr1a' =>$request['uvr1a'],
            'uvr2a' =>$request['uvr2a'],
            'uvr3a' =>$request['uvr3a'],
            'prc1a' =>$request['prc1a'],
            'prc2a' =>$request['prc2a'],
            'prc3a' =>$request['prc3a'],
            'estado'=>$request['txtestado'],
            'separado'=>$request['txtseparado'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        //dd($request->all);
        ApProcedimientoNivel::create($arr);
        return redirect('administracion/procedimientos');
    }



    function index4()
    {
        return view('archivo_plano/procedimientos/frmasignonivel');
    }

    function fetch(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = ApProcedimiento::where('descripcion', 'LIKE', "%{$query}%")->get();
            $output = '<ul id="cierrate" class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= '
        <li><a href="#"> Codigo: ' . $row->codigo . '    |    Descripción: ' . $row->descripcion . '</a></li>
        <input type="hidden" name="id_procedimiento" value="' . $row->codigo . '"> <input type="hidden" name="id" value="' . $row->id . '">
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }
    
    function cambio_estado()
    {
        $procedimientos = ApProcedimiento::where('estado','0')->get();
        foreach ($procedimientos as $value) {
            $arr=[
            'estado' => '1',
            'ip_modificacion' => 'cambio estado',
            ];

            $value->update($arr);
        }      

        return "ok";
    }

    public function item_nivel($id, $nivel){

        $procedimiento = ApProcedimiento::find($id);

        return view('archivo_plano/procedimientos/item_nivel',['procedimiento' =>$procedimiento, 'id' =>$id, 'nivel' =>$nivel]);
    }

    public function nivel($id){

        $procedimiento = ApProcedimiento::find($id);

        return view('archivo_plano/procedimientos/nivel',['procedimiento' =>$procedimiento, 'id' =>$id]);
    }

     public function actualiza_nivel($id, $nivel){

        $procedimiento = ApProcedimiento::where('ap_procedimiento.id',$id)->join('ap_procedimiento_nivel as apn','apn.id_procedimiento','ap_procedimiento.id')->where('apn.cod_conv',$nivel)->select('ap_procedimiento.descripcion','ap_procedimiento.codigo','ap_procedimiento.id as idproced','apn.*')->first();

        //dd($procedimiento);

        return view('archivo_plano/procedimientos/actualiza_nivel',['procedimiento' =>$procedimiento, 'id' =>$id, 'nivel' =>$nivel]);
    }

    public function update_nivel(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_procedimiento =$request['id_procedimiento'];
        $nivel =$request['txtnivel'];

        $proced_nivel =ApProcedimientoNivel::where('id_procedimiento',$id_procedimiento)->where('cod_conv',$nivel)->first();

        $arr=[
            'id_procedimiento'=>$id_procedimiento,
            'codigo' =>$request['codigo'],
            'cod_conv' =>$nivel,
            'desc_conv' =>$request['txtdes'],
            'anexo' =>$request['txtanexo'],
            'uvr1' =>$request['uvr1'],
            'uvr2' =>$request['uvr2'],
            'uvr3' =>$request['uvr3'],
            'prc1' =>$request['prc1'],
            'prc2' =>$request['prc2'],
            'prc3' =>$request['prc3'],
            'uvr1a' =>$request['uvr1a'],
            'uvr2a' =>$request['uvr2a'],
            'uvr3a' =>$request['uvr3a'],
            'prc1a' =>$request['prc1a'],
            'prc2a' =>$request['prc2a'],
            'prc3a' =>$request['prc3a'],
            'estado'=>$request['txtestado'],
            'separado'=>$request['txtseparado'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        //dd($request->all);
        $proced_nivel->update($arr);
        
        return redirect('administracion/procedimientos');
    }
}
