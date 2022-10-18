<?php

namespace Sis_medico\Http\Controllers\hospital\uci;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Sala;

class UciController extends Controller
{
    public function index()
    {
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $salas = Sala::where('estado','1')->where('id_hospital','5')->get();
        

        return view('hospital/uci/index',['salas' =>$salas]);;
    }

    public function index_uci(){

        
        return view('hospital/uci/index_uci');
    }
}
