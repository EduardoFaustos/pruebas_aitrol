<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Creacion_Campo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'creacion_campo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */


    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','user');
    }

    protected $guarded = [];

}
