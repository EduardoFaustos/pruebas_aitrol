<?php

namespace Sis_medico\Http\Controllers\limpieza_pentax;

use Excel;
use Response;
use Image;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\servicios_generales\LimpiezaBanosController;
use Sis_medico\Http\Controllers\servicios_generales\MantenimientoHorarioController;
use Sis_medico\Http\Controllers\servicios_generales\LimpiezaAreaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Agenda;
use Sis_medico\Limpieza;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\Sala;
use Sis_medico\User;
use Sis_medico\LimpiezaPentax;

class LimpiezaPentaxController extends Controller
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
    if (in_array($rolUsuario, array(1, 24)) == false) {
      return true;
    }
  }

  public function index_pentax()
  {


    $sala = Sala::where('id_hospital', 2)
      ->where('estado', 1)
      ->where('proc_consul_sala', 1)
      ->get();
    return view('limpieza_pentax/index', ['sala' => $sala]);
  }

  public function buscar_sala(Request $request)
  {
    //dd($request->all());
    $pacientes = Agenda::where("agenda.estado_cita", "4")
      ->where('agenda.proc_consul', '1')
      ->whereBetween('agenda.fechaini', [$request['fechaa'] . ' 00:00', $request['fechad'] . ' 23:59'])
      ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
      ->join('pentax as px', 'agenda.id', 'px.id_agenda')
      ->where('px.id_sala', $request['sala_id'])
      ->select('agenda.*', 'px.id as id_pentax', 'px.id_agenda as agenda_pentax', 'px.id_sala as pentax_sala')->get();
    return view('limpieza_pentax.vista_nueva', ['pacientes' => $pacientes, 'sala_id' => $request['sala_id']]);
  }

  public function created_pentax(Request $request)
  {

    $paciente       = Paciente::find($request['id']);
    $anestesiologos = User::where('id_tipo_usuario', '9')->where('estado', '1')->get();
    return view('limpieza_pentax.modal_crear', ['id_pentax' => $request['id_pentax'], 'paciente' => $paciente, 'id_sala' => $request['id_sala'], 'anestesiologos' => $anestesiologos]);
  }

  public function uploading($imagen, $idusuario)
  {

    try {
      $extension       = $imagen->getClientOriginalExtension();
      $nuevo_nombre    = "foto_evidencia" . date('YmdHis') . $idusuario . "." . $extension;
      $r1              = Storage::disk('public')->put($nuevo_nombre, \File::get($imagen));
      $image           = Image::make(Storage::disk('public')->get($nuevo_nombre));
      $image->resize(1280, null, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
      });
      Storage::disk('public')->put($nuevo_nombre, (string) $image->encode('jpg', 30));

      return ['err' => false, 'data' => $nuevo_nombre];
    } catch (\Exception $e) {
      dd($e);
      return ['err' => true, 'data' => ''];
    }
  }

  public function save_pentax(Request $request)
  {

    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;
    date_default_timezone_set('America/Guayaquil');
    try {
      if ($request['imagen_antes'] != '' || $request['imagen_antes'] != null) {
        $pathAntes = $this->uploading($request['imagen_antes'], $idusuario);
        if ($pathAntes['err']) {
          return ['err' => true, 'msj' => 'Hubo un error en la subida de la foto'];
        }
      }
      $limpiezaPentax = new LimpiezaPentax;
      $limpiezaPentax->id_paciente = $request['id_paciente'];
      $limpiezaPentax->id_pentax = $request['id_pentax'];
      $limpiezaPentax->id_sala = $request['id_sala'];
      $limpiezaPentax->tipo_desinfeccion = $request['tipo_desinfeccion'];
      $limpiezaPentax->nombre_detergente = $request['nom_detergente'];
      $limpiezaPentax->techo = $request['techo'];
      $limpiezaPentax->otros_equipos = $request['otros_equipos'];
      $limpiezaPentax->observaciones = $request['observacion'];
      $limpiezaPentax->paredes = $request['paredes'];
      $limpiezaPentax->path_antes = $pathAntes['data'];
      $limpiezaPentax->piso = $request['piso'];
      $limpiezaPentax->id_usuariocrea = $idusuario;
      $limpiezaPentax->id_usuariomod = $idusuario;
      $limpiezaPentax->save();

      return redirect()->back();
    } catch (\Exception $e) {
      dd($e);
      return ['err' => true, 'msj' => 'Hubo un error en el guardado'];
    }
  }

  public function updated_pentax(Request $request)
  {
    $user = LimpiezaPentax::find($request['id']);
    return view('limpieza_pentax.modal_editar', ['id' => $user]);
  }

  public function edit_pentax(Request $request)
  {
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;

    try {

      $pentax = LimpiezaPentax::find($request['id_registro']);
      $pathAntes = [];
      if ($pentax->path_despues != '' || $pentax->path_despues != null) {
        $pathAntes = ['data' => $pentax->path_despues];
      }
      if ($request['imagen_despues'] != '' || $request['imagen_despues'] != null) {
        $pathAntes = $this->uploading($request['imagen_despues'], $idusuario);
        if ($pathAntes['err']) {
          return ['err' => true, 'msj' => 'Hubo un error en la subida de la foto'];
        }
      }
      $pentax->tipo_desinfeccion = $request['tipo_desinfeccion'];
      $pentax->nombre_detergente = $request['nom_detergente'];
      $pentax->techo = $request['techo'];
      $pentax->otros_equipos = $request['otros_equipos'];
      $pentax->observaciones = $request['observacion'];
      $pentax->paredes = $request['paredes'];
      $pentax->path_despues = $pathAntes['data'];
      $pentax->piso = $request['piso'];
      $pentax->id_usuariocrea = $idusuario;
      $pentax->id_usuariomod = $idusuario;
      $pentax->save();
      return redirect()->back();
    } catch (\Exception $e) {
      dd($e);
      return ['err' => true, 'msj' => 'Hubo un error en el guardado'];
    }
  }

  public function modal_foto(Request $request)
  {

    $foto = LimpiezaPentax::find($request['id']);
    return view('limpieza_pentax.modal_foto', ['foto' => $foto, 'tipo' => $request['tipo']]);
  }

  public function eleccion_tipo(Request $request)
  {

    $banos = new LimpiezaBanosController;
    $area = new LimpiezaAreaController;
    $tipo = $request['tipo'];
    $id = $request['id'];
    if ($tipo == 1) {
      return  $banos->create($id, $request);
    } else {
      return  $area->created($id, $request);
    }
  }

  public function eleccion_tipo2(Request $request)
  {

    $banos = new LimpiezaBanosController;
    $area = new LimpiezaAreaController;
    $tipo2 = $request['tipo2'];
    $id_2 = $request['id_2'];
    if ($tipo2 == 1) {
      return  $banos->index($id_2, $request);
    } else {
      return  $area->index($id_2, $request);
    }
  }
}
