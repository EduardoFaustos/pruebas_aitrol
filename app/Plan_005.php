<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Plan_005 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_005';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

     public function scopeMedico_plan($query,$medico_plan){
     if($medico_plan)

        return $query->where('medico_plan','LIKE',"%$medico_plan%");
    }
    public function scopeDescripcion_plan($query,$descripcion_plan){
     if($descripcion_plan)

    return $query->where('descripcion_plan','LIKE',"%$descripcion_plan%");
    }

}
 