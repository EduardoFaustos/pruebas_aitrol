@extends('contable.plantillas_prestamos.base')
@section('action-content')

<section class="content">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
			<li class="breadcrumb-item"><a href="#">Nomina</a></li>
			<li class="breadcrumb-item active" aria-current="page">Plantillas Prestamos</li>
		</ol>
	</nav>
	<div class="box">
		<div class="box-header">
			<div class="col-md-7">
				<h5><b>Plantillas Prestamos</b></h5>
			</div>
			<div class="col-md-4">
				<a type="button" class="btn btn-primary" href="{{route('plantillas_nomina.excel_plantilla_prestamo')}}"><span class="glyphicon glyphicon-download-alt"></span> Plantilla</a>
			</div>
		</div>
		<div class="row head-title">
			<div class="col-md-12 cabecera">
				<label class="color_texto" for="title"></label>
			</div>
		</div>

		<div class="box-body dobra">
			<form method="POST" id="form_prestamo" enctype="multipart/form-data" role="form">
				{{ csrf_field() }}
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="form-group col-md-2 col-xs-2">
					<label class="texto" for="year">{{trans('contableM.Anio')}}</label>
				</div>

				<div class="form-group col-md-2 col-xs-10 container-4">
					<select class="form-control" name="anio" value="{{$anio}}">
						@php $x=2019; $anio_actual=date('Y'); @endphp
						@for($x=2019;$x<=$anio_actual;$x++) <option @if($x==$anio) selected @endif>{{$x}}</option>
							@endfor
					</select>
				</div>
				<div class="form-group col-md-2 col-xs-2">
					<label class="texto" for="mes">{{trans('contableM.mes')}}</label>
				</div>
				<div class="form-group col-md-2 col-xs-10 container-4">
					<select class="form-control" name="mes">
						<option value="1" @if($mes==1) selected @endif>Enero</option>
						<option value="2" @if($mes==2) selected @endif>Febrero</option>
						<option value="3" @if($mes==3) selected @endif>Marzo</option>
						<option value="4" @if($mes==4) selected @endif>Abril</option>
						<option value="5" @if($mes==5) selected @endif>Mayo</option>
						<option value="6" @if($mes==6) selected @endif>Junio</option>
						<option value="7" @if($mes==7) selected @endif>Julio</option>
						<option value="8" @if($mes==8) selected @endif>Agosto</option>
						<option value="9" @if($mes==9) selected @endif>Septiembre</option>
						<option value="10" @if($mes==10) selected @endif>Octubre</option>
						<option value="11" @if($mes==11) selected @endif>Noviembre</option>
						<option value="12" @if($mes==12) selected @endif>Diciembre</option>
					</select>
				</div>

				<div class="form-group col-md-2 col-xs-2">
					<label class="texto" for="prestamos">Prestamos</label>
				</div>
				<div class="form-group col-md-2 col-xs-10 container-4" style="padding-left: 15px;">
					<select class="form-control" name="prestamos">
						<option value="1">Quirografario</option>
						<option value="2">Hipotecario</option>
					</select>
				</div>

				<div class="form-group col-md-2 col-xs-2">
					<label class="texto" for="ag_archivo">Agregar Archivo</label>
				</div>

				<div class="form-group col-md-6 col-xs-10 container-4" style="padding-left: 15px;">
					<input name="archivo" id="archivo" type="file" class="archivo form-control" required accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
				</div>

				<div class="col-xs-2">
					<button type="button" class="btn btn-primary" onclick="guardar();">
						Guardar
					</button>
				</div>
			</form>
		</div>
	</div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
	function guardar() {
		event.preventDefault();
		var form = $('#form_prestamo')[0];
		var data = new FormData(form);

		$.ajax({
			enctype: 'multipart/form-data',
			type: "post",
			url: "{{ route('plantillas_nomina.subir_prestamos')}}",
			headers: {
				'X-CSRF-TOKEN': $('input[name=_token]').val()
			},
			datatype: "html",
			data: data,
			processData: false,
			contentType: false,
			cache: false,
			timeout: 600000,
			success: function(datahtml) {
				//console.log(datahtml);
				if (datahtml == 'ok') {
					swal("Guardado!", "Correcto", "success");
					setTimeout(function() {
						location.reload();
					}, 1000);
				}
			},
			error: function(datahtml) {
				//console.log(datahtml);
			}
		});
	}
</script>

@endsection