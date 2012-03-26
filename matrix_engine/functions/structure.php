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
## set up Defined Values       
## =======================================================================        
define("PAGE_INACTIVE",1);
define("PAGE_ACTIVE",2);
define("PAGE_HOMEPAGE",4);
define("PAGE_INVISIBLE",8);
define("PAGE_PENDING",16);
define("PAGE_HASLOCALCOPY",32);
define("PAGE_APPROVAL_REQUESTED",64);

## =======================================================================        
## structure_getStructure       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getStructure($page_filter=null) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	global $gStructure;
	global $test;
	
	$gStructure = array();
	$test = array();

	$subquery = '';
	if(isset($page_filter) && (count($page_filter) >0)) {
		foreach($page_filter as $current_page) {
			if($subquery=='') {
				$subquery .= "a.structure_id='".$current_page."'";
			} else {
				$subquery .= " OR a.structure_id='".$current_page."'";
			}
		}
		$subquery = 'AND ( '.$subquery.')';
	}	

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." AS a INNER JOIN ".USER_PAGES." AS b ON a.page_id = b.page_id WHERE a.client_id='$client_id' $subquery ORDER BY structure_parent, structure_order";
	$result = $db_connection->query($query);	

	while($db_connection->next_record()) {
		$structure_parent 	= $db_connection->Record["structure_parent"];
		$structure_id 		= intval($db_connection->Record["structure_id"]);
	
		## first we get all the data
		$gHelpStructure[$structure_parent][$structure_id]	= $db_connection->Record["structure_text"];
		
		$gStructure[$structure_id]["parent"] 			= $db_connection->Record["structure_parent"];
		$gStructure[$structure_id]["order"] 			= $db_connection->Record["structure_order"];
		$gStructure[$structure_id]["text"] 				= $db_connection->Record["structure_text"];
		$gStructure[$structure_id]["id"] 				= $db_connection->Record["structure_id"];
		$gStructure[$structure_id]["page_id"] 			= $db_connection->Record["page_id"];
		$gStructure[$structure_id]["structure_flag"] 	= $db_connection->Record["structure_flag"];
		$gStructure[$structure_id]["type"]			 	= $db_connection->Record["type"];
		$gStructure[$structure_id]["template"]			= $db_connection->Record["template"];
		$gStructure[$structure_id]["editable"]		= true;
		
		if($db_connection->Record["structure_homepage"]=='1') {
			$gStructure[$structure_id]["homepage"] 	= true;
		} else {
			$gStructure[$structure_id]["homepage"] 	= false;
		}
		$gStructure[$structure_id]["url"] 	= $db_connection->Record["structure_url"];
	}	
	

	## let's parse the tree levels
	if(!empty($gHelpStructure)) {
		structure_createBranch(0,0,$gHelpStructure,100);
	}
	return $test;
}


## =======================================================================        
## structure_getStructure       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getStructureWorkspace($workspace_entry_points) {
	global $Auth,$gStructure,$test;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## pages contains the list of workspace root pages
	$pages = array();
	$db = new DB_Sql();
		
	## then for each parent page we need to fetch the entries
	$structure = array();
	foreach($workspace_entry_points as $current_entry_point) {
		## we need to find the parent pages for each entry point
		$pages = structure_getBranchUpwards($current_entry_point);
		krsort($pages);
		
		## start at the root level
		$level = 1;
		foreach($pages as $current_page) {			
			## okay now fetch the details for this page
			$pagedetails = structure_getPage($current_page);
			$pagedetails['level'] = $level;
			
			if($current_page == $current_entry_point) {
				$pagedetails['editable'] = true;
			} else {
				$pagedetails['editable'] = false;
			}
			## insetr the entry into our structure
			$structure[] = $pagedetails; 
			$level++;
		}
		
		$structure = array_merge($structure,structure_getBranchDetails($current_entry_point,$level));
	}

	return $structure;
	
}


## =======================================================================        
## structure_getWholeBranch     
## =======================================================================        
## pass it a structure id and we will return a list of structure_ids
## that are above this page
##    
## =======================================================================
function structure_getBranchUpwardsDetails($structure_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$pages = array();
	
	$db = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." AS A INNER JOIN ".USER_PAGES." AS B ON A.page_id=B.page_id WHERE A.structure_id = '$structure_id' AND A.client_id='$client_id'";
	$result = $db->query($query);	
	
	if($db->next_record()) {
		## store the pages on this level
		$structure_parent 	= $db->Record["structure_parent"];
		$structure_id 		= intval($db->Record["structure_id"]);
	
		$pages[$structure_id]["parent"] 		= $db->Record["structure_parent"];
		$pages[$structure_id]["order"] 			= $db->Record["structure_order"];
		$pages[$structure_id]["text"] 			= $db->Record["structure_text"];
		$pages[$structure_id]["id"] 			= $db->Record["structure_id"];
		$pages[$structure_id]["page_id"] 		= $db->Record["page_id"];
		$pages[$structure_id]["structure_flag"] = $db->Record["structure_flag"];
		$pages[$structure_id]["type"]			 = $db->Record["type"];
		$pages[$structure_id]["template"]		= $db->Record["template"];
		$pages[$structure_id]["url"]		= $db->Record["structure_url"];
		$pages[$structure_id]["editable"]		= true;
		$pages[$structure_id]["level"]		= count($pages);
		
		if($db->Record["structure_homepage"] == 1) {
			$pages[$structure_id]["homepage"] 	= true;
		} else {
			$pages[$structure_id]["homepage"] 	= false;
		}
		
		$pages  = array_merge(structure_getBranchUpwardsDetails($structure_parent),$pages);
	}
	
	return $pages;
} 


