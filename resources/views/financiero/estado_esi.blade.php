@extends('financiero.base')
@section('action-content')
<section class="content">
@php
    $activot = 0;
    $activonoct = 0;
    $pasivot = 0;
    $pasivonoct = 0;
    $patrimoniot = 0;
    $anio='2020';
@endphp
@if(count(@$data)>0)
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <div class="box-header with-border">
              </div>
              <!-- /.box-header -->
              {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl>
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">{{trans('etodos.EstadodesituaciónInicial')}}</h4>
                </div>
                <div class="col-md-4">
                  <dl>
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
                    <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
                  </dl>
                </div>
              </div> --}}
              <div class="box-body">
                <div class="col-md-1">
                  <dl>
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
                  </dl>
                </div>
                <div class="col-md-3">
                  <dl>
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">{{trans('etodos.EstadodesituaciónInicial(ESI)')}}</h4>
                  <h5 style="text-align: center;">{{trans('etodos.Periodo')}} @php echo $anio;  @endphp</h5>
                </div>
                <div class="col-md-4">
                </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>
        <div class="row">
          <div class="table-responsive col-md-12">
          <div class="content">
          <div class="content col-md-6">
          <h5 style="text-align: left;">{{trans('etodos.ACTIVOSCORRIENTES')}}</h5>
              <table id="example2" class="table table-condensed col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Cuenta')}}</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Detalle')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Saldo')}}</th>
                  </tr>
                </thead>
                <tbody>
                @php
                    $band = 0;      $acum_mas = 0;      $acum_menos = 0;
                @endphp
                @foreach($data as $value)
                @php
                    if($value['signo']=='1'){
                        $acum_mas += $value['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];
                    }
                @endphp


                  <tr>
                    <td style="font-size: 10px;">{{$value['id_plan']}}</td>
                    <td style="font-size: 10px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                  </tr>

                  <!-- @if(trim($value['signo'])=='2' and $band==0)
                    @php
                        $band = 1;
                    @endphp
                    <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">TOTAL ACTIVO CORRIENTE</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                    </tr>
                @endif -->

                @endforeach
                @php $activot=$acum_mas; @endphp
                <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">{{trans('etodos.TOTALACTIVOCORRIENTE')}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                </tr>
                <!-- <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">TOTAL EGRESOS</td>
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos<0) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                </tr> -->
                </tbody>
              </table>
          </div>

          <div class="content col-md-6">
          <h5 style="text-align: left;">{{trans('etodos.PASIVOSCORRIENTES')}}</h5>
              <table id="example2" class="table table-condensed col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Cuenta')}}</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Detalle')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Saldo')}}</th>
                  </tr>
                </thead>
                <tbody>
                @php
                    $band = 0;      $acum_mas = 0;      $acum_menos = 0;
                @endphp
                @foreach($datap as $value)
                @php
                    if($value['signo']=='1'){
                        $acum_mas += $value['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];
                    }
                @endphp


                  <tr>
                    <td style="font-size: 10px;">{{$value['id_plan']}}</td>
                    <td style="font-size: 10px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                  </tr>

                 <!--  @if(trim($value['signo'])=='2' and $band==0)
                    @php
                        $band = 1;
                    @endphp

                @endif
 -->
                @endforeach
                @php $pasivot=$acum_mas; @endphp
                <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">{{trans('etodos.TOTALPASIVOCORRIENTE')}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                    </tr>
                <!-- <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">TOTAL EGRESOS</td>
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos<0) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                </tr> -->
                </tbody>
              </table>
          </div>

          <div class="content col-md-6">
          <h5 style="text-align: left;">{{trans('etodos.ACTIVOSNOCORRIENTES')}}</h5>
              <table id="example2" class="table table-condensed col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Cuenta')}}</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Detalle')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Saldo')}}</th>
                  </tr>
                </thead>
                <tbody>
                @php
                    $band = 0;      $acum_mas = 0;      $acum_menos = 0;
                @endphp
                @foreach($dataanc as $value)
                @php
                    if($value['signo']=='1'){
                        $acum_mas += $value['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];
                    }
                @endphp


                  <tr>
                    <td style="font-size: 10px;">{{$value['id_plan']}}</td>
                    <td style="font-size: 10px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                  </tr>

                 <!--  @if(trim($value['signo'])=='2' and $band==0)
                    @php
                        $band = 1;
                    @endphp
                    <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">TOTAL ACTIVO NO CORRIENTE</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                    </tr>
                @endif -->

                @endforeach
                @php $activonoct=$acum_mas; @endphp
                <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">{{trans('etodos.TOTALACTIVONOCORRIENTE')}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                </tr>

                <!-- <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">TOTAL EGRESOS</td>
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos<0) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                </tr> -->
                </tbody>
              </table>
          </div>

          <div class="content col-md-6">
          <h5 style="text-align: left;">{{trans('etodos.PASIVOSNOORRIENTES')}}</h5>
              <table id="example2" class="table table-condensed col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Cuenta')}}</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Detalle')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Saldo')}}</th>
                  </tr>
                </thead>
                <tbody>
                @php
                    $band = 0;      $acum_mas = 0;      $acum_menos = 0;
                @endphp
                @foreach($datapnc as $value)
                @php
                    if($value['signo']=='1'){
                        $acum_mas += $value['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];
                    }
                @endphp


                  <tr>
                    <td style="font-size: 10px;">{{$value['id_plan']}}</td>
                    <td style="font-size: 10px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                  </tr>

                 <!--  @if(trim($value['signo'])=='2' and $band==0)
                    @php
                        $band = 1;
                    @endphp

                @endif
 -->
                @endforeach
                @php $pasivonoct=$acum_mas; @endphp
                <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">{{trans('etodos.TOTALPASIVONOCORRIENTE')}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                    </tr>
                <!-- <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">TOTAL EGRESOS</td>
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos<0) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                </tr> -->
                </tbody>
              </table>
          </div>

          <div class="content col-md-6">
          <h5 style="text-align: left;">{{trans('etodos.PATRIMONIO')}}</h5>
              <table id="example2" class="table table-condensed col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Cuenta')}}</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Detalle')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('etodos.Saldo')}}</th>
                  </tr>
                </thead>
                <tbody>
                @php
                    $band = 0;      $acum_mas = 0;      $acum_menos = 0;
                @endphp
                @foreach($datapat as $value)
                @php
                    if($value['signo']=='1'){
                        $acum_mas += $value['saldo'];
                    }else{
                        $acum_menos += $value['saldo'];
                    }
                @endphp


                  <tr>
                    <td style="font-size: 10px;">{{$value['id_plan']}}</td>
                    <td style="font-size: 10px;">{{ strtoupper($value['nombre']) }}</td>
                    <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                  </tr>

                 <!--  @if(trim($value['signo'])=='2' and $band==0)
                    @php
                        $band = 1;
                    @endphp

                @endif
 -->
                @endforeach
                @php $patrimoniot=$acum_mas; @endphp
                <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;">{{trans('etodos.TOTALPATRIMONIO')}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format($acum_mas,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 12px;font-weight: bold;">{{trans('etodos.TOTALPASIVOYPATRIMONIO')}}</td>
                        <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas < 0) color:red; @endif" >{{number_format(($pasivot+$pasivonoct+$patrimoniot),2)}}</td>
                    </tr>
                <!-- <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">TOTAL EGRESOS</td>
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_menos < 0) color:red; @endif" >{{number_format($acum_menos,2)}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10px;font-weight: bold">SALDO FINAL DEL EFECTIVO</td>
                    @php if($acum_menos<0) $acum_menos = $acum_menos *-1;  @endphp
                    <td style="font-size: 10px;text-align: right; font-weight: bold; @if($acum_mas-$acum_menos<0) color:red; @endif" >{{number_format($acum_mas-$acum_menos,2)}}</td>
                </tr> -->
                </tbody>
              </table>
          </div>

          </div>
        </div>
      </div>
@endif
</section>
@endsection
