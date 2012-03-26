<?php
	require("../config.php");
	require("functions/group.php");
	## first get all accessrights for this user
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);

	## then we need to check if we are allowed to access this module
	if($access_rights['users']['edit_own']['access'] != 1) {
		if ($access_rights['users']['edit_all']['access'] != 1) {
			## we don't have access to the users -> display the error message
			$inputFile = "empty_menu.tpl";
			$input_template = new Template(INTERFACE_DIR);
			$input_template->set_templatefile(array("body" => $inputFile));
			## the next step is to ouput the head
			$input_template->pfill_block("body");
			exit;
		}
	} else {
		## we are only allowed to edit our own group
		$allowed_group =  $access_rights['group_id'];
	}	
		
	## if a group is open, we will recieve the group_id here
	$open_groups = isset($_GET['p']) ? explode("|",$_GET['p']) :  array();

	$img_expand    = "interface/images/menu/menu_corner_plus.gif";
	$img_collapse  = "interface/images/menu/menu_corner_minus.gif";
	$img_space     = "interface/images/menu/blank.gif";  

	## prepare the template file
	$layout_template = new Template('interface/');
	$layout_template->set_templatefile(array("head" => "usermenu.tpl","spacer" => "usermenu.tpl","expand" => "usermenu.tpl","element" => "usermenu.tpl","lastelement" => "usermenu.tpl","foot" => "usermenu.tpl"));
	
	$layout_template->set_var('url','');
	
	$newpageIMG = "lang/".$Auth->auth["language"]."_neueruser.gif";
	$targetURL = 'user.php';          
	$targetURL = $gSession->url($targetURL);
	$layout_template->set_var("targetURL",$targetURL);
	
	if(isset($allowed_group)) {
		$topnav = '<img src="interface/images/empty.gif" width="269" height="22" border="0">';
	} else {
		$topnav = '<a href="'.$targetURL.'&op=new_group" target="text"><img src="interface/'.$newpageIMG.'" width="269" height="22" border="0"></a>';
	}
	$layout_template->set_var("TOPNAV",$topnav);
	
	$menuURL = 'groups.php';          
	$menuURL = $gSession->url($menuURL);
	## here we stzart to ouptut the whole date
	$layout_template->pfill_block("head");
		
	$addition = isset($allowed_group) ? ('group_id='.$allowed_group.' AND') : '';


	## get all groups
	$groups = group_getAllGroups();
	
	$items_displayed=2;
	foreach($groups as $group_id => $group_name) {
		## get the members for this group
		$group_members = group_getGroupMembers($group_id);
		$group_memberCount = count($group_members);
		
		$spacer = '';
		$member_output = '';
		$subitems = 0;
		if($group_memberCount>0) {
			$spacer = 'expand';
			## this means we need to handle the collaps/expand functionality		
			if(in_array($group_id,$open_groups)) {
				$expand	 = $img_collapse;
				$menuParameters = group_utilityPrepareExpandOptions($open_groups,$group_id);
				## in this case we are open and need to output our members
				$member_output = group_outputMembers($group_members,$items_displayed+1);	

				$subitems = $group_memberCount;	
			} else {
				$expand = $img_expand;
				$menuParameters = group_utilityPrepareExpandOptions($open_groups,-1).$group_id.'|';
			}
			$layout_template->set_var("menuURL",$menuURL.'&'.$menuParameters);
			$layout_template->set_var("EXPAND",$expand);
		} else {
			## just output the name and an spacer element
			$spacer = 'spacer';
		}
		
		$layout_template->set_var("text",$group_name);
		$layout_template->set_var("groupid",$group_id);
		$layout_template->set_var("id",$items_displayed);
		
		$layout_template->pfill_block($spacer);
		$layout_template->pfill_block("element");
		
		## and output the members
		echo $member_output;
		
		$items_displayed+=$subitems;
		$items_displayed++;
	}	


	$contextmenu_template = new Template(INTERFACE_DIR);
	$contextmenu_template->set_templatefile(array("menu" => "context_menu.tpl","element" => "context_menu.tpl","seperator" => "context_menu.tpl","menuend" => "context_menu.tpl"));
	
	$context_menu = "";
	$contextmenu_template->set_var("menuname","pulldown0");
	$context_menu .= $contextmenu_template->fill_block("menu");
			
	$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(1);");
	$contextmenu_template->set_var("itemname",LANG_MenuEdit);
	$context_menu .= $contextmenu_template->fill_block("element");
		
	$context_menu .= $contextmenu_template->fill_block("seperator");

	$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(0);");
	$contextmenu_template->set_var("itemname",LANG_MenuInfoNewUser);
	$context_menu .= $contextmenu_template->fill_block("element");


	if(!isset($allowed_group)) {
		$context_menu .= $contextmenu_template->fill_block("seperator");
			
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(2);");
		$contextmenu_template->set_var("itemname",LANG_MenuDelete);
		$context_menu .= $contextmenu_template->fill_block("element");
	}				
	$context_menu .= $contextmenu_template->fill_block("menuend");


	$contextmenu_template->set_var("menuname","pulldown1");
	$context_menu .= $contextmenu_template->fill_block("menu");
	
	$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(3);");
	$contextmenu_template->set_var("itemname",LANG_MenuEdit);
	$context_menu .= $contextmenu_template->fill_block("element");

	if(!isset($allowed_group)) {		
		$context_menu .= $contextmenu_template->fill_block("seperator");
		
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(4);");
		$contextmenu_template->set_var("itemname",LANG_MenuDelete);
		$context_menu .= $contextmenu_template->fill_block("element");
	}				
	$context_menu .= $contextmenu_template->fill_block("menuend");	

	
	$layout_template->set_var("menu",$context_menu);
	$layout_template->pfill_block("foot");



