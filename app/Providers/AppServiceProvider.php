<?php

namespace Sis_medico\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Sis_medico\User;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Observers\Ct_AcreedoresObserver;
use Sis_medico\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->app->validator->extendImplicit('max_consulta', function ($attribute, $value, $parameters) {

            return $parameters[0] < $parameters[1];
        }, 'Doctor ha superado el máximo de consultas por día!');
        $this->app->validator->extendImplicit('comparamayor', function ($attribute, $value, $parameters) {

            return $parameters[0] < $parameters[1];
        }, 'comparamayor');

        $this->app->validator->extendImplicit('max_procedimiento', function ($attribute, $value, $parameters) {

            return $parameters[0] < $parameters[1];
        }, 'Doctor ha superado el máximo de procedimientos por día!');

        $this->app->validator->extendImplicit('unique_doctor', function ($attribute, $value, $parameters) {

            return $parameters[0] < 1;
        }, 'Doctor ya se encuentra con paciente asignado');

        $this->app->validator->extendImplicit('edad_fecha', function ($attribute, $value, $parameters) {

            $date2  = date('Y-m-d'); //
            $diff   = abs(strtotime($date2) - strtotime($value));
            $years  = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

            return $years > 17;
        }, 'Es menor de Edad');

        $this->app->validator->extendImplicit('caducidad_codigo', function ($attribute, $value, $parameters) {

            $flag  = true;
            $date2 = date('Y-m-d'); //

            if ($parameters[2] < $date2) {
                if ($parameters[1] != $parameters[2]) {
                    if ($value == $parameters[0]) {
                        $flag = false;
                    }
                }
            }

            return $flag;
        }, 'Debe cambiar el código de validación');

        $this->app->validator->extendImplicit('comparahoras', function ($attribute, $value, $parameters) {

            $hora = substr($attribute, 0, 8);

            if ($hora == 'hora_ini') {
                return $value < $parameters[0];
            } else {
                return $parameters[0] < $value;
            }

        }, 'hora de inicio debe ser menor a hora fin ');

        $this->app->validator->extendImplicit('son_iguales', function ($attribute, $value, $parameters) {

            return $parameters[0] === $parameters[1];
        }, 'son_iguales');

        $this->app->validator->extendImplicit('aux', function ($attribute, $value, $parameters) {

            $aux = substr($value, 0, 3);
            return $aux != 'AUX';
        }, 'aux');

        User::observe(UserObserver::class);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
