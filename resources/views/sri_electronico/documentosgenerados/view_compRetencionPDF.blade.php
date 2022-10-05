<?php
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\EmisionDocumentosController;

$xml = simplexml_load_file($xml);
$xmlComprobamte = simplexml_load_string($xml->autorizaciones->autorizacion->comprobante);
$barcode = EmisionDocumentosController::setBarcode($xmlComprobamte->infoTributaria->claveAcceso, '/storage/app/facturaelectronica/barcode/' . $xmlComprobamte->infoTributaria->ruc . '/comprobanteRetencion/');
/*echo base_path() . str_replace('public/', '', $barcode);
echo '<br/>';
echo base_path() . $barcode;
exit;*/
$barcode = '<img src="' . base_path() . $barcode . '" />';
$empresa = Empresa::where('id', $xmlComprobamte->infoTributaria->ruc)->first();
$logo = '/storage/app/logo/' . $empresa->logo_form;
$logo = '<img src="' . base_path() . $logo . '" style="width:50%;" />';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 8 PDF</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            margin-left: -20px;
            padding: 0;
            background-color: #FAFAFA;
            font: 10pt "Tahoma";
            font-size: 10px;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 21cm;
            padding: 1cm;
            margin: 1cm auto;
            margin-left: -20px;
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
</head>

