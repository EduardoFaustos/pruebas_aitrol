<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
	<div class="row">
		<div class="table-responsive col-md-12" style="min-height: 210px;">
			<table id="example4" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
				<thead>
					<tr>
						<th>{{trans('dtraduccion.Orden')}}</th>
						<th>{{trans('dtraduccion.Paciente')}}</th>
						<th>{{trans('dtraduccion.Seguro')}}</th>
						<th>{{trans('dtraduccion.Cantidad')}}</th>
						<th>{{trans('dtraduccion.Total')}}</th>
						<th>{{trans('dtraduccion.FechaOrden')}}</th>
						<th>{{trans('dtraduccion.Acción')}}</th>
					</tr>
				</thead>
				<tbody>

					@foreach($ordenes as $orden)
					<tr>
						<td>{{$orden->id}}</td>
						<td>{{$orden->paciente->apellido1}} {{$orden->paciente->apellido2}} {{$orden->paciente->nombre1}} {{$orden->paciente->nombre2}}</td>
						<td>{{$orden->seguro->nombre}}</td>
						<td>{{$orden->cantidad}}</td>
						<td>{{$orden->total_valor}}</td>
						<td>{{$orden->fecha_orden}}</td>
						<td><a onclick="guardar_det('{{$orden->id}}');" class="btn btn-success btn-xs">{{trans('dtraduccion.Agregar')}}</a></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>