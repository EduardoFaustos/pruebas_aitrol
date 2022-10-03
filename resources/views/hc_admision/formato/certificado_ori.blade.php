<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 15">
<meta name=Originator content="Microsoft Word 15">




</head>

<body lang=ES-EC link=blue vlink=purple style='tab-interval:36.0pt;margin-right: -20pt;margin-left: -15pt;margin-top: -30pt;border: 1.5px solid #009999;margin-bottom: -30pt;'>

<div style="height: 90%;" class=WordSection1>

<p class=MsoNormal align=center style='text-align:center; padding-top: 2pt;text-align: right;'><img width=160
height=60 src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}" align=center
hspace=12></p>  


<p class=MsoNormal style='margin-left: 30px;margin-top: 30px;'><span style='font-size:12pt'>Guayaquil, {{substr($date, 8, 2)}}  de <?php $mes = substr($date, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($date, 0, 4)}}</span></p>

<p class=MsoNormal><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><span style='mso-ansi-language:ES-EC;
mso-fareast-language:ES'><o:p>&nbsp;</o:p></span></span></span></p>


<h5 align=center style='text-align:center;page-break-after:avoid'><span
style='mso-bookmark:_Hlk504475451'><span style='mso-bookmark:_Hlk504475441'><span
style='font-size:12.0pt;font-family:"Arial",sans-serif;mso-ansi-language:ES-EC'>CERTIFICADO
MÉDICO <o:p></o:p></span></span></span></h5>


<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-top: 50px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>A quien
interese:<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>



<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:25px;margin-top: 20px;text-align:justify;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC;text-align:justify;'>El que
suscribe, {{$doctor}}, médico Gastroenterólogo en ejercicio
legal de su profesión certifica: <span style='mso-spacerun:yes'></span><o:p></o:p></span></i></span></span></p>


<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:25px;text-align:justify;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal;text-align:justify;'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Por medio del presente certifico haber atendido profesionalmente a {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} con cédula {{$paciente->id}} quien se acerca a nuestra institución para realizar <b>{{$tipo}}</b>, el día {{substr($cfecha, 8, 2)}}  de <?php $mes = substr($cfecha, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($cfecha, 0, 4)}}. <span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>

@if($descanso>0)

<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:25px;text-align:justify;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC;text-align:justify;'>Dandosele descanso médico {{$descanso}}({{$letras}}) días desde el día {{substr($cfecha, 8, 2)}}  de <?php $mes = substr($cfecha, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($cfecha, 0, 4)}} hasta el día {{substr($fecha_hasta, 8, 2)}}  de <?php $mes = substr($fecha_hasta, 5, 2); if($mes == 01){ echo "Enero";} if($mes == 02){ echo "Febrero";} if($mes == 03){ echo "Marzo";} if($mes == 04){ echo "Abril";} if($mes == 05){ echo "Mayo";} if($mes == 06){ echo "Junio";} if($mes == 07){ echo "Julio";} if($mes == '08'){ echo "Agosto";}  if($mes == '09'){ echo "Septiembre";} if($mes == '10'){ echo "Octubre";} if($mes == '11'){ echo "Noviembre";} if($mes == '12'){ echo "Diciembre";} ?> del {{substr($fecha_hasta, 0, 4)}} para su total recuperación.<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>


@endif

<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:25px;text-align:justify;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal;text-align:justify;'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Certificado que extiendo profesionalmente a {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} y puede hacer uso del mismo como considere necesario.<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>

<p class=MsoNormal style='text-align:justify'><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'><o:p>&nbsp;</o:p></span></i></span></span></p>


<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; "><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Atentamente,<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>

<p class=MsoNormal style='text-align:justify'><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'><o:p>&nbsp;</o:p></span></i></span></span></p>


@if($id_doctor1=='1314490929')
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Dra. {{$doctor}}<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Gastroenteróloga<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Registro No. 120078<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Libro 1 Folio 4131<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
@else
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Dr. {{$doctor}}<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Gastroenterólogo<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Registro No. 16203<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
<p class=MsoNormal style="margin-left: 30px;font-size: 12pt;margin-right:15px; margin-top: -15px;"><span style='mso-bookmark:_Hlk504475451'><span
style='mso-bookmark:_Hlk504475441'><i style='mso-bidi-font-style:normal'><span
style='mso-bidi-font-family:Arial;mso-ansi-language:ES-EC'>Libro 1 Folio 3771 No 11003<span style='mso-spacerun:yes'>                </span><o:p></o:p></span></i></span></span></p>
@endif







</div>
<div style="height: 5%;width: 98%;padding-left: 5pt;margin-top: 30pt;margin-bottom: -30pt;">
<table style="">
  <tbody style="font-size: 9pt;border: 1pt solid;">
    <tr >
      <td style="padding-left: 10pt;">Telf: (04) 2109180 / Fax: (04) 2109180 / iecedgye@gmail.com / <span style="color: blue;text-decoration: underline;">www.ieced.com.ec</span> Av. Abel Romeo Castillo y Av. Juan Tanca Marengo, Torre Vitales 1 -Mezanine 3, Guayaquil</td>
    </tr>
    
  </tbody>
</table>
</div>

</body>

</html>
