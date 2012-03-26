<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 

## =======================================================================        
## checkboxgroup_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function checkboxgroup_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	##var_dump($xmldata);
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_checkboxgroup/interface/");
	$template->set_templatefile(array("body" => "interface.tpl"));
	
	## we got your record to process the data
	$value = $data['text'];

	$options = split(',',$xmldata["OPTIONS"]);
	$labels = split(',',$xmldata["LABELS"]);
	$itemsperrow = $xmldata["ITEMCOUNT"];
	
	$output = '<table border="0" cellspacing="0" cellpadding="0">';
	for($i=0;$i<count($options); $i++) {
		$output .= '<tr>';
		for($j=0;$j<$itemsperrow; $j++) {
			## each line contains $itemsperrow entries
			if(isset($options[$i])) {
				$checked = isset($value) ? (in_array($options[$i],$value) ? 'checked' : '') : '';
				$output .= '<td><p><input type="checkbox" name="'.$xmldata['NAME'].'[]" value="'.$options[$i].'" '.$checked.'>'.$labels[$i].'</p></td>';
				$output .= '<td><img src="interface/images/blank.gif" alt="" width="8" height="1" border="0"></td>';	
			}
			$i++;
		}
		$output .= '</tr>';
		$output .= '<tr><td colspan="'.(($itemsperrow*2)-1).'"><img src="interface/images/blank.gif" alt="" width="1" height="6" border="0"></td></tr>';
	}
	$output .= '</table>';
	
	## set the vars accordingly
	$template->set_var('value',$output);	

	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}	
							
	return $template->fill_block("body");
}


## =======================================================================        
##  checkboxgroup_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function checkboxgroup_storeData($page_id, $identifier) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## we will recieve an array 
	$values = $_POST[$identifier];

	## prepare the db-object
	$db_connectionStore = new DB_Sql();

	## we should delete all old entries- 
	$select_query = "DELETE FROM ".DB_PREFIX."extra_checkboxgroup WHERE page_id = '$page_id' AND identifier = '$identifier' AND client_id='$client_id'";
	$result_pointer = $db_connectionStore->query($select_query);	

	## now we can savely insert our values
	if(isset($values)) {
		foreach($values as $current_value) {
			$insert_query = "INSERT INTO ".DB_PREFIX."extra_checkboxgroup (page_id, identifier, text, client_id) values ('$page_id', '$identifier', '$current_value','$client_id')";
			$result_pointer = $db_connectionStore->query($insert_query);
		}
	}
}

## =======================================================================        
##  checkboxgroup_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function checkboxgroup_getData($vPageID,&$page_record) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT text, identifier FROM ".DB_PREFIX."extra_checkboxgroup WHERE page_id='$vPageID' AND client_id='$client_id' ORDER BY identifier";
	$result_pointer = $db_connectionMain->query($select_query);
	
	
	$group = array();
	$old_identifier = '';
	while($db_connectionMain->next_record()) {
		$varname 	= $db_connectionMain->Record["identifier"];
		$group[$varname][] 	= $db_connectionMain->Record["text"];
	}
	
	foreach($group as $identifier=>$values) {
		$page_record[$identifier]["type"] = "CHECKBOXGROUP";
		$page_record[$identifier]["text"] = $values;
	}
}

