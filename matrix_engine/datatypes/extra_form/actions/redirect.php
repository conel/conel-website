<?php
	## =======================================================================
	## extra_formAction_redirect												
	## =======================================================================
	## is a simple action that redirects to a specified page
	## =======================================================================
	function extra_formAction_redirect($id,$page_id,$params,$previous_results) {
		## redirect to the page passed to us
		$target_page = getTargetURL($params['target_pageid']);
		header("Location: ".SITE_URL.$target_page);			
		header("Status: 303");
		exit;	
	}					
?>