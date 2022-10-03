@extends('contable.caja_banco.base')
@section('action-content')

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('mcaja_banco.contable')}}</a></li>
      <li class="breadcrumb-item active">{{trans('mcaja_banco.cajabanco')}}</< /li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">
        <h5><b>{{trans('mcaja_banco.cajaybancos')}}</b></h5>
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('caja_banco.crear')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>{{trans('mcaja_banco.agregarcajaybancos')}}
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('mcaja_banco.buscadorcajaybancos')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="buscad_caja_banco" action="{{ route('caja_banco.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_codigo">{{trans('mcaja_banco.codigo')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_codigo" name="buscar_codigo" value="@if(isset($searchingVals)){{$searchingVals['codigo']}}@endif" autocomplete="off" placeholder="{{trans('mcaja_banco.ingresecodigo')}}..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_nombre">{{trans('mcaja_banco.nombre')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_nombre" name="buscar_nombre" value="@if(isset($searchingVals)){{$searchingVals['nombre']}}@endif" autocomplete="off" placeholder="{{trans('mcaja_banco.ingresenombre')}}..." />
        </div>
        <div class="col-xs-2">
          <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('mcaja_banco.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('mcaja_banco.listadoscajaybancos')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-row">
        <div id="contenedor">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr class="well-dark">
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.codigo')}}</th>
                      <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.nombre')}}</th>
                      <th width="14%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.clase')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.grupo')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.mayorcuenta')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.empresa')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.sucursal')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.formapago')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.estado')}}</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('mcaja_banco.accion')}}</th>
                    </tr>
                  </thead>
                  <tbody style="background:#F5F5F5">
                    @foreach ($caja_banco as $value)
                    @php
                    $obt_nomb_grupo = Sis_medico\Plan_Cuentas::find($value->grupo);
                    $obt_nomb_cuenta = Sis_medico\Plan_Cuentas::find($value->cuenta_mayor);
                    $obt_nomb_empresa = Sis_medico\Empresa::find($value->id_empresa);
                    $obt_nomb_sucursal = Sis_medico\Ct_Sucursales::find($value->sucursal);
                    $obt_tip_pago = Sis_medico\Ct_Tipo_Pago::find($value->formas_pago);
                    @endphp
                    @if(isset($obt_nomb_cuenta->pempresa))
                    <tr>
                      <td>@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                      <td>@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                      <td>
                        @if($value->clase == 1)
                        Cuenta Bancaria
                        @elseif($value->clase == 2)
                        Cuenta de Caja
                        @endif
                      </td>
                      <td>@if(!is_null($obt_nomb_grupo)){{$obt_nomb_grupo->pempresa->nombre}}@endif</td>
                      <td>@if(!is_null($obt_nomb_cuenta)){{$obt_nomb_cuenta->pempresa->nombre}}@endif</td>
                      <td>@if(!is_null($obt_nomb_empresa)){{$obt_nomb_empresa->razonsocial}}@endif</td>
                      <td>@if(!is_null($obt_nomb_sucursal)){{$obt_nomb_sucursal->nombre_sucursal}}@endif</td>
                      <td>@if(!is_null($obt_tip_pago)){{$obt_tip_pago->nombre}}@endif</td>
                      <td>@if($value->estado == 1)
                        Activo
                        @elseif($value->estado == 0)
                        Inactivo
                        @endif
                      </td>
                      <td>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('caja_banco.editar', ['id' => $value->id,'id_emp' => $empresa->id]) }}" class="btn btn-success btn-gray">
                          <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>
                      </td>
                    </tr>
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('mcaja_banco.mostrando')}} {{1 + (($caja_banco->currentPage() - 1) * $caja_banco->perPage())}} / {{count($caja_banco) + (($caja_banco->currentPage() - 1) * $caja_banco->perPage())}} de {{$caja_banco->total()}} registros
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $caja_banco->appends(Request::only(['codigo','nombre','buscar_nombre','buscar_codigo']))->links() }}
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
    'ordering': true,
    'info': false,
    'autoWidth': false
  })
</script>
@endsection