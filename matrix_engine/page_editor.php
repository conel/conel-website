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
  
	## include the db class
	require(CLASSES_DIR."db_mysql.php");
	
	## include the template class
	require(CLASSES_DIR."template.php");	
	require(CLASSES_DIR."class_mailer.php");

	## let's start the session handling
	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	include("interface/lang/".$Auth->auth["language"].".php");

  	$menuURL = 'matrix_menu.php';          
	$menuURL = $gSession->url($menuURL);

  	$modeURL = 'matrix_folder_menu.php';          
	$modeURL = $gSession->url($modeURL);
	
  	$editorURL = 'page_edit.php';          
	$editorURL = $gSession->url($editorURL);
	
  	$mainURL = 'main.php';          
	$mainURL = $gSession->url($mainURL);		

	print '<html>';
	print '<head>';
	print '<title>webmatrix</title>';
	print '</head>';
  	print '<frameset frameborder="1" framespacing="0" border="1" cols="240,*" bordercolor="#000000">';
   	print '   <frame marginwidth="0" marginheight="0" src="'.$menuURL.'" name="code" scrolling="auto" frameborder="1">';
   	print ' <frame marginwidth="0" marginheight="0" src="'.$mainURL.'" name="text" frameborder="0">';
  	print '</frameset>';
  	print '</html>';
	
?>
