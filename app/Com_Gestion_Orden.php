<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Com_Gestion_Orden extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'com_gestion_orden';
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
