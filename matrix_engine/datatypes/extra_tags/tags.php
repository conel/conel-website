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
## tags_displayInput        
## =======================================================================        
## displays the input form
## ======================================================================= 
function tags_displayInput($xmldata, $data) {
	## init the vars
	$return = "";
	##var_dump($xmldata);
	## we should open our own template
	$template = new Template(ENGINE."datatypes/extra_tags/interface/");
	$template->set_templatefile(array("body" => "interface.tpl"));
		
	## set the vars
	$template->set_var('element_name',$xmldata['NAME']);
	$template->set_var('element_desc',$xmldata['DESC']);
	
	## now it's time to check the previous data entered
	
	$value = is_array($data['tags']) ? $data['tags'] : array();
	$value = join(' ',array_values(array_reverse($value)));	
	
	## set the value accordingly
	$template->set_var('value',$value);	

	if(!$xmldata['TAG']) {
		$template->set_var('element_tag',LANG_ElementText);
	} else {
		$template->set_var('element_tag',$xmldata['TAG']);
	}
								
	return $template->fill_block("body");
}



## =======================================================================        
##  tags_storeData        
## =======================================================================        
## save the content in the db
## ======================================================================= 
function tags_storeData($page_id, $identifier) {
	global $Auth;
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## first we clean up the the data
	tags_deleteData($page_id,$identifier);
	
	## we will recieve an array 
	$values = htmlentities(utf8_decode($_POST[$identifier]));
	$tags = split(' ',$values);
	$tags = array_unique($tags);
	
	## prepare the db-object
	$db = new DB_Sql();

	## now we need to process each of the tags
	foreach($tags as $current_tag) {
		if(!empty($current_tag)) {
			$current_tag = mysql_real_escape_string(trim($current_tag));
			
			## okay first we need to check if the tag already exists
			$query = "SELECT id FROM ".DB_PREFIX."extra_tags WHERE text='$current_tag'";
			$result_pointer = $db->query($query);	
	
			if($db->num_rows() > 0) {
				$db->next_record();
				$tag_id = $db->Record['id'];
				
				## update the counter
				$query = "UPDATE ".DB_PREFIX."extra_tags SET counter = counter +1 WHERE id='$tag_id'";
				$db->query($query);	
			} else {
				## okay we need to insert the new tag
				$query = "INSERT INTO ".DB_PREFIX."extra_tags (text,counter) values ('$current_tag',1)";
				$rp = $db->query($query);
				$tag_id = $db->db_insertid($rp);	
			}
			
			## okay we now have the tag id- let's connect it to the object
			$query = "INSERT INTO ".DB_PREFIX."extra_tags2page (page_id,item_id,identifier) values ('$page_id','$tag_id','$identifier')";
			$result_pointer = $db->query($query);
		}
	}
}

## =======================================================================        
##  tags_getData        
## =======================================================================        
##  get the Data
## ======================================================================= 
function tags_getData($vPageID,&$page_record) {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## data connection
	$db_connectionMain = new DB_Sql();
	## get all text elements
	$select_query = "SELECT * FROM ".DB_PREFIX."extra_tags2page AS A INNER JOIN ".DB_PREFIX."extra_tags AS B ON A.item_id=B.id WHERE A.page_id='$vPageID' ORDER BY A.identifier";
	$result_pointer = $db_connectionMain->query($select_query);
	
	## fetch the data
	while($db_connectionMain->next_record(MYSQL_ASSOC)) {
		$text 		= $db_connectionMain->Record["text"];
		$varname 	= $db_connectionMain->Record["identifier"];

		$page_record[$varname]["type"] = "TAGS";
		$page_record[$varname]["tags"][] = $text;
	}	
}


