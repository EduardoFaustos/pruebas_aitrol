<style>
    .centrar {
        text-align: center;
    }
</style>
<div class="modal-content" style="margin-top: 10%;">
    <div class="modal-header">
        <h5 class="modal-title">Pendientes de Pago</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">

                <form id="formulario">
                    {{ csrf_field() }}
                    <div class="col-md-2">
                        <label for="cedula">Cedula</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="cedula" name="cedula">
                    </div>

                    <div class="col-md-2">
                        <label for="nombres">Nombres</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="nombres" name="nombres">
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" onclick="tabla()">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-12">

                <div id="tabla">

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function tabla() {
        $.ajax({
            url: "{{route('ordenes.modal_buscar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#formulario').serialize(),
            type: 'POST',
            dataType: 'html',
            success: function(datahtml, data) {
                $("#tabla").html(datahtml);

            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }
</script>