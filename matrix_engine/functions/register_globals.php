<?php
	##register_globals cheat code

	//HTTP_GET_VARS
	while (list($key, $val) = @each($HTTP_GET_VARS)) {
       $GLOBALS[$key] = $val;
	}
	
	//HTTP_POST_VARS
	while (list($key, $val) = @each($HTTP_POST_VARS)) {
		$GLOBALS[$key] = $val;
	}

	//_FILES
	while (list($key, $val) = @each($_FILES)) {
		$GLOBALS[$key] = $val;
	}
	
	//$HTTP_SESSION_VARS
	while (list($key, $val) = @each($HTTP_SESSION_VARS)) {
		$GLOBALS[$key] = $val;
	}
	//$HTTP_SERVER_VARS
	while (list($key, $val) = @each($HTTP_SERVER_VARS)) {
		$GLOBALS[$key] = $val;
	}
?>
