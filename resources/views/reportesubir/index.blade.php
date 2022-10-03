@extends('reportesubir.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />

<section class="content">
<div class="modal fade" id="reportesubir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
 
<div class="box">
  <div class="box-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12" style="text-align: right;">
                        <a type="button" href="{{url('agenda') }}" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">Regresar</span>
                        </a>
        </div>
        <div class="col-md-2">
                <a href="{{ route('reportesubir.vistareporte')}}" data-toggle="modal" data-target="#reportesubir" class="btn btn-primary">
                  Subir Reporte
                </a>                 
              </div>
       <div class="form-group col-md-6">
       
      </div>
     <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Total de Agendas</th>
                <th>Asisten</th>
                <th>No asisten</th>
                <th>No responden </th>
                <th>No Procesados</th>
                <th>Detalle</th>
          </tr>
          </thead>
          <tbody>

              @foreach ($callcontroler as $value)

                <tr role="row" class="odd">
                  
                  <td>{{$value->created_at}}</td>
                  <td>{{$value->usuario->apellido1}} {{$value->usuario->nombre1}}</td>
                  <td>{{($value->total_aceptado)+($value->total_rechazado)+($value->total_noresp)+($value->total_suspendido)+($value->total_noprocesado)}}</td>
                  <td>{{$value->total_aceptado}}</td>
                  <td>{{$value->total_rechazado}}</td>
                  <td>{{$value->total_noresp}}</td>
                  <td>{{$value->total_noprocesado}}</td>
                  <td> <input type="hidden" >
                              <a href="{{ route('reportesubir.detalle', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                              Detalle
                              </a>
                  </td>
                </tr>
                  @endforeach
                  </tr>
                </tbody>
              </table>

              <!---Paginador-->
              
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($callcontroler)}} de {{$callcontroler->total()}} registros</div>
                  </div>
              <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                   {{ $callcontroler->links()}}
                  </div>
              </div>
              </div>


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $('#reportesubir').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
</script>
<script type="text/javascript">
  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 0, "asc" ]]
    });
</script>

@endsection

  