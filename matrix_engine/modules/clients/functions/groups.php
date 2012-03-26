<?php
## =======================================================================        
##  portlets_storePage        
## =======================================================================        
##  we store the base information for a certain template    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function clients_setupStoreGroup() {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## we need to prepare the input - needs to be done properly
	$groupname 	= $_POST['groupname'];
	$controlfile= $_POST['controlfile'];

	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	$lock_query = "LOCK TABLE ".DB_PREFIX."clients_groups write";
	$result_pointer = $db_connectionStore->query($lock_query);	
	
	## if we are editing the current client- we need to update
	if(isset($_POST['group']) && intval($_POST['group']) !=0) {
		$query = "UPDATE ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."groups SET groupname='$groupname', controlfile='$controlfile' WHERE id=".intval($_POST['group']);
		$result_pointer = $db_connectionStore->query($query);	
		$id = intval($_POST['client']);
	} else {
		$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']."groups (groupname,controlfile) values ('$groupname','$controlfile')";
		$result_pointer = $db_connectionStore->query($query);	
		$id = $db_connectionStore->db_insertid($result_pointer);
	}
	
	$lock_query = "UNLOCK TABLE";
	$result_pointer = $db_connectionStore->query($lock_query);

	return $id;
}


## =======================================================================        
##  clients_groupDisplayOverview        
## =======================================================================        
##  returns an array with all pages of a certain category    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function clients_groupDisplayOverview($id=null,$record=null) {
	global $Auth,$gSession;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	## okay let's use this file, as a template
	## prepare the template file- we changed to structure of the template file
	$inputFile = "setup_groupinput.tpl";
	$input_template = new Template(ENGINE.'modules/clients/interface');
	$input_template->set_templatefile(array("header" => $inputFile,"body" => $inputFile,"footer" => $inputFile));

	## language
	$input_template->set_var("saveIMG","../../interface/lang/".$Auth->auth["language"]."_button_save.gif");
	$input_template->set_var("backIMG","interface/lang/".$Auth->auth["language"]."_buttonoverview.gif");

	$input_template->set_var('language_header',LANG_MODULE_CLIENTS_SETUP_CreateTitle);
	$input_template->set_var('language_description',LANG_MODULE_CLIENTS_SETUP_CreateDesc);
	
	$actionURL = "groups.php";
	$actionURL = $gSession->url($actionURL);
	$input_template->set_var('actionURL',$actionURL);
	
	$backURL = "groups.php";
	$backURL = $gSession->url($backURL);
	$input_template->set_var('backURL',$backURL);
	
	## set the content of supplied
	if(isset($record)) {
		foreach($record as $key => $value) {
			$input_template->set_var($key,$value);
		}
	}
			
	## the next step is to ouput the head
	$input_template->pfill_block("header");
	$input_template->pfill_block("body");

	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="group" value="'.$id.'">';
	$output .=  '<input type="hidden" name="cmd" value="store">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$input_template->set_var("hiddenfields",$output);
	
	$input_template->pfill_block("footer");
}

?>