<?php

defined('__COMMON__') or die('Direct access not allowed here');

defined('DO_ACTION') or failure(400);

/*****************************************************
  Old processing part that would take the TAGS
  field as an URL to ping the IFTTT action request.
  Here it is for retrocompatibility.
******************************************************/

/*
// Make the webrequest
// Only if we have a valid url
if ($obj->url) {
	
	if (valid_url($obj->url, true)) {
		
		// Load Requests Library
		include('requests/Requests.php');
		Requests::register_autoloader();
		
		$headers = array('Content-Type' => 'application/json');
		$response = Requests::post($url, $headers, json_encode($obj));
		
		if ($response->success) {
			success('<string>' . $response->status_code . '</string>');
		} else {
			failure($response->status_code);
		}
	}
	
	else {
		//since the url was invalid, we return 400 (Bad Request)
		failure(400);
	}
} else {
	success('<string>No forward url, but will assume data was handled locally</string>');
}
*/

// No webrequest to make
// we handle the request locally here
// to send a email for debug purposes

$data = json_decode(file_get_contents('php://input'))."\n\n".print_r($_REQUEST,1).print_r($obj,1);

mail("root","IFTTT Android Push service",$data);

success('<string>' . 200 . '</string>');

?>