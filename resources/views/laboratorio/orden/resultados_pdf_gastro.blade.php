<!DOCTYPE html>
<html>
<head>
  <style>
    @page { margin: 200px 70px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -220px; right: 0px; height: 150px; }

  </style>    

</head>
<body>
  
  <script type="text/php">
        if (isset($pdf)) {
          $font = $fontMetrics->getFont("Arial", "bold");
          $pdf->page_text(555, 80, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
  </script>
  
 
    <div id="header">
        
      <div class="col-md-12" style="width: 1122px;border-bottom: 0.5px solid #009a98;border-top: 0.5px solid #ec6c25;">
       <img src="{{base_path().'/public/imagenes/labs_res_gas.jpg'}}" align=center>
      </div> 

      <div style="border-bottom: 1px solid #009a98;padding: 0px;width: 1122px;">
        <table style="font-size: 16px;color: #009a98;">
          <tbody>
            <tr role="row">
              <td width="50" style="background-color: #e5f5f5;height: 20px;"><b>PACIENTE</b></td>
              <td width="206" style="color: black;">{{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</td>
              <td width="50" style="background-color: #e5f5f5;"><b>CEDULA</b></td>
              <td width="40" style="color: black;">{{$orden->id_paciente}}</td>
              <td width="45" style="background-color: #e5f5f5;"><b>EDAD</b></td>
              <td width="40" style="color: black;">{{$age}}</td>         
              <td width="50" style="background-color: #e5f5f5;"><b>SEXO</b></td>
              <td width="40" >@if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
            <tr role="row" style="background-color: #e5f5f5;">
              <td width="50" ></td>
              <td width="200"></td>
              <td width="50" ></td>
              <td width="40"></td>
              <td width="50" ></td>
              <td width="40"></td>         
              <td width="50" ></td>
              <td width="40"></td>
              <td width="50" ></td>
            </tr>  
            <tr role="row">
              <td style="background-color: #e5f5f5;height: 20px;"><b>AN??LISIS</b></td>
              <td>&nbsp;</td>
              <td style="background-color: #e5f5f5;"><b>FECHA</b></td>
              <td style="color: black;">@if($orden->seguro->tipo == '0')@if($orden->fecha_convenios == null){{substr($orden->fecha_orden,0,10)}}@else{{substr($orden->fecha_convenios,0,10)}}@endif @else {{substr($orden->fecha_orden,0,10)}} @endif</td>
              <td style="background-color: #e5f5f5;"><b>HORA</b></td>
              <td style="color: black;">{{substr($orden->fecha_orden,10,10)}}</td>         
              <td style="background-color: #e5f5f5;"><b>P??GINA</b></td>
              <td>&nbsp;</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
          </tbody>
        </table>
      </div>  
              
    </div> 

    <div id="footer">
      <!--img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs.png'}}" align=center hspace=12-->
      <div align="right">
        <p style="font-size: 12px;">{{substr($ucreador->nombre1,0,1)}}{{$ucreador->apellido1}}</p>
      </div>
    </div> 
  
  
     <div id="content"> 
      
      @foreach($agrupador as $value)
        @php  $i = 0;$xvhfecha = null;@endphp
            @foreach($detalle as $validador_2) 

                @if($validador_2->examen->id_agrupador == $value->id)
                    @if($i == 0)
                    <div class="col-md-4">
                      @if($validador_2->id_examen != 639 )
                      <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;width: 1122px;">
                        <b>{{$value->nombre}}</b>  
                      </div>
                      @endif 
                      @php $indicador=0; @endphp   
                        
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                               
                          @foreach($detalle as $value_detalle)
                            @if($value_detalle->examen->id_agrupador == $value->id)
                            @if($validador_2->id_examen != 639 )
                            <div class="table-responsive">
                              @if($value_detalle->examen->id_agrupador != 2 && $value_detalle->examen->id_agrupador != 8)
                              @if($value_detalle->id_examen!='639')
                              <div style="border-bottom: 1px solid black;padding: 0px;width: 1122px;">
                                <b style="font-size: 20px;">{{$value_detalle->examen->nombre}}</b>
                                 @if($xvhfecha!=null)Fecha Muestra: {{$xvhfecha}} @endif
                              </div>
                              @endif
                              @endif


                               

                              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 18px;table-layout: fixed;width: 1300px;">
                                
                                @if($indicador==0)
                                <thead>
                                  <tr style="font-size: 19px;">  
                                    <th style="width: 30%;">NOMBRE</th>
                                    <th style="width: 20%;">RESULTADO</th>
                                    <th style="width: 10%;">UNIDADES</th>
                                    <th style="width: 40%;">REFERENCIA</th>
                                  </tr>  
                                </thead>
                                @php $indicador=1; @endphp
                                @endif
                                 
                                <tbody>
                                  @php
                                    $xvhfecha = null;
                                   if($value_detalle->examen->sexo_n_s=='0'){
                                      $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                                    }else{
                                      $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                    }

                                  @endphp
                                  @if(substr($value_detalle->examen->nombre,0,12) == 'TEST ALIENTO' || substr($value_detalle->examen->nombre,0,15) == 'ALERGIAS A ALIM')
                                    @foreach($parametro_nuevo as $xvhpar)
                                      @php
                                        $resultado = $resultados->where('id_parametro', $xvhpar->id)->first();
                                      @endphp
                                      @if(!is_null($resultado))
                                          @php
                                            $xvhfecha = $resultado->created_at;
                                            
                                            break;

                                          @endphp  
                                      @endif
                                    @endforeach
                                    @php  @endphp
                                  @endif
                                   @if($value_detalle->id_examen=='661')
                                    <tr style="font-size: 19px;">
                                        <td style="width: 30%;"><b>GRADO</b></td>
                                        <td style="width: 20%;"><b>% CELULAS</b></td>
                                        <td style="width: 10%;"><b>L.A.P. PUNTOS</b></td>
                                        <td style="width: 40%;"><b>REFERENCIA</b></td>
                                    </tr>
                                  @endif
                                  @if($value_detalle->id_examen=='661')
                                    @foreach($parametro_nuevo as $value_agrupador)
                                      @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                        @php
                                        $resultado = $resultados->where('id_orden', $value_detalle->id_examen_orden)->where('id_parametro', $value_agrupador->id)->first();
                                        @endphp

                                        @if($orden->created_at > Date('Y-m-d'))
                                          <tr role="row" class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >
                                            <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                            @php 
                                              $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                              $resultado_2 = null;
                                              if(!is_null($hermano)){
                                                $resultado_2 = $resultados->where('id_orden', $value_detalle->id_examen_orden)->where('id_parametro', $hermano->id)->first();
                                              } 
                                              
                                            @endphp
                                            <td style="width: 20%;">
                                              <div style="word-wrap: break-word;">
                                                @if(!is_null($hermano))
                                                  @if(!is_null($resultado_2)){{$resultado_2->valor}}@else{{"0"}}@endif
                                                @endif
                                              </div>  
                                            </td>
                                            <td style="width: 10%;">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</td>
                                            <td style="width: 40%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                                  
                                          </tr>
                                        @else 
                                          @if(!is_null($resultado))
                                          <tr role="row" class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >
                                            <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                            @php 
                                              $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                              $resultado_2 = null;
                                              if(!is_null($hermano)){
                                                $resultado_2 = $resultados->where('id_orden', $value_detalle->id_examen_orden)->where('id_parametro', $hermano->id)->first();
                                              } 
                                              
                                            @endphp
                                            <td style="width: 20%;">
                                              <div style="word-wrap: break-word;">
                                                @if(!is_null($hermano))
                                                  @if(!is_null($resultado_2)){{$resultado_2->valor}}@else{{"0"}}@endif
                                                @endif
                                              </div>  
                                            </td>
                                            <td style="width: 10%;">@if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif</td>
                                            <td style="width: 40%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                                  
                                          </tr>
                                          @endif 
                                        @endif
                                      @endif    
                                    @endforeach
                                    @elseif($value_detalle->id_examen=='639')
                                  @else
                                    @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291')) 
                                      @foreach($parametro_nuevo as $value_agrupador)
                                        @php
                                        $resultado = $resultados->where('id_orden', $value_detalle->id_examen_orden)->where('id_parametro', $value_agrupador->id)->first();
                                        @endphp

                                        @if($orden->created_at > Date('Y-m-d'))
                                          <tr role="row" class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >
                                            <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                            <td style="width: 20%;">
                                              <!--div style="word-wrap: break-word;"-->
                                                @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                              <!--/div-->  
                                            </td>
                                            <td style="width: 10%;">{{$value_agrupador->unidad1}}</td>
                                            <td style="width: 40%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>
                                                  
                                          </tr>
                                        @else 
                                          @if(!is_null($resultado))
                                          <tr role="row" class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >
                                            <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                            <td style="width: 20%;">
                                              <div style="word-wrap: break-word;">
                                                @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                              </div>  
                                            </td>
                                            @php
                                              $fl_ref = 0;
                                              $ref_ant = Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                              if(!is_null($ref_ant)){
                                                $fl_ref = 1;
                                              }
                                            @endphp
                                            <td style="width: 10%;">@if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}} @endif</td>
                                            <td style="width: 40%;">
                                            @if($fl_ref)
                                              @if($ref_ant->texto_referencia == "")
                                               {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}  
                                              @else
                                                <?php echo $ref_ant->texto_referencia; ?> 
                                              @endif 
                                            @else 
                                              @if($value_agrupador->texto_referencia == "")
                                               {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}} 
                                              @else
                                                <?php echo $value_agrupador->texto_referencia; ?> 
                                              @endif
                                            @endif
                                            </td>
                                                  
                                          </tr>
                                          @endif 
                                        @endif  
                                      @endforeach
                                      @if($value_detalle->examen->tiene_detalle=='1')
                                        @php
                                          $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('id_examen',$value_detalle->id_examen)->get();
                                        @endphp
                                        <tr >
                                          <td colspan="4" id="sub_tabla">
                                            <table class="table table-bordered table-hover dataTable">
                                              <tbody>
                                                @foreach($sub_resultados as $sub)
                                                <tr>
                                                  <td style="width: 300px !important;">{{$sub->campo1}}</td>
                                                  <td style="width: 300px !important;">{{$sub->campo2}}</td>
                                                  <td style="width: 300px !important;">{{$sub->campo3}}</td>
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
                            @endif
                            @if($value_detalle->id_examen=='639')
                              @if($detalle->count() > 1)
                              <div style="page-break-after:always;"></div>
                              @endif
                              <section style="float:left;width: 69%;">
                                <br>
                                <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px; width: 1122px;">
                                            <b>ALERGIAS A ALIM. POR IGG - 59 </b> @if($xvhfecha!=null)Fecha Muestra: {{$xvhfecha}} @endif
                                </div> 
                                <br>
                                
                                 @php $cuenta=0;

                                  $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                                 @endphp 

                                  <table style="width: 98%;">
                                    @foreach($parametro_nuevo as $value_agrupador)
                                      @php 
                                        $rvalor=0; 
                                        $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();
                                        if(!is_null($resultado)){
                                          $rvalor=$resultado->valor;
                                        }
                                      @endphp
                                      @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                        @if($cuenta == 0) <tr> @endif  
                                          <td  style=" font-size: 15px; padding: 12px;">{{$value_agrupador->sec}}</td>
                                          <td style=" font-size: 15px;">{{$value_agrupador->nombre}}</td>
                                          <td style="border: 1px solid #FFF;text-align: center;" ></td>
                                          <td style="border: 1px solid #FFF;text-align: center;" ></td>
                                          <td style="background-color:#A8A8A8 ; border: 1px solid #FFF;text-align: center; font-size: 18px;"> @if($value_agrupador->orden=='48')  <span style="color: white">x</span> @else &nbsp; @endif</td>
                                          @php $cuenta++; @endphp
                                          @if($cuenta == 2)
                                          </tr>

                                        @endif  
                                      @else 
                                        @if($cuenta == 0) <tr> @endif  
                                        <td  style=" font-size: 15px;  padding: 12px;width: 5px;">{{$value_agrupador->sec}}</td>
                                        <td style=" font-size: 15px; width: 60px;">{{$value_agrupador->nombre}}</td>
                                        <td style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 25px;" >@if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif</td>
                                        <td style="background-color:#8AB0DB ; border: 1px solid #FFF;text-align: center; font-size: 18px; width: 25px;" >@if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif</td>
                                        <td style="background-color:#376EAC ; border: 1px solid #FFF;text-align: center;font-size: 18px; width: 25px;"> @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @endif </span> @else &nbsp; @endif</td>
                                        @php $cuenta++; @endphp
                                        @if($cuenta == 2)
                                        </tr> 
                                         @php
                                          $cuenta=0;
                                         @endphp
                                        @endif 
                                      @endif
                                    @endforeach
                                </table>  
                              </section>
                              <section style="float:right;width: 30%;">
                                <table>
                                  <tr>
                                    <td>
                                      <br>
                                      <br>
                                      <p style=" font-size: 20px;">*19 Pescado Blanco Mix: Bacalao y Lenguado.</p>
                                      <p style=" font-size: 20px;">*20 Pescado de Agua Dulce Mix: Salm??n y Trucha.</p>
                                      <p style=" font-size: 20px;">*22 Marisco Mix: Camar??n, Langostino, Cangrejo, Langosta, Mejillones.</p>
                                      <p style=" font-size: 20px;">*30 Mezclas de pimientos: rojo, verde y amarillo</p>
                                      <p style=" font-size: 20px;">*31 Leguminosas: Arverjas, lentejas, fr??jol, habas.</p>
                                      <p style=" font-size: 20px;">*33 Mel??n Mix: Mel??n y Sand??a</p>
                                      <p align="justify" style=" font-size: 20px; "> Si sus resultados indican una reacci??n elevada al gluten, le recomendamos que evite todos los alimentos que contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta positiva como el trigo, el centeno, la cebada, espelta, kamut, malta, esencia de malta, vinagre de malta, salvado, triticale, dextrina.</p>
                                      <p align="justify" style=" font-size: 20px;">Algunas personas con intolerancia al gluten son sensibles tambi??n a la avena. </p>
                                    </td>
                                  </tr>
                                  <tr>
                                    <table align="right"  cellpadding="0" cellspacing="0" width="100%" >
                                    <tr>
                                      <td BGCOLOR="ACCBEE" width="15%" border="1" > &nbsp;</td>
                                      <td width="30%" style=" font-size: 20px;">Reacci??n Leve</td>
                                    </tr>
                                    <tr>
                                      <td BGCOLOR="8AB0DB"  width="15%" border="1"> &nbsp;</td>
                                      <td width="50%" style=" font-size: 20px;">Reacci??n Moderada</td>
                                    </tr>
                                    <tr>
                                      <td BGCOLOR="376EAC"  width="15%" border="1"> &nbsp;</td>
                                      <td width="30%" style=" font-size: 20px;"> Reacci??n Fuerte</td>
                                    </tr>  
                                    </table>      
                                  </tr>
                                </table>
                              </section> 
                            @endif     
                            @endif
                        @endforeach
                        </div>
                    </div>
                         @php 
                            $i = 1;
                        @endphp
                @endif
                @endif
            @endforeach
        @endforeach

       
    </div>  




      </div>  

</body>
</html>  

  
