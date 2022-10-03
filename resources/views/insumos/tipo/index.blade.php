

@extends('insumos.tipo.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">{{trans('winsumos.lista_productos')}}</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('tipo.create') }}">{{trans('winsumos.agregar_nuevo_tipo')}}</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{ route('tipo.search') }}">
         {{ csrf_field() }}
         @component('layouts.search1', ['title' => trans('winsumos.Buscar')])
          @component('layouts.one-cols-search-row', ['items' => ['Nombre'], 
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
              <tr >
                <th >{{trans('winsumos.nombre')}}</th>
                <th >{{trans('winsumos.descripcion')}}</th>
                <th >{{trans('winsumos.estado')}}</th>
                <th >{{trans('winsumos.accion')}}</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($tipo as $tipo)
                <tr >
                  <td> {{ $tipo->nombre }}</td>
                  <td> {{ $tipo->descripcion }}</td>
                  <td> {{$tipo->estado == 0 ? trans('winsumos.inactivo') : trans('winsumos.activo')}}</td>
                  <td>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('tipo.edit', ['id' => $tipo->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        {{trans('winsumos.actualizar')}}
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 / {{count($tipo)}} {{trans('winsumos.de')}} {{$total}} {{trans('winsumos.registros')}}</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $tipos->links() }}
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
    
    $(document).ready(function(){


    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });


    
    
});


  </script>
@endsection