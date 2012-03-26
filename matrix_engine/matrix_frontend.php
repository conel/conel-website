<?PHP
## ======================================================================= 
##   webmatrix                                                     
## ======================================================================= 
##   Copyright (c) 2001, 2002, 2003 Stefan Elsner, Workmatrix        
## ======================================================================= 
##   <info@workmatrix.de> 
##
## ======================================================================= 
require("functions/search.php");
require("classes/class_usersession.php");
require_once("functions/template.php");
require_once("functions/folder.php");
require_once("functions/cache.php");

require("datatypes/linklist/linklist_editor.php");
require("datatypes/image/image.php");
require("datatypes/date/date.php");
require("datatypes/text/text.php");
require("datatypes/copytext/copytext.php");
require("datatypes/linklist/linklist.php");
require("datatypes/include/include.php");
require("datatypes/link/link.php");
require("datatypes/file/file.php");
require("datatypes/listview/listview.php");
require("datatypes/box/box.php");

## =======================================================================        
##  frontend_getPageInfo        
## =======================================================================        
##  mainly gets the page id. If it finds one, we get some additional data
##
##  TODO:       
## ======================================================================= 
function frontend_getPageInfo($page_id=0,$client_id) {
	## init the page record
	$pageInfo = array();
	
	if($page_id == 0) {
		## get the homepage
		$pageInfo = structure_getHomePage();
	} else { 
		$dbConnection = new DB_Sql();
		
		$query = 'SELECT modified,title, template,structure_id,structure_text,A.type,structure_flag FROM '.USER_PAGES.' AS A LEFT JOIN '.STRUCTURE." AS B ON A.page_id=B.page_id WHERE A.page_id='$page_id' AND A.client_id='$client_id'";
		$result_pointer = $dbConnection->query($query);
		$dbConnection->next_record();

		$pageInfo["page_id"]	 = $page_id;
		$pageInfo["text"]		 = $dbConnection->Record["structure_text"];
		$pageInfo["template_id"] = $dbConnection->Record["template"];
		$pageInfo["type"]		 = $dbConnection->Record["type"];
		$pageInfo["modified"]	 = $dbConnection->Record["modified"];
		$pageInfo["id"]			 = $dbConnection->Record["structure_id"];
		$pageInfo["flags"]	 = $dbConnection->Record["structure_flag"];
	}

	return $pageInfo;
}	

