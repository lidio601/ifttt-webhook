<?php

define('__COMMON__',1);

require_once(dirname(__FILE__) . '/settings.php');
require_once(dirname(__FILE__) . '/log.php');
require_once(dirname(__FILE__) . '/plugin.php');
require_once(dirname(__FILE__) . '/function.php');

if($DEBUG) {
  error_reporting(-1);
  ini_set('display_errors', 1);
}

?>