## =======================================================================        
## structure_getWholeBranch     
## =======================================================================        
## pass it a structure id and we will return a list of structure_ids
## that are above this page
##    
## =======================================================================
function structure_getBranchUpwards($structure_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## stores the found pages
	$structure_array = array();
	
	$db_connection = new DB_Sql();
	$query = "SELECT structure_parent FROM ".STRUCTURE." WHERE structure_id = '$structure_id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	
	$db_connection->next_record();
	
	$current_parent = $db_connection->Record["structure_parent"];
	$structure_array[] = $structure_id;
	
	if($current_parent != 0) {
		## we need to call the structure_getBranchUpwards again
		$subpages = array();
		$subpages = structure_getBranchUpwards($current_parent);
		$structure_array = array_merge($structure_array,$subpages);
	}
	return $structure_array;
} 

## =======================================================================        
## structure_getBranchStructureID      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getBranchStructureID($structure_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## we get the structure_id and need to get all subpages
	## for this item
	$subpages = array();

	$db_connection = new DB_Sql();
	$query = "SELECT structure_id FROM ".STRUCTURE." WHERE structure_parent = '$structure_id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	while($db_connection->next_record()) {
		## okay for each item we'll find
		## we will get the subpages
		$structure_id = $db_connection->Record["structure_id"];
		
		## add the page to our array
		$subpages[] = $structure_id;

		## now get all subpages
		$extra_pages = structure_getBranchStructureID($structure_id);

		foreach($extra_pages as $page) { 
	 		$subpages[] = $page; 
		}		
		
		## get the items for this 
	}
	
	return $subpages;
} 


## =======================================================================        
## structure_getBranch      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getBranch($structure_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## we get the structure_id and need to get all subpages
	## for this item
	$subpages = array();

	$db_connection = new DB_Sql();
	$query = "SELECT structure_id,page_id FROM ".STRUCTURE." WHERE structure_parent = '$structure_id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	while($db_connection->next_record()) {
		## okay for each item we'll find
		## we will get the subpages
		$current_id 	= $db_connection->Record["page_id"];
		$structure_item = $db_connection->Record["structure_id"];
		
		## add the page to our array
		$subpages[] = $current_id;
		
		## now get all subpages
		$extra_pages = structure_getBranch($structure_item);

		foreach($extra_pages as $page) { 
	 		$subpages[] = $page; 
		}		
		
		## get the items for this 
	}
	
	return $subpages;
} 


## =======================================================================        
## structure_getBranchDetails      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getBranchDetails($structure_id,$level = 0) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## we get the structure_id and need to get all subpages
	## for this item
	$pages = array();

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." AS A INNER JOIN ".USER_PAGES." AS B ON A.page_id=B.page_id WHERE A.structure_parent = '$structure_id' AND A.client_id='$client_id'";
	$result = $db_connection->query($query);	

	while($db_connection->next_record()) {
		## store the pages on this level
		$structure_parent 	= $db_connection->Record["structure_parent"];
		$structure_id 		= intval($db_connection->Record["structure_id"]);

		$pages[$structure_id]["parent"] 		= $db_connection->Record["structure_parent"];
		$pages[$structure_id]["order"] 			= $db_connection->Record["structure_order"];
		$pages[$structure_id]["text"] 			= $db_connection->Record["structure_text"];
		$pages[$structure_id]["id"] 			= $db_connection->Record["structure_id"];
		$pages[$structure_id]["page_id"] 		= $db_connection->Record["page_id"];
		$pages[$structure_id]["structure_flag"] = $db_connection->Record["structure_flag"];
		$pages[$structure_id]["type"]			 = $db_connection->Record["type"];
		$pages[$structure_id]["template"]		= $db_connection->Record["template"];
		$pages[$structure_id]["url"]		= $db_connection->Record["structure_url"];
		$pages[$structure_id]["level"]		= $level;
		$pages[$structure_id]["editable"]		= true;
		
		if($db_connection->Record["structure_homepage"] == 1) {
			$pages[$structure_id]["homepage"] 	= true;
		} else {
			$pages[$structure_id]["homepage"] 	= false;
		}
				
		## now get all subpages
		$pages = array_merge($pages,structure_getBranchDetails($structure_id,$level+1));
	}
	
	return $pages;
} 

