<?php
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## =======================================================================        
## structure_drawMenu       
## =======================================================================        
## draws the structure as an expandable menu
##    
## =======================================================================
function structure_drawMenu($p,$workspace=null,$entrypoints = null) {
	global $gSession,$Auth;

	## get the user id	
	$user_id = $Auth->auth['user_id'];
	
	## prepare the template file
	$layout_template = new Template(INTERFACE_DIR);
	$layout_template->set_templatefile(array("head" => "menu.tpl","foot" => "menu.tpl"));
	
	## prepare the links
	$adminURL = $gSession->url('admin.php');        
	$layout_template->set_var("adminURL",$adminURL);

	## prepare the top navigation
	$newpage = "lang/".$Auth->auth["language"]."_neueseite.gif";
	$output = '<img src="interface/'.$newpage.'" width="269" height="22" border="0" USEMAP="#topnav">';

	$layout_template->set_var("TOPNAV",$output);
	$layout_template->pfill_block("head");
	
	
	## prepare the unfold parameters
	$state = explode('|',$p);
	$level = 0;
	$counter = 2;
	$maxlevel = 6;

	## okay to start we need to the root level pages- they are always visible 
	## unless we have a workspace - this will be handled later
	$root_level_pages = structure_getAllSubPages(0);	
	foreach($root_level_pages as $current_page) {	
		## determine if we should output this page
		if(!empty($workspace)) {
			## okay now we need to determine if any of the root pages
			## should be displayed
			foreach($workspace as $key => $current_workspace) {
				$entry_key = array_search($current_page['id'],$current_workspace);
				if($entry_key !== false) {
					unset($current_workspace[$entry_key]);
					
					if(in_array($current_page['id'],$entrypoints)) {
						$ghost = false;
					} else {
						$ghost = true;
					}
					structure_outputMenuItems($current_page,$level,$state,$counter,$ghost);
					## now we handle the subpages- check if we are open
					$position = array_search($current_page['page_id'],$state);
					if($position !== false && structure_hasSubPages($current_page['id'])) {
						$counter = structure_handleSubMenuEntries($current_page,$level+1,$state,$counter,$current_workspace,$entrypoints);
					}
				
					$counter++;
				}
			}
		} else {
		
			structure_outputMenuItems($current_page,$level,$state,$counter);
			## now we handle the subpages- check if we are open
			$position = array_search($current_page['page_id'],$state);
			if($position !== false && structure_hasSubPages($current_page['id'])) {
				$counter = structure_handleSubMenuEntries($current_page,$level+1,$state,$counter);
			}
		
			$counter++;	
		}

	}	
		


	## finally we generate the language menu
	$layout_template->set_var("menu",structure_outputContextMenu());
	
	$layout_template->pfill_block("foot");
  }

## =======================================================================        
## structure_handleSubMenuEntries      
## =======================================================================        
## handles the generation of the submenu entry
##    
## =======================================================================
function structure_handleSubMenuEntries($parent_page,$level,$state,$counter,$workspace=null,$entrypoints=null) {
	global $gSession,$Auth;
	
	## get the subpages
	$subpages = structure_getAllSubPages($parent_page['id']);

	$workspace_controlled = false;
	foreach($subpages as $current_page) {
		if(!empty($workspace) || $workspace_controlled) {
			$workspace_controlled = true;
			
			$key = array_search($current_page['id'],$workspace);
			if($key !== false) {
				unset($workspace[$key]);
				
				## determine if we should output this page
				## increase the page count
				$counter++;
			
				## actually output the element
				if(in_array($current_page['id'],$entrypoints)) {
					$ghost = false;
				} else {
					$ghost = true;
				}				
				structure_outputMenuItems($current_page,$level,$state,$counter,$ghost);
						
				## wo we handle the subpages- check if we are open
				$position = array_search($current_page['page_id'],$state);
				if($position !== false && structure_hasSubPages($current_page['id'])) {
					$counter = structure_handleSubMenuEntries($current_page,$level+1,$state,$counter,$workspace,$entrypoints);
				}
			}
		} else {
			## determine if we should output this page
			## increase the page count
			$counter++;
		
			## actually output the element
			structure_outputMenuItems($current_page,$level,$state,$counter);
					
			## wo we handle the subpages- check if we are open
			$position = array_search($current_page['page_id'],$state);
			if($position !== false && structure_hasSubPages($current_page['id'])) {
				$counter = structure_handleSubMenuEntries($current_page,$level+1,$state,$counter);
			}
		}
	}	

	return $counter;
}


