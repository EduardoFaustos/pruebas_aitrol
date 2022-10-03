<style type="text/css">
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }
</style>

@php
    $var_tiempo_endos  = rand(0,99999);
@endphp
<input type="hidden" name="contador{{$var_tiempo_endos}}" id="contador{{$var_tiempo_endos}}" value="0">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      </div>
    </div>
</div>
<div class="modal fade" id="cpre_eco{{$var_tiempo_endos}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      </div>
    </div>
</div>
<div class="row">
    @php
        $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\Agenda::where('id', $procedimiento->historia->id_agenda)->first()->fechaini));
    @endphp
    <input type="hidden" id="tipo_guardado{{$var_tiempo_endos}}" value="3">
    <form id="hc_protocolo{{$procedimiento->id}}" style="width: 100%;">
        <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
         <input type="hidden" name="tipo_procedimiento" value="{{$tipo}}">
        <input type="hidden" name="id_procedimiento" value="{{$procedimiento->id}}">
        <input type="hidden" name="id_protocolo" value="<?php if (!is_null($protocolo)): ?><?php echo e($protocolo->id); ?><?php else: ?><?php echo e('0'); ?><?php endif;?>">
        <input type="hidden" name="tipo" value="{{$tipo}}">
        <div class="col-12">&nbsp;</div>
        <div class="row">
            <div class="col-6">

                <div class="col-12">
                    <span style="font-family: 'Helvetica general';">Procedimiento</span>
                </div>
                <?php
