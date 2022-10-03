@extends('insumos.plantillas.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title">{{trans('winsumos.lista_plantillas_enfermeria')}}</h3>
        </div>
        <div class="col-sm-6">
          <a class="btn btn-primary" href="{{route('plantilla.crear')}}">{{trans('winsumos.agregar_plantilla')}}</a>
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
<form method="POST" action="{{route('plantilla.buscar')}}">
  {{ csrf_field() }}
  <div class="row">
    <div class="form-group col-md-4 ">
      <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre')}}</label>
      <div class="col-md-7">
          <input id="nombre" value="{{$nombre}}"  type="text" class="form-control input-sm" name="nombre">
      </div>
    </div>
    <div class="form-group col-md-2 ">
      <button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search" >{{trans('winsumos.Buscar')}}</span></button>
    </div>
  </div>
    
</form>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="frmpro" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
            
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">{{trans('winsumos.codigo')}}</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('winsumos.nombre')}}</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('winsumos.estado')}}</th>
                <th  width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">{{trans('winsumos.accion')}}</th>
                
              </tr>
            </thead>
            <tbody>
                @foreach($plantillas as $value)
                <tr>
                  <td>{{$value->codigo}}</td>
                  <td>{{$value->nombre}}</td>
                  <td>@if($value->estado==1) {{trans('winsumos.activo')}} @else {{trans('winsumos.inactivo')}} @endif</td>
                  <td>
                    <a href="{{route('plantilla.edit',['id' =>$value->id])}}" class="btn btn-warning ">
                        {{trans('winsumos.actualizar')}}
                    </a>
                    <a href="{{route('plantilla.item_lista',['id' =>$value->id])}}" class="btn btn-success ">
                        {{trans('winsumos.items')}}
                    </a>
                    <a href="{{route('plantilla.eliminar_plantilla',['id' => $value->id])}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} {{1 + (($plantillas->currentPage() - 1) * $plantillas->perPage())}} / {{count($plantillas) + (($plantillas->currentPage() - 1) * $plantillas->perPage())}} {{trans('winsumos.de')}} {{$plantillas->total()}} {{trans('winsumos.registros')}}</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $plantillas->appends(Request::only(['id', 'nombres']))->links() }}
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