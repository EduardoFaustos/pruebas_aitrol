<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Bo_Solicitud extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bo_solicitud';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function bo_estado(){
        return $this->belongsTo('Sis_medico\Bo_Estado','id_estado');
    }
}
