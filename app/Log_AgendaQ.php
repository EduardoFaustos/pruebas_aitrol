<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Log_AgendaQ extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_agenda_quirofano';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
