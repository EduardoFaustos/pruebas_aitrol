
@extends('agenda.base')

@section('action-content')
<style type="text/css">
    @foreach($calendarios as $valores)
        @foreach($valores as $value)
        .a{{$value->id}}
        {
            @if($value->paciente_dr == 0)
                @if($value->estado_cita == 0)
                    color: black;
                @else
                    color: {{ $value->color}};

                @endif
            @endif
            @if($value->paciente_dr == 1)
                @if($value->estado_cita == 0)
                    color: black;
                @else
                    color: red;
                @endif
            @endif
        }
        @endforeach
    @endforeach

</style>
        <style>
            .glyphicon-refresh-animate {
                -animation: spin .7s infinite linear;
                -webkit-animation: spin2 .7s infinite linear;
            }

            @-webkit-keyframes spin2 {
                from { -webkit-transform: rotate(0deg);}
                to { -webkit-transform: rotate(360deg);}
            }

            @keyframes spin {
                from { transform: scale(1) rotate(0deg);}
                to { transform: scale(1) rotate(360deg);}
            }
        /* Style the tab */
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
            }

            /* Style the buttons inside the tab */
            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: 17px;
            }

            /* Change background color of buttons on hover */
            .tab button:hover {
                background-color: #ddd;
            }

            /* Create an active/current tablink class */
            .tab button.active {
                background-color: #ccc;
            }

            /* Style the tab content */
            .tabcontent {
                display: none;
                padding: 6px 12px;
                border: 1px solid #ccc;
                border-top: none;
            }
            .table>tbody>tr>td{
                padding: 4px;
            }
            .table>thead>tr>th{
                padding: 4px;
            }
</style>



<!--link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' /-->
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >

    <div class="box">
        <div class="box-header">
            @if($reuniones != '[]')
            <div  class="table-responsive col-md-12"><h4>REUNIONES</h4>
                        <table class="table table-striped" style="font-size: 12px;">
                            <thead>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Tipo</th>
                                <th>Doctor</th>

                                <th>Agdo.por</th>
                                <th>Modf.por</th>

                            </thead>
                            <tbody >
                                @foreach($reuniones as $reunion)
                                <tr style="background-color: #ffccf2;">

                                    <td>{{$reunion->fechaini}}</td>
                                    <td>{{$reunion->fechafin}}</td>
                                    <td>{{$reunion->procedencia}} {{$reunion->observaciones}}</td>
                                    <td>{{$reunion->dnombre}} {{$reunion->dapellido}}</td>
                                    <td>{{$reunion->ucnombre1}} {{$reunion->ucapellido1}}</td>
                                    <td>{{$reunion->umnombre1}} {{$reunion->umapellido1}}</td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            @endif
            @if($pentax_pend != [])
            <div  class="table-responsive col-md-12"><h4>PENDIENTES DE EXAMEN</h4>
                        <table class="table table-striped" style="font-size: 12px;">
                            <thead>
                              <tr role="row">
                                <th >Fecha/Hora</th>
                                <th >C??dula</th>
                                <th >Nombres</th>
                                <th >Seguro</th>
                                <th >Procedimiento</th>
                                <th >Pendiente</th>
                                <th >Observaci??n</th>
                              </tr>
                            </thead>
                            <tbody >
                                @foreach($pentax_pend as $value)
                                <tr >
                                    <td >{{$value['0']->fechaini}}</td>
                                    <td >{{$value['0']->id_paciente}}</td>
                                    <td >{{$value['0']->apellido1}} {{$value['0']->apellido2}} {{$value['0']->nombre1}} {{$value['0']->nombre2}}</td>
                                    <td >{{$value['0']->snombre}}</td>
                                    <td >{{$value['0']->procedimiento}}</td>
                                    <td ><?php echo $value['1'] ?>&nbsp<?php echo $value['2'] ?></td>
                                    <td >{{$value['0']->epobservacion}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            @endif

            <div class="col-md-6">
                <h4>AGENDA DE PROCEDIMIENTOS PENTAX</h4>
            </div>




            <div class="form-group col-md-12 {{ $errors->has('fecha') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                <label class="col-md-1 control-label">Fecha</label>
                <div class="col-md-12">
                    <form method="POST" action="{{ route('preagenda.pentax')}}" >
                        {{ csrf_field() }}
                        <div class="col-md-3">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" value="" name="fecha" class="form-control input-sm" id="fecha" onchange="fechacalendario();">
                            </div>
                        </div>
                        <input type="hidden" id="sel_sala" name="sel_sala" value=@if($sel_sala!=null)"{{$sel_sala}}" @else "{{old('sel_sala')}}" @endif>
                        <input type="submit" id="enviar_fecha" style="display: none;">
                        <div class="form-group col-md-3 col-xs-5">
                            <button type="submit" class="btn btn-primary" formaction="{{route('preagenda.salas_todas')}}"> SALAS TODAS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="box-body">

            <div class="w3-bar w3-blue">
                @foreach($salas_encontradas as $value)
                    @if($value->proc_consul_sala == '1')
                        <button id="btn{{$value->id}}" class="w3-bar-item w3-button tablink w3-orange" onclick="AbrirSala(event,'{{$value->id}}')">{{$value->nombre_sala}}</button>
                    @endif
                @endforeach
            </div>

            @php $sala=0; @endphp
            @foreach($salas_encontradas as $value)
                @if($value->nombre_sala != 'RECUPERACION')
                    <div id="{{$value->id}}" class="w3-container sala" >
                        <!--h4>{{$value->nombre_sala}}</h4-->
                        <div id='calendar{{$sala}}'></div>
                    </div>
                @endif
                @php $sala=$sala+1; @endphp
            @endforeach

       </div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

    <script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>

<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}'
            });
        $("#fecha").on("dp.change", function (e) {
            fechacalendario();
        });
    });
