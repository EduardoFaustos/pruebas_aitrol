@extends('contable.comp_egreso.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.ComprobantedeEgreso')}}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistroComprobantedeEgresoVarios')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-7">
        <!--<h8 class="box-title size_text">Comprobante </h8>-->
        <!--<label class="size_text" for="title">Egreso</label>-->
        <h3 class="box-title">Comprobante de Egreso Varios</h3>
      </div>

      <div class="col-md-4 text-right">
        <button onclick="location.href='{{route('egresov.create')}}'" class="btn btn-success btn-gray">
          <i class="fa fa-file"></i> &nbsp; {{trans('contableM.nuevo')}}
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.BUSCADORDECOMPDEEGRESOVARIOS')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('comp_egresov.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">{{trans('contableM.id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />

        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="beneficiario">{{trans('contableM.beneficiario')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="beneficiario" name="beneficiario" value="@if(isset($searchingVals)){{$searchingVals['beneficiario']}}@endif" placeholder="Ingrese beneficiario..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia">{{trans('contableM.secuencia')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="asiento_cabecera">{{trans('contableM.Idasiento')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="asiento_cabecera" name="asiento_cabecera" value="@if(isset($searchingVals)){{$searchingVals['id_asiento_cabecera']}}@endif" placeholder="Ingrese ID Asiento Cabecera..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="detalle">{{trans('contableM.nro')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="cheque" name="cheque" value="@if(isset($searchingVals)){{$searchingVals['nro_cheque']}}@endif" placeholder="Ingrese número de cheque..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fechacheque')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha_cheque']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="descripcion">{{trans('contableM.concepto')}}:</label>
        </div>
        <div class="form-group col-md-3 col-xs-5">
          <input class="form-control" type="text" name="descripcion" id="descripcion" value="@if(isset($searchingVals)){{$searchingVals['descripcion']}}@endif" placeholder="Ingrese concepto">
        </div>
        <div class="col-xs-1">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.LISTADODECOMPROBANTES')}}</label>
      </div>
    </div>
    <div class="box-body dobra">

      <div class="row">
        <div class="table-responsive col-md-12" style="width: 100%;">
          <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr class='well-dark'>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.asientocabecera')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.serie')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fechacheque')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.NroCheque')}}</th>
                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Descripcion')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.creadopor')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($comp_egreso as $value)
              <tr class="well">
                <td>{{$value->id_asiento_cabecera}}</td>
                <td>{{$value->secuencia}}</td>
                <td>{{$value->fecha_cheque}}</td>
                <td>{{$value->nro_cheque}}</td>
                <td>{{$value->beneficiario}}</td>
                <td>{{$value->descripcion}}</td>
                <td>{{$value->valor}}</td>
                <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif </td>
                <td>
                  <a class="btn btn-warning btn-gray" target="_blank" href="{{route('pdf_egresovarios',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o "></i></a>
                  @if(($value->estado)==1)
                  <a class="btn btn-danger btn-gray" href="javascript:anular('{{$value->id}}')"><i class="fa fa-trash"></i></a>
                  @endif
                  <a class="btn btn-success btn-gray" href="{{route('egresosv.edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>

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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($comp_egreso->currentPage() - 1) * $comp_egreso->perPage())}} / {{count($comp_egreso) + (($comp_egreso->currentPage() - 1) * $comp_egreso->perPage())}} de {{$comp_egreso->total()}} {{trans('contableM.registros')}}</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $comp_egreso->appends(Request::only(['beneficiario', 'id', 'secuencia','no_cheque','fecha_cheque','descripcion','id_asiento_cabecera', 'cheque']))->links() }}
          </div>
        </div>
      </div>


    </div>
  </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'sInfoEmpty': true,
      'sInfoFiltered': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });

  });
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });

  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta comprobante?',
      text: `{{trans('contableM.norevertiraccion')}}!`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        var acumulate = "";
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
          url: "{{ url('contable/acreedores/documentos/cuentas/egreso/varios/anular/')}}/" + id,
          datatype: 'json',
          data: {
            'concepto': text
          },
          success: function(data) {
            Swal.fire(`{{trans('contableM.correcto')}}!`, `{{trans('contableM.anulacioncorrecta')}}`, "success");
            location.reload();
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