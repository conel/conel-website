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
	##  formparser.php														  
	## =======================================================================  
	##  Version: 		1.0.0													  
	##  Last change: 	30.7.2007												  
	##  by: 			S. Elsner											  
	## =======================================================================  
	class formparser {
		## the parser obhject
		var $xml_parser;
		
		## the xml file
		var $xml_file;
		
		
		## the xml file
		var $xml;		

		## helpers
		var $document;
		var $stack;

		## =======================================================================        
		## formparser        
		## =======================================================================        
		## the constructor - expects the file to be parsed as input
		##       
		## =======================================================================        
		function formparser($xmlfile) {
			$this->xml_file = $xmlfile;
			
			## init the stack
    		$this->stack = array();
		}

		## =======================================================================        
		## parse       
		## =======================================================================        
		## start the parsing of the specified file            
		##        
		## =======================================================================                        
		function parse() {
			## check if the xml file exists
			if(!file_exists($this->xml_file)) {
				die('Could not open xml file');
			}
		
			## now we need to prepae the parser
			$this->xml_parser = xml_parser_create();
			xml_set_object($this->xml_parser, $this);
			
			xml_set_element_handler($this->xml_parser, 'startElement', 'endElement');
			xml_set_character_data_handler($this->xml_parser, 'characterData');

			## read he whole file and scn in the inormation
			$this->xml = file_get_contents($this->xml_file);
			
        	## prepare the error handling
			if(!xml_parse($this->xml_parser, $this->xml)) {
				$this->handleError(xml_get_error_code($this->xml_parser), xml_get_current_line_number($this->xml_parser), xml_get_current_column_number($this->xml_parser));
			}
			
			## free the parser
        	xml_parser_free($this->xml_parser);
		
			
		}


		## =======================================================================        
		## getError      
		## =======================================================================        
		## returns the general error message      
		##        
		## =======================================================================                        
		function getError() {
			## fetch the error
			if(isset($this->document->errors[0]->tagData)) {
				return $this->document->errors[0]->tagData;
			} else {
				return '';
			}
		}

		## =======================================================================        
		## getActions       
		## =======================================================================        
		## after you parsed the doc- you can call this function         
		##        
		## =======================================================================                        
		function getPrepareActions() {
			## fetch the actions
			$actions = array();
			foreach($this->document->prepareactions[0]->tagChildren as $current_action) {
				$subactions = array();
				## now we need to check if we have a succeeded or failed tag
				if(count($current_action->tagChildren) > 0) {
					foreach($current_action->tagChildren as $current_subaction) {
						if($current_subaction->tagName == 'succeeded' || $current_subaction->tagName == 'failed') {
							$subactions[$current_subaction->tagName] = $this->_getSubActions($current_subaction);
						}
					}
				}
				$thisaction = $current_action->tagAttrs;
				$actions[] = array_merge($thisaction,$subactions);
			}

			return $actions;
		}
		
		## =======================================================================        
		## getActions       
		## =======================================================================        
		## after you parsed the doc- you can call this function         
		##        
		## =======================================================================                        
		function getPostActions() {
			## fetch the actions
			$actions = array();
			foreach($this->document->postactions[0]->tagChildren as $current_action) {
				$subactions = array();
				## now we need to check if we have a succeeded or failed tag
				if(count($current_action->tagChildren) > 0) {
					foreach($current_action->tagChildren as $current_subaction) {
						if($current_subaction->tagName == 'succeeded' || $current_subaction->tagName == 'failed') {
							$subactions[$current_subaction->tagName] = $this->_getSubActions($current_subaction);
						}
					}
				}
				$thisaction = $current_action->tagAttrs;
				$actions[] = array_merge($thisaction,$subactions);
			}

			return $actions;
		}
		
		## =======================================================================        
		## _getSubActions       
		## =======================================================================        
		## helper function to fetch the other actions recursively  
		##        
		## =======================================================================                        
		function _getSubActions($branch) {
			## fetch the actions
			$actions = array();
			foreach($branch->tagChildren as $current_action) {
				$subactions = array();
				## now we need to check if we have a succeeded or failed tag
				if(count($current_action->tagChildren) > 0) {
					foreach($current_action->tagChildren as $current_subaction) {
						if($current_subaction->tagName == 'succeeded' || $current_subaction->tagName == 'failed') {
							$subactions[$current_subaction->tagName] = $this->_getSubActions($current_subaction);
						}
					}
				}
				$thisaction = $current_action->tagAttrs;
				$actions[] = array_merge($thisaction,$subactions);
			}
			
			return $actions;
		}		

		## =======================================================================        
		## getFields      
		## =======================================================================        
		## after you parsed the doc- you can call this function         
		##        
		## =======================================================================                        
		function getFields() {
			## fetch the actions
			$fields = array();
			foreach($this->document->fields[0]->tagChildren as $current_field) {
				## check if we have any error elements defined
				$errors = array();
				if(!empty($current_field->tagChildren)) {
					foreach($current_field->tagChildren as $current_error) {
						if($current_error->tagName == 'error') {
							## okay we have an error element- now we need to store it
							$errors[$current_error->tagAttrs['name']] = $current_error->tagData;
						}
					}
				}
				
				## okay now we handle the actual element
				$attributes = array_merge($current_field->tagAttrs,array('errors'=>$errors,'type'=>$current_field->tagName));
		
				$fields[] = $attributes;			
			}
			
			return $fields;
		}	
		

		## =======================================================================        
		## getFields      
		## =======================================================================        
		## after you parsed the doc- you can call this function         
		##        
		## =======================================================================                        
		function getTypes() {
			## fetch the actions
			$types = array();
			foreach($this->document->fields[0]->tagChildren as $current_field) {
				if(!isset($current_field->tagAttrs['validate']) || $current_field->tagAttrs['validate'] == 'true') {
					$types[$current_field->tagName] = true;		
				}
			}
			
			return $types;
		}		

		## =======================================================================        
		## handleError       
		## =======================================================================        
		## start the parsing of the specified file            
		##        
		## =======================================================================                        
		function handleError($code, $line, $col) {
			trigger_error('XML Parsing Error at '.$line.':'.$col.'. Error '.$code.': '.xml_error_string($code));
		}

		## =======================================================================        
		## getStackLocation       
		## =======================================================================        
		## Gets the reference to the current direct parent          
		##        
		## =======================================================================                        
		function getStackLocation() {
			$return = '';

			foreach($this->stack as $stack) {
				$return .= $stack.'->';
			}
        
			return rtrim($return, '->');
		}

		## =======================================================================        
		## startElement       
		## =======================================================================        
		## Handler function for the start of a tag         
		##        
		## =======================================================================                        
		function startElement($parser, $name, $attrs = array()) {
			## Make the name of the tag lower case
			$name = strtolower($name);
        
			## Check to see if tag is root-level
			if(count($this->stack) == 0) {
				## If so, set the document as the current tag
				$this->document = new XMLTag($name, $attrs);

				## And start out the stack with the document tag
				$this->stack = array('document');
			} else {
				## Get the name which points to the current direct parent, relative to $this
				$parent = $this->getStackLocation();
            
				## Add the child
				eval('$this->'.$parent.'->AddChild($name, $attrs, '.count($this->stack).');');

				## Update the stack
				eval('$this->stack[] = $name.\'[\'.(count($this->'.$parent.'->'.$name.') - 1).\']\';');
			}
		}

		## =======================================================================        
		## endElement       
		## =======================================================================        
		## Handler function for the end of a tag        
		##        
		## =======================================================================                        
		function endElement($parser, $name) {
			## Update stack by removing the end value from it as the parent
			array_pop($this->stack);
		}


		## =======================================================================        
		## characterData       
		## =======================================================================        
		## Handler function for the character data within a tag        
		##        
		## =======================================================================                        
		function characterData($parser, $data) {
			## Get the reference to the current parent object
			$tag = $this->getStackLocation();

			## Assign data to it
			eval('$this->'.$tag.'->tagData .= trim($data);');
		}
	}



	## =======================================================================        
	## XML Tag Object        
	## =======================================================================        
	## This object stores all of the direct children of itself in the 
	## $children array. They are also stored by type as arrays. So, if, 
	## for example, this tag had 2 <font> tags as children, there would be 
	## a class member called $font created as an array. $font[0] would be 
	## the first font tag, and $font[1] would be the second.
	##
	## To loop through all of the direct children of this object, the 
	## $children member should be used.
	##
	## To loop through all of the direct children of a specific tag for  
	## this object, it is probably easier to use the arrays of the specific
	## tag names, as explained above.
	##       
	## =======================================================================        

	## =======================================================================        
	## XMLTag       
	## =======================================================================                                
	class XMLTag {
	
		## Array with the attributes of this XML tag
		var $tagAttrs;

		## The name of the tag
		var $tagName;

		## keyowrds
		var $tagData;

		## Array of references to the objects of all direct children of this XML object
		var $tagChildren;
		var $tagParents;


		## =======================================================================        
		## XMLTag       
		## =======================================================================        
		## Constructor, sets up all the default values    
		##        
		## =======================================================================                        
		function XMLTag($name, $attrs = array(), $parents = 0) {
			## Make the keys of the attr array lower case, and store the value
			$this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);

			## Make the name lower case and store the value
			$this->tagName = strtolower($name);
			
			## Set the number of parents
			$this->tagParents = $parents;
			
			## Set the types for children and data
			$this->tagChildren = array();
			$this->tagData = '';
		}

		## =======================================================================        
		## AddChild       
		## =======================================================================        
		## Adds a direct child to this object
		##        
		## =======================================================================                        
		function AddChild($name, $attrs, $parents) {    
			## If there is no array already set for the tag name being added, 
			## create an empty array for it
			if(!isset($this->$name))
				$this->$name = array();
	
			## If the tag has the same name as a member in XMLTag, or somehow the 
			## array wasn't properly created, output a more informative error than
			## PHP otherwise would.
			if(!is_array($this->$name)) {
				trigger_error('You have used a reserved name as the name of an XML tag. Please consult the documentation (http://www.thousandmonkeys.net/xml_doc.php) and rename the tag named '.$name.' to something other than a reserved name.', E_USER_ERROR);
				return;
			}
	
			## Create the child object itself
			$child = new XMLTag($name, $attrs, $parents);
			
			## Add the reference of it to the end of an array member named for the tag's name
			$this->{$name}[] =& $child;
			
			## Add the reference to the children array member
			$this->tagChildren[] =& $child;
		}


		## =======================================================================        
		## GetXML       
		## =======================================================================        
		## Adds a direct child to this object
		##        
		## =======================================================================                        
		function GetXML() {
			## Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
			$out = "\n".str_repeat("\t", $this->tagParents).'<'.$this->tagName;

			## For each attribute, add attr="value"
			foreach($this->tagAttrs as $attr => $value) {
				$out .= ' '.$attr.'="'.$value.'"';
			}
			
			## If there are no children and it contains no data, end it off with a />
			if(empty($this->tagChildren) && empty($this->tagData)) {
				$out .= " />";
			} else {    
				## If there are children
				if(!empty($this->tagChildren)) {
					## Close off the start tag
					$out .= '>';
			
					## For each child, call the GetXML function (this will ensure that all children are added recursively)
					foreach($this->tagChildren as $child) {
						$out .= $child->getXML();
					}
			
					## Add the newline and indentation to go along with the close tag
					$out .= "\n".str_repeat("\t", $this->tagParents);
				} elseif(!empty($this->tagData)) {
					$out .= '>'.$this->tagData;
				}
				
				## Add the end tag    
				$out .= '</'.$this->tagName.'>';
			}
			
			## Return the final output
			return $out;
		}
	}
?>