<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TipoSugerencia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_sugerencia';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
