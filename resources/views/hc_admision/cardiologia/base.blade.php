@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      trans{{('cardiologia.HistoriaClínica')}}
      </h1>
      
      <ol class="breadcrumb">
        
        <li><a href="{{asset('/')}}"><i class="fa fa-home"></i> trans{{('cardiologia.Inicio')}}</a></li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
