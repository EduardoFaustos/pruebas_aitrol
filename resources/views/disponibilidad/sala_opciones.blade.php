<!-- split buttons box -->
@extends('disponibilidad.base')
@section('action-content')

<style>
    .btn{
        font-size: 15px;
        font-weight: bold;
    }
    
    }
</style>

 <section class="content">
  <div class="box">
    <div class="box-header">  

        <h4><b>{{$hospital->nombre_hospital}}</b></h4>
      <div class="col-md-12" style="text-align: right;">
                            <a type="button" href="{{route('disponibilidad.disponibilidad_menu')}}" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-arrow-left">{{trans('edisponibilidad.Regresar')}}</span>
                            </a>
                         </div>
                         
      </div>
       <div class="box-body">
           <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <table id="example2" class="table table-bordered table-hover dataTable" >
              <tbody>
               @foreach($sala as $sala)
                  <div class="col-md-4" style="padding: 5px;">
                      <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                          <a class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center" onClick="agenda_sala({{$sala->id}});">{{$sala->nombre_sala}}
                          </a>
                      </div>
                  </div>
              @endforeach
                <div class="col-md-12" id="agenda"></div>
              </tbody>

            </table>
             <div class="box-header"> 
            <form id="fecha_enviar">
              <div class="form-group col-md-12" >
                  <label class="col-md-1 control-label">{{trans('edisponibilidad.Fecha')}}</label>
                  <div class="col-md-6">
                      <div class="input-group date">
                          <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" value="{{$fecha}}" name="fecha" class="form-control" id="fecha" onchange="fechacalendario_todas();"  required value="{{$fecha}}">
                      </div>
                      @if ($errors->has('fecha'))
                      <span class="help-block">
                          <strong>{{ $errors->first('fecha') }}</strong>
                      </span>
                      @endif
                  </div>
              </div>
              
            </form>
          </div>   


     
          <div class="col-md-12" id="agendas_todas"></div>
        </div>
      </div>
        
  </div>
   
  <!-- /.box-body -->
    </section>
    

    <!-- /.content -->
    

 <link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
  <script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>
  <script type="text/javascript">
    $(function () {
        //agenda_salas_todas();
        $('#fecha').datetimepicker({
          format: 'YYYY/MM/DD',
          //minDate: '{{date("Y/m/d")}}',
          defaultDate: '{{$fecha}}'
        });

         fechacalendario_todas('{{$rsala}}'); 
       
      
        
        $("#fecha").on("dp.change", function (e) {
            //alert("cambio");
            fechacalendario_todas('{{$rsala}}');
        });
    });
  </script> 

  <script type="text/javascript">
         function agenda_sala(id){
          location.href = "{{url('disponibilidad/sala_opciones')}}/{{$id}}/"+id;
            /*console.log(id);
            $.ajax({
        
            type: "GET",
            url: "{{ url('disponibilidad/sala_agenda')}}/"+id,
            datatype: "html",
            success: function(datahtml){

                $("#agenda").html(datahtml);

            },
            error:  function(){
              alert('error al cargar');
            }
          });*/
        }

        function agenda_salas_todas(){
          //alert("ok");
          
            $.ajax({
        
            type: "get",
            url: "{{ route('disponibilidad.salas_todas',['id_hospital'=> $hospital->id])}}",
            datatype: "html",
            success: function(datahtml){
                  console.log(datahtml);
                $("#agendas_todas").html(datahtml);

            },
            error:  function(){
              alert('error al cargar');
            }
          });
        }
        function fechacalendario_todas(id){
         //alert("hola");
      @if($rsala=='T')
          //alert("hola");
      $.ajax({
        type: 'post',
        url:"{{ route('disponibilidad.salas_todas',['id_hospital'=> $hospital->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#fecha_enviar").serialize(),
        success: function(data){
          $('#agendas_todas').empty().html(data);
        },
        error: function(data){
          //console.log(data);
        }
      }); 
      @else
       //alert(id);
      $.ajax({
    
          type: "post",
          url: "{{ url('disponibilidad/sala_agenda')}}/"+id,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: "json",
          data: $("#fecha_enviar").serialize(),
          success: function(datahtml){

              $("#agendas_todas").empty().html(datahtml);

          },
          error:  function(){
            alert('error al cargar');
          }
        }); 
      @endif    
    } 
         
    </script> 

@endsection

