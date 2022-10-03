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
class FormularioController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
     
    public function formulario(){

       
    	return view('prueba/formulario');

    }
    public function formulario_guardar(Request $request)
    {
        $resultado=[
            'fecha' => $request['fecha'],
            'paciente' => $request['paciente'],
            'procedimiento' => $request['procedimiento'],
            'seguro' => $request['seguro'],
            'factura' => $request['factura'],
            'cobradopcte' => $request['cobradopcte'],
            'cxccliente' => $request['cxccliente'],
            'xfcaseg' => $request['xfcaseg'],
            'valortotal' => $request['valortotal'],
        ];
        xavier::create($resultado);
        //return view('prueba/formulario');
        return  redirect('reportecomisiones');
        
    }
    
}
