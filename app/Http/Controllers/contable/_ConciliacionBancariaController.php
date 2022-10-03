<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Conciliacion_Bancaria;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_divisas;
use Sis_medico\Ct_Nota_Credito;
use Sis_medico\Ct_Tipo_pago;
use Sis_medico\Ct_Transferencia_Bancaria;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nota_Debito;
use Sis_medico\Plan_Cuentas;

class ConciliacionBancariaController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
      //  dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $bancos     = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y/m/d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y/m/d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        if (!is_null($request['tipo'])) {
            $tipo = $request['tipo'];
        } else {
            $tipo = "";
        }
        if (!is_null($request['estado'])) {
            $estado = $request['estado'];
        } else {
            $estado = "";
        }
        if (!is_null($request['banco'])) {
            $banco = $request['banco'];
        } else {
            $banco = "";
        }

        // dd($tipo);
        // $principales = Ct_Debito_Bancario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(5);
        $registros = array();
        $anterior  = $this->getSaldoAnterior($request, $fecha_desde, $banco, "");
        if ($tipo == "BAN-ND" or $tipo == "") {
            //dd("aqui entra");
            $registros = $this->getNotasDebito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-NC" or $tipo == "") {
            $registros = $this->getNotasCredito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }
        
        if ($tipo == "BAN-TR" or $tipo == "") {
            $registros = $this->getTransfBancarias($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }
        
        if ($tipo == "BAN-ND-AC" or $tipo == "") {
            $registros = $this->getDebitoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }
        
        $empresa = Empresa::find($id_empresa);

        if (count($registros) > 0) {
            foreach ($registros as $key => $part) {
                $sort[$key] = strtotime($part['fecha']);
            }
            array_multisort($sort, SORT_ASC, $registros);
        }

      //  dd($tipo);
        //dd($registros);
        return view('contable/conciliacion_bancaria/index', ['registros' => $registros, 'empresa' => $empresa, 'anterior' => $anterior, 'fecha_desde' => $fecha_desde, 'banco' => $banco, 'tipo' => $tipo, 'fecha_hasta' => $fecha_hasta, 'estado' => $estado, 'tipo' => $tipo, 'bancos' => $bancos]);
    }
    public function array_sort_by_column(&$array, $column, $direction = SORT_ASC)
    {
        $reference_array = array();

        foreach ($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }
        //dd($reference_array);
        array_multisort($reference_array, $direction, $array);
    }
    public function getTransfBancarias($id_empresa, $registros, $desde, $hasta, $estado, $idbanco = "")
    {
        $conditions = array();
        if ($idbanco != null) {
            $banco      = Ct_Caja_banco::find($idbanco);
            $conditions = array(
                array('id_cuenta_origen', '=', $banco->codigo),
            );
        }

        $transbanc = Ct_Transferencia_Bancaria::where('estado', '!=', 0)
            ->whereBetween('fecha_asiento', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('empresa', $id_empresa)
            ->where($conditions)
            ->orderby('id', 'desc')
            ->get();
        // dd($transbanc);
        foreach ($transbanc as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha_asiento));
            $data['fecha_banco']     = $row->fecha_asiento;
            $data['tipo']            = $row->tipo;
            $data['numero']          = $row->numero;
            $data['numcheque']       = $row->numcheque;
            $data['beneficiario']    = $row->beneficiario;
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor_destino, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            if ($estado == "1" and $data['conciliado'] == 1) {
                $registros[] = $data;
            } elseif ($estado == "0" and $data['conciliado'] == 0) {
                $registros[] = $data;
            } elseif ($estado == "") {
                $registros[] = $data;
            }

        }
        return $registros;
    }

    public function getNotasCredito($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $conditions = array();
        if ($banco != null) {
            $conditions = array(
                array('id_banco', '=', $banco),
            );
        }
        $query = Ct_Nota_Credito::where('estado', '!=', 0)
            ->where($conditions)
            ->whereBetween('fecha', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('empresa', $id_empresa)
            ->where($conditions)
            ->orderby('id', 'desc')
            ->get();
        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_banco']     = date('Y/m/d', strtotime($row->fecha));
            $data['tipo']            = $row->tipo;
            $data['numero']          = "";
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            if ($estado == "1" and $data['conciliado'] == 1) {
                $registros[] = $data;
            } elseif ($estado == "0" and $data['conciliado'] == 0) {
                $registros[] = $data;
            } elseif ($estado == "") {
                $registros[] = $data;
            }
        }
        return $registros;
    }

    public function getNotasDebito($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $conditions = array();
        if ($banco != null) {
            $conditions = array(
                array('id_banco', '=', $banco),
            );
        }else{
            $conditions = array(
                array('id_banco', '>', '0'),
            );
        }
        $query = Nota_Debito::where('estado', '!=', 0)
            ->whereBetween('fecha', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('empresa', $id_empresa)
            ->where($conditions)
            ->orderby('id', 'desc')
            ->get();
        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_banco']     = date('Y/m/d', strtotime($row->fecha));
            $data['tipo']            = $row->tipo;
            $data['numero']          = "";
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            if ($estado == "1" and $data['conciliado'] == 1) {
                $registros[] = $data;
            } elseif ($estado == "0" and $data['conciliado'] == 0) {
                $registros[] = $data;
            } elseif ($estado == "") {
                $registros[] = $data;
            }
        }
        return $registros;
    }

    public function getDebitoBancario($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $conditions = array();
        if ($banco != null) {
            $conditions = array(
                array('id_banco', '=', $banco),
            );
        }
        $query = Ct_Debito_Bancario::where('estado', '!=', 0)
            ->whereBetween('fecha', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
        // ->where('empresa', $id_empresa)
            ->where('id_banco', $banco)
            ->where($conditions)
            ->orderby('id', 'desc')
            ->get();
        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_banco']     = date('Y/m/d', strtotime($row->fecha));
            $data['tipo']            = $row->tipo;
            $data['numero']          = "";
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            if ($estado == "1" and $data['conciliado'] == 1) {
                $registros[] = $data;
            } elseif ($estado == "0" and $data['conciliado'] == 0) {
                $registros[] = $data;
            } elseif ($estado == "") {
                $registros[] = $data;
            }
        }
        return $registros;
    }

    public function getConciliado($tipo, $id)
    {
        $empresa  = Session::get('id_empresa');
        $consulta = Ct_Conciliacion_Bancaria::where('tipo', $tipo)
            ->where('id_tipo', $id)
            ->where('empresa', $empresa)
            ->first();
        if (isset($consulta->id)) {
            return true;
        }
        return false;
    }

    public function actualizar(Request $request)
    {
        // dd($request);
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $dato     = explode('-:-', $request['id']);
        $consulta = Ct_Conciliacion_Bancaria::where('tipo', $dato[0])
            ->where('id_tipo', $dato[1])
            ->first();
        if (isset($consulta->id)) {
            if ($consulta->estado == 1) {$estado = 0;} else { $estado = 1;}
            $conciliacion         = Ct_Conciliacion_Bancaria::find($consulta->id);
            $conciliacion->estado = $estado;
            $conciliacion->save();
        } else {
            $conciliacion                  = new Ct_Conciliacion_Bancaria;
            $conciliacion->tipo            = $dato[0];
            $conciliacion->id_tipo         = $dato[1];
            $conciliacion->empresa         = $empresa;
            $conciliacion->estado          = 1;
            $conciliacion->id_usuariocrea  = $idusuario;
            $conciliacion->id_usuariomod   = $idusuario;
            $conciliacion->ip_creacion     = $ip_cliente;
            $conciliacion->ip_modificacion = $ip_cliente;
            $conciliacion->save();
        }

        // dd($request);
        $input['msg'] = "actualizacion ok";
        return response()->json($input);
    }

    public function actualizarmasivo(Request $request)
    {
        // dd($request);
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $dato       = explode('-:-', $request['id']);
        $accion     = $request['accion'];
        $consulta   = Ct_Conciliacion_Bancaria::where('tipo', $dato[0])
            ->where('id_tipo', $dato[1])
            ->first();
        if ($accion == "true") {$estado = 1;} else { $estado = 0;}
        if (isset($consulta->id)) {
            $conciliacion         = Ct_Conciliacion_Bancaria::find($consulta->id);
            $conciliacion->estado = $estado;
            $conciliacion->save();
        } else {
            $conciliacion                  = new Ct_Conciliacion_Bancaria;
            $conciliacion->tipo            = $dato[0];
            $conciliacion->id_tipo         = $dato[1];
            $conciliacion->empresa         = $empresa;
            $conciliacion->estado          = $estado;
            $conciliacion->id_usuariocrea  = $idusuario;
            $conciliacion->id_usuariomod   = $idusuario;
            $conciliacion->ip_creacion     = $ip_cliente;
            $conciliacion->ip_modificacion = $ip_cliente;
            $conciliacion->save();
        }
        // dd($request);
        $input['msg'] = "actualizacion ok";
        return response()->json($input);
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id_asiento'    => $request['buscar_asiento'],
            'fecha_asiento' => $request['fecha_asiento'],
            'concepto'      => $request['concepto'],
            'id'            => $request['numero'],
        ];
        $registros = $this->doSearchingQuery($constraints);

        return view('contable/transferencia_bancaria/index', ['request' => $request, 'registros' => $registros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Transferencia_Bancaria::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->orderby('id', 'desc')->paginate(5);
    }

    public function show($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro    = Ct_Transferencia_Bancaria::findorfail($id);
        $id_empresa  = Session::get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = Ct_Tipo_pago::where('estado', '1')->get();
        $divisas     = Ct_divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_banco::where('estado', '1')->where('clase', '1')->get();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->get();
        $cuentas     = plan_cuentas::all();
        return view('contable/transferencia_bancaria/show', ['divisas' => $divisas, 'empresa'    => $empresa, 'banco'       => $banco,
            'bancos'                                                       => $bancos, 'formas_pago' => $formas_pago, 'cuentas' => $cuentas, 'registro' => $registro]);
    }

    public function imprimir($id)
    {
        $registro = Ct_Transferencia_Bancaria::findorfail($id);

        $vistaurl = "contable.transferencia_bancaria.pdf";
        $view     = \View::make($vistaurl, compact('registro'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Trasnferencia Bancaria-' . $id . '.pdf');
        //return view('contable/nota_debito/pdf_nota', compact('registro', 'detalle'));

    }
    public function getSaldoAnterior(Request $request, $fecha_desde, $asiento_id = "", $banco = "")
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $fecha_desde = str_replace('/', '-', $fecha_desde);
        $timestamp   = \Carbon\Carbon::parse($fecha_desde)->timestamp;
        $fecha_desde = date('Y/m/d', $timestamp);

        $detalles = Ct_Asientos_Detalle::where('fecha', '<', $fecha_desde . " 00:00:00")
            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
            ->where('c.id_empresa', $id_empresa)
            ->where('ct_asientos_detalle.estado', '<>', 0)
            ->where('c.estado', '<>', 0);
        if ($asiento_id != "") {
            $detalles = $detalles->where('c.id', '=', $asiento_id);
        }

        if ($banco != "") {
            $ban = Ct_Caja_Banco::where('id', $banco)->where('clase', '1')->where('id_empresa', $id_empresa)->first();
            if (isset($ban->cuenta_mayor)) {
                $detalles = $detalles->where('id_plan_cuenta', '=', $ban->cuenta_mayor);
            }
        } else {
            $bancos    = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->where('id_empresa', $id_empresa)->get();
            $condicion = "";
            foreach ($bancos as $row) {
                $condicion .= "$row->cuenta_mayor,";
            }
            if ($condicion != "") {
                $cuentasbancos = explode(',', $condicion);
                $detalles      = $detalles->whereIn('id_plan_cuenta', $cuentasbancos);
            }
        }
        $detalles = $detalles->first();

        return $detalles->saldo;

    }

    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = null;
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        if (!is_null($request['fecha_desde2']) && !is_null($request['fecha_hasta2'])) {
            $fecha_desde = date('Y/m/d', strtotime($request['fecha_desde2']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta2']));
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        if (!is_null($request['tipo2'])) {
            $tipo = $request['tipo2'];
        } else {
            $tipo = "";
        }
        if (!is_null($request['estado2'])) {
            $estado = $request['estado2'];
        } else {
            $estado = "";
        }
        if (!is_null($request['banco2'])) {
            $banco = $request['banco2'];
        } else {
            $banco = "";
        }
        // dd($tipo);
        // $principales = Ct_Debito_Bancario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(5);
        $registros     = array();
        $saldoanterior = $this->getSaldoAnterior($request, $fecha_desde, "", $banco);
        if ($tipo == "BAN-ND" or $tipo == "") {
            $registros = $this->getNotasDebito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-NC" or $tipo == "") {
            $registros = $this->getNotasCredito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-TR" or $tipo == "") {
            $registros = $this->getTransfBancarias($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-ND-AC" or $tipo == "") {
            $registros = $this->getDebitoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }
        foreach ($registros as $key => $part) {
            $sort[$key] = strtotime($part['fecha']);
        }
        array_multisort($sort, SORT_ASC, $registros);
        //dd($registros);

        Excel::create('Ct_Conciliacion_Bancaria-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $registros, $saldoanterior) {
            $excel->sheet('Ct_Conciliacion_Bancaria', function ($sheet) use ($empresa, $registros, $saldoanterior) {
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $sheet->mergeCells('A2:A2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B2:B2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Banco ');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:C2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('D2:D2');
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Número');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E2:E2');
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cheque');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F2:F2');
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G2:G2');
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H2:H2');
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $i = 3;

                $totaldebito  = 0;
                $totalcredito = 0;
                $saldofinal   = 0;
                foreach ($registros as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date("d-m-Y", strtotime($value['fecha_banco'])));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['tipo']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['numero']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['numcheque']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['beneficiario']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['detalle']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['valor']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    if ($value['tipo'] != "BAN-NC") {
                        $totaldebito += $value['valor'];
                    } else {
                        $totalcredito += $value['valor'];
                    }

                    $i++;
                }
                $i++;
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo Anterior');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Debitos');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Creditos');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo Final');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $i++;
                $sheet->cell('B' . $i, function ($cell) use ($saldoanterior) {
                    // manipulate the cel
                    $cell->setValue($saldoanterior);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('C' . $i, function ($cell) use ($totaldebito) {
                    // manipulate the cel
                    $cell->setValue($totaldebito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('D' . $i, function ($cell) use ($totalcredito) {
                    // manipulate the cel
                    $cell->setValue($totalcredito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('E' . $i, function ($cell) use ($totalcredito, $totaldebito) {
                    // manipulate the cel
                    $final = round($totalcredito - $totaldebito, 2);
                    $cell->setValue($final);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                // $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $cell->setValue($value['valor']);
                //$cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
                //});

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));

            });
            $excel->getActiveSheet()->getStyle('A2:A2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B2:B2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:C2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D2:D2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E2:E2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F2:F2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G2:G2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H2:H2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);

        })->export('xlsx');
    }

    public function setDetalles($consulta, $sheet, $i)
    {

        foreach ($consulta as $value) {

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->numero2);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });

            return $i;
        }
    }

}
