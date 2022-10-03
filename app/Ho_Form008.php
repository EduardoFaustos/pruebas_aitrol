<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Form008 extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_form008';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda','id_agenda');
    }

   

}