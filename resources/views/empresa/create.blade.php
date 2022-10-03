@extends('empresa.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">{{trans('empresa.agregarnuevaempresa')}}</h3>
            </div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('empresa.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">

                    <!--RUC-->
                    <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="id" class="col-md-4 control-label">{{trans('empresa.ruc')}}</label>
                        <div class="col-md-7">
                            <input id="id" type="text" class="form-control" name="id" value="{{ old('id') }}" style="text-transform:uppercase;" maxlength="13" required autofocus onkeyup="validarCedula(this.value);">
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--Razon Social-->
                    <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                        <label for="razonsocial" class="col-md-4 control-label">{{trans('empresa.razonsocial')}}</label>
                        <div class="col-md-7">
                            <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ old('razonsocial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('razonsocial'))
                            <span class="help-block">
                                <strong>{{ $errors->first('razonsocial') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--Nombre Comercial-->
                    <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                        <label for="nombrecomercial" class="col-md-4 control-label">{{trans('empresa.nombrecomercial')}}</label>
                        <div class="col-md-7">
                            <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ old('nombrecomercial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                        </div>
                    </div>

                    <!--Tipo Empresa-->
                    <div class="form-group col-xs-6{{ $errors->has('persona_nat_jur') ? ' has-error' : '' }}">
                        <label for="persona_nat_jur" class="col-md-4 control-label">{{trans('eempresa.tipoempresa')}} </label>
                        <div class="col-md-6">
                            <select id="persona_nat_jur" name="persona_nat_jur" class="form-control" onchange="mostrardatos()">
                                <option>{{trans('ecamilla.seleccione')}}</option>
                                <option value="1">{{trans('ecamilla.natural')}}</option>
                                <option value="2">{{trans('ecamilla.juridico')}}</option>

                            </select>
                        </div>
                    </div>
                    <!--Nombre Contador-->
                    <div class="form-group col-xs-6{{ $errors->has('id_contador') ? ' has-error' : '' }}">
                        <label for="id_contador" class="col-md-4 control-label">{{trans('empresa.contador')}}</label>
                        <div class="col-md-7">
                            <select class="form-control select2_contador" style="width: 100%;" name="nombre_proveedor" id="nombre_proveedor">

                            </select>
                        </div>
                    </div>

                    <!--numero de registro del contador-->
                    <div class="form-group col-xs-6{{ $errors->has('num_registro') ? ' has-error' : '' }}">
                        <label for="num_registro" class="col-md-4 control-label">{{trans('empresa.numregistro')}}</label>
                        <div class="col-md-7">
                            <input id="num_registro" type="text" class="form-control" name="num_registro" value="{{ old('num_registro') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                        </div>
                    </div>
                    <!--Abreviatura profesional del contador-->
                    <div class="form-group col-xs-6{{ $errors->has('pref_contador') ? ' has-error' : '' }}">
                        <label for="pref_contador" class="col-md-4 control-label">{{trans('empresa.abreviaturaprofesional')}}</label>
                        <div class="col-md-6">
                            <select id="pref_contador" name="pref_contador" class="form-control">
                                <option>{{trans('ecamilla.seleccione')}}</option>
                                @foreach($prefijos as $value)
                                <option value="{{$value->id}}">{{$value->titulo_prefijo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!--Abreviatura profesional del representante legal-->
                    <div class="col-md-6 pref_representante">

                    </div>

                    <!--Nombre Representate Legal-->
                    <div class="col-md-6 id_representante">

                    </div>



                    <!--Tipo Representate-->
                    <div class="col-md-6 tipo_representante">

                    </div>
                    <div>
                        &nbsp;
                    </div>

                    <!--Empresa Representante-->
                    <div class="col-md-6 empresa_representante">

                    </div>
                    <div>
                        &nbsp;
                    </div>

                    <!--Ciudad-->
                    <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                        <label for="ciudad" class="col-md-4 control-label">{{trans('empresa.ciudad')}}</label>
                        <div class="col-md-7">
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                        </div>
                    </div>

                    <!--Direccion-->
                    <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                        <label for="direccion" class="col-md-4 control-label">{{trans('empresa.direccion')}}</label>

                        <div class="col-md-7">
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                        </div>
                    </div>

                    <!--email-->
                    <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">{{trans('empresa.e-mail')}}</label>

                        <div class="col-md-7">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <!--telefono1-->
                    <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                        <label for="telefono1" class="col-md-4 control-label">{{trans('eempresa.TeléfonoDomicilio')}}</label>

                        <div class="col-md-7">
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required autofocus>


                        </div>
                    </div>

                    <!--telefono2-->
                    <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                        <label for="telefono2" class="col-md-4 control-label">{{trans('eempresa.TeléfonoCelular')}}</label>

                        <div class="col-md-7">
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ old('telefono2') }}" required autofocus>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{trans('eempresa.Agregar')}}
                            </button>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>

</div>
</div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $('.select2_contador').select2({
        placeholder: "Seleccione...",
        allowClear: true,
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: '{{route("empresa.buscar_usuario")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });


    const mostrardatos = () => {
        let id_representante = document.querySelector(".id_representante")
        let pref_representante = document.querySelector(".pref_representante")
        let tipo_representante = document.querySelector(".tipo_representante")
        let selectValor = document.getElementById("persona_nat_jur").value
        let v1 = `
                    <label for="id_representante" class="col-md-4 control-label">{{trans('empresa.replegal')}}</label>
                        <div class="col-md-7">
                            <select class="form-control select2_contador2" style="width: 100%;" name="id_representante" id="id_representante">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>                 
        `;

        let v2 = `
        <label for="pref_representante" class="col-md-4 control-label">{{trans('empresa.abreviaturaprofesional2')}}</label>
                        <div class="col-md-4">
                            <select id="pref_representante" name="pref_representante" class="form-control">
                                <option>{{trans('ecamilla.seleccione')}}</option>
                                @foreach($prefijos as $value)
                            <option value="{{$value->id}}">{{$value->titulo_prefijo}}</option>
                            @endforeach
                            </select>
                        </div>
        `;
        let v3 = `
        <label for="tipo_representante" class="col-md-4 control-label">{{trans('eempresa.tiporepresentante')}}</label>
                        <div class="col-md-6">
                            <select id="tipo_representante" name="tipo_representante" onchange="mostrarText()" class="form-control">
                                <option>{{trans('ecamilla.seleccione')}}</option>
                                <option value="1">{{trans('ecamilla.natural')}}</option>
                                <option value="2">{{trans('ecamilla.juridico')}}</option>

                            </select>
                        </div>
        `;

        if (selectValor == '2') {
            $(".id_representante").empty();
            $(".id_representante").append(v1);
            $(".pref_representante").append(v2);
            $(".tipo_representante").append(v3);
        } else {
            $(".id_representante").empty();
            $(".pref_representante").empty();
            $(".tipo_representante").empty();
        }

        $('.select2_contador2').select2({
            placeholder: "Seleccione...",
            allowClear: true,
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: '{{route("empresa.buscar_usuario")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
    }

    const mostrarText = () => {
        let empresa_representante = document.querySelector(".empresa_representante")
        let selectValor = document.getElementById("tipo_representante").value
        let textArea = `
            <label for="empresa_representante" class="col-md-4 control-label">{{trans('empresa.emprepresentativa')}}</label>
            <div class="col-md-7">
                <textarea id="empresa_representante" name="empresa_representante" rows="2" cols="40"> </textarea>
            </div>
        `;

        if (selectValor == '2') {
            $(".empresa_representante").empty();
            $(".empresa_representante").append(textArea);
        } else {
            $(".empresa_representante").empty();
        }
    }
</script>
@endsection