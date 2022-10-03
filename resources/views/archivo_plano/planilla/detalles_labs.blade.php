<style type="text/css">
    .titulo {
        background-color: #ABD8F1;
        margin-left: 0px;
        margin-right: 0px;
        height: 30px;
        line-height: 30px;
        color: #000000;
        text-align: center;
    }


    table,
    th,
    td {
        border: 1px solid black;
    }

    .centro {
        text-align: center;
    }
</style>

<div style="text-align:center;">
    <table border="1" width="100%">

        <tr class="titulo">

            <th style="text-align: center;">Tarifario</th>
            <th>
                <p style="text-align: center;">Nombres de Exámenes</p>
                <p style="text-align: center;">Tipo: @if($orden->protocolo!=null)
                    <span class="label pull-center bg-primary" style="font-size:10px" style="text-align: left;">{{$orden->protocolo->pre_post}}</span>
                    @endif
                    &nbsp; Valor: {{$orden->total_valor}} &nbsp; Cantidad:{{$orden->cantidad}}
                </p>
                </p>

            </th>
            <th style="text-align: center;">Resultados</th>
            <th style="text-align: center;">Acción</th>
        </tr>

        <tr>
            @foreach ($detalles as $value)
            @php

            $nombre_examen=Sis_medico\Examen::where('id',$value['id_examen'])->first();
            @endphp
            <td>{{$nombre_examen->tarifario}}</td>
            <td>@if(is_null($nombre_examen->nombre_iess)) {{$nombre_examen->nombre}} @else {{$nombre_examen->nombre_iess}} @endif</td>
            <td>@if($value['certificados'] == 0 ) <p style="color:red"> No se ha certificado</p> @else <p style="color:red">Certificado</p> @endif</td>
            <td>@if($value['certificados'] != 0 ) <button type="button" onclick='enviarUno("{{$value["id_examen_orden"]}}")' class="btn btn-danger form-control" style="margin:1px;padding:2px;"> enviar </button> @else <button type="button" class="btn btn-success" style="margin:1px;padding:2px;"> <i class="fa fa-times" aria-hidden="true"></i>
                </button> @endif
            </td>
        </tr>
        @endforeach

    </table>
</div>
<script type="text/javascript">
    function enviarUno(id_examen_orden) {

        console.log(id_examen_orden);
        $.ajax({
            type: 'post',
            url: "{{ route('archivo_plano.guardar_plantilla_iess') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_examen_orden' : id_examen_orden
            },
            success: function(data) {
                console.log(data);
                
            },
            error: function(data) {
                console.log(data);
                //swal("Complete todos los campos");
            }
        });

    }
</script>