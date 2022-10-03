<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modulo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="col-nmd-12">
            <div class="row">
                <div class="col-md-12">
                    <label>MODULO - @if(isset($module['original']['compra'])) {{$module['original']['compra']['module']}} @endif</label>
                </div>
                <div class="col-md-6">

                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script>
    $(function() {

        $('#fechacierre').datetimepicker({
            format: 'YYYY/MM/DD H:ss',
            defaultDate: '{{date("Y/m/d H:s")}}',
        });
    });
</script>