
<!--@extends('layout')


@section('title',"crear usuario")

@section ('content')

<h1> crear usuario </h1>

@endsection
-->


<!DOCTYPE html>
<html>

<head>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
     <title>@yield('title') </title>

</head>

<body>

       Menu
  <section class="form-register"> <!-- la class se usa para controlar lo q haran las opciones en css-->
     <h3> Formulario Registro <?php echo $nombres;?> </h3>
     <input class="controls "  type="text" name="nombres" id="nombres " placeholder="Ingrese sus Nombres">  
     <input class="controls"   type="text" name="apellidos" id="apellidos " placeholder="Ingrese sus Apellidos">
     <input class="controls"   type="email" name="correo" id="correo " placeholder="Ingrese su Correo">
     <input  class="controls"  type="password" name="correo" id="correo " placeholder="Ingrese su Contraseña">
   
     <p> Estoy de acuerdo con  <a href="#"> Terminos y Condiciones </a></p>
     <input class=" boton1" type="submit"  value="Registrar">

    <p><a href="#">¿ya tengo cuenta?</a></p>

</body>  
  
</html>