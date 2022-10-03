<table>
    <table class="table">
        <thead>
            <th class="centrar" scope="col">Examen</th>
            <th class="centrar" scope="col">Factura</th>
            <th class="centrar" scope="col">Fecha</th>
            <th class="centrar" scope="col">Cédula</th>
            <th class="centrar" scope="col">Paciente</th>
            <th class="centrar" scope="col">Monto Factura</th>
            <th class="centrar" scope="col">Saldo</th>
            <th class="centrar" scope="col">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($query as $val)
            <tr>
                <td class="centrar">{{$val->id}}</td>
                <td class="centrar">{{$val->comprobante}}</td>
                <td class="centrar">{{$val->fecha}}</td>
                <td class="centrar">{{$val->cedula}}</td>
                <td class="centrar">{{$val->nombre}}</td>
                <td class="centrar">{{$val->valor}}$</td>
                <td class="centrar">{{$val->valor - $val->valor_adelanto}}</td>
                <td class="centrar">
                    <i class="fa fa-arrow-right" aria-hidden="true" style="cursor:pointer" onclick="agregarValor({{$val->id}})"></i>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</table>
<script type="text/javascript">
    function agregarValor(id) {
        $.ajax({
            url: "{{route('ordenes.agregar_valor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': id
            },
            type: 'GET',
            dataType: 'html',
            success: function(datahtml) {
                $("#tabla").html(datahtml);

            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }
</script>