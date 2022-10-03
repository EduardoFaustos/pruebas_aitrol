@extends('contable.precio_producto.base')
@section('action-content')
<style>
    .caja {
        display: flex;
        justify-content: space-between;
    }

    .input-style {
        border-radius: 5px;

    }
</style>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.precio_aprobado')}}</a></li>
            <li class="breadcrumb-item"><a href="{{route('importaciones.index')}}">Crear</a></li>
        </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
        <div class="box box-solid" style="padding:10px">
            <div class="container">
                <div class="col-md-4">
                    <label for="">Productos</label>
                    <select id="producto" name="producto" onchange="buscarPrecios()" class="form-control select2_productos" style="width:100%" required>
                    </select>
                </div>
            </div>
            <br>

            <div class="container">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:10%;">Precio</th>
                            <th style="width:50%;">Obeservacion</th>
                            <th style="width:10%;">Importante</th>
                            <th style="width:10%;">Aprobado</th>
                            <th style="width:10%;">
                                <button onclick="crearFila()" type="button" class="btn btn-success">
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="detalles">

                    </tbody>

                    <input type="hidden" value="0" id="contador">
                </table>
            </div>
            <div style="text-align: center;">
                <button onclick="guardar()" type="button" class="btn btn-success">Guardar</button>
            </div>

        </div>
    </form>
    <br>
</section>

<script>
    const crearFila = () => {
        let id = document.getElementById('contador').value;
        let fila = `
            <tr>
                <td> <input autocomplete="off" type="text" class="form-control input-style" placeholder="Precio" name="precio[]" id="precio${id}"> </td>
                <td> <textarea class="form-control input-style" name="descripcion[]" id="descripcion${id}"></textarea></td> 
                <td> 
                    <input onchange="verificar(this, 'importante_val${id}')" type="checkbox" id="importante${id}">
                    <input type="hidden" name="importante[]" id="importante_val${id}" value="0">
                </td>
                <td> 
                    <input onchange="verificar(this, 'aprobado_val${id}')" type="checkbox"  id="aprobado${id}">
                    <input  type="hidden" name="aprobado[]" id="aprobado_val${id}" value="0">
                </td>
                <th>
                    <button onclick="crearFila()" type="button" class="btn btn-success">Aprobar Precio</button>
                </th>
            </tr>`

            document.getElementById('contador').value = parseInt(id) + 1;
        $("#detalles").append(fila)
    }

    const buscarPrecios = () => {
        $.ajax({
            type: 'post',
            url: `{{ route('importaciones.PrecioProductoAprobado.buscarTabla') }}`,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_producto': $("#producto").val()
            },
            success: function(data) {
                $("#detalles").empty();
                $("#detalles").append(data.table);
            },
            error: function(data) {

            }
        });
    }

    const guardar = () => {
        $.ajax({
            type: 'post',
            url: `{{ route('importaciones.PrecioProductoAprobado.store') }}`,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#crear_factura").serialize(),
            success: function(data) {
                console.log(data);
            },
            error: function(data) {

            }
        });
    }

    const verificar = (e, id) => {
        console.log(e.checked, id);
        if(e.checked){
            document.getElementById(id).value = 1;
        }else{
            document.getElementById(id).value = 0;
        }
    }
</script>

<script>
    //funciones Secundarias
    window.onload = function() {
        $('.select2_productos').select2({
            tags: false
        });
        $('.select2_productos').select2({
            placeholder: "Seleccione un producto...",
            allowClear: true,
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: '{{route("importaciones.productos")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
    }
</script>



@endsection