@extends('activosfijos.mantenimientos.activofijo.base')
@section('action-content')
<style type="text/css">
  .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }

    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
</style>
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.activosfijos')}}</a></li>
      <li class="breadcrumb-item"><a href="#">Informe</a></li>
      <li class="breadcrumb-item active">Listado Activo Fijo</li>
    </ol>
  </nav>
  <div class="box">
  	<div class="box-header header_new">
        <div class="col-md-9">
          <h3 class="box-title">Informe Listado de Activos</h3>
        </div>  
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">BUSCADOR DE ACTIVOS FIJOS</label>
      </div>
    </div>

    <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{route('activofjo.excel_listado_general')}}">
        {{ csrf_field() }}
 
          <div class="form-group col-md-1">
            <label class="texto" for="buscar_nombre">{{trans('contableM.tipo')}}: </label>
          </div>

          <div class="form-group col-md-2">
            <select id="id_tipo" name="id_tipo" class="form-control">
              <option value="">{{trans('contableM.seleccione')}}...</option>
              @foreach($tipos as $value)
                <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-1 ">
            <label class="texto" for="desde">{{trans('contableM.Desde')}}: </label>
          </div>
          <div class="form-group col-md-2">
            <input type="date" name="desde" id="desde" class="form-control" value="{{$desde}}">
          </div>
          
          <div class="form-group col-md-1 ">
            <label class="texto" for="hasta">{{trans('contableM.Hasta')}}: </label>
          </div>
          <div class="col-md-2">
            <input type="date" name="hasta" id="hasta" class="form-control" value="{{$hasta}}">
          </div>

          <div class="col-md-1">
            <button type="submit" id="buscar" class="btn btn-xs btn-primary"> Listado Tipo </button>
          </div>     

          <div class="form-group col-md-2">
            <div class="btn-group">
              <button type="submit" id="listado_tipo" name="listado_tipo"  class="btn btn-primary oculto" formaction="{{route('activofjo.index_listado_tipo')}}"> Listado General</button>
              <button type="submit" id="pdflistado" name="pdflistado" class="btn btn-primary oculto" formaction="{{route('activofjo.pdf_listado_general')}}">Pdf Tipo</button>
              <button type="submit" id="pdflistado_tipo" name="pdflistado_tipo" class="btn btn-primary oculto" formaction="{{route('activofjo.pdf_listado_tipo')}}"> Pdf General </button>

              <button type="button" class="btn btn-info">{{trans('contableM.reportes')}}</button>
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="height: 34px;">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
              </button>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a class="btn" onclick="pdf_general();" style="border-bottom: 1px solid #000;"><span>Pdf Tipo</span></a>
                  </li>
                  <li>
                    <a class="btn" onclick="listado_tipo();" style="border-bottom: 1px solid #000;"><span>Listado General</span></a>
                  </li>
                  <li>
                    <a class="btn" onclick="pdf_tipo();" style="border-bottom: 1px solid #000;"><span>Pdf General</span></a>
                  </li>
                </ul>
            </div>
          </div>               
        </form>
        <form method="POST" id="print_reporte_master" action="{{route('activofjo.buscar_listado_activos')}}" target="_blank">
            {{ csrf_field() }}
          <input type="hidden" name="desde" id="desde" value="{{$desde}}">
          <input type="hidden" name="hasta" id="hasta" value="{{$hasta}}">
          <input type="hidden" name="id_tipo" id="id_tipo" value="{{$tipos}}" >
          <div class="col-md-6 col-xs-9">
            <button type="submit" class="btn btn-gray"> <span class="fa fa-search"></span> {{trans('contableM.buscar')}}</button>
          </div>    
        </form>
    </div>   
        

  <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">LISTADO DE ACTIVOS FIJOS</label>
          </div>
  </div>

  <div class="box-body">
    <div class="row">
      <div class="form-group col-md-12">
        <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <div class="table-responsive col-md-12">
                      <table id="example2" class="table table-bordered table-hover dataTable table-striped" aria-describedby="example2_info">
                        <thead>
                          <tr class="well-dark">
                            <th width="5%">{{trans('contableM.id')}}</th>
                            <th width="5%">{{trans('contableM.fechacompra')}}</th>
                            <th width="5%">{{trans('contableM.codigo')}}</th>
                            <th width="25%">{{trans('contableM.nombre')}}</th>
                            <th width="15%">{{trans('contableM.Descripcion')}}</th>
                            <th width="15%">{{trans('contableM.tipo')}}</th>
                            <th width="15%">{{trans('contableM.categoria')}}</th>
                            <th width="5%">{{trans('contableM.marca')}}</th>
                            <th width="5%">{{trans('contableM.modelo')}}</th>
                            <th width="5%">{{trans('contableM.serie')}}</th>
                            <th width="5%">Resp.</th>
                            <th width="5%">{{trans('contableM.ubicacion')}}</th>
                            <th width="5%">{{trans('contableM.vidautil')}}</th>
                            <th width="5%">{{trans('contableM.usuariocrea')}}</th>
                             <th width="5%">{{trans('contableM.estado')}}</th>
                          </tr>
                        </thead>
                        <tbody id="tbl_detalles" name="tbl_detalles">
                          @foreach ($activos_fijos as $value)
                            @php
                            $fcompra= substr($value->fecha_compra, 0, 10);
                            @endphp
                            <tr class="well">
                              <td>{{ $value->id}}</td>
                              <td>{{$fcompra}}</td>
                              <td>{{ $value->codigo}}</td>
                              <td>{{ $value->nombre}}</td>
                              <td>{{ $value->descripcion}}</td>
                              <td>{{ $value->tipo->nombre }}</td>
                              <td>{{ $value->sub_tipo->nombre }}</td>
                              <td> @if(!is_null($value->marca)) {{ $value->marca }} @endif </td>
                              <td>@if(!is_null($value->modelo)) {{ $value->modelo }} @endif</td>
                              <td> @if(!is_null($value->serie)) {{ $value->serie }} @endif </td>
                              <td> @if(!is_null($value->responsable)) {{ $value->responsable}} @endif</td>
                              <td> @if(!is_null($value->ubicacion)) {{$value->ubicacion}} @endif</td>
                              <td> @if(!is_null($value->vida_util)) {{ $value->vida_util}} @endif</td>
                              <td> @if(!is_null($value->id_usuariocrea)) {{$value->user_crea->apellido1}} {{$value->user_crea->nombre1}} @endif</td>
                              <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
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
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($activos_fijos->currentPage() - 1) * $activos_fijos->perPage())}} / {{count($activos_fijos) + (($activos_fijos->currentPage() - 1) * $activos_fijos->perPage())}} de {{$activos_fijos->total()}} registros</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $activos_fijos->appends(Request::only(['codigo', 'nombre']))->links() }}
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

  
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
  function pdf_general(){
    $('#pdflistado').click();   
  }

  function listado_tipo() {
    $('#listado_tipo').click();  
  }

  function pdf_tipo(){
    $('#pdflistado_tipo').click();
  }
</script>
@endsection