<!DOCTYPE html>
<html>

<head>
	<title>{{trans('emergencia.ORDEN007')}}</title>
	<style>
		@page {
			margin-top: 60px;
			margin-left: 30px;
			margin-right: 30px;

		}

		/** Define now the real margins of every page in the PDF **/
		body {
			margin-top: 50px;
			margin-left: 20px;
			margin-right: 20px;
			margin-bottom: 1px;
			font-size: 10px;
		}

		/** Define the header rules **/
		header {
			position: fixed;
			top: 0.5cm;
			left: 0.5cm;
			right: 0.5cm;
			height: 0.5cm;
		}

		/** Define the footer rules **/
		footer {
			position: fixed;
			bottom: 0cm;
			left: 0cm;
			right: 0cm;
			height: 2cm;
		}

		td {
			border: 1px solid black;
		}

		table {
			/*width: 100px;*/
			border-collapse: collapse;
		}

		.table2d {
			border: none;
			text-align: center;
		}

		.titulo {
			background-color: #C7C3C2;
		}

		p {
			padding-top: 0;
			padding-bottom: 0;
			margin: 0;
		}

		.small {
			font-size: x-small;
			font-weight: bold;
		}

		.texto {
			font-size: 12px;

		}

		.altura {
			height: 20px;
		}

		.espacio {

			padding: 10px;
		}


		.altura2 {
			height: 20px;
		}

		div {
			padding-top: 0;
			padding-bottom: 0;
		}
	</style>
</head>

