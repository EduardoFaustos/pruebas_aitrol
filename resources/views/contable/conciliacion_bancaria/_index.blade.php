@extends('contable.debito_bancario.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('Kconciliacion.banco')}}</a></li>
      <li class="breadcrumb-item active">{{trans('Kconciliacion.ConciliacionBancaria')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">{{trans('Kconciliacion.ConciliarCuentaBancaria')}}</h3>
      </div>
      <div class="col-md-1 text-right">
        {{-- <button type="button" onclick="location.href='{{route('transferenciabancaria.create')}}'" class="btn btn-success btn-gray">
        <i aria-hidden="true"></i>Conciliaci&oacute;n Bancaria
        </button> --}}
      </div>
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('Kconciliacion.Buscador')}}  {{trans('Kconciliacion.ConciliacionBancaria')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('conciliacionbancaria.index') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">{{trans('Kconciliacion.caja')}}/Banco: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control" name="banco" id="banco" autofocus>
            <option value="">{{trans('Kconciliacion.todos')}}</option>
            @foreach($bancos as $value)
            <option @if($value->id == $banco) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">{{trans('Kconciliacion.tipo')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control" name="tipo" id="tipo" autofocus>
            <option value="">{{trans('Kconciliacion.todos')}}</option>
            <option @if($tipo=='BAN-NC' ) selected @endif value="BAN-NC">{{trans('Kconciliacion.NotasCredito')}}</option>
            <option @if($tipo=='BAN-ND' ) selected @endif value="BAN-ND">{{trans('Kconciliacion.NotasDebito')}}</option>
            <option @if($tipo=='BAN-ND-AC' ) selected @endif value="BAN-ND-AC">{{trans('Kconciliacion.DebitoBancario')}}</option>
            <option @if($tipo=='BAN-TR' ) selected @endif value="BAN-TR">{{trans('Kconciliacion.TransferenciaBancaria')}}</option>
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">{{trans('Kconciliacion.estado')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <select class="form-control" name="estado" id="estado" autofocus>
            <option value="">{{trans('Kconciliacion.todos')}}</option>
            <option value="1">{{trans('Kconciliacion.conciliados')}}</option>
            <option value="0">{{trans('Kconciliacion.NoConciliados')}}</option>
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('Kconciliacion.desde')}} </label>
        </div>

        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <input type="date" name="fecha_desde" class="form-control" id="fecha_desde" value="@if(isset($fecha_desde)){{date('Y-m-d', strtotime($fecha_desde))}}@endif">
            </div>
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('Kconciliacion.hasta')}} </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <input type="date" name="fecha_hasta" class="form-control" id="fecha_hasta" value="@if(isset($fecha_hasta)){{date('Y-m-d', strtotime($fecha_hasta))}}@endif">
            </div>
          </div>
        </div>
        <div class="col-xs-1">
          <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>{{trans('Kconciliacion.Buscar')}}
          </button>
        </div>
      </form>
      <form method="GET" id="4" action="{{ route('conciliacionbancaria.exportar_excel') }}">
        {{ csrf_field() }}
        <input type="hidden" name="fecha_desde2" id="fecha_desde2" value="@if(isset($fecha_desde)){{$fecha_desde}}@endif">
        <input type="hidden" name="fecha_hasta2" id="fecha_hasta2" value="@if(isset($fecha_hasta)){{$fecha_hasta}}@endif">
        <input type="hidden" name="estado2" id="estado2" value="@if(isset($estado)){{$estado}}@endif">
        <input type="hidden" name="tipo2" id="tipo2" value="@if(isset($tipo)){{$tipo}}@endif">
        <input type="hidden" name="banco2" id="banco2">
        <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
          <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>{{trans('Kconciliacion.Exportar')}} Excel
        </button>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">CONCILIACI&Oacute;NES BANCARIAS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">&nbsp;</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.Fecha')}}</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.FechaBanco')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.tipo')}}</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.Numero')}}</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.Cheque')}}</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.Beneficiario')}}</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.Detalle')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('Kconciliacion.Valor')}}</th>
                      </tr>
                    </thead>
                    <tbody id="tbl_detalles" name="tbl_detalles">
                      @php $tipo_debito = array('BAN-ND'); @endphp
                      @foreach ($registros as $value)
                      <tr class="well">
                        <td><input type="checkbox" id="id_consiliacion_{{ $value['id_consiliacion'] }}" class="form-check-input" name="id_consiliacion_{{ $value['id_consiliacion'] }}" onchange="actualizar(this);calcular_todo()" value="{{ $value['id_consiliacion'] }}" @if(@$value['conciliado']=="1" ) checked @endif></td>
                        <td>{{ date('d/m/Y',strtotime($value['fecha'])) }}</td>
                        <td>{{ date('d/m/Y',strtotime($value['fecha_banco'])) }}</td>
                        <td>{{ $value['tipo'] }}
                          <input type="hidden" name="tip" class="form-control" id="tip" value="@if(isset($value['tipo'])){{$value['tipo']}}@endif">
                        </td>
                        <td>{{ $value['numero'] }}</td>
                        <td>{{ $value['numcheque'] }}</td>
                        <td>{{ $value['beneficiario'] }}</td>
                        <td>{{ $value['detalle'] }}</td>
                        <td style="text-align: right; @if(in_array($value['tipo'], $tipo_debito)) text-color:red; @endif">{{ $value['valor'] }}
                          <input type="hidden" name="val" class="form-control" id="val" value="@if(isset($value['valor'])){{$value['valor']}}@endif">
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-2">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('Kconciliacion.TotalRegistros')}} {{count($registros)}} </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2">
                  <button type="button" id="buscarAsiento" onclick="seleccionar_todo(true)" class="btn btn-default">
                    <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp; {{trans('contableM.marcartodos')}}
                  </button>
                </div>
                <div class="col-xs-2">
                  <button type="button" id="buscarAsiento" onclick="seleccionar_todo(false)" class="btn btn-default">
                    <span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp; {{trans('contableM.desmarcartodos')}}
                  </button>
                </div>
                {{-- <div class="col-sm-2">
                      <input class="form-control" type="text" id="buscar_asiento" name="buscar_asiento" readonly/>
                    </div> --}}

                <div class="form-group col-xs-2 px-0">
                  <div class="col-md-12 px-0">
                    <label for="saldo_anterior" style="text-align: right" class="label_header">Saldo Anteriors &nbsp;</label>
                  </div>
                  <div class="col-md-12 px-0">
                    <input id="saldo_anterior" style="text-align: right" type="text" size="17" class="form-control text_der number" value="{{number_format($anterior,2,'.','')}}" name="saldo_anterior" required autofocus>
                  </div>
                </div>
                <div class="form-group col-xs-2 px-0">
                  <div class="col-md-12 px-0">
                    <label for="total_debito" style="text-align: right" class="label_header">Total Debitos &nbsp;</label>
                  </div>
                  <div class="col-md-12 px-0">
                    <input id="total_debito" style="text-align: right" type="text" size="17" class="form-control text_der number" value="0.00" name="total_debito" required autofocus>
                  </div>
                </div>
                <div class="form-group col-xs-2 px-0">
                  <div class="col-md-12 px-0">
                    <label for="total_credito" style="text-align: right" class="label_header">Total Creditos &nbsp;</label>
                  </div>
                  <div class="col-md-12 px-0">
                    <input id="total_credito" style="text-align: right" type="text" size="17" class="form-control text_der number" value="0.00" name="total_credito" required autofocus>
                  </div>
                </div>
                <div class="form-group col-xs-2 px-0">
                  <div class="col-md-12 px-0">
                    <label for="saldo_final" style="text-align: right" class="label_header">Saldo Final &nbsp;</label>
                  </div>
                  <div class="col-md-12 px-0">
                    <input id="saldo_final" style="text-align: right" type="text" size="17" class="form-control text_der number" value="0.00" name="saldo_final" required autofocus>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#estado').val({{$estado}});
    $('#tipo').val('{{ $tipo }}');

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'scrollY': '50vh',
      'scrollCollapse': true,
    });
    calcular_todo();
  });

  function actualizar(obj) {
    $.post("{{route('conciliacionbancaria.actualizar')}}", {
      id: obj.value,
      _token: "{{ csrf_token() }}"
    });
    calcular_todo();
  }

  function actualizarmasivo(obj, accion = "") {
    $.post("{{route('conciliacionbancaria.actualizarmasivo')}}", {
      id: obj.value,
      accion: accion,
      _token: "{{ csrf_token() }}"
    });
  }

  function seleccionar_todo(checked) {
    var miTabla = document.getElementById('tbl_detalles');
    var acumdebito = 0;
    var acumcerdito = 0;
    for (i = 0; i < miTabla.rows.length; i++) {
      miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
      actualizarmasivo(miTabla.rows[i].getElementsByTagName("input")[0], checked);
      if (miTabla.rows[i].getElementsByTagName("input")[0].checked == true) {
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumdebito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-NC") {
          acumcerdito = parseFloat(acumcerdito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumcerdito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));

        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND-AC") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumdebito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-TR") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumdebito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));
        }
        // alert(miTabla.rows[i].getElementsByTagName("input")[1].value);
      }
    }
    $('#total_debito').val(redondeafinal(acumdebito, 4));
    $('#total_credito').val(redondeafinal(acumcerdito, 4));
    $('#saldo_final').val(redondeafinal(parseFloat($('#total_credito').val()) - parseFloat($('#total_debito').val()), 4));

  }

  function calcular_todo() {
    var miTabla = document.getElementById('tbl_detalles');
    var acumdebito = 0;
    var acumcerdito = 0;
    for (i = 0; i < miTabla.rows.length; i++) {
      if (miTabla.rows[i].getElementsByTagName("input")[0].checked == true) {
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumdebito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-NC") {
          acumcerdito = parseFloat(acumcerdito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumcerdito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));

        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-ND-AC") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumdebito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));
        }
        if (miTabla.rows[i].getElementsByTagName("input")[1].value == "BAN-TR") {
          acumdebito = parseFloat(acumdebito) + parseFloat(miTabla.rows[i].getElementsByTagName("input")[2].value);
          // console.log(acumdebito + " " + (miTabla.rows[i].getElementsByTagName("input")[1].value));
        }
        // alert(miTabla.rows[i].getElementsByTagName("input")[1].value);
      }
    }
    $('#total_debito').val(redondeafinal(acumdebito, 4));
    $('#total_credito').val(redondeafinal(acumcerdito, 4));
    $('#saldo_final').val(redondeafinal(parseFloat($('#total_credito').val()) - parseFloat($('#total_debito').val()), 4));
  }

  function devuelvefloat(cant, decimales) {
    var tmp = null;
    $.ajax({
      url: "{{route('transferenciabancaria.devuelvefloat')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      type: 'POST',
      datatype: 'json',
      async: false,
      data: {
        cantidad: cant,
        decimales: decimales
      },
      success: function(data) {
        tmp = data.valor;
      },
      error: function(data) {
        console.error(data.responseText);
      }
    });
    return tmp;
  }
  //final function 
  function redondeafinal(value, decimales = 3) {
    value = +value;
    if (isNaN(value)) return NaN; // Shift 
    value = value.toString().split('e'); 
    value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
    value = value.toString().split('e'); 
    return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
  }
</script>
@endsection