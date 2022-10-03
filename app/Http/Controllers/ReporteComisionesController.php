<?php

namespace Sis_medico\Http\Controllers;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\User;
use Sis_medico\xavier;
use Sis_medico\Ct_ventas;
use Sis_medico\Seguro;

class  ReporteComisionesController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function reporte_comisiones()
    {
        $ct_ventas = Ct_ventas::where('estado', '1')->where('id_empresa', '0992704152001')->paginate(20);
        $seguro = Seguro::where('inactivo','1')->get();





        return view('contable/ventas/reporte/boton', ['ct_ventas' => $ct_ventas,'seguro'=> $seguro]);
    }
}
