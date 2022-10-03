<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Response;
use Sis_medico\Ct_Denominacion;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class Ct_DenominacionController extends Controller 
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
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }
    
    


    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
    

      $denominacion = Ct_Denominacion::where('estado','!=', '0')->paginate(15);
    
        
        return view('contable/arqueo_caja/index',['denominacion' => $denominacion]);
    }
    
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $denominacion = Ct_Denominacion::where('estado','!=', '0')->paginate(15);
      
        return view('contable/arqueo_caja/create',['denominacion' => $denominacion]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        

        Ct_Denominacion::create([
            'nombre' => $request['nombre'],
            'valor' => $request['valor'],
            'estado'     => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        return json_encode("ok");
    }
    public function edit($id)
    {
        $denominacion = Ct_Denominacion::where('id', $id)->first();


        return view('contable/arqueo_caja/edit', ['denominacion' => $denominacion, 'id' => $id]);
    }

    public function update(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id; 
        $denominacion= Ct_Denominacion::find( $request['id']) ;
        $denominacion->nombre          = $request['nombre'];
        $denominacion->valor          = $request['valor'];
        $denominacion->estado               = 1;
        $denominacion->ip_creacion          = $ip_cliente;
        $denominacion->ip_modificacion      = $ip_cliente;
        $denominacion->id_usuariocrea       = $idusuario;
        $denominacion->id_usuariomod        = $idusuario;
        $denominacion->save();

        return json_encode('ok');
    }
    public function delete(Request $request){    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request->id;

        $denominaciones = Ct_Denominacion::find($id);
        $array_denominacion= [
            'nombre' => $denominaciones->nombre, 
            'valor' => $denominaciones->valor,
            'estado' => 0,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ];

        $denominaciones->update($array_denominacion);
        return json_encode('ok');
    }

}