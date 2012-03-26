<?php
	## =======================================================================
	## extra_formAction_redirect												
	## =======================================================================
	## is a simple action that redirects to a specified page
	## =======================================================================
	function extra_formAction_generate($id,$page_id,$params) {
		## redirect to the page passed to us
		
		## in order to prevent the execution of any forms
		## on this page we need to clear the post vars
		$_POST = array();
		print page_generatePage($params['target_pageid']);
		exit;	
	}					
?>