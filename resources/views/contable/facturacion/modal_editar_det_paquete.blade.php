<style type="text/css">
    .modal-body .form-group {
        margin-bottom: 0px;
    }
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">EDITAR ORDEN DETALLE PAQUETE</h4>
</div>
<div class="modal-body">
    <div class="row" style="padding-left: 60px">
        <form id="form_det_paquete" method="post"> 
            <input type="hidden" name="id_tar_paq"  id="id_tar_paq"  value="{{$orde_det_paq->id}}">
            {{ csrf_field() }} 
            <div class="form-group col-md-6">
                <label for="cantidad" class="col-md-4 control-label">Cantidad:</label>
                <div class="col-md-8">
                    <input id="cantidad"  type="text" class="form-control"  name="cantidad" value="{{$orde_det_paq->cantidad}}" onkeypress="return isNumberKey(event)">
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="precio" class="col-md-4 control-label">Precio:</label>
                <div class="col-md-8">
                  <input id="precio"  type="text" class="form-control"  name="precio" value="{{$orde_det_paq->precio}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);">
                </div>
            </div>
        </form>
        <br><br>
        <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
            <center>    
                <div class="col-md-6 col-md-offset-2">
                    <div class="col-md-7">
                        <button type="button" class="btn btn-primary" onclick="actualiza_orden_detalle();"><span class="glyphicon glyphicon-floppy-disk">{{trans('contableM.actualizar')}}</span> </button>
                    </div>
                </div>
            </center>
        </div>
       <br><br>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script type="text/javascript">

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) {

        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){
            entry.value='0.00';
        }
        if (test < 0) {

            entry.value = '0.00';
        }

    }
    
    function actualiza_orden_detalle(){

        Swal.fire({
            title: '¿Desea Actualizar los datos?',
            text: `{{trans('contableM.norevertiraccion')}}!`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
        
        if(result.isConfirmed) {

            $.ajax({
                type: 'post',
                url:"{{ route('producto_detalle_paquete.update',['id_ord_deta_paq' => $orde_det_paq->id]) }}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#form_det_paquete").serialize(),
                success: function(data){
                    if(data == "ok"){
                        swal({
                            title: "Datos Guardados",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                        })
                        
                        $("#edit_orde_det_paq").modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                        carga_tabla_detalle_paquete();
                        //$("#tarifario_paquete").removeData('modal');
                    
                    };

                },
                error: function(data){
                    console.log(data);
        
                }
            });

        }

        })

    }

</script>