</script>
<script>


    $(document).ready(function()
    {
        <?php $j = 0;?>
        @foreach($calendarios as $valores)
            @if($salas_encontradas[$j]->nombre_sala != 'RECUPERACION')
        // page is now ready, initialize the calendar...
            $('#calendar{{$j}}').fullCalendar({
                // put your options and callbacks here
                minTime: "05:00:00",
                lang: 'es',
                locate: 'es',
                @if($fecha != '0')
                    defaultDate: '{{$fecha}}',
                @endif
                views:{
                    agenda:{
                        slotDuration: "00:15:00",
                        slotLabelFormat: 'HH:mm',
                        scrollTime: "07:00:00",
                    }
                },
                @php $fecha_valida = date("Y-m-d H:i");
                @endphp
                events : [
                    @foreach($reuniones as $reunion)
                    {
                        start: '{{$reunion->fechaini}}',
                        end: '{{$reunion->fechafin}}',
                        color: '#FF00BF',
                        rendering: 'background',
                    },
                    @endforeach

                    @foreach($arr_rec[$j] as $recurrente)
                    {

                        id    : '{{$recurrente->id}}',
                        className: 'r{{$recurrente->id}}',
                        title : '{{$recurrente->motivo}}',
                        start : '{{$fecha}} {{$recurrente->hora_ini}}',
                        end : '{{$fecha}} {{$recurrente->hora_fin}}',
                        editable: false,
                        color: 'red',
                        //url: '{{ route('preagenda.edit', ['id' => $value->id])}}',



                    },
                    @endforeach

                    @foreach($arr_dia[$j] as $diario)
                    {

                        id    : '{{$diario->id}}',
                        className: 'r{{$diario->id}}',
                        title : '{{$diario->motivo}}',
                        start : '{{$diario->fecha_ini}}',
                        end : '{{$diario->fecha_fin}}',
                        editable: false,
                        color: 'blue',
                        //url: '{{ route('preagenda.edit', ['id' => $value->id])}}',



                    },
                    @endforeach

                    @foreach($valores as $value)
                    {
                        @if($value->estado == '-1')
                          @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); @endphp
                          id    : '{{$value->id}}',
                          className: 'a{{$value->id}}',
                          title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif |PROC: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Por Confirmar @elseif($value->estado_cita == 1)  Confirmada @elseif($value->estado_cita == 2) Reagendada  @elseif($value->estado_cita == 4) ADMISIONADO @endif | Agdo por: {{$value->uapellido1}} | Seg: {{ $value->nombre_seguro}} | **PENDIENTE DE ASIGNAR DOCTOR** @if($value->omni=='SI') | OMNI @endif @if($value->ciudad != null) |Ciudad Proc: {{$value->ciudad}} @endif',
                          start : '{{ $value->fechaini }}',
                          end : '{{ $value->fechafin }}',
                          @if($value->fechaini <= $fecha_valida)
                            editable: false,
                          @else
                            editable: true,
                          @endif
                          url: '{{ route('preagenda.edit', ['id' => $value->id])}}',
                          @if($value->paciente_dr == 0)
                              @if($value->estado_cita == 0)
                                color: 'black',
                              @else
                                color: '{{ $value->color}}',
                                textColor: 'black',

                              @endif
                          @endif
                          @if($value->paciente_dr == 1)
                              @if($value->estado_cita == 0)
                                color: 'black',
                              @else
                                color: 'red',
                            @endif
                          @endif
                        @endif

                        @if($value->estado == '1')
                          @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get();  @endphp
                          @php $doctor=Sis_medico\User::find($value->id_doctor1); @endphp
                          id    : '{{$value->id}}',
                          className: 'a{{$value->id}}',
                          title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | Doctor: {{$doctor->nombre1}} {{$doctor->apellido1}} | PROCEDIMIENTOS: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) Estado:  Reagendada  @elseif($value->estado_cita == 4) Estado:  ADMISIONADO @endif |  Agendado por: {{$value->unombre1}} {{$value->uapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}} @if($value->omni=='SI') | OMNI @endif @if($value->ciudad != null) |Ciudad Proc: {{$value->ciudad}} @endif',
                          start : '{{ $value->fechaini }}',
                          end : '{{ $value->fechafin }}',
                          @if($value->fechaini <= $fecha_valida )
                            editable: false,
                          @else
                            editable: true,
                          @endif
                          url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => '0'])}}',
                          @if($value->paciente_dr == 0)
                              @if($value->estado_cita == 0)
                                color: 'black',
                              @else
                                color: '{{ $value->color}}',
                                textColor: 'black',

                              @endif
                          @endif
                          @if($value->paciente_dr == 1)
                              @if($value->estado_cita == 0)
                                color: 'black',
                              @else
                                color: 'red',
                            @endif
                          @endif
                        @endif

                    },
                    @endforeach


                ],

                defaultView: 'agendaDay',
                duration: '00:15:00',
                selectHelper: true,
                selectable: true,
                columnHeader: true,
                handleWindowResize: false,
                allDaySlot: false,
                startEditable: false,




                select: function(start, end, allDay) {
                    var check = $.fullCalendar.formatDate(start,'YYYYMMDD');
                    var today = '{{date('Ymd')}}';
                    if(check < today)
                    {
                       alert("Fecha pasada, no es posible seleccionar");
                    }
                    else
                    {
                        var dia =  $.fullCalendar.formatDate(start,'ddd');
                        var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                        var dato = 0;
                        location.href = "{{route('preagenda.nuevo') }}/"+start+"/0/{{$salas_encontradas[$j]->id}}";
                    }

                },
                eventDrop: function(event, delta, start, end){ // event drag and drop
                   var start = event.start;
                   var id = event.id;
                   var end = event.end;
                   var url = "{{ route('preagenda.actualizarhorario') }}/"+id+"/"+start+"/"+end;
                   $.get(url, function(result){
                      alert(result);
                      //location.reload(true);
                      $('#enviar_fecha').click();
                   });
                },


                header: {
                          left: '',
                          center: 'title',
                          right: 'agendaWeek,agendaDay,listDay',
                      },
            });

            @endif
        <?php $j = $j + 1;?>
        @endforeach
        $('#favoritesModal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
        });
        function modal1(){
            $('.classreunion').find("a").attr("data-toggle","modal");
            $('.classreunion').find("a").attr("data-target","#favoritesModal");
        };


    });
