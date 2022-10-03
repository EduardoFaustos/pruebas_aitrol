<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Datos_item extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'datos_item';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function plato()
    {
        return $this->belongsTo('Sis_medico\Plato','id_plato')->with('id');
        
    }

    public function items_plato()
    {
        return $this->belongsToMany('Sis_medico\Items_plato', 'datos_item', 'id', 'id_item_plato');
        //belongsTo('Sis_medico\Items_plato','id_item_plato')->with('id');

        //return $this->hasManyThrough('Sis_medico\datos_item', 'Sis_medico\Items_plato', '', 'id_item_plato', 'id', '');
        
    }

}