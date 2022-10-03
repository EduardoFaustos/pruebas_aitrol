<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function pedido()
    {
        return $this->hasOne('Sis_medico\Pedido','id','id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto','id_producto');
    }

    public function movimieto_inv()
    {
        return $this->belongsTo('Sis_medico\InvDetMovimientos','id','id_detalle_pedido');
    }

    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega','id_bodega');
    }
}
