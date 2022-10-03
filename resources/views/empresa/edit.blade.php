@extends('empresa.base')

@section('action-content')
<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-sm-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('eempresa.EditarEmpresa')}}</h3>
                </div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('empresa.update', ['id' => $empresa->id]) }}">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <!--RUC-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-xs-6 control-label">{{trans('eempresa.RUC')}}</label>
                            <input id="id" type="text" class="form-control" name="id" value="{{ $empresa->id }}" required autofocus>
                            @if ($errors->has('id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--Razón Social-->
                        <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                            <label for="razonsocial" class="col-xs-6 control-label">{{trans('eempresa.RazónSocial')}}</label>
                            <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ $empresa->razonsocial }}" required autofocus>
                            @if ($errors->has('razonsocial'))
                            <span class="help-block">
                                <strong>{{ $errors->first('razonsocial') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-xs-8 control-label">{{trans('eempresa.NombreComercial')}}</label>
                            <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ $empresa->nombrecomercial }}" required autofocus>
                            @if ($errors->has('nombrecomercial'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombrecomercial') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!--Tipo Empresa-->
                        <div class="form-group col-xs-6{{ $errors->has('persona_nat_jur') ? ' has-error' : '' }}">
                            <label for="persona_nat_jur" class="col-xs-8 control-label">{{trans('eempresa.tipoempresa')}} </label>
                            <div class="col-md-6">
                                <select id="persona_nat_jur" name="persona_nat_jur" class="form-control" onchange="mostrardatos()">

                                    <option value="{{ $empresa->persona_nat_jur }}">{{trans('ecamilla.natural')}}</option>
                                    <option value="{{ $empresa->persona_nat_jur }}">{{trans('ecamilla.juridico')}}</option>

                                </select>
                            </div>
                        </div>
                        <!--Nombre Contador-->
                        <div class="form-group col-xs-6{{ $errors->has('id_contador') ? ' has-error' : '' }}">
                            <label for="id_contador" class="col-xs-8 control-label">{{trans('empresa.contador')}}</label>
                            <div class="col-md-12">
                                <select class="form-control select2_contador" style="width: 100%;" name="nombre_proveedor" id="nombre_proveedor">
                                    <option value="{{$empresa->id_contador}}">@if(!is_null($empresa->id_contador)){{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}}@endif</option>

                                </select>
                            </div>
                        </div>

                        <!--numero de registro del contador-->
                        <div class="form-group col-xs-6{{ $errors->has('num_registro') ? ' has-error' : '' }}">
                            <label for="num_registro" class="col-xs-12 control-label">{{trans('empresa.numregistro')}}</label>
                            <div class="col-md-12">
                                <input id="num_registro" type="text" class="form-control" name="num_registro" value="{{$empresa->num_registro_contador}}">

                            </div>
                        </div>
                        <!--Abreviatura profesional del contador-->
                        <div class="form-group col-xs-6{{ $errors->has('pref_contador') ? ' has-error' : '' }}">
                            <label for="pref_contador" class="col-xs-12 control-label">{{trans('empresa.abreviaturaprofesional')}}</label>
                            <div class="col-md-6">
                                <select id="pref_contador" name="pref_contador" class="form-control">
                                    <option value="{{$empresa->pref_contador}}">@if(!is_null($empresa->pref_contador)){{$empresa->pref_cont->titulo_prefijo}}@endif</option>
                                    @foreach($prefijos as $value)
                                    <option value="{{$value->id}}">{{$value->titulo_prefijo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--Abreviatura profesional del Representante-->
                        <div class="form-group col-xs-6{{ $errors->has('pref_representante') ? ' has-error' : '' }}">
                            <label for="pref_representante" class="col-xs-12 control-label">{{trans('empresa.abreviaturaprofesional2')}}</label>
                            <div class="col-md-6">
                                <select id="pref_representante" name="pref_representante" class="form-control">
                                    <option value="{{$empresa->pref_representante}}">@if(!is_null($empresa->pref_representante)){{$empresa->pref_repre->titulo_prefijo}}@endif</option>
                                    @foreach($prefijos as $value)
                                    <option value="{{$value->id}}">{{$value->titulo_prefijo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <!--Nombre Representate Legal-->
                         <div class="form-group col-xs-6{{ $errors->has('id_contador') ? ' has-error' : '' }}">
                            <label for="id_contador" class="col-xs-8 control-label">{{trans('empresa.replegal')}}</label>
                            <div class="col-md-12">
                                <select class="form-control select2_contador" style="width: 100%;" name="id_representante" id="id_representante">
                                    <option value="{{$empresa->id_representante}}"> @if(!is_null($empresa->id_representante)){{$empresa->usuario_representante->nombre1}}{{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}@endif</option>

                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('empresa_representante') ? ' has-error' : '' }}">
                            <label for="empresa_representante" class="col-xs-10 control-label">{{trans('empresa.emprepresentativa')}}</label>
                            <div class="col-md-7">
                                <textarea id="empresa_representante" name="empresa_representante" rows="2" cols="30"> </textarea>
                            </div>
                        </div>

              
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-xs-6 control-label">{{trans('eempresa.Ciudad')}}</label>
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $empresa->ciudad }}" required>
                            @if ($errors->has('ciudad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('ciudad') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-xs-6 control-label">{{trans('eempresa.Dirección')}}</label>
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $empresa->direccion }}" required>
                            @if ($errors->has('direccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--email-->
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-xs-6 control-label">{{trans('eempresa.Email')}}</label>
                            <input id="email" type="text" class="form-control" name="email" value="{{ $empresa->email }}" required>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-xs-8 control-label">{{trans('eempresa.TeléfonoDomicilio')}}</label>
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $empresa->telefono1 }}" required>
                            @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
                            </span>
                            @endif
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-xs-8 control-label">{{trans('eempresa.TeléfonoCelular')}}</label>
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ $empresa->telefono2 }}" required>
                            @if ($errors->has('telefono2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono2') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-xs-6 control-label">{{trans('eempresa.Estado')}}</label>
                            <select id="estado" name="estado" class="form-control">
                                <option {{$empresa->estado == 0 ? 'selected' : ''}} value="0">{{trans('ecamilla.INACTIVO')}}</option>
                                <option {{$empresa->estado == 1 ? 'selected' : ''}} value="1">{{trans('ecamilla.ACTIVO')}}</option>

                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('admision') ? ' has-error' : '' }}">
                            <label for="admision" class="col-xs-6 control-label">{{trans('eempresa.Admisión')}}</label>
                            <select id="admision" name="admision" class="form-control">
                                <option {{$empresa->admision == 0 ? 'selected' : ''}} value="0">{{trans('econsultam.NO')}}</option>
                                <option {{$empresa->admision == 1 ? 'selected' : ''}} value="1">{{trans('econsultam.SI')}}</option>

                            </select>
                            @if ($errors->has('admision'))
                            <span class="help-block">
                                <strong>{{ $errors->first('admision') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('prioridad') ? ' has-error' : '' }}">
                            <label for="prioridad" class="col-xs-6 control-label">{{trans('eempresa.Prioridad')}}</label>
                            <select id="prioridad" name="prioridad" class="form-control">
                                <option {{$empresa->prioridad == 0 ? 'selected' : ''}} value="0">{{trans('econsultam.NO')}}</option>
                                <option {{$empresa->prioridad == 1 ? 'selected' : ''}} value="1">{{trans('econsultam.SI')}}</option>

                            </select>
                            @if ($errors->has('prioridad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('prioridad') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('electronica') ? ' has-error' : '' }}">
                            <label for="electronica" class="col-xs-6 control-label">{{trans('eempresa.Electrónica')}}</label>
                            <select id="electronica" name="electronica" class="form-control">
                                <option {{$empresa->electronica == 0 ?  'selected' : ''}} value="0">{{trans('econsultam.NO')}}</option>
                                <option {{$empresa->electronica == 1 ?  'selected' : ''}} value="1">{{trans('econsultam.SI')}}</option>


                            </select>
                            @if ($errors->has('electronica'))
                            <span class="help-block">
                                <strong>{{ $errors->first('electronica') }}</strong>
                            </span>
                            @endif
                        </div>


                        <div id="appid1" class="form-group col-xs-6{{ $errors->has('appid') ? ' has-error' : '' }}">
                            <label for="appid" class="col-xs-6 control-label">{{trans('eempresa.AppID')}}</label>
                            <input id="appid" type="text" class="form-control" name="appid" value="{{ $empresa->appid }} " requiered>
                            @if ($errors->has('appid'))
                            <span class="help-block">
                                <strong>{{ $errors->first('appid') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div id="appsecret1" class="form-group col-xs-6{{ $errors->has('appsecret') ? ' has-error' : '' }}">
                            <label for="appsecret" class="col-xs-6 control-label">{{trans('eempresa.AppSecret')}}</label>
                            <input id="appsecret" type="text" class="form-control" name="appsecret" value="{{ $empresa->appsecret }}" requiered>
                            @if ($errors->has('appsecret'))
                            <span class="help-block">
                                <strong>{{ $errors->first('appsecret') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div id="url1" class="form-group col-xs-6{{ $errors->has('url') ? ' has-error' : '' }}">
                            <label for="url" class="col-xs-6 control-label">{{trans('eempresa.URL')}}</label>
                            <input id="url" type="text" class="form-control" name="url" value="{{ $empresa->url }}" requiered>
                            @if ($errors->has('url'))
                            <span class="help-block">
                                <strong>{{ $errors->first('url') }}</strong>
                            </span>
                            @endif
                        </div>


                        <div id="establecimiento1" class="form-group col-xs-6{{ $errors->has('establecimiento') ? ' has-error' : '' }}">
                            <label for="establecimiento" class="col-xs-6 control-label">{{trans('eempresa.Establecimiento')}}</label>
                            <input id="establecimiento" type="text" class="form-control" name="establecimiento" value="{{ $empresa->establecimiento }}" requiered>
                            @if ($errors->has('establecimiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('establecimiento') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div id="punto_emision1" class="form-group col-xs-6{{ $errors->has('punto_emision') ? ' has-error' : '' }}">
                            <label for="punto_emision" class="col-xs-6 control-label">{{trans('eempresa.Puntodeemisión')}}</label>
                            <input id="punto_emision" type="text" class="form-control" name="punto_emision" value="{{ $empresa->punto_emision }}" requiered>
                            @if ($errors->has('punto_emision'))
                            <span class="help-block">
                                <strong>{{ $errors->first('punto_emision') }}</strong>
                            </span>
                            @endif
                        </div>
                     



                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{trans('ecamilla.Actualizar')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--right-->
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('empresa.cambiarlogo')}}</h3>
                </div>
                <form id="subir_imagen" name="subir_imagen" method="post" action="{{ route('empresa.subir_logo', ['id' => $empresa->id]) }}" class="formarchivo" enctype="multipart/form-data">
                    <input type="hidden" name="logo" value="{{$empresa->id}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        <div class="form-group col-xs-12">
                            <input type="hidden" name="carga" value="@if($empresa->logo=='') {{$empresa->logo='../logo/avatar.jpg'}} @endif">
                            <img src="../../logo/{{$empresa->logo}}" alt="Logo Image" style="width:160px;height:160px;" id="logo_empresa">
                            <!-- User image -->
                        </div>
                        <div class="form-group col-xs-12{{ $errors->has('archivo') ? ' has-error' : '' }}">
                            <label for="archivo">{{trans('empresa.agregarlogo')}} </label>
                            <input name="archivo" id="archivo" type="file" class="archivo form-control" required /><br /><br />
                            @if ($errors->has('archivo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('archivo') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">{{trans('empresa.actualizarlogo')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        var estado = $("#electronica").val();
        if (estado == 0) {

            $('#appid1').addClass("oculto");
            $('#appsecret1').addClass("oculto");
            $('#url1').addClass("oculto");
            $('#establecimiento1').addClass("oculto");
            $('#punto_emision1').addClass("oculto");

            $('#appid').removeAttr("required");
            $('#appsecret').removeAttr("required");
            $('#url').removeAttr("required");
            $('#establecimiento').removeAttr("required");
            $('#punto_emision').removeAttr("required");
        }

        $("#electronica").change(function() {

            var estado = $("#electronica").val();

            if (estado == 0) {
                $('#appid1').addClass("oculto");
                $('#appsecret1').addClass("oculto");
                $('#url1').addClass("oculto");
                $('#establecimiento1').addClass("oculto");
                $('#punto_emision1').addClass("oculto");

                $('#appid').removeAttr("required");
                $('#appsecret').removeAttr("required");
                $('#url').removeAttr("required");
                $('#establecimiento').removeAttr("required");
                $('#punto_emision').removeAttr("required");
            }

            if (estado == 1) {
                $('#appid1').removeClass("oculto");
                $('#appsecret1').removeClass("oculto");
                $('#url1').removeClass("oculto");
                $('#establecimiento1').removeClass("oculto");
                $('#punto_emision1').removeClass("oculto");

                $('#appid').attr("required", true);
                $('#appsecret').attr("required", true);
                $('#url').attr("required", true);
                $('#establecimiento').attr("required", true);
                $('#punto_emision').attr("required", true);


            }

        });
    });

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
                    <option value="{{$empresa->id_representante}}">{{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}}</option>
                    
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

        let v2 = `
        <label for="pref_representante" class="col-md-4 control-label">{{trans('empresa.abreviaturaprofesional2')}}</label>
                        <div class="col-md-4">
                            <select id="pref_representante" name="pref_representante" class="form-control">
                            <option value="{{$empresa->pref_representante}}">@if(!is_null($empresa->pref_representante)){{$empresa->pref_repre->titulo_prefijo}}@endif</option>
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