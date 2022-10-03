<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class FrecuenciaMantenimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'frecuencia_llimpieza';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    
    protected $guarded = [];
}
