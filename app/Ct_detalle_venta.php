<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_detalle_venta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_venta';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Ventas', 'id_ct_ventas', 'id');
    }
   
    public static function getDetalles($id)
    {
        return Ct_detalle_venta::where('id_ct_ventas', $id)->get();
    }
}
