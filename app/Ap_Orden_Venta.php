<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ap_Orden_Venta extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_orden_venta';

    public function fx_seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','seguro'); 
    }

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}

