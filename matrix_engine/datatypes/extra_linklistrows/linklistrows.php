<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
## this is a datatype plugin. Use this as a starting point for creating
## a datatype which uses an external editor/window for each creation 
## process
##
## you need to implement the following functions:
## 
## _displayInput
## _storeData
## _getData
## _getMultiData
## _deleteData
## _output
## ======================================================================= 


## =======================================================================        
## linklistrows_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function linklistrows_displayInput($xmldata, $data) {			
	return linklist_displayInput($xmldata, $data);
}

## =======================================================================        
##  linklistrows_storeData        
## =======================================================================        
## save the data in the db
## ======================================================================= 
function linklistrows_storeData($page_id, $identifier) {
	return false;
}

## =======================================================================        
##  linklistrows_getData     
## =======================================================================        
##  get Data
## ======================================================================= 
function linklistrows_getData($vPageID,&$page_record) {
	linklist_getData($vPageID,$page_record);
}

## =======================================================================        
##  linklistrows_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function linklistrows_deleteData($vPageID) {
	linklist_deleteData($vPageID);
}


## =======================================================================        
##  linklistrows_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function linklistrows_copyData($source_id, $target_id) {
	linklist_copyData($source_id, $target_id);	
}

## =======================================================================        
##  output_linklist       
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function linklistrows_output($item,$structure,$menu_id) {
	## this need to be rewritten.
	## a) open the template and check what type of elements we need
	## b) get the MultiData entries that are part of the element.
	##       -> they should have a form which can be easliy processed
	##		 -> we need to update the search output if the format is changed	
	## c) call each output function for each datatype
	 
	
	## here we store the output that is generated
	$storage = "";
	
	## first we open the xml-file
	if(isset($structure['TEMPLATE'])) {
		$basename = $structure['TEMPLATE'];
		$xmlFile  = $basename.".xml";
		$filename = $basename.".tpl";
				
		$xmlStructure = new xmlparser(HTML_DIR.$xmlFile);
		$xmlStructure->parse();
		## parse the template file
		$objects = $xmlStructure->getObjects();
		$xmlStructure = $xmlStructure->getElements();	
		
		## now we open the template file for output
		$templateFile = new Template(HTML_DIR);
		$templateFile->set_templatefile(array("head" => $filename,"rowstart" => $filename,"rowend_alternate" => $filename,"rowstart_alternate" => $filename,"rowend" => $filename,"body" => $filename,"foot" => $filename,"empty" => $filename,"active" => $filename,"alternate" => $filename)); 

	}

	## get the page names
	$page_names		= structure_getMultiPageName($item);
	
	$row_items = 1;
	$rows = 1;	
	## let's generate each selected page using the normal generate page
	for($i=0;$i<$item["length"]; $i++) {
		$page_record = page_getPage($item[$i]["page_id"],$objects);
		$page_id = $item[$i]["page_id"];
		## now loop through all xmlelements
		$counter =0;
		$num_elements = count($xmlStructure)-1;
		
		## reset the template
		$templateFile->varkeys = array();  
		$templateFile->varvals = array(); 
		
		
		while($counter < $num_elements) {					
			## okay first we try to find out what type we have
			## we wrap this up in a switch statemnt- this way we can
			## extend it more easily
			$element_type = $xmlStructure[$counter]['TYPE'];
			$element_name = $xmlStructure[$counter]['NAME'];
			
			switch($element_type) {
				case 'TEXT':
				case 'COPYTEXT':
				case 'DATE': 
				case 'LINK' :
				case 'FILE':
				case 'BOX':
				case 'LINKLIST':
				case 'IMAGE': {
					## basically we need to call the function output_"element_type"
					## and the output the results to the template
					$target = strtolower($element_type); 
					if(isset($page_record[$element_name])) {
						eval("\$element = output_".$target."(\$page_record[\$element_name],\$xmlStructure[\$counter],$menu_id);");	
						if(is_array($element)) {
							$templateFile->set_vars($element);
						} else {
							$templateFile->set_var($element_name,$element);
						}
					}
					break;
				}				
				default: {	
					## we need to check if we have a module for this datatype
					$target = strtolower($element_type);	
					## first we try to include the apropriate file 
					@include_once("datatypes/extra_".$target."/".$target.".php");	
					## now we check if the function exists
					if(function_exists($target."_output")) {
						## no we call the function		
						## check if the page_record entry is defined
						## if not we need to pass the whole record
						if(isset($page_record[$element_name])) {
							eval("\$element = ".$target."_output(\$page_record[\$element_name],\$xmlStructure[\$counter],$menu_id);");
						} else {
							eval("\$element = ".$target."_output(\$page_record,\$xmlStructure[\$counter],\$layout_template,\$menu_id,\$page_id);");
						}	
						if(is_array($element)) {
							$templateFile->set_vars($element);
						} else {
							$templateFile->set_var($element_name,$element);
						}
											
					}
					break;
				}
			}
			$counter++;
		}
		## now output the internal vars
		$page_id = $item[$i]["page_id"];
		$targetURL = getTargetURL($page_id);	
		
		$templateFile->set_var('matrix:TARGETPAGE',$targetURL);
		$templateFile->set_var('matrix:PAGETITLE',$page_names[$page_id]['name']);
		$templateFile->set_var('matrix:PAGEID',$page_id);
		$templateFile->set_var("matrix:COUNTER", $i);


		## we want to output different rows.
		## is this the beginning of a row
		if($row_items == 1) {
			## beginning of a row
			$storage .= $templateFile->fill_block("rowstart");
			$rows ++;	
		} 
		
		$storage .= $templateFile->fill_block("body");

		if($row_items == ($structure['ITEMCOUNT'])) {
			## the end of a row
			$storage .= $templateFile->fill_block("rowend");
			## reset the counter
			$row_items=0;
		} 
		
		$row_items++;		
	}
	
	if($row_items != 1) {
		$storage .= $templateFile->fill_block("rowend");
	}

	
	if(is_object($templateFile)) {	
		if($i>=1) {
			$head = $templateFile->fill_block("head");
			$foot = $templateFile->fill_block("foot");	
			$return[$structure['NAME']] = $head.$storage.$foot;
		} else {
			$storage = $templateFile->fill_block("empty");
			$return[$structure['NAME']] = $storage;
		}
	} else {
		$return[$structure['NAME']] = "";
	}

	$return["matrix:MAXCOUNT:".$structure['NAME']] = $item["length"];
	
	return $return;		
}

?>
