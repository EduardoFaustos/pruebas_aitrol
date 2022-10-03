<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class GrupoPreguntas_Labs extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grup_pregunta_labs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}