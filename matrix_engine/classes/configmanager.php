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
  ##  configmanager.php														  
  ## =======================================================================  
  ##  Version: 		0.1													  
  ##  Last change: 	18.04.2003												  
  ##  by: 			S. Elsner											  
  ## =======================================================================  
  ##  Description:															  
  ##    * writes configuartions files. Reading can be done by
  ##      importing the resulting .php file
  ##           - now xml-files to imporvie security for the config data
  ##           - direct php include to increase speed
    
  ## =======================================================================  
	class Configmanager { 

		## name of the file where the configuration should be stored
		## the .php will be added by the script
		var $config_file;

		## path to the config file
		var $config_path = "";
				
		## contains all added config vars
		var $config_buffer;
		
		## the name of the array that will contain the vonfig info
		var $config_var = "config";
		
        ## =======================================================================        
        ##  configmanager        
        ## =======================================================================        
        ##   the constructor - you should pass the path of the config file    
        ##       
        ## =======================================================================        
		function Configmanager($vPath="") {
			## is there a weay to check if the path exists
			$this->config_path = $vPath;
		}
		
        ## =======================================================================        
        ##  setConfigFile        
        ## =======================================================================        
        ##  used to specify the file name of the config-file    
        ##       
        ## =======================================================================        
		function setConfigFile($vFilename) {
			## is there a weay to check if the path exists
			$this->config_file = $vFilename;
		}

        ## =======================================================================        
        ##  setConfigPath       
        ## =======================================================================        
        ##  used to specify the config path   
        ##       
        ## =======================================================================        
		function setConfigPath($vPath) {
			## is there a weay to check if the path exists
			$this->config_path = $vPath;
		}	
		
        ## =======================================================================        
        ##  setConfigName       
        ## =======================================================================        
        ##  used to specify the name of the config var 
        ##       
        ## =======================================================================        
		function setConfigName($vName) {
			## is there a weay to check if the path exists
			$this->config_var = $vName;
		}				

        ## =======================================================================        
        ##  isConfigWriteable        
        ## =======================================================================        
        ##  we should check if the file can be written  
        ##       
        ## =======================================================================        
		function isConfigWriteable() {
			## construct the path/file
			$path = $this->config_path.$this->config_file.".php";
			
			return (@fclose(@fopen($path, 'a')));
		}
				
        ## =======================================================================        
        ##  addConfigVar        
        ## =======================================================================        
        ##  call this function to add a var to the buffer 
        ##       
        ## =======================================================================        
		function addConfigVar($key,$value,$type='string') {
			## convert the var to the specified type
			if ($type == 'string') {
				$value = "\"$value\"";
			} elseif ($type == 'boolean') {
				$value = ($value ? 'true' : 'false');
			} else if ($type == 'integer') {
				$value = intval($value);
			}
	
			## add the var to the buffer
			$this->config_buffer .="\$".$this->config_var.'["'.$key.'"]='.$value.';'."\r";
		}
		
        ## =======================================================================        
        ##  writeConfigFile        
        ## =======================================================================        
        ## flush the buffer into the config file
        ##       
        ## =======================================================================        
		function writeConfigFile() {
			## construct the path/file
			$path = $this->config_path.$this->config_file.".php";		
		
			## if the buffer is empty- the file will be emptied
			if($handle = @fopen($path,'w')) {
				$data = "<?php \r".$this->config_buffer."?>";
				$result = @fwrite($handle, $data);
				@fclose ($handle);
				
				return $result;
			} else {
				return(false);
			}
		}
				
        ## =======================================================================        
        ##  readConfigFile        
        ## =======================================================================        
        ## import the config file
        ##       
        ## =======================================================================        
		function readConfigFile(){
			$path = $this->config_path.$this->config_file.".php";
			
			include($path);
			eval("\$config = \$".$this->config_var.";");
		
			if (!isset($config) || !is_array($config)) {
				echo "couldn't not open the file";
			}	
			return $config;
		}
	}


?>