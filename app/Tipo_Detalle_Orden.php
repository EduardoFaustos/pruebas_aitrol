<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Tipo_Detalle_Orden extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_detalle_orden';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
