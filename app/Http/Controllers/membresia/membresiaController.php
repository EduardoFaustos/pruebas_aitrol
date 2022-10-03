<?php

namespace Sis_medico\Http\Controllers\membresia;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Response;
use Sis_medico\User;
use Sis_medico\Membresia;
use Sis_medico\Empresa;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class membresiaController extends Controller 
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
       //dd("holis");
       // $mantenimientos_horarios = Ct_Rh_Horario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(10);
      //dd($mantenimientos_horarios);

      $membresia = Membresia::where('estado','!=', '0')->paginate(15);
      $empresas= Empresa::where('estado', '1')->get();
    
        
        return view('membresia/index',['membresia' => $membresia, 'empresas' => $empresas]);
    }
    
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $membresia = Membresia::where('estado','!=', '0')->paginate(15);
        $empresas = Empresa::where('estado', '1')->get();
      
        return view('membresia/create',['membresia' => $membresia, 'empresas' => $empresas]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        

        Membresia::create([

            //'user_id' => $request['user_id'],
            'empresa_id' => $request['empresa_id'],
            'nombre' => $request['nombre'],
            'precio_mensual' => $request['precio_mensual'],
            'precio_anual' => $request['precio_anual'],
            'url'  => $request['url'],
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
        $membresia = Membresia::where('id', $id)->first();
        $empresas = Empresa::where('id', '<>', $id)->where('estado', '1')->get();


        return view('membresia/edit', ['membresia' => $membresia, 'id' => $id,'empresas' => $empresas]);
    }

    public function update(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id; 
        $membresia = Membresia::find( $request['id']) ;
        $membresia->empresa_id    = $request['empresa_id'];
        $membresia->nombre          = $request['nombre'];
        $membresia-> precio_mensual   = $request['precio_mensual'];
        $membresia->  precio_anual    = $request['precio_anual'];
        $membresia->  url          = $request['url'];
        $membresia->estado               = 1;
        $membresia->ip_creacion          = $ip_cliente;
        $membresia->ip_modificacion      = $ip_cliente;
        $membresia->id_usuariocrea       = $idusuario;
        $membresia->id_usuariomod        = $idusuario;
        $membresia->save();

        return json_encode('ok');
    }
    public function delete(Request $request){    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request->id;

        $membresias = Membresia::find($id);
        //dd($tipos_titulo);
        $array_membresia = [
            'empresa_id' => $membresias->empresa_id,
            'nombre' => $membresias->nombre,
            'precio_mensual' => $membresias->precio_mensual,
            'precio_anual' => $membresias->precio_anual,
            'url'  => $membresias->url,
            'estado'     => 0,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ];

        $membresias->update($array_membresia);
        return json_encode('ok');
        //return redirect(route('membresia.index'));
    }

}