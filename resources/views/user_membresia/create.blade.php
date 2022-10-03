@extends('user_membresia.base')
@section('action-content')
<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .disableds {
        display: none;
    }

    .dogde {
        width: 100%;
        height: 20px;
    }

    .disableds2 {
        display: none;
    }

    .wells {
        background-color: #E3F2FD;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .cabecera {
        background-color: #3c8dbc;
        border-radius: 8px;
        color: white;
    }

    .borde {
        border: 2px solid #3c8dbc;
    }

    .s {
        font-size: 17px !important;
    }

    .visible {
        display: none;
    }
</style>
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Membresia Usuario</h3></div>
            <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                
                    <!--Area-->
                    <div class="form-group col-xs-12{{ $errors->has('user_id') ? ' has-error' : '' }}">
                        <label for="buscador" class="col-md-3 control-label">Identificación del Paciente</label>
                        <div class="col-md-7">
                            <input id="buscadorasesor" class="form-control input-sm" onblur="" type="text" name="buscadorasesor" value="">
                            <input type="hidden" id="iduser" name="iduser" value="">
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('membresia_id') ? ' has-error' : '' }}">
                        <label for="membresia_id" class="col-md-3 control-label">Membresia</label>
                        <div class="col-md-7">
                            <select class="form-control input-sm" name="membresia_id" id="membresia_id" required>
                                <option value="">Seleccione ...</option>
                                @foreach($membresia as $membr)
                                <option value= {{$membr->id}}>{{$membr->nombre}}</option>
                                @endforeach
                                @if ($errors->has('membresia_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('membresia_id') }}</strong>
                                    </span>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('fecha_compra') ? ' has-error' : '' }}">
                        <label for="fecha_compra" class="col-md-3 control-label">Fecha de compra</label>
                        <div class="col-md-7">
                            <input  id="fecha_compra" type="date" class="form-control" name="fecha_compra" value="{{ old('fecha_compra') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('fecha_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_compra') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('meses') ? ' has-error' : '' }}">
                        <label for="meses" class="col-md-3 control-label">Meses</label>
                        <div class="col-md-7">
                            <input  id="meses" type="text" class="form-control" name="meses" value="{{ old('meses') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('meses'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meses') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('valor_pagado') ? ' has-error' : '' }}">
                        <label for="valor_pagado" class="col-md-3 control-label">Valor Pagado</label>
                        <div class="col-md-7">
                            <input  id="valor_pagado" type="text" class="form-control" name="valor_pagado" value="{{ old('valor_pagado') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('valor_pagado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('valor_pagado') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('meses_contratados') ? ' has-error' : '' }}">
                        <label for="meses_contratados" class="col-md-3 control-label">Meses Contratados</label>
                        <div class="col-md-7">
                            <input  id="meses_contratados" type="text" class="form-control" name="meses_contratados" value="{{ old('meses_contratados') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('meses_contratados'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('meses_contratados') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <!--ESTADO 
                        <div class="form-group col-xs-12">
                            <label for="estado_titulo" class="col-md-3 texto">Estado</label>
                            <div class="col-md-7">
                            <select id="estado_titulo" name="estado_titulo" class="form-control" required>
                                <option>Seleccione...</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            </div>
                        </div> -->
               
                        
                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary btn-gray">
                                Agregar
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function guardar(){
  
        var paciente = $("#user_id").val();
        var memb = $("#membresia_id").val();
        var fecha = $("#fecha").val();
        var mes = $("#mes").val();
        var valor = $("#valor").val();
        var meses_contratados = $("#meses_contratados").val();
        if(paciente == "" || memb == "" || fecha=="" || mes == "" || valor=="" || meses_contratados==""){
            swal("Error!", "Campos Vacios", "error");
        }else{
               $.ajax({
                url: "{{route('usermembresia.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('usermembresia.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 1000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }

    } 
    $("#buscadorasesor").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('ticketpermisos.vh_buscar_usuario')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term,
                    tipo: 7,
                },
                dataType: "json",
                type: 'post',
                success: function(data){
                    //console.log(data);
                    response(data);
                }
            })
        },
        minLength: 5,
        select: function(data, ui){
            
            //console.log("++"+ui.item.codigo);
            $('#iduser').val(ui.item.id);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    } );
</script>
@endsection