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
##  sort_module_byname_init        
## =======================================================================        
##  returns a string containing the name of the module
##	which gets displayed to the user
##
##  TODO:
## =======================================================================
function sort_module_byname_init() {
	global $Auth;
	
	## get the apropriate language file
	require('sort/byname/lang/'.$Auth->auth["language"].'.php');

	## okay now we return the string for our sort method
	return LANG_Sort_ByName_Name;
}

?>
