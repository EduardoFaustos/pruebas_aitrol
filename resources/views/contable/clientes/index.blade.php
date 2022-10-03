@extends('contable.clientes.base')
@section('action-content')

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item active">{{trans('contableM.Clientes')}}</li>    
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h5><b>{{trans('contableM.Clientes')}}</b></h5>
            </div>
            <div class="col-md-1 text-right">
              <button type="button" onclick="location.href='{{route('clientes.crear')}}'" class="btn btn-success btn-gray">
                   <i aria-hidden="true"></i>{{trans('contableM.agregarnuevocliente')}}
              </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">{{trans('contableM.buscadorclientes')}}</label>
          </div>
        </div>
        <div class="box-body dobra">
          <form method="POST" id="buscad_clientes" action="{{ route('clientes.index') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-1 col-xs-2">
               <label class="texto" for="buscar_identificacion">{{trans('contableM.identificacion')}}: </label>
            </div>
            <div class="form-group col-md-2 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_identificacion" name="buscar_identificacion" value="@if(isset($searchingVals)){{$searchingVals['buscar_identificacion']}}@endif" autocomplete="off" placeholder="Ingrese Identificacion..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="buscar_nombre">{{trans('contableM.nombre')}}: </label>
            </div>
            <div class="form-group col-md-2 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_nombre" name="buscar_nombre" value="@if(isset($searchingVals)){{$searchingVals['buscar_nombre']}}@endif" autocomplete="off" placeholder="Ingrese el nombre..." />
            </div>
            <!-- Lopez -->
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="buscar_correo">{{trans('contableM.email')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="buscar_correo" name="buscar_correo" value="@if(isset($searchingVals)){{$searchingVals['buscar_correo']}}@endif" autocomplete="off" placeholder="Ingrese el correo..." />
            </div>
            <!-- -->
            <div class="col-xs-2">
              <button type="submit" id="buscarCodigo" class="btn btn-primary">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
              </button>
            </div>
          </form>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" >LISTADO CLIENTES</label>
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
                        <tr class="well-dark" >
                          <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.identificacion')}}</th>
                          <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.nombre')}}</th>
                          <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.direccion')}}</th>
                          <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.email')}}</th>
                          <th width="7%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                          <th width="7%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                        </tr>
                      </thead>
                      <tbody>
                          @foreach ($clientes as $value)
                            <tr class="well">
                              <td>@if(!is_null($value->identificacion)){{$value->identificacion}}@endif</td>
                              <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                              <td>@if(!is_null($value->direccion_representante)){{$value->direccion_representante}}@endif</td>
                              <td>@if(!is_null($value->email_representante)){{$value->email_representante}}@endif</td>
                              <td>
                                @if($value->estado == 1)
                                  Activo
                                @elseif($value->estado == 0)
                                  Inactivo
                                @endif
                              </td>
                              <td>
                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                  @if($value->estado == '1')
                                    <a href="{{ route('clientes.editar', ['id' => $value->identificacion]) }}" class="btn btn-success btn-gray">
                                      <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                                    </a>
                                  @endif
                              </td>
                            </tr>
                          @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($clientes->currentPage() - 1) * $clientes->perPage())}} / {{count($clientes) + (($clientes->currentPage() - 1) * $clientes->perPage())}} de {{$clientes->total()}} registros
                     </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $clientes->appends(Request::only(['identificacion','nombre','correo']))->links() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </section>
  </div>
  <script type="text/javascript">
    
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    })
  
  </script> 

@endsection