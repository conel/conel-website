<?php
require("../framework.php");
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

error_reporting(0);

## include the subobjects
require_once("functions/page.php");
require_once("functions/newsletter.php");
require_once("functions/recipients.php");
require("../clients/functions/groups.php");	



## we need to load the local language file
include("interface/lang/".$Auth->auth["language"].".php");

	## process the input vars
	$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');

	## this is the main switchbox
	switch($op) {
		case "create":			
			## check if we have more then one template
			$nextStep = newsletter_pageGetTemplateList();

			if(count($nextStep) > 1) {	
				## in this case we need to display the template selector
				newsletter_displayTemplateList($nextStep,intval($_GET['id']));
			} else {		
				## first we store the page
				$page_id = page_createPage(LANG_NoName,$nextStep[0]['template_id'],'newsletter');
				
				## then we need to link the page to the newsletter!
				$id = intval($_GET['id']);
				newsletter_pageConnect2Newsletter($id,$page_id);
				
				## now output the input form
				newsletter_pageDisplayInputForm($nextStep[0]['template_id'],$page_id,'create');
			}			

			break;
		case "storetemplate":			
			## get the template
			$templateID = isset($_POST['templateID']) ? $_POST['templateID'] : null;
			
			$page_id = page_createPage(LANG_NoName,$templateID,'newsletter');

			## then we need to link the page to the newsletter!
			$id = intval($_POST['id']);
			newsletter_pageConnect2Newsletter($id,$page_id);
		
			## now output the input form
			newsletter_pageDisplayInputForm($nextStep[0]['template_id'],$page_id,'create');
			
			break;		
		case "editpage":
			## vars needed: PARENT, TEMPLATEID, MODE
			$page_id 	= isset($_GET['page_id']) ? $_GET['page_id'] : null;
			$templateID = isset($_GET['templateID']) ? $_GET['templateID'] : null;
	
			newsletter_pageDisplayInputForm($templateID,$page_id,'edit');
			break;
					
			
		case "store":
			## vars needed: PAGEID, TEMPLATEID, MODE
			$page_id 	= intval($_POST['pageID']);
		
			## depending on the mode, we need to call
			## the store page function and then
			## a) display the page_name input OR
			## b) preview the page
	
			newsletter_pageStoreContents();
			
			## redisplay the overview page- we need to get the newsletter id first
			$db_connectionStore = new DB_Sql();
			$query = "SELECT id FROM ".DB_PREFIX."newsletter WHERE page_id='".$page_id."'";
			$result_pointer = $db_connectionStore->query($query);
			$db_connectionStore->next_record();
			
			$id = $db_connectionStore->Record["id"];
			
			newsletter_displayOverview($id);
			
			break;

		case "editor":
			## thiscommand is called whenever we need to
			## open or close an editor window
			## we need to get the page info

			
			## we first store the page
			newsletter_pageStoreContents();
			
			## after storing, we redisplay the input form
			$page_id 	= $_POST['pageID'];
			$templateID = $_POST['templateID'];
			$mode		= $_POST['mode'];
			
			newsletter_pageDisplayInputForm($templateID,$page_id,$mode);		 		
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
						
		case "preview":
			## vars needed: PAGEID, TEMPLATEID, MODE
			$page_id 	= intval($_GET['page_id']);
		
			$base_newsletter = newsletter_pageGeneratePage($page_id);
			
			## we should fill the preview with example data
			
			## the example data will be extracted from the ctl file
			$base_newsletter = str_replace('[matrix:NEWSLETTERID]',1,$base_newsletter);
			$base_newsletter = str_replace('[matrix:CLIENTID]',1,$base_newsletter);
			$base_newsletter = str_replace('[matrix:TARGETPAGE]',SITE_ROOT.getTargetURL($page_id),$base_newsletter);
			echo $base_newsletter;
			
			break;	
		case "delete":
			$page_id 	= intval($_GET['page_id']);
			$id 	= intval($_GET['id']);
		
			## first we will unlik the newsletter and the page
			newsletter_pageDisConnect2Newsletter($id);
			
			## then delete the page
			page_deletePage($page_id);
			
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