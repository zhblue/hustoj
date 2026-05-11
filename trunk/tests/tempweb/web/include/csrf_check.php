<?php
  	@session_start();
  	if( $_SERVER['REQUEST_METHOD'] == 'POST'){
	  	if( !isset($_SESSION[$OJ_NAME.'_'.'csrf_keys'])
			|| !is_array($_SESSION[$OJ_NAME.'_'.'csrf_keys'])
	  		|| !isset($_POST['csrf'])
			|| !in_array($_POST['csrf'], $_SESSION[$OJ_NAME.'_'.'csrf_keys'])
		){
		  	http_response_code(403);
		  	echo "Invalid csrf token";
		  	exit;
		} else {
			$index = array_search($_POST['csrf'],$_SESSION[$OJ_NAME.'_'.'csrf_keys']);
			array_splice($_SESSION[$OJ_NAME.'_'.'csrf_keys'], $index, 1);
		}
  	}
?>