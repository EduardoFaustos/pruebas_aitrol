<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Comprobante_Ingreso extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_comprobante_ingreso';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */


    protected $guarded = [];

    public function detalle_formapago()
    {
        return $this->belongsTo('Sis_medico\Examen_Detalle_Forma_Pago', 'id_examen_detalle_pago');
    }
}
