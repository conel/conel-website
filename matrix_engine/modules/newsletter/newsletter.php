<?php
## =======================================================================        
##  newsletter.php        
## =======================================================================        
##  newsletter is the main switchbox for all functions related to the
##  newsletter object. 
##
##	it handles a) the creation of a new newsletter 
##                  -> display input for name
##					-> store name, create a new newsletter object
##					-> display the main page for the newsletter
##				b) modification of a newsletter
##					-> open an existing newsletter, call the subobjects
##					-> and display the overview page
##				c) deletion of a newsletter
##					-> calls all subobjects 
##					-> deletes the main object
##					-> displays confirmation page
##
##  TODO:   
##     - check if it works    
## =======================================================================

require("../framework.php");

error_reporting(0);

require("functions/newsletter.php");
require_once("../../matrix_frontend.php");
require_once("../../../user_modules.php");
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
require("functions/delivery.php");
require("functions/recipients.php");
require("../clients/functions/groups.php");	

## get the usermodule
require("../../functions/structure.php");	




## we need to load the local language file
include("interface/lang/".$Auth->auth["language"].".php");

	## process the input vars
	$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');

	## this is the main switchbox
	switch($op) {
		case "create":			
			## first we create a dummy newsletter entry
			$page_id = newsletter_createNewsletter(LANG_NoName);
			
			
			## now we ask the user for a name
			$actionURL = "newsletter.php";
			$actionURL = $gSession->url($noURL);			
			newsletter_displayInputName($page_id,'',$actionURL);		
			break;
			
		case "updatename":			
			## get the data we recieved
			$id 	= intval($_POST['id']);
			$text	= $_POST['menu_text'];
			
			## store the newsletter name
			newsletter_updateName($id,$text);
			
			## now we can display the newsletter overview
			newsletter_displayOverview($id);
				
			break;
		case "storereply":			
			## get the data we recieved
			$id 	= intval($_GET['id']);
			$text	= $_POST['reply'];
			
			## store the newsletter name
			newsletter_updateReplyAddress($id,$text);
			
			## now we can display the newsletter overview
			newsletter_displayOverview($id);
				
			break;
		case "storesubject":			
			## get the data we recieved
			$id 	= intval($_GET['id']);
			$text	= $_POST['email'];
			
			## store the newsletter name
			newsletter_updateSubject($id,$text);
			
			## now we can display the newsletter overview
			newsletter_displayOverview($id);
				
			break;			
		case "edit":
			## this is called when the user wants to edit a certain newsletter
			$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : '');
			
			newsletter_displayOverview($id);
			
			break;						

		case "rename":			
			## first we create a dummy newsletter entry
			$page_id = $_GET['id'];
			
			$data = newsletter_getNewsletter($page_id);
			## now we ask the user for a name
			$actionURL = "newsletter.php";
			$actionURL = $gSession->url($noURL);	

			newsletter_displayInputName($page_id,$data['name'],$actionURL);		
			break;		

		case "save":
			$client = isset($_GET['client']) ? intval($_GET['client']): intval($_POST['client']);
			## store the selected item
			clients_storeClients();
			## finnally redisplay input form
			$clientRecord = clients_getClient($client);
				## finally we display the input form
			clients_dispayInputForm($client,$clientRecord);		
			break;
		case "delete":
			## we need to display the prompt if the user really wants to delete the page
			
			## prepare the yes URL
			$yesURL = "newsletter.php?op=dodelete&id=".intval($_GET['id']);
			$yesURL = $gSession->url($yesURL);
	
			## prepare the no URL	
			$noURL = "newsletter.php";
			$noURL = $gSession->url($noURL);

			module_newsletter_promptDelete($yesURL,$noURL,LANG_MODULE_Newsletter_DeleteTitle,LANG_MODULE_Newsletter_DeleteTitleDesc);
			break;
		case "dodelete":
			## now we can actually delete the clients
			newsletter_deleteNewsletter(intval($_GET['id']));
			
			## prepare the url- in order to refresh the newsletter menu
			$menuURL = "menu.php";
			$menuURL = $gSession->url($menuURL);
						
			## redisplay the listing
			newsletter_output_confirm('Newsletter deleted successfully',' ',$menuURL);
			break;

		case "copy":
			## now we can actually delete the clients
			newsletter_copyNewsletter(intval($_GET['id']));
			
			## prepare the url- in order to refresh the newsletter menu
			$menuURL = "menu.php";
			$menuURL = $gSession->url($menuURL);
						
			## redisplay the listing
			newsletter_output_confirm('Newsletter copied successfully',' ',$menuURL);
			break;
			
		case "testmail":
			## we should test the email first- for now its okay
			$email = $_POST['email'];
			
			## prepare the id
			$id 	= intval($_POST['id']);
			
			## finally send the the email
			newsletter_deliverySendTestMail($id,$email);	
			
			## after we are done, we should display a confirmation
			## for now: just a simple message- and the overview again

			newsletter_displayOverview($id);
			break;
		
		case "send":
			## the user decided to send the mails- we will ask him
			## first if he is sure about it
			
			## first check if we can connect to the smtp server
			$configmanager = new Configmanager();
			$configmanager->setConfigPath(MATRIX_BASEDIR."settings/modules/settings/");
			$configmanager->setConfigFile("prefs");
			$configmanager->setConfigName("settings");
			$current_config = $configmanager->readConfigFile();

			$mail = new phpmailer();

			## the sender of this message
			$mail->From     = $current_config['SENDERSEMAIL'];
			$mail->FromName = $current_config['SENDERSNAME'];
						
			## the subject of the message-> its' the name of the newsletter
			$mail->Subject  = $newsletterInfo['name'];
		
			## general settings
			$mail->Host     = $current_config['SMTPHOST'];
			$mail->Mailer   = 'smtp';
			
			$mail->SMTPAuth = true;
			$mail->Username = $current_config['SMTPUSER'];
			$mail->Password = $current_config['SMTPPASS'];
			
			## check if we can connect to the smtp server
			if($mail->SmtpConnect()) {
				## prepare the id
				$id 	= intval($_POST['id']);
	
				## prepare the yes URL
				$yesURL = "mail_engine.php?op=dosend&id=".intval($_GET['id']);
				$yesURL = $gSession->url($yesURL);
		
				## prepare the no URL	
				$noURL = "newsletter.php?id=".intval($_GET['id']);
				$noURL = $gSession->url($noURL);
							
				## display the dialog box
				module_newsletter_promptDelete($yesURL,$noURL,LANG_MODULE_Newsletter_SendTitle,LANG_MODULE_Newsletter_SendDesc);	
			} else {
				## we need to display an error message
				$target = "menu.php?id=".intval($_GET['id']);
				newsletter_output_confirm('Error',"Couldn't connect to the smtp-server please check the data entered in the settings tab.",$target);
			}
			break;			
			
    	default:
			## we need to display the startup screen- which will be a little
			## intro to the newsletter object
			ui_output_error("<b>".LANG_MODULE_Newsletter_Title."</b><br>".LANG_MODULE_Newsletter_Desc);
      	break;
    }

## =======================================================================        
##  portlets_promptDelete        
## =======================================================================        
function module_newsletter_promptDelete($yesURL,$noURL,$title,$desc) {
	global $gSession,$Auth;

	## prepare the template file
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));

	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',$title);
	$select_template->set_var('language_doyouwant',$desc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  output_confirm        
## =======================================================================        
##  display a confirmation page that the current
##  action was completed succesfully
##
##  TODO:
##  
## ======================================================================= 
function newsletter_output_confirm($title,$message,$target) {
	global $gSession;
	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "dialog_confirm.tpl"));
	$select_template->set_var('title',$title);
	$select_template->set_var('message',$message);
	
	$targetURL = $target;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}
?>
