<style type="text/css">

  .mce-edit-focus,
        .mce-content-body:hover {
            outline: 2px solid #2276d2 !important;
        }

</style>
@php

  $random = rand(100,999);

@endphp

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>

    <h4 class="modal-title" id="myModalLabel"> {{trans('admision.CertificadoMédico')}}</h4>
</div>
<div class="modal-body">
  <form method="post" action="{{route('controldoc.generar_cert')}}">
    {{csrf_field()}}

    <input type="hidden" name="id" value="{{$id}}">


    <div class="form-group col-md-12">
      <label for="id_doctor1" class="col-md-6 control-label">{{trans('admision.Doctor')}}</label>
      <div class="col-md-5">
        <select class="form-control input-sm" id="id_doctor1" name="id_doctor1" required>

          @if($historia->id_doctor1 == '4444444444' )
              <option value="0920875788">ECHEVERRIA BARZOLA RONALD FABRICIO</option>
          @else
            @foreach($users as $val)

                <option @if($historia->id_doctor1==$val->id) selected @endif value="{{$val->id}}">{{$val->apellido1}} @if($val->apellido2!='(N/A)') {{$val->apellido2}} @endif {{$val->nombre1}}</option>

            @endforeach
          @endif
        </select>
      </div>
    </div>

    <div class="form-group col-md-12" >
      <label class="col-md-6 control-label">{{trans('admision.Fecha')}}</label>
      <div class="col-md-5">
        <div class="input-group date" id="fecha">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" value="{{$historia->fechaini}}" name="cfecha" class="form-control pull-right input-sm" id="cfecha" required autocomplete="off" >
        </div>
      </div>
    </div>

    <div class="form-group col-md-12" >
      <label class="col-md-6 control-label">{{trans('admision.Desde')}}</label>
      <div class="col-md-5">
        <div class="input-group date" id="desde">
            <div class="input-group-addon">
                <i class="glyphicon glyphicon-time"></i>
            </div>
            <input type="text" name="idesde" class="form-control pull-right input-sm" id="idesde" autocomplete="off" >
        </div>
      </div>
    </div>

    <div class="form-group col-md-12" >
      <label class="col-md-6 control-label">{{trans('admision.Hasta')}}</label>
      <div class="col-md-5">
        <div class="input-group date" id="hasta">
            <div class="input-group-addon">
                <i class="glyphicon glyphicon-time"></i>
            </div>
            <input type="text" name="ihasta" class="form-control pull-right input-sm" id="ihasta" autocomplete="off" >
        </div>
      </div>
    </div>

    <div class="form-group col-md-12">
      <label for="descanso" class="control-label col-md-6">{{trans('admision.DescansoMédico')}}</label>
      <div class="col-md-5">
        <input id="descanso" type="number" class="form-control input-sm" name="descanso" value="0" required>
      </div>
    </div>

    <div class="form-group col-md-12">
      <label for="observacion" class="col-md-12 control-label">{{trans('admision.Observación')}}</label>
      <input id="observacion" type="text" class="form-control input-sm" name="observacion" value="{{$tipo}}" required>
    </div>

    <div class="form-group col-md-12">
      <label for="institucion" class="col-md-12 control-label">{{trans('admision.Institución/Empresa')}}</label>
      <input id="institucion" type="text" class="form-control input-sm" name="institucion"  required>
    </div>

    <div class="form-group col-md-12">
      <label for="familiar" class="col-md-12 control-label">{{trans('admision.Familiar')}}</label>
      <input id="familiar" type="text" class="form-control input-sm" name="familiar" value="{{$paciente->apellido1familiar}} @if($paciente->apellido2familiar!='(N/A)') {{$paciente->apellido2familiar}} @endif {{$paciente->nombre1familiar}} @if($paciente->nombre2familiar!='(N/A)'){{$paciente->nombre2familiar}} @endif">
    </div>

    <label for="observacion" class="col-md-12 control-label">{{trans('admision.Diagnóstico')}}</label>
    <div class="form-group col-md-12" >
      <div id="tdiagnostico{{$random}}" style="border: solid 1px;min-height: 100px;">
        @foreach($diagnostico as $value)
          @php
            $cie10 = Sis_medico\Cie_10_3::find($value->cie10);
            if(is_null($cie10)){
              $cie10 = Sis_medico\Cie_10_4::find($value->cie10);
            }
          @endphp
          @if(!is_null($cie10))
          <p>{{$value->cie10}}: {{$cie10->descripcion}} </p>
          @endif
        @endforeach
      </div>
      <input type="hidden" name="diagnostico" id="diagnostico">
    </div>


    <button type="submit" class="btn btn-primary" formtarget="_blank">{{trans('admision.Generar')}}</button>


  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('admision.Cerrar')}}</button>
</div>

<script type="text/javascript">

  $(function () {
    $('#fecha').datetimepicker({

      format: 'YYYY/MM/DD'

    });


    $( "#cfecha" ).click(function() {
      $('#fecha').datetimepicker('show');
    });


    $('#desde').datetimepicker({

      format: 'HH:mm',

    });


    $( "#idesde" ).click(function() {
      $('#desde').datetimepicker('show');
    });



    $('#hasta').datetimepicker({

      format: 'HH:mm'

    });


    $( "#ihasta" ).click(function() {
      $('#hasta').datetimepicker('show');
    });



  });

  tinymce.init({
        selector: '#tdiagnostico{{$random}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tdiagnostico{{$random}}');
                $("#diagnostico").val(ed.getContent());
            });
        },

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tdiagnostico{{$random}}');
                $("#diagnostico").val(ed.getContent());

            });
          }
    });



</script>
