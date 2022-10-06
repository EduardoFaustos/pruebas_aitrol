<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_ventas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_ventas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente', 'identificacion');
    }
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }

    public function retenciones()
    {
        return $this->belongsTo('Sis_medico\Ct_Cliente_Retencion', 'id', 'id_factura');
    }

    public function retenciones_2()
    {
        return $this->hasMany('Sis_medico\Ct_Cliente_Retencion', 'id_factura');
    }
    public function comp_ingreso()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Ingreso', 'id_factura');
    }
    public function cruce()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce_Clientes', 'id_factura');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_detalle_venta', 'id_ct_ventas');
    }
    public function chequepost()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cheque_Post', 'id_factura');
    }
    public function cab_retenciones()
    {
        return $this->hasOne(Ct_Cliente_Retencion::class, 'id_factura');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuariomod()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
    public function cruce_cuentas()
    {
        return $this->hasMany('Sis_medico\Ct_Cruce_Cuentas_Clientes', 'id_factura');
    }
    public function credito()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Credito_Clientes', 'id_factura');
    }
    public function ct_orden_venta()
    {
        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'orden_venta', 'id');
    }
    public function detalle_nota_credito_parcial()
    {
        return $this->hasMany('Sis_medico\Ct_Devolucion_Productos', 'id_factura');
    }
    public function pdetalle_nota_credito_parcial()
    {
        return $this->belongsTo('Sis_medico\Ct_Devolucion_Productos', 'id_factura', 'id');
    }
    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'seguro_paciente');
    }
    public static function getVentasCabecera()
    {
        return Ct_ventas::join('de_empresa as em', 'ct_ventas.id_empresa', '=', 'em.id_empresa')
            ->where('ct_ventas.electronica', 1)
            ->where('ct_ventas.id_empresa', '0922729587001')
            ->where('ct_ventas.doc_electronico', 0)
            ->get([
                'ct_ventas.id',
                'ct_ventas.id_empresa',
                'ct_ventas.created_at',
                'sucursal',
                'punto_emision',
                'ct_ventas.numero',
                'fecha_envio',
                'ct_ventas.fecha',
                'nro_comprobante',
                'direccion_cliente',
                'ruc_id_cliente',
                'telefono_cliente',
                'email_cliente',
                'nombres_paciente',
                'subtotal_0',
                'subtotal_12',
                'subtotal_total',
                'descuento',
                'base_imponible',
                'impuesto',
                'iva_total',
                'total_final',
                'ventas_netas',
                'dias_plazo',
                'nombre_cliente',
                'nota_electronica'
            ]);
    }
    public static function updateGenerarXML($idG, $clave)
    {
        $arrayDoc = [
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 5,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 9,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }

    public function nota_credito()
    {
        return $this->hasMany('Sis_medico\Ct_Nota_Credito_Clientes', 'id_factura');
    }


}
