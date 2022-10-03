<!DOCTYPE html>
<html>
<head>
  
  <title>{{trans('ehistorialexam.ORDENDEPROCEDIMIENTO')}}</title>

  <style>
   
    @page { margin: 80px 70px; }
    #header { position: fixed; left: 0px; top: -40px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: 0px; right: 0px; height: 320px; }
    #footer1 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 110px; }
    #footer2 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 255px; }
   
    /* Font Definitions */
    @font-face
      {font-family:"Cambria Math";
      panose-1:2 4 5 3 5 4 6 3 2 4;}
    @font-face
      {font-family:Calibri;
      panose-1:2 15 5 2 2 2 4 3 2 4;}
     /* Style Definitions */
     p.MsoNormal, li.MsoNormal, div.MsoNormal
      {margin-top:0cm;
      margin-right:0cm;
      margin-bottom:8.0pt;
      margin-left:0cm;
      line-height:107%;
      font-size:11.0pt;
      font-family:"Calibri",sans-serif;}
    .MsoChpDefault
      {font-family:"Calibri",sans-serif;}
    .MsoPapDefault
      {margin-bottom:8.0pt;
      line-height:107%;}

    div.WordSection1
      {page:WordSection1;}
    .parrafo2 p{
      margin: 0;
    }
  </style>    
