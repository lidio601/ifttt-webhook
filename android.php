<?php

$data = json_decode(file_get_contents('php://input'))."\n\n".print_r($_REQUEST,1);

mail("root","IFTTT Android Push service",$data);

?>