@extends('laboratorio.orden.base')

@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .table-hover>tbody>tr:hover{
      background-color: #b3ffe6;
      cursor:pointer;
    }
    p{
      font-size:12px;
    }

</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal editar -->
<div class="modal fade" id="edit_crea_sub" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-12">    
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="col-md-6"><h4>Paciente : <span style="color: red;">{{$orden->id_paciente}}-{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif {{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif</span></div>
                            <div class="col-md-2"><h4>Edad : <span id="edad" name="edad" style="color: red;"></span></h4></div>
                            
                            <div class="col-md-2">  
                              <form method="POST" action="{{route('orden.search_control')}}" id="lab_control">
                                {{ csrf_field() }}
                                 <input type="hidden" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" value="{{$fecha}}">
                                 <input type="hidden" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$fecha_hasta}}">
                                 <input type="hidden" class="form-control input-sm" name="seguro" id="seguro" autocomplete="off" value="{{$seguro}}">
                                 <input value="@if($nombres!=''){{$nombres}}@endif" type="hidden" class="form-control input-sm" name="nombres" id="nombres">
                                  
                                  <button type="submit" class="btn btn-success btn-sm" id="boton_buscar">
                                    Regresar
                                  </button>                                    
                              </form>
                            </div>
                            <button class="btn btn-primary btn-sm" id="btn_val">Imprimir</button>
                            <!--div class="col-md-2">   
                              <a class="btn btn-primary btn-sm oculto" id="btn_imp" target="_blank" href="{{ route('resultados.imprimir',['id' => $orden->id]) }}">Imprimir</a>
                            </div-->
                        </div>
                    </div>
                    <!--div class="col-md-4 col-md-offset-8">
                        <a class="btn btn-primary" href="{{ route('orden.realizar',['id' => $orden->id, 'realizado' => '3']) }}" >Volver a Pendiente</a>
                        <a class="btn btn-primary" href="{{ route('orden.realizar',['id' => $orden->id, 'realizado' => '1']) }}" >Verificar</a>
                        <a class="btn btn-primary" href="{{ route('orden.index_control') }}" >Regresar</a>
                        <a class="btn btn-primary" href="{{ route('resultados.imprimir',['id' => $orden->id]) }}">Imprimir</a>
                    </div-->


                   
                    @foreach($agrupador as $value)
                      @php $i_agrupador=0; @endphp 
              
                      @foreach($detalle as $value_detalle)
                        @php

                          $i=0; 
                          if($orden->seguro->tipo == '0' ){
                            $id_agrupador = $value_detalle->examen->id_agrupador;
                          }else{
                            $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();
                            if(!is_null($agrupador_part)){
                              $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                            }
                            
                          }
                        @endphp


                      
                          @if($value_detalle->examen->maquina == $maq &&  $id_agrupador == $value->id )

                            @php
                              
                              
                              if($value_detalle->examen->sexo_n_s=='0'){
                                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                              }else{
                                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                              }
                            @endphp
                            <div class="col-md-12" style="padding: 5px;">
                              @if($i_agrupador == 0)
                                <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;">    
                                  <h2 class="box-title"><b>{{$value->nombre}}</b></h2>
                                  @php $i_agrupador=1; @endphp
                                </div>

                                
                                <div style="width: 30%;float:left;" ><b>NOMBRE.</b></div>
                                <div style="width: 15%;float:left;" ><b>RESULTADO</b></div>
                                <div style="width: 10%;float:left;" ><b>UNIDADES</b></div>
                                <div style="width: 35%;float:left;" ><b>REFERENCIA</b></div>
                                <div style="width: 10%;float:left;" ><b>CERTIFICAR</b></div>
                                

                                <div style="clear:both;"></div>
                              @endif
                              @if($i == 0) 
                                @if($parametro_nuevo->count() > 1) 
                                  @if($value_detalle->id_examen=='639')

                                    <!-- ALEXANDRA AQUI VA EL FORMATO DE LA INTOLERANCIA DE ALIMENTOS-->
                                     
                                      <br>
                                      <b>ALERGIAS A ALIM. POR IGG - 59</b>
                                      <br>
                                      <br>
                                      <div class="col-md-10" style="padding: 0px;">
                                        @php
                                          $cuenta=1;
                                        @endphp  
                                        @foreach($parametro_nuevo as $value_agrupador)
                                          @php 
                                         
                                            
                                    
                                            $rvalor=0; 

                                            $resultado=Sis_medico\Examen_Resultado::where('id_orden',$orden->id)
                                            ->where('id_parametro',$value_agrupador->id)
                                            ->first();

                                            if(!is_null($resultado)){
                                              $rvalor=$resultado->valor;

                                            }
                                          

                                          @endphp
                                            @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                              <div class="col-md-6" style="padding: 0px;">
                                                <div class="col-md-1" style="padding: 0px;"><p style=" font-size: 12px; padding: 0px">{{$value_agrupador->orden}}</p></div>
                                                <div class="col-md-4" style="padding: 0px;"><p style=" font-size: 12px; padding: 0px">{{$value_agrupador->nombre}}</p></div>
                                                 <div class="col-md-1" style="padding: 0px; border: 1px solid #FFF;">&nbsp;
                                                </div>
                                                <div class="col-md-1" style="border: 1px solid #FFF;text-align: center; padding: 0px;">&nbsp;
                                                </div>
                                                <div class="col-md-1" style="border: 1px solid #FFF;text-align: center; padding: 0px;">&nbsp;
                                                </div>
                                                <div class="col-md-1"  style="background-color:#376EAC; padding: 0px ; border: 1px solid #FFF;text-align: center;">@if($value_agrupador->orden=='48')  <span style="color: white; font-size: 15px;">x</span> @else &nbsp; @endif
                                                </div>
                                              </div>
                                            @else
                                              <div class="col-md-6" style="padding: 0px;">
                                                <div class="col-md-1" style="padding-left: 5px;padding-right: 5px;"><p style=" font-size: 12px; padding: 0px;">{{$value_agrupador->orden}}</p></div>
                                                <div class="col-md-4" style="padding: 0px;"><p style=" font-size: 12px; padding: 0px;">{{$value_agrupador->nombre}}</p></div>
                                                <div class="col-md-1" style="padding: 0px; border: 1px solid #FFF;"><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="0">
                                                </div>
                                                <div class="col-md-1" style="background-color:#ACCBEE ; padding: 0px; border: 1px solid #FFF;text-align: center;"><input  type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="1" @if($rvalor==1) checked  @endif>
                                                </div>
                                                <div class="col-md-1" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center; padding: 0px;"><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="2" @if($rvalor==2) checked  @endif >
                                                </div>
                                                <div class="col-md-1"  style="background-color:#376EAC; border: 1px solid #FFF;text-align: center; padding: 0px;" ><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="3" @if($rvalor==3) checked  @endif>
                                                </div>
                                                <div class="col-md-1"  style="border: 1px solid #FFF;text-align: center; padding: 0px;" ><input id="ch{{$value_agrupador->id}}" name="ch{{$value_agrupador->id}}" type="checkbox" class="flat-orange"@if(!is_null($resultado)) enabled @if($resultado->certificado=='1') checked @endif @else disabled @endif>
                                                </div>
                                                
                                                
                                              </div>
                                            @endif
                                          @php
                                            $cuenta++;
                                          @endphp
                                        @endforeach  
                                      </div>
                                      <div class="col-md-2" style="padding: 0px;">
                                        <p>*19 Pescado Blanco Mix: Bacalao y Lenguado.</p>
                                        <p>*20 Pescado de Agua Dulce Mix: Salmón y Trucha.</p>
                                        <p>*22 Marisco Mix: Camarón, Langostino, Cangrejo, Langosta, Mejillones.</p>
                                        <p>*30 Mezclas de pimientos: rojo, verde y amarillo</p>
                                        <p>*31 Leguminosas: Arverjas, lentejas, fréjol, habas.</p>
                                        <p> *33 Melón Mix: Melón y Sandía</p>
                                        <p align="justify"> Si sus resultados indican una reacción elevada al gluten, le recomendamos que evite todos los alimentos que contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta positiva como el trigo, el centeno, la cebada, espelta, kamut, malta, esencia de malta, vinagre de malta, salvado, triticale, dextrina.</p>
                                        <br> <p>Algunas personas con intolerancia al gluten son sensibles también a la avena. </p></br>
                                        <table scope="col" align="right" border="1" cellpadding="0" cellspacing="0" width="100%" >
                                                <tr>
                                                  <td BGCOLOR="ACCBEE" width="15%" border="1"> &nbsp</td>
                                                  <td width="30%">Reacción Leve</td>
                                                </tr>
                                                <tr>
                                                  <td BGCOLOR="8AB0DB"  width="15%" border="1"> &nbsp</td>
                                                  <td width="50%">Reacción Moderada</td>
                                                </tr>
                                                <tr>
                                                  <td BGCOLOR="376EAC"  width="15%" border="1"> &nbsp</td>
                                                  <td width="30%"> Reacción Fuerte</td>
                                                </tr>  
                                          </table>        
                                      </div>   
                                      
                                    <!-- ALEXANDRA AQUI VA EL FORMATO DE LA INTOLERANCIA DE ALIMENTOS-->
                                  @else    
                                    <div style="border-bottom: 1px solid #009a98;padding: 0px;clear:both;">
                                      <b>{{$value_detalle->examen->nombre}}</b>  
                                      @if($value_detalle->examen->tiene_detalle=='1')
                                        <!--b>{{$value_detalle->examen->nombre}}</b-->
                                        <a href="{{route('subresultado.crear',['id_orden' => $orden->id, 'id_examen' => $value_detalle->id_examen])}}" data-toggle="modal" data-target="#edit_crea_sub" class="btn btn-success btn-xs" ><span class="glyphicon glyphicon-plus"></span> Detalle</a>
                                      @endif
                                    </div>
                                  @endif  
                                @elseif($parametro_nuevo->count() == 0) 
                                  <div style="border-bottom: 1px solid #009a98;padding: 0px;clear:both;">
                                    <span style="background-color: red;color: white;"> <b>{{$value_detalle->examen->nombre}} @if($value_detalle->examen->no_resultado=='1') NO SE INGRESA INFORMACION POR SISTEMA @else SIN PARAMETRO @endif</b></span> 
                                  </div>
                                @endif
                                
                                @php $i = 1; @endphp
                              @endif
                              
                              <div style="border-bottom: 1px solid white;padding: 0px;clear:both;"></div>
                              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="table-responsive">
                                  <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;;table-layout: fixed;width: 100%;">
                                    <tbody>
                                      @if($value_detalle->id_examen=='661')
                                        <tr>
                                          <td width="25%"><b>GRADO</b></td>
                                          <td width="15%"><b>% CELULAS</b></td>
                                          <td width="5%"><b>CER</b></td>
                                          <td width="10%"><b>L.A.P. PUNTOS</b></td>
                                          <td width="5%"><b>CER</b></td>
                                          <td width="40%"><b>REFERENCIA</b></td>
                                        </tr>
                                      @endif
                                      
                                      
                                      @if($value_detalle->id_examen=='661')
                                        @foreach($parametro_nuevo as $value_agrupador)
                                          @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                            @php
                                              $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                            @endphp
                                            <tr role="row" >
                                              <td width="25%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->nombre}}</td>
                                              @php 
                                                $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                $resultado_2 = null;
                                                if(!is_null($hermano)){
                                                  $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                                } 
                                              @endphp
                                              <td width="15%" style="padding-top: 2px;padding-bottom: 2px;">
                                                @if(!is_null($hermano))
                                                <a href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $hermano->id]) }}" data-toggle="modal" data-target="#edit_crea" ><span id="{{$hermano->id}}">@if(!is_null($resultado_2)){{$resultado_2->valor}}@else{{"0"}}@endif</span></a>
                                                @endif
                                              </td>
                                              <td width="5%">@if(!is_null($hermano))<input id="ch{{$hermano->id}}" name="ch{{$hermano->id}}" type="checkbox" class="flat-orange"@if(!is_null($resultado_2)) enabled @if($resultado_2->certificado=='1') checked @endif @else disabled @endif @endif</td>
                                              <td width="10%" style="padding-top: 2px;padding-bottom: 2px;">
                                                <a href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" ><span id="{{$value_agrupador->id}}">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</span></a>    
                                              </td>
                                               <td width="5%"><input id="ch{{$value_agrupador->id}}" name="ch{{$value_agrupador->id}}" type="checkbox" class="flat-orange"@if(!is_null($resultado)) enabled @if($resultado->certificado=='1') checked @endif @else disabled @endif</td>
                                              <td width="40%" style="padding-top: 2px;padding-bottom: 2px;"> @if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>    
                                            </tr>
                                          @endif    
                                        @endforeach  
                                      @elseif($value_detalle->id_examen=='639')
                                      @else
                                        @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                          @foreach($parametro_nuevo as $value_agrupador)
                                            @php
                                              $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                            @endphp
                                            <tr role="row" >
                                              @if($parametro_nuevo->count()=='1')
                                              <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" width="30%" style="padding-top: 2px;padding-bottom: 2px;"><b>{{$value_agrupador->nombre}}</b></td>
                                                
                                                @if($value_detalle->examen->tiene_detalle=='1')
                                                  <b>{{$value_detalle->examen->nombre}}</b>
                                                  <a href="{{route('subresultado.crear',['id_orden' => $orden->id, 'id_examen' => $value_detalle->id_examen])}}" data-toggle="modal" data-target="#edit_crea_sub" class="btn btn-success btn-xs" ><span class="glyphicon glyphicon-plus"></span> Detalle</a>
                                                @endif
                                              @else
                                              <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" width="30%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->nombre}}</td>
                                              @endif
                                              <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" width="15%" style="padding-top: 2px;padding-bottom: 2px;"><span id="{{$value_agrupador->id}}">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</span>
                                                  <!--a href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</a-->
                                              </td>
                                              <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" width="10%" style="padding-top: 2px;padding-bottom: 2px;">{{$value_agrupador->unidad1}}</td>
                                              <td class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" width="35%" style="padding-top: 2px;padding-bottom: 2px;"> @if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                              <td width="10%" style="padding-top: 2px;padding-bottom: 2px;"><input id="ch{{$value_agrupador->id}}" name="ch{{$value_agrupador->id}}" type="checkbox" class="flat-orange"@if(!is_null($resultado)) enabled @if($resultado->certificado=='1') checked @endif @else disabled @endif</td>
                                                    
                                            </tr>
                                          @endforeach
                                          @if($value_detalle->examen->tiene_detalle=='1')
                                            @php
                                              $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$value_detalle->id_examen)->get();
                                            @endphp
                                            <tr >
                                              <td colspan="5" id="sub_tabla" style="padding: 0px;" >
                                                <table class="table table-bordered table-hover dataTable" style="width: 100%;">
                                                  <tbody>
                                                    @foreach($sub_resultados as $sub)
                                                      <tr id="sr{{$sub->id}}">
                                                        
                                                        <td style="width: 30% !important;padding: 0px;">{{$sub->campo1}}</td>
                                                        <td style="width: 30% !important;padding: 0px;">{{$sub->campo2}}</td>
                                                        <td style="width: 30% !important;padding: 0px;">{{$sub->campo3}}</td>
                                                        <td style="width: 10% !important;padding: 0px;"><button class="btn btn-sm btn-danger" onclick="elimina_sub('{{$sub->id}}')" href=""><i class="glyphicon glyphicon-trash"></i></button></td>
                                                      </tr>
                                                    @endforeach
                                                  </tbody>
                                                </table>  
                                              </td>
                                            </tr>
                                          @endif
                                        @endif  
                                      @endif  
                                    </tbody>
                                  </table>
                                </div> 
                              </div>
                            </div>
                          @endif   
                      
                      @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script>
