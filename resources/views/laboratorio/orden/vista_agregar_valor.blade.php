<div class="row">
    <hr>
    <input type="hidden" name="id_examen_orden" id="id_examen_orden" value="{{$id_examen_orden->id}}">
    <div class="col-md-12 text-center">
        <div class="col-md-2">
            <label for="">Nombres</label>
        </div>
        <div class="col-md-3">
            <label>{{$id_examen_orden->examen_orden->paciente->nombre1}} {{$id_examen_orden->examen_orden->paciente->nombre2}} {{$id_examen_orden->examen_orden->paciente->apellido1}} {{$id_examen_orden->examen_orden->paciente->apellido2 }}</label>
        </div>
        <div class="col-md-2">
            <label for="">Cédula</label>
        </div>
        <div class="col-md-3">
            <label>{{$id_examen_orden->examen_orden->paciente->id}}</label>
        </div>

    </div>
    <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class='well' style="color: black;">
                    <th style="text-align: center;" tabindex="0">Fecha</th>
                    <th style="text-align: center;" tabindex="0">Valor</th>
                </tr>
            </thead>
            <tbody style="text-align: center;">
                @foreach($lista_abono as $val)
                <tr>
                    <td>
                        {{$val->fecha}}
                    </td>
                    <td>
                        {{$val->valor}}$
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <div class="col-md-2">
            <label for="valor">Valor Actual</label>
        </div>
        <div class="col-md-2">
            <input type="text" id="valor_ant" class="form-control" value="{{$id_examen_orden->valor}}" readonly>
        </div>

        <div class="col-md-2">
            <label for="valor_restante">Saldo Restante</label>
        </div>
        <div class="col-md-2">
            <input type="text" id="valor_restante" class="form-control" value="{{$valor_restante}}" readonly>
        </div>
    </div>
    <div class="col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class='well' style="color: black;">
                    <th tabindex="0">Metodos</th>
                    <th tabindex="0">Fecha</th>
                    <th tabindex="0">Tipo</th>
                    <th style="width: 10%;" tabindex="0">Número</th>
                    <th tabindex="0">Banco</th>
                    <th tabindex="0">Cuenta</th>
                    <th tabindex="0">Valor</th>
                    <th tabindex="0">Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="form-group">
                            <select onchange="validaciones(this)" name="forma_pago" id="forma_pago" class="form-control" style="width: auto;border:0">
                                <option value="">Seleccione</option>
                                @foreach($metodo_de_pago as $val)
                                <option value="{{$val->id}}">{{$val->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input style="border:0" type="date" class="form-control vtdobra" name="fp_fecha_nueva" id="fp_fecha_nueva" value="{{ date('Y-m-d') }}">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <select style="width: auto;border:0" id="fp_tarjetanueva" class="form-control vtdobra" name="fp_tarjetanueva" required placeholder="Seleccione La Tarjeta">
                                <option value="">Seleccione La Tarjeta</option>
                                @foreach($tipo_tarjeta as $tt)
                                <option value="{{ $tt->id }}">{{ $tt->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input style="border:0" type="text" class="form-control vtdobra" name="fp_numero_nuevo" id="fp_numero_nuevo">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <select style="width: auto;border:0" id="fp_banco" class="form-control vtdobra" name="fp_banco" required placeholder="Seleccione Banco">
                                <option value="">Seleccione Banco</option>
                                @foreach($lista_banco as $lb)
                                <option value="{{ $lb->id }}">{{ $lb->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input style="border:0" type="text" class="form-control vtdobra" name="fp_cuenta_nueva" id="fp_cuenta_nueva">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input style="border:0" type="text" name="abonar" id="abonar" class="form-control" onchange="validationNumber(this)">
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <div class="form-group">
                            <i style="cursor: pointer;font-size:25px;" class="fa fa-floppy-o" aria-hidden="true" onclick="guardarElDetalle()"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function isNumberKey(evt) {
        var e = evt || window.event;
        var charCode = e.which || e.keyCode;
        if (charCode > 31 && (charCode < 45 || charCode > 57))
            return false;
        if (e.shiftKey) return false;
        return true;
    }

    function validationNumber(e) {
        let valorPrimero = document.getElementById("valor_restante").value;
        if (Number(e.value) > Number(valorPrimero)) {
            console.log(e.value > valorPrimero);
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'EL valor tiene que ser menor del valor inicial',
                showConfirmButton: false,
                timer: 1500
            })

            document.getElementById("abonar").value = "";

        }
    }

    function guardarElDetalle() {
        if (document.getElementById("abonar").value != '' && document.getElementById("forma_pago").value != '' && document.getElementById("fp_numero_nuevo") && document.getElementById("fp_cuenta_nueva")) {
            $.ajax({
                url: "{{route('ordenes.guardarvalor')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'id_examen_orden': document.getElementById("id_examen_orden").value,
                    'abonar': document.getElementById("abonar").value,
                    'forma_pago': document.getElementById("forma_pago").value,
                    'fecha': document.getElementById("fp_fecha_nueva").value,
                    'tarjeta': document.getElementById("fp_tarjetanueva").value,
                    'numero': document.getElementById("fp_numero_nuevo").value,
                    'banco': document.getElementById("fp_banco").value,
                    'cuenta': document.getElementById("fp_cuenta_nueva").value,
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'ok') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: data.msj,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        document.getElementById("tabla").innerHTML = '';

                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: data.msj,
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        } else {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: "Campos Vacios",
                showConfirmButton: false,
                timer: 1500
            })

        }

    }

    function validaciones(e) {
        console.log(e.value);
        if (e.value == 4 || e.value == 6) {
            document.getElementById("fp_tarjetanueva").removeAttribute("disabled");
            document.getElementById("fp_banco").setAttribute("disabled", "disabled");
        } else if(e.value == 1){
            document.getElementById("fp_banco").setAttribute("disabled", "disabled");
            document.getElementById("fp_tarjetanueva").setAttribute("disabled", "disabled");
        }else {
            document.getElementById("fp_tarjetanueva").setAttribute("disabled", "disabled");
            document.getElementById("fp_banco").removeAttribute("disabled");
        }
    }
</script>