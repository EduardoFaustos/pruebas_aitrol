<?php

use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_transportista;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

$xml = simplexml_load_file($xml);
$xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
EmisionDocumentosController::crearcarpeta(base_path() . '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/factura/');
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$deEmpresa = De_Empresa::where('id_empresa', $xmlComprobamte->infoTributaria->ruc)->first();
$cliente = Ct_Clientes::getCliente($xmlComprobamte->infoFactura->identificacionComprador);
$logo = '/storage/app/logo/' . $empresa->logo;
$logo = '<img src="' . base_path() . $logo . '" style="width:200px;" />';

//DB::enableQueryLog();

$establecimiento = (int)$xmlComprobamte->infoTributaria->estab;
$emision = (int)$xmlComprobamte->infoTributaria->ptoEmi;
$secuencial = (int)$xmlComprobamte->infoTributaria->secuencial;

$ventas = Ct_ventas::where('id_empresa', $empresa->id)
    ->where('sucursal', str_pad($establecimiento, 3, 0, STR_PAD_LEFT))
    ->where('punto_emision',  str_pad($emision, 3, 0, STR_PAD_LEFT))
    ->where('numero', str_pad($secuencial, 9, 0, STR_PAD_LEFT))
    ->first();

/*
echo '<pre>';
print_r(DB::getQueryLog());
exit;
*/
$paciente = '';
$paciente = Paciente::where('id', $ventas->id_paciente)->first();
$seguros = '';
if ($paciente != '') {
    $seguros = Seguro::where('id', $paciente->id_seguro)->first();
}

$formaPago = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
    ->where('id_ct_ventas', $ventas->id)->first();

?>
<style>
    body {
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 10pt "Tahoma";
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .page {
        width: 21cm;
        padding: 1cm;
        margin: 1cm auto;
    }

    .subpage {
        padding: 1cm;
        border: 5px red solid;
        height: 256mm;
        outline: 2cm #FFEAEA solid;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
</style>
<div class="book">
    <div class="page">
        <div style="width:90%;">
            <table style="width:100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:50%;">
                        <table style="font-size: 0.800em;width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" valign="top"><?= $logo ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center"><b><?= $xmlComprobamte->infoTributaria->razonSocial; ?></b></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">R.U.C.:</b> <?= $xmlComprobamte->infoTributaria->ruc; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Dir Matriz:</b> <?= $xmlComprobamte->infoTributaria->dirMatriz; ?></td>
                            </tr>
                            <tr>
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b> <?= $xmlComprobamte->infoFactura->obligadoContabilidad; ?></td>
                            </tr>
                            <tr>
                                <?php
                                if (isset($xmlComprobamte->infoFactura->contribuyenteEspecial)) {
                                    echo '<td><b style="font-size: 10;">Contribuyente especial:</b> Resolución Nro. NAC-DNCRASC20-00000001</td>';
                                } else {
                                    echo '<td></td>';
                                }
                                ?>
                            </tr>
                            <?php
                            if ($deEmpresa->agente_retencion != 0) {
                                echo '<td><b style="font-size: 10;">Agente de Retención:</b> ' . $xmlComprobamte->infoFactura->contribuyenteEspecial . '</td>';
                            }
                            ?>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="3"><b>Factura</b></td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="font-size: 18px;" colspan="3"><?= $xmlComprobamte->infoTributaria->estab; ?>-<?= $xmlComprobamte->infoTributaria->ptoEmi; ?>-<?= $xmlComprobamte->infoTributaria->secuencial; ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 10;"><b>Autorización número:</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 10;"><small><?= $xml->claveAccesoConsultada; ?></small></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Fecha y Hora:</b></td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($xml->autorizaciones->autorizacion->fechaAutorizacion)); ?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Ambiente:</b></td>
                                            <td colspan="2"><?= $xml->autorizaciones->autorizacion->ambiente; ?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Emisión:</b></td>
                                            <td colspan="2">NORMAL</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="font-size: 10;"><b>Clave de Acceso:</b></td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="3" style="font-size: 10;">
                                                <?= $barcode; ?>
                                                <small>{{ $xmlComprobamte->infoTributaria->claveAcceso }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width:100%;">
                            <tr>
                                <td>Razón social:</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>CI/RUC:</td>
                                <td>{{ $cliente->identificacion }}</td>
                            </tr>
                            <tr>
                                <td>Fecha emisión:</td>
                                <td>{{ $xmlComprobamte->infoFactura->fechaEmision }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <hr>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width:100%;">
                            <tr>
                                <th>Cod principal</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Descuento</th>
                                <th>Precio total</th>
                            </tr>
                            <?php
                            foreach ($xmlComprobamte->detalles as $row) {
                                //echo '<pre>';print_r($row->detalle);exit;
                                echo '
                               <tr>
                                <td align="center">' . $row->detalle->codigoPrincipal . '</td>
                                <td>' . $row->detalle->descripcion . '</td>
                                <td align="right">' . $row->detalle->cantidad . '</td>
                                <td align="right">' . $row->detalle->precioUnitario . '</td>
                                <td align="right">' . $row->detalle->descuento . '</td>
                                <td align="right">' . $row->detalle->precioTotalSinImpuesto . '</td>
                            </tr>
                               ';
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;" colspan="2">
                        <table style="width: 100%;">
                            <tr>
                                <td>Información adicional</td>
                            </tr>
                            <?php
                            if ($paciente != '') {
                            ?>
                                <tr>
                                    <td>Paciente:</td>
                                    <td>{{ $paciente->id.' - '.$paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2}}</td>
                                </tr>
                                <tr>
                                    <td>Ciudad:</td>
                                    <td>{{ $paciente->ciudad }}</td>
                                </tr>
                                <tr>
                                    <td>Dirección:</td>
                                    <td>{{ $paciente->direccion }}</td>
                                </tr>
                                <tr>
                                    <td>E-mail:</td>
                                    <td>{{ $paciente->mail_primera_vez }}</td>
                                </tr>
                            <?php
                            }
                            ?>
                            <?php
                            if (isset($oden->numeroorden)) {
                                echo '<tr>
                                <td>Orden:</td>
                                <td></td>
                            </tr>';
                            }
                            ?>
                            <?php
                            if ($seguros != '') {
                            ?>
                                <tr>
                                    <td>Seguro:</td>
                                    <td>{{ $seguros->nombre }}</td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td>Forma de pago:</td>
                                <td>{{ $formaPago->nombre .' - '.$xmlComprobamte->infoFactura->pagos->pago->plazo .' '.$xmlComprobamte->infoFactura->pagos->pago->unidadTiempo  }}</td>
                            </tr>
                            <?php
                            $cont = 0;
                            $adicionales = De_Documentos_Electronicos::getInfoAdicional($ventas->id, $xml->claveAccesoConsultada);
                            $detAdic = json_decode($adicionales->infoAdicional);
                            foreach ($detAdic as $row) {
                                /* echo '<pre>';
                                print_r($row->campoAdicional);
                                exit;*/
                                echo '<tr>
                                    <td>' . ucwords(strtolower($row->campoAdicional->nombre)) . ': </td>
                                    <td>' . $row->campoAdicional->valor . '</td>
                                </tr>';
                                $cont++;
                            }
                            ?>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>