$(document).ready(function(){
  $('input[type="radio"].flat-orange2').iCheck({
    checkboxClass: 'flat-orange2',
    radioClass: 'iradio_square-grey',
  }) 
});
</script>
<script type="text/javascript">
  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  }) 
  
  $(".clickable-row").click(function() {
      //console.log(this);
      //$("#edit_crea").modal();
      //window.location = $(this).data("href");
      var url = $(this).data("href");
      //  alert(url); 
      $.get(url,function(data) {
        $(".modal-content").html(data);
        $("#edit_crea").modal();
      });
  });

      
  

  $('#edit_crea').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });

  $('#edit_crea_sub').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
      

  });

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){

    $.ajax({
      type: 'get',
      url:"{{ url('certificar/resultado') }}/{{$orden->id}}/"+this.name.substring(2)+"/1/{{$maq}}", 
      
      success: function(data){
        
        //alert(data);
        //console.log(data);

      },


      error: function(data){
        
         
      }
    });     

  });
  $('input[type="radio"].flat-orange2').on('ifChecked', function(event){

    //console.log(this);
    $.ajax({
      type: 'get',
      url: "{{url('guardar/testdealimentos')}}/"+this.name.substring(2)+"/"+this.value+"/{{$orden->id}}", 
      
      success: function(data){
        
        $('#ch'+data).iCheck('enable');

      },


      error: function(data){
        
         
      }
    });     

  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
 
    $.ajax({
      type: 'get',
      url:"{{ url('certificar/resultado') }}/{{$orden->id}}/"+this.name.substring(2)+"/0/{{$maq}}", 
      
      success: function(data){
        
        //alert(data);

      },


      error: function(data){
        
         
      }
    });     

  });

  $(document).ready(function() {

    var edad = calcularEdad('<?php echo $orden->paciente->fecha_nacimiento; ?>');
    $('#edad').text( edad );

  });

  $( "#btn_val" ).click(function() {

    $.ajax({
      type: 'get',
      url:"{{ route('resultados.puede_imprimir',['id' => $orden->id]) }}", 
      
      success: function(data){
        
        if(data.certificados>0){
         
          window.open('{{ route('resultados.imprimir',['id' => $orden->id]) }}');
          
        }else{
          alert("No tiene exámenes certificados");
        }

      },


      error: function(data){
        
         
      }
    });
   
  });

  function elimina_sub(id){
    var confirmar = confirm("Desea Eliminar Resultado");
    if(confirmar){
      $.ajax({
        type: 'get',
        url:"{{ url('subresultado/eliminar') }}/"+id, 
        
        success: function(data){
          alert(data);
          console.log($('#sr'+id));
          $('#sr'+id).hide();  
          

        },


        error: function(data){
          
           
        }
      });   
    }
  }


  





  

</script>
@endsection