$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
?>
                @if(!is_null($adicionales->last()))
                    @php
                        $mas = true;
                        $texto = "";
                        foreach($adicionales as $value2)
                        {
                            if($mas == true){
                             $texto = $texto.$value2->procedimiento->nombre  ;
                             $mas = false;
                             }
                            else{
                             $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                             }
                        }
                    @endphp
                    <div class="col-12">
                        <span>{{$texto}}
                        </span>
                    </div>
                @else
                    <div class="col-12">
                        @php  $procedimiento_completo = \Sis_medico\procedimiento_completo::find($procedimiento->id_procedimiento_completo);
                        @endphp
                        <span>@if(!is_null($procedimiento_completo)) {{$procedimiento_completo->nombre_general}} @endif</span>
                    </div>
                @endif
                <div class="col-12">

                    <select id="id_procedimiento{{$var_tiempo_endos}}" class="form-control input-sm select2_proc" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 95.5%; padding-left: 15px " autocomplete="off" >
                        @php
                            $aceptacion = 0;


                        @endphp
                          @foreach($px as $key => $value)
                              @php
                                  $validacion = \Sis_medico\Hc_Procedimiento_Final::where('id_procedimiento', $value->id)->where('id_hc_procedimientos', $procedimiento->id)->first();
                                  if($key == 0){
                                    $cantidad = \Sis_medico\Hc_Procedimiento_Final::where('id_procedimiento', $value->id)->count();
                                    if($cantidad >= 1){
                                        $valida_d = 1;
                                    }else{
                                        $valida_d = 0;
                                    }
                                  }


                                  $clase = 'c';
                                  if(!is_null($value->grupo_procedimiento)){
                                    $clase = $clase.$value->grupo_procedimiento->tipo_procedimiento;
                                  }
                                  if(!is_null($validacion)){
                                    $aceptacion++;
                                  }
                              @endphp
                              <option data-clase="{{$clase}}" class="{{$clase}}" @if(!is_null($validacion)) selected @else @if($valida_d == 1 && strlen($clase) > 1 ) disabled="disabled"  @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                          @endforeach
                    </select>
                    <br>
                    @if($fecha_r == date('Y-m-d') )
                    <button type="button" class="btn-primary btn" onclick="autoguardado({{$procedimiento->id}}, 1)"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp; Guardar</button>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="col-12" >
                    <!--<center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_proc_endos_plantilla', ['id' => $procedimiento->id.date('his') ])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">Agregar Plantilla
                            </a>
                        </div>
                    </center>-->
                    <center>
                        <div class="col-12" style="padding-top: 10px">
                            <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_cpre.eco_modal', ['hcid' => $protocolo->hcid])}}" style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%; color: white; background-color: green"  type="a" class="btn btn-primary">Agregar CPRE+ECO
                            </a>
                        </div>
                    </center>
                </div>
            </div>
        </div>
        <div id="select_proc" class="alert alert-danger alerta_correcto alert-dismissable col-10" role="alert" style="display:none; margin-left: 20px; font-size: 14px">
            <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
          Seleccione un Procedimiento Endoscopico
        </div>
        <br>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Plantilla de Procedimientos</span>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-7" style="width: 100%">
                    <select class="form-control select2_plantilla"  name="proc_com" style="width: 100%;">
                            <option value="">Todos</option>
                        @foreach($proc_completo as $value)
                            <option @if($value->id == $procedimiento_completo_plantilla) selected @endif value="{{$value->id}}">{{$value->nombre_general}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5" >
                    <a class="btn btn-info btn_ordenes" style="color: white; height: 100%; " onClick="cargar_plantilla('1',{{$procedimiento->id}}{{$var_tiempo_endos}}, {{$procedimiento->id}});">
                        <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                            <label style="font-size: 16px">Insertar Plantilla</label>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <br>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">Seguro</span>
            </div>
        <!-- DOCTOR TRAINING -->
        <!--VH. PREGUNTAR POR EL ESTADO ADMISIONADO -->
            @php
                $dr_training = Auth::user();
                $proc_training = Sis_medico\Hc_protocolo_training::where('id_hc_protocolo',$protocolo->id)->where('id_training',$dr_training->id)->first();
            @endphp
            @if($dr_training->training)
                <div class="col-6" >
                    <span style="font-family: 'Helvetica general';">Training </span>
                </div>
            @endif
        <!-- DOCTOR TRAINING -->
        </div>

        <div class="row" style="padding-left: 15px;">
            <div class="col-6">
                <span style="font-family: 'Helvetica general';">{{$hc_seguro->nombre}}</span>
            </div>
            <!-- DOCTOR TRAINING -->
            @if($dr_training->training)
            <div class="col-6">
                <input id="ch{{$protocolo->id}}" name="ch" type="checkbox" class="flat-orange" @if(!is_null($proc_training)) @if($proc_training->estado) checked @endif @endif> <span id="train" @if(!is_null($proc_training)) @if($proc_training->estado) class="text-red" @endif @else class="text-muted" @endif> Participe de este Procedimiento</span>
            </div>
            @endif
            <!-- DOCTOR TRAINING -->
        </div>
        <br>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Hallazgos</span>
        </div>
        <div class="col-12">
            <div id="thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->hallazgos ?><?php endif;?></div>
            <input type="hidden" name="hallazgos" id="hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>
        <div class="col-12">&nbsp;</div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">Conclusiones</span>
        </div>
        <div class="col-12">
            <div id="tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}" style="border: solid 1px;"><?php if (!is_null($protocolo)): ?><?php echo $protocolo->conclusion ?><?php endif;?></div>
            <input type="hidden" name="conclusion" id="conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}">
        </div>

        <div class="col-12">
            <span style="font-family: 'Helvetica general';">&nbsp;</span>
        </div>
        <div class="col-12">
            <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
        </div>
        <div class="col-4">
            <select name="id_doctor_examinador" onchange="autoguardado({{$procedimiento->id}}, 1)">
                @foreach($doctores as $value)
                    @if($value->training != 1)
                    <option
                    @if(!is_null($procedimiento->id_doctor_examinador))
                        @if($procedimiento->id_doctor_examinador == $value->id)
                            selected
                        @endif
                    @endif
                    value="{{$value->id}}"> {{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}
                    </option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="col-12" >
        <center>
        <div class="col-5" style="padding-top: 15px;text-align: center;">
            <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" onclick="guardar_procedimiento({{$procedimiento->id}})" ><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar
            </button>
        </div>
        </center>
        </div>
    </form>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">


    $(document).ready(function(){

      $('.select2_plantilla').select2({
        tags: false
      });


      $(".breadcrumb").append('<li class="active">Procedimientos</li>');

    });




     tinymce.init({
        selector: '#thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",
        toolbar: [
          'undo redo | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
        ],
        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());


            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}}, 1);
                @endif
            });

            @if($fecha_r == date('Y-m-d') )
                editor.on('keyup', function (e) {
                    contador = parseInt($('#contador{{$var_tiempo_endos}}').val());
                    if(contador <= 19){
                        contador++
                        $('#contador{{$var_tiempo_endos}}').val(contador);
                    }else{
                        var ed = tinyMCE.get('thallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                        $("#hallazgos<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                        autoguardado({{$procedimiento->id}}), 1;
                        $('#contador{{$var_tiempo_endos}}').val(0);
                    }
                });
            @endif
          }
    });

    tinymce.init({
        selector: '#tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        <?php if (is_null($protocolo)): ?>
        readonly: 1,
        <?php else: ?>
        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());

            });
        },
        <?php endif;?>


        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                $("#conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}}, 1);
                @endif
            });
            @if($fecha_r == date('Y-m-d') )
            editor.on('keyup', function (e) {
                contador = parseInt($('#contador{{$var_tiempo_endos}}').val());
                if(contador <= 19){
                    contador++
                    $('#contador{{$var_tiempo_endos}}').val(contador);
                }else{
                    var ed = tinyMCE.get('tconclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}');
                    $("#conclusion<?php echo e($procedimiento->id); ?>{{$var_tiempo_endos}}").val(ed.getContent());
                    autoguardado({{$procedimiento->id}}, 1);
                    $('#contador{{$var_tiempo_endos}}').val(0);
                }
            });
            @endif
          }
    });
    function guardar_procedimiento(id){
        var jprocedimientos = $('#id_procedimiento{{$var_tiempo_endos}}').val();
        //console.log (jprocedimientos);

        if (jprocedimientos != ""){
            var entra = id;
                $.ajax({
                type: "POST",
                url: "<?php echo e(route('guardar.procedimiento_endoscopico')); ?>",
                data: $("#hc_protocolo"+id).serialize(),
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "html",
                success: function(datahtml, entra){

                    if(datahtml == 'No puede Guardar sin Procedimiento'){
                        alert(datahtml);

                    }else if(datahtml == 'Seleccione un procedimiento principal.'){
                        alert(datahtml);

                    }else{
                        $("#procedimiento"+id).html(datahtml);
                    }
                    //console.log(datahtml);
                },
                error:  function(){
                    alert('error al cargar');
                }
                });
        }else {
            $("#select_proc").fadeIn(1000);
            $("#select_proc").fadeOut(3000);
            //alert("Seleccione un Procedimiento");
        }
    };

