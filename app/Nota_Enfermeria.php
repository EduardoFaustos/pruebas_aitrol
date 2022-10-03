<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nota_Enfermeria extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nota_enfermeria';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function evolucion()
    {
        return $this->belongsTo('Sis_medico\Evolucion_Habitacion','id_evolucion');
    }

}