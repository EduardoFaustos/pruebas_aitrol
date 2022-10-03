<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <span class="sradio">10</span>
                </div>
                <div class="col-md-7">
                    <label style="color: white;" class="control_label">{{trans('paso2.DESCARGODEENFERMERIA')}}</label>
                </div>
                <!--div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_lab_publico();"><i class="fa fa-plus"></i> {{trans('Publico')}}</button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-info" onclick="agregar_lab_privado();"><i class="fa fa-plus"></i> {{trans('Privado')}}</button>
                </div-->

                <!--div class="col-md-2">
                    <button class="btn btn-primary btn-xs" type="button" > <i class="fa fa-remove"></i> </button>
                </div-->
            </div>


        </div>
    </div>
    <div class="card-body" style="padding: 0;">
    	<br>
    	@foreach($agenda as $value)
	   	    @php
	   	    	$recetas = $value->historia_clinica->recetas;
	   	   	@endphp
	   	   	@foreach($recetas as $receta)
		   	   	@php
		    		$xdoctor = null;
					if($receta->id_doctor_examinador != ""){
		               $xdoctor = $receta->doctor;
		            }
		            $fecha = substr($receta->created_at,0,10);
				@endphp
					
				<div class="col-md-12" style="padding: 0;">
					<div class="card-header bg bg-primary colorbasic">
						
		        		<div class="col-md-5">
		        			@if(!is_null($fecha))
	                            @php
		                         $dia =  Date('N',strtotime($fecha));
		                         $mes =  Date('n',strtotime($fecha));
		                        @endphp

		                        <b>
		                        @if($dia == '1') Lunes
	                             @elseif($dia == '2') Martes
	                             @elseif($dia == '3') Miércoles
	                             @elseif($dia == '4') Jueves
	                             @elseif($dia == '5') Viernes
	                             @elseif($dia == '6') Sábado
	                             @elseif($dia == '7') Domingo
	                            @endif
	                             {{substr($fecha,8,2)}} de
	                            @if($mes == '1') Enero
	                                 @elseif($mes == '2') Febrero
	                                 @elseif($mes == '3') Marzo
	                                 @elseif($mes == '4') Abril
	                                 @elseif($mes == '5') Mayo
	                                 @elseif($mes == '6') Junio
	                                 @elseif($mes == '7') Julio
	                                 @elseif($mes == '8') Agosto
	                                 @elseif($mes == '9') Septiembre
	                                 @elseif($mes == '10') Octubre
	                                 @elseif($mes == '11') Noviembre
	                                 @elseif($mes == '12') Diciembre
	                            @endif
	                            del {{substr($fecha,0,4)}}</b>
	                        @endif	
		        		</div>
		        		<div class="col-md-4">
		        			Dr (a):	@if(!is_null($xdoctor)) {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}@endif
		        		</div>	
		        		<div class="col-md-2">
		        			
	                        <button id="min{{$receta->id}}" type="button" class="btn btn-primary" onclick="ocultar_orden('{{$receta->id}}')" style="display: none">
	                        	<i class="fa fa-minus"></i>
	                        </button>
	                        	
	                        <button id="plus{{$receta->id}}" type="button" class="btn btn-primary" onclick="ver_receta('{{$receta->id}}')">
	                        	<i class="fa fa-plus"></i>
	                        </button>
		        		</div>
			        </div>	
			        <div class="card-body" id="receta{{$receta->id}}" style="padding: 0px;">
			        			          
			        </div>
			    </div>
			    <br>
			@endforeach    
		        
		@endforeach 
    	
	</div>		   
</div>

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>
<script type="text/javascript">
	
	function ver_receta(id){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/receta/descargo/enfermeria')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#receta"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();	
	}

	function ocultar_orden(id){

		$("#labs_detalle"+id).html("<br>");
		$("#min"+id).hide();
		$("#plus"+id).show();


	}	

	function agregar_lab_publico(){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('hospital.decimo_laboratorio_crear_pb',[ 'id' => $solicitud->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#content").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
			
	}

	function agregar_lab_privado(){

		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('decimopaso.laboratorio_orden_crear_part',[ 'id' => $solicitud->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#content").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});


	}

	function editar_labs(id){
		//alert("editar");
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/editar/pub')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#labs_detalle"+id).html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
		$("#plus"+id).hide();
		$("#min"+id).show();	
	}

</script>	
