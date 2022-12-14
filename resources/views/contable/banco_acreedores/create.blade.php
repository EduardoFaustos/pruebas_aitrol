@extends('contable.comp_egreso.base')
@section('action-content')
<style type="text/css">
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        .alerta_correcto{
            position: absolute;
            z-index: 9999;
            top: 100px;
            right: 10px;
	    }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 18px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

/* Hide the browser's default checkbox */
        .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

/* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }

</style>
<script type="text/javascript">
    function goBack() {
        location.href="{{ route('acreedores_cegreso') }}";
    }
    
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="{{route('acreedores_cegreso')}}">{{trans('contableM.ComprobantedeEgreso')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.NuevoComprobantedeEgreso')}}</li>
      </ol>
    </nav>
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  {{trans('contableM.GuardadoCorrectamente')}}
</div>   
    <form class="form-vertical " method="post" id="form_guardado">
            {{ csrf_field() }}
            <div class="box">
                    <div class="box-header header_new">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9 col-sm-9 col-6">
                                    <div class="box-title " ><b>{{trans('contableM.COMPDEEGRESOSACREEDORES')}}</b></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                         <button type="button" id="botong" class="btn btn-success btn-xs btn-gray" onclick="guardar()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                        </button>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}</button>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()" style="margin-left: 10px;">
                                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body dobra">
                        <div class="row header">
                            <div class="col-md-12">
                                <div class="form-row ">
                                    <div class=" col-md-2 px-1" >
                                        <label class="label_header">{{trans('contableM.estado')}}</label>
                                        <div style="background-color: green; " class="form-control col-md-1"></div>           
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="id_factura">{{trans('contableM.id')}}:</label>
                                            <input class="form-control " type="text" name="id_factura" id="id_factura" readonly>    
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="numero_factura">{{trans('contableM.numero')}}</label>
                                            <input class="form-control " type="text" id="numero_factura" name="numero_factura" readonly>
                                        
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                        <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" value="BAN-EG" readonly>    
                                    
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="label_header" for="asiento">{{trans('contableM.asiento')}}</label>
                                            <input class="form-control " type="text" id="asiento" name="asiento" readonly>
                                        
                                    
                                    </div>
                                    
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                            <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy" value="{{date('Y-m-d')}}">
                                            <input type="hidden" name="total_favor" id="total_favor">
                                        
                                    </div>
                                </div>
                                <div class="form-row " id="no_visible">
                                    <div class=" col-md-10 px-0">
                                            <label class="label_header" for="acreedor">{{trans('contableM.concepto')}}:</label>
                                            <input class="form-control  col-md-12" type="text" name="aaa" autocomplete="off" id="aaa" >
                                    </div>
                                    <div class=" col-md-2 px-0">
                                        <input type="hidden" name="verificar_superavit" id="verificar_superavit" value="0">
                                        <label class="container col-md-12">{{trans('contableM.Chequeentregado')}}
                                            <input class="spropety" type="checkbox" id="cheque_entregado" name="cheque_entregado">
                                            <span class="checkmark"></span>

                                        </label>
                                    </div>
                                    <div class=" col-md-4  px-0">
                                            <label class="col-md-12 label_header" for="ruc">{{trans('contableM.banco')}}:</label>
                                            <select class="form-control " name="banco" id="banco">
                                            <option value="0">Seleccione..</option>
                                            @foreach($banco as $value)
                                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                            </select>
                                    </div>
                                    <div class=" col-md-2  px-0">
                                            <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.divisass')}}:</label>
                                            <select class="form-control col-md-12 "   name="divisas" id="divisas">
                                                <option value="0">Seleccione...</option>
                                                @foreach($divisas as $value)
                                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class=" col-md-2  px-1">
                                            <label class="col-md-12 label_header" for="secuencia">{{trans('contableM.cambio')}}:</label>
                                            <input class="form-control " type="text" name="secuencia" id="secuencia" value="1.00" readonly>
                                    </div>
                                    <div class=" col-md-2  px-1">
                                            <label  class="col-md-12 label_header control-label" for="numero_cheque">{{trans('contableM.NroCheque')}}</label>
                                            <input class="form-control " type="number" name="numero_cheque" id="numero_cheque"> 
                                    </div>
                                    <div class=" col-md-2  px-1">
                                            <label class="col-md-12 label_header" for="fecha_cheque">{{trans('contableM.fechacheque')}}: </label>
                                            <input class="form-control " type="date" name="fecha_cheque" id="fecha_cheque" value="{{date('Y-m-d')}}">
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        <label for="valor" class="label_header">{{trans('contableM.valor')}}</label>
                                        <input class="form-control " type="text" autocomplete="off" name="valor_cheque" id="valor_cheque" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                    
                                    </div>
                                    <div class=" col-md-3 px-0">
                                            {{ csrf_field() }} 
                                            <input type="hidden" name="superavit" id="superavit" value="0">
                                            <label class="col-md-12 label_header" for="acreedor">{{trans('contableM.acreedor')}}</label>
                                            <input type="text" id = "nombre_proveedor" name="nombre_proveedor" class= "form-control form-control-sm nombre_proveedor " onchange="cambiar_nombre_proveedor()" >
                                    </div>
                                    <div class=" col-md-2 px-1">
                                        
                                            <label class="col-md-12 label_header" for="direccion">{{trans('contableM.direccion')}}: </label>
                                            <input class="form-control " type="text" name="direccion" id="direccion" >
                                    </div>
                                    <div class=" col-md-2 px-1"> 
                                            <label class="col-md-12 label_header" for="ruc">{{trans('contableM.ruc')}}:</label>
                                            <input class="form-control " type="text" name="id_proveedor" id="id_proveedor"  readonly>
                                    </div>
                                    <div class=" col-md-2">
                                        <div class="col-md-12">&nbsp;</div>
                                        <button type="button" class="btn btn-success btn-xs btn-gray" id="aplicar_deuda" onclick="boton_deuda();">{{trans('contableM.AplicarDeuda')}}</button>
                                    </div>                                  
                                    <div class=" col-md-4  px-1">
                                            <label class="col-md-12 label_header" for="girado"> {{trans('contableM.giradoa')}}:</label>
                                            <input class="form-control " type="text" name="giradoa" id="giradoa">
                                    </div>
                                    <div class="col-md-4 px-1">
                                        <label for="" class="control-label label_header">{{trans('contableM.formasdepago')}}</label>
                                        <select name="formas_pago" id="formas_pago" class="form-control">
                                            <option value="">Seleccione..</option>
                                            @foreach($formas_pago as $value)
                                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="hidden" name="total_suma" id="total_suma">
                                </div>
                            </div>
                            <div class="col-md-12">
                            <label class="label_header" for="detalle_deuda">{{trans('contableM.DetallededeudasdelProveedor')}}</label>
                            
                                <input type="hidden" name="id_compra" id="id_compra">
                                <input type="hidden" name="contador" id="contador" value="0">
                                    <div class="table-responsive col-md-12 px-1" style="min-height: 250px; max-height: 250px;">
                                    <table id="example2" class="px-1"  role="grid" aria-describedby="example2_info">
                                        <thead style="background-color: #9E9E9E; color: white;" >
                                        <tr style="position: relative;">
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.vence')}}</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.tipo')}}</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.numero')}}</th>
                                            <th style="width: 20%; text-align: center;">{{trans('contableM.concepto')}}</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.div')}}</th>
                                            <th style="width: 15%; text-align: center;">{{trans('contableM.saldo')}}</th>
                                            <th style="width: 15%; text-align: center;">{{trans('contableM.abono')}}</th>
                                            <th style="width: 10%; text-align: center;">{{trans('contableM.saldobase')}}</th>
                                           
                                        </tr>
                                        </thead>
                                        <tbody id="crear">
                                        @php $cont=0; @endphp
                                        @foreach (range(1, 6) as $i)
                                            <tr>
                                                <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                                <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                                <td> <input class="form-control" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                                <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                                <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                                <td> <input class="form-control" style="background-color: #c9ffe5; width: 100% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                                <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                                <td> <input class="form-control" style="width: 100%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                              
                                                
                                            </tr>
                                            @php $cont = $cont +1; @endphp
                                        @endforeach
                                            
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                    </div>

                                
                                <div class="col-md-12" style="margin-top: 30px;">
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                           
                                            <input type="hidden" name="saldo_hidden0" id="saldo_hidden0">
                                            <input type="hidden" name="verificar_cheque" id="verificar_cheque" value="0">
                                            <input type="hidden" name="total_egreso" id="total_egreso" value="0">
                                        </div>
                                       
                                        <div class="form-group col-md-3" style="text-align: right;">
                                           
                                        </div>
                                        <div class="form-group col-md-2">
                                            <div class="input-group">
                                              
                                                <input type="hidden" name="proveedor" id="proveedor"> 
                                                <input type="hidden" name="sobrante" id="sobrante">
                                                <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                
                                    <input type="hidden" name="saldo_final" id="saldo_final">
                                </div>
                            </div>

                        </div>
                        </div>

            </div>
    </form>

</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#cheque_entregado').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });
   
    $('.spropety').on('ifChecked', function(event){
        $("#verificar_cheque").val(1);
    });
    $('.spropety').on('ifUnchecked', function (event) {
        $("#verificar_cheque").val(0);
    });
    function buscar_factura(){
        $("#buscar").next().remove();
        $.ajax({
            type: 'post',
            url:"{{route('acreedores_buscar_codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_factura':$("#buscar").val()},
            success: function(data){
                var esfact= $("#esfac_contable").val();
                //console.log(data);
                var iva= (data[10]*0.12); 
                $("#id_factura").val(data[0]);
                $("#numero_factura").val(data[12]);
                $("#concepto").val(data[4]+'.'+' '+'REF :'+data[0]);
                $("#asiento").val(data[11]);
                $("#id_proveedor").val(data[0]);
                $('#nombre_proveedor').val(data[2]);
                $("#direccion").val(data[3]);
                $("#total_factura").val(data[10]);
                $("#total_deudas").val(data[10]);
                //$("#id_compra").val(data[1]);
                $("#vence").val(data[6]);
                buscar_proveedor();
             
                
            },
            error: function(data){
                //console.log(data);
            }
        })
    }
    $(".buscar").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('retenciones_codigo')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
                //console.log(data);
            }
            } );
        },
        minLength: 1,
    } );
    $('#fact_contable_check').on('ifChanged', function(event){
        //aqui funciona si cambio el input time
        if($(this).prop("checked")){
            $("#esfac_contable").val(1);
        }else{
            $("#esfac_contable").val(0);
        }

    });
    function buscarAsiento(id_asiento){
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '7'
            },
            success: function(data) {

                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data[0]);
                    $('#numero_factura').val(data[1]);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    function funciones_pago(){
        var formas_pago= parseInt($("#formas_pago").val());
        switch(formas_pago){
            case 1:
                [].forEach.call(document.querySelectorAll('.'), function (el) {
                el.style.display = 'none';
                });
                break;
            case 2:
                [].forEach.call(document.querySelectorAll('.'), function (el) {
                el.style.display = 'block';
                });
                break;
        }
    }
    
    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
                if(data.value!='no'){
                    console.log(data);
                    $("#id_proveedor").val(data.value);
                    $("#direccion").val(data.direccion);
                    buscar_proveedor()
                }else{
                    $('#id_proveedor').prop('readonly', false);
                }

            },
            error: function(data){
                //console.log(data);
            }
        })
    }
    $(".nombre_proveedor").autocomplete({
        source: function( request, response ) {
            $.ajax( {
            url: "{{route('compra_buscar_nombreproveedor')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                //console.log(data)
                response(data);
            }
            } );
        },
        minLength: 1,
    } );
    function abono_totales(){
        var valor= parseFloat($("#valor_cheque").val());
        var saldo= parseFloat($("#saldo0").val());
        if(!isNaN(valor)){
            $("#abono0").val(valor);
            $("#abono_base0").val(valor);
            var totales= saldo-valor;
            //alert(totales);
            if(totales>0){
                $("#saldo0").val(totales.toFixed(2));
                var saldo_hidden= parseFloat($("#saldo_hidden0").val());
                var total_sinresta=saldo_hidden.toFixed(2)-totales.toFixed(2);
                $("#saldo_final").val(total_sinresta);
            }else{
                var saldo_hidden= parseFloat($("#saldo_hidden0").val());
                var total_sinresta=saldo_hidden.toFixed(2)-totales.toFixed(2);
                $("#saldo_final").val(total_sinresta);
                $("#saldo0").val('0');
            }
            
        }else{
            var valor =parseFloat($("#saldo_hidden0").val());
            //alert(valor); 
            $("#saldo0").val(valor);
            $("#abono_base0").val(valor);
        }
    }
    function guardar(){
        //swal("hassta aqui");
        var formulario = document.forms["form_guardado"];
        var valor_cheque= formulario.valor_cheque.value;
        var acreedores= formulario.id_proveedor.value;
        var formas_pago= formulario.formas_pago.value;
        var superavit= parseInt($("#verificar_superavit").val());
        var msj = "";
        if(valor_cheque==""){
            msj+="Por favor, Llene el valor del cheque<br/>";
        }
        if(acreedores==""){
            msj+="Por favor, Llene el campo de acreedor<br/>";
        }
        if(formas_pago==""){
            msj+="Por favor, Llene las formas de pago <br/>";
        }
        
        var vence= $("#vence0").val();
        var tipo= $("#tipo0").val();
        var numero= $("#numero0").val();
        var final_valor_cheque= parseFloat($("#valor_cheque").val());
        var total_suma= parseFloat($("#total_suma").val());

        var concepto= $("#concepto0").val();
        var saldo_final= $("#saldo_base0").val();
        if(msj==""){
            console.log(superavit);
            if(superavit==1){
                if(total_suma<final_valor_cheque){
                    swal("??Error!","El pago de las factura no cumple con el total del valor.","error");
                }else{
                    console.log("entro store");
                    $.ajax({
                        type: 'post',
                        url:"{{route('acreedores_cstore')}}",
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: 'json',
                        data: $('#form_guardado').serialize(),
                        success: function(data){
                            
                            if((data)!='false'){
                                $("#id_factura").val(data);
                                buscarAsiento(data);
                                swal(`{{trans('contableM.correcto')}}!`,"Los comprobantes se generaron con exito","success");
                                document.getElementById("aplicar_deuda").disabled = true;
                                $('#form_guardado input').attr('readonly', 'readonly');
                                document.getElementById("botong").disabled = true;
                                url="{{ url('contable/compra/comprobante/egreso/pdf/')}}/"+data;
                                window.open(url,'_blank');
                            }else{
                                
                            }                    
                        },
                        error: function(data){
                        console.log(data);
                        }
                    })
                }
               
            }else{
                if (confirm('Existe un super??vit de '+final_valor_cheque+' en la cobertura de las deudas. \n ??Desea que ??ste valor sea considerado como un D??bito a favor de la Empresa?')) {
                    $("#superavit").val(1);
                    $("#total_favor").val(final_valor_cheque);
                    $.ajax({
                    type: 'post',
                    url:"{{route('acreedores_cstore')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $('#form_guardado').serialize(),
                    success: function(data){
                        
                        if((data)!='false'){
                            buscarAsiento(data);
                            $("#id_factura").val(data);
                            $('#form_guardado input').attr('readonly', 'readonly');
                            document.getElementById("botong").disabled = true;
                            swal(`{{trans('contableM.correcto')}}!`,"El anticipo a proveedor se gener?? con exito","success");
                            document.getElementById("aplicar_deuda").disabled = true;
                        }else{
                            
                        }                    
                    },
                    error: function(data){
                      console.log(data);

                    }
                    })
                }else{
                }
            }
        }else{
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
        }

    }
    function boton_deuda(){
        var valor= parseFloat($("#valor_cheque").val());
        var valor2= parseFloat($("#valor_cheque").val());
        var valor_saldo= parseFloat($("#saldo0").val());
        var contador=parseInt($("#contador").val());
        var saldo=0
        var abono=0;
        var total=0;
        var nuevo_s=0;
        for(i=0; i<=contador; i++){
            saldo+= parseFloat($("#saldo"+i).val());
            valor_saldo= parseFloat($("#saldo"+i).val());
            valor-=valor_saldo;
            var cont= parseFloat($("#abono"+i).val());
            if(isNaN(cont)){
                cont=0;
            }
            abono+= cont;
            console.log(valor);
            if(valor>valor_saldo){
                $("#abono"+i).val(valor_saldo.toFixed(2,2));
            }else{
                total= valor+valor_saldo;
                console.log(total+" anthonby");
                if(total<=valor2 ){
                    if(total>0){
                        console.log("entra");
                        if(total>valor_saldo){
                            total=valor_saldo;
                            $("#abono"+i).val(total.toFixed(2,2));
                        }else{
                            $("#abono"+i).val(total.toFixed(2,2));
                        }
                
                    }else{
                       
                    }
                }
                
                
               
            }
            console.log("veces");
        }

        var total=0;
        if(!isNaN(valor) && !isNaN(valor_saldo)){
            /*
            if(valor_saldo<=valor){
                total= valor-valor_saldo;
                $("#verificar_superavit").val(1);
                $("#abono0").val(valor_saldo.toFixed(2,2));
            }else{
                total= valor_saldo-valor;
                $("#valor_cheque").val(total.toFixed(2,2));
                $("#verificar_superavit").val(1);
                $("#abono0").val(valor.toFixed(2,2));
            }*/
            $("#verificar_superavit").val(1);
            
            
        }else{
            swal("Error!","Ingrese valor de cheque primero","error");
        }
    }
    function setNumber(e){
       // return parseFloat(e).toFixed(2);
       //if(e.length)
       if(e==""){
        e=0;
       }
        $("#valor_cheque").val(parseFloat(e).toFixed(2))

    }
    function nuevo_comprobante(){
        location.href='{{route('acreedores_ccreate')}}';
    }
    function buscar_proveedor(){
        var proveedor= $("#id_proveedor").val();
        var tipo= parseInt($("#esfac_contable").val());
        var provedores=$("#nombre_proveedor").val();
        $("#giradoa").val(provedores);
        $.ajax({
            type: "post",
            url: "{{route('acreedores_buscarproveedor')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'proveedor':proveedor,'tipo':tipo},
            success: function(data){
                if(data.value!="no"){
                    $("#crear").empty();
                    var fila = 0;

                    for(i=0; i<data[5].length;i++){

                        if(data[5][i].tipo==1){
                            var row =addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FA', data[5][i].numero, data[5][i].proveedor, data[5][i].valor_nuevo);
                            $('#example2').append(row);
                            fila = i;
                        }else{
                            var row =addNewRow(i, data[5][i].fecha_asiento, data[5][i].valor_contable, 'COM-FACT', data[5][i].numero, data[5][i].proveedor, data[5][i].valor_nuevo);
                            $('#example2').append(row);
                            fila = i;
                        }
                    }
                   
                    $("#contador").val(fila);
                   
                }
               
            },
            error:  function(data){
               console.log(data);             

            }
        });

        

    }
    function validar_td(id){
        if((id)!=null){
            var valor= parseFloat($("#valor_cheque").val());
            var abono= parseFloat($("#abono"+id).val());
            var saldo= parseFloat($("#saldo"+id).val());
            suma_totales();
            var cantidad= parseFloat($("#total_suma").val());
            if(!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)){
                if(cantidad<=valor){
                    var uno=1;
                    $("#verificar_superavit").val(uno);
                    if(abono>saldo){
                        abono=saldo;
                    }
                    $("#abono"+id).val(abono.toFixed(2,2));
                }else{
                    valor=0;
                    $("#abono"+id).val(valor.toFixed(2,2));
                    swal("??Error!","Error no puede superar al valor del cheque","error")
                }
            }else{
                abono=0;
                valor=0;
                $("#abono"+id).val(valor.toFixed(2,2));
            }
        }else{
            alert("error");
        }
    }
    function suma_totales(){
      contador  =  0;
      iva = 0;
      total = 0;
      sub = 0;
      descu1 = 0;
      total_fin = 0;
      descu = 0;
      cantidad = 0;
     
      $("#crear tr").each(function(){
        $(this).find('td')[0];
                cantidad= parseFloat($("#abono"+contador).val());
                if(!isNaN(cantidad)){
                    total+=cantidad;
                }
            contador = contador+1;
        });
        if(isNaN(total)){ total=0;}
        $("#total_suma").val(total.toFixed(2,2));

        //alert(total_fin);

    }
    function addNewRow(pos,fecha, valor, factura, fact_numero, observacion, valor_nuevo){
        var markup = "";
        var num= parseInt(pos)+1;
        markup = "<tr>"+
                "<td> <input class='form-control' type='text' name='vence"+pos+"' id='vence"+pos+"' readonly='' value='"+fecha+"'> </td>"+
                "<td> <input class='form-control' type='text' name='tipo"+pos+"' id='tipo"+pos+"' value='"+factura+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='numero"+pos+"' id='numero"+pos+"' value='"+fact_numero+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='concepto"+pos+"' id='concepto"+pos+"' value='Fact #:"+fact_numero+" Prov: "+observacion+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='div"+pos+"' id='div"+pos+"' value='$' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo"+pos+"' value='"+valor+"' id='saldo"+pos+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono"+pos+"' id='abono"+pos+"' onchange='validar_td("+pos+")'></td>"+
                "<td> <input class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo"+pos+"' value='"+valor+"' id='nuevo_saldo"+pos+"' readonly=''></td>"+
                
            "</tr>";
        return markup;

    }


</script>

@endsection
