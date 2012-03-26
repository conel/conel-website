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
## include_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function include_displayInput($xmldata, $data) {
	return "";
}


## =======================================================================        
##  include_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function include_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  include_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function include_getData($vPageID,&$page_record) {
}

## =======================================================================        
##  include_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function include_deleteData($vPageID) {
	return true;
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function output_include($item,$structure,$menu_id,$page_id=0) {
	$value = "";
	$param = array();

	if($structure['FUNCTION']!="") {
		## okay here we check for files to be included
		## we will call the function using the eval
		if(isset($structure['PARAMETER'])) {
			$param['parameter'] = $structure['PARAMETER'];
		}
		$param['page_id'] = $page_id;
			
		$value = $structure['FUNCTION']($menu_id,$param);
	}
	return $value;
}
?>