## =======================================================================        
##  generate_page        
## =======================================================================        
##  generates a page identified by a page_id  
##
##  TODO:       
## ======================================================================= 
function page_generatePage($page_id=0,$offset=0,$extravars=array()) {
	global $Auth,$input_language,$previewMode;

	## prepare the multiclient support
	$client_id = $Auth->auth["client_id"];	

	## get the page info- and the homepage if we don't have a page
	$pageInfo = frontend_getPageInfo($page_id,$client_id);
	$page_id 		= intval($pageInfo["page_id"]);
	$menu_id 		= isset($pageInfo["id"]) ? $pageInfo["id"] : 0;		
	$template_id	= $pageInfo["template_id"];

	## try to fetch the cached scaffold page
	$cached_scaffold = pagecache_getCacheFile($page_id);

	## check if we have a scaffold
	if($cached_scaffold === false) {
		## we need to create a new scaffold file
		$cached_scaffold = page_generateScaffold($pageInfo,$offset,$extravars);
	}
			
	$dbConnection = new DB_Sql();
				
	## grab the information for this page
	$select_query = "SELECT basename FROM ".PAGE_TEMPLATE." WHERE template_id='$template_id' AND client_id='$client_id'";
	$result_pointer = $dbConnection->query($select_query);
	
	if($dbConnection->next_record()) {
		$filename = $dbConnection->Record["basename"];
		$xmlFile  = $filename.".xml";
		$filename = $filename.".tpl";
	} else {
		## maybe we can come with some good default behavior
		exit();
	}

	## for the body we need to examine the xml file- to find out 
	## what type of form elements we need to position
	$wt = new xmlparser(HTML_DIR.$xmlFile);
	$wt->parse();
	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getCacheElements();	
	$objects = $wt->getCacheObjects();	
	
	## we should get the page content
	$page_record = page_getPage($page_id,$objects);
	
	$counter = 0;
	$num_elements = count($elements);
	
	while($counter < $num_elements) {
		## okay first we try to find out what type we have
		## we wrap this up in a switch statemnt- this way we can extend it more easily
		$element_type = $elements[$counter]['TYPE'];
		$element_name = $elements[$counter]['NAME'];	
		switch($element_type) {
			case 'TEXT':
			case 'COPYTEXT':
			case 'DATE': 
			case 'LINK' :
			case 'FILE':
			case 'IMAGE': 
			case 'LINKLIST': 
			case 'BOX':
				## get the data and set the var in the template
				$target = strtolower($element_type); 
				if(isset($page_record[$element_name])) {
					eval("\$element = output_".$target."(\$page_record[\$element_name],\$elements[\$counter],$menu_id,$page_id);");	

					if(is_array($element)) {
						$varkeys = array();
						$varvals = array();
					
						foreach ($element as $varname => $value) {		
							$varkeys[$varname] = "{".$varname."}";
							$varvals[$varname] = $value;  
						}				
					
						$cached_scaffold = @str_replace($varkeys, $varvals, $cached_scaffold);
					} else {
						$varkeys[$element_name] = "{".$element_name."}";
						$varvals[$element_name] = $element;  				
						$cached_scaffold = @str_replace($varkeys, $varvals, $cached_scaffold);
					}
				}
				break;
		
			case 'INCLUDE' : {
				## basically we need to call the function output_"element_type"
				## and the output the results to the template
				$target = strtolower($element_type); 
				
				eval("\$element = output_".$target."('',\$elements[\$counter],$menu_id,$page_id);");
				
				if(is_array($element)) {
					$varkeys = array();
					$varvals = array();
				
					foreach ($element as $varname => $value) {		
						$varkeys[$varname] = "{".$varname."}";
						$varvals[$varname] = $value;  
					}				

					$cached_scaffold = @str_replace($varkeys, $varvals, $cached_scaffold);
				} else {
					$varkeys[$element_name] = "{".$element_name."}";
					$varvals[$element_name] = $element;  				
					$cached_scaffold = @str_replace($varkeys, $varvals, $cached_scaffold);
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
						eval("\$element = ".$target."_output(\$page_record[\$element_name],\$elements[\$counter],$menu_id,\$page_id);");
					} else {
						##var_dump(eval();
						eval("\$element = ".$target."_output(\$page_record,\$elements[\$counter],\$layout_template,\$menu_id,\$page_id);");
					}	
			
					if(is_array($element)) {
						$varkeys = array();
						$varvals = array();
					
						foreach ($element as $varname => $value) {		
							$varkeys[$varname] = "{".$varname."}";
							$varvals[$varname] = $value;  
						}				
					
						$cached_scaffold = @str_replace($varkeys, $varvals, $cached_scaffold);
					} else {
						
						$varkeys[$element_name] = "{".$element_name."}";
						$varvals[$element_name] = $element;  	
		
						$cached_scaffold = @str_replace($varkeys, $varvals, $cached_scaffold);
					}
										
				}
				break;
			}
		}

		$counter++;
	}

	## finally strip all empty tags
	$cached_scaffold = preg_replace('/{[^ \t\r\n}]+}/', "", $cached_scaffold);    

	## this is it- so we will flush the template here		
	return $cached_scaffold;
}



