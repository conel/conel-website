<?php
## =======================================================================        
##  config.php        
## =======================================================================        
##  this is the main config file, it loads the genral settings
##  and includes the settings file which contains the user
##  specified settings
## =======================================================================
## Cache-Settings
define("MODULE_CACHE_PATH",ENGINE."modules/cache/cache.php");  ## dir to the module
define("MODULE_CACHE_DIR",MATRIX_BASEDIR."cache");    ## the directory wher we store the pages
define("CACHE_SEND304",FALSE);           ## turn this off when there are problems
define("CACHE_GC",1); 	                 ## Probability of garbage collection (i.e: 1%)
define("CACHE_TIME",10); 	             ## number seconds to cache
define("CACHE_DEBUG",0); 	             ## 0 - Turn debugging on/off

define(CACHE_GC, 1);        ## Default: 1 - Probability of garbage collection (i.e: 1%)
define('CACHE_VERSION', "1.0");     ## Version of cache
   
## finally we include the use settings file
global $cache_config;
$cache_config = array();
require(MATRIX_BASEDIR."matrix_engine/modules/cache/settings.php");
?>