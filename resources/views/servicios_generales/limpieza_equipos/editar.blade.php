@extends('servicios_generales.limpieza_equipos.base')
@section('action-content')
</style>

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('limpieza_equipof.general')}}</a></li>
            <li class="breadcrumb-item"><a href="../ambiente">{{trans('limpieza_equipof.cleaning')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('limpieza_equipof.edit')}}</li>
        </ol>
    </nav>
    <form id="formulario" class="form-vertical" role="form">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="box">
            <div class="box-header">
                <div class="col-md-9">
                    <h5><b>{{trans('limpieza_equipof.control')}}</b></h5>
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" onclick="editar();" class="btn btn-primary">
                        <i class="glyphicon glyphicon-registration-mark" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('limpieza_equipof.edit')}}
                    </button>
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" onclick="goBack()" class="btn btn-danger">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('limpieza_equipof.return')}}
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body">
                <div class="col-md-12">
                    <h5 style="color: red;"><b>{{trans('limpieza_equipof.control')}}</b></h5>
                </div>
                <div class="form-group  col-xs-4">
                    <label for="fecha" class="col-md-6 texto">{{trans('limpieza_equipof.date')}}</label>
                    <div class="col-md-6">
                        <input id="fecha" name="fecha" type="date" class="form-control" value="{{$result->fecha_antes}}" placeholder="{{trans('limpieza_equipof.date')}}" required autofocus>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="proc" class="col-md-6 texto">{{trans('limpieza_equipof.medical')}}</label>
                    <div class="col-md-6">
                        <select name="proc[]" multiple="multiple" class="form-control select-2" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            @foreach($proTodo as $value)
                            @foreach($pro as $val)
                            <option {{$value->id_procedimiento == $val->id ? 'selected' : ''  }} value="{{$val->id}}">{{$val->nombre}}</option>
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="hora" class="col-md-6 texto">{{trans('limpieza_equipof.time')}}</label>
                    <div class="col-md-6">
                        <input id="text" name="hora" value="{{$result->hora}}" type="time" class="form-control" placeholder="{{trans('limpieza_equipof.time')}}" required autofocus>
                    </div>
                </div>
                <div class="form-group  col-xs-4">
                    <label for="paciente" class="col-md-6 texto">{{trans('limpieza_equipof.patient')}}</label>
                    <div class="col-md-6">
                        <input type="text" onmouseover="ok();" value="{{$result->id_paciente}}" class="form-control" id="paciente" name="paciente" readonly>
                    </div>
                </div>
                <div class="form-group col-xs-8">
                    <label for="equipo" class="col-md-6 texto">{{trans('limpieza_equipof.medical')}}</label>
                    <div class="col-md-6">
                        <select id="equipo" onchange="cambio(this);" name="equipo[]" multiple="multiple" class="form-control equipoUsado" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            @foreach($detalle as $value)
                            @foreach($equipo as $val)
                            <option {{$value->id_equipo == $val->id ? 'selected' : ''  }} value="{{$val->id}}">{{$val->nombre}}</option>
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="modelo" class="col-md-6 texto">{{trans('limpieza_equipof.model')}}</label>
                    <div class="col-md-6">
                        @foreach($detalle as $value)
                        @php $modelo = Sis_medico\Equipo::where('id',$value->id_equipo)->first(); @endphp
                        <input id="modelo" name="modelo" readonly value="{{$modelo->nombre}}" type="text" class="form-control" placeholder="{{trans('limpieza_equipof.model')}}" required autofocus> <br>
                        @endforeach
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label for="pruebas" class="col-md-6 texto">{{trans('limpieza_equipof.sterilizing')}}</label>
                    <div class="col-md-6">
                        <select id="pruebas" name="pruebas" class="form-control" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            <option {{$result->prueba_antes == 0 ? 'selected' : ''  }} value="0">No</option>
                            <option {{$result->prueba_antes == 1 ? 'selected' : ''  }} value="1">{{trans('limpieza_equipof.yes')}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group  col-xs-6">
                    <label for="serie" class="col-md-6 texto">{{trans('limpieza_equipof.series')}}</label>
                    <div class="col-md-6">
                        @foreach($detalle as $value)
                        @php $modelo = Sis_medico\Equipo::where('id',$value->id_equipo)->first(); @endphp
                        <input id="serie" name="serie" type="text" value="{{$modelo->serie}}" class="form-control" placeholder="serie" required autofocus> <br>
                        @endforeach
                    </div>
                </div>
                <div class="form-group  col-xs-6">
                    <label for="estado_equipo" class="col-md-6 texto">{{trans('limpieza_equipof.status')}}</label>
                    <div class="col-md-6">
                        <input id="estado_equipo" name="estado_equipo" value="{{$result->estado_equipo}}" type="text" class="form-control" placeholder="{{trans('limpieza_equipof.status')}}" required autofocus>
                    </div>
                </div>
                <div class="col-md-12">
                    <h5 style="color: red;"><b>{{trans('limpieza_equipof.after')}}</b></h5>
                </div>
                <div class="form-group  col-xs-6">
                    <label for="hora_ester" class="col-md-6 texto">{{trans('limpieza_equipof.time')}}</label>
                    <div class="col-md-6">
                        <input id="hora_ester" name="hora_ester" value="{{$result->hora_esterilizacion}}" type="time" class="form-control" placeholder="hora esterilización de equipo" required autofocus>
                    </div>
                </div>
                <div class="form-group  col-xs-6">
                    <label for="prueba_desp" class="col-md-6 texto">{{trans('limpieza_equipof.leak')}}</label>
                    <div class="col-md-6">
                        <select id="prueba_desp" name="prueba_desp" class="form-control" name="{" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            <option {{$result->prueba_despues == 0 ? 'selected' : ''  }} value="0">No</option>
                            <option {{$result->prueba_despues == 1 ? 'selected' : ''  }} value="1">{{trans('limpieza_equipof.yes')}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label for="obs" class="col-md-6 texto">{{trans('limpieza_equipof.remarks')}}</label>
                    <div class="col-md-6">
                        <input id="obs" name="obs" type="text" value="{{$result->observaciones}}" val class="form-control" placeholder="{{trans('limpieza_equipof.remarks')}}" required autofocus>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function() {
        $('.equipoUsado').select2({
            tags: false
        });
    });


    $('.select-2').select2({
        tags: false
    });

    function ok() {
        $("#paciente").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{route('limpieza_equipo.autocomplete')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            change: function(event, ui) {
                $("#paciente").val(ui.item.id);
            },
            minLength: 2,
        });
    }

    function goBack() {
        var url = '{{route("limpieza_equipo.index")}}';
        window.location = url;
    }

    function editar() {

        $.ajax({
            url: "{{route('limpieza_control.actualizar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#formulario').serialize(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var url = "{{route('limpieza_equipo.index')}}"
                if (data == 'ok') {
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert(`{{trans('limpieza_equipof.problem')}}`);
                //console.log(xhr);
            },
        });
    }

    function cambio() {

        var id = $("#equipo").val();
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
                alert(`{{trans('limpieza_equipof.problem')}}`);
                //console.log(xhr);
            },
        });
    }
</script>

@endsection