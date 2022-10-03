<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Empresa;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Agenda;
use Sis_medico\UserMembresia;
use Sis_medico\Log_usuario;
use Sis_medico\Http\Controllers\ApiFacturacionController;

class BigDataController extends Controller
{
    public function _consulta(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $elemento = $request->getContent();
                //var_dump($elemento);exit();
                $data = json_decode($elemento, true);
                $empresa  = Empresa::where('id', 'like', '%%')->first();
                return json_encode($empresa);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }

    public function consulta(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $elemento = json_decode($request->getContent(), true);
                $desde = $request['desde'];
                if (is_null($desde)) {
                    $desde = date("Y-m-d", strtotime(date('2022-01-01')));
                }

                $hasta = $request['hasta'];
                if (is_null($hasta)) {
                    $hasta = date('Y-m-d');
                }

                $tipo = $elemento['tipo'];

                if (is_null($tipo)) {
                    $tipo = 0;
                }

                $ordenes = Agenda::where('agenda.estado', '1')
                    ->wherebetween('agenda.fechaini', [$desde . " 00:00:00", $hasta . " 23:59:59"])
                    ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                    ->join('seguros as s', 's.id', 'ov.id_seguro')
                    ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
                    //->groupby('s.id_seguro_tipos', DB::raw('MONTH(agenda.fechaini)'))
                    ->where("s.id_seguro_tipos", "<>", "null")
                    ->where('st.id', '!=', '1')
                    ->where('ov.estado', '1');



                if ($tipo == 0) {
                    $ordenes = $ordenes->where('proc_consul', '0');
                }

                if ($tipo == 1) {
                    $ordenes = $ordenes->where('proc_consul', '1');
                }


                $meses_consul = $ordenes->select(DB::raw('MONTH(agenda.fechaini) as mes'))->groupby(DB::raw('MONTH(agenda.fechaini)'))->get();
                $api = [];
                $meses = array('Todos', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                foreach ($meses_consul as $value) {
                    $data = BigDataController::armarApi($tipo, $value->mes, $desde, $hasta);
                    $mes = [
                        "mes"       => $meses[$value->mes],
                        "details"   => $data,
                        "id_mes"    => $value->mes
                    ];
                    array_push($api, $mes);
                }
                return json_encode($api);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }

    public static function sqlQuery($tipo = 0, $desde, $hasta, $seguro_tipo, $mes)
    {
        $ordenes2 = Agenda::where('agenda.estado', '1')
            ->wherebetween('agenda.fechaini', [$desde . " 00:00:00", $hasta . " 23:59:59"])
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            ->join('seguros as s', 's.id', 'ov.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            //->groupby('s.id_seguro_tipos', DB::raw('MONTH(agenda.fechaini)'))
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1')
            ->where('ov.estado', '1');

        if ($tipo == 0) {
            $ordenes2 = $ordenes2->where('proc_consul', '0');
        }

        if ($tipo == 1) {
            $ordenes2 = $ordenes2->where('proc_consul', '1');
        }
        $ordenes2 = $ordenes2->where(DB::raw('MONTH(agenda.fechaini)'), $mes)->groupby(DB::raw('MONTH(agenda.fechaini)'))->where("s.id_seguro_tipos", "{$seguro_tipo}")->select("s.id_seguro_tipos", "st.nombre", DB::raw('SUM(ov.total) as total'), "agenda.fechaini")->first();
        //dd($ordenes2);
        return $ordenes2;
    }
    public static function armarApi($tipo, $mes, $desde, $hasta)
    {
        $particular = BigDataController::sqlQuery($tipo, $desde, $hasta, "3", $mes);
        $privados = BigDataController::sqlQuery($tipo, $desde, $hasta, "2", $mes);
        $promo = BigDataController::sqlQuery($tipo, $desde, $hasta, "4", $mes);

        $total = [];
        if (!is_null($particular)) {
            $detalle = ["nombre" => $particular->nombre, "total" => $particular->total];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Particular", "total" => 0];
            array_push($total, $detalle);
        }

        if (!is_null($privados)) {
            $detalle = ["nombre" => $privados->nombre, "total" => $privados->total];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Privado", "total" => 0];
            array_push($total, $detalle);
        }

        if (!is_null($promo)) {
            $detalle = ["nombre" => $promo->nombre, "total" => $promo->total];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Promo", "total" => 0];
            array_push($total, $detalle);
        }
        return $total;
    }
    public function total_anio(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $elemento = json_decode($request->getContent(), true);
                $desde = $request['desde'];
                if (is_null($desde)) {
                    // $desde = date("Y-m-d", strtotime(date('Y-m-d').'-1 year'));
                    $desde = date("Y-m-d", strtotime(date('2022-01-01')));
                }

                $hasta = $request['hasta'];
                if (is_null($hasta)) {
                    $hasta = date('Y-m-d');
                }

                //dd($desde, $hasta);

                $tipo = $elemento['tipo'];

                if (is_null($tipo)) {
                    $tipo = 0;
                }

                $ordenes_anio = Agenda::where('agenda.estado', '1')
                    ->wherebetween('agenda.fechaini', [$desde . " 00:00:00", $hasta . " 23:59:59"])
                    ->join('seguros as s', 's.id', 'agenda.id_seguro')
                    ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
                    ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                    ->groupby('s.id_seguro_tipos', DB::raw('YEAR(agenda.fechaini)'))
                    ->where("s.id_seguro_tipos", "<>", "null")
                    ->where('st.id', '!=', '1')
                    ->where('proc_consul', $tipo)
                    ->where('ov.estado', '1')
                    ->select(DB::raw('YEAR(agenda.fechaini) as anio'), 'st.id as id_tipo_seguro', "st.nombre", DB::raw('SUM(ov.total) as total'), DB::raw('COUNT(agenda.id) as cantidad'))->get();
                //dd($ordenes_anio);
                return json_encode($ordenes_anio);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
    public function labs_meses(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $anio = $request->anio;
                if (is_null($anio)) {
                    $anio = date('Y');
                }

                $examen_orden = Examen_Orden::where('examen_orden.estado', '1')
                    ->where('examen_orden.anio', $anio)
                    ->where('examen_orden.realizado', '1')
                    ->where('s.id_seguro_tipos', '!=', 'null')
                    ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                    ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
                    ->groupBy('examen_orden.mes')
                    ->select('examen_orden.mes as mes')->get();

                $arr = [];
                $meses = array('Todos', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                foreach ($examen_orden as $value) {
                    $data = BigDataController::armarLabs($value->mes);
                    $mes = [
                        "mes"       => $meses[$value->mes],
                        "details"   => $data,
                        "id_mes"    => $value->mes
                    ];
                    array_push($arr, $mes);
                }

                //dd($arr);

                return json_encode($arr);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }

    public static function sql_labs($mes, $seguro_tipo)
    {
        $anio = date("Y");
        $examen = Examen_Orden::where('examen_orden.estado', '1')
            ->where('examen_orden.realizado', '1')
            ->where('examen_orden.mes', $mes)
            ->where("examen_orden.anio", $anio)
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where("s.id_seguro_tipos", "{$seguro_tipo}")
            ->join("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->groupBy('examen_orden.mes')
            ->select('s.id_seguro_tipos', 'st.nombre', DB::raw('COUNT(examen_orden.id) as cantidad'), DB::raw('SUM(examen_orden.total_valor - examen_orden.recargo_valor) as total'))->first();

        //dd($examen);
        return $examen;
    }

    public static function armarLabs($mes)
    {
        $particular = BigDataController::sql_labs($mes, "3");
        $privados = BigDataController::sql_labs($mes, "2");
        $promo = BigDataController::sql_labs($mes, "4");
        $publico = BigDataController::sql_labs($mes, "1");

        $total = [];
        if (!is_null($particular)) {
            $detalle = ["nombre" => $particular->nombre, "total" => $particular->total, "cantidad" => $particular->cantidad, 'id_seguro_tipos' => $particular->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Particular", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 3];
            array_push($total, $detalle);
        }

        if (!is_null($privados)) {
            $detalle = ["nombre" => $privados->nombre, "total" => $privados->total, "cantidad" => $privados->cantidad, 'id_seguro_tipos' => $privados->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Privado", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 2];
            array_push($total, $detalle);
        }

        if (!is_null($promo)) {
            $detalle = ["nombre" => $promo->nombre, "total" => $promo->total, "cantidad" => $promo->cantidad, 'id_seguro_tipos' => $promo->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Promo", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 4];
            array_push($total, $detalle);
        }

        if (!is_null($publico)) {
            $detalle = ["nombre" => $publico->nombre, "total" => $publico->total, "cantidad" => $publico->cantidad, 'id_seguro_tipos' => $publico->id_seguro_tipos];
            array_push($total, $detalle);
        } else {
            $detalle = ["nombre" => "Publico", "total" => 0, "cantidad" => 0, 'id_seguro_tipos' => 1];
            array_push($total, $detalle);
        }
        return $total;
    }

    public function buscar_usuario(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $email = $request->email;
                // dd($email);
                if (!is_null($email)) {
                    $usuario = User::where('email', $email)->orwhere('id', $email)->where('estado', '1')->first();
                    //  dd($usuario);

                    if (!is_null($usuario)) {
                        $imagen = '';
                        // if ($imagen != ' ') {
                        //     $image_2 = Storage::disk('public')->get($usuario->imagen_url);
                        //     $imagen = utf8_encode(base64_decode($image_2));
                        // }
                        //dd($imagen);
                        //$arr = [];
                        $menbresia = UserMembresia::where('user_id', $usuario->id)->where('user_membresia.estado', '1')->get();

                        //dd($imagen);


                        return response()->json(["respuesta" => $usuario, "claveUser" => $usuario->password, 'status' => 'okay', "menbresia" => $menbresia, 'imagen' => $imagen]);
                    } else {
                        return json_encode(["respuesta" => 'No existe', 'status' => 'no', "menbresia" => 'no tiene']);
                    }
                } else {
                    return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
                }
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function buscar_examenes(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $id_paciente = $request->id_paciente;
                // return json_encode(["respuesta" => $id_paciente, 'status' => 'no']);
                if (!is_null($id_paciente)) {
                    $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                        ->join('users as usuario', 'usuario.id', 'p.id_usuario')
                        ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                        ->where('usuario.id', $id_paciente)
                        ->where('examen_orden.estado', '1')
                        ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                        ->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor');

                    $paciente = Paciente::find($id_paciente);

                    if (!is_null($paciente)) {
                        if ($paciente->id != $paciente->id_usuario) {
                            $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                                ->join('users as usuario', 'usuario.id', 'p.id')
                                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                                ->where('usuario.id', $id_paciente)
                                ->where('examen_orden.estado', '1')
                                ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                                ->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor'); //dd($ordenes1->get());
                        }
                    }

                    $ordenes2 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                        ->join('labs_grupo_familiar as gf', 'gf.id', 'p.id')
                        ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                        ->where('gf.id_usuario', $id_paciente)
                        ->where('gf.estado', '1')
                        ->where('examen_orden.estado', '1')
                        ->join('users as us_doc', 'us_doc.id', 'examen_orden.id_doctor_ieced')
                        ->select('examen_orden.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre as nombre_seguro', 'us_doc.nombre1 as nombre_doctor', 'us_doc.apellido1 as apellido_doctor');

                    $ordenes = $ordenes1->union($ordenes2);
                    $querySql = $ordenes->toSql();
                    $ordenes = DB::table(DB::raw("($querySql order by fecha_orden desc) as a"))->mergeBindings($ordenes->getQuery());
                    $ordenes  = $ordenes->get();

                    return json_encode(['respuesta' => $ordenes, 'status' => 'okay']);
                } else {
                    return json_encode(["respuesta" => 'No existe', 'status' => 'no']);
                }
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function listado_examenes(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $exam  = Examen::where('examen.estado', '1')
                    ->where('examen.valor', '>', 0)
                    ->where('examen.id', '<>', '1124')
                    ->join('examen_agrupador_sabana as s', 's.id_examen', 'examen.id')
                    ->where('s.estado', 1)
                    ->select('examen.id', 'examen.valor', 'examen.nombre', DB::raw('SUBSTRING(examen.nombre, 1, 1) AS initial'), 'examen.nombre_largo', 'examen.sugerencia')
                    ->orderBy('examen.nombre')->get();
                return json_encode(['respuesta' => $exam, 'status' => 'okay']);
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }

    public function crear_orden_labs(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
                $json_request = json_decode($request->getContent());
                //dd($json_request->usuario);
                $tipo_pago            = $json_request->tipopago; //[MEM]->ES PAGO DE MEMBRESIA, [ORD]-> ES PAGO DE ORDEN
                $membresia_id         = $json_request->membresia_id; //ID DE LA MEMBRESIA ACTUAL DEL PACIENTE, SI ES 0 EL PAGO FUE HECHO SIN MEMBRESIA
                $membresia_detalle_id = $json_request->membresia_detalle_id;
                $ip_cliente           = $_SERVER["REMOTE_ADDR"];
                if ($membresia_id == 0) {
                    $membresia_id = null;
                }
                $id_seguro         = '1';
                $id_protocolo      = null;
                $id_nivel          = null;
                $total             = 0;
                $contador          = 0;
                $descuento_detalle = 0;
                $empresa_labs = Empresa::where('prioridad_labs', '1')->first();
                $cedula_paciente  = $json_request->id_paciente;
                $paciente = Paciente::find($cedula_paciente);

                if (is_null($paciente)) {

                    $user = user::find($cedula_paciente);

                    if (!is_null($user)) {
                        $input_pac = [

                            'id'                 => $cedula_paciente,
                            'id_usuario'       => $cedula_paciente,
                            'nombre1'            => strtoupper($json_request->usuario->nombre1),
                            'nombre2'            => strtoupper($json_request->usuario->nombre2),
                            'apellido1'          => strtoupper($json_request->usuario->apellido1),
                            'apellido2'          => strtoupper($json_request->usuario->apellido2),
                            'fecha_nacimiento'   => $json_request->usuario->fecha_nacimiento,
                            //'sexo'               => $request['sexo'],
                            'telefono1'          => $json_request->usuario->telefono1,
                            'telefono2'          => $json_request->usuario->telefono2,
                            'parentesco'         => 'Principal',
                            'parentescofamiliar' => 'Principal',
                            'tipo_documento'     => 1,
                            'id_seguro'          => 1,
                            'imagen_url'         => ' ',
                            'menoredad'          => 0,
                            'ip_creacion'        => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,
                            'id_usuariocrea'     => 'CREAUSER',
                            'id_usuariomod'      => 'CREAUSER',
                            'otros'              => 'APP LABS',

                        ];
                        Paciente::create($input_pac);
                    } else {
                        $arr_us = [
                            'id'               => $cedula_paciente,
                            'email'            => $json_request->usuario->email,
                            'nombre1'          => strtoupper($json_request->usuario->nombre1),
                            'id_tipo_usuario'  => $json_request->usuario->id_tipo_usuario,
                            'nombre2'          => strtoupper($json_request->usuario->nombre2),
                            'apellido1'        => strtoupper($json_request->usuario->apellido2),
                            'apellido2'        => strtoupper($json_request->usuario->apellido2),
                            'estado'           => '1',
                            'imagen_url'       => ' ',
                            'telefono1'        => $json_request->usuario->telefono1,
                            'telefono2'        => $json_request->usuario->telefono2,
                            'direccion'        => $json_request->usuario->direccion,
                            'tipo_documento'   => '1',
                            'id_pais'          => '1', //pendiente revisar
                            'fecha_nacimiento' => $json_request->usuario->fecha_nacimiento,
                            'password'         => $json_request->usuario->password,
                            'id_usuariocrea'   => 'CREAUSER',
                            'id_usuariomod'    => 'CREAUSER',
                            'ip_creacion'      => '::1',
                            'ip_modificacion'  => '::1',
                        ];

                        $id_usuario = User::insertGetId($arr_us);

                        $input_pac = [

                            'id'                 => $cedula_paciente,
                            'id_usuario'         => $cedula_paciente,
                            'nombre1'            => strtoupper($json_request->usuario->nombre1),
                            'nombre2'            => strtoupper($json_request->usuario->nombre2),
                            'apellido1'          => strtoupper($json_request->usuario->apellido1),
                            'apellido2'          => strtoupper($json_request->usuario->apellido2),
                            'fecha_nacimiento'   => $json_request->usuario->fecha_nacimiento,
                            //'sexo'               => $request['sexo'],
                            'telefono1'          => $json_request->usuario->telefono1,
                            'telefono2'          => $json_request->usuario->telefono2,
                            'parentesco'         => 'Principal',
                            'parentescofamiliar' => 'Principal',
                            'tipo_documento'     => 1,
                            'id_seguro'          => 1,
                            'imagen_url'         => ' ',
                            'menoredad'          => 0,
                            'ip_creacion'        => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,
                            'id_usuariocrea'     => 'CREAUSER',
                            'id_usuariomod'      => 'CREAUSER',
                            'otros'              => 'APP LABS',

                        ];
                        Paciente::create($input_pac);

                        $input_log = [
                            'id_usuario'  => 'CREAUSER',
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "CREA NUEVO PACIENTE APP",
                            'dato_ant1'   => $json_request->id_paciente,
                            'dato1'       => strtoupper($json_request->usuario->nombre1 . " " . $json_request->usuario->nombre2 . " " . $json_request->usuario->apellido1 . " " . $json_request->usuario->apellido2),
                            'dato_ant4'   => " PARENTESCO: Principal",
                            'dato2'       => 'APP',
                        ];
    
                        Log_usuario::create($input_log);
                    }
                    $paciente = Paciente::find($cedula_paciente);
                }


                    $input_ex = [
                        'id_paciente'          => $paciente['id'],
                        'fecha_tentativa'      => $json_request->fecha_tentativa,
                        'cedula_factura'       => $json_request->cedula_factura,
                        'nombre_factura'       => $json_request->nombre_factura,
                        'direccion_factura'    => $json_request->direccion_factura,
                        'ciudad_factura'       => $json_request->ciudad_factura,
                        'email_factura'        => $json_request->email_factura,
                        'telefono_factura'     => $json_request->telefono_factura,
                        'anio'                 => substr(date('Y-m-d'), 0, 4),
                        'mes'                  => substr(date('Y-m-d'), 5, 2),
                        'fecha_orden'          => date('Y-m-d'),
                        'id_protocolo'         => $id_protocolo,
                        'id_seguro'            => $id_seguro,
                        'id_nivel'             => $id_nivel,
                        'est_amb_hos'          => '0',
                        'id_doctor_ieced'      => '1234517896',
                        'doctor_txt'           => 'COTIZACION GENERADA EN APP MÒVIL',
                        'observacion'          => '',
                        'id_empresa'           => $empresa_labs->id,
                        'cantidad'             => '0',
                        'estado'               => '-1',
                        'realizado'            => '0',
                        'total_valor'          => $json_request->total,
                        'valor'                => $json_request->total,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => '1234517896',
                        'id_usuariomod'        => '1234517896',
                        'motivo_descuento'     => '',
                        'membresia_id'         => $membresia_id,
                        'membresia_detalle_id' => $membresia_detalle_id,
                    ];
                    $id_orden = Examen_Orden::insertGetId($input_ex);
                    foreach ($json_request->detalles as $pexamen) {
                        //CREA DETALLE
                        $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $id_orden)->first();
                        if (is_null($detalle)) {
                            $contador++;
                            $examen = Examen::find($pexamen->id);
                            $valor    = $pexamen->total; //AQUI PONERMOS EL VALOR YA CON DESCUENTO DE MEMBRESIA Y TODO DE LA APP
                            $cubre    = 'NO';
                            $ex_nivel = Examen_Nivel::where('id_examen', $pexamen->id)->where('nivel', $id_nivel)->first();
                            if (!is_null($ex_nivel)) {
                                if ($ex_nivel->valor1 != 0) {
                                    $valor = $ex_nivel->valor1;
                                    $cubre = 'SI';
                                }
                            }
                            $input_det = [
                                'id_examen_orden' => $id_orden,
                                'id_examen'       => $pexamen->id,
                                'valor'           => $valor,
                                'cubre'           => $cubre,
                                'p_descuento'     => $pexamen->pdiscount,
                                'valor_descuento' => $pexamen->discount,
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => '1234517896',
                                'id_usuariomod'   => '1234517896',
                            ];
                            Examen_detalle::create($input_det);
                            $total += $valor;
                            $descuento_detalle += $pexamen->discount;
                        }
                    }

                    if ($request['pres_dom'] == '1') {
                        //[1] Domicilio, [0] Presencial
                        $examen = Examen::find('1203');
                        if (!is_null($examen)) {
                            $input_det = [
                                'id_examen_orden' => $id_orden,
                                'id_examen'       => $examen->id,
                                'valor'           => $examen->valor,
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => '1234517896',
                                'id_usuariomod'   => '1234517896',
                                'human_labs'      => 0,
                                'p_descuento'     => 0,
                                'valor_descuento' => 0,
                            ];
                            Examen_detalle::create($input_det);
                            $total = $total + $examen->valor;
                            //$contador++;
                        }
                    }

                    $total             = $total;
                    $recargo_p         = 0;
                    $subtotal_pagar    = $total - $descuento_detalle;
                    $recargo_valor     = $subtotal_pagar * $recargo_p / 100;
                    $recargo_valor     = $recargo_valor;
                    $valor_total       = $subtotal_pagar + $recargo_valor;
                    $valor_total       = $valor_total;

                    if ($tipo_pago == "ORD") {
                        $orden     = Examen_Orden::find($id_orden);
                        $input_ex2 = [
                            'recargo_valor'   => $recargo_valor,
                            'total_valor'     => $total - $descuento_detalle,
                            'cantidad'        => $contador,
                            'valor'           => $total,
                            'descuento_valor' => $descuento_detalle,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariomod'   => '1234517896',
                            'estado_pago'     => '0',
                            'pres_dom'        => $request->pres_dom,
                        ];
                        $orden->update($input_ex2);
                    }

                    $apps_orden = DB::table('apps_orden')->insertGetId(
                        [
                            'id_orden' => $id_orden,
                            'id_usuario' => $paciente['id'],
                            'estado' => 1,
                            'total' => $valor_total
                        ]
                    );
                    return json_encode(['respuesta' => $id_orden, 'status' => 'okay']);
                

                
                
            } else {
                return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
            }
        } else {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'no']);
        }
    }
}
