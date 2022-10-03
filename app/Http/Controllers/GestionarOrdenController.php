<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Response;
use Storage;
use Sis_medico\Orden;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Sis_medico\User;
use Sis_medico\Com_Gestion_Orden;
use Sis_medico\Seguro;
use Carbon\Carbon;


class GestionarOrdenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //dd($request->all());  
        $desde = $request->desde;
        $hasta = $request->hasta;
        $tipo  = $request->tipo;
        $nombre_paciente = $request->id;

        if ($desde == null) {
            $desde = date('Y-m-d');
        }
        if ($hasta == null) {
            $hasta = date('Y-m-d');
        }

        $ordenes = DB::table('orden as o')
            ->join('paciente as p', 'p.id', 'o.id_paciente')
            ->join('orden_tipo as ot', 'ot.id_orden', 'o.id')
            ->wherebetween('o.fecha_orden', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupby('o.fecha_orden');
        //la funcion de groupby esta a prueba     
        //dd($ordenes->get());
        if (!is_null($tipo)) {
            $ordenes = $ordenes->where('o.tipo_procedimiento', $request['tipo']);
        }

        if ($nombre_paciente != null) {
            $nombres2 = explode(" ", $nombre_paciente);
            $cantidad = count($nombres2);
            $nombres_sql = "";
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > '1') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {
                $ordenes = $ordenes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        $ordenes = $ordenes->select('o.*', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ot.id as tiporden')->orderBy('o.fecha_orden', 'desc')->paginate(50);
        return view('ordenes_gestion/index', ['ordenes' => $ordenes, 'tipo' => $tipo, 'desde' => $desde, 'hasta' => $hasta, 'nombre_paciente' => $nombre_paciente]);
    }

    public function cantidad()
    {

        $fecha = date('Y-m-d');
        $observaciones = DB::table('orden')->join('orden_tipo as ot', 'orden.id', 'ot.id_orden')->whereBetween('fecha_orden', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->get();
        return $observaciones->count();
    }

    public function editar_gestion($id)
    {
        $segurox = Seguro::where('tipo', 1)->get();
        //dd($seguro);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden = Orden::find($id);
        $seguro = $orden->paciente->seguro;//dd($seguro);
        $id_seguro = $seguro->id;
        $mail = $seguro->mail;
        //dd($listacorreo);
        //RXISTE POR NUMERO DE ORDEN
        $gestion = Com_Gestion_Orden::where('id_orden',$id)->where('estado',1)->first();
        //SI NO EXISTE --- CREAS CON INSERT GET ID
        if(is_null($gestion)){
            $array = [
                'id_seguro'       => $id_seguro,
                'id_orden'        => $id,
                'id_paciente'     => $orden->id_paciente,
                'estado'          => '1',
                'email_seguro'    => $mail,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario, 
            ];
            $id_gestion = Com_Gestion_Orden::insertGetid($array);
            $gestion = Com_Gestion_Orden::find($id_gestion);
        }
        //ELSE ---- OBTENER ID
        //FIND SOBRE GESTION
        return view('ordenes_gestion/modal', ['orden' => $orden, 'gestion' => $gestion, 'segurox' => $segurox, 'mail' => $mail]);
    }

    public function guardar_gestion(Request $request)
    {
        //dd($request->all());  
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        Com_Gestion_Orden::create([

            'id_paciente' => $request['id_paciente'],
            'id_seguro' => $request['id_seguro'],
            'id_orden' => $request['id_orden'],
            'email_seguro' => $request['email_seguro'],
            'estado' => $request['estado'],
            'observacion' => $request['observacion'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
       return response()->json('ok');
       
    }

    public function cargar_correo (Request $request)
    {

        $segurox = Seguro::where('id', $request['seguro'])->first();

        return json_encode($segurox);

    }

}