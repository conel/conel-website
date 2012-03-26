<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
define('PAGECACHE_TIMEOUT', 600);

## =======================================================================        
##  pagecache_checkfile        
## =======================================================================        
##  locks a page- this is done by calling this function using the page_id
##  and the user_id- it will lock the page and update the structure flags
##  which it will return for processing 
## ======================================================================= 
function pagecache_getCacheFile($page_id) {
	global $input_language;
	## okay- in order to allow previews- we check
	## if we are in preview mode- if yes we return false
	global $previewMode;
	
	if(isset($previewMode) && $previewMode == true) {
		return false;
	}
	
	if($page_id > 0) {
	## check if we can find a cache file
		if($input_language != DEFAULT_INPUTLANGUAGE) {
			$file = MATRIX_CACHEDIR.'/'.$page_id.'_'.$input_language.'.scp';
		} else {
			$file = MATRIX_CACHEDIR.'/'.$page_id.'.scp';
		}	
	
       $fp=@fopen($file, "r");
        if (!$fp) {
           return false;
        }
        flock($fp, LOCK_SH);
        $buff="";
        while (($tmp=fread($fp, 4096))) {
            $buff.=$tmp;
        }
        fclose($fp);
        
        ## get the data
        $data = unserialize($buff);
        
        ## check the date
        if((time() - $data['date']) < PAGECACHE_TIMEOUT) {
        	## okay we return the data
        	return $data['output'];
        } else {
        	return false;
        }
	} else {
		return false;
	}
}

## =======================================================================        
##  pagecache_updatefile       
## =======================================================================        
##  remove a lock from a page- this is done by simply deleting the 
##  lock and updating the structure flags
##  which it will return for processing 
## ======================================================================= 
function pagecache_createCacheFile($page_id,$output) {
	global $input_language;
	
	## we won't write any files- if we are in preview mode
	global $previewMode;

	if(isset($previewMode) && $previewMode == true) {
		return false;
	}
	
	## first we prepare the data
	$data = array();
	
	## prepare the data
	$data['date'] = time();
	$data['output'] = $output;
	
	## serialize the data
	$buffer = serialize($data);

	## okay save this file to the cache
	if($input_language != DEFAULT_INPUTLANGUAGE) {
		$file = MATRIX_CACHEDIR.'/'.$page_id.'_'.$input_language.'.scp';
	} else {
		$file = MATRIX_CACHEDIR.'/'.$page_id.'.scp';
	}	

   	$fp=@fopen($file, "w");	
	if ($fp) {
		fwrite($fp, $buffer, strlen($buffer));
		fclose($fp);
	}
}


## =======================================================================        
##  pagecache_updatefile       
## =======================================================================        
##  remove a lock from a page- this is done by simply deleting the 
##  lock and updating the structure flags
##  which it will return for processing 
## ======================================================================= 
function pagecache_deleteCacheFile($page_id) {
	global $input_language;
	## first we prepare the data
	## okay save this file to the cache
	## okay save this file to the cache
	if($input_language != DEFAULT_INPUTLANGUAGE) {
		$file = MATRIX_CACHEDIR.'/'.$page_id.'_'.$input_language.'.scp';
	} else {
		$file = MATRIX_CACHEDIR.'/'.$page_id.'.scp';
	}	

   	$fp=@unlink($file);		
}

?>
