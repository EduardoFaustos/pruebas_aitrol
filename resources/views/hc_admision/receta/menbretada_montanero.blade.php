  <html>
  <head>
    <title>Receta alex</title>
  </head>
  <body>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
      @page { margin: 0px; }
      #footer1 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 150px; }
      body { margin: 0px; }
    </style>
    @php
        $date = $historia->fecha_atencion; 
        if(is_null($date)){
          $date = $historia->agenda->fechaini;
        }
    @endphp
    <table style="width:100%;margin-left: 40px;margin-top: 50px;">
      <tr style="width:100%;">
        <td style="width:50%;text-align: left;"><img style="width:150px; height: auto;" src="{{base_path().'/public/imagenes/logo_omni.png'}}"></td>
        <td style="width:50%;text-align: center"><font size= "5" face="Comic Sans MS,arial,verdana"> Dr. Eduardo Montanero Soledispa</font><br> Traumatólogo - Ortopedista</br></td>
      </tr>
     
      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
      <tr style="width: 100%;">
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
      </tr>
      <tr style="width: 100%;">
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
      </tr>
      <tr style="width: 100%;">
        <td style="width: 12%;text-align: right; padding-right: 100px;" ><b>Edad: </b>{{$edad}} años</td>
        <td style="width: 12%;text-align: right; padding-right: 100px;" ><b>Edad: </b>{{$edad}} años</td>
      </tr>
      <tr>
        <td><b>Rp:</b></td>
        <td><b>Prescripción:</b></td>
      </tr>
      <!--tr>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->rp; ?></textarea></td>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->prescripcion; ?></textarea></td>
      </tr-->
      @foreach($detalles as $value)
      @php $genericos = $medicinas->where('id',$value->id_medicina)->first()->genericos; @endphp
      <tr style="font-size: 13px !important;">
        <td><p style="width: 95%; border: none; margin:0">{{$value->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$value->cantidad}}</p></td>
        <td><p style="width: 95%; border: none;margin:0">{{$value->medicina->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach

     <tr style="font-size: 14px !important;width: 100%;">
        <td style="vertical-align: top;"><div class="div_tiny" style="width: 95%;line-height: 1.2;"><?php echo $receta->rp; ?></div></td>
        <td style="vertical-align: top;"><div class="div_tiny" style="width: 90%;line-height: 1.2;"><?php echo $receta->prescripcion; ?></div></td>
      </tr>
    </table>
  
 <div id="footer1" style="text-align: center">
      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><o:p><img style="width:1000px; height: 6px" src="{{base_path().'/public/imagenes/lineas_recetas2.png'}}"></o:p></span>
      </p>
      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><font size= "1";face="Comic Sans MS,arial,verdana">Av. Abel Romeo Castillo y Av. Tanca Marengo, 3er. Piso. Consultorio 314 * <b>Teléfono:</b> 2109194 - 6012377 <b>Cel.:</b> 0999266750<br><b>Email:</b> emontaneros@hotmail.com . consultorioemontanero@gmail.com * <b>Web:</b> www.omnihospital.com * Guayaquil - Ecuador</font>
      </p>
    </div>
     </body>
    
</html>
