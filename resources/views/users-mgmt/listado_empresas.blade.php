@extends('users-mgmt.base')
@section('action-content')
<div class="container-fluid">
    <div class="row">
        <!--left-->
        <div class="col-sm-7">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('adminusuarios.editar')}}</h3>
                </div>
                <form class="form-vertical" role="form" method="POST" action="">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group col-xs-6{{ $errors->has('empresas') ? ' has-error' : '' }}">
                            <label for="empresas" class="col-xs-8 control-label">Empresa</label>
                            @foreach ($empresas as $empresa)
                            <checkbox value="{{$empresa->id}}">{{$empresa->razonsocial}} </checkbox>
                            @endforeach
                            @if ($errors->has('empresas'))
                            <span class="help-block">
                                <strong>{{ $errors->first('empresas') }}</strong>
                            </span>
                            @endif
                        </div>


                    </div>

                


                </form>
             </div>
    </div>
                