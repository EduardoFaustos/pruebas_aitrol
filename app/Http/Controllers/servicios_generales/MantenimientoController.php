<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use IlluminateFilesystem\FilesystemManager;
use Response;
use Sis_medico\Examen;
use Excel;


class MantenimientoController extends Controller
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
        if (in_array($rolUsuario, array(1, 24, 4)) == false) {
            return true;
        }
    }


    public function index()
    {
        $examenes = Examen::paginate(10);
        return view('servicios_generales.examenes_mantenimiento.index', ['examenes' => $examenes]);
    }

    public function actualizar(Request $request)
    {
        $examenes = Examen::find($request['id']);
        return view('servicios_generales.examenes_mantenimiento.update', ['examenes' => $examenes]);
    }
    public function update(Request $request)
    {
        $examenes = Examen::find($request['id']);
        
        try {
            $examenes->cantidad_tubos = $request['cantidad'];
            $examenes->indice_tubos  = $request['indice'];
            $examenes->save();

            return json_encode('ok');
        } catch (\Throwable $th) {
            //dd($th);
            return json_encode('error');
        }
    }

    public function buscador(Request $request)
    {

        $examenes = Examen::where('nombre','like','%'.$request['nombre'].'%')->paginate(10);
        return view('servicios_generales.examenes_mantenimiento.index', ['examenes' => $examenes]);
    }

    public function excel_tubos (){

        Excel::filter('chunk')->load('public/exceltubos.xlsx')->chunk(250, function ($results) {

            foreach($results as $row)
            {
                $examen = Examen::where('id',intval($row['id']))->first();
               
            }
        });
    }

    public function login_css(){
        return view('css_login.index');
    }
}
