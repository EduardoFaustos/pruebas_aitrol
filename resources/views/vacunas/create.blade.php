
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
  <div class="box">
    <div class="box-header">
        <div class="row">
            <div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <button type="button" class="btn btn-primary" onclick="guardar()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                </div>
            </div>
        </div>
    </div>
      <div class="box-body">
        <form class="form-horizontal" id="form">
            {{ csrf_field() }}
        <div class="row">
         	<div class="form-group col-md-4">
                <label for="cedula" class="col-md-4 control-label">Cédula:</label>
                <div class="col-md-7">
                    <input id="cedula" maxlength="15" required type="text" class="form-control input-sm" name="cedula" value="{{$user->id}}">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="nombre1" class="col-md-4 control-label">Primer Nombre:</label>
                <div class="col-md-7">
                    <input id="nombre1" maxlength="15" required type="text" class="form-control input-sm" name="nombre1" value="{{$user->nombre1}}">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="nombre2" class="col-md-4 control-label">Segundo Nombre:</label>
                <div class="col-md-7">
                    <input id="nombre2" maxlength="15" required type="text" class="form-control input-sm" name="nombre2" value="{{$user->nombre2}}">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="apellido1" class="col-md-4 control-label">Primer Apellido:</label>
                <div class="col-md-7">
                    <input id="apellido1" maxlength="15" required type="text" class="form-control input-sm" name="apellido1" value="{{$user->apellido1}}">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="apellido2" class="col-md-4 control-label">Segundo Apellido:</label>
                <div class="col-md-7">
                    <input id="apellido2" maxlength="15" required type="text" class="form-control input-sm" name="apellido2" value="{{$user->apellido2}}">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label for="edad" class="col-md-4 control-label">Edad:</label>
                <div class="col-md-7">
                    <input id="edad" maxlength="15" required type="text" class="form-control input-sm" name="edad" value="">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label for="biologico" class="col-md-4 control-label">Biologico:</label>
                <div class="col-md-7">
                    <input id="biologico" maxlength="15" required type="text" class="form-control input-sm" name="biologico" value="">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label for="fecha" class="col-md-4 control-label">Fecha:</label>
                <div class="col-md-7">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text"  class="form-control input-sm" id="fecha" name="fecha" placeholder="AAAA/MM/DD" value='{{$fecha}}'>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="lote" class="col-md-4 control-label">Lote:</label>
                <div class="col-md-7">
                    <input id="lote" maxlength="15" required type="text" class="form-control input-sm" name="lote" value="">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="responsable" class="col-md-4 control-label">Responsable:</label>
                <div class="col-md-7">
                    <input id="responsable" maxlength="15" required type="text" class="form-control input-sm" name="responsable" value="">
                </div>
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
	$('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',            
        });

    function guardar(){
        //alert("ingreso");
        $.ajax({
          type: 'post',
          url:"{{ route('vacunas.guardar') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            console.log(data);
            if(data == "ok"){
                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })
                
            };
            location.reload();
          },
          error: function(data){
             console.log(data);
             //swal("Complete todos los campos");
          }
        });

    } 
</script>
