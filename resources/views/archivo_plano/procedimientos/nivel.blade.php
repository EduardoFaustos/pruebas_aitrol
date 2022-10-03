@extends('archivo_plano.procedimientos.base')
@section('action-content')

<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-6">
		          <h3 class="box-title" style="margin-top: 7px">Asignar Nivel a Procedimientos </h3>
		        </div>
		    </div>
		</div>
		<div class="content">
			<h3 class="box-title" style="margin-top: 7px">{{$procedimiento->descripcion}}</h3>
			<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
		      <div class="row">
		        <div class="table-responsive col-md-12">
		          <table id="frmpro" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
		          	<thead>
		          		<th>Nivel</th>
		          		<th>Acción</th>
		          	</thead>
		          	<tbody>
		          		@php
		          			$proced = Sis_medico\ApProcedimientoNivel::where('id_procedimiento', $id)->where('cod_conv','1')->first();
		          			$proced2 = Sis_medico\ApProcedimientoNivel::where('id_procedimiento', $id)->where('cod_conv','2')->first();
		          			$proced3 = Sis_medico\ApProcedimientoNivel::where('id_procedimiento', $id)->where('cod_conv','3')->first();
		          		@endphp
		          		<tr>
		          			<td>Nivel 1</td>
		          			<td>@if(isset($proced)) <a href="{{route('ap_procedimiento.actualiza_nivel',['id' =>$id, 'nivel' =>'1'])}}" class="btn btn-primary btn-xs">Actualizar</a> @else <a href="{{route('ap_procedimiento.item_nivel',['id' =>$id, 'nivel' =>'1'])}}" class="btn btn-primary btn-xs">Asignar Nivel</a> @endif </td>
		          		</tr>
		          		<tr>
		          			<td>Nivel 2</td>
		          			<td>@if(isset($proced2)) <a href="{{route('ap_procedimiento.actualiza_nivel',['id' =>$id, 'nivel' =>'2'])}}" class="btn btn-primary btn-xs">Actualizar</a> @else <a href="{{route('ap_procedimiento.item_nivel',['id' =>$id, 'nivel' =>'2'])}}" class="btn btn-primary btn-xs">Asignar Nivel</a> @endif </td>
		          		</tr>
		          		<tr>
		          			<td>Nivel 3</td>
		          			<td>@if(isset($proced3)) <a href="{{route('ap_procedimiento.actualiza_nivel',['id' =>$id, 'nivel' =>'1'])}}" class="btn btn-primary btn-xs">Actualizar</a> @else <a href="{{route('ap_procedimiento.item_nivel',['id' =>$id, 'nivel' =>'3'])}}" class="btn btn-primary btn-xs">Asignar Nivel</a> @endif </td>
		          		</tr>
		          	</tbody>
		          </table>
		      	</div>
		  	  </div>
			</div>
	
    	</div> 

</section>
@endsection