## =======================================================================        
## structure_createBranch      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_createBranch($structure_parent,$level,$table,$maxlevel) {
	global $gStructure;
	global $test;
	
	## it is possible that we get a a list of pages that don't have
	## the parent 0... what can we do then?
	
	## get the subtree of this node
	$subtree = $table[$structure_parent]; 
	if(is_array($subtree)) {
		while(list($key,$val) = each($subtree)) {
			## set the level for the subtree
			$table[$structure_parent][$key]["level"] = $level;
			$gStructure[$key]["level"] = $level+1;
			array_push($test,$gStructure[$key]);

			## check if there are more entries underneath the thing   
			if ((isset($table[$key])) AND (($maxlevel>$level+1) OR ($maxlevel=="0"))) {
				structure_createBranch($key,$level+1,$table,$maxlevel);
			}
		}
	}
}     
	
## =======================================================================        
## structure_getPage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getPage($vID) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." AS A INNER JOIN ".USER_PAGES." AS B ON A.page_id=B.page_id WHERE A.structure_id = $vID AND A.client_id='$client_id'";
	$result = $db_connection->query($query);	

	$pageInfo = array();
	
	if($result) {
		if($db_connection->next_record(MYSQL_ASSOC)) {
			$pageInfo["parent"] 		= $db_connection->Record["structure_parent"];
			$pageInfo["order"] 			= $db_connection->Record["structure_order"];
			$pageInfo["text"] 			= $db_connection->Record["structure_text"];
			$pageInfo["id"] 			= $db_connection->Record["structure_id"];
			$pageInfo["page_id"] 		= $db_connection->Record["page_id"];
			$pageInfo["flags"] 			= $db_connection->Record["structure_flag"];
			$pageInfo["structure_flag"]	= $db_connection->Record["structure_flag"];
			$pageInfo["type"]			= $db_connection->Record["type"];
			$pageInfo["template"]		= $db_connection->Record["template"];
			$pageInfo["url"] 			= $db_connection->Record["structure_url"];		
			
		
			if($db_connection->Record["structure_homepage"] == 1) {
				$pageInfo["homepage"] 	= true;
			} else {
				$pageInfo["homepage"] 	= false;
			}			
		}
	}	
	return $pageInfo;
}

## =======================================================================        
## structure_getPage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_copyPage($page_id,$parent = null,$prefix='',$visibility=PAGE_INACTIVE) {
	## here we will store all vars of this page
	$currentPageInfo = array();
	
	## first we gather all data
	$pageInfo = page_getPageInfo($page_id);	
	
	$currentPageInfo['page_id'] = $page_id;
	$currentPageInfo['type'] = $pageInfo['type'];
	
	$pageInfo = structure_getStructureID($page_id);			
	
	$currentPageInfo['parent'] = isset($parent) ? $parent : $pageInfo['parent'];
	$currentPageInfo['text'] = $pageInfo['text'];
	
	## now start the actual copying of this page
	if($currentPageInfo['type'] == 'folder') {
		## okay we need to make a new folder
		$new_page = folder_createFolder(LANG_NoName);
	} else {
		## copy this page
		$new_page = page_copyPage($currentPageInfo['page_id']);
	}
	
	## register the page in the structure
	$menu_id = structure_storePage($new_page,$currentPageInfo['parent'],$visibility);
		
	## finally rename the page
	structure_setPageName($menu_id,$currentPageInfo['text'].$prefix);
	
	return $menu_id;
}


## =======================================================================        
## structure_getPage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_copySubPages($menu_id,$new_menu_id,$visibility=PAGE_INACTIVE) {
	## okay we need to get all subpages for this page
	$subpages = structure_getAllSubPages($menu_id);
	
	## now we copy all subpages
	foreach($subpages as $current_subpage) {
		$new_submenu_id = structure_copyPage($current_subpage['page_id'],$new_menu_id,'',$visibility);
		
		## okay for each page we need to get all subpages and copy them as well
		structure_copySubPages($current_subpage['id'],$new_submenu_id);
	}	
}




## =======================================================================        
## structure_getWholeBranch     
## =======================================================================        
## pass it a structure id and we will return a list of structure_ids
## that are above this page
##    
## =======================================================================
function structure_getParentPageID($page_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## stores the found pages
	$structure_array = array();
	
	$db_connection = new DB_Sql();
	$query = 'SELECT A.page_id FROM '.STRUCTURE.' AS A INNER JOIN '.STRUCTURE.' AS B ON A.structure_id=B.structure_parent WHERE B.page_id='.$page_id;
	$result = $db_connection->query($query);	
	if($db_connection->next_record()) {
		return $db_connection->Record['page_id'];
	}
} 


## =======================================================================        
## structure_getPage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getStructureByName($vName) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." WHERE structure_text = '$vName' AND client_id=$client_id ORDER BY structure_order";
	$result = $db_connection->query($query);	

	$structureInfo = array();
	
	if($result) {
		if($db_connection->next_record()) {
			$structureInfo["id"] = $db_connection->Record["structure_id"];
			$structureInfo["text"] = $db_connection->Record["structure_text"];
			$structureInfo["url"] = $db_connection->Record["structure_url"];
			$structureInfo["flags"] = $db_connection->Record["structure_flag"];
			$structureInfo["order"] = $db_connection->Record["structure_order"];
			$structureInfo["parent"] = $db_connection->Record["structure_parent"];
			$structureInfo["page_id"] = $db_connection->Record["page_id"];
		}
	}	
	return $structureInfo;
}

