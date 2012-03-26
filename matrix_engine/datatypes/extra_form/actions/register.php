<?php
	## =======================================================================
	## extra_formAction_register												
	## =======================================================================
	## sample register action- takes just three fields 
	## =======================================================================
	function extra_formAction_register($id,$page_id,$params) {
		## all data is checked. so we only need to
		## check if the user already exist and if not register him

		## fetch the fields we need to enter into the db
		$structurefile = MATRIX_BASEDIR.'settings/datatypes/extra_form/'.$params['structure'].'.xml';
		
		if(!file_exists($structurefile)) {
			return array('status'=>false);
		}
		
		## okay open the form file and parse it
		$ctl_form = new formparser($structurefile);
		$ctl_form->parse();

		## fetch the data from the formfile
		$fields = $ctl_form->getFields();
		
		## we need to find out if we have an entry that is marked as unique
		$unique_element = array();
		foreach($fields as $current_element) {
			if(isset($current_element['unique'])) {
				$unique_element = $current_element;
			}
		}
		
		## prepare the db connection
		$db = new DB_Sql();
		
		## prepare the target db object module
		$module = mysql_real_escape_string($params['target']);
		
		## here we track which object we manipulate
		$object_id = 0;

		## we need to check if we have an element that matches our unique identifier
		if(isset($unique_element['identifier'])) {
			## for now we only support elements that store their data in the main table
			$unique_value = mysql_real_escape_string($_POST[$unique_element['identifier']]);
			$query = "SELECT id FROM ".DB_PREFIX.$module." WHERE ".$unique_element['identifier']."='".$unique_value."'";
			$result_pointer = $db->query($query);	
			
			if($db->num_rows() > 0 && !empty($unique_value)) {
				if($params['update'] != "false") {
					$db->next_record();
					$object_id = $db->Record['id'];
				}
			} else {
				## we need to create a new entry
				$query = "INSERT INTO ".DB_PREFIX.$module." (groupid,entered) values ('1',now())";
				$result_pointer = $db->query($query,true);
				$object_id = $db->db_insertid($result_pointer);
			}
		} else {
			## there is no unique identifier- this menas we need to insert the element anyway
			$query = "INSERT INTO ".DB_PREFIX.$module." (groupid,entered) values ('1',now())";
			$result_pointer = $db->query($query,true);
			$object_id = $db->db_insertid($result_pointer);
		}

		## if everything worked out we now should hav an id of the raw object
		if($object_id > 0) {
			## include the modules settings file
			@include_once(ENGINE.'modules/'.$module.'/settings.php');		
		
			foreach($fields as $current_element) {
				$type = strtolower($current_element['type']);
	
				## make sure all elements are upper case
				$current_element = array_change_key_case($current_element,CASE_UPPER);
				
				## normally this should be already included 
				@include_once(ENGINE.'modules/clients/attributetypes/'.$type.'/attribute.php');
	
				## now we check if the function exists
				if(function_exists("clients_".$type."_storeInput")) {
					## no we call the function
					eval("\$element = clients_".$type."_storeInput(\$current_element,\$object_id);");
				}	
			}

			return array('status'=>true,'object_id'=>$object_id);
		}
		
		return array('status'=>false);
	}					
?>