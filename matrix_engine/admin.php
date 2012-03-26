<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
##error_reporting(E_ALL^E_Notice);
#################################
#     Required Include Files    #
#################################
require("config.php");

## include the template class
require(CLASSES_DIR."template.php");
  
## include the db class
require(CLASSES_DIR."db_mysql.php");


require(CLASSES_DIR."container.php");
require(CLASSES_DIR."session.php");
require(CLASSES_DIR."authentication.php");
require(CLASSES_DIR."page.php");

##page_open(array("session" => "session_object")); 
##page_close();

## the xmlparser
require(CLASSES_DIR."xmlparser.php");

require(CLASSES_DIR."class_mailer.php");

## include the db class
require("../user_modules.php");

## include the db class
require(CLASSES_DIR."files.php");

## matrix_functions => general functions
require("functions/ui_dialogs.php");
require("functions/utilities.php");

require("functions/page.php");
require("functions/lock.php");
require("functions/versions.php");
include("functions/access.php");
require("functions/pageselector.php");
require("functions/events.php");
require("functions/plugins.php");

require("matrix_frontend.php");
include_once("userpage.php");            //Gives all page creation and editing functions for new user pages.
include_once("functions/folder.php");
##require("menu_edit.php");           //Used for editing menu items.

if(REWRITE_GLOBALS == "ON") {
	include("functions/register_globals.php");
}

require("functions/structure.php");	

require("sort/sort.php");

// nkowald - 2009-10-07 - Added new redirect function include
require("redirect/redirect.php");

## first get the available languages
include('functions/language.php');
include_once("functions/ui_utilities.php");	
	
page_open(array("session" => "session_object", "authenticate" => "Auth")); 
page_close();
include("interface/lang/".$Auth->auth["language"].".php");

## register the language
language_registerLanguage();

## prepare the command list
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');

