@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{trans('econsultam.Detalle')}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('consultam ') }}"><i class="fa fa-calendar-minus-o"></i>{{trans('econsultam.ConsultasyProcedimientos')}}</a></li>
        <li class="active">{{trans('econsultam.Detalle')}}</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection