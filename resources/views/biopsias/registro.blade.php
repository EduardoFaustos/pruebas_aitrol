@extends('biopsias.base')

@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                
                <div class="box-body">

                    <form class="form-vertical" role="form" method="POST" enctype="multipart/form-data" action="{{route('biopsias.registroguardar')}}">
                        {{ csrf_field() }}
                    
                        <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Registro')}}:</div>
                  <div class="col-md-8"><input class = "form-control" type="text" name="campo_registro" required maxlength="13" style="margin-top: 20px;"></div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.NombrePaciente')}}:</div>
                  <div class="col-md-8"><input class ="form-control" type="text" name="nombre_paciente" readonly value="{{$paciente->nombre1}} {{$paciente->apellido1}}" required maxlength="30" style="margin-top: 20px;">
                    <input class ="form-control" type="hidden" name="id_hc_biopsia" required value="{{$id}}">
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row"> 
                  <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.MédicoSolicitante')}}:</div>
                  <div class="col-md-8"><input class = "form-control" type="text" name="md_solicitante" readonly value="{{$doctor[0]->nombre1}} {{$doctor[0]->apellido1}}"  style="margin-top: 25px"></div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <?php  
                  //$today = date("Y-m-d");
                  $today = date("Y");
                  $f1 = new DateTime($today);
                  $f2 = new DateTime($paciente->fecha_nacimiento);
              
                  $edad = $f1->diff($f2);
                  //$edad1 = (string)$edad;
                  //echo strval($edad);
                  //dd($edad);
                  ?>

                  @php
                    $edad= 0;
                    if ($paciente->fecha_nacimiento != null) {
                        $edad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                    } 
                  @endphp

                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Edad')}}:</div>
                  <div class="col-md-8"><input class = "form-control" type="number" name="edad"  value="{{ $edad }}" style="margin-top: 20px;" readonly ></div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Obtenido')}}</div>
                  <div class="col-md-8" style="margin-top: 20px;">
                     <div class="form-group">

                      
                       <div class='col-sm-8'>
                        <input type='text' name="obtenido" class="form-control" id='datetimepicker6' />
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#datetimepicker6').datetimepicker({
                                format: 'YYYY-MM-DD HH:mm:ss'
                              });
                        });
                    </script>

                  </div>
                    
                </div>
              </div>
            </div>
             
                
                <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.Recibido')}}:</div>
                  <div class="col-md-8" style="margin-top: 25px;">
                     <div class="form-group">

                       <div class='col-sm-8'>
                        <input type='text' name="recibido" class="form-control" id='datetimepicker8' />
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#datetimepicker8').datetimepicker({
                                format: 'YYYY-MM-DD HH:mm:ss'
                              });
                        });
                    </script>

                  </div>
                    
                </div>
              </div>
            </div>
              
               <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.Reportado')}}:</div>
                  <div class="col-md-8" style="margin-top: 25px;">
                     <div class="form-group">

                       <div class='col-sm-8'>
                        <input type='text' name="reportado" class="form-control" id='datetimepicker9' />
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#datetimepicker9').datetimepicker({
                                format: 'YYYY-MM-DD HH:mm:ss'
                              });
                        });
                    </script>

                  </div>
                    
                </div>
              </div>
            </div>

              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.DatosOrientaciónDiagnóstica')}}:</div>
                  <div class="col-md-8">
                    <textarea class="form-control" name="Ori_diagnostica" rows="4" style="margin-top: 20px;"></textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row"> 
                  <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.Macroscopia')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="macroscopia" rows="4" style="margin-top: 25px"></textarea>
                  </div>
                </div>
              </div>
               <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Microscopia')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="microscopia" rows="4" style="margin-top: 20px"></textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                    <div class="row"> 
                    <div class="col-md-4" style="padding-top: 10px">trans{{('biopsias.Imagen')}} #1:</div>
                      <div class="col-md-8">                    
                      <input type="hidden" name="img1" value="">    
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                           <input name="img1" id="img1" type="file"   class="archivo form-control"  required>
                                @if ($errors->has('archivo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('archivo') }}</strong>
                                        </span>
                                    @endif
                            
                        </div>
                   

                    </div>

          </div>
          <div class="col-md-8">
                    <div class="row"> 
                    <div class="col-md-4" style="padding-top: 10px">trans{{('biopsias.Imagen')}} #2:</div>
                      <div class="col-md-8">                    
                      <input type="hidden" name="img2" value="">    
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                           <input name="img2" id="img2" type="file"   class="archivo form-control"  >
                                @if ($errors->has('archivo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('archivo') }}</strong>
                                        </span>
                                    @endif
                            
                        </div>
                   

                    </div>

          </div>
              
               <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Diagnóstico')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="diagnostico" rows="4" style="margin-top: 20px"></textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Observación')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="observacion" rows="4" style="margin-top: 20px"></textarea>
                  </div>
                </div>
              </div>
            
                
                       
                     

                    
              <br>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> trans{{('biopsias.Guardar')}}
                                </button>
                            </div>
                        </div>
                    </form>
                   
                </div>
            
            </div>
        </div>
    </div>
    
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>




<script type="text/javascript">

    $(document).ready(function() {
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i> Examen</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    

</script>
@endsection
