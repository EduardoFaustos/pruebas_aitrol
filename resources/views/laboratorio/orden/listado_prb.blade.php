<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .dataTable > tbody> tr:hover{
     background-color: #99ffe6;
  }
</style>


@php
  $rolUsuario = Auth::user()->id_tipo_usuario;
@endphp

<div id="obsefinal">
</div>
<table class="col-md-12">
  <tr>
    <td><h4>@if($seguro->id=='1')Cotización Particular @else Cotización para el seguro {{$seguro->nombre}}@endif No. {{$orden->id}}</h4></td>
    <td><b>Cantidad: </b></td>
    <td><span id="scantidad">{{$orden->cantidad}}</span></td>
    <td><b>SubTotal: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id='svalor'>@if($orden->cobrar_pac_pct < 100) {{ $orden->valor_con_oda }} @else {{$orden->valor}} @endif</span></td>
  </tr>  
  <tr>
    <td>
        <a target="_blank" href="{{route('cotizador.imprimir',['id' => $orden->id])}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-download-alt"></span> Cotización</a>
        <a target="_blank" href="{{route('pdf_cotizacion',['id' => $orden->id])}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-download-alt"></span> Recibo de Cobro</a>
        @if($orden->estado=='-1')
        <!--a href="{{route('cotizador.generar',['id' => $orden->id])}}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-ok"></span> Emitir Orden</a-->
        <a href="javascript:emitir()" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-ok"></span> Emitir Orden</a>
        @endif

        @if(is_null($agenda))
          <a align="right" type="button" class="btn btn-primary btn-sm" href="javascript:div_agendar()">
            <span class="glyphicon glyphicon glyphicon-calendar"></span> Agendar
          </a>
        @else
          @if($agenda->agenda->estado=='0')
          <a align="right" type="button" class="btn btn-primary btn-sm" href="javascript:div_agendar()">
            <span class="glyphicon glyphicon glyphicon-calendar"></span> Agendar
          </a>
          @else
          <a align="right" type="button" class="btn btn-success btn-sm" href="{{ route('agenda.edit2', ['id' => $agenda->id_agenda, 'doctor' => '4444444444'])}}" target="_blank">
            <span class="glyphicon glyphicon glyphicon-calendar"></span> Agendado
          </a>
          @endif
        @endif
       
        @if($orden->estado=='-1' || $rolUsuario=='1')
          <a id="boton_info_factura" class="btn @if($orden->cedula_factura=='') btn-danger @else btn-success @endif btn-sm"  href="{{route('facturalabs.informacion_factura',['id' =>$orden->id])}}" data-toggle="modal" data-target="#informacion_factura"> Informacion de Factura</a>
          <a id="boton_forma_pago" class="btn btn-info btn-sm" href="{{route('facturalabs.forma_pago',['id_orden' =>$orden->id])}}" data-toggle="modal" data-target="#forma_pago"> Forma de Pago HumanLabs</a>
          @if($orden->cedula_factura=='')
          <button class="btn btn-danger btn-sm" onclick="alert('Ingrese la informacion de factura');"> Forma de Pago Gastro Clinica</button>
          @else
          <a id="boton_forma_pago_gas" class="btn btn-success btn-sm" href="{{route('orden.facturacion_gastroclinica',['id' =>$orden->id])}}" data-toggle="modal" data-target="#forma_pago_gas"> Forma de Pago Gastro Clinica</a>
          @endif
        @endif

      
    </td>
    
    <td></td>
    <td></td>
    <td><b>Descuento: </b></td>
    <td style="font-size: 20px;text-align: right;">(-)$ <span id="sdescuento_valor"> {{$orden->descuento_valor}} </span></td>
  </tr>  
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td><b>Fee Administrativo: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id="srecargo_valor">{{$orden->recargo_valor}}</span></td>
  </tr>    
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td><b>Total: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id="stotal_valor">@if($orden->cobrar_pac_pct < 100) {{ $orden->total_con_oda }} @else {{$orden->total_valor}} @endif</span></td>
  </tr>   
</table>

