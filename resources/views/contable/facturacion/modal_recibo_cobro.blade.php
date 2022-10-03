<style type="text/css">
    
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 10pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    table{
      font-size: 16px !important;
    }

</style>
<div class="modal-content">
      <div style="text-align: left" class="modal-header">
          <div class="box-header">
            <div class="col-md-8">
              <h3 class="box-title">LISTADO RECIBO COBRO</h3>
            </div>
            @php 
              $tipo_usuario  = Auth::user()->id_tipo_usuario;
            @endphp 
            <div class="col-md-1 text-right">
              @if($tipo_usuario == '1')
              <a class="btn btn-primary btn-sm"  id="examenes_externos"  href="{{ route('nuevorecibocobro.crear', ['id' => $fact_venta->id_agenda]) }}" >CREAR NUEVO sis</a>
              @else
              <a class="btn btn-primary btn-sm"  id="examenes_externos"  href="{{ route('factura.agenda', ['id' => $fact_venta->id_agenda]) }}" >CREAR NUEVO</a>
              @endif
            </div>
            <div class="col-md-3">
              <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
            </div>
          </div>  
           
          <div class="form-group col-md-12">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th width="5%">No.</th>
                        <th width="10%">Fecha Creación</th>
                        <th width="75%">{{trans('contableM.detalle')}}</th>
                        <th width="5%">{{trans('contableM.valor')}}</th>
                        <th width="5%">{{trans('contableM.acciones')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($obtener_recibos as $value)
                        <tr>
                          <td @if($value->estado == '-1') style="background-color: yellow" @else style="background-color: green" @endif> 
                            {{$value->id}}<br><b>@if($value->estado == '-1')PENDIENTE DE EMITIR @endif</b>
                          </td>
                          <td>@if(!is_null($value->fecha_emision)){{$value->fecha_emision}}@endif</td>
                          <td>@foreach($value->detalles as $detalle) {{ $detalle->nombre_producto }}<br> @endforeach</td>
                          <td>{{$value->total}}</td>

                          <td>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button  type ="button" onclick="anular_recibo({{$value->id}});" class="btn btn-danger btn-xs" data-dismiss="modal"><span class="glyphicon glyphicon-trash"></span></button>
                            @if($tipo_usuario == 1)
                            <a href="{{ route('nuevorecibocobro.editar', ['orden' =>$value->id]) }}" class="btn btn-warning  btn-xs" ><span class="glyphicon glyphicon-edit"></span>E</a>
                            @else
                            <a href="{{ route('facturaagenda.editn', ['orden' =>$value->id]) }}" class="btn btn-warning  btn-xs" ><span class="glyphicon glyphicon-edit"></span></a>
                            @endif
                            
                            @if($value->estado == 1)
                                <a target="_blank" href="{{ route('facturacion.imprimir_ride', ['id_orden' =>$value->id]) }}" class="btn btn-success  btn-xs" ><span class="glyphicon glyphicon-download-alt"></span></a>
                                @if($value->id_agenda!=null)
                                
                                <!--  <a href="{{ route('factura.editar_cp', ['orden' =>$value->id,'valor' => 0]) }}" class="btn btn-warning  btn-xs" ><span class="glyphicon glyphicon-edit"></span></a> -->
                                @endif
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>


      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
          
      </div>

</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">

  $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : false,
    'info'        : false,
    'autoWidth'   : false
  })

  function anular_recibo(id){


      $.ajax({
            type: 'post',
            url:"{{route('recibo_cobro.anular')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:{'id_orden':id},
            success: function(data){
              //console.log(data);
            },
            error: function(data){
                console.log(data);

            }    
      });

  

  }

</script>


