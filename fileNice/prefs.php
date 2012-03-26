<?php

	// default skin 
	$defaultSkin = "default";

	//default sort
	$defaultSort = "date";
	// default sort order
	$defSortDirection = "descending"; 

	// default time to show each image in a slideshow (in seconds)
	$defaultSSSpeed = 3;

	// Show "send to Flickr" links
	$flickr = false;

	// any files you don't want visible to the file browser add into this 
	// array...
	$ignoreFiles = array(	"index.php",
							"fComments.txt",
							"content.php",
							"empty.html",
							"file_browser.php",
							"mail.php",
							"preview.php",
							"user_modules-20090501.php",
							"user_modules-20090527.php",
							"user_modules-20090609.php",
							"user_modules.dist.php",
							"user_modules.php",
							"webmatrix.html",
							"rss.php",
							"Popup.zip",
							"unblock.php"
							);
							
	// any folders you don't want visible to the file browser add into this 
	// array...						
	$ignoreFolders = array("fileNice",
							"cache",
							"cgi-bin",
							"extensions",
							"layout",
							"logs",
							"matrix_engine",
							"nbproject",
							"phpmyadmin",
							"pop-up",
							"settings",
							"Backup"
							);

	// file type handling, add file extensions to these array to have the 
	// file types handled in certain ways
	$imgTypes 	= array("gif","jpg","jpeg","bmp","png");
	$embedTypes = array("mp3","mov","aif","aiff","wav","swf","mpg","avi","mpeg","mid","wmv","flv");
	$htmlTypes 	= array("html","htm","txt","css","siblt");
	$phpTypes 	= array("php","php3","php4","asp","js");
	$miscTypes 	= array("pdf","doc","zip","sit","rar","rm","ram","ibl","siblt","gz");

	// date format - see http://php.net/date for details
	$dateFormat = "F d Y ";

?>
