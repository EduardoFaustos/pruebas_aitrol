<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Bloqueo_Sala extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bloqueo_sala';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /*public function hospital() {
        return $this->belongsTo('Sis_medico\Hospital','id_hospital');
        }*/
}
