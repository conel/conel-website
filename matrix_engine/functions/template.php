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
##  template_getTemplate      
## =======================================================================        
##  gets the Template Info via a page_id 
##
##  TODO: needs error checking
## ======================================================================= 
function template_getTemplate($page_id) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$templateInfo = array();
	
	## database connection
	$db_connection = new DB_Sql();
	
	## grab the information for this page
	$query = "SELECT icon,description,basename,parent,template_id, ".PAGE_TEMPLATE.".title FROM ".PAGE_TEMPLATE." INNER JOIN ".USER_PAGES." ON ".PAGE_TEMPLATE.".template_id=".USER_PAGES.".template WHERE page_id = '$page_id' AND ".USER_PAGES.".client_id='$client_id'";
	$result_pointer = $db_connection->query($query);
	$db_connection->next_record();

	$templateInfo['icon'] = $db_connection->Record["icon"];
	$templateInfo['description'] = $db_connection->Record["description"];
	$templateInfo['basename'] = $db_connection->Record["basename"];
	$templateInfo['template_id'] = $db_connection->Record["template_id"];
	$templateInfo['title'] = $db_connection->Record["title"];
	$templateInfo['parent'] = $db_connection->Record["parent"];

	## return the templateInfo
	return $templateInfo;
}

## =======================================================================        
##  template_getTemplate        
## =======================================================================        
##  retrieves the template using the template_id
##
##  TODO:
##  
## ======================================================================= 
function template_getTemplateID($vID) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];

	## prepare the db-object
	$db_connection = new DB_Sql();
	## then we need to get the xmlfile for this template
	$select_query = "SELECT * FROM ".PAGE_TEMPLATE." WHERE template_id=".$vID." AND client_id='$client_id'";
	$result = $db_connection->query($select_query);

	$templateInfo = array();
	if($result) {
		if($db_connection->next_record()) {
			$templateInfo["id"] 			= $db_connection->Record["template_id"];
			$templateInfo["title"] 			= $db_connection->Record["title"];
			$templateInfo["icon"] 			= $db_connection->Record["icon"];
			$templateInfo["description"]	= $db_connection->Record["description"];
			$templateInfo["basename"] 		= $db_connection->Record["basename"];
			$templateInfo["parent"] 		= $db_connection->Record["parent"];
		}
	}	
	return $templateInfo;		
}

## =======================================================================        
##  template_getTemplateList        
## =======================================================================        
##  get's all templates or some if the parent flag is set
##
##  NEW:
##  	- checks if the flag hiddenor notselectable are set
## ======================================================================= 
function template_getTemplateList($parent) {
	global $Auth;

	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## prepare the db-object
	$db_connection = new DB_Sql();

	## first we get the level
	if($parent > 0) {
		$level = structure_getBranchUpwards($parent);
		$level = count($level)+1;
	} else {
		$level = -1;
	}
	
	$getallentries = true;
	if($parent != 0) {
		## first get the page
		$pageInfo = structure_getPage($parent);
		$page = $pageInfo["page_id"];

		## we have the current page_id - lets get the template	
		$templateInfo = template_getTemplate($page);
		$parent_name  = $templateInfo['basename']; 
	} 
	

	if($getallentries) {	
		## in this case we need to find out the level we are currently in.
		## we should get them all		
		$select_query = "SELECT title,icon,template_id,description,basename,parent FROM ".PAGE_TEMPLATE." WHERE (level=-1 OR level = '$level') AND client_id='$client_id' AND (hidden != '1') ORDER BY title";
		$result_pointer = $db_connection->query($select_query);
	}	
			
	## we must have found somthing by this time:
	## so start processing
	$templates = array();
	$filtered_templates = array();
	$counter   = 0;
	while($db_connection->next_record()) {
		## we need to check if we have a parent name
		if(empty($parent_name) && empty($db_connection->Record["parent"])) {
			## get the data
			$templates[$counter]['title'] 		= $db_connection->Record["title"];
			$templates[$counter]['icon']		= $db_connection->Record["icon"];
			$templates[$counter]['template_id'] = $db_connection->Record["template_id"];
			$templates[$counter]['description'] = $db_connection->Record["description"];
			$templates[$counter]['basename'] 	= $db_connection->Record["basename"];
			$templates[$counter]['parent'] 	= $db_connection->Record["parent"];
			
			$counter++;			
		} else if(empty($db_connection->Record["parent"])){
			## get the data
			$templates[$counter]['title'] 		= $db_connection->Record["title"];
			$templates[$counter]['icon']		= $db_connection->Record["icon"];
			$templates[$counter]['template_id'] = $db_connection->Record["template_id"];
			$templates[$counter]['description'] = $db_connection->Record["description"];
			$templates[$counter]['basename'] 	= $db_connection->Record["basename"];
			$templates[$counter]['parent'] 	= $db_connection->Record["parent"];
			
			$counter++;			
		} else if(!empty($db_connection->Record["parent"])) {
			## first we  will remove any spaces
			
			$parent_templates = str_replace(" ","",$db_connection->Record["parent"]);
			$parent_templates = explode(',',$parent_templates);


			## now check if the current entry equals any of the entries
			if(in_array($parent_name,$parent_templates)) {
				## get the data
				$filtered_templates[$counter]['title'] 		= $db_connection->Record["title"];
				$filtered_templates[$counter]['icon']		= $db_connection->Record["icon"];
				$filtered_templates[$counter]['template_id'] = $db_connection->Record["template_id"];
				$filtered_templates[$counter]['description'] = $db_connection->Record["description"];
				$filtered_templates[$counter]['basename'] 	= $db_connection->Record["basename"];
				$filtered_templates[$counter]['parent'] 	= $db_connection->Record["parent"];
				
				$counter++;	
			}
		} 
	}

	if(!empty($filtered_templates)) {
		return $filtered_templates;
	} else {
		return $templates;	
	}
}



?>
