<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Datos_Paciente extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_datos_paciente';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
