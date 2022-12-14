<?php

namespace Sis_medico\Http\Controllers;

use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Barcode;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_Guia_Remision_Detalle;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_transportista;
use Sis_medico\De_Codigo_Impuestos;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Empresa;
use Sis_medico\De_Info_Tributaria;
use Sis_medico\De_Log_Error;
use Sis_medico\De_Maestro_Documentos;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\sri_electronico\ClaveAcceso;
use SoapClient;
use Sis_medico\Ct_Sucursales;
use PHPMailer\PHPMailer\PHPMailer;
use Sis_medico\De_Estado_Sri;
use Mail;

class EmisionDocumentosController extends Controller
{
    public function index(Request $req)
    {
        if ($req->opcion == '') {
            if (isset($req->idUsuario)) {
                $this->getGuiaRemision(null, $req->idUsuario);
                $this->getNotaCredito(null, $req->idUsuario);
                $this->getFactura(null, $req->idUsuario);
                $this->getRetencio(null, $req->idUsuario);
                $this->getNotaDebito(null, $req->idUsuario);
                $this->getLiquidacion(null, $req->idUsuario);
            } else {
                $this->getGuiaRemision(null);
                $this->getNotaCredito();
                $this->getFactura();
                $this->getRetencio();
                $this->getNotaDebito();
                $this->getLiquidacion();
            }
        } elseif ($req->opcion == 'leerxml') {
            $this->obtenercomprobanteSRI('2508202206092272958700110010010000000200000002018');
        } elseif ($req->opcion == 'barcode') {
            $this->setBarcode('2408202206092272958700110010010000000160000001619', '');
        } elseif ($req->opcion == 'generarPdf') {
            return  $this->generarPdf($req->clave, $req->clave);
        } elseif ($req->opcion == 'enviar_correo') {
            $param = [
                'nombre' => 'Alex Omar',
                'apellido' => 'Mite Salazar',
                'email' => 'amite@mdconsgroup.com',
                'estab' => '001',
                'ptoEmi' => '001',
                'secuencial' => '000000099',
                'tipoDoc' => '06',
                'claveAcceso' => '1209202206092272958700110010010000000990000009918',
                'fechaEmision' => '12/09/2022',
                'valorPagar' => 152.25,
            ];
            $this->enviar_correo($param);
        } elseif ($req->opcion == 'descargarXML') {
            $this->descargarXML($req->clave);
        } elseif ($req->opcion == 'ValidarFuera') {
            echo $this->ValidarFuera('<?xml version="1.0" encoding="UTF-8"?>
            <guiaRemision id="comprobante" version="1.1.0">
              <infoTributaria>
                <ambiente>1</ambiente>
                <tipoEmision>1</tipoEmision>
                <razonSocial>EDUARDO FAUSTOS NIVELO</razonSocial>
                <nombreComercial>EDUARDO FAUSTOS NIVELO</nombreComercial>
                <ruc>0922729587001</ruc>
                <claveAcceso>0509202206092272958700110010010000000950000009517</claveAcceso>
                <codDoc>06</codDoc>
                <estab>001</estab>
                <ptoEmi>001</ptoEmi>
                <secuencial>000000095</secuencial>
                <dirMatriz>SAUCES 6 MZ 259 F43 V1</dirMatriz>
              </infoTributaria>
              <infoGuiaRemision>
                <dirEstablecimiento>Av de los Shiris</dirEstablecimiento>
                <dirPartida>La 21 Entre Oriente y sedalana</dirPartida>
                <razonSocialTransportista>Walter Alarcon</razonSocialTransportista>
                <tipoIdentificacionTransportista>05</tipoIdentificacionTransportista>
                <rucTransportista>0951561075</rucTransportista>
                <obligadoContabilidad>NO</obligadoContabilidad>
                <fechaIniTransporte>05/09/2022</fechaIniTransporte>
                <fechaFinTransporte>05/09/2022</fechaFinTransporte>
                <placa>dfr454</placa>
              </infoGuiaRemision>
              <destinatarios>
                <destinatario>
                  <identificacionDestinatario>0921605895</identificacionDestinatario>
                  <razonSocialDestinatario>Alex Mite</razonSocialDestinatario>
                  <dirDestinatario>los esteros</dirDestinatario>
                  <motivoTraslado>prueba</motivoTraslado>
                  <codEstabDestino>000</codEstabDestino>
                  <ruta>Sur - oeste</ruta>
                  <detalles>
                    <detalle>
                      <codigoInterno>6969</codigoInterno>
                      <codigoAdicional>6969</codigoAdicional>
                      <descripcion>PRUEBA 1</descripcion>
                      <cantidad>1</cantidad>
                      <detallesAdicionales>
                        <detAdicional nombre="observacion" valor="prueba 2"/>
                        <detAdicional nombre="descripcion" valor="hdfgh"/>
                      </detallesAdicionales>
                    </detalle>
                  </detalles>
                </destinatario>
              </destinatarios>
              <infoAdicional>
                <campoAdicional nombre="email">walarcon95@hotmail.com</campoAdicional>
                <campoAdicional nombre="telefono">0980631943</campoAdicional>
              </infoAdicional>
            </guiaRemision>
            ', 'guiaRemision');
        } elseif ($req->opcion == 'firmarFuera') {
            echo $this->firmarFuera('<?xml version="1.0" encoding="UTF-8"?>
            <guiaRemision id="comprobante" version="1.1.0">
              <infoTributaria>
                <ambiente>1</ambiente>
                <tipoEmision>1</tipoEmision>
                <razonSocial>EDUARDO FAUSTOS NIVELO</razonSocial>
                <nombreComercial>EDUARDO FAUSTOS NIVELO</nombreComercial>
                <ruc>0922729587001</ruc>
                <claveAcceso>0509202206092272958700110010010000000950000009517</claveAcceso>
                <codDoc>06</codDoc>
                <estab>001</estab>
                <ptoEmi>001</ptoEmi>
                <secuencial>000000095</secuencial>
                <dirMatriz>SAUCES 6 MZ 259 F43 V1</dirMatriz>
              </infoTributaria>
              <infoGuiaRemision>
                <dirEstablecimiento>Av de los Shiris</dirEstablecimiento>
                <dirPartida>La 21 Entre Oriente y sedalana</dirPartida>
                <razonSocialTransportista>Walter Alarcon</razonSocialTransportista>
                <tipoIdentificacionTransportista>05</tipoIdentificacionTransportista>
                <rucTransportista>0951561075</rucTransportista>
                <obligadoContabilidad>NO</obligadoContabilidad>
                <fechaIniTransporte>05/09/2022</fechaIniTransporte>
                <fechaFinTransporte>05/09/2022</fechaFinTransporte>
                <placa>dfr454</placa>
              </infoGuiaRemision>
              <destinatarios>
                <destinatario>
                  <identificacionDestinatario>0921605895</identificacionDestinatario>
                  <razonSocialDestinatario>Alex Mite</razonSocialDestinatario>
                  <dirDestinatario>los esteros</dirDestinatario>
                  <motivoTraslado>prueba</motivoTraslado>
                  <codEstabDestino>000</codEstabDestino>
                  <ruta>Sur - oeste</ruta>
                  <detalles>
                    <detalle>
                      <codigoInterno>6969</codigoInterno>
                      <codigoAdicional>6969</codigoAdicional>
                      <descripcion>PRUEBA 1</descripcion>
                      <cantidad>1</cantidad>
                      <detallesAdicionales>
                        <detAdicional nombre="observacion" valor="prueba 2"/>
                        <detAdicional nombre="descripcion" valor="hdfgh"/>
                      </detallesAdicionales>
                    </detalle>
                  </detalles>
                </destinatario>
              </destinatarios>
              <infoAdicional>
                <campoAdicional nombre="email">walarcon95@hotmail.com</campoAdicional>
                <campoAdicional nombre="telefono">0980631943</campoAdicional>
              </infoAdicional>
            </guiaRemision>
            ', 'guiaRemision', 'firma.p12', '3duard0faustos');
        }
    }
    public function getGuiaRemision($dinfo = null, $idUsuario = '0921605895')
    {
        $data = [];
        $id_doc = 0;
        try {
            if (is_null($dinfo)) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $guiaCabecera = Ct_Guia_Remision_Cabecera::getGuiaCabecera();
                if (count($guiaCabecera) > 0) {
                    if (!$this->consultarEstadoSRI($guiaCabecera[0]->id_empresa)) {
                        $arrayLog = [
                            'id_de_documentos_electronicos' => $id_doc,
                            'descripcion_error' => json_encode(['Error:' => 'SRI sin servicio']),
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'id_documento' => $id_doc
                        ];
                        De_Log_Error::setErrorLog($arrayLog);
                        $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $arrayLog], 'message' => 'Error');
                        echo json_encode($result);
                        return;
                    } else {
                        $cont = 0;
                        foreach ($guiaCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            $emailTransportista = '';
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha_emision_documento));
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '06';
                                $infoTributaria['estab'] = str_pad($row->establecimiento, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $secuencial = $datosTributarios->secuencial_nro + 1;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_emision_documento))),
                                    'tipo_comprobante' => $infoTributaria['codDoc'],
                                    'ruc' => $infoTributaria['ruc'],
                                    'tipo_ambiente' => $infoTributaria['ambiente'],
                                    'punto_establecimiento' => $infoTributaria['estab'],
                                    'punto_emision' => $infoTributaria['ptoEmi'],
                                    'numero_comprobante' => $infoTributaria['secuencial'],
                                    'codigo_numerico' => $infoTributaria['secuencial'],
                                    'tipo_emision' => $infoTributaria['tipoEmision']
                                ];
                                $claveObj->llenarDatos($campos);
                                $infoTributaria['claveAcceso'] = $claveObj->clave_acceso;
                                $data['infoTributaria'] = $infoTributaria;

                                $sucursales = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();

                                $caja = Ct_Caja::where('codigo_caja', $infoTributaria['ptoEmi'])
                                    ->where('id_sucursal', $sucursales->id)
                                    ->first();

