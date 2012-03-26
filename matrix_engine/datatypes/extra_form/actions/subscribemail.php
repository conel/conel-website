<?php
	## =======================================================================
	## extra_formAction_register												
	## =======================================================================
	## sample register action- takes just three fields 
	## =======================================================================
	function extra_formAction_subscribemail($id,$page_id,$params,$previous) {
		## first we need to fetch the previously created object
		$object_id = $previous['object_id'];

		## fetch the fields of the current source
		$structurefile = MATRIX_BASEDIR.'settings/datatypes/extra_form/'.$params['structure'].'.xml';		
		if(!file_exists($structurefile)) {
			return array('status'=>false);
		}
		## okay open the form file and parse it
		$ctl_form = new formparser($structurefile);
		$ctl_form->parse();
		$fields = $ctl_form->getFields();

		## prepare the db connection
		$db = new DB_Sql();
		
		## prepare the target db object module
		$module = mysql_real_escape_string($params['target']);
			
		## for now we only support elements that store their data in the main table
		@include_once(ENGINE.'modules/'.$module.'/settings.php');		
		@include_once(ENGINE.'modules/clients/functions/elements.php');
			
		$object_info = clients_getClientDetail($object_id);
		
		## now quickly check for a unique email field
		$email_field = array();
		foreach($fields as $current_field) {
			if($current_field['type'] == 'email' && $current_field['unique'] == 'true') {
				## okay we found an entry
				$email_field = $current_field;
			}
		}
		
		## check if we have an email field- otherwise we need to stop here
		if(empty($email_field)) {
			return array('status'=>false);
		}
		
		## if we are still here, we should get the specified page
		## generate the html and prepare the page to be send
		require_once(ENGINE.CLASSES_DIR."class_mailer.php");
		require_once(ENGINE.CLASSES_DIR."class_smtp.php");
		require_once(ENGINE."functions/mail.php");
		
		$body = page_generatePage($params['email_pageid']);
		
		## the subject is the page name
		$pageInfo = structure_getStructureID($params['email_pageid']);
		$subject = $pageInfo['text'];
		
		## finally substitute any fields in the email
		$search = array();
		$replace = array();
		foreach($object_info as $key =>$val) {
			$search[] = '['.$key.']';
			$replace[] = ''.$val.'';
		}
		
		$body = str_replace($search,$replace,$body);	

		$recipients = array(array('email'=>$object_info[$email_field['identifier']],'user_name'=>$object_info[$email_field['identifier']]));
		sendMail(array("address"=>MAIL_FROMMAIL,"name"=>MAIL_FROM),$subject,$recipients,$body);

		return array('status'=>true);
		
	}					
?>