@extends('contable.rubros.base')
@section('action-content')

<section class="content">
    <div class="box ">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h3 class="box-title">Editar Rubro</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>
        <div class="box-body dobra">
            <form class="form-vertical" role="form" method="POST" action="{{ route('rubros_update', ['id' => $rubro->codigo]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" value="{{$rubro->codigo}}">
                    <!--Código del Rubro-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">{{trans('contableM.codigo')}}</label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" maxlength="13" class="form-control" name="codigo" value="{{$rubro->codigo}}" style="text-transform:uppercase;"  maxlength="13"  required autofocus >
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Nombre o descripción del Rubro-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">{{trans('contableM.nombre')}}</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{$rubro->nombre}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Cuenta de Débito que estará relacionada con el Rubro-->
                    <div class="form-group col-xs-6">
                        <label for="cuenta_debe" class="col-md-4 control-label">Cuenta DEBE</label>
                        <div class="col-md-7">
                            <select id="cuenta_debe" name="cuenta_debe" class="form-control select2_cuentas" required autofocus>
                                <option value="">Seleccione...</option> 
                                    @foreach($cuentas as $value)    
                                          <option {{$rubro->debe == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach   
                            </select>
                        </div>
                    </div>
                    <!--Cuenta de Crédito que estará relacionada con el Rubro-->
                    <div class="form-group col-xs-6">
                        <label for="cuenta_haber" class="col-md-4 control-label">Cuenta HABER</label>
                        <div class="col-md-7">
                            <select id="cuenta_haber" name="cuenta_haber" class="form-control select2_cuentas" required autofocus>
                                <option value="">Seleccione...</option> 
                                    @foreach($cuentas as $value)    
                                          <option {{$rubro->haber == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach   
                            </select>
                        </div>
                    </div>
                    <!--Si el rubro es de Débito o Crédito-->
                    <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="tipo" class="col-md-4 control-label">{{trans('contableM.tipo')}}</label>
                        <div class="col-md-7">
                             <select id="tipo" name="tipo" class="form-control" required>
                               <option {{$rubro->tipo == 1 ? 'selected' : ''}} value="1">INGRESO</option>
                               <option {{$rubro->tipo == 0 ? 'selected' : ''}} value="0">EGRESO</option>
                            </select>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Nota adicional del Rubro.-->
                    <!--<div class="form-group col-xs-6{{ $errors->has('nota') ? ' has-error' : '' }}">
                        <label for="nota" class="col-md-4 control-label">{{trans('contableM.nota')}}</label>
                        <div class="col-md-7">
                            <textarea class="form-control" rows="2" name="nota" id="nota">{{$rubro->nota}}</textarea>
                            @if ($errors->has('nota'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nota') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>-->
                    <!--Establece si el Rubro está Activo. -->
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-4 control-label">{{trans('contableM.estado')}}</label>
                        <div class="col-md-7">
                             <select id="estado" name="estado" class="form-control" required>
                                <option  {{$rubro->estado == '1' ? 'selected' : ''}} value="1" >ACTIVO</option>
                                <option  {{$rubro->estado == '0' ? 'selected' : ''}} value="0" >INACTIVO</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <br><br>
                    <!--Button Actualizar-->
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                            {{trans('contableM.actualizar')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

<script type="text/javascript">
       
    $(document).ready(function(){
      $('.select2_cuentas').select2({
        tags: false
      });
    });


    function goBack() {
      window.history.back();
    }

</script>


