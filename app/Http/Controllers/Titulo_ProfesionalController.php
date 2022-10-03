<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Response;
use Sis_medico\User;
use Sis_medico\Titulo_Profesional;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class Titulo_ProfesionalController extends Controller 
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

      $titulos = Titulo_Profesional::where('estado','!=', '0')->paginate(15);
    
        
        return view('titulo_profesional/index',['titulos' => $titulos]);
    }
    
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }

      
        return view('titulo_profesional/create');
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        Titulo_Profesional::create([

            'titulo_universitario' => $request['titulo_universitario'],
            'titulo_prefijo' => $request['titulo_prefijo'],
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
        $titulos = Titulo_Profesional::where('id', $id)->first();

        return view('titulo_profesional/edit', ['titulos' => $titulos, 'id' => $id]);
    }

    public function update(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $titulos = Titulo_Profesional::find( $request['id']) ;
        $titulos->titulo_universitario          = $request['titulo_universitario'];
        $titulos->titulo_prefijo          = $request['titulo_prefijo'];
        $titulos->estado               = 1;
        $titulos->ip_creacion          = $ip_cliente;
        $titulos->ip_modificacion      = $ip_cliente;
        $titulos->id_usuariocrea       = $idusuario;
        $titulos->id_usuariomod        = $idusuario;
        $titulos->save();

        return json_encode('ok');
    }
    public function delete(Request $request){    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request->id;

        $tipos_titulo = Titulo_Profesional::find($id);
        //dd($tipos_titulo);
        $array_titulo = [
            'titulo_universitario' => $tipos_titulo->titulo_universitario,
            'titulo_prefijo' => $tipos_titulo->titulo_prefijo,
            'estado'     => 0,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ];

        $tipos_titulo->update($array_titulo);
        return json_encode('ok');
        //return redirect(route('tituloprofesional.index'));
    }

}