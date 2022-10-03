@extends('tipo_usuario-mgmt.base')
@section('action-content')


<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('tipo_usuario-mgmt.listatipodeusuario')}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('tipo_usuario-management.create') }}">{{trans('tipo_usuario-mgmt.agregarnuevotipodeusuario')}}</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <!--AQUI VA EL BUSCADOR-->
      <form method="POST" action="{{ route('tipo_usuario-management.search') }}">
        {{ csrf_field() }}
        @component('layouts.search', ['title' => 'Buscar'])
        @component('layouts.two-cols-search-row', ['items' => ['Nombre'],
        'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
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

                  <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('tipo_usuario-mgmt.nombre')}}</th>
                  <th width="60%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('tipo_usuario-mgmt.descripcion')}}</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">{{trans('tipo_usuario-mgmt.accion')}}</th>

                </tr>
              </thead>
              <tbody>
                @foreach ($tipo_usuarios as $tipo_usuario)
                <tr role="row" class="odd">

                  <td class="sorting_1">{{ $tipo_usuario->nombre }}</td>
                  <td> {{ $tipo_usuario->descripcion }}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('tipo_usuario-management.edit', ['id' => $tipo_usuario->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('tipo_usuario-mgmt.actualizar')}}
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
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('tipo_usuario-mgmt.mostrando')}} 1 / {{count($tipo_usuarios)}} de {{count($tipo_usuarios)}} {{trans('tipo_usuario-mgmt.registros')}}</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $tipo_usuarios->appends(Request::only(['nombre']))->links() }}
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