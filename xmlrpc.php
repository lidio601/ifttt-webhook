<?php

require_once(dirname(__FILE__) . '/common.php');

$request_body = file_get_contents('php://input');
$xml = simplexml_load_string($request_body);

__log("Endpoint triggered");

// Plugin?
$__PLUGIN = null;

if (!$xml) {
	failure(400,"Ooops! No XML Payload: You possibly want to read the documentation at http://".$_SERVER['HTTP_HOST']."/index.php");
}

__log("Method called: ".$xml->methodName);
switch ($xml->methodName) {
	
	/**********************************************
	********* Verification Step procedure *********
	***********************************************/
	
	// Wordpress blog verification technique
	/*
	Sample HTTP call request body
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
	<methodName>mt.supportedMethods</methodName>
	<params>
	</params>
</methodCall>
	*/
	case 'mt.supportedMethods':
		success('metaWeblog.getRecentPosts');
        break;
	
	/**********************************************
	********* IFTTT Trigger support code **********
	***********************************************/
	
	// first authentication request from IFTTT
	/*
	Sample HTTP call request body
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
	<methodName>metaWeblog.getRecentPosts</methodName>
	<params>
		<param><value><string>0</string></value></param>
		<param><value><string>user</string></value></param>
		<param><value><string>pass</string></value></param>
		<param><value><i4>4</i4></value></param>
	</params>
</methodCall>
        */
    case 'metaWeblog.getRecentPosts':
		
		// if you don't need the Trigger part
		// (this allow you to setup a Recipe that is triggered 
		//   by a "fake new post" event from here)
		if( $BE_A_TRIGGER == false ) {
			//send a blank blog response
			//this also makes sure that the channel is never triggered
			success('<array><data></data></array>');
		}
		// if you need the the Trigger part
		// let's call an external PHP file
		// to see if I have to post
		// some event now.
		// IFTTT will call this web service periodically
		// to check if there is some "new post" to fetch
		else if( $BE_A_TRIGGER == true ) {
			
			__log("IFTTT-TRIGGER: Processing getrecentpost request");
			
			// let's sumup all the values
			// from IFTTT in a single object
	        $obj = new stdClass;
		
	        // get the parameters from xml
	        $obj->user = (string) $xml->params->param[1]->value->string;
	        $obj->pass = (string) $xml->params->param[2]->value->string;
			
	        if( !checkLogin($obj->user,$obj->pass) ) {
          
	          __log("No valid user and pass specified");
	          failure(400);
          
	        } else {
				
				// process the request throw the external file
				define('DO_TRIGGER',true);
				require_once( dirname(__FILE__) .'/'. 'trigger.php' );
				
				// N.B. this endpoint is apparently been unused from IFTTT.
				// I'm keeping it to be compatible with future improvement from them...
				
				// To check for new posts they query directly 
				//the RSS Feed from the URI "/feed/".
				// So, let's move there.
				
			}
			
		} // end if be a trigger
        
        break;
	
	/**********************************************
	********** IFTTT Action support code **********
	***********************************************/
	
	// new action web service responder
	/*
		@see http://codex.wordpress.org/XML-RPC_WordPress_API/Posts#wp.newPost
	*/
    case 'metaWeblog.newPost':
		
        __log("IFTTT-ACTION: Processing newpost payload");
		
		if( $BE_AN_ACTION == false ) {
			
			failure(400);
			
		} else {
        
			// let's sumup all the values
			// from IFTTT in a single object
	        $obj = new stdClass;
		
	        // get the parameters from xml
	        $obj->user = (string) $xml->params->param[1]->value->string;
	        $obj->pass = (string) $xml->params->param[2]->value->string;
        
	        if( !checkLogin($obj->user,$obj->pass) ) {
          
	          __log("No valid user and pass specified");
	          failure(400);
          
	        } else {
			
				// @see content in the wordpress docs
				$content = $xml->params->param[3]->value->struct->member;
				foreach ($content as $data) {
				
					switch ((string) $data->name) {
					
						// we can use the TAGS field for providing webhook URL
						case 'mt_keywords':
							$url = $data->xpath('value/array/data/value/string');
							$url = (string) $url[0];
							if(valid_url($url, true)) {
								$obj->url = $url;
							} else {
								$obj->ketwords = $url;
							}
							break;
					
						//the passed categories are parsed into an array
						case 'categories':
							$categories = array();
							foreach ($data->xpath('value/array/data/value/string') as $cat) {
								array_push($categories, (string) $cat);
							}
							$obj->categories = $categories;
							break;
					
						//this is used for title/description
						default:
							$obj->{$data->name} = (string) $data->value->string;
							break;
					}
				
				} // end foreach content_data
			
				// Plugin details
				if ($ALLOW_PLUGINS) {
					__log("Plugins are permitted");
				
					foreach ($obj->categories as $category) {
						if (strpos($category, 'plugin:') !== false) {
							$__PLUGIN = $category;
						}
					}
				
					// If we allow plugins, pass the constructed object to 
					if ($__PLUGIN) {
						$processed = executePlugin($__PLUGIN, $obj, $content);
						if ($processed) {
							$obj = $processed;
						} else {
							__log("Plugin was invalid");
							failure(400);
						}
					} else {
						__log("No valid plugin specified");
						failure(400);
					}
				} // end if ALLOW_PLUGINS
			
				else {
					// No plugin allowed here
					// only description json string is accepted
				
					$obj->body = json_decode($obj->description); //new stdClass;
				
					if (!$obj->body) {
					
						/*
						// this was to return an error
						__log("Invalid JSON payload '$obj->description'", 'ERROR');
						failure(400);
						return false;
						*/

						// let's make it become a normal text string
						$obj->body = strval($obj->description);
					}
			
				} // end if not ALLOW_PLUGINS
			
				// process the request throw the external file
				define('DO_ACTION',true);
				require_once( dirname(__FILE__) .'/'. 'action.php' );
          
	        } // end if checkLogin
			
		} // end if action_enabled
		
		break;

} // end switch methodName

die();

?>