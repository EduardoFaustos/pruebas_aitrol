<!DOCTYPE html>
@php $data = date("Y-m-d"); @endphp
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>{{trans('tecnicof.fallrisk1')}}</title>
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

    <table class="table" style="width: 100%;">
        <tr style="padding: 20px;">
            <td>
                <img src="{{ public_path('imagenes/logo_riesgo.png')}}" alt="logo" />
            </td>
            <th style="text-align: center !important;width:50%;font-size:30px !important;">
                {{trans('tecnicof.macdem')}}
            </th>
            <th style="width:15%;padding:10px;">
                <table border="1" style="padding: none;width:100%;">
                    <tr>
                        <th>{{trans('tecnicof.version')}}</th>
                        <td style="width: 65px;">1</td>
                    </tr>
                    <tr>
                        <th>{{trans('tecnicof.code')}}</th>
                        <td>DNCSS-MSP-008</td>
                    </tr>
                    <tr>
                        <th>{{trans('tecnicof.date')}}</th>
                        <td>{{$data}}</td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #EAF2F5;height:45px !important;">
                {{trans('tecnicof.adults')}}
            </th>
        </tr>
    </table>
    <table class="table" style="width: 100%;">
        <tr>
            <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;height:45px !important;">
                {{trans('tecnicof.variables')}}
            </th>
            <th class="th" style="background-color: #C1E1E0;height:45px !important;">
                {{trans('tecnicof.scores')}}
            </th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">1. {{trans('tecnicof.fall')}}</th>
            <th class="th">@if(($camilla_gestion->caida_previa)== 0) NO @elseif(($camilla_gestion->caida_previa)== 25) {{trans('tecnicof.yes')}} @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">2. {{trans('tecnicof.comorbidities')}}</th>
            <th class="th">@if(($camilla_gestion->comorbilidades)== 0) NO @elseif(($camilla_gestion->comorbilidades)== 15) {{trans('tecnicof.yes')}} @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">3. {{trans('tecnicof.ambulation')}}</th>
            <th class="th">@if(($camilla_gestion->deambular)== 0) {{trans('tecnicof.none')}} @elseif(($camilla_gestion->deambular)== 15) {{trans('tecnicof.crutch')}} @elseif(($camilla_gestion->deambular)== 30) {{trans('tecnicof.leans')}} @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">4. {{trans('tecnicof.venoclysis')}}</th>
            <th class="th">@if(($camilla_gestion->venoclisis)== 0) NO @elseif(($camilla_gestion->venoclisis)== 20) {{trans('tecnicof.yes')}} @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">5. {{trans('tecnicof.gait')}}</th>
            <th class="th">@if(($camilla_gestion->marcha)== 0) {{trans('tecnicof.bedrest')}} @elseif(($camilla_gestion->marcha)== 10) {{trans('tecnicof.weak')}} @elseif(($camilla_gestion->marcha)== 20) {{trans('tecnicof.limited')}} @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">6. {{trans('tecnicof.mental')}}</th>
            <th class="th">@if(($camilla_gestion->estado_mental )== 0) {{trans('tecnicof.recognizes')}} @elseif(($camilla_gestion->estado_mental)== 15) {{trans('tecnicof.overestimates')}} @endif</th>
        </tr>
        <tr>
            <th style="height:60px !important;" class="th">7. {{trans('tecnicof.final')}}</th>
            <th class="th">{{$camilla_gestion->nivel_riesgo}}</th>
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
        @if($camilla_gestion->nivel_riesgo>=0 && $camilla_gestion->nivel_riesgo < 25) <tr>
            <th style="height:60px !important;width:80%;" class="th"><img src="{{ public_path('imagenes/bajo_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
            <th class="th" style="width:10.7%;">
                <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
            </th>
            <th class="th">
                <div style="margin-top:10 !important;">{{trans('tecnicof.nursingc')}}</div>
            </th>
            </tr>
            @endif
            @if($camilla_gestion->nivel_riesgo >= 25 && $camilla_gestion->nivel_riesgo<=50) <tr>
                <th style="height:60px !important;width:80%;" class="th"><img src="{{ public_path('imagenes/medio_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
                <th class="th" style="width:10.7%;">
                    <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
                </th>
                <th class="th">
                    <div style="margin-top:10 !important;font-size:10px !important;">{{trans('tecnicof.implementplan')}}</div>
                </th>
                </tr>
                @endif
                @if($camilla_gestion->nivel_riesgo>=50)
                <tr>
                    <th style="height:60px !important;width:80%;" class="th"><img src="{{ public_path('imagenes/alto_flecha.png')}}" style="margin-top:15px;" alt="logo" /></th>
                    <th class="th" style="width:10.7%;">
                        <div style="margin-top:10 !important;">{{$camilla_gestion->nivel_riesgo}}</div>
                    </th>
                    <th class="th">
                        <div style="margin-top:10 !important;font-size:10px !important;">{{trans('tecnicof.measures')}}</div>
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