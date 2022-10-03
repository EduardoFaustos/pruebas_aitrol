<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="modal-header">
        <h4 class="modal-title">ACTUALIZAR EL ESTADO DE LA ORDEN</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload()">
            <span aria-hidden="true">&times;</span></button>
    </div>
    <div class="box">
        <div class="box-body">
            <form action="{{route('gestionarorden.guardar_gestion')}}" id="guardar_formulario" method="POST">
                {{ csrf_field() }}
                <div class="row">
                    <!-- Datos predeterminados -->
                    <div class="form-group col-md-6 col-xs-6">
                        <label for="paciente" class="col-md-3 control-label">Paciente: </label>
                        <div class="col-md-9">
                            <input type="hidden" name="id_paciente" value="{{$orden->paciente->id}}">
                            <input type="text" id="paciente" name="paciente" readonly class="form-control input-sm" value="{{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}} {{$orden->paciente->nombre1}}">
                        </div>
                    </div>
                    <div class="form-group col-md-6 col-xs-6">
                        <label for="paciente" class="col-md-3 control-label">Numero de Orden: </label>
                        <div class="col-md-9">
                            <input type="text" name="id_orden" readonly class="form-control input-sm" value="{{$orden->id}}">
                        </div>
                    </div>
                    <!-- Termina datos predeterminados -->
                    <div class="form-group col-md-6 col-xs-6">
                        <label for="paciente" class="col-md-3 control-label">Seguro: </label>
                        <div class="col-md-9">
                            <select name="id_seguro" id="id_seguro" onchange="verEmail(this)" class="form-control" required autofocus>
                                <option value="">Seleccione</option>
                                @foreach($segurox as $val)
                                <option value="{{$val->id}}">{{$val->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-10">
                        <label for="email_seguro" class="col-md-4 control-label">Mail_seguro:</label>
                        <div class="col-md-8">
                            <input type="text" name="email_seguro" id="email_seguro" class="form-control">
                        </div>
                    </div>
                    <!-- Prueba -->
                    <!-- <div class="form-group col-md-10">
                        <label for="prueba" class="col-md-4 control-label">Prueba:</label>
                        <div class="col-md-8">
                            <input type="text" name="rapero" id="rapero" class="form-control">
                        </div>
                    </div> -->
                    <!-- Termina prueba -->
                    <div class="form-group col-md-4">
                        <label for="observacion" class="col-md-6 control-label">Estado:</label>
                        <select name="estado" id="estado" class="form-control select2" style="width:100%;">
                            <option value="0">Por gestionar</option>
                            <option value="1">Enviado al Seguro</option>
                            <option value="2">Respuesta del Seguro</option>
                            <option value="3">Finalizado</option>
                        </select>
                    </div>
                    <div class="box-body col-xs-15">
                        <label for="nombre" class="col-md-4 control-label">Observacion</label>
                        <div class="col-md-7">
                            <textarea class="form-control" name="observacion" id="observacion" cols="5" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-betwenn">
                        <!-- <input type="button" value="Cerrar" class="btn btn-ligth" onclick="window.location.reload()"> -->
                        <button type="button" class="btn btn-light" data-dismiss="modal" onclick="window.location.reload()">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
    $('#modificar_registro').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    function verEmail(e) {
        let id_seguro = e.value; 
        $.ajax({
            type: 'POST',
            url: "{{route('consultar_correo_seguro')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data:{
                'seguro': id_seguro,
            },
            success: function(data) {
               data.email == null ? document.getElementById("email_seguro").value = 'No hay correo' :   document.getElementById("email_seguro").value = data.email;
            },
            error: function(data) {
                console.log(data);
                swal("Error!", data, "error");
            }
        });
    }

    function guardar() {
        $.ajax({
            type: 'POST',
            url: "{{route('gestionarorden.guardar_gestion')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#guardar_formulario').serialize(),
            success: function(data) {
                console.log(data);
                swal("Exito!", 'Guardado con Exito', "success");
                setTimeout(function() {
                    // location.href = "{{route('gestionarorden.index')}}";
                    $('#modificar_registro').modal('hide');
                    $('#buscar').click();
                    $('#modificar_registro').modal();
                }, 500);
            },
            error: function(data) {
                console.log(data);
                swal("Error!", data, "error");
            }
        });
    }
    //lopez
</script>