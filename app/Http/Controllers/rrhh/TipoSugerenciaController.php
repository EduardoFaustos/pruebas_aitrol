<?php

namespace Sis_medico\Http\Controllers\rrhh;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Area;
use Sis_medico\TipoSugerencia;
use Sis_medico\Sugerencia;
use Sis_medico\Pregunta;
use Sis_medico\GrupoPregunta;
use Sis_medico\Master_encuesta;
use Sis_medico\Encuesta_1;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Paciente;
use Sis_medico\Encuesta_Complemento;
use Sis_medico\Encuesta_Complementolabs;
use Excel;
use Sis_medico\Agenda;
use Sis_medico\Sala;


class TipoSugerenciaController extends Controller
{
    protected $redirectTo = '/area';

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
        if(in_array($rolUsuario, array(1, 11)) == false){
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
        $tiposugerencia = TipoSugerencia::paginate(25);
      

        return view('rrhh/tiposugerencia/index', ['tiposugerencia' => $tiposugerencia]);
    }

    public function create()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('rrhh/tiposugerencia/create');
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        TipoSugerencia::create([

            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/tipo_sugerencia');
    }

    private function validateInput($request) {
       $messages = [
        'nombre.required' => 'Agrega el nombre del Area.',
        'descripcion.required' => 'Agrega la descripcion del Area.',
        ];
        
        $constraints = [
        	'nombre' => 'required',
        	'descripcion' => 'required',
        ];

        $this->validate($request, $constraints, $messages);

    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tiposugerencia = TipoSugerencia::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($tiposugerencia == null || count($tiposugerencia) == 0) {
            return redirect()->intended('/tipo_sugerencia');
        }

        return view('rrhh/tiposugerencia/edit', ['tiposugerencia' => $tiposugerencia]);
    }

    public function update(Request $request,  $id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tipo_sugerencia = TipoSugerencia::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del area.',
        'descripcion.required' => 'Agrega la descripcion del area.',
          
        ];

        
        $constraints = [
        'nombre' => 'required',           
        'descripcion' => 'required'

            ];
  
                

        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        TipoSugerencia::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/tipo_sugerencia');
    }

    public function resultados(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $sugerencia = Sugerencia::paginate(25);
        $area = Area::all();
        $tiposugerencia  = TipoSugerencia::all();
        $id_area = null;
        $id_tiposugerencia = null;
        $fecha = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');    

        return view('rrhh/resultados/index', ['sugerencia' => $sugerencia, 'area' => $area,  'tiposugerencia' => $tiposugerencia, 'id_area' => $id_area, 'id_tiposugerencia' => $id_tiposugerencia, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta]);
    }

    public function search(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }


