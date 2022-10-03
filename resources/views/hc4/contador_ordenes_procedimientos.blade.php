<style type="text/css">

   .table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
  } 
  
  .dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }

  .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px white !important;
  }

  .boton-2{
    font-size: 10px ;
    width: 100%;
    height: 100%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
  }

  .color{
    font-size: 12px; 
    color: #004AC1; 
  }

</style>

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








 


