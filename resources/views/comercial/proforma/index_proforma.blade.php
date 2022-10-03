@extends('comercial.proforma.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <form id="form_producto" method="post" action="">
                {{ csrf_field() }}
                <!-- <div class="form-group col-md-4 ">
                    <div class="row">
                        <div class="form-group col-md-10 ">
                            <label for="nombre" class="col-md-4 control-label">N. Proforma:</label>
                            <div class="col-md-8">
                                <input type="text" name="num_prof" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-2 ">
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('prod_tar.buscar')}}</button>
                    </div>
                </div> -->

                <div class="form-group col-md-10">
                    <div class="col-md-7">
                        <a href="{{route('proforma.index')}}" class="btn btn-info">{{trans('contableM.crearproforma')}}</a>
                    </div>
                </div>

                <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                  <label class="control-label col-md-3">{{trans('winsumos.Paciente')}}</label>
                    <div class="col-md-9">
                    <input type="text" name="paciente" id="paciente" class="form-control autocom input-sm" placeholder="{{trans('winsumos.Ingrese_paciente')}}" value="@if(isset($paciente)){{$paciente}}@endif">
                    </div>
                </div>

          
                <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="seguro" class="col-md-3 control-label">Seguro:</label>
                    <div class="col-md-9">
                        <select class="form-control input-sm" name="seguro" id="seguro" onchange="buscar();">
                            <option value="">{{trans('winsumos.seleccione')}}</option>
                        @foreach($seguros as $value)
                            <option @if($value->id==$seguro) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-2 col-xs-6" style="text-align: right;">
                    <button type="submit" formaction="{{ route('comercial.proforma.index_proforma')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.Buscar')}}&nbsp;</span></button>
                </div>

                <div class="col-md-2 text-right">
                    <a href="{{route('comercial.excel_proforma')}}" class="btn btn-success">Excel</a>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>N.</th>
                                        <th>{{trans('proforma.Paciente')}}</th>
                                        <th>{{trans('proforma.observacion')}}</th>
                                        <th>{{trans('proforma.Seguro')}}</th>
                                        <th style="width: 15%;">{{trans('proforma.estado')}}</th>
                                        <th>{{trans('proforma.total')}}</th>
                                        <th>{{trans('proforma.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proformas as $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td> {{$value->nombre1}}  {{$value->nombre2}}  {{$value->apellido1}} {{$value->apellido2}}</td>
                                        <td> {{$value->observacion}}</td>
                                        <td> {{$value->nombre_seguro}}</td>
                                        @if($value->estado == -1)
                                        <td style="background: #f39c12; font-weight: bold; text-align: center;">Proforma pendiente de emitir</td>
                                        @else
                                        <td style="background: #00a65a; font-weight: bold; text-align: center;">Emitida</td>
                                        @endif
                                        <td> {{$value->total}}</td> 
                                        <td>
                                        
                                            @if($value->pagado == 0 and $value->estado == -1)
                                            <a href="{{route('comercial.proforma.editar',['id' => $value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="{{route('comercial.proforma.eliminar_proforma', ['id' => $value->id])}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                            @endif
                                            @if($value->estado == 1)
                                            <a target="_blank" href="{{route('comercial.proforma.pdf_proforma',['id' => $value->id])}}" class="btn btn-success"> <i class="fa fa-download"></i></a>
                                            @endif
                                            @if($value->id_orden != null)
                                                @php 
                                                    $npro   = Sis_medico\Proforma_Cabecera::find($value->id);
                                                    $recibo = $npro->recibo; 
                                                @endphp
                                                @if($recibo->estado == 1)
                                                    <a target="_blank" href="{{ route('facturacion.imprimir_ride', ['id_orden' =>$recibo->id]) }}" class="btn btn-success  btn-xs" ><span class="glyphicon glyphicon-download-alt">Recibo/Cobro</span></a>
                                                @endif
                                            @endif   
                                            @if ($value->id_orden == null and $value->estado == 1)
                                            <a href="{{route('comercial.proforma.editar',['id' => $value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="{{route('comercial.proforma.eliminar_proforma', ['id' => $value->id])}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($proformas->currentPage() - 1) * $proformas->perPage())}} / {{count($proformas) + (($proformas->currentPage() - 1) * $proformas->perPage())}} de {{$proformas->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $proformas->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','id_asiento_cabecera','id','detalle','secuencia_f']))->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false
    })
</script>

@endsection


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">


function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

 </script>