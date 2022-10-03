@extends('dashboard.base')
@section('action-content')

<style>
    .btn{
        font-size: 15px;
        font-weight: bold;
    }
    
    }
</style>
  <div class="content">
     <div class="col-lg-12 col-md-12" style="background: #004AC1">
       <div class="box-body">
        <!-- Split button -->

        <div class="margin">
            @foreach($modulos as $modulo)
             <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a class="btn" href="{{ route('ieced.submodulos',['id' => $modulo->id])}}" role="button" style="width: 100%; height: 40%; line-height: 40px; font-size: 20px; text-align: center;  border-radius: 20px; background-image: linear-gradient(to right,#FFFFFF,#FFFFFF,#d1d1d1); color: white;">
                        <img style="width:40%;height:100%; text-align: left" src="{{asset('/imagenes/modulos/')}}/{{$modulo->imagen}}">
                        <div  style="text-align: center"> 
                            {{$modulo->nombre}}
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        </div>  
    </div>
</div>
@endsection

<!-- end split buttons box -->