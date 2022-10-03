 <html>
  <head>
    <title>Receta IECED</title>
  </head>
  <body>
    <style type="text/css">
      .div_tiny p{
        margin: 0;
      }
      @page { margin: 0px; }
      body { margin: 0px; }
    </style>
    @php
        $date = $historia->fecha_atencion;
    @endphp
    <table style="width:100%;margin-left: 40px;margin-top: 20px;">
      <tr style="width:100%;font-size: 12px !important;">
        <td style="width:50%;text-align: center;"><img style="width:250px; height: 100px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}" /></td> 
        <td style="width:50%;text-align: center;"><img style="width:250px;height: 100px;" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}" /></td>
      </tr>
      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
      <tr style="width: 100%;font-size: 12px !important;">
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
        <td><b>Fecha: </b> Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2);if ($mes == 01) {echo "Enero";}if ($mes == 02) {echo "Febrero";}if ($mes == 03) {echo "Marzo";}if ($mes == 04) {echo "Abril";}if ($mes == 05) {echo "Mayo";}if ($mes == 06) {echo "Junio";}if ($mes == 07) {echo "Julio";}if ($mes == '08') {echo "Agosto";}if ($mes == '09') {echo "Septiembre";}if ($mes == '10') {echo "Octubre";}if ($mes == '11') {echo "Noviembre";}if ($mes == '12') {echo "Diciembre";}?> del {{substr($date, 0, 4)}}</td>
      </tr>
      <tr style="width: 100%;font-size: 12px !important;">
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
        <td><b>Nombre: </b> {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif {{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif</td>
      </tr>
      <tr style="width: 100%;font-size: 12px !important;">
        <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
        <td ><b>CI.</b> {{$paciente->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Edad: </b>{{$edad}} años</td>
      </tr>
      <tr style="width: 100%;font-size: 12px !important;">
          <td ><b>Alergia: </b>
            {{$principioActivo}}
          </td> 
          <td ><b>Alergia:</b> 
            {{$principioActivo}}
          </td>
      </tr>
      <tr style="width: 100%;font-size: 12px !important;">
        <td><b>Rp:</b></td>
        <td><b>Prescripción:</b></td>
      </tr>
      <!--tr>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->rp;?></textarea></td>
        <td><textarea style="width: 95%; height: 400px;border: none;"><?php echo $receta->prescripcion;?></textarea></td>
      </tr-->
      @foreach($detalles as $value)
      @php $genericos = $medicinas->where('id',$value->id_medicina)->first()->genericos; @endphp
      <tr style="font-size: 12px !important;">
        <td><p style="width: 95%; border: none; margin:0">{{$value->medicina->nombre}}( @foreach($genericos as $gen) {{$gen->generico->nombre}} @endforeach ): {{$value->cantidad}}</p></td>
        <td><p style="width: 95%; border: none;margin:0">{{$value->medicina->nombre}}: {{$value->dosis}}</p></td>
      </tr>
      @endforeach

      <tr style="font-size: 13px !important;width: 100%;">
        <td style="vertical-align: top;"><div class="div_tiny" style="width: 95%;line-height: 1.2;"><?php echo $receta->rp; ?></div></td>
        <td style="vertical-align: top;"><div class="div_tiny" style="width: 90%;line-height: 1.2;"><?php echo $receta->prescripcion; ?></div></td>
      </tr>

    </table>
    <br>
    <div class="div_tiny" style="width: 46%;line-height: 1.2;float:left;margin-left: 55px;padding: 0;margin-right: -70px">
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=200 height=auto src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" style="" align=center hspace=12><br>
        @endif
      </div>
      <div style="float: left;width: 45%;text-align: center;">
        @if(!is_null($firma))
          <img width=200 height=auto src="{{base_path().'/storage/app/avatars/'.$firma->nombre}}" align=center hspace=12><br>
        @endif  
      </div>
    </div>

      <div style="position: fixed; left: 10px; bottom: 10px; right: 10px; height: 150px;">
        <!--Left-->
        <div style="float: left;width: 50%;margin-top: 50px;">
          <div>
            <div style="margin-left: 80px">
            <label style="background-color:#0995BA; color: #FFFFFF; text-align: left;"> &nbsp;Citas médicas&nbsp;</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label style="background-color:#0995BA; color: #FFFFFF; text-align: right;"> &nbsp;Medios Digitales&nbsp;</label>
            </div>
            <table style="width:100%;margin-left: 40px;">
                <tr style="width:100%;">
                  <td style="width:6%;"> 
                    <img style="width:20px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-AGENDA.png'}}" />
                  </td>
                  <td style="width:47%;"> 
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">042109180 - 0983753477 - 0991051413/0986637750</label>
                  </td>
                  <td style="width:47%;">
                    <img style="width:20px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-MUNDO-.png'}}" />
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">www.ieced.com.ec</label>
                  </td>
                </tr>
                <tr style="width:100%;">
                  <td style="width:6%;"> 
                    <img style="width:20px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-UBICACION.png'}}" />
                  </td>
                  <td style="width:47%;">
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">
                    Av. Abel R. Castillo S/N y Av. Juan Tanca Marengo, Torre Médica I, mezanine 3, y 
                    Torre Medica II, piso 4, consultorios 405 - 406, Ciudad del Sol(Omnihospital)
                    </label>
                  </td>
                  <td style="width:47%;"> 
                    <img style="width:80px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-REDES-.png'}}" />
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">
                    @Ieced
                    </label>
                  </td>
                </tr>
            </table>
          </div>
        </div>
        <!--rigth-->
        <div style="float: right;width: 50%;margin-top: 50px;">
          <div>
            <div style="margin-left: 80px">
            <label style="background-color:#0995BA; color: #FFFFFF; text-align: left;"> &nbsp;Citas médicas&nbsp;</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label style="background-color:#0995BA; color: #FFFFFF; text-align: right;"> &nbsp;Medios Digitales&nbsp;</label>
            </div>
            <table style="width:100%;margin-left: 40px;">
                <tr style="width:100%;">
                  <td style="width:6%;"> 
                    <img style="width:20px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-AGENDA.png'}}" />
                  </td>
                  <td style="width:47%;"> 
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">042109180 - 0983753477 - 0991051413/0986637750</label>
                  </td>
                  <td style="width:47%;">
                    <img style="width:20px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-MUNDO-.png'}}" />
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">www.ieced.com.ec</label>
                  </td>
                </tr>
                <tr style="width:100%;">
                  <td style="width:6%;"> 
                    <img style="width:20px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-UBICACION.png'}}" />
                  </td>
                  <td style="width:47%;">
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">
                    Av. Abel R. Castillo S/N y Av. Juan Tanca Marengo, Torre Médica I, mezanine 3, y 
                    Torre Medica II, piso 4, consultorios 405 - 406, Ciudad del Sol(Omnihospital)
                    </label>
                  </td>
                  <td style="width:47%;"> 
                    <img style="width:80px; height: 20px;" src="{{base_path().'/public/icons_receta_membretada/ICONO-REDES-.png'}}" />
                    <label class="col-md-12 control-label" style="width: 100%;font-size: 10px !important;">
                    @Ieced
                    </label>
                  </td>
                </tr>
            </table>
          </div>
        </div>
      </div>
  </body>
</html>