<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>CRM - CONSENTIMIENTO ENTEROSCOPIA ALTA-rev02</title>
    <style>
        * {
            font-size: 12px;
            padding: -1px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;

        }

        .table,
        .td {
            border: 1px solid black;
            text-align: center;
        }

        .th {
            border: 1px solid black;
            text-align: center;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 0px auto;
        }
    </style>

</head>

<body>
    <div id="container">
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA : ENTEROSCOPIA ANTEROGRADA O ALTA</td>
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
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">4.Fecha : </td>
                <td class="td" style="font-weight: bold;">5.Hora : </td>
            </tr>
        </table>
        <table style="background : #C2BCBA ;width:100%;text-align:center;border: 1px solid black">
            <tr>
                <td style="font-weight: bold;">6.INFORMACION DEL PACIENTE</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center ">
            <tr>
                <td style="width: 100%;font-weight: bold;" class="td" rowspan="2">DOCUMENTO DE INDENTIFICACION</td>
                <td style="width: 100%;font-weight: bold;" class="td">CEDULA</td>
                <td style="width: 100%;" class="td">{{$paciente->id}}</td>

            </tr>
            <tr>
                <td style="width: 100%;font-weight: bold;" class="td">PASAPORTE</td>
                <td style="width: 100%;" class="td">&nbsp;</td>
            </tr>
        </table>

        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="th">Apellido Paterno</th>
                    <th class="th">Apellido Materno</th>
                    <th class="th">Nombres</th>
                    <th class="th">Fecha de Nacimiento</th>
                    <th class="th">Edad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{$paciente->apellido1}}</td>
                    <td class="td">{{$paciente->apellido2}}</td>
                    <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}}</td>
                    <td class="td">{{$paciente->fecha_nacimiento}}</td>
                    <td class="td">{{$edad}}</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;font-weight:bold;">
            <tr>
                <td class="td" style="font-weight: bold;">7.NOMBRE DEL DIAGNOSTICO : </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    8.TIPO DE ATENCION
                </td>
                <td style="width: 70%;" class="td"></td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    9. NOMBRE DEL PROCEDIMIENTO RECOMENDADO
                </td>
                <td style="width: 70%;" class="td">{{$nombre->nombre}}</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center ">
            <thead>
                <tr>
                    <th class="th">
                        10. ??EN QU?? CONSISTE?
                    </th>
                    <th class="th">
                        11. GR??FICO DE LA INTERVENCI??N
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">Para el diagn??stico y tratamiento de enfermedades del intestino delgado (duodeno, yeyuno e ileon), Para el procedimiento m??dico ambulatorio se usa un tubo largo y flexible con un sistema de iluminaci??n y una c??mara (enteroscopio). </td>
                    <td><img style="margin:6px;margin-top:10px;height: 60px;width:60px;" src="{{public_path('/imagenes/enteroscopia1.jpg')}}" />
                        <img style="margin:6px;margin-top:10px;height: 60px;width:60px;" src="{{public_path('/imagenes/enteroscopia2.jpg')}}" /></td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th>
                        12. ?? C??MO SE REALIZA?
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: justify;" class="td">La exploraci??n endosc??pica se realiza con anestesia general, en una posici??n c??moda sobre el lado izquierdo o boca arriba (dec??bito supino, Se introduce por la boca (anter??grada o alta) y se avanza progresivamente a trav??s del intestino delgado hasta encontrar la zona de estudio.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    13. DURACI??N ESTIMADA DE LA EXPLORACI??N
                </td>
                <td style="width: 70%;" class="td">Aproximadamente 45 a 60 minutos (excepcionalmente el tiempo puede ser mayor)</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th class="th">
                        14. BENEFICIOS DE LA EXPLORACI??N
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: justify;" class="td">La Enteroscopia Anterograda o Alta permite el diagn??stico de lesiones del intestino delgado que pueden ser las causantes de una hemorragia digestiva de origen desconocido, tambi??n permite el estudio de ciertos casos de enfermedades inflamatorias (enfermedad de Crohn), de p??lipos y poliposis familiares (como el s??ndrome de Peutz-Jeghers) o de los hallazgos vistos en otras pruebas de imagen.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    15. RIESGOS FRECUENTES
                </td>
                <td style="width: 70%;" class="td">En las exploraciones m??s prolongadas puede quedar sensaci??n de hinchaz??n por el aire introducido durante la misma.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    16. RIESGOS INFRECUENTES
                </td>
                <td style="width: 70%;" class="td">Puede producirse hemorragia al tratar grandes lesiones o por alguna que ya estuviese sangrando. Podr??a ocurrir tambi??n una perforaci??n de alguna zona del intestino o a??n menos frecuente</td>
            </tr>

        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <th class="th">
                        17. DE EXISTIR, ESCRIBA LOS RIESGOS ESPECIFICOS RELACIONADOS CON EL PACIENTE (edad y estado de salud)
                    </th>
                </tr>
            </thead>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td">Diga SI o NO. De ser afirmativo, en cada caso, detalle lo que corresponda:</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">

            <tr>
                <td style="width: 30%;" class="td">Enfermedades respiratorias</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Enfermedades cardi??cas</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Enfermedades respiratorias</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Marcapasos</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Alergias (a medicinas, alimentos, otros)</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Toma de medicamentos antiagregantes o anticuagulantes (plavix, aspirina, rivaraxoban, dabigtran, etc)</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">??Ha recibido anestesia anteriormente?<</td> <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Asma</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

            <tr>
                <td style="width: 30%;" class="td">Otro</td>
                <td style="width: 20%;" class="td">&nbsp;</td>
                <td style="width: 50%;text-align:none" class="td">Especifique:</td>

            </tr>

        </table>

        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    18. ALTERNATIVAS DE LA EXPLORACI??N
                </td>
                <td style="width: 70%;" class="td"> Cuando se prescribe una enteroscopia ya se han agotado los m??todos diagn??sticos de imagen o existe una lesi??n en el intestino delgado que se debe estudiar o tratar.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    19. MANEJO POSTERIOR AL PROCEDIMIENTO
                </td>
                <td style="width: 70%;" class="td"> Luego del procedimiento, el paciente es trasladado al ??rea de Recuperaci??n en donde despu??s de recuperar su nivel de conciencia, producto de la sedaci??n y comprobada su recuperaci??n, puede ser dado de alta.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    20. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACI??N
                </td>
                <td style="width: 70%;" class="td"> Mal diagn??stico, diagn??stico errado o diagn??stico tard??o de enfermedades del tracto digestivo alto.</td>
            </tr>

        </table>

        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;">21. DECLARACI??N DEL CONSENTIMIENTO INFORMADO</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:10px" class="td">He facilitado la informaci??n completa que conozco, y que me ha sido solicitada, sobre los antecedentes personales, familiares y de mi estado de salud. Soy consciente de que omitir o falsear estos datos pueden afectar los resultados del tratamiento. Estoy de acuerdo con el procedimiento m??dico ambulatorio que se me ha propuesto; he sido informado de las ventajas e inconvenientes del mismo; se me ha explicado de forma clara en qu?? consiste, los beneficios y posibles riesgos del procedimiento m??dico ambulatorio. He escuchado, le??do y comprendido la informaci??n recibida y se me ha dado la oportunidad de preguntar sobre el procedimiento. He tomado consiente y libremente la decisi??n de autorizar el procedimiento, en consecuencia, libero de toda responsabilidad civil, penal o administrativa, que pudiese derivarse de caso fortuito, fuerza mayor o evento imprevisible, incontrolable o fuera del alcance de la capacidad humana y profesional a m??dicos y personal param??dico que participen en el proceso. Consiento que, durante la exploraci??n, me realicen otro procedimiento adicional, si es considerado necesario seg??n el juicio del profesional de la salud, para mi beneficio. Tambi??n conozco que puedo retirar mi consentimiento cuando lo estime oportuno.</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                    <th class="th">C??DULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                    <td class="td">{{$paciente->id}}</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td class="td" style="font-weight: bold;">SI EL PACIENTE NO EST?? EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado tambi??n en caso de ser el paciente menor de edad o presente una incapacidad): </td>

            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL</th>
                    <th class="th">C??DULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;
                        <!--{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}}-->
                    </td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL PROFESIONAL QUE REALIZA EL PROCEDIMIENTO</th>
                    <th class="th">C??DULA DEL CIUDADANIA</th>
                    <th class="th">FIRMA, SELLO Y C??DIGO DEL PROFESIONAL QUE REALIZA EL PROCEDIMIENTO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td class="td" style="font-weight: bold;">22. NEGATIVA DEL CONSENTIMIENTO INFORMADO</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:5px" class="td">Una vez que he entendido claramente los procedimientos m??dicos ambulatorios propuestos, as?? como las consecuencias posibles si no se realiza la intervenci??n, no autorizo y me niego a que se me realice el procedimiento propuesto y libero de responsabilidades futuras de cualquier ??ndole al establecimiento de salud y al profesional sanitario que me atiende por no realizar el procedimiento medico sugerido.</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                    <th class="th">C??DULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td"></td>
                    <td class="td"></td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td class="td" style="font-weight: bold;">SI EL PACIENTE NO EST?? EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado tambi??n en caso de ser el paciente menor de edad o presente una incapacidad): </td>

            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL</th>
                    <th class="th">C??DULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;
                        <!--{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}}-->
                    </td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td class="td" style="font-weight: bold;">Si el paciente no acepta el procedimiento sugerido por el profesional y se niega a firmar este ac??pite: </td>
            </thead>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL TESTIGO</th>
                    <th class="th">C??DULA DEL CIUDADANIA</th>
                    <th class="th">FIRMA DEL TESTIGO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <th class="th">23. REVOCATORIA DEL CONSENTIMIENTO INFORMADO</th>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <tr>
                <td style="text-align:justify;padding:10px" class="td">De forma libre y voluntaria, revoco el consentimiento realizado en fecha _______________ y manifiesto expresamente mi deseo de no continuar con los procedimientos m??dicos ambulatorios y que doy por finalizado en esta fecha.
                    Libero de responsabilidades futuras de cualquier ??ndole al establecimiento de salud y al profesional sanitario que me atiende. </td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE COMPLETO DEL PACIENTE</th>
                    <th class="th">C??DULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL PACIENTE O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td"></td>
                    <td class="td"></td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <thead>
                <tr>
                    <td class="td" style="font-weight: bold;">SI EL PACIENTE NO EST?? EN CAPACIDAD DE FIRMAR EL CONSENTIMIENTO INFORMADO (a ser llenado tambi??n en caso de ser el paciente menor de edad o presente una incapacidad): </td>
            </thead>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center; ">
            <thead>
                <tr>
                    <th class="th">NOMBRE DEL REPRESENTANTE LEGAL</th>
                    <th class="th">C??DULA DE CIUDADANIA O PASAPORTE</th>
                    <th class="th">FIRMA DEL REPRESENTANTE LEGAL O HUELLA DACTILAR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">&nbsp;
                        <!--{{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}}-->
                    </td>
                    <td class="td">&nbsp;</td>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
        </table>




    </div>
</body>

</html>
