<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Valida_Anticipo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string 
     */
    protected $table = 'ct_valida_anticipo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function asiento_cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Asientos_Cabecera','asiento');
    }
}
 
 