<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTADO CONCILIACION BANCARIA</title>
</head>

<style>
    #page_pdf {
        width: 100%;
        margin: 15px auto 10px auto;
    }

    #importaciones_head {
        width: 100%;
        margin-bottom: 10px;
    }


    .info_empresa {
        width: 50%;
        text-align: left;
    }



    table {
        border-collapse: collapse;
        font-size: 12pt;
        font-family: 'sans-serif';
        width: 100%;
    }


    table tr:nth-child(odd) {
        background: #FFF;
    }

    table td {
        text-align: center;
        color: #000000;
        padding: 10px;
    }

    table th {
        text-align: center;
        color: #000000;
        font-size: 14px;
    }

    * {
        font-family: 'sans-serif' !important;
    }

    .table_1 {

        margin-right: 0px;
        margin-top: 0px;
        margin-left: 0px;
        margin-bottom: 0px;
        font-size: 12px;
        width: 100%;

    }

    .table_2 {

        margin-right: 0px;
        margin-top: 0px;
        margin-left: 0px;
        margin-bottom: 0px;
        font-size: 20px;
        width: 100%;
    }

    .table_3 {

        margin: 15px auto;
        margin-top: 20px;
        font-size: 12px;
        width: 20%;
    }


    .titulo_css {

        text-align: center;
        color: black;
    }

    .page_break {
        page-break-before: always;
    }
</style>

<body>
    <div>
        <table border=1, style="font-size: 12px" width="100%">
            <thead>
                <tr>
                    <td colspan="8" , style="font-size: 18px;width:100%" , align="center">{{$x}}</td>

                </tr>
                <tr>
                    <td colspan="8" ,style="font-size: 18px;width:100%" , align="center">CONCILIACION BANCARIA</td>
                </tr>
            </thead>
            <tbody>
                @foreach($libro_mes as $lm)
                @foreach ($consulta_mes as $value)
                <tr>
                    <td align="center" style="width: 15%">Saldo Anterior</td>
                    <td align="center" style="width: 15%">{{$value->saldo_anterior}}</td>
                    <td colspan="4" style="width: 40%"></td>
                    <td align="center" style="width: 15%">Saldo Anterior</td>
                    <td align="center" style="width: 15%">{{$lm->saldo_anterior}}</td>

                </tr>
                <tr>
                    <td align="center" style="width: 15%"> (+) Depositos </td>
                    <td align="center" style="width: 15%">{{$value->valor_depositos}}</td>
                    <td colspan="4" style="width: 40%"></td>
                    <td align="center" style="width: 15%"> (+) Depositos </td>
                    <td align="center" style="width: 15%">{{$lm->valor_depositos}}</td>

                </tr>
                <tr>
                    <td align="center" style="width: 15%"> (+) Valor Acreditado </td>
                    <td align="center" style="width: 15%">{{$value->valor_acreditado}}</td>
                    <td colspan="4" style="width: 40%"></td>
                    <td align="center" style="width: 15%"> (+) Valor Acreditado </td>
                    <td align="center" style="width: 15%">{{$lm->valor_acreditado}}</td>
                </tr>
                <tr>
                    <td align="center" style="width: 15%"> (-) Cheques Pagado </td>
                    <td align="center" style="width: 15%">{{$value->valor_cheques}}</td>
                    <td colspan="4" style="width: 40%"></td>
                    <td align="center" style="width: 15%"> (-) Cheques Pagado </td>
                    <td align="center" style="width: 15%">{{$lm->valor_cheques}}</td>
                </tr>
                <tr>
                    <td align="center" style="width: 15%"> (-) Valores Debitados</td>
                    <td align="center" style="width: 15%">{{$value->valor_debitado}}</td>
                    <td colspan="4" style="width: 40%"></td>
                    <td align="center" style="width: 15%"> (-) Valores Debitados</td>
                    <td align="center" style="width: 15%">{{$lm->valor_debitado}}</td>

                </tr>
                <tr>
                    <td align="center" style="width: 15%"> Saldo Según Bancos </td>
                    <td align="center" style="width: 15%">{{$value->saldo_actual}}</td>
                    <td colspan="4" style="width: 40%"></td>
                    <td align="center" style="width: 15%"> Saldo Según Libros </td>
                    <td align="center" style="width: 15%">{{$lm->saldo_actual}}</td>
                </tr>
                @endforeach
                @endforeach
                <tr>
                    <td colspan="8" style="font-size: 18px;width:100%" , align="center">LISTADO DE DOCUMENTOS PENDIENTES</td>
                </tr>

                <tr>

                    <th>Fecha</th>
                    <th> Tipo </th>
                    <th> Id Asiento </th>
                    <th> Detalle </th>
                    <th> Valor </th>
                    <th> Secuencia </th>
                    <th> Cheque </th>
                    <th> Beneficiario </th>

                </tr>
                @foreach ($pendientes as $pen)
                <tr>

                    <td align="center">{{$pen->fecha}}</td>
                    <td align="center">{{$pen->tipo}}</td>
                    <td align="center">{{$pen->id_asiento}}</td>
                    <td align="center">{{$pen->detalle}}</td>
                    <td align="center">{{$pen->valor}}</td>
                    <td align="center">{{$pen->secuencia}}</td>
                    <td align="center">{{$pen->cheque}}</td>
                    <td align="center">{{$pen->beneficiario}}</td>


                </tr>

                @endforeach



        </table>

    </div>


</body>

</html>