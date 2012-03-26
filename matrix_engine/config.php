<?php
## =======================================================================        
##  config.php        
## =======================================================================        
## ======================================================================

error_reporting(0);
########################################################
## Developement Settings
########################################################

// nkowald - 2010-01-14 - Check if we got to the conel site from the enfield website, set up FROM_ENFIELD constant holding result
if (isset($_GET['from']) && $_GET['from'] == 'enfield') {
	define('FROM_ENFIELD',TRUE);
} else {
	define('FROM_ENFIELD',FALSE);
}

if (FROM_ENFIELD) {
	define("DEVELOPMENT", TRUE);
} else {
	define("DEVELOPMENT", FALSE);
}
//define("DEVELOPMENT",TRUE);

// nkowald - 2011-07-29 - Required variable for TubePress
$tubepress_base_url = 'http://www.conel.ac.uk/tubepress';

########################################################
## Multi-Client Settings
########################################################
define("SITE_OWNER",1);		

## the domain settings

define("SITE_ROOT","http://www.conel.ac.uk/");	
define("SITE_URL","http://www.conel.ac.uk");
define("SSL_SITE_URL","http://www.conel.ac.uk");
/*
define("SITE_ROOT","http://172.20.10.16/");	
define("SITE_URL","http://172.20.10.16");
define("SSL_SITE_URL","http://172.20.10.16");
*/
define("SITE",SITE_ROOT."matrix_engine/");

########################################################
## path related stuff
########################################################
define("MATRIX_BASEDIR","C:\\Program Files\\Apache Software Foundation\\Apache2.2\\htdocs\\");
define("MATRIX_CLIENTDIR","C:\\Program Files\\Apache Software Foundation\\Apache2.2\\htdocs\\");


define("UPLOAD_DIR","images/");
define("ABSOLUTE_UPLOAD_DIR",SITE_ROOT."images/");
define("UPLOAD_DIR_DOCS","docs/");
define("ENGINE",MATRIX_BASEDIR."matrix_engine\\");
########################################################
## get the global settings file
########################################################
require(ENGINE."settings.php");

########################################################
## turn this option on to activate the path based targeting
########################################################
define("URL_REWRITE",true);


########################################################
## eventmanager
########################################################
define("EVENTMANAGER_TRIGGER",'index'); ## defines which component handles the calls


########################################################
## activated sort options
########################################################
global $_PAGE_SORTOPITIONS;
$_PAGE_SORTOPITIONS = array('default','byname','bydate');

########################################################
## mail
########################################################
define("MAIL_FROMMAIL","webmaster@staff.conel.ac.uk");
define("MAIL_FROM","webmaster@staff.conel.ac.uk");

########################################################
## additional modules settings
########################################################
define("MODULE_STATS","OFF");

##$installed_modules = array("forms","approval","forum","donations","dbobject","orders","newsletter","glossary","clients","statistik","backup","cache","info");
##$installed_modules = array("members","companies","publish","search","clients","templates","newsletter","glossary","reports","backup","cache","seo");
$installed_modules = array("clients","templates","newsletter");

##$installed_modules = array("statistik");

?>
