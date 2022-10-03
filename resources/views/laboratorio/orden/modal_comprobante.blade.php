<style>
    .centrar {
        text-align: center;
    }
</style>
<div class="modal-content" style="margin-top: 10%;">
    <div class="modal-header">
        <h5 class="modal-title">Pendientes de Laboratorio</h5>
        <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">

                <div id="tabla">
                    <table id="comprExa" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr class='well' style="color: black;">
                                <th style="text-align: center;" tabindex="0">Fecha</th>
                                <th style="text-align: center;" tabindex="0">Cédula</th>
                                <th style="text-align: center;" tabindex="0">Nombre Paciente</th>
                                <th style="text-align: center;" tabindex="0">Valor</th>
                                <th style="text-align: center;" tabindex="0">Generar Comprobante</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            @foreach($examen_comprobante_ingreso as $key=>$val)
                            @php

                            $paciente = Sis_medico\Paciente::where('id',$val->id_paciente)->first();
                            @endphp
                            <tr>
                                <input type="hidden" value="{{$val->id}}" id="id_valor">
                                <td>
                                    {{$val->fecha}}
                                </td>
                                <td>
                                    {{$paciente->id}}
                                </td>
                                <td>
                                    {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}
                                </td>
                                <td>
                                    {{$val->valor}}
                                </td>
                                <td>
                                    <input type="checkbox" id='generar_comprobante{{$key}}' value="{{$val->nuevo_id}}" onchange="cambio({{$key}},this)">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function cambio(id, event) {
        if (event.checked) {
            $.ajax({
                url: "{{route('ordenes.llenar_campos')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'id': event.value,
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    document.getElementById("id_cliente").value = data.query[0].id_paciente;
                    $("#valor_total").val(data.tot[0].valor);
                    document.getElementById("click").click();
                    $("#fecha").val(data.query[0].fecha);
                    $(`#id_cliente option[value='${data.query[0].id_paciente}']`).prop('selected', 'selected').change();
                    $(`#tipo0 option[value='${data.tot[0].id_forma_pago}']`).prop('selected', 'selected').change();
                    $("#fecha0").val(data.tot[0].fecha);
                    $("#cuenta0").val(data.tot[0].cuenta)
                    $(`#valor0`).val(data.tot[0].valor);
                    $("#examen_comprobante_id").val(data.tot[0].id);
                    $('#banco0').val('11');
                    $('#banco0').trigger('change');
                    setTimeout(() => {
                        $('.color' + data.query[0].id).css('background-color', 'red');
                        $('.num_factura' + data.query[0].id).val(data.tot[0].valor).change();
                    }, 2500);

                    document.getElementById("close").click();

                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    }
</script>