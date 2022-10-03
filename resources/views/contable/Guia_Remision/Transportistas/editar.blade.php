@extends('contable.Guia_Remision.Transportistas.base')
@section('action-content')
<div class="box-body">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Editar Transportista</h3>
        </div>
        <form id="editar_plantilla" method="post" action="{{route('transportistas.update')}}">
            {{ csrf_field() }}
            <input type="hidden" name="id" id="id" value="{{$id}}">
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('ci_ruc') ? ' has-error' : '' }}">
                    <label for="ci_ruc" class="col-md-20 control-label">Identificación/RUC</label>
                    <input type="number" name="ci_ruc" size="20" class="form-control" value="{{$mantenimientos_transportistas->ci_ruc}}" required maxlength="100" onblur="">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('tipo_documento') ? ' has-error' : '' }}">
                    <label for="tipo_documento" class="col-md-20 control-label">Tipo de Documento</label>
                    <select class="form-control validar" name="tipo_documento" id="tipo_documento">
                        <option {{$mantenimientos_transportistas->tipo_documento == 4 ? 'selected' : ''}} value="4">RUC</option>
                        <option {{$mantenimientos_transportistas->tipo_documento == 5 ? 'selected' : ''}} value="5">Cédula</option>
                        <option {{$mantenimientos_transportistas->tipo_documento == 6 ? 'selected' : ''}} value="6">Pasaporte</option>
                        <option {{$mantenimientos_transportistas->tipo_documento == 8 ? 'selected' : ''}} value="8">Identificación del Exterior</option>
                    </select>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('razon_social') ? ' has-error' : '' }}">
                    <label for="razon_social" class="col-md-20 control-label">Razón Social </label>
                    <input type="text" name="razon_social" size="20" class="form-control" value="{{$mantenimientos_transportistas->razon_social}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="nombres" class="col-md-20 control-label">Nombre</label>
                    <input type="text" name="nombres" size="20" class="form-control" value="{{$mantenimientos_transportistas->nombres}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="apellidos" class="col-md-20 control-label">Apellidos</label>
                    <input type="text" name="apellidos" size="20" class="form-control" value="{{$mantenimientos_transportistas->apellidos}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="nombrecomercial" class="col-md-20 control-label">Nombre Comercial</label>
                    <input type="text" name="nombrecomercial" size="20" class="form-control" value="{{$mantenimientos_transportistas->nombrecomercial}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="ciudad" class="col-md-20 control-label">Ciudad</label>
                    <input type="text" name="ciudad" size="20" class="form-control" value="{{$mantenimientos_transportistas->ciudad}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="direccion" class="col-md-20 control-label">Dirección</label>
                    <input type="text" name="direccion" size="20" class="form-control" value="{{$mantenimientos_transportistas->direccion}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="email" class="col-md-20 control-label">Email 1</label>
                    <input type="text" name="email" size="20" class="form-control" value="{{$mantenimientos_transportistas->email}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="email2" class="col-md-20 control-label">Email 2</label>
                    <input type="text" name="email2" size="20" class="form-control" value="{{$mantenimientos_transportistas->email2}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="telefono1" class="col-md-20 control-label">Teléfono 1</label>
                    <input type="number" name="telefono1" size="20" class="form-control" value="{{$mantenimientos_transportistas->telefono1}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="telefono2" class="col-md-20 control-label">Teléfono 2</label>
                    <input type="number" name="telefono2" size="20" class="form-control" value="{{$mantenimientos_transportistas->telefono2}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('placa') ? ' has-error' : '' }}">
                    <label for="placa" class="col-md-20 control-label">Placa</label>
                    <input type="text" onchange="" name="placa" size="20" class="form-control" value="{{$mantenimientos_transportistas->placa}}" required maxlength="100">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="rise" class="col-md-20 control-label">RISE</label>
                    <select class="form-control validar" name="rise" id="rise">
                        <option {{$mantenimientos_transportistas->rise == 1 ? 'selected' : ''}} value="1">SI</option>
                        <option {{$mantenimientos_transportistas->rise == 0 ? 'selected' : ''}} value="0">NO</option>
                    </select>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('contabilidad') ? ' has-error' : '' }}">
                    <label for="contabilidad" class="col-md-20 control-label">Contabilidad</label>
                    <select class="form-control validar" name="contabilidad" id="contabilidad">
                        <option {{$mantenimientos_transportistas->contabilidad == 1 ? 'selected' : ''}} value="1">SI</option>
                        <option {{$mantenimientos_transportistas->contabilidad == 0 ? 'selected' : ''}} value="0">NO</option>
                    </select>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('contribuyente_especial') ? ' has-error' : '' }}">
                    <label for="contribuyente_especial" class="col-md-20 control-label">Editar Contribuyente Especial</label>
                    <select class="form-control validar" name="contribuyente_especial" id="contribuyente_especial">
                        <option {{$mantenimientos_transportistas->contribuyente_especial == 1 ? 'selected' : ''}} value="1">SI</option>
                        <option {{$mantenimientos_transportistas->contribuyente_especial == 0 ? 'selected' : ''}} value="0">NO</option>
                    </select>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" name="btnFalla" id="btnFalla" class="btn btn-primary btn-gray">
                            Actualizar
                        </button>
                        <button type="button" onclick="history.back()" class="btn btn-danger btn-gray">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="modal_falla" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Revisar el número de dígitos en Identificación/RUC</h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#btnFalla").click(function() {
            $.ajax({
                url: "{{route('transportistas.update')}}",
                type: 'POST',
                data: $('#editar_plantilla').serialize(),
                dataType: 'json',
                success: function(json) {
                    dd(json);
                    if (json.result == 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Datos guardados correctamente',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                window.open('index', '_self');
                            }
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al guardar los datos',
                            text: json.mensajes,
                            confirmButtonText: 'OK',
                        })
                    }

                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        });
    });
    $(document).ready(function() {
        $('#entidadLogo').change(function() {
            $('#estado_entidad').html("<img src='public/img/horizontal.gif' style='width:120px;height:13px;' />");
            $('#estado_entidad').css({
                'display': 'block',
            });
            var inputFileImage = document.getElementById('entidadLogo');
            var file = inputFileImage.files[0];
            var fileSize = $('#entidadLogo')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                $('#estado_entidad').html('');
                $('#estado_entidad').css({
                    'display': 'none',
                });
                $('#entidadLogo').val('');
                alert('Error: imagen muy grande o tiene una resolucion muy alta');
            } else {
                var fd = new FormData();
                fd.append('entidadLogo', file);
                fd.append('id_empresa', $('#id_empresa').val());
                fd.append('tipo_archivo', 'entidadLogo');
                fd.append('_token', $('input[name=_token]').val());
                $.ajax({
                    url: 'guardarArchivo',
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    success: function(data) {
                        var imagen = data.split('|');
                        if (imagen[0] == 'ok') {
                            $('#entidadLogo').val('');
                            $('#logoEntidad').html(imagen[1]);
                        } else {
                            alert(imagen[1]);
                            $('#entidadLogo').val('');
                        }
                    }
                });
            }
        });
    })
</script>
@endsection