## =======================================================================        
## structure_getPage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getStructureID($vID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." WHERE page_id = $vID AND client_id=$client_id";
	$result = $db_connection->query($query);	

	$structureInfo = array();
	
	if($result) {
		if($db_connection->next_record()) {
			$structureInfo["id"] = $db_connection->Record["structure_id"];
			$structureInfo["text"] = $db_connection->Record["structure_text"];
			$structureInfo["url"] = $db_connection->Record["structure_url"];
			$structureInfo["flags"] = $db_connection->Record["structure_flag"];
			$structureInfo["order"] = $db_connection->Record["structure_order"];
			$structureInfo["parent"] = $db_connection->Record["structure_parent"];
			$structureInfo["page_id"] = $db_connection->Record["page_id"];
		}
	}	
	return $structureInfo;
}

## =======================================================================        
## structure_getPageID       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getPageID($vName) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT page_id FROM ".STRUCTURE." WHERE structure_text = '$vName' AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	$pageInfo = array();
	
	if($result) {
		if($db_connection->next_record()) {
			$page_id = $db_connection->Record["page_id"];
		}
	}
	return $page_id;
}

## =======================================================================        
## structure_hasSubPages      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_hasSubPages($vID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db = new DB_Sql();
	$query = "SELECT structure_id FROM ".STRUCTURE." WHERE structure_parent = $vID AND client_id='$client_id'";
	$result = $db->query($query);	

	if($db->num_rows() > 0) {
		return true;
	} else {
		return false;
	}
}

## =======================================================================        
## structure_getMultiPageName       
## =======================================================================        
## returns an array of pge_ids and names that match the items array
##    
## =======================================================================
function structure_getMultiPageName($vItems) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();

	$query = "";	
	for($i=0;$i<count($vItems); $i++) {
		if(isset($vItems[$i])) {
			if($query != "") {
				$query .= " OR ";
			}
		
			$targetPage= $vItems[$i]["page_id"];
			$query .= "page_id ='$targetPage'";
		}
	}

	if($query =="") {
		## we haven't found any... nothing to output
		return;
	}
	
	$query = "SELECT structure_text,page_id FROM ".STRUCTURE." WHERE (".$query.") AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	$pageInfo = array();
	
	if($result) {
		while($db_connection->next_record()) {
			$page_id 		= $db_connection->Record["page_id"];
			$structure_text = $db_connection->Record["structure_text"];
	
			$pageInfo[$page_id]["name"] = $structure_text;
		}
	}
	return $pageInfo;
}

## =======================================================================        
## structure_getHomePage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getHomePage() {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE."  INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE structure_homepage ='1' AND ".STRUCTURE.".client_id='$client_id' LIMIT 1";
	$result = $db_connection->query($query);	

	$pageInfo = array();
	
	if($db_connection->next_record()) {
		$pageInfo["id"] = $db_connection->Record["structure_id"];
		$pageInfo["text"] = $db_connection->Record["structure_text"];
		$pageInfo["url"] = $db_connection->Record["structure_url"];
		$pageInfo["flags"] = $db_connection->Record["structure_flag"];
		$pageInfo["order"] = $db_connection->Record["structure_order"];
		$pageInfo["parent"] = $db_connection->Record["structure_parent"];
		$pageInfo["page_id"] = $db_connection->Record["page_id"];
		$pageInfo["template_id"] = $db_connection->Record["template"];
		$pageInfo["modified"] = $db_connection->Record["modified"];
		$pageInfo["type"] = $db_connection->Record["type"];
	}
	
	return $pageInfo;
}

## =======================================================================        
## structure_getSameLevelPages      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getSameLevelPages($vID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT B.* FROM ".STRUCTURE." AS A, ".STRUCTURE." AS B WHERE A.structure_parent = B.structure_parent AND A.structure_id=$vID AND A.client_id='$client_id' ORDER BY B.structure_order";
	$result = $db_connection->query($query);	
	
	$returnValue = array();
	while($db_connection->next_record()) {
		$pageInfo["parent"] 		= $db_connection->Record["structure_parent"];
		$pageInfo["order"] 			= $db_connection->Record["structure_order"];
		$pageInfo["text"] 			= $db_connection->Record["structure_text"];
		$pageInfo["url"] 			= $db_connection->Record["structure_url"];
		$pageInfo["id"] 			= $db_connection->Record["structure_id"];
		$pageInfo["page_id"] 		= $db_connection->Record["page_id"];
		$pageInfo["structure_flag"] = $db_connection->Record["structure_flag"];			
		## push this record onto the array
		array_push($returnValue,$pageInfo);
	}
	return $returnValue;
}