</script>
<script type="text/javascript">

    @php $dr_training = Auth::user(); @endphp

    @if($dr_training->training)

        $('#ch{{$protocolo->id}}').on('ifChecked', function(event){

            $.ajax({
              type: 'get',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '1']) }}",
              datatype: 'json',
              success: function(data_cpre){
                $('#train').removeClass('text-muted');
                $('#train').addClass('text-red');

              },
              error: function(data_cpre){
                 //console.log(data);
              }
            });

        });

        $('#ch{{$protocolo->id}}').on('ifUnchecked', function(event){

            $.ajax({
              type: 'get',
              url:"{{ route('protocolo_training.crear_training', ['training' => $dr_training->id,'protocolo' => $protocolo->id, 'n' => '0']) }}",


              datatype: 'json',

              success: function(data_cpre){
                $('#train').removeClass('text-red');
                $('#train').addClass('text-muted');

              },
              error: function(data_cpre){
                 //console.log(data);
              }
            });

        });

    @endif

    $('.c{{$tipo}}').removeAttr('disabled');

    $('#id_procedimiento{{$var_tiempo_endos}}').select2({
        tags: false,
    });


    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
    });

    function actualiza_select{{$var_tiempo_endos}}(){
        //alert("actualiza");
        var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
        var longitud =  seleccionados.length;

        console.log(seleccionados);


      if(longitud>=1){
        //alert("entro");
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');
      }

      seleccionados.each(function( index ) {
          $(this).removeAttr('disabled');
      });

          $('#id_procedimiento{{$var_tiempo_endos}}').select2({
                tags: false,
            });
    }

    function quita_select{{$var_tiempo_endos}}(){
        //alert("quita");
        var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
        var longitud =  seleccionados.length;

      if(longitud>1){

        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });

        $('#id_procedimiento{{$var_tiempo_endos}}').select2({
            tags: false,
        });


    }

    function cargar_plantilla(actualiza, id, id_proc){

        $.ajax({
          type: 'post',
          url:"{{route('procedimiento.tecnica_plantilla')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#hc_protocolo"+id_proc).serialize(),
          success: function(data){
            if(data.tecnica_quirurgica!=null){
                var tecnica = data.tecnica_quirurgica;
            }else{
                alert("No Existe Plantilla Precargada !!");
                var tecnica = "";
            }

            if(actualiza=='1'){
                anterior1=  $('#hallazgos'+id).val();
                anterior2=  tinyMCE.get('thallazgos'+id).getContent();
                $('#hallazgos'+id).val(anterior1+tecnica);
                tinyMCE.get('thallazgos'+id).setContent(anterior2+tecnica);
                @if($fecha_r == date('Y-m-d') )
                    autoguardado({{$procedimiento->id}}, 1);
                @endif
            }
          },
          error: function(data){
             console.log(data);
          }
        });

    //guardar();
    }

    $("#id_procedimiento{{$var_tiempo_endos}}").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");

        actualiza_select{{$var_tiempo_endos}}();
    });

    $("#id_procedimiento{{$var_tiempo_endos}}").on("select2:unselecting", function (evt) {
        quita_select{{$var_tiempo_endos}}();
    });


    @if($aceptacion > 0 )
        $('#id_procedimiento{{$var_tiempo_endos}}').select2({
                tags: false,
            });
    @endif

    @if($fecha_r == date('Y-m-d') )

        function autoguardado(id, tipo){
            var seleccionados =  $('#id_procedimiento{{$var_tiempo_endos}}').find(':selected');
            var longitud =  parseInt(seleccionados.length);
            //console.log('entra');
            //console.log(tipo);
            //console.log($("#hc_protocolo"+id).serialize());return 0;
            var tipo_2 = parseInt($('#tipo_guardado{{$var_tiempo_endos}}').val());
            if(tipo == 1){
                if(longitud>0){
                    var entra = id;
                        $.ajax({
                        type: "POST",
                        url: "{{route('guardar.procedimiento_endoscopico_autoguardado')}}",
                        data: $("#hc_protocolo"+id).serialize(),
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: "html",
                        success: function(datahtml){
                            console.log(datahtml);
                            if(datahtml == "ok"){
                                $("#alerta_datos").fadeIn(1000);
                                $("#alerta_datos").fadeOut(3000);
                            }else{
                                alert(datahtml);
                            }

                        },
                        error:  function(){
                            alert('error al cargar');
                        }
                        });
                }else {
                    //$("#alerta_datos").fadeIn(1000);
                    //$("#alerta_datos").fadeOut(3000);
                    //console.log('parsea');
                    alert("Debe Seleccionar un procedimiento Principal.");
                }

            }else{
                if(tipo != parseInt(tipo_2)){
                    if(longitud>0){
                        var entra = id;
                            $.ajax({
                            type: "POST",
                            url: "{{route('guardar.procedimiento_endoscopico_autoguardado')}}",
                            data: $("#hc_protocolo"+id).serialize(),
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: "html",
                            success: function(datahtml){
                                console.log(datahtml);
                                if(datahtml == "ok"){
                                    $("#alerta_datos").fadeIn(1000);
                                    $("#alerta_datos").fadeOut(3000);
                                }else{
                                    alert(datahtml);
                                }

                            },
                            error:  function(){
                                alert('error al cargar');
                            }
                            });
                    }else {
                        //$("#alerta_datos").fadeIn(1000);
                        //$("#alerta_datos").fadeOut(3000);
                        alert("Debe Seleccionar un procedimiento Principal");
                    }
                }
            }
            $('#tipo_guardado{{$var_tiempo_endos}}').val(tipo);

        }

    @else
        function autoguardado(id){

        }
    @endif
</script>