        $id_area = $request['id_area'];
        $id_tiposugerencia = $request['id_tiposugerencia'];;
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];    
        $sugerencia = Sugerencia::where('id_area', 'like', '%'.$id_area.'%')
                            ->where('id_tiposugerencia', 'like', '%'.$id_tiposugerencia.'%')
                            ->whereBetween('created_at', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:59'])
                            ->paginate(25);
        if($request['fecha'] == "" || $request['fecha'] == null){
                $sugerencia = Sugerencia::where('id_area', 'like', '%'.$id_area.'%')
                            ->where('id_tiposugerencia', 'like', '%'.$id_tiposugerencia.'%')
                            ->paginate(25);
        }                   
        $area = Area::all();
        $tiposugerencia  = TipoSugerencia::all();
        

        return view('rrhh/resultados/index', ['sugerencia' => $sugerencia, 'area' => $area,  'tiposugerencia' => $tiposugerencia, 'id_area' => $id_area, 'id_tiposugerencia' => $id_tiposugerencia, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta]);
    }

    public function resultados_ok(){
        if($this->rol()){
            return response()->view('errors.404');
        } 
        
        $grupopregunta = GrupoPregunta::all();
        
        $encuesta = Encuesta_1::get();

        return view('rrhh/listado_encuesta', [ 'encuesta'=>$encuesta, 'grupopregunta'=>$grupopregunta]);
    }

    public function listado_index(){
        $encuestas = Master_encuesta::where('estado', 1)->get();

        return view('rrhh/listado_index',['encuestas'=>$encuestas]);
    }


    public function listado_detalle($id, Request $request){
                
        $grupopregunta = GrupoPregunta::all();

        $anio = $request->anio;
        $mes = $request->mes;

        if (!is_null($anio)) {
            $anio = date("Y");
        }

        if (!is_null($mes)) {
            $mes = date("m");
        }
        
        $encuesta = Encuesta_1::where('id_area')->where('mes', $mes)->where('anio', $anio)->get();

        return view('rrhh/nuevo_listado_encuesta', [ 'encuesta'=>$encuesta, 'grupopregunta'=>$grupopregunta]);
    }

    public function rrhh_estadisticas (Request $request){
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
       
        
        //$anio='2019';
        //$mes='12';
        $encuestas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->get();
        $master_encuestas = Master_encuesta::all();
        $preguntas = Pregunta::where('id_grupopregunta','1')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->get();
        //dd($preguntas->where('id','11')->first()->nombre);
        $preguntas_tiempo = Pregunta::where('id_grupopregunta','2')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*')->where('id_masterencuesta','1')->get();
       
        return view('rrhh.estadisticas',['preguntas'=>$preguntas, 'encuestas'=>$encuestas,'anio'=>$anio,'master_encuestas'=>$master_encuestas,'preguntas_tiempo'=>$preguntas_tiempo,'mes'=>$mes]);
    }
    public function detalle_mes (Request $request){

        //return "hola";
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
       
        $encuestas_consultas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->where('id_area','1')->get();
        $encuestas_procedimientos = Encuesta_1::where('anio',$anio)->where('mes',$mes)->where('id_area','2')->get();
        

        
        $preguntas_consultas = Pregunta::join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('pe.id_masterencuesta','1')->get();
        $preguntas_procedimientos = Pregunta::join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('pe.id_masterencuesta','2')->get();
        //dd($preguntas_procedimientos);

        Excel::create('Detalle de Encuestas', function($excel) use($encuestas_consultas, $preguntas_consultas, $anio, $mes, $preguntas_procedimientos, $encuestas_procedimientos) {

            $excel->sheet('Consulta Agendas', function($sheet) use($encuestas_consultas, $preguntas_consultas, $anio, $mes, $preguntas_procedimientos, $encuestas_procedimientos) {
                
                $sheet->mergeCells('A1:S1');
                $sheet->mergeCells('A2:P2');  

                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $sheet->cell('A1', function($cell) use($mes_letra, $anio){
                    // manipulate the cel
                    $cell->setValue('ENCUESTAS DE CONSULTAS '.$mes_letra.' - '.$anio);
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->cell('A2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->mergeCells('D3:K3');
                $sheet->cell('C3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cont = 1;$i = 4;

                foreach ($preguntas_consultas as $value) {
                    $sheet->mergeCells('D'.$i.':K'.$i);
                    $sheet->cell('C'.$i, function($cell) use($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $i++;$cont++;

                }
                $i++;

                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;$x=1;
                foreach ($preguntas_consultas as $value) {
                     $sheet->cell($arr[$ltr].$i, function($cell) use($ltr,$x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta '.$x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                     $ltr++;$x++;
                }
                $sheet->cell('L'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('IP Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Unidad');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;$cantidad = 1;
                foreach($encuestas_consultas as $val){
                    $fecha_crea = substr($val->created_at, 0,10);
                    $agenda = Agenda::where('id_paciente',$val->id_paciente)->whereBetween('fecha_ini', [$fecha_crea . ' 00:00', $fecha_crea . ' 23:59'])->first();
                    $sala = Sala::where('id',$agenda->id_sala)->first();

                    $sheet->cell('A'.$i, function($cell) use($cantidad) {
                        // manipulate the cel
                        $cell->setValue($cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $paciente = Paciente::find($val->id_paciente);
                    $nombre = '';
                    if(!is_null($paciente)){
                        $nombre = $paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2;
                    }
                    $sheet->cell('B'.$i, function($cell) use($val, $nombre) {
                        // manipulate the cel
                        $cell->setValue($nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                    $ltr = 0;$x=1;
                    foreach ($preguntas_consultas as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1',$val->id)->where('id_pregunta',$p->id)->first();
                        if(!is_null($complemento)){//4-2=>bueno-regular-malo
                            $txt = $complemento->valor;
                            if($complemento->valor=='4'){
                                $txt = 'BUENO';
                            }
                            if($complemento->valor=='3'){
                                $txt = 'REGULAR';
                            }
                            if($complemento->valor=='2'){
                                $txt = 'MALO';
                            }
                        }
                        $sheet->cell($arr[$ltr].$i, function($cell) use($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;$x++;
                    }
                    $sheet->cell('L'.$i, function($cell) use($val) {
                        // manipulate the cel
                        if($val->ip_ingreso == '192.168.80.7'){
                            $cell->setValue("SLIGNA");}
                        elseif ($val->ip_ingreso == '192.168.80.6') {
                           $cell->setValue("ESANCHEZ");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.8') {
                           $cell->setValue("MCIFUENTES");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.9') {
                           $cell->setValue("OGALLARDO");
                        }
                        elseif ($val->ip_ingreso == '192.168.75.216') {
                           $cell->setValue("VZAMBRANO");
                        }
                        elseif ($val->ip_ingreso == '10.0.0.1') {
                           $cell->setValue("WEB");
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('M'.$i, function($cell) use($val) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 

                    $sheet->cell('N'.$i, function($cell) use($sala) {
                        $cell->setValue($sala->hospital->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $cantidad++;$i++;      
                }
                $i= $i+2;
                $sheet->mergeCells('A'.$i.':S'.$i); 
                $sheet->cell('A'.$i, function($cell) use($mes_letra, $anio){
                    // manipulate the cel
                    $cell->setValue('ENCUESTAS DE PROCEDIMIENTOS '.$mes_letra.' - '.$anio);
                    $cell->setFontWeight('bold');
                    
                });
                $i++;
                $sheet->mergeCells('A'.$i.':S'.$i); 
                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                    
                });
                $i++;
                $sheet->mergeCells('D'.$i.':K'.$i);
                $sheet->cell('C'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;$cont = 1;
                foreach ($preguntas_procedimientos as $value) {
                    $sheet->mergeCells('D'.$i.':K'.$i);
                    $sheet->cell('C'.$i, function($cell) use($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $i++;$cont++;

                }
                $i++;

                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;$x=1;
                foreach ($preguntas_procedimientos as $value) {
                     $sheet->cell($arr[$ltr].$i, function($cell) use($ltr,$x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta '.$x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                     $ltr++;$x++;
                }
                $sheet->cell('K'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('IP Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Unidad');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;$cantidad = 1;
                foreach($encuestas_procedimientos as $val){
                    $fecha_crea = substr($val->created_at, 0,10);
                    $agenda = Agenda::where('id_paciente',$val->id_paciente)->whereBetween('fecha_ini', [$fecha_crea . ' 00:00', $fecha_crea . ' 23:59'])->first();
                    $sala = Sala::where('id',$agenda->id_sala)->first();
                    $sheet->cell('A'.$i, function($cell) use($cantidad) {
                        // manipulate the cel
                        $cell->setValue($cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $paciente = Paciente::find($val->id_paciente);
                    $nombre = '';
                    if(!is_null($paciente)){
                        $nombre = $paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2;
                    }
                    $sheet->cell('B'.$i, function($cell) use($val, $nombre) {
                        // manipulate the cel
                        $cell->setValue($nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                    $ltr = 0;$x=1;
                    foreach ($preguntas_procedimientos as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1',$val->id)->where('id_pregunta',$p->id)->first();
                        if(!is_null($complemento)){//4-2=>bueno-regular-malo
                            $txt = $complemento->valor;
                            if($complemento->valor=='4'){
                                $txt = 'BUENO';
                            }
                            if($complemento->valor=='3'){
                                $txt = 'REGULAR';
                            }
                            if($complemento->valor=='2'){
                                $txt = 'MALO';
                            }
                        }
                        $sheet->cell($arr[$ltr].$i, function($cell) use($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;$x++;
                    }
                    $sheet->cell('K'.$i, function($cell) use($val) {
                        // manipulate the cel
                        if($val->ip_ingreso == '192.168.80.7'){
                            $cell->setValue("SLIGNA");}
                        elseif ($val->ip_ingreso == '192.168.80.6') {
                           $cell->setValue("ESANCHEZ");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.8') {
                           $cell->setValue("MCIFUENTES");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.9') {
                           $cell->setValue("OGALLARDO");
                        }
                        elseif ($val->ip_ingreso == '192.168.75.216') {
                           $cell->setValue("VZAMBRANO");
                        }
                        elseif ($val->ip_ingreso == '10.0.0.1') {
                           $cell->setValue("WEB");
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('L'.$i, function($cell) use($val) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('M'.$i, function($cell) use($sala) {
                        $cell->setValue($sala->hospital->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cantidad++;$i++;      
                }
                                 
                    
                    
            });
        })->export('xlsx');
        
        
       
        
    }

    public function encuesta_estadistica(){
        
        $encuestas = Master_encuesta::where('estado', 1)->get();
        return view('rrhh/encuestas_estadistica',['encuestas' => $encuestas]);
    }

    public function rrhh_estadisticas_2 (Request $request, $id){
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
       
        
        //$anio='2019';
        //$mes='12';
        $encuestas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->get();
        $master_encuestas = Master_encuesta::find($id);
        $preguntas = Pregunta::where('id_grupopregunta','1')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('id_masterencuesta', $id)->get();
        //dd($preguntas->where('id','11')->first()->nombre);
        $preguntas_tiempo = Pregunta::where('id_grupopregunta','2')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id)->get();
       
        return view('rrhh/prueba_estadisticas',['preguntas'=>$preguntas, 'encuestas'=>$encuestas,'anio'=>$anio,'master_encuestas'=>$master_encuestas,'preguntas_tiempo'=>$preguntas_tiempo,'mes'=>$mes, 'id' => $id]);
    }

    public function detalle_mes_2 (Request $request,$id){

        //return "hola";
        $anio=$request['anio'];
        $mes=$request['mes'];
        $fecha = $request->fecha;

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
        $master_encuestas = Master_encuesta::find($id);
        if($fecha != null){
            $encuestas = Encuesta_1::whereBetween('created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])->where('id_area', $id)->get();
        }else{
            $encuestas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->where('id_area', $id)->get();
        }
        $preguntas = Pregunta::join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('pe.id_masterencuesta',$id)->get();
        
        //dd($preguntas_procedimientos);

        Excel::create('Detalle de Encuestas', function($excel) use($encuestas, $preguntas, $anio, $mes, $master_encuestas) {

            $excel->sheet('Consulta Agendas', function($sheet) use($encuestas, $preguntas, $anio, $mes, $master_encuestas) {
                
                $sheet->mergeCells('A1:S1');
                $sheet->mergeCells('A2:P2');  

                if($mes == '01'){ $mes_letra = "ENERO";} 
                if($mes == '02'){ $mes_letra = "FEBRERO";} 
                if($mes == '03'){ $mes_letra = "MARZO";} 
                if($mes == '04'){ $mes_letra = "ABRIL";} 
                if($mes == '05'){ $mes_letra = "MAYO";} 
                if($mes == '06'){ $mes_letra = "JUNIO";} 
                if($mes == '07'){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $sheet->cell('A1', function($cell) use($mes_letra, $anio, $master_encuestas){
                    // manipulate the cel
                    $cell->setValue($master_encuestas->descripcion.' '.$mes_letra.' - '.$anio);
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->cell('A2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->mergeCells('D3:K3');
                $sheet->cell('C3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cont = 1;$i = 4;

                foreach ($preguntas as $value) {
                    $sheet->mergeCells('D'.$i.':K'.$i);
                    $sheet->cell('C'.$i, function($cell) use($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $i++;$cont++;

                }
                $i++;

                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;$x=1;
                foreach ($preguntas as $value) {
                     $sheet->cell($arr[$ltr].$i, function($cell) use($ltr,$x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta '.$x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                     $ltr++;$x++;
                }
                $sheet->cell('L'.$i, function($cell) use($ltr,$x){
                    // manipulate the cel
                    $cell->setValue('IP Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M'.$i, function($cell) use($ltr,$x) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N'.$i, function($cell) use($ltr,$x) {
                    // manipulate the cel
                    $cell->setValue('Unidad');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;$cantidad = 1;

                foreach($encuestas as $val){
                    $fecha_crea = substr($val->created_at, 0,10);
                    // dd($val);
                    //dd($fecha_crea);
                        $agenda = Agenda::where('id_paciente',$val->id_paciente)->whereBetween('fechaini', [$fecha_crea . ' 00:00:00', $fecha_crea . ' 23:59:59'])->first();
                        //dd($agenda);

                        if(!is_null($agenda)){
                           $sala = Sala::where('id',$agenda->id_sala)->first();
                           if(!is_null($sala)){
                           // dd($sala);
                        //dd($sala);
                        $sheet->cell('A'.$i, function($cell) use($cantidad) {
                            // manipulate the cel
                            $cell->setValue($cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $paciente = Paciente::find($val->id_paciente);
                        $nombre = '';
                        if(!is_null($paciente)){
                            $nombre = $paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2;
                        }
                        $sheet->cell('B'.$i, function($cell) use($val, $nombre) {
                            // manipulate the cel
                            $cell->setValue($nombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }); 
                        $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                        $ltr = 0;$x=1;
                    
                    foreach ($preguntas as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1',$val->id)->where('id_pregunta',$p->id)->first();
                        if(!is_null($complemento)){//4-2=>bueno-regular-malo
                            $txt = $complemento->valor;
                            if($complemento->valor=='5'){
                                $txt = 'EXCELENTE';
                            }
                            if($complemento->valor=='4'){
                                $txt = 'MUY BUENO';
                            }
                            if($complemento->valor=='3.5'){
                                $txt = 'BUENO';
                            }
                            if($complemento->valor=='3'){
                                $txt = 'NI BUENO NI MALO';
                            }
                            if($complemento->valor=='2.5'){
                                $txt = 'MALO';
                            }
                            if($complemento->valor=='1'){
                                $txt = 'MUY MALO';
                            }
                            if($complemento->valor=='otros'){
                                $txt = 'Otros - '.$complemento->valor2;
                            }

                        }
                        $sheet->cell($arr[$ltr].$i, function($cell) use($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;$x++;
                    }
                    $sheet->cell('L'.$i, function($cell) use($val, $ltr, $x) {
                        // manipulate the cel
                        /*if($val->ip_ingreso == '192.168.80.7'){
                            $cell->setValue("SLIGNA");}
                        elseif ($val->ip_ingreso == '192.168.80.6') {
                           $cell->setValue("ESANCHEZ");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.8') {
                           $cell->setValue("MCIFUENTES");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.9') {
                           $cell->setValue("OGALLARDO");
                        }
                        elseif ($val->ip_ingreso == '192.168.75.216') {
                           $cell->setValue("VZAMBRANO");
                        }
                        elseif ($val->ip_ingreso == '10.0.0.1') {
                           $cell->setValue("WEB");
                        }*/
                        $cell->setValue("");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('M'.$i, function($cell) use($val, $ltr, $x) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 

                    $sheet->cell('N'.$i, function($cell) use($sala) {
                        $cell->setValue($sala->hospital->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cantidad++;$i++;      
                } 
              }
            }      
        });
        })->export('xlsx');
        
        
       
        
    }
    
}
