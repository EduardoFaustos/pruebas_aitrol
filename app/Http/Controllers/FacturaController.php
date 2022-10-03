<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\Http\Controllers\Controller;
use Sis_medico\sri\ClaveAcceso;

class FacturaController extends Controller
{
    public function index()
    {
        $cedu=ApiFacturacionController::validarCedula('0921605895');
        //$cedu->validarCedula('0921605895');
dd($cedu);
        $datos = [
            'razonSocial' => 'Alex Mite',
            'nombreComercial' => 'Alex Mite',
            'ruc' => '0921605895001',
            'tipo_comprobante' => '01',
            'fecha_emision' => date('Y-m-d'),
            'ruc' => '0921605895001',
            'tipo_ambiente' => 2,
            'numero_comprobante' => 25,
            'punto_establecimiento' => '001',
            'punto_emision' => '001',
            'tipo_emision' => 1,
            'codDoc' => '01',
            'dirMatriz' => 'Jorge Delgado 512',
            'agente_retencion' => 0,
            'rimpe_emprendedor' => 0,
            'rimpe_popular' => 0,
        ];
        $clave = new ClaveAcceso;
        $clave->llenarDatos($datos);
        $clave->toArray();
        $datos = array('claveAcceso' => $clave->clave_acceso) + $datos;
        $this->generarFacturaXml($datos);
    }
    public function generarFacturaXml($campos)
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $root = $xml->createElement('factura');
        $root->setAttribute('id', 'comprobante');
        $root->setAttribute('version', '1.1.0');
        $infoTributaria = $this->getInfoTributaria($xml, $campos);
        $root->appendChild($infoTributaria);

        $xml->appendChild($root);
        $xml->formatOutput = true;
        $xml->saveXML();
        $xml->save('Factura-'.$campos['punto_establecimiento'].'-'.$campos['punto_emision'].'-'.str_pad($campos['numero_comprobante'],9,0,STR_PAD_LEFT).'.xml');
    }
    public function getInfoTributaria($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoTributaria');
        $nodoDetalle->appendChild($xmlDocument->createElement('ambiente', $campos['tipo_ambiente']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoEmision', $campos['tipo_emision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocial', $campos["razonSocial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('nombreComercial', str_replace('&', 'Y', $campos["nombreComercial"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('ruc', $campos["ruc"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('claveAcceso', $campos["claveAcceso"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDoc', $campos["codDoc"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('estab', $campos["punto_establecimiento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('ptoEmi', $campos["punto_emision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('secuencial', $campos["numero_comprobante"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirMatriz', $campos["dirMatriz"]));
        if ($campos['agente_retencion'] != 0)
            $nodoDetalle->appendChild($xmlDocument->createElement('agenteRetencion', "201"));
        elseif ($campos['rimpe_emprendedor'] == 1)
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE RÉGIMEN RIMPE"));
        elseif ($campos['rimpe_popular'] == 1)
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE RÉGIMEN RIMPE"));
        return $nodoDetalle;
    }
}