## =======================================================================        
##  checkboxgroup_getMultiData       
## =======================================================================        
##  get the Data for multiple pages
## ======================================================================= 
function checkboxgroup_getMultiData($vItems) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	##db connection
	$db_connection = new DB_Sql();
	
	## init the vars
	$checkboxgroup_elements = array();
	$query = "";

	## let's prepare a query filter	
	for($i=0;$i<count($vItems); $i++) {
		if($query != "") {
			$query .= " OR ";
		}
		$targetPage= $vItems[$i]["page_id"];
		$query .= "page_id ='$targetPage'";
	}
				
	if($query =="") {
		## we haven't found any... nothing to output
		return;
	}
	
	$select_query = "SELECT text, identifier,page_id FROM ".DB_PREFIX."extra_checkboxgroup WHERE ($query) AND client_id='$client_id' ORDER BY page_id";
	$result_pointer = $db_connection->query($select_query);

	$group = array();
	$old_identifier='';
	$old_page_id = -1;
	while($db_connection->next_record()) {
		$text = $db_connection->Record["text"];
		$varname = $db_connection->Record["identifier"];
		$page_id = $db_connection->Record["page_id"];

		if($old_identifier =='') {
			$old_identifier = $varname;
		}
		if($old_page_id ==-1) {
			$old_page_id = $page_id;
		}		

		if(($varname != $old_identifier) || ($page_id != $old_page_id)) {
			$checkboxgroup_elements[$old_page_id][$varname]["type"] = "CHECKBOXGROUP";
			$checkboxgroup_elements[$old_page_id][$varname]["text"] = $group;
			$checkboxgroup_elements[$old_page_id]["page_id"] = $old_page_id;
			
			$group = array();
			$old_identifier = $varname;
			$old_page_id = $page_id;
			
		}
		$group[] 	= $text;
	}

	if(!isset($checkboxgroup_elements[$page_id])) {
		$checkboxgroup_elements[$old_page_id][$varname]["type"] = "CHECKBOXGROUP";
		$checkboxgroup_elements[$old_page_id][$varname]["text"] = $group;
		$checkboxgroup_elements[$old_page_id]["page_id"] = $old_page_id;
	}		
	
	## return the results
	return $checkboxgroup_elements;
}

## =======================================================================        
##  checkboxgroup_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function checkboxgroup_deleteData($vPageID) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];

	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$query = "DELETE FROM ".DB_PREFIX."extra_checkboxgroup WHERE page_id='$vPageID' AND client_id='$client_id'";
	$result_pointer = $db_connectionMain->query($query);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function checkboxgroup_output($item,$structure,$menu_id) {
	$selected_values = $item['text'];
	
	$options = split(',',$structure["OPTIONS"]);
	$labels = split(',',$structure["LABELS"]);
	
	// nkowald - 2009-10-06 - Need to add an option to CMS to show/hide application form links
	if ($structure['NAME'] == 'SHOWFORMLINKS' && ($item['text'][0] == 'y' || $item['text'][0] == NULL)) {
		$output = 
		'<p>The application form below is an editable pdf. If you are using version 8 or higher of Adobe Acrobat Reader, this means that you can type directly in to it on screen, 
		save a copy to your computer and then email it to us. Of course, you can always print the form out and fill it in with a pen too.</p> 
		<p>To download the latest version of Adobe Reader now, <a href="http://get.adobe.com/uk/reader" target="_blank">click here</a></p>

		<ul class="extra">
		<li><a class="greyb" href="/docs/Application_Form_College_of_Haringey_Enfield_and_North_East_London.pdf" alt="tooltips text" target="_blank">
			<span class="HAappFormFont">
				<img src="/layout/img/pdf.gif" alt="PDF file" />
				Application Form, 811 KB
			</span>
		</a></li>

		<li><a class="greyb" href="/docs/Application_Guidance_Notes.pdf" alt="tooltips text" target="_blank">
			<span class="HAappFormFont">
				<img src="/layout/img/pdf.gif" alt="PDF file" />
				Guidance Notes, 175 KB
			</span>
		</a></li>
		</ul>';
		return $output;
		
	} else if ($structure['NAME'] == 'SHOWFORMLINKS' && $item['text'][0] == 'n') {
		// Selected 'No' (Don't show application form links) - return nothing
		return '';
	} else {
	
		$output_elements = array();
		if(is_array($selected_values)) {
			for($i=0;$i<count($options); $i++) {
				$current_element = array_search($options[$i],$selected_values);
		
				if($current_element !== false) {
					$output_elements[] = $labels[$i];
				}
				$current_element = null;
			}
		}
			
		return join(', ',$output_elements);
		
	} // show form application link check
}
?>
