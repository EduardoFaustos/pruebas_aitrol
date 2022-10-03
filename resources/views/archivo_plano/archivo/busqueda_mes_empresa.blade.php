<style type="text/css">
  
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .info_nomina{
      width: 69%;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .datos_nomina
    {
      font-size: 0.8em;
    }

    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    #rol_pago{
      width: 100%;
      margin-bottom: 10px;
    }


    .info_nomina .col-xs-8 {
        padding-left:10px;
        font-size: 0.9em;
    }
    .info_nomina .round{
        padding-top:10px;
    }

    .titulo-wrapper{
        width: 100%;
        text-align: center;
    }

    .modal-body .form-group {
        margin-bottom: 0px;
    }

    .h3.modal_h3{
        font-family: 'BrixSansBlack';
        font-size: 8pt;
        display: block;
        background: #3d7ba8;
        color: #FFF;
        text-align: center;
        padding: 3px;
        margin-bottom: 5px;
        padding: 7px;
        font-size: 1em;
        margin-bottom: 15px;
    }
    .h3.modal_h3_2{
        margin-top: -20px !important;
        margin-bottom: 25px !important;
        padding: 7px;
        font-size: 1em;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .separator{
      width:100%;
      height:20px;
      clear: both;
    }

    .separator1{
      width:100%;
      height:5px;
      clear: both;
    }

    
    /* Nuevo CSS*/

    .mLabel{
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 10px;
    }

    .texto {
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 0;
      line-height: 15px;
    }

    .color_texto{
      color:#FFFFFF;
    }

    .head-title{
      background-color: #4682B4;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .t9{
      font-size: 0.9rem;
    }

    .well-dark{
      background-color: #cccccc;
    }

    .table>tbody>tr>td{
    padding: 0;
    }
    .control-label{
        padding: 1;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }
    table.dataTable thead > tr > th {
    padding-right: 10px;
    } 
    td{
        font-size: 12px;
    }
</style>

<div style="border-radius: 8px;" id="det_busqueda">
  <div id="contenedor">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
      <div class="col-md-12">
        <div class="table-responsive col-md-12">
          <table id="example2_item" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
            <thead style="background-color: #4682B4">
              <tr>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEG</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEGURO</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">N_EXP</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">BASE_0</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">BASE_IVA</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">V_IVA</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">GAST_AMD_10</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TOTAL_M_IVA</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">ACCION</th>
              </tr>
            </thead>
            <tbody>
              @php
                    $total_0=0;
                    $total_iva=0;
                    $total_v_iva=0;
                    $total_10=0;
                @endphp

                @foreach($cant_pac as $value)
                @php
                    if(isset($arr_base_0[$value->id])) {
                        $total_0=$arr_base_0[$value->id];
                    }
                    if(isset($arr_base_iva[$value->id])) {
                        $total_iva=$arr_base_iva[$value->id];
                    }
                    if (isset($arr_v_iva[$value->id])) {
                        $total_v_iva=$arr_v_iva[$value->id];
                    }
                    
                    if (isset($arr_amd_10[$value->id])) {
                        $total_10=$arr_amd_10[$value->id];
                    }
                    
                    $total_m_iva=$total_0+$total_iva+$total_v_iva+$total_10;
                @endphp
                <tr>
                    <input value="5" class="hidden" type="text" id="seguro" >
                    <td>{{$value->tipo}} <input value="{{$value->id}}" class="hidden" type="text" id="tipo_seguro" ></td>
                    <td>{{$value->nombre}} <input value="{{$value->nombre}}" class="hidden" type="text" id="nombre_tseg"></td>
                    <td>{{$value->cantidad}} <input value="{{$value->cantidad}}" class="hidden" type="text" id="cantidad_{{$value->id}}"></td>
                    <td>@if(isset($arr_base_0[$value->id])) ${{round($arr_base_0[$value->id],2)}} <input value="{{round($arr_base_0[$value->id],2)}}" class="hidden" type="text" id="base_0_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="base_0_{{$value->id}}"> @endif </td>
                    <td>@if(isset($arr_base_iva[$value->id])) ${{round($arr_base_iva[$value->id],2)}} <input value="{{round($arr_base_iva[$value->id],2)}}" class="hidden" type="text" id="base_iva_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="base_iva_{{$value->id}}"> @endif</td>
                    <td>@if(isset($arr_v_iva[$value->id])) ${{round($arr_v_iva[$value->id],2)}} <input value="{{round($arr_v_iva[$value->id],2)}}" class="hidden" type="text" id="v_iva_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="v_iva_{{$value->id}}">  @endif</td>
                    <td>@if(isset($arr_amd_10[$value->id])) ${{round($arr_amd_10[$value->id],2)}} <input value="{{round($arr_amd_10[$value->id],2)}}" class="hidden" type="text" id="amd_10_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="amd_10_{{$value->id}}"> @endif</td>
                    <td>${{round($total_m_iva,2)}} <input value="{{round($total_m_iva,2)}}" class="hidden" type="text" id="total_iva_{{$value->id}}"> </td>
                    <td><button id="guardar_agrupado" type="submit" class="btn btn-success btn-xs" onclick="guardar('{{$value->id}}')">GUARDAR</button></td>
                </tr>
                    
                @endforeach
            </tbody>
          </table>
        </div> 
      </div> 
    </div>
  </div>
</div>

<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
  $('#example2').DataTable({
      'language': {
        'emptyTable': '<span class="label label-primary" style="font-size:14px;">No se encontraron registros.</span>'
      },
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  function guardar(id){
 
    var mes_plano = $('#mes_plano').val();
    var seguro = $('#seguro').val();
    var empresa = $('#id_empresa').val();
    var cantidad_exp = $('#cantidad_'+id).val();
    var base_0 = $('#base_0_'+id).val(); 
    var base_iva = $('#base_iva_'+id).val(); 
    var v_iva = $('#v_iva_'+id).val();  
    var amd_10 = $('#amd_10_'+id).val();
    var total_iva = $('#total_iva_'+id).val();

    console.log(mes_plano, empresa, seguro, id, cantidad_exp, base_0, base_iva);
    $.ajax({
        type: 'post',
        url:"{{ route('archivo.guardar_agrupado')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {
            "tipo_seguro":id,
            "mes_plano":mes_plano,
            "seguro":seguro,
            "empresa":empresa,
            "cantidad":cantidad_exp,
            "base_0":base_0,
            "base_iva":base_iva,
            "v_iva":v_iva,
            "amd_10":amd_10,
            "total_iva":total_iva,
        },
        success: function(data){
            console.log(data);
            if(data == "ok"){
                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })
                
            };
        },
        error: function(data){
            console.log(data);
        }
        });
    }
</script>





