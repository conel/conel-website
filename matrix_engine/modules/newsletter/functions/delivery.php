<?php

## =======================================================================        
##  newsletter_deleteNewsletter        
## =======================================================================        
##  actually sends a test email to the supplied address.
##  it handles everything: content generation+email generation
##
## ======================================================================= 
function newsletter_deliverySendTestMail($id,$email) {
	/*
	## load up the config file
	$configmanager = new Configmanager();
	$configmanager->setConfigPath(MATRIX_BASEDIR."settings/modules/settings/");
	$configmanager->setConfigFile("prefs");
	$configmanager->setConfigName("settings");
	$current_config = $configmanager->readConfigFile();
	*/
	
	
	
	## first we need to get the email
	$newsletterInfo = newsletter_getNewsletter($id);
	
	## now we have all required info- we need to get the content now
	$html_message = newsletter_pageGeneratePage($newsletterInfo['page_id']);

	## so now we should prepare the mail class
	$mail = new phpmailer();
	
	## the sender of this message
	$mail->From     = MAIL_FROMMAIL;
	$mail->FromName = MAIL_FROM;
				
	## the subject of the message-> its' the name of the newsletter
	$mail->Subject  =convert_html($newsletterInfo['subject']);
	$mail->Sender = $newsletterInfo['replyto'];

	## general settings
	//$mail->Host     = '192.168.2.4';
	$mail->Host     = 'ADMIN-EXCH.conel.ac.uk';
	//$mail->Mailer   = 'smtp';
	
	/*
	$mail->SMTPAuth = true;
	$mail->Username = $current_config['SMTPUSER'];
	$mail->Password = $current_config['SMTPPASS'];
	*/		
	## the body of the message	
	$mail->Body = str_replace('[matrix:email]',$current_recipient['email'],$html_message);
	$mail->Body = str_replace('[matrix:page]',$newsletterInfo['page_id'],$mail->Body);
	$mail->IsHTML(true);
	


	## and finally the recipient
	$mail->AddAddress($email,$email);

	## finally send the message
	$mail->Send();
	$mail->ClearAddresses();
    				
}

## =======================================================================        
##  newsletter_deliveryDisplayInput       
## =======================================================================        
##  displays the main page for a newsletter- this function needs to
##  call the other subobjects to retrieve the information to be displayed  
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function newsletter_deliveryDisplayInput($id) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## first we need to get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($id);
	
	$select_template = new Template('interface/');
	$select_template->set_templatefile(array("body" => "delivery_inputform.tpl"));
	
	$select_template->set_var("LANG",$Auth->auth["language"]);

	## prepare the urls
	$newsletterURL = 'newsletter.php?op=edit&id='.$id;
	$newsletterURL = $gSession->url($newsletterURL);	
	
	$deliveryURL = 'delivery.php?id='.$id;
	$deliveryURL = $gSession->url($deliveryURL);	
	
	## set the newsletter vars
	$tabs = ui_renderSectionTab('Overview',$newsletterURL,0,1,true);
	$tabs .= ui_renderSectionTab('Delivery',$deliveryURL,1,1,false,'fd');
	
	$select_template->set_var('TABS',$tabs);
	
	$select_template->set_var('GROUPTABS',$tabs);

	## first we have the test email
	$actionURL = 'delivery.php?id='.$id;
	$actionURL = $gSession->url($actionURL);		
	$select_template->set_var('actionURL',$actionURL);

	$hiddenfields_test = '<input type="hidden" name="op" value="testmail">';
	$hiddenfields_test .= '<input type="hidden" name="id" value="'.$id.'">';
	$select_template->set_var('hiddenfields_test',$hiddenfields_test);
	
	## here the url for the actual mailing
	## first we have the test email
	$sendURL = 'delivery.php?op=send&id='.$id;
	$sendURL = $gSession->url($sendURL);		
	$select_template->set_var('sendURL',$sendURL);	
	
	
	$select_template->pfill_block("body");
}



?>