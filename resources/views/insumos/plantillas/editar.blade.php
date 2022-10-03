@extends('insumos.plantillas.base')
@section('action-content')

<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 300px;
        width: 25%;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000px;
        font-size: 11px;
        float: left;
        display: none;
        min-width: 160px;
        _width: 140px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
</style>
    <section class="content">
      <div class="box">
  		<div class="box-header">
	    	<div class="row">
		        <div class="col-sm-6">
		          <h3 class="box-title" style="margin-top: 7px">{{trans('winsumos.crear_plantillas')}}</h3>
		        </div><br><hr>
	    	</div>
  		</div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="POST" id="form_plantilla">
            {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_plantilla" class="form-control" value="{{$id}}">
              <div class="col-md-3">
                <label>{{trans('winsumos.codigo')}}</label><br>
                <input type="text" name="codigo" placeholder="{{trans('winsumos.codigo')}}"  class="form-control" value="{{$plantilla->codigo}}">
              </div>
              <div class="col-md-4">
                <label>{{trans('winsumos.nombre')}}</label><br>
                <input type="text" name="nombre" placeholder="{{trans('winsumos.nombre')}}"  class="form-control" value="{{$plantilla->nombre}}" style="text-transform: uppercase">
              </div>
              <div class="col-md-2">
                <label>{{trans('winsumos.estado')}}</label><br>
                <select name="estado" class="form-control">
                  <option @if($plantilla->estado == 1) selected @endif value="1">{{trans('winsumos.activo')}}</option>
                  <option @if($plantilla->estado == 0) selected @endif value="0">{{trans('winsumos.inactivo')}}</option>
                </select>
              </div>
				<br>
				<div class="col-md-12"><br>
				<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i>{{trans('winsumos.items_plantilla')}}</h4><br>
     			<br/>

                <input name='contador_items' id='contador_items' type='hidden' value="0">
                <div class="col-md-12 table-responsive">
                    <table id="items" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                        <thead class="thead-dark">
                            <tr class='well-darks'>
                                <th width="50%" tabindex="0">{{trans('winsumos.items')}}</th>
                                <th width="20%" tabindex="0">{{trans('winsumos.Tipo')}}</th>
                                <th width="10%" tabindex="0">{{trans('winsumos.cantidad')}}</th>
                                <th width="10%" tabindex="0">
                                    <button type="button" class="btn btn-success btn-gray agregar_items">
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plantillas_items as $value)
                            <tr class="fila-fija">
                                <td>
                                    <input type="hidden" name="id_item[]" class="id_item" value="{{$value->id_producto}}" /><input required name="item_id[]" id="item_id" onchange="agregar_item(this)" class=" buscador_items form-control"  style="height:25px;width:90%;" value="{{$value->nom_prod}}" name="producto[]"/>
                                </td>
                                <td>
                                    <select class="form-control" name="tipo[]"  style="height:25px;width:75%;text-transform:uppercase;">
                                        <option @if($value->tipo == 1) selected @endif value="1">{{trans('winsumos.enfermeria')}}</option>
                                        <option @if($value->tipo == 2) selected @endif value="2">{{trans('winsumos.anestesiologo')}}</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="item_cant[]" id="item_cant" class="form-control" style="height:25px;width:75%;" value="{{$value->cantidad}}">
                                    
                                </td>
                                <td> <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> 

				</div>
				<br>

              	<div class="col-md-6"><br></div><div id="proced_list"></div>
              <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
                <!--button type="submit" class="btn btn-success">
                    Actualizar
                </button-->

                <a onclick="actualizar_plantilla('{{$plantilla->id}}');" type="button" class="btn btn-success" id="btn_guardar">{{trans('winsumos.actualizar')}}</a>
              </div>
            
          </form>
      
    </div>
  <!-- /.box-body -->
</div>
</section>
    <!-- /.content -->
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script>

$('.agregar_items').on('click', function() {
            agregar_items();
        });

        function agregar_items() {
            var id = document.getElementById('contador_items').value;
            var tr = `<tr class="columnas"> 
                            <td>
                                <input type="hidden" name="id_item[]" id="id_item${id}" class="iditem">
                                <input required name="item_id[]" id="item_id" onchange="agregar_item(this)" class="form-control buscador_items " style="height:25px;width:90%;" name="producto[]">
                            </td>

                            <td>
                                <select class="form-control" name="tipo[]"  style="height:25px;width:75%;text-transform:uppercase;">
                                    <option value="1">{{trans('winsumos.enfermeria')}}</option>
                                    <option value="2">{{trans('winsumos.anestesiologo')}}</option>
                                </select>
                            </td>

                            <td>
                                <input type="number" name="item_cant[]" id="item_cant" class="form-control cant" style="height:25px;width:75%;" value="0">
                            </td>

                            <td>
                                <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                            </td>                    
                        </tr> `;
            $('#items').append(tr);
            /*var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_items").value = parseInt(sum);*/
            
            var ids = id;

            $(".buscador_items").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('planilla.buscar_item_producto')}}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },

                minLength: 2,
                select: function(data, ui){
                    //alert("asd");
                    console.log("select",ui.item.value1);
                    $('#id_item'+ids).val(ui.item.value1);                    
                }
            });
            id++;
            document.getElementById('contador_items').value= id;
        }

        $(document).on("click", ".remover", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function deleteRow(btn) {
            var row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
            //calcular();
        }

        function agregar_item(e) {

            $.ajax({
                type: 'post',
                url: "{{route('planilla.find_id_item')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data) {
                    if (data[0] != "") {
                        $(e).parent().parent().find('.id_item').val(data[0].id);
                        
                    }
                    if (data.value != 'no resultados') {
                        console.log(data);
                    } else {}
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function alertas(icon, title, text) {
            Swal.fire({
                icon: `${icon}`,
                title: `${title}`,
                html: `${text}`,
            })
        }

        function actualizar_plantilla(id){
            $('#btn_guardar').attr('disabled',true);
            $.ajax({
                type: 'post',
                url: "{{url('insumos/plantillas/actualizar')}}/"+id,
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_plantilla").serialize(),
                success: function(data) {

                    console.log(data);

                    if (data == "ok") {
                        alertas('success', "{{trans('winsumos.exito')}}", "{{trans('winsumos.guardado_exito')}}");
                        location.href = "{{route('plantilla.index')}}"; 
                    }else{
                        $('#btn_guardar').attr('disabled',false);
                    }
                    
                },
                error: function(data) {
                    alertas('error', "{{trans('winsumos.error')}}", "{{trans('winsumos.error')}}");
                    console.log(data);
                }
            });
        }
</script>

@endsection