<?php
require("../framework.php");
require("functions/newsletter.php");
## include the required clients function that allows us to get the groups
require("../clients/functions/groups.php");	
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

## get the usermodule
require_once("../../../user_modules.php");
require("../../functions/structure.php");	

## include the subobjects
require_once("functions/page.php");
require_once("functions/newsletter.php");
require_once("functions/recipients.php");



## we need to load the local language file
include("interface/lang/".$Auth->auth["language"].".php");

	## process the input vars
	$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');

	## this is the main switchbox
	switch($op) {
		case "create":			
			## display the group selector- we will extend this later
			## we need to be able to apply search criterias and save them
			## as filters- for now, to get this startedm we need to be able
			## to select a group
			$id = intval($_GET['id']);
			newsletter_recipientsDisplayInputForm($id);		
			break;

		case "store":
			## vars needed: PAGEID, TEMPLATEID, MODE
			$id 	= intval($_POST['id']);
			$group 	= intval($_POST['group']);
			
			## depending on the user selection we need to call 
			## a different function.
			if($_FILES['import']['size'] > 0) {
				## okay we have a file
				newsletter_recipientsCreateCueByFile();
			} else {
				## create the newsletter cue based on a filter
				newsletter_recipientsCreateCueByFilter();
			}
			
			## and redisplay the newsletter-overview page
			newsletter_displayOverview($id);
			
			break;
					
		case "delete":
			$page_id 	= intval($_GET['page_id']);
			$id 	= intval($_GET['id']);
		
			## first we remove all entries from the cue
			newsletter_recipientsDeleteCue($id);
									
			## now redisplay the overview page			
			newsletter_displayOverview($id);
			break;											
    	default:
			## we need to display the startup screen- which will be a little
			## intro to the newsletter object
			ui_output_error("<b>".LANG_MODULE_Newsletter_Title."</b><br>".LANG_MODULE_Newsletter_Desc);
      	break;
    }

?>