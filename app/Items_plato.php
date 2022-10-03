<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Items_plato extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items_plato';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function datos_item()
    {
        return $this->hasMany('Sis_medico\Datos_item', 'id_item_plato');
    }

}
 