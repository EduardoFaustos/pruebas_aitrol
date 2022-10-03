<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\TipoProveedor;

class ProveedorController extends Controller
{
    protected $redirectTo = '/dashboard';

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
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 7)) == false){
          return true;
        }
    }
    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }
        $proveedores = DB::table('proveedor')->join('tipoproveedor', 'proveedor.id_tipoproveedor', '=', 'tipoproveedor.id')->select('proveedor.*', 'tipoproveedor.nombre')->paginate('5');
        return view('insumos/proveedor/index', ['proveedores' => $proveedores]);
    }

    public function create(){
    	if($this->rol()){
            return response()->view('errors.404');
        }

        $tipos = TipoProveedor::all();
        return view('insumos/proveedor/create', ['tipos' => $tipos] );

    }

    public function store(Request $request)
    {

       
        $reglas = [
            'id' => 'required|max:13|min:13|unique:empresa',
            'razonsocial' => 'required|max:255',
            'nombrecomercial' => 'required|max:255',
            'ciudad' => 'required|max:60',
            'direccion' => 'required|max:255', 
            'email' => 'required|email|max:191',
            'telefono1' => 'required|numeric|max:9999999999',
            'telefono2' => 'required|numeric|max:9999999999',
            'estado' => 'required',
            'id_tipo_proveedor' => 'required',
        ];

        $mensajes = [
            'id.unique' => 'El RUC ya se encuentra registrado.',
            'id.required' => 'Agrega el RUC.',
            'id.max' =>'El RUC no puede ser mayor a :max caracteres.',
            'id.min' =>'El RUC no puede ser menor a :min caracteres.',
            'razonsocial.required' => 'Agrega la razón social.',
            'razonsocial.max' =>'La razon social no puede ser mayor a :max caracteres.',
            'nombrecomercial.required' => 'Agrega el nombre comercial.',
            'nombrecomercial.max' =>'El nombre comercial no puede ser mayor a :max caracteres.',
            'apellido2.required' => 'Agrega el segundo apellido.',
            'apellido2.max' =>'El segundo apellido no puede ser mayor a :max caracteres.',
            'ciudad.required' => 'Agrega la ciudad.',
            'ciudad.max' =>'La ciudad no puede ser mayor a :max caracteres.',
            'direccion.required' => 'Agrega la direccion.',
            'direccion.max' =>'La direccion no puede ser mayor a :max caracteres.',
            'email.required' => 'Agrega el Email.',
            'email.max' =>'El Email no puede ser mayor a :max caracteres.',
            'email.email' =>'El Email tiene error en el formato.',
            'telefono1.required' => 'Agrega el teléfono domicilio del usuario.',
            'telefono1.max' =>'El teléfono domicilio del usuario no puede ser mayor a 10 caracteres.',
            'telefono1.numeric' =>'El telefono domicilio del usuario debe ser numerico.',
            'telefono2.required' => 'Agrega el teléfono celular del usuario.',
            'telefono2.max' =>'El teléfono celular del usuario no puede ser mayor a 10 caracteres.',
            'telefono2.numeric' =>'El telefono cellular del usuario debe ser numérico.',   
            'estado.required' => 'Agrega el estado.',    
            'id_tipo_proveedor.required' => 'Agrega el tipo de proveedor.',                        
        ];
 
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $this->validateInput($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');

        $proveedor_id = Proveedor::where('id', $request['id'])->first();

        if(is_null($proveedor_id)){

            Proveedor::create([
                'id' => $request['id'],
                'razonsocial' => strtoupper($request['razonsocial']),
                'nombrecomercial' => strtoupper($request['nombrecomercial']),
                'ciudad' => strtoupper($request['ciudad']),
                'direccion' => strtoupper($request['direccion']),
                'email' => $request['email'],
                'telefono1' => $request['telefono1'],
                'telefono2' => $request['telefono2'],
                'id_tipoproveedor' => $request['id_tipo_proveedor'],
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario
            ]);
            
        }
        return redirect()->intended('/proveedor');
    }

    private function validateInput($request) {
	    $this->validate($request,[]);
	}
    

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        $proveedor = DB::table('proveedor')->join('tipoproveedor', 'proveedor.id_tipoproveedor', '=', 'tipoproveedor.id')->select('proveedor.*', 'tipoproveedor.nombre')->where('proveedor.id', '=', $id)->get();

        // Redirect to user list if updating user wasn't existed
        if ($proveedor == null || count($proveedor) == 0) {
            return redirect()->intended('/dashboard');
        }
        $tipos = TipoProveedor::all();
        return view('insumos/proveedor/edit', ['proveedor' => $proveedor, 'tipos' => $tipos]);
    }

    public function update(Request $request, $id)
    {
        $empresa = Proveedor::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $mensajes = [

            'id.unique' => 'El RUC ya se encuentra registrado.',
            'id.required' => 'Agrega el RUC.',
            'id.max' =>'El RUC no puede ser mayor a :max caracteres.',
            'id.min' =>'El RUC no puede ser menor a :min caracteres.',

            'razonsocial.required' => 'Agrega la razón social.',
            'razonsocial.max' =>'La razon social no puede ser mayor a :max caracteres.',

            'nombrecomercial.required' => 'Agrega el nombre comercial.',
            'nombrecomercial.max' =>'El nombre comercial no puede ser mayor a :max caracteres.',
       
            'apellido2.required' => 'Agrega el segundo apellido.',
            'apellido2.max' =>'El segundo apellido no puede ser mayor a :max caracteres.',

            'ciudad.required' => 'Agrega la ciudad.',
            'ciudad.max' =>'La ciudad no puede ser mayor a :max caracteres.',

            'direccion.required' => 'Agrega la direccion.',
            'direccion.max' =>'La direccion no puede ser mayor a :max caracteres.',

            'email.required' => 'Agrega el Email.',
            'email.max' =>'El Email no puede ser mayor a :max caracteres.',
            'email.email' =>'El Email tiene error en el formato.',

            'telefono1.required' => 'Agrega el teléfono domicilio del usuario.',
            'telefono1.max' =>'El teléfono domicilio del usuario no puede ser mayor a 10 caracteres.',
            'telefono1.numeric' =>'El telefono domicilio del usuario debe ser numerico.',
            'telefono2.required' => 'Agrega el teléfono celular del usuario.',
            'telefono2.max' =>'El teléfono celular del usuario no puede ser mayor a 10 caracteres.',
            'telefono2.numeric' =>'El telefono cellular del usuario debe ser numérico.',
            'id_tipo_proveedor.required' => 'Agrega el tipo de proveedor.', 
            'estado.required' => 'Agrega el estado.',

            ];        
        $constraints = [
            'id' => 'required|max:13|min:13|unique:empresa,id,'.$id,
            'razonsocial' => 'required|max:255',
            'nombrecomercial' => 'required|max:255',
            'ciudad' => 'required|max:60',
            'direccion' => 'required|max:255', 
            'email' => 'required|email|max:191',
            'telefono1' => 'required|numeric|max:9999999999',
            'telefono2' => 'required|numeric|max:9999999999',
            'estado' => 'required',
            'id_tipo_proveedor' => 'required',
            ];

            $prov = Proveedor::where('id', $request['id'])->first();
     
            if(!is_null($prov)){
                 $input = [
                //'id' => $request['id'],
                'razonsocial' => strtoupper($request['razonsocial']),
                'nombrecomercial' => strtoupper($request['nombrecomercial']),
                'ciudad' => strtoupper($request['ciudad']),
                'direccion' => strtoupper($request['direccion']),
                'email' => $request['email'],
                'telefono1' => $request['telefono1'],
                'telefono2' => $request['telefono2'],
                'id_tipoproveedor' => $request['id_tipo_proveedor'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario
                ];
            }
            else{
                
                $input = [
                'id' => $request['id'],
                'razonsocial' => strtoupper($request['razonsocial']),
                'nombrecomercial' => strtoupper($request['nombrecomercial']),
                'ciudad' => strtoupper($request['ciudad']),
                'direccion' => strtoupper($request['direccion']),
                'email' => $request['email'],
                'telefono1' => $request['telefono1'],
                'telefono2' => $request['telefono2'],
                'id_tipoproveedor' => $request['id_tipo_proveedor'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario
                ];
            }

           
       $this->validate($request, $constraints, $mensajes);
  
        Proveedor::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/proveedor');
    }

    public function subir_logo(Request $request)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $id=$request['logo'];
        $reglas = ['archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:900',];
        $mensajes = [

            'archivo.required' => 'Agrega el Logo.',
            'archivo.image' => 'El logo debe ser una imagen.',
            'archivo.mimes' =>'Los archivos permitidos son: jpeg,png,jpg,gif,svg.',
            'archivo.max' =>'El peso del logo no puede ser mayor a :max KB.', ];
       
        $this->validate($request, $reglas, $mensajes);
  
        $nombre_original=$request['archivo']->getClientOriginalName();
        $extension=$request['archivo']->getClientOriginalExtension();
        $nuevo_nombre="logo_proveedor".$id.".".$extension;
            
        $r1=Storage::disk('logo')->put($nuevo_nombre,  \File::get($request['archivo']) );

        $rutadelaimagen=$nuevo_nombre;
        if ($r1){
   
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $empresa=proveedor::find($id);
            $empresa->logo=$rutadelaimagen;
            $empresa->ip_modificacion=$ip_cliente;
            $empresa->id_usuariomod=$idusuario;
            $r2=$empresa->save();
               
            return redirect()->intended('/proveedor');
          }
    }

    public function search(Request $request) {
        //return $request->all();
        if($this->rol()){
            return response()->view('errors.404');
        }
        $constraints = [
            'id' => $request['ruc'],
            'razonsocial' => $request['razonsocial']
            ];


       $proveedor = $this->doSearchingQuery($constraints);

       return view('insumos/proveedor/index', ['proveedores' => $proveedor, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Proveedor::query()->join('tipoproveedor', 'proveedor.id_tipoproveedor', '=', 'tipoproveedor.id')->select('proveedor.*', 'tipoproveedor.nombre');
        $fields = array_keys($constraints);
        
        $index = 0;
        
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( 'proveedor.'.$fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        
        return $query->paginate(5);
    }



}
