<?php
## =======================================================================        
##  search       
## =======================================================================        
##  does the actual search 
##
##  TODO:       
## ======================================================================= 
function search($query){
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## handle data input
	if($query=="" || !isset($query)) {
		## we need to handle the error in here.
	}
	
	## db connection
	$connection= new DB_Sql();
	
	## get the fields in which we will perform the fulltext
	$searchFields = get_fulltext_key(PAGE_CONTENT,$connection);
	
	$select_part = boolean_sql_select(boolean_inclusive_atoms($query),$searchFields);
	
	if(!empty($select_part)) {
		## first we need to get all matches with page_id and page_type
		$select_query  =  "SELECT B.template,identifier,content_id,A.page_id,type, ".boolean_sql_select(boolean_inclusive_atoms($query),$searchFields)." as relevance ";
		$select_query .= "FROM ".PAGE_CONTENT." AS A INNER JOIN ".USER_PAGES." AS B ON B.page_id = A.page_id WHERE ".boolean_sql_where($query,$searchFields)." AND A.client_id='$client_id' "."HAVING relevance>0 ORDER BY relevance DESC";	
		## perform the search
		$result_pointer = $connection->query($select_query); 
	
		## store the foundpages in here
		$foundPages = array();
		
		## store the complete results infos in here
		$searchResults = array();
		
		## how many items have we found
		$countResults = 0;
		
		## let's walk the results and compare the page id to the pages we have already collected
		while($connection->next_record()) {
			## the the page_id
			$page_id = $connection->Record["page_id"];
			
			## check if we already have the pageid in our list
			if(!in_array($page_id, $foundPages)) {
				## let's add this page to the list
				$foundPages[] = $page_id;
				
				## add the page info for this result to the array
				$searchResults[$connection->Record["page_id"]]["page_id"] = $connection->Record["page_id"];
				$searchResults[$connection->Record["page_id"]]["content_id"] = $connection->Record["content_id"];
				$searchResults[$connection->Record["page_id"]]["identifier"] = $connection->Record["identifier"];
				$searchResults[$connection->Record["page_id"]]["type"] = $connection->Record["type"];
				$searchResults[$connection->Record["page_id"]]["template"] = $connection->Record["template"];	
				$searchResults[$connection->Record["page_id"]]["relevance"] = $connection->Record["relevance"];	
				
				$countResults++;	
			}
		}
	}
	
	return $searchResults;
}


## =======================================================================        
##  search       
## =======================================================================        
##  does the actual search 
##
##  TODO:       
## ======================================================================= 
function search_forum($query){
	global $Auth;
	
	## multiclient
	$client_id = $Auth->auth["client_id"];
	
	## handle data input
	if($query=="" || !isset($query)) {
		## we need to handle the error in here.
	}
	
	## db connection
	$connection= new DB_Sql();
	
	## get the fields in which we will perform the fulltext
	$searchFields = get_fulltext_key(DB_PREFIX.'forum_posts_text',$connection);
	$select_part = boolean_sql_select(boolean_inclusive_atoms($query),$searchFields);
	
	if(!empty($select_part)) {
		## first we need to get all matches with page_id and page_type
		$select_query  =  "SELECT ".DB_PREFIX."forum_posts_text.post_id,topic_id,".DB_PREFIX."forum_forums.forum_id, ".$select_part." as relevance ";
		$select_query .= "FROM ".DB_PREFIX."forum_posts_text, ".DB_PREFIX."forum_posts,".DB_PREFIX."forum_forums WHERE status > 0 AND ".DB_PREFIX."forum_forums.forum_id=".DB_PREFIX."forum_posts.forum_id AND ".DB_PREFIX."forum_forums.guest_group=0 AND ".DB_PREFIX."forum_posts_text.post_id=".DB_PREFIX."forum_posts.post_id AND ".boolean_sql_where($query,$searchFields)."HAVING relevance>0 ORDER BY relevance DESC";	


		## perform the search
		$result_pointer = $connection->query($select_query); 
	
		## store the foundpages in here
		$foundPages = array();
		
		## store the complete results infos in here
		$searchResults = array();
		
		## how many items have we found
		$countResults = 0;
		
		## let's walk the results and compare the page id to the pages we have already collected
		while($connection->next_record()) {
			
			## the the page_id
			$post_id = $connection->Record["post_id"];
			
			## check if we already have the pageid in our list
			if(!in_array($post_id, $foundPages)) {
				## let's add this page to the list
				$foundPages[] = $page_id;
				
				## add the page info for this result to the array
				$searchResults[$post_id]["forum_id"] = $connection->Record["forum_id"];
				$searchResults[$post_id]["topic_id"] = $connection->Record["topic_id"];
				$searchResults[$post_id]["post_id"] = $connection->Record["post_id"];
				$searchResults[$post_id]["relevance"] = $connection->Record["relevance"];	
				
				$countResults++;	
			}
		}
	}
	return $searchResults;
}



