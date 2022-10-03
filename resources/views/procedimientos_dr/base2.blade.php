@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      {{trans('procedimientodr.ProcedimientosTV')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url('procedimientos_dr') }}"><i class="fa fa-television"></i> {{trans('procedimientodr.Control')}}</a></li>
      <li class="active">{{trans('procedimientodr.ProcedimientosTV')}}</li>
    </ol>
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection