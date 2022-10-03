@extends('contable.reporte_cierre_caja.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="buscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master" action="{{ route('cierrecaja.reporte')}}">
        {{ csrf_field() }}

        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="col-md-2 control-label">{{trans('contableM.Desde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="col-md-2 control-label">{{trans('contableM.Hasta')}}</label>
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
        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-3 control-label">{{trans('contableM.empresa')}}</label>
          <div class="col-md-9">
              <select class="form-control input-sm" name="id_empresa" id="id_empresa">
                @foreach($empresas as $empresa)
                <option @if($request['id_empresa'] == $empresa->id) selected="selected" @endif value="{{$empresa->id}}">{{$empresa->nombrecomercial}}</option>
                @endforeach
              </select>
          </div>
        </div>
        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">{{trans('contableM.caja')}}</label>
          <div class="col-md-5">
              <select class="form-control input-sm" name="caja" id="caja">
               <option @if($request['caja'] == "Torre 1") selected="selected" @endif  value="Torre 1" >Torre 1</option>
               <option @if($request['caja'] == "Torre 2") selected="selected" @endif value="Torre 2">Torre 2</option>
              </select>
          </div>
        </div>


        <div class="form-group col-md-6 col-xs-6" style="text-align: right;">
          <button type="submit" formaction="{{ route('reporte.index_cierre')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
        </div>

        <div class="form-group col-md-6 col-xs-6" >
          <button type="submit" class="btn btn-primary btn-sm" formtarget="_blank" >
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span></button>
        </div>
      </form>

      <div class="form-group col-md-12">
        <div class="form-row">
            <div id="resultados">
            </div>
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
                  <div class="row">
                    <div class="table-responsive col-md-12">
                      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                          <tr >
                            <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >#</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Jornada</th>
                            <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Apellidos</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombres</th>
                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Seguro')}}</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Hora Pago</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Hora Cita</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Cita</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Recibo </th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Referencia </th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Efectivo </th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tarjeta </th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">7% T/C </th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">2% T/D </th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tran/Dep</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cheque')}}</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">PEND FC SEG</th>
                            <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total SIN T/C</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                            $tefectivo = 0;
                            $ttarjeta = 0;
                            $tp
                          @endphp
                          @foreach ($ordenes as $value)
                            @php
                              $pagos = $value->pagos;
                              $efectivo  = 0;
                              $tcredito = 0;
                              $p7 = 0;
                              $p2 = 0;
                              $tran = 0;
                              $cheque = 0;
                              $total = 0;
                              $referencia = "";
                              foreach($pagos as $pago){
                                if($pago->tipo == '1'){
                                  $efectivo += $pago->valor;
                                  $total += $pago->valor;
                                }

                                if($pago->tipo == 4){
                                  $va = $pago->valor/(1 +$pago->p_fi);
                                  $po = $va * $pago->p_fi;
                                  $tcredito += $va;
                                  $p7 += $po;
                                  $total += $va;
                                }

                                if($pago->tipo == 6){
                                  $va = $pago->valor/(1+$pago->p_fi);
                                  $po = $va * $pago->p_fi;
                                  $tcredito += $va;
                                  $p2 += $po;
                                  $total += $va;
                                }

                                if($pago->tipo == 3 || $pago->tipo == 5){
                                  $tran += $pago->valor;
                                  $total += $pago->valor;
                                }

                                if($pago->tipo == 2 ){
                                  $cheque += $pago->valor;
                                  $total += $pago->valor;
                                }

                              }
                              if($efectivo > 0){
                                $referencia = "CASH";
                              }
                              if($tcredito > 0){
                                if(!is_null($referencia)){
                                  $referencia = "Tarjeta ";
                                }else{
                                  $referencia = "+Tarjeta ";
                                }
                              }
                              if($tran > 0){
                                if(!is_null($referencia)){
                                  $referencia = "TRAN/DEP";
                                }else{
                                  $referencia = "+TRAN/DEP";
                                }
                              }
                              if($cheque > 0){
                                if(!is_null($referencia)){
                                  $referencia = "CH";
                                }else{
                                  $referencia = "+CH";
                                }
                              }

                            @endphp
                            <tr>
                              <td >1</td>
                              <td >@if(substr($value->agenda->fechaini, 12,2) >14) Vespertino @else Matutino @endif</td>
                              <td >{{$value->agenda->paciente->apellido1}} @if($value->agenda->paciente->apellido2 != 'N/A'){{$value->agenda->paciente->apellido2}} @endif</td>
                              <td >{{$value->agenda->paciente->nombre1}} @if($value->agenda->paciente->nombre2 != 'N/A'){{$value->agenda->paciente->nombre2}} @endif</td>
                              <td >{{$value->agenda->paciente->seguro->nombre}} </td>
                              <td >{{substr($value->created_at, 11,5)}} </td>
                              <td >{{substr($value->agenda->fechaini, 11,5)}} </td>
                              <td> @if($value->agenda->tipo_cita == 0) Primera vez @else Consecutivo @endif</td>
                              <td >{{substr($value->agenda->fechaini, 0,10)}} </td>
                              <td >{{$value->id}} </td>
                              <td >{{$referencia}}</td>
                              <td >{{sprintf("%.2f",$efectivo)}}</td>
                              <td >{{sprintf("%.2f",$tcredito)}}</td>
                              <td >{{sprintf("%.2f",$p7)}}</td>
                              <td >{{sprintf("%.2f",$p2)}}</td>
                              <td >{{sprintf("%.2f",$tran)}}</td>
                              <td >{{sprintf("%.2f",$cheque)}}</td>
                              <td >{{sprintf("%.2f",$value->valor_oda)}}</td>
                              <td >{{sprintf("%.2f",$total)}}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="row">
                  </div>
              </div>
            </div>
        </div>
      </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            defaultDate: '{{$fecha}}',

        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            defaultDate: '{{$fecha_hasta}}',
        });

        $("#fecha").on("dp.change", function (e) {
            $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
        });

         $("#fecha_hasta").on("dp.change", function (e) {
        });
  });
</script>
@endsection
