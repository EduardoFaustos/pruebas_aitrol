<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_compras extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_compras';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function proveedorf()
    {
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }
/*     public function proveedorf()
    {
        $id_empresa = Session::get('id_empresa');
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    } */
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_detalle_compra', 'id_ct_compras');
    }
    public function retenciones(){
        return $this->hasMany('Sis_medico\Ct_Retenciones','id_compra');
    }
    public function egresos(){
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Egreso','id_compra','id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function master_tipos(){
        return $this->belongsTo('Sis_medico\Ct_master_tipos', 'tipo_comprobante','codigo');
    }
    public function cruce(){
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce','id_factura','id');
    }
    public function cruce_cuentas(){
        return $this->hasMany('Sis_medico\Ct_Cruce_Cuentas','id_factura','id');
    }
    public function bndebito(){
        return $this->hasMany('Sis_medico\Ct_Debito_Bancario_Detalle','id_compra','id');
    }
    public function debitoacreedor(){
        return $this->hasMany('Sis_medico\Ct_Detalle_Debito_Acreedores','id_factura','id');
    }
    public function credito_acreedor(){
        return $this->hasMany('Sis_medico\Ct_Credito_Acreedores','id_compra','id');
    }
    public function masivos()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo', 'id_compra','id');
    }

    public function termino(){
        
        return $this->belongsTo('Sis_medico\Ct_Termino', 'termino', 'id');
    }

    public function facturaActivo(){
        
        return $this->belongsTo('Sis_medico\AfFacturaActivoCabecera', 'id_asiento_cabecera', 'id_asiento');
    }

    
    
}
