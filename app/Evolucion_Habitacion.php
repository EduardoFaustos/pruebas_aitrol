<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Evolucion_Habitacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evolucion_habitacion';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
      public function notas_enfermeria()
    {
        return $this->hasMany('Sis_medico\Nota_Enfermeria','id_evolucion');
    }
      public function evolucion()
    {
        return $this->hasMany(App\evolucion::class);
    }

}