</head>
<body lang=ES-EC style="margin-top: 5px;">
    
    <div id="header">
     
      <div style="float: left;width: 10%;text-align: left;">
        <img style="margin: 0;width: 200px" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}">
      </div>
      <div  style="text-align: center;">
          <img style="margin: 0;width: 180px;">
          <p style="font-size: 19px;"><b style="text-align: center;">{{trans('ehistorialexam.INSTITUTOECUATORIANO')}} <b><br><b>{{trans('ehistorialexam.DEENFERMEDADESDIGESTIVAS')}}</b></p>
      </div>
    </div>
    <br>
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td style="border-right: 1px solid;width: 103px;text-align: center;font-size: 19px;background-color: #4682B4;"><b style="text-align: center;color: white">{{trans('ehistorialexam.ORDENDEPROCEDIMIENTO')}}</b></td>
        </tr>
      </tbody>
    </table>
      @php
        if(!is_null($paciente)){
          $seguro = Sis_medico\Seguro::find($paciente->id_seguro); 
        }
      @endphp
      @if(!is_null($orden_proc->fecha_orden))
        @php
          $fecha = substr($orden_proc->fecha_orden,0,10);
          $invert = explode( '-',$fecha);
          $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
        @endphp
      @endif
      @php
        if(!is_null($orden_proc->id)){
          $procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $orden_proc->id)->get();
        }

        $texto1 = ""; 
        $texto2 = "";
        $texto3 = "";
        $texto4 = "";
        $texto5 = "";
        $texto6 = "";
        $texto7 = "";
        $texto8 = "";

       
        if(!is_null($procedimiento_orden_tipo)){ 
          foreach($procedimiento_orden_tipo as $value1)
          {
                                          
            if($value1->id_grupo_procedimiento == 1){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true;
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();
                if($mas == true){
                  $texto1 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                  $texto1 = $texto1.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

                                           
            if($value1->id_grupo_procedimiento == 2){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto2 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                  $texto2 = $texto2.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

            
            if($value1->id_grupo_procedimiento == 3){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto3 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                 $texto3 = $texto3.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

                                            
            if($value1->id_grupo_procedimiento == 9){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto4 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                   $texto4 = $texto4.' + '.$nombre_procedimiento->nombre;
                }

              }
            }
                                            
                                            
            if($value1->id_grupo_procedimiento == 10){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto5 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                 $texto5 = $texto5.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

            if($value1->id_grupo_procedimiento == 14){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto6 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                 $texto6 = $texto6.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

            if($value1->id_grupo_procedimiento == 18){
              
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true;
                              
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto7 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }else{
                  $texto7 = $texto7.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

            if($value1->id_grupo_procedimiento == 20){
                $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true;
  
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto8 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }else{
                  $texto8 = $texto8.' + '.$nombre_procedimiento->nombre;
                }

              }
            }
          }
        }   
      @endphp
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;{{trans('ehistorialexam.FECHA')}}</b></td>
          <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$fecha_invert}}</td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;{{trans('ehistorialexam.C.I')}}</b></td>
          <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$paciente->id}}</td>
          @if($orden_proc->tipo_procedimiento == 0)
            <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{trans('ehistorialexam.ProcedimientoEndoscopicos')}}</b></td>
          @elseif($orden_proc->tipo_procedimiento == 1)
            <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{trans('ehistorialexam.PROCEDIMIENTOSFUNCIONALES')}}</b></td>
          @elseif($orden_proc->tipo_procedimiento == 2)
            <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{trans('ehistorialexam.PROCEDIMIENTOSIMAGENES')}}</b></td>
          @endif
        </tr>
        <tr>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;{{trans('ehistorialexam.PACIENTE:')}}</b></td>
          <td colspan="2" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>{{trans('ehistorialexam.EDAD:')}}</b></td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$edad}}</td>
        </tr>
        <tr>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;{{trans('ehistorialexam.DOCTORSOLICITANTE:')}}</b></td>
          <td colspan="2" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$doctor_solicitante->apellido1}} {{$doctor_solicitante->apellido2}} {{$doctor_solicitante->nombre1}} {{$doctor_solicitante->nombre2}}</td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>{{trans('ehistorialexam.SEGURO:')}}</b></td>
          <td  style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$seguro->nombre}}</td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.ANTECEDENTESPATOLOGICOS:')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $paciente->antecedentes_pat?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.ANTECEDENTESFAMILIARES:')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $paciente->antecedentes_fam?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.ANTECEDENTESQUIRURGICOS:')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $paciente->antecedentes_quir?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.MOTIVO:')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $orden_proc->motivo_consulta?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.RESUMENDELAHISTORIACLINICA:')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 5px;"><?php echo $orden_proc->resumen_clinico?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.DIAGNOSTICO:')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 5px;"><?php echo $orden_proc->diagnosticos?></td>
        </tr>
      </tbody>
    </table>
    <br>
    @if($texto7 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.PROCEDIMIENTOSFUNCIONALES')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto7}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto8 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.PROCEDIMIENTOSIMAGENES')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto8}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto1 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.ENDOSCOPIASDIGESTIVAS')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto1}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto2 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
       <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.COLONOSCOPIA-PROCTOLOGIA')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto2}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto3 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.INTESTINODELGADO')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto3}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto4 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.ECOENDOSCOPIAS')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto4}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto5 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
      <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.CPRE')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto5}}</td>
        </tr>
      </tbody>
    </table>
    <br> 
    @endif
    @if($texto6 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
      <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.BRONCOSCOPIA')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto6}}</td>
        </tr>
      </tbody>
    </table>
    <br> 
    @endif
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;{{trans('ehistorialexam.OBSERVACIONMEDICA')}}</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px">
            @if($orden_proc->necesita_valoracion=='SI') {{trans('ehistorialexam.REQUIEREVALORACIÓNCARDIOLÓGICA')}}<BR> @endif
            <?php echo $orden_proc->observacion_medica?></td>
        </tr>
      </tbody>
    </table>
    
    @php
      $nom = "";
      if(!is_null($firma)){
        $nom = "/storage/app/avatars/$firma->nombre";
      }
    @endphp

    @if($nom != "")
      <div id="footer" style="text-align: center">
        <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><o:p><img style='position: absolute;top: -12px;margin-left: 220pt;padding-left: 0px;' width=250 height=100 src="{{base_path().$nom}}" align=center hspace=12></o:p></span>
        </p>
      </div>
    @endif


    <div id="footer1">
      <div style="float: left;font-size: 14px;width: 50%;text-align: center;">
          Av.Juan Tanca Marengo, Calle 13E NE <br> 
          Torre Médico Vitalis 1 - Mezanine 3 <br>
          Telfs.: 042109180 - 042109180 <br>
          Celular: 09993066407 - 0959777712 <br>
          iecedgye@gmail.com / www.ieced.com.ec
      </div>
      <div style="font-size: 14px;text-align: center;">
          Av.Juan Tanca Marengo, Calle 13E NE <br> 
          Torre Médica II - 4to piso # 408-406 <br> 
          Telfs.: 042109180 - 042109180 <br> 
          Celular: 09993066407 - 0959777712 <br> 
          iecedgye@gmail.com / www.ieced.com.ec
      </div>
    </div>
    <div id="footer2">
      <p>
        <hr style="width: 30%;margin-left: 202pt">
        <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px;">{{trans('ehistorialexam.Doctor(a)')}}
        </label>
      </p>
    </div>
  </body>
</html>  