<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Sis_medico\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\User;
use Sis_medico\pais;
use Sis_medico\especialidad;
use Sis_medico\user_espe;
use Sis_medico\tipousuario;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Horario_Doctor;

class HorarioController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user-management';

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
   
    public function index()
    {
        $idusuario = Auth::user()->id;
        $usuario = User::find($idusuario);
        $horarios = Horario_Doctor::where('id_doctor',$idusuario)->orderBy('ndia', 'asc')->get();


       return view('horario/index', ['id' => $idusuario, 'usuario' => $usuario, 'horarios' => $horarios]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioRequest $request)
    {
         
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
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
         return redirect()->intended('/user-management');
    }

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {

         
    }
    

private function validateInput($request) {
        


    $this->validate($request,[]);


}

    
    public function creahorario(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $reglas = ['dia' => 'required|in:LU,MA,MI,JU,VI,SA,DO,TD',
                    'hora_ini' => 'required',
                    'hora_fin' => 'required'
            ];
        
        $mensajes = [
                'dia.required' => 'Selecciona el día.',
                'dia.in' => 'Selecciona el día correcto.',
                'hora_ini.required' => 'Selecciona la hora de inicio.',
                'hora_fin.required' => 'Selecciona la hora de fin.',
                ];

        $this->validate($request, $reglas, $mensajes); 

        if($request['dia']=='TD')
        {
            for($x=1; $x<6; $x++)
            {
                if($x==1)
                {
                    $dia='LU';
                }
                elseif($x==2)
                {
                    $dia='MA';
                }
                elseif($x==3){
                    $dia='MI';
                }
                elseif($x==4)
                {
                    $dia='JU';
                }
                elseif($x==5)
                {
                    $dia='VI';
                }

                $this->validatehorario3($request,$id,$dia); 
            } 

            for($y=1; $y<6; $y++)
            {

                if($y==1)
                {
                    $dia='LU';

                }
                elseif($y==2)
                {
                    $dia='MA';

                }
                elseif($y==3)
                {
                    $dia='MI';
                    
                }
                elseif($y==4)
                {
                    $dia='JU';

                }
                elseif($y==5)
                {
                    $dia='VI';
                }
                
                $input = [
                    'dia' => $dia,
                    'ndia' => $y,
                    'hora_ini' => $request['hora_ini'],
                    'hora_fin' => $request['hora_fin'],
                    'id_doctor' => $id,

                    'ip_creacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
            
                ];
                

                Horario_Doctor::create($input);

            }
                    

        }
        else{
            if($request['dia']=='LU'){
                $ndia=1;
            }elseif($request['dia']=='MA'){
                $ndia=2;
            }elseif($request['dia']=='MI'){
                $ndia=3;
            }elseif($request['dia']=='JU'){
                $ndia=4;
            }elseif($request['dia']=='VI'){
                $ndia=5;
            }elseif($request['dia']=='SA'){
                $ndia=6;
            }
            elseif($request['dia']=='DO'){
                $ndia=7;
            }

            

            $this->validatehorario($request,$id);

             $input = [
                'dia' => $request['dia'],
                'ndia' => $ndia,
                'hora_ini' => $request['hora_ini'],
                'hora_fin' => $request['hora_fin'],
                'id_doctor' => $id,

                'ip_creacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            
            ];

            Horario_Doctor::create($input);

        }

        
        
        return redirect()->intended('/agenda');
        
    }

    private function validatehorario(Request $request, $id_doctor)
    {
        

        $ini2 = date_create($request['hora_ini']);
        $fin2 = date_create($request['hora_fin']);
        
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

        $inicio = date_format($inicio, 'H:i:s');
        $fin = date_format($fin, 'H:i:s');
         
        $dato = Horario_Doctor::where('id_doctor',$id_doctor)->where('dia', $request['dia'])
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN hora_ini and hora_fin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN hora_ini and hora_fin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(hora_ini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("hora_fin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();        

        $cantidad = $dato->count();



        $reglas = [
                    'hora_ini' => 'comparahoras:'.$request['hora_fin'],
                    'hora_fin' => 'comparahoras:'.$request['hora_ini'],
                    'dia' => 'unique_doctor:'.$cantidad,
                    
            ];

        
        $mensajes = [
            'hora_ini.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'dia.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
             ];

        $this->validate($request, $reglas, $mensajes); 
        
    }

    private function validatehorario3(Request $request, $id_doctor, $dia)
    {
        

        $ini2 = date_create($request['hora_ini']);
        $fin2 = date_create($request['hora_fin']);
        
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

        $inicio = date_format($inicio, 'H:i:s');
        $fin = date_format($fin, 'H:i:s');
         
        $dato = Horario_Doctor::where('id_doctor',$id_doctor)->where('dia', $dia)
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN hora_ini and hora_fin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN hora_ini and hora_fin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(hora_ini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("hora_fin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();        

        $cantidad = $dato->count();



        $reglas = [
                    'hora_ini' => 'comparahoras:'.$request['hora_fin'],
                    'hora_fin' => 'comparahoras:'.$request['hora_ini'],
                    'dia' => 'unique_doctor:'.$cantidad,
                    
            ];

        
        $mensajes = [
            'hora_ini.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'dia.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
             ];

        $this->validate($request, $reglas, $mensajes); 
        
    }

    public function editahorario(Request $request, $id)
    {
        
        
        $user = User::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $horarios = Horario_Doctor::where('id_doctor',$id)->get();
       /* $reglas = ['dia' => 'required|in:LU,MA,MI,JU,VI,SA,DO,TD',
                    'hora_ini' => 'required',
                    'hora_fin' => 'required'
            ];
        
        $mensajes = [
            'dia.required' => 'Selecciona el día.',
            'dia.in' => 'Selecciona el día correcto.',
            'hora_ini.required' => 'Selecciona la hora de inicio.',
            'hora_fin.required' => 'Selecciona la hora de fin.',
             ];

        $this->validate($request, $reglas, $mensajes); */

        if(!is_null($horarios)){
            foreach($horarios as $horario){

                $this->validatehorario2($request, $horario->id, $horario->id_doctor, $horario->dia);
                
                
                $estado=$request['estado'.$horario->id];
                if(is_null($estado)){
                    $estado=0;
                }

                $input = [
                'hora_ini' => $request['hora_ini'.$horario->id],
                'hora_fin' => $request['hora_fin'.$horario->id],
                'estado' => $estado,

                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            
                ];
                
                Horario_doctor::find($request['hid'.$horario->id])->update($input);       
            } 
             
        }
        

        
        
        return redirect()->intended('/agenda');
        
    }

    private function validatehorario2(Request $request, $hid, $id_doctor, $dia)
    {
        
        if($request['estado'.$hid]==1){
            $ini2 = date_create($request['hora_ini'.$hid]);
            $fin2 = date_create($request['hora_fin'.$hid]);
        
            $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
            $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

            $inicio = date_format($inicio, 'H:i:s');
            $fin = date_format($fin, 'H:i:s');
         
            $dato = Horario_Doctor::where('id_doctor',$id_doctor)->where('dia', $dia)->where('id','<>',$hid)
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN hora_ini and hora_fin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN hora_ini and hora_fin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(hora_ini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("hora_fin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();        

            $cantidad = $dato->count();
            


        }
        else{ $cantidad=0; }
                

        $reglas = [
                    'hora_ini'.$hid => 'comparahoras:'.$request['hora_fin'.$hid].'|unique_doctor:'.$cantidad,
                    'hora_fin'.$hid => 'comparahoras:'.$request['hora_ini'.$hid].'|unique_doctor:'.$cantidad,                              
            ];

        
        $mensajes = [
            'hora_ini'.$hid.'.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
             'hora_fin'.$hid.'.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
             'hora_ini'.$hid.'.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
             'hora_fin'.$hid.'.unique_doctor' => 'El rango de Horario ya se encuentra incluido .'
             ];


           $this->validate($request, $reglas, $mensajes);  
        
        
    }

    




}
