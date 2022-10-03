@extends('contable.arqueo_caja.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Detalle Arqueo de Caja</h3>
        </div>
        <div class="box-body">
            <form id="crear_arqueo" method="post" action="">
                {{ csrf_field() }}
                <div class="form-group col-md-6">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado_pro_agrup">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th> Cantidad </th>
                                    
                                        
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($detalles as $detalle)

                                        <tr>
                                            <td>{{$detalle->denominacion}}</td>
                                            <td> <input type="number" class="form-control input-sm" name="detalle_cantidad{{$detalle->id}}" id="detalle_cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="actualizar_cantidad({{$detalle->id}})"></td>
                                        
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                                        
            </form>

            <form>

            </form>

        </div>
    </div>
</section>



@endsection

<script>
    function actualizar_cantidad(id){
      let valor = document.getElementById("detalle_cantidad"+id).value;
      
        $.ajax({
      type: 'post',
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      url:"{{route('ct_arqueo_caja.update_arqueo')}}",
      data:{
          'id_detalle': id,
          'valor': valor
        },
      datatype: 'json',
      success: function(data){
          console.log(data);
          //alert(data)
      },
      error: function(data){
        //console.log(data);
        //alert(data)
      }
    })

    }
</script>