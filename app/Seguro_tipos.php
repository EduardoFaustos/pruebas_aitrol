<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Seguro_tipos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seguro_tipos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
