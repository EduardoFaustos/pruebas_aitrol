@extends('rrhh.empleados.base')

@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Editar Empleado</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('empleados.update', ['id' => $empleado->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                    
                    
                        <!--cedula-->
                        <div class="form-group col-md-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Cédula</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="@if(old('id')!=''){{old('id')}}@else{{$empleado->id}}@endif" required autofocus onkeyup="validarCedula(this.value);">
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--primer nombre-->
                        <div class="form-group col-md-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-4 control-label">Primer Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre1" class="form-control input-sm" type="text" name="nombre1" value="@if(old('nombre1')!=''){{old('nombre1')}}@else{{$empleado->nombre1}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-6{{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-md-4 control-label">Segundo Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre2" type="text" class="form-control input-sm" name="nombre2" value="@if(old('nombre2')!=''){{old('nombre2')}}@else{{$empleado->nombre2}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                                @if ($errors->has('nombre2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
           
                        <!--primer apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-4 control-label">Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="@if(old('apellido1')!=''){{old('apellido1')}}@else{{$empleado->apellido1}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-4 control-label">Segundo Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido2" type="text" class="form-control input-sm" name="apellido2" value="@if(old('apellido2')!=''){{old('apellido2')}}@else{{$empleado->apellido2}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
                                @if ($errors->has('apellido2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido2') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                       
                        <!--Pais-->
                        <div class="form-group col-md-6 {{ $errors->has('id_pais') ? ' has-error' : '' }}">
                            <label for="id_pais" class="col-md-4 control-label">Pais</label>
                            <div class="col-md-7">
                                <select id="id_pais" name="id_pais" class="form-control input-sm">
                                    @foreach($paises as $pais)
                                    <option @if(old('id_pais')==$pais->id) selected @endif @if($empleado->id_pais==$pais->id) selected @endif {{$empleado->id_pais}} value="{{$pais->id}}">{{$pais->nombre}}</option>
                                    @endforeach
                                </select>  
                                @if ($errors->has('id_pais'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_pais') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    
                        <!--Ciudad-->
                        <div class="form-group col-md-6{{ $errors->has('lugar_nacimiento') ? ' has-error' : '' }}">
                            <label for="lugar_nacimiento" class="col-md-4 control-label">Lugar Nacimiento</label>

                            <div class="col-md-7">
                                <input id="lugar_nacimiento" type="text" class="form-control input-sm" name="lugar_nacimiento" value="@if(old('lugar_nacimiento')!=''){{old('lugar_nacimiento')}}@else{{$empleado->lugar_nacimiento}}@endif"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>

                                @if ($errors->has('lugar_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lugar_nacimiento') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Direccion-->
                        <div class="form-group col-md-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">Dirección</label>

                            <div class="col-md-7">
                                <input id="direccion" type="text" class="form-control input-sm" name="direccion" value="@if(old('direccion')!=''){{old('direccion')}}@else{{$empleado->direccion}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--telefono1-->
                        <div class="form-group col-md-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">Telefono Domicilio</label>

                            <div class="col-md-7">
                                <input id="telefono1" type="text" class="form-control input-sm" name="telefono1" value="@if(old('telefono1')!=''){{old('telefono1')}}@else{{$empleado->telefono1}}@endif" required autofocus>

                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--telefono2-->
                        <div class="form-group col-md-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-4 control-label">Telefono Celular</label>

                            <div class="col-md-7">
                                <input id="telefono2" type="text" class="form-control input-sm" name="telefono2" value="@if(old('telefono2')!=''){{old('telefono2')}}@else{{$empleado->telefono2}}@endif" required autofocus>

                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!--fecha_nacimiento-->
                        <div class="form-group col-md-6{{ $errors->has('fecha_nacimiento') ? ' has-error' : $errors->has('menoredad') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right  input-sm"  name="fecha_nacimiento" id="fecha_nacimiento" value="@if(old('fecha_nacimiento')!=''){{old('fecha_nacimiento')}} @else{{$empleado->fecha_nacimiento}}@endif" required autofocus >
                                </div>
                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                    @endif   
                            </div>
                        </div>
                        
                        <!--email-->                        
                        <div class="form-group col-md-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control input-sm" name="email" value="@if(old('email')!=''){{old('email')}}@else{{$usuario->email}}@endif" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                         <!--estado civil 1=SOLTERO(A) 2=CASADO(A) 3=VIUDO(A) 4=DIVORCIADO(A) 5=UNION LIBRE-->
                        <div class="form-group col-md-6{{ $errors->has('estadocivil') ? ' has-error' : '' }}" >
                            <label for="estadocivil" class="col-md-4 control-label">Estado Civil</label>
                            <div class="col-md-7">
                            <select id="estadocivil" name="estadocivil" class="form-control input-sm" required>
                                <option value="">Seleccionar ..</option>
                                <option {{old('estadocivil') == 1 ? 'selected' : ''}} @if($empleado->estadocivil == 1) selected @endif value="1">SOLTERO(A)</option>
                                <option {{old('estadocivil') == 2 ? 'selected' : ''}} @if($empleado->estadocivil == 2) selected @endif value="2">CASADO(A)</option>
                                <option {{old('estadocivil') == 3 ? 'selected' : ''}} @if($empleado->estadocivil == 3) selected @endif value="3">VIUDO(A)</option>
                                <option {{old('estadocivil') == 4 ? 'selected' : ''}} @if($empleado->estadocivil == 4) selected @endif value="4">DIVORCIADO(A)</option>
                                <option {{old('estadocivil') == 5 ? 'selected' : ''}} @if($empleado->estadocivil == 5) selected @endif value="5">UNION LIBRE</option>
                                        
                            </select>  
                                @if ($errors->has('estadocivil'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estadocivil') }}</strong>
                                    </span>
                                @endif
                            </div>    
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('licencia') ? ' has-error' : '' }}">
                            <label for="licencia" class="col-md-4 control-label">Licencia</label>

                            <div class="col-md-7">
                                <select id="licencia" name="licencia" class="form-control input-sm">
                                    <option @if(old('licencia') == "NO") selected @endif @if($empleado->licencia == "NO") selected @endif value="NO">NO</option>
                                    <option @if(old('licencia') == "SI") selected @endif @if($empleado->licencia == "SI") selected @endif value="SI">SI</option>
                                </select> 

                                @if ($errors->has('licencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('licencia') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('auto') ? ' has-error' : '' }}">
                            <label for="auto" class="col-md-4 control-label">auto</label>

                            <div class="col-md-7">
                                <select id="auto" name="auto" class="form-control input-sm">
                                    <option @if(old('auto') == "NO") selected @endif @if($empleado->auto == "NO") selected @endif value="NO">NO</option>
                                    <option @if(old('auto') == "SI") selected @endif @if($empleado->auto == "SI") selected @endif value="SI">SI</option>
                                </select> 

                                @if ($errors->has('auto'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('auto') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('visa_trabajo') ? ' has-error' : '' }}">
                            <label for="visa_trabajo" class="col-md-4 control-label">Visa de Trabajo</label>

                            <div class="col-md-7">
                                <input id="visa_trabajo" type="text" class="form-control input-sm" name="visa_trabajo" value="@if(old('visa_trabajo')!=''){{old('visa_trabajo')}}@else{{$empleado->visa_trabajo}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('visa_trabajo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('visa_trabajo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('contacto') ? ' has-error' : '' }}">
                            <label for="contacto" class="col-md-2 control-label">Contacto</label>

                            <div class="col-md-10">
                                <input id="contacto" type="text" class="form-control input-sm" name="contacto" value="@if(old('contacto')!=''){{old('contacto')}}@else{{$empleado->contacto}}@endif" style="text-transform:uppercase; width: 95.5%; margin-left: -5px;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('contacto'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('contacto') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--parentesco-->
                        <div class="form-group col-md-6{{ $errors->has('parentesco') ? ' has-error' : '' }}" >
                                <label for="parentesco" class="col-md-4 control-label">Parentesco</label>

                                <div class="col-md-7">    
                                <select id="parentesco" name="parentesco" class="form-control input-sm" onchange="cambia_parentesco(this.value)" required>
                                    <option value="">Seleccione ..</option>
                                    <option @if(old('parentesco')=="Padre/Madre"){{"selected"}} @endif @if($empleado->parentesco=="Padre/Madre") selected @endif value="Padre/Madre">Padre/Madre</option>
                                    <option @if(old('parentesco')=="Conyugue"){{"selected"}} @endif @if($empleado->parentesco=="Conyugue") selected @endif value="Conyugue">Conyugue</option>
                                    <option @if(old('parentesco')=="Hijo(a)"){{"selected"}} @endif @if($empleado->parentesco=="Hijo(a)") selected @endif value="Hijo(a)">Hijo(a)</option>
                                    <option @if(old('parentesco')=="Hermano(a)"){{"selected"}} @endif @if($empleado->parentesco=="Hermano(a)") selected @endif value="Hermano(a)">Hermano(a)</option>
                                    <option @if(old('parentesco')=="Sobrino(a)"){{"selected"}} @endif @if($empleado->parentesco=="Sobrino(a)") selected @endif value="Sobrino(a)">Sobrino(a)</option>
                                    <option @if(old('parentesco')=="Nieto(a)"){{"selected"}} @endif @if($empleado->parentesco=="Nieto(a)") selected @endif value="Nieto(a)">Nieto(a)</option>
                                    <option @if(old('parentesco')=="Primo(a)"){{"selected"}} @endif @if($empleado->parentesco=="Primo(a)") selected @endif value="Primo(a)">Primo(a)</option>
                                </select>                  
                                @if ($errors->has('parentesco'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('parentesco') }}</strong>
                                </span>
                                @endif
                                </div>
                        </div>

                            <!--telefono1-->
                        <div class="form-group col-md-6{{ $errors->has('telefono3') ? ' has-error' : '' }}">
                            <label for="telefono3" class="col-md-4 control-label">Telefono Contacto</label>

                            <div class="col-md-7">
                                <input id="telefono3" type="text" class="form-control input-sm" name="telefono3" value="@if(old('telefono3')!=''){{old('telefono3')}}@else{{$empleado->telefono3}}@endif" required autofocus>

                                @if ($errors->has('telefono3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $("#id_tipo_usuario").change(function () {
            
            //var valor = 0;
            var estado = document.getElementById("id_tipo_usuario").value;
            
             
        });

        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'


            });
        $(".breadcrumb").append('<li><a href="{{asset('/empleados')}}"></i> Empleados</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
           

    });

    

</script>
@endsection
