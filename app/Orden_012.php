<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Orden_012 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_012';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function evolucion(){
        return $this->belongsTo('Sis_medico\Hc_Evolucion','id_hc_evolucion');    
    }
}