## =======================================================================        
## structure_getPageUsingPath     
## =======================================================================        
## returns a page if based to the path passed to this function
##    
## =======================================================================
function structure_getPageUsingPath($path) {
	## init the return value
	$page_id = -1;

	## multiclient
	global $Auth;
	$client_id = $Auth->auth["client_id"];

	$levels = count($path);

	if($levels == 0) {
		## this means we need to display the homepage
		$pageInfo = structure_getHomePage();
		$page_id = $pageInfo['page_id'];
	} else {
		## okay we need to process the entries-
		## fetch the first entry- and then get all subpages of this entry
		$db = new DB_Sql();
		$db_inner = new DB_Sql();
		
		$query = "SELECT page_id,structure_id,structure_parent FROM ".STRUCTURE." WHERE structure_url = '".$path[0]."' AND structure_parent='0' AND client_id=$client_id LIMIT 1";
		$result = $db->query($query);	

		if($db->next_record()) {
			$structure_id = $db->Record["structure_id"];
			$page_id = $db->Record["page_id"];
			$structure_parent = $db->Record["structure_parent"];
			
			## for each level we need to check if we can find a page (max levels: 8)
			$max_levels = min(count($path),8);

			for($i=1; $i<$max_levels;$i++) {
				$query = "SELECT page_id,structure_id,structure_parent FROM ".STRUCTURE." WHERE structure_url = '".$path[$i]."' AND structure_parent='".$structure_id."' AND client_id=$client_id LIMIT 1";
				$result = $db_inner->query($query);

				if($db_inner->next_record()) {
					$structure_id = $db_inner->Record["structure_id"];
					$page_id = $db_inner->Record["page_id"];
					$structure_parent = $db_inner->Record["structure_parent"];
				} 
			}
		}
	}

	return $page_id;
}

## =======================================================================        
## structure_getBranch      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getBranchNames($structure_id,$level) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## we get the structure_id and need to get all subpages
	## for this item
	$subpages = array();

	$db_connection = new DB_Sql();
	$query = "SELECT structure_id,page_id,structure_text,structure_flag FROM ".STRUCTURE." WHERE structure_parent = '$structure_id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	while($db_connection->next_record()) {
		## okay for each item we'll find
		## we will get the subpages
		$current_id 	= $db_connection->Record["page_id"];
		$structure_item = $db_connection->Record["structure_id"];
		$structure_flag = $db_connection->Record["structure_flag"];
		$structure_text = strtolower(urlencode($db_connection->Record["structure_text"]));
		
		$structure_text = str_replace("%fc",'ue',$structure_text);
		$structure_text = str_replace("%e4",'ae',$structure_text);
		$structure_text = str_replace("%f6",'oe',$structure_text);
		$structure_text = str_replace("+",' ',$structure_text);	
		
		
		## check the visibility
		$subpages[$level][$structure_text] = $current_id;
		
		## now get all subpages
		$extra_pages = structure_getBranchNames($structure_item,$level+1);
		if(!empty($extra_pages)) {
			foreach($extra_pages as $current_level => $current_entry) {
				foreach($current_entry as $key=>$val) { 
					$subpages[$current_level][$key] = $val; 
				}	
			}
		}
		## get the items for this 
	}
	return $subpages;
} 


## =======================================================================        
## structure_getWholeBranch     
## =======================================================================        
## pass it a structure id and we will return a list of structure_ids
## that are above this page
##    
## =======================================================================
function structure_getBranchNamesUpwards($structure_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## stores the found pages
	$structure_array = array();
	
	$db_connection = new DB_Sql();
	$query = "SELECT structure_parent,structure_text FROM ".STRUCTURE." WHERE structure_id = '$structure_id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	
	$db_connection->next_record();
	
	$current_parent = $db_connection->Record["structure_parent"];
	$structure_text = $db_connection->Record["structure_text"];
	$structure_array[] = $structure_text;
	
	if($current_parent != 0) {
		## we need to call the structure_getBranchUpwards again
		$subpages = array();
		$subpages = structure_getBranchUpwards($current_parent);
		$structure_array = array_merge($structure_array,$subpages);
	}
	return $structure_array;
} 

## =======================================================================        
## structure_getWholeBranch     
## =======================================================================        
## pass it a structure id and we will return a list of structure_ids
## that are above this page
##    
## =======================================================================
function structure_getPath($page_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## stores the found pages
	$structure_array = array();
	
	$db_connection = new DB_Sql();
	$query = "SELECT structure_id,structure_url FROM ".STRUCTURE." WHERE page_id = '$page_id' AND client_id='$client_id'";
	$result = $db_connection->query($query);	
	
	if($db_connection->next_record()) {
		$current_parent = $db_connection->Record["structure_id"];
		$structure_array[] = $db_connection->Record["structure_url"];
	
		$counter = 0;
		while($current_parent != 0) {
			## now get all parent pages
			$query = "SELECT structure_parent,structure_url FROM ".STRUCTURE." WHERE structure_id = '$current_parent' AND client_id='$client_id'";
			$result = $db_connection->query($query);
			if($db_connection->next_record()) {
			
				$current_parent = $db_connection->Record["structure_parent"];
				$structure_array[] = $db_connection->Record["structure_url"];
			
				$counter++;
			} else {
				$current_parent = 0;
			}
			
			if($counter > 10) {
				exit;
			}
		}
	}
	
	## remove the first element
	array_shift($structure_array);
	
	return $structure_array;
} 