###########################################################
#    The SWITCH:                                                                   #
#       This switch statement takes the arguement $op passed from an admin page    #
#       and decides which functions to call based on that variable.                #
####################################################################################  
switch($op) {            
	case "display_template_list":
		$parent 	= isset($_GET['parent']) ? $_GET['parent'] : null;
		## this is the starting point for a new page- 
		## we will display a list of templates available to the user
		display_template_list($parent);
		break;
		
	case "editpage":
		## vars needed: PARENT, TEMPLATEID, MODE
		$parent 	= isset($_POST['parent']) ? $_POST['parent'] : null;
		$templateID = isset($_POST['templateID']) ? $_POST['templateID'] : null;
		$mode		= isset($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
		$type		= isset($_POST['type']) ? $_POST['type'] : $_GET['type'];
		
		## get the user id	
		$user_id = $Auth->auth['user_id'];

		## on creation we create a empty page and register it in the structure
		if($mode == 'create') {
			## we need to store the page, and its template
			$page_id = page_createPage(LANG_NoName,$templateID);
			
			## now we have a page id
			## let's register it in the structure
			$menu_id = structure_storePage($page_id,$parent,PAGE_INACTIVE);	
			
			## now check if we need to create a local copy right away
			$access_rights = _getUserAccessRights($Auth->auth['user_id']);
			if(isset($access_rights['pages']['no_signoff'])) {
				structure_setStateID($page_id,PAGE_HASLOCALCOPY);
				$page_id = version_createLocalCopy($page_id);
			}
		} else if($mode == 'edit') {
			$page_id = isset($_POST['pageID']) ? $_POST['pageID'] : $_GET['page_id'];
			
			## check if the page is locked by another user
			if(lock_pageislocked($page_id,$user_id)) {
				## then we need to display an error message- and exit 
				ui_output_confirm(LANG_VERSION_PageLocked,LANG_VERSION_PageLocked_Desc,"matrix_menu.php");
				exit;
			}
		}
		
		## now lock the page for editing
		lock_lockpage($page_id,$user_id);
		
		if($mode == 'edit') {
			## the page is locked- now we should check if we need to create a new version
			## if yes, we will create a new copy of the page and set the page id accordingly
			$access_rights = _getUserAccessRights($Auth->auth['user_id']);
			if(isset($access_rights['pages']['no_signoff'])) {
				## set the status in the structure
				structure_setStateID($page_id,PAGE_HASLOCALCOPY);
				## okay we need to create a copy
				$page_id = version_createLocalCopy($page_id);
			} else {
				## okay we need to check if we need a signoff first
				$local_id = version_checkIfLocalCopyExists($page_id);
				## if yes display a dialog box with the options: singoff, nosignoff, cancel
				if($local_id > 0) {
					## generate the urls first
					$cancelURL = 'page_editor.php';
					$cancelURL = $gSession->url($cancelURL);
				
					## no - means we will discard the changes
					$noURL = 'admin.php?op=discardChangedPage&page_id='.$page_id;
					$noURL = $gSession->url($noURL);
				
					## no - means we will discard the changes
					$yesURL = 'admin.php?op=mergeChangedPage&page_id='.$page_id;
					$yesURL = $gSession->url($yesURL);
					
					$buttons[1] = array(image=>'cancel',url=>$cancelURL);
					$buttons[2] = array(image=>'nein',url=>$noURL);
					$buttons[3] = array(image=>'ja',url=>$yesURL);
					module_ui_displayDialogThreeOptions(LANG_VERSION_VersionExistsTitle,LANG_VERSION_VersionExistsDesc,$buttons);
					exit;
				}
			}		
		}
		
		if($type == 'folder') {			 
			## we need to display the input form
			folder_editFolder($page_id,$mode);
		} else {
			display_input_form($templateID,$page_id,$mode);
		}
		break;		
 	
 	case 'discardChangedPage':
 		## okay we need to get the base pageid and delete the copies acordingly
 		$page_id = intval($_GET['page_id']);
 		
 		if($page_id > 0) {
 			## okay get the localCopy
 			$local_id = version_checkIfLocalCopyExists($page_id);
 			
 			## okay now we will delete the page
 			if($local_id > 0) {
 				page_deletePage($local_id);
 				version_removeLocalCopy($page_id);
 			}
 		}

		structure_unsetStateID($page_id,PAGE_HASLOCALCOPY);

 		## okay we are done- so we will proceed normally in editing this page
		$target = 'admin.php?op=editpage&mode=edit&page_id='.$page_id;
		$target = $gSession->url($target);
		## redirect the browser
		header("Status: 302 Moved Temporarily");
		header("Location: ".SITE.$target);
		exit;
 	
 		break;
 
  	case 'mergeChangedPage':
 		## okay we need to get the base pageid
 		$page_id = intval($_GET['page_id']);
 		
 		if($page_id > 0) {
 			## okay get the localCopy
 			$local_id = version_checkIfLocalCopyExists($page_id);
 			
 			## merge the page
 			page_mergePage($local_id,$page_id);
 			
 			## remove the local copy
 			page_deletePage($local_id);
 			version_removeLocalCopy($page_id);
 			
 			## finally remove the cache file
 			pagecache_deleteCacheFile($page_id);
 		}

		structure_unsetStateID($page_id,PAGE_HASLOCALCOPY);

 		## okay we are done- so we will proceed normally in editing this page
		$target = 'admin.php?op=editpage&mode=edit&page_id='.$page_id;
		$target = $gSession->url($target);
		## redirect the browser
		header("Status: 302 Moved Temporarily");
		header("Location: ".SITE.$target);
		exit;
 	
 		break;
 
 	case "store":
 		## vars needed: PAGEID, TEMPLATEID, MODE
		$page_id 	= $_POST['pageID'];
		$mode		= $_POST['mode'];
	
 		## depending on the mode, we need to call
 		## the store page function and then
 		## a) display the page_name input OR b) preview the page

		## store the page
 		page_storePage();
 		
 		## first try to get the original page
 		$original_page = version_getOriginalPage($page_id);
 		## okay we can unlock the page
 		lock_unlockpage($original_page);
 		
 		## now process the next step
 		if($mode == 'edit') {
 			## prepare the url
			$target = SITE_ROOT.'preview.php?page_id='.$page_id;
			$target = $gSession->url($target);
			
 			## confirm the storage and preview the page
 			ui_output_confirm(LANG_PageSaved,LANG_PageSavedDescription,"matrix_menu.php",$target);
			exit;
 		} else if($mode == 'create') {
 			## we are still creating a page- so
 			## we need to rename the structure entry
 			
 			## get the entry, and display the naming page
 			$structureInfo = structure_getStructureID($page_id);
 			structure_editText($structureInfo['id']);
 			
 			$access_rights = _getUserAccessRights($Auth->auth['user_id']);
 			if(isset($access_rights['pages']['auto_signoff'])) {
 				structure_toggleState($structureInfo['id'],PAGE_INACTIVE+PAGE_ACTIVE);	
 			}

 		}
 		break;
 
 	case "store_element":
		## store the page
 		page_storePage();

 		## get the element
 		echo page_getDataByIdentifier(intval($_POST['pageID']),$_POST['id']);

 		break; 			
  	case "editor":
 		## thiscommand is called whenever we need to
 		## open or close an editor window
		## we need to get the page info
		$type		= isset($_POST['type']) ? $_POST['type'] : (isset($_GET['type']) ? $_GET['type'] : null);

		if($type == 'folder') {
			$page_id 	= $_POST['pageID'];
			$templateID = $_POST['templateID'];
			$mode		= $_POST['mode'];					 
			folder_editFolder($page_id,$mode);
		} else {
			$input_language = $_POST['save_language'];
		
			## we first store the page
			page_storePage();
			
			$input_language = $_POST['language'];
			
			## after storing, we redisplay the input form
			$page_id 	= $_POST['pageID'];
			$templateID = $_POST['templateID'];
			$mode		= $_POST['mode'];
			
			display_input_form($templateID,$page_id,$mode);	
		}	 		
 		break;
 	
 	case "requestApproval":
 		## the user requested approval- so we will display the input form
 		## to allow the user to enter some comments on the changes he made
 		$page_id = intval($_POST['pageID']);
 		## store the page
 		page_storePage();
 		
 		## prepare the hidden fields
 		$hiddenfields = array('op'=>'DoRequestApproval','page_id'=>$page_id);
 		ui_output_commentField('Request approval','Please enter a short description of your changes below','admin.php',$hiddenfields); 	
 		break;
 		
 	case "DoRequestApproval":
 		## prepare the data
 		## the supplied page id is the local copy_id
 		$local_id = intval($_POST['page_id']);
 		$comment = addslashes($_POST['comment']);
 		$user_id = $Auth->auth['user_id'];

 		$page_id = version_getOriginalPage($local_id);
		## now lock the page using the system user id: -1
		lock_lockpage($page_id,-1); 		
		## now we need to store the comments, mark the local copy etc.
		version_requestApproval($page_id,$comment,$user_id,time());

		## update the page_flag to show we have requested approval
		structure_setStateID($page_id,PAGE_APPROVAL_REQUESTED);
		## finally display a confirmation dialog
		ui_output_confirm('Approval','Your approval request has been submitted.',"matrix_menu.php");

 		break; 	
 	case "preview":
 		## this is the preview of the page
		$page_id 	= $_GET['page_id'];		
		
		## prepare the url
		$target = 'preview.php?page_id='.$page_id.'&language='.$input_language;
		$target = $gSession->url($target);
		## redirect the browser
		header("Status: 302 Moved Temporarily");
		header("Location: ".SITE_ROOT.$target);
		exit;		
		
		break;
  	case "store_page":
		## this means the user has decided to add an element
		## we store the page info, and refresh the page 
		page_storePage();
		display_input_form($template_id,$page_id, $low_sub,"choose_menu");
		break; 		 		 
	case "create_folder":
		$parent 	= $_GET['parent'];
		## this is the starting point for the folder creation process
		## for now this only works on the root level (parent is ignored)
		$folder_id = folder_createFolder(LANG_NoName);
		## register the folder in the menu_structure
		$menu_id = structure_storePage($folder_id,$parent,8);
		
		## rename the folder
		structure_editText($menu_id);
		break;
 	
 	case "store_folder":
 		$page_id 	= isset($_POST['pageID']) ? $_POST['pageID']: $_GET['pageID'];
 		folder_storeFolder($page_id);
 		ui_output_confirm(LANG_PageSaved,LANG_PageSavedDescription,"matrix_menu.php");
 		break;				
 		  	
  	case "showTemplate":
  		$page_id 	= intval($_GET['page_id']);
		page_showPageInfo($page_id);
		break;	  		
 		  	
  	case "editTimer":
  		$page_id 	= intval($_GET['page_id']);
  		$edit_element 	= intval($_GET['edit']);
		page_showPageTimer($page_id,$edit_element);
		break;	
  	case "storeEvent":
  		$page_id 	= intval($_GET['page_id']);
		var_dump($_POST);
		break;		
	case "choose_menu":
		## another bad name for an action		
		## prepare the db-object
		page_storePage();
		$menu_id = structure_storePage($page_id,$low_sub);
		display_namepage($menu_id, $menu_text,$actionURL);			
		break;
 	
  	case "move":
		structure_promptMove($_GET['page_id']);
		break;	 
 	
  	case "movedisplaystructure":
  		## display the websites structure- the user can select a new parent page
		## first get the page structure
		$menuItems = structure_getStructure();
		
		$varsToBeSet["Session"] = $gSession->id;
		$varsToBeSet["op"] 		= 'domove';
		$varsToBeSet["pageToBeMoved"] = $_GET['page_id'];
		
		$targetURL="admin.php?op=movedisplaystructure&page_id=".$_GET['page_id'];
		pageselector_drawMenu($menuItems,$varsToBeSet,$targetURL);

		break;
  	case "domove":
  		## now we have to page ids- so we actually move the page
  		structure_MovePage($_POST['pageToBeMoved'],$_POST['selected_page']);
  		
  		## close the window- and reload a confirmation screen
		$targetURL = 'admin.php?op=confirmmove';
		$targetURL = $gSession->url($targetURL);
  		close_reload($targetURL);
		break;		
	case "confirmmove":
		ui_output_confirm(LANG_MovePageSuccess,LANG_MovePageSuccessDesc,"matrix_menu.php");
		break;
		
   case "display_input_form":
      display_input_form($template_id,$page_id, $low_sub,$cmd);
      break;
      		      
    case "update_page_menu":
    	$menu_id = intval($_POST['menu_id']);
    	$menu_text = $_POST['menu_text'];
    
      	update_page_menu($menu_id, $menu_text);
      ui_output_confirm(LANG_PageSaved,LANG_PageSavedDescription,"matrix_menu.php");
      break;			
	
	
	case "make_homepage":
		## get the menu_id
		$menu_id = $_GET['menu_id'];
 		
 		$hiddenfields = array('op'=>'DoMakeHomepage','menu_id'=>$menu_id);
 		ui_output_Dialog('Homepage','Please enter a short description of your changes below','admin.php',$hiddenfields); 	
		break;		           
	case "DoMakeHomepage":
		$menu_id = intval($_POST['menu_id']);
	
		structure_setHomepage($menu_id);
		ui_output_confirm(LANG_Homepage,LANG_HomepageDescription,"matrix_menu.php");
		break;
 
     case "copyPage":
		## get the menu_id
		$page_id = $_GET['page_id'];
 		
 		$hiddenfields = array('op'=>'DoCopyPage','page_id'=>$page_id);
 		ui_output_Dialog(LANG_MENU_COPYPAGE,LANG_CopyPageDesc,'admin.php',$hiddenfields);
   		break;
      
      
    case "DoCopyPage":
    	$page_id = intval($_POST['page_id']);

		## we need to handle folders differently- so try to find out what the selected page is
		$pageInfo = page_getPageInfo($page_id);
		
		if($pageInfo['type'] == 'folder') {
			## okay we need to make a new folder
			$new_page = folder_createFolder(LANG_NoName);
		} else {
			## copy this page
			$new_page = page_copyPage($page_id);
		}
		
		## we need to get the page name and the structure parent
		$pageInfo = structure_getStructureID($page_id);
		
		## register the page in the structure
		$menu_id = structure_storePage($new_page,$pageInfo['parent'],PAGE_INVISIBLE+PAGE_INACTIVE);
			
		## finally rename the page
		structure_setPageName($menu_id,$pageInfo['text'].'_copy');

		## and confirm the changes ( updates the main menu as well)
      	ui_output_confirm(LANG_MENU_COPYPAGE,LANG_CopyPageSuccess,"matrix_menu.php");
      break;

 
     case "copyStructure":
		## get the menu_id
		$page_id = $_GET['page_id'];
 		
 		$hiddenfields = array('op'=>'DoCopyStructre','page_id'=>$page_id);
 		ui_output_Dialog(LANG_MENU_COPYSTRUCTURE,LANG_CopyStructureDesc,'admin.php',$hiddenfields);
   		break;
      
      
    case "DoCopyStructre":
		## prpare the root page id
    	$page_id = intval($_POST['page_id']);
    	
    	## get the structure id
		$pageInfo = structure_getStructureID($page_id);			
		$menu_id = $pageInfo['id'];

		## okay here we copy the page
		$new_menu_id = structure_copyPage($page_id,null,'_copy');
		
		## finally recursively copy all pages
		structure_copySubPages($menu_id,$new_menu_id);		
		
		## and confirm the changes ( updates the main menu as well)
      	ui_output_confirm(LANG_MENU_COPYSTRUCTURE,LANG_CopyStructureSuccess,"matrix_menu.php");
      break;      
    
    case "menu_prompt_delete":
    	$menu_id = intval($_GET['menu_id']);
    	$page_id = intval($_GET['page_id']);
		structure_promptDelete($menu_id,$page_id);
		break;
      
      
    case "menu_delete":
    	$menu_id = intval($_GET['menu_id']);
    	$page_id = intval($_GET['page_id']);
		
		## delete the page form the structure
		$has_nosubpages = structure_deletePage($menu_id);

      	if($has_nosubpages>0) {
      		page_deletePage($page_id);
      		
      		## delete the potentially existing localcopies
 			$local_id = version_checkIfLocalCopyExists($page_id);
 			
 			## okay now we will delete the page
 			if($local_id > 0) {
 				page_deletePage($local_id);
 				version_removeLocalCopy($page_id);
 			}      		
      		ui_output_confirm(LANG_DeletePage,LANG_DeletePageSuccess,"matrix_menu.php");
      	} else {
      		ui_output_confirm(LANG_DeletePage,LANG_DeletePageSubpages,"matrix_menu.php");
		}
      break;
      
    case "menu_active":
      structure_toggleState(intval($_GET['menu_id']),PAGE_ACTIVE);	
      ui_output_confirm(LANG_Visibility,LANG_VisibilitySuccess,"matrix_menu.php");
      break;
      
     case "change_state":
      structure_toggleState(intval($_GET['menu_id']),PAGE_INVISIBLE);	
      ##change_state($menu_id);
      ui_output_confirm(LANG_Visibility,LANG_VisibilitySuccess,"matrix_menu.php");
      break;     
      
    case "edit_menu_item":
      $menu_id = intval($_GET['menu_id']);
      structure_editText($menu_id);
      break;
      
            
	case "order_menu":
		global $_PAGE_SORTOPITIONS;
		$menu_id = intval($_GET['menu_id']);
		## check the number of modules
		if(count($_PAGE_SORTOPITIONS) > 1) {
			sort_promptSortMethod($menu_id,$_PAGE_SORTOPITIONS);
		} else {
			## otherwise call the method directly
			sort_executeModules($_PAGE_SORTOPITIONS[0]);
		}
		break;
      
    case "update_menu_order":
    	$sort_method = isset($_POST['sortmethod']) ? $_POST['sortmethod'] : (isset($_GET['sortmethod']) ? $_GET['sortmethod'] : '');
		sort_executeModules($sort_method);
		break;
		
	// nkowald - 2009-10-07 - Adding new case for page redirections	
	case "redirect_page":
		$page_id = intval($_GET['page_id']);
		redirect_displayForm($page_id);
		break;
		
	case "redirect_page_update":
		if (isset($_POST)) {
			redirect_updateRedirect($_POST);
			
			// Now we can display the updated form
			$page_id = intval($_POST['page_id']);
			redirect_displayForm($page_id);
			
		} else {
			redirect_displayForm($page_id);
		}
		break;
      
    default:
      ## we should display an error- or redirect the user to a certain page
		## prepare the url
		$target = 'main.php';
		$target = $gSession->url($target);
		## redirect the browser
		header("Status: 302 Moved Temporarily");
		header("Location: ".SITE_ROOT.'matrix_engine/'.$target);
		exit;      
      break;
    }


?>
