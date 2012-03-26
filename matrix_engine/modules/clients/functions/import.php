<?php

## =======================================================================        
##  dbobject_importTest        
## =======================================================================        
##  handles the importing of dbojects from a csv file
##
## =======================================================================        
function clients_doImport() {
	global $gSession;
	
	## import the data files. the import process is divided in steps and segments
	$current_step = intval($_GET['step']);
	$current_group = intval($_GET['group']);
	## let's see what the current step is

	switch($current_step) {
		case 0:
			## first ask the user for the file to import
			clients_importDisplaySelectFile();
			## in order to show the screen right away we do nothing 
			break;
		case 1:
			## we need to store the settings 
			$update = intval($_POST['update']);
		
			## okay test out the uploading of the file
			$userfile	= $_FILES['upload']['tmp_name'];
			$file_name	= $_FILES['upload']['name'];
			$file_size	= $_FILES['upload']['size'];
			$file_type	= $_FILES['upload']['type'];

			## okay we first create an upload object
			$f = new file_object();  
			if ($userfile != "none" && $userfile!='') {              
				##then we upload the file
				$filename = $f->upload($userfile, 'import.csv',$file_size,$file_type, MATRIX_BASEDIR."settings/modules/".$GLOBALS['_MODULE_DATAOBJECTS_NAME']."/import/",1);
				## check if the file was successfully uploaded
				if($filename != -1) {
					output_progress('Import','Please wait while the file is beeing processed',"module.php?cmd=import&step=2&group=".$current_group.'&update='.$update);
				} else {
					## there was an error processing the file
					
				}
			} else {
				$targetURL = $gSession->url('module.php?cmd=import');
				output_confirm_refresh('Please select a file','Please select a file before proceeding',$targetURL);
			}
			break;
		case 2:
			## convert the file
			$update = intval($_GET['update']);
			
			dbobject_importConvertDataFile(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/import/import.csv',MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/import/tmp.csv');
			output_progress('Import','prepareing default values',"module.php?cmd=import&step=3&group=".$current_group.'&update='.$update);
			
			break;
		case 3:

			## in this step we will inform the user about the fields we found- and let the user
			## specify which elements he wants to assign to what fields from the db
			##exit;
			## okay we have a file- now we need to ask them what default values they want to set
			## let#s get the availaable options
			
			##fetch the field names form the file
			$data = clients_importGetSegementOfFile(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/import/tmp.csv',0,1);		
			$data = $data['data'][0];

			
			clients_importDisplaySelectValues($data);			
			break;

		case 4:
			## the user has selected the desired mapping- we need to prepare it and 
			## store the mapping for the next importing steps...
			
			$column_counter = intval($_POST['column_count']);
			$update = intval($_GET['update']);
			
			## prepare the fields
			$mapping = array();
			for($i = 0; $i <= $column_counter; $i++) {
				if($_POST['COLUMN_'.$i] != -1) {
					## okay we found a field
					$mapping[$i] = $_POST['COLUMN_'.$i];
				}
			}
				
			## now we will store the mapping using the session identifier
			$filename = MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/import/'.$gSession->id.'.mapping';
			$content = serialize($mapping);

			## store the mapping file
			$fp = fopen($filename,'w');
			if($fp) {
				## write the data
				fwrite($fp,$content);
				fclose($fp);
			}			
			
			## finally jump to the next step
			output_progress('Import','starting import',"module.php?cmd=import&step=5&update=".$update);	
			break;

		case 5:
			## first load the mapping file
			$filename = MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/import/'.$gSession->id.'.mapping';
			
			$update = intval($_GET['update']);
			
			## load the file
			$content = file_get_contents($filename);	
			$mapping = unserialize($content);
		
			## this step is divided in substeps
			$current_substeps = isset($_GET['substeps']) ? $_GET['substeps'] : 1;
			$current_pos = intval($_GET['pos']) ? $_GET['pos'] : 1;

			## process the products
			$dataInfo = clients_importGetSegementOfFile(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/import/tmp.csv',$current_substeps,$current_substeps+100,$mapping);
			$data = $dataInfo['data'];
			
			## okay now we will prepare the 
			if(isset($data[0])) {
				_clientsDoImport($data,$_POST['default_values'],$update);
				output_progress('Import','processing data:'.($current_substeps+99),"module.php?cmd=import&step=5&values=".$values."&pos=".$dataInfo['pos']."&substeps=".($current_substeps+100)."&group=".$current_group."&update=".$update);	
			} else {
				## reload and display the next step
				output_progress('Import','finishing',"module.php?cmd=import&step=6");	
			}
			break;			
		default:
			## okay we are done... so for now just quit
			output_confirm('Import','The data was sucessfully imported','module.php');

			break;
		}
	
}


## =======================================================================        
##  clients_importDisplaySelectFile       
## =======================================================================        
## displays an input form that allows the user to select a file that
## will be uploaded and later processed by this script
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function clients_importDisplaySelectFile() {
	global $gSession;
	
	$current_group = intval($_GET['group']);
	
	## setup the template
	$select_template = new Template();
	$select_template->set_templatefile(array("body" => ENGINE."modules/clients/interface/fileupload.tpl"));

	$select_template->set_var('language_inputhead','<b>'.LANG_MODULE_CLIENTS_ImportSelectFile.'</b>');
	$select_template->set_var('language_inputbody',LANG_MODULE_CLIENTS_ImportSelectFileDesc.'<br>');
	
	$select_template->set_var('update',LANG_MODULE_CLIENTS_ImportUpdate);
	$select_template->set_var('explain_update',LANG_MODULE_CLIENTS_ImportUpdateDesc);
	
	$select_template->set_var('element_desc','Click the \'Browse\' button to locate the text file<br> containing the subscribers on your computer.');
	
	$targetURL = "module.php?cmd=import&step=1&group=".$current_group;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('actionURL',$targetURL);	
	
	$select_template->pfill_block("body");
	
}

## =======================================================================        
##  clients_importDisplaySelectFile       
## =======================================================================        
## displays an input form that allows the user to select a file that
## will be uploaded and later processed by this script
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function clients_importDisplaySelectValues($data) {
	global $gSession;
	
	$current_group = intval($_GET['group']);
	$update = intval($_GET['update']);
	
	## setup the template
	$file = ENGINE."modules/clients/interface/import_asssign_fields.tpl";
	$select_template = new Template();
	$select_template->set_templatefile(array("body" => $file,'column'=>$file));

	$select_template->set_var('language_inputhead','Match the file with your subscriber fields');
	$select_template->set_var('language_inputbody',"For each column, select the field it corresponds to in 'Belongs to'. Click the 'Next' button when you're do");
	$select_template->set_var('element_desc',' ');
	
	$targetURL = "module.php?cmd=import&step=4&group=".$current_group.'&update='.$update;
	$targetURL = $gSession->url($targetURL);
	$select_template->set_var('actionURL',$targetURL);	
	
	## prepare the select element wiht all available fields
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();		
	$elements = $wt->getElements();	
	
	$allowed_types = array('text','email','copytext','tags','selectbox','date');
	$field_selector = '<option label="" value="-1">Skip This Column</option><option label="" value="-1"></option>';
	foreach($elements as $current_row) {
		foreach($current_row as $current_element) {
			$type = strtolower($current_element['TYPE']);
			$identifier = strtolower($current_element['IDENTIFIER']);
			
			if(in_array($type,$allowed_types)) {
				## first we try to include the apropriate file 
				@include_once(MATRIX_BASEDIR."/matrix_engine/modules/clients/attributetypes/".$type."/attribute.php");			
				if(function_exists("clients_".$type."_getData")) {
					$field_selector .= '<option label="'.$current_element['NAME'].'" value="'.$identifier.'">'.$current_element['NAME'].'</option>';
				}
			}
		}
	}	
	
	
	## now we will output each colum recoginzed by the system
	$output = '';
	$counter = 0;

	foreach($data as $key=>$value) {
		$current_selector = '<select name="COLUMN_'.$counter.'">'.$field_selector.'</select>';
	
		$select_template->set_var('COLUMN_NAME',$key);
		$select_template->set_var('COLUMN_SELECTOR',$current_selector);
		
		$output .= $select_template->fill_block("column");
		$counter++;
	}
	
	## prpeare the hidden fields
	$hiddenfields = '<input name="column_count" id="column_count" type="hidden" value="'.($counter-1).'">';
	
	$select_template->set_var('COLUMNS',$output);	
	$select_template->set_var('hiddenfields',$hiddenfields);
	
	$select_template->pfill_block("body");
	
}


## =======================================================================        
##  _clientsDoImport       
## =======================================================================        
##  we store the base information for a certain template    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function _clientsDoImport($data,$defaults,$update=0) {

	## prepare the select element wiht all available fields
	$wt = new ctlparser(MATRIX_BASEDIR.'settings/modules/'.$GLOBALS['_MODULE_DATAOBJECTS_NAME'].'/base.xml');
	$wt->parse();		
	$elements = $wt->getSimplifiedElements();
	$objects = $wt->getObjects();

	$allowed_types = array('text','email');
	
	## we need to find out if we have an entry that is marked as unique
	$unique_element = array();
	foreach($elements as $current_element) {
		if(isset($current_element['UNIQUE'])) {
			$unique_element = $current_element;
		}
	}

	$db_connectionStore = new DB_Sql();
	## the data array can now be processed.
	##var_dump($elements);
	foreach($data as $current_dataelement) {
		## here we will stroe the id of the current entry
		$id = 0;
	
		## we need to check if we have an element that matches our unique identifier
		if(isset($unique_element['IDENTIFIER']) && !empty($current_dataelement[$unique_element['IDENTIFIER']])) {		
			## for now we only support text elements of email elements as unique tokens
			$query = "SELECT id FROM ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." WHERE ".$unique_element['IDENTIFIER']."='".$current_dataelement[$unique_element['IDENTIFIER']]."'";
			$result_pointer = $db_connectionStore->query($query);

			if($db_connectionStore->num_rows() < 1) {	
				## we need to import the other fields. 
				$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." (groupid) values ('1')";
				$result_pointer = $db_connectionStore->query($query,true);
				$id = $db_connectionStore->db_insertid($result_pointer);	
			} else if($update == 1) {
				$db_connectionStore->next_record();
				$id = $db_connectionStore->Record['id'];
			}
		} else {
			## we do not have a unique attribute- so no way to identifiy if there are any duplicate entries-
			## this means we need to import them all 
			$query = "INSERT INTO ".DB_PREFIX.$GLOBALS['_MODULE_DATAOBJECTS_DBPREFIX']." (groupid) values ('1')";
			$result_pointer = $db_connectionStore->query($query,true);
			$id = $db_connectionStore->db_insertid($result_pointer);
		}
		
		## okay we have a base object- now we should call the import function of each attribute
		if($id > 0) {
			foreach($objects as $current_attribute=>$value) {
				$type = strtolower($current_attribute);
	
				## first we try to include the apropriate file 
				@include_once(ENGINE."modules/clients/attributetypes/".$type."/attribute.php");

				## now we check if the function exists
				if(function_exists("clients_".$type."_importData")) {
					## no we call the function
					eval("clients_".$type."_importData(\$id,\$elements,\$current_dataelement);");
				}
			}				
		}
	}
}




## =======================================================================        
##  dbobject_importGetSegementOfFile        
## =======================================================================        
##  we store the base information for a certain template    
##
##  TODO:
##       - make all pages by default inactive
## =======================================================================        
function clients_importGetSegementOfFile($file,$startline,$endline,$mapping=null) {
	## then we open the the new file and start reading it

	$handle = fopen($file,"r"); 

	$fields = array();
	$data = array();
	$linecount = 0;
	## let's position the filepointer- and while we are at it read the fields form the first line
	while($linecount <= $startline && ($current_data = fgetcsv($handle, 24000, ",")) != false) {	
		if($linecount == 0) {
    		$fields = $current_data;
    	} 
		$linecount++;
    }
   
    ## assing the mapping
   	if(!isset($mapping)) {
   		$mapping = $fields;
   	}
    
    
    ## okay now we can start to read form start to end
	$item = 0;

	while(($current_data = fgetcsv($handle, 24000, ",",'"')) != false && $linecount <= $endline) {	
		$num = count($current_data);
		for ($c=0; $c < $num; $c++) {
			if(!empty($mapping[$c])) {
				$data[$item][$mapping[$c]] = addslashes($current_data[$c]);
				
			}
		}
		$linecount++;
		$item++;
     }
	
	fclose($handle);
	return array('data'=>$data);
}



## =======================================================================        
##  dbobject_importConvertDataFile        
## =======================================================================        
##  converts the excel style file to a normal csv file- and stores it.
##
##  TODO:
##  
## ======================================================================= 
function dbobject_importConvertDataFile($inputfile,$outputfile) {
	## first we open the file
	
	$content = file_get_contents($inputfile);
	$content = str_replace("\r","\n",$content);
	## write the file
	## open the file
	## we need to split the file into sections-
	## we will cut it up in slices of 1000 entries		
	$fp = fopen($outputfile,'w');
	if($fp) {
		## write the data
		fwrite($fp,$content);
		fclose($fp);
	}

 }
?>