@extends('contable.tipo_pago.base')
@section('action-content')

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item active">Tipo Pago</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">
        <h5><b>TIPO DE PAGO</b></h5>
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('tipo_pago.crear')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Tipo Pago
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR TIPO DE PAGO</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="buscad_tip_Pago" action="{{route('tipo_pago.search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_codigo">{{trans('contableM.codigo')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_codigo" name="buscar_codigo" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" autocomplete="off" placeholder="Ingrese codigo..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_tipo_pago">Tipo Pago:</label>
        </div>
        <div class="form-group col-md-5 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_tipo_pago" name="buscar_tipo_pago" value="@if(isset($searchingVals)){{$searchingVals['nombre']}}@endif" autocomplete="off" placeholder="Ingrese el Tipo Pago..." />
        </div>
        <div class="col-xs-2">
          <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO TIPO DE PAGO</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-row">
        <div id="contenedor">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr class="well-dark">
                      <th width="28%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Pago</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.codigo')}}</th>
                      <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($tip_pago as $value)
                    <tr class="well">
                      <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                      <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                      <td>
                        @if($value->estado == 1)
                        Activo
                        @elseif($value->estado == 0)
                        Inactivo
                        @endif
                      </td>
                      <td>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{route('tipo_pago.editar', $value->id)}}" class="btn btn-success btn-gray">
                          <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($tip_pago->currentPage() - 1) * $tip_pago->perPage())}} / {{count($tip_pago) + (($tip_pago->currentPage() - 1) * $tip_pago->perPage())}} de {{$tip_pago->total()}} registros
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{$tip_pago->appends(Request::only(['id','nombre']))->links()}}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': false ,
    'info': false,
    'autoWidth': false
  })
</script>
@endsection