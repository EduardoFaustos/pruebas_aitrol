<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Sala;
use Sis_medico\Camilla;
use Sis_medico\Hospital;
use Mail;
use Session;
use Illuminate\Support\Facades\Auth; 

class CamillaManagementController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/camilla-management';

         /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4)) == false){
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
        if($this->rol()){
            return response()->view('errors.404');
        }
        $camillas = Camilla::paginate(5);
      

        return view('camilla-mgmt/index', ['camillas' => $camillas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('camilla-mgmt/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Camilla::create([

            'nombre_camilla' => strtoupper($request['nombre_camilla']),
            'id_hospital' => $request['id_hospital'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/camilla-management/{hospital}/listascamillas');
    }

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
        if($this->rol()){
            return response()->view('errors.404');
        }
        $camillas = Camilla::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($camillas == null || count($camillas) == 0) {
            return redirect()->intended('/camilla-management');
        }

        return view('camilla-mgmt/edit', ['camillas' => $camillas]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $camillas = Camilla::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre_camilla.required' => 'Agrega el nombre de la camilla.',
        'nombre_camilla.max' =>'El nombre de la camilla no puede ser mayor a :max caracteres.',
        'id_hospital.required' => 'Agrega el hospital.',
        'estado.required' => 'Agrega el estado.',
          
        ];

        
        $constraints = [
        'nombre_camilla' => 'required|max:30',
        'id_hospital' => 'required',            
        'estado' => 'required'
            ];
  
                

        $input = [
            'nombre_camilla' => strtoupper($request['nombre_camilla']),
            'id_hospital' => $request['id_hospital'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        Camilla::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/camilla-management');
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
     public function search(Request $request) {
        $constraints = [
            'nombre_camilla' => $request['nombre_camilla'],
            
            ];

       $camillas = $this->doSearchingQuery($constraints);
     
       return view('camilla-mgmt/index', ['ca$camillas' => $camillas, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Camilla::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(5);
    }

    private function validateInput($request) {
       $messages = [
        'nombre_camilla.required' => 'Agrega el nombre de la camilla.',
        'nombre_camilla.max' =>'El nombre de la camilla no puede ser mayor a :max caracteres.',

          
        ];

        
        $constraints = [
        'nombre_camilla' => 'required|max:30',

            ];

        $this->validate($request, $constraints, $messages);

    }

    public function listascamillas($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }       
        $camillas = Camilla::where('id_hospital', '=', $id)->paginate(5);
        $hospital = Hospital::find($id);
   

        $nombre_hospital = $hospital->nombre_hospital;
      
        return view('camilla-mgmt/listascamillas', ['camillas' => $camillas, 'hospital' => $hospital]);
    }

    public function crear($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $hospital = Hospital::find($id);
        return view('camilla-mgmt/crearcamilla', ['hospital' => $hospital]);
    }

    public function grabar(Request $request, $id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Camilla::create([

            'nombre_camilla' => strtoupper($request['nombre_camilla']),
            'id_hospital' => $request['id_hospital'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        $camillas = Camilla::where('id_hospital', '=', $id)->paginate(5);
        $hospital = Hospital::find($id);
   

        $nombre_hospital = $hospital->nombre_hospital;
        return view('camilla-mgmt/listascamillas', ['camillas' => $camillas, 'hospital' => $hospital]);
    }

    public function editar($id_hospital, $id_camilla)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $camillas = Camilla::find($id_camilla);
        // Redirect to user list if updating user wasn't existed
        if ($camillas == null || count($camillas) == 0) {
            //return redirect()->intended('/Camilla-management');
        }
        $hospital = Hospital::find($id_hospital);
   

        $nombre_hospital = $hospital->nombre_hospital;

        return view('camilla-mgmt/editar', ['camillas' => $camillas, 'hospital' => $hospital]);
    }

   public function actualizar(Request $request, $id_hospital, $id_camilla)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $camillas = Camilla::findOrFail($id_camilla);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre_camilla.required' => 'Agrega el nombre de la camilla.',
        'nombre_camilla.max' =>'El nombre de la camilla no puede ser mayor a :max caracteres.',
        'estado.required' => 'Agrega el estado.',
          
        ];

        
        $constraints = [
        'nombre_camilla' => 'required|max:30',           
        'estado' => 'required'
            ];
  
                

        $input = [
            'nombre_camilla' => strtoupper($request['nombre_camilla']),
            'id_hospital' => $request['id_hospital'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        Camilla::where('id', $id_camilla)
            ->update($input);
        
        $camillas = Camilla::where('id_hospital', '=', $id_hospital)->paginate(5);
        $hospital = Hospital::find($id_hospital);
   

        $nombre_hospital = $hospital->nombre_hospital;
        return view('camilla-mgmt/listascamillas', ['camillas' => $camillas, 'hospital' => $hospital]);
    }

    
}
