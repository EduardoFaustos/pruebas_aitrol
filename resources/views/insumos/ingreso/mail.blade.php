<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Correo</title>
   <style>

   .titulo {
    color: #1e80b6;
    padding-top: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    }
    .body{
     background-color: #ECECEC;	
    }
    .div_contenido{
    color: #1e80b6;
    margin-top: 15px;
    padding-top: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    background-color: #ffffff !important;
   }
   </style>
</head> 

<body class="body">

<div class="titulo" ><h1>Numero de Pedido</h1></div>
<hr>
<div class=".div_contenido" >Buen día ING , tiene un nuevo pedido con el numero <?php echo $id_pedido ; ?></div>
</body>
</html>