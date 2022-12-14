<div id="calendar"></div>


<script type="text/javascript">
	$(function() { // document ready

        $('#calendar').fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultView: 'agendaDay',
            defaultDate: '{{$fecha}}',
            editable: false,
            allDaySlot: false,
            views:{
                agenda:{
                    slotDuration: "00:15:00",
                    @if(!is_null($doctor_t))
                        @if($id_doctor == $doctor_t->id_doctor)
                            slotDuration: "00:10:00" ,
                        @endif
                    @endif
                    slotLabelFormat: 'HH:mm',
                    scrollTime: "06:00:00",
                    minTime: "05:00:00",   
                }
            },
            selectable: true,
            eventLimit: true, // allow "more" link when too many events
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaDay,agendaTwoDay,agendaWeek,month'
            },
          
            //// uncomment this line to hide the all-day slot
            //allDaySlot: false,
            @php $fecha_valida = date("Y-m-d H:i");
            @endphp
            
            events: [

            	@foreach($horario as $value)
                    {
                    
                        start: '{{$value->hora_ini}}', 
                        end: '{{$value->hora_fin}}',
                        color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                        rendering: 'background',
                        @if($value->ndia != 7)
                        dow: [ {{$value->ndia}}],
                        @endif
                        @if($value->ndia == 7)
                            dow: [0],
                        @endif
                    },
                @endforeach
                @foreach($extra as $value)
                    {
                    
                        start: '{{$value->inicio}}', 
                        end: '{{$value->fin}}',
                        color: '@if($value->tipo == 0) #61c9ff @endif @if($value->tipo == 1) #6666FF @endif @if($value->tipo == 2) #FF00BF @endif',
                        rendering: 'background',
                    },
                @endforeach
                @foreach($agenda as $value)
                {
                    
                  @php $agendaprocedimientos=Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get();  @endphp  
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif | PROC: {{$value->nombre_procedimiento}}@if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc)+{{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif| @if($value->estado_cita == 0) Estado:  Por Confirmar @elseif($value->estado_cita == 1) Estado:  Confirmada @elseif($value->estado_cita == 2) @if($value->estado==1) Estado:  Completar Datos @else Estado:  Por Reagendar @endif  @elseif($value->estado_cita == 4) Estado:  ASISTI?? @endif |  Agendado: {{$value->uapellido1}} | Modificado: {{$value->umapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}}',	
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                   @if($value->fechaini <= $fecha_valida || ($value->estado_cita >= 4 ))
                            editable: false,
                          @else
                            editable: true,
                          @endif
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => $url_doctor])}}',
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

                },
                @endforeach
                @foreach($agenda3 as $value)
                {
                  id    : '{{$value->id}}',
                  className: 'a{{$value->id}}',
                  title : '{{ $value->pnombre1}} @if($value->pnombre2 != "(N/A)"){{ $value->pnombre2}}@endif {{ $value->papellido1}} @if($value->papellido2 != "(N/A)"){{ $value->papellido2}}@endif |  CONSULTA | @if($value->estado_cita == 0) Estado:  Por Confirmar @endif @if($value->estado_cita == 1) Estado:  Confirmada @endif @if($value->estado_cita == 2) @if($value->estado==1) Estado:  Completar Datos @else Estado:  Por Reagendar @endif @endif @if($value->estado_cita == 4) Estado:  ASISTI?? @endif|  @if($value->tipo_cita == 0) Primera vez @endif @if($value->tipo_cita == 1) Consecutivo @endif | Agendado: {{$value->uapellido1}} | Modificado: {{$value->umapellido1}} | Seguro: {{ $value->nombre_seguro}} | Cortesia: {{ $value->cortesia}}',  
                  start : '{{ $value->fechaini }}',
                  end : '{{ $value->fechafin }}',
                            editable: true,
                  url: '{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => $url_doctor])}}',
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

                },
                @endforeach

                @foreach($agenda2 as $value)
                {
                  @php $sala=Sis_medico\Sala::find($value->id_sala) @endphp  
                  
                  id    : '{{$value->id}} idreunion',
                  className: 'classreunion',
                  title : '{{$value->procedencia}} - {{ $value->observaciones}} | {{$sala->nombre_sala}}/{{Sis_medico\Hospital::find($sala->id_hospital)->nombre_hospital}} | Agendado: {{substr($value->unombre1, 0, 1)}}{{$value->uapellido1}} | Modificado: {{substr($value->umnombre1, 0, 1)}}{{$value->umapellido1}} ',  
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
            

            select: function(start, end, allDay) {
                
                //console.log(start, end);
                var fin = moment(start).add(30, 'm');
                @if(!is_null($doctor_t))
                    @if($id_doctor == $doctor_t->id_doctor)
                        var fin = moment(start).add({{$doctor_t->tiempo*10}}, 'm');
                    @endif
                @endif
                //alert(fin);

                
                @php 
                    date_default_timezone_set('America/Guayaquil');
                @endphp
                var check = $.fullCalendar.formatDate(start,'YYYYMMDD HH:mm');

                var today = '{{date("Ymd H:m")}}';
                if(check <= today)
                {
                   alert("Fecha pasada, no es posible seleccionar");
                }else{
                  //console.log("ok");
                	var dia =  $.fullCalendar.formatDate(start,'ddd');
                    var hora = $.fullCalendar.formatDate(start,'HH:mm:ss');
                    var dato = 0;
                    @foreach($horario as $value)
                    if(dia == '<?php echo $value->dia;?>'){ 
                        if(hora >= '<?php echo $value->hora_ini;?>' && hora < '<?php echo $value->hora_fin;?>'){
                            dato = 1; 
                            //alert("listo");
                            $('#calendar').fullCalendar('renderEvent', {
          			              title: 'CONSULTA CARDIOLOGIA / {{$paciente}}',
          			              start: start,
          			              end: fin,
          			            });

        				            $('#inicio').val($.fullCalendar.formatDate(start,'YYYY/MM/DD HH:mm'));
        				            $('#fin').val($.fullCalendar.formatDate(fin,'YYYY/MM/DD HH:mm'));
        				            //alert("ok"); 
        				            $('#bagregar').attr('disabled',false);                         
                        }

                    }
                    @endforeach
                    var inicial2=  $.fullCalendar.formatDate(start,'YYYY-MM-DD HH:mm:ss');
                    @foreach($extra as $key=>$value)
                        if(inicial2 >= '<?php echo $value->inicio;?>' && inicial2 < '<?php echo $value->fin;?>'){
                            dato = 1;
                            $('#calendar').fullCalendar('renderEvent', {
                              title: 'CONSULTA CARDIOLOGIA / {{$paciente}}',
                              start: start,
                              end: fin,
                            });

                            $('#inicio').val($.fullCalendar.formatDate(start,'YYYY/MM/DD HH:mm'));
                            $('#fin').val($.fullCalendar.formatDate(fin,'YYYY/MM/DD HH:mm'));
                            //alert("ok"); 
                            $('#bagregar').attr('disabled',false);   
                        }
                    @endforeach
                    if(dato == 0)
                    {
                        alert('??AGENDA FUERA DEL HORARIO DISPONIBLE PARA EL DOCTOR!');
                        
                    }

                }

            },
            
            dayClick: function(date, jsEvent, view, resource) {
                /*console.log(
                  'dayClick',
                  date.format(),
                  resource ? resource.id : '(no resource)'
                );*/
            }
        });
          
    });
</script>