<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc4_Tipo_Biopsias_Ptv_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc4_tipo_biopsias_ptv_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function tipo_biopsia()
    {
        return $this->belongsTo('Sis_medico\Hc4_Tipo_Biopsias_Ptv', 'id_hc4_tipo_biopsias');
    }

    
}
 