@extends('servicios_generales.limpieza_equipos.base')
@section('action-content')
@php
$date = date('Y-m-d');
@endphp
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('limpieza_equipof.general')}}</a></li>
            <li class="breadcrumb-item"><a href="../ambiente">{{trans('limpieza_equipof.cleaning')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('limpieza_equipof.create')}}</li>
        </ol>
    </nav>
    <form id="formulario" class="form-vertical" role="form">
        {{ csrf_field() }}
        <input type="hidden" id="sala_id" name="sala_id" value="{{$id_sala}}">
        <input type="hidden" name="id_pentax" id="id_pentax" value="{{$id_pentax}}">
        <div class="box">
            <div class="box-header">
                <div class="col-md-9">
                    <h5><b>{{trans('limpieza_equipof.control')}}</b></h5>
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" onclick="guardar();" class="btn btn-primary">
                        <i class="glyphicon glyphicon-registration-mark" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('limpieza_equipof.save')}}
                    </button>
                </div>
                <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-danger">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('limpieza_equipof.return')}}
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body">
                <div class="col-md-12">
                    <h5 style="color: red;"><b>{{trans('limpieza_equipof.before')}}</b></h5>
                </div>
                <div class="form-group  col-xs-4">
                    <label for="fecha" class="col-md-6 texto">{{trans('limpieza_equipof.date')}}</label>
                    <div class="col-md-6">
                        <input id="fecha" name="fecha" type="date" class="form-control" placeholder="{{trans('limpieza_equipof.date')}}" required autofocus>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="proc" class="col-md-6 texto">{{trans('limpieza_equipof.medical')}}</label>
                    <div class="col-md-6">
                        <select name="proc[]" multiple="multiple" class="form-control select-2" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            @foreach($pro as $val)

                            <option value="{{$val->id}}">{{$val->nombre}}</option>

                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="hora" class="col-md-6 texto">{{trans('limpieza_equipof.time')}}</label>
                    <div class="col-md-6">
                        <input id="text" name="hora" type="time" class="form-control" placeholder="{{trans('limpieza_equipof.time')}}" required autofocus>
                    </div>
                </div>
                <div class="form-group  col-xs-4">
                    <label for="paciente" class="col-md-6 texto">{{trans('limpieza_equipof.patient')}}</label>
                    <div class="col-md-6">
                        <input type="text" style="text-transform:uppercase" class="form-control" id="paciente" name="paciente" value="{{$id}}" readonly>
                    </div>
                </div>
                <div class="form-group col-xs-8">
                    <label for="equipo" class="col-md-4 texto">{{trans('limpieza_equipof.medical')}}</label>
                    <div class="col-md-8">
                        <select id="equipo" onchange="cambio(this);" class="equipoUsado" name="states[]" multiple="multiple" style="width: 100%;" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            @foreach($equipo as $val)
                            <option value="{{$val->id}}">{{$val->nombre}}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div id="models">

                </div>
                {{--<div class="form-group col-xs-4">
                    <label for="modelo" class="col-md-6 texto">{{trans('limpieza_equipof.model')}}</label>
                    <div class="col-md-6">
                        <input id="modelo" readonly type="text" class="form-control" placeholder="{{trans('limpieza_equipof.model')}}" required autofocus>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="serie" class="col-md-6 texto">{{trans('limpieza_equipof.series')}}</label>
                    <div class="col-md-6">
                        <input id="serie" readonly type="text" class="form-control" placeholder="{{trans('limpieza_equipof.series')}}" required autofocus>
                    </div>
                </div>--}}
                <div class="form-group col-xs-8">
                    <label for="pruebas" class="col-md-6 texto">{{trans('limpieza_equipof.sterilizing')}}</label>
                    <div class="col-md-6">
                        <select id="pruebas" name="pruebas" class="form-control" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            <option value="0">No</option>
                            <option value="1">{{trans('limpieza_equipof.yes')}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group  col-xs-4">
                    <label for="estado_equipo" class="col-md-6 texto">{{trans('limpieza_equipof.status')}}</label>
                    <div class="col-md-6">
                        <input id="estado_equipo" name="estado_equipo" type="text" class="form-control" placeholder="{{trans('limpieza_equipof.status')}}" required autofocus>
                    </div>
                </div>
                <div class="col-md-12">
                    <h5 style="color: red;"><b>{{trans('limpieza_equipof.after')}}</b></h5>
                </div>
                <div class="form-group  col-xs-6">
                    <label for="hora_ester" class="col-md-6 texto">{{trans('limpieza_equipof.time')}}</label>
                    <div class="col-md-6">
                        <input id="hora_ester" name="hora_ester" type="time" class="form-control" placeholder="{{trans('limpieza_equipof.time')}}" required autofocus>
                    </div>
                </div>
                <div class="form-group  col-xs-6">
                    <label for="prueba_desp" class="col-md-6 texto">{{trans('limpieza_equipof.leak')}}</label>
                    <div class="col-md-6">
                        <select id="prueba_desp" name="prueba_desp" class="form-control" required autofocus>
                            <option value="">{{trans('limpieza_equipof.select')}}</option>
                            <option value="0">No</option>
                            <option value="1">{{trans('limpieza_equipof.yes')}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label for="obs" class="col-md-6 texto">{{trans('limpieza_equipof.remarks')}}</label>
                    <div class="col-md-6">
                        <input id="obs" name="obs" type="text" class="form-control" placeholder="{{trans('limpieza_equipof.remarks')}}" required autofocus>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
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



    function guardar() {
        $fecha = $("#fecha").val();
        $proc = $('#proc').val();
        $hora = $('#hora').val();
        $paciente = $('#paciente').val();
        $pruebas = $('#pruebas').val();
        $serie = $('#serie').val();
        $estado_equipo = $('#estado_equipo').val();
        $hora_ester = $('#hora_ester').val();
        $prueba_desp = $('#prueba_desp').val();
        $obs = $('#obs').val();
        if ($fecha == "" || $proc == "" || $hora == "" || $paciente == "" || $pruebas == "" || $serie == "" || $estado_equipo == "" || $hora_ester == "" || $prueba_desp == "" || $obs == "") {
            swal("Error!", "Campos Vacios", "error");
        } else {
            $.ajax({
                url: "{{route('limpieza_equipo.guardar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('limpieza_equipo.index')}}"
                    if (data == 'ok') {
                        swal("Guardado!", "Correcto", "success");
                        window.location = url;
                    }
                },
                error: function(xhr, status) {
                    alert(xhr);

                },
            });
        }
    }

    function cambio() {

        var id = $("#equipo").val().at(-1);
        console.log(id);
        $.ajax({
            url: "{{route('limpieza_control.marca')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#formulario').serialize(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                
                $("#models").empty();

                data.forEach(element =>  $("#models").append(models(element)));
                // document.getElementById("modelo").value = data.conj;
                // document.getElementById("serie").value = data.serie;


            },
            error: function(xhr, status) {
                alert(`{{trans('limpieza_equipof.problem')}}`);
                //console.log(xhr);
            },
        });
    }

    const models = data => {

    console.log(data.serie);
      return `
        <div class="col-md-12">
            <div class="form-group col-xs-4">
                        <label for="modelo" class="col-md-6 texto">{{trans('limpieza_equipof.model')}}</label>
                        <div class="col-md-6">
                            <input id="modelo" readonly type="text" class="form-control" placeholder="{{trans('limpieza_equipof.model')}}" value="${data.conj}" required autofocus>
                        </div>
                    </div>
                    <div class="form-group col-xs-4">
                        <label for="serie" class="col-md-6 texto">{{trans('limpieza_equipof.series')}}</label>
                        <div class="col-md-6">
                            <input id="serie" readonly type="text" class="form-control" placeholder="{{trans('limpieza_equipof.series')}}" value="${data.serie}" required autofocus>
                        </div>
            </div>
        </div>
        `
    }
    

    function goBack() {
        window.history.back();
    }
</script>
@endsection