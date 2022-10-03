@extends('layouts.app-template')
@section('content')
  <style>
    .color_rojo{
      color: red;
      font-weight: bold;
      font-size: 22px;
    }
  </style>

  <div class="content-wrapper cnt_wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 align="center">
          BUSCADOR EN BASE
      </h1>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection