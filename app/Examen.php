<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function agrupador()
    {
        return $this->belongsTo('Sis_medico\Examen_agrupador','id_agrupador');
    }
    public function scopemaquina($query, $type)
    {
        return $query->where('maquina', $type);
    }
    public function detalles(){
        return $this->belongsTo('Sis_medico\Examen_Detalles','id_examen');
    }

    public function parametros(){
        return $this->belongsTo('Sis_medico\Examen_Parametro','id_examen');
    }

  
}
