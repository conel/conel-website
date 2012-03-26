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
  ##  xmlparser.php														  
  ## =======================================================================  
  ##  Version: 		1.2.1													  
  ##  Last change: 	20.5.2003												  
  ##  by: 			S. Elsner											  
  ## =======================================================================  
  ##  Description:															  
  ##    * handles the xml description files- so we know what the var
  ##      names are the user typed in....	  
  ## =======================================================================  
	class xmlparser { 

		## stores the different items that where found      
		var $objects = array();  
		var $cache_objects = array();
		var $normal_objects = array();
			
		## stores the current block (header, task, link)        
		var $current_block = "";  
		## always contains the current tag (on close this
		## should be reset
		var $current_tag = ""; 
        
		## store the parser object
		var $xml_parser;
        
		## here we store the actual metafile name
		## the default is the simple file
		var $xml_file ="simple.xml";   
                
		## okay we save all inputfilednames and its types in here
		var $element_object = array();
        
		## here we store all element information
		var $page_object = array();
		var $cache_object = array();
		var $normal_object = array();
		
		## the page counter
		var $page_counter = 0;
		
		## page/section title- we need to handle this!
		var $page_name = '';
						
		## =======================================================================        
		##  xmlparser        
		## =======================================================================        
		##   the constructor - expects the varname to be parsed as input    
		##       
		## =======================================================================        
        function xmlparser($wft_file) {
			## the xml-file musst be specified on constuction
			## used to include form different locations
			$this->xml_file = $wft_file;
        } 

        ## =======================================================================        
        ##  startElement        
        ## =======================================================================        
        ##   this method gets called when ever we hit a start tag       
        ##       
        ## ======================================================================= 
        function startElement($parser,$name,$attrs) {
              $this->current_tag = $name;
                                        
              switch($name) {
                    case 'PAGE_HEAD' : {
                          $this->current_block = $name;
                          break;
                    }
                    case 'PAGE_BODY' : {
                    	  ## we need to set the page counter
                          $this->current_block = $name;
                          $this->page_name = isset($attrs['NAME']) ? $attrs['NAME'] : '';
                          break;
                    }                   
                    case 'PAGE' :
                    case 'TAG' :
                    case 'OWNER' :
                    case 'CREATION_DATE' :
                    case 'TITLE' :
                    case 'PARAMETER' :
                    case 'DESCRIPTION' : {
                          $this->current_block = $name;
                          break;
                    }    
                    case 'LINK' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'LINK';
                          $this->element_object['NAME'] = $attrs['NAME'];                          
                          break;
                    }  
                    case 'IMAGE' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'IMAGE';
                          $this->element_object['NAME'] = $attrs['NAME'];
                          $this->element_object['WIDTH'] = isset($attrs['WIDTH']) ? $attrs['WIDTH'] :  null;
                          $this->element_object['HEIGHT'] = isset($attrs['HEIGHT']) ? $attrs['HEIGHT'] :  null;
                          $this->element_object['LANG'] = isset($attrs['LANG']) ? $attrs['LANG'] : null;
                          $this->element_object['ALT'] = isset($attrs['ALT']) ? $attrs['ALT'] : null; 
                          $this->element_object['SHOWALT'] = (isset($attrs['SHOWALT']) && strtolower($attrs['SHOWALT']) == 'true') ? true : false;
                          break;
                    }
                    case 'FILE' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'FILE';
                          $this->element_object['NAME'] = $attrs['NAME'];                           
                          break;
                    }                      
                    case 'LISTVIEW' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'LISTVIEW';
                          $this->element_object['NAME'] = $attrs['NAME'];
                          $this->element_object['TEMPLATE'] = $attrs['TEMPLATE']; 
                          $this->element_object['CATEGORY'] = $attrs['CATEGORY']; 
                          $this->element_object['FIELD']    = isset($attrs['FIELD']) ? $attrs['FIELD'] : null;
                          $this->element_object['COUNT']    = isset($attrs['COUNT']) ? $attrs['COUNT'] : null;
                          $this->element_object['SCOPE']    = isset($attrs['SCOPE']) ? $attrs['SCOPE'] : null;
                          $this->element_object['SORT']    = isset($attrs['SORT']) ? $attrs['SORT'] : null;
                          $this->element_object['SORTBY']    = isset($attrs['SORTBY']) ? $attrs['SORTBY'] : null;
                          $this->element_object['PAGENAME']    = isset($attrs['PAGENAME']) ? $attrs['PAGENAME'] : null;
                          $this->element_object['HIGHLIGHT']    = isset($attrs['HIGHLIGHT']) ? $attrs['HIGHLIGHT'] : null;
                          
                          if(isset($attrs['HIGHLIGHT']) && $attrs['HIGHLIGHT']=="YES") {
                          	$this->element_object['HIGHLIGHT'] = true;	
                          }
                          $this->element_object['ORDERDIRECTION']   = isset($attrs['ORDERDIRECTION']) ? $attrs['ORDERDIRECTION'] : null;                         
                          break;
                    }  
                    case 'SEARCH' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'SEARCH';
                          $this->element_object['NAME'] = $attrs['NAME'];
                          $this->element_object['PAGE'] = $attrs['PAGE'];  
                          $this->element_object['LIMIT']    = $attrs['LIMIT'];                         
                          break;
                    } 
                    case 'SEARCHRESULT' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'SEARCHRESULT';
                          $this->element_object['NAME'] = $attrs['NAME'];
                          $this->element_object['TEMPLATE'] = $attrs['TEMPLATE'];  
                          $this->element_object['LIMIT']    = $attrs['LIMIT'];
                          $this->element_object['CACHE']    = $attrs['CACHE'];
                          break;
                    }                                                             

                    case 'DIVIDER' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'DIVIDER';
                          $this->element_object['NAME'] = 'NAME'; 
                          break;                          
                    }                                                                                                                  
                    case 'TAG' :
                    case 'DESCRIPTION' :
							if($this->current_block == 'PAGE_BODY') {
                          		$this->current_block = $name;
                          	}                          
                          break;    
                    case 'LINKLIST' : {
                          $this->current_block = $name;
                          $this->element_object['TYPE'] = 'LINKLIST';
                          $this->element_object['NAME'] = $attrs['NAME'];
                          $this->element_object['MAXCOUNT'] = isset($attrs['MAXCOUNT']) ? $attrs['MAXCOUNT'] : null;
                          $this->element_object['TEMPLATE'] = $attrs['TEMPLATE']; 
                          $this->element_object['TITLE'] = isset($attrs['TITLE']) ? $attrs['TITLE'] : null;
                          $this->element_object['CACHE']    = isset($attrs['CACHE']) ? $attrs['CACHE'] : true;
                          break;
                    } 
                    case 'INCLUDE' : {
                    	  $this->current_block = $name;
                    	  $this->element_object['TYPE'] = $name;
                    	  $this->element_object['CACHE'] = false;
                    	  
                          while (list($key, $val) = each($attrs)) {
                          	$this->element_object[$key] = $val;
                          }                         
                          break;                          
                    }                      
                    default: {
                    	  $this->current_block = $name;
                    	  $this->element_object['TYPE'] = $name;
                    	  ##$this->element_object['CACHE'] = true;
                    	  
                          while (list($key, $val) = each($attrs)) {
                          	$this->element_object[$key] = $val;
                          }
                         ## echo "<br>";
                    }
                                                                                                                 
		}   
	}

        ## =======================================================================        
        ##  endElement        
        ## =======================================================================        
        ##   called when the tags are closed            
        ##        
        ## =======================================================================                 
        function endElement($parser, $name) {
              ## here we rest the current tag
              $this->current_tag = "";
              
              switch($name) {
                    case 'PAGE_HEAD' : {
                          ## we should delete the current block this is standard
                          $this->current_block = '';
        
                          break;
                    }  
                    case 'PAGE_BODY' : {
                          $this->page_object[$this->page_counter]['NAME'] = $this->page_name;
                          
                          ## we should delete the current block this is standard
                          $this->current_block = '';
        				  ## we should increase the counter
        				  $this->page_counter++;
        				  
        				  $this->page_name = '';
                          break;
                    }                      
                    case 'PAGE' :
                    case 'TAG' :
                    case 'OWNER' :
                    case 'CREATION_DATE' :
                    case 'TITLE' :
                    case 'PARAMETER' :
                    case 'DESCRIPTION' : {
                    	  $this->current_block = '';
                          break;
                    }                        
                    default: {
                          ## we should delete the current block this is standard
                          $this->current_block = '';
                          ## okay we reached the end of this element- so we store this element
                          ## into our main array- and then reset the collector array.
                          $current_element = $this->element_object;

                          if(isset($current_element['CACHE']) && ($current_element['CACHE'] == false || $current_element['CACHE'] == "false")) {
							  ## store the thing- needs to be checked!
							  if(isset($this->cache_object[$this->page_counter])) {
								$num_elements = count($this->cache_object[$this->page_counter]);
							  } else {
								$num_elements = 0;
							  }
							  $this->cache_object[$this->page_counter][$num_elements++] = $current_element;

							  ## store the object
							  $this->cache_objects[$current_element['TYPE']] = true;
						  } else {
							  ## store the thing- needs to be checked!
							  if(isset($this->normal_object[$this->page_counter])) {
								$num_elements = count($this->normal_object[$this->page_counter]);
							  } else {
								$num_elements = 0;
							  }
							  $this->normal_object[$this->page_counter][$num_elements++] = $current_element;

							  ## store the object
							  $this->normal_objects[$current_element['TYPE']] = true;
						  }						  

						  if(isset($this->page_object[$this->page_counter])) {
							$num_elements = count($this->page_object[$this->page_counter]);
						  } else {
							$num_elements = 0;
						  }					  
						  $this->page_object[$this->page_counter][$num_elements++] = $current_element;

						  ## store the object
						  $this->objects[$current_element['TYPE']] = true;

                          $this->element_object = '';
                          break;
                    }

              }                 

        }     

        ## =======================================================================        
        ##  characterData        
        ## =======================================================================        
        ##   called beetween the tags             
        ##        
        ## =======================================================================         
        function characterData($parser, $data) {
        	if($data!='') {
              switch($this->current_tag) {
                    case 'TAG':            
                          $this->element_object['TAG'] .= htmlentities(utf8_decode($data));
                          break;
                    case 'DESCRIPTION': 
                          $this->element_object['DESC'] .= htmlentities(utf8_decode($data));
                          break;                          
              }                   
        	}
         }  

        ## =======================================================================        
        ##  destroy        
        ## =======================================================================        
        ##   call this method to kill the parser               
        ##        
        ## =======================================================================         
        function destroy() {
              xml_parser_free($this->xml_parser);
        }
        
        ## =======================================================================        
        ##  number_of_elements        
        ## =======================================================================        
        ##   returns the number of variables in this metafile            
        ##        
        ## =======================================================================                
        function number_of_elements() {
              return count($this->page_object);
        }   

        ## =======================================================================        
        ##  getElement        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getElements($page=0) {
              return $this->page_object[$page];
        } 
        ## =======================================================================        
        ##  getObjects        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getObjects() {
              return $this->objects;
        }                         

        ## =======================================================================        
        ##  getElement        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getCacheElements($page=0) {
        	  if(isset($this->cache_object[$page])) {
              	return $this->cache_object[$page];
              }
        } 
        ## =======================================================================        
        ##  getObjects        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getCacheObjects() {
              return $this->cache_objects;
        }  
        
        ## =======================================================================        
        ##  getElement        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getNormalElements($page=0) {
              return $this->normal_object[$page];
        } 
        ## =======================================================================        
        ##  getObjects        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getNormalObjects() {
              return $this->normal_objects;
        }        


		## =======================================================================        
		##  parse       
		## =======================================================================        
		##   start the parsing of the specified file            
		##        
		## =======================================================================                        
		function parse() {
			## the xml class uses caching when in live mode
					
			## generate the cache filename
			$cache_file = md5($this->xml_file).'.php';
			
			## then we need to check if the cache file already exists
			if(!DEVELOPMENT && file_exists(MATRIX_CACHEDIR.'/templates/'.$cache_file)) {
				## the file exists we need to include it
				require(MATRIX_CACHEDIR.'/templates/'.$cache_file);
				
				## assign all vars
				$this->cache_object = $cache_object;
				$this->cache_objects = $cache_objects;
				$this->page_object = $page_object;
				$this->objects = $objects;
				$this->normal_objects = $normal_objects;
				$this->normal_object = $normal_object;
				
			} else {

				## first check if the supplied xml file exists
				if(!($fp = fopen($this->xml_file,"r"))) {
					die("could not open XML input:".$this->xml_file);
				}	
			
				## okay the file was not yet created- process the xml
				$this->xml_parser = xml_parser_create('UTF-8');
				xml_set_object($this->xml_parser,$this);
			
				## convert all tags to upper
				xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, true);
			
				xml_set_element_handler($this->xml_parser,'startElement', 'endElement');
				xml_set_character_data_handler($this->xml_parser,'characterData');
			
				## read the whole file and scan in the information
				while($data = fread($fp, 4096)) {
					if(!xml_parse($this->xml_parser,$data, feof($fp))) {
						  die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->xml_parser)), xml_get_current_line_number($this->xml_parser)));
					}
				}
				fclose($fp);
				$this->destroy();
			
				## okay we need to store the data in the cache file for later use
				$cached_vars = '';
				
				$cached_vars .= '$cache_object = '.var_export($this->cache_object,true).';'."\n";
				$cached_vars .= '$cache_objects = '.var_export($this->cache_objects,true).';'."\n";
				$cached_vars .= '$page_object = '.var_export($this->page_object,true).';'."\n";
				$cached_vars .= '$objects = '.var_export($this->objects,true).';'."\n";
				$cached_vars .= '$normal_objects = '.var_export($this->normal_objects,true).';'."\n";
				$cached_vars .= '$normal_object = '.var_export($this->normal_object,true).';'."\n";
				
				$cached_vars = '<?php '.$cached_vars.' ?>';
				
				## okay store the data into the cache file
				$fp=@fopen(MATRIX_CACHEDIR.'/templates/'.$cache_file, "w");	
				if ($fp) {
					fwrite($fp, $cached_vars, strlen($cached_vars));
					fclose($fp);
				}
			}
		}
}
?>