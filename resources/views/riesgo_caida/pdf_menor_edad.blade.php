<!DOCTYPE html>
@php $data = date("Y-m-d"); @endphp
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>{{trans('tecnicof.fallrisk2')}} </title>
    <style>
        * {
            font-size: 18px;
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
    <table class="table" style="width: 100%;">
        <tr style="padding: 20px;">
            <td>
                <img src="{{ public_path('imagenes/logo_riesgo.png')}}" alt="logo" />
            </td>
            <th style="text-align: center !important;width:50%;font-size:30px !important;">
                {{trans('tecnicof.macdem')}}
            </th>
            <th style="width:25%;padding:10px;">
                <table class="table" style="padding: none;width:100%;">
                    <tr>
                        <th style="border-bottom:1px solid black;border-right:1px solid black;">{{trans('tecnicof.version')}}</th>
                        <td style="border-bottom:1px solid black;">1</td>
                    </tr>
                    <tr>
                        <th style="border-bottom:1px solid black;border-right:1px solid black;">{{trans('tecnicof.code')}}</th>
                        <td style="border-bottom:1px solid black;">DNCSS-MSP-008</td>
                    </tr>
                    <tr>
                        <th style="border-right:1px solid black;">{{trans('tecnicof.date')}}</th>
                        <td>{{$data}}</td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #EAF2F5;height:45px !important;">
                {{trans('tecnicof.kids')}}
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:55px !important;">
                {{trans('tecnicof.variables')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;height:55px !important;">
                {{trans('tecnicof.scores')}}
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <td rowspan="5" style="border-right:1px solid black;height:25%">
                1.{{trans('tecnicof.age')}}
            </td>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                {{trans('tecnicof.newborn')}}
            </td>
            <td style="border-bottom:1px solid black;">
                2
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                {{trans('tecnicof.youngerinf')}}
            </td>
            <td style="border-bottom:1px solid black;">
                2
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                {{trans('tecnicof.olderinfant')}}
            </td>
            <td style="border-bottom:1px solid black;">
                3
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                {{trans('tecnicof.preschoool')}}
            </td>
            <td style="border-bottom:1px solid black;">
                3
            </td>
        </tr>
        <tr>
            <td style="border-right:1px solid black;border-bottom:1px solid black;width:55%;">
                {{trans('tecnicof.schoolage')}}
            </td>
            <td>
                1
            </td>
        </tr>
        <tr>

        </tr>
    </table>
    <table style="width: 100%;border:1px solid black;height:8% !important;margin-top:12px;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;border-right:1px solid black;">
                {{trans('tecnicof.risk')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;border-right:1px solid black;">
                {{trans('tecnicof.score')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;">
                {{trans('tecnicof.action')}}
            </th>
        </tr>
    </table>
    <table class="table" style="width:100%;">
        @if($camilla_gestion->nivel_riesgo>=0 && $camilla_gestion->nivel_riesgo <= 1) <tr>
            <th style="height:60px !important;width:60%!important;" class="th"><img src="{{ public_path('imagenes/bajo_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
            <th class="th" style="width:20.7%;">
                <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
            </th>
            <th class="th">
                <div style="margin-top:10 !important;">{{trans('tecnicof.nursingc')}}</div>
            </th>
            </tr>
            @endif
            @if($camilla_gestion->nivel_riesgo >=2 && $camilla_gestion->nivel_riesgo<=3) <tr>
                <th style="height:80px !important;width:60%;" class="th"><img src="{{ public_path('imagenes/medio_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
                <th class="th" style="width:10.7%;">
                    <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
                </th>
                <th class="th">
                    <div style="margin-top:10 !important;font-size:10px !important;">{{trans('tecnicof.implementplan')}}</div>
                </th>
                </tr>
                @endif
                @if($camilla_gestion->nivel_riesgo >=4 && $camilla_gestion->nivel_riesgo<=6) <tr>
                    <th style="height:80px !important;width:58%;" class="th"><img src="{{ public_path('imagenes/alto_flecha.png')}}" style="margin-top:15px;height:50px;" alt="logo" /></th>
                    <th class="th" style="width:22%;">
                        <div style="margin-top:10 !important;font-size:15px">{{$camilla_gestion->nivel_riesgo}}</div>
                    </th>
                    <th class="th">
                        <div style="margin-top:10 !important;font-size:15px !important;">{{trans('tecnicof.measures')}}</div>
                    </th>
                    </tr>
                    @endif
    </table>
    @php
    use Carbon\Carbon;
    $edad = Carbon::parse($datosUsuarios->fecha_nacimiento)->age;
    @endphp
    <table class="table" style="width:100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                {{trans('tecnicof.names')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                {{trans('tecnicof.lname')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                {{trans('tecnicof.dateb')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:60px !important;">
                {{trans('tecnicof.age')}}
            </th>
        </tr>
        <tr>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$datosUsuarios->nombre1}} {{$datosUsuarios->nombre2}}
            </td>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$datosUsuarios->apellido1}} {{$datosUsuarios->apellido2}}
            </td>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$datosUsuarios->fecha_nacimiento}}
            </td>
            <td style="border-right:1px solid black;height:60px !important;">
                {{$edad}}
            </td>
        </tr>
    </table>
</body>

</html>