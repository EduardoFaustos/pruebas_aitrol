<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examenes extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examenes';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}
