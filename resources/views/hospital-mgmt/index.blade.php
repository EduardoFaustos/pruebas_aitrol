@extends('hospital-mgmt.base')
@section('action-content')


<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('hospital-mgmt.listadeunidadesdeatencion')}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('hospital-management.create') }}">{{trans('hospital-mgmt.agregarnuevaunidad')}}</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('hospital-management.search') }}">
        {{ csrf_field() }}
        @component('layouts.search', ['title' => 'Buscar'])
        @component('layouts.two-cols-search-row', ['items' => ['Nombre Unidad'],
        'oldVals' => [isset($searchingVals) ? $searchingVals['nombre_hospital'] : '']])
        @endcomponent
        </br>
        @endcomponent
      </form>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">

                  <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('hospital-mgmt.nombreunidad')}}</th>
                  <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('hospital-mgmt.ciudad')}}</th>
                  <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('hospital-mgmt.direccion')}}</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">{{trans('hospital-mgmt.accion')}}</th>

                </tr>
              </thead>
              <tbody>
                @foreach ($hospitales as $hospital)
                <tr role="row" class="odd">

                  <td class="sorting_1">{{ $hospital->nombre_hospital }}</td>
                  <td> {{ $hospital->ciudad }}</td>
                  <td> {{ $hospital->direccion }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('hospital-management.edit', ['id' => $hospital->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('hospital-mgmt.actualizar')}}
                    </a>
                    <a href="{{ route('sala-management.listasalas', ['id' => $hospital->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('hospital-mgmt.salas')}}
                    </a>
                    <a href="{{ route('camilla-management.listascamillas', ['id' => $hospital->id]) }}" class="btn btn-primary col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('hospital-mgmt.camillas')}}
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>

              </tfoot>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('hospital-mgmt.mostrado')}} 1 / {{count($hospitales)}} de {{count($hospitales)}} {{trans('hospital-mgmt.registros')}}</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $hospitales->links() }}
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
@endsection