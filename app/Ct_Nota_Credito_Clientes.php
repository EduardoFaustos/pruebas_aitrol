<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nota_Credito_Clientes extends Model
{
    
    protected $table = 'ct_nota_credito_clientes';
    protected $guarded = [];

    public function cliente(){
        
        // /$id_empresa   = session()->get('id_empresa');
        //return $this->belongsTo('Sis_medico\Ct_Clientes_Empresa','id_cliente', 'identificacion')->where('id_empresa',$id_empresa);
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente', 'identificacion');

    }

    public function usercrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
        
    }
    public function valorf()   
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_factura');
    }

}
