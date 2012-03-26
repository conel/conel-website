<?php
	require("../framework.php");

	## check if we have the correct access rights
	$access_rights = _getUserAccessRights($Auth->auth['user_id']);
	
	if($access_rights['template']['access']!=1) {
		## display the error message
		$inputFile = "empty_menu.tpl";
		$input_template = new Template(INTERFACE_DIR);
		$input_template->set_templatefile(array("body" => $inputFile));
		## the next step is to ouput the head
		$input_template->pfill_block("body");
		exit;
	} 	

	## multiclient
	$client_id = $Auth->auth["client_id"];
  
	## prepare the db-object
	$db_connection = new DB_Sql();  

	## initialize the menuCounter
	$menuCounter=0;
	$menuID=0;

	## grab the information
	$select_query = "SELECT template_id, title FROM ".PAGE_TEMPLATE." WHERE client_id='$client_id' ORDER by title";
	$result_pointer = $db_connection->query($select_query);	
	
	$menuItems = array();
	while($db_connection->next_record()) {
		## first we get all the data
		$template_id = $db_connection->Record["template_id"];
		$title = $db_connection->Record["title"];
		
		## we will need to generate the menu url here-
		## the exact code will be done later on....	
		$menuURL = "template.php";
		
		## now set the item info
		$item[0]=1;
 		$item[1] = $title;
 		$item[2] = $menu_url;
 		$item[3]="";
 		$item[4]=0;
  		$item[5]=$template_id;
 		$item[6]=$menuID;
 		$item[7]=$low_sub;
 		
 		## we don't have homepages
 		$item[8]=0;	
 		
 		## and all entries are active
 		$item[9]=1;
 		
 		$menuItems[] = $item;	
	}
	
	drawMenu( $menuItems,$p);



function drawMenu($tree,$p) {
	global $gSession,$Auth;
	
	## prepare the template file
	$layout_template = new Template("interface/");
	$layout_template->set_templatefile(array("head" => "templatemenu.tpl","spacer" => "templatemenu.tpl","expand" => "templatemenu.tpl","element" => "templatemenu.tpl","lastelement" => "templatemenu.tpl","foot" => "templatemenu.tpl"));

	$layout_template->set_var("newpageIMG","lang/".$Auth->auth["language"]."_neuevorlage.gif");
	
	$img_expand    = "../../interface/images/menu/menu_corner_plus.gif";
	$img_collapse  = "../../interface/images/menu/menu_corner_minus.gif"; 
	$img_norm      = "../../interface/images/menu/norm_page.gif";
	

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
	
	$targetURL = 'template.php';          
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
      		$targetURL = "template.php";
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
	
	$contextmenu_template = new Template(INTERFACE_DIR);
	$contextmenu_template->set_templatefile(array("menu" => "context_menu.tpl","element" => "context_menu.tpl","seperator" => "context_menu.tpl","menuend" => "context_menu.tpl"));
	$contextmenu_template->set_var("SITE",SITE);
	
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
		
	$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(1);");
	$contextmenu_template->set_var("itemname",LANG_MenuEdit);
	$context_menu .= $contextmenu_template->fill_block("element");
		
	$context_menu .= $contextmenu_template->fill_block("seperator");
	
	$contextmenu_template->set_var("actionURL","javascript:WMDoCommand(2);");
	$contextmenu_template->set_var("itemname",LANG_MenuDelete);
	$context_menu .= $contextmenu_template->fill_block("element");
			
	$context_menu .= $contextmenu_template->fill_block("menuend");	
	
	
	$layout_template->set_var("menu",$context_menu);
	
	
	
	$layout_template->pfill_block("foot");
  }
?>
