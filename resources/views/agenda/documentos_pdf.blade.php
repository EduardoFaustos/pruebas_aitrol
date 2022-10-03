<style>
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 120px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
  }

  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 40%;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<div class="modal-content">
  <div class="modal-header" style="background: #3c8dbc;">
    <input type="hidden" value="{{$empresa->id}}" name="empresacheck">
    <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
    <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title"> CONSENTIMIENTOS INFORMADO </h3>
  </div>
  <form method="post" action="{{route('descargar_pdf',[$empresa->id])}}">
    {{csrf_field()}}
    <input type="hidden" name="id" value="{{$paciente->id}}">
    <div style="margin-top: 5px;" class="col-md-12">
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="nombre" class="col-form-label-sm">Nombres</label>
          <input name="nombre" style="width:80%;" type="text" value="{{$paciente->nombre1}} {{$paciente->nombre2}}" class="form-control" readonly />
        </div>
        <div class="form-group col-md-4">
          <label for="apellido" class="col-form-label-sm">Apellidos</label>
          <input name="apellido" style="width:80%;" type="text" value="{{$paciente->apellido1}} {{$paciente->apellido2}}" class="form-control" readonly />
        </div>
        <div class="form-group col-md-4">
          <label for="cedula" class="col-form-label-sm">Cedula :</label>
          <input name="cedula" style="width:80%;" type="text" value="{{$paciente->id}}" class="form-control" readonly />
        </div>
      </div>
    </div>

    <div style="margin-top: 2px;" class="col-md-12">
      <div class="form-row">
        <!--
        <div class="form-group col-md-4">
          <label for="nombre_diagnostico" class="col-form-label-sm">Nombre diagnostico</label>
          <input name="nombre_diagnostico" id="cie10" style="width:80%;" type="text" class="form-control" required />
        </div>
        -->
        <div class="form-group col-md-4">
          <label for="nombre_procedimiento" class="col-form-label-sm">Formato</label>
          <select name="nombre_procedimiento" id="nombre_procedimiento" style="width:80%;" type="text" class="form-control select2">
            <option value="">Seleccione...</option>
            @foreach($consentimiento as $value)
            <option value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach

          </select>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" formtarget="_blank" class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>
  </form>
  <button type="button" class="btn btn-danger" style="margin-top:-4px;" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i>Cerrar</button>
</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $("#cie10").autocomplete({
    source: function(request, response) {

      $.ajax({
        url: "{{route('epicrisis.cie10_nombre')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          term: request.term
        },
        dataType: "json",
        type: 'post',
        success: function(data) {
          response(data);
          console.log(data);

        }
      })
    },
    minLength: 2,
  });

  $("#cie10").change(function() {
    $.ajax({
      type: 'post',
      url: "{{route('epicrisis.cie10_nombre2')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#cie10"),
      success: function(data) {
        console.log(data);
        if (data != '0') {
          $('#codigo').val(data.id);
        }

      },
      error: function(data) {

      }
    })
  });
  $('.select2').select2({
    tags: false
  });
</script>