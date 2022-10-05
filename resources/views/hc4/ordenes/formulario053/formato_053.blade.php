<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<style type="text/css">
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
        font-size: 10px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow: ellipsis;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }
    table {
          border-collapse: collapse;
        } 
</style>
@php 

$rd = rand(); 

@


<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
        
        <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
            <div class="row">
                <div class="col-8" style="font-size: 15px;"> 
                    <span style="margin-left: 10px;" >Formulario 013</span>
                </div>
            </div>
            <a class="btn btn-success btn-sm" href="" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Descargar 013</a>
        </div>
        <form id="frm_evol">
            <div class="box-body" style="font-size: 13px;font-family: 'Helvetica general3';">
                <div class="col-md-12" style="padding: 1px;">
                    <div class="form-row">
                            <label for="datos_ins" class="control-label">1. Datos Institucionales:</label>
                        <div class="form-group col-md-4">  
                            <label>Distrito/Area</label>
                            <input class="form-control input-sm" type="text" name="distrito" id="distrito" required="required"  >
                        </div>

                        <div class="form-group col-md-4">  
                            <label>Refiere o deriva a</label>
                            <input class="form-control input-sm" type="text" name="refiere" id="refiere" required="required"  >
                        </div>


                        <div class="form-group col-md-4">
                            <label> Establecimiento de Salud</label>
                            <input class="form-control input-sm" type="text" name="establecimiento" id="establecimiento" required="required"  >
                        </div>

                        <div class="form-group col-md-4">
                            <label> Servicio </label>
                            <input class="form-control input-sm" type="text" name="servicio" id="servicio" required="required"  >
                        </div>

                        <div class="form-group col-md-4">
                            <label> Especialidad </label>
                            <input class="form-control input-sm" type="text" name="especialidad" id="especialidad" required="required"  >
                        </div>


                        <div class="form-group col-md-4">
                            <label>Fecha Orden</label>
                        
                            <div class="form-group">
                                <div class="input-group date" id="fecha" data-target-input="nearest">
                                    <div class="input-group-append" data-target="#fecha" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="fecha_orden" id="fecha_orden" class="form-control input-sm datetimepicker-input" data-target="#fecha">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label for="datos_ins" class="control-label">2. Motivo de la Referencia o Derivación:</label>
                            <div class="col-md-12" style="padding: 1px;">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="limitada_capacidad" id="limitada_capacidad" required="required" >Limitada capacidad resolutiva    
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="ausencia_temporal" id="ausencia_temporal" required="required" > Ausencia temporal del profesional     
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="falta_profesional" id="falta_profesional">Falta de profesional
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="saturacion_capacidad" id="saturacion_capacidad" required="required" >Saturación de capacidad instalada     
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="otros" id="otros" required="required" >Otros Especifique:
                                        <input class="form-control input-sm" type="text" name="texto_otros" id="texto_otros" required="required"  >    
                                    </div>
                                   
                                </div>
                                
                            </div>
                        
                            <div class="col-md-12" style="padding: 1px;">
                                <div class="form-row">

                                    <div class="form-group col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">3. Resumen Clínico:</span>
                                        <div id="thistoria_clinica"  style="width: 80%; border: 2px solid #004AC1;">
                                            <?php echo "prueba" ?>
                                        </div>
                                        <input type="hidden" name="historia_clinica" id="historia_clinica">
                                    </div>
                                    
                                </div>
                            </div>
                                        
                        <label class="col-md-9 control-label" style="padding-left: 0px;"><b>4. Diagnósticos</b></label>
                            <div class="form-row">
                                <div class="form-group col-md-9" style="padding: 1px;">
                                    <input id="cie10_012" type="text" class="form-control input-sm ui-autocomplete-input" style="text-transform:uppercase;" placeholder="Diagnóstico">
                                </div>

                                <div class="form-group col-md-2" style="padding: 1px;">
                                    <select id="pre_def" name="pre_def" class="form-control input-sm" required="">
                                        <option value="">Seleccione ...</option>
                                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                                        <option value="DEFINITIVO">DEFINITIVO</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-1" style="padding: 1px;">
                                    <button id="bagregar_012" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span></button>
                                </div>    
                            </div>

                            <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">
                                    @foreach($form_053_cie10 as $val_cie10)
                                        <tr id="tdiag">
                                            <td><b>{{$val_cie10->cie10}}</b></td>
                                            @php 
                                                $c10 = Sis_medico\Cie_10_3::find($val_cie10->cie10);
                                                if(is_null($c10)){
                                                    $c10 = Sis_medico\Cie_10_4::find($val_cie10->cie10);
                                                }
                                            @endphp
                                            <td>{{$val_cie10->presuntivo_definitivo}}</td>
                                            <td>{{$c10->descripcion}}</td>
                                            <td><a href="" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" ></span></a></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                    </div>
                </div>
            </div>    
        </form>
    </div>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>


<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>

<script type="text/javascript">    
    $(function () {
        $('#fecha').datetimepicker({
            format: 'DD/MM/YYYY hh:mm',
        });
    });

    $("#fecha").on("dp.change", function (e) {
       guardar();
    });

    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
    }); 

</script>