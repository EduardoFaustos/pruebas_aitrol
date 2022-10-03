<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">
  
  <div class="card-body" style="padding: 1px,">   
    <div class="row">
      <input type="hidden" name="contador" id="contador" value="1">
      <div class="form-group">
        <div class="row">
          <div class="col-md-12" id="tabla_detalle">
            <table role="table" aria-busy="false" aria-colcount="4" class="table b-table">
              <thead role="rowgroup" class="">
                <tr role="row" class="">
                  <th width="20%" role="columnheader" scope="col" aria-colindex="1" class=""><div>{{trans('paso2.MEDICAMENTO')}}</div></th>
                  <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{trans('paso2.Cantidad')}}</div></th>
                  <th width="40%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
                  <th width="30%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
                </tr>
              </thead>
              <tbody role="rowgroup">
                @foreach($detalles as $detalle)
                @php $producto = Sis_medico\Producto_Medicina::where('id_medicina',$detalle->id_medicina)->first(); @endphp
                <tr role="row" class="">
                  <input type="hidden" name="d{{$detalle->id_medicina}}" value="{{$detalle->id_medicina}}">
                  @if($producto!=null)
                  <input type="hidden" name="p{{$producto->id}}" value="{{$producto->id}}">
                  @endif
                  <td aria-colindex="1" role="cell" class="">{{$detalle->nombre}}</td>
                  <td aria-colindex="2" role="cell" class="">{{$detalle->cantidad}}</td>
                  <td aria-colindex="3" role="cell" class="">
                    {{$detalle->dosis}}
                  </td>
                  <td aria-colindex="4" role="cell" class="">
                    @if($detalle->descargo)
                      <input id="serie{{$detalle->id}}" name="serie{{$detalle->id}}" type="text" class="form-control is-valid" onchange="descargar_xserie('{{$detalle->id}}', this);" value="{{$detalle->serie}}" disabled><br>
                      <span class="font-weight-bolder text-success">{{trans('paso2.Entregado')}}</span>
                    @else
                      @if(!is_null($producto)) <!-- BODEGA 13 ES HOSPITAL -->
                        @php $inventario = Sis_medico\InvInventario::where('id_producto',$producto->id_producto)->where('id_bodega',19)->first(); @endphp
                        @if(!is_null($inventario))
                          @if($inventario->existencia > 0)
                            <input id="serie{{$detalle->id}}" name="serie{{$detalle->id}}" type="text" class="form-control form-control-sm" onchange="descargar_xserie('{{$detalle->id}}', this);"><br>
                            <span class="font-weight-bolder text-success" id="sp_ex{{$detalle->id}}">{{$inventario->existencia}}  {{trans('paso2.EnStock')}} </span>
                          @else
                            <span class="font-weight-bolder text-danger">{{trans('paso2.NoseencuentraenStock.')}} </span>  
                          @endif  
                        @else
                          <span class="font-weight-bolder text-danger">{{trans('paso2.NoseencuentraenStock...')}}</span>   
                        @endif  
                      @else
                        <span class="font-weight-bolder text-danger">{{trans('paso2.NoseencuentraenStock...')}}</span>   
                      @endif
                    @endif     
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
         
          
        </div>
      </div>
    </div> 
  </div>

</div>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">

  function descargar_xserie(id, element) {
    console.log(id, element.value );
    $.ajax({
      type: "post",
      url: "{{route('hospitalizacion.descargo_enfermeria_detalle_store')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {id: id, serie: element.value},

      success: function(datahtml, data) {
        console.log(datahtml);
        alert(datahtml.msn);
        if(datahtml.estado=='E'){
          $('#serie'+id).val('');  
        }else{
          $('#serie'+id).attr('disabled','disabled'); 
          $('#sp_ex'+id).text('Entregado'); 
        }
      },
      error: function() {
        alert('error al cargar');
      }
    });

  }


</script>