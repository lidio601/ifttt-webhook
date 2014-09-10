<?php

//print_r($_SERVER);die();

if( isset($_SERVER) && isset($_SERVER['REQUEST_URI']) && in_array( strval($_SERVER['REQUEST_URI']) , array( '/feed', '/?feed=rss2', '/feed/' ) ) ) {
  require_once( dirname(__FILE__) .'/'. 'feed.php' );
  return;
}

//Redirect to the github project for information about the webhook (my fork)
header("Location: https://github.com/lidio601/ifttt-webhook");

?>