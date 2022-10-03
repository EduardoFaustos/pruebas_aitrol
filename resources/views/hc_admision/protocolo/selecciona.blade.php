
@extends('hc_admision.anestesiologia.base') 

@section('action-content')
 
<style type="text/css">
    
    .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }

</style>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">

                    <form id="form">
                        <input type="hidden" name="paciente" value="{{$paciente->id}}">
                        <div id="cambio3" class="form-group col-md-12 {{ $errors->has('procedimiento') ? ' has-error' : '' }}">
                            <label for="id_procedimiento" class="col-md-2 control-label">Procedimientos </label>
                            <div class="col-md-10" style="margin-left: -5px;">
                                <select id="id_procedimiento" class="form-control input-sm select2" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione"
                                style="width: 95.5%;" autocomplete="off">
                                    @foreach($px as $procedimiento)
                                        @php 
                                            $clase = 'c'; 
                                            if(!is_null($procedimiento->grupo_procedimiento)){
                                                $clase = $clase.$procedimiento->grupo_procedimiento->tipo_procedimiento;
                                            }
                                        @endphp    
                                        <option disabled="disabled" class="{{$clase}}" value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </form>              
                    <button id="boton" onclick="guardar();">Crear Procedimiento</button>    
                    

                </div>
            </div>
        </div>  

        
        

    </div>
</div>

<script type="text/javascript">
    
    $('.c{{$tipo}}').removeAttr('disabled');

    $('.select2').select2({
        tags: false,  
    });

    function actualiza_select(){
        alert("actualiza");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 
     
      if(longitud>=1){
        //alert("entro");
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        $('.c{{$tipo}}').removeAttr('disabled'); 
        $('.c').attr('disabled','disabled');   
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });
      
      $('.select2').select2({
            tags: false,  
        });
    }

    function quita_select(){
        alert("quita");
        var seleccionados =  $('#id_procedimiento').find(':selected');   
        var longitud =  seleccionados.length; 

      if(longitud>1){
        
        $('.c{{$tipo}}').attr('disabled','disabled');
        $('.c').removeAttr('disabled');

      }else{
        //alert("salio");
        $('.c{{$tipo}}').removeAttr('disabled');
        $('.c').attr('disabled','disabled');  
      }

      seleccionados.each(function( index ) {
          $( this ).removeAttr('disabled');;
        });

      $('.select2').select2({
            tags: false,  
        });
      
      
    }
     
    $("select").on("select2:select", function (evt) {

        var element = evt.params.data.element;
        //console.log(element);
        var $element = $(element);

        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
        actualiza_select();
    }); 

    $("select").on("select2:unselecting", function (evt) {

        quita_select();    
    }); 

    function guardar(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4_procedimiento.crear')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            console.log(data);
            alert("ok");
          },
          error: function(data){
            //console.log(data);
          }
        });
    }

</script>

@endsection
