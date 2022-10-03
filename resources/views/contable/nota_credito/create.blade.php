@extends('contable.nota_credito.base')
@section('action-content')

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
      //window.location.reload(history.back());
    }
    function goNew(){
        $(".btn_add").attr("disabled", false);

        $("#asiento").val("");
        $("#id").val("");
        $("#numero").val("");
        $(".print").css('visibility', 'hidden');
    }

</script>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
    <li class="breadcrumb-item"><a href="#">{{trans('contableM.banco')}}</a></li>
    <li class="breadcrumb-item"><a href="../notacredito">Nota de Cr&eacute;dito</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.crear')}}</li>
  </ol>
</nav>
    <div class="box" >
        <div class="box-header header_new">
            <div class="col-md-8">
              <h3 class="box-title">Crear Nota de Cr&eacute;dito</h3>
            </div>
            <div class="col-md-1 print" id="imprimir">
                <a target="_blank" href="{{ route('notadebito.imprimir', ['id' => 3]) }}" class="btn btn-info btn-gray">
                    <i class="glyphicon glyphicon-print" aria-hidden="true"></i><!--&nbsp;&nbsp; Revisar Nota-->
                </a>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goNew()" class="btn btn-primary btn-gray" >
                   Nuevo
                </button>
            </div>
            <div class="col-md-1 text-right" >
                <button onclick="goBack()" class="btn btn-success btn-gray">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                </button>
            </div>
        </div>


        <div class="box-body dobra">
            <form id="form"  class="form-vertical" role="form">
                {{ csrf_field() }}


                <!--header row-->
                <div class="header row">
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id" class=" label_header">{{trans('contableM.id')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input type="text" class="form-control"  name="id" id="id" value="" >
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                            </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="numero" class=" label_header">{{trans('contableM.numero')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input type="text" class="form-control"  name="numero" id="numero" value="" >
                                @if ($errors->has('numero'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numero') }}</strong>
                                    </span>
                                @endif
                            </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="tipo" class="label_header">{{trans('contableM.tipo')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                                <input type="text" class="form-control" name="tipo" id="tipo" value="BAN-NC" readonly>
                            @if ($errors->has('tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tipo') }}</strong>
                                </span>
                            @endif
                            </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{trans('contableM.asiento')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input type="text" class="form-control"  name="asiento" id="asiento" value="" >
                            @if ($errors->has('asiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('asiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="asiento" class="label_header">{{trans('contableM.proyecto')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input type="text" class="form-control" value="0000"  name="proyecto" id="proyecto" value="" >
                            @if ($errors->has('proyecto'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('proyecto') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="fecha_asiento" class="label_header">{{trans('contableM.fecha')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="fecha" type="text" class="form-control"  name="fecha_asiento" value="{{ old('fecha_asiento') }}"   required >
                            @if ($errors->has('fecha_asiento'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fecha_asiento') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- 2 row -->
                    <div class="form-group col-xs-12 px-1">
                        <div class="col-md-12 px-0">
                            <label for="observacion" class="label_header">{{trans('contableM.concepto')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="observacion" type="text" class="form-control"  name="observacion" value="{{ old('observacion') }}"    required autofocus >
                            @if ($errors->has('observacion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observacion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!-- 3 row -->
                    <div class="form-group col-xs-4 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_banco" class="label_header">{{trans('contableM.banco')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="id_banco" id="id_banco" required>
                                <option value="">Seleccione...</option>
                                @foreach($bancos as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="id_divisa" class="label_header">{{trans('contableM.divisas')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <select class="form-control" name="divisa" id="divisa" required>
                                @foreach($divisas as $value)
                                    <option value="{{$value->id}}">{{$value->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="cambio" class="label_header">{{trans('contableM.cambio')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                            <input id="cambio" type="number" class="form-control"  value="1.00" name="cambio" required autofocus >
                        </div>
                    </div>
                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="valor" class="label_header">{{trans('contableM.valor')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="valor" type="text" class="form-control"  name="valor" readonly autofocus >
                        </div>
                    </div>

                    <div class="form-group col-xs-2 px-1">
                        <div class="col-md-12 px-0">
                            <label for="estado" class="label_header">{{trans('contableM.estado')}}</label>
                        </div>
                        <div class="col-md-12 px-0">
                        <input id="estado" type="text" class="form-control"  name="estado" value="Activa" readonly autofocus >
                        </div>
                    </div>
                </div>

                <div class="col-md-12 table-responsive">
                    <input type="hidden" name="contador" id="contador" value="0">
                    <input type="hidden" name="total" id="total" value="0">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                        <tr class='well-dark'>
                                <th width="5%"  tabindex="0"></th>
                                <th width="55%" class="" tabindex="0">{{trans('contableM.nombre')}}</th>
                                <th width="20%" class="" tabindex="0">{{trans('contableM.Debe')}}</th>
                                <th width="20%" class="" tabindex="0">{{trans('contableM.Haber')}}</th>
                                <th width="5%" class="" tabindex="0">
                                    <button onclick="nuevo()" type="button" class="btn btn-success btn-gray" >
                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="agregar_cuentas">
                        <tr class="well">
                                <td>
                                    <input type="hidden" name="no_no[]" class="no_no" />
                                </td>
                                <td>

                                    <select id="nombre[]" name="nombre[]" class="form-control select2_cuentas" style="width:100%" required  >
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                            @if(isset($value->pempresa))
                                            <option value="{{$value->id}}" data-name="{{$value->nombre}}">{{$value->id}} - {{$value->nombre}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                <input class="form-control input-sm debe" disabled="true" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="debe[]" required>
                                </td>
                                <td>
                                <input class="form-control input-sm haber" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00"  onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" name="haber[]" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" >
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr style="display:none" id="mifila">
                                <td>
                                    <input type="hidden" name="no_no[]" class="no_no" value="qw" />
                                </td>

                                <td>
                                        <select name="nombre[]" class="form-control select2_cuentas class_nombre" style="width:100%">
                                        <option> </option>
                                        @foreach($cuentas as $value)
                                            @if(isset($value->pempresa))
                                            <option value="{{$value->id}}" data-name="{{$value->pempresa->nombre}}"> {{$value->pempresa->plan}} - {{$value->pempresa->nombre}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                <input class="form-control input-sm debe" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" disabled="true" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="debe[]">
                                </td>
                                <td>
                                <input class="form-control input-sm haber" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);addvalue();" name="haber[]">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-gray delete" >
                                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class='well'>
                                <td></td>
                                <td class="text-right">{{trans('contableM.totales')}}</td>
                                <td id="debe_contable">
                                    0.00
                                </td>
                                <td id="haber_contable"></td><td></td>
                        </tfoot>
                    </table>

                </div>

                <div class="form-group col-xs-10 text-center">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="button"  class="btn btn-default btn-gray btn_add">
                        <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.guardar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script type="text/javascript">
var fila = $("#mifila").html();
$(".print").css('visibility', 'hidden');
    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
          });
      });
        $(function () {
            $('#fecha').datetimepicker({
                format: 'YYYY/MM/DD',
                defaultDate: '{{date("Y-m-d")}}',
                });
      });

    function buscar_prod(){
        $.ajax({
            type: 'post',
            url:"{{route('contable_find_insumo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: $("#codigo_prod"),
            success: function(data){
              console.log(data);
               if(data != 'no'){
                    //console.log("siii");
                    $('#codigo').val(data[0].codigo);
                    $('#nombre').val(data[0].nombre);
                    $('#descripcion').val(data[0].descripcion);
                    $('#stock_minimo').val(data[0].minimo);
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    };

    $('body').on('click', '.delete', function () {
        var borrar = $(this).parent().prev().children().closest('.haber').val();
        var total = $("#valor").val();
        var dc = $("#haber_contable").val();

        total = parseFloat(total) - parseFloat(borrar);
        total = parseFloat(total).toFixed(2)
        $("#valor").val(total);

        dc = parseFloat(dc) - parseFloat(borrar);
        dc = parseFloat(dc).toFixed(2)
        $("#hc").val(total);
        $(this).parent().parent().remove();
    });
    $('body').on('change', '.select2_cuentas', function(){
        var eas = $(this).val();
        console.log($(this).data("name"));
        var selectedText = $(".select2_cuentas option:selected").data("name");
        console.log(selectedText);
        var sdf = $(this).find('option:selected').data("name");
        console.log(sdf);
        console.log(eas);
       // $(".select2_cuentas").parent().parent().first().children().children().closest(".no_no").val($(".select2_cuentas option:selected").data("name"));

        $(this).parent().parent().first().children().children().closest(".no_no").val(sdf);

    });

    function nuevo(){
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
        rowk.innerHTML = fila;
        rowk.className="well";
      //  console.log(rowk);

        $('.select2_cuentas').select2({
            tags: false
        });

    }

    function agregar(elemento){
        var anterior =  $('#agregar_cuentas').html();
        var contador = $('#contador').val();
        contador = parseInt(contador) + 1;
        $.ajax({
            type: 'post',
            url:"{{route('notacredito.buscar_asiento')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: {'nombre': $('#'+elemento).val(),
             'contador': contador},
            success: function(data){
                console.log(data);
              $('#contador').val(contador);
              document.getElementById("agregar_cuentas").insertRow(contador-1).innerHTML = data;
            },
            error: function(data){
                console.log(data);
            }
        });
    }

/*
    document.getElementById("enviar_nota").addEventListener("submit", function(event){

        var contador =  $('#contador').val();
        var tdebe= 0;
        var thaber=0;
        if(contador <= 0){
            alert('No se puede agregar el asiento, no tiene ninguna cuenta');
            event.preventDefault();
            return false;
        }
        for (var i = 1; contador >= i; i++) {
            debe = parseFloat($('#debe'+i).val());
            haber = parseFloat($('#haber'+i).val());
            tdebe = tdebe + debe;
            thaber = thaber + haber;
        }
        $('#total').val(thaber-tdebe);
    });*/
    $(".btn_add").click(function(){
        var haberco = parseFloat($("#haber_contable").html());
        var haber = parseFloat($("#valor").val());
        if(haberco==haber){
          if($("#form").valid()){
              $(".print").css('visibility', 'visible');
              $(".btn_add").attr("disabled", true);
                $.ajax({
                    url:"{{route('notacredito.store')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    type: 'POST',
                    datatype: 'json',
                    data: $("#form").serialize(),
                    success: function(data){
                        console.log(data);
                        $("#asiento").val(data.idasiento);
                        $("#id").val(data.iddebito);
                        $("#numero").val(data.iddebito);
                        swal("Listo!", "Nota de Credito creada con exito!", "success");
                        //swal(`{{trans('contableM.correcto')}}!`,"Por favor ingrese correctamente los valores..","error");
                        var url = '{{ route("notacredito.imprimir", ":id") }}';
                    url = url.replace(':id', data.iddebito);
                    console.log('url',url);
                    $('#imprimir').html('<a href="'+url+'" target="_blank" class="btn btn-info btn-gray"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></a>');

                    },
                    error: function(data){
                        console.error(data.responseText);
                    }
                });
          }
        }else{
            alert("Hay un error en las cuentas");
        }
      });
  function isNumberKey(evt)
   {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
   }

   function sumacontable(){
    var total = 0;
       $(".haber").each(function() {
           console.log("sc",$(this).parent().prev().children().closest(".select2_cuentas").val());
           var cuenta = $(this).parent().prev().children().closest(".select2_cuentas").val();
           if($(this).val().length>0 && cuenta!=""){
            total = parseFloat(total) + parseFloat($(this).val());
           }
       });
       total = parseFloat(total).toFixed(2)
       $("#haber_contable").html(total);
   }
   function addvalue(){
        sumacontable();

       var total = 0;
       $(".haber").each(function() {
           if($(this).val().length>0){
            total = parseFloat(total) + parseFloat($(this).val());
           }
       });
       total = parseFloat(total).toFixed(2)
       $("#valor").val(total);
   }

</script>

</section>
@endsection
