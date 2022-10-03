<?php

namespace Sis_medico;

use Illuminate\Support\Facades\DB;
class Utilidades
{
    public static function getDia($id){
        if($id > 8 or $id < 0){
            return 'N/A';
        } 
        $day = ['N/A','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
        return $day[$id];
    }

    public static function getMes($id){
        if($id > 13 or $id < 0){
            return 'N/A';
        } 
        $meses = ['N/A','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        return $meses[$id];
    }
}