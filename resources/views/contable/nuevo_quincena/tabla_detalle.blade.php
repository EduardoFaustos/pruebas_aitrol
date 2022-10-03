Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()" >
<div class="table-responsive col-md-12">
<table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
    <thead>
        <tr>
            <th width="5%">Sel.</th>
            <th width="5%">{{trans('contableM.id')}}</th>
            <th width="10%">{{trans('contableM.identificacion')}}</th>
            <th width="20%">Nombres</th>
            <th width="10%">Fecha <br> Ingreso</th>
            <th width="5%">Area</th>
            <th width="15%">Cargo</th>
            <th width="15%">Sueldo <br>Mensual</th>
            <th width="10%">Anticipo <br> 1RA quinc</th>
            <th width="5%">{{trans('contableM.asiento')}}</th>
        </tr>
    </thead>
    <tbody>
        @php $sum_anticipos = 0; @endphp
        @foreach($valor_anticipos as $valor)
        @php
        $sum_anticipos += $valor->valor_anticipo;
        @endphp
        <tr>
            <td>@if($valor->id_asiento_cabecera == null)<input type="checkbox" name="roles[]" value="{{$valor->id}}">@endif</td>    
            <td>{{$valor->id}}</td>
            <td>{{$valor->id_user}}</td>
            <td>{{$valor->apellido1}} {{$valor->apellido2}} {{$valor->nombre1}} </td>
            <td>{{$valor->fecha_ingreso}}</td>
            <td>{{$valor->area}}</td>
            <td>{{$valor->cargo}}</td>
            <td>{{$valor->sueldo_neto}}</td>
            <td>
                <input id="val_anticipo" type="text" class="form-control" name="val_anticipo{{$valor->id}}" onchange="actualiza_anticipo('{{$valor->id}}');" @if(!is_null($valor->valor_anticipo)) value="{{$valor->valor_anticipo}}" @else value="0.00" @endif autocomplete="off" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" @if($valor->id_asiento_cabecera != null) readonly @endif>
            </td>
            <td>@if($valor->id_asiento_cabecera != null) <a class="btn btn-primary btn-sm" href="{{route('librodiario.edit',['id'=>$valor->id_asiento_cabecera])}}" target="_blank">{{trans('contableM.asiento')}}@if($usuario=='0950839209'){{id_asiento_cabecera}}</a> @endif</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><b> Total Registros:</b></td>
            <td style="text-align:center;">{{count($valor_anticipos)}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>{{trans('contableM.total')}}</b></td>
            <td>{{number_format($sum_anticipos,2, '.', '')}}</td>
        </tr>
    </tfoot>
</table>
</div>

<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false,
        'order': [
            [2, 'asc']
        ]
    });

    function sel_todos(){
        // Get the checkbox
        var checkBox = document.getElementById("ctodos");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true){
            $(':checkbox').prop('checked', true);    
        } else {
            $(':checkbox').prop('checked', false);    
        }
    }
</script>    