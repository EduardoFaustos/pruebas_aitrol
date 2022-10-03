<?php
namespace Sis_medico;
use Illuminate\Database\Eloquent\Model;
class xavier extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prueba';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}