@extends('contable.nota_credito_cliente.base')
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

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
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
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

    /* Hide default HTML checkbox */
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    .ocultos{
        display: none;
        width: 90%;
    }
    .ocultosp{
        width: 90%;
    }
</style>

<script type="text/javascript">
  function goBack() {
    location.href="{{ route('nota_credito_cliente.index') }}";
  }
</script>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="{{route('nota_credito_cliente.index')}}">Nota Crédito Cliente</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
        </ol>
    </nav>
    <form class="form-vertical " method="post" id="form_nota_credito_cl">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">
                            <!--<div class="box-title "><b>Crear Nota de Crédito Clientes</b></div>-->
                            <h5><b>CREAR NOTA CRÉDITO CLIENTE</b></h5>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <button type="button" id="boton_guardar_nota" onclick="guarda_nota_credito(this)"
                                    class="btn btn-success btn-gray btn-xs"><i class="glyphicon glyphicon-floppy-disk"
                                    aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                                </button>
                                <button type="button" class="btn btn-success btn-xs btn-gray"
                                    onclick="nueva_nota_credito()" style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.nuevo')}}
                                </button>
                                <button type="button" class="btn btn-success btn-xs btn-gray" onclick="goBack()"
                                    style="margin-left: 10px;">
                                    <i class="glyphicon glyphicon-arrow-left"
                                        aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
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
                            <div class=" col-md-1 px-1">
                                <label class="label_header">{{trans('contableM.estado')}}</label>
                                <div style="background-color: green; " class="form-control col-md-1"></div>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="id_nota_credito">{{trans('contableM.id')}}:</label>
                                <input class="form-control " type="text" name="id_nota_credito" id="id_nota_credito" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="numero_secuencia">Número:</label>
                                <input class="form-control " type="text" id="numero_secuencia" name="numero_secuencia"
                                    readonly>
                            </div>
                            <div class=" col-md-1 px-1">
                                <label class="col-md-12 label_header" for="tipo">{{trans('contableM.tipo')}}</label>
                                <input class="form-control " type="text" name="tipo" id="tipo" value="CLI-RP" readonly>
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="label_header" for="asiento">{{trans('contableM.asiento')}}:</label>
                                <input class="form-control " type="text" id="num_asiento" name="num_asiento" readonly>
                                @if(isset($iva_param))
                                    <input type="text" name="iva_par" id="iva_par" class="hidden"
                                    value="{{$iva_param->iva}}">
                                @endif
                            </div>
                            <div class="col-md-2 px-1">
                                <label class="col-md-12 label_header" for="fecha_hoy">{{trans('contableM.fecha')}}: </label>
                                <input class="form-control " type="date" name="fecha_hoy" id="fecha_hoy"
                                    value="{{date('Y-m-d')}}">
                            </div>
                            <div class=" col-md-2 px-1">
                                <label class="col-md-12 label_header" for="nro_factura">{{trans('contableM.NoFactura')}} </label>
                                <input class="form-control " type="text" name="nro_factura" id="nro_factura" readonly autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group col-xs-6  col-md-1 px-1">
                            <div class="col-md-12 px-0">
                                <label for="empresa" class="label_header">Electrónica</label>
                            </div>
                            <div class="col-md-12 px-0">
                                <label class="switch">
                                <input  class="electros" @if($empresa->electronica==1)  @else disabled @endif  id="toggleswitch" type="checkbox">
                                <span class="slider round"></span>
                                <input type="hidden" id="electronica" name="electronica" value="0">
                                </label>
                            </div>
                        </div>
                        <div class="col-md-7 px-1">
                            <input type="hidden" name="total_suma" id="total_suma">
                            <label class="label_header" for="concepto">{{trans('contableM.concepto')}}:</label>
                            <input class="form-control  col-md-12" type="text" maxlength="50" name="concepto" id="concepto" autocomplete="off">
                        </div>

                        <div class="col-md-2 px-1">
                            <label class="label_header" for="sucursal">{{trans('contableM.sucursal')}}:</label>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="sucursal" id="sucursal" onchange="obtener_caja()" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($sucursales as $value)    
                                        <option value="{{$value->id}}">{{$value->codigo_sucursal}}</option>
                                    @endforeach    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 px-1">
                            <label class="label_header" for="punto_emision">Punto de Emision:</label>
                            <div class="col-md-12 px-0">
                                <select class="form-control" name="punto_emision" id="punto_emision" required>
                                  <option value="">Seleccione...</option> 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 px-0" style="padding-top: 13px">
                          <span style="font-family: 'Helvetica general';font-size: 16px;color: black;padding-left: 15px;">Archivo del SRI</span>
                          <input style="width:17px;height:17px;padding-top:13px" type="checkbox" id="check_archivo_sri" class="flat-green" name="check_archivo_sri" value="1"
                          @if(old('check_archivo_sri')=="1")
                            checked
                          @endif>
                        </div>
                        <div class="col-md-5 px-0">
                            <label class="label_header"> Factura </label>
                            <select class="form-control select2" style="width: 100%;" onchange="showData()" name="factura" id="id_factura">
                                <option value="">Seleccione...</option>
                                @foreach($ventas as $v)
                                    <option value="{{$v->id}}">{{$v->nro_comprobante}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 px-0">
                            <label class="label_header">{{trans('contableM.total')}}</label>
                            <input type="text" class="form-control" readonly name="total" id="total_final" value="0.00">
                        </div> 
                    </div>
                </div>
              
                <div class="col-md-12" style="padding-left: 30px; padding-top: 10px">
                    <label  for="observaciones">{{trans('contableM.observaciones')}}</label>
                    <textarea class="col-md-12" name="observaciones" id="observaciones" cols="150" rows="3"></textarea>
                </div>
            </div>
        </div>
    </form>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">
    
        var input = document.getElementById('toggleswitch');
        let electronica = document.getElementById('electronica').value;
        input.addEventListener('change', function() {
            if (this.checked) {
               $("#electronica").val(1);
            } else {
               $("#electronica").val(0);
            }
        });
    
    
</script>


<script type="text/javascript">

    $(document).ready(function(){
     
      $('#check_archivo_sri').attr('checked', true);
      $('.select2').select2({
      placeholder: 'Seleccione Factura',
      allowClear: true, 
      ajax: {
        url: '{{route("nota_credito_cliente.getcomprobante")}}',
        data: function(params) {
          var query = {
            search: params.term,
            type: 'public'
          }
          return query;
        },
        processResults: function(data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          console.log(data);
          return {
            results: data
          };
        }
      },
     
    });

    });
    $(document).on("focus","#id_cliente",function(){

        $("#id_cliente").autocomplete({

            source: function( request, response ) {
                $.ajax( {
                type: 'GET',
                url: "{{route('notacredito.buscarclientexid')}}",
                dataType: "json",
                data: {term: request.term},
                success: function( data ) {
                    response(data);
                }
                } );
            },
            change:function(event, ui){
                $("#nombre_cliente").val(ui.item.nombre);
                  //buscar_deudas_cliente();
                  //suma_deudas_cliente();
                //obtener_total_deudas();
            },
            selectFirst: true,
            minLength: 1,
        
        });

    });
    function seleccionar_todo(){
        $('.verificar').each(function(){
            $(this).prop('checked',true);  
        });
        adder()
       
    }
    function deseleccionar_todo(){
        $('.verificar').each(function(){
            $(this).prop('checked',false);  
        });
        adder()
    }
    function guarda_nota_credito(e){
        if($('#form_nota_credito_cl').valid()){
                $(e).hide();
                if($('#total_final').val()>0){
                    $.ajax({
                    type: 'post',
                    url:"{{route('nota_cliente_debito.newstore')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data:$('#form_nota_credito_cl').serialize(),
                    success: function(data){
                        console.log(data);
                        if(data.sri!=""){
                            console.log(data.sri.original.comprobante); 
                            if(data.sri.original.comprobante!=""){
                                swal('Mensaje','Correcto envio Sri. Comprobante #:'+data.sri.original.comprobante,'success');
                                //swal(`{{trans('contableM.correcto')}}!`,"Nota de Crédito generada con exito","success");
                                $('#form_nota_credito_cl input').attr('readonly', 'readonly');
                                $("#boton_guardar_nota").attr("disabled", true);
                            }else{
                                if(data.sri.original.reason!=""){
                                  swal('Mensaje',data.sri.original.reason,'error');
                                }
                            }
                          
                           /*  console.log(data.sri.original.message);  */
                        }else{
                            swal(`{{trans('contableM.correcto')}}!`,"Nota de Crédito generada con exito","success");
                            $('#form_nota_credito_cl input').attr('readonly', 'readonly');
                            $("#boton_guardar_nota").attr("disabled", true);
                        }      
                    },
                    error: function(data){
                        console.log(data);
                    }
                  })
                }else{
                    $(e).show();
                    swal("Mensaje:","Ingrese productos a devolver","error");
                }
                
            
        }

    }
    function nueva_nota_credito(){
        location.href ="{{route('nota_credito_cliente.create2')}}";
    }

</script>

@endsection