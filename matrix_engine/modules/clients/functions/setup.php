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

	## multiclient
	$client_id = $Auth->auth["client_id"];	

	## get the template for now
	$template2 = new Template(ENGINE."modules/clients/interface/");
	$template2->set_templatefile(array("portlets" => "filtersetup.tpl","portlets_header" => "filtersetup.tpl","portlets_row" => "filtersetup.tpl","portlets_foot" => "filtersetup.tpl"));


	## get the template for now
	$template = new Template(ENGINE."modules/clients/interface/");
	$template->set_templatefile(array("head" => "filtersetup.tpl","intro" => "filtersetup.tpl","foot" => "filtersetup.tpl"));
	
	$self = $gSession->url($_SERVER['PHP_SELF']);

	## header and intro element
	$template->set_var('LANG',$Auth->auth['language']);
	$template->set_var('language_inputhead',LANG_MODULE_CLIENTS_SETUP_Title);
	$template->set_var('language_inputbody',LANG_MODULE_CLIENTS_SETUP_Desc);

	## frist setup the back link to the normal page
	$actionURL = "module.php";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('overviewURL',$actionURL);	
	
	
	## specify the target of this page
	$actionURL = "groups.php";
	$actionURL = $gSession->url($actionURL);
	$template->set_var('actionURL',$actionURL);


	## let's set the command
	$output =  '<input type="hidden" name="op" value="">';
	$output .=  '<input type="hidden" name="cmd" value="">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$template->set_var("hiddenfields",$output);
	
	$template->pfill_block("head");
	$template->pfill_block("intro");
	## we need to preapre the action links for the portlets ui
	$addlinkURL = "groups.php?cmd=create";
	$addlinkURL = $gSession->url($addlinkURL);	
	$template2->set_var('addlinkURL',$addlinkURL);
	
	$filters = clients_getFilters();
	
	## output the header		
	$template2->set_var('GroupsTitle',LANG_MODULE_CLIENTS_SETUP_GroupsTitle);
	$template2->set_var('GroupsSection',LANG_MODULE_CLIENTS_SETUP_GroupsSection);
	$template2->set_var('UsersSection',LANG_MODULE_CLIENTS_SETUP_UsersSection);	

	$template2->pfill_block("portlets");		

	$editURL = "groups.php?cmd=edit";
	$editURL = $gSession->url($editURL);	
	
	##$editURL = $gSession->url($editURL);	
	$template2->pfill_block("portlets_header");

	## prepare the db-object
	$db = new DB_Sql();
		
	$counter = 0;		
	foreach($filters as $current_filter) {
			## set all database fields
			foreach($current_filter as $key => $value) {
				$template2->set_var($key,$value);
			}
			
			## we need to get the number of users that match thif filter- that is wwhy we need to execute it
			$query = $current_filter['query'];
			$rp 	= $db->query($query);
			$max_entries = $db->num_rows();
			
			$template2->set_var('usercount',intval($max_entries));
							
			$template2->set_var('editURL',$editURL.'&filter='.$current_filter["id"]);
			
			if($current_filter['visible'] == 1) {
				$template2->set_var('visible','<img src="../../interface/images/icon_checked.gif" width="20" height="15">');
			} else {
				$template2->set_var('visible','<img src="../../interface/images/blank.gif" width="20" height="15">');
			}
			$template2->set_var('identifier_id',$counter);
			$template2->set_var('group_id',$current_filter["id"]);		
			$template2->pfill_block("portlets_row");
			
			$counter++;
	}
	$template2->pfill_block("portlets_foot");
	
	$template->pfill_block("foot");
}

?>