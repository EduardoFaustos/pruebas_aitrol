<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class UsuarioEspecial extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuario_especial';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
