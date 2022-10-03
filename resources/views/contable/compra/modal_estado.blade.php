<div class="modal-content">
  <div class="modal-header">
        <h5 class="modal-title" style="text-align: left;font-weight:bold;line-height: normal;">Datos del Asiento Contable</h5>
        <div class="box-body dobra">
            <div class="box-body col-xs-12">
                @if(!is_null($id_empresa->logo))
                 <img src="{{asset('/logo').'/'.$id_empresa->logo}}"  alt="Logo Image"  style="width:80px;height:80px;" id="logo_empresa" >
                @endif
            </div>
            <div class="box-body col-xs-12">
                <label style="font-size: 14px">CALLE:{{$id_empresa->direccion}}</label><br/>
            </div>
       </div>
    </div>
    <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <span><b>Fecha de Registro:</b> {{$registro->fecha_asiento}}</span><br>
                                <span><b>Estado:</b> @if($registro->estado == '1') Activo @elseif($registro->estado =='2') Retenciones @elseif($registro->estado =='3') Comprobante Egreso @else Anulada @endif</span>
                                <p><b>Valor Registrado:</b> {{$registro->valor}}</p>
                                <p><b>Detalle: </b> </br>
                                    {{$registro->observacion}}
                                </p>
                            </div>
                            <div class="col-md-12 table table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                  <thead>
                                    <tr >
                                      <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Código</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Debe</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Haber</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @php $contador=0; @endphp
                                    @foreach ($detalle as $value)
                                    <tr>
                                        <td>{{$value->cuenta_empresa->plan}}</td>
                                        <td>{{$value->cuenta_empresa->nombre}}</td>
                                        <td>{{$value->descripcion}}</td>
                                        <td id="de{{$contador}}">$ {{$value->debe}}</td>
                                        <td id="h{{$contador}}">$ {{$value->haber}}</td>
                                        <input type="hidden" name="totales{{$contador}}" id="totales{{$contador}}" value="@if(($value->debe)!=0) {{$value->debe}} @elseif(($value->haber)!=0) {{$value->haber}} @else 0 @endif">
                                        <input type="hidden" name="debe{{$contador}}" id="debe{{$contador}}" value="{{$value->debe}}">
                                        <input type="hidden" name="haber{{$contador}}" id="haber{{$contador}}" value="{{$value->haber}}">
                                    </tr>
                                    @php $contador++; @endphp
                                    @endforeach
                                  </tbody>
                                  <tfoot>
                                    <thead>
                                        <tr>
                                            <th>TOTALES</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th> <span id="debe_total"></span> </th>
                                            <th><span id="haber_total"></span> </th>
                                        </tr>
                                    </thead>
                                  </tfoot>
                                </table>
                                <div style ="text-align: center">
                                  <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

$(document).ready(function(){
    $('.select2_cuentas').select2({
        tags: false
    });
    sumadebe()
    sumahaber()
});

    function goBack() {
      window.history.back();
    }

    function sumadebe(){
        var contador= parseInt({{$contador}});
        var sumador=0;
        for(i=0; i<contador; i++){
            var totales= parseFloat($("#debe"+i).val());

            //alert(totales);
            if((totales)!=NaN){
              sumador+=totales;
              //alert(totales)
            }
            else{
                sumador=0;
            }
        }
        //alert(sumador);
        $("#debe_total").html('$ '+sumador.toFixed(2));
    }
    function sumahaber(){
        var contador= parseInt({{$contador}});
        var sumador=0;
        for(i=0; i<contador; i++){
            var totales= parseFloat($("#haber"+i).val());

            //alert(totales);
            if((totales)!=NaN){
              sumador+=totales;
              //alert(totales)
            }
            else{
                sumador=0;
            }
        }
        //alert(totales);
        $("#haber_total").html('$ '+sumador.toFixed(2));
    }
</script>
