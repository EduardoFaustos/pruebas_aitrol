<style type="text/css">
   .fc-title{
    font-size: 1em !important;
    font-weight: bold;

    }
    .fc-title:before{

    }

    .fc-time-grid .fc-slats td{
        height: 1.2em !important;
    }
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
</style>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
  <div class="box">
    <div id="calendario_todas" >
    </div>
  </div>
</section>

<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>


<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script>
  $(document).ready(function(){
    $(".breadcrumb").empty();
    $(".breadcrumb").append('<li><a href="#"><i class="fa fa-list"></i> Unidades</a></li>');
    $(".breadcrumb").append('<li><a href="{{asset('/disponibilidad/disponibilidad_menu')}}">Sala todas</li>');
    //$(".breadcrumb").append('<li><a href="{{asset('disponibilidad.disponibilidad_menu')}}"></a></li>');
    //$(".breadcrumb").append('<li class="active">Unidades</li>');
      // page is now ready, initialize the calendar...
        $('#calendario_todas').fullCalendar({
            // put your options and callbacks here
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            lang: 'es',
            allDaySlot: false,
            defaultView: 'agendaDay',
            duration: '00:15:00',
            selectHelper: true,
            selectable: true,
            columnHeader: true,
            handleWindowResize: false,
            allDaySlot: false,
            minTime: "07:00:00",
            startEditable: false,
            header: {
              left: 'prev,next today',
              center: 'title',
              //right: 'month,agendaWeek,agendaDay,listMonth,listDay',
              right: 'month,agendaWeek,agendaDay',
            },
            selectable: true,
            locate: 'es',
            @if($fecha != '0')
              @php
                date_default_timezone_set('Europe/London');
                $fecha2 = substr($fecha, 0, 4) . '-' . substr($fecha, 5, 2) . '-' . substr($fecha, 8, 2);
              @endphp
                defaultDate: '{{$fecha2}}',
            @endif
            views:{
                agenda:{
                    slotDuration: "00:15:00" ,
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "07:00:00"
                }
            },
            @php $fecha_valida = date("Y-m-d H:i");
            @endphp
            resources: [
                @foreach($salas as $sala)
                    {
                      id: '{{$sala->id}}',
                      title: '{{$sala->nombre_sala}}'
                    },
                @endforeach
            ],
            events : [
                @foreach($procedimientos as $value)
                {
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get();
                       $varhospital = Sis_medico\Sala::find($value->id_sala)->hospital->id;
                  @endphp
                  id    : '{{$value->id}}',
                  resourceId: '{{$value->id_sala}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif @if($value->paciente_dr=="1") /PART @endif',
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  //url: '{{ route('preagenda.solca_editar', ['ruta' => '2', 'id' => $value->id])}}',
                  @if($varhospital==2)
                    editable: false,// 10/10/2018 BLOQUEAR AGENDA
                  @else
                    editable: true,
                  @endif

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
                  @if($value->vip == 1)
                    color: '#ff3300',
                  @endif
                },
                @endforeach
                @foreach($salas as $x)
                  @php
                    $horario = DB::table('horario_sala')
                    ->where('id_sala', '=', $x->id)->orderBy('ndia')
                    ->orderBy('hora_ini')
                    ->get(); //dd($value->id_sala);
                  @endphp
                  @foreach($horario as $key=>$xx)
                      {
                        resourceId: '{{$x->id}}',
                        start: '{{$xx->hora_ini}}',
                        end: '{{$xx->hora_fin}}',
                        color: '@if($xx->tipo == 0) #61c9ff @endif @if($xx->tipo == 1) #6666FF @endif @if($xx->tipo == 2) #FF00BF @endif @if($xx->tipo == 3) #00e600 @endif',
                        rendering: 'background',
                        @if($xx->ndia != 7)
                        dow: [ {{$xx->ndia}}],
                        @endif
                        @if($xx->ndia == 7)
                            dow: [0],
                        @endif
                      },
                  @endforeach
                @endforeach
                @foreach($consultas as $value)
                  @php
                    $atendido = 0;
                    $hc = Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                    if(!is_null($hc)){
                        $cie10 = Sis_medico\Hc_Cie10::where('hcid',$hc->hcid)->first();
                        if(!is_null($cie10)){
                            $atendido = 1;

                        }
                    }
                  @endphp
                  {
                    id    : '{{$value->id}}',
                    resourceId: '{{$value->id_sala}}',
                    className: 'a{{$value->id}}',
                    title : '{{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif @if($value->paciente_dr=="1") /PART @endif',
                    start : '{{ $value->fechaini }}',
                    end : '{{ $value->fechafin }}',
                   // url: '{{ route('preagenda.solca_editar', ['ruta' => '2', 'id' => $value->id])}}',
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
                        textColor: 'black',
                      @endif
                    @endif
                    @if($value->vip == 1)
                      color: '#ff3300',
                    @endif
                  },
                @endforeach

                @foreach($reuniones as $value)
                    {

                  @php $sala=Sis_medico\Sala::find($value->id_sala); //dd($sala);
                  @endphp

                  id    : '{{$value->id}} idreunion',
                  resourceId: '{{$value->id_sala}}',
                  className: 'classreunion',
                  title : '{{$value->procedencia}} - {{ str_replace(array("\r\n", "\r", "'"), " ", $value->observaciones)}} | {{$sala->nombre_sala}}/{{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}}',
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                  color: '#023f84',
                  url: '{{route('reunion.edit2', ['id' => $value->id]) }}',
                  @if($value->fechaini >= $fecha_valida)
                  editable: false,
                  @endif

                },


                @endforeach





            ],

            viewRender: function (view, element) {
              //var unix =  Math.round(new Date($('#fecha').val()+' 0:00:00').getTime());
              //console.log(view.name);
              console.log($.fullCalendar.formatDate(view.start,'YYYY/MM/DD'),'input', $('#fecha').val());
              if(view.name == 'agendaDay' || view.name == 'listDay'){
                if($.fullCalendar.formatDate(view.start,'YYYY/MM/DD')!= $('#fecha').val()){
                  $('#fecha').val($.fullCalendar.formatDate(view.start,'YYYY/MM/DD'));
                  fechacalendario_todas('T');
                }
              }

              //fechacalendario_todas();
              //obtener_fecha(view.start._i);
            },

            eventClick: function(event, jsEvent, view) {
              //alert(event.id);

              /*$.ajax({
                type: 'get',
                url: "{{ url('disponibilidad/sala_ajax')}}/"+event.id,
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'html',
                data: $("#calendario_todas").serialize(),
                success: function(datahtml,data){
                    //console.log(data);
                    if(data!='no'){

                        swal({
                          title: "Datos",
                          type: "success",
                          html: datahtml
                        });
                    }
                },
                error: function(datahtml,data){
                    //console.log(datahtml);
                    alert('error al cargar');
                }

                });*/
            },

           /* select: function(start, end, event, view, resource) {

                @php
                    date_default_timezone_set('America/Guayaquil');
                @endphp

                var check = $.fullCalendar.formatDate(start,'YYYYMMDD HH:mm');
                var today = '{{date("Ymd H:m")}}';
                //console.log(resource);
                if(check <= today)
                {
                  alert("Fecha pasada, no es posible seleccionar");
                }
                else
                {
                  //location.href = "{{url('solca/preagenda/nuevo/agendar') }}/2/"+start+"/"+resource.id+"/0";
                  location.href = "{{url('solca/preagenda/nuevo/agendar') }}/2/"+start+"/"+resource.id+"/0";
                }

            },*/

        });
     });



</script>

<script type="text/javascript">


 $(document).ready(function() {



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


function veractivas(e, suspendidas)
{
    alert(suspendidas);
    var boton1 =document.getElementsByClassName("suspendidas");
    if(suspendidas == 0){
        $(boton1).text('Ver Activas');
        var suspendidas=1;
        alert(suspendidas);
    }
    if(suspendidas == 1){
        $(boton1).text('Ver Suspendidas');
        var suspendidas=0;
        alert(suspendidas);
    }

}
 function fecha_calendario(id) {
     var fecha = document.getElementById('fecha').value;
     var unix =  Math.round(new Date(fecha).getTime()/1000);



     if(fecha=='' ||fecha==' '){
     }else{
         //console.log(id);
        $.ajax({
          type: 'get',
          url: "{{ url('disponibilidad/salas_todas/')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'html',
          data: $(".fecha_enviar").serialize(),
          success: function(datahtml){


              $("#calendario_todas").html(datahtml);

          },
          error: function(datahtml){
            //console.log(data);
            alert('error al cargar');
          }

        });
    }
}



</script>