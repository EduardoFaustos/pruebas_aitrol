@extends('contable.comp_egreso.base')
@section('action-content')
<style type="text/css">
  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box; 
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<script type="text/javascript">  

$(function () {    
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});    
</script>
<div class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.ComprobantedeEgreso')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.RegistroComprobantedeEgreso')}}</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
              <!--<h8 class="box-title size_text">Empleados</h8>-->
              <!--<label class="size_text" for="title">EMPLEADOS</label>-->
              <h3 class="box-title">{{trans('contableM.ComprobantedeEgreso')}}</h3>
            </div>

            <div class="col-md-1 text-right">
              <button onclick="location.href='{{route('acreedores_ccreate')}}'" class="btn btn-success btn-gray" >
                <i class="fa fa-file"></i>
                </button>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">{{trans('contableM.BUSCADORDECOMPDEEGRESO')}}</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="reporte_master" action="{{ route('comp_egreso.buscar') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="id">{{trans('contableM.id')}}</label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
     
            </div>
            
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <select class="form-control select2" style="width: 100%;" name="proveedor" id="proveedor">
                  <option value="">Seleccione...</option>
                  @foreach($proveedor as $value)
                      <option  @if(isset($searchingVals)) {{ $value->id == $searchingVals['id_proveedor'] ? 'selected' : ''}} @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                  @endforeach
              </select>
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="secuencia">{{trans('contableM.secuencia')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="secuencia" name="secuencia" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="detalle">{{trans('contableM.NroCheque')}}</label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="cheque" name="cheque" value="@if(isset($searchingVals)){{$searchingVals['no_cheque']}}@endif" placeholder="Ingrese número de cheque..." />
            </div>
             <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fecha">{{trans('contableM.fechacheque')}}: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text"  name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha_cheque']}}@endif">
                  </div>
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
          <div class="form-group col-md-12">
                  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <table id="example2" class="table-bordered table-hover dataTable table-striped col-md-12" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr class='well-dark'>
                                <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.secuencia')}}</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fechacheque')}}</th>
                                <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.NroCheque')}}</th>
                                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.proveedor')}}</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                                <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                                <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                            
                              @foreach ($comp_egreso as $value)
                                <tr  class="well">
                                  <td >{{$value->secuencia}}</td>
                                  <td >{{$value->fecha_cheque}}</td>
                                  <td >{{$value->no_cheque}}</td>
                                  <td>{{$value->proveedor->nombrecomercial}}</td>
                                  <td>{{$value->valor_pago}} </td>
                                  <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif</td>
                                  <td>
                                   <a class="btn btn-danger btn-gray" target="_blank" href="{{route('pdf_comprobante_egreso',['id'=>$value->id])}}"><i class="fa fa-file-pdf-o "></i></a>
                                   @if(($value->estado)==1)
                                   <a class="btn btn-danger btn-gray" href="{{route('acreedores_anular',['id'=>$value->id])}}"><i class="fa fa-trash"></i></a>
                                   @endif
                                   <a class="btn btn-success btn-gray" href="{{route('egresoa_edit',['id'=>$value->id])}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
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
                            {{ $comp_egreso->appends(Request::only(['id_proveedor', 'id', 'secuencia','no_cheque','fecha_cheque']))->links() }}
                          </div>
                        </div>
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
   $(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': true,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : true,
        'sInfoEmpty':  true,
        'sInfoFiltered': true,
        'language': {
              "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
          }
        });

  });
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
  });
    $(".nombre_proveedor").autocomplete({
      source: function( request, response ) {
          $.ajax( {
          url: "{{route('compra_buscar_nombreproveedor')}}",
          dataType: "json",
          data: {
              term: request.term
          },
          success: function( data ) {
              response(data);
          }
          } );
      },
    minLength: 2,
    } );
    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
                if(data.value != "no"){
                    $('#id_proveedor').val(data.value);
                    $('#proveedor').val(data.value);
                    $('#direccion_id_proveedor').val(data.direccion);
                }else{
                    $('#id_proveedor').val("");
                    $('#proveedor').val("");
                    $('#direccion_proveedor').val("");
                }

            },
            error: function(data){
                console.log(data);
            }
        });
    }
    $('.select2').select2({
            tags: false
    });

</script>

@endsection
