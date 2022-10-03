@extends('hc_admision/biopsias/base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
  /* unvisited link */
  a:link {
    color: black;
  }

  /* visited link */
  a:visited {
    color: lightgreen;
  }

  /* mouse over link */
  a:hover {
    color: blue;
  }

  button {
    width: 100%;
  }
</style>

<section class="content">
  <div class="box">
    <div class="box-header">
      <form id="formulario_biopsias" action="{{route('muestrabiopsias.index')}}" method="POST">
        {{ csrf_field() }}

        <div class="col-md-12">
          <div class="form-group col-md-3">
            <label for="cedula" class="control-label"> {{trans('tecnicof.patientidentificationcard')}}</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Numero de Cedula" onchange="fecha_buscador()">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = '';"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label for="examen_aqui" class="col-md-5"> {{trans('tecnicof.testsperformedhere')}}</label>
            <div class="col-md-5">
              <select class="form-control" id="examen_aqui" name="examen_aqui">
                <option value=""> {{trans('tecnicof.select')}} </option>
                <option @if ($examen_aqui==0) selected @endif value="0"> No </option>
                <option @if ($examen_aqui==1) selected @endif value="1"> {{trans('tecnicof.yes')}} </option>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label for="pacientes_omni" class="col-md-5"> {{trans('tecnicof.patiet')}} Omni </label>
            <div class="col-md-5">
              <select class="form-control" id="pacientes_omni" name="pacientes_omni">
                <option value=""> {{trans('tecnicof.select')}} </option>
                <option @if ($pacientes_omni==0) selected @endif value="0"> No </option>
                <option @if ($pacientes_omni==1) selected @endif value="1"> {{trans('tecnicof.yes')}} </option>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label for="tipo_seguro" class="col-md-5 control-label"> {{trans('tecnicof.typeofinsurance')}}</label>
            <div class="col-md-7">
              <select class="form-control" id="tipo_seguro" name="tipo_seguro">
                <option value=""> {{trans('tecnicof.select')}} </option>
                <option @if ($tipo_seguro==1) selected @endif value="1"> {{trans('tecnicof.private')}} </option>
                <option @if ($tipo_seguro==0) selected @endif value="0"> {{trans('tecnicof.publico')}} </option>
              </select>
            </div>
          </div>

        </div>

        <div class="col-md-12">
          <div class="form-group col-md-3">
            <label class="col-md-2 control-label">{{trans('tecnicof.from')}}</label>
            <div class="col-md-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" value="" name="fecha" class="form-control" id="fecha" required>
              </div>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label class="col-md-2 control-label">{{trans('tecnicof.to')}} </label>
            <div class="col-md-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" value="" name="fechafin" class="form-control" id="fechafin" required>
              </div>
            </div>
          </div>

          <div class="form-group col-md-2">
            <button type="submit" class="col-md-7 btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>{{trans('tecnicof.search')}}</button>
          </div>

          <div class="form-group col-md-2">
            <button type="submit" class="btn btn-success btn-sm" formaction="{{route('muestrabiopsias.reporte_muestras_biopsias')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('tecnicof.samplesreport')}} </button>
          </div>

          <div class="form-group col-md-2">
            <button type="submit" class="btn btn-success btn-sm" formaction="{{route('muestrabiopsias.pdf_muestras_biopsias')}}"><span class="fa fa-file-pdf-o" aria-hidden="true"></span> {{trans('tecnicof.pdfbiopsy')}} </button>
          </div>

        </div>

        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr>
                    <th>{{trans('tecnicof.patientsname')}}</th>
                    <th>{{trans('tecnicof.id')}}</th>
                    <th>{{trans('tecnicof.medicalhistory')}} </th>
                    <th>{{trans('tecnicof.patiet')}} Omni </th>
                    <th>{{trans('tecnicof.typeofinsurance')}}</th>
                    <th>{{trans('tecnicof.description')}} </th>
                    <th>{{trans('tecnicof.bottles')}}</th>
                    <th>{{trans('tecnicof.biopsies')}}</th>
                    <th>{{trans('tecnicof.testsperformedhere')}}</th>


                  </tr>
                </thead>
                <tbody>
                  @if(count($biopsias) > 0)
                  @foreach ($biopsias as $biopsia)
                  <tr>
                    <td>@if(isset($biopsia->pacientes)){{$biopsia->pacientes->nombre1}} {{$biopsia->pacientes->nombre2}} {{$biopsia->pacientes->apellido1}} {{$biopsia->pacientes->apellido2}} @endif</td>
                    <td>{{$biopsia->id_paciente}}</td>
                    <td>{{$biopsia->paciente_omni->hcid}} {{$biopsia->paciente_omni->id_agenda}} </td>
                    <td> {{$biopsia->paciente_omni2->id}} {{$biopsia->paciente_omni2->omni}}</td>
                    <td>{{$biopsia->seguros->nombre}} {{$biopsia->seguros->tipo}}</td>
                    <td>{{$biopsia->descripcion_frasco}}</td>
                    <td>{{$biopsia->numero_frasco}} </td>
                    <td> <a class="btn btn-success" target="_blank" href="{{route('paciente.historial_orden_biopsias',['id'=>$biopsia->id_paciente])}}"><i class="glyphicon glyphicon-copy" aria-hidden="true"></i></a> </td>
                    <td>
                      <select class="form-control " id="biopsias_biopsia" onchange="actualizar_estadosm(this, {{$biopsia->id}})">
                        <option @if ($biopsia->muestra_biopsia == 0) selected @endif value="0"> No </option>
                        <option @if ($biopsia->muestra_biopsia == 1) selected @endif value="1"> {{trans('tecnicof.yes')}} </option>
                      </select>

                  </tr>
                  @endforeach
                  @else
                  <tr>
                    <td colspan="9">
                      No hay datos
                    </td>
                  </tr>
                  @endif

                </tbody>

              </table>
              {{$biopsias->appends(Request::only(['fechafin','fecha']))->links()}}
            </div>

          </div>
        </div>
    </div>
  </div>
  </form>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset("/plugins/datetimepicker/bootstrap-material-datetimepicker.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}'

    });
    $("#fecha").on("dp.change", function(e) {
      $('#fechafin').data("DateTimePicker").minDate(e.date);
      fecha_buscador();
      //alert('entra:inicio');
    });

    $('#fechafin').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fechafin}}'
    });
    $("#fechafin").on("dp.change", function(e) {
      //alert('entra:fin');
      fecha_buscador();
    });

  });
</script>
<script type="text/javascript">
  function fecha_buscador() {
    //alert('entra');
    $('#formulario_biopsias').submit();
  }
</script>
<script type="text/javascript">
  function actualizar_estadosm(e, id) {
    console.log(e.value)
    console.log(id)
    Swal.fire({
      title: 'Desea actualizar la información',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Aceptar`,
      denyButtonText: `No`,

    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'post',
          url: "{{route('muestrabiopsias.update')}}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {
            "estado": e.value,
            "id": id,
          },
          success: function(data) {
            console.log(data);
            //alert(data.msj);
            if (data.status == "success") {
              Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Actualizado',
                showConfirmButton: false,
                timer: 1500
              })
              location.reload();
            }
          }
        });
      } else if (result.isDenied) {
        Swal.fire('Changes are not saved', '', 'info')
      }
      location.reload();
    })
  }
</script>


@endsection