<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class LimpiezaPentax extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'limpieza_salas_pentax';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
