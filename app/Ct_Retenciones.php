<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Retenciones extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_retenciones';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function proveedor()
    {

        return $this->belongsTo('Sis_medico\Proveedor', 'id_proveedor');
    }
    public function detalle()
    {
        return $this->hasMany('Sis_medico\Ct_detalle_retenciones', 'id_retenciones');
    }
    public function usuario()
    {

        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function compras()
    {
        return $this->belongsTo('Sis_medico\Ct_compras','id_compra','id');
    }
    public function gasto()
    {
        return $this->belongsTo('Sis_medico\Ct_Factura_Contable', 'id_gasto');
    }

    public static function getRetenciones(){
        return Ct_Retenciones::join('de_empresa as em', 'ct_retenciones.id_empresa', '=', 'em.id_empresa')
        ->where('ct_retenciones.electronica', 1)
        ->where('ct_retenciones.doc_electronico', 0)->get([
            'ct_retenciones.id',
            'ct_retenciones.id_empresa',
            'ct_retenciones.created_at',
            'sucursal',
            'punto_emision',
            'nro_secuencial',
            'valor_fuente',
            'valor_iva',
            'total',
            'nro_autorizacion',
            'nro_comprobante',
            'fecha',
            'id_compra',
            
        ]);
    }
    public static function updateSinGenerarXML($id)
    {
        $arrayDoc = [
            'doc_electronico' => 7,
        ];
        Ct_Retenciones::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($id, $clave)
    {
        $arrayDoc = [
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave
        ];
        Ct_Retenciones::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($id)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        Ct_Retenciones::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($id)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        Ct_Retenciones::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($id)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        Ct_Retenciones::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($id, $clave, $secuencial)
    {
        $arrayDoc = [
            'doc_electronico' => 5,
            'nro_autorizacion' => $clave,
            'nro_secuencial' => $secuencial
        ];
        Ct_Retenciones::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 9
        ];
        Ct_Retenciones::where('id', $idG)->first()
            ->update($arrayDoc);
    }
}
