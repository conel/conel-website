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
##  loadExtraDatatypes        
## =======================================================================        
##  loads the extra datatypes
##  this fucntion is needed for gernal calls like "delete"
##  because there is no xml file to determine the needed extra
##
## =======================================================================
  function loadExtraDatatypes() {
    ## init vars
    $extras = array();

    if ($dir = @opendir('datatypes')) {
      while (($file = @readdir($dir)) !== false) {
        if ($directoryName = strstr($file, 'extra_')) {
        	$extra = substr($file, 6);

        	if(include_once('datatypes/'.$directoryName.'/'.$extra.'.php')) {
        		## we have included the extra,
        		## so we need to set the extra array
        		$extras[] = $extra;
        	}
         }
      }  
      @closedir($dir);
    }
    return $extras;
  }
?>