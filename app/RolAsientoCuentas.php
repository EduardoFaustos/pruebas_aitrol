<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class RolAsientoCuentas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rol_asiento_cuentas';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
