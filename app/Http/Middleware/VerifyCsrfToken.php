<?php

namespace Sis_medico\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    	'http://192.168.75.109/sis_medico_prb/public/maquina/hemograma/prueba',//ruta de la maquina 1
        //
    ];
    
}