## =======================================================================        
## structure_getAllSubPages       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getAllSubPages($vID,$order = NULL,$news_event = FALSE) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$orderby = isset($order) ? $order : 'structure_order';

	$db_connection = new DB_Sql();
	if ($news_event) {
	    // nkowald - modified 23/06/2009 - if page is a news or event then we need to include article date in pageInfo array
	    $query = "SELECT FROM_UNIXTIME(".DB_PREFIX."page_date.date) as date,template,structure_parent,structure_order,structure_text,structure_id,structure_flag,structure_url,".STRUCTURE.".page_id 
		FROM ".STRUCTURE." 
		INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id 
		INNER JOIN ".DB_PREFIX."page_date ON ".STRUCTURE.".page_id = ".DB_PREFIX."page_date.page_id
		WHERE structure_parent = $vID 
		AND ".STRUCTURE.".client_id='$client_id' 
		ORDER BY ".$orderby;
	    $result = $db_connection->query($query);	
	} else {
	    $query = "SELECT template,structure_parent,structure_order,structure_text,structure_id,structure_flag,structure_url,".STRUCTURE.".page_id FROM ".STRUCTURE." INNER JOIN ".USER_PAGES." ON ".STRUCTURE.".page_id = ".USER_PAGES.".page_id WHERE structure_parent = $vID AND ".STRUCTURE.".client_id='$client_id' ORDER BY ".$orderby;
	    $result = $db_connection->query($query);	
	}


	$returnValue = array();
	while($db_connection->next_record()) {
		$pageInfo["parent"] 		= $db_connection->Record["structure_parent"];
		$pageInfo["order"] 			= $db_connection->Record["structure_order"];
		$pageInfo["text"] 			= $db_connection->Record["structure_text"];
		$pageInfo["id"] 			= $db_connection->Record["structure_id"];
		$pageInfo["page_id"] 		= $db_connection->Record["page_id"];
		$pageInfo["template"] 		= $db_connection->Record["template"];
		$pageInfo["structure_flag"] = $db_connection->Record["structure_flag"];		
		$pageInfo["url"] = $db_connection->Record["structure_url"];	

		## push this record onto the array
		array_push($returnValue,$pageInfo);
	}
	return $returnValue;
}

## =======================================================================        
## structure_getSameLevelPages      
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getSubPagesWithTemplate($vID,$template) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT B.* FROM ".STRUCTURE." AS B, ".USER_PAGES." AS C WHERE B.structure_parent=$vID AND B.client_id='$client_id' AND B.page_id=C.page_id AND C.template='$template' ORDER BY structure_order";
	$result = $db_connection->query($query);	
	
	$pageInfo = array();
	$counter = 0;
	while($db_connection->next_record()) {
		$pageInfo[$counter]["parent"] 		= $db_connection->Record["structure_parent"];
		$pageInfo[$counter]["order"] 			= $db_connection->Record["structure_order"];
		$pageInfo[$counter]["text"] 			= $db_connection->Record["structure_text"];
		$pageInfo[$counter]["id"] 			= $db_connection->Record["structure_id"];
		$pageInfo[$counter]["page_id"] 		= $db_connection->Record["page_id"];
		$pageInfo[$counter]["structure_flag"] = $db_connection->Record["structure_flag"];	
		$counter++;
	}
	return $pageInfo;
}
	
	
## =======================================================================        
## structure_getSubPage       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_getSubPage($vID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "SELECT * FROM ".STRUCTURE." WHERE structure_parent = $vID AND client_id='$client_id'";
	$result = $db_connection->query($query);	

	if($db_connection) {
		if($db_connection->next_record()) {
			$pageInfo["structure_id"] = $db_connection->Record["structure_id"];
			$pageInfo["structure_text"] = $db_connection->Record["structure_text"];
			$pageInfo["structure_url"] = $db_connection->Record["structure_url"];
			$pageInfo["structure_flag"] = $db_connection->Record["structure_flag"];
			$pageInfo["structure_order"] = $db_connection->Record["structure_order"];
			$pageInfo["page_id"] = $db_connection->Record["page_id"];
		}
	}
	return $pageInfo;
}

## =======================================================================        
##  structure_promptMove     
## =======================================================================        
function structure_promptMove($pageToBeMoved) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "openwindow.tpl"));

	## prepare the buttons
	$targetURL = "admin.php?op=movedisplaystructure&page_id=".$pageToBeMoved;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('targetURL',$targetURL);
	
	$select_template->pfill_block("body");
}


## =======================================================================        
## structure_MovePage       
## =======================================================================        
## changes the parent of the supplied page (structure_id) to the supplied
## structure id. newParentPage- is a structure id
##    
## =======================================================================
function structure_MovePage($pageToBeMoved,$newParentPage) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## get the structure ids for the supplied pages
	$pageToBeMoved = structure_getStructureID($pageToBeMoved);

	$db_connection = new DB_Sql();
	$query = "UPDATE ".STRUCTURE." SET structure_parent='".$newParentPage."' WHERE structure_id='".$pageToBeMoved['id']."' AND client_id='$client_id'";
	$result = $db_connection->query($query);	
}

