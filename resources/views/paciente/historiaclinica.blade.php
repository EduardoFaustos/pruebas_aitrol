<html>

<head>

	<title>Paciente_{{ $paciente->apellido1}}_{{ $paciente->nombre1}}</title>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
	<meta name=Generator content="Microsoft Word 15 (filtered)">
	<style type="text/css">
		.div_tiny p {
			margin: 0;
		}
	</style>
	<style>
		/* Font Definitions */
		@font-face {
			font-family: "Cambria Math";
			panose-1: 2 4 5 3 5 4 6 3 2 4;
		}

		@font-face {
			font-family: Calibri;
			panose-1: 2 15 5 2 2 2 4 3 2 4;
		}

		@font-face {
			font-family: Harrington;
			panose-1: 4 4 5 5 5 10 2 2 7 2;
		}

		/* Style Definitions */
		p.MsoNormal,
		li.MsoNormal,
		div.MsoNormal {
			margin-top: 0cm;
			margin-right: 0cm;
			margin-bottom: 10.0pt;
			margin-left: 0cm;
			line-height: 115%;
			font-size: 11.0pt;
			font-family: "Calibri", sans-serif;
		}

		p.MsoNoSpacing,
		li.MsoNoSpacing,
		div.MsoNoSpacing {
			margin: 0cm;
			margin-bottom: .0001pt;
			font-size: 9.0pt;
			font-family: "Calibri", sans-serif;
		}

		span.apple-converted-space {
			mso-style-name: apple-converted-space;
		}

		.MsoChpDefault {
			font-size: 10.0pt;
			font-family: "Calibri", sans-serif;
		}

		@page WordSection1 {
			size: 595.3pt 841.9pt;
			margin: 42.55pt 49.55pt 7.1pt 70.9pt;
		}

		div.WordSection1 {
			page: WordSection1;
		}

		/* List Definitions */
		ol {
			margin-bottom: 0cm;
		}

		ul {
			margin-bottom: 0cm;
		}

		span {
			font-size: 10.0pt;
		}
	</style>


</head>

