<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc4_Biopsias_Ptv_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc4_biopsia_ptv_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function biopsia()
    {
        return $this->belongsTo('Sis_medico\Hc4_Biopsias_Ptv', 'id_hc4_biopsias');
    }

    
}
 