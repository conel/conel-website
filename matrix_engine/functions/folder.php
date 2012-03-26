<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 


## =======================================================================        
##  folder_createFolder       
## =======================================================================        
##  we create a new folder in here 
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function folder_createFolder($title) {
	global $Auth;
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db = new DB_Sql();
	$query = "INSERT INTO ".USER_PAGES." (title, template, type, created, modified, client_id) values ('$title', 0,'folder',now(),now(),'$client_id')";

	$rp = $db->query($query);	
	$folder_id = $db->db_insertid($rp);
		
	return $folder_id;
}
	
## =======================================================================        
##  folder_editFolder       
## =======================================================================        
##  displays the input form which allows to setup up
##
##  TODO:
##       - make it work
## =======================================================================        
function folder_editFolder($page_id,$mode) {
	global $gSession,$Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	if(!$page_id) {
		## we should return an error- how?
		## don't know yet
		exit();
	}

	## ======================================================================= 
	## some datatypes require additonal information so we need to setup some global vars
	## -> we need to handle this lately
	## ======================================================================= 
	global $g_pageID;
	global $gParentPage;
	global $gEditMode;
	
	## setup global vars to be used in special datatypes
	$g_pageID		= $page_id;
	$gParentPage	= $low_sub;
	$gEditMode		= $cmd;

	## setup up is complete, we can start processing the rest
	## ======================================================================= 

	
	## prepare the db-object
	$db_connectionMain = new DB_Sql(); 
		
	$inputFile = "master_input.tpl";
	$input_template = new Template(INTERFACE_DIR);
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"foot" => $inputFile));

	## language
	$input_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var('language_deleteelementdesc',LANG_DeleteElementDescription);
	$input_template->set_var('language_inputhead',LANG_EnterData);
	$input_template->set_var('language_inputbody',LANG_EnterDataDescription);
	
	$actionURL = "admin.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
		
	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");
		
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="store_folder">';
	$output .= '<input type="hidden" name="mode" value="'.$mode.'">';
	$output .= '<input type="hidden" name="type" value="folder">';
	$output .= '<input type="hidden" name="pageID" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);
	
	## we need to fix this- provide a hardcoded verions of the objects list
	$page_record = page_getPage($page_id,array('ALIAS'=>true));		


	## check for the file 
	@include_once("datatypes/extra_alias/alias.php");

	## now we check if the function exists
	if(function_exists("alias_displayInput")) {
		## no we call the function
		eval("\$element = alias_displayInput(\$elements[\$counter],\$page_record[\$element_name]);");
		## output the input form					
		print $element;
	}
		$input_template->pfill_block("foot");
}

## =======================================================================        
##  folder_storeFolder       
## =======================================================================        
##  this is used for outputinh the content of the folder
##
##  TODO:
##       - make it work
## =======================================================================        
function folder_storeFolder($page_id) {
	$record = array();
	## first we should check if we have recieved any data
	@include_once("datatypes/extra_alias/alias.php");
	## now we check if the function exists
	
	if(function_exists("alias_getData")) {
		## get the data
		alias_getData($page_id,$record);
	}

	if($record[""][0]["page_id"] > 0) {
		## we need to set the flag of this page
		structure_setPageFlagsID($page_id,2);
	} else {
		structure_setPageFlagsID($page_id,PAGE_INVISIBLE);
	}
}

## =======================================================================        
##  folder_outputFolder       
## =======================================================================        
##  this is used for outputinh the content of the folder
##
##  TODO:
##       - make it work
## =======================================================================        
function folder_outputFolder($page_id) {
	## prepare the data
	$offset  = isset($_GET['offset']) ? intval($_GET['offset']) : null;
	
	$record = array();
	## first we should check if we have recieved any data
	@include_once(ENGINE."datatypes/extra_alias/alias.php");
	## now we check if the function exists
	
	if(function_exists("alias_getData")) {
		## get the data
		alias_getData($page_id,$record);
	}

	if($record[""][0]["page_id"] > 0) {
		print page_generatePage($record[""][0]["page_id"],$offset);
	}
}
?>
