<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_productos;

class Producto_Precio_Aprobado extends Model
{
    //
    protected $table = 'producto_precio_aprobado';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'id_producto');
    }
    

}
