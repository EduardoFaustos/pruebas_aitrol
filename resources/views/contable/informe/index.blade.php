@extends('contable.balance_comprobacion.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
            <label>Resumen Contabilidad</label>
        </div>
        <div class="box-body">
            <div class="col-md-12">
                <b style="font-size: 18px;">{{trans('contableM.Compras')}}</b>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>

            @foreach($compras as $c)
            @php
            $cxs= \Sis_medico\Contable::recovery_by_model('O','C',$c->id);
            $cxs = json_encode($cxs);
            $cxs= json_decode($cxs);
            //dd($cxs['original']);
            @endphp

            @php //dd($cxs->original); @endphp

            <div class="col-md-12">
                
                <div class="panel panel-default">
                    <div class="panel panel-heading">
                        <div class="row">

                            <div class="col-md-1">
                                <b>ID</b>
                            </div>
                            <div class="col-md-2">
                                {{$cxs->original->id}}
                            </div>
                            <div class="col-md-2">
                                <b>Numero Comprobante</b>
                            </div>
                            <div class="col-md-2">
                                {{$cxs->original->nro_comprobante}}
                            </div>
                            <div class="col-md-1">
                                <b>{{trans('contableM.asiento')}}</b>
                            </div>
                            <div class="col-md-2">
                                {{$cxs->original->asiento}}
                            </div>
                            <div class="col-md-2" style="text-align: right;">
                                <button type="button" class="btn btn-primary btn-gray btn-xs" onclick="sendfix('{{$c->id}}')"> <i class="fa fa-paper-plane"></i> </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                   <b> FINAL: </b>
                                </div>
                                <div class="col-md-12">
                                    <span>{{$cxs->original->total_final}} </span>
                                </div>
                                <div class="col-md-12">
                                   <b> VALOR CONTABLE: </b>
                                </div>
                                <div class="col-md-12">
                                     <span> {{$cxs->original->valor_contable}}</span> 
                                </div>
                            </div>
                        </div>
                        @if(isset($cxs->original->retencion))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>RETENCION</b>
                                    </div>
                                    <div class="col-md-12">
                                        <span>#: {{$cxs->original->retencion}}</span>
                                    </div>
                                </div>
                           </div>
                        @endif
                        @if(isset($cxs->original->egresos))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>EGRESOS</b>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($cxs->original->egresos as $x)
                                        <span>#: {{$x}}</span> <br/>
                                        @endforeach
                                    </div>
                                </div>
                           </div>
                        @endif
                        @if(isset($cxs->original->cruce))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>CRUCE</b>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($cxs->original->cruce as $x)
                                        <span>#: {{$x}}</span> <br/>
                                        @endforeach
                                    </div>
                                </div>
                           </div>
                        @endif
                        @if(isset($cxs->original->bndebito))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>DEBITO BANCARIO</b>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($cxs->original->bndebito as $x)
                                        <span>#: {{$x}}</span> <br/>
                                        @endforeach
                                    </div>
                                </div>
                           </div>
                        @endif
                        @if(isset($cxs->original->debitoacreedor))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>DEBITO ACREEDOR</b>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($cxs->original->debitoacreedor as $x)
                                        <span>#: {{$x}}</span> <br/>
                                        @endforeach
                                    </div>
                                </div>
                           </div>
                        @endif
                        @if(isset($cxs->original->credito_acreedor))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>CREDITO ACREEDOR</b>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($cxs->original->credito_acreedor as $x)
                                        <span>#: {{$x}}</span> <br/>
                                        @endforeach
                                    </div>
                                </div>
                           </div>
                        @endif
                        @if(isset($cxs->original->masivos))
                           <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <b>CREDITO ACREEDOR</b>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($cxs->original->masivos as $x)
                                        <span>#: {{$x}}</span> <br/>
                                        @endforeach
                                    </div>
                                </div>
                           </div>
                        @endif
                    </div>
                </div>
            </div>


            @endforeach



        </div>
    </div>
    <script>
        function sendfix(id){
            //alert(id);
            $.ajax({
            type: 'get',
            url: "{{route('api.loadData')}}",
            datatype: 'json',
            data: {
                'parameter': '1',
                'id':id,
                'type':'C'
            },
            success: function(data) {
               //console.log(data);
                Swal.fire('Success');
            },
            error: function(data) {}
        })
        }
    </script>
</section>

@endsection