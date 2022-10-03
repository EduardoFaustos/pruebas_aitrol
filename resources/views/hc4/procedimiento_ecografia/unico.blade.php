
<div class="row">
		<div class="col-12">&nbsp;</div> 
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Procedimiento</span>
		</div>
		<?php 
			$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
		?>
		@if(!is_null($adicionales->last()))
		@php 
			$mas = true; 
			$texto = "";

			foreach($adicionales as $value2)
			{
				if($mas == true){
				 $texto = $texto.$value2->procedimiento->nombre  ;
				 $mas = false; 
				 }
				else{
			  	 $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
			  	 }					  						
			}
		@endphp
		<div class="col-12">
			<span>{{$texto}}</span>
		</div>
		@else
		<div class="col-12">
			@php  $procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
			@endphp
			<span>@if(!is_null($procedimiento_completo)){{$procedimiento_completo->nombre_general}}@endif</span>
		</div>
		@endif
		<div class="col-12">&nbsp;</div>


		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Seguro</span>
		</div>
		<div class="col-12">
			<span><?php echo strip_tags($hc_seguro->nombre); ?></span>
		</div>
		<br>

		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Hallazgos</span>
		</div>
		<div class="col-12">
			<span><?php echo strip_tags($protocolo->hallazgos); ?></span>
		</div>
		<div class="col-12">&nbsp;</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">Conclusiones</span>
		</div>					  						
		<div class="col-12">
			<span><?php if(!is_null($protocolo->conclusion)){echo strip_tags($protocolo->conclusion);}else{ echo ' &nbsp;&nbsp;';} ?></span>
		</div>

		<div class="col-12">
			<span style="font-family: 'Helvetica general';">&nbsp;</span>
		</div>
		<div class="col-12">
			<span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
		</div>
		<div class="col-4">
			<span>Dr. {{$procedimiento->doctor->nombre1}} {{$procedimiento->doctor->apellido1}}</span>
		</div>
</div>