<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>MSP</title>
    <style>
        * {
            margin: 0px;
            padding: 5px;

        }

        .table {
            border-collapse: collapse;
            padding: -1px;

        }

        .table,
        .td {
            border: 1px solid black;

        }

        .th {
            border: 1px solid black;
            text-align: left;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 0px auto;
        }

        span {
            font-size: 16px;
        }

        .linea {
            border-top: 1px solid black;
            height: 2px;
            padding: 0;
            margin: 20px 10px auto 10px;
        }

        .linea2 {
            border-top: 1px solid black;
            height: 2px;
            padding: 0;
            max-width: 600px;
            margin: 20px 10px auto 10px;
        }

        .linea3 {
            margin-top: 1px;
            border-top: 1px solid black;
            height: 2px;
            padding: 0;
            max-width: 600px;
        }
    </style>


</head>

<body>
    <div style="width: 40%;float:left;">
        <div style="padding: 10px;">
            <div style="text-align:center;padding:5px">
                <span>Dr. Salvador Gustavo Peralta Perez</span>
            </div>
            <div style="text-align:center;margin-top:2px">
                <span>Medico Psiquiatra</span>
            </div>

            <div style="text-align:center;margin-top:2px">
                <span>Especializado en el instituto de investigaciones Neuropsiquiatricas "Lopez Ibor" de Madrid-España</span>
            </div>

            <div style="text-align:center;margin-top:2px">
                <span>Lorem ipsum es el texto que se usa habitualmente en diseño gráfico o de moda en demostraciones de tipografías o de borradores de diseño para probar el diseño visual antes de insertar el texto final.
                    <!--  -->
                </span>
            </div>
            <div style="text-align:right">
                <span style="color:red;font-size:30px">00000000000</span>
            </div>
        </div>

        <span style="text-align:left;margin-top:8px">{{$agenda->empresa->ciudad}}, {{$agenda->fechaini}}</span>
        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Ciudad/Fecha</span>
        </div>

        <span style="text-align:left;margin-top:8px">{{$agenda->paciente->nombre1}} {{$agenda->paciente->nombre2}} {{$agenda->paciente->apellido1}} {{$agenda->paciente->apellido2}}</span>
        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Apellidos/Nombres del paciente</span>
        </div>

        <table style="width: 100%;">
            <tr>
                <th style="text-align: left;width:10%">
                    <span>Edad</span>
                </th>
                <th>
                <span style="text-align:left;margin-top:8px">{{$agenda->paciente->fecha_nacimiento}}</span>
                    <div class="linea2"></div>
                </th>
                <th style=" text-align: left;width:10%">
                    <span>C.C del paciente</span>
                </th>
                <th>
                <span style="text-align:left;margin-top:8px">{{$agenda->id_paciente}} </span>
                    <div class="linea2"></div>
                </th>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <th style=" text-align: left;width:10%">
                <span style="text-align:left;margin-top:8px">{{$historia_cli->hcid}} </span>
                <div class="linea2"></div>
                    <span>N°. de Historia Clínica</span>
                   
                </th>
               
                <th style=" text-align: down;width:10%">
             
                <div class="linea2"></div>
                    <span>CIE 10</span>
                </th>
                
            </tr>
        </table>

        <div style="text-align:left;margin-top:8px">
            <span>Consulta Privada</span>
            <div class="linea3"></div>
        </div>
        <span style="text-align:left;margin-top:8px">{{$receta}} </span>
              
        <div style="text-align:left;margin-top:8px">
            <span>Nombre generico del medicamento, forma farmaceutica y concentración</span>
        </div>
        
        <div class="linea"></div>

        <div style="text-align:left;margin-top:8px">
            <span>Cantidad en letras y numeros del medicamento</span>
        </div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Dosis frecuencia y via de administración</span>
        </div>

        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Duración del tratamiento</span>
        </div>
        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Dr. Salvador Peralta Perez</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Nombre y apellidos del prescriptor</span>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Medico Psiquiatrico</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Profesion del prescriptor</span>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>1006R 10 2635</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Numero del registro del titulo del prescriptor</span>
        </div>

        <table style="width: 100%;">
            <tr>
                <th style="text-align: left;width:10%">
                    <span>Sello</span>
                </th>
                <th>

                </th>

                <th style=" text-align: left;width:10%">
                    <span>Firma</span>
                </th>
                <th>
                    <div class="linea2"></div>
                </th>
            </tr>
        </table>
    </div>

    <div style="width: 10%;float:right">

    </div>

    <div style="width: 40%;float:right;">
        <div style="padding: 10px;">
            <div style="text-align:center;padding:5px">
                <span>Dr. Salvador Gustavo Peralta Perez</span>
            </div>
            <div style="text-align:center;margin-top:2px">
                <span>Medico Psiquiatra</span>
            </div>

            <div style="text-align:center;margin-top:2px">
                <span>Especializado en el instituto de investigaciones Neuropsiquiatricas "Lopez Ibor" de Madrid-España</span>
            </div>

            <div style="text-align:center;margin-top:2px">
                <span>Lorem ipsum es el texto que se usa habitualmente en diseño gráfico o de moda en demostraciones de tipografías o de borradores de diseño para probar el diseño visual antes de insertar el texto final.
                    <!--  -->
                </span>
            </div>
            <div style="text-align:right">
                <span style="color:red;font-size:30px">00000000000</span>
            </div>
        </div>
        
        <span style="text-align:left;margin-top:8px">{{$agenda->empresa->ciudad}}, {{$agenda->fechaini}}</span>
        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Ciudad/Fecha</span>
        </div>
        <div class="linea"></div>
        <div style="text-align:left;margin-top:8px">
            <span>Apellidos/Nombres del paciente</span>
        </div>

        <table style="width: 100%;">
            <tr>
                <th style=" text-align: left;width:10%">
                    <span>C.C del paciente</span>
                </th>
                <th>
                    <div class="linea2"></div>
                </th>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <th style=" text-align: left;width:10%">
                    <span>Indicaciones</span>
                </th>
            </tr>
        </table>

        <div style="height:200px">

        </div>

        <div style="text-align:left;margin-top:8px">
            <span>Consulta Privada</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Nombre del establecimiento de salud</span>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Dr. Salvador Peralta Perez</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Nombre y apellidos del prescriptor</span>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Medico Psiquiatrico</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Profesion del prescriptor</span>
        </div>

        <div style="text-align:left;margin-top:8px">
            <span>1006R 10 2635</span>
            <div class="linea3"></div>
        </div>
        <div style="text-align:left;margin-top:8px">
            <span>Numero del registro del titulo del prescriptor</span>
        </div>

        <table style="width: 100%;">
            <tr>
                <th style="text-align: left;width:10%">
                    <span>Sello</span>
                </th>
                <th>

                </th>

                <th style=" text-align: left;width:10%">
                    <span>Firma</span>
                </th>
                <th>
                    <div class="linea2"></div>
                </th>
            </tr>
        </table>
    </div>
</body>

</html>