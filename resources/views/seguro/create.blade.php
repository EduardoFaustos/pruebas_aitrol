@extends('seguro.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{trans('seguros.agregarnuevoseguro')}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('form_enviar_seguro.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">{{trans('seguros.nombredelseguro')}}</label>

                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required autofocus style="text-transform:uppercase;">

                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">{{trans('seguros.descripcion')}}</label>

                            <div class="col-md-6">
                                <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" required>

                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
                            <label for="tipo" class="col-md-4 control-label">{{trans('seguros.tipodeseguro')}}</label>

                            <div class="col-md-6">
                                <select id="tipo" class="form-control" name="tipo" onchange="campos();">
                                    <option value="0" selected="selected">{{trans('seguros.publico')}}</option>
                                    <option value="1">{{trans('seguros.privado')}}</option>
                                </select>

                            </div>
                        </div>
                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">{{trans('seguros.colordelaetiqueta')}}</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ old('color') }}">
                                <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span>
                                @if ($errors->has('color'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('color') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group oculto" id="cambio1">
                            <label for="tipo" class="col-md-4 control-label">{{trans('seguros.poseeurldeverificacion')}}</label>
                            <div class="col-md-6">
                                <input type="checkbox" name="posee_url" id="posee_url">{{trans('seguros.si')}}<br>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('url_validacion') ? ' has-error' : '' }} oculto" id="cambio2">
                            <label for="url_validacion" class="col-md-4 control-label">{{trans('seguros.urldevalidacion')}}</label>

                            <div class="col-md-6">
                                <input id="url_validacion" type="text" class="form-control" name="url_validacion" value="{{ old('url_validacion') }}">

                                @if ($errors->has('url_validacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('url_validacion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{trans('seguros.crear')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function campos() {
        var valor = document.getElementById("tipo").value;
        var elemento1 = document.getElementById("cambio1");
        if (valor == 0) {
            $(elemento1).addClass('oculto');
        }
        if (valor == 1) {
            $(elemento1).removeClass('oculto');
        }
    }

    $('#posee_url').on('click', function() {
        var elemento2 = document.getElementById("cambio2");
        if ($(this).is(':checked')) {
            // Hacer algo si el checkbox ha sido seleccionado
            $(elemento2).removeClass('oculto');
            $('#url_validacion').prop("required", true);

        } else {
            // Hacer algo si el checkbox ha sido deseleccionado
            $(elemento2).addClass('oculto');
            $('#url_validacion').removeAttr("required");
        }
    });
</script>
@endsection


<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>