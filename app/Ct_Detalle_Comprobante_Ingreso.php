<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Comprobante_Ingreso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_comprobante_ingreso';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function ventas()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_factura');
    }
    public function ingreso()
    {
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Ingreso','id_comprobante');
    }

}