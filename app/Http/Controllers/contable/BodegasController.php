<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Empresa;
use Sis_medico\Hospital;

class BodegasController extends Controller
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

    
    /************************************************
    ********LISTADO DE BODEGAS POR EMPRESA***********
    /************************************************/
    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodegas = Ct_Bodegas::where('estado','=', 1)->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(5);
        $hospital = Hospital::all();


        return view('contable.bodegas.index', ['empresa' => $empresa,'bodegas' => $bodegas,'hospital' => $hospital]); 
    }
    
    /*************************************************
    ********CREAR NUEVA BODEGAS POR EMPRESA***********
    /*************************************************/
    public function crear(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $hospital = hospital::all();
        
        return view('contable.bodegas.create', ['empresa' => $empresa,'hospital' => $hospital]); 
    }

    /*************************************************
    *************GUARDA BODEGAS POR EMPRESA***********
    /*************************************************/    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        
        Ct_Bodegas::create([
            
            'id_hospital'        => $request['id_hospital'],
            'nombre'             => $request['nombre'],
            'color'              => $request['color'],
            'departamento'       => $request['departamento'],
            'id_empresa'         => $id_empresa,
            'estado'             => $request['estado'],
            'encargado'          => $idusuario,
            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
           
        ]); 

        return redirect()->route('bodegas.index');
    }

    /*************************************************
    *************EDITAR BODEGAS POR EMPRESA***********
    /*************************************************/
    public function editar($id,$id_empresa)
    {
        
        $empresa  = Empresa::where('id', $id_empresa)->first();
        
        $hospital = hospital::all();

        $bodegas = Ct_Bodegas::findorfail($id);


        return view('contable.bodegas.edit',['empresa'=> $empresa,'hospital'=> $hospital,'bodegas'=>$bodegas]);
    }

    /*************************************************
    ***********ACTUALIZA BODEGAS POR EMPRESA**********
    /*************************************************/    
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id = $request['id_bodeg'];
        $bodegas = Ct_Bodegas::findOrFail($id);
        $id_empresa = $request->session()->get('id_empresa');
        
        $input = [

            'id_hospital'        => $request['id_hospital'],
            'nombre'             => $request['nombre'],
            'color'              => $request['color'],
            'departamento'       => $request['departamento'],
            'id_empresa'         => $id_empresa,
            'estado'             => $request['estado_bodega'],
            'encargado'          => $idusuario,
            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
            
        ]; 

        $bodegas->update($input); 
        
        return redirect()->route('bodegas.index');
    }

  
    /*************************************************
    ************BUSCAR BODEGAS POR EMPRESA************
    /*************************************************/  
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $constraints = [
            'nombre'       => $request['nombre'],
            'id_hospital'  => $request['id_hospital'],
            'departamento' => $request['departamento'],
            'estado'       => 1,
            'id_empresa'   => $id_empresa,
        ];
        $hospital = hospital::all();
        $bodegas = $this->doSearchingQuery($constraints,$request);
        return view('contable.bodegas.index', ['request' => $request, 'bodegas' => $bodegas, 'searchingVals' => $constraints,'empresa' => $empresa,'hospital' => $hospital]);
    
    }

    private function doSearchingQuery($constraints,Request  $request)
    {
        
        $id_empresa = $request->session()->get('id_empresa');

        $query  = Ct_Bodegas::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }

}
