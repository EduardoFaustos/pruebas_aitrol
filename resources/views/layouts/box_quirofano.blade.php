<style>
    .recuadro {
        height: 200px;
        width: 100px;
    }

    .sradio {
        border: 1px solid white;
        border-radius: 20px;
        font-size: 20px;
        width: 40px;
        text-align: center;
        color: white;
    }

    .colorbasic {
        color: white !important;
    }

    b {
        padding-top: 11px !important;
    }

    .burbuja {
        padding-left: 4px;
        height: 200px;
    }

    .card-title {
        font-size: 14px !important;
    }

    .card-header {

        max-height: 100px;
    }
</style>

<div class="col-md-12">
    <div class="row">
    @if($tipo == 1)
        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Cirugia')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="ordenes_funcionales();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b>{{trans('transquirofano.Fecha')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    <div class="col-md-12">
                        <b>{{trans('transquirofano.MédicoExaminador')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$pro_final->apellido1}} {{$pro_final->nombre1}}</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        @php
                            $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $pro_final->id_procedimiento)->get();
                                $mas = true;
                                $texto = "";
                                foreach($adicionales as $value2)
                                {
                                    if($mas == true){
                                     $texto = $texto.$value2->procedimiento->nombre  ;
                                     $mas = false;
                                     }
                                    else{
                                     $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                                     }
                                }
                        @endphp
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>
    @endif

    @if($tipo == 0)
    @php
        $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $pro_final->id_procedimiento)->get();
            $mas = true;
            $texto = "";
            foreach($adicionales as $value2)
            {
                if($mas == true){
                 $texto = $texto.$value2->procedimiento->nombre  ;
                 $mas = false;
                 }
                else{
                 $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                 }
            }
    @endphp

        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('Imagenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="ecografia();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.MédicoExaminador')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$pro_final->apellido1}} {{$pro_final->nombre1}}</span>
                    </div>
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>
    @endif

        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Evolución')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="evolucion();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!is_null($evolucion)){{$evolucion->created_at}}@endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Motivo')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!is_null($evolucion)){{$evolucion->motivo}}@endif</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('Detalle')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>@if(!is_null($evolucion)){{$evolucion->cuadro_clinico}}@endif</span>
                    </div>
                    

                </div>
            </div>
        </div>
        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Medicamentos')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="medicamentos();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    @php 
                        $receta = $historia->recetas->last();
                    @endphp
                    <br>
                    <div class="col-md-12">
                        <b>{{trans('transquirofano.Fecha')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Medicamentos')}}</b>
                    </div>
                    <div class="col-md-12">
                       
                    </div>

                </div>
            </div>
        </div>

        

        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.ArmarEstudios')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="armar_estudio();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Fecha')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    
                    <div class="col-md-12">
                        <b> {{trans('transquirofano.Procedimiento')}}  </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.Pre-VisualizarEstudios')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="ver_estudio();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-80">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <span class="sradio">6</span>
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('transquirofano.ResultadodeExamenes')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" onclick="mostrarExamenes()"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="height: 250px;">
                    <br> 
                    @php
                        $examenes= \Sis_medico\Ho_Solicitud::where('id',$solicitud->id)->first();
                        $examen= \Sis_medico\Examen_Orden::where('id_paciente',$examenes->id_paciente)->orderBy('created_at', 'DESC')->limit(1)->get();
                    @endphp
                    <div class="row">
                        
                        <div class="col-md-12" >
                            <table id="example2" class="table " >
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Fecha</th>
                                        <th style="text-align: center;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($examen as $value)
                                    <tr>
                                        <td>@if($value == null)  @else {{$value->created_at}} @endif</td>
                                        <td> <a  class="btn btn-primary btn-xs"  href="{{route('hospitalizacion.imprimir',['id' => $value->id])}}" target="_blank">{{trans('hospitalizacion.DescargarExamen')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                    </div>    
                </div>
                <!---->
                <!---->
            </div>
        </div>
        <div class="col-md-6" >
            <div class="card h-80" id="ingreso">
                <div class="card-header bg bg-primary colorbasic">
                    <div class="d-flex align-items-center"> <!--span class="sradio"></span-->
                        &nbsp;
                        <h4 class="card-title ml-10 colorbasic">
                            {{trans('Epicrisis')}}
                        </h4>
                    </div>
                    <div class="col-md-2 align-items-right">
                        <button class="btn btn-primary btn-xs" type="button" id="btn_evolucion" onclick="epicrisis();"> <i class="fa fa-plus"></i> </button>
                    </div>
                </div>
                <div class="card-body" style="min-height: 250px;">
                    <br>
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.Fecha')}}</b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$historia->created_at}}</span>
                    </div>
                    
                    <div class="col-md-12">
                        <b>  {{trans('transquirofano.Procedimiento')}} </b>
                    </div>
                    <div class="col-md-12">
                        <span>{{$texto}}</span>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
    });
    function mostrarExamenes(){
        $.ajax({
            type: "get",
            url: "{{route('hospitalizacion.cargar_examenes',['id' => $solicitud->id])}}",
            datatype: "html",
            success: function(datahtml, data) {
                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }
    function evolucion() {
        //var id_orden = "1";
        //console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_evolucion',['id' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }


    function medicamentos(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('formulario005.f5_medicamentos',['id' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }


    function ordenes_funcionales(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.index_funcionales',['id_solicitud' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function armar_estudio(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.armar_estudio',['id_solicitud' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function ver_estudio(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.ver_estudio',['id_solicitud' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    function ecografia(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.ecografia',['id_solicitud' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }
    function epicrisis(){
        //var id_orden = "1";
        console.log("aqui");
        $.ajax({
            type: "get",
            url: "{{route('quirofano.epicrisis',['id_solicitud' => $solicitud->id])}}",
            data: {
               // 'ep': id_orden,
            },
            datatype: "html",
            success: function(datahtml, data) {

                $("#quirofano").html(datahtml);
            },
            error: function() {
                alert('error al cargar');
            }
        });
    }

    
</script>