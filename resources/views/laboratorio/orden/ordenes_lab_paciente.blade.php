@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>
<section class="content">
    <div class="box">
      <div class="box-body">
        <form method="POST" id="form_ordenes_lab_paciente">
          {{ csrf_field() }}
          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-3 control-label">Cedula</label>
            <div class="col-md-9">
              <div class="input-group">
                <input type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cedula">
              </div>  
            </div>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <button  type="button" onclick ="listado_ordenes_lab_paciente();" class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>      
        </form>
      </div>
    </div>
    <div class="box-body"  id="listado_ordenes">
    </div>

  </section>


  <script type="text/javascript">

    function listado_ordenes_lab_paciente(){
        $.ajax({
          type: 'post',
          url:"{{route('busqueda.ordenes_lab_paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_ordenes_lab_paciente").serialize(),
          success: function(data){
            console.log(data);
            $("#listado_ordenes").html(data);
            
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }

  </script>


@endsection