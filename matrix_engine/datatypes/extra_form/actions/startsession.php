<?php
	## =======================================================================
	## extra_formAction_if												
	## =======================================================================
	## simple if command- checks if the previous command was successfull
	## and executes different commands depeding on the result
	## =======================================================================
	function extra_formAction_startsession($id,$page_id,$params,$results) {
		UserSession::start('workmatrix_user');
	}					
?>