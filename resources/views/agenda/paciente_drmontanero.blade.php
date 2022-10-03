@extends('agenda.base')

@section('action-content')
<style type="text/css">
    .ro{
        color: orange;
    }
    .ro_2{
        color: blue;
    }
    .radio_origen_2.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    .radio_origen_3.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Agregar Nuevo paciente</h4>    
                </div>
                <div class="box-body">
                    <div class="alert alert-warning m1 oculto">
                        <strong>Atencion!</strong> <span id="alertms"></span>
                    </div>
                    <div class="alert alert-warning m2 oculto">
                        <strong>Atencion!</strong> <span id="alertms2"></span>
                    </div>
                    <span class="help-block">@if($user != Array()){{'**Usuario ya registrado en el sistema'}}@endif</span>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('agenda.guardar') }}" id="form">
                        {{ csrf_field() }}
                        <input id="doctor"  type="hidden"  name="doctor" value="{{$id}}" " >
                        <input id="fecha"  type="hidden"  name="fecha" value="{{$fecha}}" " >
                        <input type="hidden" name="sala" id="sala" value="{{$sala}}">    
                        

                        <!--cedula-->
                        <div class="form-group col-md-6 {{ $errors->has('id2') ? ' has-error' : '' }}">
                            <label for="id2" class="col-md-4 control-label">* Cédula</label>
                            <div class="col-md-7">
                                <input id="id2" maxlength="10" type="text" class="form-control input-sm" name="id2" value="@if($user != Array()) {{$user[0]->id}} @elseif($i != '' && $i != '0'){{$i}} @else{{old('id')}}@endif" required autofocus onkeyup="validarCedula(this.value);">
                                @if ($errors->has('id2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div> 

                         <!--parentesco-->
                        <div class="form-group col-md-6{{ $errors->has('parentesco') ? ' has-error' : '' }}">
                            <label for="parentesco" class="col-md-4 control-label">* Parentesco</label>
                            <div class="col-md-7">
                                <select id="parentesco" name="parentesco" class="form-control input-sm" required >

                                    <option value="">Seleccione .. </option>
                                    <option @if(old('parentesco')== 'Principal') selected @endif value="Principal">Principal</option>
                                    <option @if(old('parentesco')== 'Padre/Madre') selected @endif value="Padre/Madre">Padre/Madre</option> 
                                    <option @if(old('parentesco')== 'Conyuge') selected @endif value="Conyugue">Conyuge</option>
                                    <option @if(old('parentesco')== 'Hijo(a)') selected @endif value="Hijo(a)">Hijo(a)</option>
                                    <option @if(old('parentesco')== 'Hermano(a)') selected @endif value="Hermano(a)">Hermano(a)</option>
                                    <option @if(old('parentesco')== 'Sobrino(a)') selected @endif value="Sobrino(a)">Sobrino(a)</option>
                                    <option @if(old('parentesco')== 'Nieto(a)') selected @endif value="Nieto(a)">Nieto(a)</option>
                                    <option @if(old('parentesco')== 'Primo(a)') selected @endif value="Primo(a)">Primo(a)</option>
                                    <option @if(old('parentesco')== 'Familiar') selected @endif value="Familiar">Familiar</option>
                                     
                                </select>  
                                @if ($errors->has('parentesco'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('parentesco') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 

                                     
                        
                        <!--PRIMER NOMBRE-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre12') ? ' has-error' : '' }}">
                            <label for="nombre12" class="col-md-4 control-label">* Primer Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre12" type="text" class="form-control input-sm" name="nombre12" value="{{ old('nombre12') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="busca_usuario_nombre1();">
                                @if ($errors->has('nombre12'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre12') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre22') ? ' has-error' : '' }}">
                            <label for="nombre22" class="col-md-4 control-label">* Segundo Nombre</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="nombre22" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre22" value="{{ old('nombre22') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="busca_usuario_nombre1();">
                                    <ul class="dropdown-menu usuario3" id="dnombre22">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div> 
                                @if ($errors->has('nombre22'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre22') }}</strong>
                                </span>
                                @endif

                                       
                             </div>
                        </div>
                        
           
                        <!--primer apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido12') ? ' has-error' : '' }}">
                            <label for="apellido12" class="col-md-4 control-label">* Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido12" type="text" class="form-control input-sm" name="apellido12" value="{{ old('apellido12') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="busca_usuario_nombre1();">
                                @if ($errors->has('apellido12'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido12') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-6 {{ $errors->has('apellido22') ? ' has-error' : '' }}">
                            <label for="apellido22" class="col-md-4 control-label">* Segundo Apellido</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="apellido22" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido22" value="{{ old('apellido22') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="busca_usuario_nombre1();">
                                    <ul class="dropdown-menu usuario4">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div> 
                                @if ($errors->has('apellido22'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido22') }}</strong>
                                </span>
                                @endif

                                       
                             </div>
                        </div>

                        <!--id_tipo_seguro-->
                        <div class="form-group col-md-6{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-md-4 control-label">* Seguro</label>
                            <div class="col-md-7">
                                <select id="id_tipo_seguro" name="id_seguro" class="form-control input-sm" required>
                                        <option value="">Seleccione ..</option>
                                    @foreach($seguros as $seguro) 
                                        <option @if(old('id_seguro')== $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>   
                                    @endforeach
                                </select>  
                                
                                @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>    
                      
                                             
                        <!--telefono1-->
                        <div class="form-group col-md-6{{ $errors->has('telefono12') ? ' has-error' : '' }}">
                            <label for="telefono12" class="col-md-4 control-label">Telefono Domicilio</label>

                            <div class="col-md-7">
                                <input id="telefono12" type="text" class="form-control input-sm" name="telefono12" value="{{ old('telefono12') }}" autofocus>

                                @if ($errors->has('telefono12'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono12') }}</strong>
                                    </span>
                                @endif
                                
                            </div>
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-md-6{{ $errors->has('telefono22') ? ' has-error' : '' }}">
                            <label for="telefono22" class="col-md-4 control-label">* Telefono Celular</label>

                            <div class="col-md-7">
                                <input id="telefono22" type="text" class="form-control input-sm" name="telefono22" value="{{ old('telefono22') }}" required autofocus>

                                @if ($errors->has('telefono22'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono22') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Pais-->
                        <div class="form-group col-md-6{{ $errors->has('id_pais2') ? ' has-error' : '' }}">
                            <label for="id_pais2" class="col-md-4 control-label">* Pais</label>
                            <div class="col-md-7">
                                
                                <select id="id_pais2" name="id_pais2" class="form-control input-sm" onchange="pais();">
                                    @foreach($pais as $pvalor)
                                    <option @if(old('id_pais2') == $pvalor->id) selected @endif value="{{$pvalor->id}}">{{$pvalor->nombre}}</option>
                                    @endforeach
                                </select>  
                                @if ($errors->has('id_pais2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-6{{ $errors->has('fecha_nacimiento2') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento2" class="col-md-4 control-label">* Fecha Nacimiento</label>

                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input onchange="edad();" id="fecha_nacimiento2" type="text" class="form-control input-sm" name="fecha_nacimiento2" value="{{ old('fecha_nacimiento2') }}" required autofocus placeholder="1980/01/01">
                                    @if ($errors->has('fecha_nacimiento2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento2') }}</strong>
                                    </span>
                                    @endif
                                </div>        
                            </div>
                        </div>
                        
                        <!-- Div de edad -->
                        <div class="form-group col-md-3{{ $errors->has('Xedad') ? ' has-error' : '' }}">
                            <label for="Xedad" class="col-md-4 control-label">Edad</label>

                            <div class="col-md-7">
                                <input id="Xedad" type="text" class="form-control input-sm" name="Xedad" readonly="readonly">

                                @if ($errors->has('Xedad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Xedad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--menoredad-->
                        <div class="form-group col-md-3{{ $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="menoredad" class="col-md-4 control-label input-sm">Menor de Edad</label>

                            <div class="col-md-7">
                                <input id="tmenoredad" type="text" class="form-control input-sm" name="tmenoredad" required autofocus  readonly="readonly">
                                <input id="menoredad" type="hidden" class="form-control input-sm" name="menoredad" >

                                @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12 {{ $errors->has('id') ? ' has-error' : '' }}">&nbsp;</div>
                        <h4 class="box-title">Datos del Representante </h4>
                        <div class="form-group col-md-6 {{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">* Cédula</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="" required autofocus onchange="usuario();" @if($user != Array()){{'readonly'}}@endif >
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--email-->                        
                        <div class="form-group col-md-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">* E-Mail</label>

                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control input-sm" name="email" value="@if($user != Array()){{$user[0]->email}}@elseif(old('email')!=''){{ old('email') }}@else @ @endif" required @if($user != Array()){{'readonly'}}@endif>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Primer nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-4 control-label">* Primer Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre1" type="text" class="form-control input-sm" name="nombre1" value="@if($user != Array()){{$user[0]->nombre1}}@else{{ old('nombre1') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="busca_usuario_nombre();">
                                @if ($errors->has('nombre1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-md-4 control-label">* Segundo Nombre</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="@if($user != Array()){{$user[0]->nombre2}}@else{{ old('nombre2') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus @if($user != Array()){{'readonly'}}@endif required onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        
           
                        <!--primer apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-4 control-label">* Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="@if($user != Array()){{$user[0]->apellido1}}@else{{ old('apellido1') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="busca_usuario_nombre();">
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-6 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-4 control-label">* Segundo Apellido</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="@if($user != Array()){{$user[0]->apellido2}}@else{{ old('apellido2') }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus @if($user != Array()){{'readonly'}}@endif required onchange="busca_usuario_nombre();">
                                    <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                      
                                             

                        <input id="telefono1" type="hidden" class="form-control" name="telefono1" value="@if($user != Array()){{$user[0]->telefono1}}@else{{ old('telefono1') }}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>

                        <input id="telefono2" type="hidden" class="form-control input-sm" name="telefono2" value="@if($user != Array()){{$user[0]->telefono2}}@else{{ old('telefono2')}}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>

                         <input id="id_pais" type="hidden" class="form-control" name="id_pais" value="@if($user != Array()){{$user[0]->id_pais}}@else{{old('id_pais')}}@endif" required autofocus @if($user != Array()){{'readonly'}}@endif>
                        

                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right input-sm"  name="fecha_nacimiento" id="fecha_nacimiento" value="@if($user != Array()) {{$user[0]->fecha_nacimiento}} @elseif(old('fecha_nacimiento')!='') {{ old('fecha_nacimiento') }} @endif" required autofocus @if($user != Array()){{'readonly'}}@endif onchange="copiafecha(event);"  placeholder="1980/01/01">
                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('menoredad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('menoredad') }}</strong>
                                    </span>
                                @endif    
                            </div>
                        </div>

                         <div class="form-group col-md-12{{ $errors->has('fecha_nacimiento') ? ' has-error' : $errors->has('menoredad') ? ' has-error' : '' }}">&nbsp;</div>


                        <!--terceraedad
                        <div class="form-group col-md-6" >
                            <label for="terceraedad" class="col-md-4 control-label">Tercera Edad</label>

                            <div class="col-md-7">
                                <input id="terceraedad" type="text" class="form-control input-sm" name="terceraedad" required autofocus  readonly="readonly">
                             </div>
                        </div>-->


                        <!--origen-->
                        <div class="form-group col-md-6" >
                            <label for="origen" class="col-md-4 control-label">Origen</label>

                            <div class="col-md-7">
                                <input class="radio_origen" type="radio" name="origen" required id="medio_impreso" value="MEDIO IMPRESO"> <span class="ro"> MEDIO IMPRESO</span><br>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_2" type="radio" name="origen_impreso" value="REVISTA"> <span class="ro_2"> REVISTA</span>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_2" type="radio" name="origen_impreso" value="FLYERS"> <span class="ro_2"> FLYERS</span>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_2" type="radio" name="origen_impreso" value="PERIODICO"> <span class="ro_2"> PERIODICO</span><br>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_2" type="radio" name="origen_impreso" id="origen_otros" value="OTROS"> <span class="ro_2"> OTROS</span> <span>&nbsp;&nbsp;&nbsp;</span><input class="input-sm" type="text" name="impreso_otros" id="impreso_otros" maxlength="100"><br>
                                <input class="radio_origen" type="radio" name="origen" id="medio_digital" required value="MEDIO DIGITAL"> <span class="ro"> MEDIO DIGITAL</span><br>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_3" type="radio" name="origen_digital" value="FACEBOOK"> <span class="ro_2"> FACEBOOK</span>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_3" type="radio" name="origen_digital" value="INSTAGRAM"> <span class="ro_2"> INSTAGRAM</span>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_3" type="radio" name="origen_digital" value="EMAIL"> <span class="ro_2"> EMAIL</span><br>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_3" type="radio" name="origen_digital" value="GOOGLE"> <span class="ro_2"> GOOGLE</span> <br>
                                <span>&nbsp;&nbsp;&nbsp;</span><input class="radio_origen_3" type="radio" name="origen_digital" value="OTROS" id="origen_otros2"> <span class="ro_2"> OTROS</span> <span>&nbsp;&nbsp;&nbsp;</span><input type="text" class="input-sm" name="digital_otros" id="digital_otros" maxlength="100"><br>
                                <input class="radio_origen" type="radio" name="origen" required value="REFERIDO" id="ireferido"> <span class="ro"> REFERIDO (a)</span><span>&nbsp;&nbsp;&nbsp;</span><input type="text" class="input-sm" name="referido" id="referido"><br>
                             </div>
                        </div>
                        <!--cedula-->
                        
                       
                            
          

                            


                        
                        

                     
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        
                    </form>
                </div>    
                
            </div>
        </div>
    </div>    
</section>



<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/paciente.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">

$(function () {
    $('input[type="radio"].radio_origen').iCheck({
        
        radioClass: 'iradio_square-orange',
    });
    $('input[type="radio"].radio_origen_2').iCheck({
        
        radioClass: 'iradio_square-blue',
    });
    $('input[type="radio"].radio_origen_3').iCheck({
      
        radioClass: 'iradio_square-green',
    });
    $('#medio_impreso').on('ifChecked', function(event){
        //habilito los campos
        $(".radio_origen_2").iCheck('enable');
        $("#impreso_otros").removeAttr('disabled');
        //campos requeridos
        $(".radio_origen_2").attr("required", "true");
        //deshabilito otros campos
        $(".radio_origen_3").iCheck('disable');
        $("#digital_otros").attr("disabled", "disabled");
        $("#referido").attr("disabled", "disabled");
        //quito requeridos otros campos
        $(".radio_origen_3").removeAttr('required');
        $("#digital_otros").removeAttr('required');
        $("#referido").removeAttr('required');


    });
    $('#medio_digital').on('ifChecked', function(event){
        //habilito los campos
        $(".radio_origen_3").iCheck('enable');
        $("#digital_otros").removeAttr('disabled');
        //campos requeridos
        $(".radio_origen_3").attr("required", "true");
        //deshabilito otros campos
        $(".radio_origen_2").iCheck('disable');
        $("#impreso_otros").attr("disabled", "disabled");
        //quito requeridos otros campos
        $(".radio_origen_2").removeAttr('required');
        $("#impreso_otros").removeAttr('required');
        
    
    });
    
    $('#ireferido').on('ifChecked', function(event){
        //habilito los campos
        $("#referido").removeAttr('disabled');
        //campos requeridos
        $("#referido").attr("required", "true");
        //deshabilito otros campos
        $(".radio_origen_2").iCheck('disable');
        $(".radio_origen_3").iCheck('disable');
        $("#impreso_otros").attr("disabled", "disabled");
        $("#digital_otros").attr("disabled", "disabled");
        //quito requeridos otros campos
        $(".radio_origen_2").removeAttr('required');
        $(".radio_origen_3").removeAttr('required');
        $("#impreso_otros").removeAttr('required');
        $("#digital_otros").removeAttr('required');
        
    
    });
    
    $('#origen_otros').on('ifChecked', function(event){
        $("#impreso_otros").attr("required", "true");
    

    });

    $('#origen_otros2').on('ifChecked', function(event){
        $("#digital_otros").attr("required", "true");
    

    });
    $('#fecha_nacimiento2').datetimepicker({
        format: 'YYYY/MM/DD'


    });
        
    $('#fecha_nacimiento').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
         //Important! See issue #1075
        
    });
    $("#fecha_nacimiento2").on("dp.change", function(e) {
         copiafecha(e);
         
    });
    <?php if($user == Array()) {?>
    <?php } ?>       
});

/*$("#fecha_nacimiento").on("dp.change", function(e) {
     copiafecha(e);
     
});*/

$(document).ready(function () {

    edad();
        /*var valor2 = document.getElementById("id").value;
        var nombre1 = document.getElementById("nombre1").value;
        var nombre2 = document.getElementById("nombre2").value;
        var apellido1 = document.getElementById("apellido1").value;
        var apellido2 = document.getElementById("apellido2").value;
        var telefono1 = document.getElementById("telefono1").value;
        var telefono2 = document.getElementById("telefono2").value;
        var fecha = document.getElementById("fecha_nacimiento").value;
        var pais = document.getElementById("id_pais").value;
        var relacion = document.getElementById("parentesco").value;*/
    var relacion = document.getElementById("parentesco").value;
    var valor2 = document.getElementById("id2").value;
    var nombre1 = document.getElementById("nombre12").value;
    var nombre2 = document.getElementById("nombre22").value;
    var apellido1 = document.getElementById("apellido12").value;
    var apellido2 = document.getElementById("apellido22").value;
    var telefono1 = document.getElementById("telefono12").value;
    var telefono2 = document.getElementById("telefono22").value;
    var fecha = document.getElementById("fecha_nacimiento2").value;
    var pais = document.getElementById("id_pais2").value;
    $("#fecha_nacimiento").val(fecha);
    $("#id_pais").val(pais);    

    if (relacion == "Principal") {
         
            /*$("#id2").val(valor2);
            $("#nombre12").val(nombre1); 
            $("#nombre22").val(nombre2);
            $("#apellido12").val(apellido1);
            $("#apellido22").val(apellido2);
            $("#telefono12").val(telefono1);
            $("#telefono22").val(telefono2);
            $("#fecha_nacimiento2").val(fecha);
            $("#id_pais2").val(pais);
            $("#id2").attr("readonly","readonly");
            $("#nombre12").attr("readonly","readonly"); 
            $("#nombre22").attr("readonly","readonly");
            $("#apellido12").attr("readonly","readonly");
            $("#apellido22").attr("readonly","readonly");
            $("#telefono12").attr("readonly","readonly");
            $("#telefono22").attr("readonly","readonly");
            $("#fecha_nacimiento2").attr("readonly","readonly");
            $("#id_pais2").attr("readonly","readonly");*/
            $("#id").val(valor2);
            $("#nombre1").val(nombre1); 
            $("#nombre2").val(nombre2);
            $("#apellido1").val(apellido1);
            $("#apellido2").val(apellido2);
            $("#telefono1").val(telefono1);
            $("#telefono2").val(telefono2);
        }else{
            /*$("#id2").removeAttr("readonly");
            $("#nombre12").removeAttr("readonly"); 
            $("#nombre22").removeAttr("readonly");
            $("#apellido12").removeAttr("readonly");
            $("#apellido22").removeAttr("readonly");
            $("#telefono12").removeAttr("readonly");
            $("#telefono22").removeAttr("readonly");
            $("#fecha_nacimiento2").removeAttr("readonly");
            $("#id_pais2").removeAttr("readonly");*/

        }
    

    $("#parentesco").click(function () {
        /*var valor2 = document.getElementById("id").value;
        var nombre1 = document.getElementById("nombre1").value;
        var nombre2 = document.getElementById("nombre2").value;
        var apellido1 = document.getElementById("apellido1").value;
        var apellido2 = document.getElementById("apellido2").value;
        var telefono1 = document.getElementById("telefono1").value;
        var telefono2 = document.getElementById("telefono2").value;
        var fecha = document.getElementById("fecha_nacimiento").value;
        var pais = document.getElementById("id_pais").value;
        
        var valor2 = document.getElementById("id2").value;*/
        
        var relacion = document.getElementById("parentesco").value;
        var valor2 = document.getElementById("id2").value;
        var nombre1 = document.getElementById("nombre12").value;
        var nombre2 = document.getElementById("nombre22").value;
        var apellido1 = document.getElementById("apellido12").value;
        var apellido2 = document.getElementById("apellido22").value;
        var telefono1 = document.getElementById("telefono12").value;
        var telefono2 = document.getElementById("telefono22").value;
        var fecha = document.getElementById("fecha_nacimiento2").value;
        var pais = document.getElementById("id_pais2").value;
        $("#fecha_nacimiento").val(fecha);
        $("#id_pais").val(pais);
        

        if (relacion == "Principal") {
            /*$("#id2").val(valor2);
            $("#nombre12").val(nombre1); 
            $("#nombre22").val(nombre2);
            $("#apellido12").val(apellido1);
            $("#apellido22").val(apellido2);
            $("#telefono12").val(telefono1);
            $("#telefono22").val(telefono2);
            $("#fecha_nacimiento2").val(fecha);
            $("#id_pais2").val(pais);
            edad(); 
            $("#id2").attr("readonly","readonly");
            $("#nombre12").attr("readonly","readonly"); 
            $("#nombre22").attr("readonly","readonly");
            $("#apellido12").attr("readonly","readonly");
            $("#apellido22").attr("readonly","readonly");
            $("#telefono12").attr("readonly","readonly");
            $("#telefono22").attr("readonly","readonly");
            $("#fecha_nacimiento2").attr("readonly","readonly");
            $("#id_pais2").attr("readonly","readonly");*/
            $("#id").val(valor2);
            $("#nombre1").val(nombre1); 
            $("#nombre2").val(nombre2);
            $("#apellido1").val(apellido1);
            $("#apellido2").val(apellido2);
            $("#telefono1").val(telefono1);
            $("#telefono2").val(telefono2);
        }else{
            /*
            $("#id2").removeAttr("readonly");
            $("#nombre12").removeAttr("readonly"); 
            $("#nombre22").removeAttr("readonly");
            $("#apellido12").removeAttr("readonly");
            $("#apellido22").removeAttr("readonly");
            $("#telefono12").removeAttr("readonly");
            $("#telefono22").removeAttr("readonly");
            $("#fecha_nacimiento2").removeAttr("readonly");
            $("#id_pais2").removeAttr("readonly");*/

        }
    }); 

    <?php if($user == Array()) {?> 

    /*$("#id").keyup(function () {
        var relacion = document.getElementById("parentesco").value;    
        var value = $(this).val();
        validarCedula(value);

        
        if (relacion == "Principal") {
           $("#id2").val(value); 
        } 
    });



    $("#nombre1").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#nombre12").val(value);
      }
    });

    $("#nombre2").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
      
        if (relacion == "Principal") {
        $("#nombre22").val(value);}
    });

    $("#apellido1").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
      
        if (relacion == "Principal") {
        $("#apellido12").val(value);}
    });

    $("#apellido2").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#apellido22").val(value);}
    });

    $("#telefono1").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#telefono12").val(value); }
    });

    $("#telefono2").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
       
        if (relacion == "Principal") {
        $("#telefono22").val(value);}
    });*/

    $("#id2").keyup(function () {
        var relacion = document.getElementById("parentesco").value;    
        var value = $(this).val();
        validarCedula(value);

        
        if (relacion == "Principal") {
           $("#id").val(value); 
        } 
    });

    $("#nombre12").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#nombre1").val(value);
      }
    });

    $("#nombre22").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
      
        if (relacion == "Principal") {
        $("#nombre2").val(value);}
    });

    $("#apellido12").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
      
        if (relacion == "Principal") {
        $("#apellido1").val(value);}
    });

    $("#apellido22").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        if (relacion == "Principal") {
        $("#apellido2").val(value);}
    });

    $("#telefono12").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        $("#telefono1").val(value);
    
    });

    $("#telefono22").keyup(function () {
        var value = $(this).val();
        var relacion = document.getElementById("parentesco").value;
        
        $("#telefono2").val(value);
    
    });

    function pais(){
        var pais2 = document.getElementById("pais2").value;
        $("#id_pais").val(pais2);
    }
    


    /*$(function() {
    $('.usuario1 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;

        if (relacion == "Principal") {
        $("#nombre22").val('(' + $(this).attr('data-value') + ')');

        } 

        busca_usuario_nombre();
    });})

    $(function() {
    $('.usuario2 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;
 
        if (relacion == "Principal") {
        $("#apellido22").val('(' + $(this).attr('data-value') + ')');} 

        busca_usuario_nombre();
    });})*/

    $(function() {
        $('.usuario3 a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode')
          .val('(' + $(this).attr('data-value') + ')');

            var relacion = document.getElementById("parentesco").value;

            if (relacion == "Principal") {
            $("#nombre2").val('(' + $(this).attr('data-value') + ')');

            } 

            busca_usuario_nombre();
        });
    })

    $(function() {
    $('.usuario4 a').click(function() {
    $(this).closest('.dropdown').find('input.nombrecode')
      .val('(' + $(this).attr('data-value') + ')');

        var relacion = document.getElementById("parentesco").value;
 
        if (relacion == "Principal") {
        $("#apellido2").val('(' + $(this).attr('data-value') + ')');} 

        busca_usuario_nombre();
    });})


    <?php } ?>

    $(function() {
    $('.usuario3 a').click(function() {
        $(this).closest('.dropdown').find('input.nombrecode').val('(' + $(this).attr('data-value') + ')');
        busca_usuario_nombre1(); 

    });})



    




});

function copiafecha(e)
{
     
    var relacion = document.getElementById("parentesco").value;
    if (relacion == "Principal") {  
        //var valor = document.getElementById("fecha_nacimiento").value;
        //$("#fecha_nacimiento2").val(valor)
        var valor = document.getElementById("fecha_nacimiento2").value;
        $("#fecha_nacimiento").val(valor);
        edad();
    }
      
}
function usuario()
{
        vcedula = document.getElementById("id").value;
        
        vcedula =  vcedula.trim();
       
        if (vcedula != ""){
            /*location.href =" {{route('agenda.paciente2', ['id' => $id])}}/"+vcedula+"/{{$fecha}}/{{$sala}}";*/
        }
}

var busca_usuario_nombre = function ()
{
    
    var jnombre1 = document.getElementById('nombre1').value;
    var jnombre2 = document.getElementById('nombre2').value;
    var japellido1 = document.getElementById('apellido1').value;
    var japellido2 = document.getElementById('apellido2').value;

    if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
    {
        
        $('#alertms').empty().html('');
        $(".m1").addClass("oculto"); 
        $.ajax({
        type: 'get',
        url:'{{ route('paciente.pacientexnombre')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
        success: function(data){
            
                if(data!='0'){
                    $('#alertms').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                    $(".m1").removeClass("oculto");    
                }    
                
            },
        
        })
    
    }    
    
    
   

    
    
}

var busca_usuario_nombre1 = function ()
{
    
    var jnombre1 = document.getElementById('nombre12').value;
    var jnombre2 = document.getElementById('nombre22').value;
    var japellido1 = document.getElementById('apellido12').value;
    var japellido2 = document.getElementById('apellido22').value;

    if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
    {
        
        $('#alertms2').empty().html('');
        $(".m2").addClass("oculto"); 
        $.ajax({
        type: 'get',
        url:'{{ route('paciente.pacientexnombre')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
        success: function(data){
            
                if(data!='0'){
                    $('#alertms2').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                    $(".m2").removeClass("oculto");    
                }    
                
            },
        
        })
    
    }    
    
    
   

    
    
}
    
</script>

@endsection

