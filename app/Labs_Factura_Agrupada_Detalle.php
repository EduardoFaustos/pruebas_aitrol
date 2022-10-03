<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Factura_Agrupada_Detalle extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_factura_agrupada_detalle';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function ordenes()
    {
        return $this->hasMany('Sis_medico\Labs_Factura_Agrupada_Orden','id_agrup_det');   
    }


}