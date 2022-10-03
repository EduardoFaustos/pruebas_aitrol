@extends('de_maestro_documentos.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">Editar Maestro Documento Electrónico</h3>
            </div>
            <form id="editar_plantilla" method="post">
                {{ csrf_field() }}
                <input type="hidden" id="action" value="{{route('demaestrodoc.update')}}" />
                <div class="box-body col-xs-24">

                    <input type="hidden" id="id" name="id" value="{{$id}}">

                    <!--Nombre del area-->

                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $de_maestro->nombre }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-2 control-label">Código </label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" name="codigo" value="{{ $de_maestro->codigo }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('codigo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                            <button id="btnFalla" name="btnFalla" type="button" class="btn btn-success btn-gray">
                                Actualizar
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>

    </div>
</div>
<div class="modal fade" id="modal_falla" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Documento ya existe</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script>
    $(function() {
        $("#btnFalla").click(function() {
            $.ajax({
                url: $('#action').val(),
                type: 'post',
                data: $('#editar_plantilla').serialize(),
                dataType: 'json',
                success: function(json) {
                    if (json.result == 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'El documento ya existe',
                            text: 'AiTrol informa'
                        });
                    } else {
                        window.open('../../de_maestro_documentos/index', '_self');
                    }
                }
            });
        });
    });
</script>
@endsection