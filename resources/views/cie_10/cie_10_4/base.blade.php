@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      {{trans('cie10_4.administraciondecodigoscie10')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{asset('/')}}"><i class="fa fa-home"></i> {{trans('cie10_4.inicio')}}</a></li>
    </ol>
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection