<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Factura_Informacion_Factura extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'factura_informacion_factura';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
   
}
 