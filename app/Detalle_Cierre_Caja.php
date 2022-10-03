<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Detalle_Cierre_Caja extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'detalle_cierre_caja';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
