@extends('ticket_permiso.base')
@section('action-content')
@php
$fecha = date('Y-m-d',strtotime($registro->fecha_registro));
$fecha1 = date('Y-m-d',strtotime($registro->fecha_desde));
$fecha2 = date('Y-m-d',strtotime($registro->fecha_hasta));
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp
<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }

    th {

        text-align: center;
    }

    .hola {
        z-index: 999999 !important;
        z-index: 999999999 !important;
        z-index: 99999999999999 !important;
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        display: block;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
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
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-7">
                    <h3 class="box-title">TICKET PERMISOS EDITAR</h3>
                </div>

                <div class="col-md-3">
                    <label for="">No de solicitud {{$registro->no_solicitud}}</label>
                </div>
                <div class="col-md-2">
                    <input type="button" class="btn btn-danger" value="Regresar" onclick="goBack()">
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-2">
                    <label for="">Nombres y Apellidos</label>
                </div>
                <div class="col-md-3">
                    <input type="text" id="usuario" readonly value="{{$registro->nombre->nombre1}} {{$registro->nombre->nombre2}} {{$registro->nombre->apellido1}} {{$registro->nombre->apellido2}}">
                    <div id="result">
                    </div>
                </div>
                <div class="col-md-3">

                </div>
                <div class="col-md-1">
                    <label for="">Fecha</label>
                </div>
                <div class="col-md-3">
                    <input type="date" id="fecha" readonly value="{{$fecha}}" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-top:5px;">
                <div class="col-md-1">
                    <label for="">Cédula</label>
                </div>
                <div class="col-md-2">
                    <input type="text" readonly id="cedula" value="{{$registro->cedula}}" class="form-control">

                </div>
                <div class="col-md-1" >
                    <label for="">Cargo</label>
                </div>
                <div class="col-md-2" >
                    <input type="text" readonly id="cargo" class="form-control" value="{{$registro->cargo}}">
                </div>
                <div class="col-md-1">
                    <label for="">Departamento</label>
                </div>
                <div class="col-md-2">
                    <input type="text" readonly id="area" class="form-control" value="{{$registro->departamento}}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-2">
                        <label for="">1.Permiso por fallecimiento</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="fallecimiento" @if($registro->tipo_permiso == 'fallecimiento') checked @endif>
                    </div>

                    <div class="col-md-2">
                        <label for="">6.Permiso medico</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="medico" @if($registro->tipo_permiso == 'medico') checked @endif>
                    </div>
                </div>
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-2">
                        <label for="">2.Licencia por maternidad</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="maternidad" @if($registro->tipo_permiso == 'maternidad') checked @endif>
                    </div>

                    <div class="col-md-2">
                        <label for="">7.Permiso Personal</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="personal" @if($registro->tipo_permiso == 'personal') checked @endif>
                    </div>
                </div>

                <div class="row" style="margin-top:5px;">
                    <div class="col-md-2">
                        <label for="">3.Licencia por paternidad</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="paternidad" @if($registro->tipo_permiso == 'paternidad') checked @endif>
                    </div>

                    <div class="col-md-2">
                        <label for="">8.Vacaciones</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="vacaciones" @if($registro->tipo_permiso == 'vacaciones') checked @endif>
                    </div>
                </div>

                <div class="row" style="margin-top:5px;">
                    <div class="col-md-2">
                        <label for="">4.Calamidad domestica</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="domestica" @if($registro->tipo_permiso == 'domestica') checked @endif>
                    </div>
                    <div class="col-md-2">
                        <label for="">9.Atrasos</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="domestica" @if($registro->tipo_permiso == 'atraso') checked @endif>
                    </div>
                </div>
                <div class="row" style="margin-top:5px;">
                    <div class="col-md-2">
                        <label for="">5.Olvido de marcación</label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" onclick="return false;" class="check" id="domestica" @if($registro->tipo_permiso == 'olvido') checked @endif>
                    </div>
                </div>

            </div>
            <div class="col-md-6">
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Para permiso en dias</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="">Fecha desde</label>
                    </div>
                    <div class="col-md-4">
                        <input type="date" readonly id="desde" value="{{$fecha1}}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha hasta</label>
                    </div>
                    <div class="col-md-4">
                        <input type="date" readonly id="hasta" value="{{$fecha2}}" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Para permiso en horas</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="">Sale</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" readonly id="sale" class="form-control" value="{{$registro->ora_salida}}">
                    </div>
                    <div class="col-md-2">
                        <label for="">Ingresa</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" readonly id="ingresa" value="{{$registro->ora_ingresa}}" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 5px;">
                    <div class="col-md-2">
                        <label for="">Estado</label>
                    </div>
                    <div class="col-md-4">
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Seleccione</option>
                            <option {{$registro->estado_solicitud == 0  ? 'selected' : ''  }} value="0">No aprobado</option>
                            <option {{$registro->estado_solicitud == 1  ? 'selected' : ''  }} value="1">Aprobado</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Subir Documento</label>
                    </div>
                </div>
                <div class="row">
                    @if($registro->ruta_archivo != null || $registro->ruta_archivo != '') <a target="_blank" class="btn btn-primary" href="{{route('ticketpermisos.ver_pdf',['id'=>$registro->id])}}">Descargar Documento</a> @else <a href="">No hay documento</a> @endif
                </div>
                <div class="row">
                    <div style="text-align: center;">
                        <label for="">Justificación</label>
                    </div>
                </div>
                @if($rolUsuario == 8 || $rolUsuario == 1)
                <div class="row" style="margin-top: 5px;">
                    <textarea name="obs_acepta" id="obs_acepta" placeholder="....." id="obs" style="width: 100%;">@if(!is_null($registro->justificacion_final)){{$registro->justificacion_final}} @endif</textarea>
                </div>
                @endif
            </div>
        </div>
        <div class="row" style="margin-left: 20px;">
            <p>*Todos los permisos por horas son recuperables,la forma de recuperar devera ser detallada en el presente documento</p>
            <p>*A todos los permisos debera adjuntarse el soporte respectivo, a excepción de los puntos 7 y 9 especificados en la presente acción de personal</p>
        </div>
        <div class="row" style="margin-left: 20px;">
            <textarea readonly name="obs" id="obs" placeholder="Observaciones" id="obs" style="width: 100%;">{{$registro->observaciones}}</textarea>
        </div>
        <div class="row">
            <div style="text-align: center;margin-bottom:10px;">
                <button class="btn btn-danger" id="editar">
                    Atender
                </button>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
    function veri(dato) {
        var datoEvitar = dato.id;
        var array = document.getElementsByClassName("check");
        for (let index = 0; index < array.length; index++) {
          
            if (array[index].id != datoEvitar) {
                document.getElementById(array[index].id).checked = false;
            }
        }
    }

    document.getElementById("usuario").addEventListener('keydown', function() {
        let usuario = document.getElementById("usuario").value;
        //console.log(usuario);

        $.ajax({
            url: "{{route('ticket_soporte_tecnico.autocompletar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'data': usuario,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                // console.log(data.length); 
                var resultado = document.getElementById("result");
                var list = '';
                if (data.length > 0) {
                    document.getElementById("result").style.visibility = "visible";
                    for (let i = 0; i < data.length; i++) {
                        var nombreCompleto = data[i]['nombre1'] + ' ' + data[i]['nombre2'] + ' ' + data[i]['apellido1'] + ' ' + data[i]['apellido2'];
                        list += '<li  onclick="oprimir(this)" name="' + nombreCompleto + '" id=' + data[i]['id'] + '>' + data[i]['nombre1'] + ' ' + data[i]['nombre2'] + ' ' + data[i]['apellido1'] + ' ' + data[i]['apellido2'] + '</li>';
                    }
                    resultado.innerHTML = '<ul class="hola" style="margin-top:30px;">' + list + '</ul>';
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });

    });

    function veri(dato) {

        var datoEvitar = dato.id;

        var array = document.getElementsByClassName("check");
        for (let index = 0; index < array.length; index++) {
            if (array[index].id != datoEvitar) {
                document.getElementById(array[index].id).checked = false;
            }
        }
    }

    function oprimir(valor) {
        var te = valor.getAttribute("name");
        document.getElementById("result").style.visibility = "hidden";
        document.getElementById("usuario").value = te;
        document.getElementById("cedula").value = valor.id;
        $.ajax({
            url: "{{route('ticketpermisos.verificar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': valor.id,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var area = '';
                if (data.area == 1) {
                    area = 'ADMINISTRATIVA';
                } else {
                    area = 'MEDICA';
                }
                if (data.cargo != undefined) {
                    document.getElementById("cargo").value = data.cargo;
                }
                if (area != undefined) {
                    document.getElementById("area").value = area;
                }
                var url = "{{route('ticket_soporte_tecnico.index')}}"
                if (data == 'ok') {

                    swal("Guardado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });

    }

    window.addEventListener('click', function(e) {
        document.getElementById("result").style.visibility = "hidden";
    });



    document.getElementById("editar").addEventListener('click', function() {
        var res = '';
        var todo = document.getElementsByClassName('check');
        for (let index = 0; index < todo.length; index++) {

            if (todo[index].checked == true) {

                res = todo[index].id;
            }
        }
        var arrayCompleto = [

            {
                'fecha_desde': document.getElementById('desde').value
            }, {
                'fecha_hasta': document.getElementById('hasta').value
            },
            {
                'sala': document.getElementById('sale').value
            }, {
                'ingresa': document.getElementById('ingresa').value
            },
            {
                'permiso': res
            },
            {
                'observaciones': document.getElementById("obs").value
            },
            {
                'id': <?php echo json_encode($registro->id); ?>
            },
            {
                'nombre': document.getElementById("usuario").value
            },
            {
                'fecha_registro': document.getElementById("fecha").value
            },
            {
                'cargo': document.getElementById("cargo").value
            },
            {
                'departamento': document.getElementById("area").value
            },
            {
                'estado': document.getElementById("estado").value
            },
            {
                'cedula': document.getElementById("cedula").value
            },
            {
                'obs_acepta': document.getElementById("obs_acepta").value
            }
        ];;
        enviar(arrayCompleto);
    });



    function enviar(arrayCompleto) {

        $.ajax({
            url: "{{route('ticketpermisos.editar_datos')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'todo': arrayCompleto,
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var url = "{{route('ticketpermisos.index')}}"
                if (data == 'ok') {

                    swal("Editado!", "Correcto", "success");
                    window.location = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }



    function goBack() {
        window.history.back()
    }
</script>


@endsection