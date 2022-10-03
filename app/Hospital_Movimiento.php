<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital_Movimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_movimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function bodega(){

        return $this->belongsTo('Sis_medico\Hospital_Bodega', 'id_bodega');
    }

}
 