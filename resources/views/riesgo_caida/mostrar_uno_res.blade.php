@foreach($camilla as $value)
@php $nombre = Sis_medico\Hospital::where('id',$value->id_hospital)->first();
$estado = Sis_medico\Camilla_Gestion::where('camilla',$value->id)->whereBetween('estado_uso',[0, 4])->where('num_atencion','<>','0')->first();
    $fecha_hoy = date("d-m-Y");
    @endphp
    <tr>
        <td>
            {{$value->nombre_camilla}}
        </td>
        <td>
            {{$nombre->nombre_hospital}}
        </td>
        <td style="text-align:center;color:white;" @if(empty($estado->estado_uso)) bgcolor='#2ECC71' @elseif(($estado->estado_uso)==1) bgcolor='#2ECC71' @elseif(($estado->estado_uso)==3) bgcolor='#FEE34A' @elseif(($estado->estado_uso)==4) bgcolor='#D516EF' @elseif(($estado->estado_uso)==2) bgcolor='#F41717' @endif>@if(empty($estado->estado_uso)) {{trans('tecnicof.free')}} @elseif(($estado->estado_uso)==1) {{trans('tecnicof.free')}} @elseif(($estado->estado_uso) == 2) {{trans('tecnicof.occupied')}} @elseif(($estado->estado_uso) == 3) {{trans('tecnicof.preparation')}} @elseif(($estado->estado_uso) == 4) {{trans('tecnicof.cleaning')}} @endif
        </td>
        <td style="text-align: center;">
            @if(empty($estado->estado_uso))
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_caida.modal',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_riesgo_caida"> {{trans('tecnicof.form')}}</a>
            @elseif(($estado->estado_uso)==1)
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_caida.modal',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_riesgo_caida"> {{trans('tecnicof.form')}}</a>
            @elseif(($estado->estado_uso)==2)
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_cambio.modal',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_riesgo_cambio">{{trans('tecnicof.change')}}</a>
            @elseif(($estado->estado_uso)== 3 || 4 )
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <a class="btn btn-primary btn-xs agbtn" data-remote="{{ route('riesgo_cambio.modal_estado',[$value->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#documentos_cambio_estado">{{trans('tecnicof.change')}}</a>
            @endif
        </td>
    </tr>
    @endforeach