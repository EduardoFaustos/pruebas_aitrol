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
use Sis_medico\membresia;
use Sis_medico\UserMembresia;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class user_membresiaController extends Controller 
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

      $usermembresia = UserMembresia::where('estado','!=', '0')->paginate(15);
      $membresia = Membresia::where('estado', '1')->get();
      $user = User::where('estado', '1')->get();
    
        
        return view('user_membresia/index',['usermembresia' => $usermembresia, 'membresia' => $membresia, 'user' => $user]);
    }
   
 
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $usermembresia = UserMembresia::where('estado','!=', '0')->paginate(15);
        $membresia = Membresia::where('estado', '1')->get();
        $user = User::where('estado', '1')->get();
    
        
        return view('user_membresia/create',['usermembresia' => $usermembresia, 'membresia' => $membresia, 'user' => $user]);
      
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        

        UserMembresia::create([

            //'user_id' => $request['user_id'],
            'user_id' => $request['iduser'],
            'membresia_id' => $request['membresia_id'],
            'fecha_compra' => $request['fecha_compra'],
            'meses' => $request['meses'],
            'valor_pagado'  => $request['valor_pagado'],
            'meses_contratados'   => $request['meses_contratados'],
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
        $usermembresia = UserMembresia::where('id', $id)->first();
        $membresia = Membresia::where('id', '<>', $id)->where('estado', '1')->get();
        $user = User::where('id', '<>', $id)->where('estado', '1')->get();

        return view('user_membresia/edit', ['usermembresia' => $usermembresia, 'id' => $id, 'membresia' => $membresia, 'user' => $user]);
    }

    public function update(Request $request)
    {
        //dd($request ->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id; 
        $usermembresia = UserMembresia::find( $request['id']) ;
        $usermembresia->user_id    = $request['iduser'];
        $usermembresia->membresia_id          = $request['membresia_id'];
        $usermembresia-> fecha_compra           = $request['fecha_compra'];
        $usermembresia->  meses                 = $request['meses'];
        $usermembresia->  valor_pagado          = $request['valor_pagado'];
        $usermembresia->  meses_contratados     = $request['meses_contratados'];
        $usermembresia->estado               = 1;
        $usermembresia->ip_creacion          = $ip_cliente;
        $usermembresia->ip_modificacion      = $ip_cliente;
        $usermembresia->id_usuariocrea       = $idusuario;
        $usermembresia->id_usuariomod        = $idusuario;
        $usermembresia->save();

        return json_encode('ok');
    }
    public function delete(Request $request){    
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request->id;

        $membresias = UserMembresia::find($id);
        //dd($tipos_titulo);
        $array_membresia = [
            'user_id' => $membresias->user_id,
            'membresia_id' => $membresias->membresia_id,
            'fecha_compra' => $membresias->fecha_compra,
            'meses' => $membresias->meses,
            'valor_pagado'  => $membresias->valor_pagado,
            'meses_contratados'   => $membresias->meses_contratados,
            'estado'     => 0,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ];

        $membresias->update($array_membresia);
        return json_encode('ok');
        //return redirect(route('tituloprofesional.index'));
    }
    public function vh_buscar_usuario(Request $request)
    {

        $nombres = $request['term'];

        $nombres2 = explode(" ", $nombres);
        $cantidad = count($nombres2);

        $usuarios = User::where('estado', '1');

        if ($cantidad == '2' || $cantidad == '3') {

            $usuarios = $usuarios->where(function ($jq1) use ($nombres) {
                $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1) LIKE ?', ['%' . $nombres . '%']);
            });
        } else {

            $usuarios = $usuarios->whereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%' . $nombres . '%']);
        }

        $usuarios = $usuarios->get();

        $data      = array();

        foreach ($usuarios as $usuario) {
            $data[] = array('value' => $usuario->apellido1 . ' ' . $usuario->apellido2 . ' ' . $usuario->nombre1 . ' ' . $usuario->nombre2, 'id' => $usuario->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
}