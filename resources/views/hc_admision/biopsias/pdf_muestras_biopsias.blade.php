<!DOCTYPE html>
@php $data = date("Y-m-d"); @endphp
@section('action-content')
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Reporte Muestras de Biopsias</title>
    <style>
        @page {
            margin: 20px 20px;
        }

        * {
            font-size: 14px;
            padding: 0px;

        }

        .table {
            border-collapse: collapse;
            padding: 1px;

        }

        .table,
        .td {
            border: 1px solid black;
            text-align: center;
            color: white;
        }

        .th {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            color: white;
            background-color: #0033cc;
        }

        .new_exl_cabecera {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
            color: black;
            background-color: #D1E9F2;
        }

        .new_exl_cuerpo {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            /*font-weight: bold;*/
            font-size: 12px;
            color: black;
        }

        .pie {
            font-weight: bold;
            color: black;
            background-color: #b3c6ff;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 15px 15px;
        }

        .estilos {
            font-weight: bold;
            color: black;
        }



        .saltoDePagina {
            display: block;
            page-break-before: always;
        }
    </style>
</head>

<body>

    <table style="width: 100%; border: 1px solid black;">
        <tr style="background-color: #D1E9F2;">
            <td colspan="7">
                <center><label style="font-size: 20px; color: black;">{{trans('tecnicof.samplesreport')}}</label></center>
            </td>
        </tr>

    </table>
    <table class="table" style="width: 100%; border: none !important;">
        <thead>
            <tr>
                <th class="new_exl_cabecera">
                    {{trans('tecnicof.patientsname')}}
                </th>
                <th class="new_exl_cabecera">
                    {{trans('tecnicof.id')}}
                </th>
                <th class="new_exl_cabecera">
                    {{trans('tecnicof.typeofinsurance')}}
                </th>
                <th class="new_exl_cabecera">
                    {{trans('tecnicof.description')}}
                </th>
                <th class="new_exl_cabecera">
                    {{trans('tecnicof.bottles')}}
                </th>
                <th class="new_exl_cabecera">
                    {{trans('tecnicof.testsperformedhere')}}
                </th>
            </tr>
        </thead>
        <tbody>
            @php
            $fecha = date('Y-m-d');
            $fechafin = date('Y-m-d');
            $biopsias = Sis_medico\Hc4_Biopsias:: where('estado', '1')->where('muestra_biopsia', '1')->groupBy('hc_id_procedimiento')->get();
            @endphp

            @foreach ($biopsias as $value)

            <tr>
                <th class="new_exl_cuerpo">
                    @if(isset($value->pacientes)){{$value->pacientes->nombre1}} {{$value->pacientes->nombre2}} {{$value->pacientes->apellido1}} {{$value->pacientes->apellido2}} @endif
                </th>
                <th class="new_exl_cuerpo">
                    {{$value->id_paciente}}
                </th>
                <th class="new_exl_cuerpo">
                    {{$value->seguros->nombre}}
                </th>
                <th class="new_exl_cuerpo">
                    {{$value->descripcion_frasco}}
                </th>
                <th class="new_exl_cuerpo">
                    {{$value->numero_frasco}}
                </th>
                <th class="new_exl_cuerpo">
                    @if ($value->muestra_biopsia ==0)
                    No
                    @endif
                    @if ($value->muestra_biopsia ==1)
                    Si
                    @endif
                </th>
            </tr>
            @endforeach

        </tbody>
    </table>



</body>

</html>