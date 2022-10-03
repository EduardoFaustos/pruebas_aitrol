<!DOCTYPE html>
<html lang="en">
<head>

  <title>Nota de Débito Cliente</title>
  <style>


    #page_pdf{
      width: 50%;
      /*margin: 15px auto 10px auto;*/
      margin: 0 0;
      float: left;
      padding-right: 20px;
      border-right: solid 1px;
    }
    #page_pdf2{
      width: 50%;
      /*margin: 15px auto 10px auto;*/
      float: left;
      padding-left: 20px;

    }

    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
      /*margin-bottom: 10px;*/

    }
    #factura_head{
      margin-top: -50px;
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid black;
      overflow: hidden;
      padding-bottom: 10px;

    }
    
    #detalles_fotter tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid black;
      overflow: hidden;
      padding-bottom: 10px;

    }
    #detalles_fotter span {
      font-family: 'BrixSansBlack';
      text-align: right;
      font-size: 14px; 

    }
    #detalles_fotter b {
      font-family: 'BrixSansBlack';
      text-align: right;
      font-size: 17px; 

    }
    #detalle_productos span{
      font-family: 'BrixSansBlack';
      text-align: right;
      font-size: 14px; 
    }
    #detalle_totales span{
      font-family: 'BrixSansBlack';
      text-align: right;
    }
    #boderspx tr{
      border: 1px solid black !important;
    }

    .logo_factura{
      width: 25%;
    }

    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 27%;
    }

    .info_nota{
      width: 25%;
    }

    .info_cliente{
      width: 69%;
    }

    .textright{
      padding-left: 3;
    }


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

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .marco{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    table{
       border-collapse: collapse;
       font-size: 12pt;
       font-family: 'arial';
       width: 100%;
    }

   table tr:nth-child(odd){
       background: #FFF;
    }

    table td{
      padding: 4px;


    }

    table th{
       text-align: left;
       color:#3d7ba8;
       font-size: 1em;
    }

    .datos_cliente
    {
      font-size: 0.8em;
    }

    .datos_cliente label{
       width: 75px;
       display: inline-block;
    }

    .lab{
      font-size: 18px;
      font-family: 'arial';
    }

    *{
      font-family:'Arial' !important;
    }

    .mLabel{
      width:12%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;

    }

    .mLabel1{
      width:39%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;
    }

    .mLabel2{
      width:18%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;
    }

    .mLabel3{
      width:48%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;
    }
    
    .mValue{
      /*width:79%;*/
      width:50%;
      display: inline-block;
      vertical-align: top;
      /*padding-left:7px;*/
      padding: 0;
      font-size: 0.9em;
    }

    .totals_wrapper{
      width:100%;
    }
    .totals_label{
      display: inline-block;
      vertical-align: top;
      width:85%;
      text-align: right;
      font-size: 0.7em;
      font-weight: bold;
      font-family: 'Arial';
    }
    .totals_value{
      display: inline-block;
      vertical-align: top;
      width:14%;
      text-align: right;
      font-size: 0.7em;
      font-weight: normal;
      font-family: 'Arial';
    }
    .totals_separator{
      width:100%;
      height:1px;
      clear: both;
    }

    .separator{
      width:100%;
      height:20px;
      clear: both;
    }
    .separator2{
      width:100%;
      height:20px;
    }
    .separator3{
      width:100%;
      height:10px;
    }

    .details_title_border_left{
      background: #3d7ba8;
      border-top-left-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-left:10px;
    }

    .details_title_border_right{
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-right:3px;
    }

    .details_title{
      background: #3d7ba8;
      color:#FFF;
      padding: 10px;
    }

  .enlace_desactivado {
    pointer-events: none;
    cursor: default;
  }
  .row {
    padding: -1;
  }
  </style>
</head>
@php
  $subtotal   = 0;
  $iva    = 0;
  $impuesto   = 0;
  $tl_sniva   = 0;
  $total    = 0;
@endphp

<body>

  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <td class="info_factura">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  style="width:250px;height:110px">
          </div>
          <div class="round">
            <p style="padding-left: 10px;font-size: 0.7em">
              <strong>R.U.C.:</strong>&nbsp;{{$empresa->id}}<br/>
              <strong>Nombre Comercial:</strong>&nbsp;{{$empresa->nombrecomercial}}<br/>
              <strong>Teléfono:</strong>&nbsp;{{$empresa->telefono1}}<br/>
              <strong>Dir.Matriz:</strong>&nbsp;{{$empresa->direccion}}<br/>
              <strong>OBLIGADO A LLEVAR CONTABILIDAD:</strong>&nbsp;SI<br/>
              <strong>CONTRIBUYENTE ESPECIAL No.:</strong>&nbsp;18337<br/>
              <strong>Correo Electrónico:</strong>&nbsp;jesseniac.ieced@gmail.com; andreal.ieced@gmail.com<br/>
            </p>
          </div>
        </td>
        <td class="info_nota">
          <div class="round">
            <span class="h3" style="padding:20px">NOTA DE DÉBITO</span>
            <p style="padding-left: 10px;font-size: 0.6em">
              <strong>No.:</strong>&nbsp;{{$nota_debito->secuencia}}<br/>
              <strong>NÚMERO DE AUTORIZACIÓN:</strong><br/>
              1607202004099270415200120010020200000150000000113<br/>
              <strong>FECHA Y HORA DE AUTORIZACION:</strong><br/>
              2020-07-16T12:27:26-05:00<br/>
              <strong>{{trans('contableM.ambiente')}}:</strong>&nbsp;Prueba<br/>
              <strong>CLAVE DE ACCESO:</strong><br/>
              1607202004099270415200120010020200000150000000113<br/>
              @if(!is_null($nota_debito->numero)) <strong>No. Factura:</strong>&nbsp;{{$nota_debito->numero}}<br/>  @endif
            </p>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="marco">
            <div class="col-md-12">
              <table class="datos_cliente">
                <tr>
                  <td>
                    <br/>
                    <div class="row">
                      <div class="mLabel1">
                        Razón Social / Nombres y Apellidos:
                      </div>
                      <div class="mValue">
                        @if(isset($nota_debito->cliente))
                        {{trim($nota_debito->cliente->nombre)}}
                        @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                       RUC / CI:
                      </div>
                      <div class="mValue">
                      @if(isset($nota_debito->cliente))
                        {{$nota_debito->cliente->identificacion}}
                        @endif
                      </div>
                    </div>
                    @php
                      $fech_not_cred = substr($nota_debito->fecha, 0, 10);
                      $fech_inver_not = date("d/m/Y",strtotime($fech_not_cred));
                      $fech_venta = substr($nota_debito->fecha, 0, 10);
                      $fech_inver_venta = date("d/m/Y",strtotime($fech_venta));
                    @endphp
                    <div class="row">
                      <div class="mLabel2">
                       Fecha Emisión:
                      </div>
                      <div class="mValue">
                        {{$fech_inver_not}}
                      </div>
                    </div>
                    <div class="separator2"></div>
                    <div class="row" style="border-bottom: 2px solid black;">
                    </div>
                    <div class="separator3"></div>
                    <div class="row">
                      <div class="mLabel1">
                       Comprobante que se modifica:
                      </div>
                      <div class="mValue">
                        FACTURA
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                      @if(!is_null($nota_debito->numero)) No: @endif
                      </div>
                      <div class="mValue">
                      @if(!is_null($nota_debito->numero)) {{$nota_debito->numero}}<br/>  @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel3">
                      Fecha Emisión (Comprobante a modificar):
                      </div>
                      <div class="mValue">
                       @if(isset($fech_inver_venta)) {{$fech_inver_venta}} @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel1">
                      Razon por la que se modifica :
                      </div>
                      <div class="mValue">
                        {{$nota_debito->concepto}}
                      </div>
                    </div>
                  </td>
                  <td>
                    <br/>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_detalle">
      <thead>
        <tr>
          <th style="font-size: 16px"><div class="details_title">Código Principal</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.cantidad')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.Descripcion')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.precio')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.descuento')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">Precio Total</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
          @foreach($nota_debito->detalle as $value)
            <tr>
                <td> <span>@if(($value->codigo)!=null){{$value->codigo}}@endif</span></td>
                <td> <span>1</span> </td>
                <td> <span>@if(($value->concepto)!=null){{$value->concepto}}@endif</span></td>
                <td><span>@if(($value->valor)!=null){{$value->valor}}@endif</span></td>
                <td><span>0.00</span></td>
                <td> <span>@if(($value->valor)!=null){{$value->valor}}@endif</span> </td>
            </tr>
          @endforeach
      </tbody>
      <tfoot id="detalles_fotter">
          <tr>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <b>Subtotal 12%</b> 
              </td>
              <td>
              &nbsp;
              </td>
              <td>
                <span>@if(($nota_debito->iva)==1) {{$nota_debito->valor_contable}} @else 0.00 @endif</span> 
              </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>{{trans('contableM.subtotal0')}}%</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>@if(($nota_debito->iva)==0) {{$nota_debito->valor_contable}} @else 0.00 @endif</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal No Sujeto de Iva</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal Total Sin Impuestos</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>{{trans('contableM.descuento')}}</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Ice </b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Iva 12%</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
            <span>@if(($nota_debito->iva)==1) @php $subtotal= $nota_debito->valor_contable *$iva_param->iva; $subtotal2= $subtotal+$nota_debito->valor_contable; @endphp {{number_format($subtotal,2)}} @endif</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Valor Total</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>{{$nota_debito->valor_contable}}</span> 
            </td>
          </tr>
      </tfoot>  
    </table>

  </div>

  <div id="page_pdf2" style="float: left;">
    <table id="factura_head">
      <tr>
        <td class="info_factura">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  style="width:250px;height: 110px">
          </div>
          <div class="round">
            <p style="padding-left: 10px;font-size: 0.7em">
              <strong>R.U.C.:</strong>&nbsp;{{$empresa->id}}<br/>
              <strong>Nombre Comercial:</strong>&nbsp;{{$empresa->nombrecomercial}}<br/>
              <strong>Teléfono:</strong>&nbsp;{{$empresa->telefono1}}<br/>
              <strong>Dir.Matriz:</strong>&nbsp;{{$empresa->direccion}}<br/>
              <strong>OBLIGADO A LLEVAR CONTABILIDAD:</strong>&nbsp;SI<br/>
              <strong>CONTRIBUYENTE ESPECIAL No.:</strong>&nbsp;18337<br/>
              <strong>Correo Electrónico:</strong>&nbsp;jesseniac.ieced@gmail.com; andreal.ieced@gmail.com<br/>
            </p>
        </td>
        <td class="info_nota">
          <div class="round">
            <span class="h3" style="padding:20px">NOTA DE DÉBITO</span>
            <p style="padding-left: 10px;font-size: 0.6em">
              <strong>No.:</strong>&nbsp;{{$nota_debito->secuencia}}<br/>         
               <strong>NÚMERO DE AUTORIZACIÓN:</strong><br/>
               1607202004099270415200120010020200000150000000113<br/>
               <strong>FECHA Y HORA DE AUTORIZACION:</strong><br/>
               2020-07-16T12:27:26-05:00<br/>
               <strong>{{trans('contableM.ambiente')}}:</strong>&nbsp;Prueba<br/>
               <strong>CLAVE DE ACCESO:</strong><br/>
               1607202004099270415200120010020200000150000000113<br/>
               @if(!is_null($nota_debito->numero)) <strong>No. Factura:</strong>&nbsp;{{$nota_debito->numero}}<br/>  @endif
            </p>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="marco">
            <div class="col-md-12">
              <table class="datos_cliente">
                <tr>
                  <td>
                    <br/>
                    <div class="row">
                      <div class="mLabel1">
                        Razón Social / Nombres y Apellidos:
                      </div>
                      <div class="mValue">
                      @if(isset($nota_debito->cliente))
                        {{trim($nota_debito->cliente->nombre)}}
                        @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        RUC / CI:
                      </div>
                      <div class="mValue">
                      @if(isset($nota_debito->cliente))
                        {{$nota_debito->cliente->identificacion}}
                        @endif
                      </div>
                    </div>
                    @php
                      $fech_not_cred = substr($nota_debito->fecha, 0, 10);
                      $fech_inver_not = date("d/m/Y",strtotime($fech_not_cred));
                      $fech_venta = substr($nota_debito->fecha, 0, 10);
                      $fech_inver_venta = date("d/m/Y",strtotime($fech_venta));
                    @endphp
                    <div class="row">
                      <div class="mLabel2">
                       Fecha Emisión:
                      </div>
                      <div class="mValue">
                        {{$fech_inver_not}}
                      </div>
                    </div>
                    <div class="separator2"></div>
                    <div class="row" style="border-bottom: 2px solid black;">
                    </div>
                    <div class="separator3"></div>
                    <div class="row">
                      <div class="mLabel1">
                       Comprobante que se modifica:
                      </div>
                      <div class="mValue">
                        FACTURA
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                      @if(!is_null($nota_debito->numero)) No: @endif
                      </div>
                      <div class="mValue">
                      @if(!is_null($nota_debito->numero)) {{$nota_debito->numero}}<br/>  @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel3">
                      Fecha Emisión (Comprobante a modificar):
                      </div>
                      <div class="mValue">
                      @if(isset($fech_inver_venta)){{$fech_inver_venta}} @endif
                      </div>
                     
                    </div>
                    <div class="row">
                      <div class="mLabel1">
                        Razon por la que se modifica :
                      </div>
                      <div class="mValue">
                        {{$nota_debito->concepto}}
                      </div>
                    </div>
                    
                  </td>
                  <td>
                    <br/>
                  </td>
                </tr>
              
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_detalle">
      <thead>
        <tr>
          <th style="font-size: 16px"><div class="details_title">Código Principal</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.cantidad')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.Descripcion')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.precio')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.descuento')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">Precio Total</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
          @foreach($nota_debito->detalle as $value)
            <tr>
                <td> <span>@if(($value->codigo)!=null){{$value->codigo}}@endif</span></td>
                <td><span>1</span></td>
                <td><span>@if(($value->concepto)!=null){{$value->concepto}}@endif</span></td>
                <td><span>@if(($value->valor)!=null){{$value->valor}}@endif</span></td>
                <td><span>0.00</span></td>
                <td> <span>@if(($value->valor)!=null){{$value->valor}}@endif</span></td>
            </tr>
          @endforeach
          
      </tbody>
      <tfoot id="detalles_fotter">
          <tr>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <b>Subtotal 12%</b> 
              </td>
              <td>
              &nbsp;
              </td>
              <td>
                <span>@if(($nota_debito->iva)==1) {{$nota_debito->valor_contable}} @else 0.00 @endif</span> 
              </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>{{trans('contableM.subtotal0')}}%</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>@if(($nota_debito->iva)==0) {{$nota_debito->valor_contable}} @else 0.00 @endif</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal No Sujeto de Iva</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal Total Sin Impuestos</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>{{trans('contableM.descuento')}}</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Ice </b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Iva 12%</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
            <span>@if(($nota_debito->iva)==1) @php $subtotal= $nota_debito->valor_contable *$iva_param->iva; $subtotal2= $subtotal+$nota_debito->valor_contable; @endphp {{number_format($subtotal,2)}} @endif</span> 
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Valor Total</b> 
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>{{$nota_debito->valor_contable}}</span> 
            </td>
          </tr>
      </tfoot>
    </table>

    
  </div>

</body>
</html>
