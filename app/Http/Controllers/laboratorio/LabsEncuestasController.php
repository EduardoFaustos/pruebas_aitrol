<?php

<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Area;
use Sis_medico\Pregunta_Labs;
use Sis_medico\GrupoPreguntas_Labs;
use Sis_medico\TipoSugerencia;
use Sis_medico\Sugerencia;


class Labs_EncuestasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1)) == false){
          return true;
        }
    }

    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $pregunta = Pregunta_Labs::paginate(25);
 

        return view('laboratorio/pregunta/index', ['preguntas' => $pregunta]);
    }

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $grupopreguntalabs = GrupoPreguntas_Labs::all();

        return view('laboratorio/pregunta/create', ['grupopreguntalabs' => $grupopreguntalabs]);
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Pregunta_Labs::create([

            'nombre' => $request['nombre'],
            'id_grupo_preglabs' => $request['id_grupopregunta'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/preguntas');
    }

    private function validateInput($request) {
       $messages = [
        'nombre.required' => 'Agrega el nombre de la pregunta.',
        'id_grupopregunta.required' => 'Agrega al grupo que pertece la pregunta.',
        ];
        
        $constraints = [
            'nombre' => 'required',
            'id_grupopregunta' => 'required',
        ];

        $this->validate($request, $constraints, $messages);

    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $pregunta = Pregunta_Labs::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($pregunta == null || count($pregunta) == 0) {
            return redirect()->intended('/preguntas');
        }
        $grupopreguntalabs = GrupoPreguntas_Labs::all();
        return view('laboratorio/pregunta/edit', ['pregunta' => $pregunta, 'grupopreguntalabs' => $grupopreguntalabs]);
    }

    public function update(Request $request,  $id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $Pregunta = Pregunta_Labs::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del area.',
        'id_grupopregunta.required' => 'Agrega la descripcion del area.',
          
        ];

        
        $constraints = [
        'nombre' => 'required',           
        'id_grupopregunta' => 'required',           
        'estado' => 'required'
            ];
  
                

        $input = [
            'nombre' => $request['nombre'],
            'id_grupo_preglabs' => $request['id_grupopregunta'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        Pregunta_Labs::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/preguntas');
    }




}