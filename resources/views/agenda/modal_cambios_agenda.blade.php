<div class="modal-content">
    <div class="modal-header">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="" class="modal-title"> CAMBIOS AGENDA</h3>
    </div>

    <div class="modal-body">
        <div class="row">
            <form id="form_cambios" method="POST" action="{{route('agenda.update_cambios')}}">
                {{ csrf_field() }}

                <div style="margin-top: 5px;" class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="hidden" name="id_agenda" id="id_agenda" value="{{$agenda->id}}">
                            <input type="hidden" id="unix" name="unix" value="">
                            <label for="fecha_ini">Fecha Inicio</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{$agenda->fechaini}}" name="inicio" class="form-control pull-right" id="inicio">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin">Fecha Fin</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="{{$agenda->fechafin}}" name="fin" class="form-control pull-right" id="fin">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="doctor">Doctor</label>
                            <select name="id_doctor" class="form-control" id="id_doctor">
                                @foreach ($usuarios as $usuario)
                                    @if($usuario->id!='9666666666')
                                    @if(!($usuario->training == '1' && $agenda->proc_consul == '1'))
                                    <option @if($usuario->id!='1307189140') class="sel" @endif @if(old('id_doctor1') != '') @if(old('id_doctor1') == $usuario->id) {{"selected"}} @endif @elseif($usuario->id == $agenda->id_doctor1) {{"selected"}} @endif value="{{$usuario->id}}" @if($usuario->id=='1307189140') style="color: red;" @elseif($usuario->id=='1314490929') style="color: blue;" @endif>{{$usuario->apellido1}} @if($usuario->apellido2!='(N/A)'){{$usuario->apellido2}}@endif {{$usuario->nombre1}} @if($usuario->nombre2!='(N/A)'){{$usuario->nombre2}}@endif</option>
                                    @endif
                                    @endif
                                @endforeach                            
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_sala">Sala</label>
                            <select name="id_sala" class="form-control" id="id_sala">
                                @foreach ($salas as $sala)
                                    @if($sala->proc_consul_sala==$agenda->proc_consul)
                                    <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                    @endif
                                    @if($sala->proc_consul_sala=='-1')
                                    <option @if(old('id_sala')==$sala->id){{"selected"}} @elseif($agenda->id_sala==$sala->id){{"selected"}} @endif value="{{$sala->id}}">{{$sala->nombre_sala}} / {{$sala->nombre_hospital}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <br>
                            <button type="submit" id="btn_guardar" onclick="" class="btn btn-info">Actualizar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
    </div>

   
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">

    $(document).ready(function()
    {
        var fecha_url = document.getElementById('inicio').value;
        var unix =  Math.round(new Date(fecha_url).getTime()/1000)-18000;
        $("#unix").val(unix);
    });
  
    $('#inicio').datetimepicker({
        format: 'YYYY/MM/DD HH:mm'


        });
    $('#fin').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD HH:mm',

         //Important! See issue #1075

    });

    

    function guardar_cambios(){
        

        $.ajax({
            type: 'post',
            url:"{{route('agenda.update_cambios')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#form_cambios").serialize(),
            success: function(data){
                //console.log();
                //location.reload(); 
            },
            error: function(data){
            }
        })
    }

    
</script>