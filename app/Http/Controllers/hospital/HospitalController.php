<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Hospital_Producto;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Hospital_Log_Movimiento;
use Sis_medico\Habitacion;
use Illuminate\Pagination\Paginator;
use Sis_medico\agenda;
use Sis_medico\Ingreso_emer_008;
use Sis_medico\Cama;
use Sis_medico\Piso;
use Sis_medico\AgendaQ;
use Sis_medico\Quirofano;
use Sis_medico\User;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\Seguro;
use Sis_medico\Imagen;
use Sis_medico\CamaTransaccion;
use Sis_medico\Hospital_Emergencia;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Examen;
//use Sis_medico\Hospital;
use Sis_medico\Firma_Usuario;
use Sis_medico\CamaPaciente;
use Sis_medico\Formulario005;
use Sis_medico\Evolucion_005;
use Sis_medico\Diagnostico_005;
use Sis_medico\Medidas_generales;
use Sis_medico\Tratamiento_005;
use Sis_medico\Plan_005;
use Sis_medico\Hospital_Salas;
use Sis_medico\hc_cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Datos_Paciente;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Hospital;
use Sis_medico\Ho_Solicitud;
use Sis_medico\HoEstadoPaso;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\Log_agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Medicina;
use Sis_medico\Sala;
use Sis_medico\User_espe;

class HospitalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $fechahoy = date("Y-m-d");
        //$otrointento = AgendaQ::where("estado","1")->whereBetween('fechaini', [$fechahoy.' 00:00:00', $fechahoy.' 23:59:59'])->get();
        /*$otrointento = AgendaQ::where("estado","1")->where('fechaini',$fechahoy)->get();
        $totales=0;
        if($otrointento=="[]"){
            $otrointento= 0;
        }else {
            $totales= $otrointento->count();
        }*/

        /*$agendaprogramadas= AgendaQ::where('estado','=','1')->get();

        $query = "SELECT SUBSTRING(fechaini,1,9) as fecha
                 FROM `agenda_quirofano` WHERE fecha.fecha ";

        $datesubstring = DB::select($query);
        $totales= count($);*/
        $nombres = DB::table('cama_paciente')
            ->join('paciente', 'paciente.id', '=', 'cama_paciente.id_paciente')
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'cama_paciente.id_paciente', 'cama_paciente.created_at', 'cama_paciente.id_cama')
            ->get();
        //dd($nombres);
        //$inicio = CamaPaciente::all();
        $totales = AgendaQ::where('estado', '=', '1')->where('fecha_total', '=', $fechahoy)->count();

        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $especialidades = Especialidad::where('estado', '1')->get();
        return view('hospital/index', ['totales' => $totales, 'nombres' => $nombres, 'doctores' => $doctores, 'seguros' => $seguros, 'especialidades' => $especialidades, 'id_especialidad' => null, 'id_doctor1' => null, 'id_seguro' => null, 'request' => $request]);
    }
    public function farmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $farmacia = Hospital_Producto::paginate(5);
        return view('hospital/farmacia', ['farmacia' => $farmacia]);
    }
    /*public function iniciobusca(Request $request){
    $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        if(($request->nombre1)!=""){
            $nombres= CamaPaciente::where("id_paciente","like",$request->nombre1."%")->get();
        }
        elseif(($request->apellido1)!=""){
            $nombres= CamaPaciente::where("id_cama","like",$request->apellido1."%")->get();    
        }
    return view('hospital/iniciobusca',['nombres'=>$nombres]);
    }*/
    public function buscadorfarmacia(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if (($request->codigo) != "") {
            $farmacia = Hospital_Producto::where("codigo", "like", $request->codigo . "%")->get();
        } elseif (($request->nombre) != "") {
            $farmacia = Hospital_Producto::where("nombre", "like", $request->nombre . "%")->get();
        }

        return view('hospital/buscadorfarmacia', ['farmacia' => $farmacia]);
    }
    public function agregarp()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/agregarp');
    }
    public function quirofano()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $agendados = DB::table('agenda_quirofano')
            ->join('users', 'users.id', '=', 'agenda_quirofano.id_doctor')
            ->join('paciente', 'paciente.id', '=', 'agenda_quirofano.id_paciente')
            ->select('users.nombre1', 'users.apellido1', 'agenda_quirofano.fechaini', 'agenda_quirofano.fechafin', 'agenda_quirofano.observaciones', 'paciente.nombre2', 'paciente.apellido2')
            ->paginate(5);
        //dd($agendados);
        //$paciente=hospital::with('nombre')->get();
        //$paciente=Paciente::find(1)->paciente:
        $quirofano = Quirofano::all();
        $agenda = AgendaQ::paginate(5);
        //dd($quirofano);
        return view('hospital/quirofano/quirofano', ['quirofano' => $quirofano, 'agenda' => $agenda, 'agendados' => $agendados]);
    }
    public function modalq()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/quirofano/modalq');
    }
    public function resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();

        $medico = $request->get('medico');
        $codigo = $request->get('codigo');
        $nota_evolucion = $request->get('nota_evolucion');

        $prescri = Evolucion_005::where('id_paciente', '=', $id_paciente)
            ->medico($medico)
            ->codigo($codigo)
            ->nota_evolucion($nota_evolucion)
            ->paginate(5);
        return view('hospital/emergencia/resultado', ['prescri' => $prescri, 'id_paciente' => $id_paciente, 'users' => $users]);
    }
    public function modaleditar($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $campo = Evolucion_005::findOrFail($id);
        $editar = Evolucion_005::where('id', '=', $id)->paginate(5);
        return view('hospital/emergencia/modaleditar', ['editar' => $editar, 'id' => $id, 'campo' => $campo]);
    }
    public function registro()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/emergencia/registro');
    }
    public function vistac()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $id_tipo = 1;
        $muestra = Hospital::where('id_tipo', $id);
        return view('hospital/vistac', ['muestra' => $muestra]);
    }
    public function questionario($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $formnularioid = paciente::find($id);
        // dd($formnularioid);
        return view('hospital/emergencia/questionario', ['formnularioid' => $formnularioid]);
    }
    public function modalprescripcion()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/modalprescripcion');
    }
    public function modalplan()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return
                redirect('/');
        }
        $plan = Plan_005::paginate(5);
        return view('hospital/emergencia/modalplan', ['plan' => $plan]);
    }
    public function emergencia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $firma = firma_usuario::all();
        $cama1 = cama::all();
        $seguro = seguro::all();
        $examenes = examen::all();
        $paciente = paciente::paginate(20);
        $user = User::all();
        //dd($paciente);
        $hospital_emergencia = DB::table('hospital_emergencia as a')
            //->where('a.tipo', '2')
            //->join('examen','hospital_emergencia.id_examen','=','id')
            ->join('examen as b', 'b.id', 'a.id_examen')
            ->select('a.id_examen')
            ->get();
        $milo = DB::table('hospital_emergencia as a')
            ->join('firma_usuario as b', 'b.id', 'a.id_nombre')
            ->select('a.id_nombre')
            ->get();


        return view('hospital/emergencia/emergencia', ['paciente' => $paciente, 'firma' => $firma, 'examenes' => $examenes, 'user' => $user, 'cama1' => $cama1, 'milo' => $milo, 'seguro' => $seguro]);
    }
    /*public function buscadore(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        if(($request->nombre1)!=""){
            $paciente= paciente::where("nombre1","like",$request->nombre1."%")->get();
        }
      
        elseif(($request->apellido1)!=""){
            $paciente= paciente::where("apellido1","like",$request->apellido1."%")->get();    
        }
        
        return view('hospital/emergencia/buscadore',['paciente'=>$paciente]);
    }*/
    public function autocompletar(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM `paciente`
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }
    public function autocompletar2(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, fecha_nacimiento 
                FROM paciente
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        if ($nombres != array()) {
            $data = $nombres[0]->fecha_nacimiento;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function autocompletar3(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, historia_clinica
                    FROM paciente
                    WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        if ($nombres != array()) {
            $data = $nombres[0]->historia_clinica;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }
    public function autocompletar4(Request $request)
    {
        $nombre_encargado = $request['apellido'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id_seguro,observacion,gruposanguineo,referido,antecedentes_fam,antecedentes_pat,id
                FROM paciente
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'seguro' => $product->id_seguro, 'observacion' => $product->observacion, 'gruposanguineo' => $product->gruposanguineo, 'referido' => $product->referido, 'antecedentes_fam' => $product->antecedentes_fam, 'antecedentes_pat' => $product->antecedentes_pat, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }
    public function registrome(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $hola  = Auth::user()->cedula;
        Hospital_Emergencia::create([
            'apellidos' => $request['apellido'],
            'anotaciones' => $request['anotaciones'],
            'id_examen' => $request['nameid'],
            'id_nombre' => $request['id_tipo'],
            'apellidos' => $request['apellido'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,

        ]);
        return back();
    }

    public function agregarpa()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        // $paises = Pais::all();
        // $seguros = Seguro::all();

        // return view('hospital/emergencia/agregarpa', ['paises'=>$paises, 'seguros'=>$seguros]);
        return view('hospital/emergencia/agregarpa');
    }

    // public function registropac(Request $request){
    //     $opcion = '1';
    //     if ($this->rol_new($opcion)) {
    //         return redirect('/');
    //     }
    //     $ip_cliente = $_SERVER["REMOTE_ADDR"];
    //     $idusuario  = Auth::user()->id;

    //     Paciente::create([
    //         'id' => $request['id_usuario'],
    //         'id_seguro' => $request['id_seguro'],
    //         'nombre1' => $request['nombre1'],
    //         'nombre2' => $request['nombre2'],
    //         'apellido1' => $request['apellido1'],
    //         'apellido2' => $request['apellido2'],
    //         'telefono1' => $request['telefono1'],
    //         'telefono2' => $request['telefono2'],
    //         'id_pais' => $request['id_pais'],
    //         'fecha_nacimiento' => $request['fecha_nacimiento'],
    //         'email' => $request['email'],
    //     ]);
    //     return back();
    // }

    public function registropac(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        date_default_timezone_set('America/Guayaquil');
        $bandera = 0;
        $id      = $request['cedula'];

        $user = User::find($id);

        // Redirect to user list if updating user wasn't existed
        if (!is_null($user)) {

            $this->validateInput2($request);
            Paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);

            Cortesia_paciente::create([
                'id'              => $request['cedula'],
                'cortesia'        => $request['cortesia'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        } else {

            $this->validateInput($request);

            User::create([
                'id'               => $request['cedula'],
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'telefono1'        => '1',
                'id_pais'          => '1',
                'id_tipo_usuario'  => 2,
                'email'            => $request['email'],
                'password'         => bcrypt($request['cedula']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'fecha_nacimiento' => $request['fecha_nacimiento'],
            ]);
            paciente::create([
                'id'                 => $request['cedula'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'telefono1'          => '1',

                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'id_pais'            => '1',
                'tipo_documento'     => 1,
                'imagen_url'         => ' ',
                'menoredad'          => '0',
                'id_seguro'          => '1',
                'id_usuario'         => $request['cedula'],
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
            ]);
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['cedula'],
                'dato1'       => strtoupper($request['nombre1']) . " " . strtoupper($request['nombre2']) . " " . strtoupper($request['apellido1']) . " " . strtoupper($request['apellido2']),
            ]);
        }

        $paciente = Paciente::find($id);

        if ($request->cortesia == 'SI') {
            $cortesia_paciente = Cortesia_paciente::find($id);
            if (is_null($cortesia_paciente)) {
                Cortesia_paciente::create([
                    'id'              => $id,
                    'cortesia'        => 'SI',
                    'ilimitado'       => 'NO',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }

        return $paciente->id;
    }

    private function validateInput2($request)
    {

        $rules = [

            'cedula'           => 'required|max:10|unique:paciente,id',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [

            'cedula'           => 'required|max:10|unique:users',
            'nombre1'          => 'required|max:60',
            'nombre2'          => 'required|max:60',
            'apellido1'        => 'required|max:60',
            'apellido2'        => 'required|max:60',
            'email'            => 'required|email|max:191|unique:users',
            'cedula'           => 'required|max:10|unique:paciente,id',
            'fecha_nacimiento' => 'required',

        ];

        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar Ninguno.',
            'id.required'                => 'Agrega la cédula.',
            'id.max'                     => 'La cédula no puede ser mayor a :max caracteres.',
            'id.unique'                  => 'Cedula ya se encuentra registrada.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre2.required'           => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono1.required'         => 'Agrega el teléfono del domicilio.',
            'telefono1.numeric'          => 'El teléfono de domicilio debe ser numérico.',
            'telefono1.max'              => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono2.required'         => 'Agrega el teléfono celular.',
            'telefono2.numeric'          => 'El teléfono celular debe ser numérico.',
            'telefono2.max'              => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais.required'           => 'Selecciona el pais.',
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.required'             => 'Agrega el Email.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.unique'               => 'el Email ya se encuentra registrado.',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'cedula.required'            => 'Agrega la cédula.',
            'cedula.max'                 => 'La cédula no puede ser mayor a :max caracteres.',
            'cedula.unique'              => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a :max caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a :max caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

    public function formulario05(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $cie = hc_cie10::all();
        //dd($cie);
        $servicio = Evolucion_005::where('id_paciente', '=', 'id_paciente')->get();
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'paciente.fecha_nacimiento', 'paciente.sexo', 'ingreso_emer_008.id')
            ->get();
        //dd($users);
        return view('hospital/emergencia/formulario05', ['id_paciente' => $id_paciente, 'servicio' => $servicio, 'users' => $users, 'cie' => $cie]);
    }
    public function medicamentos(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        Formulario005::create([
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,
            'medicamento' => $request['medicamento'],
            'posologia' => $request['posologia'],
            'indicaciones_medicinas' => $request['indicaciones_medi'],
            'cantidad_medicinas' => $request['cantidad'],
            'nombre_medicina' => $request['nombre_medicina'],
            'presentacion_medicamento' => $request['presentacion_medicamento'],
            'concentracion_medicamento' => $request['concentracion_medicamento'],
            'dosis_medicamento' => $request['dosis_medicamento'],
            'unidad_medicamento' => $request['unidad_medicamento'],
            'via_medicamento' => $request['via_medicamento'],
            'frecuencia_medicamento' => $request['frecuencia_medicamento'],
            'duracion_medicamento' => $request['duracion_medicamento'],
            'id_paciente' => $request['id_paciente'],
        ]);

        return back()->with('ok', 'Guardado con exito:');
    }

    public function formuarioevolucion(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        $this->validate($request, [

            'medico' => 'required|max:255',
            'examen_fisico' => 'required|max:255',
            'nota_de_evolucion' => 'required|max:255',
            'codigo' => 'required|max:255',

        ]);

        Evolucion_005::create([

            'id_usuariocrea'    =>  $idusuario1,
            'ip_creacion'       =>  $ip_clientes,
            'id_usuariomod'     =>  $idusuario1,
            'no_evolucion'      =>  $request['no_evolucion'],
            'medico'            =>  $request['medico'],
            'codigo'            =>  $request['codigo'],
            'nota_evolucion'    =>  $request['nota_de_evolucion'],
            'fecha_evolucion'   =>  $request['fecha_evolucion'],
            'examen_fisico'     =>  $request['examen_fisico'],
            'id_paciente'       =>  $request['id_paciente'],
        ]);

        return back()->with('success', 'Guardado con exito:');
    }

    public function diagnostico005(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //$data=request::all();
        //dd($request->all());

        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        if (count($request->tipo) > 0) {
            foreach ($request->tipo as $item => $v) {

                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_diagnostico' => $request->fecha_diagnostico[$item],
                    'cie_diagnostico' => $request->cie[$item],
                    'operacion_diagnostico' => $request->operacion[$item],
                    'tipo_diagnostico' => $request->tipo[$item],
                    'medico_diagnostico' => $request->medico_urgente[$item]
                );

                Diagnostico_005::insert($data2);
            }
        }
        return redirect()->back()->with('successo', 'Guardado Doc');
    }

    public function medidas_generales(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medico_general) > 0) {
            foreach ($request->medico_general as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_generales' => $request->fecha_generales[$item],
                    'medico_general' => $request->medico_general[$item],
                    'descripcion_general' => $request->descripcion_general[$item],
                );
                Medidas_generales::insert($data2);
            }
        }
        return redirect()->back()->with('exito', 'Guardado Doc');
    }

    public function salas(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        //dd($request->all());
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medicina_salas) > 0) {
            foreach ($request->medicina_salas as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fecha_salas' => $request->fecha_salas[$item],
                    'area_salas' => $request->area_salas[$item],
                    'medicina_salas' => $request->medicina_salas[$item],
                    'descripcion_salas' => $request->descripcion_salas[$item],
                    'medico_salas' => $request->medico_salas[$item],
                );
                Hospital_Salas::insert($data2);
            }
        }
        return redirect()->back()->with('dato', 'Guardado Doc');
    }
    public function tratamiento(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->medico_tratamiento) > 0) {
            foreach ($request->medico_tratamiento as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fechatratamiento' => $request->fechatratamiento[$item],
                    'medico_tratamiento' => $request->medico_tratamiento[$item],
                    'descripcion_tratamiento' => $request->descripcion_tratamiento[$item],
                );
                Tratamiento_005::insert($data2);
            }
        }
        return redirect()->back()->with('listo', 'Guardado Doctor!!');
    }
    public function plan(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;
        if (count($request->descripcion_plan) > 0) {
            foreach ($request->descripcion_plan as $item => $v) {
                $data2 = array(
                    'id_usuariocrea' => $idusuario1,
                    'ip_creacion' => $ip_clientes,
                    'id_usuariomod' => $idusuario1,
                    'id_paciente' => $request['id_paciente'],
                    'fechaplan' => $request->fechaplan[$item],
                    'medico_plan' => $request->medico_plan[$item],
                    'descripcion_plan' => $request->descripcion_plan[$item],
                );
                Plan_005::insert($data2);
            }
        }
        return redirect()->back()->with('validado', 'Guardado Doctor!!');
    }

    public function editarevolucion(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Evolucion_005::findOrFail($id);
        $editar = [
            'examen_fisico' => $request['examen_fisico'],
            'nota_evolucion' => $request['nota_evolucion'],
        ];
        $variable = $campo->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function resultado_diagnostico(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_diagnostico = $request->get('medico_diagnostico');
        $operacion_diagnostico = $request->get('operacion_diagnostico');
        $cie_diagnostico = $request->get('cie_diagnostico');

        $prescri = Diagnostico_005::where('id_paciente', '=', $id_paciente)
            ->medico_diagnostico($medico_diagnostico)
            ->operacion_diagnostico($operacion_diagnostico)
            ->cie_diagnostico($cie_diagnostico)
            ->paginate(5);

        return view('hospital/emergencia/resultado_diagnostico', ['id_paciente' => $id_paciente, 'users' => $users, 'prescri' => $prescri]);
    }
    public function modaleditar_diagnostico(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Diagnostico_005::findOrFail($id);

        return view('hospital/emergencia/modaleditar_diagnostico', ['campo' => $campo, 'id' => $id]);
    }

    public function editardiagnostico(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $campo = Diagnostico_005::findOrFail($id);
        $editar = [
            'operacion_diagnostico' => $request['operacion'],
            'cie_diagnostico' => $request['cie'],
            'tipo_diagnostico' => $request['tipo'],
        ];
        $variable = $campo->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function resultado_generales(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_general = $request->get('medico_general');
        $descripcion_general = $request->get('descripcion_general');
        $prescri = medidas_generales::where('id_paciente', '=', $id_paciente)
            ->medico_general($medico_general)
            ->descripcion_general($descripcion_general)
            ->paginate(5);

        return view('hospital/emergencia/resultado_generales', ['id_paciente' => $id_paciente, 'users' => $users, 'prescri' => $prescri]);
    }
    public function editar_generales(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = medidas_generales::findOrFail($id);

        return view('hospital/emergencia/editar_generales', ['dato' => $dato, 'id' => $id]);
    }
    public function editar_gene(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Medidas_generales::findOrFail($id);
        $editar = [
            'medico_general' => $request['medico_general'],
            'descripcion_general' => $request['descripcion_general'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function mostrar_resultadotratamiento(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_tratamiento = $request->get('medico_tratamiento');
        $descripcion_tratamiento = $request->get('descripcion_tratamiento');

        $tratamiento = Tratamiento_005::where('id_paciente', '=', $id_paciente)
            ->medico_tratamiento($medico_tratamiento)
            ->descripcion_tratamiento($descripcion_tratamiento)
            ->paginate(5);
        return view('hospital/emergencia/mostrar_resultadotratamiento', ['id_paciente' => $id_paciente, 'users' => $users, 'tratamiento' => $tratamiento]);
    }

    public function modal_tratamiento(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Tratamiento_005::findOrFail($id);

        return view('hospital/emergencia/modal_tratamiento', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_tratamiento(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Tratamiento_005::findOrFail($id);
        $editar = [
            'medico_tratamiento' => $request['medico_tratamiento'],
            'descripcion_tratamiento' => $request['descripcion_tratamiento'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function resultado_plan(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medico_plan = $request->get('medico_plan');
        $descripcion_plan = $request->get('descripcion_plan');

        $tratamiento = Plan_005::where('id_paciente', '=', $id_paciente)
            ->medico_plan($medico_plan)
            ->descripcion_plan($descripcion_plan)
            ->paginate(5);
        return view('hospital/emergencia/resultado_plan', ['id_paciente' => $id_paciente, 'users' => $users, 'tratamiento' => $tratamiento]);
    }

    public function modal_editarplan(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Plan_005::findOrFail($id);

        return view('hospital/emergencia/modal_editarplan', ['dato' => $dato, 'id' => $id]);
    }
    public function editar_plan(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Plan_005::findOrFail($id);
        $editar = [
            'descripcion_plan' => $request['descripcion_plan'],
            'medico_plan' => $request['medico_plan'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }
    public function medicamentos_resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $medicamento = $request->get('medicamento');

        $medicamento = Formulario005::where('id_paciente', '=', $id_paciente)
            ->medicamento($medicamento)
            ->paginate(5);

        return view('hospital/emergencia/medicamentos_resultado', ['id_paciente' => $id_paciente, 'users' => $users, 'medicamento' => $medicamento]);
    }

    public function modal_medicamentos(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Formulario005::findOrFail($id);

        return view('hospital/emergencia/modal_medicamentos', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_medi(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Formulario005::findOrFail($id);
        $editar = [
            'medicamento' => $request['medicamento'],
            'posologia' => $request['posologia'],
            'indicaciones_medicinas' => $request['indicaciones_medicinas'],
            'cantidad_medicinas' => $request['cantidad_medicinas'],
            'nombre_medicina' => $request['nombre_medicina'],
            'presentacion_medicamento' => $request['presentacion_medicamento'],
            'concentracion_medicamento' => $request['concentracion_medicamento'],
            'dosis_medicamento' => $request['dosis_medicamento'],
            'unidad_medicamento' => $request['unidad_medicamento'],
            'via_medicamento' => $request['via_medicamento'],
            'frecuencia_medicamento' => $request['frecuencia_medicamento'],
            'duracion_medicamento' => $request['duracion_medicamento'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function salas_resultado(Request $request, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $users = DB::table('ingreso_emer_008')
            ->join('paciente', 'paciente.id', '=', 'ingreso_emer_008.id_paciente')
            ->where('ingreso_emer_008.id_paciente', '=', $id_paciente)
            ->select('paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2')
            ->get();
        $area_salas = $request->get('area_salas');
        $medicina_salas = $request->get('medicina_salas');

        $medicamento = Hospital_Salas::where('id_paciente', '=', $id_paciente)
            ->area_salas($area_salas)
            ->medicina_salas($medicina_salas)
            ->paginate(5);

        return view('hospital/emergencia/salas_resultado', ['id_paciente' => $id_paciente, 'users' => $users, 'medicamento' => $medicamento]);
    }

    public function editar_salas(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Hospital_Salas::findOrFail($id);
        //dd($dato);

        return view('hospital/emergencia/editar_salas', ['dato' => $dato, 'id' => $id]);
    }

    public function editar_modal(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
            // dd($request->all());
        }
        $dato = Hospital_Salas::findOrFail($id);
        $editar = [
            'area_salas' => $request['area_salas'],
            'medicina_salas' => $request['medicina_salas'],
            'descripcion_salas' => $request['descripcion_salas'],
        ];
        $variable = $dato->update($editar);
        return back()->with('success', 'Editado con exito:');
    }

    public function autocompletarcie(Request $request)
    {

        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', id, descripcion) as completo
                  FROM `Cie_10_3`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $query1 = "SELECT CONCAT_WS(' ', id, descripcion) as completo1
                  FROM `Cie_10_4`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        $nombres1 = DB::select($query1);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo);
        }
        foreach ($nombres1 as $product) {
            $data[] = array('value' => $product->completo1);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function detalles($id, Request $request)
    {

        //$paciente = Paciente::find($id_paciente);
        $solicitud = Ho_Solicitud::find($id);
        //dd($solicitud);

        return view('hospital.detalles', ['solicitud' => $solicitud]);
    }
    public function primer_paso(Request $request)
    {

        return view('hospital.primerpaso');
    }
    public function segundo_paso(Request $request)
    {
        return view('hospital.segundopaso');
    }
    public function tercer_paso(Request $request)
    {

        return view('hospital.tercerpaso');
    }
    public function cuarto_paso(Request $request)
    {
        return view('hospital.cuartopaso');
    }
    public function quinto_paso(Request $request)
    {
        return view('hospital.quintopaso');
    }

    public function genericos(Request $request)
    {
        $genericos = [];
        if ($request['search'] != null) {
            $genericos = Medicina::where('medicina.nombre', 'LIKE', $request['search'] . '%')->where('estado', '1')->select('medicina.nombre as text', 'medicina.id as id')->get();
        }
        return response()->json($genericos);
    }
    public function getResponse($id)
    {
        $porSi = "";
        return $id;
    }

    public function admision($id_paso){

        $fecha = date('Y-m-d');
        $seguros = Seguro::where('inactivo', '1')->get();      

        return view('hospital/admision',['seguros' => $seguros, 'fecha' => $fecha, 'id_paso' => $id_paso]);
    }

    public function ingreso_modulos(Request $request)
    {
        DB::beginTransaction();
        try {
            $ip_cliente   = $_SERVER["REMOTE_ADDR"];
            $idusuario    = Auth::user()->id;
            $id_doctor    = Auth::user()->id;
            $id_paciente = $request['cedula'];
            $paciente = paciente::find($id_paciente);
            $user = User::find($id_paciente);
            $datos_paciente = Ho_Datos_Paciente::Where('id_paciente', $id_paciente)->first();
            $id_paso = $request['id_paso'];
            $estado_paso = HoEstadoPaso::find($id_paso);

            $input_pac = [
                'id'                 => $id_paciente,
                'id_usuario'         => $id_paciente,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['f_nacimiento'],
                'sexo'               => $request['sexo'],
                'ciudad'             => $request['ciudad'],
                'direccion'          => $request['direccion'],
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'telefono_llamar'    => $request['telefono_llamar'],
                'ocupacion'          => $request['ocupacion'],
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'referido'           => $request['referido'],
                'tipo_documento'     => 1,
                'id_seguro'          => 1,
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
            ];
    
            $input_pac_upd = [
                'id'                 => $id_paciente,
                'id_usuario'         => $id_paciente,
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['f_nacimiento'],
                'sexo'               => $request['sexo'],
                'ciudad'             => $request['ciudad'],
                'direccion'          => $request['direccion'],
                'telefono1'          => $request['telefono1'],
                'telefono2'          => $request['telefono2'],
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentescofamiliar' => $request['parentesco'],
                'telefono3'          => $request['telefono_llamar'],
                'ocupacion'          => $request['ocupacion'],
                'referido'           => $request['referido'],
                'ip_modificacion'    => $ip_cliente,
                'id_usuariomod'      => $idusuario,
            ];
    
            $input_usu_c = [
                'id'               => $id_paciente,
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_tipo_usuario'  => 2,
                'email'            => $request['id'] . '@mail.com',
                'password'         => bcrypt($request['id']),
                'tipo_documento'   => 1,
                'estado'           => 1,
                'imagen_url'       => ' ',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
            ];
    
            $input_usu_up = [
                'id'               => $id_paciente,
                'nombre1'          => strtoupper($request['nombre1']),
                'nombre2'          => strtoupper($request['nombre2']),
                'apellido1'        => strtoupper($request['apellido1']),
                'apellido2'        => strtoupper($request['apellido2']),
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'telefono1'        => $request['telefono1'],
                'telefono2'        => $request['telefono2'],
                'id_tipo_usuario'  => 2,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
            ];
    
            $arr_ho_pac = [
                'id_paciente'        => $id_paciente,
                'barrio'             => $request['barrio'],
                'parroquia'          => $request['parroquia'],
                'canton'             => $request['canton'],
                'provincia'          => $request['provincia'],
                'zona_ur'            => $request['zona'],
                'grupo_cultural'     => $request['grupo_cultural'],
                'edad'               => $request['edad'],
                'direccion_familiar' => $request['direccion_familiar'],
                'forma_llegada'      => $request['forma_llegada'],
                'fuente_informacion'     => $request['fuente_informacion'],
                'telefono_inst_per_paci' => $request['telefono_inst_per_paci'],
                'instruccion'            => $request['instruccion'],
                'empresa_trabajo'        =>$request['empresa'],
                'llamar_a'               => $request['llamar_a'],
                'nacionalidad'           => $request['nacionalidad'],
                'ip_creacion'            => $ip_cliente,
                'ip_modificacion'        => $ip_cliente,
                'id_usuariocrea'         => $idusuario,
                'id_usuariomod'          => $idusuario,
                'parentesco_afinidad'    => $request['parentesco'],
            ];
    
            $arr_ho_pac_up = [
                'id_paciente'        => $id_paciente,
                'barrio'             => $request['barrio'],
                'parroquia'          => $request['parroquia'],
                'canton'             => $request['canton'],
                'provincia'          => $request['provincia'],
                'zona_ur'            => $request['zona'],
                'grupo_cultural'     => $request['grupo_cultural'],
                'edad'               => $request['edad'],
                'direccion_familiar' => $request['direccion_familiar'],
                'forma_llegada'      => $request['forma_llegada'],
                'fuente_informacion'     => $request['fuente_informacion'],
                'telefono_inst_per_paci' => $request['telefono_inst_per_paci'],
                'instruccion'            => $request['instruccion'],
                'empresa_trabajo'        =>$request['empresa'],
                'llamar_a'               => $request['llamar_a'],
                'nacionalidad'           => $request['nacionalidad'],
                'parentesco_afinidad'    => $request['parentesco'],
                'ip_modificacion'        => $ip_cliente,
                'id_usuariomod'          => $idusuario,
            ];
    
    
            if (is_null($paciente)) {
    
                if (!is_null($user)) {
                    $user->update($input_usu_up);
                } else {
                    User::create($input_usu_c);
                }
    
                paciente::create($input_pac);
    
                $input_log = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CREA NUEVO PACIENTE",
                    'dato_ant1'   => $id_paciente,
                    'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                    'dato2'       => 'Hospital',
                ];
    
                Log_usuario::create($input_log);
            } else {
                $paciente->update($input_pac_upd);
                if (!is_null($datos_paciente)) {
                    $datos_paciente->update($arr_ho_pac_up);
                }
            }
    
            if (is_null($datos_paciente)) {
                Ho_Datos_Paciente::create($arr_ho_pac);
            } else {
                $datos_paciente->update($arr_ho_pac_up);
            }
    
            $id_sala= Sala::where('nombre_sala', 'like', $estado_paso->descripcion)->first();
            $especialidad = User_espe::where('usuid', $id_doctor)->get()->first();
            $empresa = Empresa::where('prioridad', '2')->where('estado','1')->first();

            if (!is_null($especialidad)) {
                $espid = $especialidad->espid;
            } else {
                $espid = '4';
            }

            $input_agenda = [
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'id_paciente'     => $id_paciente,
                'id_doctor1'      => $id_doctor,
                'proc_consul'     => '1',
                'estado_cita'     => '1',
                'id_empresa'      => $empresa->id,
                'espid'           => $espid,
                'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
                'id_seguro'       => $paciente->id_seguro,
                'estado'          => '1',
                'id_sala'         => $id_sala->id,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_doctor,
                'id_usuariomod'   => $id_doctor,
            ];

            $id_agenda = agenda::insertGetId($input_agenda);


            $consulta_crear_new = [
                'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
                'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
                'id_paciente'     => $id_paciente,
                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            Hc_Log::create($consulta_crear_new);

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '1',
                'estado_cita'     => '1',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_doctor1'      => $id_doctor,
                'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',
                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];

            Log_agenda::create($input_log);

            $input_historia = [

                'parentesco'      => $paciente->parentesco,
                'id_usuario'      => $paciente->id_usuario,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $id_paciente,
                'id_seguro'       => $paciente->id_seguro,
                'id_doctor1'      => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $id_procedimiento_completo = '40';

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_hc_procedimiento = [
                'id_hc'                     => $id_historia,
                'id_seguro'                 => $paciente->id_seguro,
                'id_procedimiento_completo' => $id_procedimiento_completo,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'ip_creacion'               => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            $input_hc_evolucion = [
                'hc_id_procedimiento' => $id_hc_procedimiento,
                'hcid'                => $id_historia,
                'secuencia'           => '0',
                'fecha_ingreso'       => ' ',
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
                'id_usuariocrea'      => $idusuario,
                'ip_creacion'         => $ip_cliente,

            ];
            $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);

            $input_child_pugh = [
                'id_hc_evolucion'       => $id_evolucion,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
            ];

            $id_child = hc_child_pugh::create($input_child_pugh);

            $input_hc_receta = [
                'id_hc'           => $id_historia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,

            ];
            hc_receta::insert($input_hc_receta);


            $arr_solicitud = [
                'id_paciente'           => $request['cedula'],
                'id_agenda'             => $id_agenda,
                'id_seguro'             => $request['id_seguro'],
                'fecha_ingreso'         => date('Y-m-d H:i:s'),
                'estado_paso'           => $id_paso,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
            ];

            $ho_solicitud = Ho_solicitud::insertGetId($arr_solicitud);

            $sol_log = [
                'id_ho_solicitud'       => $ho_solicitud,
                'estado_paso'           => $id_paso,
                'id_agenda'             => $id_agenda,
                'fecha_ingreso'         => date('Y-m-d'),
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
            ];
            Ho_Log_Solicitud::create($sol_log);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id_agenda' => $id_agenda];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }
    }
    
    public function index_modulos($id_paso){
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $salas = Sala::where('estado','1')->where('id_hospital','5')->get();
        return view('hospital/modulos/index',['id_paso' => $id_paso, 'salas' => $salas]);
    }
}
