@if(isset($_SERVER['SERVER_NAME']))
    @if($_SERVER['SERVER_NAME'] == '186.70.157.2')

        <!DOCTYPE html>
        <html>

        @include('layouts.partials.htmlheader')
        <body>
            <div id="app" v-cloak>
                <!-- Main content -->
                <section class="content">
                    <!-- Your Page Content Here -->
                    <div class="error-page">
                        <h2 class="headline text-yellow"> 404</h2>
                        <div class="error-content">
                            <h3><i class="fa fa-warning text-yellow"></i> Oops! Página no encontrada.</h3>
                            <p>
                                SE HA REDIRECCIONADO A UN NUEVO DOMINIO dar clic en el enlace de abajo.<br>
                                <a style="font-size: 40px;" href='{{ url("https://ieced.siaam.ec") }}'>ieced.siaam.ec</a>
                            </p>
                        </div><!-- /.error-content -->
                    </div><!-- /.error-page -->
                </section>
            </div>
            @section('scripts')
                @include('layouts.partials.scripts')
            @show
        </body>
        </html>
        @php
            exit();

        @endphp
    @elseif($_SERVER['SERVER_NAME'] == 'ieced.siaam.ec' && !isset($_SERVER['HTTPS']))
        @php
            header("Status: 301 Moved Permanently");
            header("Location: https://ieced.siaam.ec");
            exit;
        @endphp
    @endif
@endif

@extends('layouts.app')
@section('content')
@php

 $ingreso =1;
 if(isset($_SERVER['SERVER_NAME'])){
     if($_SERVER['SERVER_NAME'] == 'labs.siaam.ec'){
         $ingreso = 0;
     }
 }


@endphp
<script type="text/javascript">
    console.log('{{$_SERVER['SERVER_NAME']}}');
</script>

<div>
    <!--opcional-->
    <br>
</div>

<div class="container">
    <div class="row" >
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default ">
                <div class="panel-heading" align="center">
                    <img style="width:75%; margin-top: 20px;" src="{{asset('/imagenes')}}/nuevo_logo.png" >
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                    {!! csrf_field() !!}
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }} {{ $errors->has('0') ? ' has-error' : '' }}">
                            <div class="col-md-8 col-md-offset-2">
                                <input placeholder="Email ID" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required >
                                @if ($errors->has('0'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('1') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div  class="col-md-8 col-md-offset-2">
                                <input id="password" placeholder="Contraseña" type="password" class="form-control" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="checkbox btn btn-link">
                                    <label >
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme
                                    </label>
                                    <a class="btn btn-link" style="margin-left: 15px;" href="{{ route('password.request') }}">
                                         Olvidó su Contraseña?
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button type="submit" class="btn btn-primary col-md-10" style="background-color: #097ff5;" >
                                    Iniciar Sesion
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
