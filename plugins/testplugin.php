<?php

defined('__COMMON__') or die('Direct access not allowed here');

/**
	* Example webhook format plugin.
	*/
class TestPlugin extends Plugin {

	public function execute($plugin, $object, $raw) {
		
		error_log("Plugin: " . $plugin);
		error_log("Object:" . print_r($object, true));
		error_log("Raw: ". print_r($raw, true));
		
		return $object;
		
	}
	
}

?>