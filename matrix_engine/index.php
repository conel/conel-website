<?PHP

#################################
#     Required Include Files    #
#################################
	require("config.php");             

	# call all session related objects
	require(CLASSES_DIR."container.php");
	require(CLASSES_DIR."session.php");
	require(CLASSES_DIR."authentication.php");
	require(CLASSES_DIR."page.php");
  
	## include the template class
	require(CLASSES_DIR."template.php");
  
	## include the db class
	require(CLASSES_DIR."db_mysql.php");
	require(CLASSES_DIR."class_mailer.php");	

	if(REWRITE_GLOBALS == "ON") {
		include("functions/register_globals.php");
	}

	## let's start the session handling
	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	include("interface/lang/".$Auth->auth["language"].".php");

  	$menuURL = 'matrix_menu.php';          
	$menuURL = $gSession->url($menuURL);

  	$modeURL = 'matrix_folder_menu.php';          
	$modeURL = $gSession->url($modeURL);
	
	// nkowald - 2010-11-01 - CSV exporters should ONLY be allowed to export csvs
	if (isset($_SESSION['wm']['username']) && $_SESSION['wm']['username'] == 'csvexport') {
		$editorURL = 'export_csv.php';          
		$editorURL = $gSession->url($editorURL);
	} else {
		$editorURL = 'page_editor.php';          
		$editorURL = $gSession->url($editorURL);	
	}


	print '<html>';
	print '<head>';
	print '<title>webmatrix</title>';
	print '</head>';
	##print '<frameset frameborder="0" framespacing="0" border="1" rows="55,*">';
  	##print '<frame marginwidth="0" marginheight="0" src="interface/head.html" name="heading" scrolling="no" noresize frameborder="0">';
  	print '  <frameset frameborder="0" framespacing="0" border="0" rows="74,*">';
    print '  <frame marginwidth="0" marginheight="0" src="'.$modeURL.'" name="foldermenu" scrolling="no" noresize frameborder="0">';
    print '  <frame marginwidth="0" marginheight="0" src="'.$editorURL.'" name="editor" scrolling="yes" noresize frameborder="1">';    
   	##5print '</frameset> ';
	print '</frameset>';
	print '</html>';
	
?>
