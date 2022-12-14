<?php

namespace Sis_medico\Http\Controllers;

use DOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
use Sis_medico\De_Estado_Sri;
use Mail;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Detalle_Credito_Clientes;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Detalle_Rubro_Credito;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Forma_Pago;
use Sis_medico\Ct_Nota_Credito;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_Nota_Credito_Detalle;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_ventas;
use Sis_medico\Log_Api;
use Sis_medico\Paciente;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use stdClass;

class EmisionDocumentosController extends Controller
{
    public $errorGeneral = '';
    private $tipoDocumento = null;
    public function index(Request $req)
    {
        if ($req->opcion == '') {
            if (isset($req->idUsuario)) {
                $this->getGuiaRemision(null, $req->idUsuario);
                //$this->getNotaCredito(null, $req->idUsuario);
                //$this->getFactura(null, $req->idUsuario);
                $this->getRetencion(null, $req->idUsuario);
                // $this->getNotaDebito(null, $req->idUsuario);
                // $this->getLiquidacion(null, $req->idUsuario);
            } else {
                $this->getGuiaRemision(null);
                //$this->getNotaCredito();
                //$this->getFactura(null);
                //$this->getRetencion(null);
                // $this->getNotaDebito();
                // $this->getLiquidacion();
            }
        } elseif ($req->opcion == 'leerxml') {
            $this->obtenercomprobanteSRI('2508202206092272958700110010010000000200000002018');
        } elseif ($req->opcion == 'barcode') {
            $this->setBarcode('2408202206092272958700110010010000000160000001619', '');
        } elseif ($req->opcion == 'generarPdf') {
            return  $this->generarPdf($req->clave, $req->clave, 'comprobanteRetencion');
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
    public function getGuiaRemision($dinfo = null, $idUsuario = 'FACELECTRO')
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
                    $dds = !$this->consultarEstadoSRI($guiaCabecera[0]->id_empresa);
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
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
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
                                if ($datosFirma->agente_retencion == 1) {
                                    $infoTributaria['agenteRetencion'] = 201;
                                }
                                $data['infoTributaria'] = $infoTributaria;

                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                $caja = Ct_Caja::where('id_sucursal', $sucursal->id)
                                    ->where('codigo_caja', $infoTributaria['ptoEmi'])
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
                                    if ($datosFirma->contabilidad == 1)
                                        $infoGuiaRemision['obligadoContabilidad'] = 'SI'; //Opcional
                                    if ($datosFirma->contribuyente_especial != 'NO')
                                        $infoGuiaRemision['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                    $infoGuiaRemision['fechaIniTransporte'] =  str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_ini)));
                                    $infoGuiaRemision['fechaFinTransporte'] =  str_replace('/', '-', date('d/m/Y', strtotime($row->fecha_fin)));
                                    $infoGuiaRemision['placa'] = $row->placa;
                                } else {
                                    $infoGuiaRemision['razonSocialTransportista'] = null;
                                    $infoGuiaRemision['tipoIdentificacionTransportista'] = null;
                                    $infoGuiaRemision['rucTransportista'] = null;
                                    $infoGuiaRemision['rise'] = null;
                                    $infoGuiaRemision['obligadoContabilidad'] = null; //Opcional
                                    $infoGuiaRemision['contribuyenteEspecial'] = null; //Opcional
                                }
                                $data['infoGuiaRemision'] = $infoGuiaRemision;
                                $destinatario['identificacionDestinatario'] = $row->ci_destinatario;
                                $destinatario['razonSocialDestinatario'] =  $row->razon_social_destinatario;
                                $destinatario['dirDestinatario'] = $row->direccion_destinatario;
                                $destinatario['motivoTraslado'] = $row->motivo_traslado_destinatario;
                                $destinatario['docAduaneroUnico'] = null;
                                $destinatario['codEstabDestino'] = $row->codigo_est_destino != '' ? $row->codigo_est_destino : null;
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
                                        $detalle['descripcion'] = Ct_productos::getNombreProducto($detaGuia->cod_principal);
                                        $detalle['cantidad'] = $detaGuia->cantidad;
                                        $contAdicional = 0;
                                        if ($detaGuia->observacion != '') {
                                            $detAdicional = [
                                                'nombre' => 'detalle1',
                                                'valor' => $detaGuia->observacion
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaGuia->descripcion != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'detalle2',
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
                                $informacion_adicional[0] = $campoAdicional;
                                $campoAdicional['nombre'] = "telefono";
                                $campoAdicional['valor'] = $telefonoTransportista;
                                $informacion_adicional[1] = $campoAdicional;
                                $infoAdicional['campoAdicional'] = $informacion_adicional;
                                $destinatarios['destinatario'] = $destinatario;
                                $data['destinatarios'] = $destinatarios;
                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
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
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    Ct_Guia_Remision_Cabecera::updateGenerarXML($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $data['infoTributaria']['claveAcceso']);
                                    if (!empty($comprobante)) {
                                        $docAnte = simplexml_load_string($comprobante);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'Error al validar XML con XSD');
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_Guia_Remision_Cabecera::updateValidacionXSD($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            //$ruta_p12 = $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
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
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_Guia_Remision_Cabecera::updateRecibidoSri($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_Guia_Remision_Cabecera::updateXmlAutorizacion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
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
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_Guia_Remision_Cabecera::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_documento' => $row->id,
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                    }
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
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
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
                if ($claveInsertados != '') {
                    $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                }
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
    public function getFactura($dinfo = null, $idUsuario = 'FACELECTRO')
    {
        $data = [];
        $id_doc = 0;
        $numDocumento = '';
        $respuestaNo = new stdClass();
        try {
            if ($dinfo == null) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $facturaCabecera = Ct_ventas::getVentasCabecera();
                if (count($facturaCabecera) > 0) {
                    if (!$this->consultarEstadoSRI($facturaCabecera[0]->id_empresa)) {
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
                        foreach ($facturaCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = 1;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '01';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $datosTributarios->secuencial_nro + 1;
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
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
                                //Info Factura
                                $infoFactura['fechaEmision'] = $row->fecha;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                if (isset($sucursal->direccion_sucursal)) {
                                    $infoFactura['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                                } else {
                                    $infoFactura['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $telefonoCliente = '';
                                if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                                    $infoFactura['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                }
                                $infoFactura['obligadoContabilidad'] = 'NO'; //Opcional
                                if ($datosFirma->contabilidad == 1)
                                    $infoFactura['obligadoContabilidad'] = 'SI'; //Opcional
                                $cliente = Ct_Clientes::getCliente($row->ruc_id_cliente);
                                if ($cliente != '') {
                                    $email = $cliente->email_representante;
                                    $telefonoCliente = $cliente->telefono1;
                                    $infoFactura['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                                    $infoFactura['identificacionComprador'] = $cliente->identificacion;
                                    $infoFactura['razonSocialComprador'] = $cliente->nombre;
                                    $infoFactura['direccionComprador'] = $cliente->direccion_representante;
                                } else {
                                    $infoFactura['tipoIdentificacionComprador'] = null;
                                    $infoFactura['identificacionComprador'] = null;
                                    $infoFactura['razonSocialComprador'] = null;
                                    $infoFactura['direccionComprador'] = null; //Opcional
                                }
                                if ($row->guiaRemision != '')
                                    $infoFactura['guiaRemision'] = $row->guiaRemision;
                                $infoFactura['totalSinImpuestos'] = $row->subtotal_0 + $row->subtotal_12;
                                $infoFactura['totalConImpuestos'] = $row->total_final;
                                $totalConImpuestos = [];
                                $totalConImpuesto = [];
                                $conImpuesto = 0;
                                $impuestos = Ct_detalle_venta::getDetalles($row->id);
                                if (count($impuestos) > 0) {
                                    foreach ($impuestos as $impuesto) {
                                        if ($impuesto->check_iva == 1)
                                            $codigoImpuesto = De_Codigo_Impuestos::where('id', 1)->first();
                                        else
                                            $codigoImpuesto = De_Codigo_Impuestos::where('id', 2)->first();
                                        $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                        $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                        $totalConImpuesto['descuentoAdicional'] = $impuesto->descuento;
                                        $totalConImpuesto['baseImponible'] = ($impuesto->cantidad * $impuesto->precio) - $impuesto->descuento;
                                        if ($impuesto->check_iva == 1)
                                            $totalConImpuesto['valor'] = $this->getDecimal(($impuesto->precio * $impuesto->cantidad) * $impuesto->porcentaje);
                                        else
                                            $totalConImpuesto['valor'] = $this->getDecimal(0);
                                        array_push($totalConImpuestos, $totalConImpuesto);
                                        $conImpuesto++;
                                    }
                                }
                                $infoFactura['totalConImpuestos'] = $totalConImpuestos;
                                $infoFactura['propina'] = $this->getDecimal(0);
                                $infoFactura['importeTotal'] = $row->total_final;
                                $infoFactura['moneda'] = Ct_Divisas::getMoneda();
                                $detallePagos = Ct_detalle_venta::getDetalles($row->id);
                                //Formas de pago
                                $pagos = [];
                                $pago = [];
                                $conPagos = 0;
                                $formasPagos = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
                                    ->where('id_ct_ventas', $row->id)->get([
                                        't.codigo'
                                    ]);
                                if (count($formasPagos) > 0) {
                                    foreach ($formasPagos as $formaPago) {
                                        $pago['formaPago'] = str_pad($formaPago->codigo, 2, 0, STR_PAD_LEFT);
                                        $pago['total'] = $row->total_final;
                                        $pago['plazo'] = $row->dias_plazo;
                                        $pago['unidadTiempo'] = 'dias';
                                        array_push($pagos, $pago);
                                    }
                                }
                                $infoFactura['pagos'] = $pagos;
                                $data['infoFactura'] = $infoFactura;
                                //detalles
                                $detalle = [];
                                $detalles = [];
                                $conDetalle = 0;
                                $detalleFactura = Ct_detalle_venta::getDetalles($row->id);
                                if (count($detalleFactura) > 0) {
                                    foreach ($detalleFactura as $detaFactura) {
                                        $detalle['p_impuesto'] = $detaFactura->check_iva !== null ? 12 : 0;
                                        $detalle['codigoPrincipal'] = $detaFactura->id_ct_productos;
                                        $detalle['codigoAuxiliar'] = $detaFactura->id_ct_productos;
                                        $detalle['descripcion'] = $detaFactura->nombre;
                                        $detalle['cantidad'] = $detaFactura->cantidad;
                                        $detalle['precioUnitario'] = $detaFactura->precio;
                                        $detalle['descuento'] = $detaFactura->descuento;
                                        $detalle['precioTotalSinImpuesto'] = $row->subtotal_0 + $row->subtotal_12;
                                        $contAdicional = 0;
                                        if ($detaFactura->detalle != '') {
                                            $detAdicional = [
                                                'nombre' => 'detalle',
                                                'valor' => $detaFactura->detalle
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($detaFactura->adicional != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'adicional',
                                                'valor' => $detaFactura->adicional
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($contAdicional > 0) {
                                            $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                            $detalle['detallesAdicionales'] = $detallesAdicionales;
                                        }
                                        $detalles[$conDetalle] = $detalle;
                                        $conDetalle++;
                                    }
                                }
                                $data['detalles'] = $detalles;

                                $cont = 0;
                                if (isset($row->nota_electronica) && $row->nota_electronica != '') {
                                    $campoAdicional['nombre'] = "detalle";
                                    $campoAdicional['valor'] =  $row->nota_electronica;
                                    $informacion_adicional[$cont]['campoAdicional'] = $campoAdicional;
                                    $cont++;
                                }
                                if (isset($row->email_cliente) && $row->email_cliente != '') {
                                    $campoAdicional['nombre'] = "email";
                                    $campoAdicional['valor']  =  $row->email_cliente;
                                    $informacion_adicional[$cont]['campoAdicional'] = $campoAdicional;
                                    $cont++;
                                }
                                if (isset($row->telefono_cliente) && $row->telefono_cliente != '') {
                                    $campoAdicional['nombre'] = "telefono";
                                    $campoAdicional['valor'] =  $row->telefono_cliente;
                                    $informacion_adicional[$cont]['campoAdicional'] = $campoAdicional;
                                } elseif (isset($row->direccion_cliente) && $row->direccion_cliente != '') {
                                    $campoAdicional['nombre'] = "direccion";
                                    $campoAdicional['valor'] =  $row->direccion_cliente;
                                    $informacion_adicional[$cont]['campoAdicional'] = $campoAdicional;
                                }

                                $infoAdicional['campoAdicional'] = $informacion_adicional;

                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoTributaria['identificacionComprador'],
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
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'detalles' => json_encode($data['detalles']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
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
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    Ct_ventas::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    if (!empty($comprobante)) {
                                        $docAnte = simplexml_load_string($comprobante);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir');
                                                echo json_encode($result);
                                                return;
                                            }

                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_ventas::updateValidacionXSD($row->id);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if (!empty($result)) {
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_ventas::updateXmlFirmado($row->id);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_ventas::updateRecibidoSri($row->id);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                    Ct_ventas::updateXmlAutorizacion($row->id);
                                                    $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                    $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                    $param = [
                                                        'email' => $row->email_traslado_destinatario,
                                                        'nombre' => $row->razon_social_destinatario,
                                                        'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                        'estab' => $data['infoTributaria']['estab'],
                                                        'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                        'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                        'tipoDoc' => $this->tipoDocumento
                                                    ];
                                                    $this->enviar_correo($param);
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_ventas::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
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
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
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
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                if (!$this->consultarEstadoSRI(session('id_empresa'))) {
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
                    return json_encode($result);
                } else {
                    $cont = 0;
                    $empresa = Empresa::getEmpresa($dinfo['empresa']);
                    if ($empresa != '') {
                        $datosFirma = De_Empresa::getDatos($dinfo['empresa']);
                        $venta = Ct_ventas::where('id', $dinfo['idVenta'])->first();
                        $firma['ruta_firma'] = $datosFirma->ruta_firma;
                        $firma['clave_firma'] = $datosFirma->clave_firma;
                        $data['datosfirma'] = $firma;
                        $infoTributaria['fecha_emision'] = date('d/m/Y');
                        $infoTributaria['ambiente'] = 1;
                        $infoTributaria['tipoEmision'] = 1;
                        $infoTributaria['razonSocial'] = $empresa->razonsocial;
                        $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                        $infoTributaria['ruc'] = $empresa->id;
                        $infoTributaria['codDoc'] = '01';
                        $infoTributaria['estab'] = str_pad($venta->sucursal, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['ptoEmi'] = str_pad($venta->punto_emision, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['dirMatriz'] = $empresa->direccion;
                        $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                        $datosTributarios = De_Info_Tributaria::getDatos(session('id_empresa'), $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                        if ($datosTributarios == '') {
                            $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                            echo json_encode($result);
                            return;
                        }
                        $secuencial = $datosTributarios->secuencial_nro + 1;
                        $infoTributaria['ambiente'] = $datosFirma->ambiente;
                        $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                        $claveObj = new ClaveAcceso();
                        $campos = [
                            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($venta->fecha))),
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
                        //InfoTributaria
                        $infoFactura['fechaEmision'] = $venta->fecha;
                        $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                            ->where('codigo_sucursal', $infoTributaria['estab'])
                            ->first();
                        if (isset($sucursal->direccion_sucursal)) {
                            $infoFactura['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                        } else {
                            $infoFactura['dirEstablecimiento'] = $empresa->direccion;
                        }
                        if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                            $infoFactura['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                        }
                        $infoFactura['obligadoContabilidad'] = 'NO'; //Opcional
                        if ($datosFirma->contabilidad == 1)
                            $infoFactura['obligadoContabilidad'] = 'SI'; //Opcional
                        $cliente = Ct_Clientes::getCliente($venta->id_cliente);
                        if ($cliente != '') {
                            $email = $cliente->email_representante;
                            $telefonoCliente = $cliente->telefono1;
                            $infoFactura['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                            $infoFactura['identificacionComprador'] = $cliente->identificacion;
                            $infoFactura['razonSocialComprador'] = $cliente->nombre;
                            $infoFactura['direccionComprador'] = $cliente->direccion_representante;
                        } else {
                            $infoFactura['tipoIdentificacionComprador'] = null;
                            $infoFactura['identificacionComprador'] = null;
                            $infoFactura['razonSocialComprador'] = null;
                            $infoFactura['direccionComprador'] = null; //Opcional
                        }
                        $infoFactura['totalSinImpuestos'] = $venta->subtotal_0 + $venta->subtotal_12;
                        $infoFactura['totalConImpuestos'] = $venta->total_final;
                        $totalConImpuestos = [];
                        $totalConImpuesto = [];
                        $conImpuesto = 0;
                        $impuestos = Ct_detalle_venta::getDetalles($venta->id);
                        if (count($impuestos) > 0) {
                            foreach ($impuestos as $impuesto) {
                                if ($impuesto->check_iva == 1)
                                    $codigoImpuesto = De_Codigo_Impuestos::where('id', 1)->first();
                                else
                                    $codigoImpuesto = De_Codigo_Impuestos::where('id', 2)->first();
                                $totalConImpuesto['codigo'] = $codigoImpuesto->codigo_impuesto;
                                $totalConImpuesto['codigoPorcentaje'] = $codigoImpuesto->codigo;
                                $totalConImpuesto['descuentoAdicional'] = $impuesto->descuento;
                                $totalConImpuesto['baseImponible'] = ($impuesto->cantidad * $impuesto->precio) - $impuesto->descuento;
                                if ($impuesto->check_iva == 1)
                                    $totalConImpuesto['valor'] = $this->getDecimal(($impuesto->precio * $impuesto->cantidad) * $impuesto->porcentaje);
                                else
                                    $totalConImpuesto['valor'] = $this->getDecimal(0);
                                array_push($totalConImpuestos, $totalConImpuesto);
                                $conImpuesto++;
                            }
                        }
                        $infoFactura['totalConImpuestos'] = $totalConImpuestos;
                        $infoFactura['propina'] = $this->getDecimal(0);
                        $infoFactura['importeTotal'] = $venta->total_final;
                        $infoFactura['moneda'] = Ct_Divisas::getMoneda();
                        $detallePagos = Ct_detalle_venta::getDetalles($venta->id);
                        //Formas de pago
                        $pagos = [];
                        $pago = [];
                        $conPagos = 0;
                        $formasPagos = Ct_Forma_Pago::join('ct_tipo_pago as t', 'ct_forma_pago.tipo', '=', 't.id')
                            ->where('id_ct_ventas', $venta->id)->get([
                                't.codigo'
                            ]);
                        if (count($formasPagos) > 0) {
                            foreach ($formasPagos as $formaPago) {
                                $pago['formaPago'] = str_pad($formaPago->codigo, 2, 0, STR_PAD_LEFT);
                                $pago['total'] = $venta->total_final;
                                $pago['plazo'] = $venta->dias_plazo;
                                $pago['unidadTiempo'] = 'dias';
                                array_push($pagos, $pago);
                            }
                        }
                        $infoFactura['pagos'] = $pagos;
                        $data['infoFactura'] = $infoFactura;
                        //detalles
                        $detalle = [];
                        $detalles = [];
                        $conDetalle = 0;
                        $detalleFactura = Ct_detalle_venta::getDetalles($venta->id);
                        if (count($detalleFactura) > 0) {
                            foreach ($detalleFactura as $detaFactura) {
                                $detalle['p_impuesto'] = $detaFactura->check_iva !== null ? 12 : 0;
                                $detalle['codigoPrincipal'] = $detaFactura->id_ct_productos;
                                $detalle['codigoAuxiliar'] = $detaFactura->id_ct_productos;
                                $detalle['descripcion'] = $detaFactura->nombre;
                                $detalle['cantidad'] = $detaFactura->cantidad;
                                $detalle['precioUnitario'] = $detaFactura->precio;
                                $detalle['descuento'] = $detaFactura->descuento;
                                $detalle['precioTotalSinImpuesto'] = $venta->subtotal_0 + $venta->subtotal_12;
                                $contAdicional = 0;
                                if ($detaFactura->detalle != '') {
                                    $detAdicional = [
                                        'nombre' => 'detalle',
                                        'valor' => $detaFactura->detalle
                                    ];
                                    $contAdicional++;
                                }
                                /*if ($detaFactura->adicional != '') {
                                    $detAdicional_ = [
                                        'nombre' => 'adicional',
                                        'valor' => $detaFactura->adicional
                                    ];
                                    $contAdicional++;
                                }*/
                                if ($contAdicional > 0) {
                                    $detallesAdicionales = [isset($detAdicional) ? $detAdicional : []];
                                    $detalle['detallesAdicionales'] = $detallesAdicionales;
                                }
                                $detalles[$conDetalle] = $detalle;
                                $conDetalle++;
                            }
                        }
                        $data['detalles'] = $detalles;
                        $campoAdicional['nombre'] = "detalle";
                        $campoAdicional['valor']  = $venta->nota_electronica;
                        $informacion_adicional[0] = $campoAdicional;
                        $infoAdicional['campoAdicional'] = $informacion_adicional;
                        $data['infoAdicional'] = $informacion_adicional;
                        $errores = $this->validarData($data);
                        if (count($errores) > 0) {
                            $arrayDocElec = [
                                'id_de_pasos' => 7,
                                'infoTributaria' => json_encode($data['infoTributaria']),
                                'infoFactura' => json_encode($data['infoFactura']),
                                'infoAdicional' => json_encode($data['infoAdicional']),
                                'establecimiento' => $infoTributaria['estab'],
                                'emision' => $infoTributaria['ptoEmi'],
                                'secuencial' => $infoTributaria['secuencial'],
                                'ruc_receptor' => $infoTributaria['identificacionComprador'],
                                'ruc_emisor' => $infoTributaria['ruc'],
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                'id_documento' => $venta->id,
                            ];
                            $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayDocElec, 7);
                            Ct_ventas::updateSinGenerarXML($venta->id);
                            $idInsertados .= $id_doc . ',';
                            $arrayLog = [
                                'id_de_documentos_electronicos' => $id_doc,
                                'descripcion_error' => json_encode($errores),
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'id_documento' => $venta->id
                            ];
                            De_Log_Error::setErrorLog($arrayLog);
                            $infoErrores = De_Documentos_Electronicos::revisionEstadosDocumentosErrores($idInsertados);
                            $infoAutorizados = '';
                            if ($claveInsertados != '')
                                $infoAutorizados = De_Documentos_Electronicos::revisionEstadosDocumentosAutorizados($claveInsertados);
                            $result = array('code' => 200, 'state' => true, 'data' => ['errores:' => $infoErrores, 'autorizado:' => $infoAutorizados], 'message' => 'proceso ejecutado correctamente');
                            return json_encode($result);
                        } else {
                            //Creacion XML
                            $arrayLogError = [
                                'id_de_pasos' => 1,
                                'infoTributaria' => json_encode($data['infoTributaria']),
                                'infoFactura' => json_encode($data['infoFactura']),
                                'detalles' => json_encode($data['detalles']),
                                'infoAdicional' => json_encode($data['infoAdicional']),
                                'establecimiento' => $infoTributaria['estab'],
                                'emision' => $infoTributaria['ptoEmi'],
                                'secuencial' => $infoTributaria['secuencial'],
                                'ruc_receptor' =>  $infoFactura['identificacionComprador'],
                                'ruc_emisor' => $infoTributaria['ruc'],
                                'id_usuariomod' => $idUsuario,
                                'id_usuariocrea' =>  $idUsuario,
                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                                'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                                'id_documento' => $venta->id
                            ];
                            $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                            if (is_numeric($id_doc)) {
                                $idInsertados .= $id_doc . ',';
                                $comprobante = $this->generarDocElectronicoXml($data);
                                Ct_ventas::updateGenerarXML($venta->id, $data['infoTributaria']['claveAcceso']);
                                if (!empty($comprobante)) {
                                    $docAnte = simplexml_load_string($comprobante);
                                    if (!empty($docAnte)) {
                                        //validar xsd
                                        $validacion = $this->validarXmlToXsd($comprobante);
                                        if ($validacion['isValidoXsd'] != 1) {
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($validacion),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
                                            $result = array('code' => 500, 'state' => true, 'data' => json_encode($validacion), 'message' => 'Error: al validar documento xml.');
                                            return json_encode($result);
                                        }
                                        De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                        Ct_ventas::updateValidacionXSD($venta->id);
                                        //firmar xml
                                        $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                        $password = $datosFirma->clave_firma; //'3duard0faustos';
                                        $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                        if (!empty($result)) {
                                            De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                            Ct_ventas::updateXmlFirmado($venta->id);
                                            //recepcion sri
                                            $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                            De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                            Ct_ventas::updateRecibidoSri($venta->id);
                                            if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                //$this->procesarRespuestaXml($respuesta);
                                                $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                //autorzacion sri

                                                $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                if ($estadoAutorizacion == 'AUTORIZADO') {
                                                    De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                    Ct_ventas::updateXmlAutorizacion($venta->id);
                                                    $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                    $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                    $param = [
                                                        'email' => $venta->email_representante,
                                                        'nombre' => $venta->nombre,
                                                        'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                        'estab' => $data['infoTributaria']['estab'],
                                                        'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                        'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                        'tipoDoc' => $this->tipoDocumento
                                                    ];
                                                    $this->enviar_correo($param);
                                                    $arrayVentaAct = [
                                                        'numero' => $data['infoTributaria']['secuencial']
                                                    ];
                                                    Ct_ventas::where('id', $venta->id)->update($arrayVentaAct);
                                                } else {
                                                    De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                    Ct_ventas::updateXmlNoAutorizacion($venta->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuestaAutorizacion),
                                                        'id_documento' => $venta->id,
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
                                                }
                                            } else {
                                                $this->generarXmlRespuesta($respuesta);
                                                De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                Ct_ventas::updateXmlNoRecepcion($venta->id);
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($respuesta),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
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
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                echo json_encode($result);
                                return;
                            }
                        }
                        $respuestaNo->nro_comprobante = $numDocumento;
                        return $respuestaNo;
                    }
                }
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
            return json_encode($result);
        }
    }
    private static function getNonce($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
    public static function envio_factura($data)
    {
        if (Auth::check()) {
            $id_usuario = Auth::user()->id;
        } else {
            $id_usuario       = 'FACELECTRO';
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $nonce      = EmisionDocumentosController::getNonce(12);
        $empresa    = Empresa::findorfail($data['empresa']);
        if ($empresa->electronica != 1 && !is_null($empresa->appid) && !is_null($empresa->appsecret) && !is_null($empresa->url) && !is_null($empresa->establecimiento) && !is_null($empresa->punto_emision)) {
            return "empresa no permite facturacion electronica";
        } elseif ($empresa->electronica != 1) {
            return "empresa no permite facturacion electronica";
        }
        $appId     = $empresa->appid;
        $appSecret = $empresa->appsecret;
        $date      = date('c');
        $Token     = base64_encode(sha1($nonce . $date . $appSecret));
        $envio['company'] = $data['empresa'];
        //VALIDO SI LA CEDULA O RUC ES VALIDO
        $valida_cedula = true;
        if ($data['cliente']['tipo'] != 6) {
            $valida_cedula = EmisionDocumentosController::validarCedula($data['cliente']['cedula']);
        }
        if (!$valida_cedula && ($data['cliente']['tipo'] == 4 || $data['cliente']['tipo'] == 5)) {
            return "numero de cedula incorrecto";
        }
        //INGRESO LOS DATOS DE LA PERSONA
        $person['document'] = $data['cliente']['cedula'];
        if ($data['cliente']['tipo'] == 6) {
            $tipo = "06";
        } elseif ($data['cliente']['tipo'] == 8) {
            $tipo = "08";
        } elseif (strlen($data['cliente']['cedula']) == 13 && substr($data['cliente']['cedula'], -3) == '001') {
            $tipo = "04";
        } else {
            $tipo = "05";
        }
        $person['documentType']       = $tipo;
        $person['name']               = $data['cliente']['nombre'];
        $person['surname']            = $data['cliente']['apellido'];
        $person['email']              = $data['cliente']['email'];
        $person['mobile']             = $data['cliente']['telefono'];
        $person['address']['street']  = $data['cliente']['direccion']['calle'];
        $person['address']['city']    = $data['cliente']['direccion']['ciudad'];
        $person['address']['country'] = 'EC';
        $envio['person']              = $person;
        if (count($data['productos']) <= 0) {
            return "no existen productos";
        }
        //ingreso y creo los productos
        $productos = array();
        foreach ($data['productos'] as $key => $value) {
            $nombre_p            = "";
            $nombre_p            = substr($value['sku'], 0, 20);
            $nombre_p            = str_replace(" ", "_", $nombre_p);
            $arreglo             = array();
            $arreglo['sku']      = $nombre_p;
            $arreglo['name']     = $value['nombre'];
            $arreglo['qty']      = intval($value['cantidad']);
            $arreglo['price']    = floatval(number_format($value['precio'], 2, '.', ''));
            $arreglo['discount'] = floatval(number_format($value['descuento'], 2, '.', ''));
            $arreglo['subtotal'] = floatval(number_format($value['subtotal'], 2, '.', ''));
            $arreglo['tax']      = floatval(number_format($value['tax'], 2, '.', ''));
            $arreglo['total']    = floatval(number_format($value['total'], 2, '.', ''));
            array_push($productos, $arreglo);
        }
        $envio['items'] = $productos;
        //parametros de pago
        if (isset($data['externo'])) {
            $pago['establecimiento'] = $data['establecimiento'];
            $pago['ptoEmision']      = $data['ptoEmision'];
        } else {
            $pago['establecimiento'] = $empresa->establecimiento;
            $pago['ptoEmision']      = $empresa->punto_emision;
        }
        if ($empresa->externo == 1) {
            $data['establecimiento'] = $empresa->establecimiento;
            $data['ptoEmision']      = $empresa->punto_emision;
        }
        $info_adicional          = array();
        foreach ($data['pago']['informacion_adicional'] as $key => $value) {
            $nombre_p         = "";
            $nombre_p         = substr($value['nombre'], 0, 20);
            $nombre_p         = str_replace(" ", "_", $nombre_p);
            $arreglo          = array();
            $arreglo['key']   = $nombre_p;
            $arreglo['value'] = $value['valor'];
            array_push($info_adicional, $arreglo);
        }
        $pago['infoAdicional']      = $info_adicional;
        $pago['formaPago']          = $data['pago']['forma_pago'];
        $pago['plazoDias']          = $data['pago']['dias_plazo'];
        $envio['billingParameters'] = $pago;
        $envio['userAgent']         = "SIAAM SOFTWARE/1";
        $envio_2 = json_encode($envio);
        if ($empresa->externo == 1) {
            $url     = $empresa->url;
        } else {
            $url     = $empresa->url . 'billing/create/';
        }
        EmisionDocumentosController::crearcarpeta($url);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $envio_2,
            'dato2'       => "Inicia api",
        ]);
        if ($empresa->externo == 0) {
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'POST',
                    'content' => $envio_2,
                ),
            );
        } else {
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n" . "Token: " . $Token . "\r\n" . "Seed: " . $date . "\r\n" . "Nonce: " . base64_encode($nonce) . "\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );
        }
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        Log_Api::create([
            'id_usuario'  => $id_usuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "COMUNICACION API",
            'url'         => $url,
            'dato1'       => $response,
            'dato2'       => "FIN API",
        ]);
        /*$response        = '{"status":{"status":"success","message":"","reason":"","date":"2020-12-30T10:12:54-05:00"},"requestId":"39","comprobante":"004-002-000000013"}';*/
        $respuesta_array = json_decode($response);
        echo '<pre>';
        print_r($respuesta_array);
        exit;
        if ($data['contable'] == 1 && $empresa->externo == 0) {
            EmisionDocumentosController::crea_factura($data, $respuesta_array);
        }
        return $respuesta_array;
    }
    public static function crea_factura($data, $info_comprobante)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = 'FACELECTRO';
        $fecha_as        = date('Y-m-d');
        $id_empresa      = $data['empresa'];
        $llevaOrden      = false;
        $numero          = $info_comprobante->comprobante;
        $numero1         = $numero;
        $num_comprobante = 0;
        $cliente = Ct_Clientes::where('identificacion', '=', $data['cliente']['cedula'])->first();
        //return $cliente. " - ". $request['identificacion_cliente'];
        $cliente_datos = $data['cliente'];

        if (is_null($cliente)) {
            // cliente
            Ct_Clientes::create([
                'nombre'                  => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'tipo'                    => $cliente_datos['tipo'],
                'identificacion'          => $cliente_datos['cedula'],
                'clase'                   => '1',
                'nombre_representante'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
                'cedula_representante'    => $cliente_datos['cedula'],
                'ciudad_representante'    => $cliente_datos['direccion']['ciudad'],
                'direccion_representante' => $cliente_datos['direccion']['calle'],
                'telefono1_representante' => $cliente_datos['telefono'],
                'email_representante'     => $cliente_datos['email'],
                'estado'                  => '1',
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ]);
        }
        $partes          = explode("-", $info_comprobante->comprobante);
        $c_sucursal      = $partes['0'];
        $c_caja          = $partes['1'];
        $num_comprobante = $info_comprobante->comprobante;
        $nfactura        = $partes['2'];
        $proced          = ' ';
        if ($data['laboratorio'] == 1) {
            $proced = 'Examenes de Laboratorio';
        }
        $pacis = Paciente::find($data['paciente']);

        $pac = "";
        if (!is_null($pacis)) {
            $pac = $pacis->apellido1 . ' ' . $pacis->apellido2 . ' ' . $pacis->nombre1;
        }

        $text        = 'Fact #' . ':' . $num_comprobante . '-' . $proced . ' | ' . $pac;
        $id_paciente = $data['paciente'];

        if (is_null($id_paciente)) {
            $id_paciente = '9999999999';
        }
        //fix contranstrain
        if (is_null($pacis)) {
            $id_paciente = '9999999999';
        }
        //***GUARDADO EN LA TABLA LOG FACTURAS VENTA***
        //7******GUARDAdo TABLA ASIENTO CABECERA********
        $total1       = 0;
        $subtotal0    = 0;
        $subtotal12   = 0;
        $descuento    = 0;
        $descuento_0  = 0;
        $descuento_12 = 0;
        $iva          = 0;
        foreach ($data['productos'] as $value) {
            $total1 += $value['total'];
            $descuento += $value['descuento'];
            $iva += $value['tax'];
            if ($value['tax'] == 0) {
                $subtotal0 += $value['subtotal'];
                $descuento_0 += $value['descuento'];
            } else {
                $subtotal12 += $value['subtotal'];
                $descuento_12 += $value['descuento'];
            }
        }
        $base_imponible = $subtotal0 + $subtotal12;

        $input_cabecera = [
            'fecha_asiento'   => $fecha_as,
            'fact_numero'     => $nfactura,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text,
            'valor'           => $total1,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        //$id_asiento_cabecera = 0;
        //GUARDAdo TABLA CT_VENTA.
        $factura_venta = [
            'sucursal'          => $c_sucursal,
            'punto_emision'     => $c_caja,
            'numero'            => $nfactura,
            'nro_comprobante'   => $num_comprobante,
            'id_asiento'        => $id_asiento_cabecera,
            'id_empresa'        => $id_empresa,
            'tipo'              => 'VEN-FA',
            'fecha'             => $fecha_as,
            'fecha_envio'       => $fecha_as,
            'divisas'           => 1,
            'nombre_cliente'    => strtoupper($cliente_datos['nombre']) . ' ' . strtoupper($cliente_datos['apellido']),
            'id_cliente'        => $cliente_datos['cedula'], //nombre_cliente
            'direccion_cliente' => $cliente_datos['direccion']['calle'],
            'ruc_id_cliente'    => $cliente_datos['cedula'],
            'telefono_cliente'  => $cliente_datos['telefono'],
            'email_cliente'     => $cliente_datos['email'],
            'id_paciente'       => $id_paciente,
            'nombres_paciente'  => $pac,
            'seguro_paciente'   => $data['id_seguro'],
            'concepto'          => $data['concepto'],
            'copago'            => $data['copago'],
            'subtotal_0'        => $subtotal0,
            'subtotal_12'       => $subtotal12,
            'descuento'         => $descuento,
            'base_imponible'    => $base_imponible,
            'impuesto'          => $iva,
            'total_final'       => $total1,
            'valor_contable'    => $total1,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'electronica'       => 1,
        ];
        // return $factura_venta;
        $id_venta = Ct_ventas::insertGetId($factura_venta);
        //$id_venta = 0;
        $arr_total      = [];
        $total_iva      = 0;
        $total_impuesto = 0;
        $total_0        = 0;
        //kardex
        foreach ($data['productos'] as $valor) {
            $datos_iva = 0;
            if ($valor['tax'] > 0) {
                $datos_iva = 1;
            }
            $detalle = [
                'id_ct_ventas'    => $id_venta,
                'id_ct_productos' => $valor['sku'],
                'nombre'          => $valor['nombre'],
                'cantidad'        => $valor['cantidad'],
                'precio'          => $valor['precio'],
                'descuento'       => $valor['descuento'],
                'extendido'       => $valor['subtotal'],
                'detalle'         => '',
                'copago'          => $valor['copago'],
                'check_iva'       => $datos_iva,
                'porcentaje'      => '0.12',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            Ct_detalle_venta::create($detalle);
        }
        //***MODULO CUENTA POR COBRAR***
        //cUENTAS X COBRAR CLIENTES
        $val_tol = $total1;
        if ($val_tol > 0) {
            $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => '1.01.02.05.01',
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => $val_tol,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        //    2.01.07.01.01 iva sobre ventas
        if ($iva > 0) {
            $plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => '2.01.07.01.01',
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $iva,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }
        // 4.1.01.02    Ventas Mercaderia Tarifa 12%
        if ($subtotal12 > 0) {
            $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.02')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => '4.1.01.02',
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'debe'                => '0',
                'haber'               => $subtotal12 + $descuento_12,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        // 4.1.01.01    Ventas Mercaderia Tarifa 0%
        if ($subtotal0 > 0) {
            if ($data['empresa'] == '1391914857001') {
                $plan_cuentas = Plan_Cuentas::where('id', '4.1.09.01')->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '4.1.09.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal0 + $descuento_0,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            } else {
                $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => '4.1.01.01',
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_as,
                    'debe'                => '0',
                    'haber'               => $subtotal0 + $descuento_0,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
        }
        if ($descuento > 0) {

            $plan_cuentas = Plan_Cuentas::where('id', '4.1.06.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => '4.1.06.01',
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fecha_as,
                'haber'               => '0',
                'debe'                => $descuento,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        $variable    = $data['formas_pago'];
        $arr_p       = [];
        $total_pagos = 0;
        foreach ($variable as $value) {
            Ct_Forma_Pago::create([

                'id_ct_ventas'    => $id_venta,
                'tipo'            => $value['id_tipo'],
                'fecha'           => $value['fecha'],
                'tipo_tarjeta'    => $value['tipo_tarjeta'],
                'numero'          => $value['numero_transaccion'],
                'banco'           => $value['id_banco'],
                'cuenta'          => $value['cuenta'],
                'giradoa'         => $value['giradoa'],
                'valor'           => $value['valor'],
                'valor_base'      => $value['valor_base'],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ]);
            if ($value['id_tipo'] != 7) {
                $arr_pagos = [
                    'id_tip_pago'    => $value['id_tipo'],
                    'fecha_pago'     => $value['fecha'],
                    'tipo_tarjeta'   => $value['tipo_tarjeta'],
                    'numero_pago'    => $value['numero_transaccion'],
                    'id_banco_pago'  => $value['id_banco'],
                    'id_cuenta_pago' => $value['cuenta'],
                    'giradoa'        => $value['giradoa'],
                    'valor'          => $value['valor'],
                    'valor_base'     => $value['valor_base'],
                ];
                $total_pagos += $value['valor'];

                array_push($arr_p, $arr_pagos);
            }
        }
        //agregar comprobantes de ingreso
        $erf = ApiFacturacionController::crearComprobante($nfactura, $data, $arr_p, $id_venta, $id_empresa, $total_pagos);
        return true;
    }
    public function getRetencion($dinfo = null, $idUsuario = 'FACELECTRO')
    {
        $data = [];
        $id_doc = 0;
        try {
            if (is_null($dinfo)) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $cabecera = Ct_Retenciones::getRetenciones();
                if (count($cabecera) > 0) {
                    if (!$this->consultarEstadoSRI($cabecera[0]->id_empresa)) {
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
                        foreach ($cabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            $email = '';
                            $telefono = '';
                            if ($empresa != '') {
                                $deEmpresa = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $deEmpresa->ruta_firma;
                                $firma['clave_firma'] = $deEmpresa->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = $deEmpresa->ambiente;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '07';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $subString = explode('-', $row->punto_emision);
                                if (count($subString) == 2) {
                                    $infoTributaria['ptoEmi'] = str_pad($subString[1], 3, 0, STR_PAD_LEFT);
                                } else {
                                    $infoTributaria['ptoEmi'] = str_pad($subString[0], 3, 0, STR_PAD_LEFT);
                                }
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);

                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $datosTributarios->secuencial_nro + 1;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
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

                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])->first();

                                $caja = Ct_Caja::where('id_sucursal', $sucursal->id)
                                    ->where('codigo_caja', $infoTributaria['ptoEmi'])->first();

                                $infoCompRetencion['fechaEmision'] = $row->fecha;
                                if (isset($caja->sucursal->direccion_sucursal)) {
                                    $infoCompRetencion['dirEstablecimiento'] = $caja->sucursal->direccion_sucursal;
                                } else {
                                    $infoCompRetencion['dirEstablecimiento'] = $empresa->direccion;
                                }

                                $infoCompRetencion['contribuyenteEspecial'] = null; //Opcional   
                                if ($deEmpresa->contribuyente_especial != 0)
                                    $infoCompRetencion['contribuyenteEspecial'] = $deEmpresa->contribuyente_especial;

                                $infoCompRetencion['obligadoContabilidad'] = 'NO';
                                if ($deEmpresa->contabilidad == 1)
                                    $infoCompRetencion['obligadoContabilidad'] = 'SI';

                                $compras = Ct_compras::where('id', $row->id_compra)->first();

                                if (!is_null($compras)) {
                                    $proveedor = Proveedor::where('id', $compras->proveedor)->first();

                                    if (!is_null($proveedor)) {
                                        $email = $proveedor->email;
                                        $telefono = $proveedor->telefono;
                                        $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = str_pad($proveedor->tipo, 2, 0, STR_PAD_LEFT);
                                        $infoCompRetencion['razonSocialSujetoRetenido'] = $proveedor->razonsocial;
                                        $infoCompRetencion['identificacionSujetoRetenido'] = $proveedor->id;
                                    } else {
                                        $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = '';
                                        $infoCompRetencion['razonSocialSujetoRetenido'] = '';
                                        $infoCompRetencion['identificacionSujetoRetenido'] = '';
                                    }
                                } else {
                                    $proveedor = Proveedor::where('id', $row->id_proveedor);
                                    $infoCompRetencion['tipoIdentificacionSujetoRetenido'] = str_pad($proveedor->tipo, 2, 0, STR_PAD_LEFT);
                                    $infoCompRetencion['razonSocialSujetoRetenido'] = $proveedor->razonsocial;
                                    $infoCompRetencion['identificacionSujetoRetenido'] = $proveedor->id;
                                }

                                $infoCompRetencion['periodoFiscal'] = date('Y-m-d');
                                $data['infoCompRetencion'] = $infoCompRetencion;
                                //impuestos
                                $impuesto = [];
                                $impuestos = [];
                                $conDetalle = 0;
                                $detalleReten = Ct_detalle_retenciones::getDetalles($row->id);

                                if (count($detalleReten) > 0) {
                                    foreach ($detalleReten as $deta) {
                                        $porcentajeRetencion = Ct_Porcentaje_Retenciones::where('id', $deta->id_porcentaje)->first();
                                        if ($porcentajeRetencion->tipo == 2) {
                                            $impuesto['codigo'] = '1'; //RENTA
                                        } elseif ($porcentajeRetencion->tipo == 1) {
                                            $impuesto['codigo'] = '2'; //IVA
                                        } else {
                                            $impuesto['codigo'] = '6'; //ISD
                                        }
                                        $impuesto['codigoRetencion'] = $porcentajeRetencion->codigo;
                                        $impuesto['baseImponible'] = $deta->base_imponible;
                                        $impuesto['porcentajeRetener'] = $porcentajeRetencion->valor;
                                        $impuesto['valorRetenido'] = $deta->totales;
                                        $impuesto['codDocSustento'] = '01'; //al momento de desarrollo solo se realizan retenciones x facturas

                                        if (!is_null($compras)) {
                                            $numDocSus = str_replace('-', '', $compras->numero);
                                            $impuesto['numDocSustento'] = $numDocSus;
                                            $impuesto['fechaEmisionDocSustento'] = $compras->fecha;
                                        }
                                        $impuestos[$conDetalle] = $impuesto;
                                        $conDetalle++;
                                    }
                                }
                                $data['impuestos'] = $impuestos;

                                $campoAdicional['nombre'] = 'email';
                                if ($email != '') {
                                    $campoAdicional['valor']  = $email;
                                } else {
                                    $campoAdicional['valor']  = 'no tiene';
                                }
                                $informacion_adicional[0] = $campoAdicional;

                                $campoAdicional['nombre'] = 'telefono';
                                if ($telefono != '') {
                                    $campoAdicional['valor'] = $telefono;
                                } else {
                                    $campoAdicional['valor']  = 'no tiene';
                                }
                                $informacion_adicional[1] = $campoAdicional;

                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }

                            $errores = $this->validarData($data);

                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoCompRetencion' => json_encode($data['infoCompRetencion']),
                                    'impuestos' => json_encode($data['impuestos']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoCompRetencion['identificacionSujetoRetenido'],
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
                                Ct_Retenciones::updateSinGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
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
                                    'infoCompRetencion' => json_encode($data['infoCompRetencion']),
                                    'impuestos' => json_encode($data['impuestos']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoCompRetencion['identificacionSujetoRetenido'],
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
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    Ct_Retenciones::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    if (!empty($comprobante)) {
                                        $docAnte = simplexml_load_string($comprobante);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante); //$this->ValidarFuera($comprobante, $this->tipoDocumento);
                                            //1: ok; 0: error
                                            if ($validacion['isValidoXsd'] == 0) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|Error al validar XML con XSD');
                                                echo json_encode($result);
                                                return;
                                            }
                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_Retenciones::updateValidacionXSD($row->id);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $deEmpresa->ruta_firma; //firma.p12
                                            //$ruta_p12 = $deEmpresa->ruta_firma; //firma.p12
                                            $password = $deEmpresa->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if (!empty($result)) {

                                                $docAnte = simplexml_load_string($comprobante);
                                                $dom = new DOMDocument();
                                                $dom->loadXML($result);
                                                $this->crearcarpeta(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/');
                                                $dom->save(base_path() . '/storage/app/facturaelectronica/firmados/' . $docAnte->infoTributaria->ruc . '/' . $this->tipoDocumento . '/' . $this->tipoDocumento . '-' . $docAnte->infoTributaria->estab . '-' . $docAnte->infoTributaria->ptoEmi . '-' . $docAnte->infoTributaria->secuencial . '.xml');
                                                echo '<pre>';
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_Retenciones::updateXmlFirmado($row->id);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, 1);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_Retenciones::updateRecibidoSri($row->id);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso);
                                                    $estadoAutorizacion = $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    if ($estadoAutorizacion == 'AUTORIZADO') {
                                                        De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                        Ct_Retenciones::updateXmlAutorizacion($empresa->id, $data['infoTributaria']['estab'], $data['infoTributaria']['ptoEmi'], $row->id, $docAnte->infoTributaria->claveAcceso, $secuancialDocumento);
                                                        $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                        $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
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
                                                        De_Documentos_Electronicos::updateXmlNoAutorizacion($comprobante, $respuestaAutorizacion);
                                                        Ct_Retenciones::updateXmlNoAutorizacion($row->id);
                                                        $arrayLog = [
                                                            'id_de_documentos_electronicos' => $id_doc,
                                                            'descripcion_error' => json_encode($respuestaAutorizacion),
                                                            'id_documento' => $row->id,
                                                            'id_usuariomod' => $idUsuario,
                                                            'id_usuariocrea' =>  $idUsuario,
                                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                        ];
                                                        De_Log_Error::setErrorLog($arrayLog);
                                                    }
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_Retenciones::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
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
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
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
    public function getNotaCredito($dinfo = null, $idUsuario = 'FACELECTRO')
    {
        $data = [];
        $id_doc = 0;
        $numDocumento = '';
        try {
            if ($dinfo == null) {
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                $notaCreditoCabecera = Ct_Nota_Credito_Clientes::getNotaCredito();
                if (count($notaCreditoCabecera) > 0) {
                    if (!$this->consultarEstadoSRI($notaCreditoCabecera[0]->id_empresa)) {
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
                        foreach ($notaCreditoCabecera as $row) {
                            $cont++;
                            $empresa = Empresa::getEmpresa($row->id_empresa);
                            if ($empresa != '') {
                                $datosFirma = De_Empresa::getDatos($row->id_empresa);
                                $firma['ruta_firma'] = $datosFirma->ruta_firma;
                                $firma['clave_firma'] = $datosFirma->clave_firma;
                                $data['datosfirma'] = $firma;
                                $infoTributaria['fecha_emision'] = date('d/m/Y', strtotime($row->fecha));
                                $infoTributaria['ambiente'] = 1;
                                $infoTributaria['tipoEmision'] = 1;
                                $infoTributaria['razonSocial'] = $empresa->razonsocial;
                                $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                                $infoTributaria['ruc'] = $empresa->id;
                                $infoTributaria['codDoc'] = '04';
                                $infoTributaria['estab'] = str_pad($row->sucursal, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['ptoEmi'] = str_pad($row->punto_emision, 3, 0, STR_PAD_LEFT);
                                $infoTributaria['dirMatriz'] = $empresa->direccion;
                                $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                                $datosTributarios = De_Info_Tributaria::getDatos($row->id_empresa, $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                                if ($datosTributarios == '') {
                                    $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                                    echo json_encode($result);
                                    return;
                                }
                                $secuencial = $datosTributarios->secuencial_nro + 1;
                                $infoTributaria['ambiente'] = $datosFirma->ambiente;
                                $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                                $claveObj = new ClaveAcceso();
                                $campos = [
                                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($row->fecha))),
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
                                //Info Nota Credito

                                $infoNotaCredito['fechaEmision'] = $row->fecha;
                                $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                                    ->where('codigo_sucursal', $infoTributaria['estab'])
                                    ->first();
                                if (isset($sucursal->direccion_sucursal)) {
                                    $infoNotaCredito['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                                } else {
                                    $infoNotaCredito['dirEstablecimiento'] = $empresa->direccion;
                                }
                                $telefonoCliente = '';
                                $direccionCliente = '';
                                $emailCliente = '';
                                $cliente = Ct_Clientes::getCliente($row->id_cliente);

                                if ($cliente != '') {
                                    if (isset($cliente->email_representante)) {
                                        $emailCliente = $cliente->email_representante;
                                    }

                                    if (isset($cliente->telefono1_representante)) {
                                        $telefonoCliente = $cliente->telefono1_representante;
                                    }

                                    if (isset($cliente->direccion_representante)) {
                                        $direccionCliente = $cliente->direccion_representante;
                                    }
                                    $infoNotaCredito['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                                    $infoNotaCredito['razonSocialComprador'] = $cliente->nombre;
                                    $infoNotaCredito['identificacionComprador'] = $cliente->identificacion;
                                } else {
                                    $infoNotaCredito['tipoIdentificacionComprador'] = null;
                                    $infoNotaCredito['razonSocialComprador'] = null;
                                    $infoNotaCredito['identificacionComprador'] = null;
                                }
                                if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                                    $infoNotaCredito['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                                }
                                $infoNotaCredito['obligadoContabilidad'] = 'NO';
                                if ($datosFirma->contabilidad == 1)
                                    $infoNotaCredito['obligadoContabilidad'] = 'SI'; //Opcional

                                if ($datosFirma->rise != '')
                                    $infoNotaCredito['rise'] = $datosFirma->rise; //Opcional

                                $infoNotaCredito['codDocModificado'] = '01';
                                $factura = null;
                                $idFactura = $row->numero_factura; //numero_factura es id_factura cuando es completa; id_factura para parcial
                                $factura = Ct_ventas::find($idFactura)->first();

                                if (!is_null($factura)) {
                                    $infoNotaCredito['fechaEmisionDocSustento'] = $factura->fecha;
                                    $infoNotaCredito['numDocModificado']  = str_replace('-', '', $factura->nro_comprobante);
                                    $infoNotaCredito['fechaEmisionDocSustento'] = $factura->fecha;
                                }
                                $infoNotaCredito['totalSinImpuestos'] = $row->subtotal0;
                                $infoNotaCredito['valorModificacion'] = $row->total_credito;
                                $divisa = Ct_Divisas::getMoneda($factura->divisas); //Ct_Divisas::where('id',$factura->divisas);
                                $infoNotaCredito['moneda'] = $divisa;
                                $totalConImpuestos = [];
                                $totalConImpuesto = [];
                                $conImpuesto = 0;
                                // $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $row->id)->first();
                                $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $row->numero_factura)->get();

                                if (count($venta_detalle) > 0) {
                                    foreach ($venta_detalle as $detail) {
                                        $totalConImpuesto['codigo'] = '2'; //IVA
                                        /*
                                                Porcentaje            Codigo
                                                    0%                  0
                                                    12%                 2
                                                    14%                 3
                                            No Objeto de Impuesto       6
                                            Exento de IVA               7
                                        */
                                        if ($detail->check_iva == 1) {

                                            $totalConImpuesto['codigoPorcentaje'] = '2'; //como se cuando son los otros tipos de iva 
                                            $totalConImpuesto['valor'] = $this->getDecimal(($detail->precio * $detail->cantidad) * $detail->porcentaje);
                                        } else {
                                            $totalConImpuesto['codigoPorcentaje'] = '0';
                                            $totalConImpuesto['valor'] = $this->getDecimal(0);
                                        }
                                        //$totalConImpuesto['descuentoAdicional'] = $detail->descuento;
                                        $totalConImpuesto['baseImponible'] = ($detail->cantidad * $detail->precio) - $detail->descuento;

                                        $totalConImpuestos[$conImpuesto] = $totalConImpuesto;
                                        $conImpuesto++;
                                    }
                                }
                                $infoNotaCredito['totalConImpuestos'] = $totalConImpuestos;
                                $infoNotaCredito['motivo'] = $row->concepto;
                                $data['infoNotaCredito'] = $infoNotaCredito;

                                //detalles
                                $detalle = [];
                                $detalles = [];
                                $conDetalle = 0;
                                if (count($venta_detalle) > 0) {
                                    foreach ($venta_detalle as $deta) {
                                        $detalle['p_impuesto'] = $deta->check_iva !== null ? 12 : 0;
                                        $detalle['codigoInterno'] = $deta->id_ct_productos;
                                        $detalle['codigoAdicional'] = $deta->id_ct_productos;
                                        $detalle['descripcion'] = $deta->nombre;
                                        $detalle['cantidad'] = $deta->cantidad;
                                        $detalle['precioUnitario'] = $deta->precio;
                                        $detalle['descuento'] = $deta->descuento;
                                        $detalle['precioTotalSinImpuesto'] = $row->sub_sin_imp;
                                        $contAdicional = 0;

                                        if ($deta->detalle != '') {
                                            $detAdicional = [
                                                'nombre' => 'detalle',
                                                'valor' => $deta->detalle
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($deta->adicional != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'adicional1',
                                                'valor' => $deta->adicional
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($deta->nombre != '') {
                                            $detAdicional_ = [
                                                'nombre' => 'nombre',
                                                'valor' => $deta->nombre
                                            ];
                                            $contAdicional++;
                                        }
                                        if ($contAdicional < 3) {
                                            if ($deta->bodega != '') {
                                                $detAdicional_ = [
                                                    'nombre' => 'bodega',
                                                    'valor' => $deta->bodega
                                                ];
                                                $contAdicional++;
                                            }
                                        }

                                        if ($contAdicional > 0) {
                                            $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                            $detalle['detallesAdicionales'] = $detallesAdicionales;
                                        }
                                        $detalles[$conDetalle] = $detalle;
                                        $conDetalle++;
                                    }
                                }
                                $data['detalles'] = $detalles;

                                $cont = 0;
                                if (isset($row->concepto) && $row->concepto != '') {
                                    $campoAdicional['nombre'] = "concepto";
                                    $campoAdicional['valor'] =  $row->concepto;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }
                                if ($emailCliente != '') {
                                    $campoAdicional['nombre'] = "email";
                                    $campoAdicional['valor']  =  $emailCliente;
                                    $informacion_adicional[$cont] = $campoAdicional;
                                    $cont++;
                                }

                                if ($telefonoCliente != '') {
                                    $campoAdicional['nombre'] = "telefono";
                                    $campoAdicional['valor'] =  $telefonoCliente;
                                } elseif ($direccionCliente != '') {
                                    $campoAdicional['nombre'] = "direccion";
                                    $campoAdicional['valor'] =  $direccionCliente;
                                }
                                $informacion_adicional[$cont] = $campoAdicional;
                                $infoAdicional['campoAdicional'] = $informacion_adicional;

                                $data['infoAdicional'] = $informacion_adicional;
                            } else {
                                $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'ok|No existe la empresa');
                                echo json_encode($result);
                                return;
                            }
                            $errores = $this->validarData($data);
                            if (count($errores) > 0) {
                                $arrayDocElec = [
                                    'id_de_pasos' => 7,
                                    'infoTributaria' => json_encode($data['infoTributaria']),
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' => $infoTributaria['identificacionComprador'],
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
                                Ct_Nota_Credito_Clientes::updateSinGenerarXML($row->id);
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
                                    'infoFactura' => json_encode($data['infoFactura']),
                                    'detalles' => json_encode($data['detalles']),
                                    'infoAdicional' => json_encode($data['infoAdicional']),
                                    'establecimiento' => $infoTributaria['estab'],
                                    'emision' => $infoTributaria['ptoEmi'],
                                    'secuencial' => $infoTributaria['secuencial'],
                                    'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
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
                                if (is_numeric($id_doc)) {
                                    $idInsertados .= $id_doc . ',';
                                    $comprobante = $this->generarDocElectronicoXml($data);
                                    //dd($comprobante);
                                    Ct_Nota_Credito_Clientes::updateGenerarXML($row->id, $data['infoTributaria']['claveAcceso']);
                                    if (!empty($comprobante)) {
                                        $docAnte = simplexml_load_string($comprobante);
                                        //dd($docAnte);
                                        if (!empty($docAnte)) {
                                            //validar xsd
                                            $validacion = $this->validarXmlToXsd($comprobante);
                                            if ($validacion['isValidoXsd'] != 1) {
                                                $arrayLog = [
                                                    'id_de_documentos_electronicos' => $id_doc,
                                                    'descripcion_error' => json_encode($validacion),
                                                    'id_usuariomod' => $idUsuario,
                                                    'id_usuariocrea' =>  $idUsuario,
                                                    'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                    'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                ];
                                                De_Log_Error::setErrorLog($arrayLog);
                                                $result = array('code' => 204, 'state' => true, 'data' => json_encode($validacion), 'message' => 'ok|No existen documentos para emitir');
                                                echo json_encode($result);
                                                return;
                                            }

                                            De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                            Ct_Nota_Credito_Clientes::updateValidacionXSD($row->id);
                                            //firmar xml
                                            $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                            $password = $datosFirma->clave_firma; //'3duard0faustos';
                                            $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                            if (!empty($result)) {
                                                De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                                Ct_Nota_Credito_Clientes::updateXmlFirmado($row->id);
                                                //recepcion sri
                                                $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                                De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                                Ct_Nota_Credito_Clientes::updateRecibidoSri($row->id);
                                                if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                                    //$this->procesarRespuestaXml($respuesta);
                                                    $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                                    $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                                    De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                                    //autorzacion sri
                                                    $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                                    $this->generarXmlAutorizacion($respuestaAutorizacion);
                                                    De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                                    Ct_Nota_Credito_Clientes::updateXmlAutorizacion($row->id, $docAnte->infoTributaria->claveAcceso, str_pad($secuancialDocumento, 9, '0', STR_PAD_LEFT));
                                                    $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                                    $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                                    $param = [
                                                        'email' => $row->email_traslado_destinatario,
                                                        'nombre' => $row->razon_social_destinatario,
                                                        'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                        'estab' => $data['infoTributaria']['estab'],
                                                        'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                        'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                        'tipoDoc' => $this->tipoDocumento
                                                    ];
                                                    $this->enviar_correo($param);
                                                } else {
                                                    $this->generarXmlRespuesta($respuesta);
                                                    De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                                    Ct_Nota_Credito_Clientes::updateXmlNoRecepcion($row->id);
                                                    $arrayLog = [
                                                        'id_de_documentos_electronicos' => $id_doc,
                                                        'descripcion_error' => json_encode($respuesta),
                                                        'id_usuariomod' => $idUsuario,
                                                        'id_usuariocrea' =>  $idUsuario,
                                                        'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                        'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                                    ];
                                                    De_Log_Error::setErrorLog($arrayLog);
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
                                } else {
                                    $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                                    echo json_encode($result);
                                    return;
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
                $result = [];
                $idInsertados = '';
                $claveInsertados = '';
                if (!$this->consultarEstadoSRI(session('id_empresa'))) {
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
                    return json_encode($result);
                } else {
                    $cont = 0;
                    $empresa = Empresa::getEmpresa($dinfo['empresa']);
                    if ($empresa != '') {
                        $datosFirma = De_Empresa::getDatos($dinfo['empresa']);

                        $notaCredito = Ct_Nota_Credito_Clientes::where('id', $dinfo['idVenta'])->first();

                        $firma['ruta_firma'] = $datosFirma->ruta_firma;
                        $firma['clave_firma'] = $datosFirma->clave_firma;
                        $data['datosfirma'] = $firma;
                        $infoTributaria['fecha_emision'] = date('d/m/Y');
                        $infoTributaria['ambiente'] = 1;
                        $infoTributaria['tipoEmision'] = 1;
                        $infoTributaria['razonSocial'] = $empresa->razonsocial;
                        $infoTributaria['nombreComercial'] = $empresa->nombrecomercial;
                        $infoTributaria['ruc'] = $empresa->id;
                        $infoTributaria['codDoc'] = '01';
                        $infoTributaria['estab'] = str_pad($notaCredito->sucursal, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['ptoEmi'] = str_pad($notaCredito->punto_emision, 3, 0, STR_PAD_LEFT);
                        $infoTributaria['dirMatriz'] = $empresa->direccion;
                        $idDoc = De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']);
                        $datosTributarios = De_Info_Tributaria::getDatos(session('id_empresa'), $infoTributaria['estab'], $infoTributaria['ptoEmi'], $idDoc);
                        if ($datosTributarios == '') {
                            $result = array('code' => 204, 'state' => true, 'data' => ['error:' => 'no se ha configurado los datos tributarios para esta empresa: ' . $empresa->id], 'message' => 'no existen datos tributarios para esta empresa');
                            echo json_encode($result);
                            return;
                        }
                        $secuencial = $datosTributarios->secuencial_nro + 1;
                        $infoTributaria['ambiente'] = $datosFirma->ambiente;
                        $infoTributaria['secuencial'] = str_pad($secuencial, 9, 0, STR_PAD_LEFT);
                        $claveObj = new ClaveAcceso();
                        $campos = [
                            'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($notaCredito->fecha))),
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
                        //InfoTributaria
                        $infoNotaCredito['fechaEmision'] = $notaCredito->fecha;
                        $sucursal = Ct_Sucursales::where('id_empresa', $empresa->id)
                            ->where('codigo_sucursal', $infoTributaria['estab'])
                            ->first();

                        if (isset($sucursal->direccion_sucursal)) {
                            $infoNotaCredito['dirEstablecimiento'] = $sucursal->direccion_sucursal;
                        } else {
                            $infoNotaCredito['dirEstablecimiento'] = $empresa->direccion;
                        }
                        if ($datosFirma->contribuyente_especial != '' && $datosFirma->contribuyente_especial != 0) {
                            $infoNotaCredito['contribuyenteEspecial'] = $datosFirma->contribuyente_especial; //Opcional
                        }
                        $infoNotaCredito['obligadoContabilidad'] = 'NO'; //Opcional
                        if ($datosFirma->contabilidad == 1)
                            $infoNotaCredito['obligadoContabilidad'] = 'SI'; //Opcional
                        $cliente = Ct_Clientes::getCliente($notaCredito->id_cliente);

                        if ($cliente != '') {
                            $email = $cliente->email_representante;
                            $telefonoCliente = $cliente->telefono1;
                            $infoNotaCredito['tipoIdentificacionComprador'] = str_pad($cliente->tipo, 2, 0, STR_PAD_LEFT);
                            $infoNotaCredito['identificacionComprador'] = $cliente->identificacion;
                            $infoNotaCredito['razonSocialComprador'] = $cliente->nombre;
                            $infoNotaCredito['direccionComprador'] = $cliente->direccion_representante;
                        } else {
                            $infoNotaCredito['tipoIdentificacionComprador'] = null;
                            $infoNotaCredito['identificacionComprador'] = null;
                            $infoNotaCredito['razonSocialComprador'] = null;
                            $infoNotaCredito['direccionComprador'] = null; //Opcional
                        }
                        $infoNotaCredito['totalSinImpuestos'] = $notaCredito->subtotal_0 + $notaCredito->subtotal_12;
                        $infoNotaCredito['codDocModificado'] = '01';
                        $factura = null;
                        $idFactura = $notaCredito->numero_factura; //numero_factura es id_factura cuando es completa; id_factura para parcial
                        $factura = Ct_ventas::find($idFactura)->first();

                        if (!is_null($factura)) {
                            $infoNotaCredito['fechaEmisionDocSustento'] = $factura->fecha;
                            $infoNotaCredito['numDocModificado']  = $factura->nro_comprobante;
                        }
                        $infoNotaCredito['totalSinImpuestos'] = $notaCredito->subtotal0;
                        $infoNotaCredito['valorModificacion'] = $notaCredito->total_final;
                        $divisa = Ct_Divisas::getMoneda($factura->divisas); //Ct_Divisas::where('id',$factura->divisas);
                        $infoNotaCredito['moneda'] = $divisa;
                        $totalConImpuestos = [];
                        $totalConImpuesto = [];
                        $conImpuesto = 0;
                        // $details       = Ct_Detalle_Credito_Clientes::where('id_not_cred', $notaCredito->id)->first();
                        $venta_detalle = Ct_detalle_venta::where('id_ct_ventas', $idFactura)->get();
                        if (count($venta_detalle) > 0) {
                            foreach ($venta_detalle as $detail) {
                                $totalConImpuesto['codigo'] = '2'; //IVA
                                if ($detail->check_iva == 1) {
                                    $totalConImpuesto['codigoPorcentaje'] = '12';
                                    $totalConImpuesto['valor'] = $this->getDecimal(($detail->precio * $detail->cantidad) * $detail->porcentaje);
                                } else {
                                    $totalConImpuesto['codigoPorcentaje'] = '0';
                                    $totalConImpuesto['valor'] = $this->getDecimal(0);
                                }
                                $totalConImpuesto['descuentoAdicional'] = $detail->descuento;
                                $totalConImpuesto['baseImponible'] = ($detail->cantidad * $detail->precio) - $detail->descuento;

                                $totalConImpuestos[$conImpuesto] = $totalConImpuesto;
                                $conImpuesto++;
                            }
                        }
                        $infoNotaCredito['totalConImpuestos'] = $totalConImpuestos;
                        $infoNotaCredito['motivo'] = $notaCredito->concepto;
                        $data['infoNotaCredito'] = $infoNotaCredito;

                        //detalles
                        $detalle = [];
                        $detalles = [];
                        $conDetalle = 0;
                        if (count($venta_detalle) > 0) {
                            foreach ($venta_detalle as $deta) {
                                $detalle['p_impuesto'] = $deta->check_iva !== null ? 12 : 0;
                                $detalle['codigoInterno'] = $deta->id_ct_productos;
                                $detalle['codigoAdicional'] = $deta->id_ct_productos;
                                $detalle['descripcion'] = $deta->nombre;
                                $detalle['cantidad'] = $deta->cantidad;
                                $detalle['precioUnitario'] = $deta->precio;
                                $detalle['descuento'] = $deta->descuento;
                                $detalle['precioTotalSinImpuesto'] = $notaCredito->sub_sin_imp;
                                $contAdicional = 0;
                                if ($deta->detalle != '') {
                                    $detAdicional = [
                                        'nombre' => 'detalle',
                                        'valor' => $deta->detalle
                                    ];
                                    $contAdicional++;
                                }
                                if ($deta->adicional != '') {
                                    $detAdicional_ = [
                                        'nombre' => 'adicional',
                                        'valor' => $deta->adicional
                                    ];
                                    $contAdicional++;
                                }
                                if ($contAdicional > 0) {
                                    $detallesAdicionales = [isset($detAdicional) ? $detAdicional : [], isset($detAdicional_) ? $detAdicional_ : []];
                                    $detalle['detallesAdicionales'] = $detallesAdicionales;
                                }
                                $detalles[$conDetalle] = $detalle;
                                $conDetalle++;
                            }
                        }
                        $data['detalles'] = $detalles;
                        $campoAdicional['nombre'] = "detalle";
                        $campoAdicional['valor']  = $notaCredito->nota_electronica;
                        $informacion_adicional[0] = $campoAdicional;
                        $infoAdicional['campoAdicional'] = $informacion_adicional;
                        $data['infoAdicional'] = $informacion_adicional;
                        echo '<pre>';
                        print_r($data);
                        exit;
                        $errores = $this->validarData($data);

                        //Creacion XML
                        $arrayLogError = [
                            'id_de_pasos' => 1,
                            'infoTributaria' => json_encode($data['infoTributaria']),
                            'infoFactura' => json_encode($data['infoFactura']),
                            'detalles' => json_encode($data['detalles']),
                            'infoAdicional' => json_encode($data['infoAdicional']),
                            'establecimiento' => $infoTributaria['estab'],
                            'emision' => $infoTributaria['ptoEmi'],
                            'secuencial' => $infoTributaria['secuencial'],
                            'ruc_receptor' =>  $infoTributaria['identificacionComprador'],
                            'ruc_emisor' => $infoTributaria['ruc'],
                            'id_usuariomod' => $idUsuario,
                            'id_usuariocrea' =>  $idUsuario,
                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                            'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                            'id_maestro_documento' => De_Maestro_Documentos::getIdMaestroDocuemnto($infoTributaria['codDoc']),
                            'id_documento' => $notaCredito->id
                        ];
                        $id_doc = De_Documentos_Electronicos::setDocElectronico($arrayLogError, 1);
                        if (is_numeric($id_doc)) {
                            $idInsertados .= $id_doc . ',';
                            $comprobante = $this->generarDocElectronicoXml($data);
                            Ct_ventas::updateGenerarXML($notaCredito->id, $data['infoTributaria']['claveAcceso']);
                            if (!empty($comprobante)) {
                                $docAnte = simplexml_load_string($comprobante);
                                if (!empty($docAnte)) {
                                    //validar xsd
                                    $validacion = $this->validarXmlToXsd($comprobante);
                                    if ($validacion['isValidoXsd'] != 1) {
                                        $arrayLog = [
                                            'id_de_documentos_electronicos' => $id_doc,
                                            'descripcion_error' => json_encode($validacion),
                                            'id_usuariomod' => $idUsuario,
                                            'id_usuariocrea' =>  $idUsuario,
                                            'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                            'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                        ];
                                        De_Log_Error::setErrorLog($arrayLog);
                                        $result = array('code' => 500, 'state' => true, 'data' => json_encode($validacion), 'message' => 'Error: al validar documento xml.');
                                        return json_encode($result);
                                    }
                                    De_Documentos_Electronicos::updateValidacionXSD($comprobante);
                                    Ct_ventas::updateValidacionXSD($notaCredito->id);
                                    //firmar xml
                                    $ruta_p12 = base_path() . '/storage/app/facturaelectronica/p12/' . $datosFirma->ruta_firma; //firma.p12
                                    $password = $datosFirma->clave_firma; //'3duard0faustos';
                                    $result = $this->generarXmlSignJar($comprobante, $this->tipoDocumento, $ruta_p12, $password, $docAnte->infoTributaria->claveAcceso, $docAnte->infoTributaria->secuencial, $docAnte->infoTributaria->ruc);
                                    if (!empty($result)) {
                                        De_Documentos_Electronicos::updateXmlFirmado($comprobante);
                                        Ct_ventas::updateXmlFirmado($notaCredito->id);
                                        //recepcion sri
                                        $respuesta = $this->recibirWs($result, $data['infoTributaria']['ambiente']);
                                        De_Documentos_Electronicos::updateRecibidoSri($comprobante, $respuesta);
                                        Ct_ventas::updateRecibidoSri($notaCredito->id);
                                        if ($respuesta['estado'] == 'RECIBIDA') { //Devuelta
                                            //$this->procesarRespuestaXml($respuesta);
                                            $numDocumento = $data['infoTributaria']['estab'] . '-' . $data['infoTributaria']['ptoEmi'] . '-' . $data['infoTributaria']['secuencial'];
                                            $secuancialDocumento = (int)$data['infoTributaria']['secuencial'];
                                            De_Info_Tributaria::updateNumDocumento($empresa->id, $numDocumento, $secuancialDocumento, $datosTributarios->id_sucursal, $datosTributarios->id_caja, $datosTributarios->id_maestro_documentos);
                                            //autorzacion sri
                                            $respuestaAutorizacion = $this->autorizacion_sri($docAnte->infoTributaria->claveAcceso, $data['infoTributaria']['ambiente']);
                                            $this->generarXmlAutorizacion($respuestaAutorizacion);
                                            De_Documentos_Electronicos::updateXmlAutorizacion($comprobante, $respuesta);
                                            Ct_ventas::updateXmlAutorizacion($notaCredito->id);
                                            $claveInsertados .= $data['infoTributaria']['claveAcceso'] . ',';
                                            $this->generarPdf($docAnte->infoTributaria->claveAcceso, '', $this->tipoDocumento);
                                            $param = [
                                                'email' => $notaCredito->email_traslado_destinatario,
                                                'nombre' => $notaCredito->razon_social_destinatario,
                                                'claveAcceso' => $docAnte->infoTributaria->claveAcceso,
                                                'estab' => $data['infoTributaria']['estab'],
                                                'ptoEmi' =>  $data['infoTributaria']['ptoEmi'],
                                                'secuencial' =>  $data['infoTributaria']['secuencial'],
                                                'tipoDoc' => $this->tipoDocumento
                                            ];
                                            $this->enviar_correo($param);
                                        } else {
                                            $this->generarXmlRespuesta($respuesta);
                                            De_Documentos_Electronicos::updateXmlNoRecepcion($comprobante, $respuesta);
                                            Ct_ventas::updateXmlNoRecepcion($notaCredito->id);
                                            $arrayLog = [
                                                'id_de_documentos_electronicos' => $id_doc,
                                                'descripcion_error' => json_encode($respuesta),
                                                'id_usuariomod' => $idUsuario,
                                                'id_usuariocrea' =>  $idUsuario,
                                                'ip_creacion' => $_SERVER['REMOTE_ADDR'],
                                                'ip_modificacion' =>  $_SERVER['REMOTE_ADDR'],
                                            ];
                                            De_Log_Error::setErrorLog($arrayLog);
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
                        } else {
                            $result = array('code' => 204, 'state' => true, 'data' => '', 'message' => 'error|' . $id_doc);
                            echo json_encode($result);
                            return;
                        }
                    }
                }
                $respuesta = new stdClass();
                $respuesta->nro_comprobante = $numDocumento;
                echo '<pre>';
                print_r($respuesta);
                exit;
                return $respuesta;
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
    public static function getNotaDebito($dinfo = null, $idUsuario = 'FACELECTRO')
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
        return $result;
    }
    public static function setBarcode($clave, $ruta)
    {
        $im = imagecreatetruecolor(300, 80);
        $black = ImageColorAllocate($im, 0x00, 0x00, 0x00);
        $white = ImageColorAllocate($im, 0xff, 0xff, 0xff);
        imagefilledrectangle($im, 0, 0, 300, 300, $white);
        EmisionDocumentosController::crearcarpeta(base_path() . $ruta);
        Barcode::gd($im, $black, 150, 40, 0, "code128", $clave, 2, 50);
        imagepng($im, base_path() . $ruta . $clave . '.png');
        imagedestroy($im);
        return $ruta . $clave . '.png';
    }
    public function pingDomain($domain)
    {
        ini_set('max_execution_time', '300');
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
        $error = [];
        $cont = 0;
        $valida_cedula = false;
        $codDoc = '';
        $codigo = '';
        $codigoRetencion = '';
        $porcentajeRetener = '';
        if (!isset($data['datosfirma']['clave_firma'])) {
            $error[$cont] =  'Error: ' . ($cont + 1) . ': clave de firma no registrada (' . $data['datosfirma']['clave_firma'] . ')';
            $cont++;
        } else {
            if ($data['datosfirma']['clave_firma'] == '') {
                $error[$cont] =  'Error: ' . ($cont + 1) . ': clave de firma no registrada (' . $data['datosfirma']['clave_firma'] . ')';
                $cont++;
            }
        }
        if (!isset($data['datosfirma']['ruta_firma'])) {
            $error[$cont] =  "Error: " . ($cont + 1) . ": firma no registrada";
            $cont++;
        } else {
            if ($data['datosfirma']['ruta_firma'] == '') {
                $error[$cont] =  "Error: " . ($cont + 1) . ": firma no registrada";
                $cont++;
            } else {
                if (!file_exists(base_path() . '/storage/app/facturaelectronica/p12/' . $data['datosfirma']['ruta_firma'])) {
                    $error[$cont] =  'Error: ' . ($cont + 1) . ': el archivo de la firma no existe (' . $data['datosfirma']['ruta_firma'] . ')';
                    $cont++;
                } else {
                    if (!$this->verificar_token(base_path() . '/storage/app/facturaelectronica/p12/' . $data['datosfirma']['ruta_firma'], $data['datosfirma']['clave_firma'])) {
                        $error[$cont] =  "Error: " . ($cont + 1) . ": la clave no corresponde al archivo de la firma";
                        $cont++;
                    }
                }
            }
        }
        if (isset($data['infoTributaria']['ruc'])) {
            if ($data['infoTributaria']['ruc'] == '') {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc no puede estar vacio';
                $cont++;
            }
            if (strlen($data['infoTributaria']['ruc']) != 13) {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                $cont++;
            }
            $empresa = Empresa::where('id', $data['infoTributaria']['ruc'])->first();
            if (is_null($empresa)) {
                $error[$cont] =  "Error: " . ($cont + 1) . ": no existe la empresa";
                $cont++;
            } else {
                //InfoFactura
                //validar si la empresa esta habilitada como facturacion electronica
                $deEmpresa = De_Empresa::where('id_empresa', $empresa->id)->first();
                //dd($deEmpresa,$empresa);
                if (is_null($deEmpresa)) {
                    $error[$cont] =  "Error: " . ($cont + 1) . ": No se encuentra informacion de facturacion electronica";
                    $cont++;
                }
                //validar ambiente
                if (isset($data['infoTributaria']['ambiente'])) {
                    if ($data['infoTributaria']['ambiente'] == '') {
                        $error[$cont] =  "Error " . ($cont + 1) . ': el campo ambiente es obligatorio';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['ambiente']) != 1) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el ambiente debe contener un digito';
                        $cont++;
                    }
                    if ($data['infoTributaria']['ambiente'] != 1 && $data['infoTributaria']['ambiente'] != 2) {
                        $error[$cont] = 'Error ' . ($cont + 1) . ': el ambiente solo puede ser 1 o 2';
                        $cont++;
                    }
                    if (!is_numeric($data['infoTributaria']['ambiente'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el ambiente debe ser numerico';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ambiente es obligatorio';
                    $cont++;
                }
                //validar tipo Emision
                if (isset($data['infoTributaria']['tipoEmision'])) {
                    if ($data['infoTributaria']['tipoEmision'] == '') {
                        $error[$cont] =  "Error " . ($cont + 1) . ': el campo tipoEmision es obligatorio';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['tipoEmision']) != 1) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el tipoEmision debe contener un digito';
                        $cont++;
                    }
                    if ($data['infoTributaria']['tipoEmision'] != 1) {
                        $error[$cont] = 'Error ' . ($cont + 1) . ': el tipoEmision solo puede ser 1';
                        $cont++;
                    }
                    if (!is_numeric($data['infoTributaria']['tipoEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el tipoEmision debe ser numerico';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoEmision es obligatorio';
                    $cont++;
                }
                //Validar razonSocial
                if (isset($data['infoTributaria']['razonSocial'])) {
                    if ($data['infoTributaria']['razonSocial'] == '') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['razonSocial']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocial es obligatorio';
                    $cont++;
                }
                //Validar nombreComercial
                if (isset($data['infoTributaria']['nombreComercial'])) {
                    if ($data['infoTributaria']['nombreComercial'] != '') {
                        if (strlen($data['infoTributaria']['nombreComercial']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo nombreComercial es obligatorio';
                    $cont++;
                }
                //validar ruc
                if (isset($data['infoTributaria']['ruc'])) {
                    if ($data['infoTributaria']['ruc'] == '') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                        $cont++;
                    }
                    if (strlen($data['infoTributaria']['ruc']) != 13) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc debe contener 13 caracteres';
                        $cont++;
                    } else {
                        $correcto = $this->validarCedula($data['infoTributaria']['ruc']);
                        if (!$correcto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': numero de ruc incorrecto';
                            $cont++;
                        }
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ruc es obligatorio';
                    $cont++;
                }
                //Validar si puede emitir documentos electronicos
                if ($deEmpresa == '') {
                    //if ($empresa->electronica != 1) {
                    $error[$cont] = "Error " . ($cont + 1) . ": empresa no permite facturacion electronica";
                    $cont++;
                }
                //validar ClaveAcceso
                if (isset($data['infoTributaria']['claveAcceso'])) {
                    if (!$data['infoTributaria']['claveAcceso'] == '') {
                        if (strlen($data['infoTributaria']['claveAcceso']) != 49) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso debe contener 49 caracteres (' . $data['infoTributaria']['claveAcceso'] . ' No. de caracteres ingresados: ' . strlen($data['infoTributaria']['claveAcceso']) . ' )';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['claveAcceso'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso debe ser numerico (' . $data['infoTributaria']['claveAcceso'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo claveAcceso es obligatorio';
                    $cont++;
                }
                //validar codDoc
                if (isset($data['infoTributaria']['codDoc'])) {
                    if ($data['infoTributaria']['codDoc'] != '') {
                        if (strlen($data['infoTributaria']['codDoc']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc debe contener 2 caracteres (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['codDoc'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc debe ser numerico (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                        if ($data['infoTributaria']['codDoc'] != '01' && $data['infoTributaria']['codDoc'] != '03' && $data['infoTributaria']['codDoc'] != '04' && $data['infoTributaria']['codDoc'] != '05' && $data['infoTributaria']['codDoc'] != '06' && $data['infoTributaria']['codDoc'] != '07') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['infoTributaria']['codDoc'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDoc es obligatorio';
                    $cont++;
                }
                //validar estab
                if (isset($data['infoTributaria']['estab'])) {
                    if (strlen($data['infoTributaria']['estab']) != '') {
                        if (strlen($data['infoTributaria']['estab']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estab debe contener 3 caracteres (' . $data['infoTributaria']['estab'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['estab'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo estab debe ser numerico (' . $data['infoTributaria']['estab'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo estab no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo estab es obligatorio';
                    $cont++;
                }
                //validar ptoEmi
                if (isset($data['infoTributaria']['ptoEmi'])) {
                    if (strlen($data['infoTributaria']['ptoEmi']) != '') {
                        if (strlen($data['infoTributaria']['ptoEmi']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi debe contener 3 caracteres (' . $data['infoTributaria']['ptoEmi'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['ptoEmi'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi debe ser numerico (' . $data['infoTributaria']['ptoEmi'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ptoEmi es obligatorio';
                    $cont++;
                }
                //validar secuencial
                if (isset($data['infoTributaria']['secuencial'])) {
                    if (strlen($data['infoTributaria']['secuencial']) != '') {
                        if (strlen($data['infoTributaria']['secuencial']) != 9) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial debe contener 9 caracteres (' . $data['infoTributaria']['secuencial'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($data['infoTributaria']['secuencial'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial debe ser numerico (' . $data['infoTributaria']['secuencial'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo secuencial es obligatorio';
                    $cont++;
                }
                //validar dirMatriz
                if (isset($data['infoTributaria']['dirMatriz'])) {
                    if ($data['infoTributaria']['dirMatriz'] != '') {
                        if (strlen($data['infoTributaria']['dirMatriz']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirMatriz es obligatorio';
                    $cont++;
                }
                $codDoc = $data['infoTributaria']['codDoc'];
                if ($codDoc == '06') { //GuiaRemision
                    //validarInfoGuiaRemision
                    if (isset($data['infoGuiaRemision'])) {
                        if ($data['infoGuiaRemision'] != '') {
                            $error = $this->validarInfoGuiaRemision($data['infoGuiaRemision'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoGuiaRemision no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoGuiaRemision es obligatorio';
                        $cont++;
                    }
                    //validar destinatarios
                    if (isset($data['destinatarios'])) {
                        if ($data['destinatarios'] != '') {
                            $error = $this->validarDestinatarios($data['destinatarios'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo destinatarios no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo destinatarios es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '01') { //Factura
                    //validarInfoFactura
                    if (isset($data['infoFactura'])) {
                        if ($data['infoFactura'] != '') {
                            $error = $this->validarInfoFactura($data['infoFactura'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoFactura no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoFactura es obligatorio';
                        $cont++;
                    }
                    //ValidarDetalle
                    if (isset($data['detalles'])) {
                        if ($data['detalles'] != '') {
                            $error = $this->validarDetalles($data['detalles'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '03') { //LiquidacionCompra
                    //validarInfoLiquidacion
                    if (isset($data['infoLiquidacion'])) {
                        if ($data['infoLiquidacion'] != '') {
                            $error = $this->validarInfoLiquidacion($data['infoLiquidacion'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoLiquidacion no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoLiquidacion es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '04') { //NotaCredito
                    //validarInfoNotaCredito
                    if (isset($data['infoNotaCredito'])) {
                        if ($data['infoNotaCredito'] != '') {
                            $error = $this->validarInfoNotaCredito($data['infoNotaCredito'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaCredito no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaCredito es obligatorio';
                        $cont++;
                    }
                    //ValidarDetalle
                    if (isset($data['detalles'])) {
                        if ($data['detalles'] != '') {
                            $error = $this->validarDetalles($data['detalles'], $codDoc, $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '05') { //NotaDebito
                    //validarInfoNotaDebito
                    if (isset($data['infoNotaDebito'])) {
                        if ($data['infoNotaDebito'] != '') {
                            $error = $this->validarInfoNotaDebito($data['infoNotaDebito'], $cont, $error);
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaDebito no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoNotaDebito es obligatorio';
                        $cont++;
                    }
                } elseif ($codDoc == '07') { //Retencion
                    //validarInfoCompRetencion
                    if (isset($data['infoCompRetencion'])) {
                        if ($data['infoCompRetencion'] != '') {
                            $error = $this->validarInfoCompRetencion($data['infoCompRetencion'], $cont, $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo infoCompRetencion no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoCompRetencion es obligatorio';
                        $cont++;
                    }
                    //validar impuestos
                    if (isset($data['impuestos'])) {
                        if (count($data['impuestos']) > 0) {
                            $error = $this->validarImpuestos($data['impuestos'], $cont, $error);
                            $cont++;
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestos no puede ser vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo impuestos es obligatorio';
                        $cont++;
                    }
                }
                //validarInfoAdicional
                if (isset($data['infoAdicional'])) {
                    if ($data['infoAdicional'] != '') {
                        $this->validarInfoAdicional($data['infoAdicional'], $cont, $error);
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo infoAdicional no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo infoAdicional es obligatorio';
                    $cont++;
                }
            }
        } else {
            $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido es obligatorio';
            $cont++;
        }
        return $error;
    }
    private function validarInfoFactura($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar tipoIdentificacionComprador
            if (isset($data['tipoIdentificacionComprador'])) {
                if ($data['tipoIdentificacionComprador'] != '') {
                    if (strlen($data['tipoIdentificacionComprador']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe contener 2 caracteres (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionComprador'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe ser numerico (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionComprador'] != '04' && $data['tipoIdentificacionComprador'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador solo puede ser 04 o 05 (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador es obligatorio';
                $cont++;
            }
            //validar guiaRemision
            if (isset($data['guiaRemision'])) {
                if ($data['guiaRemision'] != '') {
                    if (strlen($data['guiaRemision']) != 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo guiaRemision debe contener 15 caracteres';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo guiaRemision no puede ser vacio';
                    $cont++;
                }
            }
            //validar razonSocial
            if (isset($data['razonSocialComprador'])) {
                if ($data['razonSocialComprador'] != '') {
                    if (strlen($data['razonSocialComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador es obligatorio';
                $cont++;
            }
            //validar identificacionComprador
            if (isset($data['identificacionComprador'])) {
                if ($data['identificacionComprador'] != '') {
                    if (strlen($data['identificacionComprador']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar direccionComprador
            if (isset($data['direccionComprador'])) {
                if ($data['direccionComprador'] != '') {
                    if (strlen($data['direccionComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo direccionComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo direccionComprador no puede ser vacio';
                    $cont++;
                }
            }
            //validar totalSinImpuestos
            if (isset($data['totalSinImpuestos'])) {
                if ($data['totalSinImpuestos'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['totalSinImpuestos']));
                    if (!ctype_digit($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['totalSinImpuestos']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos no puede ser vacio';
                    $cont++;
                }
            }
            //totalConImpuestos
            if (isset($data['totalConImpuestos'])) {
                if (count($data['totalConImpuestos']) > 0) {
                    $error = $this->validarTotalImpuestos($data['totalConImpuestos'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos debe contener al menos un registro';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos es obligatorio';
                $cont++;
            }
            //validar propina
            if (isset($data['propina'])) {
                if ($data['propina'] != '') {
                    //$digits = str_replace(array('.',','), array('',''), strrev($data['propina']));
                    if (!is_numeric($data['propina'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo propina debe ser numerico';
                        $cont++;
                    }
                    if (strlen($data['propina']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo propina debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo propina no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo propina es obligatorio';
                $cont++;
            }
            //validar importeTotal
            if (isset($data['importeTotal'])) {
                if ($data['importeTotal'] != '') {
                    if (!is_numeric($data['importeTotal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo propina debe ser numerico';
                        $cont++;
                    }
                    if (strlen($data['importeTotal']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo importeTotal es obligatorio';
                $cont++;
            }
            //validar Pagos
            if (isset($data['pagos'])) {
                if (count($data['pagos']) > 0) {
                    $error = $this->validarPagos($data['pagos'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo pagos es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarPagos($data, &$cont, $error)
    {
        try {
            $conPago = 0;
            foreach ($data as $pago) {
                //formaPago
                if (isset($pago['formaPago'])) {
                    if ($pago['formaPago'] != '') {
                        if (strlen($pago['formaPago']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' debe contener 2 caracteres (' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($pago['formaPago'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' debe ser numerico (' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                        if ($pago['formaPago'] != '01' && $pago['formaPago'] != '15' && $pago['formaPago'] != '16' && $pago['formaPago'] != '17' && $pago['formaPago'] != '18' && $pago['formaPago'] != '19' && $pago['formaPago'] != '20' && $pago['formaPago'] != '21') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' solo pueden ser una de siguientes opciones 01 - 15 - 16 - 17 - 18 - 19 - 20 - 21(' . $pago['formaPago'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo formaPago del Pago ' . ($conPago + 1) . ' es obligatorio';
                    $cont++;
                }
                //total
                if (isset($pago['total'])) {
                    if ($pago['total'] != '') {
                        if (!is_numeric($pago['total'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' debe ser numerico (' . $pago['total'] . ')';
                            $cont++;
                        }
                        if (strlen($pago['total']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' debe contener hasta 14 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo total del Pago ' . ($conPago + 1) . ' es obligatorio';
                    $cont++;
                }
                //plazo
                if (isset($pago['plazo'])) {
                    if ($pago['plazo'] != '') {
                        if (!is_numeric($pago['plazo'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo plazo del Pago ' . ($conPago + 1) . ' debe contener solo numeros';
                            $cont++;
                        }

                        if (strlen($pago['plazo']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo plazo del Pago ' . ($conPago + 1) . ' debe contener hasta 14 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo plazo del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //unidaadTiempo
                if (isset($pago['unidadTiempo'])) {
                    if ($pago['unidadTiempo'] != '') {
                        if (strlen($pago['unidadTiempo']) > 10) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo unidadTiempo del Pago ' . ($conPago + 1) . ' debe contener hasta 10 digitos maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo unidadTiempo del Pago ' . ($conPago + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                $conPago++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . ': ' . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarInfoGuiaRemision($data, &$cont, $error)
    {
        try {
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar razonSocialTransportista
            if (isset($data['razonSocialTransportista'])) {
                if ($data['razonSocialTransportista'] != '') {
                    if (strlen($data['razonSocialTransportista']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialTransportista es obligatorio';
                $cont++;
            }
            //validar dirPartida
            if (isset($data['dirPartida'])) {
                if ($data['dirPartida'] != '') {
                    if (strlen($data['dirPartida']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirPartida es obligatorio';
                $cont++;
            }
            //validar tipoIdentificacionTransportista
            if (isset($data['tipoIdentificacionTransportista'])) {
                if ($data['tipoIdentificacionTransportista'] != '') {
                    if (strlen($data['tipoIdentificacionTransportista']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista debe contener 2 caracteres (' . $data['tipoIdentificacionTransportista'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionTransportista'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista debe ser numerico (' . $data['tipoIdentificacionTransportista'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionTransportista'] != '04' && $data['tipoIdentificacionTransportista'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista solo puede ser 04 o 05 (' . $data['tipoIdentificacionTransportista'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionTransportista es obligatorio';
                $cont++;
            }
            //validar rucTransportista
            if (isset($data['rucTransportista'])) {
                if (strlen($data['rucTransportista']) != 10 && strlen($data['rucTransportista']) != 13) {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista debe contener 10 o 13 caracteres maximo (No. de caracteres ingresados ' . strlen($data['rucTransportista']) . ')';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo rucTransportista es obligatorio';
                $cont++;
            }
            //validar Rise
            if (isset($data['rise'])) {
                if ($data['rise'] != '') {
                    if (strlen($data['rise']) > 40) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo rise debe contener 40 caracteres (' . $data['rise'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rise no puede ser vacio';
                    $cont++;
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar fechaIniTransporte
            if (isset($data['fechaIniTransporte'])) {
                if ($data['fechaIniTransporte'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaIniTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no es valida (' . $data['fechaIniTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaIniTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no tiene el formato correcto dd/mm/yyyy (' . $data['fechaIniTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFechaInicio($data['fechaIniTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte debe ser igual o mayor a la fecha actual (' . $data['fechaIniTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaIniTransporte es obligatorio';
                $cont++;
            }
            //validar fechaFinTransporte
            if (isset($data['fechaFinTransporte'])) {
                if ($data['fechaFinTransporte'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no es valida (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no tiene el formato correcto dd/mm/yyyy (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFechaInicio($data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte debe ser igual o mayor a la fecha actual (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFechaFinal($data['fechaIniTransporte'], $data['fechaFinTransporte'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte debe ser igual o mayor a la fecha de inicio (' . $data['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaFinTransporte es obligatorio';
                $cont++;
            }
            //validar placa
            if (isset($data['placa'])) {
                if ($data['placa'] != '') {
                    if (strlen($data['placa']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo placa debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo placa no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo placa es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarDestinatarios($data, $codDoc, &$cont, $error)
    {
        try {
            foreach ($data as $destinatario) {
                if (isset($destinatario['identificacionDestinatario'])) {
                    if ($destinatario['identificacionDestinatario'] != '') {
                        if (strlen($destinatario['identificacionDestinatario']) > 20) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                    $cont++;
                }
                //validar razonSocialDestinatario
                if (isset($destinatario['razonSocialDestinatario'])) {
                    if ($destinatario['razonSocialDestinatario'] != '') {
                        if (strlen($destinatario['razonSocialDestinatario']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialDestinatario es obligatorio';
                    $cont++;
                }
                //validar dirDestinatario
                if (isset($destinatario['dirDestinatario'])) {
                    if ($destinatario['dirDestinatario'] != '') {
                        if (strlen($destinatario['dirDestinatario']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirDestinatario es obligatorio';
                    $cont++;
                }
                //Validar motivoTraslado
                if (isset($destinatario['motivoTraslado'])) {
                    if ($destinatario['motivoTraslado'] != '') {
                        if (strlen($destinatario['motivoTraslado']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo motivoTraslado es obligatorio';
                    $cont++;
                }
                //validar docAduaneroUnico
                if (isset($destinatario['docAduaneroUnico'])) {
                    if ($destinatario['docAduaneroUnico'] != '') {
                        if (strlen($destinatario['docAduaneroUnico']) > 20) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo docAduaneroUnico debe contener hasta 20 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo docAduaneroUnico no puede ser vacio';
                        $cont++;
                    }
                }
                //validar codEstabDestino
                if (isset($destinatario['codEstabDestino'])) {
                    if ($destinatario['codEstabDestino'] != '') {
                        if (strlen($destinatario['codEstabDestino']) != 3) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino debe contener 3 caracteres';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['codEstabDestino'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino debe ser numerico (' . $destinatario['codEstabDestino'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codEstabDestino no puede ser vacio';
                        $cont++;
                    }
                }
                //validar ruta
                if (isset($destinatario['ruta'])) {
                    if ($destinatario['ruta'] != '') {
                        if (strlen($destinatario['ruta']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta debe contener hasta 300 caracteres maximo';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta no puede ser vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo ruta no puede ser vacio';
                    $cont++;
                }
                //validar codDocSustento
                if (isset($destinatario['codDocSustento'])) {
                    if ($destinatario['codDocSustento'] != '') {
                        if (strlen($destinatario['codDocSustento']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe contener 2 caracteres';
                            $cont++;
                        }
                        if ($destinatario['codDocSustento'] != '01' && $destinatario['codDocSustento'] != '03' && $destinatario['codDocSustento'] != '04' && $data['infoTributaria']['codDoc'] != '05' && $destinatario['codDocSustento'] != '06' && $destinatario['codDocSustento'] != '07') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $destinatario['codDocSustento'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['codDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe ser numerico (' . $destinatario['codDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar numDocSustento
                if (isset($destinatario['numDocSustento'])) {
                    if ($destinatario['numDocSustento'] != '') {
                        $destinatario['numDocSustento'] = str_replace('-', '', $destinatario['numDocSustento']);
                        if (strlen($destinatario['numDocSustento']) != 15) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento debe contener 15 caracteres (No. de caracteres ' . strlen($destinatario['numDocSustento']) . ')';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['numDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento debe ser numerico (' . $destinatario['numDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //Validar numAutDocSustento
                if (isset($destinatario['numAutDocSustento'])) {
                    if ($destinatario['numAutDocSustento'] != '') {
                        if (strlen($destinatario['numAutDocSustento']) != 10 && strlen($destinatario['numAutDocSustento']) != 37 && strlen($destinatario['numAutDocSustento']) != 49) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe contener 10, 37 o 49 caracteres (numero de caracteres ingresados ' . strlen($destinatario['numAutDocSustento']) . ')';
                            $cont++;
                        }
                        if (!is_numeric($destinatario['numAutDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento debe ser numerico (' . $destinatario['numAutDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numAutDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar fechaEmisionDocSustento
                if (isset($destinatario['fechaEmisionDocSustento'])) {
                    if ($destinatario['fechaEmisionDocSustento'] != '') {
                        $nfecha = $this->getdate(str_replace('/', '-', $destinatario['fechaEmisionDocSustento']));
                        $destinatario['fechaEmisionDocSustento'] = $nfecha;
                        if (!$this->validarFecha($nfecha)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no tiene el formato correcto dd/mm/yyyy (' . $destinatario['fechaEmisionDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no puede ser vacio';
                        $cont++;
                    }
                }
                //validar detalles
                if (isset($destinatario['detalles'])) {
                    if (count($destinatario['detalles']) == 0) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles no puede estar vacio';
                        $cont++;
                    } else {
                        $error = $this->validarDetalles($destinatario['detalles'], $codDoc, $cont, $error);
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detalles es obligatorio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoNotaCredito($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar tipoIdentificacionComprador
            if (isset($data['tipoIdentificacionComprador'])) {
                if ($data['tipoIdentificacionComprador'] != '') {
                    if (strlen($data['tipoIdentificacionComprador']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe contener 2 caracteres (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionComprador'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe ser numerico (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionComprador'] != '04' && $data['tipoIdentificacionComprador'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador solo puede ser 04 o 05 (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador es obligatorio';
                $cont++;
            }
            //validar razonSocial
            if (isset($data['razonSocialComprador'])) {
                if ($data['razonSocialComprador'] != '') {
                    if (strlen($data['razonSocialComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador es obligatorio';
                $cont++;
            }
            //validar identificacionComprador
            if (isset($data['identificacionComprador'])) {
                if ($data['identificacionComprador'] != '') {
                    if (strlen($data['identificacionComprador']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar rise
            if (isset($data['rise'])) {
                if ($data['rise'] != '') {
                    if (strlen($data['rise']) > 40) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo rise debe contener hasta 40 caracteres';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo rise no puede ser vacio';
                    $cont++;
                }
            }
            //validar codDocModificado
            if (isset($data['codDocModificado'])) {
                if ($data['codDocModificado'] != '') {
                    if (strlen($data['codDocModificado']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe contener 2 caracteres (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['codDocModificado'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe ser numerico (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if ($data['codDocModificado'] != '01' && $data['codDocModificado'] != '03' && $data['codDocModificado'] != '04' && $data['codDocModificado'] != '05' && $data['codDocModificado'] != '06' && $data['codDocModificado'] != '07') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado es obligatorio';
                $cont++;
            }

            //validar numDocModificado
            if (isset($data['numDocModificado'])) {
                if ($data['numDocModificado'] != '') {
                    if (strlen($data['numDocModificado']) != 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado debe contener 15 caracteres (' . $data['numDocModificado'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['numDocModificado'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado debe ser numerico (' . $data['numDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado es obligatorio';
                $cont++;
            }

            //validar fechaEmisionDocSustento
            if (isset($data['fechaEmisionDocSustento'])) {
                if ($data['fechaEmisionDocSustento'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmisionDocSustento'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['infoFactura']['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmisionDocSustento'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento es obligatorio';
                $cont++;
            }

            //validar totalSinImpuestos
            if (isset($data['totalSinImpuestos'])) {
                if ($data['totalSinImpuestos'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['totalSinImpuestos']));
                    if (!is_numeric($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['totalSinImpuestos']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalSinImpuestos no puede ser vacio';
                    $cont++;
                }
            }
            //validar valorModificacion
            if (isset($data['valorModificacion'])) {
                if ($data['valorModificacion'] != '') {
                    $digits = str_replace(array('.', ','), array('', ''), strrev($data['valorModificacion']));
                    if (!is_numeric($digits)) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorModificacion debe contener solo numeros';
                        $cont++;
                    }
                    if (strlen($data['valorModificacion']) > 14) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorModificacion debe contener hasta 14 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valorModificacion no puede ser vacio';
                    $cont++;
                }
            }
            //totalConImpuestos
            if (isset($data['totalConImpuestos'])) {
                if (count($data['totalConImpuestos']) > 0) {
                    $error = $this->validarTotalImpuestos($data['totalConImpuestos'], $cont, $error);
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos debe contener al menos un registro';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo totalConImpuestos es obligatorio';
                $cont++;
            }
            //validar motivo
            if (isset($data['motivo'])) {
                if ($data['motivo'] != '') {
                    if (strlen($data['motivo']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo motivo debe contener hasta 300 digitos maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo motivo no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo motivo es obligatorio';
                $cont++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoNotaDebito($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {

                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar tipoIdentificacionComprador
            if (isset($data['tipoIdentificacionComprador'])) {
                if ($data['tipoIdentificacionComprador'] != '') {
                    if (strlen($data['tipoIdentificacionComprador']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe contener 2 caracteres (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionComprador'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador debe ser numerico (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionComprador'] != '04' && $data['tipoIdentificacionComprador'] != '05') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador solo puede ser 04 o 05 (' . $data['tipoIdentificacionComprador'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionComprador es obligatorio';
                $cont++;
            }
            //validar razonSocial
            if (isset($data['razonSocialComprador'])) {
                if ($data['razonSocialComprador'] != '') {
                    if (strlen($data['razonSocialComprador']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialComprador es obligatorio';
                $cont++;
            }
            //validar identificacionComprador
            if (isset($data['identificacionComprador'])) {
                if ($data['identificacionComprador'] != '') {
                    if (strlen($data['identificacionComprador']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionComprador no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionDestinatario es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar codDocModificado
            if (isset($data['codDocModificado'])) {
                if ($data['codDocModificado'] != '') {
                    if (strlen($data['codDocModificado']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe contener 2 caracteres (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['codDocModificado'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado debe ser numerico (' . $data['codDocModificado'] . ')';
                        $cont++;
                    }
                    if ($data['codDocModificado'] != '01' && $data['codDocModificado'] != '03' && $data['codDocModificado'] != '04' && $data['infoNotaCredito']['codDocModificado'] != '05' && $data['infoNotaCredito']['codDocModificado'] != '06' && $data['infoNotaCredito']['codDocModificado'] != '07') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $data['infoNotaCredito']['codDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocModificado es obligatorio';
                $cont++;
            }
            //validar numDocModificado
            if (isset($data['numDocModificado'])) {
                if ($data['numDocModificado'] != '') {
                    if (strtoupper($data['numDocModificado']) != 'SI' && strtoupper($data['numDocModificado']) != 'NO') {
                        $data['numDocModificado'] = strtoupper($data['numDocModificado']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado Solo puiede ser SI o NO (' . $data['numDocModificado'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocModificado no puede ser vacio';
                    $cont++;
                }
            }
            //validar moneda
            if (isset($data['moneda'])) {
                if ($data['moneda'] != '') {
                    if (strlen($data['moneda']) > 15) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda debe contener max 15 caracteres (' . $data['moneda'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo moneda no puede ser vacio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoCompRetencion($data, &$cont, $error)
    {
        try {
            //fechaEmision
            if (isset($data['fechaEmision'])) {
                if ($data['fechaEmision'] != '') {
                    if (!$this->validarFechaCorrecta($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no es valida (' . $data['fechaEmision'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['fechaEmision'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no tiene el formato correcto dd/mm/yyyy (' . $data['infoGuiaRemision']['fechaFinTransporte'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmision es obligatorio';
                $cont++;
            }
            //validar dirEstablecimiento
            if (isset($data['dirEstablecimiento'])) {
                if ($data['dirEstablecimiento'] != '') {
                    if (strlen($data['dirEstablecimiento']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo dirEstablecimiento es obligatorio';
                $cont++;
            }
            //validar contribuyenteEspecial
            if (isset($data['contribuyenteEspecial'])) {
                if ($data['contribuyenteEspecial'] != '') {
                    if (strlen($data['contribuyenteEspecial']) >= 3 && strlen($data['contribuyenteEspecial']) <= 13) {
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo contribuyenteEspecial debe contener entre 3 y 13 caracteres (' . $data['contribuyenteEspecial'] . ')';
                        $cont++;
                    }
                }
            }
            //validar obligadoContabilidad
            if (isset($data['obligadoContabilidad'])) {
                if ($data['obligadoContabilidad'] != '') {
                    if (strtoupper($data['obligadoContabilidad']) != 'SI' && strtoupper($data['obligadoContabilidad']) != 'NO') {
                        $data['obligadoContabilidad'] = strtoupper($data['obligadoContabilidad']);
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad Solo puiede ser SI o NO (' . $data['obligadoContabilidad'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo obligadoContabilidad no puede ser vacio';
                    $cont++;
                }
            }
            //validar tipoIdentificacionSujetoRetenido
            if (isset($data['tipoIdentificacionSujetoRetenido'])) {
                if ($data['tipoIdentificacionSujetoRetenido'] != '') {
                    if (strlen($data['tipoIdentificacionSujetoRetenido']) != 2) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido debe contener 2 caracteres (' . $data['tipoIdentificacionSujetoRetenido'] . ')';
                        $cont++;
                    }
                    if (!is_numeric($data['tipoIdentificacionSujetoRetenido'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido debe ser numerico (' . $data['tipoIdentificacionSujetoRetenido'] . ')';
                        $cont++;
                    }
                    if ($data['tipoIdentificacionSujetoRetenido'] != '04' && $data['tipoIdentificacionSujetoRetenido'] != '05' && $data['tipoIdentificacionSujetoRetenido'] != '06' && $data['tipoIdentificacionSujetoRetenido'] != '08') {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido solo puede ser 04, 05, 06, o 08 (' . $data['tipoIdentificacionSujetoRetenido'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo tipoIdentificacionSujetoRetenido es obligatorio';
                $cont++;
            }
            //validar razonSocialSujetoRetenido
            if (isset($data['razonSocialSujetoRetenido'])) {
                if ($data['razonSocialSujetoRetenido'] != '') {
                    if (strlen($data['razonSocialSujetoRetenido']) > 300) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialSujetoRetenido debe contener hasta 300 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialSujetoRetenido no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo razonSocialSujetoRetenido es obligatorio';
                $cont++;
            }
            //validar identificacionSujetoRetenido
            if (isset($data['identificacionSujetoRetenido'])) {
                if ($data['identificacionSujetoRetenido'] != '') {
                    if (strlen($data['identificacionSujetoRetenido']) > 20) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido debe contener hasta 20 caracteres maximo';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido no puede ser vacio';
                    $cont++;
                }
            } else {
                $error[$cont] = "Error " . ($cont + 1) . ': el campo identificacionSujetoRetenido es obligatorio';
                $cont++;
            }
            //periodoFiscal
            if (isset($data['periodoFiscal'])) {
                if ($data['periodoFiscal'] != '') {
                    if (!$this->validarFechaCorrecta($data['periodoFiscal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo periodoFiscal no es valida (' . $data['periodoFiscal'] . ')';
                        $cont++;
                    }
                    if (!$this->validarFecha($data['periodoFiscal'])) {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo periodoFiscal no tiene el formato correcto dd/mm/yyyy (' . $data['periodoFiscal'] . ')';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo periodoFiscal no puede ser vacio';
                    $cont++;
                }
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarTotalImpuestos($data, &$cont, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $impuesto) {
                //codigo
                if (isset($impuesto['codigo'])) {
                    if ($impuesto['codigo'] != '') {
                        $codigo = $impuesto['codigo'];
                        if ($impuesto['codigo'] != '2' && $impuesto['codigo'] != '3' && $impuesto['codigo'] != '5') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' debe ser 1,2 o 6';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoPorcentaje
                if (isset($impuesto['codigoPorcentaje'])) {
                    if ($impuesto['codigoPorcentaje'] != '') {
                        $codigoPorcentaje = $impuesto['codigoPorcentaje'];
                        if (strlen($impuesto['codigoPorcentaje']) < 1 || strlen($impuesto['codigoPorcentaje']) > 5) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' debe contener entre 1 y 5 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //baseImponible
                if (isset($impuesto['baseImponible'])) {
                    if ($impuesto['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valor
                if (isset($impuesto['valor'])) {
                    if ($impuesto['valor'] != '') {
                        if (!is_numeric($impuesto['valor'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valor del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //validar Informacion impuesto
                if ($codigo != '' && $codigoPorcentaje != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo_impuesto', $codigo)->where('codigo', $codigoPorcentaje)->first();

                    if ($deCodigo != '') {
                        if ($codigoPorcentaje != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no puede ser vacio';
                            $cont++;
                        }

                        if ($codigo != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPorcentaje del impuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigoPorcentaje (' . $codigoPorcentaje . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . ': ' . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarImpuestos($data, &$cont, $error)
    {
        try {
            $conImp = 0;
            foreach ($data as $impuesto) {
                //codigo
                if (isset($impuesto['codigo'])) {
                    if ($impuesto['codigo'] != '') {
                        $codigo = $impuesto['codigo'];
                        if ($impuesto['codigo'] != '1' && $impuesto['codigo'] != '2' && $impuesto['codigo'] != '6') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' debe ser 1,2 o 6';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //codigoRetencion
                if (isset($impuesto['codigoRetencion'])) {
                    if ($impuesto['codigoRetencion'] != '') {
                        $codigoRetencion = $impuesto['codigoRetencion'];
                        if (strlen($impuesto['codigoRetencion']) < 1 || strlen($impuesto['codigoRetencion']) > 5) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion del impuesto ' . ($conImp + 1) . ' debe contener entre 1 y 5 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //baseImponible
                if (isset($impuesto['baseImponible'])) {
                    if ($impuesto['baseImponible'] != '') {
                        $digitsCod = str_replace(array('.', ','), array('', ''), strrev($impuesto['baseImponible']));
                        if (!is_numeric($digitsCod)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo baseImponible del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //porcentajeRetener
                if (isset($impuesto['porcentajeRetener'])) {
                    if ($impuesto['porcentajeRetener'] != '') {
                        if (!is_numeric($impuesto['porcentajeRetener'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        // if (strlen($impuesto['porcentajeRetener']) < 1 || strlen($impuesto['porcentajeRetener']) > 3) {
                        //     
                        //     $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener debe contener entre 1 y 3 caracteres';
                        //     $cont++;
                        // }
                        $porcentajeRetener = $impuesto['porcentajeRetener'];
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //valorRetenido
                if (isset($impuesto['valorRetenido'])) {
                    if ($impuesto['valorRetenido'] != '') {
                        if (!is_numeric($impuesto['valorRetenido'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo valorRetenido del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }

                //validar Informacion impuesto
                if ($codigo != '' && $codigoRetencion != '' && $porcentajeRetener != '') {
                    $deCodigo = De_Codigo_Impuestos::where('codigo', $codigoRetencion)->first();
                    if ($deCodigo != '') {
                        if ($porcentajeRetener != $deCodigo->porcentaje_retencion) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo porcentajeRetener. (' . $porcentajeRetener . ') del impuesto ' . ($conImp + 1) . ' no corresponde al porcentajeRetener (' . $deCodigo->porcentaje_retencion . ') del impuesto';
                            $cont++;
                        }

                        if ($codigoRetencion != $deCodigo->codigo) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoRetencion del impuesto ' . ($conImp + 1) . ' no corresponde al codigoRetencion del impuesto';
                            $cont++;
                        }

                        if ($codigo != $deCodigo->codigo_impuesto) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigo del impuesto ' . ($conImp + 1) . ' no corresponde al codigo del impuesto';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el codigo (' . $codigoRetencion . ') del impuesto ' . ($conImp + 1) . ' se encuentra desactualizado o es incorrecto';
                        $cont++;
                    }
                }
                //codDocSustento
                if (isset($impuesto['codDocSustento'])) {
                    if ($impuesto['codDocSustento'] != '') {
                        if (strlen($impuesto['codDocSustento']) != 2) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento debe contener 2 caracteres (' . $impuesto['codDocSustento'] . ')';
                            $cont++;
                        }
                        if (!is_numeric($impuesto['codDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                        if ($impuesto['codDocSustento'] != '01' && $impuesto['codDocSustento'] != '03' && $impuesto['codDocSustento'] != '04' && $impuesto['codDocSustento'] != '05' && $impuesto['codDocSustento'] != '06' && $impuesto['codDocSustento'] != '07') {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento solo pueden ser una de siguientes opciones 01 - 03 - 04 - 05 - 06 - 07 (' . $impuesto['codDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo codDocSustento del impuesto ' . ($conImp + 1) . ' es obligatorio';
                    $cont++;
                }
                //numDocSustento
                if (isset($impuesto['numDocSustento'])) {
                    if ($impuesto['numDocSustento'] != '') {
                        if (!is_numeric($impuesto['numDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento del impuesto ' . ($conImp + 1) . ' debe contener solo numeros';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                //fechaEmisionDocSustento
                if (isset($impuesto['fechaEmisionDocSustento'])) {
                    if ($impuesto['fechaEmisionDocSustento'] != '') {
                        if (!$this->validarFechaCorrecta($impuesto['fechaEmisionDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no es valida (' . $impuesto['fechaEmisionDocSustento'] . ')';
                            $cont++;
                        }
                        if (!$this->validarFecha($impuesto['fechaEmisionDocSustento'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo fechaEmisionDocSustento no tiene el formato correcto dd/mm/yyyy (' . $impuesto['fechaEmisionDocSustento'] . ')';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo numDocSustento del impuesto ' . ($conImp + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                $conImp++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function validarDetalles($data, $codDoc, &$cont, $error)
    {
        try {
            $contDeta = 0;
            foreach ($data as $detalle) {
                if ($codDoc == '01' || $codDoc == '03') {
                    //validarCodigoPrincipal
                    if (isset($detalle['codigoPrincipal'])) {
                        if ($detalle['codigoPrincipal'] != '') {
                            if (strlen($detalle['codigoPrincipal']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPrincipal del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoPrincipal del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //validarCodigoAuxiliar
                    if (isset($detalle['codigoAuxiliar'])) {
                        if ($detalle['codigoAuxiliar'] != '') {
                            if (strlen($detalle['codigoAuxiliar']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAuxiliar del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAuxiliar del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAuxiliar del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                } else {
                    //validarCodigoInterno
                    if (isset($detalle['codigoInterno'])) {
                        if ($detalle['codigoInterno'] != '') {
                            if (strlen($detalle['codigoInterno']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoInterno del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoInterno del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    }
                    //validarCodigoAdicional
                    if (isset($detalle['codigoAdicional'])) {
                        if ($detalle['codigoAdicional'] != '') {
                            if (strlen($detalle['codigoAdicional']) > 25) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle ' . ($contDeta + 1) . ' puede tener maximo 25 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo codigoAdicional del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                }

                //validarDescripcion
                if (isset($detalle['descripcion'])) {
                    if ($detalle['descripcion'] != '') {
                        if (strlen($detalle['descripcion']) > 300) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle ' . ($contDeta + 1) . ' puede tener maximo 300 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo descripcion del detalle ' . ($contDeta + 1) . ' es obligatorio';
                    $cont++;
                }
                //validarCantidad
                if (isset($detalle['cantidad'])) {
                    if ($detalle['cantidad'] != '') {
                        if (!is_numeric($detalle['cantidad'])) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                            $cont++;
                        }
                        if (strlen($detalle['cantidad']) > 14) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo cantidad del detalle ' . ($contDeta + 1) . ' es obligatorio';
                    $cont++;
                }

                if ($codDoc != 06) {
                    //validarPrecioUnitario
                    if (isset($detalle['precioUnitario'])) {
                        if ($detalle['precioUnitario'] != '') {
                            if (!is_numeric($detalle['precioUnitario'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['precioUnitario']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo precioUnitario del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                    //validarDescuento
                    if (isset($detalle['descuento'])) {
                        if ($detalle['descuento'] != '') {
                            if (!is_numeric($detalle['descuento'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['descuento']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo descuento del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                    //validarPrecioTotalSinImpuesto
                    if (isset($detalle['precioTotalSinImpuesto'])) {
                        if ($detalle['precioTotalSinImpuesto'] != '') {
                            if (!is_numeric($detalle['precioTotalSinImpuesto'])) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' solo puede contener numeros';
                                $cont++;
                            }
                            if (strlen($detalle['precioTotalSinImpuesto']) > 14) {
                                $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' puede tener maximo 14 caracteres';
                                $cont++;
                            }
                        } else {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo precioTotalSinImpuesto del detalle ' . ($contDeta + 1) . ' es obligatorio';
                        $cont++;
                    }
                }
                //validarDetallesAdicionales
                if (isset($detalle['detallesAdicionales'])) {
                    if (count($detalle['detallesAdicionales']) > 0) {
                        $error = $this->validarDetAdicionales($detalle['detallesAdicionales'], $cont, $error);
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campo detallesAdicionales del detalle ' . ($contDeta + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                } else {
                    $error[$cont] = "Error " . ($cont + 1) . ': el campo detallesAdicionales del detalle ' . ($contDeta + 1) . ' es obligatorio';
                    $cont++;
                }
                $contDeta++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarDetAdicionales($data, &$cont, $error)
    {
        try {
            $contAdi = 0;
            foreach ($data as $detAdicional) {
                if (isset($detAdicional['nombre'])) {
                    if ($detAdicional['nombre'] != '') {
                        if (strlen($detAdicional['nombre'] > 100)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional nombre ' . ($contAdi + 1) . ' debe tener maximo 100 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional nombre ' . ($contAdi + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                    if ($detAdicional['valor'] != '') {
                        if (strlen($detAdicional['valor'] > 300)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional valor ' . ($contAdi + 1) . ' debe tener maximo 300 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el detAdicional valor ' . ($contAdi + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                }
                $contAdi++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    public function validarInfoAdicional($data, &$cont, $error)
    {
        try {
            $contAdi = 0;
            foreach ($data as $campoAdicional) {
                if (isset($campoAdicional['nombre'])) {
                    if ($campoAdicional['nombre'] != '') {
                        if (strlen($campoAdicional['nombre'] > 100)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campoAdicional nombre ' . ($contAdi + 1) . ' debe tener maximo 100 caracteres';
                            $cont++;
                        }
                    } else {
                        $error[$cont] = "Error " . ($cont + 1) . ': el campoAdicional nombre ' . ($contAdi + 1) . ' no puede estar vacio';
                        $cont++;
                    }
                    if ($campoAdicional['valor'] != '') {
                        if (strlen($campoAdicional['valor'] > 300)) {
                            $error[$cont] = "Error " . ($cont + 1) . ': el campoAdicional valor ' . ($contAdi + 1) . ' debe tener maximo 300 caracteres';
                            $cont++;
                        }
                    }
                }
                $contAdi++;
            }
        } catch (Exception $ex) {
            $error[$cont] = "Error " . ($cont + 1) . $ex->getMessage();
            return $error;
        }
        return $error;
    }
    private function getDate($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('d/m/Y', strtotime($valor));
        return $result;
    }
    public function getDatePeriodoFiscal($valor)
    {
        if (trim($valor) == "") return "";
        $result = date('m/Y', strtotime($valor));
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
        $codDoc = $campos['infoTributaria']['codDoc'];
        $nodoDetalle = $xmlDocument->createElement('detalles');
        $detalles = $campos["detalles"];
        foreach ($detalles as $detalle) {
            $precioTotalSinImpuesto = $detalle["cantidad"] * $detalle["precioUnitario"];
            $item = $xmlDocument->createElement('detalle');
            if ($codDoc == '01' || $codDoc == '03') {
                $item->appendChild($xmlDocument->createElement('codigoPrincipal', $detalle["codigoPrincipal"]));
                $item->appendChild($xmlDocument->createElement('codigoAuxiliar', $detalle["codigoAuxiliar"]));
            } elseif ($codDoc == '04' || $codDoc == '06') {
                $item->appendChild($xmlDocument->createElement('codigoInterno', $detalle["codigoInterno"]));
                $item->appendChild($xmlDocument->createElement('codigoAdicional', $detalle["codigoAdicional"]));
            }
            $item->appendChild($xmlDocument->createElement('descripcion', str_replace('&', 'y', $detalle["descripcion"])));
            $item->appendChild($xmlDocument->createElement('cantidad', $this->getDecimal($detalle["cantidad"])));
            $item->appendChild($xmlDocument->createElement('precioUnitario', $this->getDecimal($detalle["precioUnitario"])));
            $item->appendChild($xmlDocument->createElement('descuento', $this->getDecimal($detalle["descuento"])));
            $item->appendChild($xmlDocument->createElement('precioTotalSinImpuesto', $this->getDecimal($precioTotalSinImpuesto)));
            if (count($detalle["detallesAdicionales"]) > 0) {
                $detallesAdicionales = $this->getDetallesAdicionales($xmlDocument, $detalle["detallesAdicionales"]);
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
            else
                $sri_tipo_impuesto_iva_id = 2;
            $impuestoIva = De_Codigo_Impuestos::where('id', $sri_tipo_impuesto_iva_id)->first();
            $baseImponible = $detalle["cantidad"] * $detalle["precioUnitario"];
            $impuesto = ($baseImponible * $impuestoIva->porcentaje) / 100;
            $item->appendChild($xmlDocument->createElement('codigo', $impuestoIva->codigo_impuesto));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $impuestoIva->codigo));
            $item->appendChild($xmlDocument->createElement('tarifa', $impuestoIva->porcentaje));
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
        $detalles = array();
        $valor = '';
        $cantidadInfoAdicional = 0;
        $informacionAdicional = $campos['infoAdicional'];

        if (count($informacionAdicional) > 0) {
            $cantidadInfoAdicional = count($informacionAdicional);
        }

        for ($i = 0; $i < $cantidadInfoAdicional; $i++) {
            if (isset($informacionAdicional[$i]['nombre']))
                $nombre = $informacionAdicional[$i]['nombre'];
            if (isset($informacionAdicional[$i]['valor']))
                $valor = $informacionAdicional[$i]['valor'];
            array_push($detalles, array("nombre" => $nombre, "valor" => str_replace(' ', '', str_replace('(', '', str_replace(')', '', str_replace('-', '', $valor))))));
            $nodoDetalle = $xmlDocument->createElement('infoAdicional');
        }
        foreach ($detalles as $item) {
            $campoAdicional = $xmlDocument->createElement('campoAdicional', isset($item["valor"]) ? str_replace("'", "", str_replace('&', '&amp;', $item["valor"])) : 'NN');
            $campoAdicional->setAttribute('nombre', $item["nombre"]);
            $nodoDetalle->appendChild($campoAdicional);
        }
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
        elseif ($campos['infoTributaria']['codDoc'] == 03)
            $this->tipoDocumento = 'liquidacionCompra';
        elseif ($campos['infoTributaria']['codDoc'] == 04)
            $this->tipoDocumento = 'notaCredito';
        elseif ($campos['infoTributaria']['codDoc'] == 05)
            $this->tipoDocumento = 'notaDebito';
        elseif ($campos['infoTributaria']['codDoc'] == 06)
            $this->tipoDocumento = 'guiaRemision';
        elseif ($campos['infoTributaria']['codDoc'] == 07)
            $this->tipoDocumento = 'comprobanteRetencion';

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
        } else if ($this->tipoDocumento == 'notaCredito') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoNotaCredito = $this->getInfoNotaCredito($xml, $campos);
            $root->appendChild($infoNotaCredito);
            $detalles = $this->getDetalles($xml, $campos);
            $root->appendChild($detalles);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'notaDebito') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoNotaDebito = $this->getInfoNotaDebito($xml, $campos);
            $root->appendChild($infoNotaDebito);
            $motivos = $this->getMotivos($xml, $campos);
            $root->appendChild($motivos);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'comprobanteRetencion') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.0.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoCompRetencion = $this->getInfoCompRetencion($xml, $campos);
            $root->appendChild($infoCompRetencion);
            $impuestos = $this->getImpuestosRetencion($xml, $campos);
            $root->appendChild($impuestos);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else if ($this->tipoDocumento == 'liquidacionCompra') {
            $root = $xml->createElement($this->tipoDocumento);
            $root->setAttribute('id', 'comprobante');
            $root->setAttribute('version', '1.1.0');
            $infoTributaria = $this->getInfoTributaria($xml, $campos);
            $root->appendChild($infoTributaria);
            $infoLiquidacionCompra = $this->getInfoLiquidacionCompra($xml, $campos);
            $root->appendChild($infoLiquidacionCompra);
            $reembolsos = $this->getReembolsos($xml, $campos);
            $root->appendChild($reembolsos);
            $maquinaFiscal = $this->getMaquinaFiscal($xml, $campos);
            $root->appendChild($maquinaFiscal);
            $infoAdicional = $this->getInfoAdicional($xml, $campos);
            $root->appendChild($infoAdicional);
        } else {
        }
        if ($this->tipoDocumento != '') {
            //Guarda XML
            $xml->appendChild($root);
            $xml->formatOutput = true;
            $comprobante = $xml->saveXML();
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
            mkdir($ruta, 777, true);
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
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/ComprobanteRetencion_V1.0.0.xsd';
                    break;
                case 'notaCredito': //1.1.0
                    $xsd = base_path() . '/storage/app/facturaelectronica/SriXsd/NotaCredito_V1.1.0.xsd';
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
        if ($dir != '') {
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
        return $result;
    }
    public function htmlspecial($valor)
    {
        $result = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        return $result;
    }
    public function recibirWs($comprobante, $tipoAmbiente = 1)
    {
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
        $client = new SoapClient($url);
        $result = $client->validarComprobante($params);
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
            echo '<pre>';
            print_r($xmlRespuesta);
            exit;
            if ($estado == 'DEVUELTA') {
                $mensajes = $xmlRespuesta['mensajesWs'];
                $estado = $xml->createElement('estado', $xmlRespuesta['estado']);
                $root->appendChild($estado);
                $nodoComprobantes = $xml->createElement('comprobantes');
                $comprobantes = $xmlRespuesta['comprobantes'];
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
            return json_encode(['Error: ' => $ex->getMessage(), 'Linea: ' => $ex->getLine()]);
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
    public function autorizacion_sri($claveAcceso, $ambiente = 1)
    {
        ini_set('max_execution_time', 300);
        sleep(3);

        switch ($ambiente) {
            case 1:
                //$url = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl"; //Pruebas Autorizacioin de Documentos
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
                break;
            case 2:
                //$url="https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl"; //Prodccion Atorizacion de Documentos
                $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
                break;
        }


        $client = new SoapClient($url);
        $param = array(
            'claveAccesoComprobante' => $claveAcceso
        );
        return $client->autorizacionComprobante($param);
    }
    public function generarXmlAutorizacion($xmlRespuesta)
    {
        $nombreArchivo = "noName";
        $estado = "sin_procesar";
        try {
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

                if (!is_null($autorizacion->mensajes)) {
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
            return $estado;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
            return;
        }
    }
    public function generarXmlNoAutorizado($data)
    {
        $nombreArchivo = "noName";
        $estado = "sin procesar";
        try {
            $xmlRespuesta = simplexml_load_string($data);
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $root = $xml->createElement('RespuestaAutorizacionComprobante');
            $clave = $xml->createElement('claveAccesoConsultada', $xmlRespuesta->claveAccesoConsultada);
            $root->appendChild($clave);
            $nombreArchivo = $xmlRespuesta->claveAccesoConsultada;
            $numeroComprobante = $xml->createElement('numeroComprobantes', $xmlRespuesta->numeroComprobantes);
            $root->appendChild($numeroComprobante);
            $nodoAutorizaciones = $xml->createElement('autorizaciones');
            $autorizaciones = $xmlRespuesta->autorizaciones;

            foreach ($autorizaciones as $autorizacion) {
                $nodoAutorizacion = $xml->createElement('autorizacion');
                $estado = $autorizacion->autorizacion->estado;
                $nodoAutorizacion->appendChild($xml->createElement('estado', $autorizacion->autorizacion->estado));
                $nodoAutorizacion->appendChild($xml->createElement('fechaAutorizacion', $autorizacion->autorizacion->fechaAutorizacion));
                $nodoAutorizacion->appendChild($xml->createElement('ambiente', $autorizacion->autorizacion->ambiente));
                $nodoAutorizacion->appendChild($xml->createElement('comprobante', $autorizacion->autorizacion->comprobante));
                $mensajes = $autorizacion->autorizacion->mensajes;
                if (count($mensajes) > 0) {
                    $nodoMensajes = $this->getMensajesAutorizacion($xml, $mensajes->mensaje);
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
            $r_ = base_path() . '/storage/app/facturaelectronica/respuestaSri/Autorizacion' . '/';
            $this->crearcarpeta($r_);
            $ruta = $r_ . $nombreArchivo . '_' . $estado . '.xml';
            $xml->save($ruta);
            return $comprobante;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            echo '<br/>' . $ex->getLine();
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
    public function generarPdf($xml, $accion = '', $tipo = '')
    {
        $docCod = substr($xml, 8, 2);
        $clave = $xml;
        $xml = base_path() . '/storage/app/facturaelectronica/respuestaSri/autorizacion/' . $clave . '_AUTORIZADO.xml';
        if ($docCod == '01')
            $pdf = 'sri_electronico.documentosgenerados.view_facturapdf';
        elseif ($docCod == '03')
            $pdf = 'sri_electronico.documentosgenerados.';
        elseif ($docCod == '04')
            $pdf = 'sri_electronico.documentosgenerados.';
        elseif ($docCod == '05')
            $pdf = 'sri_electronico.documentosgenerados.';
        elseif ($docCod == '06')
            $pdf = 'sri_electronico.documentosgenerados.view_guiaremisionpdf';
        elseif ($docCod = '07')
            $pdf = 'sri_electronico.documentosgenerados.view_compRetencionPDF';

        if (file_exists($xml)) {
            $data['xml'] = $xml;
            $view = \Illuminate\Support\Facades\View::make($pdf, $data)->render();
            $pdf = \Illuminate\Support\Facades\App::make('dompdf.wrapper');
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
            echo json_encode(['Error: ' => $e->getMessage(), 'Linea: ' => $e->getLine()]);
        }
    }
    public function getInfoFactura($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoFactura');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate(isset($campos['infoFactura']['fecha_emision']) ? $campos['infoFactura']['fecha_emision'] : $campos['infoFactura']['fechaEmision'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoFactura']['dirEstablecimiento']));
        if ($campos['infoFactura']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoFactura']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoFactura']['obligadoContabilidad']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoFactura']['tipoIdentificacionComprador']));
        if ($campos['infoFactura']['guiaRemision']) $nodoDetalle->appendChild($xmlDocument->createElement('guiaRemision', $campos['infoFactura']['guiaRemision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoFactura']['razonSocialComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoFactura']['identificacionComprador']));
        if ($campos['infoFactura']['direccionComprador']) $nodoDetalle->appendChild($xmlDocument->createElement('direccionComprador', $campos['infoFactura']['direccionComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos['infoFactura']['totalSinImpuestos'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos['infoFactura']['totalDescuento'])));
        $totalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        $detalles = $campos['infoFactura']["totalConImpuestos"];
        foreach ($detalles as $detalle) {
            $item = $xmlDocument->createElement('totalImpuesto');
            $item->appendChild($xmlDocument->createElement('codigo', $detalle["codigo"]));
            $item->appendChild($xmlDocument->createElement('codigoPorcentaje', $detalle["codigoPorcentaje"]));
            $item->appendChild($xmlDocument->createElement('descuentoAdicional', $detalle["descuentoAdicional"]));
            $item->appendChild($xmlDocument->createElement('baseImponible', $detalle["baseImponible"]));
            $item->appendChild($xmlDocument->createElement('valor', $this->getDecimal($detalle["valor"], '2')));
            $totalConImpuestos->appendChild($item);
        }
        $nodoDetalle->appendChild($totalConImpuestos);
        $nodoDetalle->appendChild($xmlDocument->createElement('propina', $this->getDecimal($campos['infoFactura']['propina'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos['infoFactura']['importeTotal'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        $pagos = $campos['infoFactura']['pagos'];
        $nodoDetalle->appendChild($this->formaPago($xmlDocument, $campos));
        if ($campos['infoFactura']['valorRetIva']) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetIva', $campos['infoFactura']['valorRetIva']));
        if ($campos['infoFactura']['valorRetRenta']) $nodoDetalle->appendChild($xmlDocument->createElement('valorRetRenta', $campos['infoFactura']['valorRetRenta']));
        return $nodoDetalle;
    }
    public function getInfoNotaCredito($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoNotaCredito');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision',  $this->getDate($campos['infoNotaCredito']['fechaEmision'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoNotaCredito']['dirEstablecimiento']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoNotaCredito']['tipoIdentificacionComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoNotaCredito']['razonSocialComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoNotaCredito']['identificacionComprador']));
        if ($campos['infoNotaCredito']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoNotaCredito']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoNotaCredito']['obligadoContabilidad']));
        if ($campos['infoNotaCredito']['rise']) $nodoDetalle->appendChild($xmlDocument->createElement('rise', $campos['infoNotaCredito']['rise']));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDocModificado', $campos['infoNotaCredito']['codDocModificado']));
        $nodoDetalle->appendChild($xmlDocument->createElement('numDocModificado', $campos['infoNotaCredito']['numDocModificado']));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $this->getDate($campos['infoNotaCredito']['fechaEmisionDocSustento'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $campos['infoNotaCredito']['totalSinImpuestos']));
        $nodoDetalle->appendChild($xmlDocument->createElement('valorModificacion', $campos['infoNotaCredito']['valorModificacion']));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        $totalConImpuestos = $campos['infoNotaCredito']['totalConImpuestos'];
        $nodoDetalle->appendChild($this->totalConImpuestos($xmlDocument, $totalConImpuestos));
        $nodoDetalle->appendChild($xmlDocument->createElement('motivo', $campos['infoNotaCredito']['motivo']));
        return $nodoDetalle;
    }
    public function getInfoNotaDebito($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoNotaDebito');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $campos['infoNotaDebito']['fecha_emision']));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoNotaDebito']['dirEstablecimiento']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionComprador', $campos['infoNotaDebito']['tipoIdentificacionComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialComprador', $campos['infoNotaDebito']['razonSocialComprador']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionComprador', $campos['infoNotaDebito']['identificacionComprador']));
        if ($campos['infoNotaDebito']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoNotaDebito']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoNotaDebito']['obligadoContabilidad']));
        $nodoDetalle->appendChild($xmlDocument->createElement('codDocModificado', $this->getDate($campos['infoNotaDebito']['codDocModificado'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('numDocModificado', $this->getDate($campos['infoNotaDebito']['numDocModificado'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $campos['infoNotaDebito']['fechaEmisionDocSustento']));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $campos['infoNotaDebito']['totalSinImpuestos']));
        $impuestos = $campos['infoNotaDebito']['impuestos'];
        $nodoDetalle->appendChild('impuestos', $this->getImpuestos($xmlDocument, $impuestos));
        $pagos = $campos['infoNotaDebito']['pagos'];
        $nodoDetalle->appendChild('Pagos', $this->formaPago($xmlDocument, $pagos));

        $nodoDetalle->appendChild($xmlDocument->createElement('motivo', $campos['infoNotaDebito']['motivo']));
        return $nodoDetalle;
    }
    public function getInfoCompRetencion($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('infoCompRetencion');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate($campos['infoCompRetencion']['fechaEmision'])));
        if ($campos['infoCompRetencion']['dirEstablecimiento']) $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoCompRetencion']['dirEstablecimiento']));
        if (isset($campos['infoCompRetencion']['contribuyenteEspecial'])) {
            if ($campos['infoCompRetencion']['contribuyenteEspecial'] != '') {
                $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', strtoupper($campos['infoCompRetencion']['contribuyenteEspecial'])));
            }
        }
        if ($campos['infoCompRetencion']['obligadoContabilidad']) $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', strtoupper($campos['infoCompRetencion']['obligadoContabilidad'])));
        if ($campos['infoCompRetencion']['tipoIdentificacionSujetoRetenido']) $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionSujetoRetenido', $campos['infoCompRetencion']['tipoIdentificacionSujetoRetenido']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialSujetoRetenido', $campos['infoCompRetencion']['razonSocialSujetoRetenido']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionSujetoRetenido', $campos['infoCompRetencion']['identificacionSujetoRetenido']));
        $nodoDetalle->appendChild($xmlDocument->createElement('periodoFiscal', $this->getDatePeriodoFiscal($campos['infoCompRetencion']['periodoFiscal'])));
        return $nodoDetalle;
    }
    public function getInfoLiquidacionCompra($xmlDocument, $campos)
    {
        $moneda = 'DOLAR';
        $nodoDetalle = $xmlDocument->createElement('infoLiquidacionCompra');
        $nodoDetalle->appendChild($xmlDocument->createElement('fechaEmision', $this->getDate($campos['infoLiquidacionCompra']['fecha_emision'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('dirEstablecimiento', $campos['infoLiquidacionCompra']['dirEstablecimiento']));
        if ($campos['infoLiquidacionCompra']['contribuyenteEspecial']) $nodoDetalle->appendChild($xmlDocument->createElement('contribuyenteEspecial', $campos['infoLiquidacionCompra']['contribuyenteEspecial']));
        $nodoDetalle->appendChild($xmlDocument->createElement('obligadoContabilidad', $campos['infoLiquidacionCompra']['obligadoContabilidad']));
        $nodoDetalle->appendChild($xmlDocument->createElement('tipoIdentificacionProveedor', $campos['infoLiquidacionCompra']['tipoIdentificacionProveedor']));
        $nodoDetalle->appendChild($xmlDocument->createElement('razonSocialProveedor', $campos['infoLiquidacionCompra']['razonSocialProveedor']));
        $nodoDetalle->appendChild($xmlDocument->createElement('identificacionProveedor', $campos['infoLiquidacionCompra']['identificacionProveedor']));
        if ($campos['infoLiquidacionCompra']['direccionProveedor']) $nodoDetalle->appendChild($xmlDocument->createElement('direccionProveedor', $campos['infoLiquidacionCompra']['direccionProveedor']));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalSinImpuestos', $this->getDecimal($campos['infoLiquidacionCompra']['totalSinImpuestos'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('totalDescuento', $this->getDecimal($campos['infoLiquidacionCompra']['totalDescuento'])));
        if ($campos['infoLiquidacionCompra']['codDocReembolso']) $nodoDetalle->appendChild($xmlDocument->createElement('codDocReembolso', $campos['infoLiquidacionCompra']['codDocReembolso']));
        if ($campos['infoLiquidacionCompra']['totalComprobantesReembolso']) $nodoDetalle->appendChild($xmlDocument->createElement('totalComprobantesReembolso', $campos['infoLiquidacionCompra']['totalComprobantesReembolso']));
        if ($campos['infoLiquidacionCompra']['totalBaseImponibleReembolso']) $nodoDetalle->appendChild($xmlDocument->createElement('totalComprobantesReembolso', $campos['infoLiquidacionCompra']['totalBaseImponibleReembolso']));
        if ($campos['infoLiquidacionCompra']['totalImpuestoReembolso']) $nodoDetalle->appendChild($xmlDocument->createElement('totalComprobantesReembolso', $campos['infoLiquidacionCompra']['totalImpuestoReembolso']));
        $totalConImpuestos = $campos['infoLiquidacionCompra']['totalConImpuestos'];
        $nodoDetalle->appendChild('totalConImpuestos', $this->totalConImpuestos($xmlDocument, $totalConImpuestos));
        $nodoDetalle->appendChild($xmlDocument->createElement('importeTotal', $this->getDecimal($campos['infoLiquidacionCompra']['importeTotal'])));
        $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $moneda));
        if ($campos['infoLiquidacionCompra']['moneda']) $nodoDetalle->appendChild($xmlDocument->createElement('moneda', $campos['infoLiquidacionCompra']['moneda']));
        $pagos = $campos['infoLiquidacionCompra']['pagos'];
        $nodoDetalle->appendChild($this->formaPago($xmlDocument, $pagos));
        return $nodoDetalle;
    }
    public function getImpuestosRetencion($xmlDocument, $campos)
    {
        $nodoDetalle = $xmlDocument->createElement('impuestos');
        $impuestos = $campos["impuestos"];
        foreach ($impuestos as $impuesto) {
            $item = $xmlDocument->createElement('impuesto');
            $item->appendChild($xmlDocument->createElement('codigo', $impuesto['codigo']));
            $item->appendChild($xmlDocument->createElement('codigoRetencion', $impuesto['codigoRetencion']));
            $item->appendChild($xmlDocument->createElement('baseImponible', $this->getDecimal($impuesto['baseImponible'])));
            $item->appendChild($xmlDocument->createElement('porcentajeRetener', $impuesto['porcentajeRetener']));
            $item->appendChild($xmlDocument->createElement('valorRetenido', $this->getDecimal($impuesto['valorRetenido'])));
            $item->appendChild($xmlDocument->createElement('codDocSustento', $impuesto['codDocSustento']));
            if (isset($impuesto['numDocSustento'])) {
                if ($impuesto['numDocSustento'] != '') {
                    $item->appendChild($xmlDocument->createElement('numDocSustento', $impuesto['numDocSustento']));
                }
            }
            if (isset($impuesto['fechaEmisionDocSustento'])) $item->appendChild($xmlDocument->createElement('fechaEmisionDocSustento', $this->getDate($impuesto['fechaEmisionDocSustento'])));
            $nodoDetalle->appendChild($item);
        }
        return $nodoDetalle;
    }
    public function totalConImpuestos($xmlDocument, $totalConImpuestos)
    {
        $nodoTotalConImpuestos = $xmlDocument->createElement('totalConImpuestos');
        foreach ($totalConImpuestos as $totalImpuesto) {
            $nodoTotalImpuesto = $xmlDocument->createElement('totalImpuesto');
            /*$nodoTotalImpuesto->appendChild($xmlDocument->createElement('formaPago', $totalImpuesto['formaPago']));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('total', $this->getDecimal($totalImpuesto['total'])));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('plazo', $totalImpuesto['plazo']));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('unidadTiempo', $totalImpuesto['unidadTiempo']));*/
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('codigo', $totalImpuesto["codigo"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('codigoPorcentaje', $totalImpuesto["codigoPorcentaje"]));
            if (isset($totalImpuesto["descuentoAdicional"])) $nodoTotalImpuesto->appendChild($xmlDocument->createElement('descuentoAdicional', $totalImpuesto["descuentoAdicional"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('baseImponible', $totalImpuesto["baseImponible"]));
            $nodoTotalImpuesto->appendChild($xmlDocument->createElement('valor', $this->getDecimal($totalImpuesto["valor"], '2')));
            $nodoTotalConImpuestos->appendChild($nodoTotalImpuesto);
        }
        return $nodoTotalConImpuestos;
    }
    public function getReembolsos($xmlDocument, $reembolsos)
    {
        $nodoReembolsos = $xmlDocument->createElement('reembolsos');
        foreach ($reembolsos as $reembolso) {
            $nodoReembolso = $xmlDocument->createElement('reembolsoDetalle');
            $nodoReembolso->appendChild($xmlDocument->createElement('tipoIdentificacionProveedorReembolso', $reembolso['tipoIdentificacionProveedorReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('identificacionProveedorReembolso', $this->getDecimal($reembolso['identificacionProveedorReembolso'])));
            $nodoReembolso->appendChild($xmlDocument->createElement('codPaisPagoProveedorReembolso', $reembolso['codPaisPagoProveedorReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('tipoProveedorReembolso', $reembolso['tipoProveedorReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('codDocReembolso', $reembolso['codDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('estabDocReembolso', $reembolso['estabDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('ptoEmiDocReembolso', $reembolso['ptoEmiDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('secuencialDocReembolso', $reembolso['secuencialDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('fechaEmisionDocReembolso', $reembolso['fechaEmisionDocReembolso']));
            $nodoReembolso->appendChild($xmlDocument->createElement('numeroautorizacionDocReemb', $reembolso['numeroautorizacionDocReemb']));
            $nodoReembolso->appendChild($this->getDetallesImpuestos($xmlDocument, $reembolso['detalleImpuestos']));
            $nodoReembolsos->appendChild('reembolsoDetalle', $nodoReembolso);
        }
        return $nodoReembolsos;
    }
    public function getMaquinaFiscal($xmlDocument, $maquinaFiscal)
    {
        $nodoMaquinaFiscal = $xmlDocument->createElement('maquinaFiscal');
        $nodoMaquinaFiscal->appendChild($xmlDocument->createElement('marca', $maquinaFiscal['marca']));
        $nodoMaquinaFiscal->appendChild($xmlDocument->createElement('modelo', $this->getDecimal($maquinaFiscal['modelo'])));
        $nodoMaquinaFiscal->appendChild($xmlDocument->createElement('serie', $maquinaFiscal['serie']));
        return $nodoMaquinaFiscal;
    }
    public function getDetallesImpuestos($xmlDocument, $detalles) //Liquidacion compra
    {
        $nodoDetallesImpuestos = $xmlDocument->createElement('detalleImpuestos');
        foreach ($detalles as $detalle) {
            $detalleImpuesto = $xmlDocument->createElement('detalleImpuesto');
            $detalleImpuesto->appendChild($xmlDocument->createElement('codigo', $detalle["codigo"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('codigoPorcentaje', $detalle["codigoPorcentaje"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('tarifa', $detalle["tarifa"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('baseImponible', $detalle["baseImponible"]));
            $detalleImpuesto->appendChild($xmlDocument->createElement('impuestoReembolso', $this->getDecimal($detalle["impuestoReembolso"], '2')));
            $nodoDetallesImpuestos->appendChild($detalleImpuesto);
        }
        return $nodoDetallesImpuestos;
    }
    public function formaPago($xmlDocument, $detalles)
    {
        $plazo = $detalles['infoFactura']['pagos']["plazo"];
        if ($plazo == '')
            $plazo = 0;
        $nodeFormaPago = $xmlDocument->createElement('pagos');
        foreach ($detalles['infoFactura']['pagos'] as $row) {
            $item = $xmlDocument->createElement('pago');
            $item->appendChild($xmlDocument->createElement('formaPago', $row["formaPago"]));
            $item->appendChild($xmlDocument->createElement('total', $row['total']));
            $item->appendChild($xmlDocument->createElement('plazo', $plazo));
            $item->appendChild($xmlDocument->createElement('unidadTiempo', $row["unidadTiempo"]));
            $nodeFormaPago->appendChild($item);
        }
        return $nodeFormaPago;
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
    private static function validarCedula($cedula)
    {
        if ((strlen($cedula) == 10) || (strlen($cedula) == 13)) {
            $numero           = $cedula;
            $suma             = 0;
            $residuo          = 0;
            $pri              = false;
            $pub              = false;
            $nat              = false;
            $numeroProvincias = 24;
            $modulo           = 11;

            /* Verifico que el campo no contenga letras */
            $ok = 1;
            $i  = substr($numero, 0, 2);
            if ($i > $numeroProvincias) {
                return false;
            }

            /* Aqui almacenamos los digitos de la cedula en variables. */
            $d1  = substr($numero, 0, 1);
            $d2  = substr($numero, 1, 1);
            $d3  = substr($numero, 2, 1);
            $d4  = substr($numero, 3, 1);
            $d5  = substr($numero, 4, 1);
            $d6  = substr($numero, 5, 1);
            $d7  = substr($numero, 6, 1);
            $d8  = substr($numero, 7, 1);
            $d9  = substr($numero, 8, 1);
            $d10 = substr($numero, 9, 1);

            /* El tercer digito es: */
            /* 9 para sociedades privadas y extranjeros */
            /* 6 para sociedades $publicas */
            /* menor que 6 (0,1,2,3,4,5) para personas $naturales */

            if ($d3 == 7 || $d3 == 8) {
                return false;
            }

            /* Solo para personas $naturales ($modulo 10) */
            if ($d3 < 6) {
                $nat = true;
                $p1  = $d1 * 2;
                if ($p1 >= 10) {
                    $p1 -= 9;
                }

                $p2 = $d2 * 1;
                if ($p2 >= 10) {
                    $p2 -= 9;
                }
                $p3 = $d3 * 2;
                if ($p3 >= 10) {
                    $p3 -= 9;
                }
                $p4 = $d4 * 1;
                if ($p4 >= 10) {
                    $p4 -= 9;
                }
                $p5 = $d5 * 2;
                if ($p5 >= 10) {
                    $p5 -= 9;
                }
                $p6 = $d6 * 1;
                if ($p6 >= 10) {
                    $p6 -= 9;
                }
                $p7 = $d7 * 2;
                if ($p7 >= 10) {
                    $p7 -= 9;
                }

                $p8 = $d8 * 1;
                if ($p8 >= 10) {
                    $p8 -= 9;
                }

                $p9 = $d9 * 2;
                if ($p9 >= 10) {
                    $p9 -= 9;
                }

                $modulo = 10;
            }

            /* Solo para sociedades $publicas ($modulo 11) */
            /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */ else if ($d3 == 6) {
                $pub = true;
                $p1  = $d1 * 3;
                $p2  = $d2 * 2;
                $p3  = $d3 * 7;
                $p4  = $d4 * 6;
                $p5  = $d5 * 5;
                $p6  = $d6 * 4;
                $p7  = $d7 * 3;
                $p8  = $d8 * 2;
                $p9  = 0;
            }

            /* Solo para entidades privadas ($modulo 11) */ else if ($d3 == 9) {
                $pri = true;
                $p1  = $d1 * 4;
                $p2  = $d2 * 3;
                $p3  = $d3 * 2;
                $p4  = $d4 * 7;
                $p5  = $d5 * 6;
                $p6  = $d6 * 5;
                $p7  = $d7 * 4;
                $p8  = $d8 * 3;
                $p9  = $d9 * 2;
            }

            $suma    = $p1 + $p2 + $p3 + $p4 + $p5 + $p6 + $p7 + $p8 + $p9;
            $residuo = $suma % $modulo;

            /* Si $residuo=0, dig.ver.=0, caso contrario 10 - $residuo*/
            $digitoVerificador = $residuo == 0 ? 0 : $modulo - $residuo;

            /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
            if ($pub == true) {
                if ($digitoVerificador != $d9) {
                    return false;
                }
                /* El ruc de las empresas del sector $publico terminan con 0001*/
                if (substr($numero, 9, 4) != '0001') {
                    return false;
                }
            } else if ($pri == true) {
                if ($digitoVerificador != $d10) {
                    return false;
                }
                if (substr($numero, 10, 3) != '001') {
                    return false;
                }
            } else if ($nat == true) {
                if ($digitoVerificador != $d10) {
                    return false;
                }
                if (strlen($numero) > 10 && substr($numero, 10, 3) != '001') {
                    return false;
                }
            }
        }
        return true;
    }
}
