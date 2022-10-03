<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\User;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\ApProcedimiento;


class CrudMedicamentosController extends Controller
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


  public function index()
  {

    $medicamentos = ApProcedimiento::where('tipo', 'M')->paginate(20);
    return view('archivo_plano/mantenimientomedicamentos/index', ['medicamentos' => $medicamentos]);
  }

  public function crear()
  {

    $medicamentos = ApProcedimiento::where('tipo', 'M')->get();
    $medic = ApProcedimiento::where('tipo', 'M')->count()+1;
    //dd($medicamentos);
    return view('archivo_plano/mantenimientomedicamentos/crear', ['medicamentos' => $medicamentos,'medic' => $medic]);
  }
  public function guardar(Request $request)
  {
    //dd($request->all());
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    $medic = ApProcedimiento::where('tipo', 'M')->count()+1;
    date_default_timezone_set('America/Guayaquil');

    ApProcedimiento::create([

      'codigo'  => $medic,
      'descripcion' => $request['descripcion'],
      'valor' => $request['valor'],
      'tipo' => 'M',
      'cantidad' => 0,
      'porcentaje_clasificado' => 0,
      'IVA' => 0,
      'porcentaje10' => 0,
      'estado' => 1,
      'ip_creacion' => $ip_cliente,
      'ip_modificacion' => $ip_cliente,
      'id_usuariocrea' => $idusuario,
      'id_usuariomod' => $idusuario,

    ]);

    return redirect(route('index.medicamentos'),['medic' => $medic]);
  }


  public function editar($id)
  {
    $medicamentos = ApProcedimiento::find($id);
    return view('archivo_plano.mantenimientomedicamentos.editar', ['medicamentos' => $medicamentos]);
  }



  public function update_med(Request $request)
  {
    //dd($request->all());
    //$id =  ApProcedimiento::where('tipo','M')->get();
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    $idmed = $request['idmedicamento'];
    $medicamento = ApProcedimiento::find($idmed);

    $medicamento->update([

      'codigo'  => $request['codigo'],
      'descripcion' => $request['descripcion'],
      'valor' => $request['valor'],
      'tipo' => 'M',
      'cantidad' => 0,
      'porcentaje_clasificado' => 0,
      'IVA' => 0,
      'porcentaje10' => 0,
      'estado' => 1,

    ]);

    $medicamentos = ApProcedimiento::where('tipo', 'M')->get();



    return view('archivo_plano.mantenimientomedicamentos.index', ['medicamentos' => $medicamentos]);
  }

  public function buscar (Request $request){
    $medicamento = $request['descripcion'];
    //dd($medicamento);
    $medicamentos = ApProcedimiento::where('descripcion', 'LIKE','%'.$medicamento.'%')->paginate(20);

    //dd($medicamentos);

    // if($medicamento!=null)
    //     {
    //         $medicamentos = $medicamentos->where(function($jq1) use($medicamento){
    //             $jq1->orwhereraw('descripcion LIKE ?', ['%'.$medicamento.'%']);
    //         });
    //     }

        //$medicamentos=$medicamentos->paginate(20);


    return view('archivo_plano.mantenimientomedicamentos.index',['medicamentos' => $medicamentos,'medicamento' => $medicamento]); 


  }
}
