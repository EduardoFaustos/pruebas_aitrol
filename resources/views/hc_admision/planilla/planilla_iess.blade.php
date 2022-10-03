@extends('hc_admision.planilla.base')
@section('action-content')
<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">{{trans('eplanilla.INGRESODEPLANILLASIESS')}}</h3>
		        </div>
		    </div>
		</div>
	  	<div class="box-body">
			<form>
				<div class="row">
				<div class="form-group col-md-4 ">
	                <label for="cedula_ben" class="col-md-4 control-label">{{trans('eplanilla.Id.Beneficiario:')}}</label>
	                <div class="col-md-7">
	                    <input id="cedula_ben" maxlength="10" type="number" class="form-control input-sm" name="cedula_ben" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <div class="col-md-7">
	                    <button type="submit" class="btn btn-primary">{{trans('eplanilla.Buscar')}}</button>
	                </div>
                </div>
                </div>
                <div class="row">
			  	<div class="form-group col-md-4 ">
	                <label for="hc" class="col-md-4 control-label">{{trans('eplanilla.Hc:')}}</label>
	                <div class="col-md-7">
	                    <input id="hc" maxlength="10" type="text" class="form-control input-sm" name="hc">
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="nombres" class="col-md-4 control-label">{{trans('eplanilla.NombresBeneficiario:')}}</label>
	                <div class="col-md-7">
	                    <input id="nombres" maxlength="10" type="text" class="form-control input-sm" name="nombres" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="apellidos" class="col-md-4 control-label">{{trans('eplanilla.ApellidosBeneficiario:')}}</label>
	                <div class="col-md-7">
	                    <input id="apellidos" maxlength="10" type="text" class="form-control input-sm" name="apellidos" required>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="sexo" class="col-md-4 control-label">{{trans('eplanilla.Sexo:')}}</label>
	                <div class="col-md-7">
	                    <input id="sexo" maxlength="10" type="text" class="form-control input-sm" name="sexo" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="edad" class="col-md-4 control-label">{{trans('eplanilla.Edad:')}}</label>
	                <div class="col-md-7">
	                    <input id="edad" maxlength="10" type="text" class="form-control input-sm" name="edad" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="fecha_nac" class="col-md-4 control-label">{{trans('eplanilla.FechadeNacimiento:')}}</label>
	                <div class="col-md-7">
	                    <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text"  class="form-control input-sm" id="fecha_nac" name="fecha_nac" placeholder="AAAA/MM/DD" required >
                        </div>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="cedula_afi" class="col-md-4 control-label">{{trans('eplanilla.CédulaAfiliado:')}}</label>
	                <div class="col-md-7">
	                    <input id="cedula_afi" maxlength="10" type="number" class="form-control input-sm" name="cedula_afi" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="nom_afi" class="col-md-4 control-label">{{trans('eplanilla.NombresAfiliado:')}}</label>
	                <div class="col-md-7">
	                    <input id="nom_afi" maxlength="10" type="text" class="form-control input-sm" name="nom_afi" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="ape_afi" class="col-md-4 control-label">{{trans('eplanilla.ApellidosAfiliado:')}}</label>
	                <div class="col-md-7">
	                    <input id="ape_afi" maxlength="10" type="text" class="form-control input-sm" name="ape_afi" required>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="parentesco" class="col-md-4 control-label">{{trans('eplanilla.Parentesco:')}}</label>
	                <div class="col-md-7">
	                    <select id="parentesco" name="parentesco" class="form-control input-sm" required>
	                    	<option>Seleccione...</option>
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="fecha_ing" class="col-md-4 control-label">{{trans('eplanilla.FechaIngreso:')}}</label>
	                <div class="col-md-7">
	                    <input id="fecha_ing" maxlength="10" type="text" class="form-control input-sm" name="fecha_ing" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="fecha_alt" class="col-md-4 control-label">{{trans('eplanilla.FechaAlta:')}}</label>
	                <div class="col-md-7">
	                    <input id="fecha_alt" maxlength="10" type="text" class="form-control input-sm" name="fecha_alt" required>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="seguro" class="col-md-4 control-label">{{trans('eplanilla.TipoSeguro:')}}</label>
	                <div class="col-md-7">
	                    <select id="seguro" name="seguro" class="form-control input-sm" required>
	                    	<option>{{trans('eplanilla.Seleccione')}}...</option>
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="seguro_priv" class="col-md-4 control-label">{{trans('eplanilla.SeguroPrivado:')}}</label>
	                <div class="col-md-7">
	                    <select id="seguro_priv" name="seguro_priv" class="form-control input-sm" required>
	                    	<option>{{trans('eplanilla.Seleccione')}}...</option>
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="cob_comp" class="col-md-4 control-label">{{trans('eplanilla.CoberturaCompartida:')}}</label>
	                <div class="col-md-7">
	                    <select id="cob_comp" name="cob_comp" class="form-control input-sm" required>
	                    	<option>{{trans('eplanilla.Seleccione')}}...</option>
	                    </select>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="diagnostico" class="col-md-4 control-label">{{trans('eplanilla.DiagnosticoPrincipal:')}}</label>
	                <div class="col-md-7">
	                    <input id="diagnostico" maxlength="10" type="text" class="form-control input-sm" name="diagnostico" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="mes_plano" class="col-md-4 control-label">{{trans('eplanilla.MesPlano:')}}</label>
	                <div class="col-md-7">
	                    <input id="mes_plano" maxlength="10" type="text" class="form-control input-sm" name="mes_plano" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="empresa" class="col-md-4 control-label">{{trans('eplanilla.Empresa:')}}</label>
	                <div class="col-md-7">
	                    <select id="empresa" name="empresa" class="form-control input-sm" required>
	                    	<option>{{trans('eplanilla.Seleccione')}}...</option>
	                    </select>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="cod_deriva" class="col-md-4 control-label">{{trans('eplanilla.Cod.Derivación:')}}</label>
	                <div class="col-md-7">
	                    <select id="cod_deriva" name="cod_deriva" class="form-control input-sm" required>
	                    	<option>{{trans('eplanilla.Seleccione')}}...</option>
	                    </select>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="pres_def" class="col-md-4 control-label">{{trans('eplanilla.DxPresuntivooDef.')}}</label>
	                <div class="col-md-7">
	                    <input id="pres_def" maxlength="10" type="text" class="form-control input-sm" name="pres_def" required>
	                </div>
                </div>
                <div class="form-group col-md-4 ">
	                <label for="cod_depen" class="col-md-4 control-label">{{trans('eplanilla.Cod.Dependencia')}}</label>
	                <div class="col-md-7">
	                    <select id="cod_depen" name="cod_depen" class="form-control input-sm" required>
	                    	<option>{{trans('eplanilla.Seleccione')}}...</option>
	                    </select>
	                </div>
                </div>
                </div>
                <div class="row">
                <div class="form-group col-md-4 ">
	                <label for="nom_plantilla" class="col-md-4 control-label">{{trans('eplanilla.NombredePlantilla:')}}</label>
	                <div class="col-md-7">
	                    <input id="nom_plantilla" maxlength="10" type="text" class="form-control input-sm" name="nom_plantilla" required>
	                </div>
                </div>
                </div>
			  
			</form>
	  	</div>
	</div>
</section>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">



    $(function () {
        
        $('#fecha_nac').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
        
    });
</script>
@endsection