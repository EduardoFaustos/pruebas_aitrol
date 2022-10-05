<?php

namespace Sis_medico\Http\Controllers\ticket_permisos;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Nomina as Sis_medicoCt_Nomina;
use Sis_medico\Empresa;
use Sis_medico\User;
use Sis_medico\TipoUsuario;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\Ct_Nomina;
use Sis_medico\TicketPermiso;
use Mail;
use Excel;
use Sis_medico\Ct_Rh_Valores;

class TicketPermisosController extends Controller
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
        $idusuario = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $trabajadores = null;
        $tipo_permisos = null;

        $desde   = null;
        $hasta   = null;
        $cedula  = null;
        $permiso = null;
        $estado  = '-1';


        $datos = [];
        if ($rolUsuario  == 1 || $rolUsuario == 20) {
            $datos = TicketPermiso::where('estado_solicitud', $estado)->where('estado', '1')->orderBy('created_at', 'DESC')->paginate(50);
        } else {

            $datos = TicketPermiso::where('cedula', $idusuario)->get();
        }

        return view('ticket_permiso/index', ['datos' => $datos, 'trabajadores' => $trabajadores, 'tipo_permisos' => $tipo_permisos, 'desde' => $desde, 'hasta' => $hasta, 'cedula' => $cedula, 'permiso' => $permiso, 'estado' => $estado]);
    }
    public function create()
    {

        $idusuario  = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;

        $empresa_nombres = Empresa::where('estado', 1)->get();


        if ($rolUsuario == '20' || $rolUsuario == '1') {
            return view('ticket_permiso/create_con_nomina', ['empresa_nombres' => $empresa_nombres]);
        }
        /*
        $valida = Sis_medicoCt_Nomina::where('id_user', $idusuario)->first();
        if (!is_null($valida)) {
            return view('ticket_permiso/create', ['valida' => $valida]);
        } else {
            return view('ticket_permiso/create_con_nomina');
        }*/
    }

    public function verificar(Request $request)
    {
        $valida = Sis_medicoCt_Nomina::where('id_user', $request['id'])->where('estado', 1)->first();
        if (!empty($valida)) {
            return json_encode($valida);
        } else {
            return json_encode('no');
        }
    }
    public function save(Request $request)
    {
        //dd($request->all());
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;


        $ultimo = TicketPermiso::latest()->first();;
       
        $serie = 0;
        if (!is_null($ultimo)) {
            $sum = $ultimo->id + 1;
            $serie = str_pad($sum, 10, "0", STR_PAD_LEFT);
        } else {
            $serie1 = 1;
            $serie =  str_pad($serie1, 10, "0", STR_PAD_LEFT);
        }



        date_default_timezone_set('America/Guayaquil');

        $variable =   [
            'fecha_registro' => $hoy,
            'cedula'  => $request['datos_nomina']['id_user'],
            'cargo' => $request['datos_nomina']['cargo'],
            'departamento' => $request['datos_nomina']['area'] == 1 ? 'ADMINISTRATIVA' : 'MEDICA',
            'tipo_permiso' => $request['todo'][4]['permiso'],
            'fecha_desde' => $request['todo'][0]['fecha_desde'],
            'fecha_hasta' => $request['todo'][1]['fecha_hasta'],
            'ora_salida' => $request['todo'][2]['sala'],
            'ora_ingresa' => $request['todo'][3]['ingresa'],
            'hora_ingresomar' => $request['todo'][2]['ingreso'],
            'hora_salidamar' => $request['todo'][3]['salida'],
            'observaciones' => $request['todo'][5]['observaciones'],
            'id_usuario_crea' => $idusuario,
            'no_solicitud'  => $serie,
        ];



        try {
            if (is_null($request->id)) {
                TicketPermiso::create($variable);
            } else {
                TicketPermiso::where('id', $request['id'])->update(
                    $variable
                );
            }
        } catch (\Throwable $th) {
            return json_encode($th);
        }

        return json_encode('ok');
    }



    public function subir_pdf(Request $request)
    {
        //$id_no = NULL;
        //dd($request->all());
        $id_permiso = $request->id;
        $permiso = TicketPermiso::find($id_permiso);
        if (!is_null($permiso)) {
            /*if(!is_null($request['userfile']) or $request['userfile']!= "" or $request['userfile']!= "undefined"){*/
            if ($request['userfile'] != "undefined") {
                $nombre_archivo  = $request['userfile']->getClientOriginalName();
                $extension       = $request['userfile']->getClientOriginalExtension();
                $nuevo_nombre    = $permiso->id . "_" . date('YmdHis') . "." . $extension;
                $r5 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['userfile']));
                //dd($r5);
                $permiso->update([
                    'ruta_archivo' => $nuevo_nombre,
                ]);
                return json_encode("ok");
            }
        }

        return json_encode("no");
    }


    public function subir_pdf1(Request $request)
    {   //dd($request->all());
        $id_no = NULL;
        if ($request->userfile == "undefined") {
            return json_encode($id_no);
        } else {
            if (!is_null($request['userfile']) or $request['userfile'] != "" or $request['userfile'] != "undefined") {
                //dd("aqui");
                $nombre_archivo = $request['userfile']->getClientOriginalName();
                $nuevo_nombre  = $nombre_archivo;
                $r5 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['userfile']));

                $variable =   TicketPermiso::create([
                    'ruta_archivo' => $nuevo_nombre,
                ]);

                return json_encode($variable->id);
            }
        }
    }

    public function save_sin_dato(Request $request)
    {
        //dd($request->all());
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        //$ultimo = TicketPermiso::latest()->first();;
        $serie = 0;

        $usuario = User::find($request->cedula);

        date_default_timezone_set('America/Guayaquil');
        $this->mail_permisos(1);
        $ct_datos_empresa = Sis_medicoCt_Nomina::where('id_user', $request['cedula'])->where('estado', 1)->first();
        if (is_null($ct_datos_empresa)) {

            $datos_nomina_inactivos = [

                'id_user'                   => $request->cedula,
                'nombres'                   => strtoupper($usuario->nombre1) . " " . strtoupper($usuario->nombre2) . " " . strtoupper($usuario->apellido1) . " " . strtoupper($usuario->apellido2),
                'id_empresa'                => $request['id_empresa'],
                'area'                      => '1',
                'cargo'                     => $request['cargo'],
                'estado'                    => '0',
                'pago_fondo_reserva'        => '1',
                'aporte_personal'           => '1',
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,

            ];

            // dd($datos_nomina_inactivos);

            Sis_medicoCt_Nomina::create($datos_nomina_inactivos);
        }


        $variable =  [
            'fecha_registro' => $request->fecha,
            'cedula'         => $request->cedula,
            'cargo'          => $request->cargo,
            'departamento'   => $request->area,
            'tipo_permiso'   => $request->permiso,
            'fecha_desde'    => $request->desde,
            'fecha_hasta'    => $request->hasta,
            'ora_salida'     => $request->sale,
            'ora_ingresa'    => $request->ingresa,
            'hora_ingresomar' => $request->ingreso,
            'hora_salidamar' => $request->salida,
            'observaciones'  => $request->observaciones,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'  => $idusuario,
            'no_solicitud'   => $serie,
            'ip_creacion'    => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];


        $usuario = User::find($request->cedula);

        $nomina  = Sis_medicoCt_Nomina::where('id_user', $request->cedula)->where('estado', 1);

        $usuario->update(['cargo' => $request->cargo, 'departamento' => $request->area, 'servicios' => $request->servicios]);
        $nomina->update(['cargo' => $request->cargo]);

        $clave = TicketPermiso::insertGetId($variable);

        return ['msn' => 'ok', 'id' => $clave];
    }

    public function editar($id)
    {
        $registro = TicketPermiso::where('id', $id)->first();
        return view('ticket_permiso/edit', ['registro' => $registro]);
    }


    public function editar_datos(Request $request)
    {
        //dd($request->all());
        $idusuario = Auth::user()->id;
        $registro = TicketPermiso::find($request->id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $registro->update([
            //'fecha_registro'  => $request['todo'][8]['fecha_registro'],
            //'cedula' => $request['todo'][12]['cedula'],
            //'cargo' => $request['cargo'],
            //'departamento' => $request['todo'][10]['departamento'],
            //'tipo_permiso' => $request['todo'][4]['permiso'],
            //'fecha_desde' => $request['todo'][0]['fecha_desde'],
            //'fecha_hasta' => $request['todo'][1]['fecha_hasta'],
            //'ora_salida' => $request['todo'][2]['sala'],
            //'ora_ingresa' => $request['todo'][3]['ingresa'],
            //'hora_ingresomar' => $request['todo'][2]['ingreso'],
            //'hora_salidamar' => $request['todo'][3]['salida'],
            //'observaciones' => $request['todo'][5]['observaciones'],
            'id_usuariomod'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'estado_solicitud' => $request->estado,
            'justificacion_final' => $request->obs_acepta,
        ]);

        $usuario = $registro->nombre;
        $usuario->update(['servicios' => $request->servicios]);

        return json_encode('ok');
    }


    public function buscador(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde   = $request->desde;
        $hasta   = $request->hasta;
        $cedula  = $request->id_usuario;
        $permiso = $request->permiso;
        $estado  = $request->estado;
        $soportes = TicketPermiso::where('created_at', '>', '2021-12-01 00:00:00')->where('estado', 1);
        if ($request['excel'] == 0) {


            //dd($soportes->get());
            if ($desde != null) {
                $soportes = $soportes->where('fecha_registro', '>', $desde . ' 00:00:00');
            }

            if ($hasta != null) {
                $soportes = $soportes->where('fecha_registro', '<', $hasta . ' 00:00:00');
            }

            if ($estado < '2') {
                if ($estado == 0) {
                    $soportes = $soportes->where('estado_solicitud', '-1');
                } else {
                    $soportes = $soportes->where('estado_solicitud', '>', '-1');
                }
            }

            if ($cedula != null) {
                $soportes = $soportes->where('cedula', $cedula);
            }

            if ($permiso != null) {
                $soportes = $soportes->where('tipo_permiso', $permiso);
            }

            $soportes = $soportes->paginate(50);

            $tipo_permisos = TicketPermiso::all();
            //$trabajadores = User::where('id_tipo_usuario', '<>',2)->where('estado', 1)->orderby('apellido1')->get();
            $trabajadores = null;
            return view('ticket_permiso/index', ['datos' => $soportes, 'trabajadores' => $trabajadores, 'tipo_permisos' => $tipo_permisos, 'desde' => $desde, 'hasta' => $hasta, 'cedula' => $cedula, 'permiso' => $permiso, 'estado' => $estado]);
        } else {
            //dd($request->all());
            $titulos = array("FECHA","CEDULA" ,"USUARIO", "SOLICITUD", "MOTIVO DE PERMISO", "DEPARTAMENTO","OLVIDO DE MARCACION, ATRASOS Y TELETRABAJO (Ingreso/ Salida)", "PERMISOS EN DIAS (Fecha desde / Fecha hasta)","PERMISOS EN HORAS (Sale/Ingresa)","APROBACIÓN", "ESTADO", "OBSERVACION",  "JUSTIFICACIÓN");
            //Posiciones en el excel
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            if ($desde != null) {
                $soportes = $soportes->where('fecha_registro', '>', $desde . ' 00:00:00');
            }

            if ($hasta != null) {
                $soportes = $soportes->where('fecha_registro', '<', $hasta . ' 00:00:00');
            }

            if ($estado < '2') {
                if ($estado == 0) {
                    $soportes = $soportes->where('estado_solicitud', '-1');
                } else {
                    $soportes = $soportes->where('estado_solicitud', '>', '-1');
                }
            }

            if ($cedula != null) {
                $soportes = $soportes->where('cedula', $cedula);
            }

            if ($permiso != null) {
                $soportes = $soportes->where('tipo_permiso', $permiso);
            }

            $soportes = $soportes->get();

            $tipo_permisos = TicketPermiso::all();
            //$trabajadores = User::where('id_tipo_usuario', '<>',2)->where('estado', 1)->orderby('apellido1')->get();

            Excel::create('Solicitud de permisos laborales', function ($excel) use ($titulos, $posicion, $soportes, $request, $tipo_permisos) {
                $excel->sheet('Solicitud de Permiso', function ($sheet) use ($titulos, $posicion, $soportes, $request, $tipo_permisos) {
                    $sheet->mergeCells('A1:G1');
                    $sheet->cell('A1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('SOLICITUD DE PERMISO LABORALES');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $comienzo = 2; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL

                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    /*****FIN DE TITULOS DEL EXCEL***********/


                    foreach ($soportes as $soporte) {


                        //dd($days);
                        $datos_excel = array();
                        $estado = ["-1" => "POR APROBAR", "1" => "APROBADO", "0" => "NO APROBADO"];
                        $estado_2 = ["-1" => "POR ATENDER", "1" => "ATENDIDO", "0" => "ATENDIDO"];
                        array_push($datos_excel, substr($soporte->fecha_registro, 0, 11), $soporte->cedula ,"{$soporte->nombre->nombre1} {$soporte->nombre->nombre2} {$soporte->nombre->apellido1} {$soporte->nombre->apellido2}",$soporte->id, $soporte->tipo_permiso, $soporte->departamento,"{$soporte->hora_ingresomar} - {$soporte->hora_salidamar} ","{$soporte->fecha_desde} - {$soporte->fecha_hasta}","{$soporte->ora_salida} -  {$soporte->ora_ingresa}",$estado["{$soporte->estado_solicitud}"], $estado_2["{$soporte->estado_solicitud}"], $soporte->observaciones,$soporte->justificacion_final);


                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                });
            })->export('xlsx');
        }
        //dd($request->all());

    }

    public function ver_pdf(Request $request)
    {

        $nombreArchivo = TicketPermiso::where('id', $request['id'])->first();
        $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->ruta_archivo;
        // dd($path1);
        return response()->file($path1);
    }

    public function vh_buscar_usuario(Request $request)
    {
        
        $nombres = $request['term'];

        $nombres2 = explode(" ", $nombres);
        $cantidad = count($nombres2);

        $usuarios = User::where('estado', '1');

        if($request['tipo'] == 7){

            $usuarios = $usuarios->where('id_tipo_usuario','<>',2);

        }

        if ($cantidad == '2' || $cantidad == '3') {

            $usuarios = $usuarios->where(function ($jq1) use ($nombres) {
                $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1) LIKE ?', ['%' . $nombres . '%']);
            });
        } else {

            $usuarios = $usuarios->whereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', ['%' . $nombres . '%']);
        }

        $usuarios = $usuarios->get();

        $data      = array();

        foreach ($usuarios as $usuario) {
            $data[] = array('value' => $usuario->apellido1 . ' ' . $usuario->apellido2 . ' ' . $usuario->nombre1 . ' ' . $usuario->nombre2, 'id' => $usuario->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function vh_buscar_nomina($id)
    {

        $nomina = Sis_medicoCt_Nomina::where('id_user', $id)->where('estado', 1)->first();
        $usuario = User::find($id);

        if (!is_null($usuario)) {
            if (!is_null($nomina)) {
                return json_encode(['cargo' => $nomina->cargo, 'departamento' => $usuario->departamento, 'servicios' => null, 'empresa' => $nomina->empresa->nombrecomercial, 'tiene_nomina' => 1]);
            } else {
                return json_encode(['cargo' => $usuario->cargo, 'departamento' => $usuario->departamento, 'servicios' => $usuario->servicios, 'empresa' => null, 'tiene_nomina' => 0]);
            }
        } else {
            return json_encode(['cargo' => null, 'departamento' => null, 'servicios' => null, 'empresa' => null, 'tiene_nomina' => null]);
        }
    }

    public function pdf_permiso($id, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $registro = TicketPermiso::where('id', $id)->first();
        // $empresa = Empresa::where('id', $comp_egreso->id_empresa)->first();
        $vistaurl = "ticket_permiso.permisos_pdf";
        $view     = \View::make($vistaurl, compact('empresa', 'registro'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function buscador_usuarios(Request $request)
    {


        $idusuario = Auth::user()->id;
        //dd($request->all());
        $desde   = $request->desde;
        $hasta   = $request->hasta;
        $cedula  = $request->id_usuario;
        $permiso = $request->permiso;
        $estado  = $request->estado;
        //dd($estado);

        $soportes = TicketPermiso::query();
        //dd($soportes->get());
        if ($desde != null) {
            $soportes = $soportes->where('fecha_registro', '>', $desde . ' 00:00:00');
        }

        if ($hasta != null) {
            $soportes = $soportes->where('fecha_registro', '<', $hasta . ' 00:00:00');
        }
        //dd($estado);
        if (is_null($estado)) {
            $soportes = $soportes->whereIn('estado_solicitud', [0, 1, -1]);
        } elseif ($estado == 2) {
            $soportes = $soportes->whereIn('estado_solicitud', [0, 1, -1]);
        } elseif ($estado == 1) {
            $soportes = $soportes->whereIn('estado_solicitud', [0, 1]);
        } else {
            $soportes = $soportes->where('estado_solicitud', -1);
        }

        $soportes = $soportes->where('cedula', $idusuario)->orderBy('created_at', 'DESC')->paginate(50);

        $tipo_permisos = TicketPermiso::all();

        $trabajadores = null;
        return view('ticket_permiso/index_usuario', ['datos' => $soportes, 'trabajadores' => $trabajadores, 'tipo_permisos' => $tipo_permisos, 'desde' => $desde, 'hasta' => $hasta, 'cedula' => $cedula, 'permiso' => $permiso, 'estado' => $estado]);
    }
    public function create_usuario()
    {
        $idusuario  = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;


        return view('ticket_permiso/create_usuario');
    }
    public function editar_usuario($id)
    {

        $registro = TicketPermiso::where('id', $id)->first();
        if ($registro->estado_solicitud == 0 || $registro->estado_solicitud == 1) {

            return redirect()->route('ticketpermisos.index_usuario');
        }

        return view('ticket_permiso/edit_usuario', ['registro' => $registro]);
    }

    public function editar_datos_usuarios(Request $request)
    {
        // dd($request->all());
        $idusuario = Auth::user()->id;
        $registro = TicketPermiso::find($request->id_registro);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $registro->update([
            //'fecha_registro'  => $request['todo'][8]['fecha_registro'],
            //'cedula' => $request['todo'][12]['cedula'],
            'cargo' => $request['cargo'],
            'departamento' => $request['departamento'],
            'tipo_permiso' => $request['permiso'],
            'fecha_desde' => $request['desde'],
            'fecha_hasta' => $request['hasta'],
            'ora_salida' => $request['ora_salida'],
            'ora_ingresa' => $request['ora_ingresa'],
            'hora_ingresomar' => $request['ingreso'],
            'hora_salidamar' => $request['salida'],
            'observaciones' => $request['observaciones'],
            'id_usuariomod'  => $idusuario,
            'ip_modificacion' => $ip_cliente,

        ]);

        $usuario = $registro->nombre;
        $usuario->update(['servicios' => $request->servicios]);

        return 'ok';
    }
    public function mail_permisos($id)
    {
       
        $permiso = TicketPermiso::find($id);
        $correo  = $permiso->nombre->email;
        $array   = ['nombre' => $permiso->nombre->apellido1 . ' ' . $permiso->nombre->apellido2 . ' ' . $permiso->nombre->nombre1 . ' ' . $permiso->nombre->nombre2, 'estado_solicitud' => $permiso->estado_solicitud, 'justificacion_final' => $permiso->justificacion_final];
        Mail::send('ticket_permiso.mail', $array, function ($msj) use ($correo) {
            $msj->subject('Respuesta de Solicitud de Permiso');
            $msj->to($correo);
            $msj->from('alexo8ec@hotmail.com');
            $msj->bcc('torbi10@hotmail.com');
        });

        return redirect()->back();
    }

}
