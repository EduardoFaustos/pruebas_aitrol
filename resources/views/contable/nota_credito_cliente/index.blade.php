@extends('contable.nota_credito_cliente.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<script type="text/javascript">
  $(function() {
    $(".clickable-row").click(function() {
      window.location = $(this).data("href");
    });
  });
</script>
<div class="content">
  <div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

      </div>
    </div>
  </div>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <!--<li class="breadcrumb-item"><a href="#">Credito Cliente</a></li>-->
      <li class="breadcrumb-item active" aria-current="page">Nota Crédito Cliente </li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-7">
        <!--<h8 class="box-title size_text">Empleados</h8>-->
        <!--<label class="size_text" for="title">EMPLEADOS</label>-->
        <h5><b>NOTAS CRÉDITO CLIENTES</b></h5>
      </div>
      <div class="col-md-5 text-right">
        <!--<button onclick="location.href='{{route('nota_credito_cliente.create')}}'" class="btn btn-success btn-gray" >
                <i class="fa fa-file"></i>
              </button>-->
        <button type="button" onclick="location.href='{{route('nota_credito_cliente.create')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Nota de Cr&eacute;dito
        </button>
        <button type="button" onclick="location.href='{{route('nota_credito_cliente.create2')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Nota de Cr&eacute;dito Parcial
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR NOTAS DE CRÉDITOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="buscar_not_cred" action="{{route('nota_credito_cliente.search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">{{trans('contableM.asiento')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_asiento" name="buscar_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" autocomplete="off" placeholder="Ingrese asiento..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" autocomplete="off" placeholder="Ingrese número de Nota Crédito..." />
        </div>
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="id_cliente">{{trans('contableM.cliente')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-3">
          <select class="form-control select2" style="width: 100%;" name="id_cliente" id="id_cliente">
            <option value="">Seleccione...</option>

            @foreach($clientes as $value)
            <option value="{{$value->identificacion}}" @if(isset($searchingVals)) @if($searchingVals['id_cliente']==$value->identificacion) selected="selected" @endif @endif>{{$value->nombre}}</option>
            @endforeach

          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="concepto">{{trans('contableM.detalle')}}: </label>
        </div>
        <div class="form-group col-md-8 col-xs-10 container-4">
          <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" autocomplete="off" placeholder="Ingrese el detalle..." />
        </div>
        <div class="col-xs-1">
          <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
      <form method="GET" id="xls_reporte_nota_credito_cliente" action="{{route('nota_credito_cliente.exportar_excel')}}">
        <input type="hidden" name="buscar_asiento2" id="buscar_asiento2" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif">
        <input type="hidden" name="numero2" id="numero2" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif">
        <input type="hidden" name="id_cliente2" id="id_cliente2" value="@if(isset($searchingVals)){{$searchingVals['id_cliente']}}@endif">
        <input type="hidden" name="concepto2" id="concepto2" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif">
        <!--  <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
          <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Excel
        </button> -->
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO NOTAS DE CRÉDITOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># de Nota Crédito</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># de Asiento</th>
                        <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cliente')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.tipo')}}</th>
                        <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Crédito</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.totaldeudas')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Abono</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.electronica')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $sum_total_deudas = 0;
                      $sum_total_cred = 0;
                      $sum_total_abono = 0;
                      @endphp
                      @foreach ($nota_credito as $value)
                      @php
                      $fech = substr($value->fecha, 0, 10);
                      $fech_inver = date("d/m/Y",strtotime($fech));
                      $sum_total_deudas = $sum_total_deudas+$value->total_deudas;
                      $sum_total_cred = $sum_total_cred+$value->total_credito;
                      $sum_total_abono = $sum_total_abono+$value->total_abonos;
                      @endphp
                      <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>@if(!is_null($value->id_asiento_cabecera)){{$value->id_asiento_cabecera}}@endif</td>
                        <td>@if(isset($value->cliente)) @if(!is_null($value->cliente->nombre)){{$value->cliente->nombre}}@endif @endif</td>
                        <td>@if(!is_null($fech_inver)){{$fech_inver}}@endif</td>
                        <td>@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                        <td>@if(!is_null($value->concepto)){{$value->concepto}}@endif</td>
                        <td style="text-align: right;">
                          @if(!is_null($value->total_credito))
                          {{$value->total_credito}}
                          @else
                          0.00
                          @endif
                        </td>
                        <td style="text-align: right;">
                          @if(!is_null($value->total_deudas))
                          {{$value->total_deudas}}
                          @else
                          0.00
                          @endif
                        </td>
                        <td style="text-align: right;">
                          @if(!is_null($value->total_abonos))
                          {{$value->total_abonos}}
                          @else
                          0.00
                          @endif
                        </td>
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                        <td>
                          @if($value->electronica==1)
                          SI
                          @endif
                        </td>
                        <td>
                          @if($value->electronica==1)
                          <!--<a href="{{route('notacredito.crear_pdf',['id'=>$value->id])}}" class="btn btn-warning btn-gray" target="_blank" rel="noopener noreferrer"><i class="fa fa-file-pdf-o "></i></a>-->
                          <!--   <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Fact</a> -->
                          <a target="_blank" href="{{ route('facturacion.comprobante_publico_general', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf', 'documento' => 'nota_credito']) }}" class="btn btn-success btn-margin btn-gray col-md-6 col-xs-6" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Retencion</a>
                          <a data-toggle="modal" data-target="#log_factura" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log Factura</a>
                          <!--<a class="btn btn-danger btn-gray" href="{{route('nota_credito_cliente.anular',['id'=>$value->id])}}"><i class="fa fa-trash"></i></a>-->
                          @endif
                          @if(($value->estado)==0)
                          <a class="btn btn-success btn-gray" href="{{route('nota_credito_cliente.edit2',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-close" aria-hidden="true"></i></a>
                          @else
                          
                          <a href="javascript:anular({{$value->id}})" class="btn btn-danger btn-gray"> <i class="fa fa-remove"></i> </a>
                          <a class="btn btn-success btn-gray" href="{{route('nota_credito_cliente.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>

                          @endif
                          @if(Auth::user()->id=="1316262193")
                          <button type="button" class="btn btn-primary" onclick="perm('{{$value->id}}')"> <i class="fa fa-file"></i> </button>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5">&nbsp;</td>
                        <td><b>{{trans('contableM.total')}}</b></td>
                        <td style="text-align: right;"><b>{{number_format($sum_total_deudas, 2, '.', '')}}</b></td>
                        <td style="text-align: right;"><b>{{number_format($sum_total_cred, 2, '.', '')}}</b></td>
                        <td style="text-align: right;"><b>{{number_format($sum_total_abono, 2, '.', '')}}</b></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($nota_credito->currentPage() - 1) * $nota_credito->perPage())}} / {{count($nota_credito) + (($nota_credito->currentPage() - 1) * $nota_credito->perPage())}} de {{$nota_credito->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $nota_credito->appends(Request::only(['id_asiento','id','id_cliente','concepto']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
  $(document).ready(function() {

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false
    });
    $('.select2').select2({

    });


  });

  function perm(id) {
    //alert(id);
    $.ajax({
      type: 'get',
      url: "{{route('nota_credito_reenviar')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {'id':id},
      success: function(data) {
        console.log(data);

      },
      error: function(data) {
        console.log(data);
      }
    })
  }

  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta nota de credito?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        let ids = "";
        var acumulate = "";
        var mensaje = "";

        let msj_ingr = "";
        let msj_ret = "";
        let msj_cruce = "";
        let msj_che = "";
        let msj_cruce_cue = "";
        let msj_credito = "";
        let mensajeaux = "";

        test(id);

      }
    })
  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/cliente/nota_credito/anular/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.href = "{{route('nota_credito_cliente.index')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }
</script>

@endsection