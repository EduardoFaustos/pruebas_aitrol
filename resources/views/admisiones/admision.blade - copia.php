
@extends('admisiones.base')

@section('action-content')
<!-- Ventana modal editar -->
<div class="modal fade " id="Editar_Principal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- Ventana modal editar -->
<div class="modal fade" id="Crear_Principal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<style type="text/css">
    
    .control-label{
        padding-left: 5px;
    }    


</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" id="frame_ventana2">
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  
        </div>
        <!--<iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="@if($segurod->url_validacion != "" ){{$segurod->url_validacion}}@endif" ></iframe> -->
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="@if($segurod->tipo == 0 )https://aplicaciones.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif @else{{$segurod->url_validacion}}@endif" ></iframe> 
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" id="frame_ventana" >
    <div class="modal-content"  id="imprimir3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  
        </div>
        <iframe style="width: 100%; height: 750px;" id="validacion" name="imprimir5" src="https://aplicaciones.msp.gob.ec/coresalud/app.php/publico/rpis/afiliacion/consulta/@if($paciente != Array()){{$paciente->id}}@elseif($i != 0){{ $i}}@else{{old('idpaciente')}}@endif" ></iframe> 
    </div>
  </div>
</div>
   
  

<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-header with-border"><h3 class="box-title">Admisión de Pacientes</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('admisiones.update', ['id' => $paciente->id]) }}" enctype="multipart/form-data" >
                    <div class="box-body">

                        
                        <div class="col-md-12" style="color: blue;">@if(!is_null($historia))** Paciente ya fue Admisionado el {{$historia->created_at}} @endif</div>   
                        
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <input type="hidden" name="cita" value="{{$cita->id}}">
                        <input type="hidden" name="ruta" value="{{$ruta}}">
                        <input type="hidden" name="url_doctor" value="{{$ruta}}">
                        <input type="hidden" name="unix" value="{{$unix}}">
                        @if(!is_null($historia))
                        <input type="hidden" name="hcid" value="{{$historia->hcid}}">
                        @endif
                         <!--seguro-->
                         @php $tipo=""; $tienecodigo=""; @endphp
                        <div class="form-group col-md-3{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-md-8 control-label">Seguro</label>
                            <select id="id_seguro" name="id_seguro" class="form-control input-sm" onchange="subseguro();" required autofocus>
                                @foreach ($seguros as $seguro)
                                    <option @if(old('id_seguro')==$seguro->id) {{"selected"}}@php $tipo=$seguro->tipo; $tienecodigo=$seguro->codigo_validacion; $cantidad2=Sis_medico\Subseguro::where('id_seguro',$seguro->id)->count(); @endphp @elseif($i == $seguro->id) {{"selected"}} @php $tipo=$seguro->tipo; $tienecodigo=$seguro->codigo_validacion; $cantidad2=Sis_medico\Subseguro::where('id_seguro',$seguro->id)->count(); @endphp @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>

                                @endforeach

                            </select>      
                            @if ($errors->has('id_seguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_seguro') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        @if($cantidad2>0)
                        <!--subseguro-->
                        <div class="form-group col-md-3 {{ $errors->has('id_subseguro') ? ' has-error' : '' }}">
                            <label for="id_subseguro" class="col-md-8 control-label">Sub-Seguro</label>
                            <select id="id_subseguro" name="id_subseguro" class="form-control input-sm" required autofocus>
                                    <option value="">Seleccione ...</option>
                                @foreach ($subseguros as $subseguro)
                                    @if($paciente->parentesco=='Principal' && $subseguro->principal=='1')
                                    <option @if(old('id_subseguro')==$subseguro->id){{"selected"}}@elseif(!is_null($historia))@if($historia->id_subseguro==$subseguro->id){{"selected"}}@endif @endif value="{{$subseguro->id}}">{{$subseguro->nombre}}</option>
                                    @endif
                                    @if($paciente->parentesco!='Principal' && $subseguro->principal=='0')
                                    <option @if(old('id_subseguro')==$subseguro->id){{"selected"}}@elseif(!is_null($historia))@if($historia->id_subseguro==$subseguro->id){{"selected"}}@endif @endif value="{{$subseguro->id}}">{{$subseguro->nombre}}</option>
                                    @endif
                                @endforeach
                            </select>     
                            @if ($errors->has('id_subseguro'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_subseguro') }}</strong>
                                </span>
                            @endif
                        </div>
                        @endif

                        @if($tipo==1)
                        <input id="copago" type="hidden" class="form-control input-sm" name="copago" value=@if(old('copago')!='') "{{old('copago')}}" @else "0" @endif required autofocus min="0" max="100"> 
                        <!--copago
                        <div class="form-group col-md-3{{ $errors->has('copago') ? ' has-error' : '' }}">
                            @if(!is_null($historia))
                            <p><b>Co-Pago(%) : </b>{{$historia->copago}} </p>
                            @else
                            <label for="copago" class="col-md-8 control-label">Co-Pago(%)</label>
                            <input id="copago" type="number" class="form-control input-sm" name="copago" value=@if(old('copago')!='') "{{old('copago')}}" @else "0" @endif required autofocus min="0" max="100"> 
                             
                                    
                            @if ($errors->has('copago'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('copago') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div> -->
                        @if($segurod->url_validacion != "" )
                        <div class="form-group col-md-3{{ $errors->has('copago') ? ' has-error' : '' }}">
                            @if($paciente != Array())
                            <br>
                                <a  data-toggle="modal" data-target="#favoritesModal2">
                                    <button type="button" class="btn btn-primary" >
                                        Consulta Cobertura
                                    </button>
                                </a>
                            @endif
                        </div>
                         @endif

                        @endif 
                        

                         
                            @if($cita->proc_consul=='1')
                            <!--procedencia-->
                            <div class="form-group col-md-3 {{ $errors->has('procedencia') ? ' has-error' : '' }} ">
                                <label for="procedencia" class="col-md-12 control-label">Procedencia</label>
                                <div class="col-md-12">
                                    <input id="procedencia" type="text" class="form-control input-sm" name="procedencia" value="@if(old('procedencia')!=''){{old('procedencia')}}@else{{$cita->procedencia}}@endif" required>
                                    @if ($errors->has('procedencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('procedencia') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                            </div>
                            @endif

                            <div class="form-group col-md-3 {{ $errors->has('id_empresa') ? ' has-error' : '' }} ">
                                <label for="id_empresa" class="col-md-12 control-label">Empresa</label>
                                <div class="col-md-12">
                                    <select id="id_empresa" name="id_empresa" class="form-control input-sm" required>
                                        <option value=""  >Seleccione..</option> 
                                        @foreach($empresas as $empresa)    
                                        <option @if(old('id_empresa')==$empresa->id){{"selected"}}@elseif($cita->id_empresa==$empresa->id){{"selected"}}@endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001" && $cita->proc_consul == "1") {{"selected"}}@endif>{{$empresa->nombrecomercial}}</option>
                                        @endforeach 
                                    </select>
                                    @if ($errors->has('id_empresa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_empresa') }}</strong>
                                    </span>
                                    @endif         
                                </div>
                            </div>

                        <!--@if($tipo==0)
                        verificar
                        @if(is_null($historia))
                        <div class="checkbox col-md-2{{ $errors->has('verificar') ? ' has-error' : '' }}">
                            
                            <label><input type="checkbox" name="verificar" value="1"  autofocus @if(old('verificar')=='1') checked @endif> Consulta Cobertura Salud</label>
                            @if ($errors->has('verificar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('verificar') }}</strong>
                                </span>
                            @endif
                        </div> 
                        @endif
                        -->

                        <!--archivo--> 
                        
                        <!--div class="form-group col-md-4{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            @if(!is_null($historia))
                                <?php // @if( $archivo_vrf==null ) ?>
                                    <a data-toggle="modal" href="#favoritesModal2">Consulta Cobertura Salud</a>
                                    <label for="archivo" class="col-md-8 control-label">Agregar Archivo</label>
                                    <input name="archivo" id="archivo" type="file"   class="archivo form-control input-sm"   autofocus/><br /><br />
                                    @if ($errors->has('archivo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('archivo') }}</strong>
                                    </span>
                                    @endif
                                <?php // @else  ?>
                                <?php // @php $archivopdf=$archivo_vrf->ruta.$archivo_vrf->archivo @endphp ?>

                                <?php // @if($archivo_vrf->archivo!="") ?>
                                <a target="_blank" <?php // href="{{asset($archivopdf)}}" ?> alt="pdf"  style="width:120px;height:120px;" id="pdf" > Consulta Cobertura Salud</a>
                                <?php // @endif ?>
                                <?php // @endif ?>
                            @else
                            <label for="archivo" class="col-md-8 control-label">Agregar Archivo</label>
                            <input name="archivo" id="archivo" type="file"   class="archivo form-control input-sm"   autofocus/><br /><br />
                            @if ($errors->has('archivo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('archivo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div  


                        @endif-->

                        <input type="hidden" name="codigo_validacion" value="{{$tienecodigo}}">
                        <input type="hidden" name="xtipo" value="{{$tipo}}">
                        @if($tienecodigo=="SI")
                        <!--codigo-->
                        <div class="form-group col-md-4{{ $errors->has('codigo') || $errors->has('fecha_codigo')? ' has-error' : '' }}">
                            @if(!is_null($historia))
                            <p><b>Codigo : </b>{{$historia->codigo}} </p>
                            @else
                            <label for="codigo" class="col-md-8 control-label">Código Validación</label>
                            <input id="codigo" type="text" class="form-control input-sm" name="codigo" value=@if(old('codigo')!='') "{{old('codigo')}}" @else "{{$paciente->codigo}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if(!is_null($historia)){{"readonly"}}@endif>     
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('fecha_codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_codigo') }}</strong>
                                </span>
                            @endif
                            @endif
                        </div> 

                        <!--fecha_codigo-->
                        <div class="form-group col-md-4{{ $errors->has('fecha_codigo') ? ' has-error' : '' }}">
                            @if(!is_null($historia))
                            <p><b>Caducidad Código : </b> {{substr($historia->fecha_codigo,0,11)}} </p>
                            @else
                            <label for="fecha_codigo" class="col-md-8 control-label">Caducidad Código</label>
                            <div class="input-group date col-md-12">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="fecha_codigo" type="text" class="form-control input-sm" name="fecha_codigo" value=@if(old('fecha_codigo')!='') "{{old('fecha_codigo')}}" @else "{{$paciente->fecha_codigo}}" @endif required autofocus >
                            </div>         
                            @if ($errors->has('fecha_codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_codigo') }}</strong>
                            </span>
                            @endif
                            @endif
                        </div>    
                        @endif 
                        
                        <fieldset class="col-md-12"><legend>Datos Básicos: </legend>
                            
                            @if(!is_null($historia))    
                            
                            <p class="col-md-3"><b>Parentesco : </b> {{$paciente->parentesco}} </p>
                            <p class="col-md-3"><b>Cédula : </b> {{$paciente->id}} </p>
                            <p class="col-md-6"><b>Nombres  : </b>  {{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}} </p>
                            <p class="col-md-3"><b>Fecha Nacimiento : </b> {{$paciente->fecha_nacimiento}}</p>
                            <input id="sexo" type="hidden" class="form-control input-sm" name="sexo" value={{$paciente->sexo}}>
                            <p class="col-md-3"><b>Sexo :</b>@if($paciente->sexo==1) {{"HOMBRE"}} @elseif($paciente->sexo==2) {{"MUJER"}} @endif </p>
                            <input id="estadocivil" type="hidden" class="form-control input-sm" name="estadocivil" value={{$paciente->estadocivil}}>
                            <p class="col-md-3"><b>Estado Civil :</b>@if($paciente->estadocivil==1) {{"SOLTERO(A)"}} @elseif($paciente->estadocivil==2) {{"CASADO(A)"}} @elseif($paciente->estadocivil==3) {{"VIUDO(A)"}} @elseif($paciente->estadocivil==5) {{"DIVORCIADO(A)"}} @elseif($paciente->estadocivil==5) {{"UNION LIBRE"}} @elseif($paciente->estadocivil==6) {{"UNION DE HECHO"}} @endif </p>
                            <p class="col-md-3"><b>Ocupación :</b>{{$paciente->ocupacion}} </p>
                            <input id="fecha_nacimiento" type="hidden" name="fecha_nacimiento" value="{{$paciente->fecha_nacimiento}}" >

                            @else
                            <!--parentesco-->
                            <div class="form-group col-md-2{{ $errors->has('parentesco') ? ' has-error' : '' }}" >
                                <label for="parentesco" class="col-md-8 control-label">Parentesco</label>    
                                <select id="parentesco" name="parentesco" class="form-control input-sm" onchange="cambia_parentesco(this.value)">
                                    <option @if(old('parentesco')=="Principal"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Principal"){{"selected"}}@endif value="Principal">Principal</option>
                                    <option @if(old('parentesco')=="Padre/Madre"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Padre/Madre"){{"selected"}}@endif value="Padre/Madre">Padre/Madre</option>
                                    <option @if(old('parentesco')=="Conyugue"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Conyugue"){{"selected"}}@endif value="Conyugue">Conyugue</option>
                                    <option @if(old('parentesco')=="Hijo(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Hijo(a)"){{"selected"}}@endif value="Hijo(a)">Hijo(a)</option>
                                    <option @if(old('parentesco')=="Hermano(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Hermano(a)"){{"selected"}}@endif value="Hermano(a)">Hermano(a)</option>
                                    <option @if(old('parentesco')=="Sobrino(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Sobrino(a)"){{"selected"}}@endif value="Sobrino(a)">Sobrino(a)</option>
                                    <option @if(old('parentesco')=="Nieto(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Nieto(a)"){{"selected"}}@endif value="Nieto(a)">Nieto(a)</option>
                                    <option @if(old('parentesco')=="Primo(a)"){{"selected"}}@elseif(old('parentesco')=="" && $paciente->parentesco == "Primo(a)"){{"selected"}}@endif value="Primo(a)">Primo(a)</option>
                                </select>                  
                                @if ($errors->has('parentesco'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parentesco') }}</strong>
                                </span>
                                @endif
                            </div> 

                            <!--cedula-->
                            <div class="form-group col-md-2{{ $errors->has('id') || $errors->has('cita')? ' has-error' : '' }}">
                                 
                                
                              
                                <label for="id" class="col-md-8 control-label">Cédula</label>
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value=@if(old('id')!='')"{{old('id')}}"@else"{{$paciente->id}}"@endif required autofocus onkeyup="validarCedula(this.value);" >
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('cita'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cita') }}</strong>
                                    </span>
                                @endif
                        
                            </div>
                        
                            <!--primer nombre-->
                            <div class="form-group col-md-2{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                                
                                
                                 
                                <label for="nombre1" class="col-md-12 control-label">Primer Nombre</label>
                                <input id="nombre1" type="text" class="form-control input-sm" name="nombre1" value=@if(old('nombre1')!='')"{{old('nombre1')}}"@else"{{$paciente->nombre1}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus  >
                                
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                          
                            </div>
               
                            <!--segundo nombre-->
                            <div class="form-group col-md-2{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                                <label for="nombre2" class="col-md-12 control-label">Segundo Nombre</label>
                                <div class="input-group dropdown col-md-12">
                                  <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value=@if(old('nombre2')!='')"{{old('nombre2')}}"@else"{{ $paciente->nombre2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                    @if(is_null($historia))     
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>     
                                @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        
                            <!--primer apellido-->
                            <div class="form-group col-md-2{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                                <label for="apellido1" class="col-md-12 control-label">Primer Apellido</label>
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value=@if(old('apellido1')!='')"{{old('apellido1')}}"@else"{{ $paciente->apellido1 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                @if ($errors->has('apellido1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        
                            <!--segundo apellido-->
                            <div class="form-group col-md-2{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                                <label for="apellido2" class="col-md-12 control-label">Segundo Apellido</label>
                                <div class="input-group dropdown col-md-12">
                                  <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value=@if(old('apellido2')!='')"{{old('apellido2')}}"@else"{{ $paciente->apellido2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus  >
                                  @if(is_null($historia))
                                 <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                  @endif  
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>    
                                @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                @endif
                            </div>

                            

                            <!--fecha_nacimiento-->
                            <div class="form-group col-md-2{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                                 
                               
                                
                                 
                                <label for="fecha_nacimiento" class="col-md-12 control-label">Fecha Nacimiento</label>
                                <div class="input-group date col-md-12">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input id="fecha_nacimiento" onchange="edad2();" type="text" class="form-control input-sm" name="fecha_nacimiento" value=@if(old('fecha_nacimiento')!='')"{{old('fecha_nacimiento')}}"@else"{{ $paciente->fecha_nacimiento }}" @endif required autofocus @if(!is_null($historia)){{"readonly"}}@endif >
                                </div>    
                                    @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                                   
                            </div>

                                
                            <!-- Div de edad -->
                            <div class="form-group col-md-2{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                                <label for="Xedad" class="col-md-8 control-label">Edad</label>
                                <input id="Xedad" type="text" class="form-control input-sm" name="Xedad" readonly="readonly">
                                @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        

                            <!--menor edad-->
                            <div class="form-group col-md-2{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                                <label for="menoredad" class="col-md-12 control-label">Menor Edad</label>
                                <input id="tmenoredad" type="text" class="form-control input-sm" name="tmenoredad" value="" required autofocus readonly="readonly">
                                <input id="menoredad" type="hidden" class="form-control input-sm" name="menoredad" value="" >
                                @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                        
                            <!--sexo 1=MASCULINO 2=FEMENINO-->
                            <div class="form-group col-md-2{{ $errors->has('sexo') ? ' has-error' : '' }}" >
                                 
                                
                               
                                <label for="sexo" class="col-md-8 control-label">Sexo</label>
                                <select id="sexo" name="sexo" class="form-control input-sm" required autofocus >
                                    <option value="">Seleccionar ..</option>
                                    <option @if(old('sexo')==1){{"selected"}}@elseif(old('sexo')=="" && $paciente->sexo == 1){{"selected"}}@endif value="1">HOMBRE</option>
                                    <option @if(old('sexo')==2){{"selected"}}@elseif(old('sexo')=="" && $paciente->sexo == 2){{"selected"}}@endif value="2">MUJER</option>
                                </select>      
                                    @if ($errors->has('sexo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sexo') }}</strong>
                                    </span>
                                    @endif
                                 
                            </div>

                            <!--estado civil 1=SOLTERO(A) 2=CASADO(A) 3=VIUDO(A) 4=DIVORCIADO(A) 5=UNION LIBRE-->
                            <div class="form-group col-md-2{{ $errors->has('estadocivil') ? ' has-error' : '' }}" >
                                 
                                
                                 
                                <label for="estadocivil" class="col-md-12 control-label">Estado Civil</label>
                                <select id="estadocivil" name="estadocivil" class="form-control input-sm" required autofocus>
                                    <option value="">Seleccionar ..</option>
                                    <option @if(old('estadocivil')==1){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 1){{"selected"}}@endif value="1">SOLTERO(A)</option>
                                    <option @if(old('estadocivil')==2){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 2){{"selected"}}@endif value="2">CASADO(A)</option>
                                    <option @if(old('estadocivil')==3){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 3){{"selected"}}@endif value="3">VIUDO(A)</option>
                                    <option @if(old('estadocivil')==4){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 4){{"selected"}}@endif value="4">DIVORCIADO(A)</option>
                                    <option @if(old('estadocivil')==5){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 5){{"selected"}}@endif value="5">UNION LIBRE</option>
                                    <option @if(old('estadocivil')==6){{"selected"}}@elseif(old('estadocivil')=="" && $paciente->estadocivil == 6){{"selected"}}@endif value="6">UNION DE HECHO</option>      
                                </select>
                                    @if ($errors->has('estadocivil'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estadocivil') }}</strong>
                                    </span>
                                    @endif
                                 
                            </div>                     

                            <!--ocupacion-->
                            <div class="form-group col-md-2{{ $errors->has('ocupacion') ? ' has-error' : '' }}">
                                 
                                
                                 
                                <label for="ocupacion" class="col-md-12 control-label">Ocupación</label>
                                <input id="ocupacion" type="text" class="form-control input-sm" name="ocupacion" value=@if(old('ocupacion')!='')"{{old('ocupacion')}}"@else"{{ $paciente->ocupacion }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                    @if ($errors->has('ocupacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ocupacion') }}</strong>
                                    </span>
                                    @endif
                                 
                            </div>
                            @endif                                 

                        </fieldset>

                        <!--fieldset class="col-md-12 "><legend>Principal: </legend>
                             
                            <div class="form-horizontal col-md-6{{ $errors->has('id_prin') ? ' has-error' : '' }}">
                                <label for="id_buscar" class="col-md-2 control-label form-horizontal" style="text-align: left;">Cédula</label>
                                <input id="id_buscar" maxlength="10" type="text" class="input-sm col-md-4" name="id_buscar" onchange="busca_principal()" value=@if(old('id_buscar')!='')"{{old('id_buscar')}}"@else"{{$paciente->id_usuario}}"@endif required autofocus onkeyup="validarCedula(this.value);" >
                                <div class="col-md-6" id="id_nombre"></div>
                            </div>
                            <div class="col-md-12">&nbsp;</div>

                        </fieldset-->    
                        
                        <fieldset class="col-md-12 oculto" id="principal"><legend>Principal: </legend>
                            <div id="div_principal"></div>

                        </fieldset>

                        <fieldset class="col-md-12"><legend>Datos de ubicación: </legend> 
                            
                            @if(!is_null($historia))
                                <input id="id_pais" type="hidden" class="form-control input-sm" name="id_pais" value={{$paciente->id_pais}}>
                                <p class="col-md-3"><b>Ciudad :</b> {{$paciente->ciudad}} - @foreach($paises as $pais) @if($paciente->id_pais==$pais->id) {{$pais->nombre}} @endif @endforeach</p>
                                <p class="col-md-5"><b>Dirección :</b> {{$paciente->direccion}} </p>
                                <p class="col-md-4"><b>E-Mail :</b> {{$user_aso->email}} </p>
                                <p class="col-md-3"><b>Teléfonos:</b> {{$paciente->telefono1}} - {{$paciente->telefono2}} </p>
                                <p class="col-md-5"><b>Lugar de Nacimiento :</b> {{$paciente->lugar_nacimiento}} </p>
                                <p class="col-md-4"><b>Referido :</b> {{$paciente->referido}} </p>
                            @else    

                            <!--pais-->
                            <div class="form-group col-md-2{{ $errors->has('id_pais') ? ' has-error' : '' }}">
                                
                                 
                                <label for="id_pais" class="col-md-8 control-label">Pais</label>
                                <select id="id_pais" name="id_pais" class="form-control input-sm" required autofocus >
                                    @foreach ($paises as $pais)
                                        <option @if(old('id_pais')==$pais->id){{"selected"}}@elseif(old('id_pais')=="" && $paciente->id_pais == $pais->id){{"selected"}}@endif value="{{$pais->id}}">{{$pais->nombre}}</option>
                                    @endforeach
                                </select>       
                                    @if ($errors->has('id_pais'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais') }}</strong>
                                    </span>
                                    @endif
                                
                            </div>
                            
                            <!--ciudad-->
                            <div class="form-group col-md-2{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                                
                              
                                 
                                <label for="ciudad" class="col-md-8 control-label">Ciudad</label>
                                <input id="ciudad" type="text" class="form-control input-sm" name="ciudad" value=@if(old('ciudad')!='')"{{old('ciudad')}}"@else"{{$paciente->ciudad}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                    @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                @endif
                                
                            </div>
                           
                            <!--direccion-->
                            <div class="form-group col-md-4{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                 
                                
                                 
                                <label for="direccion" class="col-md-8 control-label">Direccion</label>
                                <input id="direccion" type="text" class="form-control input-sm" name="direccion" value=@if(old('direccion')!='')"{{old('direccion')}}"@else"{{ $paciente->direccion }}" @endif required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                    @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                    @endif
                                 
                            </div>

                            <!--email-->                        
                            <div class="form-group col-md-4{{ $errors->has('email') ? ' has-error' : '' }}">
                                 
                                
                              
                                <label for="email" class="col-md-8 control-label">E-Mail</label>
                                <input id="email" type="email" class="form-control input-sm" name="email" value=@if(old('email')!='')"{{old('email')}}"@else"{{ $user_aso->email }}" @endif required autofocus @if($paciente->id!=$paciente->id_usuario){{'readonly'}}@endif >
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                               
                            </div> 

                            <!--telefono1-->
                            <div class="form-group col-md-2{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                                 
                                
                                 
                                <label for="telefono1" class="col-md-12 control-label">Teléfono</label>
                                <input id="telefono1" type="text" class="form-control input-sm" name="telefono1" value=@if(old('telefono1')!='')"{{old('telefono1')}}"@else"{{ $paciente->telefono1 }}" @endif required autofocus >
                                    @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                    @endif
                               
                            </div>
                           
                            <!--telefono2-->
                            <div class="form-group col-md-2{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                                
                                
                                
                                <label for="telefono2" class="col-md-10 control-label">Celular</label>
                                <input id="telefono2" type="text" class="form-control input-sm" name="telefono2" value=@if(old('telefono2')!='')"{{old('telefono2')}}"@else"{{ $paciente->telefono2 }}" @endif required autofocus >
                                    @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                    @endif
                                
                            </div>

                            <!--LUGAR NACIMIENTO-->
                            <div class="form-group col-md-4{{ $errors->has('lugar_nacimiento') ? ' has-error' : '' }}">
                                
                                
                                
                                <label for="lugar_nacimiento" class="col-md-8 control-label">Lugar de Nacimiento</label>
                                <input id="lugar_nacimiento" type="text" class="form-control input-sm" name="lugar_nacimiento" value=@if(old('lugar_nacimiento')!='')"{{old('lugar_nacimiento')}}"@else"{{ $paciente->lugar_nacimiento }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                    @if ($errors->has('lugar_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lugar_nacimiento') }}</strong>
                                    </span>
                                    @endif
                                 
                            </div> 
                       
                            <!--REFERIDO-->
                            <div class="form-group col-md-4{{ $errors->has('referido') ? ' has-error' : '' }}">
                               
                                
                               
                                <label for="referido" class="col-md-8 control-label">Referencia</label>
                                <input id="referido" type="text" class="form-control input-sm" name="referido" value=@if(old('referido')!='')"{{old('referido')}}"@else"{{ $paciente->referido }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                    @if ($errors->has('referido'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('referido') }}</strong>
                                    </span>
                                    @endif
                                
                            </div>
                            @endif

                        </fieldset> 

                        <fieldset class="col-md-12"><legend>Familiar de Contacto: </legend>
                            
                            @if(!is_null($historia))
                                <p class="col-md-6"><b>Nombres :</b> {{$paciente->nombre1familiar}} {{$paciente->nombre2familiar}} {{$paciente->apellido1familiar}} {{$paciente->apellido2familiar}} </p>
                                <p class="col-md-3"><b>Parentesco :</b> {{$paciente->parentescofamiliar}}</p>
                                <p class="col-md-3"><b>Teléfono Familiar :</b> {{$paciente->telefono3}}</p>
                            @else
                            <!--nombre1familiar-->
                            <div class="form-group col-md-2{{ $errors->has('nombre1familiar') ? ' has-error' : '' }}">
                                
                                 
                                <label for="nombre1familiar" class="col-md-12 control-label">Primer Nombre</label>
                                <input id="nombre1familiar" type="text" class="form-control input-sm" name="nombre1familiar" value=@if(old('nombre1familiar')!='')"{{old('nombre1familiar')}}"@else"{{ $paciente->nombre1familiar }}" @endif required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre1familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1familiar') }}</strong>
                                </span>
                                @endif
                                
                            </div>
                             
                            <!--nombre2familiar-->
                            <div class="form-group col-md-2{{ $errors->has('nombre2familiar') ? ' has-error' : '' }}">
                                <label for="nombre2familiar" class="col-md-12 control-label">Segundo Nombre</label>
                                <div class="input-group dropdown col-md-12">
                                    <input id="nombre2familiar" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2familiar" value=@if(old('nombre2familiar')!='')"{{old('nombre2familiar')}}"@else"{{ $paciente->nombre2familiar }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                                    @if(is_null($historia))
                                    <ul class="dropdown-menu usuario3">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif  
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>    
                                @if ($errors->has('nombre2familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2familiar') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--apellido1familiar-->
                            <div class="form-group col-md-2{{ $errors->has('apellido1familiar') ? ' has-error' : '' }}">
                                <label for="apellido1familiar" class="col-md-12 control-label">Primer Apellido</label>
                                <input id="apellido1familiar" type="text" class="form-control input-sm" name="apellido1familiar" value="{{ $paciente->apellido1familiar }}" required autofocus style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" @if(!is_null($historia)){{"readonly"}}@endif >
                                @if ($errors->has('apellido1familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1familiar') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--apellido2familiar-->
                            <div class="form-group col-md-2{{ $errors->has('apellido2familiar') ? ' has-error' : '' }}">
                                <label for="apellido2familiar" class="col-md-12 control-label">Segundo Apellido</label>
                                <div class="input-group dropdown col-md-12">
                                    <input id="apellido2familiar" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2familiar" value=@if(old('apellido2familiar')!='')"{{old('apellido2familiar')}}"@else"{{ $paciente->apellido2familiar }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if(!is_null($historia)){{"readonly"}}@endif >
                                    @if(is_null($historia))
                                    <ul class="dropdown-menu usuario4">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    @endif  
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>    
                                @if ($errors->has('apellido2familiar'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2familiar') }}</strong>
                                </span>
                                @endif
                            </div>
                             
                            <!--parentescofamiliar-->
                            <div class="form-group col-md-2{{ $errors->has('parentescofamiliar') ? ' has-error' : '' }}" >
                                 
                                
                                 
                                <label for="parentescofamiliar" class="col-md-12 control-label">Parentesco</label>
                                <select id="parentescofamiliar" name="parentescofamiliar" class="form-control input-sm">
                                    <option {{$paciente->parentescofamiliar == "Principal" ? 'selected' : ''}} value="Principal">Principal</option>
                                        <option {{$paciente->parentescofamiliar == "Padre/Madre" ? 'selected' : ''}} value="Padre/Madre">Padre/Madre</option>
                                        <option {{$paciente->parentescofamiliar == "Conyugue" ? 'selected' : ''}} value="Conyugue">Conyugue</option>
                                        <option {{$paciente->parentescofamiliar == "Hijo(a)" ? 'selected' : ''}} value="Hijo(a)">Hijo(a)</option> 
                                        <option {{$paciente->parentescofamiliar == "Hermano(a)" ? 'selected' : ''}} value="Hermano(a)">Hermano(a)</option>
                                        <option {{$paciente->parentescofamiliar == "Sobrino(a)" ? 'selected' : ''}} value="Sobrino(a)">Sobrino(a)</option>
                                        <option {{$paciente->parentescofamiliar == "Nieto(a)" ? 'selected' : ''}} value="Nieto(a)">Nieto(a)</option>
                                        <option {{$paciente->parentescofamiliar == "Primo(a)" ? 'selected' : ''}} value="Primo(a)">Primo(a)</option>   
                                    </select>  
                                    @if ($errors->has('parentescofamiliar'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parentescofamiliar') }}</strong>
                                    </span>
                                @endif
                                 
                            </div>  
                            <!--telefono3-->
                                <div class="form-group col-md-2{{ $errors->has('telefono3') ? ' has-error' : '' }}">
                                     
                                    
                                     
                                    <label for="telefono3" class="col-md-12 control-label">Teléfono Familiar</label>
                                    <input id="telefono3" type="text" class="form-control input-sm" name="telefono3" value="{{$paciente->telefono3}}" required autofocus >
                                    @if ($errors->has('telefono3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono3') }}</strong>
                                    </span>
                                    @endif
                                     
                                </div>
                            @endif
                        </fieldset>

                        <div class="form-group col-xs-6">
                            <div class="col-md-12 col-md-offset-8">
                                <button type="submit" class="btn btn-primary">
                                @if(!is_null($historia)) <span class="glyphicon glyphicon-chevron-right"></span> Continuar @else <span class="glyphicon glyphicon-floppy-disk"></span> ADMISION @endif
                                </button>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>
        </div> 



    </div>
</div> 


<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>



<script type="text/javascript">
@if(is_null($historia))



$('#fecha_codigo').bootstrapMaterialDatePicker({ 
    date: true,
    shortTime: false,
    time: false,
    format : 'YYYY-MM-DD',
    lang : 'es',
});
@endif

$(document).ready(function () {

    edad2();
    //edad2_prin();
    cambia_parentesco();
    busca_principal();

    /*$('.Antecedentes').boxWidget('toggle');*/

   $('input[type="checkbox"]').on('change', function(e){
   if(e.target.checked){
     $('#favoritesModal2').modal();
   }
});
    
    var sexo = document.getElementById("sexo").value;
        
        if(sexo==2)
        {
          
           $("#primera_mens").show(); 
           $('label[for="primera_mens"]').show(); 

           $("#menopausia").show(); 
           $('label[for="menopausia"]').show();

           $("#parto_normal").show(); 
           $('label[for="parto_normal"]').show();

           $("#parto_cesarea").show(); 
           $('label[for="parto_cesarea"]').show();

           $("#aborto").show(); 
           $('label[for="aborto"]').show(); 


           

        }else{

           $("#primera_mens").hide(); 
           $('label[for="primera_mens"]').hide();
           
           $("#menopausia").hide(); 
           $('label[for="menopausia"]').hide();

           $("#parto_normal").hide(); 
           $('label[for="parto_normal"]').hide();

           $("#parto_cesarea").hide(); 
           $('label[for="parto_cesarea"]').hide();

           $("#aborto").hide(); 
           $('label[for="aborto"]').hide();

        }   

    <?php if(is_null($historia)) { ?>
    datos_edad();
    <?php } ?>

    $(function() {
    $('.usuario1 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    

    $(function() {
    $('.usuario2 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    $(function() {
    $('.usuario3 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    
    });})

    $(function() {
    $('.usuario4 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

    });

    $('#fecha_nacimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

    $('#fecha_nacimiento_prin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });

    $("#fecha_nacimiento").on("dp.change", function (e) {
            edad2();
        });

    $("#fecha_nacimiento_prin").on("dp.change", function (e) {
            edad2_prin();
        });



})

    $("#sexo").change(function () {
        var sexo = document.getElementById("sexo").value;
        
        if(sexo==2)
        {
          
           $("#primera_mens").show(); 
           $('label[for="primera_mens"]').show(); 

           $("#menopausia").show(); 
           $('label[for="menopausia"]').show();

           $("#parto_normal").show(); 
           $('label[for="parto_normal"]').show();

           $("#parto_cesarea").show(); 
           $('label[for="parto_cesarea"]').show();

           $("#aborto").show(); 
           $('label[for="aborto"]').show(); 


           

        }else{

           $("#primera_mens").hide(); 
           $('label[for="primera_mens"]').hide();
           
           $("#menopausia").hide(); 
           $('label[for="menopausia"]').hide();

           $("#parto_normal").hide(); 
           $('label[for="parto_normal"]').hide();

           $("#parto_cesarea").hide(); 
           $('label[for="parto_cesarea"]').hide();

           $("#aborto").hide(); 
           $('label[for="aborto"]').hide();

        }   

        
        
        
    });



});

function subseguro()
{
        vseguro = document.getElementById("id_seguro").value;

        vseguro =  vseguro.trim();
       
        if (vseguro != ""){
            location.href =" {{route('admisiones.admision2', ['id' => $paciente->id, 'cita' => $cita, 'ruta' => $ruta, 'unix' => $unix ])}}/"+vseguro;
        }
}

function cambia_parentesco(){
    var parentesco = document.getElementById("parentesco").value;
    if(parentesco!="Principal"){
       $("#principal").removeClass("oculto"); 
    }
}
$(document).ready(function($){
    var ventana_ancho = $(window).width();

    
    if(ventana_ancho > "962" ){
        var nuevovalor = ventana_ancho * 0.8;
    }
    else
    {
        var nuevovalor = ventana_ancho * 0.9;
    }
    $("#frame_ventana").width(nuevovalor);
    $("#frame_ventana2").width(nuevovalor);

});

/*function edad2_prin(fechana)
{
    
    var jsnacimiento = document.getElementById("fecha_nacimiento_prin").value;
    var jsedad = calcularEdad(jsnacimiento);
    
    if(isNaN(jsedad))
    {
        $("#Xedadp").val('0');
    }
    else
    {

        $("#Xedadp").val(jsedad);
    }


    if (jsedad>=18){

       $("#tmenoredadp").val("NO");
       $("#menoredadp").val("0");
    }
    else{
        $("#tmenoredadp").val("SI");
        $("#menoredadp").val("1");
    }
       
          
}*/

var busca_principal = function ()
{

    
    $.ajax({
        type: 'get',
        url: "{{ route('admisiones.busca_principal',['id' => $paciente->id])}}",
        success: function(data){
            $('#div_principal').empty().html(data); 
        }
    })  
    
}

$('#Editar_Principal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            }); 

 $('#Crear_Principal').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            }); 



</script> 
@endsection
