<!DOCTYPE html>
<html lang="es">
@php
use Carbon\Carbon;
$edad = Carbon::parse($paciente->fecha_nacimiento)->age;
@endphp

<head>
    <meta charset="utf-8" />
    <title>BRONCOSCOPIA CON TOMA DE BIOPSIA</title>
    <style>
        * {
            font-size: 12px;

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
                <td class="td" style="font-weight: bold;">1. CONSENTIMIENTO INFORMADO PARA : BRONCOSCOPIA  CON TOMA DE BIOPSIA</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">2. NOMBRE DEL ESTABLECIMIENTO DE SALUD: {{$empresa->razonsocial}}</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td" style="font-weight: bold;">3. 3. SERVICIOS DEL ESTABLECIMIENTO DE SALUD: SERVICIO MEDICOS</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;">
            <tr>
                <td class="td">4.Fecha : </td>
                <td class="td">5.Hora : </td>
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
        <table class="table" style="width: 100%;">
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
                    <td class="td">Es un procedimiento que, mediante la introducci??n por nariz o boca de un tubo flexible (Broncoscopio), permite la visualizaci??n del ??rbol bronquial as?? como la exploraci??n y toma de muestras de secreciones o tejidos para su an??lisis a fin de aproximarnos a un diagn??stico etiol??gico de la enfermedad causante.</td>
                    <td><img style="height:100px;margin:15px" src="{{public_path('/imagenes/bronco.png')}}" /></td>
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
                    <td style="text-align: justify;" class="td">Como primer paso y con el paciente habitualmente acostado, seg??n el tipo de procedimiento, se usa anestesia general e intubaci??n orotraqueal y en ocasiones anestesia local en las zonas de paso (laringe, tr??quea y bronquios). Se introduce el broncoscopio y tras visualizar todos los bronquios e identificar las posibles lesiones se procede a la toma de muestras. Las muestras pueden ser obtenidas mediante: aspirado bronquial para la recogida de secreciones bronquiales; lavado broncoalveolar, que permitir?? analizar la zona perif??rica del ??rbol respiratorio; cepillado bronquial, que consiste en realizar un raspado de la mucosa bronquial; punci??n transbronquial atravesando las paredes bronquiales en busca de tejidos que quedan ocultos tras los bronquios; biopsia, con pinza, de la superficie bronquial; y/o bien biopsia transbronquial introduciendo la pinza a lo lejos del bronquio, fuera de nuestra visi??n directa, ayudados o no por radioscopia externa.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    13. DURACI??N ESTIMADA DE LA EXPLORACI??N
                </td>
                <td style="width: 70%;" class="td">Aproximadamente 30 a 60 minutos.</td>
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
                    <td style="text-align: justify;" class="td">El estudio permite la visualizaci??n directa del interior del aparato respiratorio, la toma de biopsias y un diagnostico especifico de las patolog??as respiratorias, as?? como un diagn??stico precoz de las enfermedades malignas de la tr??quea, bronquios y pulmones.</td>
                </tr>
            </tbody>
        </table>
        <table class="table" style="width: 100%;text-align:center;">

            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    15. RIESGOS FRECUENTES
                </td>
                <td style="width: 70%;" class="td">Entre las situaciones pueden presentar ronquera, tos, fiebre, dolor local, nauseas o mareos y expectoraci??n de peque??as cantidades de sangre, que suelen auto limitarse y no representan un riesgo para la vida.</td>
            </tr>
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    16. RIESGOS INFRECUENTES
                </td>
                <td style="width: 70%;" class="td">Entre los riesgos pocos frecuentes se pueden presentar hhemorragia, hipotensi??n, hipertensi??n y neumot??rax (entrada de aire al t??rax fuera del pulm??n), especialmente cuando se realizan biopsias transbronquiales. La frecuencia de estas complicaciones es baja.</td>
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
            <tbody>
                <tr>
                    <td class="td">&nbsp;</td>
                </tr>
            </tbody>
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
                    <td style="width: 70%;" class="td"> Entre las alternativas al procedimiento se encuentran: estudios bacteriol??gicos de esputo, citolog??a de esputo y tomograf??a con emisi??n de positrones.</td>
                </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    19. MANEJO POSTERIOR AL PROCEDIMIENTO
                </td>
                <td style="width: 70%;" class="td"> Luego del procedimiento, el paciente es trasladado a la sala de recuperaci??n en donde despu??s de recuperar su nivel de conciencia despu??s de la sedaci??n y comprobada su recuperaci??n, puede ser dado de alta siempre que cuente con una persona que lo acompa??e. No podr?? conducir ning??n tipo de veh??culos en las siguientes 24 horas.</td>
            </tr>
        </table>
        <table class="table" style="width: 100%;text-align:center;">
            <tr>
                <td style="width: 30%;font-weight: bold;" class="td">
                    20. CONSECUENCIAS POSIBLES SI NO SE REALIZA LA EXPLORACI??N
                </td>
                <td style="width: 70%;" class="td"> Mal diagn??stico, diagnostico errado o diagn??stico tard??o de enfermedades del aparato respiratorio bajo.</td>
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