<body>
    <div class="container">
        <div class="col-md-12">
            <table>
                <tr>
                    <td>&nbsp;<br/><br/></td>
                </tr>
                <tr>
                    <td style="width:50%;">
                        <table style="font-size: 0.800em;width:100%;" cellpadding="0" cellspacing="0">
                            <tr colspan="4">
                                <td align="center" valign="top"><?= $logo ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr colspan="4">
                                <td align="center"><b><span style="font-size: 12;"><?= $xmlComprobamte->infoTributaria->razonSocial; ?></span></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            </tr>
                            <tr colspan="4">
                                <td><b style="font-size: 10;">R.U.C.:</b> <span style="font-size: 12;"><?= $xmlComprobamte->infoTributaria->ruc; ?></span></td>
                            </tr>
                            <tr colspan="4">
                                <td><b style="font-size: 10;">Dir Matriz:</b> <span style="font-size: 12;"><?= $xmlComprobamte->infoTributaria->dirMatriz; ?></span></td>
                            </tr>
                            <tr colspan="4">
                                <td><b style="font-size: 10;">Obligado a llevar contabilidad:</b><span style="font-size: 12;"><?= $xmlComprobamte->infoCompRetencion->obligadoContabilidad; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
                            </tr>
                            <tr>
                                <?php
                                if (isset($xmlComprobamte->infoCompRetencion->contribuyenteEspecial)) {
                                    echo '<td><b style="font-size: 10;">Contribuyente especial:</b>  ' . '<span style="font-size: 12;">'.$xmlComprobamte->infoCompRetencion->contribuyenteEspecial .'</span>'. '</td>';
                                } else {
                                    echo '<td></td>';
                                }
                                ?>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%;">
                        <table style="width:100%;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table style="font-size: 0.800em;" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="font-size: 26px;" colspan="2"><b>COMPROBANTE <br/>DE <br/>RETENCION </b></td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="font-size: 18px;" colspan="2"><span style="font-size: 12;"><?= $xmlComprobamte->infoTributaria->estab; ?>-<?= $xmlComprobamte->infoTributaria->ptoEmi; ?>-<?= $xmlComprobamte->infoTributaria->secuencial; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="font-size: 10;"><b>Autorización número:</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="font-size: 10;"><span style="font-size: 10;"><?= $xml->claveAccesoConsultada; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Fecha y Hora:</b></td>
                                            <td><span style="font-size: 10;"><?= date('d/m/Y H:i:s', strtotime($xml->autorizaciones->autorizacion->fechaAutorizacion)); ?></span></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Ambiente:</b></td>
                                            <td colspan="2"><span style="font-size: 10;"><?= $xml->autorizaciones->autorizacion->ambiente; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 10;"><b>Emisión:</b></td>
                                            <td colspan="2"><span style="font-size: 10;">NORMAL</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="font-size: 10;"><b>Clave de Acceso:</b></span></td>
                                        </tr>
                                        <tr>
                                            <td align="center" colspan="2" style="font-size: 10;">
                                                <?= $barcode; ?>
                                                <span style="font-size: 10;"><?= $xmlComprobamte->infoTributaria->claveAcceso; ?></span>
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
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style="width:100%;" colspan="2">
                        <table style="width:100%;font-size: 0.800em;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="font-size: 10;"><b>Razón Social/Nombres y Apellidos</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->razonSocialSujetoRetenido; ?></td>
                                <td style="font-size: 10;"><b>Identificación</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->identificacionSujetoRetenido; ?></td>
                            </tr>
                            <tr>
                                <td style="font-size: 10;"><b>Fecha Emisión</b></td>
                                <td style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->fechaEmision; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
                <?php
                foreach ($xmlComprobamte->impuestos->impuesto as $row) {
                ?>
                    <table class="table" style="width: 98%;">
                        <thead>
                            <tr>
                                <td align="center" style="font-size: 10;text-align:center;">Comprobante</td>
                                <td align="center" style="font-size: 10;">Número</td>
                                <td align="center" style="font-size: 10;">Fecha Emisión</td>
                                <td align="center" style="font-size: 10;">Ejericio Fiscal</td>
                                <td align="center" style="font-size: 10;">Base imponible para la Retención</td>
                                <td align="center" style="font-size: 10;">IMPUESTO</td>
                                <td align="center" style="font-size: 10;">Porcentaje Retención</td>
                                <td align="center" style="font-size: 10;">Valor Retenido</td>
                            </tr>
                        </thead>
                        <tbody>
                            {{ $cont=0 }}
                            <?php
                            foreach ($xmlComprobamte->impuestos->impuesto as $row) {
                            ?>
                                {{ $cont++ }}
                                <tr>
                                    <td align="center" style="font-size: 10;">FACTURA</td>
                                    <td align="left" style="font-size: 10;"><?= $row->numDocSustento; ?></td>
                                    <td align="center" style="font-size: 10;"><?= $row->fechaEmisionDocSustento; ?></td>
                                    <td align="center" style="font-size: 10;"><?= $xmlComprobamte->infoCompRetencion->periodoFiscal; ?></td>
                                    <td align="center" style="font-size: 10;"><?= $row->baseImponible; ?></td>
                                    <td align="center" style="font-size: 10;">
                                        @if($row->codigo==1)                                  
                                            IVA
                                        @elseif($row->codigo==2)
                                            RENTA
                                        @else
                                            IBR
                                        @endif
                                    </td>
                                    <td align="center" style="font-size: 10;"><?= $row->porcentajeRetener; ?></td>
                                    <td align="center" style="font-size: 10;"><?= $row->valorRetenido; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                }
                ?>
            <table style="width:100%;">
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr />
                        <b style="font-size: 16px;">Información adicional:</b>
                        <hr />
                    </td>
                </tr>
                <?
                if (isset($xmlComprobamte->infoAdicional->campoAdicional['2'])) {
                ?>
                    @php //dd($xmlComprobamte->infoAdicional); @endphp
                    <tr>
                        <td style="font-size: 10;"><b>Correo: </b> <?= $xmlComprobamte->infoAdicional->campoAdicional['0']; ?></td>
                    </tr>
                <?
                }

                if (isset($xmlComprobamte->infoAdicional->campoAdicional['1'])) {
                ?>
                    <tr>
                        <td style="font-size: 10;"><b>Telefono: </b> <?= $xmlComprobamte->infoAdicional->campoAdicional['1']; ?></td>
                    </tr>
                <?
                }
                ?>
            </table>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>