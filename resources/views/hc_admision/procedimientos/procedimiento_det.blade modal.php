<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 40px; font-weight: bolder;">×</span></button>
    <div class="col-md-4"><h4><span><b>Paciente: </b></span><span style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $procedimiento->historia->paciente->apellido1}} @if($procedimiento->historia->paciente->apellido2 != "(N/A)"){{ $procedimiento->historia->paciente->apellido2}}@endif {{ $procedimiento->historia->paciente->nombre1}} @if($procedimiento->historia->paciente->nombre2 != "(N/A)"){{ $procedimiento->historia->paciente->nombre2}}@endif</b></span></h4></div>
    <div class="col-md-4"><h4><span><b>Identificación: </b></span><span style="color: red; font-weight: 700; font-size: 18px;">{{$procedimiento->historia->paciente->id}}</span></h4></div>
</div>

<div class="modal-body">
    <div class="row" style="padding: 10px;">


        <div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content" style="width: 95%;">

              </div>
            </div>  
        </div>

        <div class="col-md-12">
            @if(!is_null($epicrisis))
            <a target="_blank" href="{{route('epicrisis.imprimir_stream',['epicrisis' => $epicrisis->id])}}">
                <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                    <span class="glyphicon glyphicon-file"> Epicrisis</span>
                </button>    
            </a>
            @endif
            
            <a target="_blank" href="{{route('hc_reporte.descargar', ['id_protocolo' => $protocolo->id, 'tipo' => 1])}}">
                <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                    <span class="glyphicon glyphicon-file"> Estudio</span>
                </button>    
            </a>

            <a target="_blank" href="{{route('evolucion.imprimir_stream', ['id' => $protocolo->id_hc_procedimientos ])}}">
                <button id="nuevo_proc" type="button" class="btn btn-success btn-sm">
                    <span class="glyphicon glyphicon-file"> Evolución</span>
                </button>    
            </a>

            <a href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-success btn-sm" target="_blank">
                <span class="glyphicon glyphicon-download-alt"> Receta</span>
            </a>
            
        </div>

        <div class="container-fluid" >
            <div class="row">
                
               

                @php 
                    $agenda = DB::table('agenda')->where('id',$procedimiento->historia->id_agenda)->first();
                    $dia =  Date('N',strtotime($agenda->fechaini));
                    $mes =  Date('n',strtotime($agenda->fechaini)); 
                @endphp 

                @if($agenda->estado_cita!='4')
                <div class="col-md-12" style="padding-right: 6px;">
                    @php $dia =  Date('N',strtotime($agenda->fechaini)); $mes =  Date('n',strtotime($agenda->fechaini)); @endphp
                    <div class="callout callout-warning col-md-12" style="margin-bottom: 5px;padding: 5px;">
                     Paciente Aun No Admisionado Para @if($agenda->proc_consul=='0') La Consulta @elseif($agenda->proc_consul=='1') El Procedimiento @endif 
                     Del @if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}}, se encuentra en estado @if($agenda->estado_cita=='0') POR CONFIRMAR @elseif($agenda->estado_cita=='1') CONFIRMADO @elseif($agenda->estado_cita=='2') REAGENDAR @elseif($agenda->estado_cita=='3') SUSPENDIDA: {{$agenda->observaciones}} @endif      
                    </div>
                </div>
                @endif

                @php
                    
                    $pentax = DB::table('pentax')->where('hcid',$procedimiento->historia->hcid)->first();
                    $procedimientos_pentax = null;      
                    if(!is_null($pentax)){
                        $procedimientos_pentax = Sis_medico\PentaxProc::where('id_pentax',$pentax->id)->get(); 
                    }
                @endphp
               
                <div class="col-md-12" style="padding-right: 6px;">
                    
                    <div class="box box-primary" >
                        <div class="box-header">
                            <div class="col-md-6" style="padding-left: 0px;">
                                <h3 class="box-title"><b>Procedimiento</b></h3>
                            </div>
                            
                            <div class="col-md-6" style="color: #f2f2f2;">{{$protocolo->id}}</div>
                            
                            
                                <!-- tools box 
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse">
                                        <i class="fa fa-plus"></i></button>
                                </div>
                                /. tools -->
                        </div>

                        <div class="box-body" style="padding: 5px;">            
                            
                            
                            <div class="col-md-6" >
                                
                                @php $dia =  Date('N',strtotime($agenda->fechaini));
                                    $mes =  Date('n',strtotime($agenda->fechaini)); 
                                @endphp 
                                <form id="frm2">
                                    <input type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">
                                    <input type="hidden" name="id_hc_procedimientos" value="{{$protocolo->id_hc_procedimientos}}">
                                    <input type="hidden" name="hcid" value="{{$protocolo->hcid}}">
                                    <input type="hidden" name="protocolo" value="{{$protocolo->id}}">

                                    <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                                        <b>Fecha Procedimiento: </b>@if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($agenda->fechaini,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($agenda->fechaini,0,4)}} <b>Hora: </b>{{substr($agenda->fechaini,10,10)}}
                                    </div>
                                    @if($agenda->estado_cita=='4')
                                    <div class="col-md-6" style="padding: 1px;">
                                        <label for="estado_pentax" class="control-label">Estado Paciente: </label>
                                        <span>@if(!is_null($pentax))@if($pentax->estado_pentax=='0') EN ESPERA @elseif($pentax->estado_pentax=='1') PREPARACIÓN @elseif($pentax->estado_pentax=='2') EN PROCEDIMIENTO @elseif($pentax->estado_pentax=='3') RECUPERACIÓN @elseif($pentax->estado_pentax=='4') ALTA @elseif($pentax->estado_pentax=='5') SUSPENDIDO @endif @else "NO ASISTE" @endif</span>    
                                    </div>
                                    @endif

                                    <div class="col-md-6" style="padding: 1px;">
                                        <label for="est_amb_hos" class="control-label">Ingreso: </label>
                                        <span>@if(!is_null($agenda->est_amb_hos))@if($agenda->est_amb_hos=='0') AMBULATORIO @elseif($agenda->est_amb_hos=='1') HOSPITALIZADO @endif @endif</span>    
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label class="control-label">Procedimientos Agendados</label>
                                        @foreach($procedimientos_pentax as $value)
                                        <span class="bg-blue" style="border-radius: 4px;padding: 3px;margin-right: 5px;">{{$value->procedimiento->observacion}}</span>
                                        @endforeach
                                    </div>
                                       
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="proc_com" class="control-label col-md-4" style="padding-left: 0px;">Procedimiento Realizado</label>
                                        <input class="col-md-8" type="text" name="procedimiento" readonly value="@if($procedimiento->procedimiento_completo!=null){{$procedimiento->procedimiento_completo->nombre_general}}@endif">
                                    </div>

                                    <div class="col-md-4" style="padding: 1px;">
                                        <label for="id_doctor2" class="control-label">Asistente 1</label>
                                        <input type="text" name="asistente1" readonly value="@if($procedimiento->historia->doctor_2!=null){{$procedimiento->historia->doctor_2->apellido1}} {{$procedimiento->historia->doctor_2->nombre1}}@endif">  
                                    </div>

                                    <div class="col-md-4" style="padding: 1px;">
                                        <label for="id_doctor3" class="control-label">Asistente 2</label>
                                        <input type="text" name="asistemte2" readonly value="@if($procedimiento->historia->doctor_3!=null){{$procedimiento->historia->doctor_3->apellido1}} {{$procedimiento->historia->doctor_3->nombre1}}@endif">   
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="motivo" class="control-label">Motivo</label>
                                        <textarea name="motivo" style="width: 100%;" rows="1" readonly onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->motivo}}@endif</textarea>
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="thallazgos" class="control-label">Hallazgos</label>
                                        <div id="thallazgos" style="border: solid 1px;">@if(!is_null($protocolo))<?php echo $protocolo->hallazgos ?>@endif</div>
                                        <input type="hidden" name="hallazgos" id="hallazgos">
                                    </div>

                                    
                                </form>    
                            </div>
                            <div class="col-md-6">
                                <form id="hc_protocolo">
                                
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="id_doctor_examinador" class="control-label col-md-4" style="padding-left: 0px;">Medico Examinador</label>
                                    <input class="col-md-8" type="text" name="examinador" readonly value="@if($procedimiento->doctor!=null){{$procedimiento->doctor->apellido1}} {{$procedimiento->doctor->nombre1}}@else{{$procedimiento->historia->doctor_1->apellido1}} {{$procedimiento->historia->doctor_1->nombre1}}@endif"> 
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="id_seguro" class="control-label col-md-4" style="padding-left: 0px;">Seguro</label>
                                    <input class="col-md-8" type="text" name="seguro" readonly value="@if($procedimiento->seguro!=null){{$procedimiento->seguro->nombre}}@else{{$procedimiento->historia->seguro->nombre}}@endif"> 
                                </div> 
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="observaciones" class="control-label">Observaciones</label>
                                    <textarea readonly class="form-control input-sm" id="observaciones" name="observaciones" style="width: 100%;" rows="3" onchange="hc_protocolo();">{{$procedimiento->observaciones}}</textarea>
                                </div>   
                                </form>
                                
                                <form id="frm_cie">
                                    <input type="hidden" name="codigo" id="codigo">
                                    
                                    
                                    <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px;"><b>Diagnóstico</b></label>
                                    <!--div class="form-group col-md-12" style="padding: 1px;">
                                        <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>
                                    </div>
                                    <div class="form-group col-md-3" style="padding: 1px;">
                                        <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                            <option value="">Seleccione ...</option>
                                            <option value="PRESUNTIVO">PRESUNTIVO</option>
                                            <option value="DEFINITIVO">DEFINITIVO</option>   
                                        </select> 
                                    </div--> 
                                    
                            

                                    <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                        <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">
                                            
                                        </table>
                                    </div>

                                     <div class="col-md-12" style="padding: 1px;">
                                        <label for="tconclusion" class="control-label">Conclusión</label>
                                        <div id="tconclusion" style="border: solid 1px;">@if(!is_null($protocolo))<?php echo $protocolo->conclusion ?>@endif</div>
                                        <input type="hidden" name="conclusion" id="conclusion">
                                    </div>


                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="complicacion" class="control-label">Complicaciones</label>
                                        <textarea readonly name="complicacion" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->complicacion}} @endif</textarea>
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="estado_paciente" class="control-label">Estado del Paciente al Terminar</label>
                                        <textarea readonly name="estado_paciente" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->estado_paciente}} @endif</textarea>
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="plan" class="control-label">Plan Terapeutico</label>
                                        <textarea readonly name="plan" style="width: 100%;" rows="1" onchange="guardar_procedimiento();" @if(is_null($protocolo))readonly="yes" @elseif($agenda->estado_cita!='4') readonly="yes" @endif>@if(!is_null($protocolo)){{$protocolo->plan}} @endif</textarea>
                                    </div>
                                </form> 
                                
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                        <div class="box-header">
                            <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#ima_historico').click());">Imagenes</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                                <!-- tools box -->
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="ima_historico">
                                        <i class="fa fa-plus"></i></button>
                                </div>
                                <!-- /. tools -->
                        </div>
                        <div class="box-body" style="padding: 5px;"> 
                                
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">
                                        @php $count=0; @endphp    
                                        @foreach($imagenes as $imagen)
                                        <div class="col-md-6" style='margin: 10px 0; text-align: center;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                            </a><br>
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a>
                                        @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%">  
                                            </a> 
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a>
                                        @elseif(($extension == 'mp4'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/video.png')}}" width="90%">  
                                            </a>  
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a>
                                        @else
                                            @php
                                                $variable = explode('/' , asset('/hc_ima/'));
                                                $d1 = $variable[3];
                                                $d2 = $variable[4];
                                                $d3 = $variable[5];
                                                
                                            @endphp 
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                            </a>
                                            <a type="button" href="{{asset('hc_ima_nombre')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank"><!-- ruta 0 desde la historia clinica -->
                                                <span class="glyphicon glyphicon-download-alt"> Descargar</span>
                                            </a>  
                                        @endif      
                                        </div>          
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                        <div class="box-header">
                            <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_historico').click());">Documentos &amp; Anexos</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                                <!-- tools box -->
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="doc_historico">
                                        <i class="fa fa-plus"></i></button>
                                </div>
                                <!-- /. tools -->
                        </div>
                        <div class="box-body" style="padding: 5px;"> 
                                 
                        
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">
                                        @php $count=0; @endphp    
                                        @foreach($documentos as $imagen)
                                        <div class="col-md-6" style='margin: 10px 0;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                            </a> 
                                        @elseif(($extension == 'pdf'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                <span>{{$imagen->nombre_anterior}}</span>  
                                            </a> 
                                        @else
                                            @php
                                                $variable = explode('/' , asset('/hc_ima/'));
                                                $d1 = $variable[3];
                                                $d2 = $variable[4];
                                                $d3 = $variable[5];
                                                
                                            @endphp 
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                <span>{{$imagen->nombre_anterior}}</span>
                                            </a>  
                                        @endif      
                                        </div>          
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                        <div class="box-header">
                            <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_estudios').click());">Estudios</a></b></h3></div><div class="col-md-6" style="color: #f2f2f2;"></div>
                                <!-- tools box -->
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="doc_estudios">
                                        <i class="fa fa-plus"></i></button>
                                </div>
                                <!-- /. tools -->
                        </div>
                        <div class="box-body" style="padding: 5px;"> 
                            
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">
                                        @php $count=0; @endphp    
                                        @foreach($estudios as $imagen)
                                        <div class="col-md-6" style='margin: 10px 0;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">

                                            </a> 
                                        @elseif(($extension == 'pdf'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                <span>{{$imagen->nombre_anterior}}</span>  
                                            </a> 
                                        @else
                                            @php
                                                $variable = explode('/' , asset('/hc_ima/'));
                                                $d1 = $variable[3];
                                                $d2 = $variable[4];
                                                $d3 = $variable[5];
                                                
                                            @endphp 
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                                <span>{{$imagen->nombre_anterior}}</span>
                                            </a>  
                                        @endif      
                                        </div>          
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
                        <div class="box-header">

                            <div class="col-md-6" style="padding-left: 0px;"><h3 class="box-title"><b><a href="javascript:void($('#doc_bipsia').click());">Biopsias</a></b></h3></div>
                            <div class="col-md-6" style="color: #f2f2f2;"></div>
                                <!-- tools box -->
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="doc_bipsia">
                                        <i class="fa fa-plus"></i></button>
                                </div>
                                <!-- /. tools -->
                        </div>
                        <div class="box-body" style="padding: 5px;">       
                        
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered  dataTable" >
                                    <tbody style="font-size: 12px;">
                                        @php $count=0; @endphp    
                                        @foreach($biopsias as $imagen)
                                        <div class="col-md-6" style='margin: 10px 0;' >
                                        @php
                                            $explotar = explode( '.', $imagen->nombre);
                                            $extension = end($explotar);
                                        @endphp
                                        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="90%">
                                                <span>{{$imagen->nombre_anterior}}</span>
                                            </a> 
                                        @elseif(($extension == 'pdf'))
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/pdf.png')}}" width="90%">
                                                <span>{{$imagen->nombre_anterior}}</span>  
                                            </a> 
                                        @else
                                            @php
                                                $variable = explode('/' , asset('/hc_ima/'));
                                                $d1 = $variable[3];
                                                $d2 = $variable[4];
                                                $d3 = $variable[5];
                                                
                                            @endphp 
                                            <a data-toggle="modal" data-target="#foto" href="{{ route('hc_video.mostrar_foto', ['id' => $imagen->id]) }}">
                                                <img  src="{{asset('imagenes/office.png')}}" width="90%">
                                            </a>  
                                        @endif      
                                        </div>          
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--div class="col-md-12" style="padding-right: 6px;">
                    <div class="box box-primary " style="margin-bottom: 5px;" >
                        <div class="box-header">
                            <div class="col-md-4">
                                <h3 class="box-title"><a href="javascript:void($('#receta').click());"><b>Receta Del Paciente</b></a></h3>
                            </div>
                                
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="receta">
                                    <i class="fa fa-plus"></i></button>
                            </div>
                                
                        </div>
                        <div class="box-body" style="padding: 5px;">
                            <div class="col-md-12">
                                
                                <div class="col-md-5">
                                @if($hc_receta != "")   
                                    <a href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '1']) }}" target="_blank" type="button" class="btn btn-primary btn-sm">
                                        <span class="glyphicon glyphicon-download-alt"> Imprimir</span>
                                    </a>
                                    <a href="{{ route('hc_receta.imprime', ['id' => $hc_receta->id, 'tipo' => '2']) }}" type="button" class="btn btn-primary btn-sm" target="_blank">
                                        <span class="glyphicon glyphicon-download-alt"> Imprimir Membretada</span>
                                    </a>
                                    <a href="{{url('medicina')}}/{{$agenda->id}}" type="button" class="btn btn-success btn-sm">
                                        <span class="glyphicon glyphicon-list-alt"> Medicinas</span>
                                    </a>
                                @endif
                                </div>
                                <div class="col-md-12">&nbsp;</div>
                                <input type="hidden" name="id_paciente" id="id_paciente" value="{{$agenda->id_paciente}}">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div class="form-group">
                                          <label for="inputid" class="col-md-2 control-label">Medicina</label>
                                          <div class="col-md-6">
                                            <input value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico" placeholder="Nombre"  >
                                          </div>
                                          <div class="col-md-4">
                                                <button  type="button" id="limpiar" class="btn btn-primary">
                                                        Agregar
                                                </button>
                                            </div>
                                      </div>
                                    </div>
                                </div>

                                <br>
                                <div class="col-md-2"><b>Alergias: </b></div><div class="col-md-10">@if($alergiasxpac->count()==0) <b>NO TIENE </b>@else @foreach($alergiasxpac as $ale)<span class="bg-red" style="padding: 3px;border-radius: 10px;">{{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;@endforeach @endif</div>

                                <div id="index">
                                    
                                </div> 
                                <form id="final_receta" method="POST">
                                    <input type="hidden" name="id_receta" value="{{$hc_receta->id}}">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <span><b>Rp</b></span>
                                            <textarea id="rp" name="rp" style="width: 100%" rows="10" >{{$hc_receta->rp}}</textarea>
                                        </div>
                                        <div class="col-md-6" >
                                            <span><b>Prescripcion</b></span>
                                            <textarea id="prescripcion" name="prescripcion" rows="10" style="width: 100%" >{{$hc_receta->prescripcion}}</textarea>
                                        </div>
                                        <div class="col-md-2 col-md-offset-10">
                                            <button type="button" class="btn btn-primary">
                                                Guardar
                                            </button>
                                        </div>                                
                                    </div>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div-->  
            </div>
        </div>


        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script type="text/javascript">

            $('.select2').select2({
                tags: false
            });

            tinymce.init({
                selector: '#thallazgos',
                inline: true,
                menubar: false,
                content_style: ".mce-content-body {font-size:14px;}",
                readonly: 1, 
                
                

                init_instance_callback: function (editor) {
                    editor.on('Change', function (e) {
                        var ed = tinyMCE.get('thallazgos');
                        $("#hallazgos").val(ed.getContent());
                        guardar_procedimiento(); 
                      
                    });
                  }
              });

            tinymce.init({
                selector: '#tconclusion',
                inline: true,
                menubar: false,
                content_style: ".mce-content-body {font-size:14px;}",

                readonly: 1,

                init_instance_callback: function (editor) {
                    editor.on('Change', function (e) {
                        var ed = tinyMCE.get('tconclusion');
                        $("#conclusion").val(ed.getContent());
                        guardar_procedimiento(); 
                      
                    });
                  }
              });

            

            $(document).ready(function() {
                
                index();
                Carga_proc('0');
                cargar_tabla();

                $(".breadcrumb").append('<li class="active">Historia Clinica</li>');


            }); 

            $('#foto').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

            function editar_consulta(id,agenda){
                //alert(agenda);

                location.href ="{{url('historialclinico/visitas_ingreso')}}/"+id+"/"+agenda;//visita.crea_actualiza

            }

            

            function guardar_procedimiento(){
                $.ajax({
                  type: 'post',
                  url:"{{route('procedimiento.paciente')}}",
                  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  
                  datatype: 'json',
                  data: $("#frm2").serialize(),
                  success: function(data){
                    //console.log(data);
                    //var edad;
                    //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
                    //edad = calcularEdad(fecha_nacimiento);
                    //$('#edad').text( edad );
                  },
                  error: function(data){
                     //console.log(data);
                  }
                });
            }
            
            function Carga_proc(actualiza){

                $.ajax({
                  type: 'post',
                  url:"{{route('procedimiento.tecnica')}}",
                  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  datatype: 'json',
                  data: $("#frm2").serialize(),
                  success: function(data){
                    //console.log(data.tecnica_quirurgica);
                    if(data.tecnica_quirurgica!=null){
                        var tecnica = data.tecnica_quirurgica;
                    }else{
                        var tecnica = "";
                    }
                    //var edad;
                    //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
                    //edad = calcularEdad(fecha_nacimiento);
                    if(actualiza=='1'){
                        $('#hallazgos').val(tecnica);
                        tinyMCE.activeEditor.setContent(tecnica);
                    }    
                    if(data.estado_anestesia=='0'){
                        $('#id_anestesiologo').prop( "disabled", true );    
                    }else{
                        $('#id_anestesiologo').prop( "disabled", false );    
                    }

                    if(actualiza=='1'){
                        guardar_procedimiento();    
                    }
                    
                    
                    //tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, data )
                  },
                  error: function(data){
                     //console.log(data);
                  }
                });

                //guardar();
            }

            $("#cie10").autocomplete({
                source: function( request, response ) {
                        
                    $.ajax({
                        url:"{{route('epicrisis.cie10_nombre')}}",
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                        data: {
                            term: request.term
                              },
                        dataType: "json",
                        type: 'post',
                        success: function(data){
                            response(data);
                            
                        }
                    })
                },
                minLength: 2,
            } );

            $("#cie10").change( function(){
                $.ajax({
                    type: 'post',
                    url:"{{route('epicrisis.cie10_nombre2')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $("#cie10"),
                    success: function(data){
                        if(data!='0'){
                            $('#codigo').val(data.id);
                            
                        }
                        
                    },
                    error: function(data){
                            
                        }
                })
            });

            $('#bagregar').click( function(){
                @if(!is_null($protocolo))
                if($('#pre_def').val()!=''){
                    guardar_cie10_PRO();
                    $('#pre_def').val('');
                }else{
                    alert("Seleccione Presuntivo o Definitivo");
                }
                @endif
                $('#codigo').val('');
                $('#cie10').val('');     
            });

            function guardar_cie10_PRO(){
                //alert($("#pre_def").val());
                $.ajax({
                    type: 'post',
                    url:"{{route('epicrisis.agregar_cie10')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'hcid': {{$protocolo->hcid}}, 'hc_id_procedimiento': {{$protocolo->id_hc_procedimientos}}, 'in_eg': null },
                    success: function(data){
                        console.log(data);
                        
                        
                        var indexr = data.count-1 
                        var table = document.getElementById("tdiagnostico");
                        var row = table.insertRow(indexr);
                        row.id = 'tdiag'+data.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+data.cie10+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = data.pre_def;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = data.descripcion;
                        

                           
                       
                        
                    },
                    error: function(data){
                            
                        }
                })
            }

            function eliminar(id_h){

                
                var i = document.getElementById('tdiag'+id_h).rowIndex;
                
                document.getElementById("tdiagnostico").deleteRow(i);

                $.ajax({
                  type: 'get',
                  url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
                  datatype: 'json',
                  
                  success: function(data){
                    
                  },
                  error: function(data){
                     
                  }
                });
            }

            function cargar_tabla(){
                $.ajax({
                        url:"{{route('epicrisis.cargar',['id' => $protocolo->id_hc_procedimientos])}}",
                        dataType: "json",
                        type: 'get',
                        success: function(data){
                            
                            var table = document.getElementById("tdiagnostico");

                            $.each(data, function (index, value) {
                                
                                var row = table.insertRow(index);
                                row.id = 'tdiag'+value.id;
                               
                                var cell1 = row.insertCell(0);
                                cell1.innerHTML = '<b>'+value.cie10+'</b>';
                                var cell2 = row.insertCell(1);
                                cell2.innerHTML = value.pre_def;
                                var cell3 = row.insertCell(2);
                                cell3.innerHTML = value.descripcion;
                                
                                                   
                            });

                        }
                    })    
            }
        </script>                    
                            
        <script>
            $("#limpiar").click( function(){
                $('#nombre_generico').val(''); 
            });
            $("#nombre_generico").autocomplete({
                source: function( request, response ) {
                        
                    $.ajax({
                        url:"{{route('receta.buscar_nombre')}}",
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                        data: {
                            term: request.term
                              },
                        dataType: "json",
                        type: 'post',
                        success: function(data){
                            response(data);
                            //console.log(data);
                        }
                    })
                },
                minLength: 2,
            } );
            
            $("#nombre_generico").change( function(){
                var variable1; 
                var variable2;
                $.ajax({
                    type: 'post',
                    url:"{{route('receta.buscar_nombre2')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $("#nombre_generico"),
                    success: function(data){
                        if(data!='0'){
                            //console.log(data);
                            if(data.dieta == 1 ){
                                anterior = $('#prescripcion').val();
                                $('#prescripcion').empty().html(anterior
                                    + data.value +': \n' +data.dosis);
                                cambiar_receta_2();
                            }
                            if(data.dieta == 0){
                                Crear_detalle(data);
                            }
                        }
                        
                    },
                    error: function(data){
                            //console.log(data);
                        }
                })
            });
            $("#prescripcion").change( function(){
               cambiar_receta_2();
            });
            $("#rp").change( function(){
               cambiar_receta_2();
            });


            function cambiar_receta_2(){

                $.ajax({
                    type: 'post',
                    url:"{{route('receta.update_receta_2')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $("#final_receta").serialize(),
                    success: function(data){
                        
                    },
                    error: function(data){
                            //console.log(data);
                        }
                })
            }

            @if(!is_null($hc_receta))

            function Crear_detalle(med){
                var js_cedula = document.getElementById("id_paciente").value;
                //alert(js_cedula);
                $.ajax({
                  type: 'get',
                  url:"{{url('receta_detalle/crear_detalle')}}"+"/"+{{$hc_receta->id}}+"/"+med.id+"/"+js_cedula, //receta.crear_detalle
                  //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  
                  datatype: 'json',
                  //data: $("#frm").serialize(),
                  success: function(data){
                    console.log(data);
                    if(data == 1){
                        console.log(med);
                        if(med.genericos == null){
                            anterior2 = $('#rp').val();
                            $('#rp').empty().html(anterior2 +'\n'+ med.value +':  ' +med.cantidad);
                            anterior = $('#prescripcion').val();
                            $('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                            cambiar_receta_2(); 
                        }else{
                            anterior2 = $('#rp').val();
                            $('#rp').empty().html(anterior2 +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad);
                            anterior = $('#prescripcion').val();
                            $('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
                            cambiar_receta_2(); 
                        }
                        
                    }else{
                        $('#index').empty().html(data); 
                    }
                    console.log(data);
                  },
                  error: function(data){
                     //console.log(data);
                  }
                });
            }

            function index(){
                
                $.ajax({
                  type: 'get',
                  url:"{{route('receta.index_detalle',['receta' => $hc_receta->id])}}",
                  //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  
                  datatype: 'json',
                  //data: $("#frm").serialize(),
                  success: function(data){
                    $('#index').empty().html(data);
                  },
                  error: function(data){
                     //console.log(data);
                  }
                });
            }

            function det_delete(id) {
                $.ajax({
                  type: 'get',
                  url:"{{url('receta_detalle/eliminar_detalle')}}/"+{{$hc_receta->id}}+"/"+id,
                  //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  
                  datatype: 'json',
                  //data: $("#frm").serialize(),
                  success: function(data){
                    $('#index').empty().html(data);
                  },
                  error: function(data){
                     //console.log(data);
                  }
                });
            }

            @endif


            function guardar3(){
                $.ajax({
                  type: 'post',
                  url:"{{route('receta.paciente')}}",
                  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  
                  datatype: 'json',
                  data: $("#frm").serialize(),
                  success: function(data){
                    //console.log(data);
                  },
                  error: function(data){
                    //console.log(data);
                  }
                });
            }

            function guardar2(){
                $.ajax({
                  type: 'post',
                  url:"{{route('receta.guardar2')}}",
                  headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                  
                  datatype: 'json',
                  data: $("#form2").serialize(),
                  success: function(data){
                    //console.log(data);
                  },
                  error: function(data){
                    //console.log(data);
                  }
                });
            }

            var vartiempo = setInterval(function(){ location.reload(); }, 7201000)
        </script>
    </div>
</div>        
