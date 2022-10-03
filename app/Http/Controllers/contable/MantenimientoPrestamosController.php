<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Rh_Prestamos_Detalle;



class MantenimientoPrestamosController extends Controller
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


	public function index_mantenimiento(){

		$prestamos=Ct_Rh_Prestamos_Detalle::where('estado','1')->get();
		//dd($prestamos);
		return view ('contable/mantenimiento_prestamos/index_prestamos',['prestamos'=>$prestamos]);
	}	
	public function crear_mantenimiento(){
		return view ('contable/mantenimiento_prestamos/crear_prestamos');
	}
	public function guardar_mantenimiento(){
		$ip_cliente = $_SERVER["REMOTE_ADDR"];
		$idusuario = Auth::user()->id;
		date_default_timezone_set('America/Guayaquil');

		Ct_Rh_Prestamos_Detalle::create([
		
			'id_ct_rh_prestamos' => $request['prestamos'],
			'anio' => $request['anio'],
			'mes' => $request['mes'],
			'fecha' => $request['fecha'],
			'cuota' => $request['cuota'],
			'valor_cuota' => $request['valor_cuota'],
			'id_ct_rol_pagos' => $request['id_ct_rol_pagos'],
			'estado' => $request['estado'],
			'estado_pago' => $request['estado_pago'],
			'estado' => 1,
			'fecha_pago' => $fecha,
			'id_usuariocrea' => $idusuario,
			'id_usuariomod'   => $idusuario,
			'ip_creacion' => $ip_cliente,
			'ip_modificacion'   =>$ip_cliente,
			'created_at' => $ip_modificacion,
			'updated_at' => $ip_modificacion,
		]);

		return redirect(route('mantenimientoprestamos.index'));
	}
}