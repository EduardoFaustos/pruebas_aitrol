@extends('layouts.app-template-modulos')
@section('content')
<style type="text/css">
  .wrapper{
    background-color:white !important; 
  }
</style>
  <div class="content-wrapper" style="margin-left: 0;">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="font-family: Helvetica;margin: 2px;color: white; text-align: center; padding: 20px; border-radius: 8px; background-image: linear-gradient(to right,#004AC1,#0C8BEC,#004AC1);  margin-bottom: 15px;">
      <h1><b>M&OacuteDULOS<b></h1>
    </section>

    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection