@extends('biopsias.base')

@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                
                <div class="box-body">

                    <form class="form-vertical" role="form" method="POST" enctype="multipart/form-data" action="{{route('biopsias.editresultado', ['id' => $biopsia_resultado_id->id_hc_biopsia])}}">
                        {{ csrf_field() }}
                    
                        <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Registro')}}:</div>
                  <div class="col-md-8"><input class = "form-control" type="text" name="campo_registro" value='{{ $biopsia_resultado_id->campo_registro}}' required maxlength="13" style="margin-top: 20px;"></div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.NombrePaciente')}}:</div>
                  <div class="col-md-8"><input class ="form-control" type="text" name="nombre_paciente" readonly value="{{ $biopsia_resultado_id->nombre_paciente}}" required maxlength="30" style="margin-top: 20px;">
                    <input class ="form-control" type="hidden" name="id_hc_biopsia" required value="">
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row"> 
                  <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.MédicoSolicitante')}}:</div>
                  <div class="col-md-8"><input class = "form-control" type="text" name="md_solicitante" readonly value="{{ $biopsia_resultado_id->md_solicitante}}"  style="margin-top: 25px"></div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Edad')}}:</div>
                  <div class="col-md-8"><input class = "form-control" type="number" name="edad"  readonly value="{{ $biopsia_resultado_id->edad}}" style="margin-top: 20px;"></div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Obtenido')}}</div>
                  <div class="col-md-8" style="margin-top: 20px;">
                     <div class="form-group">

                      
                       <div class='col-sm-8'>
                        <input type='text' name="obtenido" class="form-control" value="{{ $biopsia_resultado_id->obtenido}}" id='datetimepicker6' />
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
                        <input type='text' name="recibido" class="form-control" value="{{ $biopsia_resultado_id->recibido}}" id='datetimepicker8' />
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
                        <input type='text' name="reportado" class="form-control" value="{{ $biopsia_resultado_id->reportado}}" id='datetimepicker9' />
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
                    <textarea class="form-control" name="Ori_diagnostica" rows="4"  style="margin-top: 20px;">{{ $biopsia_resultado_id->Ori_diagnostica}}</textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row"> 
                  <div class="col-md-4" style="padding-top: 25px">trans{{('biopsias.Macroscopia')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="macroscopia" rows="4"  style="margin-top: 25px">{{ $biopsia_resultado_id->macroscopia}}</textarea>
                  </div>
                </div>
              </div>
               <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Microscopia')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="microscopia" rows="4"  style="margin-top: 20px">{{ $biopsia_resultado_id->microscopia}}</textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                    <div class="row"> 
                    <div class="col-md-4" style="padding-top: 10px">trans{{('biopsias.Imagen')}} #1:</div>
                      <div class="col-md-8">                    
                      <img src="{{asset('../storage/app/biopsias').'/'.$biopsia_resultado_id->img1}}" style="width:300px;height:300px;"  alt="" > 
                            
                        </div>
                   

                    </div>

          </div>
          <div class="col-md-8">
                    <div class="row"> 
                    <div class="col-md-4" style="padding-top: 10px">trans{{('biopsias.Imagen')}} #2:</div>
                      <div class="col-md-8">                    
                      <img src="{{asset('../storage/app/biopsias').'/'.$biopsia_resultado_id->img2}}" style="width:300px;height:300px;"  alt="" >
                            
                        </div>
                   

                    </div>

          </div>
              
               <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Diagnóstico')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="diagnostico" rows="4"  style="margin-top: 20px">{{ $biopsia_resultado_id->diagnostico}}</textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4" style="padding-top: 20px">trans{{('biopsias.Observación')}}:</div>
                  <div class="col-md-8">
                     <textarea class="form-control" name="observacion" rows="4"  style="margin-top: 20px">{{ $biopsia_resultado_id->diagnostico}}</textarea>
                  </div>
                </div>
              </div>
            
                
                       
                     

                    
              <br>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> trans{{('biopsias.EditarGuardar')}}
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
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i>Examen</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    

</script>
@endsection
