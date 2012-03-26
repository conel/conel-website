<?PHP

## =======================================================================        
##  show_group_input        
## =======================================================================        
##  shows the appropriate Input Form for a group
##
##  TODO:  
## =======================================================================
function show_group_input($group_id) {
	global $gSession,$Auth;
	global $allowed_group;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$inputFile = "group_input.tpl";
	$input_template = new Template('interface/');
	$input_template->set_templatefile(array("head" => $inputFile,"intro" => $inputFile,"body" => $inputFile,"foot" => $inputFile,"user_element" => $inputFile));

	$input_template->set_var('language_userhead',LANG_GroupEnterData);
	$input_template->set_var('language_userbody',LANG_GroupEnterDataDescription);

	$input_template->set_var('language_userinputname','<b>'.LANG_GroupName.'</b>');
	$input_template->set_var('language_userinputnamedesc',LANG_GroupNameDesc);
	
	$input_template->set_var('language_pageaccessrights',LANG_GroupPageAccess);
	$input_template->set_var('language_workspace',LANG_GroupPageAccessDesc);
	
	$input_template->set_var('language_templates',LANG_GroupTemplate);	
	$input_template->set_var('language_edittemplates',LANG_GroupTemplateOption);
	
	$input_template->set_var('language_signoff',LANG_GroupSignoff);
	$input_template->set_var('language_signoff_auto',LANG_GroupSignoffAuto);
	$input_template->set_var('language_signoff_normal',LANG_GroupSignoffNorm);
	$input_template->set_var('language_signoff_no',LANG_GroupSignoffNo);
	
	$input_template->set_var('LANG_GroupUsers',LANG_GroupUsers);
	$input_template->set_var('LANG_GroupUsersAllGroups',LANG_GroupUsersAllGroups);
	$input_template->set_var('LANG_GroupUsersOwnGroups',LANG_GroupUsersOwnGroups);
	$input_template->set_var('LANG_GroupUsersNoGroups',LANG_GroupUsersNoGroups);

	$input_template->set_var('Session',$gSession->id);
	$input_template->set_var('template_id',$template_id);
	$input_template->set_var('page_id',$page_id);
	$input_template->set_var('group_id',$group_id);
	$input_template->set_var('site',SITE);

	$input_template->set_var("SAVEIMG","lang/".$Auth->auth["language"]."_button_save.gif");

	$targetURL = "user.php";
	$targetURL = $gSession->url($targetURL);
	$input_template->set_var('targetURL',$targetURL);
	
	$input_template->set_var('op',"update_group");
	
	## the next step is to ouput the head
	$input_template->pfill_block("head");
	$input_template->pfill_block("intro");	
	
	## we need to get all workspace entries
	$workspace_entries = getWorkspaces();
	
	if(!$group_id) {
		## we are creating a new group
		$workspaces = '<option label="'.LANG_GroupPageAccessAllPages.'" value="-1">'.LANG_GroupPageAccessAllPages.'</option>';
		$workspaces .= '<option label="'.LANG_GroupPageAccessNoPages.'" value="0">'.LANG_GroupPageAccessNoPages.'</option>';
		foreach($workspace_entries as $current_page) {
			$workspaces .='<option label="'.$current_page['text'].'" value="'.$current_page['page_id'].'"></option>';		
		}
		$input_template->set_var("workspaces",$workspaces);
		$user_element = $input_template->fill_block("user_element");
		$input_template->set_var("user_elements",$user_element);		
		
		$input_template->pfill_block("body");
	} else {
		## prepare the db-object
		$db_connection = new DB_Sql();
					
		## get the access rights for this group
		$group_access_rights = _getGroupAccessRights($group_id);

		## now process the rights foreach workspace
		$selected_workspace = array();
		if(isset($group_access_rights['pages']['workspace'])) {
			foreach($group_access_rights['pages']['workspace'] as $num=>$text) {
				$selected_workspace[] = $num;
			}
		}
		
		## let's handle the workspace
		if(count($selected_workspace) == 0) {
			$workspaces = '<option label="'.LANG_GroupPageAccessNoPages.'" value="-1" selected>'.LANG_GroupPageAccessNoPages.'</option>';
		} else {
			$workspaces = '<option label="'.LANG_GroupPageAccessNoPages.'" value="-1">'.LANG_GroupPageAccessNoPages.'</option>';
		}
		
		if(in_array(0,$selected_workspace)) {	
			$workspaces .= '<option label="'.LANG_GroupPageAccessAllPages.'" value="0" selected>'.LANG_GroupPageAccessAllPages.'</option>';
		} else {
			$workspaces .= '<option label="'.LANG_GroupPageAccessAllPages.'" value="0">'.LANG_GroupPageAccessAllPages.'</option>';
		}	
		foreach($workspace_entries as $current_page) {

			if(in_array($current_page['id'],$selected_workspace)) {
				$workspaces .='<option label="'.$current_page['text'].'" value="'.$current_page['id'].'" selected>'.$current_page['text'].'</option>';
			} else {
				$workspaces .='<option label="'.$current_page['text'].'" value="'.$current_page['id'].'">'.$current_page['text'].'</option>';
			}			
		}
		$input_template->set_var("workspaces",$workspaces);


		## now process the rights for the templates
		if(isset($group_access_rights['template'])) {
			$input_template->set_var("templates_add",'checked');
		}
		
		## now process the rights for the groups
		## we need to cover the case: user can edit his group-
		## we need to avoid that he can set his own group to edit
		## all groups
		$user_element = '';
		$access_rights = _getUserAccessRights($Auth->auth['user_id']);

		if($access_rights['users']['edit_all']['access'] == 1) {
			## then we just don't output the user elements
			if(isset($group_access_rights['users'])) {
				## check if we are allowed to edit all groups
				if(isset($group_access_rights['users']['edit_all'])) {
					$input_template->set_var("allgroup_edit",'checked');
				} else if(isset($group_access_rights['users']['edit_own'])) {
					$input_template->set_var("owngroup_edit",'checked');
				} else if(isset($group_access_rights['users']['no_edit'])) {
					$input_template->set_var("nogroup_edit",'checked');
				}	
			}
			$user_element = $input_template->fill_block("user_element");	
		}
		
		$input_template->set_var("user_elements",$user_element);
		## now process the sign-off option
		$set = false;
		if(isset($group_access_rights['pages'])) {
			if(isset($group_access_rights['pages']['auto_signoff'])) {
				$input_template->set_var("auto_signoff",'checked');
				$set = true;
			} else if(isset($group_access_rights['pages']['signoff'])) {
				$input_template->set_var("signoff",'checked');
				$set = true;
			} else if(isset($group_access_rights['pages']['no_signoff'])) {
				$input_template->set_var("no_signoff",'checked');
				$set = true;
			}
		}
		
		if(!$set) {
			$input_template->set_var("auto_signoff",'checked');
		}		
	

		// handle the menu option
		$menu_options = array('New subpage','New folder','Edit','Preview','Rename','Sort','Move','Copy Page','Copy Structure','Deactivate/Activate','Show/Hide in menu','Homepage','Information','Delete');
		
		## loop through all modules
		$modules = '';
		$items_row = 0;
		foreach($menu_options as $key =>$current_menu) {
			## call each module and ask it about its name
			if($items_row % 2 == 0) {
				$modules .= '<tr>';
			}
			
			if(isset($group_access_rights['menu_'.$current_menu])) {
				$status = 'checked';
			} else {
				$status = '';
			}

			$modules .= '<td align="left" valign="top"><p><input type="checkbox" name="menu_'.$key.'" value="'.$key.'" '.$status.'><br></p></td><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="2" border="0"></td><td align="left" valign="top"><p>'.$current_menu.' <br></p></td>';
			if($items_row % 2) {
				$modules .= '<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>';
			}			
			$items_row++;
			
			if($items_row % 2 == 0) {
				$modules .= '</tr><tr><td align="left" valign="top" colspan="7"><img src="interface/images/blank.gif" alt="" width="170" height="5" border="0"></td></tr>';
			}			
			
		}
		$input_template->set_var("menu",$modules);



	
		## okay we need to get all installed modules
		global $installed_modules;
		
		## loop through all modules
		$modules = '';
		$items_row = 0;
		foreach($installed_modules as $key =>$current_module) {
			## call each module and ask it about its name
			if($items_row % 2 == 0) {
				$modules .= '<tr>';
			}
			
			if(isset($group_access_rights[$current_module])) {
				$status = 'checked';
			} else {
				$status = '';
			}

			$modules .= '<td align="left" valign="top"><p><input type="checkbox" name="module_'.$key.'" value="'.$key.'" '.$status.'><br></p></td><td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="5" height="2" border="0"></td><td align="left" valign="top"><p>'.$current_module.' <br></p></td>';
			if($items_row % 2) {
				$modules .= '<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="1" height="1" border="0"></td>';
			}			
			$items_row++;
			
			if($items_row % 2 == 0) {
				$modules .= '</tr><tr><td align="left" valign="top" colspan="7"><img src="interface/images/blank.gif" alt="" width="170" height="5" border="0"></td></tr>';
			}			
			
		}
		$input_template->set_var("modules",$modules);
	
		## get the basic information about this group
		$groupInfo = _groupGetInfo($group_id);
		
		$input_template->set_var("user_name",$groupInfo['name']);
		$input_template->set_var("user_id",$groupInfo['group_id']);
		
		$input_template->pfill_block("body");
	}	

	## we need to set the ids and stuff
	$input_template->pfill_block("foot");
}

