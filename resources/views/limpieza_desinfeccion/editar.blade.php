<div class="modal-content" style="width: 100%;">
  <div class="modal-body">

    <div class="box-body">
      <form class="form-horizontal" id="form">
        {{ csrf_field() }}
        <input type="hidden" name="id_limpieza" value="{{$limpieza->id}}">

        <div class="form-group col-md-6 col-xs-6">
          <label class="label label-danger" style="font-size: 16px;">{{trans('tecnicof.patiet')}} : {{$limpieza->paciente->apellido1}} {{$limpieza->paciente->apellido2}} {{$limpieza->paciente->nombre1}} {{$limpieza->paciente->nombre2}} </label>

        </div>

        <div class="form-group col-md-6 col-xs-6">
          <input type="hidden" name="id_paciente" value="{{$limpieza->id_paciente}}">
          <input type="hidden" name="id_pentax" value="{{$limpieza->id_pentax}}">
          <input type="hidden" name="id_sala" value="{{$limpieza->id_sala}}">
          <label for="tipo_desinfeccion" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.typeofdisinfection')}}</label>
          <div class="col-md-9">
            <select id="tipo_desinfeccion" name="tipo_desinfeccion" class="form-control input-sm">
              <option value="">{{trans('tecnicof.select')}}...</option>
              <option @if($limpieza->tipo_desinfecion == '1') selected @endif value="1">{{trans('tecnicof.concurrent')}}</option>
              <option @if($limpieza->tipo_desinfecion == '2') selected @endif value="2">{{trans('tecnicof.terminal')}}</option>
            </select>

          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6">
          <label for="nom_detergente" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.detergentdisinfectantname')}}</label>
          <div class="col-md-9">
            <input type="text" name="nom_detergente" class="form-control input-sm" value="{{$limpieza->nom_deter_desinfec}}">
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6">
          <label for="nom_toallas" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.nameofdisinfectantwipes')}}</label>
          <div class="col-md-9">
            <input type="text" name="nom_toallas" class="form-control input-sm" value="{{$limpieza->nom_toallitas}}">
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6">
          <label for="anestesiologia" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.anesthesiologyanesthesiamachine')}}</label>
          <div class="col-md-9">
            <select id="anestesiologia" name="anestesiologia" class="form-control input-sm">
              <option value="">{{trans('tecnicof.select')}}...</option>
              <option @if($limpieza->anestesiologia == '1') selected @endif value="1">{{trans('tecnicof.cleaning')}}</option>
              <option @if($limpieza->anestesiologia == '2') selected @endif value="2">{{trans('tecnicof.disinfection')}}</option>
              <option @if($limpieza->anestesiologia == '3') selected @endif value="3">{{trans('tecnicof.cleaninganddisinfection')}}</option>
            </select>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6">
          <label for="responsable" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.responsible')}}</label>
          <div class="col-md-9">
            <select id="responsable_anest" name="responsable_anest" class="form-control input-sm">
              <option value="">{{trans('tecnicof.select')}}...</option>
              @foreach($anestesiologos as $value)
              <option @if($limpieza->responsable_anest == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre1}} {{$value->apellido1}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-12 col-xs-6">
          <div class="form-group col-md-12 col-xs-6">
            <label for="enfermeria" class="control-label" style="font-size:12px;">{{trans('tecnicof.nursing')}}</label>
          </div>
          <div class="col-md-4">
            <label for="fecha" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.time')}}</label>
            <div class="col-md-9">
              <input type="time" name="hora_registro" id="hora_registro" class="form-control" value="{{$limpieza->hora_registro}}" readonly="">
            </div>
          </div>
          <div class="col-md-4">
            <label for="camilla" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.stretcher')}}</label>
            <div class="col-md-9">
              <select id="camilla" name="camilla" class="form-control input-sm">
                <option value="">Seleccione...</option>
                <option @if($limpieza->en_camilla == '1') selected @endif value="1">{{trans('tecnicof.cleaning')}}</option>
                <option @if($limpieza->en_camilla == '2') selected @endif value="2">{{trans('tecnicof.disinfection')}}</option>
                <option @if($limpieza->en_camilla == '3') selected @endif value="3">{{trans('tecnicof.cleaninganddisinfection')}}</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <label for="velador" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.stretchers')}}</label>
            <div class="col-md-9">
              <select id="velador" name="velador" class="form-control input-sm">
                <option value="">Seleccione...</option>
                <option @if($limpieza->en_velador == '1') selected @endif value="1">{{trans('tecnicof.cleaning')}}</option>
                <option @if($limpieza->en_velador == '2') selected @endif value="2">{{trans('tecnicof.disinfection')}}</option>
                <option @if($limpieza->en_velador == '3') selected @endif value="3">{{trans('tecnicof.cleaninganddisinfection')}}</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <label for="monitor" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.monitors')}}</label>
            <div class="col-md-9">
              <select id="monitor" name="monitor" class="form-control input-sm">
                <option value="">Seleccione...</option>
                <option @if($limpieza->en_monitor == '1') selected @endif value="1">{{trans('tecnicof.cleaning')}}</option>
                <option @if($limpieza->en_monitor == '2') selected @endif value="2">{{trans('tecnicof.disinfection')}}</option>
                <option @if($limpieza->en_monitor == '3') selected @endif value="3">{{trans('tecnicof.cleaninganddisinfection')}}</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <label for="sop_monitor" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.monitorsupport')}}</label>
            <div class="col-md-9">
              <select id="sop_monitor" name="sop_monitor" class="form-control input-sm">
                <option value="">Seleccione...</option>
                <option @if($limpieza->en_soporte == '1') selected @endif value="1">{{trans('tecnicof.cleaning')}}</option>
                <option @if($limpieza->en_soporte == '2') selected @endif value="2">{{trans('tecnicof.disinfection')}}</option>
                <option @if($limpieza->en_soporte == '3') selected @endif value="3">{{trans('tecnicof.cleaninganddisinfection')}}</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <label for="otros" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.otherequipment')}}</label>
            <div class="col-md-9">
              <select id="otros" name="otros" class="form-control input-sm">
                <option value="">Seleccione...</option>
                <option @if($limpieza->en_otros == '1') selected @endif value="1">{{trans('tecnicof.cleaning')}}</option>
                <option @if($limpieza->en_otros == '2') selected @endif value="2">{{trans('tecnicof.disinfection')}}</option>
                <option @if($limpieza->en_otros == '3') selected @endif value="3">{{trans('tecnicof.cleaninganddisinfection')}}</option>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <label for="responsable" class="col-md-4 control-label" style="font-size:12px;">{{trans('tecnicof.responsible')}}</label>
            <div class="col-md-8">
              <input type="text" name="responsable" class="form-control input-sm" value="{{$limpieza->responsable}}">
            </div>
          </div>
        </div>

        <div class="form-group col-md-8 col-xs-6">
          <div class="col-md-2">
            <label for="observacion" class="col-md-3 control-label" style="font-size:12px;">{{trans('tecnicof.observation')}}</label>
          </div>
          <div class="col-md-10">
            <input type="text" name="observacion" class="form-control input-sm" value="{{$limpieza->observacion}}">
          </div>
        </div>

        <div class="form-group col-md-2 ">
          <div class="col-md-7">
            <button type="button" class="btn btn-primary btn-xs" onclick="guardar();"> {{trans('tecnicof.update')}} </button>
          </div>
        </div>
      </form>

    </div>

  </div>
  <script src="{{ asset ("/js/jquery.validate.js") }}"></script>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
  <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script type="text/javascript">
    function alerta(title, icon, text) {
      Swal.fire({
        icon: '' + icon,
        title: '' + title,
        html: '' + text
      })
    }

    function guardar() {
      //alert("ingreso");
      $.ajax({
        type: 'post',
        url: "{{ route('limpieza.update') }}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data) {
          console.log(data);


          if (data.respuesta == 'success') {
            alerta('Exito', data.respuesta, data.msj)
            $('#edit').modal('hide');

          } else {
            alerta('Error', data.respuesta, data.msj)
          }
        },
        error: function(data) {
          console.log(data);
          //swal("Complete todos los campos");
        }
      });

    }
  </script>