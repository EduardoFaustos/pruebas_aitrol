
@extends('agenda.base')

@section('action-content')

 
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--link rel="stylesheet" href="{{ asset("/css/screen.css")}}"-->

 
<section class="content" >
    <div class="row">

        <div class="col-md-12">
              
            <div class="box box-primary">
                <div class="box-header with-border" style="padding: 5px;">
                    <div class="col-md-12">
                        <h4> AGREGAR NUEVA AGENDA PARA EL DR(A). {{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}} </h4>

                    </div>             
                </div>
                <div class="box-body">
                          
                    <form class="form-vertical" id="target" role="form" method="POST" action="{{ route('agenda.nuevo_reunion_guardar') }}">
                        <input type="hidden" name="clase" value="{{$i}}">
                        <input id="id_doctor1" type="hidden" name="id_doctor1" value="{{$doctor->id}}" >
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control input-sm" name="unix" id="unix" value="{{$unix}}">
                                     
                        <div class="form-group col-md-6 cl_inicio {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">Inicio</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$hora}}" name="inicio" class="form-control" id="inicio" required>
                                </div>
                                    @if ($errors->has('inicio'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('inicio') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 cl_fin {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Fin</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fin') }}" name="fin" class="form-control" id="fin" required>
                                </div>
                                @if ($errors->has('fin'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fin') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif
                                
                            </div>
                        </div>

                        <div class="form-group col-md-6 cl_observaciones {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-4 control-label">Titulo</label>
                            <div class="col-md-7">
                                <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{old('observaciones')}}" required="required">
                                <span class="help-block">
                                        <strong id="str_observaciones"></strong>
                                    </span>
                            </div>
                        </div>

                        <div id="tipo_clase" class="form-group col-md-6 {{ $errors->has('clase') ? ' has-error' : '' }} ">

                            <label for="clase" class="col-md-4 control-label">Tipo de Reunion</label>
                            <div class="col-md-7">
                                <select id="clase" name="clase" class="form-control input-sm" >
                                    <option  value="">Seleccione..</option> 
                                    <option @if(old('clase')=="Reuniones"){{"selected"}}@endif value="Reuniones">Reuniones</option> 
                                    <option @if(old('clase')=="Vacaciones"){{"selected"}}@endif value="Vacaciones" >Vacaciones</option>
                                    <option @if(old('clase')=="Eventos"){{"selected"}}@endif value="Eventos" >Eventos</option>
                                    <option @if(old('clase')=="Cursos"){{"selected"}}@endif value="Cursos" >Cursos</option>
                                    <option @if(old('clase')=="Otros"){{"selected"}}@endif value="Otros" >Otros</option>
                                </select>  
                                @if ($errors->has('clase'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('clase') }}</strong>
                                </span>
                                @endif
                            </div>  
                        </div>

                                     
                        <div class="form-group col-md-6 cl_id_sala {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">Ubicación</label>
                            <div class="col-md-7">
                            <select id="id_sala" name="id_sala" class="form-control" required>
                                    <option value="">Seleccione..</option>
                                    @foreach ($salas as $sala)
                                        <option @if(old('id_sala')==$sala->id){{"selected"}}@endif value="{{$sala->id}}">@if($sala->nombre_sala == "..") {{$sala->nombre_hospital}}{{$sala->nombre_sala}}@else {{$sala->nombre_sala}} / {{$sala->nombre_hospital}} @endif</option>
                                    @endforeach
                                </select>      
                                <span class="help-block">
                                        <strong id="str_id_sala"></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" >Guardar</button>
                                
                            </div>
                        </div>  
                       
                    </form>   
                </div>
            </div>
        </div> 
    </div>




</section>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
    
    $('#inicio').datetimepicker({
        format: 'YYYY/MM/DD HH:mm'
    });

    $('#fin').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD HH:mm',
        minDate: '{{$hora}}',
         //Important! See issue #1075
    });

    $("#inicio").on("dp.change", function (e) {
        $('#fin').data("DateTimePicker").minDate(e.date);
        incremento(e);
    });

    var inicio = document.getElementById("inicio").value;

    var fin = moment(inicio).add(120, 'm').format('YYYY/MM/DD HH:mm');
    $("#fin").val(fin);

    function incremento (e){
        var fjs_inicio = document.getElementById("inicio").value;
        
        var fjs_fin = moment(fjs_inicio).add(120, 'm').format('YYYY/MM/DD HH:mm');    
        

        $("#fin").val(fjs_fin);
        
    }

</script>

 


@endsection