## =======================================================================        
##  generate_page        
## =======================================================================        
##  generates a page identified by a page_id  
##
##  TODO:       
## ======================================================================= 
function page_generateScaffold($pageInfo,$offset=0,$extravars=array()) {
	global $Auth,$input_language;
			
	## multiclient
	$client_id = $Auth->auth["client_id"];

	$output = '';
	
	$page_id 		= intval($pageInfo["page_id"]);
	$menu_id 		= isset($pageInfo["id"]) ? $pageInfo["id"] : 0;
	$menu_text 		= isset($pageInfo["text"]) ? $pageInfo["text"] : '';		
	$template_id	= $pageInfo["template_id"];
	$page_type		= $pageInfo["type"];	
	$modified		= $pageInfo["modified"];
			
	$dbConnection = new DB_Sql();
			
	if($page_type == 'folder') {
		## we need to output the folder by calling
		## the folder output functions
		folder_outputFolder($page_id);
		exit();	
	}
	
	## grab the information for this page
	$select_query = "SELECT basename FROM ".PAGE_TEMPLATE." WHERE template_id='$template_id' AND client_id='$client_id'";
	$result_pointer = $dbConnection->query($select_query);
	
	$dbConnection->next_record();
	$filename = $dbConnection->Record["basename"];
	$xmlFile  = $filename.".xml";
	$filename = $filename.".tpl";

	if ($filename == ".tpl") {
		## maybe we can come with some good default behavior
		exit();
	}
			
	## prepare the template file
	$layout_template = new Template(HTML_DIR);
	$layout_template->set_templatefile(array("pager"=>$filename,"head" => $filename,"body" => $filename,"foot" => $filename)); 
	
	## here we set the global vars- the user can set them within the templates:
	$layout_template->set_var("matrix:TITLE",$menu_text);
	$layout_template->set_var("matrix:PAGEID",$page_id);
	$layout_template->set_var("matrix:TARGETPAGE",getTargetURL($page_id));
	$layout_template->set_var("matrix:MODDATE",utility_prepareDate(strtotime($modified),DEFAULT_DATE));
	
	## finally set the others values passed to the script
	$layout_template->set_vars($extravars);

	##$output .= $layout_template->fill_block("head");

	## for the body we need to examine the xml file- to find out 
	## what type of form elements we need to position
	$wt = new xmlparser(HTML_DIR.$xmlFile);
	$wt->parse();
	
	## okay we scanned in the xml file- so we now loop through all the elements
	$elements = $wt->getNormalElements();	
	$objects = $wt->getNormalObjects();	
	
	## we should get the page content
	$page_record = page_getPage($page_id,$objects);

	$counter =0;
	$num_elements = count($elements);
	while($counter < $num_elements) {
		## store the output
		$storage = ' ';	
	
		## okay first we try to find out what type we have
		## we wrap this up in a switch statemnt- this way we can extend it more easily
		$element_type = $elements[$counter]['TYPE'];
		$element_name = $elements[$counter]['NAME'];

		switch($element_type) {
			case 'TEXT':
			case 'COPYTEXT':
			case 'DATE': 
			case 'LINK' :
			case 'FILE':
			case 'BOX':
			case 'LINKLIST':
			case 'IMAGE': {
				## get the data and set the var in the template
				$target = strtolower($element_type); 
				if(isset($page_record[$element_name])) {
					$function = "output_".$target;
					$element = $function($page_record[$element_name],$elements[$counter],$menu_id,$page_id);

					if(is_array($element)) {
						$layout_template->set_vars($element);
					} else {
						$layout_template->set_var($element_name,$element);
					}
				}
				break;
			}
			case 'INCLUDE' : {
				## basically we need to call the function output_"element_type"
				## and the output the results to the template
				$target = strtolower($element_type); 
				$function = "output_".$target;
				$element = $function('',$elements[$counter],$menu_id,$page_id);				
				if(is_array($element)) {
					$layout_template->set_vars($element);
				} else {
					$layout_template->set_var($element_name,$element);
				}
				break;
			}								
			case 'LISTVIEW': {
				$element = "";
				
				$element = output_listview($page_record,$elements[$counter],$layout_template,$menu_id,$page_id); 
				$layout_template->set_var($element_name,$element);	
				break;	
			}	
		
			case 'SEARCH': {
				## now it's time to check the previous data entered:
				$layout_template->set_var($element_name.'.action',"content.php?name=".$elements[$counter]['PAGE']);
				$layout_template->set_var($element_name.'.name',"query");
				
				## prepare the hidden fields
				$hidden  ='<input type="hidden" name="PAGE" value="'.$elements[$counter]['PAGE'].'">';
				$hidden .='<input type="hidden" name="LIMIT" value="'.$elements[$counter]['LIMIT'].'">';
				
				$layout_template->set_var($element_name.'.hidden',$hidden);
				break;
			}	
			case 'SEARCHRESULTS': {
				## if we hit a searchresult tag, does means we should display
				## the results for the current search- we need to check
				## if we have a $query.
				$query = "";
				if($_POST["query"]!="" && isset($_POST["query"])) {
					## we have a query so let's perform the search
					## we need to process the umlauts
					$query = convert_general($_POST["query"]);
					$results = search($query);
				}
				$db = new DB_Sql();
				
				$found_pages = array();			## used to store the found pages
				$box_pages = array();
				## we've got the results, but they are in a raw format
				for($i=0; $i < count($results); $i++) {
					## now loop through the results, and check the type
					if($results[$i]['type']=='page') {
						## this means everything is fine, we need to check if this page
						## is currently visible (+current version)
						
						## at least we check if it is visible
						$pageInfo = structure_getStructureID($results[$i]['page_id']);
						if (!checkFlag($pageInfo["flags"],PAGE_INVISIBLE) && checkFlag($pageInfo["flags"],PAGE_ACTIVE)) {
						
							## first we check if the content element that was found belongs to the latest version of the page
							$s_query = "SELECT content_id FROM ".PAGE_CONTENT." WHERE identifier='".$results[$i]['identifier']."' AND page_id='".$results[$i]['page_id']."' AND client_id='$client_id' LIMIT 1";
							$result = $db->query($s_query);	
							if($db->next_record()) {
								if($db->Record['content_id'] == $results[$i]['content_id']) {
									## this means we can savely output the result
									
									## first check if the page_id id is aleady within the found pages
									if(!in_array($results[$i]['page_id'],$found_pages)) {
										$found_pages[] = $results[$i]['page_id'];
									}
								}
							}
						}
						
					} else {
						## if it is a container, we need to check if it is active and if the page it belongs to
						## is current and active
						
						## using this query we get: the page id that hosts the content element. We don't know if the content element of the active version of this page
						## contains the search parameter but we know it has at somepoint- leave it for now
						$s_query = "SELECT page_id FROM ".DB_PREFIX."box_item AS A INNER JOIN ".DB_PREFIX."box AS B ON A.box_id=B.box_id WHERE target='".$results[$i]['page_id']."' AND A.client_id='$client_id' ORDER BY B.modified DESC";
						$result = $db->query($s_query);
						while($db->next_record()) {
							## check if the page is active
							$pageInfo = structure_getStructureID($db->Record['page_id']);
							if (!checkFlag($pageInfo["flags"],PAGE_INVISIBLE) && checkFlag($pageInfo["flags"],PAGE_ACTIVE)) {	
												
								## first check if the page_id id is aleady within the found pages
								if(!in_array($db->Record['page_id'],$found_pages)) {
									$found_pages[] = $db->Record['page_id'];
								}		
								
								## set the found page id to the returned page id- otherwise we only have the box
								$box_pages[] =	$results[$i]['page_id']; 
								$results[$i]['page_id'] = $db->Record['page_id'];	
							}				
						}
					}
				}
				
				## now that we have the results, we will set them in the template
				$basename = $elements[$counter]['TEMPLATE'];
				$filename = $basename.'.tpl';
				$xmlFile  = $basename.'.xml';	
				
				$xmlStructure = new xmlparser(HTML_DIR.$xmlFile);
				$xmlStructure->parse();
				## parse the template file
				$objects 		= $xmlStructure->getObjects();
				$xmlStructure 	= $xmlStructure->getElements();	
						
				$searchresult_template = new Template(HTML_DIR);
				$searchresult_template->set_templatefile(array("body" => $filename)); 

				$displayed_pages = array();
				$results_counter = 1;
				for($i=0;$i<count($results); $i++) {
					if(!in_array($results[$i]["page_id"],$displayed_pages)) {
						if(in_array($results[$i]["page_id"],$found_pages)) {
							$displayed_pages[] = $results[$i]["page_id"];
							$page_data = array();
							## get the page 
							$page_data = page_getPage($results[$i]["page_id"],$objects);
							
							## output the page
							$search_results_counter =0;
							
							## reset the template
							$searchresult_template->varkeys = array();  
							$searchresult_template->varvals = array(); 	
							
							while($search_results_counter < count($xmlStructure)-1) {											
								## okay first we try to find out what type we have
								## we wrap this up in a switch statemnt- this way we can
								## extend it more easily
								$s_element_type = $xmlStructure[$search_results_counter]['TYPE'];
								$s_element_name = $xmlStructure[$search_results_counter]['NAME'];

								switch($s_element_type) {
									case 'TEXT':
									case 'COPYTEXT':
									case 'DATE': 
									case 'LINK' :
									case 'FILE':
									//case 'BOX':
									case 'LINKLIST':
									case 'IMAGE': {
										## basically we need to call the function output_"element_type"
										## and the output the results to the template
										$s_target = strtolower($s_element_type); 
										if(isset($page_data[$s_element_name])) {
											eval("\$s_element = output_".$s_target."(\$page_data[\$s_element_name],\$xmlStructure[\$search_results_counter],0);");	
											if(is_array($s_element)) {
												$searchresult_template->set_vars($s_element);
											} else {
												$searchresult_template->set_var($s_element_name,$s_element);
											}
										}
										break;
									}	
									case 'BOX': {
										if($page_data[$s_element_name]['length'] > 0) {
											for($bb=0;$bb<$page_data[$s_element_name]["length"]; $bb++) {
												$b_target_page = $page_data[$s_element_name][$bb]['target'];
			
												## now check if the target_page was wihtin the search_result
												if(in_array($b_target_page,$box_pages)) {
													## okay we need to get this page then
													
													## get the text elements for the page
													$b_data = array();
													text_getData($b_target_page,$b_data);
													
													foreach($b_data as $b_current_data) {
														## prepare the text element
														$b_current_data['text'] = '<p>'.strip_tags($b_current_data['text']).'</p>';
														
														
														$b_current_data['text']= str_colapse($b_current_data['text'],280,"center",'...');

														$searchresult_template->set_var($b_current_data['identifier'],$b_current_data['text']);
													}
												}
											}
										}
										break;
									}			
								}
								$search_results_counter++;
							}

						## finally we ouptut the link
						$page_id = $results[$i]["page_id"];
						$targetURL = getTargetURL($page_id);	
						$searchresult_template->set_var('matrix:TARGETPAGE',$targetURL);
						$searchresult_template->set_var('matrix:RESULTINDEX',$results_counter);
						
						
						$results_counter++;
						
						$searchresult_output.=$searchresult_template->fill_block("body");
						
						}
					}	
				}
				
				## finally set the general vars
				$layout_template->set_var("SEARCHRESULT.query",$query);
				$layout_template->set_var("SEARCHRESULT.hits",count($found_pages));
				## finally output the whole thing				
				
				$layout_template->set_var($element_name,$searchresult_output);
				break;
			}																				
			case 'DIVIDER': {
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
					$function = $target."_output";
					if(isset($page_record[$element_name])) {
						$element = $function($page_record[$element_name],$elements[$counter],$menu_id,$page_id);
					} else {
						$element = $function($page_record,$elements[$counter],$layout_template,$menu_id,$page_id);
					}	
					
					if(is_array($element)) {
						$layout_template->set_vars($element);
					} else {
						$layout_template->set_var($element_name,$element);
					}
										
				}
				break;
			}
		}

		$counter++;
	}

	## this is it- so we will flush the template here		
	$output .= $layout_template->fill_block("body");
	##$output .= $layout_template->fill_block("foot");
	##$output = $layout_template->finish($output);
	pagecache_createCacheFile($page_id,$output);
	return $output;
}

function str_colapse($string,$length=150,$side="center",$filler="[....]"){

 		  // $length= maximum length of string.
 		 // side= side to cut chars off, center places .. in the center.
 		 // filler is the strning used to indicate chopped of chars.

 		  $strlength=strlen($string);
 		 if($strlength>$length){
 		 	 	 if($side=="right" or $side=="end"){
 		 	 	 	 	  $tmpstring=substr($string,0,($length-strlen($filler))).$filler;
 		 	 	 } elseif($side=="left" or $side=="begin"){
 		 	 	 	 	  $tmpstring=$filler.
substr($string,
 ($strlength-($length-strlen($filler))),
 ($length-strlen($filler)));
 		 	 	 } elseif($side=="center" or $side=="middle"){
 		 	 	 	 	  $firstlength=ceil(($length/100)*20);
 		 	 	 	 	  $tmpstring=substr($string,0,$firstlength).$filler.
substr($string,
 ($strlength-($length-($firstlength+strlen($filler)))),
 ($length-($firstlength+strlen($filler))));
 		 	 	 }
 		 	 	 return $tmpstring;
 		 } else {
 		 	 	 return $string;
 		 }
 }


?>
