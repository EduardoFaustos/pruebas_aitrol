<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'especialidad';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
