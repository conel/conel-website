<?PHP

#################################
#     Required Include Files    #
#################################
require("config.php");
if(REWRITE_GLOBALS == "ON") {
	include("functions/register_globals.php");
}
## include the template class
require(CLASSES_DIR."template.php");
  
## include the db class
require(CLASSES_DIR."db_mysql.php");


require(CLASSES_DIR."container.php");
require(CLASSES_DIR."session.php");
require(CLASSES_DIR."authentication.php");
require(CLASSES_DIR."page.php");

require(CLASSES_DIR."class_mailer.php");
include("functions/access.php");
include("functions/ui_dialogs.php");

page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();

include("interface/lang/".$Auth->auth["language"].".php");


## check if we have the correct access rights
$access_rights = _getUserAccessRights($Auth->auth['user_id']);

if(!isset($access_rights['pages']['workspace'])) {
	## display the error message
	ui_output_error("<b>Pages</b><br><br> ".LANG_NoAccessRights);
	exit;
}
	


$select_template = new Template(INTERFACE_DIR);
$select_template->set_templatefile(array("body" => "help/".$Auth->auth["language"]."/help.tpl"));
$select_template->set_var('errorMessage',LANG_WelcomeMsg);
$select_template->set_var('language_copyright',LANG_Copyright);		
$select_template->pfill_block("body");

?>
