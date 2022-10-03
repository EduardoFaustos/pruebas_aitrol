<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc4_Tipo_Biopsias_Ptv extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc4_tipo_biopsias_ptv';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Hc4_Tipo_Biopsias_Ptv_Detalle','id_hc4_tipo_biopsias_ptv');   
    }

    
}
 