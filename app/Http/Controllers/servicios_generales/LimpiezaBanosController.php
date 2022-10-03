<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Image;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Limpieza_Banos;
use Sis_medico\Mantenimientos_Generales;
use Sis_medico\User;
use Sis_medico\Mantenimientos_Dotacion;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Mantenimientos_Insumos_Limpieza;
use Sis_medico\Insumos_utilizados_banos;
use Sis_medico\InsumosUtilizadosArea;
use Sis_medico\Mantenimientos_Banos;

class LimpiezaBanosController extends Controller
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
        if (in_array($rolUsuario, array(1, 24, 4, 6)) == false) {
            return true;
        }
    }

    public function index($id, Request $request)
    {

        $id_sala = $id;
        $sala = Mantenimientos_Generales::find($id_sala);
        $fecha_desde  = date('Y-m-d');
        $fecha_hasta  = date('Y-m-d');
        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $rolUsuario   = Auth::user()->id_tipo_usuario;
        $id_auth      = Auth::user()->id;
        $permisos = Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();



        $control_limp = [];
        $arrayEnvio   = array();
        $array        = array();
        $aa           = [];
        if ($rolUsuario == 24 || $rolUsuario == 6) {
            $control_limp = Limpieza_Banos::whereBetween('fecha', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->where('nombre_piso', $id_sala)->where('responsable', $id_auth)->paginate(20);
        }
        // elseif ($rolUsuario == 11) {



        //     $usuarioLog      = Ct_Nomina::where('id_user', $id_auth)->first();
        //     $usuarioLimpieza = Limpieza_Banos::where('estado', 1)->where('nombre_piso', $id_sala)->whereBetween('fecha', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->get();

        //     $ctNomina        = array();
        //     foreach ($usuarioLimpieza as $value) {
        //         $hh = Usuario_Limpieza::where('usuid', $value->id_usuariocrea)->first();
        //         if (!is_null($hh)) {
        //             $ctNomina[] = $hh;
        //         }
        //     }


        //     $elementosLimpios[] = array_filter($ctNomina);

        //     if (!is_null($usuarioLog)) {
        //         if (count($elementosLimpios) > 0) {
        //             for ($i = 0; $i < count($elementosLimpios); $i++) {
        //                 for ($j = 0; $j < count($elementosLimpios[$i]); $j++) {
        //                     if ($elementosLimpios[$i][$j]->id_empresa == $usuarioLog->id_empresa) {
        //                         $aa[] = Limpieza_Banos::where('id_usuariocrea', $elementosLimpios[$i][$j]->usuid)->first();
        //                     }
        //                 }
        //             }
        //             $control_limp = $aa;
        //         }
        //     }

        // }
        else {

            //dd('entra');
            $control_limp = Limpieza_Banos::where('nombre_piso', $id_sala)->whereBetween('fecha', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->paginate(20);
        }

        //dd($control_limp);
        return view('servicios_generales/limpieza_banos/index', ['control_limp' => $control_limp, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'sala' => $sala, 'id_sala' => $id_sala]);
    }

    public function salas()
    {
        $nombre_piso = Mantenimientos_Generales::all();
        return view('servicios_generales/limpieza_banos/index_2', ['nombre_piso' => $nombre_piso]);
    }
    public function create($id_sala, Request $request)
    {
        $piso = Mantenimientos_Generales::findorfail($id_sala);
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        $pisoBa = Mantenimientos_Banos::where('id_unidad', $id_sala)->get();
        return view('servicios_generales/limpieza_banos/create', ['pisoBa' => $pisoBa, 'productos' => $productos, 'insumos' => $insumos, 'id_sala' => $id_sala, 'piso' => $piso]);
    }

    public function pisos()
    {
        $pisos = Mantenimientos_Generales::all();
        return view('servicios_generales/limpieza_banos/pisos', ['pisos' => $pisos]);
    }

    public function guardar(Request $request)
    {


        $hoy        = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_empresa = $request->session()->get('id_empresa');
        $idusuario  = Auth::user()->id;

        Limpieza_Banos::create([
            'fecha'           => $hoy,
            'nombre_piso'     => $request['nombre_piso'],
            'desinfectante'   => $request['desinfectante'],
            'evidencia_antes' => $request['evidencia_antes'],
            // 'evidencia_desp'  => $request['evidencia_desp'],
            'observacion'     => $request['observaciones'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'responsable'     => $idusuario,
        ]);

        return json_encode("ok");
    }
    public function buscar_fecha(Request $request)
    {
        //dd($request->all());
        $fecha_desde  = $request['desde'];
        $fecha_hasta  = $request['hasta'];

        $id_sala = $request['id_sala'];
        $sala = Mantenimientos_Generales::find($id_sala);
        $control_limp = Limpieza_Banos::where('nombre_piso', $id_sala)->whereBetween('fecha', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59'])->paginate(10);
        $id_auth      = Auth::user()->id;
        $permisos = Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
        $control_limp = [];
        $array        = array();
        $aa           = [];
        $rolUsuario   = Auth::user()->id_tipo_usuario;
        if ($rolUsuario == 24) {
            $control_limp = Limpieza_Banos::whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('nombre_piso', $id_sala)->where('responsable', $id_auth)->paginate(10);
        }
        // elseif ($rolUsuario == 11|| !is_null($permisos)) {
        //     $usuarioLog      = Ct_Nomina::where('id_user', $id_auth)->first();
        //     $usuarioLimpieza = Limpieza_Banos::where('estado', 1)->where('nombre_piso', $id_sala)->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get();
        //     //dd($usuarioLimpieza);
        //     $ctNomina = array();
        //     foreach ($usuarioLimpieza as $value) {
        //         $hh = Usuario_Limpieza::where('usuid', $value->id_usuariocrea)->first();
        //         if (!is_null($hh)) {
        //             $ctNomina[] = $hh;
        //         }

        //     }
        //     $elementosLimpios[] = array_filter($ctNomina);
        //     if (!is_null($usuarioLog)) {
        //         if (count($elementosLimpios) > 0) {
        //             for ($i = 0; $i < count($elementosLimpios); $i++) {
        //                 for ($j = 0; $j < count($elementosLimpios[$i]); $j++) {
        //                     if ($elementosLimpios[$i][$j]->id_empresa == $usuarioLog->id_empresa) {
        //                         $aa[] = Limpieza_Banos::where('id_usuariocrea', $elementosLimpios[$i][$j]->usuid)->whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
        //                         ->where('nombre_piso', $id_sala)
        //                         ->first();
        //                     }
        //                 }
        //             }
        //             $control_limp = $aa;
        //             //dd($control_limp);
        //         }
        //     }
        // } 
        else {
            $control_limp = Limpieza_Banos::whereBetween('fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('nombre_piso', $id_sala)->paginate(10);
        }
        return view('servicios_generales/limpieza_banos/index', ['sala' => $sala, 'control_limp' => $control_limp, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_sala' => $id_sala]);
    }

    public function subir_imagen(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $nuevo_nombre  = "";
        $nuevo_nombre2 = "";
        $id            = $request['id']; // puede ser el id de la tabla en la base de datos
        if (!empty($request['evidencia_antes'])) {
            $nombre_original = $request['evidencia_antes']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension       = $request['evidencia_antes']->getClientOriginalExtension();
            $nuevo_nombre    = "limpiezaant_" . date('YmdHis') . $idusuario . "." . $extension;
            $r1              = Storage::disk('public')->put($nuevo_nombre, \File::get($request['evidencia_antes']));
            $image           = Image::make(Storage::disk('public')->get($nuevo_nombre));

            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::disk('public')->put($nuevo_nombre, (string) $image->encode('jpg', 30));

            //$image = Image::make(Storage::get($path));
        }
        if (!empty($request['evidencia_desp'])) {
            $nombre_original2 = $request['evidencia_desp']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension2       = $request['evidencia_desp']->getClientOriginalExtension();

            $nuevo_nombre2 = "limpiezades_" . date('YmdHis') . $idusuario . "." . $extension2;
            $r2            = Storage::disk('public')->put($nuevo_nombre2, \File::get($request['evidencia_desp']));

            $image = Image::make(Storage::disk('public')->get($nuevo_nombre2));
            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::disk('public')->put($nuevo_nombre2, (string) $image->encode('jpg', 30));

            //$image = Image::make(Storage::get($path));
        }
        //aqui creamos el registro
        $ty =  Limpieza_Banos::create([
            'responsable'     => $idusuario,
            'fecha'           => date('Y-m-d H:i:s'),
            'frecuencia1'     => date('Y-m-d H:i:s'),
            'nombre_piso'     => $request['nombre_piso'],
            'desinfectante'   => $request['desinfectante'],
            'evidencia_antes' => $nuevo_nombre,
            //'evidencia_desp'  => $nuevo_nombre2,
            'observacion'     => $request['observaciones'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'id_piso'  => $request['piso'],
            'limpieza'  => $request['limpieza'],
        ]);


        foreach ($request['insumos'] as $value) {

            $registro = new Insumos_utilizados_banos;
            $registro->id_limpieza_banos = $ty->id;
            $registro->id_insumos_banos = $value;
            $registro->id_usuariocrea = $idusuario;
            $registro->id_usuariomod = $idusuario;
            $registro->save();
        }

        foreach ($request['producto'] as $value) {

            $registro = new InsumosUtilizadosArea;
            $registro->id_limpieza_area = $ty->id;
            $registro->id_insumos = $value;
            $registro->save();
        }
        /*    $editar = DB::table('limpieza_banos')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]); //  */ //esto es en el edit pero estamos en el create asi que no tenemos el id del formulatio
        return redirect()->route('limpieza_banos.index_2', ['id' => $request['nombre_piso']]);
    }
    public function editar($id)
    {
        $control_limp = Limpieza_Banos::where('id', $id)->first();
        $nombre_piso  = Mantenimientos_Generales::all();
        $pisoBa = Mantenimientos_Banos::where('id_unidad', $control_limp->id_piso)->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        return view('servicios_generales.limpieza_banos.edit', ['insumos' => $insumos, 'productos' => $productos, 'pisoBa' => $pisoBa, 'control_limp' => $control_limp, 'id' => $id, 'nombre_piso' => $nombre_piso]);
    }

    public function actualizar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id               = $request['id']; // puede ser el id de la tabla en la base de datos
        $nombre_original  = "";
        $nombre_original2 = "";
        //dd($request->all());
        $control_limp = Limpieza_Banos::where('id', $request['id'])->first();
        if (!is_null($request['evidencia_antes'])) {
            //dd($request['evidencia_antes']);
            $nombre_original = $request['evidencia_antes']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension       = $request['evidencia_antes']->getClientOriginalExtension();
            $r1              = Storage::disk('public')->put($nombre_original, \File::get($request['evidencia_antes']));
        } else {
            $nombre_original = $control_limp->evidencia_antes;
        }
        if (!is_null($request['evidencia_desp'])) {
            $nombre_original2 = $request['evidencia_desp']->getClientOriginalName(); //request name file es el nombre del input file de la vista pero es el primera foto
            $extension2       = $request['evidencia_desp']->getClientOriginalExtension();
            $r1               = Storage::disk('public')->put($nombre_original2, \File::get($request['evidencia_desp']));
            $rutadelaimagen   = $nombre_original; //dd($request->all());
        } else {
            $nombre_original2 = $control_limp->evidencia_desp;
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //dd($control_limp);
        $control_limp->responsable     = $idusuario;
        $control_limp->desinfectante   = $request['desinfectante'];
        $control_limp->evidencia_desp  = $nombre_original2;
        $control_limp->observacion     = $request['observaciones'];
        $control_limp->ip_creacion     = $ip_cliente;
        $control_limp->ip_modificacion = $ip_cliente;
        $control_limp->id_usuariocrea  = $idusuario;
        $control_limp->id_usuariomod   = $idusuario;
        $control_limp->save();

        return redirect()->back();
    }
    public function agregarhoras(Request $request)
    {
        $hoy      = date("Y-m-d H:i:s");
        $horarios = Limpieza_Banos::where('id', $request['id'])->first();
        if ($horarios->frecuencia1 == null) {
            $horarios->frecuencia1 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } elseif ($horarios->frecuencia2 == null) {
            $horarios->frecuencia2 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } elseif ($horarios->frecuencia3 == null) {
            $horarios->frecuencia3 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } elseif ($horarios->frecuencia4 == null) {
            $horarios->frecuencia4 = $hoy;
            $horarios->save();
            return json_encode('ok');
        } else {
            $array = array('lleno', $request['id']);
            return json_encode($array);
        }
    }

    public function excel(Request $request)
    {
        //dd($request->all());

        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $tipo         = $request['tipo'];
        $fecha_desde  = $request['desde'];
        $fecha_hasta  = $request['hasta'];
        $control_limp = Limpieza_Banos::query();
        if (!is_null($request['hasta']) && !is_null($request['desde'])) {
            $control_limp = $control_limp->whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if (!is_null($request['encargados'])) {

            $control_limp = $control_limp->where('responsable', $request['encargados']);
        }
        if (!is_null($request['nombre_piso'])) {
            $control_limp = $control_limp->where('nombre_piso', $request['nombre_piso']);
        }

        $control_limp = $control_limp->where('nombre_piso', $request['id'])->get();

        //dd($request->all());


        Excel::create('REGISTRO DE LIMPIEZA Y DESINFECCION DE BAÑOS Vers. 0.1', function ($excel) use ($control_limp, $request, $empresa,$tipo) {
            $excel->sheet('REPORTE LIMPIEZA', function ($sheet) use ( $request, $control_limp, $empresa, $tipo) {

                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:B2');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(100);
                    $objDrawing->setWidth(100);
                    $objDrawing->setWorksheet($sheet);
                }
                $sheet->mergeCells('C1:R1');
                $sheet->cell('C1', function ($cell) use ($empresa, $tipo) {
                    if (!is_null($empresa)) {
                        $cell->setValue($tipo == 1 ? $empresa->razonsocial : 'CARLOS ROBLES MEDRANDA');
                    }
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('C2:R2');
                $sheet->cell('C2', function ($cell) {
                    $cell->setValue('REGISTRO DE LIMPIEZA Y DESINFECCION DE BAÑOS');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S1:T2');
                $sheet->cell('S1', function ($cell) {
                    $cell->setValue('Vers. 0.1');
                    $cell->setFontColor('#000000'); 
                    $cell->setBackground('#0499DB');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:A4');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N°');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('B3:B4');
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BAÑO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('C3:D4');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FRECUENCIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('E3:F4');
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA INICIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('G3:H4');
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA FIN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('I3:J3');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO DE DESINFECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('I4:I4');
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCURRENTE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('J4:J4');
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TERMINAL ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('K3:L4');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSUMOS ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('M3:N4');
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOTACIÓN ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('O3:P4');
                $sheet->cell('O3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESPONSABLE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('Q3:R4');
                $sheet->cell('Q3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('S3:T4');
                $sheet->cell('S3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIONES ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = 5;

                foreach ($control_limp as $value) {


                   

                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->responsable);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });


                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->observacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    
                }
               
                /*****FIN DE TITULOS DEL EXCEL***********/
              
            });
        })->export('xlsx');
    }

    public function modal_foto(Request $request)
    {
        $foto = Limpieza_Banos::where('id', $request['id'])->first();
        //dd($request);
        return view('servicios_generales.limpieza_banos.modal_foto', ['foto' => $foto, 'tipo' => $request['tipo']]);
    }
    public function vistareportes(Request $request)
    {


        $nombre_piso = Mantenimientos_Generales::where('estado', '1')->get();

        $encargados = User::where('id_tipo_usuario', '24')->get();

        return view('servicios_generales.limpieza_banos.nuevo_reporte', ['nombre_piso' => $nombre_piso, 'encargados' => $encargados, 'id' => $request['id_sala']]);
    }
}
