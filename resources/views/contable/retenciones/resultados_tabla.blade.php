@if(!is_null($retenciones))
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable " role="grid" aria-describedby="example2_info">
                    <thead>
                    <tr >
                        <th >{{trans('contableM.fecha')}}</th>
                        <th >{{trans('contableM.proveedor')}}</th>
                        <th>{{trans('contableM.Descripcion')}}</th>
                        <th >{{trans('contableM.secuenciafactura')}}</th>
                        <th >{{trans('contableM.tiporfir')}}</th>
                        <th >Tipo RFIVA</th>
                        <th >{{trans('contableM.total')}}</th>
                        <th >{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                             @foreach($retenciones as $value)
                                    <tr>
                                    <td>{{$value->created_at}}</td>
                                    <td>{{$value->id_proveedor}}</td>
                                    <td>{{$value->descripcion}}</td>
                                    <td>{{$value->secuencia}}</td>                        
                                    <td>{{$value->rfir}}</td>
                                    <td>{{$value->rfiva}}</td>
                                    <td>{{$value->total}}</td>
                                    <td><a href="{{route('retenciones_edit',['id'=>$value->id])}}" class="btn btn-danger">Editar</a>
                                        <a class="btn btn-primary" href="{{route('pdf_comprobante_retenciones',['id'=>$value->id])}}">PDF comp</a>
                                    </td>
                                    </tr>
                            @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
</div>
@else
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr >
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.NroAsiento')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                        <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.estado')}}</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                        No data avaliable
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>

</div>
@endif
<script type="text/javascript">
    $(document).ready(function(){

        $('#example2').DataTable({
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
        });

    });
</script>