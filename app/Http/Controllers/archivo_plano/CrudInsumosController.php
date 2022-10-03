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


class CrudInsumosController extends Controller
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


    public function index(){
    $insumos = ApProcedimiento::where('tipo','IV')->paginate(20);
    $codigo = ApProcedimiento:: where('tipo','IV')->get();
    return view('archivo_plano/mantenimientoinsumos/index',['insumos'=> $insumos,'codigo'=>$codigo]);

    }
    
    public function crear(){
    $insumos = ApProcedimiento::where('tipo','IV')->get();
    $insu = ApProcedimiento::where('tipo','IV')->count()+1;
         
    return view('archivo_plano/mantenimientoinsumos/crear', ['insu'=> $insu]);
        
     }
     public function guardar(Request $request)
        {
             //dd($request->all());
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            $insu = ApProcedimiento::where('tipo','IV')->count()+1;
            date_default_timezone_set('America/Guayaquil');
              ApProcedimiento::create([
                 
                'tipo'  => 'IV',
                'codigo'  => $request['codigo'],
                'descripcion' => $request['descripcion'],
                'valor' => $request['valor'],
                'estado' => $request['estado'],
                'ip_creacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                               
              ]);

             return redirect(route('index_insumos'), ['insu'=> $insu]);
        }
        public function editar($id)
        {
          $insumos = ApProcedimiento::find($id);
          return view('archivo_plano.mantenimientoinsumos.editar', ['insumos' => $insumos]);
        }
   
        public function update_ins(Request $request)
            {
              $ip_cliente = $_SERVER["REMOTE_ADDR"];
              $idusuario = Auth::user()->id;
              $idins = $request['idinsumos'];
              $insumo= ApProcedimiento::find($idins);
                   
              $insumo->update([
                       
                'codigo'  => $request['codigo'],
                'descripcion' => $request['descripcion'],
                'estado' => $request['estado'],
                'valor' => $request['valor'],
                'tipo'  => 'IV',
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'cantidad' => 0,
                'porcentaje_clasificado' => 0,
                'IVA' => 0,
                'porcentaje10' => 0,
                               
              ]);
          
              $insumos = ApProcedimiento::where('tipo', 'IV')->get();
                            
              return view('archivo_plano.mantenimientoinsumos.index', ['insumos' => $insumos]);
            }
          
            public function buscar (Request $request){
              $insumos = $request['descripcion'];
              //dd($medicamento);
              $insumos = ApProcedimiento::where('descripcion', 'LIKE','%'.$insumos.'%')->paginate(20);
          
                    
          
              return view('archivo_plano.mantenimientoinsumos.index',['insumos' => $insumos,'insumos' => $insumos]); 
          
          
            }
  
     
}