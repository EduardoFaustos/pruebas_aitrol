@extends('insumos.bodega.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Listado de Bodegas</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{route('bodega.create')}}">Agregar nueva Bodega</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('bodega.search')}}">
         {{ csrf_field() }}
         @component('layouts.search1', ['title' => 'Buscar'])
          @component('layouts.one-cols-search-row', ['items' => ['Nombre'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['nombre'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div >
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" >
            <thead>
              <tr >
                <th >Nombre</th>
                <th >Ubicación</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Color</th>
                <th >Fecha de Creación</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($bodegas as $value)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $value->nombre}}</td>
                  <td >{{ $value->nombre_hospital}}</td>
                  <td style="background-color: {{ $value->color }}; color: white">{{ $value->color }}</td>
                  <td >{{ $value->created_at }}</td>
                  <td>  
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('bodega.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($bodegas)}} de {{$bodegas->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $bodegas->links() }}
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
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  



 </script> 
@endsection