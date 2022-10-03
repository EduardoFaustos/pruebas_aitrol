@extends('servicios_generales.limpieza_equipos.base')
@section('action-content')
@php
$date = date('Y-m-d');
@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
                <h5><b>{{trans('limpieza_equipof.reportcle')}}</b></h5>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button class="btn btn-danger" onclick="goBack()"><i class="fa fa-reply" aria-hidden="true"></i> Regresar</button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{route('limpieza_control.excel')}}" method="POST">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-1">
                            <label for="">{{trans('limpieza_equipof.from')}}</label>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="date" name="desde">
                        </div>
                        <div class="col-md-1">
                            <label for="">{{trans('limpieza_equipof.to')}}</label>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="date" name="hasta">
                        </div>
                        <div class="col-md-1">
                            <label for="">{{trans('limpieza_equipof.type')}}</label>
                        </div>
                        <div class="col-md-2">
                            <select name="tipo" class="form-control" id="tipo">
                                <option value="">{{trans('limpieza_equipof.select')}}</option>
                                <option value="0">CRM</option>
                                <option value="1">Gastroclinica</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="">{{trans('limpieza_equipof.area')}}</label>
                        </div>
                        <div class="col-md-2">
                            <select name="area" class="form-control" id="area">
                                <option value="">{{trans('limpieza_equipof.select')}}</option>
                                @foreach($sala as $value)
                                <option value="{{$value->id}}">{{$value->nombre_sala}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div style="text-align: right; margin-top:2%">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-book" aria-hidden="true"></i> {{trans('limpieza_equipof.search')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
    function goBack() {
        window.history.back();
    }
</script>
@endsection