## =======================================================================        
##  structure_setState        
## =======================================================================        
##  unsets a specific flag- if it is already set nothing changes
## ======================================================================= 
function structure_unsetStateID($page_id, $vFlag) {
	$pageInfo = structure_getStructureID($page_id);

	## toggle the supplied attributes
	if(checkFlag($pageInfo["flags"],$vFlag)) {
		$vFlag = $pageInfo["flags"] - $vFlag;
	} else {
		$vFlag = $pageInfo["flags"];
	}	

	## then we should update the entry
	structure_setPageFlagsID($page_id,$vFlag);
}

## =======================================================================        
##  structure_setState        
## =======================================================================        
##  sets a specific flag- if it is already set nothing changes
## ======================================================================= 
function structure_setStateID($page_id, $vFlag) {
	$pageInfo = structure_getStructureID($page_id);

	## check if the flag is already set
	if(!checkFlag($pageInfo["flags"],$vFlag)) {
		$vFlag = $pageInfo["flags"] + $vFlag;	
	} else {
		$vFlag = $pageInfo["flags"];
	}
	## then we should update the entry
	structure_setPageFlagsID($page_id,$vFlag);
}



## =======================================================================        
## structure_setPageFlags       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_setPageFlags($vID,$vFlag) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "UPDATE ".STRUCTURE." set structure_flag='$vFlag' where structure_id='$vID' AND client_id='$client_id'";
	$result = $db_connection->query($query);
}

## =======================================================================        
## structure_setPageFlagsID      
## =======================================================================        
## set the menu_entry info using the submitted page_id
##    
## =======================================================================
function structure_setPageFlagsID($vID,$vFlag) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "update ".STRUCTURE." set structure_flag='$vFlag' where page_id='$vID' AND client_id='$client_id'";
	$result = $db_connection->query($query);	
}


## =======================================================================        
## structure_setHomepage      
## =======================================================================        
## sets the homepage and removes the current homepage
##    
## =======================================================================
function structure_setHomepage($vID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	## now we remove the old homepage
	$query = "update ".STRUCTURE." set structure_homepage='0' where structure_homepage='1' AND client_id='$client_id'";
	$result = $db_connection->query($query);

	## first we set the homepage
	$query = "update ".STRUCTURE." set structure_homepage='1' where structure_id='$vID' AND client_id='$client_id'";
	$result = $db_connection->query($query);	

}

## =======================================================================        
## structure_deletePage       
## =======================================================================        
## removes the structure entry identified by the spplied structure_id
##    
## =======================================================================
function structure_delete($vID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$db_connection = new DB_Sql();
	$query = "DELETE FROM ".STRUCTURE." WHERE structure_id = $vID AND client_id='$client_id'";
	$result = $db_connection->query($query);
	
	return 1;	
}

## =======================================================================        
## structure_setPageName       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function structure_setPageName($vID,$vPageName) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	## get the name of this page for the urlrewriting
	$url_string = utility_convertString2URL($vPageName);

	## convert the text
	$vPageName = convert_html(htmlentities($vPageName));
	
	## now we need to check if we already have a page- with the same name and the same parent
	## if yes we need to generate a new name
	$pages = structure_getSameLevelPages($vID);

	$similar_pages = array();
	$current_name = strtolower($vPageName);
	
	foreach($pages as $current_page) {
		if(strtolower($current_page['text']) == $current_name && $vID != $current_page['id']) {
			$similar_pages[] = $current_page['url'];
		}
	}

	$offset = null;
	if(count($similar_pages) > 0) {
		while(in_array($url_string.$offset,$similar_pages)) {
			$offset++;
		}

		## okay we already have a page
		$url_string .= $offset;
	}


	$db_connection = new DB_Sql();
	$vPageName = mysql_real_escape_string($vPageName);
	$query = "UPDATE ".STRUCTURE." SET structure_url='$url_string',structure_text='$vPageName' WHERE structure_id='$vID' AND client_id='$client_id'";
	$result = $db_connection->query($query)  or die(mysql_error());
}

## =======================================================================        
##  structure_deletePage        
## =======================================================================        
function structure_deletePage($vID) {
	## check if there are any subpages
  	$pageInfo = structure_getSubPage($vID);

  	## if we have a subpage we can't delete the thing	
	if(!$pageInfo["structure_text"]) {
		$error_code = structure_delete($vID);
	} else {
		$error_code = -1;
  	}
  	return $error_code;
}

## =======================================================================        
##  structure_toggleState        
## =======================================================================        
##  sets the menue flags: ACTIVE, INACTIVE
## ======================================================================= 
function structure_toggleState($vID, $vFlag) {
	## this function needs to function as a toggle-
	$pageInfo = structure_getPage($vID);
	
	## toggle the supplied attributes
	$vFlag = $pageInfo["flags"] ^ $vFlag;

	## then we should update the entry
	structure_setPageFlags($vID,$vFlag);
}


