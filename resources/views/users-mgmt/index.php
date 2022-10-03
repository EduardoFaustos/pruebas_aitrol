<?php
if($_SERVER['SERVER_NAME'] == 'www.smartcapt.com' || $_SERVER['SERVER_NAME'] == 'smartcapt.com'){

	if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
		$location = 'https://www.smartcapt.com/demo';
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $location);
		exit;
	}else{
		header('Location: demo');
	}			
}else{
	//echo "entra";exit;
	header('Location: sis_medico');
}

?>