<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nota_Credito_Clientes extends Model
{
    
    protected $table = 'ct_nota_credito_clientes';
    protected $guarded = [];

    public function cliente(){
        
        // /$id_empresa   = session()->get('id_empresa');
        //return $this->belongsTo('Sis_medico\Ct_Clientes_Empresa','id_cliente', 'identificacion')->where('id_empresa',$id_empresa);
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente', 'identificacion');

    }

    public function usercrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
        
    }
    public function valorf()   
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_factura');
    }

    public static function getRetenciones(){
        return Ct_Nota_Credito_Clientes::join('de_empresa as em', 'ct_nota_credito_clientes.id_empresa', '=', 'em.id_empresa')
        ->where('ct_nota_credito_clientes.electronica', 1)
        ->where('ct_nota_credito_clientes.doc_electronico', 0)->get([
            'ct_nota_credito_clientes.id',
            'ct_nota_credito_clientes.id_empresa',
            'ct_nota_credito_clientes.created_at',
            'sucursal',
            'punto_emision',
            'secuencia',
            'valor_fuente',
            'valor_iva',
            'total',
            'nro_autorizacion',
            'nro_comprobante',
            'numero_factura',
            'fecha_envio',
            'id_compra',
            
        ]);
    }
    public static function updateSinGenerarXML($id)
    {
        $arrayDoc = [
            'doc_electronico' => 7,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($id, $clave)
    {
        $arrayDoc = [
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($id)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($id)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($id)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($id, $clave, $secuencial)
    {
        $arrayDoc = [
            'doc_electronico' => 5,
            'nro_autorizacion' => $clave,
            'secuencia' => $secuencial
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 9
        ];
        Ct_Nota_Credito_Clientes::where('id', $idG)->first()
            ->update($arrayDoc);
    }
}
