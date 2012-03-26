<?php
## =======================================================================        
##  config.php        
## =======================================================================        
##  used to define all vars that need to be set when we install this onto
##  a new server- later we will search an replace the vbars to protect 
##  the code
##
##  TODO:   
##     - check if it works    
## =======================================================================

##ini_set("error_reporting",E_ALL & ~E_NOTICE);
########################################################
## Developement Settings
########################################################
define("DEVELOPMENT",true);		

########################################################
## Multi-Client Settings
########################################################
define("SITE_OWNER",1);		
## all tables should get a new field that contains the
## owner. Using this filed, we can use multiple websites
## using the same table and core engine

define("SITE_ROOT","http://meetingplanner/");	
define("SITE_URL","http://meetingplanner");
define("SITE",SITE_ROOT."matrix_engine/");	
	
########################################################
## path related stuff
########################################################
define("MATRIX_BASEDIR","/Users/stefan/Sites/meetingplanner/");
define("MATRIX_CLIENTDIR","/Users/stefan/Sites/meetingplanner/");


define("UPLOAD_DIR","images/");
define("ABSOLUTE_UPLOAD_DIR",SITE_ROOT."images/");
define("UPLOAD_DIR_DOCS","docs/");
define("ENGINE",MATRIX_BASEDIR."matrix_engine/");
########################################################
## get the global settings file
########################################################
require(ENGINE."settings.php");

########################################################
## turn this option on to activate the path based targeting
########################################################
define("URL_REWRITE",false);

########################################################
## activated sort options
########################################################
global $_PAGE_SORTOPITIONS;
$_PAGE_SORTOPITIONS = array('default','bylastname','byname','bydate');

########################################################
## mail
########################################################
define("MAIL_FROMMAIL","webmaster@workmatrix.de");
define("MAIL_FROM","webmaster@workmatrix.de");

########################################################
## additional modules settings
########################################################
define("MODULE_STATS","OFF");

##$installed_modules = array("forms","approval","forum","donations","dbobject","orders","newsletter","glossary","clients","statistik","backup","cache","info");
$installed_modules = array("clients","templates","newsletter","glossary","statistik","backup","cache","seo");

?>
