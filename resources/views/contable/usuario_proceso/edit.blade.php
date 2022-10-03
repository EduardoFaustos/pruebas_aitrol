@extends('contable.usuario_proceso.base')
@section('action-content')

<style type="text/css">
  .separator{
    width:100%;
    height:30px;
    clear: both;
  }
</style>

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="../ambiente">Mantenimiento Usuario Proceso</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>
    <form id="formulario" class="form-vertical" role="form" method="POST" action="">
      {{ csrf_field() }}
      <div class="box"> 
        <div class="box-header color_cab">
            <div class="col-md-9">
              <h5><b>EDITAR USUARIO PROCESO</b></h5>
            </div>
            
            <div class="col-md-1 text-right">
                  <a href=" {{ route('contable.compraspedidos.index') }} " class="btn btn-default btn-gray" >
                      <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                  </a>
              </div>
        </div> 
        <div class="separator"></div>
        <div class="box-body dobra">
            <!--Usuario Proceso-->
            <div class="form-group col-md-4">
                    <label for="usuario" class="col-md-3 control-label">{{trans('contableM.usuario')}}</label> 
                    <div class="col-md-9">
                        <select  id="usuario" name="id_usuario" class="form-control select2_cuentas" style="width: 100%;" required>
                            @foreach($usuarios as $value)
                              <option selected value="{{$value->id}}">{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}}</option>
                            @endforeach
                        </select>
                        <div id="result">
                        </div>
                    </div>
            </div> 
            <div class="form-group col-xs-6{{ $errors->has('id_paso') ? ' has-error' : '' }}">
                  <label for="id_paso" class="col-md-4 control-label">{{trans('contableM.tipo')}}</label>
                      <div class="col-md-7">
                          <select id="id_paso" name="id_paso[]" class="form-control"  multiple="multiple" required="required">
                          @foreach($paso_procesos as $p)
                            @php $selec = ""; @endphp
                            @foreach($paso as $value)
                              @if($p->id  == $value->id_paso)
                                @php $selec = "selected"; @endphp
                              @endif
                            @endforeach
                            <option {{$selec}} value="{{$p->id}}">{{$p->nombre}}</option>
                          @endforeach
                          </select>  
                             
                    </div>
           </div>
    
            <div class="form-group col-xs-10 text-center">
              <div class="col-md-6 col-md-offset-4">
                  <button type="submit" id="botonGuardar" onclick="guardar(event)" class="btn btn-default btn-gray btn_add">
                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.agregar')}}
                  </button>
              </div>
            </div>
        </div>
      </div>
    </form>
  </section>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
document.getElementById("result").style.visibility = "hidden";

$(function() {
        $(document).ready(function() {
            $('#id_paso').select2();
        });
    });

$(document).ready(function() {
    $('.select2_cuentas').select2({
        tags: false
    });


});

     function goBack() {
            var url = '{{route("compraspedidos.index_proceso")}}';
            window.location = url;
            }

    function guardar() {
        $id_paso = $("#id_paso").val();
        $usuario = $("#usuario").val();

        if ($id_paso == "" || $usuario == "") {
            swal("Error!", "Campos Vacios", "error");
        } else {
            document.getElementById("botonGuardar").disabled = true;
            $.ajax({
                url: "{{route('compraspedidos.actualizar')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('compraspedidos.index_proceso')}}"
                    if (data.respuesta == 'si') {
                        swal("Guardado!", "Correcto", "success");
                        setTimeout(()=>{
                          window.location = url;
                        }, 1500)
                    }
                },error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    }
</script>
@endsection