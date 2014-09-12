<?php

defined('__COMMON__') or die('Direct access not allowed here');

if(!$SUPPORT_WORDPRESS_AUTHENTICATION) {
	function checkLogin($user,$pass) {
		return true;
	}
} else {
	require_once(dirname(__FILE__).'/'.'auth.php');
	
	if(!function_exists('checkLogin')) {
		function checkLogin($user,$pass) {
			if(!$SUPPORT_WORDPRESS_AUTHENTICATION) {
				return true; //Always authenticated
			}
			// to implement
			return true;
		}
	}
}

/** 
	Copied from wordpress 
*/
function success($innerXML) {
    
    __log("Success!");
    
    $xml = <<<EOD
<?xml version="1.0"?>
<methodResponse>
  <params>
    <param>
      <value>
      $innerXML
      </value>
    </param>
  </params>
</methodResponse>

EOD;
    output($xml);
	
}

function failure($status, $message="Request was not successful.") {

	__log("Failure: $status", 'ERROR');

	$xml = <<<EOD
<?xml version="1.0"?>
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>$status</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>$message</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>

EOD;
	output($xml);
}

function output($xml) {
    $length = strlen($xml);
    header('Connection: close');
    header('Content-Length: ' . $length);
    header('Content-Type: text/xml');
    header('Date: ' . date('r'));
    echo $xml;
    die();
}

/** 
	Used from drupal 
*/
function valid_url($url, $absolute = FALSE) {
	
	if ($absolute) {
		
		return (bool) preg_match("
      /^                                                      # Start at the beginning of the text
      (?:https?):\/\/                                # Look for ftp, http, https or feed schemes
      (?:                                                     # Userinfo (optional) which is typically
        (?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*      # a username or a username and password
        (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@          # combination
      )?
      (?:
        (?:[a-z0-9\-\.]|%[0-9a-f]{2})+                        # A domain name or a IPv4 address
        |(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\])         # or a well formed IPv6 address
      )
      (?::[0-9]+)?                                            # Server port number (optional)
      (?:[\/|\?]
        (?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})   # The path and query (optional)
      *)?
    $/xi", $url);
		
	} else {
		return (bool) preg_match("/^(?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})+$/i", $url);
	}
	
}

?>
