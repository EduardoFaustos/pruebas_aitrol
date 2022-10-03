@extends('archivo_plano.generar.base_generar')
@section('action-content')
<style>
   td {
    font-size: 12px;
    padding: 0;
  }
  .table>tbody>tr>td{
    padding: 2px;
  }
  th{
    font-size: 12px;
  }
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
  .clase{
    padding-left: 2px;
    padding-right: 2px;  
  }
  label {
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  
  .existe_registro{
    position: absolute;
    z-index: 9999;
    bottom: 100px;
    right: 20px;
  }

</style>
<section class="content">
  <div class="box">
	  <div class="box-body">
      <form method="POST" id="generar" action="{{route('planilla.planillas_generadas')}}">
        {{ csrf_field() }}
        <input  name="cont_plano" id="cont_plano" type="text" class="hidden" value="@if(!is_null($archivo_plano)){{count($archivo_plano)}}@endif">
	      <div class="form-group col-md-12">
          <div class="row" >
            <!--<div class="form-group col-md-3 clase">
              <label for="id_empresa" class="col-md-2 control-label">Empresa:</label>
              <div class="form-group col-md-7">
                  <select id="id_empresa" name="id_empresa" class="form-control input-sm">
                    @foreach($empresas as $value)
                     <option  value="{{$value->id}}" @if($id_empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                    @endforeach
                  </select>
              </div>
            </div>-->
            <div class="form-group col-md-1 clase">
              <label for="cedula" class="col-md-12 control-label">Cédula:</label>
              <div class="col-md-12 clase">
                <input id="cedula" maxlength="20" type="text" class="form-control input-sm" name="cedula" value="{{$cedula}}" autocomplete="off">
                <input type="hidden" name="nombres" id="nombre" value="{{$nombres}}">
              </div>
            </div>
            <div class="form-group col-md-3 clase">         
              <label for="paciente" class="col-md-12 control-label">Paciente:</label>
              <div class="col-md-12 clase">
                <input id="paciente" maxlength="70" type="text" class="form-control input-sm" name="paciente" value="{{$nombres}}">
              </div>
            </div>
            <div class="form-group col-md-2 col-xs-4 clase">
              <label for="id_empresa" class="col-md-12 control-label">Empresa:</label>
              <div class="col-md-12 clase">
                  <select id="id_empresa" name="id_empresa" class="form-control input-sm">
                    <option value="">Seleccione ...</option>
                    @foreach($empresas as $value)
                      @if($value->id=='0992704152001' || $value->id=='1307189140001')
                        <option  value="{{$value->id}}" @if($id_empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                      @endif
                    @endforeach
                  </select>
              </div>
            </div>
            <div class="form-group col-md-1 col-xs-4 clase">
              <label for="id_seguro" class="col-md-12 control-label">Seguro:</label>
              <div class="col-md-12 clase">
                  <select id="id_seguro" name="id_seguro" class="form-control input-sm">
                    <option value="">Seleccione</option>    
                   <option  @if($id_seguro == 2) selected @endif value="2">IESS</option>
                   <option  @if($id_seguro == 5) selected @endif value="5">MSP</option>
                  </select>
              </div>
            </div>
            <div class="form-group col-md-1 clase">
              <label for="mes_plano" class="col-md-12 control-label">Mes Plano:</label>
              <div class="col-md-12 clase">
                <input id="mes_plano" maxlength="10" value="{{$mes_plano}}" type="text" class="form-control input-sm" name="mes_plano" autocomplete="off">
              </div>
            </div>
            
            <div class="form-group col-md-1 clase"> 
              <label class="col-md-12 control-label">&nbsp;</label>                    
              <div class="col-md-7">
                <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"></span></button>
              </div>
            </div>
            @if((count($archivo_plano)>0)&&($id_seguro == '2'))
            <div class="form-group col-md-2">                     
              <div class="col-md-7">
                <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('genera_plan.consolidada')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Planilla Consolidada</button>
              </div>
            </div>
            @elseif((count($archivo_plano)>0)&&($id_seguro == '5'))
            <div class="form-group col-md-2">                     
              <div class="col-md-7">
                <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('genera_msp.planilla_cargo_consolidado')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Planilla Consolidada</button>
              </div>
            </div>
            @endif
          </div>
        </div>
      </form>
      <div class="form-group col-md-4">
        
        <!--div class="form-group col-md-1">                     
          <div class="col-md-7">
            <button id="buscar_pac" type="button" class="btn btn-primary" > <span class="glyphicon glyphicon-search"></span></button>
          </div>
        </div-->
      </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr >
                  <th style="text-align: center;">Fecha Ingreso</th>
                  <th style="text-align: center;">Empresa</th>
                  <th style="text-align: center;">Mes Plano</th>
                  <th style="text-align: center;">Seguro</th>
                  <th style="text-align: center;">Tipo Seguro</th>
                  <th style="text-align: center;">Procedimiento</th>
                  <th style="text-align: center;">Usuario Crea</th>
                  <th style="text-align: center;">Fecha Creación</th>
                  <th style="text-align: center;">Total</th>
                  <th style="text-align: center;">Acción</th>
                </tr>
              </thead>
              <tbody>
              @if(count($archivo_plano)>0)
                  @foreach($archivo_plano as $archivo)
                    @php
                      $cantidad_det=$archivo->detalles->count(); 
                      $suma_sub=$archivo->detalles->sum('subtotal');
                      if($archivo->id_seguro == '2'){
                       $suma_tot=$archivo->detalles->where('estado','1')->sum('total');
                      }else{
                        $suma_tot=$archivo->detalles->where('estado','1')->sum('total_solicitado_usd');
                      }
                      $fecha=substr($archivo->fecha_ing,0,10);
                      $fecha_inv=date("d/m/Y",strtotime($fecha));
                    @endphp
                  <tr>                
                    <td>{{$fecha_inv}}</td>
                    <td>{{$archivo->empresa->nombrecomercial}}</td>
                    <td>{{$archivo->mes_plano}}</td>
                    <td>{{$archivo->seguro->nombre}}</td>
                    <td>@if($archivo->id_tipo_seguro !=null){{$archivo->tiposeguro->tipo}}@endif</td>
                    <td>{{$archivo->nom_procedimiento}}</td>
                    <td>{{substr($archivo->usuario_crear->nombre1,0,1)}}. {{$archivo->usuario_crear->apellido1}}</td>
                    <td>{{$archivo->created_at}}</td>
                    <td>{{round($suma_tot,2)}}</td>
                    <td>@if($archivo->id_seguro == '2')
                          <a type="button" class="btn btn-success btn-xs" id="planillar" href="{{route('archivo_plano.planilla_hcid',['hcid' => $archivo->id_hc_procedimimentos, 'id_seguro' => $archivo->id_seguro])}}"> <span  class="glyphicon glyphicon-edit"></span> </a>
                          <a onclick="elimina_cabecera('{{ $archivo->id }}')" class="btn btn-danger btn-xs" > <span  class="glyphicon glyphicon-trash"></span> </a>
                          <a id="planilla1_iess{{$archivo->id }}" class="btn btn-primary btn-xs oculto" id="planilla_iess" href="{{route('archivo_plano.planilla_individual',['idcab' => $archivo->id])}}"> <span class="glyphicon glyphicon-download-alt"></span></a>
                          <a id="planilla_iess" class="btn btn-primary btn-xs" onclick="verifica_registros_iess({{$archivo->id}})"><span class="glyphicon glyphicon-download-alt"></span></a>
                        
                        
                        @elseif($archivo->id_seguro == '5')
                          <a id="planilla_msp" type="button" href="{{route('archivo_plano.planilla_msp',['hcid' => $archivo->id_hc_procedimimentos, 'id_seguro' => $archivo->id_seguro])}}" class="btn btn-success btn-xs"> <span  class="glyphicon glyphicon-edit"></span></a>
                          <a onclick="elimina_cabecera('{{ $archivo->id }}')" class="btn btn-danger btn-xs" id="planillar" href="#"> <span  class="glyphicon glyphicon-trash"></span> </a>
                          <a id="planilla1_msp{{$archivo->id }}" class="btn btn-success btn-xs oculto" id="planilla_msp" href="{{route('archivo_plano_msp.planilla_cargo_individual',['idcab' => $archivo->id])}}"> <span class="glyphicon glyphicon-download-alt"></span> </a>
                          <a id="planilla_msp" class="btn btn-success btn-xs" onclick="verifica_registros_msp({{$archivo->id}})"><span class="glyphicon glyphicon-download-alt"></span></a>
                          

                        @endif
                    </td>
                  </tr>
                  @endforeach
              @endif
              </tbody>
              <tfoot>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
  
  /*$(document).ready(function(){

    cont = document.getElementById('cont_plano').value;

    if(cont == 0){
     
      alert("No Existen Registros");
    }
   
  });*/
     
  $("#paciente").autocomplete({
    source: function( request, response ) {
      //alert("autocomplete");
      $.ajax({
          url:"{{route('planilla.paciente_nombre')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {
              term: request.term
                },
          dataType: "json",
          type: 'post',
          success: function(data){
              response(data);
              console.log(data);

          }
      })
    },
    change:function(event, ui){
      $('#cedula').val(ui.item.id);
      $('#nombre').val(ui.item.value);
      $('#paciente').val(ui.item.nombres);
    },
    minLength: 5,
  });

  $('#example2').DataTable({
      'language': {
        'emptyTable': '<span class="label label-primary" style="font-size:14px;">No existen planillas generadas.</span>'
      },
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[0 , 'desc']]
  })

  function elimina_cabecera(i){
    if (confirm('¿Desea Eliminar el Item?')) {  
      $.ajax({
          url:"{{ url('archivo_plano/planilla/cabecera_planilla/eliminar') }}/" + i,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          data: { id: i },
          success: function(response)
          {
            console.log(response);
            swal({
                    title: "Planilla Eliminada",
                    icon: "success",
                    type: 'success',
                    buttons: true,
            })
            window.location.reload(true);
          }
      });
    
    }else{
     location.reload();
    }
  
  }


  function verifica_registros_iess(id_cabecera){

    $.ajax({
      type: 'post',
      url:"{{ route('verifica_planilla.individual') }}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: {'id_cab': id_cabecera},
          success: function(data){
              //console.log(data);
              if(data == "existe"){

                  window.location = $('#planilla1_iess'+id_cabecera).attr('href');
                  
              }
              if(data == "no_existe"){
                  swal({
                      title: "No existen registros a mostrar",
                      icon: "success",
                      type: 'success',
                      buttons: true,
                  })
                  
              }
          },
          error: function(data){
              console.log(data);
          }
    });

  }

  function verifica_registros_msp(id_cabecera){

    $.ajax({
      type: 'post',
      url:"{{ route('verifica_planilla_cargo.individualmsp') }}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: {'id_cab': id_cabecera},
      success: function(data){
          console.log(data);
          if(data == "existe"){

            window.location = $('#planilla1_msp'+id_cabecera).attr('href');
                        
          }
          if(data == "no_existe"){
              swal({
                  title: "No existen registros a mostrar",
                  icon: "success",
                  type: 'success',
                  buttons: true,
              })
          }
              
      },
      error: function(data){
        console.log(data);
      }
              
    });

  }


</script>
@endsection