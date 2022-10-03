@extends('especialidad.base')
@section('action-content')
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('especialidad.listadeespecialidades')}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{route('especialidad.create')}}">{{trans('especialidad.agregarnuevaespecialidad')}}</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('especialidad.search')}}">
        {{ csrf_field() }}
        @component('layouts.search', ['title' => 'Search'])
        @component('layouts.two-cols-search-row', ['items' => ['Nombre de la Especialidad'],
        'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
        @endcomponent
        @endcomponent
      </form>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">
                  <th width="40%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">{{trans('especialidad.nombredeespecialidad')}}</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('especialidad.descripcion')}}</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">{{trans('especialidad.fechadecreado')}}</th>
                  <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">{{trans('especialidad.accion')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($especialidades as $value)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $value->nombre}}</td>
                  <td>{{ $value->descripcion}}</td>
                  <td>{{ $value->created_at }}</td>
                  <td><input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('especialidad.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('especialidad.actualizar')}}
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('especialidad.mostrando')}} 1 al {{count($especialidades)}} de {{count($especialidades)}} {{trans('especialidad.registros')}}</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $especialidades->links() }}
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