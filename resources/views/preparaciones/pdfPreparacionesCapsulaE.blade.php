@extends('preparaciones.layouts.plantilla')
@section('content')
    <div>
        <p class="txt14 blue"><b >Indicaciones generales</b></p><br>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     El procedimiento dura aproximadamente <b class="txt10">3 horas</b>.</p><br>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     Venir en ayunas: no comida, no bebidas, ni agua el día del examen.</b>.</p><br>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     Es obligatorio venir acompañado por <b class="txt10">un familiar mayor de edad</b>.</p><br>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     En caso de algún retraso o emergencia que tenga el médico se le indicará oportunamente.</p><br>
    </div>
    <div>
        <p class="txt14 blue"><b>Indicaciones de medicamentos</b></p><br>
        <p class="txt10 gray"><b>En caso de sufrir de epilepsia o de la presión: tomarse la pastilla con un poco de agua 2 horas antes del procedimiento.</b></p>
        <p class="txt10 gray"><b>Suspender los siguientes medicamentos, bajo supervisión de su cardiólogo.</b></p><br>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     <b>Aspirina, plavix o clopidogrel (5 días antes).</b></p>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     <b>Coumadin o warfarina (7 días antes).</b></p>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     <b>Enoxaparina o clexane, o fraxiparina subcutáneo (1 día antes).</b></p>
        <p class="txt10 gray"><img src="{{base_path().'/storage/app/preparaciones_img/check.png'}}", width="15 px">     <b>Si toma otro tipo de medicamento anticoagulante, por favor notificar a las recepcionistas.</b></p>
    </div><br>
    <div>
        <p  class="txt14 blue"><b>Requisitos</b></p><br>
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