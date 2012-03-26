<?php

	// Required includes
	include_once('../config.php');
	include_once('db_mysql.php');
	include_once(CLASSES_DIR."class_mailer.php");

	/******************************************************************
	*
	*  SubjectAdmin Class
	*  ====================
	*
	*  This class manages subject topic code and subject page assignments.
	*  Automatically updates old topic codes to their new equivalents or sets as 'N/A' if they do not exist.
	*  Detects old subject page titles and updates them.
	*
	*  @usage           This class is used in 'Webmatrix > Subjects' tab
	*
	*  @author			Nathan Kowald
	*  @since			01-07-2009
	*  @lastmodified    30-09-2009
	*
	*    Class Methods
	*    --------------------
	*  - getSubjectTemplateID
	*  - pageExists
	*  - getPageTitles
	*  - getPageUrl
	*  - addSubject
	*  - getPageActive
	*  - getSubjects
	*  - updateSubjects
	*  - getSubjectStats
	*  - deleteSubject
	*  - findSimilarPage
	*  - findOldPageNames
	*  - makeURL
	*  - getTopicCodeFromTitle
	*  - getTitleFromTopicCode
	*  - createPage
	*  - updatePageInfo
	*  - updateMenuOrder
	*  - deleteOldTables
	*  - showInvalidCodes
	*  - deleteInvalidSubjectPages
	*  - getNotAssignedCourses
	*  - showNotAssignedCourses
	*  - sendSTCNotifEmail
	*
	******************************************************************/

	class SubjectAdmin {

		public $debug;
		public $errors;
		public $subject_tpl;
		public $subject_tpl_id;
		public $valid_subject_codes;
		public $subjects;
		public $subjects_structure_id;
		public $old_page_names_found;
		public $notification_email;
		public $unassigned_courses;

		public function __construct() {

			$this->debug = DEVELOPMENT; // Show MySQL Errors - Useful while developing
			$this->errors = array();
			// TODO: Get these via query in case they change
			$this->subject_tpl = "11subjectpage";
			$this->subjects_structure_id = 135;
			$this->notification_email = 'nkowald@conel.ac.uk';
			// Run set up methods
			$this->getSubjectCodes();
			$this->getSubjectTemplateID();
			$this->subjects = '';
			$this->updateSubjects();
			$this->subjects = $this->getSubjects();
			$this->old_page_names_found = $this->findOldPageNames();
			$this->unassigned_courses = array();
			$this->getNotAssignedCourses();

		} // __contstruct()

		// Get valid subject topic codes from the tblsubject table
		public function getSubjectCodes() {
	
			$this->valid_subject_codes = array();
			
			$sql = new DB_Sql();
			$sql->connect();
			$query = "SELECT Description, ID FROM tblsubject";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$key = $sql->Record['Description'];
					$this->valid_subject_codes[$key] = $sql->Record['ID'];
				}
			}
		}
		
		/***********************************
		*  getSubjectTemplateID
		*
		*  Sets "subject_tpl_id" property for use in MySQL queries
		*
		************************************/

		public function getSubjectTemplateID() {

			$sql = new DB_Sql();
			$sql->connect();
			$query = "SELECT template_id FROM webmatrix_page_template WHERE basename = '".$this->subject_tpl."'"; // Get template ID
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$this->subject_tpl_id = $sql->Record['template_id'];
				}
			}

		} // getSubjectTemplateID()


		/***********************************
		*  pageExists
		*
		*  Compares passed subject name with page title of every existing subject page
		*  A page "exists" if subject topic name matches page name.
		*
		*  @param    $subject_name    Subject Topic Description
		*  @return   boolean          TRUE/FALSE
		*
		************************************/

		public function pageExists($subject_id) {

			$sql = new DB_Sql();
			$sql->connect();

			$query = "SELECT page_id FROM webmatrix_user_pages WHERE template = ".$this->subject_tpl_id;
			$sql->query($query, $this->debug);
			// build array from page ids
			$page_ids = array();
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$page_ids[] = $sql->Record['page_id'];
				}
			}
			
			$subject_ids = array();
			foreach($page_ids as $id) {
			
				$sql = new DB_Sql();
				$sql->connect();
				$query = "SELECT text FROM webmatrix_extra_checkboxgroup w where page_id = '".$id."' AND identifier = 'SUBJECT'";
				$sql->query($query, $this->debug);
				
				if ($sql->num_rows() > 0) {
					while($sql->next_record()) {
						$subject_ids[] = $sql->Record['text'];
					}
				}
				
			} // foreach
			
			$exists = FALSE;
			// Clean subject name
			if (in_array($subject_id,$subject_ids)) {
				$exists = TRUE;
			} else {
				$exists = FALSE;
			}
			return $exists;

		} // pageExists()


		/***********************************
		*  getPageTitles
		*
		*  Gets title of every subject page and put it into an array
		*
		*  @return    array    $page_titles    Indexed array containing page titles
		*
		************************************/

		public function getPageTitles() {

			$sql = new DB_Sql();
			$sql->connect();

			$query = "SELECT title FROM webmatrix_user_pages WHERE template = ".$this->subject_tpl_id."";
			$sql->query($query, $this->debug);
			// build array from page titles
			$page_titles = array();
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$page_titles[] = $sql->Record['title'];
				}
			}
			return $page_titles;

		} // getPageTitles()


		/***********************************
		*  getPageUrl
		*
		*  Builds a string URL of the subject page passed to the method
		*
		*  @TODO     This is probably naughty; should ideally get page url via existing webmatrix methods - should use matrix_engine/functions/structure.php
		*  @param    string    $page_title    Page Title
		*  @return   string    $page_url      URL of given page
		*
		************************************/

		public function getPageUrl($page_title) {

			$sql = new DB_Sql();
			$sql->connect();

			// Get parent id of subjects
			$parent_id = '';
			$query = "SELECT structure_parent from webmatrix_structure where structure_id = ".$this->subjects_structure_id;
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$parent_id = $sql->Record['structure_parent'];
				}
			}

			// Get parent url of subjects
			if ($parent_id != '') {
				$query = "SELECT structure_url FROM webmatrix_structure WHERE structure_id = $parent_id";
				$sql->query($query, $this->debug);
				if ($sql->num_rows() > 0) {
					while($sql->next_record()) {
					$parent_url = $sql->Record['structure_url'] . "/";
					}
				}
			}

			// Get url of subjects page
			$subjects_url = '';
			$query = "SELECT structure_url FROM webmatrix_structure WHERE structure_id = ".$this->subjects_structure_id;
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$subjects_url = $sql->Record['structure_url'] . "/";
				}
			}

			// Get url of given subject page
			$pagename_url = '';
			$page_title = mysql_real_escape_string($page_title, $sql->Link_ID);
			$query = "SELECT structure_url FROM webmatrix_structure WHERE structure_parent = ".$this->subjects_structure_id." and structure_text = '$page_title'";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$pagename_url = $sql->Record['structure_url'];
				}
			}

			// Build page url
			$page_url = "/" . $parent_url . $subjects_url . $pagename_url;
			$page_url = ($pagename_url != '') ? $page_url : '';

			return $page_url;

		} // getPageUrl()


		/***********************************
		*  addSubject
		*
		*  Adds a subject to the tblsubject table
		*  Note: Not used as valid subjects come from array in constructor
		*
		*  @param    string    $id             Subject Topic Code (eg. ARTSMEDIA)
		*            string    $description    Subject Topic Description
		*  @return   boolean   TRUE/FALSE
		*
		************************************/

		public function addSubject($id, $description) {

			$sql = new DB_Sql();
			$sql->connect();
			$id = strtoupper(trim(str_replace(' ','',$id))); // trim and remove commas
			$id_length = strlen($id); // Check length of ID - musn't be longer than ten chars
			if ($id_length > 10) {
				$this->errors[] = 'Subject Topic Code Too Long (10 Character Limit)';
			}
			$id = mysql_real_escape_string($id, $sql->Link_ID); // Escape ID for MySQL INSERT
			$description = trim($description); // trim
			$description = mysql_real_escape_string($description, $sql->Link_ID); // Escape Description for MySQL INSERT
			$description_length = strlen($description); // Check length of Description Name - musn't be larger than 40 chars
			if ($description_length > 40) {
				$this->errors[] = 'Subject Name Too Long (40 Character Limit)';
			}
			// ID and Description must not be blank
			if ($id == '' || $description == '') {
				if ($id == '') $this->errors[] =  'Subject Topic Code not entered';
				if ($description == '') $this->errors[] = 'Subject Name not entered';
			}
			// Build INSERT query
			$query = "INSERT INTO tblsubject VALUES ('$id', '$description')";
			$sql->query($query,$this->debug);
			// Check if query worked
			if ($sql->num_rows_affected() == 0) {
				return false;
			} else {
				return true;
			}

		} // addSubject()


		/***********************************
		*  getPageActive
		*
		*  Works out if a page is 'active' (if it exists and is set to displayed).
		*
		*  @param    string    $subject_name       Subject Name
		*  @return   boolean   TRUE if active FALSE if de-activated
		*
		************************************/

		private function getPageActive($subject_name) {

			$sql = new DB_Sql();
			$sql->connect();
			$subject_name = mysql_real_escape_string($subject_name, $sql->Link_ID); // sanitise subject name
			$active = 0;
			$query = "SELECT structure_flag FROM webmatrix_structure WHERE structure_parent = ".$this->subjects_structure_id." AND structure_text = '$subject_name'";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while ($sql->next_record()) {
					$active = $sql->Record['structure_flag'];
				}
			} else {
				$active = 0;
			}
			
			if ($active == 2) {
				return TRUE;
			} else {
				return FALSE;	
			}

		} // getPageActive()



		/***********************************
		*  getSubjects
		*
		*  Creates an array for subjects and their stats
		*
		*  @return    array     List of Subjects and stats
		*
		************************************/

		public function getSubjects() {

			$sql = new DB_Sql();
			$sql->connect();
			$query = "SELECT * FROM tblsubject ORDER BY Description ASC";
			$sql->query($query, $this->debug);

			// Find if a page exists for subject
			$subjects = array();
			$subject_ids = array();
			$i = 0; // array counter
			while ($sql->next_record()) {
				$subject_ids[] = $sql->Record['ID'];
				$subjects[$i]['ID'] = $sql->Record['ID'];
				$subjects[$i]['Description'] = $sql->Record['Description'];
				$subjects[$i]['Exists'] = $this->pageExists($sql->Record['ID']);
				$subjects[$i]['Active'] = $this->getPageActive($sql->Record['Description']);
				$stats = $this->getSubjectStats($sql->Record['ID']);
				$subjects[$i]['No_Pages'] = $stats['pages'];
				$subjects[$i]['No_Courses'] = $stats['courses'];
				$subjects[$i]['No_News_Events'] = $stats['news_events'];
				$subjects[$i]['Valid_STC'] = (in_array($sql->Record['ID'],$this->valid_subject_codes)) ? TRUE : FALSE;
				$i++;
			}

			$missing_subjects = array();
			foreach ($this->valid_subject_codes as $title => $topic_code) {
				// If a valid subject topic code is not found in the subjects table, add it
				if (!in_array($topic_code, $subject_ids)) {
					// Add Subject to tblsubject
					$query = "INSERT INTO tblsubject VALUES ('$topic_code','$title')";
					$sql->query($query, $this->debug);
					if ($sql->num_rows_affected() > 0) {
						// Success
					} else {
						echo "Error adding $title to subjects table";
						exit;	
					}
				}
			} // foreach
			
			return $subjects;

		} // getSubjects()


		/***********************************
		*  updateSubjects
		*
		*  Updates outdated subject topic codes for page, news, events and courses with
		*  their new topic code if it exists, or 'N/A' if it doesn't.
		*
		************************************/

		public function updateSubjects() {

			$sql = new DB_Sql();
			$sql->connect();

			// update news and event subject topic code assignments
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'CONSTRBUI' WHERE text = 'BUILDSERV'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'CONSTRBUI' WHERE text = 'CNSTRCRAFT'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'ENGMATHICT' WHERE text = 'ENGMATH'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'HAIRBEAU' WHERE text = 'HAIRBEAUSP'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'FORGNLANG' WHERE text = 'MODERNLANG'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'APMEDFRSCI' WHERE text = 'SCIENCES'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'N/A' WHERE text = 'SOCSCIENCE'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'N/A' WHERE text = 'TU'";
			$sql->query($query, $this->debug);
			$query = "UPDATE webmatrix_extra_checkboxgroup SET text = 'N/A' WHERE text = 'YNGPEOPCOL'";
			$sql->query($query, $this->debug);

			// update tblunit courses to use their new sujbect topic codes
			$query = "UPDATE tblunits SET Subject_ID = 'CONSTRBUI' WHERE Subject_ID = 'BUILDSERV'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'CONSTRBUI' WHERE Subject_ID = 'CNSTRCRAFT'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'ENGMATHICT' WHERE Subject_ID = 'ENGMATH'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'HAIRBEAU' WHERE Subject_ID = 'HAIRBEAUSP'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'FORGNLANG' WHERE Subject_ID = 'MODERNLANG'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'APMEDFRSCI' WHERE Subject_ID = 'SCIENCES'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'N/A' WHERE Subject_ID = 'SOCSCIENCE'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'N/A' WHERE Subject_ID = 'TU'";
			$sql->query($query, $this->debug);
			$query = "UPDATE tblunits SET Subject_ID = 'N/A' WHERE Subject_ID = 'YNGPEOPCOL'";
			$sql->query($query, $this->debug);

			// Delete invalid subjects - will not delete any courses or assignments as we've updated them to use correct topic codes in previous steps
			$query = "DELETE FROM tblsubject WHERE ID IN ('BUILDSERV','CNSTRCRAFT','ENGMATH','HAIRBEAUSP','MODERNLANG','SCIENCES','SOCSCIENCE','YNGPEOPCOL','TU')";
			$sql->query($query, $this->debug);

			$query = "DESCRIBE tblfees";
			$sql->query($query, $debug = FALSE);
			if ($sql->num_rows() > 0) {
				$this->deleteOldTables(); // if old tables found: delete them
			}

			// Now we've updated courses - search for existing webmatrix pages using subject codes not in valid list - if found de-activate them.
			//$this->deleteInvalidSubjectPages();

		} // updateSubjects()


		/***********************************
		*  getSubjectStats
		*
		*  Returns an associative array containing number stats for:
		*  pages, courses, news & event assignments
		*
		*  @param     string    $topic_code    Subject Topic Code
		*  @return    array     $stats         Associative array containing stats for passed subject
		*
		************************************/

		private function getSubjectStats($topic_code) {

			$sql = new DB_Sql();
			$sql->connect();

			$topic_code = mysql_real_escape_string($topic_code, $sql->Link_ID); // sanitise topic_code
			// Get Subject name from topic_code
			$query = "SELECT Description FROM tblsubject WHERE ID = '$topic_code'";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$subject_name = $sql->Record['Description'];
				}
			}
			// Sanitize subject name
			$subject_name = mysql_real_escape_string($subject_name, $sql->Link_ID); // sanitise subject name

			// Get number of courses
			$query = "SELECT * FROM tblunits WHERE Subject_ID = '$topic_code'";
			$sql->query($query, $this->debug);
			$no_courses = $sql->num_rows();

			// Get page_id's of subjects so we can exclude them from assignments table retrieval
			$query = "SELECT page_id FROM webmatrix_user_pages WHERE title = '$subject_name' AND template ='".$this->subject_tpl_id."'";
			$sql->query($query, $this->debug);
			$page_ids = array();
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
				   $page_ids[] = $sql->Record['page_id'];
				}
			}
			$no_pages = count($page_ids);

			// Get assignments for current subject
			$query = "SELECT page_id FROM webmatrix_extra_checkboxgroup WHERE identifier = 'SUBJECT' AND text = '$topic_code'";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				$assign_page_ids = array();
				while($sql->next_record()) {
					$assign_page_ids[] = $sql->Record['page_id'];
				}
			}
			// Remove page_id of subject assignment to only show News and Events assignments.
			$news_events_page_ids = array_diff($assign_page_ids, $page_ids);

			// Count the number of News and Events assignments
			$no_ne_assigns = count($news_events_page_ids);

			// Build Stats Array and return it
			$stats = array();
			$stats['pages'] = $no_pages; // number of pages linked to subject
			$stats['courses'] = $no_courses; // number of courses linked to subject
			$stats['news_events'] = $no_ne_assigns; // number of news and events linked to subject

			return $stats;

		} // getSubjectStats()


		/***********************************
		*  deleteSubject
		*
		*  Deletes a subject and all pages, courses and news and events that reference it
		*
		*  @param     string    $topic_code    Subject Topic Code
		*
		************************************/

		public function deleteSubject($topic_code) {

			$sql = new DB_Sql();
			$sql->connect();

			$topic_code = mysql_real_escape_string($topic_code, $sql->Link_ID); // Sanitise topic code


			// Get Subject Name from Subject Topic Code
			$query = "SELECT Description FROM tblsubject where ID = '$topic_code'";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$subject_name = $sql->Record['Description'];
				}
			}

			// Delete subject
			// Courses and Occurrences are deleted automatically when subject deleted - due to relational table structure
			$query = "DELETE FROM tblsubject WHERE ID = '$topic_code'";
			$sql->query($query, $this->debug);

			// Sanitize subject name
			$subject_name = mysql_real_escape_string($subject_name, $sql->Link_ID); // sanitise subject name

			// Find page id of this subject page
			$page_id = '';
			$query = "SELECT page_id FROM webmatrix_user_pages WHERE template = ".$this->subject_tpl_id." AND title = '$subject_name'";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() == 1) {
				while($sql->next_record()) {
					$page_id = $sql->Record['page_id'];
				}
			}

			// Delete page
			if ($page_id != '') {
				$query = "DELETE FROM webmatrix_user_pages WHERE template = ".$this->subject_tpl_id." AND title = '$subject_name' AND page_id = $page_id";
				$sql->query($query, $this->debug);
			}

			/* delete page assignment and news & event assignments */
			$query = "DELETE FROM webmatrix_extra_checkboxgroup WHERE text = '$topic_code'";
			$sql->query($query, $this->debug);


		} // deleteSubject()


		/***********************************
		*  findSimilarPages
		*
		*  Finds matches for subject pages with titles that differ from the subject they are assigned to
		*  Uses PHP similar_text() function to get most similar page title to passed subject name
		*
		*  @param     string    $subject_name    Subject Title
		*  @return    string    $match           Most similar page returned or blank string if no matches found
		*
		************************************/

		public function findSimilarPage($subject_name) {

			// set array of page titles
			$page_titles = $this->getPageTitles();
			// Clean subject name - remove apostrophes
			$subject_name = str_replace('\'','',strtolower($subject_name));
			$matches = array();
			$match = '';
			foreach ($page_titles as $title) {
				// Clean page title - remove apostrophes
				$stripped_title = str_replace('\'','',strtolower($title));
				$similarity = similar_text($stripped_title, $subject_name);
				// 15 or higher means a likely match
				if ($similarity >= 15) {
					$matches[$title] = $similarity;
				} else {
					// Not similar enough, match on words instead
					$sanitised_subject = str_replace('and ','',$subject_name);
					$sanitised_title = str_replace('and ','',$stripped_title);
					// array of words from title
					$subject_words = explode(' ',$sanitised_subject);
					$words_found = 0;
					foreach($subject_words as $word) {
						$words_found += substr_count($sanitised_title, $word);
					}
					// if two or more words found, it's probably a match
					if ($words_found >= 2) {
						$match = $title;
					}
				}
			} // foreach

			// If more than one match found, get highest match
			if (count($matches) > 1) {
				// sort array in reverse order and maintain index association
				arsort($matches);
				// Get highest match
				$match = array_slice($matches,0,1,true);
				$match = key($match);
			} else if (count($matches) == 1) {
				// Only one match found, return match
				$match = array_slice($matches,0,1,true);
				$match = key($match);
			}

			return $match;

		} // findSimilarPage()


		/***********************************
		*  findOldPageNames
		*
		*  Looks for old subject page names - sets property as TRUE if old page names used
		*
		*  @return    boolean    $this->old_page_names_found    TRUE/FALSE
		*
		************************************/
		public function findOldPageNames() {

			foreach ($this->subjects as $subject) {
				if ($subject['Exists'] === FALSE) {
					$match = $this->findSimilarPage($subject['Description']);
					if ($match != '') {
						$this->old_page_names_found = TRUE;
					}
				}
			}
			// Before we return "old page name", check if match exists in subjects
			foreach ($this->subjects as $subject) {
				if ($subject['Description'] == $match) {
					$this->old_page_names_found = FALSE;
				}
			}
			return $this->old_page_names_found;

		} //findOldPageNames()


		/***********************************
		*  makeURL
		*
		*  Creates a url from a given string
		*  (eg. Arts and Media becomes arts_and_media)
		*
		*  @return    string    $string    url
		*
		************************************/

		public function makeURL($string) {
			if ($string != '') {
				$string = strtolower($string); // make lowercase
				$string = str_replace(' ','_',$string); // convert spaces to underscores
				$string = preg_replace('/[^\w\d_]/si', '', $string); // strip whitespace and numbers
				return $string;
			}
		} // makeURL()


		/***********************************
		*  getTopicCodeFromTitle
		*
		*  Gets subject topic code from passed subject title
		*
		*  @param     string    $title     Subject Title
		*  @return    string    $string    Subject Topic Code
		*
		************************************/

		public function getTopicCodeFromTitle($title) {

			$sql = new DB_Sql();
			$sql->connect();
			
			// Sanitise $title
			$title = mysql_real_escape_string($title, $sql->Link_ID);

			$query = "SELECT ID FROM tblsubject WHERE Description = '$title'";
			$sql->query($query, $this->debug);
			$topic_code = '';
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$topic_code = $sql->Record['ID'];
				}
			}
			return $topic_code;

		} // getTopicCodeFromTitle()
		
		
		/***********************************
		*  getTitleFromTopicCode
		*
		*  Gets subject title from passed subject topic code
		*
		*  @param     string    $topic_code     Subject Topic Code
		*  @return    string    $string    		Subject Title
		*
		************************************/

		public function getTitleFromTopicCode($topic_code) {

			$sql = new DB_Sql();
			$sql->connect();
			
			$topic_code = mysql_real_escape_string($topic_code, $sql->Link_ID);

			$query = "SELECT Description FROM tblsubject WHERE ID = '$topic_code'";
			$sql->query($query, $this->debug);
			$title = '';
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$title = $sql->Record['Description'];
				}
			}
			return $title;

		} // getTitleFromTopicCode()


		/***********************************
		*  createPage
		*
		*  Creates a page for a given page title
		*  Adds page, creates page structure, adds page heading, assigns page to its subject topic code
		*
		*  @param     string     $page_title    Page Title
		*  @return    boolean	 TRUE/FALSE depending of MySQL errors
		*
		************************************/

		public function createPage($page_title) {

			$sql = new DB_Sql();
			$sql->connect();

			$errors = FALSE;
			$error_msgs = array();

			// sanitise page_title
			$page_title = mysql_real_escape_string($page_title, $sql->Link_ID);

			// Changed to show which entries were added by the SubjectAdmin class (with FSM [Flying Spaghetti Monster] Privileges)
			$user = 'Subject Admin Script (FSM)';

			// Create date - "2009-06-21 14:34:04": MySQL timestamp format
			$date_now = date('Y-m-d H:i:s');

			// Get the page_id of the page matching the passed page_title
			$query = "SELECT page_id FROM webmatrix_user_pages WHERE title = '$page_title' AND template = ".$this->subject_tpl_id."";
			$sql->query($query, $this->debug);
			// If page ID found for page_title given
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$page_id = $sql->Record['page_id'];
				}
			} else {
				
				// Page does NOT exist so create it
				$query = "INSERT INTO webmatrix_user_pages (title, active, template, homepage, client_id, type, created, user) VALUES ('$page_title','1','".$this->subject_tpl_id."','0','1','page','$date_now','$user')";

				$sql->query($query, $this->debug);
				if ($sql->num_rows_affected() == 0) {
					$errors = TRUE;
					$error_msgs[] = "Could not add new page for $page_title";
				}

				// Get page_id of created page
				$query = "SELECT page_id FROM webmatrix_user_pages WHERE title = '$page_title' AND template = ".$this->subject_tpl_id."";
				$sql->query($query, $this->debug);
				if ($sql->num_rows() > 0) {
					while($sql->next_record()) {
						$page_id = $sql->Record['page_id'];
					}
				}
			}

			// Work out largest structure_order number for next insert
			$query = "SELECT structure_order FROM webmatrix_structure WHERE structure_parent = ".$this->subjects_structure_id." ORDER BY structure_order DESC LIMIT 1";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$latest_order_no = $sql->Record['structure_order'];
				}
			} else {
				// No pages exists so make $latest_order_no = 0;
				$latest_order_no = 0;
				/*
				$errors = TRUE;
				$error_msgs[] = "Could not retrieve structure_order of existing subjects";
				*/
			}

			// Work out if valid subject code has courses - if not, de-activate page in webmatrix
			$topic_code = $this->getTopicCodeFromTitle($page_title);
			$subject_stats = $this->getSubjectStats($topic_code);
			$no_courses = $subject_stats['pages'];
			$structure_flag = ($no_courses > 0) ? 2 : 0;

			// Create webmatrix_structure
			$structure_url = $this->makeURL($page_title);
			$next_order_no = $latest_order_no + 1;
			$query = "INSERT INTO webmatrix_structure (structure_parent, structure_order, structure_text, structure_url, structure_flag, page_id, client_id) VALUES ('$this->subjects_structure_id','$next_order_no','$page_title','$structure_url','$structure_flag','$page_id','1')";
			$sql->query($query, $this->debug);
			if ($sql->num_rows_affected() == 0) {
				$errors = TRUE;
				$error_msgs[] = "Could not update page structure for $page_title";
			}

			// Add Page Content (Just Heading)
			$query = "INSERT INTO webmatrix_page_content (page_id, identifier, text, modified, client_id) VALUES ('$page_id', 'HEADLINE', '$page_title', '$date_now','1')";
			$sql->query($query, $this->debug);
			if ($sql->num_rows_affected() == 0) {
				$errors = TRUE;
				$error_msgs[] = "Could not create Headline for $page_title";
			}

			// Create Page Date Entry - Unixtime date format
			$date_now_unixtime = date('U');
			$query = "INSERT INTO webmatrix_page_date (page_id, identifier, date, client_id) VALUES ('$page_id','DATE','$date_now_unixtiime','1')";
			$sql->query($query, $this->debug);
			if ($sql->num_rows_affected() == 0) {
				$errors = TRUE;
				$error_msgs[] = "Could not create date entry for $page_title";
			}

			// Finally, assign the newly created page to its correct Subject Topic Code if it doesn't already exist
			$assignment_exists = FALSE;
			$query = "SELECT * FROM webmatrix_extra_checkboxgroup WHERE identifier = 'SUBJECT' AND page_id = $page_id";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				$assignment_exists = TRUE;
			}
			// If page is not assigned then assign it
			if ($assignment_exists === FALSE) {
				$topic_code = $this->getTopicCodeFromTitle($page_title);
				$query = "INSERT INTO webmatrix_extra_checkboxgroup (page_id, identifier, text, client_id) VALUES ('$page_id','SUBJECT','$topic_code','1')";
				$sql->query($query, $this->debug);
				if ($sql->num_rows_affected() == 0) {
					$errors = TRUE;
					$error_msgs[] = "Could not create subject topic code assignment for $page_title";
				}
			}

			if ($errors === FALSE) {
				return TRUE;
			} else {
				if ($this->debug === TRUE) {
					echo "<h3>Errors Found</h3>";
					var_dump($error_msgs);
					exit;
				}
				return FALSE;
			}
		} // createPage()


		public function updatePageInfo($old_title, $new_title) {

			if ($old_title != '' && $new_title != '') {

				$sql = new DB_Sql();
				$sql->connect();
				$errors = FALSE;
				$error_msgs = array();

				// Sanitise name inputs for MySQL updates
				$old_title = mysql_real_escape_string($old_title, $sql->Link_ID);
				$new_title = mysql_real_escape_string($new_title, $sql->Link_ID);

				// Get topic code of passed subject
				$topic_code = $this->getTopicCodeFromTitle($new_title);

				// First get the correct page_id of the page we need to update
				$page_id = '';
				$query = "SELECT page_id FROM webmatrix_user_pages WHERE title = '$old_title' AND template = ".$this->subject_tpl_id." AND type = 'page'";
				$sql->query($query, $this->debug);
				if ($sql->num_rows() > 0) {
					while($sql->next_record()) {
					$page_id = $sql->Record['page_id'];
					}
				} else {
					$errors = TRUE;
					$error_msgs[] = 'Could not retrieve page_id';
				}

				// Only proceed if we have a page_id to work with

				if ($page_id != '') {

					// Update the page title in webmatrix_user_pages
					$query = "UPDATE webmatrix_user_pages SET title = '$new_title' WHERE template = $this->subject_tpl_id AND page_id = $page_id";
					$sql->query($query, $this->debug);
					if ($sql->num_rows_affected() == 0) {
						$errors = TRUE;
						$error_msgs[] = 'Could not update the page title';
					}

					// Create URL for new page
					$new_url = $this->makeURL($new_title);

					// Update the page title in webmatrix_structure
					$query = "UPDATE webmatrix_structure SET structure_text = '$new_title', structure_url = '$new_url' WHERE page_id = $page_id";
					$sql->query($query, $this->debug);
					if ($sql->num_rows_affected() == 0) {
						$errors = TRUE;
						$error_msgs[] = 'Could not update page structure';
					}

					// Update subject page content's headline
					$date_now = date('Y-m-d H:i:s');
					$query = "UPDATE webmatrix_page_content SET text = '$new_title', modified = '$date_now' WHERE page_id = $page_id AND identifier = 'HEADLINE'";
					$sql->query($query, $this->debug);
					if ($sql->num_rows_affected() == 0) {
						$errors = TRUE;
						$error_msgs[] = 'Could not update page heading';
					}

				}

				if ($errors === FALSE) {
					return TRUE;
				} else {
					echo "<h3>Errors Found</h3>";
					var_dump($error_msgs);
					exit;
					return FALSE;
				}

			} // if old and new titles exist

		} // updatePageInfo()


		/***********************************
		*  updateMenuOrder
		*
		*  Orders subject pages alphabetically
		*
		*  @return    boolean    $errors    TRUE/FALSE
		*
		************************************/

		public function updateMenuOrder() {

			$sql = new DB_Sql();
			$sql->connect();

			$errors = FALSE;
			$subjects_abc = array();
			// Get current order (structure_id)
			$query = "SELECT structure_id FROM webmatrix_structure WHERE structure_parent = ".$this->subjects_structure_id." ORDER BY structure_text ASC";
			$sql->query($query, $this->debug);
			if ($sql->num_rows() > 0) {
				while($sql->next_record()) {
					$subjects_abc[] = $sql->Record['structure_id'];
				}
			}

			// Update menu to be ordered alphabetical
			$no_subjects = (count($subjects_abc) - 1);
			for ($i=0; $i<=$no_subjects; $i++) {
				$structure_id = $subjects_abc[$i];
				$query = "UPDATE webmatrix_structure SET structure_order = $i, structure_flag = 2 WHERE structure_id = $structure_id";
				$sql->query($query, $this->debug);
				if ($sql->num_rows_affected() == 0) {
					$errors = TRUE;
				}
			}

			return $errors;

		} // updateMenuOrder()


		/***********************************
		*  deleteOldTables
		*
		*  Deletes tblfees and tbltimetable tables if they exist
		*
		*  @return    boolean    TRUE/FALSE
		*
		************************************/

		public function deleteOldTables() {

			$sql = new DB_Sql();
			$sql->connect();
			$query = "DROP TABLE tblfees, tbltimetable";
			$sql->query($query, $this->debug);
			if ($sql->num_rows_affected() > 0) {
				return TRUE;
			} else {
				return FALSE;
			}

		} // deleteOldTables()


		/***********************************
		*  deleteInvalidCodes
		*
		*  Deletes tblfees and tbltimetable tables if they exist
		*
		*  @return    boolean    TRUE/FALSE
		*
		************************************/

		public function deleteInvalidCodes() {

			$sql = new DB_Sql();
			$sql->connect();

			$invalid_codes = array();
			foreach($this->subjects as $subject) {
				if (!in_array($subject['ID'], $this->valid_subject_codes)) {
					$invalid_codes[] = $subject['ID'];
				}
			}

			// For each invalid topic code we want to delete them
			$error = TRUE;
			foreach($invalid_codes as $code) {
				$query = "DELETE FROM tblsubject WHERE ID = '$code'";
				$sql->query($query, $this->debug);
				if ($sql->num_rows_affected() < 0) {
					$error = FALSE;
				}
			}

			return $error;

		} // deleteInvalidCodes()


		/***********************************
		*  showInvalidCodes
		*
		*  Check for invalid codes: displays message if invalid codes found
		*
		************************************/

		public function showInvalidCodes() {

			// Look for old page names
			$invalid_subjects = array();
			$i = 0;
			foreach($this->subjects as $subject) {
				if (!$subject['Valid_STC']) {
					if (($subject['No_Pages'] == 0) && ($subject['No_Courses'] == 0) && ($subject['No_News_Events'] == 0)) {
						$this->deleteSubject($subject['ID']);
					} else {
						$invalid_subjects[$i]['ID'] = $subject['ID'];
						$invalid_subjects[$i]['Description'] = $subject['Description'];
						$invalid_subjects[$i]['No_Pages'] = $subject['No_Pages'];
						$invalid_subjects[$i]['No_Courses'] = $subject['No_Courses'];
						$invalid_subjects[$i]['No_News_Events'] = $subject['No_News_Events'];
						$i++;
					}

				}
			}
			
			// Get current session for delete link
			$session = (isset($_GET['Session']) && $_GET['Session'] != '') ? $_GET['Session'] : '';

			if (count($invalid_subjects) > 0) {

				$code = (count($invalid_subjects) == 1) ? 'Code' : 'Codes';
				echo '<div class="note">';
				echo "<h3>New Subject Topic $code in use</h3>";
				echo "<p>Valid subject topic ".strtolower($code)."?</p>";
				echo "<ul>";
				foreach ($invalid_subjects as $old) {

					$stats_text = "(";
					$stats_text .= ($old['No_Pages'] != 1) ? $old['No_Pages'] . ' Pages, ' : $old['No_Pages'] . ' Page, ';
					$stats_text .= ($old['No_Courses'] != 1) ? $old['No_Courses'] . ' Courses, ' : $old['No_Courses'] . ' Course, ';
					$stats_text .= ($old['No_News_Events'] != 1) ? $old['No_News_Events'] . ' News &amp; Events)' : $old['No_News_Events'] . ' News &amp; Event)';
					echo "<li><strong>".$old['ID']." - ".$old['Description']."</strong> - $stats_text</li>";

				}
				echo "</ul>";
				echo '<p><a href="subject_editor_do.php?action=delete_invalid_codes&Session='.$session.'">Click here</a> to delete invalid subject topic '.strtolower($code).'</p>';
				// echo '<p>This process can be automatic deleting all invalid codes where no assignments exist.</p>';
				echo '<hr />';
				echo "<p><strong>Valid Subject Code List:</strong><br />";
				$valid_codes = $this->valid_subject_codes;
				sort($valid_codes);
				echo "<ul>";
				foreach ($valid_codes as $valid) {
					echo "<li>$valid</li>";
				}
				echo "</ul>";
				echo '</p>';
				echo '<p><strong>Note:</strong><br />Subject Administration assumes subject topic codes do not change often.<br />A hardcoded master list must exist to distinguish between valid and invalid codes.<br />To update this master list of valid subject topic codes - therefore updating the functionality of this page - edit the list in <strong>\'/matrix_engine/classes/SubjectAdmin.class.php\'</strong> (line 72).</p>';
				echo '</div>';
			}

		} // showInvalidCodes()


		/***********************************
		*  deleteInvalidSubjectPages
		*
		*  Deletes invalid subject pages - so they don't show up in subjects list
		*
		*  @return    boolean    TRUE/FALSE
		*
		************************************/
		public function deleteInvalidSubjectPages() {
			
			$errors = FALSE;
			$error_msg = '';

			// Get page tiles
			$page_titles = $this->getPageTitles();
			
			// Build array of valid subject names
			$this->subjects = $this->getSubjects();
			foreach ($this->subjects as $subject) {
				$subject_names[] = $subject['Description'];	
			}

			$invalid_page_names = array();

			foreach ($page_titles as $title) {
				if (!in_array($title, $subject_names)) {
					$invalid_page_names[] = $title;
				}
			}

			// If invalid subject pages found, remove them from the database
			if (count($invalid_page_names) > 0) {

				foreach ($invalid_page_names as $page_name) {
			
					// Delete page_structure	
					$sql = new DB_Sql();
					$sql->connect();

					// Delete the page's structure
					$query = "DELETE FROM webmatrix_structure WHERE structure_parent = ".$this->subjects_structure_id." AND structure_text = '$page_name'";
					$sql->query($query, $this->debug);
					if (!$sql->num_rows_affected() > 0) {
						$errors = TRUE;
						$error_msg .= 'Could not delete page structure <br />';
					}

					// Delete the page
					$query = "DELETE FROM webmatrix_user_pages WHERE template = ".$this->subject_tpl_id." AND title = '$page_name'";
					$sql->query($query, $this->debug);
					if (!$sql->num_rows_affected() > 0) {
						$errors = TRUE;
						$error_msg .= 'Could not delete page <br />';
					}

				} // foreach

				// if in DEV mode, show error messages
				if ($errors && $this->debug == TRUE) {
					echo $error_msg;
					exit;
				}

			}

			return $errors;

		} // deleteInvalidSubjectPages()


		/***********************************
		*  getNotAssignedCourses
		*
		*  Set up class property, '$this->unassigned_courses' - set to array of unassigned courses - if they exist.
		*
		************************************/
		public function getNotAssignedCourses() {

			$sql = new DB_Sql();
			$sql->connect();

			// Get Course ID and Name for display
			$query = "SELECT id, Description FROM tblunits where Subject_ID = 'N/A'";
			$sql->query($query, $this->debug);

			if ($sql->num_rows() > 0) {
				$i = 0;
				while($sql->next_record()) {
					$this->unassigned_courses[$i]['id'] = $sql->Record['id'];
					$this->unassigned_courses[$i]['Description'] = $sql->Record['Description'];
					$i++;
				}
			}
		} // getNotAssignedCourses()


		/***********************************
		*  showNotAssignedCourses
		*
		*  Returns HTML info on all courses that are currently assigned to the 'N/A' Subject Topic Code
		*  Useful to see what courses are currently being imported with STCs that aren't used anymore
		*
		*  @return    string    HTML text containing details of courses with invalid STCs
		*  @param     array     Defaults to unassigned_courses property but can accept other arrays with same format
		*
		************************************/
		public function showNotAssignedCourses($unassigned) {
			
			if (!is_array($unassigned)) {
				$unassigned = $this->unassigned_courses;
			}
			if (count($unassigned) > 0) {
				// display couses assigned to 'N/A' subject topic code
				$course_text = '';
				$course_text .= '<div class="note">';
				$course_text .= "<h3>Courses Not Assigned</h3>";
				$course_text .= "<p>The following courses have been imported with old or unknown subject topic codes.<br />They have been updated to use the 'N/A' subject topic code.</p>";
				$course_text .= '<ul>';
				foreach ($unassigned as $course) {
					$course_text .= '<li><strong>'.$course['id'].'</strong> - '.str_replace('&','&amp;',$course['Description']).' &nbsp;(<a href="/courses/course_search/course/'.$course['id'].'" target="_blank">page</a>)</li>';	
				}
				$course_text .= '</ul>';
				$course_text .= '<p>The only way these courses can be found currently is through the search function.</p>';
				$course_text .= '</div>';
			}

			return $course_text;

		} // showNotAssignedCourses()


		/***********************************
		*  sendSTCNotifEmail
		*
		*  Sends an email to ICT Support if any courses are found assigned to 'N/A'
		*  Checks database to see when last email was sent; if sent date greater than seven days ago, will send again.
		*
		*  @param    boolean    Whether method is being called from cron, if so: echo text
		*
		************************************/
		public function sendSTCNotifEmail($cron=FALSE) {
			
			if (count($this->unassigned_courses) > 0) {

				$send_email = FALSE;

				// First we need to check whether the tblunits_unassigned table has any rows in it?
				// if it doesn't that means, email notifications for unassigned courses have NOT been sent
				$sql = new DB_Sql();
				$sql->connect();

				// Check if tblunits_unassigned exists, if tblunits_unassigned does not exist, create it:
				$query = "CREATE TABLE IF NOT EXISTS tblunits_unassigned (
					  Unit_id varchar(10) NOT NULL DEFAULT '0',
					  Description varchar(200) NOT NULL,
					  email_sent_date datetime DEFAULT NULL,
					  email_sent_address varchar(255) DEFAULT NULL,
					  PRIMARY KEY (Unit_id),
					  KEY FK_Unit_id (Unit_id)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Stores info about courses with invalid STCs'";
				$sql->query($query, $this->debug);
				
				$query = "SELECT * FROM tblunits_unassigned";
				$sql->query($query, $this->debug);

				if ($sql->num_rows() < 1) {
					$send_email = TRUE;
				}

				$i = 0; // counter
				// Update the subject_admin database with course details for each unassigned course
				foreach($this->unassigned_courses as $course) {

					// Check to see if this course exists
					$query = "SELECT Unit_id, Description, UNIX_TIMESTAMP(email_sent_date) AS email_sent_date FROM tblunits_unassigned WHERE Unit_id = '".$course['id']."'";
					$sql->query($query, $this->debug);
					
					// If this course not found in tblunits_unassigned table: add it
					if ($sql->num_rows() < 1) {

						// Insert course into the database
						$query = "INSERT INTO tblunits_unassigned (Unit_id,Description) VALUES ('".$course['id']."','".$course['Description']."')";
						$sql->query($query, $this->debug);
						// if query did not update
						if ($sql->num_rows_affected() < 1) {
							$this->errors[] = 'Could not update tblunits_unassigned table';
						}

					} else {
						// Course was found in tblunits_unassigned table, set date this course notified ICT in the unassigned courses property
						while($sql->next_record()) {
							$this->unassigned_courses[$i]['email_sent_date'] = $sql->Record['email_sent_date'];
						}
					}

					$i++;
				} // foreach
				
				// how many unassigned courses?
				$unassigned_count = count($this->unassigned_courses);
				$course_txt = ($unassigned_count > 1) ? 'courses' : 'course';

				foreach($this->unassigned_courses as $course) {

					// If Email sent date is in database, perform an 'email last sent' check
					if ((isset($course['email_sent_date']) && $course['email_sent_date'] != '')) {

						echo 'getting here';
						$date_now = time(); // Today's Date as a Unix Timestamp
						$date_emailed = $course['email_sent_date'];
						$seconds_since = $date_now - $date_emailed; // seconds since email
						$days_since = round($seconds_since / 86400); // convert to days

						// If any courses have sent email 7 (or more) days ago, we want to send another email to ICT
						if ($days_since >= 7) {
							$send_email = TRUE;
						}

					}

				} // foreach

				// If It has been more than 7 days since last email or email never sent: send email
				if ($send_email == TRUE) {

					/* Random opening to add some "funny" to the email
					$random_opening = array(
						'Your hair looks GREAT today! I just wanted you to know, also', 'So like I straight up can\'t remember my password can you like maybe reset it or sumthing... LOL, kidding: I\'m a robot, I forgot nothing! You must get that password question a lot though.', 'You really seem to be a natural at this.', 'Gosh, how about that weather today.', 'What is love, oh baby, don\'t hurt me, don\'t hurt me no more - Haddaway, but seriously, can you teach me to love? Robbin Williams did it in that movie innit.', 'It can only be attributable to human error.');
					$random_num = rand(0,(count($random_opening) -1)); */

					// Create body email text
					$body_html = '<p>Hello ICT Services Support,</p>';
					// $body_html .= "<p>".$random_opening[$random_num]." I have a notification for you.</p>"; // random opener text
					$body_html .= 'I have detected '.$unassigned_count.' unassigned '.$course_txt.' on the Conel website.</p>
						<p>The staff member in charge of managing course data should be notified and update the following ' .$unassigned_count. ' courses to use <em>valid</em> subject topic codes.</p>';
					$body_html .= $this->showNotAssignedCourses($this->unassigned_courses);
					$body_html .= '<p>If these '.$course_txt.' are not updated within seven days, you will receive another notification from Yours Truly.</p><p>Thank You Human,<br />Conel Website Notifications Bot</p>';

					// Instantiate mail class
					$mail = new phpmailer();
					$mail->IsHTML(TRUE); // send HTML email
					$mail->IsSMTP(); // use SMTP to send
					// Set Recipient
					$mail->AddAddress($this->notification_email,'ICT Services Support');
					//$mail->AddAddress('nkowald@staff.conel.ac.uk','ICT Services Support');
					$course_txt_ucfirst = ucfirst($course_txt);
					$mail->Subject = "Unassigned $course_txt_ucfirst Found";
					$mail->From = 'webmaster@staff.conel.ac.uk';
					$mail->FromName = 'Conel Website Notifications';
					$mail->Body = $body_html;
					$result = $mail->Send(); // send email notification!

					// If mail sent successfully, update database with 'email sent date and sent to address' details
					if ($result === TRUE) {
						
						if ($cron == TRUE) {
							echo "Email notification sent successfully to ".$this->notification_email."\n";
						}

						// Create date - eg. "2009-06-21 14:34:04": MySQL timestamp format
						$date_now = date('Y-m-d H:i:s');

						foreach($this->unassigned_courses as $course) {
							$query = "UPDATE tblunits_unassigned SET email_sent_date='$date_now', email_sent_address='$this->notification_email' WHERE Unit_id = '".$course['id']."'";
							$sql->query($query, $this->debug);
							if ($sql->num_rows_affected() < 1) {
								$this->errors[] = 'Could not update tblunits_unassigned with email sent dates';
							}
						} // foreach unassigned course
					}

				} // If more than 7 days since last email

			} else {
				// No unassigned courses found - it is safe to empty the tblunits_unassigned table
				if ($cron == TRUE) {
					echo "No unassigned courses found\n";
				}

				// No unassigned courses found, so flush the table
				$sql = new DB_Sql();
				$sql->connect();
				$query = "DELETE FROM tblunits_unassigned";
				$sql->query($query, $this->debug);
				
				if ($sql->num_rows_affected() > 0 && $cron == TRUE) {
					echo "Old unassigned courses removed from table\n";
				}
			}
			
			if ($cron == TRUE) {
				echo "Cron ran successfully\n";
			}

		} // sendSTCNotifEmail


		public function __destruct() {

		}

	} // SubjectAdmin Class

?>
