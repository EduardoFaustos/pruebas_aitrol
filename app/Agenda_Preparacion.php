<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Agenda_Preparacion extends Model
{
    protected $table = 'agenda_preparacion';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
