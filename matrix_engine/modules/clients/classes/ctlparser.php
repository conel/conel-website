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
  ##  ctlparser.php														  
  ## =======================================================================  
  ##  Version: 		1.2.1													  
  ##  Last change: 	20.5.2003												  
  ##  by: 			S. Elsner											  
  ## =======================================================================  
  ##  Description:															  
  ##    * handles the xml description files- so we know what the var
  ##      names are the user typed in....	  
  ## =======================================================================  
	class ctlparser { 

		## stores the different items that where found      
		var $objects = array();  
			
		## stores the current block (header, task, link)        
		var $current_block = "";  
		## always contains the current tag (on close this
		## should be reset
		var $current_tag = ""; 
        
		## store the parser object
		var $xml_parser;
        
		## here we store the actual metafile name
		## the default is the simple file
		var $xml_file ="base.xml";   
                
		## okay we save all inputfilednames and its types in here
		var $element_object = array();
        
		## here we store all element information
		var $page_object = array();
		var $rows_object = array();		
		
		## the page counter
		var $page_counter = 0;
		
		var $row_counter = 0;
		
		## page/section title- we need to handle this!
		var $page_name = '';
		var $row_name = '';
						
		## =======================================================================        
		##  xmlparser        
		## =======================================================================        
		##   the constructor - expects the varname to be parsed as input    
		##       
		## =======================================================================        
        function ctlparser($wft_file) {
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
                    case 'PAGE' : {
                          $this->current_block = $name;
                          $this->page_name = isset($attrs['NAME']) ? $attrs['NAME'] : '';
                          break;
                    }
                    case 'ROW' : {
                    	  ## we need to set the row counter
                          $this->current_block = $name;
                          $this->row_name = isset($attrs['NAME']) ? $attrs['NAME'] : '';
                          break;
                    }                   
                    default: {
                    	  $this->current_block = $name;
                    	  $this->element_object['TYPE'] = $name;
                    	  
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
                    case 'PAGE' : {
                    	  ## let's store the name for this page
                    	  $this->page_object[$this->page_counter]['NAME'] = $this->page_name;
                    	  $this->page_counter++;
                    	  
                          ## clear the vars
                          $this->current_block = '';
                          $this->page_name = '';
        
                          break;
                    }  
                    case 'ROW' : {
                    	$this->rows_object[$this->row_counter]['NAME'] = $this->row_name;
                    	$this->page_object[$this->page_counter][$this->row_counter]['NAME'] = $this->row_name;
                    	$this->row_counter++;
                          
                    	## clear the vars
                    	$this->current_block = '';
        				$this->row_name = '';
                     	break;
                    }                                             
                    default: {                          
                          ## store the thing- needs to be checked!
                          if(isset($this->rows_object[$this->row_counter])) {
                          	$num_elements = count($this->rows_object[$this->row_counter]);
                          } else {
                          	$num_elements = 0;
                          }
                          
                          $this->rows_object[$this->row_counter][$num_elements++] = $this->element_object;

                          if(isset($this->page_object[$this->page_counter][$this->row_counter])) {
                          	$num_elements = count($this->page_object[$this->page_counter][$this->row_counter]);
                          } else {
                          	$num_elements = 0;
                          }
                          
                          $this->page_object[$this->page_counter][$this->row_counter][$num_elements++] = $this->element_object;
                          $this->objects[$this->element_object['TYPE']] = true;
                         
                           ## clear the vars
                          $this->element_object = '';
                          $this->current_block = '';
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
                          $this->element_object['TAG'] = htmlentities($data);
                          break;
                    case 'DESCRIPTION':                            
                          $this->element_object['DESC'] = htmlentities($data);
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
        function getElements() {
            return $this->rows_object;
        } 
        
        ## =======================================================================        
        ##  getElement        
        ## =======================================================================        
        ##  returns the array for a certain element          
        ##        
        ## =======================================================================                
        function getPagedElements() {
            return $this->page_object;
        }         
        ## =======================================================================        
        ##  getSimplifiedElement        
        ## =======================================================================        
        ##  prepares all elements to sit in one array- for gereater ease of use         
        ##        
        ## =======================================================================                
        function getSimplifiedElements() {
			$newelements = array();
			foreach($this->page_object as $current_row) {
				## process the rows- first we need to find out how many entries
				foreach($current_row as $current_element) {
					## here we start calling all our attribute types
					if(is_array($current_element)) {
						$newelements[] = $current_element;
					}
				}
			}				
			return $newelements;
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
        ##  parse       
        ## =======================================================================        
        ##   start the parsing of the specified file            
        ##        
        ## =======================================================================                        
        function parse() {
              $this->xml_parser = xml_parser_create('UTF-8');
              xml_set_object($this->xml_parser,$this);

              ## convert all tags to upper
              xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, true);
              
              xml_set_element_handler($this->xml_parser,'startElement', 'endElement');
              xml_set_character_data_handler($this->xml_parser,'characterData');
     
              if(!($fp = @fopen($this->xml_file,"r"))) {
                    die("could not open XML input".$this->xml_file);
              }
              
              ## read the whole file and scan in the information
              while($data = fread($fp, 4096)) {
                    if(!xml_parse($this->xml_parser,$data, feof($fp))) {
                          die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->xml_parser)), xml_get_current_line_number($this->xml_parser)));
                    }
              }
              fclose($fp);
              $this->destroy();
        }
}
?>