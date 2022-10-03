<?php

namespace Sis_medico\Http\Controllers\ieced;

use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Modulo;
use Sis_medico\Opcion;
use Sis_medico\Opcion_Usuario;
use Sis_medico\SubModulo;

class IecedController extends Controller
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

    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('dashboard/dashboard2');
    }
    public function modulo()
    {
        $modulos = Modulo::where('estado', 1)->orderby('orden')->get();

        return view('dashboard/modulos', ['modulos' => $modulos]);
        //return view('dashboard/dashboard2',['modulo'=>$modulo]);
    }
    public function submodulos($id)
    {
        $submodulos = SubModulo::where('id_modulo', $id)->get();

        return view('dashboard/submodulos', ['submodulos' => $submodulos, 'id' => $id]);
        //return view('dashboard/dashboard2',['modulo'=>$modulo]);
    }
    public function opciones($id)
    {
        $opciones = Opcion::where('id_padre', $id)->get();

        return view('dashboard/opciones', ['opciones' => $opciones, 'id' => $id]);

    }
    // Opción-modulo2

    public function modulo2()
    {
        $modulos = Modulo::where('estado', 1)->orderby('orden')->get();

        return view('dashboard/modulos2', ['modulos' => $modulos]);
        //return view('dashboard/dashboard2',['modulo'=>$modulo]);
    }

}
