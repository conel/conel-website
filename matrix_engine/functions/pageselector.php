<?php
## =======================================================================        
## page_selector_drawMenu       
## =======================================================================        
function pageselector_drawMenu($vTree,$vVarsToBeSet,$vTargetURL) {
	global $gSession,$Auth;

	## first we set the vars
	$tree = $vTree;
	$p = $_GET['p'];
	
	## prepare the template file
	$layout_template = new Template('interface/');
	$layout_template->set_templatefile(array("head" => "pageselector.tpl","spacer" => "pageselector.tpl","expand" => "pageselector.tpl","element" => "pageselector.tpl","lastelement" => "pageselector.tpl","foot" => "pageselector.tpl"));

	##var_dump($layout_template);
	$layout_template->set_var("Title",LANG_MovePage);
	$layout_template->set_var("Desc",LANG_MovePageDesc);
	$layout_template->set_var("LANG_ROOTPAGE",LANG_ROOTPAGE);

	$img_expand    = "interface/images/menu/menu_corner_plus.gif";
	$img_collapse  = "interface/images/menu/menu_corner_minus.gif";
	$img_space     = "interface/images/menu/blank.gif";  
	$img_home      = "interface/images/menu/home_page.gif";
	$img_norm      = "interface/images/menu/norm_page.gif";
	$img_free      = "interface/images/menu/free_page.gif";
	
	$img_selected  = "interface/images/menu/selected_page.gif";


	## init the vars
	$maxlevel=5;
	
	for ($i=0; $i<count($tree); $i++) {
		$expand[$i]=0;
		$visible[$i]=0;
		$levels[$i]=0;
	}

	## Get Node numbers to expand           
 	if ($p!="") {
 		$explevels = explode("|",$p);
 	}

 	$i=0;
	while($i<count($explevels)) {
		$expand[$explevels[$i]]=1;
		$i++;
  	}	         
		
  	## all root nodes are always visible
	for ($i=0; $i < count($tree); $i++) {
		##echo $i." : ".$tree[$i]["text"]."<br>";
		if ($tree[$i]["level"]==1) {
			$visible[$i]=1;
		}
	}

	##  Determine visible nodes    	
	for ($i=0; $i < count($explevels); $i++) {
    	$n=$explevels[$i];
    	if (($visible[$n]==1) && ($expand[$n]==1)) {
			$j=$n+1;
			while ($tree[$j]["level"] > $tree[$n]["level"]) {
				if ($tree[$j]["level"]==$tree[$n]["level"]+1) $visible[$j]=1;     
				$j++;
			}
		}
  	}
  
	for ($i=0; $i<$maxlevel; $i++) {
		$levels[$i]=1;
	}
	
	$maxlevel++;
	
	$adminURL = 'admin.php';  
	$adminURL = $gSession->url($adminURL);        
	$layout_template->set_var("actionURL",$adminURL);

    ## finally we set the the passed vars 
    $hiddenfields = '';
    if(is_array($vVarsToBeSet)) {
		while(list($key,$val) = each($vVarsToBeSet)) {
			$hiddenfields .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
			## loop through all the entries
		}
	}
	$layout_template->set_var('hiddenfields',$hiddenfields);



	## here we stzart to ouptut the whole date
	$layout_template->pfill_block("head");

	##  Output nicely formatted tree
	$cnt=0; $id=0;
	while ($cnt<=count($tree)) {
		if ($visible[$cnt]) {
			$id++;
			
			print '<tr>';
			## vertical lines from higher levels   
      		$i=0;
      		while ($i<$tree[$cnt]["level"]-1) {
      			$layout_template->pfill_block("spacer");
				$i++;
			}
      
			/********************************************/
			/* Node (with subtree) or Leaf (no subtree) */
			/********************************************/
			if ($tree[$cnt+1]["level"] > $tree[$cnt]["level"]) {
				/****************************************/
				/* Create expand/collapse parameters    */
				/****************************************/
				$i=0; 
				$params=$vTargetURL."&p=";
        		while($i<count($expand)) {
        			if ( ($expand[$i]==1) && ($cnt!=$i) || ($expand[$i]==0 && $cnt==$i)) {
        				$params=$params.$i."|";
        			}
        			$i++;
        		}
               	$params = $gSession->url($params);
               
				if ($expand[$cnt]==0) {
					$layout_template->set_var("img",$img_expand);
				} else {
					$layout_template->set_var("img",$img_collapse);
				}
				 $layout_template->set_var("params",$params);
				$layout_template->pfill_block("expand");					
			} else {
				## Tree Leaf   
				$layout_template->pfill_block("spacer");         
			}
      
			/****************************************/
			/* output item                          */
			/****************************************/
			$url = "admin.php?op=domove&page_id=".$theCallingPage."&selected_page=".$tree[$cnt]["id"];
			$url = $gSession->url($url);
			
      		$layout_template->set_var("id",$id+1);
      		$layout_template->set_var("pageid",$tree[$cnt]["id"]);
      		$layout_template->set_var("menuid",$tree[$cnt]["id"]);
      		$layout_template->set_var("level",$tree[$cnt]["level"]-1);
      		$layout_template->set_var("lowsub",$tree[$cnt]["id"]);
      		$layout_template->set_var("span",($maxlevel-$tree[$cnt]["level"]+6));
      		$layout_template->set_var("url",$url);
      		$layout_template->set_var("target","text");
      		$layout_template->set_var("text",$tree[$cnt]["text"]);
      		
      		
      		## determine the flasg related stuff
      		if(checkFlag($tree[$cnt]["structure_flag"],1)) {
      			$layout_template->set_var("itemimg",$img_norm);
      			$layout_template->set_var("active","inactive");
      		} else {
       			$layout_template->set_var("itemimg",$img_norm);
      			$layout_template->set_var("active","inactive");
      		}   
      		
      		if(checkFlag($tree[$cnt]["structure_flag"],2)) {
      			$layout_template->set_var("itemimg",$img_norm);
      			$layout_template->set_var("active","active");
      		}
      		if(checkFlag($tree[$cnt]["structure_flag"],4)) {
      			$layout_template->set_var("itemimg",$img_home);
      			$layout_template->set_var("active","active");
      		}
      		if(checkFlag($tree[$cnt]["structure_flag"],8)) {
      			$layout_template->set_var("itemimg",$img_free);
      			$layout_template->set_var("active","active");
      		}   			 
      		       
      		## since its the slector, we should mark the appropriate item
       		if($tree[$cnt]["page_id"] == $vVarsToBeSet["targetPageID"]) {
      			$layout_template->set_var("itemimg",$img_selected);
      			$layout_template->set_var("active","selecteds");
      		}      		       
      		       		     		
      		if($tree[$cnt]["level"] < $maxlevel-1) {
      			$layout_template->pfill_block("element");
      		} else {
      			$layout_template->pfill_block("lastelement");
      		}
		}
		$cnt++;    
	}
	
	$layout_template->pfill_block("foot");
  }

?>
