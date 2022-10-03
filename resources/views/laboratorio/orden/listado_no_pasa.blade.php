<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<style type="text/css">
  .dataTable > tbody> tr:hover{
     background-color: #99ffe6;
  }
</style>
@php
  $recargo = $orden->recargo_p/100;  
  $descuento = $orden->descuento_p/100;
  $r = 1 + $recargo - ($descuento*$recargo);
@endphp
<table class="col-md-12">
  <tr>
    <td>@if($seguro->id=='1')<h4>Cotización Particular</h4>@else<h4>Cotización para el seguro {{$seguro->nombre}}</h4>@endif</td>
    <td><b>Cantidad: </b></td>
    <td><span id="scantidad">{{$orden->cantidad}}</span></td>
    <td><b>SubTotal: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id='svalor'>{{round($orden->valor * $r, 2)}}</span></td>
  </tr>  
  <tr>
    <td>
        
        <a target="_blank" href="{{route('cotizador.imprimir',['id' => $orden->id])}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-download-alt"></span> Imprimir</a>
        @if($orden->estado=='-1')
        <a href="{{route('cotizador.generar',['id' => $orden->id])}}" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-ok"></span> Emitir Orden</a>
        @endif
        
    </td>
    <td></td>
    <td></td>
    <td><b>Descuento: </b></td>
    <td style="font-size: 20px;text-align: right;">(-)$ <span id="sdescuento_valor">{{$orden->descuento_valor}}</span></td>
  </tr>  
  <!--tr>
    <td></td>
    <td></td>
    <td></td>
    <td><b>Recargo: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id="srecargo_valor">{{$orden->recargo_valor}}</span></td>
  </tr-->    
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td><b>Total: </b></td>
    <td style="font-size: 20px;text-align: right;">$ <span id="stotal_valor">{{$orden->total_valor}}</span></td>
  </tr>   
</table>


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
                <td colspan="4" style="background-color: #ff6600;color: white;margin: 0px;">{{$agrupador->nombre}}</td>
              </tr>
            @endif
            
            @foreach($examenes_labs2 as $examen)
              @if($examen->id_examen_agrupador_labs==$agrupador->id)
                <tr @if(in_array($examen->ex_id,$detalles_ch)) style="background-color: #b3e0ff;" @endif>
                  <td>{{$examen->descripcion}}</td>
                  @php 
                    $e_valor = $examen->valor;
                    $cubre = true;
                    
                    $convenio = DB::table('convenio as c')->where('c.id_seguro',$seguro->id)->get();
                    if($convenio->count()>0){
                      if($convenio->count()==1){
                        $examen_valor = DB::table('examen_nivel')->where('id_examen',$examen->ex_id)->where('nivel',$convenio->first()->id_nivel)->first();
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
                      }else{

                        $examen_valor = DB::table('examen_nivel')->where('id_examen',$examen->ex_id)->where('nivel',$id_nivel)->first();
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
                    }
                  @endphp  
                  <td>$ {{number_format($e_valor * $r ,2)}}</td>
                  <td>@if(!$cubre) No cubierto @endif</td>
                  <td ><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange" @if(in_array($examen->ex_id,$detalles_ch)) checked @endif ></td>  
                </tr>
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
  $('input[type="checkbox"].flat-orange').iCheck({
    checkboxClass: 'icheckbox_flat-orange',
    radioClass   : 'iradio_flat-orange'
  }) 

  

  $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){

    //console.log(this.name.substring(2));
    cotizador_crear_id(this.name.substring(2));

  });

  $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
 
    //cotizador_crear();
    cotizador_delete_id(this.name.substring(2));

  });


  
</script>    