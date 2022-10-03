@extends('laboratorio.orden.base')
@section('action-content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

<style>
    .contenedor {
        max-width: 95%;
        margin: 0 auto;
    }

    * {
        font-family: 'Poppins', sans-serif;
    }

    .titulo {
        font-weight: bold;
        color: red;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="col-md-12">
                    <h3 class="titulo">Empresa: {{ $empresa->id }} - {{ $empresa->nombrecomercial }}</h3>
                </div>
                <div class="col-md-12">
                    <h3 class="box-title">Plantilla</h3>
                </div>
                <div class="col-md-12 d-flex ">
                    <div class="col-md-11">
                        <h5><span style="font-weight: bold;">Paciente:</span> {{$orden->id_paciente}} - {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }}</h5>
                    </div>
                    <div class="col-md-1">
                        <a href="{{route('orden.index')}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="box box-primary contenedor">
            <table class="table">
                <thead>
                    <th width="20%">Serie</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                    <th>Accion</th>
                </thead>
                <tbody id="table_insumos">
                    @if(isset($planilla->detalles))
                    @foreach($planilla->detalles as $value)
                    <tr>
                        <td>{{$value->serie}}</td>
                        <td>{{$value->producto->nombre}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>{{ $value->cantidad }}</td>
                        <td>{{ $value->usuario->nombre1 }} {{ $value->usuario->apellido1 }}</td>
                        <td><button class='btn btn-danger' onclick="eliminar_det('{{$value->id}}')">Delete</button></td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td><input onchange="buscarProducto();" style="border-radius: 5px;" type="text" class="form-control" id="serie_prod"></td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="col-md-12 box" style="text-align: center;padding-bottom: 15px;">
        <h3><b> Plantilla </b></h3>
        <div class="col-md-12" style="text-align: center;display: flex;justify-content: center;">
            <div class="col-md-2 row">
                <b>Seleccione: </b>
            </div>
            <div class="col-md-3 row">
                <select class="form-control select2_plantilla" name="plantilla" id="plantila">
                    <option value="">Seleccione...</option>
                    @foreach($plantillas as $value)
                    <option value="{{$value->id}}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button onclick="cargarPlantilla()" class="btn btn-warning">Cargar</button>
            </div>
        </div>
    </div>

    <div class="col-md-12 box" style="text-align: center;padding-bottom: 15px;">
        <h3><b> Derivados </b></h3>
        <div class="col-md-12" style="text-align: center;display: flex;justify-content: center;">
            <div class="col-md-2 row">
                <b>Seleccione: </b>
            </div>
            <div class="col-md-3 row">
                <select class="form-control select2_examenes" name="id_examen" id="id_examen" onchange="busca_examen();">
                    <option value="">Seleccione...</option>
                    @foreach($examenes as $value)
                    <option value="{{$value->id}}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 row">
                <select class="form-control select2_examenes" name="val_derivado" id="val_derivado">
                    <option value="">Seleccione...</option>

                </select>
            </div>
            <div class="col-md-2">
                <button onclick="guardar_derivado()" class="btn btn-warning">Agregar</button>
            </div>

        </div>
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Examen</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla_derivado">
                    @if(isset($planilla_derivado->detalles))
                    @foreach($planilla_derivado->detalles as $det)

                    <tr>
                        <td>@if(isset($det->derivado)) {{$det->derivado->examen->nombre}} @endif</td>
                        <td>@if(isset($det->derivado)) {{$det->derivado->tipo_derivado->nombre}} @endif</td>
                        <td>@if(isset($det->derivado)) {{$det->derivado->valor}} @endif</td>
                        <td>
                            <button class="btn btn-danger" onclick="eliminar_det('{{$det->id}}')"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>


</section>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.onload = function() {
        $('.select2_plantilla').select2({
            tags: false
        });

        $('.select2_examenes').select2({
            tags: false
        });
    }

    function eliminar_det(id_det) {
        Swal.fire({
            title: '¿Desea eliminar este insumo?',
            text: "No puedes revertir esta acccion!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{ url('labs/plantilla_labs/eliminar_det') }}/"+id_det,
                    datatype: 'json',
                    success: function(data) {
                        alertas(data.status, data.status, data.msj)
                        setTimeout(function() {
                            location.reload()
                        }, 2000)

                    },
                    error: function(data) {

                    }
                })

            }
        })
    }

    function busca_examen() {
        let id_examen = document.getElementById('id_examen').value;
        $.ajax({
            type: 'get',
            url: `{{ route('plantillacontrollabs.busca_examen') }}`,
            datatype: 'json',
            data: {
                'id_examen': id_examen,
            },
            success: function(data) {
                $.each(data, function(key, registro) {
                    $("#val_derivado").append('<option value=' + registro.id + '>' + registro.nombre_tipo + '-' + registro.valor + '</option>');
                });
            },
            error: function(data) {

            }
        })

    }
    const buscarProducto = () => {
        $.ajax({
            type: 'get',
            url: `{{ route('plantilla.buscarProducto') }}`,
            datatype: 'json',
            data: {
                'serie': document.getElementById('serie_prod').value,
                'id_orden': `{{$orden->id}}`
            },
            success: function(data) {
                if (data.status == 'success') {
                    $("#table_insumos").append(data.fila)
                    document.getElementById('serie_prod').value = ''
                } else {
                    alertas(data.status, (`${data.status}...`).toUpperCase(), data.msj)
                }
            },
            error: function(data) {
                alertas(`error`, `Error...`, `Ha ocurrido un error`)

            }

        })
    }



    const cargarPlantilla = () => {
        let planilla = document.getElementById('plantila').value;
        $.ajax({
            type: 'get',
            url: `{{ route('plantilla.cargarPanltilla') }}`,
            datatype: 'json',
            data: {
                'id_plantilla': planilla,
                'id_orden': `{{$orden->id}}`

            },
            success: function(data) {
                if (data.status == "success") {
                    $("#table_insumos").append(data.fila)
                    if (data.falta != ' ') {
                        alertas('warning', 'Advertencia...', `Estos productos no tienen stock o no se encuentra en el inventario: <br>${data.falta}`);
                    }
                }

            },
            error: function(data) {
                alertas(`error`, `Error...`, `Ha ocurrido un error`)
            }
        })
    }

    function guardar_derivado() {
        var id_examen = document.getElementById('id_examen').value;
        var id_derivado = document.getElementById('val_derivado').value;

        $.ajax({
            type: 'get',
            url: `{{ route('plantillacontrollabs.guardar_derivado') }}`,
            datatype: 'json',
            data: {
                'id_examen': id_examen,
                'id_orden': `{{$orden->id}}`,
                'id_derivado': id_derivado

            },
            success: function(data) {
                console.log(data);
                setTimeout(function() {
                    location.reload();
                }, 1500)

            },
            error: function(data) {
                alertas(`error`, `Error...`, `Ha ocurrido un error`)
            }
        })

    }
</script>

<script>
    const alertas = (icon, title, text) => {
        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            html: `${text}`,
        })
    }
</script>


@endsection