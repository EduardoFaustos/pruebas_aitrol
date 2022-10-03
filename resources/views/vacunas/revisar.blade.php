@extends('vacunas.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade" id="crear_registro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-body">
      <div class="row">
        <div class="form-group col-md-6 ">
          <div class="row">
            <div class="form-group col-md-10 ">

            </div>
          </div>
        </div>
      </div>

      <form method="POST" action="{{route('vacunas.buscar_vacunas')}}">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{$usuario->id}}">
        <div class="form-group col-md-4 col-xs-6">
          <label for="fecha" class="col-md-3 control-label">Desde</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" placeholder="AAAA/MM/DD" value='{{$fecha}}'>
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-6">
          <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" placeholder="AAAA/MM/DD" value='{{$fecha_hasta}}'>
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-2 ">
          <div class="col-md-7">
            <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> Buscar</span></button>
          </div>
        </div>
      </form>

      <div class="col-md-2">
        <div class="col-md-7">
          <a id="crear" class="btn btn-success" href="{{route('vacunas.crear_registro',['id_usuario'=>$usuario->id])}}" data-toggle="modal" data-target="#crear_registro">Crear Registro </span></a>
        </div>
      </div>

      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row" id="listado">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr>
                  <th>Cédula</th>
                  <th>Apellidos</th>
                  <th>Nombres</th>
                  <th>Fecha</th>
                  <th>Biologico</th>
                </tr>
              </thead>
              <tbody>
                @foreach($vacunas as $value)
                <tr>
                  <td>{{$value->id_usuario}}</td>
                  <td>{{$value->apellido1}} {{$value->apellido2}}</td>
                  <td>{{$value->nombre1}} {{$value->nombre2}}</td>
                  <td>{{substr($value->fecha,0,10)}}</td>
                  <td>{{$value->biologico}}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#fecha').datetimepicker({
    useCurrent: false,
    format: 'YYYY/MM/DD',
  });

  $('#fecha_hasta').datetimepicker({
    useCurrent: false,
    format: 'YYYY/MM/DD',
  });
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false
  })
</script>
@endsection