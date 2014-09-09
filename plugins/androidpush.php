<?php

/**
 * Android webhook plugin.
 */
class AndroidPush extends Plugin {
	
	public function execute($plugin, $object, $raw) {
		
		/*
		error_log("Plugin: " . $plugin);
		error_log("Object:" . print_r($object, true));
		error_log("Raw: ". print_r($raw, true));
		*/
		
		//return $object;
		
		$newobj = json_decode($object->description); //new stdClass;
		
		if (!$newobj) {
			__log("Invalid JSON payload '$json'", 'ERROR');
			return false;
		}
		
		$newobj->user = $object->user;
		$newobj->pass = $object->pass;
		
		return $json;
	}
}

?>