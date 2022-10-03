@extends('preparaciones.layouts.plantilla')
@section('content')
    <div>
        <p class="txt14 blue"><b >Indicaciones generales</b></p><br>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     El procedimiento dura aproximadamente <b class="txt10">3 horas</b>.</p>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     Venir en ayunas: no comida, no bebidas, ni agua el día del examen.</b>.</p>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     Es obligatorio venir acompañado por <b class="txt10">un familiar mayor de edad</b>.</p>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     En caso de algún retraso o emergencia que tenga el médico se le indicará oportunamente.</p><br>
    </div>
    <div>
        <p class="txt14 blue"><b>Exámenes necesarios para el procedimiento</b></p>
        <div height="auto">
            <table width="100%" border="0">
                <tr>
                    <td>
                        <div class="left">
                            <div class="foreBlue left">
                                <center><p><b class="cabezera">Exámenes de laboratorios</b></p>
                                <p class="txt10 cabezera">Examen de sangre estar en ayunas <br>
                                Torre Medica 1 - Planta Baja (Labs)
                                </p>
                                </center>
                            </div>
                            <table style="width:100%">
                                <tr>
                                    <td>
                                        <input type="radio">
                                        <label class="txt10 gray"> Hemograma</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> Úreas</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> Creatinina</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> Sodio</label><br>  
                                    </td>
                                    <td>
                                        <input type="radio">
                                        <label class="txt10 gray"> Potasio</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> Cloro</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> TP</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> TPT</label><br>  
                                    </td>
                                    <td>
                                        <input type="radio">
                                        <label class="txt10 gray"> Bilirrubina total</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> Prueba covid pcr <br>     ultrasensible</label><br>
                                        <input type="radio">
                                        <label class="txt10 gray"> Otros</label><br>  
                                    </td>
                                </tr>
                            </table><br><br>
                            <p class="txt10 blue">Fecha:  _______________________________</p>
                        </div>
                    </td>
                    <td>
                        <div class="right">
                            <div class="foreBlue right">
                                <center><p><b class="cabezera">Valoración cardilógica</b></p></center>
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <center>
                                                <input type="radio" class="cabezera">
                                                <label class="txt10 cabezera">Torre Médica 2 <br> </label>
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                <input type="radio" class="cabezera">
                                                <label class="txt10 cabezera">Mezzanine 3</label>
                                            </center>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="foreBluesky">
                                <p class="gray">Todo paciente que se realice un procedimiento médico <br> ambulatorio, deberá realizarse una valoración <br> cardiológica</p>
                            </div>
                            <div class="foreOrange cabezera">
                                <p>Si usted cuenta con marcapaso, deberá coordinar para la <br> programación respectiva del mismo.</p>
                            </div><br>
                            <p class="txt10 blue">Fecha:  _______________________________</p>
                        </div>
                    </td>
                </tr>
            </table>
            
            
        </div>   
    </div>
    <div>
        <p  class="txt14 blue"><b>Requisitos</b></p>
        <table width="100%">
            <tr>
                <td>
                    <td width="25%">
                        <input type="radio" class="blue">
                        <label class="txt10 gray"> Orden del examen <br>(original)</label><br>
                        <input type="radio" class="blue">
                        <label class="txt10 gray"> Historia laboral de 3 <br>últimas aportaciones <br>IESS</label>
                    </td>
                    <td width="25%">
                        <input type="radio" class="blue">
                        <label class="txt10 gray"> Copia de cédula del <br>paciente y/o afiliado</label><br>
                        <input type="radio" class="blue">
                        <label class="txt10 gray"> Copia carnet <br>jubilación/ montepío <br> </label>
                    
                    </td>
                </td>
                <td>
                    <td width="25%" align="left">
                    <input type="radio" class="blue">
                        <label class="txt10 gray"> Copia de los 3 últimos <br>recibos de pago</label><br>
                        <input type="radio" class="blue">
                        <label class="txt10 gray"> copia carnet seguro <br> <br> </label>
                    </td>
                    <td width="25%">
                    <input type="radio" class="blue">
                        <label class="txt10 gray"> Copia de transferencia o<br>carta comuna</label><br>
                        <input type="radio" class="blue">
                        <label class="txt10 gray"> Otros ___________________<br>_________________________ <br> </label>
                    </td>
                </td>
            </tr>
        </table>
    </div><br>
    <div>
        <table width="100%">
            <tr>
                <td class="left">
                    <div class="foreOrange cabezera">
                        <input type="radio">
                        <label> Traer todos los resultados de exámenes del <br>paciente y en caso de estar hospitalizado <br>solicitar copia de los informes e imágenes.</label><br><br>
                        <input type="radio">
                        <label> Todo paciente hospitalizado deberá <br>coordinar el traslado en ambulancia.</label>
                    </div>
                </td>
                <td>
                    <div class="foreOrange cabezera">
                        <p><b>        IMPORTANTE: informar al personal si se <br>        encuentra en algún proceso viral (gripe, tos, <br>        fiebre o malestar general)</b></p>
                    </div>
                    <p class="txt10"> </p>
                    <p> </p>
                    <p> </p>
                </td>
            </tr>
        </table>
    </div>
@endsection