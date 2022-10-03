<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">{{trans('procedimientodr.ACTUALIZARPROCEDIMIENTO')}}</h4>
    <h4 class="modal-title" style="text-align: center;"><b>{{trans('procedimientodr.PACIENTE')}}:</b> {{$pentax->cedula}} {{$pentax->nombre1}} {{$pentax->nombre2}} {{$pentax->apellido1}} {{$pentax->apellido2}}</h4>
    <div style="text-align: right;"><span class="label label-primary" style="font-size: 100%;">@if($estado=='1') <b>{{trans('procedimientodr.PREPARACIÓN')}}</b> @endif
            @if($estado=='-1') <b>{{trans('procedimientodr.PREADMISIÓN')}}</b> @endif
            @if($estado=='0') <b>{{trans('procedimientodr.ENESPERA')}}</b> @endif
            @if($estado=='2') <b>{{trans('procedimientodr.ENPROCEDIMIENTO')}}</b> @endif
            @if($estado=='3') <b>{{trans('procedimientodr.RECUPERACIÓN')}}</b> @endif
            @if($estado=='4') <b>{{trans('procedimientodr.ALTA')}}</b> @endif
            @if($estado=='5') <b>{{trans('procedimientodr.SUSPENDER')}}</b> @endif
        </span></div>
</div>