## =======================================================================        
##  tags_deleteData        
## =======================================================================        
##  deletes all text entries of a page
## ======================================================================= 
function tags_deleteData($vPageID,$identifier='') {
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	if(!empty($identifier)) {
		$query_addon = " AND identifier='".$identifier."'";
	} else {
		$query_addon = '';
	}

	## prepare the db object
	$db = new DB_Sql();
	$db_inner = new DB_Sql(); 
		
	## in order to keep the number of tags as low as possible- we need to delete them one by one
	## and check if they are used somewhere else
	$query = "SELECT item_id FROM ".DB_PREFIX."extra_tags2page WHERE page_id='$vPageID'".$query_addon;
	$result_pointer = $db->query($query);

	while($db->next_record(MYSQL_ASSOC)) {
		## okay now we need to update the counter for this tag
		$tag_id = $db->Record['item_id'];
		
		## okay now decrease the counter for this tag
		$query = "UPDATE ".DB_PREFIX."extra_tags SET counter = counter -1 WHERE id='$tag_id'";
		$db_inner->query($query);
	}
	
	## now delete all tags
	$query = "DELETE FROM ".DB_PREFIX."extra_tags2page WHERE page_id='$vPageID'".$query_addon;
	$result_pointer = $db->query($query);
	
	## finally clean up the main tag table
	$query = "DELETE FROM ".DB_PREFIX."extra_tags WHERE counter <= 0";
	$result_pointer = $db->query($query);
}

## =======================================================================        
##  output_text        
## =======================================================================        
##  call this function to output an text 
## ======================================================================= 
function tags_output($item,$structure,$menu_id) {
	## get the data
	$data = $item['tags'];
	$output = '';

	## first we need to check if awe have a template
	if(isset($structure['TEMPLATE'])) {
		## no we need to prepare the sections and the output targets
		$identifier = $structure['NAME'];
		
		## here we stroe the sections to be output
		$sections = array('head'=>'head','body'=>'body','foot'=>'foot','empty'=>'empty');
		$output_sections[$identifier] = $sections;
			
		## check if we need to outut more then the standard elements
		if(isset($structure['EXTRA_OUTPUT'])) {
			$extra_sections = explode(',',$structure['EXTRA_OUTPUT']); 
			
			foreach($extra_sections as $current_section) {
				foreach($sections as $current_base_section) {
					$output_sections[$identifier.'.'.$current_section][$current_base_section] = $current_section.'_'.$current_base_section;
				}
			}
		}

		## now setup the template file
		$basename = $structure['TEMPLATE'];
		$filename = $basename.".tpl";
		$templateFile = new Template(HTML_DIR);
		
		$blocks = array();
		foreach($output_sections as $current_section) {
			foreach($current_section as $current_element) {
				$blocks[$current_element] = $filename;
			}
		}
		$templateFile->set_templatefile($blocks);
		
		## finally we can now output all the sections
		$output = array();
		foreach($output_sections as $identifier => $current_blocks) {
			## check if we have data
			if(is_array($data) && !empty($data)) {	
				$output[$identifier] .= $templateFile->fill_block($current_blocks['head']);	
			
				foreach($data as $current_data) {
					$templateFile->set_var('matrix:TAG',$current_data);
					$output[$identifier] .= $templateFile->fill_block($current_blocks['body']);	
				}
				
				$output[$identifier] .= $templateFile->fill_block($current_blocks['foot']);	
			} else {
				$output[$identifier] .= $templateFile->fill_block($current_blocks['empty']);	
			}
		}
	} else {
		## we have no template- then we just output everything
		if(is_array($data) && !empty($data)) {	
			foreach($data as $current_data) {
				$output .= $current_data.' ';
			}
		}
	}
	
	return $output;
}


## =======================================================================        
##  linklist_copyData        
## =======================================================================        
##  pass it the sourcepage_id and the targetpage_id. It'll copy all
##  text entries to the new page.
## ======================================================================= 
function tags_copyData($source_id, $target_id) {
	## the container of all elements
	$container = array();
	tags_getData($source_id,$container);
	
	foreach($container as $identifier => $data) {
		## okay now we need to set everything up as if the data was just entered
		$_POST[$identifier] = is_array($data['tags']) ? $data['tags'] : array();
		$_POST[$identifier] = join(' ',array_values(array_reverse($_POST[$identifier])));	
		
		tags_storeData($target_id, $identifier);
	}
}
?>
