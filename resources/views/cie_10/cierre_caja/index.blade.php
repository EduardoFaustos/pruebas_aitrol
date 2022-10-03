@extends('contable.cierre_caja.base')
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

  .right_text {
    text-align: right;
  }

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {}

  .salida {
    background-color: #FF886E;
    color: white;
    font-weight: bold;
  }

  .entrada {
    background-color: #91FF56;
    color: white;
    font-weight: bold;
  }

  .boldi {
    font-weight: bold;
  }

  .fin {
    background-color: #C1341B;
    color: white;
  }

  .tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
  }

  .tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
  }

  .tab button:hover {
    background-color: #ddd;
  }

  .tab button.active {
    background-color: #ccc;
  }

  .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  .tabcontent2 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  .tabcontent3 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  .tabcontent4 {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
  }

  td.details-control {
    background: url('{{asset("mas.png")}}') no-repeat center center;
    width: 100px;
  }

  tr.shown td.details-control {
    background: url('{{asset("menos.png")}}') no-repeat center center;
    width: 100px;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Main content -->
<section class="content">

  <div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="datos_factura">

      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal" id="modalIngreso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ingreso de dinero a Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formingreso" action="{{route('c_caja.store')}}" method="POST">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-6">
                <label>Fecha</label>
                <input type="hidden" name="inicial" value="0">
                <input class="form-control" type="text" name="fechaTime" id="fechaTime">
              </div>
              <div class="form-group col-md-6">
                <label>Valor</label>
                <input class="form-control" type="number" name="valorTime" id="valorTime">
              </div>
              <div class="form-group col-md-12">
                <label>Observacion</label>
                <textarea class="form-control col-md-12" name="observacionTime" id="observacionTime" cols="3" rows="3"></textarea>
              </div>

          </form>

        </div>
      </div>
      <div class="modal-footer">

        <button type="button" onclick="sumiter()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>

  </div>
  <div class="modal fade bd-example-modal" id="modalSalida" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Salida de dinero a Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="pors" method="POST" action="{{route('c_caja.store_salida')}}">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-6">
                <label>Fecha</label>
                <input class="form-control" type="text" name="fechaTime2" id="fechaTime2">
                <input type="hidden" name="val" value="1">
              </div>
              <div class="form-group col-md-6">
                <label>Valor</label>
                <input class="form-control" type="number" name="valorTime2" id="valorTime2">
              </div>
              <div class="form-group col-md-12">
                <label>Observacion</label>
                <textarea class="form-control col-md-12" name="observacionTime2" id="observacionTime2" cols="3" rows="3"></textarea>
              </div>
            </div>
          </form>

        </div>
        <div class="modal-footer">

          <button type="button" onclick="porsumit()" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>

  </div>
  <div class="modal fade bs-example-modal-lg" id="modal_forma" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="div_forma">

      </div>
    </div>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Cierre de Caja</a></li>
    </ol>
  </nav>

  <div class="box" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('c_caja.index') }}">
        {{ csrf_field() }}

        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->
        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha_desde" autocomplete="off" value="{{date('Y/m/d',strtotime($fecha))}}">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group col-md-2 col-xs-2" style="text-align: right;">
          <button class="btn btn-info btn-gray" onclick="return $('#reporte_master').submit()"> <i class="fa fa-search"></i> </button>
        </div>
        <div class="col-md-2" style="display: none;">
          <button formaction="{{ route('orden.cierre_caja')}}" id="btn_cierre" type="submit" class="btn btn-success btn-gray"> Reporte Cierre </button>
        </div>
      </form>
    </div>
    <!-- /.box-body -->

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>

            <div class="box-body">
              <div class="col-md-1">
                <dl>
                  <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>

                </dl>
              </div>
              <div class="col-md-2">
                <dl>
                  <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                  <dd>&nbsp; {{$empresa->id}}</dd>
                </dl>
              </div>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-7">
                    <h4 style="text-align: center;">Cierre de Caja</h4>
                  </div>
                  <!--  -->
                  <div class="col-md-3">
                    @if(count($ingresoCaja)>0)
                    @if(count($cierreFinal)>0)
                    @else
                    <button type="button" class="btn btn-success btn-gray" style="display: none;" data-toggle="modal" data-target="#modalIngresoF"> Ingreso de Caja </button>
                    @endif
                    @else
                    <button type="button" class="btn btn-success btn-gray" data-toggle="modal" data-target="#modalIngreso"> Ingreso Inicial de Caja </button>
                    @endif
                  </div>
                  @if(count($cierreFinal)>0)
                  <div class="col-md-2">
                    <span class="badge badge-pill badge-danger">CAJA CERRADA</span>
                  </div>
                  @else
                  @if(count($cierreFinal)>0)
                  @else
                  <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-gray" data-toggle="modal" style="display: none;" data-target="#modalSalida"> Salida de Caja </button>
                  </div>
                  @endif
                  @endif


                </div>


              </div>
              <div class="row">
                <div class="table-responsive col-md-12">
                  <div class="content ">
                    <table id="examples2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; width:100%;">
                      <thead>
                        <tr>
                          <th aria-controls="example2">Fecha</th>
                          <th>Seguro</th>
                          <th aria-controls="example2">Paciente</th>
                          <th>Efectivo</th>
                          <th>Cheque</th>
                          <th>Deposito</th>
                          <th>Transferencia</th>
                          <th>Credito</th>
                          <th>Debito</th>
                          <th>P. Pago</th>
                          <th>Oda</th>
                          <th>Usuario</th>
                          <th>Opciones</th>

                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                      <tfoot>
                        <tr>

                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>

                        </tr>
                      </tfoot>
                    </table>

                  </div>

                </div>


              </div>
              @if(count($cierreFinal)>0)
              @else
              <div class="col-md-12" style="text-align: center;">
                <button class="btn btn-info btn-gray" data-toggle="modal" data-target="#modalCierre"> Cierre de Caja </button>
              </div>
              @endif
            </div>

            <!-- /.box-body -->
          </div>
          <!-- /.box Anthony del futuro arreglar 21 de mayo hice edte cambio para que restara bien pero no resta bien porque las facturas están dañadas desde un comienzo -->
        </div>
      </div>



    </div>
    <div class="modal fade bd-example-modal" id="modalCierre" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Cierre de Caja {{date('Y/m/d H:i:s')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="pore" method="POST" action="{{route('c_caja.store_salida')}}">
              {{ csrf_field() }}
              <div class="row">
                <div class="form-group col-md-6">
                  <label>Fecha</label>
                  <input class="form-control" type="text" name="fechacierre" id="fechacierre">
                  <input type="hidden" name="val" value="0">
                </div>
                <div class="form-group col-md-6">
                  <label>Valor Final</label>
                  <input class="form-control" type="number" name="valorcierre" id="valorcierre" value="0.00" readonly>
                </div>
                <div class="form-group col-md-12">
                  <label>Observacion</label>
                  <textarea class="form-control col-md-12" name="observacioncierre" id="observacioncierre" cols="3" rows="3"></textarea>
                </div>
              </div>

            </form>

          </div>
          <div class="modal-footer">

            <button type="button" onclick="porsumit2()" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>

    </div>

  </div>
  <div class="modal fade bd-example-modal" id="modalIngresoF" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ingreso de dinero a Caja</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formingresof" action="{{route('c_caja.store')}}" method="POST">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group col-md-6">
                <label>Fecha</label>
                <input type="hidden" name="inicial" value="1">
                <input class="form-control" type="text" name="fechaTime" id="fechaTimef">
              </div>
              <div class="form-group col-md-6">
                <label>Valor</label>
                <input class="form-control" type="number" name="valorTime" id="valorTimef">
              </div>
              <div class="form-group col-md-12">
                <label>Observacion</label>
                <textarea class="form-control col-md-12" name="observacionTime" id="observacionTimef" cols="3" rows="3"></textarea>
              </div>

          </form>

        </div>
      </div>
      <div class="modal-footer">

        <button type="button" onclick="sumiterf()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
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
    $('body').on('hidden.bs.modal', '.modal', function() {
      $(this).removeData('bs.modal');
    });

    var table = $('#examples2').DataTable({
      dom: 'Bfrtip',
      buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
        }
      ],
      processing: true,
      serverSide: true,
      responsive: true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      "ajax": "{{route('cierre_caja.getData',['fecha'=>$fecha])}}",

      "columns": [{
          "data": "fecha"
        },
        {
          "data": "seguro",
          "targets": 0,
          "orderable": false
        },
        {
          "data": "paciente",
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataEfectivo",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataCheque",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataDeposito",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataTransferencia",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataTarjetaCredito",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataTarjetaDebito",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "dataPendientePago",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "oda",
          render: $.fn.dataTable.render.number(',', '.', 2, ''),
          "targets": 0,
          "orderable": false
        },
        {
          "data": "usuario",
          "targets": 0,
          "orderable": false
        },
        {
          "data": function(data) {
            buttonnew = "#";
            var check = "";
            var newfor = null;
            var newbu = '<a href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
            if (typeof(data.orden) == 'object') {
              var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + data.orden.id + "/" + "1";
              if (typeof(data.orden.id) == 'undefined') {
                buttonnew = "#";
                var newbu = '<a href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
              } else {
                bs = "#";
                console.log(data.orden);
                var newbu2 = '<a href="' + bs + '""> SRI </a>';
                var newbu = '<a href="' + buttonnew + '"" target="_blank"> <i class="fa fa-edit"> </i>';
                var newfor = data.orden.id;
                if (data.orden.fecha_envio != null && data.orden.comprobante != null) {
                  newbu2 = '<a onclick="emitir_sri(" ' + newfor + '")"> SRI </a>';
                } else {
                  check = "<input type='checkbox' name='orden' value='" + orden.id + "' >";
                }

              }
            }

            return ' ' + check + '  <div class="dropdown"><button class="btn btn-info btn-xs dropdown-toggle" type="button" id="about-us" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Listado<span class="caret"></span></button><ul class="dropdown-menu" aria-labelledby="about-us"><li>' + newbu + '</i> </a></li><li>' + newbu2 + '</li></ul></div>';
          },
          "targets": 0,
          "orderable": false
        }

      ],
      "order": [
        [0, 'asc']
      ],
      "footerCallback": function(row, data, start, end, display) {
        var api = this.api();
        //console.log(data);

        // Remove the formatting to get integer data for summation
        var intVal = function(i) {
          return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
        };

        // Total over all pages

        total3 = api
          .column(3)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        total4 = api
          .column(4)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        total5 = api
          .column(5)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        total6 = api
          .column(6)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        total7 = api
          .column(7)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        total8 = api
          .column(8)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        total9 = api
          .column(9)
          .data()
          .reduce(function(a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);
        // Total over this page

        // Update footer
        $(api.column(2).footer()).html(
          '<label>Totales</label>'
        );
        $(api.column(3).footer()).html(
          '$' + total3.toFixed(2, 2)
        );
        $(api.column(4).footer()).html(
          '$' + total4.toFixed(2, 2)
        );
        $(api.column(5).footer()).html(
          '$' + total5.toFixed(2, 2)
        );
        $(api.column(6).footer()).html(
          '$' + total6.toFixed(2, 2)
        );
        $(api.column(7).footer()).html(
          '$' + total7.toFixed(2, 2)
        );
        $(api.column(8).footer()).html(
          '$' + total8.toFixed(2, 2)
        );
        $(api.column(9).footer()).html(
          '$' + total9.toFixed(2, 2)
        );
        var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9;
        $("#valorcierre").val(finale);
      }
    });
    $('#examples2 tbody').on('click', 'td.details-control', function() {
      var tr = $(this).closest('tr');
      var row = table.row(tr);
      if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
      } else {
        // Open this row
        row.child(format(row.data())).show();
        tr.addClass('shown');
      }
    });
  });
  $('#cierreCaja').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');

  });


  function format(d) {
    // `d` is the original data object for the row
    console.log(d);
    var arrayPusher = d.forma_pago;
    console.log(arrayPusher)
    var datahtml = "<td> No hay datos que mostrar </td>"
    if ((arrayPusher.length) > 0) {
      datahtml = "";
      for (var car of arrayPusher) {
        //console.log(car);
        var forma = "No tiene";
        if (car.id_tipo_pago == '1') {
          forma = "Efectivo";
        } else if (car.id_tipo_pago == '2') {
          forma = "Cheque";
        } else if (car.id_tipo_pago == '3') {
          forma = "Deposito";
        } else if (car.id_tipo_pago == '4') {
          forma = "Tarjeta Credito";
        } else if (car.id_tipo_pago == '5') {
          forma = "Transferencia Bancaria";
        } else if (car.id_tipo_pago == '6') {
          forma = "Tarjeta de Debito";
        } else if (car.id_tipo_pago == '7') {
          forma = "Pendiente Pago";
        }
        datahtml += "<tr> <td>" + car.fecha + "</td> <td>" + forma + "</td> <td>" + car.valor + "</td> </tr>";
      }
    } else {
      return "<span> No existen detalles que mostrar.</span>"
    }
    var eq = d.orden;
    var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + d.orden.id + "/" + "1";
    var newfor = d.orden.id;

    console.log(buttonnew);
    console.log(newfor);
    //fecha_envio validar y comprobante

    //<button type="button" class="btn btn-info btn-xs" onclick="forma_url('+newfor+')"> <i class="fa fa-edit"> </i> </button>
    return ' <label> Examen orden # ' + d.orden.id + ' </label>  &nbsp; &nbsp; <a  target="_blank" class="btn btn-danger btn-xs" href="' + buttonnew + '"> <i class="fa fa-edit"> </i> </a> &nbsp;  <button type="button" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')"> <i class="fa fa-file"> </i> </button> <table class="table table-striped dataTable" border="0" style="padding-left:50px;"> <thead> <tr> <th>Fecha</th> <th>Detalle</th> <th>Valor</th> </tr> </thead> <tboby>' + datahtml + ' </tbody> </table>';
  }

  function emitir_sri(id_orden) {
    $.ajax({
      type: 'get',
      url: "{{url('facturacion_labs/info_factura')}}/" + id_orden,
      datatype: 'json',
      success: function(data) {
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }
  $(function() {

    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',


    });
    $('#fechaTime').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $('#fechacierre').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $('#fechaTime2').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $('#fechaTimef').datetimepicker({
      format: 'YYYY/MM/DD H:ss',
      defaultDate: '{{date("Y/m/d H:s")}}',


    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

  });

  function sendend(url) {
    console.log("entra aqui")
    window.open("www.google.com", '_blank');
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function excel() {
    $("#print_reporte_master").submit();
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

  function forma_url(urlf) {
    //console.log(urlf);
    if (urlf != null) {
      var urls = "{{url('contable/facturacion/modal/cierre_caja')}}/" + urlf;
      $.ajax({
        type: 'get',
        url: urls,
        datatype: 'json',
        success: function(data) {
          $('#div_forma').empty().html(data);
          $('#modal_forma').modal();
        },
        error: function(data) {
          //console.log(data);
        }
      });
    }

  }

  function sumiter() {
    $("#formingreso").submit();
  }

  function sumiterf() {
    $("#formingresof").submit();
  }

  function porsumit() {
    $("#pors").submit();
  }

  function porsumit2() {
    $("#pore").submit();
  }

  function cierreCaja() {
    let fecha = document.getElementById('fecha_desde').value;
    $.ajax({
      type: 'post',
      url: "{{route('orden.cierre_caja')}}",
      data: {
        'fecha': fecha
      },
      success: function(data) {},
      error: function(data) {
        //console.log(data);
      }
    });
  }
</script>
@endsection