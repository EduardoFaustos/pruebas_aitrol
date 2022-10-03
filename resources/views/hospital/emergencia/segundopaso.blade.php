<?php
date_default_timezone_set('America/Guayaquil');
$fecha = date("Y-m-d H:m:i");
?>
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="d-flex align-items-center col-md-12">

                   <span class="sradio">2</span>
                    <h4 class="card-title ml-25 colorbasic">
                        {{ trans('pasos.atencion') }}
                    </h4>

                </div>
            </div>


        </div>
    </div>
    <div class="card-body">
        <form id="form_paso2">
            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
            <div class="row" style="padding-top: 10px;">
            
                <div class="col-md-4">
                    <label class="col-form-label-sm"><b>{{ trans('pasos.Fecha') }}</b></label>
                     
                    <div class="input-group date">
                      <input type="text"  data-input="true" class="form-control input-sm flatpickr-date-time active" name="fecha" id="fecha" value="@if($solicitud->fecha_ingreso==null){{$fecha}}@else{{$solicitud->fecha_ingreso}}@endif" autocomplete="off">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle"></i>
                      </div>   
                    </div>
                </div>
                @php $paciente = $solicitud->paciente; @endphp
                <div class="col-md-4">
                    <label class="col-form-label-sm"><b>{{ trans('pasos.Gruposanguineo') }}</b></label>
            
                    <select class="form-control" name="grupo_sanguineo" id="grupo_sanguineo">
                        <option value="">Seleccione ...</option>
                        <option @if($paciente->gruposanguineo=='O+') selected @endif value="O+">O+</option>
                        <option @if($paciente->gruposanguineo=='O-') selected @endif value="O-">O-</option>
                        <option @if($paciente->gruposanguineo=='A+') selected @endif value="A+">A+</option>
                        <option @if($paciente->gruposanguineo=='A-') selected @endif value="A-">A-</option>
                        <option @if($paciente->gruposanguineo=='AB+') selected @endif value="AB+">AB+</option>
                        <option @if($paciente->gruposanguineo=='AB-') selected @endif value="AB-">AB-</option>
                    </select>
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12">
                    <label class="col-form-label-sm"><b>{{ trans('pasos.Causa') }}</b></label>
                </div>
                @php $form008 = $solicitud->form008->first(); @endphp
                <div class="col-md-12">    
                    <input type="checkbox" name="trauma" value="1" @if($form008->trauma=='1') checked @endif id="trauma"> <label>{{ trans('pasos.Trauma') }}</label><br>
                    <input type="checkbox" name="c_clinica" value="1" @if($form008->c_clinica=='1') checked @endif id="c_clinica"> <label>{{ trans('pasos.CausaClinica') }}</label><br>
                    <input type="checkbox" name="c_obstetrica" value="1" @if($form008->c_obstetrica=='1') checked @endif id="c_obstetrica"> <label>{{ trans('pasos.CausaG.Obstetrica') }}</label><br>
                    <input type="checkbox" name="c_quirurgica" value="1" @if($form008->c_quirurgica=='1') checked @endif id="c_quirurgica"> <label>{{ trans('pasos.CausaQuirurgica') }}</label><br>
                    <input type="checkbox" name="not_policia" value="1" @if($form008->n_policia=='1') checked @endif id="not_policia"> <label>{{ trans('pasos.NotificacionPolicia') }}</label><br>
                    <input type="checkbox" name="otros" value="1" @if($form008->o_motivo) checked @endif id="otros"> <label>{{ trans('pasos.OtroMotivo') }}</label>
                    
                </div>
                <div class="col-md-6">
                    <input class="form-control" type="text" name="motivo" id="motivo" value="{{$form008->motivo}}">
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                
                <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                    <button class="btn btn-primary" onclick="guardar_paso2();" type="button"> <i class="fa fa-save"></i> </button>
                </div>
               
            </div>
        </form>     
    </div>

</div>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
<script type="text/javascript">
    function guardar_paso2() {

        $.ajax({
            type: "post",
            url: "{{route('hospital.segundopaso_store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_paso2").serialize(),
            
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