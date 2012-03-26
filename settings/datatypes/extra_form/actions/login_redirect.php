<?php
	## =======================================================================
	## extra_formAction_redirect												
	## =======================================================================
	## is a simple action that redirects to a specified page
	## =======================================================================
	function extra_formAction_login_redirect($id,$page_id,$params) {
		UserSession::start('workmatrix_user');
		$target_page = intval(UserSession::get('members:entrypage'));
		
		if($target_page == 0 || $target_page == 342) {
			$target_page = $params['target_pageid'];
		}
		header("Location: ".SITE_URL.getTargetURL($target_page));			
		header("Status: 303");
		exit;		
	}					
?>