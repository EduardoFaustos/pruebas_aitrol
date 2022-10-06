<!DOCTYPE html>
<html lang="en">

<head>

  <title>Factura</title>
  <style>
    #page_pdf {
      width: 95%;
      margin: 15px auto 10px auto;
    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100%;
      margin-bottom: 10px;
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;

    }

    #detalle_totales span {
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura {
      width: 25%;
    }

    .info_empresa {
      width: 50%;
      text-align: center;
    }

    .info_factura {
      width: 31%;
    }

    .info_cliente {
      width: 69%;
    }

    .textright {
      padding-left: 3;
    }


    .h3 {
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .round {
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    table {
      border-collapse: collapse;
      font-size: 12pt;
      font-family: 'arial';
      width: 100%;
    }


    table tr:nth-child(odd) {
      background: #FFF;
    }

    table td {
      padding: 4px;


    }

    table th {
      text-align: left;
      color: #3d7ba8;
      font-size: 1em;
    }

    .datos_cliente {
      font-size: 0.8em;
    }

    .datos_cliente label {
      width: 75px;
      display: inline-block;
    }

    .lab {
      font-size: 18px;
      font-family: 'arial';
    }

    * {
      font-family: 'Arial' !important;
    }

    .mLabel {
      width: 20%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left: 15px;
      font-size: 0.9em;

    }

    .mValue {
      width: 79%;
      display: inline-block;
      vertical-align: top;
      padding-left: 7px;
      font-size: 0.9em;
    }

    .totals_wrapper {
      width: 100%;
    }

    .totals_label {
      display: inline-block;
      vertical-align: top;
      width: 85%;
      text-align: right;
      font-size: 0.7em;
      font-weight: bold;
      font-family: 'Arial';
    }

    .totals_value {
      display: inline-block;
      vertical-align: top;
      width: 14%;
      text-align: right;
      font-size: 0.7em;
      font-weight: normal;
      font-family: 'Arial';
    }

    .totals_separator {
      width: 100%;
      height: 1px;
      clear: both;
    }

    .separator {
      width: 100%;
      height: 60px;
      clear: both;
    }

    .details_title_border_left {
      background: #3d7ba8;
      border-top-left-radius: 10px;
      color: #FFF;
      padding: 10px;
      padding-left: 10px;
    }

    .details_title_border_right {
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color: #FFF;
      padding: 10px;
      padding-right: 3px;
    }

    .details_title {
      background: #3d7ba8;
      color: #FFF;
      padding: 10px;
    }

    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

  </style>

</head>

<body>
  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:250px;height: 100px">
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$fact_venta->empresa->id}}<br/>
            Nombre Comercial: {{$fact_venta->empresa->nombrecomercial}}<br/>
            Teléfono: {{$fact_venta->empresa->telefono1}}<br/>
            Dir.Matriz: {{$fact_venta->empresa->direccion}}<br/>
            <br/>
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">{{trans('contableM.DETALLEDEPAQUETE')}}</span>
            <p style="padding-left: 10px;font-size: 20px;">
              Paciente :<strong> @if(!is_null($fact_venta)){{$fact_venta->paciente->nombre1}} {{$fact_venta->paciente->apellido1}}@endif</strong><br />
              Seguro:<strong> @if(!is_null($orden)){{$orden->seguro->nombre}}@endif</strong><br />
              <!--Nivel:<strong> @if(!is_null($orden->id_nivel)){{$orden->id_nivel}}@endif</strong><br />-->
              Detalle de Factura No.:<strong> @if(!is_null($fact_venta)){{$fact_venta->nro_comprobante}}@endif</strong><br />
              Procedimiento:@if(!is_null($fact_venta)){{$fact_venta->procedimientos}}@endif<br />
              Fecha:@if(!is_null($fact_venta)){{$fact_venta->fecha}}@endif<br />
            </p>
          </div>
        </td>
      </tr>
    </table>
    <div class="modal-content">
      
      @foreach ($detalles as $value)
        
        @if(!is_null($value->id_producto))

          @php
            $paquetes = $orden->paquetes_detalles->where('id_producto',$value->id_producto);
          @endphp

          @if(!is_null($paquetes))

          <div class="table-responsive col-md-12">
                <table style="border: 1px solid; width: 100%;font-size: 18px;">
                  <caption><b>{{$value->descripcion}}</b></caption>
                  <thead>
                  <tr>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.grupo')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.cantidad')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.valor')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.iva')}}</th>
                    <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">{{trans('contableM.total')}}</th>
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
                          $grup_prod = Sis_medico\Ct_productos::where('id',$prod_paq->id_paquete )->first();
                        } 
                        
                        $total_valor += $value2->precio;
                        $total_iva += $value2->valor_iva;
                        $total_general += ($value2->precio)+($value2->valor_iva);
                      
                      @endphp
                      <tr>
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
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->descripcion}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($prod_paq)){{$prod_paq->cantidad}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->precio}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->valor_iva}}@endif</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">@if(!is_null($value2)){{$value2->precio+$value2->valor_iva}}@endif</td>
                      </tr>
                      @endforeach
                  </tbody>
                  <tfoot>
                   <tr>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;"></td>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;"></td>
	                  <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">SUMAN</td>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">{{$total_valor}}</td>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">{{$total_iva}}</td>
                    <td style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;">{{$total_general}}</td>
                   </tr>
                  </tfoot>
                </table>
              </div>
          @endif
        @endif
      @endforeach
    </div>
    <div class="separator"></div>
  </div>
</body>

</html>