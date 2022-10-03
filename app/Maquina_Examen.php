<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Maquina_Examen extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'maquina_examen';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    
}