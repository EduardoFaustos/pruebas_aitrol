<?php
  
namespace Sis_medico\Http\Controllers\pruebaxn;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Agenda_archivo;
use Sis_medico\Empresa;
use Sis_medico\Historiaclinica;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Paciente_Familia;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\Pais;
use Sis_medico\Principio_Activo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Storage;


class PruebasnController extends Controller
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
    
    
    //  public function index(Request $request)
    // {
    //     //dd('nano');
    //     $empresas = Empresa::all();
    //     $doctores = User::where('id_tipo_usuario', '3')->where('training', '0')->where('uso_sistema', '0')->orderby('apellido1')->get();
    //     return view('vistan/index',['empresa' => $empresas,'doctores' => $doctores]);
    // }
       
    public function index(Request $request){
        $paciente = DB::table('paciente')->where('id', '!=', '9999999999')
            ->paginate(15);
        //dd($paciente);

        return view('vistan/index', ['paciente' => $paciente]);
    }

    public function buscar(){

    }

}