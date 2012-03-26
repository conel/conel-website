<?php
## =======================================================================        
##  newsletter.php        
## =======================================================================        
##  newsletter is the main switchbox for all functions related to the
##  newsletter object. 
## 
## =======================================================================
require("../framework.php");
require("functions/delivery.php");
require("functions/newsletter.php");

## get the usermodule
require_once("../../../user_modules.php");
require("../../functions/structure.php");

require_once("../../matrix_frontend.php");


require_once("../../datatypes/linklist/linklist_editor.php");
require_once("../../datatypes/image/image.php");
require_once("../../datatypes/date/date.php");
require_once("../../datatypes/text/text.php");
require_once("../../datatypes/copytext/copytext.php");
require_once("../../datatypes/linklist/linklist.php");
require_once("../../datatypes/include/include.php");
require_once("../../datatypes/link/link.php");
require_once("../../datatypes/file/file.php");
require_once("../../datatypes/listview/listview.php");
require_once("../../datatypes/box/box.php");

## include the subobjects
require("functions/page.php");
require("functions/recipients.php");
require("../clients/functions/groups.php");	
require("../clients/functions/elements.php");

require("../clients/classes/ctlparser.php");

	

	## we need to load the local language file
	include("interface/lang/".$Auth->auth["language"].".php");

	## we also need to chekc the access rights!

	## items to send in one step
	$items_per_step = 20;

	## process the input vars
	$op = isset($_GET['op']) ? $_GET['op'] : '';
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	
	## process the step vars
	$page = isset($_GET['page']) ? $_GET['page'] : 0;

	$configmanager = new Configmanager();
	$configmanager->setConfigPath(MATRIX_BASEDIR."settings/modules/settings/");
	$configmanager->setConfigFile("prefs");
	$configmanager->setConfigName("settings");
	$current_config = $configmanager->readConfigFile();
	
	
	## later we will prepare data pefore we actually start to send- then we will
	## need the switchbox- but for now we generate the page on the fly
	switch($op) {
		case 'process':
			## okay first we need to get the newsletter info
			$newsletterInfo = newsletter_getNewsletter($id);

			## then generate the message
			##$text_message = newsletter_pageGeneratePage($newsletterInfo['page_id'],'_t');
			$html_message = newsletter_pageGeneratePage($newsletterInfo['page_id']);

			## then we need to get the id and all required infos to customize the newsletter
			$db_connection = new DB_Sql();

			## now we need to get the members (for now just everyone on our group
			$current_recipients = array();
			$current_recipients = newsletter_getClients($id,$items_per_step);	

			## check if there are any recipients left
			if(count($current_recipients)) {
				## now send each recipient the mail
				$mail = new phpmailer();
	
				## the sender of this message
				$mail->From     = $newsletterInfo['replyto'];
				$mail->FromName = $current_config['SENDERSNAME'];
							
				## the subject of the message-> its' the name of the newsletter
				$mail->Subject  = convert_html($newsletterInfo['subject']);
				$mail->Sender = $newsletterInfo['replyto'];
			
				## general settings
				$mail->Host     = $current_config['SMTPHOST'];
				$mail->Mailer   = 'smtp';
				
				$mail->SMTPAuth = true;
				$mail->Username = $current_config['SMTPUSER'];
				$mail->Password = $current_config['SMTPPASS'];
				$mail->IsHTML(true);
				
				$subject = '';
				foreach($current_recipients as $current_recipient) {
					## okay first we need to update the cue- that we are currently processing the client xy
					if($current_recipient['id'] > 0) {
						$query = "UPDATE ".DB_PREFIX."newsletter_cue SET status='1', opened = '".time()."' WHERE recipient_id='".$current_recipient['id']."'";
					} else {
						$query = "UPDATE ".DB_PREFIX."newsletter_cue SET status='1', opened = '".time()."' WHERE email = '".$current_recipient['email']."'";
					}
					$db_connection->query($query);

					
					## here we will replace the values for this client
					$search = array();
					$replace = array();
					foreach($current_recipient as $key => $val) {
						$search[] = '['.$key.']';
						$replace[] = ''.$val.'';
					}

					## we need to substitute some vars- especially the email to allow unsibscribe functionality
					## the body of the message

					$mail->Body = str_replace('[matrix:email]',$current_recipient['email'],$html_message);
					$mail->Body = str_replace('[matrix:page]',$newsletterInfo['page_id'],$mail->Body);
					$mail->Body = str_replace('[random]',time()+$current_recipient['id'],$mail->Body);
					$mail->Body = str_replace('[id]',mt_rand(1000,21000),$mail->Body);
					$mail->Body = str_replace('[NEWSLETTERID]',$id,$mail->Body);
					$mail->Body = str_replace($search,$replace,$mail->Body);
					
					## prpeare thge subject
					$subject = str_replace('[matrix:email]',$current_recipient['email'],convert_html($newsletterInfo['subject']));
					$subject = str_replace('[matrix:page]',$newsletterInfo['page_id'],$subject);
					$subject = str_replace('[id]',mt_rand(1000,21000),$subject);
					$subject = str_replace('[NEWSLETTERID]',$id,$subject);
					$subject = str_replace($search,$replace,$subject);					
					$mail->Subject  = $subject;

					##$mail->AltBody = str_replace('[matrix:email]',$current_recipient['email'],$text_message);
					##$mail->AltBody = str_replace('[matrix:page]',$newsletterInfo['page_id'],$mail->AltBody);
					
					## and finally the recipient
					$mail->AddAddress($current_recipient['email'],$current_recipient['email']);
					
	
					## finally send the message
					$mail->Send();
					$mail->ClearAddresses();
					
					## okay  we need to update the cue- that we've send the client xy
					if($current_recipient['id'] > 0) {
						$query = "UPDATE ".DB_PREFIX."newsletter_cue SET status='2', send = '".time()."' WHERE recipient_id='".$current_recipient['id']."'";
					} else {
						$query = "UPDATE ".DB_PREFIX."newsletter_cue SET status='2', send = '".time()."' WHERE email = '".$current_recipient['email']."'";
					}					
					$db_connection->query($query);					
				}
				
				
				## this step is done- so output the progrss bar
				$actionURL = "mail_engine.php?op=process&page=".($page+1)."&id=".intval($_GET['id']);
				
				## prepare the processing string
				$processingString = '<br>'.LANG_MODULE_Newsletter_ProcessingStatus.' '.($page*$items_per_step).'...'.(($page*$items_per_step)+$items_per_step);
 
				output_progress(LANG_MODULE_Newsletter_ProcessingTitle,(LANG_MODULE_Newsletter_ProcessingDesc.$processingString),$actionURL);
			} else {
				## here we should set the status of the newsletter
				$db_connectionStore = new DB_Sql();
				
				## first we will check how many subscribes we send this newsletter to
				$query = "SELECT COUNT(*) as subscribes FROM ".DB_PREFIX."newsletter_cue WHERE newsletter_id='$id'";
				$result_pointer = $db_connectionStore->query($query);
				
				if($db_connectionStore->next_record()) {
					$subscribers = $db_connectionStore->Record['subscribes'];
				}
				
				$insert_query = "UPDATE ".DB_PREFIX."newsletter SET status='2', send = now(),recipients='$subscribers' WHERE id='$id'";
				$result_pointer = $db_connectionStore->query($insert_query);				

				## finally we clear the que
				$query = "DELETE FROM ".DB_PREFIX."newsletter_cue WHERE newsletter_id='$id'";
				$result_pointer = $db_connectionStore->query($query);				

				## okay back to overview
				$actionURL = "newsletter.php";
				$actionURL = $gSession->url($actionURL);				
				
				output_confirm_refresh(LANG_MODULE_Newsletter_DoneSendingTitle,LANG_MODULE_Newsletter_DoneSendingDesc,$actionURL);
				
			}
			break;
	
    	default:
			## this is the default page- we display the progressbar before anyotheer steps- this way we will    		
    		## not confuse the user
			$actionURL = "mail_engine.php?op=process&id=".intval($_GET['id']);
			$actionURL = $gSession->url($actionURL);
			
			## prepare the processing string
			output_progress(LANG_MODULE_Newsletter_ProcessingTitle,(LANG_MODULE_Newsletter_ProcessingDesc.$processingString),$actionURL);
    		exit;
					
							
      	break;
    }

