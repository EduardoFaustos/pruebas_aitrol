
<!DOCTYPE html>
<html lang="en">
<head>

  <title>Cierre de Caja</title>
  <style>


    #page_pdf{
      width: 95%;
      margin: 15px auto 10px auto;
    }

    #factura_head,#factura_cliente,#factura_detalle{
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

    #detalle_totales span{
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura{
      width: 25%;
    }

    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 31%;
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
      width:20%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;

    }
    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
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
      height:60px;
      clear: both;
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

    .round td{
      font-size: 16px !important;
    }
    #factura_detalle th .details_title{
      height: 50px !important;
    }
    #factura_detalle th {
      vertical-align: middle;
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
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
        @if($empresa->id == '0992704152001')

        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:250px;height: 100px">
          </div>
          <div style="text-align: center; font-size:0.6em">
            R.U.C.: {{$empresa->id}}<br/>
            Nombre Comercial: {{$empresa->nombrecomercial}}<br/>
            Teléfono: {{$empresa->telefono1}}<br/>
            Dir.Matriz: {{$empresa->direccion}}<br/>
            <br/>
          </div>
        </td>
        @endif
      </tr>
    </table>
    <span style="font-size: 18px;"><b>Desde: </b> {{$fecha}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Hasta: </b> {{$fecha_hasta}}</span><br>
    <span style="font-size: 18px;"><b>Caja: </b> {{$caja}}</span>
    <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0" style="text-align: center;">
      <thead>
        <tr>
          <th style="font-size: 16px"><div class="details_title">#</div></th>
          <th style="font-size: 16px"><div class="details_title">Jornada</div></th>
          <th style="font-size: 16px"><div class="details_title">Apellidos</div></th>
          <th style="font-size: 16px"><div class="details_title">Nombres</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.Seguro')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">Hora Pago</div></th>
          <th style="font-size: 16px"><div class="details_title">Hora Cita</div></th>
          <th style="font-size: 16px"><div class="details_title">Tipo Cita</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.fecha')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">Recibo</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.Referencia')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.Efectivo')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">Tarjeta</div></th>
          <th style="font-size: 16px"><div class="details_title">7% T/C</div></th>
          <th style="font-size: 16px"><div class="details_title">2% T/D</div></th>
          <th style="font-size: 16px"><div class="details_title">Tran/Dep</div></th>
          <th style="font-size: 16px"><div class="details_title">{{trans('contableM.cheque')}}</div></th>
          <th style="font-size: 16px"><div class="details_title">PEND FC SEG</div></th>
          <th style="font-size: 16px"><div class="details_title">Total SIN T/C</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
        @foreach ($ordenes as $value)
          @php
            $pagos = $value->pagos;
            $efectivo  = 0;
            $tcredito = 0;
            $p7 = 0;
            $p2 = 0;
            $tran = 0;
            $cheque = 0;
            $total = 0;
            $referencia = "";
            foreach($pagos as $pago){
              if($pago->tipo == '1'){
                $efectivo += $pago->valor;
                $total += $pago->valor;
              }

              if($pago->tipo == 4){
                $va = $pago->valor/(1 +$pago->p_fi);
                $po = $va * $pago->p_fi;
                $tcredito += $va;
                $p7 += $po;
                $total += $va;
              }

              if($pago->tipo == 6){
                $va = $pago->valor/(1+$pago->p_fi);
                $po = $va * $pago->p_fi;
                $tcredito += $va;
                $p2 += $po;
                $total += $va;
              }

              if($pago->tipo == 3 || $pago->tipo == 5){
                $tran += $pago->valor;
                $total += $pago->valor;
              }

              if($pago->tipo == 2 ){
                $cheque += $pago->valor;
                $total += $pago->valor;
              }

            }
            if($efectivo > 0){
              $referencia = "CASH";
            }
            if($tcredito > 0){
              if(!is_null($referencia)){
                $referencia = "Tarjeta ";
              }else{
                $referencia = "+Tarjeta ";
              }
            }
            if($tran > 0){
              if(!is_null($referencia)){
                $referencia = "TRAN/DEP";
              }else{
                $referencia = "+TRAN/DEP";
              }
            }
            if($cheque > 0){
              if(!is_null($referencia)){
                $referencia = "CH";
              }else{
                $referencia = "+CH";
              }
            }
          @endphp
          <tr class="round">
            <td >1</td>
            <td >@if(substr($value->agenda->fechaini, 12,2) >14) Vespertino @else Matutino @endif</td>
            <td >{{$value->agenda->paciente->apellido1}} @if($value->agenda->paciente->apellido2 != 'N/A'){{$value->agenda->paciente->apellido2}} @endif</td>
            <td >{{$value->agenda->paciente->nombre1}} @if($value->agenda->paciente->nombre2 != 'N/A'){{$value->agenda->paciente->nombre2}} @endif</td>
            <td >{{$value->agenda->paciente->seguro->nombre}} </td>
            <td >{{substr($value->created_at, 11,5)}} </td>
            <td >{{substr($value->agenda->fechaini, 11,5)}} </td>
            <td> @if($value->agenda->tipo_cita == 0) Primera vez @else Consecutivo @endif</td>
            <td >{{substr($value->agenda->fechaini, 0,10)}} </td>
            <td >{{$value->id}} </td>
            <td >{{$referencia}}</td>
            <td >{{sprintf("%.2f",$efectivo)}}</td>
            <td >{{sprintf("%.2f",$tcredito)}}</td>
            <td >{{sprintf("%.2f",$p7)}}</td>
            <td >{{sprintf("%.2f",$p2)}}</td>
            <td >{{sprintf("%.2f",$tran)}}</td>
            <td >{{sprintf("%.2f",$cheque)}}</td>
            <td >{{sprintf("%.2f",$value->valor_oda)}}</td>
            <td >{{sprintf("%.2f",$total)}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="separator">

    </div>
    <div class="col-md-12">
      <br><br><br><br><br>
      <span style="font-size: 18px;border-top: solid 1px;"><br>Elaborado Por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
      <span style="font-size: 16px;"> {{Auth::user()->nombre1}} {{Auth::user()->apellido1}} {{Auth::user()->apellido2}}</span>
    </div>

  </div>

</body>
</html>
