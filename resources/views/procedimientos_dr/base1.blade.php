@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      {{trans('procedimientodr.SalaEspera')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url('pentax') }}"><i class="fa fa-television"></i> {{trans('procedimientodr.ControlPentax')}}</a></li>
      <li class="active">{{trans('procedimientodr.PentaxSalaEspera')}}</li>
    </ol>
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection