<?php

// Define a constant for path to the.htaccess file
// The .htaccess file resides in the root of the website
define('HTACCESS_FILE','../.htaccess');

// Function only builds 301 redirects at the moment, best for SEO
// Can easily add to this in the future if needed
function redirect_buildRedirect($current_page, $redirect_url) {
	$redirect_rule = "\tRewriteRule ^$current_page\$ $redirect_url [R=301,L]\n";
	return $redirect_rule;
}

// Returns an array containing current 301 redirects in the .htaccess file
function redirect_getCurrentRedirects() {

	// This script reads the .htacess file in the root directory, finds all redirects and allows editing.
	$file = HTACCESS_FILE;
	$fh = fopen($file, 'r');
	$contents = fread($fh, filesize($file));
	fclose($fh);

	// Find all 301 Redirects
	$pattern = '/RewriteRule+.+\[R=301,L\]+/';
	preg_match_all($pattern,$contents,$matches);
	
	$rewrite_content = '';
	$num_matches = count($matches[0]);
	$i = 0;
	foreach($matches[0] as $rule) {
		$rewrite_content .= "\t$rule\n";
	}

	// Add both content and matches into an associative array
	$redirect_details = array();
	$redirect_details['matches'] = $matches[0];
	$redirect_details['content'] = $rewrite_content;
	
	return $redirect_details;
}

// Checks if passed $redirect string exists in the redirects array
function redirect_checkIfExists($redirect) {
	
	$current_redirects = redirect_getCurrentRedirects();
	$current_redirects = $current_redirects['content']; // We only want array
	
	if (strpos($current_redirects,$redirect)) {
		return TRUE;
	} else {
		return FALSE;
	}
	
}

// Checks if passed $redirect string exists in the redirects array
function redirect_getExistingRedirect($redirect) {
	
	$current_redirects = redirect_getCurrentRedirects();
	$current_redirects = $current_redirects['content']; // We only want array
	
	if (strpos($current_redirects,$redirect)) {
		// we want to return the redirect url if found
		$pos = strpos($current_redirects,$redirect);
		$redir_string = substr($current_redirects,$pos,strlen($current_redirects));
		// we need to cut this down to only return the one entry
		$redir_string = substr($redir_string,0,strpos($redir_string,'['));
		// Build regex
		$pattern = '/\${1}\s{1}(.+)\s{1}/';
		preg_match_all($pattern,$redir_string,$matches);	
		// Match should always be:
		$redirect_url = $matches[1][0];
		
		return $redirect_url;
	} else {
		return '';
	}
}

// Adds passed $redirect string into the .htaccess file
function redirect_addRedirect($redirect, $page_id, $redirect_url) {
	
	if ($redirect != '') {
		// If redirect does not exist already, add it
		// We only want to check for the page part of the redirect
		$this_page_url = structure_getPathURL($page_id);
		$this_page_url = "^$this_page_url$";
		
		$exists = redirect_checkIfExists($this_page_url);
		
		// If redirect does not exist, add it!
		if (!$exists) {
			// Get redirect content
			$redirects_array = redirect_getCurrentRedirects();
			$old_redirect_content = $redirects_array['content'];
			
$redirect_header = "<IfModule mod_rewrite.c>
\tRewriteEngine On
\tRewriteBase / 
\n";
			
			// Add new redirect to redirect content
			$new_redirect_content = $old_redirect_content . $redirect;
			
$redirect_footer = "
\tRewriteRule ^dynamic(.*)$ dynamic.php?url=$1 [QSA,L]
\tRewriteCond %{REQUEST_FILENAME} !-f
\tRewriteCond %{REQUEST_FILENAME} !-d
\tRewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

</IfModule>";
			
			$file = HTACCESS_FILE;
			$fh = fopen($file, 'r');
			$contents = fread($fh, filesize($file));
			fclose($fh);
			
			// Now replace the old redirects content with the new redirects content
			$new_redirect_code = $redirect_header . $new_redirect_content . $redirect_footer;
			
			if (is_writable($file)) {
				// We need to delete everything from .htaccess and write new content into it
				$fh = fopen($file, 'w');
				fwrite($fh, $new_redirect_code);
				fclose($fh);
				echo "<p class=\"notification\">Redirect created successfully</p>";
			} else {
				echo "<p class=\"notification\">'$file' is not writable, cannot create redirect</p>";
			}
			
		} else {
			// A rule for this page exists so now check if the redirect url is the same, if it is - we want to delete it and add this new one.
			// Get page url from page id
			$page_url = structure_getPathURL($page_id);
			
			// We only want to check for the page part of the redirect
			$this_page_url = "^$page_url$";
			$redirect_found = redirect_getExistingRedirect($this_page_url);
			// If redirect exists
			if ($redirect_found != '') {
				// Check if found redirect is the same as the one entered by user in form submission
				if ($redirect_found == $redirect_url) {
					// Redirect is the same, do nothing
					echo '<p class="notification">Redirect is the same, nothing updated</p>';
				} else {
					// Redirect is different, so we need to delete current redirect and add this new one.
					$file = HTACCESS_FILE;
					$fh = fopen($file, 'r');
					$contents = fread($fh, filesize($file));
					fclose($fh);
					
					// First if the user is deleting the redirect ($redirect_url is blank) then just delete the existing redirect
					if ($redirect_url != '') {
					
						// Now we need to build the redirect string that we want to delete from the .htaccess file
						$old_redirect_string = redirect_buildRedirect($page_url, $redirect_found);
						$new_redirect_string = redirect_buildRedirect($page_url, $redirect_url);
						
						// Now replace it in the .htaccess file
						$new_redirect_code = str_replace($old_redirect_string,$new_redirect_string,$contents);
						
						if (is_writable($file)) {
							// We need to delete everything from .htaccess and write new content into it
							$file = HTACCESS_FILE;
							$fh = fopen($file, 'w');
							fwrite($fh, $new_redirect_code);
							fclose($fh);
							echo "<p class=\"notification\">Redirect changed successfully</p>";
						} else {
							echo "<p class=\"notification\">'$file' is not writable, cannot create redirect</p>";
						}
					
					} else {
						// Just delete redirect
						$old_redirect_string = redirect_buildRedirect($page_url, $redirect_found);
						// Now delete it from the .htaccess file
						$new_redirect_code = str_replace($old_redirect_string,'',$contents);
						
						if (is_writable($file)) {
							// We need to delete everything from .htaccess and write new content into it
							$file = HTACCESS_FILE;
							$fh = fopen($file, 'w');
							fwrite($fh, $new_redirect_code);
							fclose($fh);
							echo "<p class=\"notification\">Redirect deleted</p>";
						} else {
							echo "<p class=\"notification\">'$file' is not writable, cannot delete redirect</p>";
						}
						
					}

				}
			}
			
		}
	} else {
		echo '<p class="notification">Redirect not provided, cannot update</p>';
	}
	
}


## =======================================================================        
##  redirect_displayForm     
## ======================================================================= 
function redirect_displayForm($page_id) {

	global $gSession,$Auth;
		
	// Get page url from page id
	$page_url = structure_getPathURL($page_id);
	
	// We only want to check for the page part of the redirect
	$this_page_url = "^$page_url$";
	$redirect_found = redirect_getExistingRedirect($this_page_url);
	
	## prepare the output
	$select_template = new Template(INTERFACE_DIR);
	$select_template->set_templatefile(array("header" => "redirect_page.tpl","body" => "redirect_page.tpl","footer" => "redirect_page.tpl"));

	## display the form fields
	// We need to add a random integer to the end of the url so users don't see cached redirects
	$rand_num = rand(0,12345);
	$output = "
	<p><strong>This Page:</strong> <a href=\"/$page_url?no_cache=$rand_num\" target=\"_blank\">/$page_url</a></p>";
	$output .= 
	'<p><strong>Redirection URL:</strong>
	<input type="text" name="redirect_url" size="40" value="'.$redirect_found.'" style="width:100%;" /><br />
	<em>Enter a relative URL: starting from the root e.g. /employers/success_stories</p>';
	
	$select_template->set_var('value',$output);
	$select_template->set_var("saveIMG","lang/".$Auth->auth["language"]."_button_save.gif");
	
	## prepare the action
	$actionURL = "admin.php";
	$actionURL = $gSession->url($actionURL);
	$select_template->set_var('actionURL',$actionURL);		
	
	## here we initialize the hiddenfields	
	$output =  '<input type="hidden" name="op" value="redirect_page_update">';
	$output .= '<input type="hidden" name="page_id" value="'.$page_id.'">';
	$output .= '<input type="hidden" name="Session" value="'.$gSession->id.'">';
	$redir_exists = ($redirect_found != '') ? 'y' : 'n';
	$output .= '<input type="hidden" name="redir_exists" value="'.$redir_exists.'">';
	$select_template->set_var("hiddenfields",$output);

	$select_template->pfill_block("header");
	$select_template->pfill_block("body");
	## here we get all the subpages
	$select_template->pfill_block("footer");
}

// Gets called on form POST
function redirect_updateRedirect($post_vars) {

	$page_id = $_POST['page_id'];
	$redirect_url = $_POST['redirect_url'];
	
	// Get page url from page id
	$page_url = structure_getPathURL($page_id);
	
	$redirect = redirect_buildRedirect($page_url,$redirect_url);
	redirect_addRedirect($redirect, $page_id, $redirect_url);
}

?>