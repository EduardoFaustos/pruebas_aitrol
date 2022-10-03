@extends('contable.Guia_Remision.Transportistas.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Crear Transportista</h3>
        </div>
        <div class="box-body">
            <form id="crear_plantilla" method="post" action="{{route('transportistas.store')}}">
                {{ csrf_field() }}
            
                             
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('ci_ruc') ? ' has-error' : '' }}">
                        <label for="ci_ruc" class="col-md-20 control-label"> Identificación/RUC</label>
                        <input type="number" name="ci_ruc" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('razon_social') ? ' has-error' : '' }}">
                        <label for="razon_social" class="col-md-20 control-label">  Razón Social </label>
                        <input type="text" name="razon_social" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="form-group col-xs-6 }}">
                        <label for="nombres" class="col-md-20 control-label">  Nombre</label>
                        <input type="text" name="nombres" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6 }}">
                        <label for="apellidos" class="col-md-20 control-label">  Apellidos</label>
                        <input type="text" name="apellidos" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-20 control-label"> ID Empresa</label>
                        <input type="text" name="id_empresa" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="form-group col-xs-6}}">
                        <label for="nombrecomercial" class="col-md-20 control-label"> Nombre Comercial</label>
                        <input type="text" name="nombrecomercial" size="20" class="form-control" value="" required maxlength="25">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6}}">
                        <label for="ciudad" class="col-md-20 control-label"> Ciudad</label>
                        <input type="text" name="ciudad" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6 }}">
                        <label for="direccion" class="col-md-20 control-label"> Dirección</label>
                        <input type="text" name="direccion" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6}}">
                        <label for="email" class="col-md-20 control-label"> Email 1</label>
                        <input type="text" name="email" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6}}">
                        <label for="email2" class="col-md-20 control-label"> Email 2</label>
                        <input type="text" name="email2" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6}}">
                        <label for="telefono1" class="col-md-20 control-label"> Teléfono 1</label>
                        <input type="number" name="telefono1" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6 }}">
                        <label for="telefono2" class="col-md-20 control-label"> Teléfono 2</label>
                        <input type="number" name="telefono2" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="entidadLogo" class="col-md-20 control-label">Logo</label>
                        <input id="entidadLogo" name="entidadLogo" type="file" class="ruta_logo form-control" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6 }}">
                        <label for="placa" class="col-md-20 control-label"> Placa</label>
                        <input type="text" name="placa" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div>

                <!--<div class="box-body">
                    <div class="form-group col-xs-6}}">
                        <label for="identificacion" class="col-md-20 control-label"> Identificación</label>
                        <input type="text" name="identificacion" size="20" class="form-control" value="" required maxlength="100">
                    </div>
                </div> -->

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('tipo_documento') ? ' has-error' : '' }}">
                        <label for="tipo_documento" class="col-md-20 control-label">Tipo de Documento</label>
                        <select class="form-control validar" name="tipo_documento" id="tipo_documento" >
                                        <option>Seleccione</option>
                                        <option value="{{04}}">RUC</option>
                                        <option value="{{05}}">Cédula</option>
                                        <option value="{{06}}">Pasaporte</option>
                                        <option value="{{'08'}}">Identificación del Exterior</option>
                                    </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6">
                        <label for="rise" class="col-md-20 control-label"> RISE</label>
                        <select class="form-control validar" name="rise" id="rise" >
                                        
                                        <option value="{{0}}">No</option>
                                        <option value="{{1}}">Si</option>
                                    </select>
                                </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('contabilidad') ? ' has-error' : '' }}">
                        <label for="contabilidad" class="col-md-20 control-label"> Contabilidad</label>
                        <select class="form-control validar" name="contabilidad" id="contabilidad" >
                                        <option>Seleccione</option>
                                        <option value="{{0}}">No</option>
                                        <option value="{{1}}">Si</option>
                                    </select>
                    </div>
                </div>
    
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('contribuyente_especial') ? ' has-error' : '' }}">
                        <label for="contribuyente_especial" class="col-md-20 control-label"> Contribuyente Especial</label>
                        <select class="form-control validar" name="contribuyente_especial" id="contribuyente_especial" >
                                        <option>Seleccione</option>
                                        <option value="{{0}}">No</option>
                                        <option value="{{1}}">Si</option>
                                    </select>
                    </div>
                </div>

                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Agregar
                        </button>
                    </div>
                </div>   
                
                
            </form>
        </div>
    </div>
</section>

<script>
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
                            $('#logoEntidad').html(imagen[0]);
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