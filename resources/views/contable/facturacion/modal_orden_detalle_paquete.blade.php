<style type="text/css">
    
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

</style>
<div class="modal-content">
      <input  name="id_vent" id="id_venta" type="text" class="hidden" value="@if(!is_null($id)){{$id}}@endif">
      <div class="box-header">
            <br>
            <div class="col-md-9">
              <h3 class="box-title"><b>DETALLE PAQUETE</b></h3>
            </div>
            <div class="col-md-1 text-right">
            </div>
            <div class="col-md-2">
              <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
            </div>
      </div>
      <div style="text-align: left" class="modal-header">
      
        <div class="box-header">
          <h3 class="box-title"><b>Orden # {{$orden->id}} </b></h3>
        </div>
      </div>

      <div class="box-body">
        <div id="recarga_detalle_paquete">
        </div>    
      </div> 
    

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
      </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">


$(document).ready(function(){
    
    carga_tabla_detalle_paquete();

});


//Carga la Tabla de Producto Tarifario 
function carga_tabla_detalle_paquete()
{
 
  var id_venta = $("#id_venta").val();

  $.ajax({
        type:"GET",
        url:"{{route('recarga_orden_detalle_paquete.index')}}/"+id_venta,
        data: "",
        datatype: "html",
        success:function(data){
            $('#recarga_detalle_paquete').html(data);
        },
        error:function(){
           alert('error al cargar');
        }
  });

}
  
  $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : false,
    'info'        : false,
    'autoWidth'   : false
  })

</script>


