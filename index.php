<?php

define('__WPINDEX__',1);

require_once(dirname(__FILE__) . '/common.php');

// If I have to respond to Trigger request from IFTTT
if( $BE_A_TRIGGER ) {
	
	//print_r($_SERVER);die();
	
	// let's answer with the xml rss feed
	if( isset($_SERVER) && isset($_SERVER['REQUEST_URI']) && in_array( strval($_SERVER['REQUEST_URI']) , array( '/feed', '/?feed=rss2', '/feed/' ) ) ) {
		
		require_once( dirname(__FILE__) .'/'. 'trigger.php' );
		
		return;
	}
}

// Redirect to the bitbucket project for information about the webhook (my fork)
header("Location: https://bitbucket.org/lidio601/ifttt-wordpress-gateway");

?>