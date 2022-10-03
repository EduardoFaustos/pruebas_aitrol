@extends('servicios_generales.mantenimientos.base')
@section('action-content')
@php 
$date = date('Y-m-d');
@endphp

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title"> {{trans('tecnicof.adminmantenimientos')}}</h3>
        </div>
        <div class="col-sm-4">
          <a href="{{route('mantenimientos_generales.create')}}" class="btn btn-primary"><i aria-hidden="true"></i> {{trans('tecnicof.agregarunidades')}}</a </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-sm-6"></div>
          <div class="col-sm-6"></div>
        </div>
        <form method="POST" action="{{ route('mantenimientos_generales.buscar_piso') }}">
          {{ csrf_field() }}

        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">

                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('tecnicof.name')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('tecnicof.description')}}</th>
                    <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">{{trans('tecnicof.action')}}</th>

                  </tr>
                </thead>
                <tbody>
                  @foreach ($mantenimientos_g as $mantenimiento)
                  <tr role="row" class="odd">

                    <td class="sorting_1">{{ $mantenimiento->nombre }}</td>
                    <td> {{ $mantenimiento->descripcion }}</td>
                    <td>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{ route('mantenimientos_generales.edit', ['id' => $mantenimiento->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        {{trans('tecnicof.update')}}
                      </a>
                      <a  href="{{route('mantenimientos_oficinas.listasoficinas', ['id' => $mantenimiento->id]) }}"  class="btn btn-success col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('tecnicof.listasalas')}}
                    </a>
                    <a  href="" class="btn btn-info col-md-8 col-sm-8 col-xs-8 btn-margin">
                      {{trans('tecnicof.listabaños')}}
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
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('tecnicof.showing')}} {{1 + (($mantenimientos_g->currentPage() - 1) * $mantenimientos_g->perPage())}} / {{count($mantenimientos_g) + (($mantenimientos_g->currentPage() - 1) * $mantenimientos_g->perPage())}} {{trans('tecnicof.of')}} {{$mantenimientos_g->total()}} {{trans('tecnicof.records')}}
              </div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{$mantenimientos_g->appends(Request::only(['id','nombre']))->links() }}
              </div>
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