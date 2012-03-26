<?php
	## =======================================================================
	## extra_formAction_redirect												
	## =======================================================================
	## is a simple action that redirects to a specified page
	## =======================================================================
	function extra_formAction_session_redirect($id,$page_id,$params) {
		$baseURL = getTargetURL($page_id);
		$data = (substr($_GET['url'],strlen($baseURL)));	
		$data = explode('/',$data);
		
		if(intval($data[0]) > 0) {			
			header("Location: ".SITE_URL.getTargetURL(346).'/'.intval($data[0]));			
			header("Status: 303");
		} else {
			header("Location: ".SITE_URL.getTargetURL(343));			
			header("Status: 303");		
		}
		exit;		
	}					
?>