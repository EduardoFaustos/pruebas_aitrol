<!DOCTYPE html>
<html>
<head>

	<title>Resumen Procedimiento</title>
	<style type="text/css">
	@page { margin: 110px 60px; }
    #header { position: fixed; left: 0px; top: -100px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -315px; right: 0px; height: 350px; font-size: 10px !important;}
			body{
				margin: 0;
				padding: 0;
			}
		  #encabezado td{
		  	border-style: solid;
		  	border-width: thin;
		  	margin: 0;
		  	padding: 0px 10px; 
		  }
		  #contenido{
		  	margin-bottom: 10px;
		  }
		  .final td{
		  	border-style: solid;
		  	border-width: thin;
		  	margin: 0;
		  	padding: 2px 10px;
		  }
		  table{
		  	border-spacing: 0px;
		  	margin: 0;
		  	padding: 0;
		  	margin-left: -15px;
		  }
		  div
		  {
		  	margin: 0;
		  	padding: 0;
		  }
	</style>
</head>
<body>
	<div id="header">
		<table>
			<tr>
				@if($historia->id_seguro != 5)
				<td style="width: 150px;"><img style="margin: 0;width: 180px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}"	></td>
				@endif
				<td colspan="2">
					<p  style="font-size: 19px; text-align: center; width: 350px;@if($historia->id_seguro != 5) margin-left:50px; @else margin-left:180px; @endif"><b style="text-align: center;">INSTITUTO ECUATORIANO <b><br><b>DE ENFERMEDADES DIGESTIVAS</b></p>
				</td>
			</tr>
		</table>
	</div>
	<div id="contenido">
		<table id="encabezado">
			<tr>
				<td style="width: 80px;"><b>Paciente:</b></td>
				<td style="width: 250px;"><b style="font-size: 11px;">{{ $paciente->apellido1}} @if($paciente->apellido2 != '(N/A)'){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != '(N/A)' ){{ $paciente->nombre2}}@endif</b></td>
				<td style="width: 25px;"><b>Edad:</b></td>
				<td style="width: 40px; font-size: 12px;" >{{$edad}} años</td>
				<td style="width: 70px;"><b>Identificacion:</b></td>
				<td><b style="font-size: 12px;">{{$paciente->id}}</b></td>
			</tr>
			<tr>
				<td><b>Procedimiento:</b></td>
				<td colspan="5"><b style="font-size: 12px;">@if($procedimiento_completo != null){{$procedimiento_completo->nombre_completo}}@endif</b></td>
			</tr>
			<tr>
				<td><b>Referido por:</b></td>
				<td style="font-size: 12px;">@if($firma != "[]")  @if($protocolo->procedimiento->doctor_firma->id != '0918053802')  Dr. Carlos Robles Medranda  @endif
				@else Dr. Carlos Robles Medranda @endif</td>
				<td colspan="2"><b>Fecha:</b></td>
				<td colspan="2" style="font-size: 12px;">@if($protocolo->fecha == null){{date('d/m/Y', strtotime($historia->fecha_atencion))}}@else{{date('d/m/Y', strtotime($protocolo->fecha))}}@endif</td>
			</tr>
		</table>
		<br>
		<table style="margin-left: -23px;padding: 0;table-layout:fixed; width:700px;" >

			@php  $i = 0;   $j = 0;@endphp
			@foreach($imagenes as $value)
				@if($i < 8)
					@if($j == 0)
					<tr style="padding: 0; margin:0;">
					@endif
						@if($value->seleccionado_recortada == 1)
						<td style="margin: 0;padding: 0 0px 0px 0px; width: 362px;height: 270px;">
							<div style="position:relative;overflow: hidden; width: 362px; height: 270px;margin: 0; padding: 0;  ">
								<img style="margin: 0;width: 530px; height: 270px;" src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
							</div> 
						</td>
						@else
						<td style="margin: 0;padding: 0 0px 0px 0px;width: 362px; height: 270px;">
							<div style="position:relative;overflow: hidden; width: 362px; height: 270px; margin: 0; padding: 0;  ">
								<img style="margin: 0;width: 362px; height: 270px;position: absolute; " src="{{base_path().'/storage/app/hc_ima/'.$value->nombre}}" hspace=12>
							</div>							
						</td>
						@endif
					@if($j >= 1)
					</tr>
					@php $j=0-1; @endphp
					@endif
					@php $i = $i+1;$j = $j+1; @endphp
				@endif
			@endforeach
			
				
			</tr>
		</table>
		<hr>
		<!--table width="716px;" class="final">
			<tr>
				<td >Hallazgos</td>
			</tr>
			<tr>
				<td style="font-size: 12px !important;"><?php echo $protocolo->hallazgos; ?></td>
			</tr>
		</table-->
		<div style="width: 107%;margin-left: -17px;border: 1px black solid;">
			<span>Hallazgos</span>
		</div>
		<div style="width: 107%;margin-left: -17px;border: 1px black solid;font-size: 10px !important;line-height: 1.2 !important;">
			<span style='font-size:10px !important;line-height: 1.2 !important;margin:0;'><?php echo $protocolo->hallazgos; ?></span>
		</div>
		<hr>
		<table width="716px;" class="final">
			<tr>
				<td >Conclusiones </td>
			</tr>
			<tr>
				<td style="font-size: 10px !important;font-family: 'Source Sans Pro',sans-serif;line-height: 1.2 !important;" >
					<span style='font-size:10px !important;line-height: 1.2 !important;margin:0;'><?php echo $protocolo->conclusion; ?></span>
				</td>
			</tr>
		</table>
	</div>
	<div id="footer">
		<br>
		<span><i><b>Firma:</b></i></span>
		<br>
		<!--p style="text-align: center;">
			@if($firma != "[]")
				@if($historia->seguro->tipo != 0)
				<img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
				<b>DR. {{$historia->doctor_1->nombre1}} {{$historia->doctor_1->apellido1}} {{$historia->doctor_1->apellido2}}</b><br>
				GASTROENTEROLOGO
				@else
				<img width=150 height=60 src="{{base_path().'/storage/app/logo/firma_rb.png'}}" style="" align=center hspace=12><br>
				<b>DR. CARLOS ROBLES MEDRANDA</b><br>
				GASTROENTEROLOGO
				@endif
			@else
				<img width=150 height=60 src="{{base_path().'/storage/app/logo/firma_rb.png'}}" style="" align=center hspace=12><br>
				<b>DR. CARLOS ROBLES MEDRANDA</b><br>
				GASTROENTEROLOGO
			@endif
		</p-->
		<p style="text-align: center;">
			@if($firma != "[]")
				<img width=150 height=60 src="{{base_path().'/storage/app/avatars/'.$firma[0]->nombre}}" style="" align=center hspace=12><br>
				<b>DR. {{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
			@else
				<br><br><br><br><br>
				<b>DR. {{$protocolo->procedimiento->doctor_firma->nombre1}} {{$protocolo->procedimiento->doctor_firma->apellido1}} {{$protocolo->procedimiento->doctor_firma->apellido2}}</b><br>
			@endif
		</p>
	</div>  

</body>
</html>