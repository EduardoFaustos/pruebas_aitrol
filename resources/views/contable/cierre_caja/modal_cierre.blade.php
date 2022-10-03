@php
$date = date('Y-m-d H:m');
@endphp
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Cierre de Caja {{ date('Y/m/d H:i:s') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body" style="height:auto">
        <input type="hidden" name="id" id="id" value={{ $id }}>
        <form id="pore" method="POST" action="{{ route('c_caja.store_salida') }}">
            {{ csrf_field() }}
            <!--<input type="hidden" name="ordenes[]" id="ordenes" value={{$records}}> -->
            <textarea class="hide" id="ordenes" name="ordenes" rows="8" cols="80">{{$records}}</textarea>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Desde</label>
                    <input class="form-control" type="text" name="fechacierre" id="fechacierre">
                    <input type="hidden" name="val" value="0">
                </div>

                <div class="form-group col-md-6">
                    <label>Hasta</label>
                    <input class="form-control" type="datetime" name="fechahasta" id="fechahasta"
                        value="{{ $date }}" readonly>
                    <input type="hidden" name="val" value="0">
                </div>

                <div class="form-group col-md-6">
                    <label>Valor Final</label>
                    <input class="form-control" type="text" name="valorcierre" id="valorcierre"
                        value="{{ number_format(round($valor, 2), 2, '.', '') }}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('contableM.observaciones') }}</label>
                    <textarea re class="form-control col-md-12" name="observacioncierre" id="observacioncierre" cols="3"
                        rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer" style="display:flex;justify-content:center">
                <button type="submit" id="final" class="btn btn-primary">{{ trans('contableM.guardar') }}</button>
            </div>
        </form>
            <span class="label label-default">{{ trans('contableM.RESUMENDECAJA') }}</span>
            <div class="col-md-12">
                &nbsp;
            </div>
            <div class="row" style="text-align:center">
                <div class="form-group col-md-1">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-money"></i>{{ trans('contableM.Efectivo') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="efectivo">{{ number_format($arraySend['efectivo'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-1">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-money"></i>{{ trans('contableM.cheque') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="cheque">{{ number_format($arraySend['cheque'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>


                <div class="form-group col-md-1">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-newspaper-o"></i>{{ trans('contableM.Deposito') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="deposito">{{ number_format($arraySend['deposito'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-2">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-newspaper-o"></i>{{ trans('contableM.Transferencia') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="transferencia">{{ number_format($arraySend['transferencia'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-2">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-credit-card"></i>{{ trans('contableM.TarjetadeCredito') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="credito">{{ number_format($arraySend['credito'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-credit-card"></i>{{ trans('contableM.TarjetadeDebito') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="debito">{{ number_format($arraySend['debito'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-2">
                    <div class="row">
                        <div class="col-md-12">
                            <label> <i class="fa fa-file-text"></i>{{ trans('contableM.PendientedePago') }}</label>
                        </div>
                        <div class="col-md-12">
                            <span id="pendiente">{{ number_format($arraySend['pendiente'], 2, '.', '') }}</span>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>
<script src="{{ asset('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset('/js/bootstrap-datetimepicker.js') }}"></script>
<script>
    $(function() {

        $('#fechacierre').datetimepicker({
            format: 'YYYY/MM/DD H:ss',
            defaultDate: '{{ date('Y/m/d H:s') }}',
        }).on('dp.change', function(e) {
            verificarCierre();

        });
    });

    function verificarCierre(e) {

        $.ajax({
            type: 'post',
            url: "{{ route('vericar_fecha_cierra') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'fecha': document.getElementById("fechacierre").value,
            },
            success: function(data) {
                console.log(data);
                //si esta null si puede
                if (data.data.length === 0) {
                    recal();

                } else {
                    document.getElementById("fechacierre").value = '{{ date('Y/m/d H:s') }}';

                }




            },
            error: function(data) {
                console.log(data);
            }
        })
    }


    function recal() {
        const id = document.getElementById("id").value;
        let fecha = document.getElementById("fechacierre").value;
        $.ajax({
            type: 'post',
            url: "{{ route('valor_actualizado') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id,
                'fechaDesde': fecha,
            },
            success: function(data) {
              console.log(data.arraySend);
                let valor = data.valor.toFixed(2);
                document.getElementById("ordenes").value = JSON.stringify(data.records);
                document.getElementById("valorcierre").value = valor;

                document.getElementById("efectivo").innerHTML = data.arraySend.efectivo.toFixed(2);
                document.getElementById("cheque").innerHTML = data.arraySend.cheque.toFixed(2);
                document.getElementById("deposito").innerHTML = data.arraySend.deposito.toFixed(2);
                document.getElementById("transferencia").innerHTML = data.arraySend.transferencia.toFixed(2);
                document.getElementById("credito").innerHTML = data.arraySend.credito.toFixed(2);
                document.getElementById("pendiente").innerHTML = data.arraySend.pendiente.toFixed(2);
                document.getElementById("no_facturados_cant").innerHTML = data.arraySend.no_facturados_cant.toFixed(2);
                document.getElementById("facturados_cant").innerHTML = data.arraySend.facturados_cant.toFixed(2);



            },
            error: function(data) {
                console.log(data);
            }
        })
    }
</script>
