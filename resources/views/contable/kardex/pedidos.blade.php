@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
    p.s1 {
        margin-left: 10px;
        font-size: 14px;
        font-weight: bold;
    }

    p.s2 {
        margin-left: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    p.s3 {
        margin-left: 30px;
        font-size: 10px;
        font-weight: bold;
    }

    p.s4 {
        margin-left: 40px;
        font-size: 10px;
    }

    p.t1 {
        font-size: 14px;
        font-weight: bold;
    }

    p.t2 {
        font-size: 12px;
        font-weight: bold;
    }

    p.t3 {
        font-size: 10px;
    }

    .table-striped>thead>tr>th>td,
    .table-striped>tbody>tr>th>td,
    .table-striped>tfoot>tr>th>td,
    .table-striped>thead>tr>td,
    .table-striped>tbody>tr>td,
    .table-striped>tfoot>tr>td {
        padding: 0.5px;
        line-height: 1;
    }

    .secundario {
        left: 10px;
    }

    .table {
        margin-bottom: -10px;
    }

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

    .hidden-paginator {

        display: none;

    }

    .removethe {
        display: none;
    }

    .text-left {
        text-align: left;
    }

    .bold {
        font-weight: bold;
    }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
            <li class="breadcrumb-item"><a href="#">Kardex</a></li>
        </ol>
    </nav>

    <div class="box" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('pedidos_inventario.index') }}">
                {{ csrf_field() }}

                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">Productos:</label>
                    <div class="col-md-9">
                        <select class="select2_cuentas form-control" name="id_producto" id="id_producto">
                            <option value="">Seleccione... </option>
                            @foreach($productos as $value)
                            <option @if($request->id_producto==$value->id) selected="selected" @endif value="{{$value->id}}"> {{$value->codigo}} {{$value->nombre}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Bodega')}}</label>
                    <div class="col-md-9">
                        <select class="select2_cuentas form-control" name="id_bodega" id="id_bodega">
                            <option value="">Seleccione... </option>
                            @foreach($bodegas as $value)
                            <option @if($request->id_bodega==$value->id) selected="selected" @endif value="{{$value->id}}"> {{$value->nombre}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">

                    <button style="display:none;" type="button" class="btn btn-primary btn-sm" onclick="printDiv()" id="btn_imprimirs" name="btn_imprimir">
                        <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('contableM.Imprimir')}}&nbsp;</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>

                </div>

                <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

                </div>
            </form>
        </div>


        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                        </div>

                        <div class="box-body">
                            <div class="col-md-1">
                                <dl>
                                    <dd><img @if(isset($empresa->logo)) src="{{asset('/logo').'/'.$empresa->logo}}" @endif alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>

                                </dl>
                            </div>
                            <div id="imprimir">
                                <div class="col-md-3">
                                    <dl>
                                        <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                                        <dd>&nbsp; {{$empresa->id}}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">
                                    <h4 style="text-align: center;">PRODUCTOS EN BODEGA</h4>
                                    @if(($fecha_desde!=null))
                                    <h5 style="text-align: center;">Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                                    @else
                                    <h5 style="text-align: center;">Al {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                                    @endif
                                </div>

                                <table id="example2" class="display compact" style="font-size: 12px; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th colspan="3" style="background-color: skyblue;">Detalle de Producto</th>
                                            <th colspan="3" style="background-color:aquamarine;">Entrada</th>
                                            <th colspan="3" style="background-color: red;">Salida</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align:left;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.tipo')}}</th>
                                            <th style="text-align:left;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.observaciones')}}</th>
                                            <th>{{trans('contableM.fecha')}}</th>
                                            <th>{{trans('contableM.Bodega')}}</th>
                                            <th>Producto</th>
                                            <th>{{trans('contableM.cantidad')}}</th>
                                            <th>Valor Unitario</th>
                                            <th>{{trans('contableM.total')}}</th>
                                            <th>{{trans('contableM.cantidad')}}</th>
                                            <th>Valor Unitario</th>
                                            <th>{{trans('contableM.total')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pedidos as $value)
                                        @php
                                        $total= ($value->precio - $value->descuento) * ($value->cantidad);
                                        $producto= Sis_medico\Ct_productos::where('codigo',$value->codigo)->first();
                                        @endphp
                                        @if($value->check==0)
                                        <tr>
                                            <td> <label class="label label-danger">Ingreso Concesión</label> <label class="label label-success">{{$value->tipo}}</label> </td>
                                            <td>@if(isset($value->cabecera)) {{$value->cabecera->observacion}} @endif</td>
                                            <td>@if(isset($value->cabecera)) {{$value->cabecera->fecha}} @endif</td>
                                            <td>@if(isset($value->bodegap)){{$value->bodegap->nombre}} @endif</td>
                                            <td>@if(!is_null($producto)) {{$producto->nombre}} @endif</td>
                                            <td>{{number_format($value->cantidad,2,'.','')}}</td>
                                            <td>{{number_format(round($value->precio,2),2,'.','')}}</td>
                                            <td>{{number_format(round($total,2),2,'.','')}}</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                           
                                        </tr>
                                        @else
                                        <tr>
                                            <td> <label class="label label-info">Ingreso Mercaderia</label> <label class="label label-success">{{$value->tipo}}</label></td>
                                            <td>Ingresado por : <br> <label class="label label-primary">{{$value->usuariomod->nombre1}} {{$value->usuariomod->apellido1}}</label> </td>
                                            <td>@if(isset($value->cabecera)){{$value->cabecera->fecha}} @endif </td>
                                            <td>@if(isset($value->bodegap)){{$value->bodegap->nombre}} @endif</td>
                                            <td>@if(!is_null($producto)) {{$producto->nombre}} @endif</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>{{number_format($value->cantidad,2,'.','')}}</td>
                                            <td>{{number_format(round($value->precio,2),2,'.','')}}</td>
                                            <td>{{number_format(round($total,2),2,'.','')}}</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>


                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>


    </div>


    </div>
</section>

<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_cuentas').select2({
            tags: false
        });
        $('#fact_contable_check').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });

    });
    $('#example2').DataTable({
        'paging': false,
        "scrollX": true,
        "scrollY": 450,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        responsive: true,
        'info': false,
        'autoWidth': true,

        buttons: [{
                extend: 'copyHtml5',
                footer: true
            },
            {
                extend: 'excelHtml5',
                footer: true,
                title: 'REPORTE DE KARDEX {{$empresa->nombrecomercial}} @if($fecha_desde!=null) {{date("d/m/Y",strtotime($fecha_desde))}} @endif -  {{date("d/m/Y",strtotime($fecha_hasta))}}',
                customize: function(doc) {
                    var sheet = doc.xl.worksheets['sheet1.xml'];
                    //$('row c[r^="D"]', sheet).attr( 's', '64' );
                    //console.log($('row c[r^="C"]',sheet))

                    $('row', sheet).each(function() {
                        //console.log('entra aqui');
                        // Get the value
                        // console.log($('is t', this))
                        var text = $('is t', this).text();
                        if (text.includes('|')) {
                            $('row c', this).attr('s', '47');
                        } else {

                        }
                    });
                }
            },
            {
                extend: 'csvHtml5',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                title: function() {
                    return "ABCDE List";
                },
                pageSize: 'A3',
                footer: true,
                title: 'REPORTE DEUDAS PENDIENTES {{$empresa->nombrecomercial}}',
                customize: function(doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '17',
                        alignment: 'center'
                    }
                }
            }
        ],
    })

    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('div.paginationLinks').remove();

            }
        });
    });
    $('#seguimiento').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $('#cuenta').on('select2:select', function(e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
    });


    $('#nombre').on('select2:select', function(e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
    });

    $("#btn_imprimir").click(function() {
        $("#print_reporte_master").submit();
        // document.getElementById("print_reporte_master").submit();
    });


    $(document).ready(function() {

        $('input[type="checkbox"].flat-green').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });

        $('input[type="checkbox"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });

    });

    function excel() {
        $("#print_reporte_master").submit();
    }
    $("#nombre_proveedor").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('compra_buscar_nombreproveedor')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 2,
    });
    $('#fact_contable_check').on('ifChanged', function(event) {
        //aqui funciona si cambio el input time
        if ($(this).prop("checked")) {
            $("#esfac_contable").val(1);
            $("#es_fact_dos").val(1);
        } else {
            $("#esfac_contable").val(0);
        }

    });

    function cambiar_nombre_proveedor() {
        $.ajax({
            type: 'post',
            url: "{{route('compra_buscar_proveedornombre')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': $("#nombre_proveedor").val()
            },
            success: function(data) {
                if (data.value != "no") {
                    $('#id_proveedor').val(data.value);
                    $('#id_proveedor2').val(data.value);
                    $('#direccion_id_proveedor').val(data.direccion);
                } else {
                    $('#id_proveedor').val("");
                    $('#id_proveedor2').val("");
                    $('#direccion_proveedor').val("");
                }

            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    $(function() {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_desde}}',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',

        });
        $("#fecha_desde").on("dp.change", function(e) {
            verifica_fechas();
        });

        $("#fecha_hasta").on("dp.change", function(e) {
            verifica_fechas();
        });

    });

    function printDiv(nombreDiv) {
        var contenido = document.getElementById("imprimir").innerHTML;
        var contenidoOriginal = document.body.innerHTML;

        document.body.innerHTML = contenido;

        window.print();

        document.body.innerHTML = contenidoOriginal;
    }

    function buscar() {
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }

    function tipo_factura() {
        var tipo = $("#tipo").val();
        if (isNaN(tipo)) {
            tipo = 0;
        }
        $("#tipo2").val(tipo);
    }

    function observacion() {
        var observacion = $("#concepto").val();

        $("#observacion2").val(observacion);
    }

    function verifica_fechas() {
        if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Verifique el rango de fechas y vuelva consultar'
            });
        }
    }
</script>
@endsection