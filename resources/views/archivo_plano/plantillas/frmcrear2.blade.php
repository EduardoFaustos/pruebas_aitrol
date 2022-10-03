@extends('archivo_plano.plantillas.base')
@section('action-content')

<style type="text/css">

  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
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
    list-style:none;
    background-color:#FFFFFF;
    width:700px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }

</style>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px">Crear Plantillas</h3>
        </div><br><hr>

    </div>
  </div>
  <!-- /.box-header -->
    <div class="content">
          <form method="post" id="dynamic_form">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-6">
                <label>Código</label><br>
                <input type="text" name="txtcodigo" placeholder="Código"  class="form-control">
              </div>
              <div class="col-md-6">
                <label>Descripción</label><br>
                <input type="text" name="txtdescripcion" placeholder="Descripción"  class="form-control">
              </div>
              <div class="col-md-6"><br>
                <label>Descripción Completa</label><br>
                <input type="text" name="txtdescripcioncompleta" placeholder="Descripción Completa"  class="form-control">
              </div>
              <div class="col-md-6"><br>
                <label>Estado</label><br>
                <select name="txtestado" class="form-control">
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
<br>
<div class="col-md-12"><br>
<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>

<!-- bloque 5 -->

     <br />
   <div class="table-responsive">

    <span id="result"></span>
    <table class="table table-bordered table-striped" id="user_table">
      <thead>
        <tr>
            <th width="10%">Item</th>
            <th width="15%">Cantidad</th>
            <th width="15%">Honorario</th>
            <th width="15%">Orden</th>
            <th width="15%">Separado</th>
            <th width="10%">Action</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" align="right">&nbsp;</td>
          <td>
            <input type="submit" name="save" id="save" class="btn btn-primary" value="Guardar" />
          </td>
        </tr>
      </tfoot>
    </table>

   </div>



</div>
<br>

              <div class="col-md-6"><br></div><div id="proced_list"></div>
              <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
              </div>

          </form>

    </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>

<script>

  document.body.addEventListener('DOMSubtreeModified', function () {
      $(".codigo_elemento").autocomplete({
        source: function( request, response ) {
          //alert("autocomplete");
          $.ajax({
              url:"{{route('plantillas.search')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              data: {
                  term: request.term
                    },
              dataType: "json",
              type: 'post',
              success: function(data){
                  response(data);
                  console.log(data);
              }
          })
        },
        minLength: 3,
      });
  }, false);
$(document).ready(function(){

 var count = 1;

 dynamic_field(count);

 function dynamic_field(number)
 {
  html = '<tr>';
  html += '<td><input type="text" id=codigo'+count+' name="codigo[]" class="codigo_elemento form-control"  ></td>';
  html += '<td><input type="text" name="cantidad[]"  id="cantidad'+count+'" class="form-control" /></td>';
  html += '<td><select name="honorario[]" class="form-control"><option value="N">Ninguno</option><option value="100">100%</option><option value="50">50%</option></select></td>';
  html += '<td><input type="number" name="orden[]" class="form-control" /></td>';
  html += '<td><select name="separado[]" class="form-control"><option value="No">No</option><option value="Si">Si</option></select></td>';
  if(number > 1)
  {
      html += '<td><button type="button" name="remove" id="" class="btn btn-danger remove">Eliminar</button></td></tr>';
      $('tbody').append(html);
  }
  else
  {
      html += '<td><button type="button" name="add" id="add" class="btn btn-success">Nuevo Item</button></td></tr>';
      $('tbody').html(html);
  }
 }

 $(document).on('click', '#add', function(){
  count++;
  dynamic_field(count);

 });

 $(document).on('click', '.remove', function(){
  count--;
  $(this).closest("tr").remove();
 });

 $(".codigo_elemento").autocomplete({
    source: function( request, response ) {
      //alert("autocomplete");
      $.ajax({
          url:"{{route('plantillas.search')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {
              term: request.term
                },
          dataType: "json",
          type: 'post',
          success: function(data){
              response(data);
              console.log(data);
          }
      })
    },
    minLength: 3,
  });

 $('#dynamic_form').on('submit', function(event){
      $('#save').addClass('oculto');
        event.preventDefault();
        $.ajax({
            url:'{{ route("plantillas.insert") }}',
            method:'post',
            data:$(this).serialize(),
            dataType:'json',
            beforeSend:function(){
                $('#save').attr('disabled','disabled');
            },
            success:function(data)
            {
                if(data.error)
                {
                  console.log(data);
                    var error_html = '';
                    for(var count = 0; count < data.error.length; count++)
                    {
                        error_html += '<p>'+data.error[count]+'</p>';
                    }
                    $('#result').html('<div class="alert alert-danger">'+error_html+'</div>');
                    $('#save').removeClass('oculto');
                }
                else
                {
                    dynamic_field(1);
                    $('#result').html('<div class="alert alert-success">'+data.success+'</div>');
                    location.href = "{{route('applantilla.index')}}"
                }
                $('#save').attr('disabled', false);
            }
        })
 });
});
</script>
<script>

function pason(para){
  var numi = para;
$(document).ready(function(){
 $('#proced_ni'+numi).keyup(function(){
        var query = $(this).val();
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ route('plantillas.search') }}",
          method:"POST",
          data:{query:query, _token:_token, contador:numi
          },
          success:function(data){
           $('#proced_list'+numi).fadeIn();
                    $('#proced_list'+numi).html(data);
          }
         });
        }
    });



});
}

function listo(paso, t){
  var numi2 = paso;
  $('#proced_ni'+numi2).val($(t).text());
        $('#proced_list'+numi2).fadeOut();
         $("[id=cierrate]").hide();
         $('#cantidad'+numi2).focus();
        $( "#proced_ni" ).prop( "disabled", true );
        document.getElementById('cierrate').style.display="none";
        $(".dropdown-menu").hide();
        return false;
}
</script>

@endsection