<input type="hidden" name="tiene_domicilio" id="tiene_domicilio" value="{{$tiene_domicilio}}">
<input type="hidden" name="tiene_covid" id="tiene_covid" value="{{$tiene_covid}}">
<input type="hidden" name="valor_covid" id="valor_covid" value="{{$valor_covid}}">
<input type="hidden" name="stotal" id="stotal" value="{{$orden->total_valor}}">
<input type="hidden" name="orden_id" id="orden_id" value="{{$orden->id}}">
<input type="hidden" name="id_paciente" id="id_paciente" value="{{$orden->id_paciente}}">


<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
        <tbody>

          @foreach($agrupador_labs as $agrupador)
            @php
              $examenes_labs2 = $examenes_labs->where('id_examen_agrupador_labs',$agrupador->id);
            @endphp
            @if($examenes_labs2->count()>0)

              <tr>
                <td colspan="5" style="background-color: #ff6600;color: white;margin: 0px;">{{$agrupador->nombre}}</td>
              </tr>
            @endif
            @foreach($examenes_labs2 as $examen)
              <!-- si estuvo antes seleccionado se muestra el estado 0 es en agrupador sabana -->
              @if($examen->estado=='0' && !in_array($examen->ex_id,$detalles_ch))
              @else
                @if($examen->id_examen_agrupador_labs==$agrupador->id)
                @php 
                 $nombre_examen = "";
                if($examen->tiempo_examen ==""|| is_null($examen->tiempo_examen)  ){
                  $nombre_examen = "";
                }else{
                   $nombre_examen = ' - TIEMPO DE ENTREGA: ' . $examen->tiempo_examen;
                }
                  @endphp
                  <tr @if(in_array($examen->ex_id,$detalles_ch)) style="background-color: #b3e0ff;" @endif>
                    <td> {{$examen->nombre}} {{$nombre_examen}}</td>
                    @php 
                      $e_valor = $examen->valor;
                      $cubre = true;
                      if($id_nivel!=null){
                        
                        $examen_valor = $examen_valor_o->where('id_examen',$examen->ex_id)->first();
                        if(!is_null($examen_valor)){
                          if($seguro->id!='1' && ($examen_valor->valor1=='0' || $examen_valor->valor1==null)){
                            $cubre = false;
                          }else{
                            $e_valor = $examen_valor->valor1;
                            $cubre = true;
                          }
                        }else{
                          $cubre = false;
                        }
                      }
                      /*
                      if(($orden->total_valor - $valor_covid) >= 80){

                        if($examen->ex_id=='1191' || $examen->ex_id=='1195' || $examen->ex_id=='1196'){
                          $e_valor = 0;  
                        }
                      }
                      */ 
                          
                    @endphp  
                    <td>$ {{number_format($e_valor,2)}}</td>
                    @php $valor_con_oda = ''; @endphp
                    @if(in_array($examen->ex_id,$detalles_ch))
                      @php 
                        $item = $orden->detalles->where('id_examen',$examen->ex_id)->first();
                        if(!is_null($item)){
                          $valor_con_oda = $item->valor_con_oda;
                        }
                      @endphp  
                    @endif
                    <td>$ {{ $valor_con_oda }} </td>
                    <td>@if(!$cubre) No cubierto @endif</td>
                    <td ><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange" @if(in_array($examen->ex_id,$detalles_ch)) checked @endif ></td>  
                  </tr>
                @endif
              @endif
            @endforeach
            
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">
  $('#forma_pago').on('hidden.bs.modal', function(){
      location.reload();
      $(this).removeData('bs.modal');
    }); 

  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  }) 
  
  domicilio();//promo_covid();
  function domicilio(){
    //console.log("domicilio");
    var domicilio = $('#pres_dom').val();
    var tiene_dom = $('#tiene_domicilio').val();//console.log(tiene_dom);
    
    if(domicilio=='1'){
      if(tiene_dom=='0'){
        var confirmar = confirm("¿Desea agregar el valor de la toma a domicilio?");
        if(confirmar){
          
          cotizador_crear_id('1203');

              
        }
        
        //cargar_buscador(); 

      }
    }  
      
  }
  //EMITIR SIN FACTURACION ELECTRONICA
  function emitir(){
    $.ajax({
      type: 'get',
      url:"{{route('facturalabs.cuadrar',['id' => $orden->id])}}",
      datatype: 'json',
      success: function(data){
        //alert(data);
        if (data == 'ok') {
          
          swal.fire({
            title: 'Al emitir la orden, se enviará a laboratorio con la fecha de emisión {{date('Y-m-d')}}',
            //text: "You won't be able to revert this!",
            icon: "warning",
            type: 'warning',
            showCancelButton: true,
            buttons: true,
          
          }).then((result) => {
            if (result.value) {
              cierre_caja()
              //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";                
            }
          })  
        }
        if(data == 'no'){
          alert("No cuadran los valores de cotización con la forma de pago");
        }
      },
      error: function(data){
        //console.log(data);
      }
    });   

          
  
  }
  function modal_correo(){
    let id_paciente = document.getElementById("id_paciente").value;
    let id = document.getElementById('id').value;
    $.ajax({
      type: 'get',
      url:"{{route('orden.labs_validar_mail',['id' => $orden->id])}}",
      datatype: 'html',
      success: function(data){
        
      
      },
      error: function(data){
      // console.log(data);
      }
 
    });
  
  }
  function cierre_caja(){
    $.ajax({
      type: 'post',
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      url:"{{route('c_caja.storeLabs')}}",
      data:{'id_orden': '{{$orden->id}}','id_paciente':'{{$orden->id_paciente}}','total':'{{$orden->total_valor}}'},
      datatype: 'json',
      success: function(data){
          console.log(data);
          //alert(data)
      },
      error: function(data){
        //console.log(data);
        //alert(data)
      }
    });  
  }
  //CON FACTURACION ELECTRONICA
  function emitir_sri(){

    var seguro = $('#id_seguro').val();
    if(seguro=='1'){//PARTICULARES DEBE FACTURAR
      solicita_datos_facturas();
    }else{
      swal.fire({
        title: 'Al emitir la orden, se enviará a laboratorio con la fecha de emisión {{date('Y-m-d')}}',
        //text: "You won't be able to revert this!",
        icon: "warning",
        type: 'warning',
        buttons: true,
      
      }).then((result) => {
        if (result.value) {
          location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";
        }
      })
    }
      
  
  }

  function solicita_datos_facturas(){
    $.ajax({
      type: 'get',
      url:"{{route('facturalabs.datos_factura',['id' => $orden->id])}}",
      datatype: 'json',
      success: function(data){
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data){
        //console.log(data);
      }
    });  
  }

  function promo_covid(){
    /*var promo = 80; //VARIABLE TOPE DE PROMOCION
    var habilitado = 1;//VARIABLE PARA HABLITAR EN PRODUCCION
    var ctotal = $('#stotal').val();
    var tiene_covid = $('#tiene_covid').val();
    var valor_covid = $('#valor_covid').val();
    var valor_reconfirmar = 0;
  
    if(habilitado){
      if(ctotal >= promo){ 
        if(tiene_covid=='1'){
          if(valor_covid>0){
            valor_reconfirmar = ctotal - valor_covid;
            if(valor_reconfirmar>=promo){
              alert("El valor de la orden supera los $"+promo+", El valor del Covid se actualizará a 0.00");
              cotizador_recalcular();
            }
          }
        }else{
          var confirmar2 = confirm("El valor de la orden supera los $"+promo+", Desea agregar Examen Covid");
          if(confirmar2){
            cotizador_crear_id('1191'); 
            //$('#seleccionados').iCheck('check');
            //cargar_buscador(); 
          }
        }
      }else{
        if(tiene_covid=='1'){
          if(valor_covid<1){
              alert("El valor de la orden no supera los $"+promo+", El valor del Covid se actualizará"); 
              cotizador_recalcular();   
          }    
        }  
      }
    } */ 
    /*$.ajax({
      type: 'post',
      url:"#",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#fecha_enviar").serialize(),
      success: function(data){

        $('#div_agendar').empty().html(data);
      },
      error: function(data){
        //console.log(data);
      }
    });*/ 


  }

  

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){

    //console.log(this.name.substring(2));
    //alert("crea");
    cotizador_crear_id(this.name.substring(2));
    

  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
 
    //cotizador_crear();
    //alert("borra");
    cotizador_delete_id(this.name.substring(2));


  });

  

  function div_agendar(){
    
    $.ajax({
      type: 'get',
      url:"{{ url('seguros_privados/agenda_labs/agendar') }}",
      success: function(data){
        $('#agenda_privados').empty().html(data);
        $('#modal_privados').modal();
      },
      error: function(data){
        
      }
    }); 
  } 
  
</script>    