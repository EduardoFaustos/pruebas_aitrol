@extends('paciente.base')
@section('action-content')
<style type="text/css">
  table {
    font-size: 12px;
  }

  .warning {
    background-color: #e08e0b !important;
  }

  .success {
    background-color: #00a65a !important;
  }

  .xinfo {
    background-color: #00c0ef;
  }

  .navy {
    background-color: #001f3f !important;
  }

  .dropdown-menu>li>a {
    color: white !important;
  }

  .warning>li>a:hover {
    background-color: #d58512 !important;
  }

  .success>li>a:hover {
    background-color: #008d4c !important;
  }

  .xinfo>li>a:hover {
    background-color: #00acd6 !important;
  }

  .navy>li>a:hover {
    background-color: #23527c !important;
  }

  .table-bordered>tbody>tr>td {
    padding: 2px !important;
  }
</style>

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('pacientes.listapacientes')}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('agenda.paciente', ['id' => '0', 'i' => '0', 'fecha' => '0', 'sala' => '0'])}}">{{trans('pacientes.agregarnuevop')}}</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('paciente.search')}}">
        {{ csrf_field() }}
        @component('layouts.search', ['title' => 'Buscar'])
        @component('layouts.two-cols-search-row', ['items' => ['Cédula', 'Apellidos', 'Nombres'],
        'oldVals' => [isset($searchingVals) ? $searchingVals['id'] : '', isset($searchingVals) ? $searchingVals['apellido1'] : '', isset($searchingVals) ? $searchingVals['nombre1'] : '' ]])

        @endcomponent
        @endcomponent
      </form>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12" style="padding-bottom: 90px;padding-right: 44px; ">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">
                  <th width="5%">{{trans('pacientes.cedula')}}</th>
                  <th width="23%">{{trans('pacientes.nombres')}}</th>
                  <th width="7%">{{trans('pacientes.telefonos')}}</th>
                  <th width="5%">{{trans('pacientes.pacientedesde')}}</th>
                  <th width="15%">{{trans('pacientes.editbiopagenda')}}</th>
                  <th width="15%">{{trans('pacientes.archivosmedicos')}}</th>
                  <th width="15%">{{trans('pacientes.ordenes')}}</th>
                  <th width="15%">{{trans('pacientes.laboratorio')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($paciente as $value)
                <tr>
                  <td>{{ $value->id}}</td>
                  <td>{{$value->nombre1}} @if($value->nombre2!='(N/A)'){{$value->nombre2}}@endif {{$value->apellido1}} @if($value->apellido2!='(N/A)'){{$value->apellido2}}@endif</td>
                  <td>{{ $value->telefono1.' / '.$value->telefono2.' /'.$value->telefono3 }}</td>
                  <td>{{ substr($value->created_at,0,10) }}</td>
                  <td>
                    <div class="btn-group">
                      <a type="button" class="btn btn-warning btn-xs" href="{{ route('paciente.edit', ['id' => $value->id]) }}">{{trans('pacientes.editarpaciente')}}</a>
                      <div class="btn-group">
                        <button type="button" class="btn btn-warning dropdown-toggle btn-xs" data-toggle="dropdown">
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu warning">
                          <li><a href="{{ route('ingreso.biopsias2', ['id_paciente' => $value->id]) }}">{{trans('pacientes.pacientedesde')}}</a></li>
                          <li><a href="{{ route('paciente.historial_agenda', ['id_paciente' => $value->id]) }}">{{trans('pacientes.documentosagenda')}}</a></li>
                          <li><a href="{{route('proforma.proforma_paciente', ['id_paciente' => $value->id])}}"> Proformas Paciente </a></li>
                        </ul>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="btn-group">
                      <a type="button" class="btn btn-success btn-xs" href="{{ route('paciente.historia', ['id' => $value->id]) }}" target="_blank">{{trans('pacientes.historiaclinica')}}</a>
                      <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle btn-xs" data-toggle="dropdown">
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu success">
                          <li><a href="{{ route('paciente.historial_recetas', ['id' => $value->id])}}">{{trans('pacientes.ingresoverecetas')}}</a></li>
                          <li><a href="{{ route('paciente.historial_estudios', ['id_paciente' => $value->id]) }}">{{trans('pacientes.verestudios')}}</a></li>
                          <li><a href="{{ route('paciente.historial_imagenes', ['id_paciente' => $value->id]) }}">{{trans('pacientes.verimagen')}}</a></li>
                          <li><a href="{{ route('paciente.historial_documentos', ['id_paciente' => $value->id]) }}">{{trans('pacientes.verdocumentos')}}</a></li>
                        </ul>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="btn-group">
                      <a type="button" class="btn btn-info btn-xs" href="{{ route('paciente.historial_ordenes', ['id' => $value->id])}}">{{trans('pacientes.procedimientos')}}</a>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown">
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu xinfo">
                          <li><a href="{{ route('paciente.historial_orden_biopsias', ['id' => $value->id])}}">{{trans('pacientes.biopsias')}}</a></li>
                        </ul>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="btn-group">
                      <a type="button" class="btn bg-navy btn-xs" href="{{ route('paciente.historial_orden_lab', ['id' => $value->id])}}">{{trans('pacientes.humanalabs')}}</a>
                      <div class="btn-group">
                        <button type="button" class="btn bg-navy dropdown-toggle btn-xs" data-toggle="dropdown">
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu navy">
                          <li><a href="{{ route('laboratorio.externo', ['id_paciente' => $value->id]) }}">{{trans('pacientes.externo')}}</a></li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('pacientes.mostrando')}} 1 al {{count($paciente)}} de {{$paciente->total()}} {{trans('pacientes.registros')}}</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $paciente->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
</div>


<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': true,

  });
</script>

@endsection