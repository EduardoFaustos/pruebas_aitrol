@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Administración de Tipos de Proveedores
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('proveedor.index') }}"><i class="fa fa-users"></i> Proveedores</a></li>
        <li class="active">Tipos de Proveedores</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection