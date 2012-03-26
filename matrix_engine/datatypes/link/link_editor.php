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
##  linklist_promptDelete        
## =======================================================================        
function link_promptDelete($yesURL,$noURL) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(ENGINE."datatypes/link/interface");
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_LINKLIST_DeleteTitle);
	$select_template->set_var('language_doyouwant',LANG_LINKLIST_DeleteDesc);
	
	## grab the information for this page
  		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}	


## =======================================================================        
##  link_doDelete        
## =======================================================================        
function link_delete() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## we need a page id and an identifier
	$page_id = $_GET['page_id'];
	$identifier = $_GET['identifier'];
	
	$db = new DB_Sql();
	$query = "DELETE FROM ".PAGE_LINK." WHERE page_id='$page_id' AND identifier='$identifier' AND client_id='$client_id'";
	$rp = $db->query($query);
}
	
?>
