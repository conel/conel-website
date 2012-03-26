<?php  
	## =======================================================================  
	##  file.php														  
	## =======================================================================  
	##  Version: 		0.03													  
	##  Last change: 	10.11.2000												  
	##  by: 			S. Elsner											  
	## =======================================================================  
	##  Description:															  
	##    * handles all file related things		  
	## =======================================================================  
	##  15.05.2001:  
	##    * this is a new implementation of the file.php code
	##      it does only allow images files to be uploaded. it also
	##      enables us to control what should happen if we need
	##      to replace a file or if we don't want to allow this
	##      we are verystrict with the name of the files-> only lowercase and 
	##      underscores are allowed.
	##  
	##  09.01.2001:  
	##    * now allows to upload freehandfiles as well
	##  
	##  16.12.2000:  
	##    * changed the base directory to workmatrix.intern
	##      now I need to add a unix name to the project db and
	##      then we can upload files to a folder based on the project 
	##  
	##  08.12.2000:  
	##    * added debugging output
	##    * the pathname needs a fix  
	##  
	##  17.11.2000:  
	##    * fixed bug when uploading a file that already exists  
	##  
	##  10.11.2000:  
	##    * updated the file object- it will recieve the project name  
	##      where it will store the files  
	##  
	##    * the object now creates a folder in the files_root die  
	## =======================================================================  
	class file_object { 
		## the base path for files
		var $files_root			= MATRIX_BASEDIR;        
	
		## this is the bare minimum for the file upload - should be extended by the function calling
		var $allowed_extensions = array('.jpg','.jpeg','gif');      
	
		## =======================================================================        
		##  validate_upload        
		## =======================================================================        
		##   The function defines a filename for the initial value of a variable.        
		##   called with a $handle/$filename pair        
		##        
		## =======================================================================        
		function validate_upload($the_file,$the_file_type,$the_allowed_types=null) {	          
			## Did we get a file?	          
			if ($the_file == "none") { 		            		            
				return false;		     
			}
	
			## we need to check if the file was really uploaded
			if (!is_uploaded_file($the_file)) {
				return false;
			}
			/*
			## check if the filetype is allowed  
			if(isset($the_allowed_types)) {
				$this->allowed_types = $the_allowed_types;
			}
			
	
			if (!in_array($the_file_type,$this->allowed_types)) {						
				return false;		     
			}
			*/
			return true;
		}        

		## =======================================================================        
		##  clean_Filename        
		## =======================================================================           
		##        
		## =======================================================================        
		function clean_Filename($name, $maxlen=250) {
        	$noalpha = 'ÁÉÍÓÚÝáéíóúýÂÊÎÔÛâêîôûÀÈÌÒÙàèìòùÄËÏÖÜäëïöüÿÃãÕõÅåÑñÇç@°ºª';
        	$alpha   = 'AEIOUYaeiouyAEIOUaeiouAEIOUaeiouAEIOUaeiouyAaOoAaNnCcaooa';

        	$name = substr($name, 0, $maxlen);
        	$name = strtr($name, $noalpha, $alpha);
        	// not permitted chars are replaced with "_"
        	return preg_replace('/[^a-zA-Z0-9,._\+\()\-]/', '_', $name);
    	}
    
    
        ## =======================================================================        
        ##  upload        
        ## =======================================================================        
        ##   The function defines a filename for the initial value of a variable.        
        ##   called with a $handle/$filename pair        
        ##        
        ## =======================================================================        
        function upload($the_file ,$the_file_name,$the_file_type,$the_file_size,$the_target_dir,$mode=2) {          	          
			## we will generate a reuslt, which is returned to the calling object	          
			$error = 0;	          	          
			##$error = $this->validate_upload($the_file,$the_file_type);	          
			
			##if (!$error) {		      
			##      $this->haltmsg($error);		      
			##      return;		      
			##}      		      		      

			## okay we are supposed to upload this into a certain directory		      
			if (!is_dir($the_target_dir)) {		         
			      ## seems like this directory doesn't exist		         
			      ## let's create it	
			      $this->make_directory($the_target_dir);		         		         
			      ## there should be some error handling		         
			      ## if we failed creating the dir		      
			}   
	         
			## otherwise we now know the file exists		         
			## so we set up the path		      
			$target_path = $the_target_dir;   	          	          
			## before uploading the file, we check if a file with that name	          
			## already exists in the directory-> if yes. we should return an error	
			
			## okay we check the filename- if it is valid
			  ## very strict naming of file.. only lowercase letters, numbers and underscores
			 $new_name = ereg_replace("[^a-z0-9._]", "", ereg_replace(" ", "_", ereg_replace("%20", "_", strtolower($the_file_name))));

			  ## check for extention and remove it- sow we can later increment the file name
			  if(ereg("(\.)([a-z0-9]{3,5})$", $new_name)) {
				$pos = strrpos($new_name, ".");
				$file_extension = substr($new_name, $pos, strlen($new_name));
				$new_name = substr($new_name, 0, $pos);	
			  }
			  
			  $base_name = $new_name;
			  $new_name = $new_name . $file_extension;

			  ## depending on the mode we will upload the file into the directory
			  switch($mode) {
				case 1:     ## overite mode
					$aok = copy($the_file, $target_path . $new_name);
					break;
				case 2:     ## create new with incremental extension
					$n = 0;
					$copy = "_" . $n;
					while(file_exists($target_path . $base_name . $copy . $file_extension)) {
						$copy = "_" . $n;
						$n++;
					}
					
					$new_name  = $base_name . $copy . $file_extension;
					if(file_exists($the_file)) {
						$aok = copy($the_file, $target_path . $new_name);
					}
					break;
					
				case 3:     ## do nothing if exists, highest protection
					if(file_exists($target_path . $new_name)) {
						## we should return an error
					} else {
						$aok = copy($the_file, $target_path . $new_name);
					}
					break;
				default:
					break;
			}
			
			if ($aok == 1) {
				$aok = $new_name;
			}
			return $aok;			          
        }			   
        
        ## =======================================================================        
        ##  make_directory        
        ## =======================================================================        
        ##   makes a directory within the file area        
        ## =======================================================================        
        function make_directory($the_target_dir) {			
			$target_path = $the_target_dir;						
			umask(000); 
			if (!mkdir($target_path, 01707)) {			            
			      return False;			      
			} else {			            
			      return True;			      
			}        
        }        


		## =======================================================================        
		##  file_size      
		## =======================================================================        
		##   makes a directory within the file area        
		## =======================================================================        
		function file_size($the_file_path) { 
			## this should be a function in the future           
			$path = $the_file_path;
			return(filesize($path));
		}
		        
		## =======================================================================        
		##  read file        
		## =======================================================================        
		##   makes a directory within the file area        
		## =======================================================================        
		function read($the_file_path) { 
			## this should be a function in the future           
			$path = $the_file_path;
			if ($fp = fopen($path, "r")) {
				$contents = fread($fp, filesize($path));
				fclose($fp);
				return $contents;
			} else {
				return false;
			}
		}

        ## =======================================================================        
        ##  copy_file        
        ## =======================================================================        
        ##  we need to create a new instance with a unqiue incremnt       
        ## =======================================================================        
        function copy_file($the_file,$the_file_name,$target_path) {  
			
			if(ereg("(\.)([a-z0-9]{3,5})$", $the_file_name)) {
				$pos = strrpos($the_file_name, ".");
				$file_extension = substr($the_file_name, $pos, strlen($the_file_name));
				$the_file_name = substr($the_file_name, 0, $pos);	
			}
			
			$n = 0;
			$copy = "_" . $n;
			while(file_exists($target_path . $the_file_name . $copy . $file_extension)) {
				$copy = "_" . $n;
				$n++;
			}
			
			$new_name  = $the_file_name . $copy . $file_extension;
			$aok = copy($the_file, $target_path . $new_name);
			
			return $new_name;
        } 

        ## =======================================================================        
        ##  delete_file        
        ## =======================================================================        
        ##   makes a directory within the file area        
        ## =======================================================================        
        function delete_file($the_file_path) {  
        	$path = $the_file_path;           
			@unlink($path);
        } 
			  
        ## =======================================================================        
        ##  $this->haltmsg        
        ## =======================================================================        
        ##   handle the error here for now        
        ## =======================================================================        
        function haltmsg($msg) {			
			printf("<b>file_object:</b> %s<br>\n", $msg);        
        }
        
        ## =======================================================================        
        ##  clean_directory      
        ## =======================================================================        
        ##  deletes all files from the given directory     
        ## =======================================================================        
		function clean_directory($dir){
			if(!isset($dir)){
				exit;
			}
  			$current_dir = opendir($dir);
  			while($entryname = readdir($current_dir)){
     			##if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
        		##	deldir("${dir}/${entryname}");
     			##}else
	 			if($entryname != "." and $entryname!=".."){
        			unlink("${dir}/${entryname}");
     			}
  			}
  			closedir($current_dir);
		} 
  }
?>
