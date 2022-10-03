@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
        <h1 class="col-md-9">
          {{trans('ehistorialexam.PlanillasIess')}}
        </h1>
      @php

        $ruta = Cookie::get('ruta')

      @endphp
        
        <a type="button" href="{{ URL::previous() }}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> {{trans('ehistorialexam.Regresar')}}</span>
        </a>
      <!--ol class="breadcrumb">
        
        <li><a href="{{asset('/')}}" style="color: blue;"><i class="fa fa-home"></i> Inicio</a></li>
        <li><a href="{{route('agenda.agenda2')}}" style="color: blue;"></i> Agenda</a></li>
      </ol-->
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection