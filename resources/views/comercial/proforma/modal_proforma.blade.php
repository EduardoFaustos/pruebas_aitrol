<div class="modal-header">
    <h3 style="margin:0;">Seleccion de Proforma</h3>
</div>
<div class="modal-body">

    <form method="post" id="form_nivel" action="">
        {{ csrf_field() }}
        <div class="row">
            <table class="table  table-bordered">
                <thead>

                </thead>
                <tr>
                    <th> # </th>
                    <th> Fecha </th>
                    <th> Seguro </th>
                    <th> {{trans('proforma.detalle')}} </th>
                    <th> {{trans('proforma.total')}}</th>
                    
                    
                    <th></th>
                </tr>
                <tbody>
                    @foreach ($orden as $value)
                    <tr>
                        <td> {{$value->id}} </td>
                        <td> {{$value->fecha_emision}}</td>
                        <td> {{$value->seguro->nombre}} 
                           
                        </td>
                        <td> {{$value->observacion}} <br>
                        <p> 
                            @foreach($value->detalles as $det)
                                {{$det->producto->nombre}} +
                            @endforeach
                        </p>
                        </td>
                        
                        <td> {{$value->total}} </td>
                        
                        <td> <button onclick="generarRC({{$value->id}})" type="button" class="btn btn-primary">{{ trans('proforma.seleccionar') }}</button> </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>


    </form>

</div>

<div class="modal-footer">

</div>

<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
</script>