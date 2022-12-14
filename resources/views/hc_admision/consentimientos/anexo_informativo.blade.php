<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>ANEXO INFORMATIVO</title>
    <style>
        * {
            font-size: 12px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;
            text-align: center;

        }

        .table,
        .td {
            border: 1px solid black;
            text-align: center;
        }

        .th {
            border: 1px solid black;
            text-align: center;
            background: #C2BCBA;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 0px auto;
            overflow: hidden;

        }
    </style>

</head>

<body>
    <div id="container">
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA : ANEXO INFORMATIVO</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">2. NOMBRE DEL ESTABLECIMIENTO DE SALUD: {{$empresa->razonsocial}}</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">3. SERVICIOS DEL ESTABLECIMIENTO DE SALUD: SERVICIO DE GASTROENTEROLOGIA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tbody>
                <tr>
                    <td>
                        <img style="margin:6px;margin-top:10px;height: 80px;width:80px;" src="{{public_path('/imagenes/uso_imagen.png')}}" />
                    </td>
                    <td>
                        <img style="margin:6px;margin-top:10px;height: 80px;width:80px;" src="{{public_path('/imagenes/uso_imagen2.png')}}" />
                    </td>
                    <td>
                        <img style="margin:6px;margin-top:10px;height: 80px;width:80px;" src="{{public_path('/imagenes/uso_imagen3.png')}}" />
                    </td>
                    <td>
                        <img style="margin:6px;margin-top:10px;height: 80px;width:80px;" src="{{public_path('/imagenes/uso_imagen4.png')}}" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        NOMBRE COMPLETO DEL PACIENTE
                    </td>
                    <td style="width: 70%;" style="font-weight: bold;" class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                </tr>
                <tr>
                    <td style="width: 30%;font-weight: bold;" class="td">
                        PROCEDIMIENTO A REALIZAR
                    </td>
                    <td style="width: 70%;" class="td"></td>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tr>
                <td style="border-right: 1px solid black;">
                    Tipo de Anexo:
                </td>
                <td style="border-right: 1px solid black;">
                    Adulto mayor
                </td>
                <td style="border-right: 1px solid black;width:20%">
                    &nbsp;
                </td>
                <td style="border-right: 1px solid black;">
                    Ni??os y Adolescentes
                </td>
                <td style="border-right: 1px solid black;width:20%">
                    &nbsp;
                </td>
                <td>
                    Personas Discapacitadas
                </td>
                <td style="border-right: 1px solid black;">

                </td>
                <td style="width:20%">
                    &nbsp;
                </td>
            </tr>
        </table>
        <table style="background : #C2BCBA ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">ANEXO INFORMATIVO PARA ADULTOS MAYORES</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tr>
                <td>
                    Los art??culos 35, 36 y 362 de la Constituci??n de la Rep??blica del Ecuador, en concordancia con los art??culos 4, 35, 36 y 37 de la Ley Org??nica de las Personas Adultas Mayores, como titulares de derechos y garant??as, recibir??n atenci??n prioritaria y especializada como el aval que debe tener todo paciente a ser informado, para que pueda dar su consentimiento en todos los servicios m??dicos, tanto p??blicos como privados. La transmisi??n de esta informaci??n se la har?? siempre atendiendo a sus necesidades comunicacionales de manera comprensible, en el idioma seg??n la identidad cultural de la persona adulta mayor y si se requiere de los servicios de un o una traductora para tal fin.
                    He sido informado claramente del procedimiento que me van a realizar y autorizo a efectuar el mismo y en caso de no estar en esa capacidad, la persona que me acompa??a o represente, sea o no familiar, conceder?? dicha autorizaci??n para el caso que tuviera dificultades motrices o alg??n otro impedimento para suscribir o autorizar el documento pertinente.

                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRES Y APELLIDOS DEL RESPRESENTANTE LEGAL</th>
                    <th class="th">C??DULA DE CIUDADAN??A</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table style="background : #9BC2E6 ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">ANEXO INFORMATIVO DE NI??OS Y ADOLESCENTES</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td>
                    Tengo derecho a estar informado y me ampara la Constituci??n de la Rep??blica del Ecuador en sus art??culos 32 y 362; y, el C??digo de la Ni??ez y la Adolescencia que, en su art??culo 11 vela por el inter??s superior del ni??o para satisfacer el ejercicio efectivo de nuestros derechos, en armon??a con el art??culo 27 de la Ley ib??dem, que garantiza nuestro derecho a disfrutar el m??s alto nivel de salud f??sica, mental, psicol??gica y sexual. El doctor me ha explicado e ilustrado el procedimiento que me va a realizar para mi bienestar y salud. Va a introducir por mi boca y/o ano un instrumental m??dico denominado endoscopio que permitir?? examinar mi sistema digestivo y anexos y poder determinar si tengo o no alg??n tipo de inconveniente y as?? buscar mejorar mi estado de salud.
                    Mi papi o mi mami o mi representante autorizar??n la realizaci??n de este procedimiento.
                </td>
            </tr>
        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRES Y APELLIDOS DEL RESPRESENTANTE LEGAL</th>
                    <th class="th">C??DULA DE CIUDADAN??A</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table style="background : #A9D08E ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">ANEXO INFORMATIVO DE PERSONAS DISCAPACITADAS</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td>
                    El art??culo 35 en concordancia con el art??culo 47 de la Constituci??n de la Rep??blica del Ecuador, que garantizan los derechos y avalar la atenci??n prioritaria de las personas con discapacidad, as?? como el art??culo 362 de la misma Ley Suprema, que estatuye la garant??a del consentimiento informado, el acceso a la informaci??n y la confidencialidad que merecen los pacientes. Es en virtud de los art??culos aqu?? invocados que se cre?? este Anexo Informativo para, por medios id??neos, dar a conocer y se d?? seguridad con el cumplimiento de los procedimientos m??dicos a realizarse.
                    He sido informado claramente, por medios id??neos, del procedimiento que me van a realizar y autorizo a efectuar el mismo y en caso de no estar en esa capacidad, la persona que me acompa??a o represente, sea o no familiar, conceder?? dicha autorizaci??n para el caso que tuviera dificultades motrices o alg??n otro impedimento para suscribir o autorizar el documento pertinente.

                </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRES Y APELLIDOS DEL RESPRESENTANTE LEGAL</th>
                    <th class="th">C??DULA DE CIUDADAN??A</th>
                    <th class="th">PARENTESCO</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
</body>

</html>