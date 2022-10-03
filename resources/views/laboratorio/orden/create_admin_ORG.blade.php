@extends('laboratorio.orden.base')

@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

  <style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
</style>
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Solicitar Examen</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('orden.store_admin') }}">
                        {{ csrf_field() }}
                        <!--div class="col-md-12" style="text-align: right;">
                            <b>Valor:</b><span id="xvalor"></span>    
                        </div-->
                        <input type="hidden" name="id_agenda" value="{{$agenda->id}}">
                        <input type="hidden" name="id_doctor_ieced" value="@if($agenda->id_doctor1!=''){{$agenda->id_doctor1}}@else{{'1307189140'}}@endif">
                        <!--cedula-->
                        <div class="form-group col-md-12{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-2 control-label">Cédula</label>
                            <div class="col-md-2">
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{$agenda->id_paciente}}" required autofocus onchange="buscapaciente();" readonly>
                                
                            </div>
                            <div class="col-md-8">
                                <input id="paciente" maxlength="10" type="text" class="form-control input-sm" name="paciente" value="{{$agenda->nombre1}} {{$agenda->nombre2}} {{$agenda->apellido1}} {{$agenda->apellido2}}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();" readonly>
                            </div>
                        </div>

                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-6{{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="col-md-4 control-label">Sexo</label>
                            <div class="col-md-7">
                                <select id="sexo" name="sexo" class="form-control input-sm" required>
                                    <option value="">Seleccionar ..</option>
                                    <option @if($agenda->sexo=='1') selected @endif value="1">MASCULINO</option>
                                    <option @if($agenda->sexo=='2') selected @endif value="2">FEMENINO</option>
                                            
                                </select>  
                                @if ($errors->has('sexo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sexo') }}</strong>
                                    </span>
                                @endif
                            </div>        
                        </div>

                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-6 {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }} ">
                            <label class="col-md-4 control-label">Fecha Nacimiento</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$agenda->fecha_nacimiento}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required >

                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                <span class="help-block">
                                  <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                </span>
                                @endif
                                
                            </div>
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('doctor') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Médico</label>
                            <div class="col-md-7">
                                <input id="doctor" maxlength="10" type="text" class="form-control input-sm" name="doctor" value="@if($agenda->id_doctor1!=''){{$agenda->dnombre1}} {{$agenda->dapellido1}}@else{{'CARLOS ROBLES'}}@endif" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();" readonly>
                                
                            </div>
                        </div>


                            <div class="form-group col-md-6 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                                <label for="id_seguro" class="col-md-4 control-label">Seguro</label>
                                <div class="col-md-7"> 
                                    <select id="id_seguro" name="id_seguro" class="form-control input-sm" required>
                                        <!--option value="">Seleccione ...</option-->
                                    @foreach ($seguros as $seguro)
                                    @if($agenda->id_seguro==$seguro->id)
                                        <option @if(old('id_seguro')!='')@if(old('id_seguro') == $seguro->id) selected @endif @else @if($agenda->id_seguro==$seguro->id) selected @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                                    @endif    
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                            <div class="form-group col-md-6{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                                <label for="est_amb_hos" class="col-md-4 control-label">Tipo</label>
                                <div class="col-md-7">
                                    <select id="est_amb_hos" name="est_amb_hos" class="form-control input-sm" required>
                                        <option @if(old('est_amb_hos')!='') @if(old('est_amb_hos')== '0') selected @endif @else @if($agenda->est_amb_hos=='0') selected @endif @endif value="0">Ambulatorio</option>
                                        <option @if(old('est_amb_hos')!='') @if(old('est_amb_hos')== '1') selected @endif @if($agenda->est_amb_hos=='1') selected @endif @endif value="1">Hospitalizado</option>
                                    </select>  
                                
                                    @if ($errors->has('est_amb_hos'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-md-6 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                                <label for="id_protocolo" class="col-md-4 control-label">Protocolo</label>
                                <div class="col-md-7"> 
                                    <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="Protocolo();">
                                        <option value="">Seleccione ...</option>
                                    @foreach ($protocolos as $protocolo)
                                        <option @if($protocolo->pre_post=='PRE') style="color: red;" @else style="color: blue;" @endif @if(old('id_protocolo') == $protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_protocolo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_protocolo') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                            <div class="form-group col-md-6 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                            <div id="div_empresa" class="form-group {{ $errors->has('id_empresa') ? ' has-error' : '' }}" >
                            
                            </div>
                            @if ($errors->has('id_empresa'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_empresa') }}</strong>
                                </span>
                            @endif
                            </div>

                            <!--div class="form-group col-md-6 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                                <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                                <div class="col-md-7"> 
                                    <select id="id_empresa" name="id_empresa" class="form-control input-sm" >
                                        
                                    @foreach ($empresas as $value)
                                    @if($agenda->id_empresa==$value->id)
                                        <option @if(old('id_empresa')!='') @if(old('id_empresa') == $value->id) selected @endif @else @if($agenda->id_empresa==$value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                    @endif
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_empresa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_empresa') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div-->

                        <!--div class="form-group col-md-12{{ $errors->has('doctor_txt') ? ' has-error' : '' }}">
                            <label for="doctor_txt" class="col-md-2 control-label">Doctor Externo</label>

                            <div class="col-md-10" style="margin-left: -5px;">
                                <input id="doctor_txt" type="text" class="form-control input-sm" name="doctor_txt" value="{{ old('doctor_txt') }}" style="text-transform:uppercase; width: 95.5%;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

                                @if ($errors->has('doctor_txt'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doctor_txt') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div-->    


                        <!--div class="form-group col-md-12{{ $errors->has('observacion') ? ' has-error' : '' }}">
                            <label for="observacion" class="col-md-2 control-label">Observación</label>

                            <div class="col-md-10" style="margin-left: -5px;">
                                <input id="observacion" type="text" class="form-control input-sm" name="observacion" value="{{ old('observacion') }}" style="text-transform:uppercase; width: 95.5%;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

                                @if ($errors->has('observacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div-->

                        @php $cont=count(old('examen')) @endphp 
                        <!--div class="form-group col-md-12 {{ $errors->has('examen') ? ' has-error' : '' }}">
                            <label for="examen" class="col-md-2 control-label">Exámenes </label>
                            <div class="col-md-10" style="margin-left: -5px;">
                                    <select id="examen" class="form-control input-sm select2" name="examen[]" multiple="multiple" data-placeholder="Seleccione"
                                    style="width: 95.5%;" >
                                        @foreach($examenes as $examen)
                                            <option  @for($x=0; $x<$cont; $x++) @if(old('examen.'.$x)==$examen->id) selected @endif @endfor value="{{$examen->id}}">{{$examen->nombre}}:   $ {{$examen->valor}}  </option>
                                        @endforeach
                                    </select>

                                @if ($errors->has('examen'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('examen') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div-->
<!--div class="col-md-12">                        
@foreach($agrupadores as $agrupador)
<div class="col-md-4">    
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
        <div class="table-responsive">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row" style="background-color: #00bfff;">
                <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                <th >Sel.</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($examenes as $value)
            @if($value->id_agrupador==$agrupador->id)
              <tr role="row">
                <td>{{$value->nombre}}</td>
                <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
              </tr>
            @endif  
            @endforeach
            </tbody>
          </table>
        </div>
      
    </div>
</div>    
@endforeach    
</div-->


<div class="col-md-12">                        
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='1')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach 
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='3')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>

        @endif    
        @endforeach   
        @foreach($agrupadores as $agrupador)
        @if($agrupador->id=='4')
    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='2')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach      
</div>

<div class="col-md-12">                        
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='5')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach

    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='6')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach 

    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='7')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>

        @endif    
        @endforeach   
        
         
</div>                         

                    

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button id="boton" type="submit" class="btn btn-primary">
                                    <span class="ionicons ion-ios-flask"></span> Solicitar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
    var contador = 0; 


//Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    }) 
    

    $(document).ready(function() {

        @foreach($examenes as $examen)
            @if($examen->estado=='0')
                $('#ch{{$examen->id}}').iCheck('disable');
            @endif
        @endforeach

        $("#boton").prop('disabled', true);

        $('input[type="checkbox"].flat-green').on('ifChecked', function(event){
            contador = contador + 1;

            //alert(contador);
            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }
            
            
        });

        Protocolo();

        empresas();

        



        $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
            //Protocolo();
            
            

            var protocolo = document.getElementById('id_protocolo').value;
            var textid = this.id; 
            //alert(this.id);

            if(protocolo!=''){

                $.ajax({
                    type: 'get',
                    url: "{{ url('protocolo/busca/examen')}}/"+protocolo+"/"+this.id, //protocolo.buscaexamenid
                       
                    success: function(data){
                        //alert("ahora");
                        //console.log(data);
                        //alert(data);
                        if(data=='ok'){
                            alert("No puede quitar examen del protocolo"); 
                            $('#'+textid).prop('checked',true).iCheck('update');   
                        }else{
                            contador = contador - 1;    
                            //alert('resta');
                            //alert(contador);   
                        }
                        
                    }    
                });

            }else{

                contador = contador - 1;    
                //alert('resta');
                //alert(contador); 

            }

            if(contador>0){
                $("#boton").prop('disabled', false);    
            }else{
                $("#boton").prop('disabled', true);
            }



        });

        $("#id_tipo_usuario").change(function () {
            
            //var valor = 0;
            var estado = document.getElementById("id_tipo_usuario").value;
            
             
        });



        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'


            });
        
        @if($url_doctor=='0')
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        
        $(".breadcrumb").append('<li><a href="{{asset('/agenda_procedimiento/pentax_procedimiento')}}">Pentax</li>');
        @if($agenda->id_doctor1!='')
        $(".breadcrumb").append('<li><a href="{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $url_doctor])}}">Editar</li>');
        @else
        $(".breadcrumb").append('<li><a href="{{ route('preagenda.edit', ['id' => $agenda->id])}}">Editar</li>');
        @endif
        $(".breadcrumb").append('<li class="active">Orden</li>');
        @else
        $(".breadcrumb").append('<li><a href="{{asset('/agenda')}}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => $url_doctor]) }}"></i> Doctor</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $url_doctor])}}">Editar</li>');
        $(".breadcrumb").append('<li class="active">Orden</li>');
        @endif


        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');    
        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

        $('#fechaini').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

        $('.select2').select2();

        $('#examen').on('select2:select', function (e) {
            var data = e.params.data;
            //console.log(data);
        });
           

    });

     

    var buscapaciente = function ()
    {
    

        var js_paciente = document.getElementById('id').value;
        
        $.ajax({
            type: 'get',
            url: "{{ url('hospitalizados/buscapaciente')}}/"+js_paciente, //hospitalizados.buscapaciente
                       
            success: function(data){
                if(data=='no'){
                    $('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#procedencia').focus();
                }
            }    
        });  
    
    }

    function Protocolo(){

        contador = 0;
        $('input[type="checkbox"].flat-green').prop('checked',false).iCheck('update');
        

        var protocolo = document.getElementById('id_protocolo').value;
        //alert(protocolo);
 
        if(protocolo!=''){

            $.ajax({
                type: 'get',
                url: "{{ url('protocolo/busca/examen')}}/"+protocolo, //protocolo.buscaexamen
                       
                success: function(data){
                    if(data!='no'){
                    
                        data.forEach(function(element) {
                            //alert('ch'+element.id_examen);
                            $('#ch'+element.id_examen).prop('checked',true).iCheck('update');
                        
                            contador = contador + 1;
                            //alert(contador);
                            
                            if(contador>0){
                                $("#boton").prop('disabled', false);    
                            }else{
                                $("#boton").prop('disabled', true);
                            }
                        });
                 
                    }
                }    
            });

        }else{
            $("#boton").prop('disabled', true);    
        }

    } 

    function empresas()
    {
    vseguro = document.getElementById("id_seguro").value;

    vseguro =  vseguro.trim();

    if (vseguro != ""){
            
            $.ajax({
                type: 'get',
                url: "{{url('convenios/admision')}}/"+vseguro+"/{{$agenda->id}}/@if(old('id_subseguro')!=''){{old('id_empresa')}}@else{{'0'}}@endif",
                success: function(data){
                    if(data!="null"){
                        $('#div_empresa').empty().html(data);
                        $('#div_empresa').removeClass("oculto");        
                    }
                }
            })  
        }    
    }


    



    

</script>
@endsection
