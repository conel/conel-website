<?php
	## =======================================================================
	## module_GetFirstPage												
	## =======================================================================
	## gets the first subpage and displays it
	## =======================================================================	
	function module_handleSubscription($id,$params) {

		// init the vars
		$email_val = 'Your email';
		$rerror = '';
		if ($params['parameter'] == 1 || $params['parameter'] == 3) {
			$error = 'Stay on top &mdash; get our Newsletter';
		} else {
			$error = '';
		}
		$output = '';
		// check if the user submitted the form		
		if($_POST['email']) {
			// user submitted the from- ctry to validate the email address
			require_once(ENGINE.'classes/class_validate.php');
			$validation_status = Validate::email($_POST['email']);
			if($validation_status != VALIDATE_VALID) {
				// we need to output an error messagew and re-display the form
				$rerror = '<div class="mb-17">
								<div class="error clearfix">
									<p>Enter a valid email address.</p>
								</div>
								<div class="hr_error"><hr /></div>
								<img src="/layout/img/esclamation.gif" alt=" " class="error" />
							</div>';
				$email_val = $_POST['email'];
			} else {
				// we have an email- so we need to insert it into the clients db
				$email = mysql_real_escape_string($_POST['email']);
				$newsletter = mysql_real_escape_string($_POST['newsletter']);
				
				$db = new DB_Sql();
				$query = "SELECT id FROM ".DB_PREFIX."clients WHERE email='".$_POST['email']."'";
				$result_pointer = $db->query($query);	
				
				if($db->num_rows() == 0) {
					// insert the base entry
					$query = "INSERT INTO ".DB_PREFIX."clients (groupid,entered) values ('1',now())";
					$result_pointer = $db->query($query);
					$object_id = $db->db_insertid($result_pointer);		
				
					// now insert the additional data
					$query = "UPDATE ".DB_PREFIX."clients SET email='".$_POST['email']."', newsletter='".$_POST['newsletter']."' WHERE id='$object_id'";
					$result_pointer = $db->query($query);
				}
				
				// we are done inserting or ignoring it- now display the success panel
				$output = '<div class="mb-17">
								<div class="success clearfix">
									<p>You\'ve been subscribed.</p>
								</div>
								<div class="hr_success"><hr /></div>
								<img src="/layout/img/success.gif" alt=" " class="error" />
							</div>';
				//$output = '<p id="success"><span>You\'ve been subscribed to our newsletter!</span><br /></p>';				
				return array('SUBSCRIBE'=>$output);
			}
		}
		
		if ($params['parameter'] == 1) {
		
			// if we are still here- we will simply display the panel
			
			
			if (!empty($rerror)) {
				$output .= $rerror;
				$output .= '<form class="ml28" method="post" action="'.getTargetURL($params['page_id']).'">';
			} else {
				$output .= '<form class="ml28" method="post" action="'.getTargetURL($params['page_id']).'">';
				$output .= '<label for="newsletter_w170">'.$error.'</label><br />';
			}
			
				$output .=	'<select name="newsletter" id="newsletter_w170" class="mb10 w170"><option value="all">All newsletters</option><option value="learners">Learners newsletter</option><option value="employers">Employers newsletter</option><option value="staff">Staff newsletter</option></select>
							<div class="clearfix">
								<span class="fl mr10 db"><input type="text" name="email" id="email" value="'.$email_val.'" class="w165" /></span>
								<input type="image" src="/layout/img/go.gif" alt="Search" class="fl"/>
							</div>
						</form>
						<div class="hrcolhp"><hr /></div>';
		} else if ($params['parameter'] == 2) {
			// other output
			$thepage = _searchFindPage($params['page_id']);
			
			if (!empty($rerror)) {
				$output .= $rerror;
			}
			$output .= '<form method="post" action="'.getTargetURL($thepage['page_id']).'">';
			$output .= '<select name="newsletter" class="ml10 w195 mb10"><option value="all">All newsletters</option><option value="learners">Learners newsletter</option><option value="employers">Employers newsletter</option><option value="staff">Staff newsletter</option></select>
							<input type="text" name="email" id="email" value="'.$email_val.'" class="w190 ml10" /><br />
							<input type="image" src="/layout/img/send.gif" alt="Send" class="ml153 mt5 mb10" />
						</form>
						<div class="box_bottom"><hr /></div>';

		} else {
			if (!empty($rerror)) {
				$output .= $rerror;
				$output .= '<form method="post" action="'.getTargetURL($params['page_id']).'">';
			} else {
				$output .= '<form method="post" action="'.getTargetURL($params['page_id']).'">';
				$output .= '<label for="newsletter_w230">'.$error.'</label><br />';
			}
			
				$output .=	'<select name="newsletter" id="newsletter_w230" class="mb10 w230"><option value="all">All newsletters</option><option value="learners">Learners newsletter</option><option value="employers">Employers newsletter</option><option value="staff">Staff newsletter</option></select>
							<div class="clearfix">
								<input type="text" name="email" id="email" value="'.$email_val.'" class="fl mr10 w230" />
								<input type="image" src="/layout/img/go.gif" alt="Search" class="fl" />
							</div>
						</form>';
	
		
		}
		return array('SUBSCRIBE'=>$output);
	
	}	
	
	
	## =======================================================================
	## module_GetFirstPage												
	## =======================================================================
	## gets the first subpage and displays it
	## =======================================================================	
	function module_unsubscribe($id,$params) {
		
		// init the vars
		$error = '';
		
		// check if the user submitted the form		
		if(isset($_GET['email'])) {
		
			// user submitted the from- ctry to validate the email address
			require_once(ENGINE.'classes/class_validate.php');
			$validation_status = Validate::email($_GET['email']);
			if($validation_status == VALIDATE_VALID) {
				// we have an email- so we need to insert it into the clients db
				$email = mysql_real_escape_string($_GET['email']);
				$db = new DB_Sql();
			
				// first check if we already have th amil address
				$query = "DELETE FROM ".DB_PREFIX."clients WHERE email='".$email."' LIMIT 1";
				$result_pointer = $db->query($query);	

			}
		}
	
	}	

?>