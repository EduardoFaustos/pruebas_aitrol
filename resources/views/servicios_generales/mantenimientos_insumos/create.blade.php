@extends('servicios_generales.mantenimientos_insumos.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">Agregar Nuevo Insumo</h3>
            </div>
            <form class="form-vertical" role="form" id="formulario" action="{{route('mantenimientos_inlimpieza.store')}}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">

                    <!--nombre de los pisos-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="" >
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--Descripcion-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">Descripción</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="" >
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary">
                                Agregar
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function guardar() {
        var nombre = $("#nombre").val();
        var descri = $("#descripcion").val();
        if (nombre == "" || descri == "") {
            swal("Error!", "Campos Vacios", "error");
        } else {
            $.ajax({
                url: "{{route('mantenimientos_inlimpieza.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('mantenimientos_inlimpieza.index')}}"
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
</script>
@endsection