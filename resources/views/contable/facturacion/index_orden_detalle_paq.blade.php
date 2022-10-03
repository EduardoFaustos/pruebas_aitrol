@foreach ($detalles as $value)
          
          @if(!is_null($value->id_producto))
            
            @php
              $paquetes = $orden->paquetes_detalles->where('id_producto',$value->id_producto);
            @endphp

            @if(!is_null($paquetes))

              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="blue" aria-describedby="example2_info">
                  <caption><b>{{$value->descripcion}}</b></caption>
                
                  <thead>
                  <tr class="well-dark">
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.grupo')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cantidad')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.iva')}}</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
                    <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.accion')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                    @php 
                      $total_valor = 0;
                      $total_iva = 0; 
                      $total_general = 0;
                    @endphp  
                    @foreach ($paquetes as $value2)

                      @php

                        $grup_prod = null;

                        $prod_paq = Sis_medico\Ct_productos_paquete::where('id',$value2->id_producto_paquete)->where('estado','1')->first();
                        
                        if(!is_null($prod_paq)){
                          $grup_prod = Sis_medico\Ct_productos::where('id',$prod_paq->id_producto )->first();
                        }

                      @endphp
                       
                      <tr>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->descripcion}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">
                          @if(!is_null($grup_prod))
                            @if($grup_prod->grupo == '1')
                              Insumos
                            @elseif($grup_prod->grupo == '2')
                              Medicamentos
                            @elseif($grup_prod->grupo == '4')
                              Procedimientos
                            @elseif($grup_prod->grupo == '3')
                              Servicios
                            @elseif($grup_prod->grupo == '5')
                              Otros
                            @elseif($grup_prod->grupo == '6')
                              Honorario
                            @elseif($grup_prod->grupo == '7')
                              Equipo
                            @endif
                          @endif
                        </td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($prod_paq)){{$value2->cantidad}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->precio}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->valor_iva}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{($value2->cantidad*$value2->precio)+$value2->valor_iva}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;"> 
                          <a class="btn btn-warning btn-xs" data-remote="{{route('actualiza_orden_detalle.paquete', ['id_ord_det_paq' => $value2->id])}}" data-toggle="modal" data-target="#edit_orde_det_paq" style="float: center;"> <span class="glyphicon glyphicon-edit"></span></a>
                        </td>
                      </tr>
                      @endforeach
                  </tbody>
                  
                </table>
              </div>

            @endif

          @endif
         
      @endforeach