## =======================================================================        
##  structure_editText        
## =======================================================================        
function structure_editText($vID) {
	global $gSession;
	
	$actionURL = "admin.php";
	$actionURL = $gSession->url($actionURL);
	
	$pageInfo = structure_getPage($vID);
	$tempword = convert_html($pageInfo["text"]);

	display_namepage($vID, $tempword, $actionURL);
}


## =======================================================================        
## _menuMoveItemsOneDown       
## =======================================================================        
## returns the menu_entry info using the submitted menu_id
##    
## =======================================================================
function _menuMoveItemsOneDown($parentID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## we will update all subpages of the supplied parent
	$db_connection = new DB_Sql();
	$query = "UPDATE ".STRUCTURE." SET structure_order=structure_order+1 WHERE structure_parent='$parentID' AND client_id='$client_id'";
	$result = $db_connection->query($query);	
}

## =======================================================================        
##  structure_storePage        
## =======================================================================        
function structure_storePage($vPageId,$vParent,$flags=2) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	$vParent = intval($vParent);

	$db_connectionLayout = new DB_Sql();  	
	$select_query = "select max(structure_order) from ".STRUCTURE." where structure_parent='$vParent' AND client_id='$client_id'";

	$result_pointer = $db_connectionLayout->query($select_query);
	$db_connectionLayout->next_record();
	list($structure_order) = $db_connectionLayout->Record;		

  	$structure_order++;

	$query = "INSERT INTO ".STRUCTURE." (structure_parent,structure_order,structure_text,structure_flag,page_id, client_id) values ('$vParent','$structure_order','".LANG_NoName."','$flags','$vPageId','$client_id')";
	$result_pointer = $db_connectionLayout->query($query);

	$structure_id = $db_connectionLayout->db_insertid($result_pointer);
	
	return $structure_id;
	
}

## =======================================================================        
##  sort_module_byname_doSort        
## =======================================================================        
##  this function does the actual sorting
##	first we determine what pages need to be sorted
##	then we get these pages sorted by their name
##  and finally we loop through all pages and update their order number
##
##  TODO: 
## ======================================================================= 
function structure_storePageFirst($vPageId,$vParent,$flags=2) {
	global $Auth;
	$client_id = $Auth->auth["client_id"];
	
	## first we will increase the order number of all the pages
	$db = new DB_Sql();
	$query = "UPDATE ".STRUCTURE." SET structure_order=structure_order+1 WHERE structure_parent='$vParent' AND client_id='$client_id'";
	$db->query($query);
	
	## now add the new page with the order number 0
	$query = "INSERT INTO ".STRUCTURE." (structure_parent,structure_order,structure_text,structure_flag,page_id, client_id) values ('$vParent','1','".LANG_NoName."','$flags','$vPageId','$client_id')";
	$rp = $db->query($query);
	$structure_id = $db->db_insertid($rp);
	
	return $structure_id;
}


## =======================================================================        
##  structure_promptDelete        
## =======================================================================        
function structure_promptDelete($vID,$vPageId) {
	global $gSession,$Auth;
	$db_connectionLayout = new DB_Sql();  

	## prepare the template file
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("body" => "deletelink.tpl"));
	
	$select_template->set_var("yesIMG","lang/".$Auth->auth["language"]."_button_ja.gif");
	$select_template->set_var("noIMG","lang/".$Auth->auth["language"]."_button_nein.gif");
	$select_template->set_var('language_deletepage',LANG_DeletePage);
	$select_template->set_var('language_doyouwant',LANG_DeletePageWant);
	$select_template->set_var('language_reallydelete',LANG_DeletePageReallyDelete);
	
	## grab the information for this page
	$pageInfo = structure_getPage($vID);
	$menu_text = $pageInfo["text"];
	$menu_url = $pageInfo["url"];
			
	$select_template->set_var('menu_text',$menu_text); 
  
	## prepare the buttons
	$yesURL = "admin.php?op=menu_delete&menu_id=$vID&page_id=$vPageId";
	$yesURL = $gSession->url($yesURL);
	## shozld we load the main page?
	$noURL  = "admin.php?op=edit_menu";
	$noURL = $gSession->url($noURL);        
		
	$select_template->set_var('yesURL',$yesURL);
	$select_template->set_var('noURL',$noURL);
	
	$select_template->pfill_block("body");
}

// nkowald - 2009-10-07 - Could not find a function that returns a page's path as URL

// User-defined array function used to add forward slashes to path url array
function addFwdSlashes($a,$b) {
	return $a . '/' . $b;
}

## =======================================================================        
##  structure_getPathURL     
## =======================================================================  
function structure_getPathURL($page_id) {
	$page_url = structure_getPath($page_id); // get paths as an array
	$page_url = array_reverse($page_url); // reverse array
	$page_url = array_reduce($page_url,"addFwdSlashes"); // add forward slashes
	
	// Now remove first slash
	$page_url = substr($page_url,1);
	
	return $page_url;
}

?>