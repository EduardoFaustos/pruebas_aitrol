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
       <img src="{{base_path().'/public/imagenes/labs_res.jpg'}}" align=center>
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
              <td width="40" style="color: black;">@if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif</td>
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
              <td style="background-color: #e5f5f5;height: 20px;"><b>ANÁLISIS</b></td>
              <td>&nbsp;</td>
              <td style="background-color: #e5f5f5;"><b>FECHA</b></td>
              <td style="color: black;">{{substr($orden->created_at,0,10)}}</td>
              <td style="background-color: #e5f5f5;"><b>HORA</b></td>
              <td style="color: black;">{{substr($orden->created_at,10,10)}}</td>         
              <td style="background-color: #e5f5f5;"><b>PÁGINA</b></td>
              <td>&nbsp;</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
          </tbody>
        </table>
      </div>  
              
    </div> 

    <div id="footer">
      <img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs.png'}}" align=center hspace=12>
      <div align="right">
        <p style="font-size: 12px;">{{substr($ucreador->nombre1,0,1)}}{{$ucreador->apellido1}}</p>
      </div>
    </div> 
  
  
     <div id="content"> 
      
      @foreach($agrupador as $value)
        @php  $i = 0;@endphp
            @foreach($detalle as $validador_2)
                @if($validador_2->examen->id_agrupador == $value->id)
                    @if($i == 0)
                    <div class="col-md-4">
                      <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;width: 1122px;">
                        <b></b>
                        @php $indicador=0; @endphp  
                      </div>    
                        
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                               
                          @foreach($detalle as $value_detalle)
                            @if($value_detalle->examen->id_agrupador == $value->id)
                            <div class="table-responsive">
                              @if($value_detalle->examen->id_agrupador != 2)
                              <div style="border-bottom: 1px solid black;padding: 0px;width: 1122px;">
                                <b style="font-size: 20px;"></b>
                                
                              </div>
                              @endif
                              
                              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 18px;table-layout: fixed;width: 1300px;">
                                
                                @if($indicador==0)
                                <thead>
                                  <tr style="font-size: 19px;">  
                                    <th >NOMBRE</th>
                                    <th >RESULTADO</th>
                                    <th >UNIDADES</th>
                                    <th >REFERENCIA</th>
                                  </tr>  
                                </thead>
                                @php $indicador=1; @endphp
                                @endif
                                 
                                <tbody>
                                  @php
                                    
                                   if($value_detalle->examen->sexo_n_s=='0'){
                                      $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                                    }else{
                                      $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                    }

                                  @endphp
                                  @foreach($parametro_nuevo as $value_agrupador)
                                    @php
                                    $resultado = $resultados->where('id_orden', $value_detalle->id_examen_orden)->where('id_parametro', $value_agrupador->id)->first();
                                    @endphp

                                    @if($orden->created_at > Date('Y-m-d'))
                                      <tr role="row" class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >
                                        <td>{{$value_agrupador->nombre}}</td>
                                        <td style="">
                                          <div style="word-wrap: break-word;">
                                            @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                          </div>  
                                        </td>
                                        <td>{{$value_agrupador->unidad1}}</td>
                                        <td style="font-size: 14px;"> menor a .10  Ausencia o Indetectable <br> 0.10 - 0.34 Muy Bajo<br> I 0.35 - 0.69 Bajo<br> II 0.70 - 3.49 Moderado<br> III 3.50 - 17.49 Alto<br> IV 17.5 - 52.49 Muy Alto<br> V 52.5 - 99.99 Muy Alto <br> VI >= 100 Muy Alto
                                        </td>
                                              
                                      </tr>
                                    @else 
                                      @if(!is_null($resultado))
                                      <tr role="row" class="clickable-row" data-href="{{ route('resultados.crea_actualiza', ['id_orden' => $value_detalle->id_examen_orden,'id_parametro' => $value_agrupador->id]) }}" data-toggle="modal" data-target="#edit_crea" >
                                        <td>{{$value_agrupador->nombre}}</td>
                                        <td style="">
                                          <div style="word-wrap: break-word;">
                                            @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                          </div>  
                                        </td>
                                        <td>{{$value_agrupador->unidad1}}</td>
                                        <td>{{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}</td>
                                              
                                      </tr>
                                      @endif 
                                    @endif  
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                               
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
          
      
        
    
  

  

</body>
</html>  

  