<body>

	<table border="1" class="table" style="width: 100%">
		<tbody>
			<tr>
				<td class="alinear texto espacio" colspan="4"><b>{{trans('emergencia.ESTABLECIMIENTOSOLICITANTE')}}</b></td>
				<td class="alinear texto espacio" colspan="4"><b>{{trans('emergencia.NOMBRE')}}</b></td>
				<td colspan="4"><b>{{trans('emergencia.APELLIDO')}}</b></td>
				<td colspan="2"><b>{{trans('emergencia.SEXO(F-M)')}}</b></td>
				<td><b>{{trans('emergencia.EDAD')}}</b></td>
				<td colspan="2"><b>{{trans('emergencia.NHISTORIACLINICA')}}</b></td>
			</tr>
			<tr>
				<td class="alinear texto espacio" colspan="4"><b>{{ $empresa->nombrecomercial }}</b></td>
				<td colspan="4"><b>{{ $paciente->nombre1 }} {{ $paciente->nombre2 }}</b></td>
				<td colspan="4"><b>{{ $paciente->apellido1 }} {{ $paciente->apellido2 }}</b></td>
				<td colspan="2"><b>
						@if( $paciente->sexo == '1' )
						M
						@else
						F
						@endif
					</b></td>
				<td><b>{{ $age }}</b></td>
				<td colspan="2"><b>{{ $paciente->id }}</b></td>
			</tr>
		</tbody>
	</table>
	<div style="margin-top: 15px">
		<table border="1" class="table" style="width: 100%">
			<thead>
				<tr class="celda-titulo">
					<th colspan="18" class="small">{{trans('emergencia.1CARACTERISTICASDELASOLICITUDYMOTIVO')}}</th>
				</tr>

			</thead>

			<tbody class="alinear texto altura">
				<tr>
					<td class="alinear texto espacio" style="text-align: center;"><b>{{trans('emergencia.ESTABLECIMIENTO')}} <br>{{trans('emergencia.DELDESTINO')}}</b></td>
					<td colspan="5" style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td style="text-align: center;"><b>{{trans('emergencia.SERVICIOCONSULTADO')}}</b></td>
					<td colspan="3" style="text-align: center;"></td>
					<td colspan="2" style="text-align: center;"><b>{{trans('emergencia.SERVICIOQUESOLICITA')}}</b></td>
					<td colspan="2" style="text-align: center;">{{ $interconsulta->especialidad }}</td>
					<td style="text-align: center;"><b>{{trans('emergencia.SALA')}}</b></td>
					<td style="text-align: center;">&nbsp;&nbsp;</td>
					<td style="text-align: center;"><b>{{trans('emergencia.CAMA')}}</b></td>
					<td style="text-align: center;">&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td class="alinear texto espacio" style="text-align: center;"><b>{{trans('emergencia.NORMAL')}}</b></td>
					<td style="text-align: center;">&nbsp;&nbsp;</td>
					<td style="text-align: center;"><b>{{trans('emergencia.URGENTE')}}</b></td>
					<td style="text-align: center;">&nbsp;&nbsp;</td>
					<td colspan="3" style="text-align: center;"><b>{{trans('emergencia.MEDICOINTERCONSULTADO')}}</b></td>
					<td colspan="4" style="text-align: center;"></td>
					<td colspan="3" style="text-align: center;"><b>{{trans('emergencia.DESCRIPCIONDELMOTIVO')}}</b></td>
					<td colspan="4" style="text-align: center;">{{$interconsulta->tarifario}}</td>
				</tr>
				<tr>
				<td colspan="18" style="text-align: center;">{{$interconsulta->descripcion}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="margin-top: 15px">
		<table border="1" class="table" style="width: 100%">
			<thead>
				<tr class="celda-titulo">
					<th colspan="18" class="small">{{trans('emergencia.2CUADROCLINICOACTUAL')}}</th>
				</tr>

			</thead>

			<tbody class="alinear texto altura">
				<tr>
					<td class="alinear texto espacio" colspan="18" style="text-align: center">{{$interconsulta->evolucion}}
				</td>

				</tr>

			</tbody>
		</table>
	</div>

	<div style="margin-top: 10px">

		<table border="1" class="table" style="width: 100%">
			<thead>
				<tr class="celda-titulo">
					<th colspan="18" class="small">{{trans('emergencia.3RESULTADOSDEEXAMENESYPROCEDIMIENTOSDIAGNOSTICOS')}}</th>
				</tr>

			</thead>
			<tbody class="alinear texto altura">
				<tr>
					<td class="alinear texto espacio" colspan="18" style="text-align: center">{{$interconsulta->resultados_exa}}</td>

				</tr>

			</tbody>

		</table>
	</div>
	<div style="width: 100%; margin-top: 15px;margin-bottom: 25px;">
		<div style="width: 100% ">
			<table border="1" class="texto"  style="width: 100% ">
				<thead style="border-bottom: solid 1px;" class="border_none">
					<tr>
						<th class="alinear texto altura" colspan="1" style="border:none;">4</th>
						<th class="alinear texto altura" colspan="4" style="border:none;">{{trans('emergencia.DIAGNOSTICO')}}</th>
						<th width="10%" style="border:none; font-size: 8px;">{{trans('emergencia.PRE=PRESUNTIVO')}} <br> {{trans('emergencia.DEF=DEFINITIVO')}} </th>
						<th style="border:none;">&nbsp;&nbsp;</th>
						<th style="border:none;">&nbsp;&nbsp;</th>
						<th style="border:none;">&nbsp;&nbsp;</th>
						<th style="border:none;">&nbsp;&nbsp;</th>
						<th style="border:none;">&nbsp;&nbsp;</th>
						<th style="border:none;">&nbsp;&nbsp;</th>
						<th style="border:none;">{{trans('emergencia.CIE')}}</th>
						<th style="border:none;">{{trans('emergencia.PRE')}}</th>
						<th style="border:none;">{{trans('emergencia.DEF')}}</th>
					</tr>
				</thead>
				<tbody class="border_none">
					@php $cont = 1; @endphp
				@foreach($cie10 as $cie)
                <tr  class="alinear texto altura">
                    @php 
                        $c3 = Sis_medico\Cie_10_3::find($cie->cie10); 
                        $c4 = Sis_medico\Cie_10_4::find($cie->cie10);
                        $texto = '';
                        if(!is_null($c3)){
                            $texto = $c3->descripcion;
                        }
                        if(!is_null($c4)){
                            $texto = $c4->descripcion;
                        }
                    @endphp
						<td  class="alinear texto altura">{{$cont}}</td>			
						<td  class="alinear"colspan="11">{{$cie->cie10}}-{{$texto}}</td>
						<td> {{$cie->cie10}}</td>
						<td>@if($cie->presuntivo_definitivo=='PRESUNTIVO') <span> X </span>  @elseif($cie->presuntivo_definitivo=="DEFINITIVO")@endif</td>
						<td>@if($cie->presuntivo_definitivo=='PRESUNTIVO') <span>  </span>  @elseif($cie->presuntivo_definitivo=='DEFINITIVO') <span> X </span> @endif </td>	
				
					</tr>
					@php $cont++ @endphp
					@endforeach

				</tbody>
			</table>
		</div>
	</div>

	<div style="margin-top: 10px">

		<table border="1" class="table" style="width: 100%">
			<thead>
				<tr class="celda-titulo">
					<th colspan="18" class="small">{{trans('emergencia.5PLANESTERAPEUTICOSYEDUCACIONALESREALIZADOS')}}</th>
				</tr>

			</thead>
			<tbody class="alinear texto altura">
				<tr>
					<td class="alinear texto espacio" colspan="18" style="text-align: center">{{$interconsulta->plan_terapeuticos}}</td>

				</tr>

			</tbody>


		</table>
	</div>
	<div style="margin-top: 10px">

		<p style="text-align: center;">{{trans('emergencia.CODIGO')}}</p>
	</div>
	<div style="margin-top: 10px">

		<table border="1" class="table" style="width: 100%">
		@php
                $usuario= \Sis_medico\User::where('id',$interconsulta->id_usuariocrea)->first();
                @endphp
			<tbody>

				<tr>
					<td style="width:20%" class="alinear texto espacio"><b>{{trans('emergencia.Fecha')}}</b></td>
					<td style="width:30%" class="alinear texto espacio">{{ $interconsulta->fecha|date("d/m/Y") }}</td>
					<td style="width:30%" class="alinear texto espacio"><b>{{trans('emergencia.Hora')}}</b></td>
					<td style="width:50%"class="alinear texto espacio">{{ $interconsulta->fecha|date("H:i:s") }}</td>
					<td style="width:30%" class="alinear texto espacio"><b>{{trans('emergencia.Profesional')}}</b></td>
					<td style="width:50%"class="alinear texto espacio">{{ $interconsulta->doctor->apellido1 }} {{ $interconsulta->doctor->nombre1 }}</td>
					<td style="width:30%"class="alinear texto espacio">{{ $interconsulta->id }}</td>
					<td style="width:30%"class="alinear texto espacio"><b>{{trans('emergencia.Firma')}}</b></td>
					<td style="width:50%"class="alinear texto espacio">{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</td>
					<td style="width:20%"class="alinear texto espacio"><b>{{trans('emergencia.NúmerodeHoja')}}</b></td>
					<td style="width:10%" class="alinear texto espacio">1</td>
				</tr>
				<tr>
					<td class="alinear texto espacio" colspan="6">{{trans('emergencia.SNS-MSP/HCU-form.007/2008')}}</td>

					<td colspan="5">{{trans('emergencia.INTERCONSULTA-SOLICITUD')}}</td>

				</tr>

			</tbody>
		</table>
	</div>
</body>

</html>