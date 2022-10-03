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

class excelCreate extends Controller
{
    public static function details($sheet, $data){
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z","AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
        
        $datos = $data["data"];
        $comienzo = isset($data["comienzo"])? $data["comienzo"] : 1;
        $background = isset($data['background-color']) ? $data['background-color'] : "#FCFCFC";
        $fuente = isset($data['color']) ? $data["color"]: "#000000";

        if(isset($data['mergue'])){
            $sheet->mergeCells($data["columna"]);
        }
        $com = isset($data["letra"]) ? array_search($data["letra"],$posicion,false) : 0;
        $contDatos = 0;

        for ($i = $com; $i < count($datos); $i++){
            
            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos, $i, $fuente, $background, $contDatos) {
                $cell->setValue($datos[$contDatos]);
                $cell->setFontWeight('bold');
                $cell->setBackground($background);
                $cell->setFontColor($fuente);
                $cell->setAlignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $contDatos ++;
        }
      
    }
}