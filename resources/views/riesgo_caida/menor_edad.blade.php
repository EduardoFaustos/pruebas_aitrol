@extends('riesgo_caida.base')
@section('action-content')
@php
$data = date("Y-m-d");
@endphp
<style>
    * {
        font-size: 15px;

    }

    .table {
        border-collapse: collapse;
        padding: -1px;

    }

    .table,
    .td {
        border: 1px solid black;
        text-align: center;

    }

    .th {
        text-align: center;
        font-weight: bold;
    }

    table tr td {
        font-weight: bold;
        padding: 3px;

    }

    #container {
        margin: 0px auto;
    }
</style>
<section class="content">
    <div class="container-fluid" style="background-color: white;padding:10px;">
        <div class="box box-primary">
            <div class="col-md-10">
                <h3 style="font-weight:bold;margin-left:12px;">{{trans('tecnicof.anexom')}}
                </h3>
            </div>
            <div class="col-md-2" style="margin-top:5px ;">
                <button type="button" class="btn btn-info" onclick="window.history.back();">{{trans('tecnicof.return')}}</button>
            </div>
            <table style="width: 100%;border:1px solid black;">
                <tr style="background:white">
                    <th style="text-align: center !important;border-right-style:none !important;width:25%;">
                        <img src="{{asset('/imagenes/logo_riesgo.png')}}" alt="logo" style="margin:3px;">
                    </th>
                    <th style="text-align: center !important;width:50%;font-size:30px !important;">{{trans('tecnicof.macdem')}}</th>
                    <th style="width:12%;">
                        <table border="1" style="margin: 15px !important;margin-left:20px;">
                            <tr>
                                <th>{{trans('tecnicof.version')}}</th>
                                <td style="width: 65PX;">1</td>
                            </tr>
                            <tr>
                                <th>{{trans('tecnicof.code')}}</th>
                                <td>DNCSS-MSP-008</td>
                            </tr>
                            <tr>
                                <th>{{trans('tecnicof.date')}}</th>
                                <td>{{$data}}</td>
                            </tr>
                        </table>
                    </th>
                </tr>
            </table>
            <form action="" name="form" id="form">
                <input type="hidden" id="id_paciente" name="id_paciente" value="{{$paciente->id}}">
                <input type="hidden" id="id_camilla" name="id_camilla" value="{{$camilla->id}}">
                <input type="hidden" id="id_agenda" name="id_agenda" value="{{$id_agenda->id}}">
                <table style="width: 100%;border:1px solid black;height:8% !Important;">
                    <tr>
                        <th class="th" style="background-color: #EAF2F5;">
                            {{trans('tecnicof.kids')}}
                        </th>
                    </tr>
                </table>
                <table style="width: 100%;border:1px solid black;height:8% !important;">
                    <tr>
                        <th class="th" style="background-color: #C1E1E0;width:80%;border-right:1px solid black;">
                            {{trans('tecnicof.variables')}}
                        </th>
                        <th class="th" style="background-color: #C1E1E0;">
                            {{trans('tecnicof.scores')}}
                        </th>
                    </tr>
                </table>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="edad">1 {{trans('tecnicof.age')}}.</label>
                        <select name="edad" id="edad" class="form-control select2 todos_sumados" onchange="mostrar_res(this)" required>
                            <option value="" selected="selected">{{trans('tecnicof.select')}}</option>
                            <option class="mis-sumar" value="2">{{trans('tecnicof.newborn')}}</option>
                            <option class="mis-sumar" value="2">{{trans('tecnicof.youngerinf')}}</option>
                            <option class="mis-sumar" value="3">{{trans('tecnicof.olderinfant')}}</option>
                            <option class="mis-sumar" value="3">{{trans('tecnicof.preschoool')}}</option>
                            <option class="mis-sumar" value="1">{{trans('tecnicof.schoolage')}}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ant_caida">2.{{trans('tecnicof.fallh')}}</label>
                        <select onchange="mostrar_res(this)" name="ant_caida" id="ant_caida" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">{{trans('tecnicof.select')}}</option>
                            <option value="1">{{trans('tecnicof.yes')}}</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="antecedentes">3.{{trans('tecnicof.History')}}</label>
                        <select onchange="mostrar_res(this)" name="antecedentes" id="antecedentes" class="form-control select2 todos_sumados required" multiple="multiple">
                            <option value="">{{trans('tecnicof.select')}}</option>
                            <option value="1">{{trans('tecnicof.hyperactivity')}}</option>
                            <option value="1">{{trans('tecnicof.neuromuscular')}}</option>
                            <option value="1">{{trans('tecnicof.organic')}}</option>
                            <option value="1">{{trans('tecnicof.other')}}</option>
                            <option value="0">{{trans('tecnicof.nohistory')}}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="compro_conci">4.{{trans('tecnicof.consciousness')}}</label>
                        <select onchange="mostrar_res(this)" name="compro_conci" id="compro_conci" class="form-control select2 todos_sumados" required>
                            <option value="" selected="selected">{{trans('tecnicof.select')}}</option>
                            <option value="0">No</option>
                            <option value="1">{{trans('tecnicof.yes')}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-8 mb-3" style="margin-top: 10px;">
                        <label for="">7. {{trans('tecnicof.risklevel')}} </label>
                        <img style="margin-left: 4px;margin-right:4px;" src="{{asset('/imagenes/flecha.png')}}" alt="flecha">
                        <input type="text" name="punt_final" id="punt_final">
                    </div>
                    <div class="col-md-1" style="margin-top: 17px;margin-bottom:5px">
                        <button type="button" id="gard" onclick="guardar()" class="btn btn-danger res" style="display: hidden;">{{trans('tecnicof.save')}}</button>
                    </div>
                    <div class="col-md-1" style="margin-top: 17px;">
                        <button type="button" onclick="vaciar()" class="btn btn-warning res" style="display: hidden;">{{trans('tecnicof.empty')}}</button>
                    </div>
                </div>
                <table border="1" style="margin: 15px !important;margin-left:20px;">
                    <tr>
                        <th>{{trans('tecnicof.version')}}</th>
                        <td style="width: 65PX;">1</td>
                    </tr>
                    <tr>
                        <th>{{trans('tecnicof.code')}}</th>
                        <td>DNCSS-MSP-008</td>
                    </tr>
                    <tr>
                        <th>{{trans('tecnicof.date')}}</th>
                        <td>{{$data}}</td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(".res").hide();
    var contador = 0;

    function vaciar() {
        $("#edad").val("").change();
        $("#ant_caida").val("").change();
        $("#antecedentes").val(0).change();
        $("#compro_conci").val("").change();


        $("#tabs").children().remove();
        $("#punt_final").val(0);
        contador = 0;
    }


    $(document).ready(function() {
        $('.select2').select2({
            tags: false,
        });
    });
    var cont = 0;
    $(".todos_sumados").on('change', function() {
        var tot = 0;
        var f = 0;
        var suma = 0;
        var valor1 = 0;
        var valor2 = 0;
        var valor3 = 0;
        var valor4 = 0;
        cont++;
        var final = parseInt($("#punt_final").val(f));
        valor1 = parseInt($("#edad option:selected").val());
        valor2 = parseInt($("#ant_caida option:selected").val());
        valor4 = parseInt($("#compro_conci option:selected").val());
        valor3 = $("#antecedentes").val();
        var suma1 = 0;
        for (var i = 0; i < valor3.length; i++) {
            suma1 += parseInt(valor3[i]);
        }
        //tot = parseInt(valor1 + valor2 + valor4 + suma1);
        if (Number.isNaN(valor1)) {
            valor1 = 0;
        }
        if (Number.isNaN(valor2)) {
            valor2 = 0;
        }
        if (Number.isNaN(valor4)) {
            valor4 = 0;
        }
        console.log(valor1, valor2, valor4, suma1);
        $("#punt_final").val(parseInt(valor1 + valor2 + valor4 + suma1));

    });

    function mostrar_res(tot) {
        contador = parseInt(contador) + parseInt(tot.value);
        if (contador >= 0 && contador <= 1) {
            var ok = ('<td style="border-right:1px solid black;text-align:center;">Bajo</td>"+"<td style="border-right:1px solid black;text-align:center;">0 a 1</td>"+"<td style="text-align:center;">Cuidados bajo Enfermeria</td>');
            $("#tabs").empty().append(ok);

        }
        if (contador >= 2 && contador <= 3) {
            var ok1 = ('<td style="border-right:1px solid black;text-align:center;">Medio</td>"+"<td style="border-right:1px solid black;text-align:center;">2 a 3</td>"+"<td style="text-align:center;">Implementacion del plan de prevencion</td>');
            $("#tabs").empty().append(ok1);

        }
        if (contador >= 4 && contador <= 6) {
            var ok3 = ('<td style="border-right:1px solid black;text-align:center;">Alto</td>"+"<td style="border-right:1px solid black;text-align:center;">4 a 6</td>"+"<td style="text-align:center;">Implementacion de medidas especiales</td>');
            $("#tabs").empty().append(ok3);
        }
        $(".res").show();
    }

    function guardar() {

        if (document.form.edad.value == '' || document.form.ant_caida.value == '' || document.form.antecedentes.value == '' || document.form.compro_conci.value == '') {

            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Campos Vacios',
                showConfirmButton: false,
                timer: 1500
            })
        } else {
            document.getElementById("gard").disabled = true;
            $.ajax({
                url: "{{route('guardar_datos_menor')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Guardado',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    var url = "{{route('camilla.index')}}";
                    window.location = url;
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
    }
</script>
@endsection