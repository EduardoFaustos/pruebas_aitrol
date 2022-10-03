@extends('contable.plan_cuentas.base')
@section('action-content')
<!-- Ventana modal editar -->
<style type="text/css">
  #arbol a:hover{
    color: #3c8dbc;
    cursor: pointer;
  }
  #arbol a{
    color: #3c8dbc;
    mouse: pointer;
    font-size: 12px;
  }
  #arbol ul{
    list-style-type: none;
  }

  .active {
    display: block !important;
  }
  .treeview-menu {
    display: none;
  }
  #tabla_elementos{
    font-size: 12px;
  }
</style>

<div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 70%;">
    <div class="modal-content">

    </div>
  </div>
</div>
  <!-- Main content -->
  <section class="content">
    <div class="box " >
      <div class="box-header header_new" style="color: black; font-family: 'Helvetica general3';">
          <div class="col-md-12">
            <h5>Empresa actual: <b>{{$empresa->razonsocial}}</b></h5>
          </div>
      </div>

      <!-- /.box-header -->
      <div class="box-body dobra">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <form method="POST" action="{{route('plan_cuentas.guardar_empresa')}}">
                {{ csrf_field() }}
                <div class="form-group col-md-7 {{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                  <label for="id_empresa" class="col-md-4 control-label">Seleccione la empresa a usar</label>
                  <div class="col-md-6">
                      <select class="form-control" name="id_empresa" id="id_empresa" onchange="consultar_inventario();">
                        @foreach($empresas as $value)
                          <option value="{{$value->id}}" @if($empresa->id ==  $value->id) selected="selected" @endif >{{$value->nombrecomercial}}</option>
                        @endforeach
                      </select>
                  </div>
                </div>
                <div class="form-group col-md-3">
                  <label for="id_empresa" class="col-md-4 control-label">Inventario</label>
                  <div class="col-md-6">
                    
                    <select id="inventario" name="inventario" class="form-control input-sm" required style="font-size: 13px">
                    @if(!is_null($empresa))
                        <option @if($empresa->inventario=='0'){{'selected '}}@endif value="0">NO</option>
                        <option @if($empresa->inventario=='1'){{'selected '}}@endif value="1">SI</option>
                    @else
                      <option value="0" selected >NO</option>
                      <option value="1" >SI</option>
                    @endif
                  </select>
                    <!-- <input type="checkbox" name="inventario" id="inventario" @if($empresa->inventario==1) checked @endif value="1"> -->
                  </div>
                </div>
                <div class="col-md-2">
                  <button class="btn btn-primary btn-gray" type="submit">Enviar</button>
                </div>
              </form>
            </div>
            @php $pageDocument = true; @endphp
            @if($pageDocument)
            <div class="row">
              @php
                //dd('entra');
                $fecha = new DateTime();
                $fecha->modify('-2 hours');
                $ct_ventas = \Sis_medico\Ct_ventas::where('id_empresa', $empresa->id)
                  ->where('electronica', 1)->where('estado_electronica', '<>', 3)->where('revision_electronica', 0)->where('created_at', '<', $fecha->format('Y-m-d H:i:s'))->get();
                foreach($ct_ventas as $value){
                  $data['empresa']     = $empresa->id;
                  $data['comprobante'] = $value->nro_comprobante;
                  $data['tipo']        = "comprobante";

                  $envio = \Sis_medico\Http\Controllers\ApiFacturacionController::estado_comprobante($data);
                  //dd($envio);
                  $documento = \Sis_medico\Ct_ventas::find($value->id);
                  $estado = 0;
                  if(isset($envio->details)){

                    if($envio->details->log[0]->proceso == "Notificación" || $envio->details->log[0]->mensaje == "Autorizado"){
                      $estado = 3;
                      $documento->nro_autorizacion =  $envio->details->autorizacion;
                    }elseif($envio->details->log[0]->mensaje == "Documento recibido"){
                      $estado = 2;
                    }elseif($envio->details->log[0]->proceso == "Firma"){
                      $estado = 1;
                    }
                  }

                  $documento->estado_electronica =  $estado;
                  $documento->save();
                }
                $ct_ventas = \Sis_medico\Ct_ventas::where('id_empresa', $empresa->id)
                  ->where('electronica', 1)->where('estado_electronica', '<>', 3)->where('revision_electronica', 0)->where('created_at', '<', $fecha->format('Y-m-d H:i:s'))->get();

              @endphp
              @if(count($ct_ventas) > 0)
              <div class="col-md-12">
                <div class="table-responsive col-md-12">
                  <span><b>Facturas Pendientes</b></span>
                  <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th >{{trans('contableM.codigo')}}</th>
                        <th >{{trans('contableM.NUMEROFACTURA')}}</th>
                        <th >Fecha Factura</th>
                        <th >{{trans('contableM.Clientes')}}</th>
                        <th  style="font-size: 10px;">{{trans('contableM.estado')}}</th>
                        <th>{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($ct_ventas as $value)
                      <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>@if(!is_null($value->numero)){{$value->sucursal}}-{{$value->punto_emision}}-{{$value->numero}}@endif</td>
                        <td>@if(!is_null($value->fecha)){{substr($value->fecha,0,11)}}@endif</td>
                        @php
                        $cliente = \Sis_medico\Ct_Clientes::where('identificacion',$value->id_cliente)->first();
                        $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
                        @endphp
                        <td>@if(!is_null($cliente->nombre)){{$cliente->nombre}}@endif</td>
                        @if($value->electronica == 0)
                          <td>No</td>
                        @else

                          @if($value->estado_electronica == 3)
                            <td>AUTORIZADO</td>
                          @elseif($value->estado_electronica == 2)
                            <td>FIRMADO</td>
                          @elseif($value->estado_electronica == 1)
                            <td>FIRMADO</td>
                          @else
                            <td>ENVIADO</td>
                          @endif
                        @endif
                        <td style="padding-left: 1px;padding-right: 1px;">
                          <a data-toggle="modal" data-target="#log_factura"  href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log Factura</a>
                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Fact</a>
                          <a  onclick="actualizar_venta('{{$value->id}}')" class="btn btn-danger col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Corregido</a>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              @endif
            </div>
            @endif
        </div>
      </div>
      <!-- /.box-body -->
    </div>
  </section>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#log_factura').on('hidden.bs.modal', function(){
          $(this).removeData('bs.modal');
      });
    });

    function consultar_inventario(){ 
      id_empresa = document.getElementById("id_empresa").value;
      $.ajax({
          type: 'post',
          url: "{{ route('plan_cuentas.consultar_inventario') }}",
          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'id_empresa': id_empresa },
          success: function(data) {
              $("#inventario").val(data.inventario_estado);
              $("#inventario").trigger('change');

          },
          error: function(data) {
              console.log(data);
          }
      })
    };


    function actualizar_venta(id){
        Swal.fire({
          title: 'Esta Seguro que esta revisado?',
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: `Si`,
          denyButtonText: `No`,
          customClass: {
            cancelButton: 'order-1 right-gap',
            confirmButton: 'order-2',
            denyButton: 'order-3',
          }
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              type: 'get',
              url: "{{ url('contable/ventas/estado/factura/')}}/" + id,
              success: function(data) {
                Swal.fire('Listo!', '', 'success');
              },
              error: function(data) {
                Swal.fire('Error al Enviar Datos!', '', 'error');
              }
            });


          }
        });
      }
  </script>
  <!-- /.content -->

@endsection
