<?php
	## =======================================================================
	## extra_formAction_register												
	## =======================================================================
	## sample register action- takes just three fields 
	## =======================================================================
	function extra_formAction_email($id,$page_id,$params,$previous_results) {
		## all data is checked
		## so we will need to send out an email to the target specified
		require_once(ENGINE.CLASSES_DIR."class_mailer.php");
		require_once(ENGINE.CLASSES_DIR."class_smtp.php");
		require_once(ENGINE."functions/mail.php");
	
		## the contents will be passed on - they are already validated
		## so they are save to be send on
		$body = generateBody($params['template']);
		$body = str_replace('[OBJECT_ID]',$previous_results['object_id'],$body);
		
		$subject = generateSubject($params['template']);

		if(isset($params['targetid'])) {
			$recipients = getRecipientsIDs($params['targetid']);
		}

		sendMail(array("address"=>MAIL_FROMMAIL,"name"=>MAIL_FROM),$subject,$recipients,$body);
	
		return array('status'=>true,'object_id'=>$previous_results['object_id']);
	}					
?>