<div class="modal-body">

    @if($estado=='4')

    @php
    $mensaje_pre = "";
    $class_pre = "";
    if($pre_post>0){
    if(is_null($ex_pre)){
    $class_pre="callout callout-danger";
    $mensaje_pre="GENERAR ORDEN EXAMEN PRE-OPERATORIO";
    }else{
    if($ex_pre->realizado=='0'){
    $class_pre="callout callout-warning";
    $mensaje_pre="EXAMEN PRE-OPERATORIO EN PROCESO";
    }else{
    $class_pre="callout callout-success";
    $mensaje_pre="EXAMEN PRE-OPERATORIO REALIZADO";
    }
    }
    if(is_null($ex_post)){
    $class_post="callout callout-danger";
    $mensaje_post="GENERAR ORDEN EXAMEN POST-OPERATORIO";
    }else{
    if($ex_post->realizado=='0'){
    $class_post="callout callout-warning";
    $mensaje_post="EXAMEN POST-OPERATORIO EN PROCESO";
    }else{
    $class_post="callout callout-success";
    $mensaje_post="EXAMEN POST-OPERATORIO REALIZADO";
    }
    }
    }
    @endphp
    <div class="col-md-12">
        @if($pre_post>0)
        @if($pre_post=='2')
        <div class="{{$class_pre}}" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
            <p> {{$mensaje_pre}}</p>
        </div>
        <div class="{{$class_post}}" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
            <p> {{$mensaje_post}}</p>
        </div>
        @endif
        @if($pre_post=='1')
        <div class="{{$class_pre}}" style="padding-top: 2px;padding-bottom: 2px;margin-bottom: 5px;">
            <p> {{$mensaje_pre}}</p>
        </div>
        @endif
        @endif
    </div>

    @endif

    <form method="POST" action="#" id="form">

        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="estado" value="{{$estado}}">
        <input type="hidden" name="hora" value="{{$hora}}">
        @if($pentax->estado_pentax=='0' && $estado=='1' && strtotime(date('Y/m/d H:i:s')) > strtotime($pentax->fechaini))
        <div class="callout callout-danger">
            {{trans('procedimientodr.ProcedimientoDEMORADOAdmisiónRealizada')}}: {{$pentax->created_at}}
        </div>
        @endif

        <div class="row">
            <div class="form-group col-md-12 cl_proc">
                <label for="id_procedimiento" class="col-md-12 control-label">{{trans('procedimientodr.Procedimientos')}}</label>
                <select id="id_procedimiento" class="form-control select2" multiple="multiple" name="proc[]" data-placeholder="Seleccione los Procedimientos" style="width: 100%;">
                    @foreach($pentax_procs as $pentax_proc)
                    <option selected value="{{$pentax_proc->id_procedimiento}}">{{$procedimientos->find($pentax_proc->id_procedimiento)->nombre}}</option>
                    @endforeach
                    @foreach($procedimientos as $procedimiento)
                    @if(is_null($pentax_procs->where('id_procedimiento',$procedimiento->id)->first()))
                    <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                    @endif
                    @endforeach
                </select>
                <span class="help-block">
                    <strong id="str_proc"></strong>
                </span>
            </div>

            <!--div class="form-group col-md-6">
            <label for="id_sala" class="col-md-12 control-label">Salas</label>
            <select class="form-control" id="id_sala" name="id_sala"  >
                @foreach($salas as $sala)
                <option @if($pentax->id_sala==$sala->id) selected @endif value="{{$sala->id}}">{{$sala->nombre_sala}}</option>
                @endforeach
            </select>  
        </div-->

            <div class="form-group col-md-12 cl_doctor">
                <label for="id_doctor1" class="col-md-12 control-label">{{trans('procedimientodr.Doctor')}}</label>
                <select class="form-control" id="id_doctor1" name="id_doctor1">
                    @foreach($doctores as $doctor)
                    <option @if($pentax->id_doctor1==$doctor->id) selected @endif value="{{$doctor->id}}">{{$doctor->nombre1}} {{$doctor->apellido1}}</option>
                    @endforeach
                </select>
                <span class="help-block">
                    <strong id="str_doctor"></strong>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label for="id_doctor2" class="col-md-12 control-label">{{trans('procedimientodr.Asistente1')}}</label>
                <select class="form-control" id="id_doctor2" name="id_doctor2">
                    <option></option>
                    @foreach($doctores as $doctor)
                    <option @if($pentax->id_doctor2==$doctor->id) selected @endif value="{{$doctor->id}}">Dr(a). {{$doctor->nombre1}} {{$doctor->apellido1}}</option>
                    @endforeach
                    @foreach($enfermeros as $enfermero)
                    <option @if($pentax->id_doctor2==$enfermero->id) selected @endif value="{{$enfermero->id}}">Enf. {{$enfermero->nombre1}} {{$enfermero->apellido1}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="id_doctor3" class="col-md-12 control-label">{{trans('procedimientodr.Asistente2')}}</label>
                <select class="form-control" id="id_doctor3" name="id_doctor3">
                    <option></option>
                    @foreach($doctores as $doctor)
                    <option @if($pentax->id_doctor3==$doctor->id) selected @endif value="{{$doctor->id}}">Dr(a). {{$doctor->nombre1}} {{$doctor->apellido1}}</option>
                    @endforeach
                    @foreach($enfermeros as $enfermero)
                    <option @if($pentax->id_doctor3==$enfermero->id) selected @endif value="{{$enfermero->id}}">Enf. {{$enfermero->nombre1}} {{$enfermero->apellido1}}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group col-md-12 cl_obs">
                <label for="observacion" class="col-md-12 control-label">{{trans('procedimientodr.Observación')}}</label>
                <input type="text" class="form-control" id="observacion" name="observacion" maxlength="100">
                <span class="help-block">
                    <strong id="str_obs"></strong>
                </span>
            </div>

            <div class="form-group col-md-6">
                <label for="id_seguro" class="col-md-12 control-label">{{trans('procedimientodr.Seguro')}}</label>
                <select class="form-control" id="id_seguro" name="id_seguro" onchange="crear_select()">
                    @foreach($seguros as $seguro)
                    <option @if($pentax->id_seguro==$seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6 cl_sub">
                <label for="id_subseguro" class="col-md-12 control-label">{{trans('procedimientodr.SubSeguro')}}</label>
                <select class="form-control" id="id_subseguro" name="id_subseguro">

                </select>
                <span class="help-block">
                    <strong id="str_sub"></strong>
                </span>
            </div>
        </div>



        <!--Botón para abrir la ventana modal de editar -->
        <a href="#" id="confirme" class="btn btn-primary">{{trans('procedimientodr.Confirme')}}</a>

</div>
</form>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('procedimientodr.Cerrar')}}</button>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        //Initialize Select2 Elements
        $('.select2').select2({
            tags: false
        });
        $("select").on("select2:select", function(evt) {
            var element = evt.params.data.element;
            //console.log(element);
            var $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });

        crear_select();


    });

    function crear_select() {

        var js_seguro = document.getElementById("id_seguro").value;
        var sel_sub = document.getElementById("id_subseguro");
        $('option[class^="cl_sub"]').remove();
        var option2 = document.createElement("option");
        option2.value = "";
        option2.text = "Seleccione..";
        option2.setAttribute("class", "cl_sub");
        sel_sub.add(option2);

        if (js_seguro != "") {

            @foreach($subseguros as $subseguro)
            @if($pentax -> parentesco == "Principal" && $subseguro -> principal == '1')
            if (js_seguro == {
                    {
                        $subseguro - > id_seguro
                    }
                }) {

                var option2 = document.createElement("option");
                option2.value = "{{$subseguro->id}}";
                option2.text = "{{$subseguro->nombre}}";
                option2.setAttribute("class", "cl_sub");
                sel_sub.add(option2);
                @if($pentax - > id_subseguro == $subseguro - > id)
                option2.selected = true;
                @endif
            }
            @endif
            @if($pentax - > parentesco != "Principal" && $subseguro - > principal == '0')
            if (js_seguro == {
                    {
                        $subseguro - > id_seguro
                    }
                }) {

                var option2 = document.createElement("option");
                option2.value = "{{$subseguro->id}}";
                option2.text = "{{$subseguro->nombre}}";
                option2.setAttribute("class", "cl_sub");
                sel_sub.add(option2);
                @if($pentax - > id_subseguro == $subseguro - > id)
                option2.selected = true;
                @endif
            }
            @endif
            @endforeach
        }
    }

    $('#confirme').click(function(event) {

        $.ajax({
            type: 'get',
            url: '{{route('procedimientos_dr.actualiza_dr')}}',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },

            datatype: 'json',
            data: $("#form").serialize(),
            success: function(data) {
                //alert(data);
                if (data == 'error') {
                    alert("NO SE PUEDE DAR DE ALTA A PACIENTE, TIENE PENDIENTE EXÁMENES");
                }

                var fecha = document.getElementById('fecha').value;
                var unix = Math.round(new Date(fecha).getTime() / 1000);
                location.href = '{{route('
                procedimientos_dr.procedimientos_dr ')}}/buscar/' + unix;
                //location.href='{{route('pentax.pentax')}}';
            },
            error: function(data) {

                if (data.responseJSON.id_doctor1 != null) {
                    $(".cl_doctor").addClass("has-error");
                    $('#str_doctor').empty().html(data.responseJSON.id_doctor1);
                }
                if (data.responseJSON.proc != null) {
                    $(".cl_proc").addClass("has-error");
                    $('#str_proc').empty().html(data.responseJSON.proc);
                }
                if (data.responseJSON.observacion != null) {
                    $(".cl_obs").addClass("has-error");
                    $('#str_obs').empty().html(data.responseJSON.observacion);
                }
                if (data.responseJSON.id_subseguro != null) {
                    $(".cl_sub").addClass("has-error");
                    $('#str_sub').empty().html(data.responseJSON.id_subseguro);
                }
            }
        })


    });
</script>