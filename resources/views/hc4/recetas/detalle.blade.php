
<div class="has-error">
  <span class="help-block">
    <strong>{{$mensaje}}</strong>
  </span>
</div>
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  @if($detalles != '[]')
    <div class="table-responsive col-md-12" style="padding: 0;">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
        <thead>
          <tr role="row">
            <th width="25%" >Genérico</th>
            <th width="25%" >Medicina</th>
            <th width="5%" >Cantidad</th>
            <th width="25%" >Rp</th>
            <th width="40%" >Dosis</th>
            <th width="5%" >&nbsp;</th>
          </tr>
        </thead>
        <tbody>
        @foreach($detalles as $value)
          <tr role="row">
          	@php $genericos = $medicinas->where('id',$value->id_medicina)->first()->genericos; @endphp
            <td>@foreach($genericos as $gen) <span class="bg-blue" style="padding: 3px;border-radius: 10px;"><b>{{$gen->generico->nombre}}</b></span>&nbsp; @endforeach</td>
            <td>{{$value->nombre}}</td>
            <td><input type="number" onchange="act_detalle({{$value->id}});" id="cantidad{{$value->id}}" name="cantidad{{$value->id}}" style="width: 100%;" value="{{$value->cantidad}}"></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="detalle_cantidad{{$value->id}}" name="detalle_cantidad{{$value->id}}" style="width: 100%;">{{$value->detalle_cantidad}}</textarea></td>
            <td><textarea onchange="act_detalle({{$value->id}});" id="dosis{{$value->id}}" name="dosis{{$value->id}}" style="width: 100%;">{{$value->dosis}}</textarea></td>
            <td><button onclick="det_delete({{$value->id}})" class="btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></button></td>         
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

<script type="text/javascript">
	
	function act_detalle(id){
        
	    @foreach($detalles as $value)

	    	if({{$value->id}}==id){
		        $.ajax({
		            type: 'post',
		            url:"{{url('detalle_receta/detalle_editar')}}/"+{{$receta}}+"/"+{{$value->id}}, //receta.editar_detalle
		            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
		            datatype: 'json',
		            data: {'cantidad' : $("#cantidad{{$value->id}}").val(), 'dosis' : $("#dosis{{$value->id}}").val() },
		            success: function(data){
		                //console.log(data);
		                
		                
		            },
		            error: function(data){
		                    
		                }
		        }); 
		    }    

	    @endforeach    
    }
</script>