<?php
	## =======================================================================
	## extra_formAction_if												
	## =======================================================================
	## simple if command- checks if the previous command was successfull
	## and executes different commands depeding on the result
	## =======================================================================
	function extra_formAction_echo($id,$page_id,$params,$results) {
		## check if previous command 
		var_dump($params);
		var_dump($_POST);
		return array('status'=>true);
		
	}					
?>