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

    .body {
        background-color: #ffff;
        border-radius: 5px;
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
                    <h3 class="box-title">Comparativo</h3>
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

    <div class="col-md-12 body">
        <div class="col-md-12">
          
            <center>
                <h4>REPORTE COMPARATIVO</h4>
            </center>
        </div>
    </div>




    <form id="formulario">
        <input type="hidden" name="id_orden" value="{{$orden->id}}">
        <input type="hidden" name="id_planilla" value="@if(!is_null($planilla)){{$planilla->id}} @endif">

        <div class="col-md-12 body">
            <table class="table ">
                <thead>
                    <th width="20%">Serie</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Acción</th>
                    <!-- <th></th> -->
                </thead>
                <tbody id="table_insumos">

                    @if(isset($planilla->detalles))
                    @foreach($planilla->detalles as $value)
                    <input type="hidden" name="detalles[]" value="{{$value->id}}">
                    <tr>
                        <td>{{$value->serie}}</td>
                        <td>{{$value->producto->nombre}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>{{ $value->cantidad }}</td>
                        <td>
                           
                            <input id="checkbox{{$value->id}}" onchange="cambiarEstado({{$value->id}})" style="width: 17px;height: 17px;" type="checkbox" @if($value->check == 1) checked @endif>
                            <input type="hidden" value="@if($value->check == 1) 1 @else 0 @endif" id="check_aprobado{{$value->id}}" name="check_aprobado[]">
                            
                        </td>
                        <!-- <td><button class='btn btn-danger'>Delete</button></td> -->
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="col-md-12">
                <center>
                    <h3><b> Derivados </b></h3>
                </center>
            </div>

            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Examen</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tabla_derivado">

                        @if(isset($planilla_derivado->detalles))
                        @foreach($planilla_derivado->detalles as $det)

                        <tr>
                            <td>@if(isset($det->derivado)) {{$det->derivado->examen->nombre}} @endif</td>
                            <td>@if(isset($det->derivado)) {{$det->derivado->tipo_derivado->nombre}} @endif</td>
                            <td>@if(isset($det->derivado)) {{$det->derivado->valor}} @endif</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="col-md-12" style="margin-bottom: 1rem;">
                <label for="">Observacion</label>
                <textarea class="form-control" name="" id="" cols="5" rows="5">Paciente: {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }}</textarea>
            </div>
          
            <div class="col-md-12" style="margin-bottom: 1rem;">
                <center><button onclick="guardar()" type="button" class="btn btn-primary"><i class="fa fa-check-square-o" aria-hidden="true"></i></button> </center>
            </div>

          
        </div>
    </form>

</section>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const cambiarEstado = id => {
        let check = document.getElementById('checkbox' + id).checked;
        let check_aprobado = document.getElementById('check_aprobado' + id)
        console.log("Cambios de estado", check);
        if (check) {
            check_aprobado.value = 1;
        } else {
            check_aprobado.value = 0;
        }

    }

    const guardar = () => {
        // e.preventDefault();
        $.ajax({
            type: "get",
            url: `{{ route('laboratorio.plantilla.storePlanilla') }}`,
            dataType: "json",
            data: $("#formulario").serialize(),
            success: function(data) {
                console.log(data);
                alertas(data.status, data.status, data.msj)
                if(data.status == "success"){
                    setTimeout(function() {
                        //location.reload()
                    }, 1500)
                }
             
            },
            error: function(data) {

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