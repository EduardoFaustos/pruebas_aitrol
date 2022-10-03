
<div class="box-header with-border" style="background-color: #124574;color: white; font-size: 14px;">
  <form method="POST" id="form_contad_ordenes">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12" >
            <h1 style="font-size: 15px; margin:0;">
              <img style="width: 43px;" src="{{asset('/')}}hc4/img/ordenes_blanco.png">
              <b>ORDENES DE PROCEDIMIENTOS</b>
            </h1>
          </div>
          <div class="col-md-8 col-sm-6 col-12" style="padding-top: 16px">
            <div class="row">
              <div class="form-group col-md-3 col-xs-6" >
                <div class="row">
                  <label for="id_ani_me" class="col-md-3 control-label">Año/Mes</label>
                  <div class="col-7">
                    <select  style="width: 150px" class="form-control form-control-sm input-sm" name="id_anio_mes" id="id_anio_mes" onchange="buscador_anio_mes_ordenes()">
                      @php
                        $mes= ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                      @endphp
                      <option value="">Seleccione ...</option>
                      @foreach($anio_mes as $value)
                        <option  value="{{$value->anio}}-{{$value->mes}}">{{$value->anio}}-{{$mes[$value->mes-1]}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-2">
                <button type="button" onclick ="buscador_anio_mes_ordenes();" class="btn btn-danger" style="color:white; background-color: #124574; border-radius: 5px; border: 2px solid white;"> <i class="fa fa-search" aria-hidden="true">
                </i> &nbsp;BUSCAR&nbsp;</button>
              </div>
              <div class="col-4">
              </div>
              <!--<div class="col-3">
                <button type="button" onclick="#" id="visualiza_estad" class="btn btn-danger" style="color:white; background-color: #124574; border-radius: 5px; border: 2px solid white;"> Visualizar Estadistico</button>
              </div>-->
            </div>
          </div>
        </div>
  </form>
</div>
<div class="box-body" style="border: 2px solid #124574;padding-left: 0px;padding-right: 0px;" id="contador_ordenes">
    <div class="modal-body">
      <div class="panel-body">
        <div class="row">
            <div id="div_grafico_orden_proced" class="col-12 table-responsive"  style="min-height: 210px;">
              <table id="example2" class="table " role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                 <thead>
                  <tr>
                    <th class="color titulo" >DOCTOR (A)</th>
                    <th class="color titulo" style="text-align: center;">ENDOSCOPIAS DIGESTIVAS</th>
                    <th class="color titulo" style="text-align: center;">COLONOSCOPIA</th>
                    <th class="color titulo" style="text-align: center;">INTESTINO DELGADO</th>
                    <th class="color titulo" style="text-align: center;">ECOENDOSCOPIAS</th>
                    <th class="color titulo" style="text-align: center;">CPRE</th>
                    <th class="color titulo" style="text-align: center;">BRONCOSCOPIA</th>
                    <th class="color titulo" style="text-align: center;">FUNCIONALES</th>
                    <th class="color titulo" style="text-align: center;">IMAGENES</th>
                  </tr>
                </thead>
                @foreach ($array as $val)
                    @php
                      $user = Sis_medico\User::find($val['doctor']);
                    @endphp
                <tbody>
                    <tr role="row">
                      <td class="color">{{$user->nombre1}}{{$user->apellido1}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['1']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['2']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['3']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['9']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['10']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['14']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['18']}}</td>
                        <td class="color" style="text-align: center;">{{$val['proc']['20']}}</td>
                    </tr>
                </tbody>
                @endforeach
              </table>
            </div>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript">
//Buscador por anio mes ordenes de Procedimientos
    function buscador_anio_mes_ordenes(){
        //alert('id_anio_mes');
        $.ajax({
          type: 'post',
          url:"{{route('hc4_busqueda.anio_mes_ord')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_contad_ordenes").serialize(),
          success: function(data){
            console.log(data);
            $("#contador_ordenes").html(data);

          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }
</script>
