<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class FarmaProductoCategoria extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'farma_producto_categoria';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto','id_producto'); 
    }

    public function subcategoria()
    {
        return $this->belongsTo('Sis_medico\FarmaSubcategoria','id_subcategoria'); 
    }


}