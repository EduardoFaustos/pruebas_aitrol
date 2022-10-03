{{-- <b>Detalles de valores a dépositar:</b> --}}
<div id="contenedor">

    <div class="form-group col-xs-12 px-1">
        <div class="col-md-12 px-0">
            <label for="id_cuenta_origen" class="label_header">Detalle de valores recibidos a depositar</label>
        </div>
        <div class="col-md-12 px-0">
            {{-- <select class="form-control" name="id_cuenta_origen" id="id_cuenta_origen" required>
                <option value="">Seleccione...</option>
                @foreach($bancos as $value)
                    <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                @endforeach
            </select> --}}
        </div>
    </div>
    <div class="form-group col-md-12 ">
        <div class="col-md-2 ">
            <label for="id_forma_pago" class="label_header">{{trans('contableM.FechaDesde')}}</label>
            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-4 ">
            <label for="id_forma_pago" class="label_header">Fecha hasta</label>
            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value="{{date('Y-m-d')}}">
        </div>
        <div class="col-md-4">
            <label for="id_forma_pago" class="label_header">Filtrar por:</label>
            <select class="js-example-basic-multiple" style="width:100%;" name="id_forma_pago[]" multiple="multiple" id="id_forma_pago"  @if(isset($detalles)) disabled @endif required>
                <option value="">Seleccione...</option>
                <option value="-1">GENERAL</option>
                @foreach($formas_pago as $value)
                    
                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                    
                @endforeach
                
            </select>

        </div>

 <div class="col-xs-1">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
    </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
        <div class="table-responsive col-md-12">
            <table id="tbl_ventas" class="table table-sm table-bordered table-condensed dataTable table-striped dataTables_wrapper" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class="well-dark">
                <th width="3%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">&nbsp;</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ingreso</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.tipo')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.fecha')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.factura')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cheque')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.banco')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Cuenta')}}</th>
                <th width="15%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cliente')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Div.</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Importe</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                <th width="5%"  tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.ValorBase')}}</th>
                </tr>
            </thead>
            <tbody id="tbl_detalles" name="tbl_detalles">

                @if(isset($detalles))
                    @foreach($detalles as $value)
                        <tr>
                            <td></td>
                            <td>{{ $value->id_ingreso }}</td>
                            <td>{{ $value->tipo_pago->nombre }}</td>
                            <td>{{ date('d/m/Y', strtotime($value->fecha)) }}</td>
                            {{-- @if(isset())
                            @endif --}}
                            <td>{{ $value->cheque }}--{{$value->facturas}}</td>

                            @if(isset($value->banc->nombre))
                            <td>{{ $value->banc->nombre }} </td>
                            @else
                            <td></td>
                            @endif
                            <td>{{ $value->cuenta }}</td>
                            <td>{{ $value->girador }}</td>
                            <td>$</td>
                            <td>{{ number_format($value->importe,2,'.','') }}</td>
                            <td contenteditable="true">{{ number_format($value->valor,2,'.','') }}</td>
                            <td>{{ number_format($value->valor_base,2,'.','') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
            </tfoot>
            </table>
        </div>
        </div>

    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
</script>