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
##  template.php														
## =======================================================================
##  Version: 		0.8													
##  Last change: 	05.16.2005												
##  by: 			S. Elsner											
## =======================================================================
##  Description:															
##    * The template classe enables you to abstract HTML code
##      from php code using tpl files				
##
## =======================================================================
class Template {   
      
      var $template_file = array();  
      var $block_items = array();    
      /* $file[handle] = "filename"; */  
      var $file  = array();  
      /* relative filenames are relative to this pathname */  
      var $root   = "";  
      /* $varkeys[key] = "key"; $varvals[key] = "value"; */  
      var $varkeys = array();  var $varvals = array();    
      /* "yes" => halt, "report" => report error, continue, "no" => ignore error quietly */  
      var $halt_on_error  = "no";    
      /* last error message is retained here */  
      var $last_error     = "";  
      
      ## =======================================================================  
      ##  Constructor  
      ## =======================================================================  
      ##   Maybe called with an optional parameter-   
      ##   it sets the template directory  
      ##  
      ## =======================================================================  
      function Template($root = ".") {        
      	## used to include form different locations
      	global $pathOffset;
          
          	$root = $pathOffset.$root;
            if(!is_dir($root)) {            
                  $this->halt("set_root: $root is not a directory.");            
                  return false;        
            }        
            
            $this->root = $root;   
      }  
      
      ## =======================================================================  
      ##  set_file  
      ## =======================================================================  
      ##   The function defines a filename for the initial value of a variable.  
      ##   called with a $handle/$filename pair  
      ##  
      ## =======================================================================  
      function set_templatefile($item,$filename = "") {        
            ## an item is a block of html code                
            ## set the internal pointer of this array        
            ## to the startposition        
            reset($item);        
            
            while(list($h, $f) = each($item)) {              
                  $this->template_file[$h] = $this->get_filepath($f);              
                  $this->make_block($h);        
            }  
      }  
      
      ## =======================================================================  
      ##  get_filepath  
      ## =======================================================================  
      ##   check if we have to add the root path  
      ##   and then check if the file exists  
      ## =======================================================================  
      function get_filepath($filename) {        
            if (substr($filename, 0, 1) != "/") {              
                  $filename = $this->root."/".$filename;        
            }            
            
            if (!file_exists($filename)) {              
                  $this->halt("filename: file $filename does not exist.");        
            }                 
            
            return $filename;  
      }        
      
      ## =======================================================================  
      ##  make_block  
      ## =======================================================================  
      ##  we are actually getting the file, then we cut it according to the   
      ##  and we store the results in our block array. This array can later  
      ##  be called to generate an output string- which can be very long  
      ## =======================================================================  
      ##  $item:   the name of the builiding block-   
      ##           there is a file linked to it  
      ## =======================================================================  
      function make_block($item) {        
            ## here we get the block- reading from the file into the block        
            ## is done somewhere else because we want to keep a clean         
            ## template intact.                     
            ## okay now we fetch the file associated with the $item        
            $newblock = $this->get_templatefile($item);                
            
            ## since there could be more then one block in there,        
            ## we post process the result to contain just the new block       
            ## which we are interested in.                
            ## here we create the pattern we will look for        
            $search_pattern = "/<!--\s+BEGIN $item\s+-->(.*)\n*\s*<!--\s+END $item\s+-->/sm";
            
            ## do the pattern matching        
            preg_match_all($search_pattern, $newblock, $result_matches);        
            $newblock = isset($result_matches[1][0]) ? $result_matches[1][0] : '';                
            ## okay now newblock containes only the items that were surrounded        
            ## be the spezial tag and had the name $item associated with them                
            ## all we have to do now, is to store this object in a block array        
            ## - the block can be later retrieved by using the item name        
            $this->block_items[$item] = $newblock;         
      }    
      
      ## =======================================================================  
      ##  loadfile  
      ## =======================================================================  
      function get_templatefile($handle) {         
            ## is the thing defined?              
            if (!isset($this->template_file[$handle])) {              
                  $this->halt("loadfile: $handle is not a valid handle.");              
                  return false;        
            }
            
            ## retrieve the filenmae associated with this handle        
            $filename = $this->template_file[$handle];        
            ## implode: join array elemnts-> string is the outcome        
            ## right now I assume this actually loads the file         
            ## and puts the result into a var called handle    
            $str = @implode("", @file($filename));        

            if (empty($str)) {              
                  $this->halt("loadfile: While loading $handle, $filename does not exist or is empty.");              
                  return false;        
            }        
            
            return $str;  
      }  
 
      ## =======================================================================  
      ##  set_vars  
      ## =======================================================================  
      function set_vars($vars) {                
      		## loop throug the elements
      		foreach ($vars as $varname => $value) {		
				$this->varkeys[$varname] = "{".$varname."}";
            	$this->varvals[$varname] = $value;  
            }
      }  
           
      ## =======================================================================  
      ##  set_var  
      ## =======================================================================  
      function set_var($varname, $value = "") {                
            $this->varkeys[$varname] = "{".$varname."}";
            $this->varvals[$varname] = $value;  
      }    
       
      ## =======================================================================  
      ##  reset_vars 
      ## =======================================================================  
      function reset_vars() {                
            $this->varkeys = array();
            $this->varvals = array();  
      } 
                  
      ## =======================================================================  
      ##   fill block  
      ## =======================================================================  
      function fill_block($item) {   
            $str = $this->block_items[$item];   
            $str = @str_replace($this->varkeys, $this->varvals, $str);
            return $str;  
             
      }    
      
      /* public: psubst(string $handle)   
      * handle: handle of template where variables are to be substituted.   */  
      function pfill_block($item) {    
            $output = $this->fill_block($item);
           
            print $this->finish($output);    
            return false;  
      }  
      
      /* public: finish(string $str)   * str: string to finish.   */  
      function finish($str) {        
            $str = preg_replace('/{[^ \t\r\n}]+}/', "", $str);    
            return $str;  
       }
       
       function varname($varname) {    
             return preg_quote("{".$varname."}");  
       }  
       
       /***************************************************************************/  
       /* public: halt(string $msg)   * msg:    error message to show.   */  
       function halt($msg) {    
             $this->last_error = $msg;        
             if ($this->halt_on_error != "no")      
                   $this->haltmsg($msg);        
             if ($this->halt_on_error == "yes")      
                   die("<b>Halted.</b>");        
             return false;  
       }    
}
?>