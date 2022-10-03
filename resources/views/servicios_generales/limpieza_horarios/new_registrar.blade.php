@extends('servicios_generales.limpieza_banos.base')
@section('action-content')
@php
$date = date('Y-m-d');
@endphp

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Servicios Generales</a></li>
            <li class="breadcrumb-item"><a href="#">Registro de limpieza de Salas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <form id="formulario" class="form-vertical" role="form" method="POST" action="{{route('mantenimientohorario.guardar')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-9">
                    <h5><b>Control de Limpieza y Desinfección de Baños Piso: {{$piso->nombre}}</b></h5>
                </div>

                <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-danger">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>

            <div class="separator"></div>
            <div class="box-body">
                <input type="hidden" name="nombre_piso" value="{{$id_sala}}">
                <div class="form-group col-md-6">
                    <label for="desinfectante" class="col-md-3 control-label">Desinfectante</label>
                    <div class="col-md-9">
                        <input id="desinfectante" type="text" class="form-control" name="desinfectante" value="{{ old('desinfectante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        @if ($errors->has('desinfectante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('desinfectante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div id="div_hc1" class="form-group col-lg-12 {{ $errors->has('archivo') ? ' has-error' : '' }}">

                    <label for="archivo" class="col-lg-6 control-label">Evidencia antes de la limpieza y desinfección del baño (img)</label>
                    <div class="col-lg-6" style="padding-left: 10px;padding-right: 5%;">
                        <input id="file-input" name="evidencia_antes" type="file" / required>

                    </div>
                </div>
                <!--div id="div_hc2" class="form-group col-lg-12{{ $errors->has('archivo') ? ' has-error' : '' }}">

                    <label for="archivo" class="col-lg-6 control-label">Evidencia después de la limpieza y desinfección del baño (img)</label>
                    <div class="col-lg-6" style="padding-left: 10px;padding-right: 5%;">
                        <input id="file-input" name="evidencia_desp" type="file" />

                    </div>
                </div-->


                <div class="form-group col-lg-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" id="titulo" class="col-lg-2 control-label">Observaciones</label>
                    <div class="col-lg-10" style="padding-left: 10px;padding-right: 5%;">
                        <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{ old('observaciones') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        @if ($errors->has('observaciones'))
                        <span class="help-block">
                            <strong>{{ $errors->first('observaciones') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary" id="guardar">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function guardar() {
        document.getElementById("guardar").disabled = true;
        $fecha = $("#fecha").val();
        $hora = $('#hora').val();
        $nombre_piso = $("#nombre_piso").val();
        $desinfectante = $("#desinfectante").val();
        $observaciones = $("#observaciones").val();
        if ($desinfectante == "" || $hora == "" || $fecha == "" || $observaciones == "" || $nombre_piso == "") {
            swal("Error!", "Campos Vacios", "error");
        } else {
            $.ajax({
                url: "{{route('limpieza_banos.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('limpieza_banos.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 1000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }

    }

    function cambio() {

        var id = $("#nombre_piso").val();
        console.log(id);
        $.ajax({
            url: "{{route('limpieza_control.marca')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': id,
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                document.getElementById("modelo").value = data;
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }

    function goBack() {
      history.back()
    }
</script>

@endsection
