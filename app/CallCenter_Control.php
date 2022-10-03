<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CallCenter_Control extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'callcenter';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function usuario()   
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function agenda()   
    {
        return $this->belongsTo('Sis_medico\Agenda', 'id_agenda');
    }   


    
   
}
 