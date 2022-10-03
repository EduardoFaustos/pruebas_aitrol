<!DOCTYPE html>
<html>
	<head>
	  
	    <title>  <!-- CERTIFICADO {{$agenda->id_paciente}} --> </title>

	  	<style>	
	  		@page { margin: 20px 20px;}
		    #header { position: fixed; left: 20px; top: 30px; right: 20px; height: 150px; text-align: center;}
		    #content { position: fixed; left: 30px; top: 250px; right: 35px; height: 500px; }
		    #footer { position: fixed; left: 10px; bottom: -120px; right: 300px; height: 220px; }
		    p {
    			font-family:  sans-serif;
    			font-size: 18px;
    			text-align:justify;
    			font-style: italic;
			}
			.dg > p {
				font-family: sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
			}
      td{
        font-family: sans-serif;
          font-size: 18px;
          text-align:justify;
          font-style: italic;  
      }
			
		</style>
	</head>
	<body >	 
   
		<div id="header">
  		<!--	
      @php
      
        $dia = substr($agenda->fechaini, 8, 2);
        $mes = substr($agenda->fechaini, 5, 2);
        $anio = substr($agenda->fechaini, 0, 4);

        $txtmes='';
        if($mes == 01){ $txtmes = "Enero";} if($mes == 02){ $txtmes = "Febrero";} if($mes == 03){ $txtmes = "Marzo";} if($mes == 04){ $txtmes = "Abril";} if($mes == 05){ $txtmes = "Mayo";} if($mes == 06){ $txtmes = "Junio";} if($mes == 07){ $txtmes = "Julio";} if($mes == '08'){ $txtmes = "Agosto";}  if($mes == '09'){ $txtmes = "Septiembre";} if($mes == '10'){ $txtmes = "Octubre";} if($mes == '11'){ $txtmes = "Noviembre";} if($mes == '12'){ $txtmes = "Diciembre";}

      @endphp
        -->
  			<img src="{{base_path().'/public/imagenes/ae_header.png'}}" align=center hspace=12></p>
  			<h4 align="center" style="font-size: 25px;">Acta de Entrega Informe Médico</h4>
        <p>Guayaquil, <b></b> de <b></b> del <b></b> </p> 

		</div>

    

		<div id="content" > 
          
            <div style="width:100%; float:center;">
              @php
                $cuenta=1;
              @endphp  
              @foreach($parametro_nuevo as $value_agrupador)
                @php 
               
                  $rvalor=0; 

                  $resultado=Sis_medico\Examen_resultado::where('id_orden',$orden->id)
                  ->where('id_parametro',$value_agrupador->id)
                  ->first();

                  if(!is_null($resultado)){
                    $rvalor=$resultado->valor;

                  }
                

                @endphp

                  @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')

                    <div style="width:35%; float:left;">
                      <div style="width:10%; float:left;"><p style=" font-size: 12px;">{{$value_agrupador->orden}}</p></div>
                      
                      <div style="width:40%; float:left;"><p style=" font-size: 12px;">{{$value_agrupador->nombre}}</p></div>

                      <div style="width:20%; float:left; background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;">
                      </div>
                     
                      <div style="width:20%; float:left; background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center;">
                      </div>
                      

                      <div style="width:20%; float:left; background-color:#376EAC; border: 1px solid #FFF;text-align: center;" ><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="3" @if($rvalor==3) checked  @endif readonly>
                      </div>

                     <!-- <div class="col-md-2"></div> -->

                    </div>

                  @else

                    <div >
                      <div style="width:10%; float:center;><p style=" font-size: 12px;">{{$value_agrupador->orden}}</p> </div>
                      
                      <div style="width:40%; float:center;><p style=" font-size: 12px;">{{$value_agrupador->nombre}}</p></div>

                      <div style="width:20%; float:center; background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;"><input  type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="1" @if($rvalor==1) checked  @endif readonly>
                      </div>
                     
                      <div style="width:20%; float:center; background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center;"><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="2" @if($rvalor==2) checked  @endif readonly>
                      </div>
                      
                      <div style="width:20%; float:center; background-color:#376EAC; border: 1px solid #FFF;text-align: center;" ><input type="radio" name="op{{$value_agrupador->id}}" class="flat-orange2" value="3" @if($rvalor==3) checked  @endif readonly>
                      </div>

                      <!-- <div class="col-md-2"></div> -->

                    </div>

                  @endif
                @php
                  $cuenta++;
                @endphp
              @endforeach  
            </div>

            <div style="width:35%; float:center;">
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
        
    </div>


		<div id="footer">
		  <img src="{{base_path().'/public/imagenes/ae_footer.png'}}" align=center hspace=12></p> 	
		</div>

	</body>
</html>		