@extends('paciente_biopsia.base')
@section('action-content')
<div class="container-fluid">
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="col-md-9">
                        <h4>{{trans('pacientebiopsia.guardadomasivodebiopsias')}}</h4>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12{{ $errors->has('conclusiones') ? ' has-error' : '' }}">
                            <label for="conclusiones" class="col-md-12 control-label">{{trans('pacientebiopsia.biopsiasentregadas')}}</label>
                            <label for="conclusiones" class="col-md-12 control-label">{{trans('pacientebiopsia.lasbiopsiassolodebencontenerelnumerodeceduladelpaciente')}}</label>
                            <div class="col-md-12">
                                <form method="POST" action="{{route('hc_video2.guardado_foto2_biopsias2')}}" enctype="multipart/form-data" class="dropzone" id="addimage">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="fallback">

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    Dropzone.options.addimage = {
        acceptedFiles: ".pdf, .doc, .docx, .txt, .xls, .xlsx, .jpg, .jpeg, .png, .gif",
        init: function() {
            this.on("error", function(file, response) {
                alert('archivo no consta en el formato correcto o el paciente no existe, revise el archivo');
                console.log(response);
            });
            this.on("success", function(file, response) {
                console.log(response);
            });
        }
    };
</script>
@endsection