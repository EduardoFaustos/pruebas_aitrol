<?php

namespace Sis_medico\Http\Controllers\plantilla_labs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Examen_Derivado;

class MantenimientoExamDerivadosController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }


    public function index(){
       
        if ($this->rol()) {
            return response()->view('errors.404');
        }
     
       $examenes_derivados = Examen_Derivado::where('estado', '1')->get();

    return view('plantillas_labs/mantenimiento_derivados/index', ['examenes_derivados' => $examenes_derivados]);
    }

    

}