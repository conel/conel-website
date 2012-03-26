<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
## this datatype plugin returns the "path" of the current page
## this can be used to generate a breadcrumb menu
## ======================================================================= 


## =======================================================================        
## path_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function path_displayInput($xmldata, $data) {
}

## =======================================================================        
##  path_storeData        
## =======================================================================        
## save the data in the db
## ======================================================================= 
function path_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  path_getData     
## =======================================================================        
##  get Data
## ======================================================================= 
function path_getData($vPageID,&$page_record) {
}

## =======================================================================        
##  path_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function path_deleteData($vPageID) {
}

## =======================================================================        
##  output_alias       
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function path_output($item,$structure,$template,$menu_id,$page_id) {
	## we recieve the current menu_id
	## so we will build up an array containing all
	## required vars (page_name,page_id).
	$curmbs = array();
	
	$i = 0;
	## first we get the info for the current_page
	$current_page = structure_getPage($menu_id);
	$crumbs[$i] = array('page_id'=>$current_page["page_id"],'text'=>$current_page["text"]);

	## now loop until we have a parent of 0
	while($current_page["parent"] != 0) {
		$i++;
		$current_page = structure_getPage($current_page["parent"]);
		$crumbs[$i] = array('page_id'=>$current_page["page_id"],'text'=>$current_page["text"]);
	}

	## prepare the config parameters
	$seperator = isset($structure['SEPERATOR']) ? $structure['SEPERATOR'] : '/';

	## if home is set, we need to output the homepage too
	$home = isset($structure['HOME']) ? ($structure['HOME']=="true" ? true : null) : null;
	
	if(isset($home)) {
		## then we need to get the homepage
		$current_page = structure_getHomePage();
		
		## and add it to our array
		$i++;
		$text = isset($structure['HOMENAME']) ? $structure['HOMENAME'] : $current_page["text"];
		$crumbs[$i] = array('page_id'=>$current_page["page_id"],'text'=>$text);
	}	

	## generate the string
	$return_string = "";	
	while($i >= 0) {
		## get the page_url
		$page_url = getTargetURL($crumbs[$i]['page_id']);
	
		if($i!=0) {
			$return_string .= '<a href="'.$page_url.'">'.$crumbs[$i]['text'].'</a>';
			$return_string .= $seperator;
		} else {
			$return_string .= $crumbs[$i]['text'];
		}
		$i--;
	}
	
	return $return_string;
}

?>
