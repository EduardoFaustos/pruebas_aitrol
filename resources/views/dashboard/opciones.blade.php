<!-- split buttons box -->
@extends('dashboard.base')
@section('action-content')

<style>
    .btn{
        font-size: 15px;
        font-weight: bold;
    }
</style>
<div class="content">
    <div class="box-body">
        <!-- Split button -->

        <div class="margin">
            @foreach($opciones as $value)
                @if(Route::has($value->ruta)) 
                <div class="col-md-4" style="padding: 5px;">
                    <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                        <a href="{{ route($value->ruta)}}" class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center">{{$value->nombre}}
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
     <!-- /.box-body -->
    </div>
</div>
@endsection


<!-- end split buttons box proveedor.index' -->