</script>
<script type="text/javascript">

$('#inicio').bootstrapMaterialDatePicker({
    date: true,
    shortTime: false,
    format : 'YYYY/MM/DD HH:mm',
    lang : 'es',

});
$('#fin').bootstrapMaterialDatePicker({
    date: true,
    shortTime: false,
    format : 'YYYY/MM/DD  HH:mm',
    lang : 'es',

});
$('#fecha_nacimiento').bootstrapMaterialDatePicker({
    date: true,
    shortTime: false,
    format : 'YYYY/MM/DD',
    lang : 'es',
    time: false,

});

 $(document).ready(function() {
    //AbrirSala('10');
    //$('#btn10').click();

    $(".sala").css("display", "none");
    $('.tablink').removeClass('w3-orange');

    n_sala = document.getElementById('sel_sala').value;
    //alert('#btn'+n_sala);
    if(n_sala!=''){
        $('#btn'+n_sala).addClass('w3-orange');
        $('#'+n_sala).css("display", "block");
    }else{

        $('#btn10').addClass('w3-orange');
        $("#10").css("display", "block");
    }






    $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li class="active">Pentax</li>');

    $('#alternar-respuesta-ej5').toggle(

        // Primer click
        function(e){
            $('#respuesta-ej5').slideDown();
            $(this).text('Ocultar respuesta');
            e.preventDefault();
        }, // Separamos las dos funciones con una coma

        // Segundo click
        function(e){
            $('#respuesta-ej5').slideUp();
            $(this).text('Ver respuesta');
            e.preventDefault();
        }

    );




 });

 function fechacalendario() {
     var dato = document.getElementById('fecha').value;
    $('#enviar_fecha').click();
 }



function AbrirSala(evt, SalaNombre) {
    //console.log(evt);
    var i, x, tablinks;
    var x = document.getElementsByClassName("sala");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < x.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" w3-orange", "");
    }

    document.getElementById(SalaNombre).style.display = "block";
    document.getElementById('sel_sala').value= SalaNombre;
    evt.currentTarget.className += " w3-orange";
}


</script>
@include('sweet::alert')
@endsection
