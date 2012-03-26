<?php
	require("../config.php");

	## first get all accessrights for this user
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);

	## then we need to check if we are allowed to access this module

	if ($access_rights['users']['edit_all']['access'] != 1) {
		## we don't have access to the users -> display the error message
		$inputFile = "empty_menu.tpl";
		$input_template = new Template(INTERFACE_DIR);
		$input_template->set_templatefile(array("body" => $inputFile));
		## the next step is to ouput the head
		$input_template->pfill_block("body");
		exit;
	}




	$img_expand    = "interface/images/menu/menu_corner_plus.gif";
	$img_collapse  = "interface/images/menu/menu_corner_minus.gif";
	$img_space     = "interface/images/menu/blank.gif";  
	$img_home      = "interface/images/menu/norm_workspace.gif";
	$img_norm      = "interface/images/menu/norm_workspace.gif";

	## prepare the db-object
	$db_connection = new DB_Sql();

	## prepare the template file
	$layout_template = new Template('interface/');
	$layout_template->set_templatefile(array("head" => "workspacemenu.tpl","spacer" => "workspacemenu.tpl","expand" => "workspacemenu.tpl","element" => "workspacemenu.tpl","foot" => "workspacemenu.tpl"));
	
	$layout_template->set_var('url','');
	
	$newpageIMG = "lang/".$Auth->auth["language"]."_newworkspace.gif";
	$targetURL = 'workspace_entry.php';          
	$targetURL = $gSession->url($targetURL);
	$layout_template->set_var("targetURL",$targetURL);
	
		
	
	if(isset($allowed_group)) {
		$topnav = '<img src="interface/images/empty.gif" width="269" height="22" border="0">';
	} else {
		$topnav = '<a href="'.$targetURL.'&op=new_workspace" target="text"><img src="interface/'.$newpageIMG.'" width="269" height="22" border="0"></a>';
	}
	$layout_template->set_var("TOPNAV",$topnav);
	
	$menuURL = 'workspaces.php';          
	$menuURL = $gSession->url($menuURL);
	## here we stzart to ouptut the whole date
	$layout_template->pfill_block("head");
		
	$addition = isset($allowed_group) ? ('group_id='.$allowed_group.' AND') : '';

	## first we should get all workspaces
	$query = "SELECT * FROM ".DB_PREFIX."workspaces WHERE client_id='$client_id'";
	$db_connection->query($query);
	
	
	$items_displayed=2;
	while($db_connection->next_record()) {
		$workspace_id 	= $db_connection->Record["workspace_id"];
		$group_name = $db_connection->Record["name"];

		## get the members for this group
		$group_members = _getMembersOfGroup($group_id,$client_id);
		$group_memberCount = count($group_members);
		
		$spacer = '';
		$member_output = '';
		$subitems = 0;
		if($group_memberCount>0) {
			$spacer = 'expand';
			## this means we need to handle the collaps/expand functionality		
			if(in_array($group_id,$open_groups)) {
				$expand	 = $img_collapse;
				$menuParameters = _prepareExpandOptions($open_groups,$group_id);
			} else {
				$expand = $img_expand;
				$menuParameters = _prepareExpandOptions($open_groups,-1).$group_id.'|';
			}
			$layout_template->set_var("menuURL",$menuURL.'&'.$menuParameters);
			$layout_template->set_var("EXPAND",$expand);
			
		} else {
			## just output the name and an spacer element
			$spacer = 'spacer';
		}
		
		$layout_template->set_var("text",$group_name);
		$layout_template->set_var("workspace_id",$workspace_id);
		$layout_template->set_var("id",$items_displayed);
		
		$layout_template->pfill_block($spacer);
		$layout_template->pfill_block("element");
		
		## and output the members
		print $member_output;
		
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
		
	$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(2);");
	$contextmenu_template->set_var("itemname",LANG_MenuDelete);
	$context_menu .= $contextmenu_template->fill_block("element");
		
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






function _getMembersOfGroup($group_id,$client_id) {
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


function _prepareExpandOptions($options,$current_item) {
	## loop through all options
	$parameter = 'p=';
	foreach($options as $current_option) {
		if(($current_option != $current_item) && $current_option!="") {
			$parameter .= $current_option.'|';
		}
	}
	return $parameter;
}







function drawMenu($tree,$p) {
	global $gSession,$Auth;

	## prepare the template file
	$layout_template = new Template('interface/');
	$layout_template->set_templatefile(array("head" => "workspacemenu.tpl","spacer" => "workspacemenu.tpl","expand" => "workspacemenu.tpl","element" => "workspacemenu.tpl","lastelement" => "workspacemenu.tpl","foot" => "workspacemenu.tpl"));
	
	$layout_template->set_var("newpageIMG","lang/".$Auth->auth["language"]."_neueruser.gif");

	$img_expand    = "interface/images/menu/menu_corner_plus.gif";
	$img_collapse  = "interface/images/menu/menu_corner_minus.gif";
	$img_space     = "interface/images/menu/blank.gif";  
	$img_home      = "interface/images/menu/home_page.gif";
	$img_norm      = "interface/images/menu/norm_user.gif";

	/*********************************************/
	/* read file to $tree array                  */
	/* tree[x][0] -> tree level                  */
	/* tree[x][1] -> item text                   */
	/* tree[x][2] -> item link                   */
	/* tree[x][3] -> link target                 */
	/* tree[x][4] -> last item in subtree        */
	/*********************************************/
	$maxlevel=4;
 
	for ($i=0; $i<count($tree); $i++) {
		$expand[$i]=0;
		$visible[$i]=0;
		$levels[$i]=0;
	}

	/*********************************************/
	/*  Get Node numbers to expand               */
	/*********************************************/
 	if ($p!="") $explevels = explode("|",$p);
 	
 	$i=0;
	while($i<count($explevels)) {
		$expand[$explevels[$i]]=1;
		$i++;
  	}
  
	/*********************************************/
	/*  Find last nodes of subtrees              */
	/*********************************************/
	$lastlevel=$maxlevel;
	for ($i=count($tree)-1; $i>=0; $i--) {
     if ($tree[$i][0] < $lastlevel) {
       for ($j=$tree[$i][0]+1; $j <= $maxlevel; $j++) {
          $levels[$j]=0;
       }
     }
     
     if ($levels[$tree[$i][0]]==0) {
       $levels[$tree[$i][0]]=1;
       $tree[$i][4]=1;
     } else
		$tree[$i][4]=0;
		$lastlevel=$tree[$i][0];  
	}

	/*********************************************/
	/*  Determine visible nodes                  */
	/*********************************************/
  	// all root nodes are always visible
  	for ($i=0; $i < count($tree); $i++) if ($tree[$i][0]==1) $visible[$i]=1;

	for ($i=0; $i < count($explevels); $i++) {
    	$n=$explevels[$i];
    	if (($visible[$n]==1) && ($expand[$n]==1)) {
			$j=$n+1;
			while ($tree[$j][0] > $tree[$n][0]) {
				if ($tree[$j][0]==$tree[$n][0]+1) $visible[$j]=1;     
				$j++;
			}
		}
  	}
  
	/*********************************************/
	/*  Output nicely formatted tree             */
	/*********************************************/
	for ($i=0; $i<$maxlevel; $i++) $levels[$i]=1;
	$maxlevel++;
	
	$targetURL = 'user.php';          
	$targetURL = $gSession->url($targetURL);
	$layout_template->set_var("targetURL",$targetURL);
	## here we stzart to ouptut the whole date
	$layout_template->pfill_block("head");

	$cnt=0;
	$id=0;
	while ($cnt<count($tree)) {
		if ($visible[$cnt]) {
			$id++;
			/****************************************/
			/* start new row                        */
			/****************************************/      
      		echo "<tr>";

			/****************************************/
			/* vertical lines from higher levels    */
			/****************************************/
      		$i=0;
      		while ($i<$tree[$cnt][0]-1) {
      			$layout_template->pfill_block("spacer");
				$i++;
			}
      
			/********************************************/
			/* Node (with subtree) or Leaf (no subtree) */
			/********************************************/
			if ($tree[$cnt+1][0]>$tree[$cnt][0]) {
				/****************************************/
				/* Create expand/collapse parameters    */
				/****************************************/
				$i=0; 
				$params="?p=";
        		while($i<count($expand)) {
        			if ( ($expand[$i]==1) && ($cnt!=$i) || ($expand[$i]==0 && $cnt==$i)) {
        				$params=$params.$i;
        				$params=$params."|";
        			}
        			$i++;
        		}
               
				if ($expand[$cnt]==0) {
					$layout_template->set_var("params",$params);
					$layout_template->set_var("img",$img_expand);
				} else {
					$layout_template->set_var("params",$params);
					$layout_template->set_var("img",$img_collapse);
				}
				$layout_template->pfill_block("expand");					
			} else {
				/*************************/
				/* Tree Leaf             */
				/*************************/
				$layout_template->pfill_block("spacer");         
			}
      
			/****************************************/
			/* output item                          */
			/****************************************/
      		$layout_template->set_var("id",$id+1);
      		$layout_template->set_var("pageid",$tree[$cnt][5]);
      		$layout_template->set_var("menuid",$tree[$cnt][6]);
      		$layout_template->set_var("level",$tree[$cnt][0]-1);
      		$layout_template->set_var("lowsub",$tree[$cnt][7]);
      		$layout_template->set_var("span",($maxlevel-$tree[$cnt][0]));
      		$targetURL = "user.php?op=edit_template&template_id=".$tree[$cnt][5];
      		$targetURL = $gSession->url($targetURL);
			$layout_template->set_var("url",$targetURL);
			
      		$layout_template->set_var("target","text");
      		$layout_template->set_var("text",$tree[$cnt][1]);
      		
      		if($tree[$cnt][8]==0) {
      			$layout_template->set_var("itemimg",$img_norm);
      		} else {
      			$layout_template->set_var("itemimg",$img_home);
      		}
      		
      		if($tree[$cnt][9]) {
      			$layout_template->set_var("active","active");
      		} else {
      			$layout_template->set_var("active","inactive");
      		}      		

      		if($tree[$cnt][0] < $maxlevel-1) {
      			$layout_template->pfill_block("element");
      		} else {
      			$layout_template->pfill_block("lastelement");
      		}
		}
		$cnt++;    
	}
	
  }
?>