## =======================================================================        
##  _group_UpdateGroup        
## =======================================================================         
##  internal: Updates a groups name, or creates a new group
##  with the specified name
## =======================================================================
function _group_UpdateGroup($name,$group_id=0) {
	$db_connection = new DB_Sql();

	## first we check if there is a record 
	$query = "SELECT name FROM ".GROUPS." WHERE group_id='$group_id'";
	$result_pointer = $db_connection->query($query);

	if($db_connection->next_record()) {
		## okay the group is already there, so let's update it
		$query = "UPDATE ".GROUPS." SET name='".$name."' WHERE group_id='$group_id'";
		$result_pointer = $db_connection->query($query);
	} else {
		## now group found using the id. Let's create a new one
		$lock_query = "LOCK TABLE ".GROUPS." WRITE";
		$result_pointer = $db_connection->query($lock_query);
			
		## first we will update the name
		$query = "INSERT INTO ".GROUPS." (name) VALUES ('$name')";
		$result_pointer = $db_connection->query($query);
		$group_id    = $db_connection->db_insertid($result_pointer);
	
		$lock_query = "unlock table";
		$result_pointer = $db_connection->query($lock_query);	
	}	
	
	## clean up db
	$db_connection->free();
	
	return $group_id;
}

## =======================================================================        
##  update_group        
## =======================================================================         
##  updates an existing group    
## =======================================================================
function update_group($group_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$db_connection = new DB_Sql();
	
	$name = addslashes($_POST['user_name']);
	$group_id = _group_UpdateGroup($name,$group_id);
	
	## to make updating more easier, we delete all entries
	$query = "DELETE FROM ".ACCESS." WHERE group_id='$group_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	
	## first we create the entries for the workspaces
	## special values: -1 no pages...0 all pages
	if(isset($_POST['workspace'])) {
		$workspace = $_POST['workspace'];
		foreach($workspace as $current_workspace) {
			if($current_workspace != -1) {
				_setUserAccessRights($group_id,'pages','workspace',$current_workspace,1);
			}
		}
	} else {
		## nothing was selected- so the user won't have access
		_setUserAccessRights($group_id,'pages','workspace',-1,1);
	}

	## okay next: templates
	if($_POST['templates_add']=='true') {
		_setUserAccessRights($group_id,'template','',0,1);
	}
	
	## usermanagement
	_setUserAccessRights($group_id,'users',$_POST['group'],0,1);		
	
	## signoff	
	_setUserAccessRights($group_id,'pages',$_POST['page_signoff'],0,1);

	## loop through all menues
	$menu_options = array('New subpage','New folder','Edit','Preview','Rename','Sort','Move','Copy Page','Copy Structure','Deactivate/Activate','Show/Hide in menu','Homepage','Information','Delete');

	foreach($menu_options as $key =>$current_menu) {
		if(isset($_POST['menu_'.$key])) {
			## okay the module was selected- insert the access right into the db
			_setUserAccessRights($group_id,'menu_'.$current_menu,'1',0,1);
		} 
	}

	## loop through all modules
	global $installed_modules;
	foreach($installed_modules as $key =>$current_module) {
		if(isset($_POST['module_'.$key])) {
			## okay the module was selected- insert the access right into the db
			_setUserAccessRights($group_id,$current_module,'1',0,1);
		} 
	}

	return true;
}