<body lang=ES-EC style="margin-top: -20px;">
	<div id="header">
		<table>
			<tr>
				<td style="width: 150px;"><img style="margin: 0;width: 180px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}"></td>
				<td colspan="2">
					<p style="font-size: 19px; text-align: center; width: 350px; margin-left:50px;"><b style="text-align: center;">INSTITUTO ECUATORIANO <b><br><b>DE ENFERMEDADES DIGESTIVAS</b></p>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="2"><span lang=ES style='font-size:12px; margin-left:50px;'>Fecha de Impresion: @if(date('N') == 1){{"Lunes,"}}@elseif(date('N') == 2){{"Martes,"}}@elseif(date('N') == 3){{"Mi&eacute;rcoles,"}}@elseif(date('N') == 4){{"Jueves,"}}@elseif(date('N') == 5){{"Viernes,"}}@elseif(date('N') == 6){{"S&aacute;bado,"}}@elseif(date('N') == 7){{"Domingo,"}}@endif {{date('j')}} de @if(date('n') == 1){{"Enero"}}@elseif(date('n') == 2){{"Febrero"}}@elseif(date('n') == 3){{"Marzo"}}@elseif(date('n') == 4){{"Abril"}}@elseif(date('n') == 5){{"Mayo"}}@elseif(date('n') == 6){{"Junio"}}@elseif(date('n') == 7){{"Julio"}}@elseif(date('n') == 8){{"Agosto"}}@elseif(date('n') == 9){{"Septiembre"}}@elseif(date('n') == 10){{"Octubre"}}@elseif(date('n') == 11){{"Noviembre"}}@elseif(date('n') == 12){{"Diciembre"}}@endif del {{date('Y')}}</span></td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="{{$paciente->fecha_nacimiento}}">

	<div class=WordSection1>
		<p class=MsoNoSpacing><b><span lang=ES style='font-size:6.0pt'>&nbsp;</span></b></p>
		<table style="border: none; font-size: 17px !important; line-height: 25px;">
			<tbody>
				<tr>
					<td><span><b>N° C&oacute;digo: </b></span></td>
					<td></td>
				</tr>
				<tr>
					<td><span><b>C&eacute;dula: </b></span></td>
					<td>{{$paciente->id}}</td>
				</tr>
				<tr>
					<td><span><b>Apellidos: </b></span></td>
					<td>{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
				</tr>
				<tr>
					<td><span><b>Nombres: </b></span></td>
					<td>{{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</td>
				</tr>
				<tr>
					<td><span><b>Fecha de ingreso: </b></span></td>
					<td>{{date("d/m/Y", strtotime($paciente->created_at))}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.fechadenacimiento')}}: &nbsp;&nbsp;&nbsp;</b></span></td>
					<td>{{date("d/m/Y", strtotime($paciente->fecha_nacimiento))}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.edad')}}: </b></span></td>
					<td>{{$age}} {{trans('pacientes.anos')}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.sexo')}}: </b></span></td>
					<td>@if($paciente->sexo == 1){{"Masculino"}}@elseif($paciente->sexo == 2){{"Femenino"}}@endif</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.estadocivil')}}: </b></span></td>
					<td>@if($paciente->estadocivil == 1){{"Soltero"}}@elseif($paciente->estadocivil == 2){{"Casado"}}@elseif($paciente->estadocivil == 3){{"Viudo"}}@elseif($paciente->estadocivil == 4){{"Divorciado"}}@elseif($paciente->estadocivil == 5){{"Union Libre"}}@elseif($paciente->estadocivil == 6){{"Union de Hecho"}}@endif</td>
				</tr>
				@if($paciente->id != '1303411605')
				<tr>
					<td><span><b>{{trans('pacientes.seguro')}}: </b></span></td>
					<td>{{$paciente->seguro->nombre}}</td>
				</tr>
				@endif
				<tr>
					<td><span><b>{{trans('pacientes.tiposangre')}}: </b></span></td>
					<td>{{$paciente->gruposanguineo}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.lugarnacimiento')}}: </b></span></td>
					<td>{{$paciente->lugar_nacimiento}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.procedencia')}}: </b></span></td>
					<td>{{$paciente->ciudad}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.ocupacion')}}: </b></span></td>
					<td>{{$paciente->ocupacion}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.direccion')}}: </b></span></td>
					<td>{{$paciente->direccion}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.telefono')}}: </b></span></td>
					<td>{{$paciente->telefono1}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.celular')}}: </b></span></td>
					<td>{{$paciente->telefono2}}</td>
				</tr>
				<tr>
					<td><span><b>{{trans('pacientes.email')}}: </b></span></td>
					<td>{{$paciente->usuario->email}}</td>
				</tr>
			</tbody>
		</table>





	</div>
	<div style="page-break-after:always;"></div>
	<span style="font-size: 25px; font-color: blue;width: 120px;"><b>{{trans('pacientes.visitas')}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span>
	<span style="width: 30px;"> &nbsp;</span>
	<span>{{trans('pacientes.fechaimp')}}: @if(date('N') == 1){{"Lunes,"}}@elseif(date('N') == 2){{"Martes,"}}@elseif(date('N') == 3){{"Mi&eacute;rcoles,"}}@elseif(date('N') == 4){{"Jueves,"}}@elseif(date('N') == 5){{"Viernes,"}}@elseif(date('N') == 6){{"S&aacute;bado,"}}@elseif(date('N') == 7){{"Domingo,"}}@endif {{date('j')}} de @if(date('n') == 1){{"Enero"}}@elseif(date('n') == 2){{"Febrero"}}@elseif(date('n') == 3){{"Marzo"}}@elseif(date('n') == 4){{"Abril"}}@elseif(date('n') == 5){{"Mayo"}}@elseif(date('n') == 6){{"Junio"}}@elseif(date('n') == 7){{"Julio"}}@elseif(date('n') == 8){{"Agosto"}}@elseif(date('n') == 9){{"Septiembre"}}@elseif(date('n') == 10){{"Octubre"}}@elseif(date('n') == 11){{"Noviembre"}}@elseif(date('n') == 12){{"Diciembre"}}@endif del {{date('Y')}}</span>
	<hr>
	<table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
		<tbody>
			<tr>
				<td style="border-right: 1px solid;width: 103px;"><b>{{trans('pacientes.ci')}}: </b></td>
				<td style="border-right: 1px solid;width: 110px;">&nbsp;&nbsp; {{$paciente->id}}</td>
				<td style="border-right: 1px solid;width: 65px;"><b>{{trans('pacientes.paciente')}}:</b></td>
				<td style="border-right: 1px solid;" colspan="4">&nbsp;&nbsp; {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</td>
			</tr>
		</tbody>
	</table>
	<hr>
	@php
	$contador_1 = 1;
	$modulo = 0;
	@endphp
	@foreach($general as $value)
	@foreach($value->procedimiento as $procedimiento)
	@if($procedimiento->id_procedimiento_completo == 40 )
	@php
	$evolucion = Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->first();
	$cie10 = Sis_medico\Hc_Cie10::where('hc_id_procedimiento', $procedimiento->id)->get();
	$receta = Sis_medico\hc_receta::where('id_hc', $value->hcid)->first();
	@endphp
	<table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
		<tbody>
			<tr>
				<td style="border-right: 1px solid;"><b>{{trans('pacientes.fecha')}}: </b></td>
				<td style="border-right: 1px solid;">&nbsp;&nbsp; {{date("d/m/Y", strtotime($value->created_at))}}</td>
				<td style="border-right: 1px solid;"><b>{{trans('pacientes.medicoexaminador')}}:</b></td>
				<td colspan="3" style="border-right: 1px solid;">&nbsp;Dr. {{ $value->doctor_1->apellido1}} @if($value->doctor_1->apellido2 != "(N/A)"){{ $value->doctor_1->apellido2}}@endif {{ $value->doctor_1->nombre1}} @if($value->doctor_1->nombre2 != "(N/A)"){{ $value->doctor_1->nombre2}}@endif</td>
			</tr>
			<tr>
				<td style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.presion')}}: </b></td>
				<td style="border-right: 1px solid;border-top: 1px solid;"><b>{{trans('pacientes.pulso')}}: </b></td>
				<td style="border-right: 1px solid;border-top: 1px solid;"><b>{{trans('pacientes.peso')}}: </b></td>
				<td style="border-right: 1px solid;border-top: 1px solid;"><b>{{trans('pacientes.altura')}}: </b></td>
				<td style="border-right: 1px solid;border-top: 1px solid;"><b>{{trans('pacientes.preimetroab')}}: </b></td>
				<td style="border-right: 1px solid;border-top: 1px solid;"><b>{{trans('pacientes.pesoideal')}}: </b></td>
			</tr>
			<tr style="text-align: center;">
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->presion != null) @if($value->presion != 0){{$value->presion}} @endif @endif</td>
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->pulso != 0){{$value->pulso}}@endif</td>
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->peso != 0){{$value->peso}}@endif</td>
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->altura != 0){{$value->altura}}@endif</td>
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->perimetro != 0){{$value->perimetro}}@endif</td>
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->altura != 0) @php
					$altura = pow(($value->altura/100), 2);
					$peso_ideal = 21.45 * ($altura);
					$imc = $value->peso/$altura;
					$sexo = null;
					if($paciente->sexo == 1){
					$sexo = 1;
					}else{
					$sexo = 0;
					}
					$texto = "";
					if($imc < 16){ $texto="Desnutrición" ; } elseif($imc < 18){ $texto="Bajo de Peso" ; } elseif($imc < 25){ $texto="Normal" ; } elseif($imc < 27){ $texto="Sobrepeso" ; } elseif($imc < 30){ $texto="Obesidad Tipo 1" ; } elseif($imc < 40){ $texto="Obesidad Clinica" ; } else{ $texto="Obesidad Mordida" ; } $gct=((1.2 * $imc) + (0.23 * $age) - (10.8 * $sexo) - 5.4); @endphp{{number_format($peso_ideal, 2, '.', '')}}@endif</td>
			</tr>
			<tr>
				<td colspan="2" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.gct')}}: </b></td>
				<td colspan="2" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.imc')}}: </b></td>
				<td colspan="2" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.imccategoria')}}:</b></td>
			</tr>
			<tr style="text-align: center;">
				<td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->altura != 0){{number_format($gct, 2, '.', '')}}@endif</td>
				<td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->altura != 0){{number_format($imc, 2, '.', '')}}@endif</td>
				<td colspan="2" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($value->altura != 0){{$texto}}@endif</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.motivo')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($evolucion != null){{$evolucion->motivo}}@endif</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.antecedentes')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div style="padding: 0;"><b>Antecedentes Patológicos:</b>{{$paciente->antecedentes_pat}}</div>
					<div style="padding: 0;"><b>Antecedentes Famililares:</b>{{$paciente->antecedentes_fam}}</div>
					<div style="padding: 0;"><b>Antecedentes Quirúrgicos:</b>{{$paciente->antecedentes_quir}}</div>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.evolucion')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div class="div_tiny" style="width: 100%;line-height: 1.2;font-size: 12px !important;">@if($evolucion != null) @if($evolucion->cuadro_clinico != null)<?php echo $evolucion->cuadro_clinico; ?> @else{{"&nbsp;"}}@endif @else{{'&nbsp;'}}@endif &nbsp;</div>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.examenfisico')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div style="font-family:'Calibri',sans-serif;margin: 0;padding: 0;font-size: 12px;">@if($evolucion != null) @if($evolucion->child_pug != null){{$evolucion->child_pug->examen_fisico}} @else{{'&nbsp;'}}@endif @else{{'&nbsp;'}}@endif</div>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.diagnostico')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">@if($cie10 != '[]') @php
					$contador = 0;
					@endphp
					@foreach($cie10 as $value_cie10)<?php
													$cie10_nombre = Sis_medico\Cie_10_3::find($value_cie10);
													if ($cie10_nombre == '[]') {
														$cie10_nombre = Sis_medico\Cie_10_4::find($value_cie10);
													}
													if ($contador != 0) {
														echo ", ";
													}
													echo $cie10_nombre[0]->descripcion . " (" . $value_cie10->presuntivo_definitivo . ")";

													$contador++;
													?>@endforeach @else{{'&nbsp;'}}@endif</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.medicacion')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div class="div_tiny" style="width: 100%;line-height: 1.2;">@if($receta != null) @if($receta->rp != null)<?php echo $receta->rp; ?>@else{{'&nbsp;'}}@endif @else{{'&nbsp;'}}@endif</div>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.estudiossol')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div style="font-family:'Calibri',sans-serif;margin: 0;padding: 0;font-size: 12px;">@if($value->examenes_realizar != null)<?php echo $value->examenes_realizar; ?>@else{{'&nbsp;'}}@endif</div>
				</td>
			</tr>
		</tbody>
	</table>
	<hr>
	@php
	$modulo = $contador_1 % 2;
	@endphp
	@if($modulo == 0)
	<!--div style="page-break-after:always;"></div-->
	@endif
	@php
	$contador_1++;
	@endphp
	@endif
	@endforeach
	@endforeach
	@if($modulo != 0)
	<!--div style="page-break-after:always;"></div-->
	@endif

	<span style="font-size: 25px; font-color: blue;width: 120px;"><b>{{trans('pacientes.procedimientos')}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span>
	<span style="width: 30px;"> &nbsp;</span>
	<span>{{trans('pacientes.fechaimp')}}: @if(date('N') == 1){{"Lunes,"}}@elseif(date('N') == 2){{"Martes,"}}@elseif(date('N') == 3){{"Mi&eacute;rcoles,"}}@elseif(date('N') == 4){{"Jueves,"}}@elseif(date('N') == 5){{"Viernes,"}}@elseif(date('N') == 6){{"S&aacute;bado,"}}@elseif(date('N') == 7){{"Domingo,"}}@endif {{date('j')}} de @if(date('n') == 1){{"Enero"}}@elseif(date('n') == 2){{"Febrero"}}@elseif(date('n') == 3){{"Marzo"}}@elseif(date('n') == 4){{"Abril"}}@elseif(date('n') == 5){{"Mayo"}}@elseif(date('n') == 6){{"Junio"}}@elseif(date('n') == 7){{"Julio"}}@elseif(date('n') == 8){{"Agosto"}}@elseif(date('n') == 9){{"Septiembre"}}@elseif(date('n') == 10){{"Octubre"}}@elseif(date('n') == 11){{"Noviembre"}}@elseif(date('n') == 12){{"Diciembre"}}@endif del {{date('Y')}}</span>
	<hr>
	<table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
		<tbody>
			<tr>
				<td style="border-right: 1px solid;width: 103px;"><b>{{trans('pacientes.ci')}}: </b></td>
				<td style="border-right: 1px solid;width: 110px;">&nbsp;&nbsp; {{$paciente->id}}</td>
				<td style="border-right: 1px solid;width: 65px;"><b>{{trans('pacientes.paciente')}}:</b></td>
				<td style="border-right: 1px solid;" colspan="4">&nbsp;&nbsp; {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</td>
			</tr>
		</tbody>
	</table>
	<hr>
	@php
	$contador_2 = 1;
	@endphp
	@foreach($general as $value)
	@foreach($value->procedimiento as $procedimiento)
	@if($procedimiento->id_procedimiento_completo != 40 )
	@php
	$evolucion = Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->first();
	$evolucion_pre = Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->where('secuencia','0')->first();
	$evolucion_pos = Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->where('secuencia','1')->first();
	$protocolo = Sis_medico\hc_protocolo::where('id_hc_procedimientos', $procedimiento->id)->first();
	$cie10 = Sis_medico\Hc_Cie10::where('hc_id_procedimiento', $procedimiento->id)->get();
	$receta = Sis_medico\hc_receta::where('id_hc', $value->hcid)->first();

	$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
	$mas = true;
	$texto = "";

	foreach($adicionales as $value2)
	{
	if($mas == true){
	$texto = $texto.$value2->procedimiento->nombre ;
	$mas = false;
	}
	else{
	$texto = $texto.' + '. $value2->procedimiento->nombre ;
	}
	}
	@endphp
	@if(!is_null($protocolo))
	@if($protocolo->hallazgos != '')

	<table style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
		<tbody>
			<tr>
				<td style="width: 50px; border-right: 1px solid;"><b>{{trans('pacientes.procedimientos')}}: </b></td>
				<td colspan="5" style="border-right: 1px solid;">&nbsp;&nbsp;@if(!is_null($procedimiento->procedimiento_completo)) {{$procedimiento->procedimiento_completo->nombre_completo}} @else {{$texto}} @endif</td>
			</tr>
			<tr>
				<td style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.fecha')}}: </b></td>
				<td style="border-right: 1px solid; border-top: 1px solid;">&nbsp;&nbsp; {{date("d/m/Y", strtotime($value->created_at))}}</td>
				<td style="width: 130px; border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.medicoexaminador')}}:</b></td>
				<td colspan="3" style="border-right: 1px solid;border-top: 1px solid;">&nbsp;Dr. @if($procedimiento->id_doctor_examinador != null) {{ $procedimiento->doctor->apellido1}} @if($procedimiento->doctor->apellido2 != "(N/A)"){{ $procedimiento->doctor->apellido2}}@endif {{ $procedimiento->doctor->nombre1}} @if($procedimiento->doctor->nombre2 != "(N/A)"){{ $procedimiento->doctor->nombre2}}@endif @else{{ $value->doctor_1->apellido1}} @if($value->doctor_1->apellido2 != "(N/A)"){{ $value->doctor_1->apellido2}}@endif {{ $value->doctor_1->nombre1}} @if($value->doctor_1->nombre2 != "(N/A)"){{ $value->doctor_1->nombre2}}@endif @endif</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.motivo')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">&nbsp;@if($protocolo != null){{$protocolo->motivo}}@endif</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.hallazgos')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div class="div_tiny" style="width: 100%;line-height: 1.2; font-size: 12px !important;">@if($protocolo != null) @if($protocolo->hallazgos != null)<?php echo $protocolo->hallazgos; ?>@else{{"&nbsp;"}}@endif @else{{'&nbsp;'}}@endif</div>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.conclusion')}}</b></td>
			</tr>

			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid; font-size: 12px;">
					<div class="div_tiny" style="width: 100%;line-height: 1.2; font-size: 12px !important;">@if($protocolo != null) @if($protocolo->conclusion != null)<?php echo $protocolo->conclusion; ?>@else{{"&nbsp;"}}@endif @else{{'&nbsp;'}}@endif</div>
				</td>
			</tr>
			@if(!is_null($evolucion_pre))
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.evopreoperatoria')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div class="div_tiny" style="width: 100%;line-height: 1.2;">@if($evolucion_pre->cuadro_clinico != null)<?php echo $evolucion_pre->cuadro_clinico; ?> @else{{"&nbsp;"}}@endif &nbsp;</div>
				</td>
			</tr>
			@endif
			@if(!is_null($evolucion_pos))
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.evopostoperatoria')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;">
					<div class="div_tiny" style="width: 100%;line-height: 1.2;">@if($evolucion_pos->cuadro_clinico != null)<?php echo $evolucion_pos->cuadro_clinico; ?> @else{{"&nbsp;"}}@endif &nbsp;</div>
				</td>
			</tr>
			@endif
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;"><b>{{trans('pacientes.diagnosiscie10')}}</b></td>
			</tr>
			<tr>
				<td colspan="6" style="border-right: 1px solid; border-top: 1px solid;font-size: 12px !important;">@if($cie10 != '[]') @php
					$contador = 0;
					@endphp
					@foreach($cie10 as $value_cie10)<?php
													$cie10_nombre = Sis_medico\Cie_10_3::find($value_cie10);
													if ($cie10_nombre == '[]') {
														$cie10_nombre = Sis_medico\Cie_10_4::find($value_cie10);
													}
													if ($contador != 0) {
														echo ", ";
													}
													echo $cie10_nombre[0]->descripcion . ' (' . $value_cie10->presuntivo_definitivo . ')';

													$contador++;
													?>@endforeach @else{{'&nbsp;'}}@endif</td>
			</tr>
		</tbody>
	</table>
	@endif
	@endif

	<hr>
	@php
	$modulo = $contador_2 % 2;
	@endphp
	@if($modulo == 0)
	<!--div style="page-break-after:always;"></div-->
	@endif
	@php
	$contador_2++;
	@endphp
	@endif
	@endforeach
	@endforeach
	<script type="text/javascript">
		$(document).ready(function() {

			edad2();


		});


		function edad2() {

			var nacimiento = document.getElementById("fecha_nacimiento").value;
			var edad = calcularEdad(nacimiento);

			if (isNaN(edad)) {
				var jsspan = document.getElementById("xedad");

				jsspan.innerHTML = "0";
			} else {
				var jsspan = document.getElementById("xedad");
				jsspan.innerHTML = edad + " años";
			}
		}
	</script>

</body>

</html>