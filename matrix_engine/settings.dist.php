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

########################################################
## version
########################################################
define("MATRIX_VERSION","3.0.1");
define("MATRIX__LIZENZ","elsner.elsner");

########################################################
## URL related stuff
########################################################
define("CONTENT_PAGE","content.php");
define("CONTENT_PAGE_REWRITE","content");

########################################################
## path related stuff
########################################################
define("MATRIX_CACHEDIR",MATRIX_BASEDIR."cache");
define("MATRIX_UPLOADDIR",MATRIX_BASEDIR."images/");
define("MATRIX_UPLOADDIR_DOCS",MATRIX_BASEDIR."docs/");

define("INTERFACE_DIR",ENGINE."interface/");

define("CLASSES_DIR","classes/");
define("HTML_DIR",MATRIX_CLIENTDIR."layout/");
define("CACHE_DIR","cache/");
define("IMAGE_CACHE","cache/");


########################################################
## database
########################################################
define("DB_HOST","localhost");
define("DB_DATABASE","meetingplanner");
define("DB_USER","root");
define("DB_PASSWORD","trui32hn");

########################################################
## Defaults
########################################################
define("DEFAULT_LANGUAGE","deutsch");
define("DEFAULT_DATE","d.m.Y");
define("DEFAULT_TIME","H:i");

define("SESSION_LIFETIME",20);  ## minutes

########################################################
## language: edit
########################################################
define("DEFAULT_INPUTLANGUAGE",0);


########################################################
## mail
########################################################
define("MAIL_METHOD","mail");
define("MAIL_HOST","localhost");

## database settings
define("DB_PREFIX","webmatrix_");		// needed for extras
## tables
define("PAGE_CONTENT",DB_PREFIX."page_content");
define("PAGE_IMAGE",DB_PREFIX."image");
define("PAGE_LINK",DB_PREFIX."page_link");
define("PAGE_FILE",DB_PREFIX."page_file");
define("PAGE_DATE",DB_PREFIX."page_date");
define("PAGE_TEMPLATE",DB_PREFIX."page_template");

define("USER_PAGES",DB_PREFIX."user_pages");
define("USERS",DB_PREFIX."users");
define("GROUPS",DB_PREFIX."groups");
define("ACCESS",DB_PREFIX."accessrights");
define("STRUCTURE",DB_PREFIX."structure");
define("LINKLIST",DB_PREFIX."link_list");
define("LINKLISTITEM",DB_PREFIX."link_list_item");
define("LANGUAGE",DB_PREFIX."language");
define("CLIENTS",DB_PREFIX."clients");
define("LOCKS",DB_PREFIX."locks");
define("VERSIONS",DB_PREFIX."page_repository");
define("ACTIVE_SESSIONS","active_sessions");

## settings to improve compatibility
define("REWRITE_GLOBALS","ON");

?>