## =======================================================================        
##  group_getAllGroups        
## =======================================================================        
##  returns an array containg all groups
##
## =======================================================================
function group_getAllGroups() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql(); 

	## first we should get all groups
	$query = "SELECT * FROM ".GROUPS." WHERE $addition client_id='$client_id'";
	$db_connection->query($query);
	
	$groups = array();
	while($db_connection->next_record()) {
		$group_id 	= $db_connection->Record["group_id"];
		$groups[$group_id] = $db_connection->Record["name"];
	}
	
	return $groups;
}


## =======================================================================        
##  delete_group        
## =======================================================================        
##  deletes the group and all its users
##
## =======================================================================
function delete_group($group_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql(); 

	## okay we can delete this user
	$select_query = "DELETE FROM ".USERS." WHERE group_id='$group_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);	

	$select_query = "DELETE FROM ".GROUPS." WHERE group_id='$group_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);	

	$select_query = "DELETE FROM ".ACCESS." WHERE group_id='$group_id' AND client_id='$client_id'";
	$result_pointer = $db_connection->query($select_query);				

}


## =======================================================================        
## _groupGetInfo      
## =======================================================================        
## returns an array with information about the group
##
##  TODO:  
## =======================================================================
function _groupGetInfo($group_id) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];
		
	$db_connection = new DB_Sql();
	
	## grab the information
	$query = "SELECT * FROM ".GROUPS." WHERE group_id = '".$group_id."' AND client_id='$client_id' LIMIT 1";
	$result_pointer = $db_connection->query($query);	
	
	$results = array();
	if($db_connection->next_record()) {
		$results['group_id'] = $db_connection->Record["group_id"];
		$results['name'] 	 = $db_connection->Record["name"];
	}	
	## clear the db connection
	$db_connection->free();
	
	return $results;	
}

	
?>
