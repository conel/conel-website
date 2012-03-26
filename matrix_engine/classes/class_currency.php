<?php

	## =======================================================================
	##  class_currency.php														
	## =======================================================================
	##  Version: 		0.01													
	##  Last change: 	31.12.006												
	##  by: 			S. Elsner											
	## =======================================================================
	##
	##  handles the conversion of currencies
	## =======================================================================
	class currencyConverter {
		## here we will store the cache files for the currency converter
		var $cachePath = MATRIX_CACHEDIR;
		var $cacheTimeout = 800;

        ## =======================================================================        
        ##  currencyConverter        
        ## =======================================================================        
        ##  construct    
        ##       
        ## =======================================================================        		
		function currencyConverter() {
		
		}

        ## =======================================================================        
        ##  doConvert        
        ## =======================================================================        
        ##  will handle the conversion    
        ##       
        ## =======================================================================        		
		function doConvert($currency_from, $currency_to, $amount = 1) {
			## init the vars
			$value = 0;
			
			## prepare the data to be passed to yahoo
			$from = strtoupper($currency_from);
  			$to   = strtoupper($currency_to);
  			
  			## check if we have a cache file that is still valid
  			$data = $this->cacheExists($currency_from, $currency_to);
  			
  			if($data=== false) {
  				## okay we need to get fresh data from yahoo
				$url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='.$from.''.$to.'=X';

				## now we make the connection
				$handle = @fopen($url, 'r');
				if ($handle) {
					if ($record = fgets($handle, 4096)) {
						fclose($handle);
      
      					$currency_data = explode(',', $record);
      					$rate = $currency_data[1];
						$date = strtotime(str_replace('"','',$currency_data[2]));
					
						$time = $currency_data[3];			
			
						## caluclate the result
						$value = $amount * $rate;

						## finally we will store the data
						$this->cacheWriteFile($currency_from, $currency_to,$rate);						
					}
				}	
			} else {
				$value = $amount * $data;	
			}
			
			## finally return the converted value
			return $value;
		}
		
	
        ## =======================================================================        
        ##  doConvert        
        ## =======================================================================        
        ##  will handle the conversion    
        ##       
        ## =======================================================================        		
		function cacheExists($currency_from, $currency_to) {
			## check if we have a file and if it is still valid
			$file = $this->cachePath.'/'.$currency_from.'_'.$currency_to.'.cc';
			
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
			if((time() - $data['date']) < $this->cacheTimeout) {
				## okay we return the data
				return $data['output'];
			} else {
				return false;
			}        	
		}
		
		
	
        ## =======================================================================        
        ##  doConvert        
        ## =======================================================================        
        ##  will handle the conversion    
        ##       
        ## =======================================================================        		
		function cacheWriteFile($currency_from, $currency_to,$output) {
			## first we prepare the data
			$data = array();
			
			## prepare the data
			$data['date'] = time();
			$data['output'] = $output;
			
			## serialize the data
			$buffer = serialize($data);

			## check if we have a file and if it is still valid
			$file = $this->cachePath.'/'.$currency_from.'_'.$currency_to.'.cc';
			
			$fp=@fopen($file, "w");	
			if ($fp) {
				fwrite($fp, $buffer, strlen($buffer));
				fclose($fp);
			}       	
		}		
	}

?>