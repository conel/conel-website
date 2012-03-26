<?PHP

#################################
#     Required Include Files    #
#################################
	require("../config.php");             


	## let's start the session handling
	page_open(array("session" => "session_object", "authenticate" => "Auth"));
	page_close();

	include("../../interface/lang/".$Auth->auth["language"].".php");

  	$menuURL = 'menu.php';          
	$menuURL = $gSession->url($menuURL);

  	
  	$templateURL = 'user.php';          
	$templateURL = $gSession->url($templateURL);	
	

	print '<html>';
	print '<head>';
	print '<title>webmatrix</title>';
	print '</head>';
  	print '<frameset frameborder="1" framespacing="0" border="1" cols="180,*">';
   	print '   <frame marginwidth="0" marginheight="0" src="'.$menuURL.'" name="code" scrolling="no" frameborder="1">';
   	print ' <frame marginwidth="0" marginheight="0" src="'.$templateURL.'" name="text">';
  	print '</frameset>';
  	print '</html>';
	
?>
