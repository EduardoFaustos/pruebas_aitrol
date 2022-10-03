
<!DOCTYPE html>
<html>
	<head>
	  
	    <title>CERTIFICADO {{$agenda->id_paciente}}</title>

	  	<style>	
	  		@page { margin: 20px 20px;}
		    #header { position: fixed; left: 20px; top: -10px; right: 20px; height: 150px; text-align: center;}
		    #content { position: fixed; left: 20px; top: 200px; right: 20px; height: 150px; }
		    #footer { position: fixed; left: 20px; bottom: -90px; right: 20px; height: 150px; }
		    #footer1 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 150px; }
		    p {
    			font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 16px;
    			text-align:justify;
    			font-style: italic;
			}
			.dg > p {
				font-family: Comic Sans MS, cursive, sans-serif;
    			font-size: 12px;
    			text-align:justify;
    			font-style: italic;
			}
			
		</style>
	</head>
	<body >	  
		<div id="header">
			
		  <div style="float: left;width: 10%;text-align: left;">
		        <img style="width:200px; height: auto;" src="{{base_path().'/public/imagenes/logo_omni2.png'}}">
		      </div>
		      <div  style="text-align: right;">
		          <img style="margin: 0;width: 180px;">
		          <p style="text-align: right;font-size: 20px;"><font face="Comic Sans MS,arial,verdana"> Dr. Eduardo Montanero Soledispa</font><br> Traumatólogo - Ortopedista</br></td>
                </div>
                <br>

			<!--p align="left">Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</p-->
			<p align="left">Guayaquil, {{substr($cfecha, 8, 2)}}  de <?php $mes = substr($cfecha, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($cfecha, 0, 4)}}</p>
			
			<h4 align="center" >CERTIFICADO MÉDICO</h4>
		</div>

		<div id="content" > 	
			<p >A quien interese:</p>
			<p>El que suscribe, {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)') {{$doctor->apellido2}} @endif {{$doctor->nombre1}}, @if($doctor->id_tipo_usuario=='3') médico @endif {{$especialidad->nombre}} en ejercicio legal de su profesión certifica:</p>
			<p>Por medio del presente certifico haber atendido profesionalmente a {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} con cédula {{$paciente->id}} quien se acerca a nuestra institución para realizar <b>{{$tipo}}</b>, el día {{substr($cfecha, 8, 2)}}  de <?php $mes = substr($cfecha, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($cfecha, 0, 4)}} @if($desde!=null && $hasta!=null) , desde: {{$desde}} hasta: {{$hasta}} @endif .</p>
			@if($descanso>0)
				<p>Dándosele descanso médico {{$descanso}}({{$letras}}) días desde el día {{substr($cfecha, 8, 2)}}  de <?php $mes = substr($cfecha, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($cfecha, 0, 4)}} hasta el día {{substr($fecha_hasta, 8, 2)}}  de <?php $mes = substr($fecha_hasta, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($fecha_hasta, 0, 4)}} para su total recuperación.</p>
			@endif
			<p>Certificado que extiendo profesionalmente a {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} @if($familiar!=null) , acompañado por {{$familiar}} @endif y puede hacer uso del mismo como considere necesario.</p>
			@if($diagnostico!=null)
			
			<div class="dg" >
				<span ><b>Diagnóstico CIE10:</b></span>
				<?php echo $diagnostico; ?>
			</div>
			@endif
			<p>Atentamente,</p>
			<p style="text-align: left;">
				@if(!is_null($firma))
					<img width=170 height=80 src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
					<span style="font-size: 12px;">@if($doctor->id_tipo_usuario=='3') Dr(a). @else Lcda. @endif {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)') {{$doctor->apellido2}} @endif {{$doctor->nombre1}}</span><br>
				@else
					<br><br><br><br><br>
					<b>@if($doctor->id_tipo_usuario=='3') Dr(a). @else Lcda. @endif {{$doctor->apellido1}} @if($doctor->apellido2!='(N/A)') {{$doctor->apellido2}} @endif {{$doctor->nombre1}}</b><br>
				@endif
			</p>
		</div>
	          
	            
                
    <div id="footer1" style="text-align: center">
      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><img style="width:750px; height: 6px" src="{{base_path().'/public/imagenes/lineas_recetas2.png'}}"></span>
      </p>
      <p class=MsoNormal style='text-align:center; line-height:normal'><font size= "1";face="Comic Sans MS,arial,verdana">Av. Abel Romeo Castillo y Av. Tanca Marengo, 3er. Piso. Consultorio 314 * <b>Teléfono:</b> 2109194 - 6012377 <b>Cel.:</b> 0999266750<br><b>Email:</b> emontaneros@hotmail.com . consultorioemontanero@gmail.com * <b>Web:</b> www.omnihospital.com * Guayaquil - Ecuador</font>
      </p>
    </div>
	</body>
</html>		