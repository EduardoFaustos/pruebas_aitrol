<?php

namespace Sis_medico\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts($request)
    {
        $maxLoginAttempts = 10;
        $lockoutTime      = 1; // 5 minutes
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $maxLoginAttempts, $lockoutTime
        );
    }

    public function login(Request $request)
    {
        $usuario = User::where('email', $request['email'])->where('estado', 0)->first();
        if (!is_null($usuario)) {
            return back()->withErrors(['email', 'No puede acceder al Sistema, comuniquese con un administrador']);
        }

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            //dd($request);
            $usuario    = User::where('email', $request['email'])->first();
            $id_usuario = $usuario->id;
            $empresa    = Empresa::join('usuario_empresa', 'empresa.id', '=', 'usuario_empresa.id_empresa')
                ->where('id_usuario', $id_usuario)->select('empresa.*')->first();
            if (!is_null($empresa)) {
                //dd($empresa->id);
                $request->session()->put('id_empresa', $empresa->id);
            } else {
                $empresa = Empresa::where('prioridad', 1)->first();
                if (!is_null($empresa)) {
                    $request->session()->put('id_empresa', $empresa->id);
                }
            }
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}
