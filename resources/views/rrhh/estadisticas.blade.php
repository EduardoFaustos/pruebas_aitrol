@extends('rrhh.resultados.base')
@section('action-content')

<section class="content">
	<div class="box">
		<div class="box-header">
			<form class="form-vertical" method="POST" action="{{route('rrhh.estadisticas')}}">
				{{ csrf_field() }}
				<div class="form-group">
					<div class="col-md-3">
						<label>{{trans('encuestas.año')}}</label>
						<!--input type="text" name="anio" value="{{$anio}}" class="form-control"-->
						<select class="form-control" name="anio" value="{{$anio}}">
							@php $x=2018; $anio_actual=date('Y'); @endphp
							@for($x=2018;$x<=$anio_actual;$x++) <option @if($x==$anio) selected @endif>{{$x}}</option>
								@endfor
						</select>
					</div>
					<div class="col-md-3">
						<label>{{trans('encuestas.mes')}}</label>
						<select name="mes" class="form-control">
							<option value="1" @if($mes==1) selected @endif>{{trans('encuestas.enero')}}</option>
							<option value="2" @if($mes==2) selected @endif>{{trans('encuestas.febrero')}}</option>
							<option value="3" @if($mes==3) selected @endif>{{trans('encuestas.marzo')}}</option>
							<option value="4" @if($mes==4) selected @endif>{{trans('encuestas.abril')}}</option>
							<option value="5" @if($mes==5) selected @endif>{{trans('encuestas.mayo')}}</option>
							<option value="6" @if($mes==6) selected @endif>{{trans('encuestas.junio')}}</option>
							<option value="7" @if($mes==7) selected @endif>{{trans('encuestas.julio')}}</option>
							<option value="8" @if($mes==8) selected @endif>{{trans('encuestas.agosto')}}</option>
							<option value="9" @if($mes==9) selected @endif>{{trans('encuestas.septiembre')}}</option>
							<option value="10" @if($mes==10) selected @endif>{{trans('encuestas.octubre')}}</option>
							<option value="11" @if($mes==11) selected @endif>{{trans('encuestas.noviembre')}}</option>
							<option value="12" @if($mes==12) selected @endif>{{trans('encuestas.diciembre')}}</option>

						</select>
					</div>
					<div class="col-md-3">
						<div>&nbsp;</div>
						<button type="submit" class="btn btn-primary">{{trans('encuestas.buscar')}}</button>
					</div>
					<div class="col-md-3">
						<button type="submit" class="btn btn-primary btn-sm" formaction="{{route('rrhh.detalle_mes')}}"></span>{{trans('encuestas.descargardetalle')}}</button>
					</div>
				</div>
			</form>

			<div class="row">
				<div class="col-sm-8">
					@php
					$txt_mes=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
					@endphp
					<br>
					<h3 class="box-title"> <b>{{trans('encuestas.estadisticasmes')}}{{$txt_mes[$mes-1]}} / {{trans('encuestas.anio')}} {{$anio}}</b></h3>
				</div>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
					@php
					$ponderacion_total=[];
					@endphp
					@foreach($master_encuestas as $master)
					@php
					//dd($master);
					$encuestas2=$encuestas->where('id_area',$master->id);

					$preguntas_2=$preguntas->where('id_masterencuesta',$master->id);
					$total_encuestas=$encuestas2->count();

					$tiempos = explode('+', $master->tiempos);

					@endphp
					<br>
					<h4><b>{{$master->descripcion}}</b></h4>

					<table border="2">
						<thead>
							<tr role="row">
								<th width="20%" style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">{{trans('encuestas.criteriosdeevaluacion')}}</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.buena')}}</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.regular')}} </th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.mala')}}</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.totalderespuestasrecibidaporpreguntas')}}</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.indicadorsinponderarporpregunta')}}</th>
							</tr>
						</thead>
						<tbody>

							@php
							$acumulador=0;
							$contador=0;
							$ponderaciones=[];
							$sin_ponderar=0;
							@endphp

							@foreach($preguntas_2 as $pregunta)

							@php

							$buenas=0;
							$regular=0;
							$mala=0;



							foreach($encuestas2 as $encuesta){

							$buenas=$buenas+$encuesta->complementos->where('id_pregunta',$pregunta->id)->where('valor','4')->count();

							$regular=$regular+$encuesta->complementos->where('id_pregunta',$pregunta->id)->where('valor','3')->count();

							$mala=$mala+$encuesta->complementos->where('id_pregunta',$pregunta->id)->where('valor','2')->count();
							}
							if($total_encuestas<>0){

								$sin_ponderar=round((($buenas*10)+($regular*5)+($mala*0))*10/$total_encuestas,2);
								}


								$contador= $contador+1;
								$acumulador=$acumulador+$sin_ponderar;
								$promedio=round(($acumulador/$contador),2);
								$ponderaciones[$pregunta->id]=[$pregunta->nombre,$sin_ponderar];
								$ponderacion_total[$master->id]=$ponderaciones;

								@endphp
								<tr role="row">
									<td>{{$pregunta->nombre}}</td>
									<td align="center">{{$buenas}}</td>
									<td align="center">{{$regular}}</td>
									<td align="center">{{$mala}}</td>
									<td align="center">{{$total_encuestas}}</td>
									<td align="center">{{$sin_ponderar}} </td>
								</tr>

								@endforeach

								<tr role="row">
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="center"><b>{{trans('encuestas.indicadormensualsinponderacion')}}</b></td>
									<td align="center">{{$promedio}}</td>
								</tr>
						</tbody>
					</table>

					<br>


					@foreach($preguntas_tiempo as $pregunta_tiempo)
					@php
					$a=[];
					$otros=0;
					$xcantidad = 0;
					$acum_otros=0;

					foreach($encuestas2 as $encuesta){

					$otros=$otros+$encuesta->complementos->where('id_pregunta',$pregunta_tiempo->id)->where('valor','otros')->count();

					$xotros=$encuesta->complementos->where('id_pregunta',$pregunta_tiempo->id)->where('valor','otros')->first();

					if(!is_null($xotros)){
					$xcantidad = $xotros->valor2;

					}

					$acum_otros=$acum_otros+$xcantidad;

					}

					foreach($tiempos as $value1){

					$a[$value1]=0;

					}

					@endphp
					<table border="2">
						<thead>

							<tr role="row">
								<th width="25%" style="vertical-align: middle; border-color: black; font-size: 13px;">{{$pregunta_tiempo->nombre}}</th>

								@foreach($tiempos as $value)
								@php


								foreach($encuestas2 as $encuesta){
								$cantidad_tiempo=$encuesta->complementos->where('id_pregunta',$pregunta_tiempo->id)->where('valor',$value)->count();

								$a[$value]=$a[$value]+$cantidad_tiempo;



								}

								@endphp
								<th width="10%" style="text-align: center; border-color: black;vertical-align: middle;font-size: 13px;">{{$value}}</th>
								@endforeach
								<th width="12%" style="text-align: center; border-color: black;vertical-align: middle; font-size: 13px;">{{trans('encuestas.otros')}}</th>
								<th width="12%" style="text-align: center; border-color: black;vertical-align: middle; font-size: 13px;">{{trans('encuestas.promediotiempo')}}</th>
							</tr>
						</thead>
						<tbody>

							<tr role="row">
								<td>&nbsp;</td>
								@php
								$acum=0;
								$con=0;

								@endphp
								@foreach($tiempos as $value2)
								@php
								$x=$value2*$a[$value2];

								$con= $con+$a[$value2];

								$acum=$acum+$x;

								if($con==0){
								$con=1;
								}
								$promedio_tiempo=round(($acum/$con),2);


								@endphp
								<td align="center">{{$a[$value2]}}</td>

								@endforeach
								@php
								$suma_prom=$acum+$acum_otros;
								$prom_tot=round(($suma_prom/$total_encuestas),2);
								@endphp
								<td align="center">{{$otros}}</td>
								<td align="center">{{$prom_tot}}</td>
							</tr>

						</tbody>
					</table>
					<br>
					<div class="col-md-12">
						<center>
							<canvas id="canvas2{{$master->id}}" style="max-width: 70%;"></canvas>
						</center>
					</div>

					@endforeach

					@endforeach


				</div>
			</div>
		</div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

