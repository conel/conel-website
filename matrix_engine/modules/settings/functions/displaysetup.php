<?php
## =======================================================================        
##  config.php        
## =======================================================================        
##  used to define all vars that need to be set when we install this onto
##  a new server- later we will search an replace the vbars to protect 
##  the code
##
##  TODO:   
##     - check if it works    
## =======================================================================
function displaySetup() {
	global $Auth, $gSession;
	

	## get the template for now
	$template = new Template("../interface/");
	$template->set_templatefile(array("text" => "preferencesUI.tpl","head" => "preferencesUI.tpl","intro" => "preferencesUI.tpl","radio" => "preferencesUI.tpl","date" => "preferencesUI.tpl","foot" => "preferencesUI.tpl"));

	## get the config info for this module
	$configmanager = new Configmanager();
	$configmanager->setConfigPath(MATRIX_BASEDIR."settings/modules/settings/");
	$configmanager->setConfigFile("prefs");
	$configmanager->setConfigName("settings");
	$current_config = $configmanager->readConfigFile();

	## header and intro element
	$template->set_var('language_inputhead','Settings');
	$template->set_var('language_inputbody','Please specify the mail settings to be used for sending emails.');

	## specify the target of this page
	$actionURL = "module.php";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('actionURL',$actionURL);
	
	## let's set the command
	$output =  '<input type="hidden" name="op" value="save">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$template->set_var("hiddenfields",$output);
		
	
	$template->pfill_block("head");
	$template->pfill_block("intro");

	## SMTP SERVER
	$template->set_var('element_tag','SMTP Server');
	$template->set_var('element_desc','Please add the SMTP Server to be used e.g. smtp.yourhost.com');
	$template->set_var('element_name','SMTPHOST');
	$template->set_var('value',$current_config['SMTPHOST']);
	$template->pfill_block("text");
	
	$template->set_var('element_tag','Username');
	$template->set_var('element_desc','Please supply a username to access the SMTP Server');
	$template->set_var('element_name','SMTPUSER');
	$template->set_var('value',$current_config['SMTPUSER']);
	$template->pfill_block("text");	

	$template->set_var('element_tag','Password');
	$template->set_var('element_desc','Please supply a password to access the SMTP Server');
	$template->set_var('element_name','SMTPPASS');
	$template->set_var('value',$current_config['SMTPPASS']);
	$template->pfill_block("text");

	$template->set_var('element_tag','From Email');
	$template->set_var('element_desc','This is the email address your campaigns will come from.');
	$template->set_var('element_name','SENDERSEMAIL');
	$template->set_var('value',$current_config['SENDERSEMAIL']);
	$template->pfill_block("text");

	$template->set_var('element_tag','From Name');
	$template->set_var('element_desc','This is what will appear in the From field of your recipients email client when they receive campaigns.');
	$template->set_var('element_name','SENDERSNAME');
	$template->set_var('value',$current_config['SENDERSNAME']);
	$template->pfill_block("text");
	
	$template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$template->pfill_block("foot");
}
?>