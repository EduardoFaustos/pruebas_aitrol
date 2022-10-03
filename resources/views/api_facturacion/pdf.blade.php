<?php

$data        = $response;
$destination = asset('/datos.pdf');
$file        = fopen($destination, "w+");
fputs($file, $data);
fclose($file);
$filename = 'datos.pdf';
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Type: application/pdf");
header("Content-Transfer-Encoding: binary");
readfile($destination);
exit;

$path    = asset('/datos.pdf');
$content = $response;

// save PDF buffer
file_put_contents($path, $content);

// ensure we don't have any previous output
if (headers_sent()) {
    exit("PDF stream will be corrupted - there is already output from previous code.");
}

header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

// force download dialog
header('Content-Type: application/force-download');
header('Content-Type: application/octet-stream', false);
header('Content-Type: application/download', false);

// use the Content-Disposition header to supply a recommended filename
header('Content-Disposition: attachment; filename="' . basename($path) . '";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($path));
header('Content-Type: application/pdf', false);

// send binary stream directly into buffer rather than into memory
readfile($path);

// make sure stream ended
exit();
