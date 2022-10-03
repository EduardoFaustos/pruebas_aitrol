<?php

namespace Sis_medico\Http\Controllers\prueba_emily;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Pruebap;


class pruebaController extends Controller
{
  //Hola soy emili
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


    public function indexpro(Request $request){
    $productos = Pruebap::where('estado','1')->get();
    return view('prueba_emily/pruebap/index', ['productos' => $productos]);
    }
   public function crearpro(){
   
    return view('prueba_emily/pruebap/crear');
  }

    public function guardarpro(Request $request)
  {
    //dd($request->all());
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    date_default_timezone_set('America/Guayaquil');

    Pruebap::create([

   
      'codigop'  => $request['codigo'],
      'descripcionp' => $request['descripcion'],
      'nombrep' => $request['nombre'],
      'estado' => 1,
      'ip_creacion' => $ip_cliente,
      'id_usuariocrea' => $idusuario,
      

    ]);

    return redirect(route('indexpro'));
  }
   public function editarpro($id){
    $productos= Pruebap::find($id);
    return view ('prueba_emily.pruebap.editar', ['productos' => $productos]);
   }

    public function update_pro(Request $request)
  {
    //dd($request->all());
    //$id =  ApProcedimiento::where('tipo','M')->get();
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    $idpro = $request['id'];
    $productos = Pruebap::find($idpro);

    $productos->update([

      'codigop'  => $request['codigo'],
      'descripcionp' => $request['descripcion'],
      'nombrep' => $request['nombre'],
      'estado' => 1,
      'ip_modificacion' => $ip_cliente,
      'id_usuariomod' => $idusuario,

    ]);



    return view('prueba_emily.pruebap.index', ['prodcutos' => $productos]);
  }
 
  public function leer_excel(){
      
    Excel: filter ('id')->load('prueba.xlsx')-chunks(250, function($reader) use($contador){

        foreach ($reader as $book){
            $id = $book['id'];
            $valor = $book['valor'];
    }
     
    });
    return view('prueba.xlsx');  
  }
}




   