                                if (isset($caja->sucursal->direccion_sucursal)) {
                                    $infoGuiaRemision['dirEstablecimiento'] = $caja->sucursal->direccion_sucursal;
                                } else {
                                    $infoGuiaRemision['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $telefonoTransportista = '';
                                $transportista = Ct_transportista::getTransportista($row->ci_ruc_trasnportista);
                                if ($transportista != '') {
                                    $emailTransportista = $transportista->email;
                                    $telefonoTransportista = $transportista->telefono1;
                                    $infoGuiaRemision['dirPartida'] = $row->direccion_partida;
                                    $infoGuiaRemision['razonSocialTransportista'] = $transportista->razon_social;
                                    $infoGuiaRemision['tipoIdentificacionTransportista'] = str_pad($transportista->tipo_documento, 2, 0, STR_PAD_LEFT);
                                    $infoGuiaRemision['rucTransportista'] = $transportista->ci_ruc;
                                    if ($transportista->rise == 1)
                                        $infoGuiaRemision['rise'] = 'Contribuyente Regimen Simplificado RISE';
                                    $infoGuiaRemision['obligadoContabilidad'] = 'NO'; //Opcional
                                    if ($transportista->contabilidad == 1)
                                        $infoGuiaRemision['obligadoContabilidad'] = 'SI'; //Opcional
                                    $infoGuiaRemision['contribuyenteEspecial'] = null; //Opcional                       
                                    if ($transportista->contribuyente_especial != '')
                                        $infoGuiaRemision['contribuyenteEspecial'] = $transportista->contribuyente_especial; //Opcional
                                    $infoGuiaRemision['fechaIniTransporte'] =  str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_ini)));
                                    $infoGuiaRemision['fechaFinTransporte'] =  str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_fin)));
                                    $infoGuiaRemision['placa'] = $row->placa;
                                } else {
                                    $infoGuiaRemision['razonSocialTransportista'] = null;
                                    $infoGuiaRemision['tipoIdentificacionTransportista'] = null;
                                    $infoGuiaRemision['rucTransportista'] = null;
                                    $infoGuiaRemision['rise'] = null;
                                    $infoGuiaRemision['obligadoContabilidad'] = null; //Opcional
                                    $infoGuiaRemision['obligadoContabilidad'] = null; //Opcional
                                    $infoGuiaRemision['contribuyenteEspecial'] = null; //Opcional
                                    $infoGuiaRemision['contribuyenteEspecial'] = null; //Opcional
                                }
                                $data['infoGuiaRemision'] = $infoGuiaRemision;
                                $destinatario['identificacionDestinatario'] = $row->ci_destinatario;
                                $destinatario['razonSocialDestinatario'] =  $row->razon_social_destinatario;
                                $destinatario['dirDestinatario'] = $row->direccion_destinatario;
                                $destinatario['motivoTraslado'] = $row->motivo_traslado_destinatario;
                                $destinatario['docAduaneroUnico'] = null;
                                $destinatario['codEstabDestino'] = null;
                                $destinatario['ruta'] = $row->ruta;
                                if (isset($row->tipo_documento_destinatario) && $row->tipo_documento_destinatario != '') {
                                    $destinatario['codDocSustento'] = str_pad($row->tipo_documento_destinatario, 2, 0, STR_PAD_LEFT);
                                    $destinatario['numDocSustento'] = $row->num_doc_destino;
                                    $destinatario['numAutDocSustento'] = $row->num_autorizacion_sustento;
                                    $destinatario['fechaEmisionDocSustento'] = str_replace('-', '/', date('d/m/Y', strtotime($row->fecha_autorizacion_destinatario)));
                                }
                                //detalles
                                $detalle = [];
                                $detalles = [];
                                $conDetalle = 0;
                                $detalleGuia = Ct_Guia_Remision_Detalle::getDetalles($row->id);
                                if (count($detalleGuia) > 0) {
                                    foreach ($detalleGuia as $detaGuia) {
                                        $detalle['codigoInterno'] = $detaGuia->cod_principal;
                                        $detalle['codigoAdicional'] = $detaGuia->cod_adicional;
                                        $detalle['descripcion'] = Ct_productos::getNombreProducto($detaGuia->id_producto, $empresa->id);

                                        $detalle['cantidad'] = $detaGuia->cantidad;
                                        $contAdicional = 0;
                                        if ($detaGuia->observacion != '') {
                                            $detAdicional = [
                                                'nombre' => 'observacion',
                                                'valor' => $detaGuia->observacion
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaGuia->descripcion != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'descripcion',
                                                'valor' => $detaGuia->descripcion
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaGuia->detalle3 != '') {
                                            $detAdicional_1 = [
                                                'nombre' => 'detalle3',
                                                'valor' => $detaGuia->detalle3
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($contAdicional > 0) {
                                            $detallesAdicionales = [isset($detAdicional_) ? $detAdicional_ : [], isset($detAdicional) ? $detAdicional : [], isset($detAdicional_1) ? $detAdicional_1 : []];
                                            $detalle['detallesAdicionales'] = $detallesAdicionales;
                                        }
                                        $detalles[$conDetalle] = $detalle;
                                        $conDetalle++;
                                    }
                                }
                                $destinatario['detalles'] = $detalles;
                                $campoAdicional['nombre'] = "email";
                                $campoAdicional['valor']  = $emailTransportista;
                                $informacion_adicional[0]['campoAdicional'] = $campoAdicional;
                                $campoAdicional['nombre'] = "telefono";
                                $campoAdicional['valor'] = $telefonoTransportista;
                                $informacion_adicional[1]['campoAdicional'] = $campoAdicional;
                                $infoAdicional['campoAdicional'] = $informacion_adicional;
                                $destinatarios['destinatario'] = $destinatario;
                                $data['destinatarios'] = $destinatarios;
                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            //dd($data);
                            $errores = $this->validarData($data);
                            //dd($errores);
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoGuiaRemision' => json_encode($data['infoGuiaRemision']),
                                    'destinatarios' => json_encode($data['destinatarios']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $destinatario['identificacionDestinatario'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id,
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                                Ct_Guia_Remision_Cabecera::updateSinGenerarXML($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $data['infoTributaria']['claveAcceso']);
                                //dd($id_doc);
                                $idInsertados .= $id_doc . ',';
                                $arrayLog = [
                                    'id_de_documentos_electronicos' => $id_doc,
                                    'descripcion_error' => json_encode($errores),
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'id_documento' => $row->id
                                ];
                                De_Log_Error::setErrorLog($arrayLog);
                            } else {
                                //Creacion XML
                                $arrayLogError = [
                                    'id_de_pasos' => 1,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoGuiaRemision' => json_encode($data['infoGuiaRemision']),
                                    'destinatarios' => json_encode($data['destinatarios']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $destinatario['identificacionDestinatario'],
                                    'ruc_emisor' => $infoTributaria['ruc'],
                                    'id_usuariomod' => $idUsuario,
                                    'id_usuariocrea' =>  $idUsuario,
                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                    'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                    'id_documento' => $row->id
                                ];
                                $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                                $idInsertados .= $id_doc . ',';
                                $comprobante = $this->generarDocElectronicoXml($data);
                                /* echo '<pre>';
                                print_r($comprobante);
                                exit;*/
                                Ct_Guia_Remision_Cabecera::updateGenerarXML($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $data['infoTributaria']['claveAcceso']);
                                if (!empty($comprobante)) {
                                    $docAnte = simplexml_load_string($comprobante);
                                    if (!empty($docAnte)) {
                                        //validar xsd
                                        //$validacion = $this->ValidarFuera($comprobante, $this->tipoDocumento);
                                        $validacion = $this->validarXmlToXsd($comprobante);
                                        if (!empty($validacion) && $validacion['isValidoXsd'] != 1) {
                                            if ($validacion == 0) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => 'Error al conectarse al validadorXSD',
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                            } else {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                            }

                                            De_Log_Error::setErrorLog($arrayLog);
                                            $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir');
                                            echo json_encode($result);
                                            return;
                                        }
                                        De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                        Ct_Guia_Remision_Cabecera::updateValidacionXSD($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                        //firmar xml
                                        $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                        //$ruta_p12 = $datosFirma->ruta_firma; //firma.p12
                                        $password = $datosFirma->clave_firma; //'3duard0faustos';
                                        $result = $this->firmarFuera($comprobante, $this->tipoDocumento, $ruta_p12, $password);
                                        //$result = simplexml_load_file('http://pruebas.aitrol.com/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml', 'SimpleXMLElement');
                                        /*echo '<pre>';
                                        print_r($result);
                                        echo '-------------------------------------------';
                                        $result = simplexml_load_string($result);
                                        print_r($result);
                                        exit;*/
                                        //$comprobante, $tipoDocumento, $ruta_p12, $password
                                        // dd($resultado);
                                        $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);

                                        if (!empty($result)) {
                                            $docAnte = simplexml_load_string($comprobante);
                                            $dom = new DOMDocument();
                                            $dom->loadXML($result);
                                            $this->crearcarpeta(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/');
                                            $dom->save(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml');
                                            De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                            Ct_Guia_Remision_Cabecera::updateXmlFirmado($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                            //recepcion sri
                                            $respuesta = $this->recibirWs($result, 1);
                                            De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                            Ct_Guia_Remision_Cabecera::updateRecibidoSri($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                            //dd($respuesta['comprobantes']);
                                            if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                //$this->procesarRespuestaXml($respuesta);
                                                $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                //autorzacion sri
                                                $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso);
                                                $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                Ct_Guia_Remision_Cabecera::updateXmlAutorizacion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento);
                                                $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                $this->generarPdf($docAnte->infoTributaria->claveAcceso);
                                                $param = [
                                                    'email' => $row->email_traslado_destinatario,
                                                    'nombre' => $row->razon_social_destinatario,
                                                    'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                    'estab' => $data['infoTributaria']['estab'],
                                                    'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                    'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                    'tipoDoc' => $data['infoTributaria']['codDoc'],
                                                    'fechaEmision' => $data['infoTributaria']['fecha_emision'],
                                                ];
                                                $this->enviar_correo($param);
                                            } else {
                                                $this->generarXmlRespuesta($respuesta, $data['infoTributaria']['claveAcceso']);
                                                De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                Ct_Guia_Remision_Cabecera::updateXmlNoRecepcion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($respuesta),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                //dd($respuesta);
                                                //return $respuesta['mensajesWs'];
                                            }
                                        } else {
                                            //Log error al firmar XML
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($result),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                        }
                                    }
                                } else {
                                    //log No genero XML
                                    $arrayLog = [
                                        'id_de_documentos_electronicos' => $id_doc,
                                        'descripcion_error' => 'Error al crear el documento XML',
                                        'id_usuariomod' => $idUsuario,
                                        'id_usuariocrea' =>  $idUsuario,
                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                    ];
                                    De_Log_Error::setErrorLog($arrayLog);
                                }
                            }
                        }
                    }
                } else {
                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existen documentos para emitir');
                    echo json_encode($result);
                    return;
                }
                $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                $infoAutorizados = '';
                if ($claveInsertados != '')
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente');
                echo json_encode($result);
            } else {
                echo 'a terceros';
            }
        } catch (Exception $ex) {
            if ($id_doc != 0) {
                $arrayLog = [
                    'id_de_documentos_electronicos' => $id_doc,
                    'descripcion_error' => json_encode($ex->getMessage()),
                    'id_usuariomod' => $idUsuario,
                    'id_usuariocrea' =>  $idUsuario,
                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                ];
                De_Log_Error::setErrorLog($arrayLog);
            }
            $result = array('code' => 500, 'state' => true, 'data' => ['error:' => $ex->getMessage(), 'linea' => $ex->getLine()], 'message' => 'Error');
            echo json_encode($result);
            return;
        }
    }
    public static function getNotaCredito()
    {
    }
    public static function getFactura()
    {
    }
    public static function getRetencio()
    {
    }
    public static function getNotaDebito()
    {
    }
    public static function getLiquidacion()
    {
    }
    public function obtenercomprobanteSRI($clave = '')
    {
        //$servicio="https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl";
        $servicio = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
        //$servicio = "https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl";
        $parametros = array();
        $parametros['claveAccesoComprobante'] = $clave;
        $client = new SoapClient($servicio, $parametros);
        $result = $client->autorizacionComprobante($parametros);
        dd($result);
        return $result;
    }
    public static function setBarcode($clave, $ruta)
    {
        $im = imagecreatetruecolor(300, 80);
        $black = ImageColorAllocate($im, 0x00, 0x00, 0x00);
        $white = ImageColorAllocate($im, 0xff, 0xff, 0xff);
        imagefilledrectangle($im, 0, 0, 300, 300, $white);
        Barcode::gd($im, $black, 150, 40, 0, "code128", $clave, 2, 50);
        EmisionDocumentosController::crearcarpeta(base_path() . $ruta);
        imagepng($im, base_path() . $ruta . $clave . '.png');
        imagedestroy($im);
        return $ruta . $clave . '.png';
    }
    public function pingDomain($domain)
    {
        error_reporting(0);
        $estado = false;
        try {
            $result = file($domain);
            if ($result == false) {
                $estado = 0;
            } elseif (count($result) <= 1) {
                $estado = 0;
            } else {
                $estado = 1;
            }
            return $estado;
        } catch (Exception $ex) {
            $estado = false;
        }
        return $estado;
    }
    function verificar_token($ruta, $clave)
    {
        $file = $ruta;
        $pass = $clave;
        $fd = fopen($file, 'r');
        $p12buf = fread($fd, filesize($file));
        fclose($fd);
        $p12cert = array();
        openssl_pkcs12_read($p12buf, $p12cert, $pass);
        if (openssl_pkcs12_read($p12buf, $p12cert, $pass))
            return true;
        else
            return false;
    }
    private function validarData($data)
    {
        $flag_error = false;
        $error = [];
        $cont = 0;
        $tipoIdentificacionCliente = 0;
        $valida_cedula = false;
        if (!isset($data['datosfirma']['clave_firma'])) {
            $flag_error = true;
            $error[$cont] =  'Error: ' . ($cont + 1) . ': clave de firma no registrada (' . $data['datosfirma']['clave_firma'] . ')';
            $cont++;
        } else {
            if ($data['datosfirma']['clave_firma'] == '') {
                $flag_error = true;
                $error[$cont] =  'Error: ' . ($cont + 1) . ': clave de firma no registrada (' . $data['datosfirma']['clave_firma'] . ')';
                $cont++;
            }
        }
        if (!isset($data['datosfirma']['ruta_firma'])) {
            $flag_error = true;
            $error[$cont] =  "Error: " . ($cont + 1) . ": firma no registrada";
            $cont++;
        } else {
            if ($data['datosfirma']['ruta_firma'] == '') {
                $flag_error = true;
                $error[$cont] =  "Error: " . ($cont + 1) . ": firma no registrada";
                $cont++;
            } else {
                if (!file_exists(base_path() . '/storage/app/facturaelectronica/p12/' . $data['datosfirma']['ruta_firma'])) {
                    $flag_error = true;
                    $error[$cont] =  'Error: ' . ($cont + 1) . ': el archivo de la firma no existe (' . $data['datosfirma']['ruta_firma'] . ')';
                    $cont++;
                } else {
                    if (!$this->verificar_token(base_path() . '/storage/app/facturaelectronica/p12/' . $data['datosfirma']['ruta_firma'], $data['datosfirma']['clave_firma'])) {
                        $flag_error = true;
                        $error[$cont] =  "Error: " . ($cont + 1) . ": la clave no corresponde al archivo de la firma";
                        $cont++;
                    }
                }
            }
        }
        if (isset($data['infoTributaria']['ruc'])) {
            if ($data['infoTributaria']['ruc'] == '') {
                $flag_error = true;
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                $cont++;
            }
            if (strlen($data['infoTributaria']['ruc']) != 13) {
                $flag_error = true;
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                $cont++;
            }
            $empresa = Empresa::where('id', $data['infoTributaria']['ruc'])->first();
            if (is_null($empresa)) {
                $flag_error = true;
                $error[$cont] =  "Error: " . ($cont + 1) . ": no existe la empresa";
                $cont++;
            } else {
                //InfoFactura
                //validar si la empresa esta habilitada como facturacion electronica
                $deEmpresa = De_Empresa::where('id_empresa', $empresa);
                if (is_null($deEmpresa)) {
                    $flag_error = true;
                    $error[$cont] =  "Error: " . ($cont + 1) . ": No se encuentra informacion de facturacion electronica";
                    $cont++;
                } elseif ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ": empresa no permite facturacion electronica";
                    $cont++;
                }
                //validar ambiente
                if (isset($data['infoTributaria']['ambiente'])) {
                    if ($data['infoTributaria']['ambiente'] == '') {
                        $flag_error = true;
                        $error[$cont] =  "Error " . ($cont + 1) . ': el campo ambiente es obligatorio';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['ambiente']) != 1) {

                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el ambiente debe contener un digito';
                        $cont++;
                    }
                    if ($data['infoTributaria']['ambiente'] != 1 && $data['infoTributaria']['ambiente'] != 2) {
                        $flag_error = true;
                        $error[$cont] = 'Error ' . ($cont + 1) . ': el ambiente solo puede ser 1 o 2';
                        $cont++;
                    }
                    if (!is_numeric($data['infoTributaria']['ambiente'])) {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el ambiente debe ser numerico';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ambiente es obligatorio';
                    $cont++;
                }
                //validar tipo Emision
                if (isset($data['infoTributaria']['tipoEmision'])) {
                    if ($data['infoTributaria']['tipoEmision'] == '') {
                        $flag_error = true;
                        $error[$cont] =  "Error " . ($cont + 1) . ': el campo tipoEmision es obligatorio';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['tipoEmision']) != 1) {

                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el tipoEmision debe contener un digito';
                        $cont++;
                    }
                    if ($data['infoTributaria']['tipoEmision'] != 1) {
                        $flag_error = true;
                        $error[$cont] = 'Error ' . ($cont + 1) . ': el tipoEmision solo puede ser 1';
                        $cont++;
                    }
                    if (!is_numeric($data['infoTributaria']['tipoEmision'])) {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el tipoEmision debe ser numerico';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoEmision es obligatorio';
                    $cont++;
                }
                //Validar razonSocial
                if (isset($data['infoTributaria']['razonSocial'])) {
                    if ($data['infoTributaria']['razonSocial'] == '') {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['razonSocial']) > 300) {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial es obligatorio';
                    $cont++;
                }
                //Validar nombreComercial
                if (isset($data['infoTributaria']['nombreComercial'])) {
                    if ($data['infoTributaria']['nombreComercial'] != '') {
                        if (strlen($data['infoTributaria']['nombreComercial']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial es obligatorio';
                    $cont++;
                }
                //validar ruc
                if (isset($data['infoTributaria']['ruc'])) {
                    if ($data['infoTributaria']['ruc'] == '') {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['ruc']) != 13) {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc es obligatorio';
                    $cont++;
                }
                //Validar si puede emitir documentos electronicos
                if ($empresa->electronica != 1) {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ": empresa no permite facturacion electronica";
                    $cont++;
                }
                //validar ClaveAcceso
                if (isset($data['infoTributaria']['claveAcceso'])) {
                    if (!$data['infoTributaria']['claveAcceso'] == '') {
                        if (strlen($data['infoTributaria']['claveAcceso']) != 49) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso debe contener 49 caracteres (' . $data['infoTributaria']['claveAcceso'] . ' No. de caracteres ingresados: ' . strlen($data['infoTributaria']['claveAcceso']) . ' )';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['claveAcceso'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso debe ser numerico (' . $data['infoTributaria']['claveAcceso'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso es obligatorio';
                    $cont++;
                }
                //validar codDoc
                if (isset($data['infoTributaria']['codDoc'])) {
                    if ($data['infoTributaria']['codDoc'] != '') {
                        if (strlen($data['infoTributaria']['codDoc']) != 2) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc debe contener 2 caracteres (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['codDoc'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc debe ser numerico (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                        if ($data['infoTributaria']['codDoc'] != '01' && $data['infoTributaria']['codDoc'] != '03' && $data['infoTributaria']['codDoc'] != '04' && $data['infoTributaria']['codDoc'] != '05' && $data['infoTributaria']['codDoc'] != '06' && $data['infoTributaria']['codDoc'] != '07') {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['codDoc'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc es obligatorio';
                    $cont++;
                }
                //validar estab
                if (isset($data['infoTributaria']['estab'])) {
                    if (strlen($data['infoTributaria']['estab']) != '') {
                        if (strlen($data['infoTributaria']['estab']) != 3) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estab debe contener 3 caracteres (' . $data['infoTributaria']['estab'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['estab'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estab debe ser numerico (' . $data['infoTributaria']['estab'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo estab no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo estab es obligatorio';
                    $cont++;
                }
                //validar ptoEmi
                if (isset($data['infoTributaria']['ptoEmi'])) {
                    if (strlen($data['infoTributaria']['ptoEmi']) != '') {
                        if (strlen($data['infoTributaria']['ptoEmi']) != 3) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi debe contener 3 caracteres (' . $data['infoTributaria']['ptoEmi'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['ptoEmi'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi debe ser numerico (' . $data['infoTributaria']['ptoEmi'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi es obligatorio';
                    $cont++;
                }
                //validar secuencial
                if (isset($data['infoTributaria']['secuencial'])) {
                    if (strlen($data['infoTributaria']['secuencial']) != '') {
                        if (strlen($data['infoTributaria']['secuencial']) != 9) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial debe contener 9 caracteres (' . $data['infoTributaria']['secuencial'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['secuencial'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial debe ser numerico (' . $data['infoTributaria']['secuencial'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial es obligatorio';
                    $cont++;
                }
                //validar dirMatriz
                if (isset($data['infoTributaria']['dirMatriz'])) {
                    if ($data['infoTributaria']['dirMatriz'] != '') {
                        if (strlen($data['infoTributaria']['dirMatriz']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz es obligatorio';
                    $cont++;
                }
                // infoGuiadeRemision
                //validar dirEstablecimiento
                if (isset($data['infoGuiaRemision']['dirEstablecimiento'])) {
                    if ($data['infoGuiaRemision']['dirEstablecimiento'] != '') {
                        if (strlen($data['infoGuiaRemision']['dirEstablecimiento']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                    $cont++;
                }
                //validar razonSocialTransportista
                if (isset($data['infoGuiaRemision']['razonSocialTransportista'])) {
                    if ($data['infoGuiaRemision']['razonSocialTransportista'] != '') {
                        if (strlen($data['infoGuiaRemision']['razonSocialTransportista']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista es obligatorio';
                    $cont++;
                }
                //validar dirPartida
                if (isset($data['infoGuiaRemision']['dirPartida'])) {
                    if ($data['infoGuiaRemision']['dirPartida'] != '') {
                        if (strlen($data['infoGuiaRemision']['dirPartida']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida es obligatorio';
                    $cont++;
                }
                //validar tipoIdentificacionTransportista
                if (isset($data['infoGuiaRemision']['tipoIdentificacionTransportista'])) {
                    if ($data['infoGuiaRemision']['tipoIdentificacionTransportista'] != '') {
                        if (strlen($data['infoGuiaRemision']['tipoIdentificacionTransportista']) != 2) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista debe contener 2 caracteres (' . $data['infoGuiaRemision']['tipoIdentificacionTransportista'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoGuiaRemision']['tipoIdentificacionTransportista'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista debe ser numerico (' . $data['infoGuiaRemision']['tipoIdentificacionTransportista'] . ')';
                            $cont++;
                        }
                        if ($data['infoGuiaRemision']['tipoIdentificacionTransportista'] != '04' && $data['infoGuiaRemision']['tipoIdentificacionTransportista'] != '05') {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista solo puede ser 04 o 05 (' . $data['infoGuiaRemision']['tipoIdentificacionTransportista'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista es obligatorio';
                    $cont++;
                }
                //validar rucTransportista
                if (isset($data['infoGuiaRemision']['rucTransportista'])) {
                    if (strlen($data['infoGuiaRemision']['rucTransportista']) != 10 && strlen($data['infoGuiaRemision']['rucTransportista']) != 13) {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista debe contener 10 o 13 caracteres maximo (No. de caracteres ingresados ' . strlen($data['infoGuiaRemision']['rucTransportista']) . ')';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista es obligatorio';
                    $cont++;
                }
                //validar Rise
                if (isset($data['infoGuiaRemision']['rise'])) {
                    if ($data['infoGuiaRemision']['rise'] != '') {
                        if (strlen($data['infoGuiaRemision']['rise']) > 40) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo rise debe contener 40 caracteres (' . $data['infoGuiaRemision']['rise'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo rise no puede ser vacio';
                        $cont++;
                    }
                }
                //validar obligadoContabilidad
                if (isset($data['infoGuiaRemision']['obligadoContabilidad'])) {
                    if ($data['infoGuiaRemision']['obligadoContabilidad'] != '') {
                        if (strtoupper($data['infoGuiaRemision']['obligadoContabilidad']) != 'SI' && strtoupper($data['infoGuiaRemision']['obligadoContabilidad']) != 'NO') {
                            $data['infoGuiaRemision']['obligadoContabilidad'] = strtoupper($data['infoGuiaRemision']['obligadoContabilidad']);
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['infoGuiaRemision']['obligadoContabilidad'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                        $cont++;
                    }
                }
                //validar contribuyenteEspecial
                if (isset($data['infoGuiaRemision']['contribuyenteEspecial'])) {
                    if ($data['infoGuiaRemision']['contribuyenteEspecial'] != '') {
                        if (strlen($data['infoGuiaRemision']['contribuyenteEspecial']) >= 3 && strlen($data['infoGuiaRemision']['contribuyenteEspecial']) <= 13) {
                        } else {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['infoGuiaRemision']['contribuyenteEspecial'] . ')';
                            $cont++;
                        }
                    }
                }
                //validar fechaIniTransporte
                if (isset($data['infoGuiaRemision']['fechaIniTransporte'])) {
                    if ($data['infoGuiaRemision']['fechaIniTransporte'] != '') {
                        if (!$this->validarFechaCorrecta($data['infoGuiaRemision']['fechaIniTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no es valida (' . $data['infoGuiaRemision']['fechaIniTransporte'] . ')';
                            $cont++;
                        }
                        if (!$this->validarFecha($data['infoGuiaRemision']['fechaIniTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaIniTransporte'] . ')';
                            $cont++;
                        }
                        if (!$this->validarFechaInicio($data['infoGuiaRemision']['fechaIniTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte debe ser igual o mayor a la fecha actual (' . $data['infoGuiaRemision']['fechaIniTransporte'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte es obligatorio';
                    $cont++;
                }
                //validar fechaFinTransporte
                if (isset($data['infoGuiaRemision']['fechaFinTransporte'])) {
                    if ($data['infoGuiaRemision']['fechaFinTransporte'] != '') {
                        if (!$this->validarFechaCorrecta($data['infoGuiaRemision']['fechaFinTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no es valida (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                            $cont++;
                        }
                        if (!$this->validarFecha($data['infoGuiaRemision']['fechaFinTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                            $cont++;
                        }
                        if (!$this->validarFechaInicio($data['infoGuiaRemision']['fechaFinTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte debe ser igual o mayor a la fecha actual (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                            $cont++;
                        }
                        if (!$this->validarFechaFinal($data['infoGuiaRemision']['fechaIniTransporte'], $data['infoGuiaRemision']['fechaFinTransporte'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte debe ser igual o mayor a la fecha de inicio (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte es obligatorio';
                    $cont++;
                }
                //validar placa
                if (isset($data['infoGuiaRemision']['placa'])) {
                    if ($data['infoGuiaRemision']['placa'] != '') {
                        if (strlen($data['infoGuiaRemision']['placa']) > 20) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo placa debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo placa no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo placa es obligatorio';
                    $cont++;
                }
                //Destinatarios
                //validar destinatarios
                if (isset($data['destinatarios']['destinatario']['identificacionDestinatario'])) {
                    if ($data['destinatarios']['destinatario']['identificacionDestinatario'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['identificacionDestinatario']) > 20) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                    $cont++;
                }
                //validar razonSocialDestinatario
                if (isset($data['destinatarios']['destinatario']['razonSocialDestinatario'])) {
                    if ($data['destinatarios']['destinatario']['razonSocialDestinatario'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['razonSocialDestinatario']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario es obligatorio';
                    $cont++;
                }
                //validar dirDestinatario
                if (isset($data['destinatarios']['destinatario']['dirDestinatario'])) {
                    if ($data['destinatarios']['destinatario']['dirDestinatario'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['dirDestinatario']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario es obligatorio';
                    $cont++;
                }
                //Validar motivoTraslado
                if (isset($data['destinatarios']['destinatario']['motivoTraslado'])) {
                    if ($data['destinatarios']['destinatario']['motivoTraslado'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['motivoTraslado']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado es obligatorio';
                    $cont++;
                }
                //validar docAduaneroUnico
                if (isset($data['destinatarios']['destinatario']['docAduaneroUnico'])) {
                    if ($data['destinatarios']['destinatario']['docAduaneroUnico'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['docAduaneroUnico']) > 20) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo docAduaneroUnico debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo docAduaneroUnico no puede ser vacio';
                        $cont++;
                    }
                }
                //validar codEstabDestino
                if (isset($data['destinatarios']['destinatario']['codEstabDestino'])) {
                    if ($data['destinatarios']['destinatario']['codEstabDestino'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['codEstabDestino']) != 3) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino debe contener 3 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($data['destinatarios']['destinatario']['codEstabDestino'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino debe ser numerico (' . $data['destinatarios']['destinatario']['codEstabDestino'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino no puede ser vacio';
                        $cont++;
                    }
                }
                //validar ruta
                if (isset($data['destinatarios']['destinatario']['ruta'])) {
                    if ($data['destinatarios']['destinatario']['ruta'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['ruta']) > 300) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta no puede ser vacio';
                    $cont++;
                }
                //validar codDocSustento
                if (isset($data['destinatarios']['destinatario']['codDocSustento'])) {
                    if ($data['destinatarios']['destinatario']['codDocSustento'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['codDocSustento']) != 2) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe contener 2 caracteres';
                            $cont++;
                        }
                        if ($data['destinatarios']['destinatario']['codDocSustento'] != '01' && $data['destinatarios']['destinatario']['codDocSustento'] != '03' && $data['destinatarios']['destinatario']['codDocSustento'] != '04' && $data['infoTributaria']['codDoc'] != '05' && $data['destinatarios']['destinatario']['codDocSustento'] != '06' && $data['destinatarios']['destinatario']['codDocSustento'] != '07') {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['destinatarios']['destinatario']['codDocSustento'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['destinatarios']['destinatario']['codDocSustento'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe ser numerico (' . $data['destinatarios']['destinatario']['codDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar numDocSustento
                if (isset($data['destinatarios']['destinatario']['numDocSustento'])) {
                    if ($data['destinatarios']['destinatario']['numDocSustento'] != '') {
                        $data['destinatarios']['destinatario']['numDocSustento'] = str_replace('-', '', $data['destinatarios']['destinatario']['numDocSustento']);
                        if (strlen($data['destinatarios']['destinatario']['numDocSustento']) != 15) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento debe contener 15 caracteres (No. de caracteres ' . strlen($data['destinatarios']['destinatario']['numDocSustento']) . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['destinatarios']['destinatario']['numDocSustento'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento debe ser numerico (' . $data['destinatarios']['destinatario']['numDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //Validar numAutDocSustento
                if (isset($data['destinatarios']['destinatario']['numAutDocSustento'])) {
                    if ($data['destinatarios']['destinatario']['numAutDocSustento'] != '') {
                        if (strlen($data['destinatarios']['destinatario']['numAutDocSustento']) != 10 && strlen($data['destinatarios']['destinatario']['numAutDocSustento']) != 37 && strlen($data['destinatarios']['destinatario']['numAutDocSustento']) != 49) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe contener 10, 37 o 49 caracteres (numero de caracteres ingresados ' . strlen($data['destinatarios']['destinatario']['numAutDocSustento']) . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['destinatarios']['destinatario']['numAutDocSustento'])) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe ser numerico (' . $data['destinatarios']['destinatario']['numAutDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento no puede ser vacio';
                        $cont++;
                    }
                } else {
                    if (isset($data['destinatarios']['destinatario']['numDocSustento'])) {
                        if ($data['destinatarios']['destinatario']['numAutDocSustento'] != '') {
                            if (strlen($data['destinatarios']['destinatario']['numAutDocSustento']) != 10 && strlen($data['destinatarios']['destinatario']['numAutDocSustento']) != 37 && strlen($data['destinatarios']['destinatario']['numAutDocSustento']) != 49) {
                                $flag_error = true;
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe contener 10, 37 o 49 caracteres (numero de caracteres ingresados ' . strlen($data['destinatarios']['destinatario']['numAutDocSustento']) . ')';
                                $cont++;
                            }
                            if (!is_numeric($data['destinatarios']['destinatario']['numAutDocSustento'])) {
                                $flag_error = true;
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe ser numerico (' . $data['destinatarios']['destinatario']['numAutDocSustento'] . ')';
                                $cont++;
                            }
                        } else {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento no puede ser vacio';
                            $cont++;
                        }
                    }
                }
                //validar fechaEmisionDocSustento
                if (isset($data['destinatarios']['destinatario']['fechaEmisionDocSustento'])) {
                    if ($data['destinatarios']['destinatario']['fechaEmisionDocSustento'] != '') {
                        $nfecha = $this->getdate(str_replace('/', '-', $data['destinatarios']['destinatario']['fechaEmisionDocSustento']));
                        $data['destinatarios']['destinatario']['fechaEmisionDocSustento'] = $nfecha;
                        if (!$this->validarFecha($nfecha)) {
                            $flag_error = true;
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no tiene el formato correcto dd/mm/yyyy (' . $data['destinatarios']['destinatario']['fechaEmisionDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar detalles
                if (isset($data['destinatarios']['destinatario']['detalles'])) {
                    if (count($data['destinatarios']['destinatario']['detalles']) == 0) {
                        $flag_error = true;
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede estar vacio';
                        $cont++;
                    } else {
                        foreach ($data['destinatarios']['destinatario']['detalles'] as $detalle) {
                            if (count($detalle) == 0) {
                                $flag_error = true;
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles del producto no puede estar vacio';
                                $cont++;
                            } else {
                                if ($detalle['codigoInterno'] == '') {
                                    $flag_error = true;
                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoInterno del detalle del producto no puede estar vacio';
                                    $cont++;
                                }
                                if ($detalle['codigoAdicional'] == '') {
                                    $flag_error = true;
                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle del producto no puede estar vacio';
                                    $cont++;
                                }
                                if ($detalle['descripcion'] == '') {
                                    $flag_error = true;
                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle del producto no puede estar vacio';
                                    $cont++;
                                }
                                if ($detalle['cantidad'] == '') {
                                    $flag_error = true;
                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle del producto no puede estar vacio';
                                    $cont++;
                                }
                                if (isset($detalle['detallesAdicionales'])) {
                                    if (count($detalle['detallesAdicionales']) == 0) {
                                        $flag_error = true;
                                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detallesAdicionales del detalle del producto no puede estar vacio';
                                        $cont++;
                                    } else {
                                        foreach ($detalle['detallesAdicionales'] as $detA) {
                                            if (isset($detA['nombre'])) {
                                                if ($detA['nombre'] == '') {
                                                    $flag_error = true;
                                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo nombre no puede estar vacio';
                                                    $cont++;
                                                }
                                            }
                                            if (isset($detA['valor'])) {
                                                if ($detA['valor'] == '') {
                                                    $flag_error = true;
                                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valor no puede estar vacio';
                                                    $cont++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $flag_error = true;
                                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detallesAdicionales debe contener minimo un registro';
                                    $cont++;
                                }
                            }
                        }
                    }
                } else {
                    $flag_error = true;
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                    $cont++;
                }
            }
        } else {
            $flag_error = true;
            $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc es obligatorio';
            $cont++;
        }
        return $error;
    }
    private function getDate($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('d/m/Y', strtotime($valor));
        return $result;
    }
    private function validarFechaFinal($fechainicial, $fechafinal)
    {
        $fechainicial = date('Y-m-d', strtotime($fechainicial));
        $fechafinal = date('Y-m-d', strtotime($fechafinal));
        $respuesta = false;
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_entrada = strtotime($fechafinal);
        if ($fecha_entrada >= $fecha_actual)
            $respuesta = true;
        return $respuesta;
    }
    private function validarFechaInicio($fecha)
    {
        $respuesta = false;
        if ($this->validarFecha($fecha)) {
            try {
                $date = date('Y-m-d', strtotime($fecha));
                if ($date != '') {
                    if ($date >= date('Y-m-d'))
                        $respuesta = true;
                } else
                    $respuesta = false;
            } catch (Exception $e) {
                $respuesta = false;
            }
        } else {
            $respuesta = false;
        }
        return $respuesta;
    }
    private function validarFechaCorrecta($fecha)
    {
        $respuesta = true;
        $fecha = str_replace('-', '/', $fecha);
        if ($fecha == '31/12/1969') {
            $respuesta = false;
        }
        return $respuesta;
    }
    private function validarFecha($fecha)
    {
        $respuesta = true;
        try {
            //$fecha = str_replace('-', '/', $fecha);
            $fecha = date('Y-m-d', strtotime($fecha));
            $valores = explode('-', $fecha);
            if (count($valores) != 3 && strlen($valores[0]) != 4 && strlen($valores[1]) != 2 && strlen($valores[2]) != 2) {
                $respuesta = false;
            }
        } catch (Exception $e) {
            $respuesta = false;
        }
        return $respuesta;
    }
    private function getInfoTributaria($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoTributaria');
        $nodoDetalle->appendChild($xmlDocument->createElement('ambiente', $campos['infoTributaria']['ambiente']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoEmision', $campos['infoTributaria']['tipoEmision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocial', $campos['infoTributaria']["razonSocial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('nombreComercial', str_replace('&', 'Y', $campos['infoTributaria']["nombreComercial"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('ruc', $campos['infoTributaria']["ruc"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('claveAcceso', $campos['infoTributaria']["claveAcceso"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDoc', str_pad($campos['infoTributaria']["codDoc"], 2, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('estab', str_pad($campos['infoTributaria']["estab"], 3, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('ptoEmi', str_pad($campos['infoTributaria']["ptoEmi"], 3, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('secuencial', str_pad($campos['infoTributaria']["secuencial"], 9, 0, STR_PAD_LEFT)));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirMatriz', $campos['infoTributaria']["dirMatriz"]));
        if (isset($campos['infoTributaria']['agenteRetencion']))
            $nodoDetalle->appendChild($xmlDocument->createElement('agenteRetencion', "201"));
        elseif (isset($campos['infoTributaria']['rimpe_emprendedor']))
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE R??GIMEN RIMPE"));
        elseif (isset($campos['infoTributaria']['rimpe_popular']))
            $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteRimpe', "CONTRIBUYENTE R??GIMEN POPULAR"));
        return $nodoDetalle;
    }
    private  function getInfoGuiaRemision($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoGuiaRemision');
        //$nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos["fecha_emision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoGuiaRemision']["dirEstablecimiento"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirPartida', $campos['infoGuiaRemision']["dirPartida"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialTransportista', $campos['infoGuiaRemision']["razonSocialTransportista"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionTransportista', $campos['infoGuiaRemision']['tipoIdentificacionTransportista']));
        $nodoDetalle->appendChild($xmlDocument->createElement('rucTransportista', $campos['infoGuiaRemision']["rucTransportista"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoGuiaRemision']["obligadoContabilidad"]));
        if ($campos['infoGuiaRemision']["contribuyenteEspecial"]) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoGuiaRemision']["contribuyenteEspecial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaIniTransporte', $this->getDate($campos['infoGuiaRemision']["fechaIniTransporte"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaFinTransporte', $this->getDate($campos['infoGuiaRemision']["fechaFinTransporte"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('placa', $campos['infoGuiaRemision']["placa"]));
        return $nodoDetalle;
    }
    private  function getDetalles($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('detalles');
        $detalles = $campos["detalles"];
        foreach ($detalles as $detalle) {
            $precioTotalSinImpuesto = $detalle["cantidad"] * $detalle["precio"];
            $item = $xmlDocument->createElement('detalle');
            $item->appendChild($xmlDocument->createElement('codigoPrincipal', $detalle["sku"]));
            $item->appendChild($xmlDocument->createElement('codigoAdicional', $detalle["sku"]));
            $item->appendChild($xmlDocument->createElement('descripcion', str_replace('&', 'y', $detalle["nombre"])));
            $item->appendChild($xmlDocument->createElement('cantidad', $this->getDecimal($detalle["cantidad"])));
            $item->appendChild($xmlDocument->createElement('precioUnitario', $this->getDecimal($detalle["precio"])));
            $item->appendChild($xmlDocument->createElement('descuento', $this->getDecimal($detalle["descuento"])));
            $item->appendChild($xmlDocument->createElement('precioTotalSinImpuesto', $this->getDecimal($precioTotalSinImpuesto)));
            if (count($detalle["detAdicionales"]) > 0) {
                $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument, $detalle["detAdicionales"]);
                $item->appendChild($detallesAdicionales);
            }
            $impuestos = $this->getImpuestos($xmlDocument, $detalles);
            $item->appendChild($impuestos);
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    private  function getImpuestos($xmlDocument, $detalles)
    {
        $nodoDetalle = $xmlDocument->createElement('impuestos');
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('impuesto');
            if ($detalle['p_impuesto'] == 12)
                $sri_tipo_impuesto_iva_id = 1;
            $impuestoIva = De_Codigo_Impuestos::where('id', $sri_tipo_impuesto_iva_id)->first();
            $baseImponible = $detalle["cantidad"] * $detalle["precio"];
            $impuesto = ($baseImponible * $detalle['p_impuesto']) / 100;
            $item->appendChild($xmlDocument->createElement('codigo', $impuestoIva->codigo_impuesto));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $impuestoIva->codigo));
            $item->appendChild($xmlDocument->createElement('tarifa', $detalle["p_impuesto"]));
            $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($baseImponible)));
            $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($impuesto)));
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    private  function getDestinatarios($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('destinatarios');
        $destinatarios = $campos["destinatarios"];
        foreach ($destinatarios as $destinatario) {
            $email = '';
            $item = $xmlDocument->createElement('destinatario');
            $item->appendChild($xmlDocument->createElement('identificacionDestinatario', $destinatario["identificacionDestinatario"]));
            $item->appendChild($xmlDocument->createElement('razonSocialDestinatario', $destinatario["razonSocialDestinatario"]));
            $item->appendChild($xmlDocument->createElement('dirDestinatario', $destinatario["dirDestinatario"]));
            $item->appendChild($xmlDocument->createElement('motivoTraslado', $destinatario["motivoTraslado"]));
            $item->appendChild($xmlDocument->createElement('codEstabDestino', str_pad($destinatario["codEstabDestino"], 3, 0, STR_PAD_LEFT)));
            $item->appendChild($xmlDocument->createElement('ruta', $destinatario["ruta"]));
            if (isset($destinatario["codDocSustento"])) {
                $item->appendChild($xmlDocument->createElement('codDocSustento', str_pad($destinatario["codDocSustento"], 2, 0, STR_PAD_LEFT)));
                if (isset($destinatario["numDocSustento"])) {
                    if ($destinatario["numDocSustento"] != '') {
                        $item->appendChild($xmlDocument->createElement('numDocSustento', $destinatario["numDocSustento"]));
                        if ($destinatario["numAutDocSustento"] != '0')
                            $item->appendChild($xmlDocument->createElement('numAutDocSustento', $destinatario["numAutDocSustento"]));
                        else
                            $item->appendChild($xmlDocument->createElement('numAutDocSustento', '9999999999'));
                        if ($email != "") array_push($destinatarios, array("nombre" => 'Email', "valor" => strtoupper($email)));
                    }
                }
                $item->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $destinatario["fechaEmisionDocSustento"]));
            }
            $nodoDetalleMercancia = $xmlDocument->createElement('detalles');
            $detalles = $destinatario['detalles'];
            foreach ($detalles as $detalle) {
                $itemMercancia = $xmlDocument->createElement('detalle');
                $itemMercancia->appendChild($xmlDocument->createElement('codigoInterno', $detalle['codigoInterno']));
                $itemMercancia->appendChild($xmlDocument->createElement('codigoAdicional',  $detalle['codigoAdicional']));
                $itemMercancia->appendChild($xmlDocument->createElement('descripcion', $detalle['descripcion']));
                $itemMercancia->appendChild($xmlDocument->createElement('cantidad', $detalle['cantidad']));
                $nodoDetalleMercancia->appendChild($itemMercancia);
                if (count($detalle["detallesAdicionales"]) > 0) {
                    $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument, $detalle["detallesAdicionales"]);
                    $itemMercancia->appendChild($detallesAdicionales);
                }
                $item->appendChild($nodoDetalleMercancia);
                $nodoDetalle->appendChild($item);
            }
        }
        return $nodoDetalle;
    }
    private  function getDetallesAdicionales($xmlDocument, $detalles)
    {
        $nodoDetalle = $xmlDocument->createElement('detallesAdicionales');
        $count = 0;
        foreach ($detalles as $detalle) {
            if (count($detalle) > 0) {
                $count++;
                if ($count > 3) break;
                $item = $xmlDocument->createElement('detAdicional');
                $item->setAttribute('nombre', $detalle['nombre']);
                $item->setAttribute('valor', $detalle['valor']);
                $nodoDetalle->appendChild($item);
            }
        }
        return $nodoDetalle;
    }
    private  function getInfoAdicional($xmlDocument, $campos)
    {
        $resolucion = 0;
        $detalles = array();
        $cont = 0;
        $valor = '';
        $cantidadInfoAdicional = 0;
        $informacionAdicional = $campos['infoAdicional'];
        if (count($informacionAdicional) > 0) {
            $cantidadInfoAdicional = count($informacionAdicional);
        }

        for ($i = 0; $i < $cantidadInfoAdicional; $i++) {
            if (isset($informacionAdicional[$i]['campoAdicional']['nombre']))
                $nombre = $informacionAdicional[$i]['campoAdicional']['nombre'];
            if (isset($informacionAdicional[$i]['campoAdicional']['valor']))
                $valor = $informacionAdicional[$i]['campoAdicional']['valor'];
            array_push($detalles, array("nombre" => $nombre, "valor" => str_replace(' ', '', str_replace('(', '', str_replace(')', '', str_replace('-', '', $valor))))));
            $nodoDetalle = $xmlDocument->createElement('infoAdicional');
        }

        foreach ($detalles as $item) {
            $campoAdicional = $xmlDocument->createElement('campoAdicional', isset($item["valor"]) ? str_replace("'", "", str_replace('&', '&amp;', $item["valor"])) : 'NN');
            $campoAdicional->setAttribute('nombre', $item["nombre"]);
            $nodoDetalle->appendChild($campoAdicional);
        }
        // dd($nodoDetalle);
        return $nodoDetalle;
    }
    private  function getDecimal($valor = 0, $digitos = 2, $formato = false)
    {
        if (trim($valor) == "") $valor = 0;
        $resultado = round($valor, $digitos, PHP_ROUND_HALF_UP);
        $resultado = number_format($resultado, $digitos, ".", $formato ? "," : "");
        return $resultado;
    }
    private function generarDocElectronicoXml($campos)
    {
        $comprobante = null;
        if ($campos['infoTributaria']['codDoc'] == 01)
            $this->tipoDocumento = 'factura';
        elseif ($campos['infoTributaria']['codDoc'] == 06)
            $this->tipoDocumento = 'guiaRemision';
        $xml = new \DOMDocument('1.0', 'UTF-8');
        if ($this->tipoDocumento == 'guiaRemision') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoGuiaRemision = $this->getInfoGuiaRemision($xml, $campos);
            $root->appendChild($infoGuiaRemision);
            $destinatarios = $this->getDestinatarios($xml, $campos);
            $root->appendChild($destinatarios);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'factura') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoFactura = $this->getInfoFactura($xml, $campos);
            $root->appendChild($infoFactura);
            $detalles = $this->getDetalles($xml, $campos);
            $root->appendChild($detalles);
            if (isset($campos["retenciones"]) && count($campos["retenciones"]) > 0) {
                $retenciones = $this->getRetencionesFactura($xml, $campos);
                $root->appendChild($retenciones);
            }
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        }
        //Guarda XML
        $xml->appendChild($root);
        $xml->formatOutput = true;
        $comprobante = $xml->saveXML();
        if ($this->tipoDocumento == 'factura') {
            $r_ = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $campos['infoTributaria']['ruc'] . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $this->tipoDocumento . '-' . $campos['infoTributaria']['estab'] . '-' .  $campos['infoTributaria']['ptoEmi'] . '-' . str_pad($campos['infoTributaria']['secuencial'], 9, 0, STR_PAD_LEFT) . '.xml';
            $xml->save($ruta);
        } elseif ($this->tipoDocumento == 'guiaRemision') {
            $r_ = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $campos['infoTributaria']['ruc'] . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $this->tipoDocumento . '-' . $campos['infoTributaria']['estab'] . '-' .  $campos['infoTributaria']['ptoEmi'] . '-' . str_pad($campos['infoTributaria']['secuencial'], 9, 0, STR_PAD_LEFT) . '.xml';
            $xml->save($ruta);
        }
        return $comprobante;
    }
    public static function crearcarpeta($ruta)
    {
        if (!file_exists($ruta)) {
            mkdir($ruta, 0777, true);
        }
    }
    public function validarXmlToXsd($dataDoc)
    {
        ini_set('max_execution_time', 300);
        $result = array();
        $isValidoXsd = false;
        $mensajes = array();
        $xsd = "";
        try {
            $xsd = "";
            switch ($this->tipoDocumento) {
                case 'factura': //'1.1.0'
                    $xsd =  base_path() . '/storage/app/facturaelectronica/SriXsd/factura_V1.1.0.xsd';
                    break;
                case 'comprobanteRetencion': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'notaCredito': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'notaDebito': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'guiaRemision': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/GuiaRemision_V1.1.0.xsd';
                    break;
            }
            $doc = new DOMDocument();
            $doc->loadXML($dataDoc);
            libxml_clear_errors();
            libxml_use_internal_errors(true);
            $isValidoXsd = $doc->schemaValidate($xsd);
            $errors = libxml_get_errors();
            if ($errors) {
                foreach ($errors as $error) {
                    array_push($mensajes, trim("($error->code) $error->message"));
                }
            }
            $result["isValidoXsd"] = $isValidoXsd;
            $result["mensajes"] = $mensajes;
            return $result;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    public function mostrarError($errors): string
    {
        $msg = '';
        if ($errors == NULL) {
            return '';
        }
        foreach ($errors as $error) {
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $nivel = 'Warning';
                    break;
                case LIBXML_ERR_ERROR:
                    $nivel = 'Error';
                    break;
                case LIBXML_ERR_FATAL:
                    $nivel = 'Fatal Error';
                    break;
            }
            $msg .= "<b>Error $error->code [$nivel]:</b><br>"
                . str_repeat('&nbsp;', 6) . "Linea: $error->line<br>"
                . str_repeat('&nbsp;', 6) . "Mensaje: $error->message<br>";
        }
        //Limpia el buffer de errores de libxml
        libxml_clear_errors();
        return $msg;
    }
    public function validarXmlToXsd_($dataDoc)
    {
        ini_set('max_execution_time', 300);
        $respuestaCmdXSD = "";
        $docAnte = simplexml_load_string($dataDoc);
        $pathxml = base_path() . '/storage/app/facturaelectronica/sinfirmar/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml';
        try {
            $xsd = "";
            switch ($this->tipoDocumento) {
                case 'factura': //'1.1.0'
                    $xsd =  base_path() . '/storage/app/facturaelectronica/SriXsd/factura_V1.1.0.xsd';
                    break;
                case 'comprobanteRetencion': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'notaCredito': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'notaDebito': //1.0.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/.xsd';
                    break;
                case 'guiaRemision': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/GuiaRemision_V1.1.0.xsd';
                    break;
            }
            $jarXSD = base_path() . '/ValidadorXSD/dist/ValidadorXSD.jar';
            $commandXSD = "java -jar $jarXSD $xsd $pathxml 2>&1";
            $respuestaCmdXSD = exec($commandXSD, $output, $return_value);
            return $respuestaCmdXSD;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    public function generarXmlSignJar($dataDoc, $dir = '', $ruta_token = '', $pin_token = '', $clave_acceso = '', $id_factura = '', $empresa_Id)
    {
        ini_set('max_execution_time', 300);
        $docAnte = simplexml_load_string($dataDoc);
        //$pathxml = base_path() . '/storage/app/facturaelectronica/firmados/'.$docAnte->infoTributaria->ruc.'/'.$this->tipoDocumento.'/'.$this->tipoDocumento.'-'.$docAnte->infoTributaria->estab.'-'.$docAnte->infoTributaria->ptoEmi.'-'.$docAnte->infoTributaria->secuencial.'.xml';
        $fileName = ($clave_acceso) . "_" . uniqid();
        $filenameXml = tempnam(sys_get_temp_dir(), "");
        $carpeta = base_path() . '/storage/app/facturaelectronica/temp/';
        $this->crearcarpeta($carpeta);
        if ($dir == 'factura') {
            $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $empresa_Id . '/' . $this->tipoDocumento;
            $this->crearcarpeta($carpeta);
            $filenameXml .=  $fileName . ".xml";
        }
        if ($dir == 'guiaRemision') {
            $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $empresa_Id . '/' . $this->tipoDocumento;
            $this->crearcarpeta($carpeta);
            $filenameXml .=  $fileName . ".xml";
        }
        if ($dir == 'comprobanteRetencion') {
            $carpeta = base_path() . '/storage/app/facturaelectronica/firmados/' . $empresa_Id . '/' . $this->tipoDocumento;
            $this->crearcarpeta($carpeta);
            $filenameXml .=  $fileName . ".xml";
        }
        if ($dataDoc != '') {
            $file = fopen($filenameXml, "w");
            fwrite($file, $dataDoc);
            fclose($file);
        }

        $dataDoc = $filenameXml;
        $dataDoc = escapeshellarg($dataDoc);
        $isPathXml = "1";
        $isExite = null;
        $vcontribuyente = $pin_token;
        $certificado_path = $ruta_token;
        $isExite = file_exists($certificado_path) ? true : false;
        $certificado = escapeshellarg($certificado_path);
        if (!$isExite) return null;
        $clave = $vcontribuyente;
        $clave = escapeshellarg($clave);
        $jar = base_path() . '/CompelJar/dist/CompelJar.jar';
        $jar = escapeshellarg($jar);
        $command = "java -jar $jar $certificado $clave $dataDoc $isPathXml 2>&1";
        $respuestaCmd = exec($command, $output, $return_value);
        $resultado = strpos($respuestaCmd, 'El certificado firmante ha caducado');
        if ($resultado !== FALSE) {
            echo 'El certificado firmante ha caducado: ' . $empresa_Id . ' para firmar: ' . $dir . ' - No.: ' . $id_factura . ' <br/>';
            $mns = 'El certificado firmante ha caducado: ' . $empresa_Id;
            if (file_exists($filenameXml)) unlink($filenameXml);
            $mensaje = $mns;
            return $mensaje;
        }
        $response = "";
        if ($return_value == -1) {
            $response = "";
            foreach ($output as $line)
                $response .= $line . "\n";
        } else {
            if (file_exists($filenameXml)) {
                $file = fopen($filenameXml, "r");
                $response = fread($file, filesize($filenameXml));
                fclose($file);
            }
        }
        if (file_exists($filenameXml))
            unlink($filenameXml);
        $response = trim($response);
        $pos = strpos($response, '<?xml');
        if ($pos !== false) {
            $response = substr($response, $pos);
        } else {
            $pos = strpos($response, '<html');
            $response = substr($response, $pos);
            if ($response == "") $response = $this->obtenerRespuestaXml(false, "", array("No se obtuvo respuesta de firmado"));
            $response = $response = $this->obtenerRespuestaXml(false, "", array($response));
        }
        $mensajes = array('0', $output);
        $error = '';
        $cadena = $response;
        $buscar = "El certificado firmante ha caducado:";
        $resultado = strpos($cadena, $buscar);
        if ($resultado !== FALSE) {
            echo '<pre>';
            print_r($response);
            exit;
        } else {
            $response = $this->procesarRespuestaXml($response);
            $archivo_firmado = $response['comprobante'];
            $mensajes = isset($response["mensajes"][0]) ? $response["mensajes"][0] : '';
            $xmlFirmado = simplexml_load_string($response['comprobante']);
            $r_ = base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/';
            $this->crearcarpeta($r_);
            $xmlFirmado = $xmlFirmado->saveXML($r_ . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' .  $docAnte->infoTributaria->ptoEmi . '-' . str_pad($docAnte->infoTributaria->secuencial, 9, 0, STR_PAD_LEFT) . '.xml');
        }
        return $archivo_firmado;
    }
    public function procesarRespuestaXml($xmlGenerado)
    {
        $compelXml = simplexml_load_string($xmlGenerado);
        //dd($compelXml);
        $isValido = false;
        $comprobante = "";
        $mensajes = array();
        $isValido = isset($compelXml->firmado) ? ($compelXml->firmado == "VALIDO" ? true : false) : false;
        if ($isValido) {
            $comprobanteRespuesta = (string)$compelXml->comprobante;
            $lineas = explode("\n", $comprobanteRespuesta);
            foreach ($lineas as $linea) {
                if (strlen(trim($linea)) > 0) $comprobante .= $linea . "\n";
            }
        }
        if (isset($compelXml->mensajes)) {
            if ($compelXml->mensajes) {
                if (isset($compelXml->mensajes->mensaje)) {
                    foreach ($compelXml->mensajes->mensaje as $mensaje) {
                        $mensaje = $this->htmlspecial($mensaje);
                        array_push($mensajes, "<br>- " . $mensaje);
                    }
                }
            }
        }
        $result = array();
        $result["isValido"] = $isValido;
        $result["mensajes"] = $mensajes;
        $result["comprobante"] = $comprobante;
        //dd($result);
        return $result;
    }
    public function htmlspecial($valor)
    {
        $result = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        return $result;
    }
    public function recibirWs($comprobante, $tipoAmbiente = 1)
    {
        //echo '<pre>';print_r($comprobante);exit;
        ini_set('max_execution_time', 300);
        $url = "";
        switch ($tipoAmbiente) {
            case 1:
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
                break;
            case 2:
                $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
                break;
        }
        $params = array("xml" => $comprobante);
        //echo '<pre>';print_r($params);exit;
        $client = new SoapClient($url);
        $result = $client->validarComprobante($params);
        //echo '<pre>';print_r($result);exit;
        if ($result) {
            if ($result->RespuestaRecepcionComprobante) {
                $result->isRecibida = $result->RespuestaRecepcionComprobante->estado === "RECIBIDA" ? true : false;
                if ($result->RespuestaRecepcionComprobante->comprobantes) {
                    if (isset($result->RespuestaRecepcionComprobante->comprobantes->comprobante)) {
                        $comprobantes = $result->RespuestaRecepcionComprobante->comprobantes->comprobante;
                        $result->RespuestaRecepcionComprobante->comprobantes = array();
                        if (is_array($comprobantes)) {
                            $result->RespuestaRecepcionComprobante->comprobantes = $comprobantes;
                        } else {
                            $result->RespuestaRecepcionComprobante->comprobantes[0] = $comprobantes;
                        }
                        $result->RespuestaRecepcionComprobante->mensajesWs = array();
                        $result->RespuestaRecepcionComprobante->mensajesDb = array();
                        for ($idxComprobante = 0; $idxComprobante < count($result->RespuestaRecepcionComprobante->comprobantes); $idxComprobante++) {
                            $comprobante = $result->RespuestaRecepcionComprobante->comprobantes[$idxComprobante];
                            if ($comprobante->mensajes) {
                                if (isset($comprobante->mensajes->mensaje)) {
                                    $mensajes = $comprobante->mensajes->mensaje;
                                    $comprobante->mensajes = array();
                                    if (is_array($mensajes)) {
                                        $comprobante->mensajes = $mensajes;
                                    } else {
                                        $comprobante->mensajes[0] = $mensajes;
                                    }
                                }
                                for ($idxMensaje = 0; $idxMensaje < count($comprobante->mensajes); $idxMensaje++) {
                                    $item = $comprobante->mensajes[$idxMensaje];
                                    $informacionAdicional = isset($item->informacionAdicional) ? $item->informacionAdicional : "";
                                    $estado_recibido = $result->RespuestaRecepcionComprobante->estado;
                                    $tipo = isset($item->tipo) ? $item->tipo : "";
                                    $identificador = isset($item->identificador) ? $item->identificador : "";
                                    $mensaje = $item->mensaje;
                                    $identificador = $item->identificador;
                                    $tipo = $item->tipo;
                                    $mensajeDB = trim("({$tipo}-{$identificador}) {$mensaje} {$informacionAdicional}");
                                    $mensajesWs = trim("({$tipo}-{$identificador}) {$mensaje} {$informacionAdicional}");
                                    array_push($result->RespuestaRecepcionComprobante->mensajesDb, $mensajeDB);
                                    array_push($result->RespuestaRecepcionComprobante->mensajesWs, $mensajesWs);
                                    $comprobante->mensajes[$idxMensaje] = (array)$comprobante->mensajes[$idxMensaje];
                                }
                            }
                            $result->RespuestaRecepcionComprobante->comprobantes[$idxComprobante] = (array)$result->RespuestaRecepcionComprobante->comprobantes[$idxComprobante];
                        }
                    }
                    $isRecibida = $result->isRecibida;
                    $result = (array)$result->RespuestaRecepcionComprobante;
                    $result["isRecibida"] = $isRecibida;
                }
            }
        }
        return $result;
    }
    public function generarXmlRespuesta($xmlRespuesta, $clave = '')
    {
        try {
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaRecepcioComprobante');
            $estado = $xmlRespuesta['estado'];
            $mensajes = [];
            $nombreArchivo = "noName";
            if ($estado == 'DEVUELTA') {
                $mensajes = $xmlRespuesta['mensajesWs'];
                $estado = $xml->createElement('estado', $xmlRespuesta['estado']);
                $root->appendChild($estado);
                $nodoComprobantes = $xml->createElement('comprobantes');
                $comprobantes = $xmlRespuesta['comprobantes'];
                //dd($comprobantes);
                foreach ($comprobantes as $comprobante) {
                    $nodoComprobante = $xml->createElement('comprobante');
                    $nodoComprobante->appendChild($xml->createElement('claveAcceso', $comprobante['claveAcceso']));
                    $nombreArchivo =  $comprobante['claveAcceso'];
                    $nodoMensajes = $xml->createElement('Mensajes');
                    $mensajes = $comprobante['mensajes'];
                    $nodoMensajes = $this->getMensajes($xml, $mensajes);
                    $nodoComprobante->appendChild($nodoMensajes);
                    $nodoComprobantes->appendChild($nodoComprobante);
                    $root->appendChild($nodoComprobantes);
                }
                $mensajesWs = $xmlRespuesta['mensajesWs'];
                $nodoMensajesWs = $xml->createElement('MensajesWs');
                foreach ($mensajesWs as $mensajeWs) {
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                }
                $mensajesDb = $xmlRespuesta['mensajesDb'];
                $nodoMensajesDb = $xml->createElement('MensajesDb');
                foreach ($mensajesDb as $mensajeDb) {
                    $nodoMensajesDb->appendChild($xml->createElement('mensajeDb',  $mensajeDb));
                    $root->appendChild($nodoMensajesDb);
                }
                $recibido = 'true';
                if ($xmlRespuesta['isRecibida'] == false)
                    $recibido = 'false';
                $recibida = $xml->createElement('isRecibida', $recibido);
                $root->appendChild($recibida);
                $xml->appendChild($root);
                $xml->formatOutput = true;
                $comprobante = $xml->saveXML();
                $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/recepcion/';
                $this->crearcarpeta($r_);
                $ruta = $r_ . $nombreArchivo . '.xml';
                $xml->save($ruta);
                //return $comprobante;
            } elseif ($estado == 'RECIBIDA') {
            } else {
                $mensajes = $xmlRespuesta['mensajesWs'];
                $estado = $xml->createElement('estado', $xmlRespuesta['estado']);
                $root->appendChild($estado);
                $nodoComprobantes = $xml->createElement('comprobantes');
                $comprobantes = $xmlRespuesta['comprobantes'];
                //dd($comprobantes);
                foreach ($comprobantes as $comprobante) {
                    $nodoComprobante = $xml->createElement('comprobante');
                    $nodoComprobante->appendChild($xml->createElement('claveAcceso', $clave));
                    $nombreArchivo =  $clave;
                    $nodoMensajes = $xml->createElement('Mensajes');
                    $mensajes = $comprobante['mensajes'];
                    $nodoMensajes = $this->getMensajes($xml, $mensajes);
                    $nodoComprobante->appendChild($nodoMensajes);
                    $nodoComprobantes->appendChild($nodoComprobante);
                    $root->appendChild($nodoComprobantes);
                }
                //dd($root);
                $mensajesWs = $xmlRespuesta['mensajesWs'];
                $nodoMensajesWs = $xml->createElement('MensajesWs');
                foreach ($mensajesWs as $mensajeWs) {
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                    $nodoMensajesWs->appendChild($xml->createElement('mensajeWs',  $mensajeWs));
                    $root->appendChild($nodoMensajesWs);
                }
                $mensajesDb = $xmlRespuesta['mensajesDb'];
                $nodoMensajesDb = $xml->createElement('MensajesDb');
                foreach ($mensajesDb as $mensajeDb) {
                    $nodoMensajesDb->appendChild($xml->createElement('mensajeDb',  $mensajeDb));
                    $root->appendChild($nodoMensajesDb);
                }
                $recibido = 'false';
                $recibida = $xml->createElement('isRecibida', $recibido);
                $root->appendChild($recibida);
                $xml->appendChild($root);
                $xml->formatOutput = true;
                $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/recepcion/';
                $this->crearcarpeta($r_);
                $ruta = $r_ . $nombreArchivo . '.xml';
                $comprobante = $xml->saveXML();
                $xml->save($ruta);
            }
        } catch (Exception $ex) {
            //throw $th;
        }
    }
    public function getMensajes($xmlDocument, $mensajes)
    {
        $nodoMensajes = $xmlDocument->createElement('mensajes');
        foreach ($mensajes as $mensaje) {
            $nodoMensaje = $xmlDocument->createElement('mensaje');
            $nodoMensaje->appendChild($xmlDocument->createElement('dentificador',  $mensaje['identificador']));
            $nodoMensaje->appendChild($xmlDocument->createElement('mensaje',  $mensaje['mensaje']));
            if (isset($mensaje['informacionAdicional'])) {
                $nodoMensaje->appendChild($xmlDocument->createElement('informacionAdicional',  $mensaje['informacionAdicional']));
            }

            $nodoMensaje->appendChild($xmlDocument->createElement('tipo',  $mensaje['tipo']));
            $nodoMensajes->appendChild($nodoMensaje);
        }
        return $nodoMensajes;
    }
    public function autorizacion_sri($claveAcceso)
    {
        ini_set('max_execution_time', 300);
        sleep(3);
        $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
        $client = new SoapClient($url);
        $param = array(
            'claveAccesoComprobante' => $claveAcceso
        );
        return $client->autorizacionComprobante($param);
    }
    public function generarXmlAutorizacion($xmlRespuesta)
    {
        $nombreArchivo = "noName";
        $estado = "sin procesar";
        try {
            //dd($xmlRespuesta);
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaAutorizacionComprobante');
            $clave = $xml->createElement('claveAccesoConsultada', $xmlRespuesta->RespuestaAutorizacionComprobante->claveAccesoConsultada);
            $root->appendChild($clave);
            $nombreArchivo = $xmlRespuesta->RespuestaAutorizacionComprobante->claveAccesoConsultada;
            $numeroComprobante = $xml->createElement('numeroComprobantes', $xmlRespuesta->RespuestaAutorizacionComprobante->numeroComprobantes);
            $root->appendChild($numeroComprobante);

            $nodoAutorizaciones = $xml->createElement('autorizaciones');
            $autorizaciones = $xmlRespuesta->RespuestaAutorizacionComprobante->autorizaciones;
            foreach ($autorizaciones as $autorizacion) {
                $nodoAutorizacion = $xml->createElement('autorizacion');
                $estado = $autorizacion->estado;
                $nodoAutorizacion->appendChild($xml->createElement('estado', $autorizacion->estado));
                $nodoAutorizacion->appendChild($xml->createElement('fechaAutorizacion', $autorizacion->fechaAutorizacion));
                $nodoAutorizacion->appendChild($xml->createElement('ambiente', $autorizacion->ambiente));
                $nodoAutorizacion->appendChild($xml->createElement('comprobante', $autorizacion->comprobante));
                if (is_null($autorizacion->mensajes)) {
                    $nodoMensajes = $this->getMensajesAutorizacion($xml, $autorizacion->mensajes);
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                } else {
                    $nodoMensajes = $xml->createElement('mensajes');
                    $nodoAutorizacion->appendChild(($nodoMensajes));
                }
                $nodoAutorizaciones->appendChild($nodoAutorizacion);
            }
            $root->appendChild($nodoAutorizaciones);
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $comprobante = $xml->saveXML();
            $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $nombreArchivo . '_' . $estado . '.xml';
            $xml->save($ruta);
            //dd($comprobante);
            return $comprobante;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
            return;
        }
    }
    public function getMensajesAutorizacion($xmlDocument, $mensajes)
    {
        $nodoMensajes = $xmlDocument->createElement('mensajes');

        foreach ($mensajes as $mensaje) {
            $nodoMensaje = $xmlDocument->createElement('mensaje');
            $nodoMensaje->appendChild($xmlDocument->createElement('Identificador',  $mensaje->identificador));
            $nodoMensaje->appendChild($xmlDocument->createElement('mensaje',  $mensaje->mensaje));
            if (isset($mensaje->informacionAdicional)) {
                $nodoMensaje->appendChild($xmlDocument->createElement('informacionAdicional',  $mensaje->informacionAdicional));
            }
            $nodoMensaje->appendChild($xmlDocument->createElement('tipo',  $mensaje->tipo));
            $nodoMensajes->appendChild($nodoMensaje);
        }
        return $nodoMensajes;
    }
    public function generarPdf($xml, $accion = '')
    {
        $clave = $xml;
        $xml = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.xml';
        if (file_exists($xml)) {
            $data['xml'] = $xml;
            $view     = \View::make('sri_electronico.documentosgenerados.view_guiaremisionpdf', $data)->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');
            if ($accion == '')
                $pdf->save(base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.pdf');
            //return $pdf->stream($clave . '.pdf');
            else
                return $pdf->download($clave . '_AUTORIZADO.pdf');
        } else {
            echo 'The file does not exist.';
        }
    }
    public function descargarXML($clave)
    {
        if (!empty($clave)) {
            $xml = '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.xml';
            $fileName = basename($xml);
            $filePath =  base_path() . $xml;
            if (!empty($fileName) && file_exists($filePath)) {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$fileName");
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                readfile($filePath);
                exit;
            } else {
                echo 'The file does not exist.';
            }
        } else {
            die("Invalid file name!");
        }
    }
    public function enviar_correo($param)
    {
        $nombre = $param['nombre'];
        $apellido = $param['apellido'];
        $email = $param['email'];
        $valorPagar = $param['valorPagar'];
        $proveedor = '';
        $num = $param['estab'] . '-' . $param['ptoEmi'] . '-' . $param['secuencial'];
        $param['num'] = $num;
        $tipoDoc = '';
        if ($param['tipoDoc'] == '01')
            $tipoDoc = 'Factura';
        if ($param['tipoDoc'] == '03')
            $tipoDoc = 'Liquidaci??n de compra';
        if ($param['tipoDoc'] == '04')
            $tipoDoc = 'Nota de cr??dito';
        if ($param['tipoDoc'] == '05')
            $tipoDoc = 'Nota de d??bito';
        if ($param['tipoDoc'] == '06')
            $tipoDoc = 'Gu??a de remis??n';
        if ($param['tipoDoc'] == '07')
            $tipoDoc = 'Comprobante de retenci??n';
        $param['tipoDoc'] = $tipoDoc;
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = str_replace('/', '-', $param['fechaEmision']);
        $fechaEmision = date('d', strtotime($mes)) . ' de ' . $meses[date('n', strtotime($mes)) - 1] . ' ' . date('Y', strtotime($mes));
        $mes = $meses[date('n', strtotime($mes)) - 1];
        $param['mes'] = $mes;
        $param['fechaEmision'] = $fechaEmision;
        try {
            $archivoXml = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $param['claveAcceso'] . '_AUTORIZADO.xml';
            $archivoPdf = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $param['claveAcceso'] . '_AUTORIZADO.pdf';
            $txtAsunto = $tipoDoc . ' No.: ' . $param['estab'] . '-' . $param['ptoEmi'] . '-' . $param['secuencial'] . ' || ' . ucwords(strtolower($nombre . ' ' . $apellido));
            Mail::send('sri_electronico.view_plantilla_email', $param, function ($msj) use ($txtAsunto, $email, $archivoXml, $archivoPdf) {
                $msj->subject($txtAsunto);
                $msj->to($email);
                $msj->from("no-reply@mdconsgroup.com", "Documento Electr??nico - MdConsgroup");
                $msj->attach($archivoXml);
                $msj->attach($archivoPdf);
                $msj->bcc('alexo8ec@hotmail.com');
            });
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getInfoFactura($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoFactura');

        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate($campos['infoFactura']["fecha_emision"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoFactura']["dirEstablecimiento"]));
        if ($campos['infoFactura']["contribuyenteEspecial"]) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoFactura']["contribuyenteEspecial"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoFactura']["obligadoContabilidad"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoFactura']["tipoIdentificacionComprador"]));
        if ($campos['infoFactura']["guiaRemision"]) $nodoDetalle->appendChild($xmlDocument->createElement('guiaRemision', $campos['infoFactura']["guiaRemision"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoFactura']["razonSocialComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoFactura']["identificacionComprador"]));
        if ($campos['infoFactura']["direccionComprador"]) $nodoDetalle->appendChild($xmlDocument->createElement('direccionComprador', $campos['infoFactura']["direccionComprador"]));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos['infoFactura']["totalSinImpuestos"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos['infoFactura']["totalDescuento"])));

        $totalConImpuestos = $campos['infoFactura']["totalConImpuestos"];
        $nodoDetalle->appendChild($xmlDocument->createElement('totalConImpuestos', $this->totalConImpuestos($xmlDocument, $totalConImpuestos)));

        $nodoDetalle->appendChild($xmlDocument->createElement('propina', $this->getDecimal($campos['infoFactura']["propina"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos['infoFactura']["importeTotal"])));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        if ($campos['infoFactura']["moneda"]) $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $campos['infoFactura']["moneda"]));

        $pagos = $campos['infoFactura']["pagos"];
        $nodoDetalle->appendChild($xmlDocument->createElement('Pagos', $this->formaPago($xmlDocument, $pagos)));

        if ($campos['infoFactura']["valorRetIva"]) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetIva', $campos['infoFactura']["valorRetIva"]));
        if ($campos['infoFactura']["valorRetRenta"]) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetRenta', $campos['infoFactura']["valorRetRenta"]));
        return $nodoDetalle;
    }
    public function totalConImpuestos($xmlDocument, $totalConImpuestos)
    {
        $nodoTotalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        foreach ($totalConImpuestos as $totalImpuesto) {
            $nodoTotalImpuesto = $xmlDocument->createElement('totalImpuesto');
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('formaPago', $totalImpuesto["formaPago"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('total', $this->getDecimal($totalImpuesto["total"])));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('plazo', $totalImpuesto["plazo"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('unidadTiempo', $totalImpuesto["unidadTiempo"]));
            $nodoTotalConImpuestos->appendChild($xmlDocument->createElement('totalImpuesto', $nodoTotalImpuesto));
        }
        return $nodoTotalConImpuestos;
    }
    public function formaPago($xmlDocument, $pagos)
    {
        $nodoPagos = $xmlDocument->createElement('Pagos');
        foreach ($pagos as $pago) {
            $nodoPago = $xmlDocument->createElement('pago');
            $nodoPago->appendChild($xmlDocument->createElement('formaPago', $pago["formaPago"]));
            $nodoPago->appendChild($xmlDocument->createElement('total', $this->getDecimal($pago["total"])));
            $nodoPago->appendChild($xmlDocument->createElement('plazo', $pago["plazo"]));
            $nodoPago->appendChild($xmlDocument->createElement('unidadTiempo', $pago["unidadTiempo"]));
            $nodoPagos->appendChild($xmlDocument->createElement('pago', $nodoPago));
        }
        return $nodoPagos;
    }
    public function consultarEstadoSRI($idEmpresa)
    {
        $url = '';
        $infoTributaria = De_Empresa::where('id_empresa', $idEmpresa)->first();
        $tipoAmbiente = $infoTributaria->ambiente;
        $estadoSri = 1;
        switch ($tipoAmbiente) {
            case 1:
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'; //'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl';
                break;
            case 2:
                $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'; //'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl';
                break;
        }
        $emision = new EmisionDocumentosController;
        $estadoSri = $emision->pingDomain($url);
        $estado = De_Estado_Sri::getEstado();
        if ($estadoSri != $estado) {
            De_Estado_Sri::updateEstado($estadoSri);
        }
        return $estadoSri;
    }
    public static function ValidarFuera($dataDoc, $tipoDocumento)
    {
        $fields = array('accion' => 'validar', 'dataDoc' => $dataDoc, 'tipoDocumento' => $tipoDocumento);
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pruebas.aitrol.com/conectorJava.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public static function firmarFuera($comprobante, $tipoDocumento, $ruta_p12, $password)
    {
        $array = ['comprobante' => $comprobante, 'tipoDocumento' => $tipoDocumento, 'ruta_p12' => $ruta_p12, 'password' => $password];
        /*$fields = array('accion' => 'firmar', 'dataDoc' => $comprobante, 'tipoDocumento' => $tipoDocumento, 'ruta_p12' => $ruta_p12, 'password' => $password);
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://pruebas.aitrol.com/conectorJava.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;*/
        require_once('../conectorJava.php');
        $result = generarXmlSignJar(json_encode($array));
        return $result;

        /*$result = generarXmlSignJar($comprobante, $tipoDocumento, $ruta_p12, $password);
        echo '<pre>';
        print_r($result);
        exit;*/
    }
}
