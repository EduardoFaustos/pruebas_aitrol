<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class LabsDetalleFacturacioncc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_detalle_facturacion_cc';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function proceso()
    {
        return $this->belongsTo('Sis_medico\LabsProcesoFacturacioncc', 'id_proceso');
    }

    public function orden()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden', 'id_orden');
    }

    
}
 