<?
/*
  cache.php v1.0

  This is the first version of the caching routine. We should try to expand
  the time for cached pages via integration into webmatrix
*/

## let's include the config file
include_once(ENGINE."modules/cache/config.php");

/******************************************************************************/
    $CACHE_TIME= isset($cache_config["CACHE_TIME"]) ? $cache_config["CACHE_TIME"] : 900;            // Default: 900 - number seconds to cache
    $CACHE_DEBUG=CACHE_DEBUG;             // Default: 0 - Turn debugging on/off
    $SINGLE_SITE=1;             // Default: 1 - No servername in file
    $CACHE_ON=CACHE_GC;                // Default: 1 - Turn caching on/off
    $CACHE_POST=1;              // Default: 1 - Should POST's be cached
    $CACHE_SESSION=1;              // Default: 1 - Should pages be cached if there are session values
 
/******************************************************************************/

    /* This resets the cache state */
    function cache_reset() {
        global $cache_absfile, $cache_data, $cache_variables;
        $cache_absfile  =NULL;
        $cache_data     =array();
        $cache_variables=array();
    }

    /* Saves a variable state between caching */
    function cache_variable($vn) {
        global $cache_variables;
        cache_debug("Adding $vn to the variable store");
        $cache_variables[]=$vn;
    }

    /* Take a wild guess... */
    function cache_debug($s) {
        global $CACHE_DEBUG;
        static $debugline;

        if ($CACHE_DEBUG) 
        {
            $debugline++;
            echo $s.'<br>';
           header("X-Debug-$debugline: $s");
        }
    }

    /* Generates the key for the request */
    function cache_default_key() {
        return md5('POST='.serialize($_POST).' GET=' . serialize($_GET));
    }

    /* Returns the default object used by the helper functions */
    function cache_default_object() {
        global $SCRIPT_URI, $SERVER_NAME, $SCRIPT_NAME, $SINGLE_SITE;

        if ($SINGLE_SITE) {
            $name=$SCRIPT_NAME;
        } else {
            $name=$SCRIPT_URI;
        }

        if ($name=="") {
            $name="http://$SERVER_NAME/$SCRIPT_NAME";
        }
        return $name;
    }

    /* Caches the current page based on the page name and the GET/POST
        variables.  All must match or else it will not be fectched
        from the cache! */
    function cache_all($cachetime=120) {
        $key=cache_default_key();
        $object=cache_default_object();
        return cache($cachetime, $object, $key);
    }

    /* Obtain a lock on the cache storage, this can be stripped out
        and changed to a different handler like a database or
        whatever */
    function cache_lock($file, $open=TRUE) {
        static $fp;
        if ($open) {
            $fp=fopen($file, "r");
            $ret=flock($fp, LOCK_EX);
        } else {
            if (!$fp) {
                cache_debug("Invalid file handle?!");
                $fp=NULL;
                return;
            }
            flock($fp, LOCK_UN) or die("Cannot Release Lock");
            fclose($fp);
            $fp=NULL;
        }
        return $ret;

    }

    /* This is the function that writes out the cache */
    function cache_write($file, $data) {
        $fp=@fopen($file, "w");
        if (!$fp) {
            cache_debug("Failed to open for write out to $file");
            return FALSE;
        }
        fwrite($fp, $data, strlen($data));
        fclose($fp);
        return TRUE;
    }

    /* This function reads in the cache */
    function cache_read($file) {
       
       $fp=@fopen($file, "r");
        if (!$fp) {
            return NULL;
        }
        flock($fp, LOCK_SH);
        $buff="";
        while (($tmp=fread($fp, 4096))) {
            $buff.=$tmp;
        }
        fclose($fp);
        return $buff;
    }

    function cache_storage($cacheobject) {
        return MODULE_CACHE_DIR . "/" . $cacheobject;
    }

    /* Cache garbage collection */
    function cache_gc() {
        $MODULE_CACHE_DIR=MODULE_CACHE_DIR;
        cache_debug("Running gc");
        $dp=opendir($MODULE_CACHE_DIR);
        if (!$dp) {
            cache_debug("Error opening $MODULE_CACHE_DIR for cleanup");
            return FALSE;
        }
        while (!(($de=readdir($dp))===FALSE)) {
            // To get around strange php-strpos, add additional char
            if (strpos(" $de", "wm-")==1) {
                $absfile=$MODULE_CACHE_DIR . "/" . $de;
                $cachestuff=cache_read($absfile);
                $thecache=unserialize($cachestuff);
                if (is_array($thecache)) {
                    if ($thecache["cachetime"]!="0" && $thecache["expire"]<=time())  {
						cache_lock($absfile, TRUE);                        
                        if (@unlink($absfile)) {
                            cache_debug("Deleted $absfile");
                        } else {
                            cache_debug("Failed to delete $absfile");
                        }
						cache_lock($absfile, FALSE);                        
                    } else {
                        cache_debug("$absfile expires in " . ($thecache["expire"]-time()));
                    }
                } else {
                    cache_debug("$absfile is empty, being processed in another process?");
                }
            }
        }
    }

    /* Caches $object based on $key for $cachetime, will return 0 if the object has expired 
       or the object does not exist. */
    function cache($cachetime, $object, $key=NULL) {
        global $cache_absfile, $cache_file, $cache_data, $gzcontent, $CACHE_ON;
     
        if (!$CACHE_ON) {
            cache_debug("Not caching, CACHE_ON is off");
            return 0;
        }
        
        $curtime=time();
        $cache_file=$object;
        
        // Make it a valid name
        $cache_file=eregi_replace("[^A-Z,0-9,=]", "_", $cache_file);
        $key=eregi_replace("[^A-Z,0-9,=]", "_", $key);
    
        cache_debug("Caching based on OBJECT=$cache_file KEY=$key");
                
        $cache_file="wm-" . $cache_file . ":" . $key;
        $cache_file=$cache_file . ":" . $key;
        $cache_absfile=cache_storage($cache_file);
        // Can we access the cache_file ?
        if (($buff=cache_read($cache_absfile))) {
            cache_debug("Opened the cache file");
            $cdata=unserialize($buff);
            if (is_array($cdata)) {
                $curco=$cdata["cache_object"];
                if ($curco!=$cache_absfile) {
                    cache_debug("WTF?! That is not my cache file! got=$curco wanted=$cache_absfile");
                } else {
                    if ($cdata["cachetime"]=="0" || $cdata["expire"]>=$curtime) {
                        // data not yet expired (or never expiring)
                        
                        $expirein=$cdata["expire"]-$curtime+1;
                        cache_debug("Cache expires in $expirein");
                        
                        // restore variables
                        if (is_array($cdata["variables"])) {
                            foreach ($cdata["variables"] as $k=>$v) {
                                cache_debug("Restoring variable $k to value $v");
                                $GLOBALS[$k]=$v;
                            }
                        }
                        
                        // restore gzcontent
                        cache_debug("Restoring gzipped content");
                        $gzcontent = $cdata["gzcontent"];
                        
                        $ret=$expirein;
                        if ($cdata["cachetime"]=="0") {
                            $ret="INFINITE";
                        }
                        cache_reset();
                        return $ret; 
                    }
                }
            }
        } else {
            // No cache file (yet) or unable to read
            cache_debug("No previous cache of $cache_absfile or unable to read");
        }
        
        // If we came here: start caching!
        
        // Create the file for this page and lock it
        $oldum=umask();
        umask(0022); 
        if (@readlink($cache_absfile)) {
            cache_debug("$cache_absfile is a symlink! not caching!");
            $cache_absfile=NULL;
        } else {
            cache_debug("Created $cache_absfile, waiting for callback");
            @touch($cache_absfile);
            cache_lock($cache_absfile, TRUE);
        }
        umask($oldum);
        
        // Set expire and cachetime
        $cache_data["expire"]=$curtime + $cachetime;
        $cache_data["cachetime"]=$cachetime;
       
        return 0;
    }

    /* Does the actual caching */
    function writecache($gzcontent) {
        global $cache_absfile, $cache_data, $cache_variables, $CACHE_ON;
        
        if (!$CACHE_ON) {
            cache_debug("Not caching, CACHE_ON is off");
            return 0;
        }
        
        if ($cache_absfile!=NULL) {
            $variables=array();
            foreach ($cache_variables as $vn) {
                if (isset($GLOBALS[$vn])) {
                    $val=$GLOBALS[$vn];
                    cache_debug("Setting variable $vn to $val");
                    $variables["$vn"]=$val;
                }
            }
            // Fill cache_data
            $cache_data["gzcontent"]=$gzcontent;            
            $cache_data["cache_object"]=$cache_absfile;
            $cache_data["variables"]=$variables;
            $datas=serialize($cache_data);
            // write data
            cache_write($cache_absfile, $datas);
            // unlock cachefile
            cache_lock($cache_absfile, FALSE);
        }
    }

    /* wmCacheInit()
     *
     * Checks some global variables and might decide to disable caching
     * and calls appropriate initialization-methods
     */
    function wmCacheInit() {
        global $CACHE_TIME, $CACHE_ON, $CACHE_POST, $cachetimeout;

        // Override default CACHE_TIME ?
        if (isset($cachetimeout)) {
            $CACHE_TIME=$cachetimeout;
        }

        // Force cache off when POST occured when you don't want it cached
        if (!$CACHE_POST && (count($_POST) > 0)) {
            $CACHE_ON = 0;
            $CACHE_TIME = -1;
        }

        // Force cache off when POST occured when you don't want it cached
        if (!$CACHE_SESSION && (count($_SESSION) > 0)) {
            $CACHE_ON = 0;
            $CACHE_TIME = -1;
        }        
        // A cachetimeout of -1 disables writing, only ETag and content encoding if possible
        ##if ($CACHE_TIME == -1) {
        ##    $CACHE_ON=0;
        #}
        
        // Reset cache
        cache_reset();
        
        // Output header to recognize version
        $version = CACHE_VERSION;
        header("X-Cache: cache v".$version);
    }

    /* wmCacheGC()
     *
     * Handles the garbagecollection call
     */
    function wmCacheGC() {
        // Should we garbage collect ?
        if (CACHE_GC>0) {
            mt_srand(time(NULL));
            $precision=100000;
            $r=(mt_rand()%$precision)/$precision;
            if ($r<=(CACHE_GC/100)) {
                cache_gc();
            }
        }
    }

    /* wmCacheStart()
     *
     * Sets the handler for callback
     */
    function wmCacheStart() {
        global $CACHE_TIME, $gzcontent, $size, $crc32;

        // Initialize cache
        wmCacheInit();
   
        // Check cache
        if ($et=cache_all($CACHE_TIME)) {
            // Cache is valid: flush it!
            print wmCacheFlush($gzcontent, $size, $crc32);
            exit;
        } else {
            // if we came here, cache is invalid: go generate page 
            // and wait for wmCacheEnd() which will be called automagicly
            
            // Check garbagecollection
            wmCacheGC();
            // Go generate page and wait for callback
            ob_start("wmCacheEnd");
            ob_implicit_flush(0);
        }
    }

    /* wmCacheEnd()
     *
     * This one is called by the callback-funtion of the ob_start
     */
    function wmCacheEnd($contents) {
        cache_debug("Callback happened");
        global $size, $crc32;

        $size = strlen($contents);
        $crc32 = crc32($contents);
        
        $gzcontent = $contents;
        
        // cache these thingies, as they are on original content
        // which is lost after this
        cache_variable("size");
        cache_variable("crc32");
        
        // write the cache
        writecache($gzcontent);
        
        // Return flushed data
        return wmCacheFlush($gzcontent, $size, $crc32);
    }

    /* wmCacheFlush()
     *
     * Responsible for final flushing everything.
     * Sets ETag-headers and returns "Not modified" when possible
     */
    function wmCacheFlush($gzcontents, $size, $crc32) {
        global $HTTP_SERVER_VARS;
        global $cache_config;

        if ($cache_config["CACHE_SEND304"]) {
        	// First check if we can send last-modified
       		$myETag = "\"jpd-$crc32.$size\"";
        	header("ETag: $myETag");
        	$foundETag = stripslashes($HTTP_SERVER_VARS["HTTP_IF_NONE_MATCH"]);
        	$ret = NULL;
        
        	if (strstr($foundETag, $myETag)) {
            	// Not modified!
            	header('HTTP/1.0 304');
            }
        } else {
			$ret=$gzcontents;
        }
        return $ret;
    }
    ## the cache will be executed only if the user turned it on
    if($cache_config['MODULE_CACHE_ACTIVE']) {
    	wmCacheStart();
    }
?>