## =======================================================================        
##  newsletter_getClients        
## =======================================================================        
##  this function needs to be more flexible- get get's the clients
##  and if they have an email address in the cue get's the email-address
##  (this way we can to ad-hooc importing wihtout registering the users
##  in the system
##
## =======================================================================        
function newsletter_getClients($newsletter_id,$items_perpage) {
	global $Auth;
	
	## all recipients are part of the same group so we need to only get one to find out about the rest
	$group = 0;	
	
	$recipients = array();
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first get the recipients from the cue
	$select_query = 'SELECT recipient_id,email FROM '.DB_PREFIX."newsletter_cue WHERE newsletter_id='$newsletter_id' AND status='0' LIMIT $items_perpage";
	$db_connection->query($select_query);

	$counter = 0;
	while($db_connection->next_record(MYSQL_ASSOC)) {
		$recipient_id = $db_connection->Record['recipient_id'];
		$email = $db_connection->Record['email'];
		
		## check if they are imported (then the email field is not emtpy
		if(isset($email) && !empty($email)) {
			## add-hoc import
			$recipient_info['email'] = $email;
			
			## let's fetch teh other fields
			$db = new DB_Sql();
			$query = "SELECT * FROM ".DB_PREFIX."newsletter_csv_details WHERE email='$email'";
			$db->query($query);
			
			if($db->next_record(MYSQL_ASSOC)) {
				$recipient_info = $db->Record;
			}
			
		} else {
			## okay gather the users data
			include(ENGINE.'/modules/clients/settings.php');
			$recipient_info = clients_getClient($recipient_id);
			
			## parse the xml file for the extra attrbiutes	
			$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/clients/base.xml');
			$wt->parse();	
			$elements = $wt->getElements();

			## now process all attributes
			foreach($elements as $current_row) {	
				foreach($current_row as $current_element) {
					## here we start calling all our attribute types
					$type = $current_element['TYPE'];
					switch($type) {
						default: {
							## we need to check if we have a module for this attributetype
							$type = strtolower($type);
							
							## first we try to include the apropriate file 
							@include_once("../clients/attributetypes/".$type."/attribute.php");
							## now we check if the function exists
							if(function_exists("clients_".$type."_getData")) {
								## no we call the function
								eval("\$element = clients_".$type."_getData(\$current_element,".$recipient_info['id'].");");
								## add the extra attribute to the group info
								$recipient_info[$current_element['IDENTIFIER']] = $element; 
							}					
						}
						break;
					}	
				}
			}
		}
		$recipients[$counter] = $recipient_info;
		$counter++;			
	}
	return $recipients;
}
?>
