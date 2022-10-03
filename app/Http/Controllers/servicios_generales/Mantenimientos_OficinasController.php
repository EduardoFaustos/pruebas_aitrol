<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Mantenimientos_Oficinas;
use Sis_medico\Mantenimientos_Banos;
use Sis_medico\Mantenimientos_Generales;
use Sis_medico\Sala;
use Sis_medico\Http\Controllers\Controller;

class Mantenimientos_OficinasController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'servicios_generales/mantenimientos_oficinas/listaoficinas';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 24)) == false) {
            return true;
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $oficinas = Mantenimientos_Oficinas::where('estado','!=', '0')->paginate(50);
        
        return view('servicios_generales/mantenimientos_oficinas/index', ['oficinas' => $oficinas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $oficinas = Mantenimientos_Oficinas::paginate(50);
        $generales = Mantenimientos_Generales::where('estado', '1')->get();

        return view('servicios_generales/mantenimientos_oficinas/create', ['oficinas' => $oficinas,'generales' => $generales]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $arr_oficina = [


            'nombre_oficina'     => strtoupper($request['nombre_oficina']),
            'id_unidad'     => $request['id_unidad'],
            'descripcion'     => $request['descripcion'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        Mantenimientos_Oficinas::create($arr_oficina);

        return json_encode("ok");}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $oficinas = Mantenimientos_Oficinas::find($id);
        $general = Mantenimientos_Generales::where('id', '<>', $id)->where('estado', '1')->get();
        // Redirect to user list if updating user wasn't existed
        

        return view('servicios_generales/mantenimientos_oficinas/edit', ['oficinas' => $oficinas,'id' => $id, 'general' => $general]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $mantenimientos_oficinas = Mantenimientos_Oficinas::where('id', $request['id'])->first();

        $mantenimientos_oficinas->id_unidad            = $request['id_unidad'];
        $mantenimientos_oficinas->nombre_oficina       = $request['nombre_oficina'];
        $mantenimientos_oficinas->descripcion          = $request['descripcion'];
        $mantenimientos_oficinas->estado          = $request['estado'];
        $mantenimientos_oficinas->ip_creacion          = $ip_cliente;
        $mantenimientos_oficinas->ip_modificacion      = $ip_cliente;
        $mantenimientos_oficinas->id_usuariocrea       = $idusuario;
        $mantenimientos_oficinas->id_usuariomod        = $idusuario;
        $mantenimientos_oficinas->save();

        return json_encode('ok');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
    User::where('id', $id)->delete();
    return redirect()->intended('/user-management');
    }
     */
    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $constraints = [
            'nombre_oficina' => $request['nombre_oficina'],

        ];

   

        $oficinas = $this->doSearchingQuery($constraints);

        return view('servicios_generales/mantenimientos_oficinas/index', ['oficinas' => $oficinas, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = Mantenimientos_Oficinas::query();
        $fields = array_keys($constraints);
        $index  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        return $query->paginate(50);
    }

    private function validateInput($request)
    {
        $messages = [
            'nombre_oficina.required' => 'Agrega el nombre de la Oficina.',
            'nombre_oficina.max'      => 'El nombre de la oficina no puede ser mayor a :max caracteres.',

        ];

        $constraints = [
            'nombre_oficina' => 'required|max:30',

        ];

        $this->validate($request, $constraints, $messages);

    }

    public function listasoficinas($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $oficinas    = Mantenimientos_Oficinas::where('id_unidad', '=', $id)->paginate(50);
        $mantenimientos_g = Mantenimientos_Generales::find($id);

        $nombre_unidad = $mantenimientos_g->nombre_unidad;

        return view('servicios_generales/mantenimientos_oficinas/listasoficinas', ['oficinas' => $oficinas, 'mantenimientos_g' => $mantenimientos_g]);
    }

    public function crear($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $mantenimientos_g = Mantenimientos_Generales::find($id);
        return view('servicios_generales/mantenimientos_oficinas/create', ['mantenimientos_g' => $mantenimientos_g]);
    }

    public function grabar(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Mantenimientos_Oficinas::create([

            'nombre_oficina'  => strtoupper($request['nombre_oficina']),
            'id_unidad'       => $request['id_unidad'],
            'descripcion'     => $request['descripcion'],
            'estado'          => $request['estado'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);

        //return redirect()->intended('/sala-management');
        $oficinas    = Mantenimientos_Oficinas::where('id_unidad', '=', $id)->paginate(50);
        $mantenimientos_g = Mantenimientos_Generales::find($id);

        $nombre_unidad = $mantenimientos_g->nombre_unidad;
        return view('servicios_generales/mantenimientos_oficinas/listaoficinas', ['oficinas' => $oficinas, 'mantenimientos_g' => $mantenimientos_g]);
    }

    public function editar($id_unidad, $id_oficina)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $oficinas = Mantenimientos_Oficinas::find($id_oficina);
        // Redirect to user list if updating user wasn't existed
        if ($oficinas == null || count($oficinas) == 0) {
            //return redirect()->intended('/sala-management');
        }
        $mantenimientos_g = Mantenimientos_Generales::find($id_unidad);

        $nombre_unidad = $mantenimientos_g->nombre_unidad;

        return view('servicios_generales/mantenimientos_oficinas/editar', ['oficinas' => $oficinas, 'mantenimientos_g' => $mantenimientos_g]);
    }

    public function actualizar(Request $request, $id_unidad, $id_oficina)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $oficinas = Mantenimientos_Oficinas::find($id_oficina);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $messages = [
            'nombre_oficina.required' => 'Agrega el nombre del areas.',
            'nombre_oficina.max'      => 'El nombre de la area no puede ser mayor a :max caracteres.',
            'estado.required'      => 'Agrega el estado.',

        ];

        $constraints = [
            'nombre_oficina' => 'required|max:30',
            'estado'      => 'required',
        ];

        $input = [
            'nombre_oficina'     => strtoupper($request['nombre_oficina']),
            'id_unidad'     => $request['id_unidad'],
            'estado'          => $request['estado'],
            'descripcion'     => $request['descripcion'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $this->validate($request, $constraints, $messages);

        Mantenimientos_Oficinas::where('id', $id_oficina)
            ->update($input);

   
        $oficinas    = Mantenimientos_Oficinas::where('id_unidad', '=', $id_unidad)->paginate(50);
        $mantenimientos_g = Mantenimientos_Generales::find($id_unidad);

        $nombre_unidad = $oficinas->nombre_unidad;
        return view('servicios_generales/mantenimientos_oficinas/listaoficinas', ['oficinas' => $oficinas, 'mantenimientos_g' => $mantenimientos_g]);
    }

}
