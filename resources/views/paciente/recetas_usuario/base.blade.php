@extends('layouts.app-template')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <h3>
        {{trans('pacientes.historialrecetas')}}
        <small></small>
      </h3>
      <!-- <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i>Paciente</a></li>
          <li class="active">Dashboard</li>
        </ol> -->
    </div>
  </section>
  @yield('action-content')
  <!-- /.content -->
</div>
@endsection