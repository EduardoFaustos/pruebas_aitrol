<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Asientos_Pedido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_asientos_cabecerera_pedido';
    

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    
}
