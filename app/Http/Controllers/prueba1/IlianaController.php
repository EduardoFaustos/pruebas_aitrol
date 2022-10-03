<?php

namespace Sis_medico\Http\Controllers\prueba1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\user;

class IlianaController extends Controller
{    
	 public function index(Request $request)
    {
    	dd("Bienvenido a mi formulario");
 
    }

	public function nombres(){
		return view('prueba12.nombres');
     }




    public function apellidos(){

      $user = user::create(request(['nombres','apellidos']));

     auth() ->register($user);
		return (" puedes ingresar apellidos");
     }

}


	


