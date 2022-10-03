@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{trans('winsumos.adm_productos')}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('producto.index') }}" ><i class="fa fa-list"></i>{{trans('winsumos.productos')}}</a></li>
        <li class="active">{{trans('winsumos.ingreso_bodega_producto')}}</li>
      </ol>
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection