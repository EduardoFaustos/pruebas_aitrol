<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Evolucion_005 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'evolucion_005';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function scopeMedico($query,$medico){
     if($medico)

        return $query->where('medico','LIKE',"%$medico%");
    }
    public function scopeCodigo($query,$codigo){
     if($codigo)

    return $query->where('codigo','LIKE',"%$codigo%");
    }
    public function scopeNota_evolucion($query,$nota_evolucion){
     if($nota_evolucion)

    return $query->where('nota_evolucion','LIKE',"%$nota_evolucion%");
    }


}
 