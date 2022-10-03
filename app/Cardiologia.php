<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Cardiologia extends model
{
     //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_cardio';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
