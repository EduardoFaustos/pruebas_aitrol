@extends('archivo_plano.archivo.base')
@section('action-content')
<style >
    .table>tbody>tr>td{
    padding: 0;
    }
    .control-label{
        padding: 1;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }
    table.dataTable thead > tr > th {
    padding-right: 10px;
    } 
    td{
        font-size: 12px;
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
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 470px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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
        *border-right-width: 2px;
        *border-bottom-width: 2px;
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
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">Generación Archivo Plano MSP</h3>
		        </div>
		    </div>
		</div>
    <div class="box-body">
      <form id="busq_mes_empresa" method="post" action="{{route('genera_ap_msp_excel.planilla')}}">
        {{ csrf_field() }}
        <div class="row">
            <!--<div class="form-group col-md-4">
              <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
              <div class="col-md-7">
                  <select id="mes_plano" name="mes_plano" class="form-control input-sm">
                      <option value="">Seleccione...</option>
                      @foreach($mes_plan as $value)
                      <option  value="{{$value->mes_plano}}">{{$value->mes_plano}}</option>
                      @endforeach
                  </select>
              </div>
            </div>-->
            <div class="form-group col-md-4 ">
              <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
              <div class="col-md-7">
                  <input id="mes_plano"  type="text" class="form-control input-sm" name="mes_plano">
              </div>
            </div>
            <div class="form-group col-md-4">
              <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
              <div class="col-md-7">
                  <select id="id_empresa" name="id_empresa" class="form-control input-sm">
                      <option value="">Seleccione...</option>
                      @foreach($empresas as $value)
                          @if($value->id == '0992704152001' || $value->id == '1307189140001')
                              <option  value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
            </div>
            <div class="form-group col-md-4">                     
              <div class="col-md-3">
                  <button type="button" onclick="buscar_mes_empresa();" class="btn btn-primary" id="boton_buscar">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
              </div>
              <div class="col-md-4">
                <button type="button" onclick="descargar_reporte();" class="btn btn-primary" id="btn_exportar">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar-Msp
                </button> 
              </div>
              <div class="col-md-2">
                <!--<button type="button" onclick="reporte_consolidado();" class="btn btn-primary" id="btn_exportar">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>Consolidado
                </button>-->
                <button type="button" class="btn btn-primary" id="btn_consolidado">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Consolidado
                </button>
              </div>
            </div>
          </div>
      </form>
      <br>
      <div style="border-radius: 8px;" id="det_busqueda">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
            <thead style="background-color: #4682B4">
              <tr >
                <!--<th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">N°</th>-->
                <th style="width: 8%;height:8px;color: white;text-align: center; font-size: 12px;">Fecha</th>
                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">Código de Validacion </th>
                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">Beneficiario</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Código TSNS</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Descripcion</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Clasificador</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Cantidad Total</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Precio Unitario</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Clasificador %</th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Subtotal </th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Valor por Modificador </th>
                <th style="width: 8%;height:8px;;color: white;text-align: center; font-size: 12px;">Valor Solicitado </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
<form method="POST" id="xls_reporte_consolidado" action="{{ route('genera_msp_rp_consol.planilla') }}">
  {{ csrf_field() }}
    <input type="hidden" name="id_empr" id="id_empr" value="{{@$id_empresa}}">
    <input type="hidden" name="mes_plan" id="mes_plan" value="{{@$mes_plano}}">
    <!--<input type="hidden" name="btn_exportar" id="btn_exportar" value="0">-->
    <input type="hidden" name="btn_consolidado" id="btn_consolidado" value="">
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    /*$(document).ready(function(){
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });
    });*/

    $("#mes_plano").autocomplete({
        source: function( request, response ) {
            
            $.ajax({
                url:"{{route('search.mes_plano')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {term: request.term},
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);

                }
            })
        },
        minLength: 2,
    } );

    function descargar_reporte() {
      $( "#busq_mes_empresa" ).submit(); 
    }

    $( "#btn_consolidado" ).click(function() {
      $("#id_empr").val($("#id_empresa").val());
      $("#mes_plan").val($("#mes_plano").val());
      $( "#xls_reporte_consolidado" ).submit(); 
    });


    /*function reporte_consolidado(){
       
      var formulario = document.forms["busq_mes_empresa"];
      var mes_pla = formulario.mes_plano.value;
      var id_empresa = formulario.id_empresa.value;

      var msj = "";

      if(mes_pla == ""){
        msj = msj + "Por favor, Seleccione el Mes del Plano<br/>";
      }

      if(id_empresa == ""){
        msj = msj + "Por favor, Seleccione la Empresa<br/>";
      }

      if(msj != ""){
        swal({
          title: "Error!",
          type: "error",
          html: msj
        });
        return false;
      }

      $.ajax({
          type: 'post',
          url:"{{route('genera_msp_rp_consol.planilla')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#busq_mes_empresa").serialize(),
          success: function(data){
            console.log(data);
            //$("#det_busqueda").html(data);
          },
          error: function(data){
            console.log(data);
          }
      });
    
    }*/

    function buscar_mes_empresa(){

        var formulario = document.forms["busq_mes_empresa"];
        var mes_pla = formulario.mes_plano.value;
        var id_empresa = formulario.id_empresa.value;
      
        var msj = "";

        if(mes_pla == ""){
          msj = msj + "Por favor, Seleccione el Mes del Plano<br/>";
        }

        if(id_empresa == ""){
          msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        $.ajax({
          type: 'post',
          url:"{{route('buscar.mes_plano')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#busq_mes_empresa").serialize(),
          success: function(data){
            $("#det_busqueda").html(data);
          },
          error: function(data){
            console.log(data);
          }
        });

    }


</script>
@endsection