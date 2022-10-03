<?php

// Datos de la base de datos
$servidor    = "localhost";
$basededatos = "sis_medico";
$usuario     = "root";
$password    = "G@str0@dm1n";
$id_imagen   = $_GET['id_imagen'];
$formato     = $_GET['formato'];
$ip_cliente  = $_SERVER["REMOTE_ADDR"];

if (!isset($_GET['id_imagen'])) {
    echo "no se encuentra el video";
    exit;
}

if (!isset($_GET['formato'])) {
    echo "no existe el formato a convertir";
    exit;
}

if (($_GET['formato'] != "avi") && ($_GET['formato'] != "mpeg") && ($_GET['formato'] != "mov") && ($_GET['formato'] != "mp4") && ($_GET['formato'] != "wmv")) {
    echo "no existe el formato a convertir";
}

// Create connection
$conn = new mysqli($servidor, $usuario, $password, $basededatos);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql       = "SELECT * FROM hc_imagenes_protocolo WHERE id = '" . $id_imagen . "'";
$result    = $conn->query($sql);
$resultado = array();

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        array_push($resultado, $row);
    }
} else {
    echo "no se encuentra el video";
}

if ($resultado[0][$formato] == 0) {
    // Creamos el comando ffmpeg

    if (($_GET['formato'] == "avi")) {
        $sql = "UPDATE hc_imagenes_protocolo SET avi = 1 WHERE id = '" . $id_imagen . "'";
        //echo $sql;exit;
        $ffmpeg_cmd = "ffmpeg -i  " . $resultado[0]['nombre'] . "  -vcodec copy " . substr($resultado[0]['nombre'], 0, -4) . "." . $formato;
        @exec($ffmpeg_cmd);
    }

    if (($_GET['formato'] == "mpeg")) {
        $sql        = "UPDATE hc_imagenes_protocolo SET mpeg = 1 WHERE id = '" . $id_imagen . "'";
        $ffmpeg_cmd = "ffmpeg -i " . $resultado[0]['nombre'] . " -vcodec copy " . substr($resultado[0]['nombre'], 0, -4) . "." . $formato;
        @exec($ffmpeg_cmd);
    }

    if (($_GET['formato'] == "mov")) {
        $sql        = "UPDATE hc_imagenes_protocolo SET mov = 1 WHERE id = '" . $id_imagen . "'";
        $ffmpeg_cmd = "ffmpeg -i " . $resultado[0]['nombre'] . " -vcodec copy " . substr($resultado[0]['nombre'], 0, -4) . "." . $formato;
        @exec($ffmpeg_cmd);
    }

    if (($_GET['formato'] == "mp4")) {
        $sql        = "UPDATE hc_imagenes_protocolo SET mp4 = 1 WHERE id = '" . $id_imagen . "'";
        $ffmpeg_cmd = "ffmpeg -i " . $resultado[0]['nombre'] . " -vcodec copy " . substr($resultado[0]['nombre'], 0, -4) . "." . $formato;
        @exec($ffmpeg_cmd);
    }

    if (($_GET['formato'] == "wmv")) {
        $sql        = "UPDATE hc_imagenes_protocolo SET wmv = 1 WHERE id = '" . $id_imagen . "'";
        $ffmpeg_cmd = "ffmpeg -i " . $resultado[0]['nombre'] . " -vcodec copy " . substr($resultado[0]['nombre'], 0, -4) . "." . $formato;
        @exec($ffmpeg_cmd);
    }
}

$result = $conn->query($sql);

echo "1";

$conn->close();