## =======================================================================        
##  get_fulltext_key        
## =======================================================================        
##  retrieves the fulltext key from a table as a comma delimited 
##  list of values.
##
##  TODO:       
## ======================================================================= 
function get_fulltext_key($table,$db_connection){
	## grab all keys of db.table 
	$select_query = "SHOW INDEX FROM $table";
	$result_pointer = $db_connection->query($select_query);	

	## grab only fulltext keys 
	while($db_connection->next_record()) {
		$nth_index = $db_connection->Record["Index_type"];
		if($nth_index=='FULLTEXT'){
			$match_a[].= $db_connection->Record["Column_name"];
		}
	}

	/* delimit with commas */
	$match=implode(',',$match_a);
	return $match;
	
}

## =======================================================================        
##  boolean_mark_atoms        
## =======================================================================        
##  used to identify all word atoms; works using simple
##  string replacement process:
##
##    		1. strip whitespace
##    		2. apply an arbitrary function to subject words
##    		3. represent remaining characters as boolean operators:
##       		a. ' '[space] -> AND
##       		b. ','[comma] -> OR
##       		c. '-'[minus] -> NOT
##    		4. replace arbitrary function with actual sql syntax
##    		5. return sql string
##      
## ======================================================================= 
function boolean_mark_atoms($string){

	$result=trim($string);
	$result=str_replace('-',' ',$result);

	$result=preg_replace("/([[:space:]]{2,})/",' ',$result);
	
	/* convert normal boolean operators to shortened syntax */
	$result=eregi_replace(' not ',' -',$result);
	$result=eregi_replace(' and ',' ',$result);
	$result=eregi_replace(' or ',',',$result);

	/* strip excessive whitespace */
	$result=str_replace('( ','(',$result);
	$result=str_replace(' )',')',$result);
	$result=str_replace(', ',',',$result);
	$result=str_replace(' ,',',',$result);
	##$result=str_replace('- ','-',$result);

	/* apply arbitrary function to all 'word' atoms */
	$result=preg_replace("/([&A-Za-z0-9;]{1,}[&A-Za-z0-9;\.\_]{0,})/","foo[('\\0')]bar",$result);

	/* strip empty or erroneous atoms */
	$result=str_replace("foo[('')]bar",'',$result);
	$result=str_replace("foo[('-')]bar",' ',$result);

	/* add needed space */
	$result=str_replace(')foo[(',') foo[(',$result);
	$result=str_replace(')]bar(',')]bar (',$result);

	/* dispatch ' ' to ' AND ' */
	$result=str_replace(' ',' AND ',$result);

	/* dispatch ',' to ' OR ' */
	$result=str_replace(',',' OR ',$result);

	/* dispatch '-' to ' NOT ' */
	##$result=str_replace(' -',' NOT ',$result);
	return $result;
}

## =======================================================================        
##  boolean_sql_where        
## =======================================================================        
## 	function used to transform identified atoms into mysql
##	parseable boolean fulltext sql string; allows for
##	nesting by letting the mysql boolean parser evaluate
##	grouped statements
##      
## ======================================================================= 
function boolean_sql_where($string,$match){
	$result = boolean_mark_atoms($string);
	
	/* dispatch 'foo[(#)]bar to actual sql involving (#) */
	##$result=preg_replace("/foo\[\(\'([^\)]{4,})\'\)\]bar/"," match ($match) against ('\\1')>0 ",$result);
	$result=preg_replace("/foo\[\(\'([^\)]{4,})\'\)\]bar/"," match ($match) against ('\\1') ",$result);
	$result=preg_replace("/foo\[\(\'([^\)]{1,3})\'\)\]bar/e"," '('.boolean_sql_where_short(\"\\1\",\"$match\").')' ",$result);

	return $result;
}

