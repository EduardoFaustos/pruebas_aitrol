<style type="text/css"> 
.parent{
      overflow-y:scroll;
      height: 600px;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #004AC1;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #004AC1;
      box-shadow: inset 0px 0px 0px 3px #56ABE3;
  }
</style>
<!--Div principal-->
<div id="area_index" class="container-fluid" style="padding-left: 8px">
  <!--Fila-->
  <div class="row">
    <div class="col-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px; ">
      <div class="col-12" style="border: 2px solid #004AC1; margin-left: 0px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px; background-color: #56ABE3; ">
            <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
              <img style="width: 35px; margin-left: 15px; margin-right: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/biopsias.png"> 
              <b>RESULTADOS DE BIOPSIAS</b>
              @if(!is_null($paciente))
                 @php
                  $xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age; 
                 @endphp 
                <div class="row"> 
                    <div class="col-md-9" >
                      <h1 style="font-size: 14px; margin:0;color: white;padding-left:20px" >
                        <b>PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
                          {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                          </b>
                      </h1>
                    </div>
                    <div class="col-md-3" >
                      <h1 style="font-size: 14px; margin:0;color: white;padding-left: 10px" >
                        <b>
                          EDAD: {{$xedad}} AÑOS
                        </b>
                      </h1>
                    </div> 
                    <div class="col-md-12" style="padding-top: 10px"></div>
                </div>
              @endif  
            </h1>   
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px"  >
            <div class="parent" style="margin-left: 0px;   margin-bottom: 10px ; height: 450px " >
              <div style=" margin-right: 30px;">
                @foreach($group_biopsias as $value)

                  @php

                    $biop_detalle = Sis_medico\Hc4_Biopsias::where('hc_id_procedimiento',$value->hc_id_procedimiento)
                                                           ->where('id_tipo_usuario',$tipo_usuario)->get();

                    if($value->id_doctor != ""){
                      
                      $xdoctor = Sis_medico\User::where('id', $value->id_doctor )->first();

                    }

                    if($value->hc_id_procedimiento != ""){
                      
                      $procs = Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->get();
                    }

                    if(!is_null($value->created_at)){
                            $fecha_r =  Date('Y-m-d',strtotime($value->created_at));
                    }

                  @endphp

                  <div class="box  @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white;  font-family: Helvetica; margin-bottom: 30px; padding-left: 0px; padding-right: 0px">

                    <div class="box-header " style="padding-left: 0px; padding-right: 0px" >
                          <div class="row" style="background-color: #004AC1; color: white; margin-top: 7px; margin-left: 10px; margin-right: 10px">

                            <div class="col-md-5" style="text-align: center;">
                              <span>
                                @if(!is_null($value->created_at))
                                    @php 
                                    $dia =  Date('N',strtotime($value->created_at)); 
                                    $mes =  Date('n',strtotime($value->created_at)); @endphp
                                      <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title";> 
                                        @if($dia == '1') Lunes 
                                           @elseif($dia == '2') Martes
                                           @elseif($dia == '3') Miércoles 
                                           @elseif($dia == '4') Jueves 
                                           @elseif($dia == '5') Viernes 
                                           @elseif($dia == '6') Sábado 
                                           @elseif($dia == '7') Domingo 
                                        @endif 
                                          {{substr($value->created_at,8,2)}} de 
                                        @if($mes == '1') Enero 
                                           @elseif($mes == '2') Febrero 
                                           @elseif($mes == '3') Marzo 
                                           @elseif($mes == '4') Abril 
                                           @elseif($mes == '5') Mayo 
                                           @elseif($mes == '6') Junio 
                                           @elseif($mes == '7') Julio 
                                           @elseif($mes == '8') Agosto 
                                           @elseif($mes == '9') Septiembre 
                                           @elseif($mes == '10') Octubre 
                                           @elseif($mes == '11') Noviembre 
                                           @elseif($mes == '12') Diciembre 
                                        @endif 
                                          del {{substr($value->created_at,0,4)}}
                                      </span>
                                @endif
                              </span>

                            </div>
                            <div class="col-md-5">
                              <div>
                                <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a): </span> 
                                <span style="font-size: 12px">@if(!is_null($xdoctor->nombre1))  
                                {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}@endif</span>
                              </div>
                            </div>
                            <div class="col-md-1" style="color: blue"> 
                              @if(!is_null($value->hc_id_procedimiento)) 
                                {{$value->hc_id_procedimiento}}
                              @endif
                            </div>
                            <div class="pull-right box-tools " >
                              <button   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                <i class="fa fa-minus"></i>
                              </button>
                            </div>

                          </div>
                          </br>

                          <div class="row" style="padding-left: 10px">
                            <div class="col-md-12">
                                      <span style="font-family: 'Helvetica general'; font-size: 12px">Procedimientos:</span>
                            </div>
                            <div class="col-md-12">
                              @foreach ($procs as $value1)
                                {{$value1->procedimiento->nombre}}
                                </br>
                              @endforeach
                            </div>
                          </div>

                     </div>

                    <div class="box-body">
                      <div class="row" style="padding-left: 6px">
                        @foreach($biop_detalle as $value)

                          @php
                            
                              $biop_resultado = Sis_medico\Biopsias_Result::where('id_hc_biopsia',$value->id)
                                                           ->first();
                          @endphp
                          
                          <div class="col-12" style="padding-top: 15px">

                            

                              <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;padding-top: 8px">
                                  <div class="row">
                                      <div class="col-md-6 col-sm-6 col-7">
                                          <span> <b>Resultado Frasco:{{$value->numero_frasco}}</b></span>
                                      </div>
                                      <div class="col-5" style="text-align: right;padding-top: 6px">
                                        @if(!is_null($biop_resultado))
                                         <a class="btn btn-danger" onclick="descargar_resultado_frasco({{$biop_resultado->id_hc_biopsia}});" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;">
                                        @endif 
                                        <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>Descargar</a>
                                      </div>
                                      <div class="pull-right box-tools">
                                        <button style="margin-top: 8px"   type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                          <i class="fa fa-plus"></i></button>
                                      </div>
                                  </div>
                                 
                                
                                </div>

                                <div class="box-body" style="background: white;">
                                  

                                  <div class="col-12" style="padding-top: 5px"></div>
                                
                                  @if(!is_null($value->observacion))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Observacion Muestra:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{$value->observacion}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif 
                                  @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Registro:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->campo_registro}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif 

                                   @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Obtenido:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->obtenido}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif 
                                  
                                   @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Recibido:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->recibido}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif

                                   @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Reportado:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->reportado}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Datos de Orientación Diagnóstica:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->Ori_diagnostica}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif  
                                  @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Macroscopia:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->macroscopia}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Microscopia:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->microscopia}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  <div class="col-12" style="padding-top: 5px"></div>
                                  @if(!is_null($biop_resultado))
                                  <div class="col-md-12" style="padding: 1px;">
                                    <div class="row"> 
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Imagen #1:</span>
                                      </div>
                                      <div class="col-md-12" style="padding-left: 20px;text-align: center">                    
                                          <img src="{{asset('../storage/app/biopsias').'/'.$biop_resultado->img1}}" style="width:300px;height:300px;"  alt="" > 
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  <div class="col-12" style="padding-top: 5px"></div>
                                  @if(!is_null($biop_resultado))
                                  <div class="col-md-12" style="padding: 1px;">
                                    <div class="row"> 
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Imagen #2:</span>
                                      </div>
                                      <div class="col-md-12" style="padding-left: 20px;text-align: center">                    
                                          <img src="{{asset('../storage/app/biopsias').'/'.$biop_resultado->img2}}" style="width:300px;height:300px;"  alt="" > 
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  <div class="col-12" style="padding-top: 5px"></div>
                                  @if(!is_null($biop_resultado))
                                   <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Diagnóstico:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->diagnostico}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                  <div class="col-12" style="padding-top: 5px"></div>
                                  @if(!is_null($biop_resultado))
                                  <div class="col-md-12" style="padding: 1px;">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">Observación:</span>
                                      </div>
                                      <div class="col-12">
                                        <span>{{ $biop_resultado->observacion}}</span>
                                      </div>
                                    </div>
                                  </div>
                                  @endif


                                </div>

                              </div>
                             
                          
                          </div>

                        @endforeach

                       
                      </div>

                    </div>
                  </div>

                @endforeach
              </div>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  function descargar_resultado_frasco(id_result){
    window.open('{{url('imprimir/resultado/biopsia/frasco')}}/'+id_result,'_blank');  
  } 

</script>
