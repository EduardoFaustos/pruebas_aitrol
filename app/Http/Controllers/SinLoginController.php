<?php

namespace Sis_medico\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Sis_medico\Agenda;
use Sis_medico\Area;
use Sis_medico\Contable;
use Sis_medico\Ct_Clientes;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Examen;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Examen_Resultado;
use Sis_medico\Forma_de_pago;
use Sis_medico\GrupoPregunta;
use Sis_medico\Labs_Grupo_Familiar;
use Sis_medico\Log_Api;
use Sis_medico\Log_usuario;
use Sis_medico\Master_encuesta;
use Sis_medico\Paciente;
use Sis_medico\Pregunta;
use Sis_medico\Preguntas_Labs;
use Sis_medico\Protocolo;
use Sis_medico\Sugerencia;
use Sis_medico\TipoSugerencia;
use Sis_medico\User;

class SinLoginController extends Controller
{
    public function sugerencia()
    {
        $areas          = Area::where("estado", '=', 1)->get();
        $tiposugerencia = TipoSugerencia::where('estado', '1')->get();
        return view('sinlogin/sugerencias', ['areas' => $areas, 'tiposugerencia' => $tiposugerencia]);
    }

    public function sugerenciaguardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        Sugerencia::create([
            'id_tiposugerencia' => $request['tiposugerencia'],
            'id_area'           => $request['area'],
            'observacion'       => $request['mensaje'],
        ]);
        $areas          = Area::where("estado", '=', 1)->get();
        $tiposugerencia = TipoSugerencia::where('estado', '1')->get();
        return view('sinlogin/general');
        //return redirect()->intended('/sala-management');
    }

    public function encuestas()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $areas         = Area::where("estado", '=', 1)->get();
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();

            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        return view('sinlogin/encuesta', ['arreglo' => $arreglo, 'areas' => $areas]);
    }

    public function encuestas2()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $areas         = Area::where("estado", '=', 1)->get();
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();

            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        return view('sinlogin/encuesta2', ['arreglo' => $arreglo, 'areas' => $areas]);
    }

    public function encuestaguardar(Request $request)
    {
        //return $request->all();
        $id_master  = $request->id_master;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        /*$id = DB::table('encuesta_1')->insertGetId(
        ['ip_ingreso' => $ip_cliente, 'id_area' => $request['area']]
        );*/

        if ($request['cedula'] == null) {
            return "1";
        }

        $fecha_hoy = date('Y-m-d');
        $master    = $request['id_master'];

        if ($master == 1) {
            $master = 0;
        }

        if ($master == 2) {
            $master = 1;
        }

        if ($request['cedula'] != null) {
            $paciente = Paciente::find($request['cedula']);

            if ($paciente == null) {
                return "5";
            }
        }

        //dd($master);
        $numero      = 1;
        $no_agendado = Agenda::where('id_paciente', $request['cedula'])->whereBetween('fechaini', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->where('estado', '1')->get();
        // dd($no_agendado);

        if (count($no_agendado) <= 0) {
            return "2";
        }


        if (count($no_agendado) == 1) {
            if ($no_agendado[0]->proc_consul != $master) {

                return "4";
            }
        }

        if (count($no_agendado) > 1) {

            for ($i = 0; $i < count($no_agendado); $i++) {

                if ($no_agendado[$i]->proc_consul != $master) {

                    $numero++;
                }
            }

            if ($numero == 0) {

                return "2";
            }
        }

        $grupopregunta = GrupoPregunta::all();
        //return $grupopregunta;
        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            if ($value->id != 4) {
                //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
                $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id_master)->get();
                //return $dato;
                foreach ($dato as $value2) {
                    $flag = false;
                    if ($request[$value2->id] == null) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    return "3";
                }
            }
        }

        $id = DB::table('encuesta_1')->insertGetId(
            ['ip_ingreso' => $ip_cliente, 'id_area' => $id_master, 'anio' => substr(date('Y-m-d'), 0, 4), 'mes' => substr(date('Y-m-d'), 5, 2), 'id_paciente' => $request['cedula']]
        );
        //return $id;

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id_master)->get();

            //return $dato;
            foreach ($dato as $value2) {

                DB::table('encuesta_complemento')->insert(
                    ['id_encuesta_1' => $id, 'id_pregunta' => $value2->id, 'valor' => $request[$value2->id], 'id_grupo' => $value2->id_grupopregunta, 'valor2' => $request['val2' . $value2->id]]
                );
            }
        }

        return "ok";
    }

    public function encuesta_sugencia()
    {
        return view('sinlogin/general');
    }

    public function maquina1(Request $request)
    {
        $trama      = $request->parametro;
        $enviar     = explode("torbi10", $trama);
        $valor_id   = "dato";
        $idusuario  = '9999999999';
        $ipcreacion = "maquina";
        $n_examen   = 101;
        foreach ($enviar as $value) {
            $descriptando = explode("|", $value);
            if ($descriptando[0] == "OBR") {
                $valor_id = $descriptando[3];
                $examen   = Examen_Detalle::where('id_examen', '101')->where('id_examen_orden', $valor_id)->first();
                if (!is_null($examen)) {
                    $n_examen = 101;
                } else {
                    $n_examen = 1184;
                }
            }
            if ($valor_id != "dato") {
                if (($descriptando[0] == "OBX") && ($descriptando[1] < 36)) {

                    $buscar_tipo = explode("^", $descriptando[3]);

                    $examen_parametro = examen_parametro::where('nombre', $buscar_tipo[1])->where('id_examen', $n_examen)->first();

                    if (!is_null($examen_parametro)) {

                        $id_parametro     = $examen_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            $input = [
                                'valor'           => $descriptando[5],
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'valor'           => $descriptando[5],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }
                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
        }

        return "MSH|^~\&|LIS||||" . date('YmdHis') . "||ACK^R01|1|P|2.3.1||||||UNICODE\nMSA|AA|1";
    }

    public function maquina2(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '6';
        //return "hola";
        if (count($enviar) == 4) {
            //ENVIAR EXAMENWS
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QPD") {
                    $valor_id = $descriptando[3];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo  = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||DSR^Q03|0|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                if ($value->id_examen == "1179") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1179')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                if ($value->id_examen == "1181") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1181')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                //examen especial proteinas y fracciones
                                if ($value->examen->nombre_equipo != "") {
                                    if ($contador == 29) {
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                    $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                    $contador = $contador + 1;
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        }
        if (count($enviar) == 5) {
            //RECIBIR RESULTADOS
            $codigo_maquina = 1;
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                }
                if ($descriptando[0] == "OBX") {

                    $examen = examen::where('nombre_equipo', $descriptando[3])->first();

                    $examen_parametro = examen_parametro::where('id_examen', $examen->id)->get();

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            $input = [
                                'valor'           => round($descriptando[5], 2),
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];
                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'valor'           => round($descriptando[5], 2),
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function bha200(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '69F2746D24014F21AD7139756F64CAD8';
        //return "hola";
        if (count($enviar) == 3) {
            //ENVIAR EXAMENWS
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "bha200") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QPD") {
                    $valor_id = $descriptando[3];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\&|Modulab|Systelab|bha200|Biosystems|" . date('YmdHis') . "||OML^O33^OML_O3
                            3|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo = chr(0x0B) . "MSH|^~\\&|Modulab|Systelab|bha200|Biosystems|" . date('YmdHis') . "||OML^O33^OML_O3
                            3|69F2746D24014F21AD7139756F64CAD8|P|2.5.1|||ER|AL||UNICODE UTF-8|||LAB-28^IHE\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                if ($value->id_examen == "1179") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1179')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                if ($value->id_examen == "1181") {
                                    $esp_examen_parametro = Examen_Parametro::where('id_examen', '1181')->whereNotNull('nombre_equipo')->get();
                                    foreach ($esp_examen_parametro as $esp_value) {
                                        if ($esp_value->nombre_equipo != "") {
                                            if ($contador == 29) {
                                                $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                                $contador = $contador + 1;
                                            }
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                    }
                                }
                                //examen especial proteinas y fracciones
                                if ($value->examen->nombre_equipo != "") {
                                    if ($contador == 29) {
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                    $tercero  = $tercero . "DSP|" . $contador . "||" . $value->examen->nombre_equipo . "^^^|||\n";
                                    $contador = $contador + 1;
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        }
        if (count($enviar) == 5) {
            //RECIBIR RESULTADOS
            $codigo_maquina = '69F2746D24014F21AD7139756F64CAD8';
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|Modulab|Systelab|BA400|Biosystems|" . date('YmdHis') . "||OML^O33^OML_O3
3|" . $codigo_maquina . "|P|2.5.1|||ER|AL||UNICODE UTF-8|||LAB-28^IHE\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                }
                if ($descriptando[0] == "OBX") {

                    $examen = examen::where('nombre_equipo', $descriptando[3])->first();

                    $examen_parametro = examen_parametro::where('id_examen', $examen->id)->get();

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            $input = [
                                'valor'           => round($descriptando[5], 2),
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'valor'           => round($descriptando[5], 2),
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function maquina3(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '6';
        $ultimo         = end($enviar);
        if (substr($ultimo, 0, 3) != "OBX") {
            //ENVIAR EXAMENWS
            //return "entrada";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QRD") {
                    $valor_id = $descriptando[8];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo  = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||DSR^Q03|0|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                $esp_examen_parametro = Examen_Parametro::where('id_examen', $value->id_examen)
                                    ->whereNotNull('nombre_equipo')
                                    ->where('maquina', 3)
                                    ->get();
                                foreach ($esp_examen_parametro as $esp_value) {
                                    if ($esp_value->nombre_equipo != "") {
                                        if ($contador == 29) {
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        } else {
            //return "recibir resultados";
            //RECIBIR RESULTADOS
            $codigo_maquina = 1;
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                return $descriptando[0];
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                    //return $valor_id;
                }
                if ($descriptando[0] == "OBX") {
                    //return $examen->id;
                    //return "OBX";

                    $examen_parametro = examen_parametro::where('nombre_equipo', $descriptando[3])->where('maquina', 3)->get();
                    //return $examen_parametro;

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            //return $examen_resultado->id;
                            //return "entra actual";
                            $input = [
                                'id_examen'       => $value_parametro->id_examen,
                                'valor'           => $descriptando[5],
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            //return "no tiene";
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'id_examen'       => $value_parametro->id_examen,
                                'valor'           => $descriptando[5],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function maquina4(Request $request)
    {
        $trama          = $request->parametro;
        $enviar         = explode("torbi10", $trama);
        $valor_id       = "dato";
        $idusuario      = '9999999999';
        $ipcreacion     = "maquina";
        $codigo_maquina = '6';
        $ultimo         = end($enviar);
        if (substr($ultimo, 0, 3) != "OBX") {
            //ENVIAR EXAMENWS
            //return "entrada";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                if ($descriptando[0] == "QRD") {
                    $valor_id = $descriptando[8];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examenes_detalle = Examen_Detalle::where('id_examen_orden', $valor_id)->get();
                        $examen_orden     = Examen_Orden::find($valor_id);
                        if (!is_null($examenes_detalle)) {
                            if ($examen_orden->paciente->sexo == 1) {
                                $sexo = "M";
                            } else {
                                $sexo = "F";
                            }
                            $tercero  = "";
                            $contador = 29;
                            $enviar   = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
                            $segundo  = chr(0x0B) . "MSH|^~\\&|||Mindray|BS-200E|" . date('YmdHis') . "||DSR^Q03|0|P|2.3.1||||0||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\nQRD|" . date('YmdHis') . "|R|D|1|||RD|" . $valor_id . "|OTH|||T|\nQRF|BS-200E|" . date('YmdHis') . "|" . date('YmdHis') . "|||RCT|COR|ALL||\nDSP|1||" . $examen_orden->id_paciente . "|||\nDSP|2||0|||\nDSP|3||" . $examen_orden->paciente->apellido1 . " " . $examen_orden->paciente->nombre1 . "|||\nDSP|4||" . date("Ymd", strtotime($examen_orden->paciente->fecha_nacimiento)) . "000000|||\nDSP|5||" . $sexo . "|||\nDSP|6|||||\nDSP|7|||||\nDSP|8|||||\nDSP|9|||||\nDSP|10|||||\nDSP|11|||||\nDSP|12|||||\nDSP|13|||||\nDSP|14|||||\nDSP|15||outpatient|||\nDSP|16|||||\nDSP|17||own|||\nDSP|18|||||\nDSP|19|||||\nDSP|20|||||\nDSP|21||" . $valor_id . "|||\nDSP|22|||||\nDSP|23||" . date('YmdHis') . "|||\nDSP|25|||||\nDSP|26||Suero|||\nDSP|27||LIS|||\nDSP|28||LAB|||\n";
                            foreach ($examenes_detalle as $value) {
                                //examen especial proteinas y fracciones
                                $esp_examen_parametro = Examen_Parametro::where('id_examen', $value->id_examen)
                                    ->whereNotNull('nombre_equipo')
                                    ->where('maquina', 3)
                                    ->get();
                                foreach ($esp_examen_parametro as $esp_value) {
                                    if ($esp_value->nombre_equipo != "") {
                                        if ($contador == 29) {
                                            $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                            $contador = $contador + 1;
                                        }
                                        $tercero  = $tercero . "DSP|" . $contador . "||" . $esp_value->nombre_equipo . "^^^|||\n";
                                        $contador = $contador + 1;
                                    }
                                }
                            }
                            $ultimo = "DSC||\n" . chr(0x1C) . "\r";

                            //return $ultimo;
                            if ($tercero != "") {
                                $enviar = $enviar . $segundo . $tercero . $ultimo;
                            } else {
                                $enviar = $enviar . $enviar;
                            }
                            return $enviar;
                        } else {
                            return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\r";
                        }
                    } else {
                        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|" . $codigo_maquina . "|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|NF|\n" . chr(0x1C) . "\n";
                    }
                }
            }
        } else {
            //return "recibir resultados";
            //RECIBIR RESULTADOS
            $codigo_maquina = 1;
            $valor_id       = "";
            foreach ($enviar as $value) {
                $descriptando = explode("|", $value);
                if (count($descriptando) >= 4) {
                    if ($descriptando[3] == "BS-200E") {
                        $codigo_maquina = $descriptando[9];
                    }
                }
                return $descriptando[0];
                if ($descriptando[0] == "OBR") {
                    $valor_id = $descriptando[2];
                    if ($valor_id != "" || $valor_id != "Invalid") {
                        $examen_orden = Examen_Orden::find($valor_id);
                    } else {
                        return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
                    }
                    //return $valor_id;
                }
                if ($descriptando[0] == "OBX") {
                    //return $examen->id;
                    //return "OBX";

                    $examen_parametro = examen_parametro::where('nombre_equipo', $descriptando[3])->where('maquina', 3)->get();
                    //return $examen_parametro;

                    foreach ($examen_parametro as $value_parametro) {

                        $id_parametro     = $value_parametro->id;
                        $examen_resultado = Examen_Resultado::where('id_parametro', $id_parametro)->where('id_orden', $valor_id)->first();

                        if (!is_null($examen_resultado)) {
                            //return $examen_resultado->id;
                            //return "entra actual";
                            $input = [
                                'id_examen'       => $value_parametro->id_examen,
                                'valor'           => $descriptando[5],
                                'ip_modificacion' => $ipcreacion,
                                'id_usuariomod'   => $idusuario,
                            ];

                            Examen_Resultado::where('id', $examen_resultado->id)->update($input);
                        } else {
                            //return "no tiene";
                            Examen_Resultado::create([
                                'id_orden'        => $valor_id,
                                'id_parametro'    => $id_parametro,
                                'id_examen'       => $value_parametro->id_examen,
                                'valor'           => $descriptando[5],
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                                'ip_creacion'     => $ipcreacion,
                                'ip_modificacion' => $ipcreacion,
                            ]);
                        }

                        $orden            = examen_orden::find($valor_id);
                        $orden->realizado = "1";
                        $orden->save();
                    }
                }
            }
            return chr(0x0B) . "MSH|^~\\&|||Mindray|BS-400|" . date('YmdHis') . "||ACK^R01|" . $codigo_maquina . "|P|2.3.1||||0||ASCII|||\nMSA|AA|" . $codigo_maquina . "|Message accepted|||0|\n" . chr(0x1C) . "\r";
        }
        return chr(0x0B) . "MSH|^~\&|||Manufacture|Model|" . date('YmdHis') . "||QCK^Q02|1|P|2.3.1||||||ASCII|||\nMSA|AA|1|Message accepted|||0|\nErr|0|\nQAK|SR|OK|\n" . chr(0x1C) . "\r";
    }

    public function rrhh_encuesta($id)
    {
        //dd($id);

        $encuestas = Master_encuesta::where('estado', 1)->where('id', $id)->first();
        //dd($encuestas);
        if (is_null($encuestas)) {
            // dd("esta vacio");
            $encuestas = Master_encuesta::where('estado', 1)->where('id', 1)->first();
            return view('rrhh.encuesta', ['encuestas' => $encuestas]);
        } else {
            return view('rrhh.encuesta', ['encuestas' => $encuestas]);
        }
    }

    public function formato_encuesta($id)
    {

        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $areas         = Area::where("estado", '=', 1)->get();
        $encuesta      = Master_encuesta::find($id);
        // dd($encuesta->id);
        $tiempos = explode('+', $encuesta->tiempos);

        //dd($tiempos);

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('pregunta.estado', '=', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id)->get();
            //dd($dato);
            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        //dd($id);
        return view('rrhh/formato_encuesta', ['arreglo' => $arreglo, 'areas' => $areas, 'tiempos' => $tiempos, 'encuesta' => $encuesta, 'id' => $encuesta->id]);
    }

    public function externo_web()
    {
        $covid_local     = Examen::find('1191');
        $covid_domicilio = Examen::find('1195');

        return view('laboratorio/externo/web', ['covid_local' => $covid_local, 'covid_domicilio' => $covid_domicilio]);
    }

    public function promo($id)
    {
        return view('laboratorio/externo/ingresar_orden', ['id' => $id]);
    }
    //MODIFICADA PARA PAGO EN LINEA
    public function externo_guardar(Request $request)
    {
        $_pasarela_pagos_detalle  = "";
        $_pasarela_pagos_subtotal = 0;
        $_pasarela_pagos_iva      = 0;
        $_pasarela_pagos_total    = 0;
        //return "guardar";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //SELECCIONA LA PROMO
        $id_seguro = null;
        if ($request->promo == '1') {
            $id_seguro    = '30';
            $id_protocolo = '10';
            $id_nivel     = '29';
        } elseif ($request->promo == '2') {
            $id_seguro    = '31';
            $id_protocolo = '11';
            $id_nivel     = '30';
        } elseif ($request->promo == '3') {
            $id_seguro    = '32';
            $id_protocolo = '12';
            $id_nivel     = '31';
        } elseif ($request->promo == '4' || $request->promo == '5') {
            $id_seguro    = '1';
            $id_protocolo = null;
            $id_nivel     = null;
        }
        if ($id_seguro != null) {
            $reglas = [
                'email' => 'email',
            ];
            $mensaje = [
                'email.email'  => 'El Email tiene error en el formato.',
                'email.unique' => 'El Email ya se encuentra registrado.',
            ];
            $this->validate($request, $reglas, $mensaje);
            $paciente = Paciente::find($request->id);
            if (!is_null($paciente)) {
                $usuario_mail = User::where('email', $request->email)->where('id', '<>', $paciente->id_usuario)->first();
            } else {
                $usuario_mail = User::where('email', $request->email)->first();
            }

            if (!is_null($usuario_mail)) {
                $xusuario = $usuario_mail;
                //Si encuentra el correo, crea o actualiza el grupo familiar
                $email = $request->id . '@correo.dum';
                $user  = User::find($request->id);
                if (is_null($user)) {
                    $input_us = [
                        'id'               => $request['id'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'telefono1'        => $request['celular'],
                        'telefono2'        => '1',
                        'id_pais'          => '1',
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'id_tipo_usuario'  => 2,
                        'email'            => $email,
                        'password'         => bcrypt($request['id']),
                        'tipo_documento'   => 1,
                        'estado'           => 1,
                        'imagen_url'       => ' ',
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => '1234517896',
                        'id_usuariomod'    => '1234517896',
                    ];
                    User::create($input_us);
                } else {
                    $input_us = [
                        'email'           => $email,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                    ];
                    $user->update($input_us);
                }
                $grupo_familiar = Labs_Grupo_Familiar::find($request->id);
                if (is_null($grupo_familiar)) {
                    Labs_Grupo_Familiar::create([
                        'id'              => $request->id,
                        'id_usuario'      => $usuario_mail->id,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                        'ip_creacion'     => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                    ]);
                } else {
                    $grupo_familiar->update([
                        'id_usuario'      => $usuario_mail->id,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                    ]);
                }
            } else {
                $email = $request->email;
                $user  = User::find($request->id);
                if (is_null($user)) {
                    $input_us = [
                        'id'               => $request['id'],
                        'nombre1'          => strtoupper($request['nombre1']),
                        'nombre2'          => strtoupper($request['nombre2']),
                        'apellido1'        => strtoupper($request['apellido1']),
                        'apellido2'        => strtoupper($request['apellido2']),
                        'telefono1'        => $request['celular'],
                        'telefono2'        => '1',
                        'id_pais'          => '1',
                        'fecha_nacimiento' => $request['fecha_nacimiento'],
                        'id_tipo_usuario'  => 2,
                        'email'            => $email,
                        'password'         => bcrypt($request['id']),
                        'tipo_documento'   => 1,
                        'estado'           => 1,
                        'imagen_url'       => ' ',
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => '1234517896',
                        'id_usuariomod'    => '1234517896',
                    ];
                    User::create($input_us);
                } else {
                    $input_us = [
                        'email'           => $email,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => '1234517896',
                    ];
                    $user->update($input_us);
                }
                $xusuario = User::find($request->id);
            }

            if (is_null($paciente)) {
                //CREA EL PACIENTE
                //crear paciente
                $input_pac = [
                    'id'                 => $request['id'],
                    'id_usuario'         => $request['id'],
                    'nombre1'            => strtoupper($request['nombre1']),
                    'nombre2'            => strtoupper($request['nombre2']),
                    'apellido1'          => strtoupper($request['apellido1']),
                    'apellido2'          => strtoupper($request['apellido2']),
                    'fecha_nacimiento'   => $request['fecha_nacimiento'],
                    'sexo'               => $request['sexo'],
                    'telefono1'          => $request['celular'],
                    'telefono2'          => '1',
                    'nombre1familiar'    => strtoupper($request['nombre1']),
                    'nombre2familiar'    => strtoupper($request['nombre2']),
                    'apellido1familiar'  => strtoupper($request['apellido1']),
                    'apellido2familiar'  => strtoupper($request['apellido2']),
                    'parentesco'         => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento'     => 1,
                    'id_seguro'          => $id_seguro,
                    'imagen_url'         => ' ',
                    'menoredad'          => 0,
                    'ip_creacion'        => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                    'id_usuariocrea'     => '1234517896',
                    'id_usuariomod'      => '1234517896',
                ];
                Paciente::create($input_pac);
                $paciente = Paciente::find($request->id);
            }
            $input_ex = [
                'id_paciente'      => $paciente->id,
                'anio'             => 0,
                'mes'              => 0,
                'id_protocolo'     => $id_protocolo,
                'id_seguro'        => $id_seguro,
                'id_nivel'         => $id_nivel,
                'est_amb_hos'      => '0',
                'id_doctor_ieced'  => '1234517896',
                'doctor_txt'       => 'PAGADO EN LA WEB',
                'observacion'      => '',
                'id_empresa'       => '0992704152001',
                'cantidad'         => '0',
                'estado'           => '1',
                'realizado'        => '1',
                'valor'            => '0',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => '1234517896',
                'id_usuariomod'    => '1234517896',
                'motivo_descuento' => '',
                //'fecha_orden'      => date('Y-m-d h:i:s'),
                'codigo'           => '0001',
            ];
            $id_orden  = Examen_Orden::insertGetId($input_ex);
            $pexamenes = Examen_Protocolo::where('id_protocolo', $id_protocolo)->get();
            $total     = 0;
            $contador  = 0;
            if ($request->promo == '4' || $request->promo == '5') {
                if ($request->promo == '4') {
                    $examen    = Examen::find('1191');
                    $input_det = [
                        'id_examen_orden' => $id_orden,
                        'id_examen'       => $examen->id,
                        'valor'           => $examen->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                        'id_usuariomod'   => '1234517896',
                    ];
                    Examen_detalle::create($input_det);
                    $total = $examen->valor;
                    $contador++;
                } else {
                    $examen    = Examen::find('1195');
                    $input_det = [
                        'id_examen_orden' => $id_orden,
                        'id_examen'       => $examen->id,
                        'valor'           => $examen->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => '1234517896',
                        'id_usuariomod'   => '1234517896',
                    ];
                    Examen_detalle::create($input_det);
                    $total = $examen->valor;
                    $contador++;
                }
            } else {
                foreach ($pexamenes as $pexamen) {
                    //CREA DETALLE
                    $detalle = Examen_Detalle::where('id_examen_orden', $id_orden)->where('id_examen', $pexamen->id_examen)->first();
                    if (is_null($detalle)) {
                        $contador++;
                        $examen = Examen::find($pexamen->id_examen);
                        //return $examen;
                        $valor    = $examen->valor;
                        $cubre    = 'NO';
                        $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $id_nivel)->first();
                        if (!is_null($ex_nivel)) {
                            if ($ex_nivel->valor1 != 0) {
                                $valor = $ex_nivel->valor1;
                                $cubre = 'SI';
                            }
                        }
                        $input_det = [
                            'id_examen_orden' => $id_orden,
                            'id_examen'       => $examen->id,
                            'valor'           => $valor,
                            'cubre'           => $cubre,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => '1234517896',
                            'id_usuariomod'   => '1234517896',
                        ];
                        Examen_detalle::create($input_det);
                        /// orden
                        $total += $valor;
                    }
                }
            }
            //ACTUALIZA ORDEN
            $total = round($total, 2);
            /*$descuento_p = $orden->descuento_p;
            if ($orden->descuento_p == null) {
            $descuento_valor = 0;
            $descuento_p     = 0;
            }*/
            /*if ($orden->descuento_p > 100) {
            $descuento_valor = $total;
            } else {
            $descuento_valor = $orden->descuento_p * $total / 100;
            $descuento_valor = round($descuento_valor, 2);
            }*/
            //$subtotal_pagar = $total - $descuento_valor;
            $subtotal_pagar = $total;
            $forma_pago     = Forma_de_pago::find('1');
            $recargo_p      = $forma_pago->recargo_p;
            $recargo_valor  = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor  = round($recargo_valor, 2);
            $valor_total    = $subtotal_pagar + $recargo_valor;
            $valor_total    = round($valor_total, 2);
            $orden          = Examen_Orden::find($id_orden);
            $input_ex2      = [
                'descuento_valor' => '0',
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                'cantidad'        => $contador,
                'valor'           => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => '1234517896',
                'pago_online'     => '1', //indica que es un pago en linea
                'estado_pago'     => '0', //se confirmo el pago o no
            ];
            $orden->update($input_ex2);
            Log_usuario::create([
                'id_usuario'  => '1234517896',
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GENERA COTIZACION WEB",
                'dato_ant1'   => $paciente->id,
                'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
            ]);
            /*$nombre_paciente = $paciente->apellido1;
        if($paciente->apellido2!='(N/A)' || $paciente->apellido2!='N/A'){
        $nombre_paciente = $nombre_paciente.' '.$paciente->apellido2;
        }
        $nombre_paciente = $nombre_paciente.' '.$paciente->apellido1;
        if($paciente->nombre1!='(N/A)' || $paciente->nombre1!='N/A'){
        $nombre_paciente = $nombre_paciente.' '.$paciente->nombre1;
        }

        $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $xusuario, "paciente" => $paciente);
        $correo = $xusuario->email;
        Mail::send('mails.labs_online', $msj_labs, function ($msj) use ($correo) {
        $msj->from("noreply@labs.ec", "HUMANLABS");
        $msj->subject('Resultados de Exámenes de Laboratorio');
        $msj->to($correo);
        $msj->bcc('torbi10@hotmail.com');

        });*/
        } else {
            return "Seleccione Paquete Preventivo";
        }

        /*
        INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
         */
        $RUC_LABS                 = '0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_iva      = 0;
        $_pasarela_pagos_total    = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)
        $pyf_details = array();
        array_push($pyf_details, array(
            "sku"      => '' . $id_orden . '',
            "name"     => 'LABS.EC Orden de laboratorio #' . $id_orden,
            "qty"      => 1,
            "price"    => $_pasarela_pagos_subtotal,
            "tax"      => 0.00,
            "discount" => 0.00, //falta revisar
            "total"    => $_pasarela_pagos_subtotal,
        ));
        $celular = $request['celular'];
        if (strlen($request['celular']) < 10) {
            $celular = '0900000001';
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        //json de invocación
        $data_array = array(
            "company"        => $RUC_LABS,
            "person"         => array(
                "document"     => $request['id'],
                "documentType" => $this->getDocumentType($request['id']),
                "name"         => $this->cleanNames(strtoupper($request['nombre1']) . ' ' . strtoupper($request['nombre2'])),
                "surname"      => $this->cleanNames(strtoupper($request['apellido1']) . ' ' . strtoupper($request['apellido2'])),
                "email"        => $email,
                "mobile"       => $celular,
            ),
            "paymentRequest" => array(
                "orderId"     => '' . $id_orden . '',
                "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                "items"       => array(
                    "item" => $pyf_details, //pending
                ),
                "amount"      => array(

                    "taxes"    => array(
                        array(
                            "kind"   => "Iva",
                            "amount" => 0.00,
                            "base"   => $_pasarela_pagos_subtotal,
                        ),
                    ),

                    "currency" => "USD",
                    "total"    => $_pasarela_pagos_total,
                ),
            ),
            "returnUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=" . $id_orden, //URL DE RETORNO
            "cancelUrl"      => "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=" . $id_orden, //URL DE CANCELACION
            "userAgent"      => "labs_ec/1",
        );

        $manage    = json_encode($data_array);
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response  = json_decode($make_call, true);
        if ($response['status'] != null) {
            if ($response['status']['status'] == 'success') {
                $pyf_checkout_url = $response['processUrl'];
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
            }
        } else {
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
        FIN DE INVOCACION A API DE BOTON DE PAGOS
         */

        return ['estado' => 'ok', 'usuario' => $xusuario->id, 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
    }

    public function mail_externo_web($id_paciente, $id_usuario)
    {
        $paciente        = Paciente::find($id_paciente);
        $usuario         = User::find($id_usuario);
        $nombre_paciente = $paciente->apellido1;
        if ($paciente->apellido2 != '(N/A)' || $paciente->apellido2 != 'N/A') {
            $nombre_paciente = $nombre_paciente . ' ' . $paciente->apellido2;
        }
        $nombre_paciente = $nombre_paciente . ' ' . $paciente->apellido1;
        if ($paciente->nombre1 != '(N/A)' || $paciente->nombre1 != 'N/A') {
            $nombre_paciente = $nombre_paciente . ' ' . $paciente->nombre1;
        }
        $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $usuario, "paciente" => $paciente);
        $correo   = $usuario->email;
        Mail::send('mails.labs_online', $msj_labs, function ($msj) use ($correo) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Resultados de Exámenes de Laboratorio');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');
        });

        return "ok";
    }

    public function buscapaciente($id)
    {
        $paciente = Paciente::find($id);
        if (!is_null($paciente)) {
            //Busca el paciente en el grupo Familiar
            $email          = '';
            $grupo_familiar = Labs_Grupo_Familiar::find($id);
            if (!is_null($grupo_familiar)) {
                $gf_user = User::find($grupo_familiar->id_usuario);
                if (!is_null($gf_user)) {
                    $email = $gf_user->email;
                }
            } else {
                $email = $paciente->usuario->email;
            }
            return ['id' => $paciente->id, 'nombre1' => $paciente->nombre1, 'nombre2' => $paciente->nombre2, 'apellido1' => $paciente->apellido1, 'apellido2' => $paciente->apellido2, 'telefono1' => $paciente->telefono1, 'direccion' => $paciente->direccion, 'email' => $email, 'sexo' => $paciente->sexo, 'fecha_nacimiento' => $paciente->fecha_nacimiento, 'resultado' => 'ok'];
        } else {
            return ['resultado' => 'no'];
        }
    }

    public function buscar_orden($id_orden)
    {

        $orden = Examen_Orden::find($id_orden);
        if (!is_null($orden)) {
            $paciente = $orden->paciente;

            //Busca el paciente en el grupo Familiar
            $email          = '';
            $grupo_familiar = Labs_Grupo_Familiar::find($paciente->id);
            if (!is_null($grupo_familiar)) {
                $gf_user = User::find($grupo_familiar->id_usuario);
                if (!is_null($gf_user)) {
                    $email = $gf_user->email;
                }
            } else {
                $email = $paciente->usuario->email;
            }
            $subtotal = 0;
            foreach ($orden->detalles as $value) {
                $subtotal += $value->valor;
            }

            $seguro = $orden->seguro->tipo;
            if ($seguro == '0') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " Pertenece a un Seguro Público y No se puede pagar por este medio";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($seguro == '1') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " Pertenece a un Seguro Privado y No se puede pagar por este medio";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->pago_online == '1') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " ya se encuentra pagada, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->estado_pago == '1') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " ya se encuentra pagada, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->total_valor == '0') {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " no tiene valor a pagar, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($orden->cantidad != $orden->detalles->count()) {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " tiene inconsistencia, consultar con administración";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($subtotal - $orden->valor > 1) {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " tiene inconsistencia, consultar con administración.";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }
            if ($subtotal - $orden->valor < -1) {
                $mensaje = "El número de cotización " . $id_orden . " del Paciente " . $orden->id_paciente . " tiene inconsistencia, consultar con administración..";
                return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
            }

            $detalles = $orden->detalles;
            $age      = 0;
            if ($orden->paciente->fecha_nacimiento != null) {
                $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
            }
            return view('laboratorio.externo.cotizacion_externa', ['orden' => $orden, 'detalles' => $detalles, 'age' => $age, 'email' => $email]);
        }

        $mensaje = "El número de cotización " . $id_orden . " No se encuentra registrada en el sistema, consultar con la administración";
        return view('laboratorio.externo.mensaje', ['mensaje' => $mensaje]);
    }

    public function pagar_orden(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $reglas     = [
            'email' => 'email',
        ];
        $mensaje = [
            'email.email' => 'El Email tiene error en el formato.',
            //'email.unique' => 'El Email ya se encuentra registrado.',
        ];
        $this->validate($request, $reglas, $mensaje);

        $orden = Examen_Orden::find($request->id_orden);
        if (!is_null($orden)) {
            $paciente = $orden->paciente;

            //Busca el paciente en el grupo Familiar
            $email          = '';
            $grupo_familiar = Labs_Grupo_Familiar::find($paciente->id);
            if (!is_null($grupo_familiar)) {
                $gf_user = User::find($grupo_familiar->id_usuario);
                if (!is_null($gf_user)) {
                    $email    = $gf_user->email;
                    $xusuario = $gf_user;
                }
            } else {
                $email    = $paciente->usuario->email;
                $xusuario = $paciente->usuario;
            }
            $cambio = false;
            if ($email != $request->email) {
                $cambio = true;
            }
            if ($cambio) {
                $usuario = User::where('email', $request->email)->first();
                if (!is_null($usuario)) {
                    //Si encuentra el correo, crea o actualiza el grupo familiar
                    $xusuario = $usuario;
                    if (!is_null($gf_user)) {
                        $gf_user->update([
                            'id_usuario'      => $usuario->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $usuario->id,
                        ]);
                    } else {
                        Labs_Grupo_Familiar::create([
                            'id'              => $paciente->id,
                            'id_usuario'      => $usuario->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $usuario->id,
                            'ip_creacion'     => $ip_cliente,
                            'id_usuariocrea'  => $usuario->id,
                        ]);
                    }
                } else {
                    $paciente->usuario->update([
                        'email' => $request->email,
                    ]);
                    $xusuario = $paciente->usuario;
                }
                $input_log = [
                    'id_usuario'  => $paciente->id,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "ACTUALIZA PACIENTE MAIL PAGO LABS",
                    'dato_ant1'   => $email,
                    'dato1'       => $request->email,
                    'dato_ant4'   => "COTIZACION",
                    'dato2'       => 'COTIZACION',
                ];

                Log_usuario::create($input_log);
            }
            //ACTUALIZA LA ORDEN
            $orden->update([
                'fecha_orden' => null,
                'anio'        => '0',
                'mes'         => '0',
                'estado_pago' => '1',
                'pago_online' => '1',
            ]);
            $input_log = [
                'id_usuario'  => $paciente->id,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "PAGO ONLINE LABS",
                'dato_ant1'   => 'ORDEN : ' . $orden->id,
                'dato1'       => 'PACIENTE : ' . $paciente->id,
                'dato_ant4'   => "COTIZACION",
                'dato2'       => 'COTIZACION',
            ];

            Log_usuario::create($input_log);

            /*$nombre_paciente = $paciente->apellido1;
            if($paciente->apellido2!='(N/A)' || $paciente->apellido2!='N/A'){
            $nombre_paciente = $nombre_paciente.' '.$paciente->apellido2;
            }
            $nombre_paciente = $nombre_paciente.' '.$paciente->apellido1;
            if($paciente->nombre1!='(N/A)' || $paciente->nombre1!='N/A'){
            $nombre_paciente = $nombre_paciente.' '.$paciente->nombre1;
            }

            $msj_labs = array("nombre_paciente" => $nombre_paciente, "user" => $xusuario, "paciente" => $paciente);
            $correo = $xusuario->email;
            Mail::send('mails.labs_online', $msj_labs, function ($msj) use ($correo) {
            $msj->from("noreply@labs.ec", "HUMANLABS");
            $msj->subject('Resultados de Exámenes de Laboratorio');
            $msj->to($correo);
            $msj->bcc('torbi10@hotmail.com');

            });*/

            return ['estado' => 'ok', 'usuario' => $xusuario->id, 'paciente' => $paciente->id];
        }

        return "error";
    }

    public function carrito_paciente()
    {
        $covid_local     = Examen::find('1191');
        $covid_domicilio = Examen::find('1195');

        return view('laboratorio/externo/carrito_paciente', ['covid_local' => $covid_local, 'covid_domicilio' => $covid_domicilio]);
    }

    public function carrito_guardar_orden(Request $request)
    {
        if ($request['token'] != '26ae11be61ffd5cda37477a54f6d1355') {
            return "credenciales invalidas";
        }

        $arr_examen = $request['examenes']; //RECIBO ARREGLO DE LOS EXAMENES DEL COTIZADOR
        //dd($arr_examen,$request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //SELECCIONA LA PROMO
        $id_seguro = '1'; //PARTICULAR
        $reglas    = [
            'email' => 'email',
        ];
        $mensaje = [
            'email.email'  => 'El Email tiene error en el formato.',
            'email.unique' => 'El Email ya se encuentra registrado.',
        ];
        $this->validate($request, $reglas, $mensaje);
        $paciente = Paciente::find($request->id);
        if (!is_null($paciente)) {
            //NO EXISTE PACIENTE
            $usuario_mail = User::where('email', $request->email)->where('id', '<>', $paciente->id_usuario)->first();
        } else {
            //EXISTE PACIENTE
            $usuario_mail = User::where('email', $request->email)->first();
        }

        if (!is_null($usuario_mail)) {
            $xusuario = $usuario_mail;
            //Si encuentra el correo, crea o actualiza el grupo familiar
            $email = $request->id . '@correo.dum';
            $user  = User::find($request->id);
            if (is_null($user)) {
                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $grupo_familiar = Labs_Grupo_Familiar::find($request->id);
            if (is_null($grupo_familiar)) {
                Labs_Grupo_Familiar::create([
                    'id'              => $request->id,
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                ]);
            } else {
                $grupo_familiar->update([
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ]);
            }
        } else {
            $email = $request->email;
            $user  = User::find($request->id);
            if (is_null($user)) {

                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $xusuario = User::find($request->id);
        }

        //dd($input_us,$request->all());

        if (is_null($paciente)) {
            //CREA EL PACIENTE
            //crear paciente
            $input_pac = [
                'id'                 => $request['id'],
                'id_usuario'         => $request['id'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
                'sexo'               => $request['sexo'],
                'telefono1'          => $request['celular'],
                'direccion'          => $request['direccion'],
                'telefono2'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'tipo_documento'     => 1,
                'id_seguro'          => $id_seguro,
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => '1234517896',
                'id_usuariomod'      => '1234517896',
            ];
            Paciente::create($input_pac);
            $input_log = [
                'id_usuario'  => $request['id'],
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "COTIZADOR",
                'dato2'       => 'PAGON EN LINEA LABS',
            ];

            Log_usuario::create($input_log);
            $paciente = Paciente::find($request->id);
        } else {
            $input_pac2 = [
                'telefono1'       => $request['celular'],
                'direccion'       => $request['direccion'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => '1234517896',
            ];
            $paciente->update($input_pac2);
        }

        $input_ex = [
            'id_paciente'       => $paciente->id,
            'anio'              => 0,
            'mes'               => 0,
            'id_seguro'         => $id_seguro,
            'est_amb_hos'       => '0',
            'id_doctor_ieced'   => '1234517896',
            'doctor_txt'        => 'INGRESADO EN LA WEB',
            'observacion'       => '',
            'id_empresa'        => '0992704152001',
            'cantidad'          => '0',
            'estado'            => '0',
            'realizado'         => '0',
            'valor'             => '0',
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => '1234517896',
            'id_usuariomod'     => '1234517896',
            'motivo_descuento'  => '',
            'fecha_tentativa'   => $request['fecha_tentativa'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
            //'fecha_orden'     => date('Y-m-d h:i:s'),
        ];

        //return ["Holaaaa" , $request->all()];
        /*$orden = Examen_Orden::where('id_paciente',$paciente->id)->where('estado','0')->where('pago_online','1')->first();
        if(!is_null($orden)){
        $orden->update($input_ex);
        $detalles = $orden->detalles;
        foreach ($detalles as $d) {
        $d->delete();
        }
        $id_orden = $orden->id;
        }else{
        $id_orden = Examen_Orden::insertGetId($input_ex);
        }*/

        $id_orden = Examen_Orden::insertGetId($input_ex);

        $input_cli = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => '5',
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => '1234517896',
            'id_usuariomod'           => '1234517896',
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();
        //return "holsaaa";
        //return [$paciente->id, $cliente];
        //echo $input_cli;
        //return "<b> .'$input_cli'.</b>";

        if (!is_null($cliente)) {

            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli);
            //return [$cliente];
            //$cliente->update($input_cli);
        } else {

            Ct_Clientes::create($input_cli);
        }

        $total    = 0;
        $contador = 0;
        $txt_ex   = '';
        //$pct_dcto          = 0.20;
        $pct_dcto          = 0;
        $descuento_detalle = 0;
        foreach ($arr_examen as $value) {

            $examen = Examen::find($value);
            //dd($value,$examen);
            if (!is_null($examen)) {
                $txt_ex          = $txt_ex . '+' . $examen->nombre;
                $p_descuento     = 0;
                $valor_descuento = 0;
                /*if ($examen->humanlabs == '1') {*/
                $p_descuento     = $pct_dcto;
                $valor_descuento = $examen->valor * $p_descuento;

                $valor_descuento = round($valor_descuento, 2);
                //$valor_descuento = floor($valor_descuento * 100)/100;
                $descuento_detalle += $valor_descuento;
                /*}*/
                $input_det = [
                    'id_examen_orden' => $id_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $examen->valor,
                    'human_labs'      => $examen->humanlabs,
                    'p_descuento'     => $p_descuento,
                    'valor_descuento' => $valor_descuento,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                    'id_usuariomod'   => '1234517896',
                ];
                Examen_detalle::create($input_det);
                $total = $total + $examen->valor;
                $contador++;
            }
        }
        /*if ($request['pres_dom'] == '1') {
        $examen = Examen::find('1203');
        //dd($value,$examen);
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
        $contador++;
        }
        }*/

        //ACTUALIZA ORDEN
        $total             = round($total, 2);
        $recargo_p         = 0;
        $descuento_detalle = round($descuento_detalle, 2);
        $subtotal_pagar    = $total - $descuento_detalle;

        $recargo_valor = $subtotal_pagar * $recargo_p / 100;
        $recargo_valor = round($recargo_valor, 2);
        $valor_total   = $subtotal_pagar + $recargo_valor;
        $valor_total   = round($valor_total, 2);
        $orden         = Examen_Orden::find($id_orden);
        $input_ex2     = [
            'recargo_valor'   => $recargo_valor,
            'total_valor'     => $valor_total,
            'cantidad'        => $contador,
            'valor'           => $total,
            'descuento_valor' => $descuento_detalle,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => '1234517896',
            'pago_online'     => '1',
            'estado_pago'     => '0',
            'pres_dom'        => $request->pres_dom,
        ];
        $orden->update($input_ex2);
        /*Log_usuario::create([
        'id_usuario'  => '1234517896',
        'ip_usuario'  => $ip_cliente,
        'descripcion' => "GENERA COTIZACION WEB",
        'dato_ant1'   => $paciente->id,
        'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
        ]);*/
        Log_usuario::create([
            'id_usuario'  => $paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "GENERA COTIZACION WEB",
            'dato_ant4'   => $txt_ex,
        ]);
        //return "ok";
        return view('laboratorio.externo.lista_confirmar', ['orden' => $orden, 'detalles' => $orden->detalles]);
    }

    public function paquete_guardar_orden(Request $request)
    {
        if ($request['token'] != '26ae11be61ffd5cda37477a54f6d1355') {
            return "credenciales invalidas";
        }

        $paquete = [
            '27' => '40',
            '28' => '41',
            '29' => '42',
            '30' => '43',
            '31' => '44',
            '32' => '45',
            '33' => '46',
            '93' => '68',
            '94' => '77',
            '95' => '81'
        ];

        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //SELECCIONA LA PROMO
        $nivel              = $paquete[$request->id_protocolo]; //PARTICULAR
        $id_seguro          = '35';
        $protocolo          = Protocolo::find($request->id_protocolo);
        $examenes_protocolo = $protocolo->examenes;
        //dd($protocolo,$examenes_protocolo);
        $reglas = [
            'email' => 'email',
        ];
        $mensaje = [
            'email.email'  => 'El Email tiene error en el formato.',
            'email.unique' => 'El Email ya se encuentra registrado.',
        ];
        $this->validate($request, $reglas, $mensaje);
        $paciente = Paciente::find($request->id);
        if (!is_null($paciente)) {
            //BUSCA SI EL CORREO EXISTE EN OTRO USUARIO
            $usuario_mail = User::where('email', $request->email)->where('id', '<>', $paciente->id_usuario)->first();
        } else {
            //BUSCA SI EXISTE EL CORREO
            $usuario_mail = User::where('email', $request->email)->first();
        }

        if (!is_null($usuario_mail)) {
            //SI ENCUENTRA EL CORREO
            $xusuario = $usuario_mail;
            //Si encuentra el correo, crea o actualiza el grupo familiar
            $email = $request->id . '@correo.dum';
            $user  = User::find($request->id);
            if (is_null($user)) {
                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $grupo_familiar = Labs_Grupo_Familiar::find($request->id);
            if (is_null($grupo_familiar)) {
                Labs_Grupo_Familiar::create([
                    'id'              => $request->id,
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                ]);
            } else {
                $grupo_familiar->update([
                    'id_usuario'      => $usuario_mail->id,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ]);
            }
        } else {
            $email = $request->email;
            $user  = User::find($request->id);
            if (is_null($user)) {
                $input_us = [
                    'id'               => $request['id'],
                    'nombre1'          => strtoupper($request['nombre1']),
                    'nombre2'          => strtoupper($request['nombre2']),
                    'apellido1'        => strtoupper($request['apellido1']),
                    'apellido2'        => strtoupper($request['apellido2']),
                    'telefono1'        => $request['celular'],
                    'direccion'        => $request['direccion'],
                    'telefono2'        => '1',
                    'id_pais'          => '1',
                    'fecha_nacimiento' => $request['fecha_nacimiento'],
                    'id_tipo_usuario'  => 2,
                    'email'            => $email,
                    'password'         => bcrypt($request['id']),
                    'tipo_documento'   => 1,
                    'estado'           => 1,
                    'imagen_url'       => ' ',
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => '1234517896',
                    'id_usuariomod'    => '1234517896',
                ];
                User::create($input_us);
            } else {
                $input_us = [
                    'email'           => $email,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => '1234517896',
                ];
                $user->update($input_us);
            }
            $xusuario = User::find($request->id);
        }

        //dd($input_us,$request->all());

        if (is_null($paciente)) {
            //CREA EL PACIENTE
            //crear paciente
            $input_pac = [
                'id'                 => $request['id'],
                'id_usuario'         => $request['id'],
                'nombre1'            => strtoupper($request['nombre1']),
                'nombre2'            => strtoupper($request['nombre2']),
                'apellido1'          => strtoupper($request['apellido1']),
                'apellido2'          => strtoupper($request['apellido2']),
                'fecha_nacimiento'   => $request['fecha_nacimiento'],
                'sexo'               => $request['sexo'],
                'telefono1'          => $request['celular'],
                'direccion'          => $request['direccion'],
                'telefono2'          => '1',
                'nombre1familiar'    => strtoupper($request['nombre1']),
                'nombre2familiar'    => strtoupper($request['nombre2']),
                'apellido1familiar'  => strtoupper($request['apellido1']),
                'apellido2familiar'  => strtoupper($request['apellido2']),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'tipo_documento'     => 1,
                'id_seguro'          => $id_seguro,
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => '1234517896',
                'id_usuariomod'      => '1234517896',
            ];
            Paciente::create($input_pac);
            $input_log = [
                'id_usuario'  => $request['id'],
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $request['id'],
                'dato1'       => strtoupper($request['nombre1'] . " " . $request['nombre2'] . " " . $request['apellido1'] . " " . $request['apellido2']),
                'dato_ant4'   => "PROMOS",
                'dato2'       => 'PAGON EN LINEA LABS',
            ];

            Log_usuario::create($input_log);
            $paciente = Paciente::find($request->id);
        } else {
            $input_pac2 = [
                'telefono1'       => $request['celular'],
                'direccion'       => $request['direccion'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => '1234517896',
            ];
            $paciente->update($input_pac2);
        }

        $input_ex = [
            'id_paciente'       => $paciente->id,
            'anio'              => 0,
            'mes'               => 0,
            'id_seguro'         => $id_seguro,
            'id_nivel'          => $nivel,
            'est_amb_hos'       => '0',
            'id_doctor_ieced'   => '1234517896',
            'doctor_txt'        => 'INGRESADO EN LA WEB',
            'observacion'       => '',
            'id_empresa'        => '0992704152001',
            'cantidad'          => '0',
            'estado'            => '0',
            'realizado'         => '0',
            'valor'             => '0',
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => '1234517896',
            'id_usuariomod'     => '1234517896',
            'motivo_descuento'  => '',
            'fecha_tentativa'   => $request['fecha_tentativa'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
            //'fecha_orden'      => date('Y-m-d h:i:s'),
        ];

        /*$orden = Examen_Orden::where('id_paciente',$paciente->id)->where('estado','0')->where('pago_online','1')->first();
        if(!is_null($orden)){
        $orden->update($input_ex);
        $detalles = $orden->detalles;
        foreach ($detalles as $d) {
        $d->delete();
        }
        $id_orden = $orden->id;
        }else{
        $id_orden = Examen_Orden::insertGetId($input_ex);
        }*/

        $id_orden = Examen_Orden::insertGetId($input_ex);

        $input_cli = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => '5',
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => '1234517896',
            'id_usuariomod'           => '1234517896',
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();
        //return "holsaaa";
        //return [$paciente->id, $cliente];
        //echo $input_cli;
        //return "<b> .'$input_cli'.</b>";

        if (!is_null($cliente)) {

            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli);
            //return [$cliente];
            //$cliente->update($input_cli);
        } else {

            Ct_Clientes::create($input_cli);
        }

        $pct_dcto          = 0.20;
        $sin_desc = ['94', '95'];
        if (in_array($request->id_protocolo, $sin_desc)) {
            $pct_dcto          = 0.00;
        }

        $total             = 0;
        $descuento_detalle = 0;
        $contador          = 0;

        foreach ($examenes_protocolo as $value) {

            $examen = Examen::find($value->id_examen);
            //dd($value,$examen);
            if (!is_null($examen)) {
                $valor    = $examen->valor;
                $cubre    = 'NO';
                $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                if (!is_null($ex_nivel)) {
                    if ($ex_nivel->valor1 != 0) {

                        $valor = $ex_nivel->valor1;
                        $cubre = 'SI';
                    }
                }

                $p_descuento     = 0;
                $valor_descuento = 0;
                if ($examen->humanlabs == '1') {
                    $p_descuento     = $pct_dcto;
                    $valor_descuento = $valor * $p_descuento;

                    $valor_descuento = round($valor_descuento, 2);
                    //$valor_descuento = floor($valor_descuento * 100)/100;
                    $descuento_detalle += $valor_descuento;
                }

                $input_det = [
                    'id_examen_orden' => $id_orden,
                    'id_examen'       => $examen->id,
                    'cubre'           => $cubre,
                    'valor'           => $valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => '1234517896',
                    'id_usuariomod'   => '1234517896',
                    'human_labs'      => $examen->humanlabs,
                    'p_descuento'     => $p_descuento,
                    'valor_descuento' => $valor_descuento,

                ];
                Examen_detalle::create($input_det);
                $total = $total + $valor;
                $contador++;
            }
        }
        if ($request['pres_dom'] == '1') {
            $examen = Examen::find('1203');
            //dd($value,$examen);
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
                $contador++;
            }
        }

        //ACTUALIZA ORDEN
        $total             = round($total, 2);
        $recargo_p         = 0;
        $descuento_detalle = round($descuento_detalle, 2);
        $subtotal_pagar    = $total - $descuento_detalle;

        $recargo_valor = $subtotal_pagar * $recargo_p / 100;
        $recargo_valor = round($recargo_valor, 2);
        $valor_total   = $subtotal_pagar + $recargo_valor;
        $valor_total   = round($valor_total, 2);
        $orden         = Examen_Orden::find($id_orden);
        $input_ex2     = [
            'recargo_valor'   => $recargo_valor,
            'total_valor'     => $valor_total,
            'cantidad'        => $contador,
            'valor'           => $total,
            'descuento_valor' => $descuento_detalle,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => '1234517896',
            'pago_online'     => '1',
            'estado_pago'     => '0',
            'pres_dom'        => $request->pres_dom,
        ];
        $orden->update($input_ex2);
        /*Log_usuario::create([
        'id_usuario'  => '1234517896',
        'ip_usuario'  => $ip_cliente,
        'descripcion' => "GENERA COTIZACION WEB",
        'dato_ant1'   => $paciente->id,
        'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
        ]);*/
        Log_usuario::create([
            'id_usuario'  => $paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "GENERA COTIZACION WEB",
            'dato2'       => $protocolo->nombre,
        ]);
        //return "ok";
        return view('laboratorio.externo.lista_confirmar', ['orden' => $orden, 'detalles' => $orden->detalles]);
    }

    /*
    FUNCIONES PARA EL API DEL BOTON DE PAGOS
     */
    /* POST PROCESO ACTUAL
    public function web_postproceso_pago(Request $request)
    {

    $res        = "";
    $ip_cliente = $_SERVER["REMOTE_ADDR"];

    $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
    $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';

    Log_Api::create([
    'descripcion' => 'llego',
    'dato1'       => $request,
    ]);
    if ($request['xmlreq'] != null) {
    $xmlreq             = base64_decode($request['xmlreq']);
    $status             = explode("=", explode("&", $xmlreq)[0])[1];
    $requestId          = explode("=", explode("&", $xmlreq)[1])[1];
    $refRequestId       = explode("=", explode("&", $xmlreq)[2])[1];
    $orderId            = explode("=", explode("&", $xmlreq)[3])[1];
    $date               = explode("=", explode("&", $xmlreq)[4])[1];
    $signature          = explode("=", explode("&", $xmlreq)[5])[1];
    $signature_to_check = sha1($status . $date . $PAGOSYFACTURAS_APPSECRET);
    if ($signature_to_check == $signature) {
    //
    date_default_timezone_set('America/Guayaquil');
    $pagoenlinea = DB::table('pagosenlinea')->where('request_id', $requestId)->first();

    if (!is_null($pagoenlinea)) {
    $fecha_hoy = date('Y-m-d');
    $id        = $pagoenlinea->id;
    //ACTUALIZA EL REGISTRO DE PAGO
    DB::table('pagosenlinea')
    ->where('request_id', $requestId)
    ->update(['clave' => $orderId, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]);
    } else {
    //AGREGA EL REGISTRO DE PAGO
    $fecha_hoy = date('Y-m-d');

    $id = DB::table('pagosenlinea')->insertGetId(
    ['tipo' => 'examen_orden', 'clave' => $orderId, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]
    );
    }
    //ACTUALIZA LA ORDEN DE EXAMEN SI EL PAGO FUE APROVADO
    if ($status == "APPROVED" || $status == "APPROVED_PARTIAL") {
    //ACTUALIZA EL ESTADO DE LA ORDEN DE EXAMEN A PAGADO 1
    $orden              = examen_orden::find($orderId);
    $orden->estado_pago = "1"; //aqui se actualiza el estado
    $orden->estado      = "1";
    $orden->realizado   = "1";
    $orden->save();
    Log_usuario::create([
    'id_usuario'  => $orden->paciente->id_usuario,
    'ip_usuario'  => $ip_cliente,
    'descripcion' => "LABORATORIO",
    'dato_ant1'   => $orderId,
    'dato1'       => $orden->id_paciente,
    'dato_ant2'   => "SE APRUEBA PAGO DE LA ORDEN",
    'dato2'       => $requestId,
    ]);
    }
    //ACTUALIZA LOS DETALLES DEL PAGO
    $this->getPaymentInformation($requestId);
    $res = $res . "Id=" . $id; //RETORNA EL ID DEL REGISTRO QUE SE INGRESO
    return $res;
    //}

    } else {
    echo "ERROR";
    }
    }
    } */

    /* NUEVO POST PROCESO */
    public function web_postproceso_pago(Request $request)
    {

        $res        = "";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';

        Log_Api::create([
            'descripcion' => 'llego',
            'dato1'       => $request,
        ]);
        if ($request['xmlreq'] != null) {
            $xmlreq             = base64_decode($request['xmlreq']);
            $status             = explode("=", explode("&", $xmlreq)[0])[1];
            $requestId          = explode("=", explode("&", $xmlreq)[1])[1];
            $refRequestId       = explode("=", explode("&", $xmlreq)[2])[1];
            $orderId            = explode("=", explode("&", $xmlreq)[3])[1];
            $date               = explode("=", explode("&", $xmlreq)[4])[1];
            $signature          = explode("=", explode("&", $xmlreq)[5])[1];
            $signature_to_check = sha1($status . $date . $PAGOSYFACTURAS_APPSECRET);
            if ($signature_to_check == $signature) {
                //
                date_default_timezone_set('America/Guayaquil');
                $pagoenlinea = DB::table('pagosenlinea')->where('request_id', $requestId)->first();

                $SUFIJO_ID_ORDEN = substr($orderId, 0, 3);
                $ID_ORDEN        = substr($orderId, 3);

                if ($SUFIJO_ID_ORDEN != 'ORD' && $SUFIJO_ID_ORDEN != 'MEM') {
                    //SACAR ESTO CUANDO YA NO ESTE EN LA WEB
                    $ID_ORDEN = $orderId;
                }

                if (!is_null($pagoenlinea)) {

                    $fecha_hoy = date('Y-m-d');
                    $id        = $pagoenlinea->id;
                    //ACTUALIZA EL REGISTRO DE PAGO
                    DB::table('pagosenlinea')
                        ->where('request_id', $requestId)
                        ->update(['clave' => $ID_ORDEN, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]);
                } else {
                    //AGREGA EL REGISTRO DE PAGO
                    $fecha_hoy = date('Y-m-d');

                    $tipo_pago = '';
                    if ($SUFIJO_ID_ORDEN == 'MEM') {
                        //PAGO DE MEMBRESIA
                        $tipo_pago = 'pago_membresia';
                    } else {
                        //PAGO DE ORDEN
                        $tipo_pago = 'examen_orden';
                    }

                    $id = DB::table('pagosenlinea')->insertGetId(
                        ['tipo' => $tipo_pago, 'clave' => $ID_ORDEN, 'request_id' => $requestId, 'ref_request_id' => $refRequestId, 'fecha' => $fecha_hoy, 'pasarela' => 'PAGOS&FACTURAS', 'estado' => $status]
                    );
                }

                if ($status == "APPROVED" || $status == "APPROVED_PARTIAL") {
                    if ($SUFIJO_ID_ORDEN == 'MEM') {
                        //PAGO DE MEMBRESIA
                        //use Sis_medico\Contable;
                        $data_array = array(
                            "id_orden" => $ID_ORDEN, //tipo de de orden de venta
                            //"id_usuario"  => $cedula_usuario, //fecha de orden
                        );
                        $response = Contable::update_data($data_array);
                    } else {
                        //ORDEN
                        //ACTUALIZA LA ORDEN DE EXAMEN SI EL PAGO FUE APROVADO
                        //ACTUALIZA EL ESTADO DE LA ORDEN DE EXAMEN A PAGADO 1
                        $orden              = examen_orden::find($ID_ORDEN);
                        $orden->estado_pago = "1"; //aqui se actualiza el estado
                        $orden->estado      = "1";
                        $orden->realizado   = "1";
                        $orden->save();
                        Log_usuario::create([
                            'id_usuario'  => $orden->paciente->id_usuario,
                            'ip_usuario'  => $ip_cliente,
                            'descripcion' => "LABORATORIO",
                            'dato_ant1'   => $ID_ORDEN,
                            'dato1'       => $orden->id_paciente,
                            'dato_ant2'   => "SE APRUEBA PAGO DE LA ORDEN",
                            'dato2'       => $requestId,
                        ]);
                    }
                }

                //ACTUALIZA LOS DETALLES DEL PAGO
                $this->getPaymentInformation($requestId);
                $res = $res . "Id=" . $id; //RETORNA EL ID DEL REGISTRO QUE SE INGRESO
                return $res;
                //}

            } else {
                echo "ERROR";
            }
        }
    }

    public function web_retorno_pago(Request $request)
    {
        /*return "<html><head></head><body><script>window.parent.location.href='http://186.70.157.2/sis_medico/public/laboratorio/externo/web/'</script></body></html>";*/
        return "<html><head></head><body><script>location.href='https://labs.ec'</script></body></html>";
    }

    public function web_cancelacion_pago(Request $request)
    {
        /*return "<html><head></head><body><script>window.parent.location.href='http://186.70.157.2/sis_medico/public/laboratorio/externo/web/'</script></body></html>";*/
        return "<html><head></head><body><script>location.href='https://labs.ec'</script></body></html>";
    }

    public function getPaymentInformation($requestId)
    {
        /*
        INVOCA API DE PAGOS&FACTURAS Y OBTIENE LA INFORMACION DEL PAGO EXITOSO
         */
        //$RUC_LABS='0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $pyf_checkout_url         = 'https://vpos.accroachcode.com/';
        //detalle(s)
        //json de invocación
        $data_array = array();
        $manage     = json_encode($data_array);
        $make_call  = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/' . $requestId, $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response   = json_decode($make_call, true);
        Log_usuario::create([
            'id_usuario'  => '0922290697',
            'ip_usuario'  => 'post_proc',
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => '1',
            'dato1'       => '1',
            'dato_ant2'   => "RESPUESTA DEL POST",
            'dato2'       => "prb",
            'dato_ant4'   => $make_call,
        ]);
        if ($response['status'] != null) {

            //ge payment information
            $payment_details         = $response['details'];
            $payment_nro_comprobante = $response['comprobante'];

            DB::table('pagosenlinea')
                ->where('request_id', $requestId)
                ->update([
                    'estado'          => $response['status']['status'],
                    'interes'         => $response['details']['interest'],
                    'credittype'      => $response['details']['creditType'], //00 corriente - 02 o 03 diferido -
                    'issuer'          => $response['details']['isssuerName'],
                    'paymentmethod'   => $response['details']['paymentMethodName'], //tarjetamarca
                    'lastdigits'      => $response['details']['lastDigits'],
                    'expiration'      => $response['details']['expiration'],
                    'pago_auth'       => $response['details']['authorization'],
                    'nro_comprobante' => $payment_nro_comprobante,
                ]);
        }
    }

    public function callAPI($method, $url, $data, $appId, $appSecret)
    {
        $Nonce   = $this->getNonce(12);
        $Date    = date('c');
        $Token   = base64_encode(sha1($Nonce . $Date . $appSecret));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $Date . "\r\n" . "Nonce: " . base64_encode($Nonce) . "\r\n",
                'method'  => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);
        //dd($context, $url);
        return file_get_contents($url, false, $context);
    }

    public function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function getDocumentType($document)
    {
        if (strlen($document) == 10) {
            return '05'; //CEDULA
        } else if (strlen($document) == 13) {
            return '04'; //RUC
        } else {
            return '06'; //PASAPORTE
        }
    }

    public function cleanNames($valor)
    {
        $vowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "'", '"', "|", "^");
        return str_replace($vowels, "", $valor);
    }

    /*public function carrito_pago($id_orden)//FUNCION QUE INVOCA LO DE JUANK
    {
    $orden = Examen_Orden::find($id_orden);
    $paciente = $orden->paciente;
    $id_paciente = $paciente->id;
    $grupo_fam = Labs_Grupo_Familiar::find($id_paciente);
    if (!is_null($grupo_fam)) {
    $user_aso = User::find($grupo_fam->id_usuario);
    } else {
    $user_aso = User::find($paciente->id_usuario);
    }
    $email = $user_aso->email;
    if(is_null($orden)){
    return ['estado' => "Error"];
    }
    $valor_total = $orden->total_valor;
    /*
    INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
     */
    /*$RUC_LABS='0993075000001';
    $PAGOSYFACTURAS_APPID='V1oW1RHpw8GtxwGoIkuq';
    $PAGOSYFACTURAS_APPSECRET='SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
    $_pasarela_pagos_subtotal = $valor_total;
    $_pasarela_pagos_iva = 0;
    $_pasarela_pagos_total = $valor_total;
    $pyf_checkout_url = 'https://vpos.accroachcode.com/';
    //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
    //detalle(s)
    $pyf_details=array();
    array_push($pyf_details,array(
    "sku"    =>  ''.$id_orden.'',
    "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
    "qty"    =>  1,
    "price"   =>  $_pasarela_pagos_subtotal,
    "tax"   =>  0.00,
    "discount"   =>  0.00,  //falta revisar
    "total"   =>  $_pasarela_pagos_subtotal
    ));
    $celular=$paciente->telefono2;
    if($celular==null){
    $celular='0900000001';
    }
    if(strlen($celular)<10){
    $celular='0900000001';
    }
    if(!is_numeric($celular)){
    $celular='0900000001';
    }
    // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
    $nombres=$paciente->nombre1;
    if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
    $nombres = $nombres.' '.$paciente->nombre2;
    }
    $apellidos=$paciente->apellido2;
    if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
    $apellidos = $apellidos.' '.$paciente->apellido2;
    }
    //json de invocación
    $data_array =  array(
    "company"        => $RUC_LABS,
    "person"         => array(
    "document"      => $paciente->id,
    "documentType"  => $this->getDocumentType($paciente->id),
    "name"          => $this->cleanNames(strtoupper($nombres)),
    "surname"       => $this->cleanNames(strtoupper($apellidos)),
    "email"         => $email,
    "mobile"        => $celular
    ),
    "paymentRequest"  => array(
    "orderId"       => ''.$id_orden.'',
    "description"   => "Compra en linea labs",  //PONER EN CONFIGURACION
    "items"         => array(
    "item"        => $pyf_details //pending
    ),
    "amount"    =>  array(

    "taxes"     =>  array(
    array(
    "kind"   =>  "Iva",
    "amount"   =>  0.00,
    "base"   =>  round($_pasarela_pagos_subtotal,2,PHP_ROUND_HALF_UP )
    )
    ),

    "currency" => "USD",
    "total" => round($_pasarela_pagos_total,2,PHP_ROUND_HALF_UP )
    )
    ),
    "returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
    "cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
    "userAgent" =>  "labs_ec/1"
    );

    $manage = json_encode($data_array);
    $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID,$PAGOSYFACTURAS_APPSECRET);
    $response = json_decode($make_call, true);
    if($response['status']!=null){
    if($response['status']['status']=='success'){
    $pyf_checkout_url = $response['processUrl'];
    }
    else{
    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status='.$response['status']['status'];
    }
    }
    else{
    $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
    }
    /*
    FIN DE INVOCACION A API DE BOTON DE PAGOS
     */

    /*
    return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
    //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS

    }*/

    public function carrito_pago($id_orden) //FUNCION QUE INVOCA LO DE JUANK

    {
        //return "hola";
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $orden       = Examen_Orden::find($id_orden);
        $paciente    = $orden->paciente;
        $id_paciente = $paciente->id;
        $grupo_fam   = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }
        $email = $orden->email_factura;
        if (is_null($orden)) {
            return ['estado' => "Error"];
        }
        $valor_total = $orden->total_valor;
        $valor       = $orden->valor;
        /*
        INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
         */
        $RUC_LABS                 = '0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_subtotal = round($_pasarela_pagos_subtotal * 100) / 100;
        //$_pasarela_pagos_subtotal = number_format($_pasarela_pagos_subtotal,2,'.','');
        $_pasarela_pagos_iva   = 0;
        $_pasarela_pagos_total = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_total = round($_pasarela_pagos_total * 100) / 100;
        $pyf_checkout_url      = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)

        $datosAdicionales = array();
        array_push($datosAdicionales, array(
            "key"   => "Agentes_Retencion", //los subgiones _ son espacios
            "value" => "Resolucion 1",
        ));
        array_push($datosAdicionales, array(
            "key"   => "Paciente",
            "value" => $orden->id_paciente . " " . $this->cleanNames($orden->paciente->apellido1 . " " . $orden->paciente->nombre1),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Mail",
            "value" => $email,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Ciudad",
            "value" => $this->cleanNames($orden->ciudad_factura),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Direccion",
            "value" => $orden->direccion_factura,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Orden",
            "value" => '' . $orden->id . '',
        ));

        $pyf_details = array();
        /*array_push($pyf_details,array( //VERSION SIN DESCUENTO
        "sku"    =>  ''.$id_orden.'',
        "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
        "qty"    =>  1,
        "price"   =>  $_pasarela_pagos_subtotal,
        "tax"   =>  0.00,
        "discount"   =>  0.00,  //falta revisar
        "total"   =>  $_pasarela_pagos_subtotal,
        ));*/
        /*foreach ($orden->detalles as $value) { //VERSION DESCUENTO 1
        array_push($pyf_details, array(
        "sku"      => '' . $id_orden . '-' . $value->id_examen,
        "name"     => $value->examen->nombre,
        "qty"      => 1,
        "price"    => $value->valor,
        "tax"      => 0.00,
        "discount" => $value->valor_descuento, //falta revisar
        "total"    => ($value->valor * 1) - $value->valor_descuento,
        ));
        }*/
        foreach ($orden->detalles as $value) {
            array_push($pyf_details, array(
                "sku"      => '' . $id_orden . '-' . $value->id_examen,
                "name"     => $value->examen->nombre,
                "qty"      => 1,
                "price"    => floatval($value->valor),
                "discount" => floatval($value->valor_descuento), //falta revisar
                "subtotal" => floatval($value->valor - $value->valor_descuento), //falta revisar
                "tax"      => 0.00,
                "total"    => floatval($value->valor - $value->valor_descuento),
            ));
        }

        $celular = $orden->telefono_factura;
        if ($celular == null) {
            $celular = '0900000001';
        }
        if (strlen($celular) < 10) {
            $celular = '0900000001';
        }
        if (!is_numeric($celular)) {
            $celular = '0900000001';
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        $nombres  = $orden->nombre_factura;
        $nom      = explode(" ", $nombres);
        $nom_cant = count($nom);
        if ($nom_cant == 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[2] . ' ' . $nom[3];
        } elseif ($nom_cant == 3) {
            $xnombres = $nom[0];
            $xsurname = $nom[1] . ' ' . $nom[2];
        } elseif ($nom_cant == 2) {
            $xnombres = $nom[0];
            $xsurname = $nom[1];
        } elseif ($nom_cant == 1) {
            $xnombres = $nom[0];
            $xsurname = ' ';
        } elseif ($nom_cant > 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[1] . ' ' . $nom[2] . ' ' . $nom_cant[4];
        }

        /*if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
        $nombres = $nombres.' '.$paciente->nombre2;
        }
        $apellidos=$paciente->apellido2;
        if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
        $apellidos = $apellidos.' '.$paciente->apellido2;
        }*/
        //json de invocación

        $data_array = array(
            "company"           => $RUC_LABS,
            "person"            => array(
                "document"     => $orden->cedula_factura,
                "documentType" => $this->getDocumentType($orden->cedula_factura),
                "name"         => $this->cleanNames(strtoupper($xnombres)),
                "surname"      => $this->cleanNames(strtoupper($xsurname)),
                "email"        => $email,
                "mobile"       => $celular,
                /*"address"       => array(
            "street"    =>  $orden->direccion_factura,
            "city"      =>  $orden->ciudad_factura,
            "country"   =>  "EC"
            ), */
            ),
            "paymentRequest"    => array(
                "orderId"     => '' . $id_orden . '',
                "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                "items"       => array(
                    "item" => $pyf_details, //pending
                ),
                "amount"      => array(
                    "taxes"    => array(
                        array(
                            "kind"   => "Iva",
                            "amount" => 0.00,
                            //"base"   => $_pasarela_pagos_subtotal,
                            "base"   => 0.00,
                        ),
                    ),
                    "currency" => "USD",
                    "total"    => $_pasarela_pagos_total,
                ),
            ),
            "billingParameters" => array(
                "establecimiento" => "001",
                "ptoEmision"      => "002",
                "infoAdicional"   => $datosAdicionales,
                "formaPago"       => "19",
                "plazoDias"       => "10",
            ),
            //"returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
            //"cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
            "returnUrl"         => "https://labs.ec/regresar.php", //URL DE RETORNO
            "cancelUrl"         => "https://labs.ec", //URL DE CANCELACION

            "userAgent"         => "labs_ec/1",
        );

        $manage = json_encode($data_array);
        Log_usuario::create([
            'id_usuario'  => $orden->paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "LLAMADA API",
            'dato2'       => "",
            'dato4'       => $manage,
        ]);
        //return $manage;
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response  = json_decode($make_call, true);
        if ($response['status'] != null) {
            if ($response['status']['status'] == 'success') {
                $pyf_checkout_url = $response['processUrl'];
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
            }
        } else {
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
        FIN DE INVOCACION A API DE BOTON DE PAGOS
         */

        return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
        //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS

    }

    public function app_carrito_pago($id_orden) //FUNCION QUE INVOCA LO DE JUANK PARA LAS APPS

    {

        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $orden       = Examen_Orden::find($id_orden);
        $paciente    = $orden->paciente;
        $id_paciente = $paciente->id;
        $grupo_fam   = Labs_Grupo_Familiar::find($id_paciente);
        if (!is_null($grupo_fam)) {
            $user_aso = User::find($grupo_fam->id_usuario);
        } else {
            $user_aso = User::find($paciente->id_usuario);
        }
        $email = $orden->email_factura;
        if (is_null($orden)) {
            return ['estado' => "Error"];
        }
        $valor_total = $orden->total_valor;
        $valor       = $orden->valor;
        /*
        INVOCA API DE PAGOS&FACTURAS Y RECIBE EL LINK DEL VPOS
         */
        $RUC_LABS                 = '0993075000001';
        $PAGOSYFACTURAS_APPID     = 'V1oW1RHpw8GtxwGoIkuq';
        $PAGOSYFACTURAS_APPSECRET = 'SC1o6PTqKuLTIuWpnwyUEKF3tOv9UwYvux';
        $_pasarela_pagos_subtotal = round($valor, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_subtotal = round($_pasarela_pagos_subtotal * 100) / 100;
        //$_pasarela_pagos_subtotal = number_format($_pasarela_pagos_subtotal,2,'.','');
        $_pasarela_pagos_iva   = 0;
        $_pasarela_pagos_total = round($valor_total, 2, PHP_ROUND_HALF_UP);
        $_pasarela_pagos_total = round($_pasarela_pagos_total * 100) / 100;
        $pyf_checkout_url      = 'https://vpos.accroachcode.com/';
        //$site_url = '192.168.75.51/sis_medico/public/laboratorio/externo/';
        //detalle(s)

        $datosAdicionales = array();
        array_push($datosAdicionales, array(
            "key"   => "Agentes_Retencion", //los subgiones _ son espacios
            "value" => "Resolucion 1",
        ));
        array_push($datosAdicionales, array(
            "key"   => "Paciente",
            "value" => $orden->id_paciente . " " . $this->cleanNames($orden->paciente->apellido1 . " " . $orden->paciente->nombre1),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Mail",
            "value" => $email,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Ciudad",
            "value" => $this->cleanNames($orden->ciudad_factura),
        ));
        array_push($datosAdicionales, array(
            "key"   => "Direccion",
            "value" => $orden->direccion_factura,
        ));
        array_push($datosAdicionales, array(
            "key"   => "Orden",
            "value" => '' . $orden->id . '',
        ));

        $pyf_details = array();
        /*array_push($pyf_details,array( //VERSION SIN DESCUENTO
        "sku"    =>  ''.$id_orden.'',
        "name"   =>  'LABS.EC Orden de laboratorio #'.$id_orden,
        "qty"    =>  1,
        "price"   =>  $_pasarela_pagos_subtotal,
        "tax"   =>  0.00,
        "discount"   =>  0.00,  //falta revisar
        "total"   =>  $_pasarela_pagos_subtotal,
        ));*/
        /*foreach ($orden->detalles as $value) { //VERSION DESCUENTO 1
        array_push($pyf_details, array(
        "sku"      => '' . $id_orden . '-' . $value->id_examen,
        "name"     => $value->examen->nombre,
        "qty"      => 1,
        "price"    => $value->valor,
        "tax"      => 0.00,
        "discount" => $value->valor_descuento, //falta revisar
        "total"    => ($value->valor * 1) - $value->valor_descuento,
        ));
        }*/
        foreach ($orden->detalles as $value) {
            array_push($pyf_details, array(
                "sku"      => '' . $id_orden . '-' . $value->id_examen,
                "name"     => $value->examen->nombre,
                "qty"      => 1,
                "price"    => floatval($value->valor),
                "discount" => floatval($value->valor_descuento), //falta revisar
                "subtotal" => floatval($value->valor - $value->valor_descuento), //falta revisar
                "tax"      => 0.00,
                "total"    => floatval($value->valor - $value->valor_descuento),
            ));
        }

        $celular = $orden->telefono_factura;
        if ($celular == null) {
            $celular = '0900000001';
        }
        if (strlen($celular) < 10) {
            $celular = '0900000001';
        }
        if (!is_numeric($celular)) {
            $celular = '0900000001';
        }
        // SI NO TIENE SEGUNDO APELLIDO O NOMBRE NO ENVIAR
        $nombres  = $orden->nombre_factura;
        $nom      = explode(" ", $nombres);
        $nom_cant = count($nom);
        if ($nom_cant == 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[2] . ' ' . $nom[3];
        } elseif ($nom_cant == 3) {
            $xnombres = $nom[0];
            $xsurname = $nom[1] . ' ' . $nom[2];
        } elseif ($nom_cant == 2) {
            $xnombres = $nom[0];
            $xsurname = $nom[1];
        } elseif ($nom_cant == 1) {
            $xnombres = $nom[0];
            $xsurname = ' ';
        } elseif ($nom_cant > 4) {
            $xnombres = $nom[0] . ' ' . $nom[1];
            $xsurname = $nom[1] . ' ' . $nom[2] . ' ' . $nom_cant[4];
        }

        /*if($paciente->nombre2!=null && $paciente->nombre2!='(N/A)' && $paciente->nombre2!='N/A' && $paciente->nombre2!='.'){
        $nombres = $nombres.' '.$paciente->nombre2;
        }
        $apellidos=$paciente->apellido2;
        if($paciente->apellido2!=null && $paciente->apellido2!='(N/A)' && $paciente->apellido2!='N/A' && $paciente->apellido2!='.'){
        $apellidos = $apellidos.' '.$paciente->apellido2;
        }*/
        //json de invocación

        $data_array = array(
            "company"           => $RUC_LABS,
            "person"            => array(
                "document"     => $orden->cedula_factura,
                "documentType" => $this->getDocumentType($orden->cedula_factura),
                "name"         => $this->cleanNames(strtoupper($xnombres)),
                "surname"      => $this->cleanNames(strtoupper($xsurname)),
                "email"        => $email,
                "mobile"       => $celular,
                /*"address"       => array(
            "street"    =>  $orden->direccion_factura,
            "city"      =>  $orden->ciudad_factura,
            "country"   =>  "EC"
            ), */
            ),
            "paymentRequest"    => array(
                "orderId"     => '' . $id_orden . '',
                "description" => "Compra en linea labs", //PONER EN CONFIGURACION
                "items"       => array(
                    "item" => $pyf_details, //pending
                ),
                "amount"      => array(
                    "taxes"    => array(
                        array(
                            "kind"   => "Iva",
                            "amount" => 0.00,
                            //"base"   => $_pasarela_pagos_subtotal,
                            "base"   => 0.00,
                        ),
                    ),
                    "currency" => "USD",
                    "total"    => $_pasarela_pagos_total,
                ),
            ),
            "billingParameters" => array(
                "establecimiento" => "001",
                "ptoEmision"      => "002",
                "infoAdicional"   => $datosAdicionales,
                "formaPago"       => "19",
                "plazoDias"       => "10",
            ),
            //"returnUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_retorno_pago?orderid=".$id_orden,  //URL DE RETORNO
            //"cancelUrl" =>  "http://186.70.157.2/sis_medico/public/laboratorio/externo/web_cancelacion_pago?orderid=".$id_orden, //URL DE CANCELACION
            "returnUrl"         => "https://labs.ec/regresar.php", //URL DE RETORNO
            "cancelUrl"         => "https://labs.ec", //URL DE CANCELACION

            "userAgent"         => "labs_ec/1",
        );

        $manage = json_encode($data_array);
        Log_usuario::create([
            'id_usuario'  => $orden->paciente->id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $id_orden,
            'dato1'       => $paciente->id,
            'dato_ant2'   => "LLAMADA API",
            'dato2'       => "",
            'dato4'       => $manage,
        ]);
        //return $manage;
        $make_call = $this->callAPI('POST', 'https://api.pagosyfacturas.com/api/payment/create/', $manage, $PAGOSYFACTURAS_APPID, $PAGOSYFACTURAS_APPSECRET);
        $response  = json_decode($make_call, true);
        if ($response['status'] != null) {
            if ($response['status']['status'] == 'success') {
                $pyf_checkout_url = $response['processUrl'];
            } else {
                $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=' . $response['status']['status'];
            }
        } else {
            $pyf_checkout_url = 'https://vpos.accroachcode.com/error/?status=bad request';
        }
        /*
        FIN DE INVOCACION A API DE BOTON DE PAGOS
         */

        return ['estado' => 'ok', 'url_vpos' => $pyf_checkout_url]; //AGREGAMOS LA URL DE BOTON DE PAGOS
        //return ['estado' => 'ok', 'url_vpos' => "hola"]; //AGREGAMOS LA URL DE BOTON DE PAGOS

    }

    public function buscar_clientes($id)
    {

        $cliente = Ct_Clientes::where('identificacion', $id)->first();
        if (!is_null($cliente)) {
            return ['id' => $cliente->identificacion, 'nombre' => $cliente->nombre, 'telefono' => $cliente->telefono1_representante, 'direccion' => $cliente->direccion_representante, 'email' => $cliente->email_representante, 'ciudad' => $cliente->ciudad_representante, 'resultado' => 'ok'];
        } else {
            return 'no';
        }
    }

    public function vivokey($id_paciente)
    {

        $nuevo = base64_decode(base64_decode(($id_paciente)));
        if (Auth::guest()) {
            $paciente = paciente::find($nuevo);
            return view('paciente_vivokey', ['paciente' => $paciente]);
        } else {
            $rolUsuario = Auth::user()->id_tipo_usuario;
            if ($rolUsuario == 3) {
                return redirect()->route('nd.buscador', ['id_paciente' => $nuevo]);
            }
        }
        $paciente = paciente::find($nuevo);
        return response()->json($paciente);
        //dd($paciente);
        if (is_null($paciente)) {
            return "no exite paciente";
        }

        return redirect()->route('paciente.historia', ['id_paciente' => $nuevo]);
    }
    public function formato_encuestalabs(Request $request)
    {

        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        //$areas         = Area::where("estado", '=', 1)->get();

        // dd($encuesta->id);

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato     = Preguntas_Labs::where('id_grupopregunta', '=', $variable)->where('pregunta_labs.estado', '=', '1')->get();
            //dd($dato);
            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        //dd($id);
        return view('laboratorio/encuesta_laboratorio/formato_encuestalabs', ['arreglo' => $arreglo]);
    }

    public function encuestalabsguardar(Request $request)
    {

        $calificacion = $request->calificacion;
        $id_master    = $request->id_master;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $grupopregunta = DB::table('grupo_pregunta as gp')
            ->join('pregunta_labs as pl', 'gp.id', 'pl.id_grupopregunta')
            ->where('pl.estado', 1)
            ->where('pl.id_grupopregunta', '!=', 4)->get();

        if ($request['cedula'] == null) {
            return "1";
        }

        $fecha_hoy = date('Y-m-d');
        //dd($fecha_hoy);
        //$no_agendado = Agenda::where('id_paciente', $request['cedula'])->whereBetween('fechaini', [$fecha_hoy . ' 00:00:00', $fecha_hoy . ' 23:59:59'])->first();
        $valexamen = Examen_Orden::where('id_paciente', $request['cedula'])->first();
        // dd($valexamen);
        if (is_null($valexamen) || count($valexamen) == 0) {
            return "no";
        }
        $date = Carbon::now();

        $encuesta = Encuesta_Labs::create([
            'id_paciente' => $request['cedula'],
            'mes'         => $date->month,
            'anio'        => $date->year,
            'ip_ingreso'  => $ip_cliente,

        ]);
        $i = 0;

        if (count($encuesta) > 0) {
            //dd($encuesta->id);
            foreach ($grupopregunta as $value) {
                $variable = $value->id;
                //$dato     = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();
                $dato = Preguntas_Labs::where('id_grupopregunta', '=', $variable)->where('pregunta_labs.estado', '=', '1')->get();

                //return $dato;
                foreach ($dato as $value2) {
                    if ($i == 4) {
                        //dd($request['calificacion']);
                        DB::table('encuesta_complementolabs')->insert(
                            ['id_encuesta_labs' => $encuesta->id, 'id_pregunta_labs' => $value2->id, 'calificacion' => $request['calificacion'], 'id_grupo' => $value2->id_grupopregunta]
                        );
                    } else {
                        if ($variable == 4) {
                            if (!is_null($request[$value2->id])) {
                                $respuesta = $request[$value2->id];
                            } else {
                                $respuesta = 'N/R';
                            }
                        } else {
                            $respuesta = $request[$value2->id];
                        }

                        DB::table('encuesta_complementolabs')->insert(
                            ['id_encuesta_labs' => $encuesta->id, 'id_pregunta_labs' => $value2->id, 'valor' => $respuesta, 'id_grupo' => $value2->id_grupopregunta]
                        );
                    }

                    $i++;
                }
            }
        }

        //dd($request);

        return "ok";
    }

    public function encuestas2_labs()
    {
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        //$areas         = Area::where("estado", '=', 1)->get();
        foreach ($grupopregunta as $value) {
            $variable = $value->id;

            $dato = Pregunta::where('id_grupopregunta', '=', $variable)->where('estado', '=', '1')->get();

            $arreglo[$contador] = $dato;
            $contador           = $contador + 1;
        }
        return view('sinlogin/encuesta2', ['arreglo' => $arreglo, 'areas' => $areas]);
    }

    public function consulta_rc(Request $request)
    {

        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'TG9zX2RhdG9zX3Nvbl9lbmNyaXB0YWRfTURDT05TR1JPVVA=') {
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
                $tipo = $request['tipo'];

                if (is_null($tipo)) {
                    $tipo = 0;
                }

                $ordenes = Agenda::where('agenda.estado', '1')
                    ->wherebetween('agenda.fechaini', [$desde . " 00:00:00", $hasta . " 23:59:59"])
                    ->join('seguros as s', 's.id', 'agenda.id_seguro')
                    ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
                    ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
                    //->groupby('s.id_seguro_tipos', DB::raw('MONTH(agenda.fechaini)'))
                    ->where("s.id_seguro_tipos", "<>", "null")
                    ->where('st.id', '!=', '1');



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
                    // dd($meses, $value);
                    $data = SinLoginController::armarApi($value->mes, $desde, $hasta);
                    $mes = [
                        "mes"       => $meses[$value->mes],
                        "details"   => $data,
                    ];
                    array_push($api, $mes);
                }
                // dd($api);
                // $arr_aniomes = null;
                // $array = array();
                // foreach ($ordenes as $value) {
                //     $arr_aniomes[$value->mes . '-' . $value->nom_tipo_seguro] = [$value->total];
                //     array_push($array, $value->mes);
                // }
                // dd($array);
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
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->leftjoin("seguro_tipos as st", "s.id_seguro_tipos", "st.id")
            ->leftjoin('ct_orden_venta as ov', 'ov.id_agenda', 'agenda.id')
            //->groupby('s.id_seguro_tipos', DB::raw('MONTH(agenda.fechaini)'))
            ->where("s.id_seguro_tipos", "<>", "null")
            ->where('st.id', '!=', '1');

        if ($tipo == 0) {
            $ordenes2 = $ordenes2->where('proc_consul', '0');
        }

        if ($tipo == 1) {
            $ordenes2 = $ordenes2->where('proc_consul', '1');
        }
        $ordenes2 = $ordenes2->where(DB::raw('MONTH(agenda.fechaini)'), $mes)->groupby(DB::raw('MONTH(agenda.fechaini)'))->where("s.id_seguro_tipos", "{$seguro_tipo}")->select("s.id_seguro_tipos", "st.nombre", DB::raw('SUM(ov.total) as total'), "agenda.fechaini")->first();

        return $ordenes2;
    }
    public static function armarApi($mes, $desde, $hasta)
    {

        //$seguros = $data->where(DB::raw('MONTH(agenda.fechaini)'), $mes)->groupby(DB::raw('MONTH(agenda.fechaini)'));



        // $particular     = $seguros->where("s.id_seguro_tipos", "3")->select("s.id_seguro_tipos","st.nombre", DB::raw('SUM(ov.total) as total'), "agenda.fechaini")->first();
        // $privados       = $seguros->where("s.id_seguro_tipos", "2")->select("s.id_seguro_tipos","st.nombre", DB::raw('SUM(ov.total) as total'), "agenda.fechaini")->first();
        // $promo          = $seguros->where("s.id_seguro_tipos", "4")->select("s.id_seguro_tipos","st.nombre", DB::raw('SUM(ov.total) as total'), "agenda.fechaini")->first();
        $particular = SinLoginController::sqlQuery(0, $desde, $hasta, "3", $mes);
        $privados = SinLoginController::sqlQuery(0, $desde, $hasta, "2", $mes);
        $promo = SinLoginController::sqlQuery(0, $desde, $hasta, "4", $mes);


        // dd($particular, $privados, $promo);

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

        //dd($total);
        return $total;
    }

    
}