## =======================================================================        
## structure_handleSubMenuEntries      
## =======================================================================        
## handles the generation of the submenu entry
##    
## =======================================================================
function structure_outputMenuItems($current_page,$level,$state,$counter,$ghost=false) {
	global $gSession;
	
	## global settings
	$maxlevel = 8;
		
	## prepare the unfold parameters
	$img_expand    = "interface/images/menu/menu_corner_plus.gif";
	$img_collapse  = "interface/images/menu/menu_corner_minus.gif";
	$img_space     = "interface/images/menu/blank.gif";  
	$img_home      = "interface/images/menu/home_page.gif";
	$img_norm      = "interface/images/menu/norm_page.gif";
	$img_folder    = "interface/images/menu/free_folder.gif";
	$img_linkedfolder    = "interface/images/menu/linked_folder.gif";
	$img_free      = "interface/images/menu/free_page.gif";
	
	$img_active_pending  = "interface/images/menu/page_pending.gif";
	$img_inactive_pending  = "interface/images/menu/page_inactive_pending.gif";

	$img_active_haslocalcopy  = "interface/images/menu/page_haslocalcopy.gif";
	$img_inactive_haslocalcopy  = "interface/images/menu/page_inactive_haslocalcopy.gif";
	
	$img_ghost_folder    = "interface/images/menu/icon_ghost_folder.gif";
	$img_ghost_page    = "interface/images/menu/icon_ghost_page.gif";
	
	$img_active_approvalrequested  = "interface/images/menu/page_approval.gif";
	$img_inactive_happrovalrequested  = "interface/images/menu/page_inactive_approval.gif";	
	
	## okay now we need to loop through all root pages
	## and if they are open- we need to unfold them
	$adminURL = 'admin.php';  
	$adminURL = $gSession->url($adminURL);  
	
	$base_params = "matrix_menu.php";
	$base_params = $gSession->url($base_params);
	
	## okay we need to check if we have any subpages for the current page
	if(structure_hasSubPages($current_page['id'])) {
		$control_image = (array_search($current_page['page_id'],$state) !== false) ? $img_collapse : $img_expand;
	} else {
		$control_image = 'interface/images/menu/blank.gif';
	}
	
	$position = array_search($current_page['page_id'],$state);
	if($position !== false) {
		unset($state[$position]);
	} else {
		$state[] = $current_page['page_id'];
	}
	
	## determine the icon and style
	$element_style = 'active';
	$type = 'page';
	$icon_image = $img_free;	
	$icon_image = (checkFlag($current_page["structure_flag"],0)) ? $img_free : $icon_image;
	$icon_image = (checkFlag($current_page["structure_flag"],2)) ? $img_norm : $icon_image;
	//$icon_image = (checkFlag($current_page["structure_flag"],8)) ? $img_norm : $icon_image;
	
	$icon_image = (checkFlag($current_page["structure_flag"],32)) ? $img_inactive_haslocalcopy : $icon_image;		
	$icon_image = (checkFlag($current_page["structure_flag"],16)) ? $img_inactive_pending : $icon_image;	
	$icon_image = (checkFlag($current_page["structure_flag"],64)) ? $img_inactive_happrovalrequested : $icon_image;	

	
	if(checkFlag($current_page["structure_flag"],1)) {
		$element_style = 'inactive';
	} else {
		$element_style = 'inactive';
	}   
	
	if(checkFlag($current_page["structure_flag"],2)) {
		$element_style = 'active';
	}
	if(checkFlag($current_page["structure_flag"],8)) {
		$element_style = 'inactive';
	}  
	
	if(checkFlag($current_page["structure_flag"],32)) {
		$element_style = 'inactive';
	}   
	if(checkFlag($current_page["structure_flag"],(32 | 2))) {
		$element_style = 'active';
	}   
	if(checkFlag($current_page["structure_flag"],(32 | 8))) {
		$element_style = 'inactive';
	}         		

	if(checkFlag($current_page["structure_flag"],16)) {
		$element_style = 'inactive';
	}   
	if(checkFlag($current_page["structure_flag"],(16 | 2))) {
		$element_style = 'active';
	}   
	if(checkFlag($current_page["structure_flag"],(16 | 8))) {
		$element_style = 'inactive';
	}
	
	if(checkFlag($current_page["structure_flag"],64)) {
		$element_style = 'inactive';
	}   
	if(checkFlag($current_page["structure_flag"],(64 | 2))) {
		$element_style = 'active';
	}   
	if(checkFlag($current_page["structure_flag"],(64 | 8))) {
		$element_style = 'inactive';
	} 



	if($current_page['template']== 0) {
		$type = 'folder';
		if(checkFlag($current_page["structure_flag"],8)) {
			$icon_image = $img_folder;
		} else {
			$icon_image = $img_linkedfolder;
			$element_style = 'active';
		}
	}
	
	$language = ($languages_count > 0) ? 1 : 0; 
	$span = ($maxlevel-$current_page['level']+6);	
	if($ghost) {
		$icon_image = ($type == 'page') ? $img_ghost_page : $img_ghost_folder;
		$command = '#';
	} else {
		$command = 'javascript:wmShowPullDown(0,'.$counter.','.$level.','.$current_page['id'].','.$current_page['page_id'].',\''.$type.'\','.$language.');';
	}
	
	echo '<tr>';
	for($i=0; $i <$level; $i++) {
		echo '<td valign="top" align="left" width="12"><img src="interface/images/menu/blank.gif"></td>';
	}
	if(structure_hasSubPages($current_page['id'])) {
		echo '<td valign="top" align="left" width="12"><a href="'.$base_params.'&p='.join('|',$state).'"><img src="'.$control_image.'" border="0" width="20" height="17"></a></td>';
	} else {
		echo '<td valign="top" align="left" width="12"><img src="interface/images/menu/blank.gif" border="0" width="20" height="17"></td>';
	}			
	
	echo '<td valign="top" align="left" width="12"><a href="'.$command.'" mouseover="self.status=\'test\';return true;" ><img src="'.$icon_image.'" width="20" height="17" border="0"></a></td>';
	echo '<td valign="top" align="left" colspan="'.$span.'" nowrap><a href="'.$adminURL."&op=preview&page_id=".$current_page["page_id"]."&cache=0".'" target="text" class="'.$element_style.'">'.$current_page['text'].'</a></td></tr>';
	echo '<tr><td valign="top" colspan="16" background="interface/images/menu/menu_seperator.gif"><img src="interface/images/menu/menu_seperator.gif" width="10" height="2"></td></tr>';	
}

