<?php

namespace Sis_medico\Http\Controllers\prueba_manuel;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Valores_Prueba;


class prueba_manuelController extends Controller
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

public function index_productos(){

		$productos=Valores_Prueba::where('estado','1')->get();
		return view('prueba_manuel/index_productos', ['productos'=>$productos]);
}
public function crear_productos(){
		return view('prueba_manuel/crear_productos');
}
public function guardar_productos(Request $request){
		$ip_cliente = $_SERVER["REMOTE_ADDR"];
		$idusuario = Auth::user()->id;
		date_default_timezone_set('America/Guayaquil');

		Valores_Prueba::create([
		
			'nombre' => $request['nombre'],
			'descripcion' => $request['descripcion'],
			'valor'=> $request['valor'],
			'estado' => 1,
			'ip_creacion' => $ip_cliente,
			'id_usuariocrea' => $idusuario,
			'id_usuariomod'   => $idusuario,
			'ip_modificacion'   =>$ip_cliente,
		
		]);

		return redirect(route('index.manuel'));
}
public function editar_productos($id){
		$productos=Valores_Prueba::find($id);

		return view('prueba_manuel/editar_productos',['productos'=>$productos]);
}
public function actualizar_productos(Request $request)
	{
		//dd($request->all());
		//$id =  ApProcedimiento::where('tipo','M')->get();
		$ip_cliente = $_SERVER["REMOTE_ADDR"];
		$idusuario = Auth::user()->id;

		$id = $request->id_producto;
		$productos = Valores_Prueba::find($id);

		//dd($productos);

		$productos->update([

			'nombre' => $request['nombre'],
			'descripcion' => $request['descripcion'],
			'valor'=> $request['valor'],
			'estado' => 1,
			'ip_modificacion' => $ip_cliente,
			'id_usuariomod' => $idusuario,

		]);

		return redirect(route('index.manuel'));
	}
}