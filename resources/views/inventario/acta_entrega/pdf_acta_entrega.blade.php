<!DOCTYPE html>
@section('action-content')
<html>


<head>
    <meta charset="utf-8" />
    <title> Acta de Entrega </title>

</head>

<body>
    @php
    $fechaActual = date('d-m-Y');
    $user = Auth::user();
    $id_usuario = $user->id;

    @endphp
    <div style="text-align: left">
        @if(!is_null($empresa->logo))
        <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:200px;height: 100px">
        @endif
    </div>


    <div style="text-align: right;">
        <spam>Fecha: {{$fechaActual}}</spam>
    </div>


    <div >
       &nbsp;
    </div>

    <div style="text-align: center;">
        <spam>
            <h3> ACTA DE ENTREGA </h3>
        </spam>
    </div>
    <br></br>
    <br></br>
    <br></br>
    <div style="text-align: center;">
        <spam> La presente acta tiene por objeto la constancia de la entrega-recepción del siguiente insumo medico: </spam>
    </div>
    <br></br>
    <br></br>
    <br></br>
    <style>
        .new_exl_cabecera {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
            color: black;
            background-color: #D1F2EB;
        }

        .new_exl_cuerpo {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            /*font-weight: bold;*/
            font-size: 12px;
            color: black;
        }
    </style>


    <table class="table" style="width: 100%; border: none !important;">
        <thead>
            <tr>
                <th class="new_exl_cabecera">SERIE</th>
                <th class="new_exl_cabecera">NOMBRE</th>
                <th class="new_exl_cabecera">LOTE</th>
                <th class="new_exl_cabecera">CANTIDAD</th>
                <th class="new_exl_cabecera">CANTIDAD DE USO</th>
                <th class="new_exl_cabecera">TOTAL</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rec as $x)
            <tr>
                <td class="new_exl_cuerpo">{{$serie}}</td>
                <td class="new_exl_cuerpo">B</td>
                <td class="new_exl_cuerpo">c</td>
                <td class="new_exl_cuerpo">d</td>
                <td class="new_exl_cuerpo">e</td>
                <td class="new_exl_cuerpo">f</td>
                <td class="new_exl_cuerpo">g</td>
                <td class="new_exl_cuerpo">g</td>
            </tr>
        @endforeach
        </tbody>

    </table>

    <br></br>
    <br></br>
    <br></br>
    <br></br>
    <br></br>
    <table>
        <thead>
            <tr>
                <th class="new_exl_cuerpo" style="vertical-align:text-top; width:330px; height:200px;">RECIBE</th>
                <th class="new_exl_cuerpo" style="vertical-align:text-top; width:330px; height:200px;">ENTREGA</th>
            </tr>
        </thead>

    </table>




</body>

</html>