## =======================================================================        
## structure_handleSubMenuEntries      
## =======================================================================        
## handles the generation of the submenu entry
##    
## =======================================================================
function structure_outputContextMenu() {		
	global $gSession,$Auth;
	
	$user_id = $Auth->auth['user_id'];
	
	## get access rights
	$access_rights = _getUserAccessRights($user_id);
	
	## prepare the admin url
	$adminURL = $gSession->url('admin.php');    
	
	## check out the languages
	$languages = language_getLanguages();
	$languages_count = count($languages);

	$contextmenu_template = new Template(INTERFACE_DIR);
	$contextmenu_template->set_templatefile(array("menu" => "context_menu.tpl","element" => "context_menu.tpl","seperator" => "context_menu.tpl","menuend" => "context_menu.tpl"));
	
	$context_menu = "";
	$contextmenu_template->set_var("menuname","pulldown0");
	$context_menu .= $contextmenu_template->fill_block("menu");

	if($access_rights['menu_New subpage'][1]['access'] == 1) {
		$contextmenu_template->set_var("actions",'onmouseover="WMHideLayer(\'languages\');"');	
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(2);");
		$contextmenu_template->set_var("itemname",LANG_MenuNewSubPage);
		$context_menu .= $contextmenu_template->fill_block("element");
	}
		
	if($access_rights['menu_New folder'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(21);");
		$contextmenu_template->set_var("itemname",LANG_MENU_NEWFOLDER);
		$context_menu .= $contextmenu_template->fill_block("element");	
		
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}	

	if($access_rights['menu_Edit'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(8);");
		$contextmenu_template->set_var("itemname",LANG_MenuEdit);
		$context_menu .= $contextmenu_template->fill_block("element");
	}		

	if($access_rights['menu_Preview'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(4);");
		$contextmenu_template->set_var("actions",'onmouseover="WMShowLayer(\'languages\');"');		
		$contextmenu_template->set_var("itemname",LANG_MenuPreview);
		$context_menu .= $contextmenu_template->fill_block("element");	
			
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}	

	
	$contextmenu_template->set_var("actions",'onmouseover="WMHideLayer(\'languages\');"');	
		

	
	if($access_rights['menu_Rename'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(1);");
		$contextmenu_template->set_var("itemname",LANG_MenuRename);
		$context_menu .= $contextmenu_template->fill_block("element");
	}		

	if($access_rights['menu_Sort'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(6);");
		$contextmenu_template->set_var("itemname",LANG_MenuSort);
		$context_menu .= $contextmenu_template->fill_block("element");	

	}	


	if($access_rights['menu_Move'][1]['access'] == 1) {
		if(isset($access_rights['pages']['auto_signoff'])) {	
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(15);");
			$contextmenu_template->set_var("itemname",LANG_MenuMove);
			$context_menu .= $contextmenu_template->fill_block("element");		
		}
	}		
	
	// nkowald - 2009-10-06 - Created a new item for Page Redirections
	/* This access right is set in the database, query for inserting access right = 
	   INSERT INTO webmatrix_accessrights VALUES('menu_Redirect','1','0','1','1','1'); */

	if($access_rights['menu_Redirect'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(25);");
		$contextmenu_template->set_var("itemname",LANG_MENU_REDIRECTPAGE);
		$context_menu .= $contextmenu_template->fill_block("element");	
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}
	
	if($access_rights['menu_Copy Page'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(22);");
		$contextmenu_template->set_var("itemname",LANG_MENU_COPYPAGE);
		$context_menu .= $contextmenu_template->fill_block("element");	
	}

	
	if($access_rights['menu_Copy Structure'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(23);");
		$contextmenu_template->set_var("itemname",LANG_MENU_COPYSTRUCTURE);
		$context_menu .= $contextmenu_template->fill_block("element");	
		//$context_menu .= $contextmenu_template->fill_block("seperator");
	}

	
	if($access_rights['menu_Deactivate/Activate'][1]['access'] == 1) {
		if(isset($access_rights['pages']['auto_signoff']) || isset($access_rights['pages']['signoff'])) {
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(5);");
			$contextmenu_template->set_var("itemname",LANG_MenuChangeVisibility);
			$context_menu .= $contextmenu_template->fill_block("element");
		}
	}
	
	if($access_rights['menu_Show/Hide in menu'][1]['access'] == 1) {		
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(9);");
		$contextmenu_template->set_var("itemname",LANG_MenuChangeMenuState);
		$context_menu .= $contextmenu_template->fill_block("element");	
			
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}

	
	if($access_rights['menu_Homepage'][1]['access'] == 1) {		
		if(isset($access_rights['pages']['auto_signoff'])) {	
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(7);");
			$contextmenu_template->set_var("itemname",LANG_SetHomepage);
			$context_menu .= $contextmenu_template->fill_block("element");	
			$context_menu .= $contextmenu_template->fill_block("seperator");	
		}
	}
	
	if($access_rights['menu_Information'][1]['access'] == 1) {				
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(14);");
		$contextmenu_template->set_var("itemname",LANG_MenuInfo);
		$context_menu .= $contextmenu_template->fill_block("element");
	}
	
	
	if($access_rights['menu_Delete'][1]['access'] == 1) {				
		if(isset($access_rights['pages']['auto_signoff'])) {	
			$context_menu .= $contextmenu_template->fill_block("seperator");
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(3);");
			$contextmenu_template->set_var("itemname",LANG_MenuDelete);
			$context_menu .= $contextmenu_template->fill_block("element");
		}
	}

		
		
	$context_menu .= $contextmenu_template->fill_block("menuend");


	$contextmenu_template->set_var("menuname","pulldown1");
	$context_menu .= $contextmenu_template->fill_block("menu");

	if($access_rights['menu_Edit'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(8);");
		$contextmenu_template->set_var("itemname",LANG_MenuEdit);
		$context_menu .= $contextmenu_template->fill_block("element");
	}	


	if($access_rights['menu_Preview'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(4);");
		$contextmenu_template->set_var("actions",'onmouseover="WMShowLayer(\'languages\');"');		
		$contextmenu_template->set_var("itemname",LANG_MenuPreview);
		$context_menu .= $contextmenu_template->fill_block("element");	
			
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}	

	
	$contextmenu_template->set_var("actions",'onmouseover="WMHideLayer(\'languages\');"');	
		

	
	if($access_rights['menu_Rename'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(1);");
		$contextmenu_template->set_var("itemname",LANG_MenuRename);
		$context_menu .= $contextmenu_template->fill_block("element");
	}		

	if($access_rights['menu_Sort'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(6);");
		$contextmenu_template->set_var("itemname",LANG_MenuSort);
		$context_menu .= $contextmenu_template->fill_block("element");	

	}	


	if($access_rights['menu_Move'][1]['access'] == 1) {
		if(isset($access_rights['pages']['auto_signoff'])) {	
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(15);");
			$contextmenu_template->set_var("itemname",LANG_MenuMove);
			$context_menu .= $contextmenu_template->fill_block("element");		
		}
	}		
	
	if($access_rights['menu_Copy Page'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(22);");
		$contextmenu_template->set_var("itemname",LANG_MENU_COPYPAGE);
		$context_menu .= $contextmenu_template->fill_block("element");	
	}

	
	if($access_rights['menu_Copy Structure'][1]['access'] == 1) {
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(23);");
		$contextmenu_template->set_var("itemname",LANG_MENU_COPYSTRUCTURE);
		$context_menu .= $contextmenu_template->fill_block("element");	
		
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}

	
	if($access_rights['menu_Deactivate/Activate'][1]['access'] == 1) {
		if(isset($access_rights['pages']['auto_signoff']) || isset($access_rights['pages']['signoff'])) {
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(5);");
			$contextmenu_template->set_var("itemname",LANG_MenuChangeVisibility);
			$context_menu .= $contextmenu_template->fill_block("element");
		}
	}
	
	if($access_rights['menu_Show/Hide in menu'][1]['access'] == 1) {		
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(9);");
		$contextmenu_template->set_var("itemname",LANG_MenuChangeMenuState);
		$context_menu .= $contextmenu_template->fill_block("element");	
			
		$context_menu .= $contextmenu_template->fill_block("seperator");
	}

	
	if($access_rights['menu_Homepage'][1]['access'] == 1) {		
		if(isset($access_rights['pages']['auto_signoff'])) {	
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(7);");
			$contextmenu_template->set_var("itemname",LANG_SetHomepage);
			$context_menu .= $contextmenu_template->fill_block("element");	
			$context_menu .= $contextmenu_template->fill_block("seperator");	
		}
	}
	
	if($access_rights['menu_Information'][1]['access'] == 1) {				
		$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(14);");
		$contextmenu_template->set_var("itemname",LANG_MenuInfo);
		$context_menu .= $contextmenu_template->fill_block("element");
	}
	
	
	if($access_rights['menu_Delete'][1]['access'] == 1) {				
		if(isset($access_rights['pages']['auto_signoff'])) {	
			$context_menu .= $contextmenu_template->fill_block("seperator");
			$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(3);");
			$contextmenu_template->set_var("itemname",LANG_MenuDelete);
			$context_menu .= $contextmenu_template->fill_block("element");
		}
	}
			
	$context_menu .= $contextmenu_template->fill_block("menuend");
	
	
	
	## we need to generate the top menu (for "new page", new folder etc.
	$top_menu = "";
	$contextmenu_template->set_var("menuname","pulldown_top");
	$top_menu .= $contextmenu_template->fill_block("menu");
	
	$contextmenu_template->set_var("actionURL",$adminURL.'&op=display_template_list" target="text"');
	$contextmenu_template->set_var("itemname",LANG_MENU_NEWPAGE);
	$top_menu .= $contextmenu_template->fill_block("element");

	$top_menu .= $contextmenu_template->fill_block("seperator");
	
	$contextmenu_template->set_var("actionURL",$adminURL.'&op=create_folder" target="text"');
	$contextmenu_template->set_var("itemname",LANG_MENU_NEWFOLDER);
	$top_menu .= $contextmenu_template->fill_block("element");
					
	$top_menu .= $contextmenu_template->fill_block("menuend");

	$lang_menu = "";
	
	if($languages_count > 1) {
		$contextmenu_template->set_var("menuname","languages");
		$lang_menu .= $contextmenu_template->fill_block("menu");
		
		$contextmenu_template->set_var("actions",'onmouseover="WMShowLayer(\'languages\');"');	

		for($i=0; $i< $languages_count; $i++) {	
			if($i != 0) {
				$lang_menu .= $contextmenu_template->fill_block("seperator");
			}
			$contextmenu_template->set_var("actionURL","javascript:WMDoSpecialCommand(40,".$languages[$i]['id'].");");
			$contextmenu_template->set_var("itemname",$languages[$i]['name']);
			$lang_menu .= $contextmenu_template->fill_block("element");
		}
						
		$lang_menu .= $contextmenu_template->fill_block("menuend");
	}
	
	return $context_menu.$top_menu.$lang_menu;
	
  }

?>