@foreach($master_encuestas as $master)
<script>
	function random_bg_color() {
		var x = Math.floor(Math.random() * 256);
		var y = Math.floor(Math.random() * 256);
		var z = Math.floor(Math.random() * 256);
		var bgColor = "rgb(" + x + "," + y + "," + z + ")";
		return bgColor;
	}

	var barChartData = {
		labels: ['Mes {{$txt_mes[$mes-1]}} - Año {{$anio}}'],
		datasets: [
			@foreach($ponderacion_total[$master - > id] as $p_total) {
				label: '{{$p_total[0]}}',
				backgroundColor: random_bg_color(),
				//borderColor: 'blue',
				borderWidth: 1,
				data: ['{{$p_total[1]}}']
			},
			@endforeach


		]

	};

	var ctx = document.getElementById('canvas2{{$master->id}}').getContext('2d');
	window.myBar = new Chart(ctx, {
		type: 'bar',
		data: barChartData,
		options: {
			responsive: true,
			legend: {
				position: 'bottom',
				align: 'start',
				fullWidth: 'true',
				labels: {
					//fontColor: 'rgb(255, 99, 132)',

				},
			},
			title: {
				display: true,
				text: '{{$master->descripcion}}'
			}
		}
	});
</script>
@endforeach
@endsection