## =======================================================================        
##  boolean_sql_where_short        
## =======================================================================        
##	parses short words <4 chars into proper SQL: special adaptive
##	case to force return of records without using fulltext index
##	keep in mind that allowing this functionality may have serious
##	performance issues, especially with large datasets
##
## ======================================================================= 
function boolean_sql_where_short($string,$match){
	$match_a = explode(',',$match);
	for($ith=0;$ith<count($match_a);$ith++){
		$like_a[$ith] = " $match_a[$ith] LIKE '%$string%' ";
	}
	$like = implode(" OR ",$like_a);

	return $like;
}


## =======================================================================        
##  boolean_sql_select        
## =======================================================================        
##	function used to transform a boolean search string into a
##	mysql parseable fulltext sql string used to determine the
##	relevance of each record;
##	1. put all subject words into array
##	2. enumerate array elements into scoring sql syntax
##	3. return sql string
## ======================================================================= 
function  boolean_sql_select($string,$match){
	/* build sql for determining score for each record */
	preg_match_all("/([&A-Za-z0-9;]{1,}[&A-Za-z0-9;\-\.\_]{0,})/",$string,$result);
	$result = $result[0];
	for($cth=0;$cth<count($result);$cth++) {
		if(strlen($result[$cth])>=4) {
			$stringsum_long .=" $result[$cth] ";
		} else {
			$stringsum_a[] =' '.boolean_sql_select_short($result[$cth],$match).' ';
		}
	}
	if(strlen($stringsum_long)>0){
			$stringsum_a[] = " match ($match) against ('$stringsum_long') ";
	}
	$stringsum .= @implode("+",$stringsum_a);

	return $stringsum;
}

## =======================================================================        
##  boolean_sql_select_short        
## =======================================================================        
##	parses short words <4 chars into proper SQL: special adaptive
##	case to force 'scoring' of records without using fulltext index
##	keep in mind that allowing this functionality may have serious
##	performance issues, especially with large datasets
## ======================================================================= 
function boolean_sql_select_short($string,$match){
	$match_a = explode(',',$match);
	$score_unit_weight = .2;
	for($ith=0;$ith<count($match_a);$ith++){
		$score_a[$ith] =
			" $score_unit_weight*(
			LENGTH($match_a[$ith]) -
			LENGTH(REPLACE(LOWER($match_a[$ith]),LOWER('$string'),'')))
			/LENGTH('$string') ";
	}
	$score = implode(" + ",$score_a);

	return $score;
}


## =======================================================================        
##  boolean_inclusive_atoms        
## =======================================================================        
##	returns only inclusive atoms within boolean statement
##
## ======================================================================= 
function boolean_inclusive_atoms($string){

	$result=trim($string);
	$result=preg_replace("/([[:space:]]{2,})/",' ',$result);

	/* convert normal boolean operators to shortened syntax */
	$result=eregi_replace(' not ',' -',$result);
	$result=eregi_replace(' and ',' ',$result);
	$result=eregi_replace(' or ',',',$result);

	/* drop unnecessary spaces */
	$result=str_replace(' ,',',',$result);
	$result=str_replace(', ',',',$result);
	$result=str_replace('- ','-',$result);

	/* strip exlusive atoms */
	$result=preg_replace("/(\-\([&A-Za-z0-9;]{1,}[&A-Za-z0-9;\-\.\_\,]{0,}\))/",'',$result);
	$result=preg_replace("/(\-[&A-Za-z0-9;]{1,}[&A-Za-z0-9;\-\.\_]{0,})/",'',$result);
	$result=str_replace('(',' ',$result);
	$result=str_replace(')',' ',$result);
	$result=str_replace(',',' ',$result);

	return $result;
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *	:: boolean_parsed_as($string) ::
 *	returns the equivalent boolean statement in user readable form
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function boolean_parsed_as($string){
	$result = boolean_mark_atoms($string);
	/* dispatch 'foo[(%)]bar' to empty string */
	$result=str_replace("foo[('","",$result);
	$result=str_replace("')]bar","",$result);

	return $result;
}
?>