<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d");
?>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">
                   <span class="sradio">7</span>
                    <h4 class="card-title ml-25 colorbasic">
                        {{trans('pasos.ExamenFisicoyDiagnostico')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="form_paso7">
        @php 
            $form008 = $solicitud->form008->first(); 
        @endphp
        <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
        <div class="row" style="padding-top: 10px;">
            
            <div class="col-md-12">
                <b> 1. {{trans('pasos.VíaAéreaObstruida')}}</b>
            </div>
            <div class="col-md-12">
                <select class="form-control form-control-sm" name="via_aerea_obs" id="via_aerea_obs">
                    <option value="Libre"> {{trans('pasos.Libre')}} </option>
                    <option value="Obstruida">{{trans('pasos.Obstruida')}}</option>
                </select>
            </div>
            <div class="col-md-12">
                <b> 3.{{trans('pasos.Cabeza')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="cabeza" id="cabeza" value="{{$form008->cabeza}}">
            </div> 

            <div class="col-md-12">
                <b> 3.{{trans('pasos.Cuello')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="cuello" id="cuello" value="{{$form008->cuello}}">
            </div> 

            <div class="col-md-12">
                <b> 4.{{trans('pasos.Torax')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="torax" id="torax" value="{{$form008->torax}}">
            </div> 

            <div class="col-md-12">
                <b> 5.{{trans('pasos.Abdomen')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="abdomen" id="abdomen" value="{{$form008->abdomen}}">
            </div> 

            <div class="col-md-12">
                <b> 6.{{trans('pasos.Columna')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="columna" id="columna" value="{{$form008->columna}}">
            </div>

            <div class="col-md-12">
                <b> 7.{{trans('pasos.Pelvis')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="pelvis" id="pelvis" value="{{$form008->pelvis}}">
            </div>

            <div class="col-md-12">
                <b>8.{{trans('pasos.Extremidades')}}</b>
            </div>
            <div class="col-md-12">
                <input class="form-control" type="text" name="extremidades" id="extremidades" value="{{$form008->extremidades}}">
            </div>


            <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                <button class="btn btn-primary" onclick="guardar_paso7();" type="button"> <i class="fa fa-save"></i> </button>
            </div>
        </div>
        </form>
    </div>

</div>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
    function guardar_paso7() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.septimopaso_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_paso7").serialize(),
            
            success: function(datahtml, data) {
                $("#content").html(datahtml);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`); 
            },
            error: function() {
                alert('error al cargar');
            }
        });
           
    }
</script>