## =======================================================================        
##  group_outputMembers        
## =======================================================================        
##  generates a listing of members
##
## =======================================================================
function group_outputMembers($members,$items_displayed) {
	## first prepare the template
	$template = new Template('interface/');
	$template->set_templatefile(array("spacer" => "usermenu.tpl","lastelement" => "usermenu.tpl"));

	$member_output = '';
	$i=0;
	foreach($members as $current_member) {
		$member_output .=$template->fill_block('spacer');
		$member_output .=$template->fill_block('spacer');

		$template->set_var("text",$current_member['name']);
		$template->set_var("memberid",$current_member['id']);
		$template->set_var("groupid",$current_member['group']);
		$template->set_var("id",$items_displayed+$i);
		$member_output .=$template->fill_block('lastelement');
		$i++;
	}
	return $member_output;
}

## =======================================================================        
##  group_getGroupMembers        
## =======================================================================        
##  returns a list of members for a certain group
##
## =======================================================================
function group_getGroupMembers($group_id) {
	## get the client_id
	global $Auth;
	$client_id = $Auth->auth['client_id'];
	
	$db_user = new DB_Sql();  
	
	## then we should get the members of this group
	$query = "SELECT user_id,user_name,group_id FROM ".USERS." WHERE client_id='$client_id' AND group_id='$group_id' ORDER BY user_name";
	$db_user->query($query);
	
	$group_members = array();		
	$i = 0;	
	while($db_user->next_record()) {
		$group_members[$i]['id'] = $db_user->Record["user_id"];
		$group_members[$i]['name'] = $db_user->Record["user_name"];
		$group_members[$i]['group'] = $db_user->Record["group_id"];
		$i++;
	}
	return $group_members;
}


## =======================================================================        
##  group_utilityPrepareExpandOptions        
## =======================================================================        
##  utility functions
##
## =======================================================================
function group_utilityPrepareExpandOptions($options,$current_item) {
	## loop through all options
	$parameter = 'p=';
	foreach($options as $current_option) {
		if(($current_option != $current_item) && $current_option!="") {
			$parameter .= $current_option.'|';
		}
	}
	return $parameter;
}
?>