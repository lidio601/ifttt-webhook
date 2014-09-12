<?php

defined('__COMMON__') or die('Direct access not allowed here');

require_once(dirname(__FILE__) . '/common.php');

/**
  * Debug logging
  */

function __log($message, $level = "NOTICE") {
   global $DEBUG;
   if ($DEBUG) {
       error_log("$level: $message");
   }
}

?>