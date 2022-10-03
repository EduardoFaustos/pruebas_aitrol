<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Session;

class InvCabTomaFisica extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_cab_tomafisica';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = []; 

    public function detalles()
    {
         return $this->hasMany('Sis_medico\InvDetTomaFisica', 'id_inv_cab_tomafisica', 'id');
    } 
    

    
    
}