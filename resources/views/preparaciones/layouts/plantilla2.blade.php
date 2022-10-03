<!DOCTYPE html>

<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
</head>
<style>
      *{
            margin:0;
            font-family:'Metropolis', sans-serif;
      }
      .container{
          max-width: 92%;
          margin: 0 auto;
      }

      .foreBlue{
          background-color: #0886c5;
      }
      .foreBlue2{
          background-color: #0886c5;
          color: white;
          padding:0;
          font-size:12;
      }
      .foreOrange{
          background-color: #f39200;
          color: white;
          padding:10px;
          font-size: 10;
      }
      .foreBluesky{
          background-color: #daecf6;
          color: white;
          padding:10px;
          font-size: 10;
      }
      .blue{
          color:#0886c5;
      }
      .bluesky{
          color:#daecf6;
      }
      .orange{
          color:#f39200; 
      }
      .gray{
          color:#3c3c3b;
      }
      .center{
            text-align:center;
      }
      .check{
          list-style-image: url(storage/app/preparaciones_img/check.png);
      }
      .cabezera{
          background-color: var(--foreBlue);
          color: white;
      }
      .txt20{
          font-size: 20;
          font-family: 'Metropolis';
          font-weight: bold;
          font-style: normal;
      }
      .txt14{
          font-size:14;
          font-family: 'Metropolis';
          font-style: normal;
      }
      .txt12{
          font-size:12;
          font-family: 'Metropolis';
          font-style: normal;
      }
      .txt10{
          font-size:10;
          font-family: 'Metropolis';
          font-style: normal;
      }
      .txt7{
          font-size:7;
          font-family: 'Metropolis';
          font-style: normal;
      }
      .left{
          padding:7px;
          padding-bottom:30px;
          padding-top: 10px;
      }
      .right{
          padding:7px;
          padding-bottom:25px;
          padding-top: 10px;
      }
</style>
<body class="container" style="margin-top:60px">
    <div class="cabezera foreBlue">
        <table width="100%" style="padding:-14px">
            <tr>
                <td width="10%" background-color="#f39200">
                    <img src="{{base_path().'/storage/app/preparaciones_img/QR.png'}}", width="120 px">
                </td>
                <td width="19%">
                    <img src="{{base_path().'/storage/app/preparaciones_img/LOGO-Y-REDES-SOCIALES.png'}}", width="170 px">
                </td>
                <td width="16%">
                    <p class="txt10 cabezera">Fecha procedimiento:</p>
                    <p class="txt7 cabezera">(Por confirmar)</p>
                    <p style="font-size:3"> </p>
                    <label class="txt10 cabezera">Lugar:</label><br>
                    <label style="font-size:6">(Por confirmar)</label>
                </td>
                <td width="22%">
                    <p style="font-size:1"> </p>
                    <p style="background:white"> </p>
                    <p style="font-size:7"> </p>
                    <input type="radio" class="radio">
                    <label class="txt10 cabezera">  Torre Médica II, 4to piso <br>   ofic. 405, 406</label>
                </td>
                <td  width="14%" align="right">
                    <p class="txt10 cabezera">Hora:              </p>
                    <p class="txt7 cabezera">(Por confirmar)        </p>
                    <p style="font-size:3"> </p>
                    <input type="radio">
                    <label class="txt10 cabezera">  Edificio Vitalis<br>Mezzanine 3</label>
                </td>
                <td width="15%">
                    <p style="font-size:1"> </p>
                    <p style="background:white">                     </p>
                    <p style="font-size:15"> </p><br>
                </td>
                <td width="4%">
                    <p> </p>
                </td>
            </tr>
        </table>
    </div><br>
    <div class="center">
        <p class="txt20 blue"><b>{{$procedimiento}}</b>@if(isset($nombre_secundario))<span class="txt14 blue"><b> @php echo $nombre_secundario; @endphp</b></span>@endif</p>
    </div><br><br>
    @yield('content')

    <div>
        <table width="100%">
            <tr>
                <td>
                    <center>
                    <p>__________________________</p>
                    <p class="txt10 gray">Firma del paciente</p>
                    </center>
                </td>    
                <td>
                    <center>
                    <p>__________________________</p>
                    <p class="txt10 gray">Firma de recepcionista</p>
                    </center>
                </td>
            </tr>
        </table>
    </div>
    <div><br><br>
        <center>
            <img src="{{base_path().'/storage/app/preparaciones_img/piepag.png'}}", width="80%">
        </center>
    </div>
</body>
</html>
