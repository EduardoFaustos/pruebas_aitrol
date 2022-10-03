@extends('laboratorio.examen.base')

@section('action-content')



<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Agregar Parámetro para {{$parametro->examen->nombre}}</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('examen.update_parametro') }}">
                        <input id="id" type="hidden" name="id" value="{{ $parametro->id}}">
                        <input id="id_examen" type="hidden" name="id_examen" value="{{ $parametro->id_examen}}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-3 control-label">Nombre</label>
                            <div class="col-md-7    ">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value="{{ $parametro->nombre }}"  required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-3 control-label">Descripcion</label>
                            <div class="col-md-7    ">
                                <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value="{{ $parametro->descripcion}}" required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('valor1') ? ' has-error' : '' }}">
                            <label for="valor1" class="col-md-3 control-label">Valor de Referencia Menor</label>
                            <div class="col-md-7    ">
                                <input id="valor1" class="form-control input-sm" type="text" name="valor1" value="{{ $parametro->valor1 }}" required autofocus>
                                @if ($errors->has('valor1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('valor1g') ? ' has-error' : '' }}">
                            <label for="valor1g" class="col-md-3 control-label">Valor de Referencia maximo</label>
                            <div class="col-md-7    ">
                                <input id="valor1g" class="form-control input-sm" type="text" name="valor1g" value="{{ $parametro->valor1g }}" required autofocus>
                                @if ($errors->has('valor1g'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor1g') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('unidad') ? ' has-error' : '' }}">
                            <label for="unidad" class="col-md-3 control-label">Unidad del Parametro</label>
                            <div class="col-md-7    ">
                                <input id="unidad" class="form-control input-sm" type="text" name="unidad" value="{{ $parametro->unidad1 }}"  required autofocus>
                                @if ($errors->has('unidad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unidad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('sexo') ? ' has-error' : '' }}">
                            <label for="sexo" class="col-md-3 control-label">Sexo de Referencia</label>
                            <div class="col-md-7">
                                <select class="form-control input-sm" name="sexo">
                                    <option @if($parametro->sexo == 1){{"selected"}}@endif value="1">Hombre</option>
                                    <option @if($parametro->sexo == 2){{"selected"}}@endif value="2">Mujer</option>
                                    <option @if($parametro->sexo == 3){{"selected"}}@endif value="3">Ambos</option>
                                </select>
                                @if ($errors->has('sexo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sexo') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('edad_ini') ? ' has-error' : '' }}">
                            <label for="edad_ini" class="col-md-3 control-label">Edad Inicial del Rango</label>
                            <div class="col-md-7    ">
                                <input id="edad_ini" class="form-control input-sm" type="number" name="edad_ini" value="{{ $parametro->edad_ini}}"  required autofocus>
                                @if ($errors->has('edad_ini'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('edad_ini') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('edad_fin') ? ' has-error' : '' }}">
                            <label for="edad_fin" class="col-md-3 control-label">Edad Final del Rango</label>
                            <div class="col-md-7    ">
                                <input id="edad_fin" class="form-control input-sm" type="number" name="edad_fin" value="{{ $parametro->edad_fin}}"  required autofocus>
                                @if ($errors->has('edad_fin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('edad_fin') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('orden') ? ' has-error' : '' }}">
                            <label for="orden" class="col-md-3 control-label">Orden</label>
                            <div class="col-md-7    ">
                                <input id="orden" class="form-control input-sm" type="number" name="orden" value="{{ $parametro->orden}}"  required autofocus>
                                @if ($errors->has('orden'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('orden') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-offset-1  col-md-10{{ $errors->has('texto_referencia') ? ' has-error' : '' }}">
                            <label for="texto_referencia" class="col-md-3 control-label">Texto de Referencia</label>
                            <div class="col-md-7">
                                <textarea id="texto_referencia" class="form-control input-sm" type="text" name="texto_referencia" value="" >{{ $parametro->texto_referencia}}</textarea> 
                                @if ($errors->has('texto_referencia'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('texto_referencia') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-10 col-md-offset-1 {{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-3 control-label">Estado</label>
                            <div class="col-md-7    ">
                                <select class="form-control input-sm" name="estado">
                                    <option @if($parametro->estado == 0){{"selected"}}@endif value="0">Inactivo</option>
                                    <option @if($parametro->estado == 1){{"selected"}}@endif value="1">Activo</option>
                                </select>
                                @if ($errors->has('estado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-6">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Agregar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>



<script type="text/javascript">

    $(document).ready(function() {
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i> Examen</a></li>');
        $(".breadcrumb").append('<li><a href="{{route('examen.parametro',['id_examen' => $parametro->id_examen])}}"></i> Parametro</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    

</script>
@endsection
