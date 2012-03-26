<?PHP

#################################
#     Required Include Files    #
#################################
	require("../../config.php");             
	if(REWRITE_GLOBALS == "ON") {
		include("../../functions/register_globals.php");
	}
	# call all session related objects
	require("../../".CLASSES_DIR."container.php");
	require("../../".CLASSES_DIR."session.php");
	require("../../".CLASSES_DIR."authentication.php");
	require("../../".CLASSES_DIR."page.php");
	require("../../".CLASSES_DIR."class_mailer.php");
  
	## include the db class
	require("../../".CLASSES_DIR."db_mysql.php");
	
	## include the template class
	require("../../".CLASSES_DIR."template.php");	

	## let's start the session handling
	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	include("../../interface/lang/".$Auth->auth["language"].".php");

  	$menuURL = 'menu.php';          
	$menuURL = $gSession->url($menuURL);
  	
  	$templateURL = 'newsletter.php';          
	$templateURL = $gSession->url($templateURL);	
	

	print '<html>';
	print '<head>';
	print '<title>webmatrix</title>';
	print '</head>';
  	print '<frameset frameborder="1" framespacing="0" border="1" cols="230,*">';
   	print '   <frame marginwidth="0" marginheight="0" src="'.$menuURL.'" name="code" scrolling="yes" frameborder="1">';
   	print ' <frame marginwidth="0" marginheight="0" src="'.$templateURL.'" name="text" frameborder="0">';
  	print '</frameset>';
  	print '</html>';


?>
