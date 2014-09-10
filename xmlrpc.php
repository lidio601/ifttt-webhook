<?php

require_once(dirname(__FILE__) . '/settings.php');
require_once(dirname(__FILE__) . '/log.php');
require_once(dirname(__FILE__) . '/plugin.php');
require_once(dirname(__FILE__) . '/function.php');

if($DEBUG) {
  error_reporting(-1);
  ini_set('display_errors', 1);
}

$request_body = file_get_contents('php://input');
$xml = simplexml_load_string($request_body);

__log("Endpoint triggered");

// Plugin?
$__PLUGIN = null;

if (!$xml) die ("Ooops! No XML Payload: You possibly want to <a href=\"index.php\">read the documentation!</a>");

__log("Method called: ".$xml->methodName);
switch ($xml->methodName) {

    //wordpress blog verification
    case 'mt.supportedMethods':
        
        success('metaWeblog.getRecentPosts');
        
        /*
        sample call request body
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
  <methodName>mt.supportedMethods</methodName>
  <params>
  </params>
</methodCall>
        */
        
        break;
    //first authentication request from ifttt
    case 'metaWeblog.getRecentPosts':
      
        /*
        //send a blank blog response
        //this also makes sure that the channel is never triggered
        //success('<array><data></data></array>');
        */
        
        /*
        sample call request body
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
        
        // send a fake blog post to trigger the event
        // "wordpress"::"onNewPost" on IFTTT
        // http://codex.wordpress.org/XML-RPC_WordPress_API/Posts
        // http://codex.wordpress.org/XML-RPC_MetaWeblog_API
        success('  <array>
          <data>
            <value>
              <struct>
              <member><name>postid</name><value>'.rand(199,99999).'</value></member>
              <member><name>title</name><value>Fake Post</value></member>
              <member><name>description</name><value>a new fake post</value></member>
              <member><name>link</name><value></value></member>
              <member><name>userid</name><value>0</value></member>
              <member><name>dateCreated</name><value></value></member>
              <member><name>date_created_gmt</name><value></value></member>
              <member><name>date_modified</name><value></value></member>
              <member><name>date_modified_gmt</name><value></value></member>
              <member><name>wp_post_thumbnail</name><value></value></member>
              <member><name>permaLink</name><value></value></member>
              <member><name>categories</name><value><array></array></value></member>
              <member><name>mt_keywords</name><value></value></member>
              <member><name>mt_excerpt</name><value></value></member>
              <member><name>mt_text_more</name><value></value></member>
              <member><name>wp_more_text</name><value></value></member>
              <member><name>mt_allow_comments</name><value>0</value></member>
              <member><name>mt_allow_pings</name><value>0</value></member>
              <member><name>wp_slug</name><value></value></member>
              <member><name>wp_password</name><value></value></member>
              <member><name>wp_author_id</name><value></value></member>
              <member><name>wp_author_display_name</name><value></value></member>
              <member><name>post_status</name><value>publish</value></member>
              <member><name>wp_post_format</name><value></value></member>
            </struct>
          </value>
        </data>
      </array>');
        
        break;

    case 'metaWeblog.newPost':
        __log("Processing newpost payload");
        
        //@see http://codex.wordpress.org/XML-RPC_WordPress_API/Posts#wp.newPost
        $obj = new stdClass;
        //get the parameters from xml
        $obj->user = (string) $xml->params->param[1]->value->string;
        $obj->pass = (string) $xml->params->param[2]->value->string;
        
        if( !checkLogin($obj->user,$obj->pass) ) {
          
          __log("No valid user and pass specified");
          failure(400);
          
        } else {

          //@see content in the wordpress docs
          $content = $xml->params->param[3]->value->struct->member;
          foreach ($content as $data) {
              switch ((string) $data->name) {
                  //we use the tags field for providing webhook URL
                  case 'mt_keywords':
                      $url = $data->xpath('value/array/data/value/string');
                      $url = (string) $url[0];
                      break;

                  //the passed categories are parsed into an array
                  case 'categories':
                      $categories = array();
                      foreach ($data->xpath('value/array/data/value/string') as $cat)
                          array_push($categories, (string) $cat);
                      $obj->categories = $categories;
                      break;

                  //this is used for title/description
                  default:
                      $obj->{$data->name} = (string) $data->value->string;
              }
          }
          
          /*
          // Plugin details
          if ($ALLOW_PLUGINS) {
            
              __log("Plugins are permitted");
            
              foreach ($obj->categories as $category) {
                  if (strpos($category, 'plugin:') !== false)
                          $__PLUGIN = $category;
              }
            
              // If we allow plugins, pass the constructed object to 
              if ($__PLUGIN) {
                  $processed = executePlugin($__PLUGIN, $obj, $content);
                  if ($processed)
                      $obj = $processed;
                  else
                  {
                      __log("Plugin was invalid");
                      failure(400);
                  }
              } 
              else
              {
                  __log("No valid plugin specified");
                  failure(400);
              }
          }
          */
          
          // No plugin allowed here
          // only description json string is accepted
          
      		$obj->body = json_decode($obj->description); //new stdClass;
		
      		if (!$obj->body) {
      			__log("Invalid JSON payload '$obj->description'", 'ERROR');
      			failure(400);
            //return false;
      		} else {
            
            /*
            //Make the webrequest
            //Only if we have a valid url
            if ($url)
    	{
    		if (valid_url($url, true)) {
    		    // Load Requests Library
    	            include('requests/Requests.php');
    	            Requests::register_autoloader();

    	            $headers = array('Content-Type' => 'application/json');
    	            $response = Requests::post($url, $headers, json_encode($obj));

    	            if ($response->success)
    	                success('<string>' . $response->status_code . '</string>');
    	            else
    	                failure($response->status_code);
    	        }
    	        else {
            	    //since the url was invalid, we return 400 (Bad Request)
    	            failure(400);
            	}
    	} else
    		success('<string>No forward url, but will assume data was handled locally</string>');
            */
        
            // No webrequest to make
            // we handle the request locally here
            // to send the proper push notification
            
            $data = json_decode(file_get_contents('php://input'))."\n\n".print_r($_REQUEST,1).print_r($obj,1);

            mail("root","IFTTT Android Push service",$data);
            
            
            success('<string>' . 200 . '</string>');
            
          } // end if invalid json request body
          
        } // end if checkLogin
        
}

/** Copied from wordpress */
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

function output($xml) {
    $length = strlen($xml);
    header('Connection: close');
    header('Content-Length: ' . $length);
    header('Content-Type: text/xml');
    header('Date: ' . date('r'));
    echo $xml;
    exit;
}

function failure($status) {
    
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
          <value><string>Request was not successful.</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>

EOD;
    output($xml);
}

/** Used from drupal */
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
