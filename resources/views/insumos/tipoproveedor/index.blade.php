@extends('insumos.tipoproveedor.base')
@section('action-content')
<script type="text/javascript">
    function goBack() {
        location.href = "{{ route('proveedor.index') }}";
    }
</script>

<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-sm-6">
            <h3 class="box-title">{{trans('winsumos.lista_tipos_proveedores')}}</h3>
          </div>
          <div class="col-sm-4">
            <a class="btn btn-primary" href="{{ route('tipo_proveedor.create') }}">{{trans('winsumos.agregar_nuevo_tipo')}}</a>
          </div>
          <div class="col-md-2" style="text-align: right;">
          <button type="button" class="btn btn-success btn-gray" onclick="goBack()" >
              <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.regresar')}}
          </button>
          </div>
      </div>
    </div>
    <div class="box-body">
       
        <form method="POST" action="{{ route('tipo_proveedor.index') }}">
           {{ csrf_field() }}
             <div class="col-md-6">
                    <div class="row">

                    </div>
                 
                    <div class="form-group col-md-1 col-xs-2">
                        <label class="texto" for="nombre">{{trans('winsumos.nombre')}}</label>
                    </div>
                    <div class="form-group col-md-8 col-xs-10 container-4">
                        <input class="form-control" type="text" id="nombre" name="nombre" value="@if(isset($busqueda_proveedor['nombre']))@if(!is_null($busqueda_proveedor['nombre'])){{$busqueda_proveedor['nombre']}}@endif @endif" placeholder="{{trans('winsumos.ingrese_nombre')}}..." />
                    </div>
                   
                 
                </div>
                <div class="col-md-4 col-xs-2 col-xs-10 container-4">
                    <button type="submit" id="buscarEmpleado" class="btn btn-primary">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>{{trans('winsumos.Buscar')}}
                    </button>
                </div>
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                    <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('winsumos.nombre')}}</th>
                    <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">{{trans('winsumos.descripcion')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">{{trans('winsumos.accion')}}</th>

                  </tr>
                </thead>
                <tbody>
                @foreach ($tipos as $empresa)
                  <tr role="row" class="odd">
                    <td> {{ $empresa->nombre }}</td>
                    <td> {{ $empresa->descripcion }}</td>
                    <td>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('tipo_proveedor.edit', ['id' => $empresa->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
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
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 / {{count($tipos)}} {{trans('winsumos.de')}} {{$tipos->total()}} {{trans('winsumos.registros')}}
              </div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $tipos->links() }}
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</section>
@endsection