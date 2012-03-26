<?php
# PHP File Uploader with progress bar Version 2.0
# Based on progress.php, a contrib to Megaupload, by Mike Hodgson.
# Changed for use with AJAX by Tomas Larsson
# http://tomas.epineer.se/

# Licence:
# The contents of this file are subject to the Mozilla Public
# License Version 1.1 (the "License"); you may not use this file
# except in compliance with the License. You may obtain a copy of
# the License at http://www.mozilla.org/MPL/
# 
# Software distributed under this License is distributed on an "AS
# IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
# implied. See the License for the specific language governing
# rights and limitations under the License.

require_once("upload_settings.php");

function bye_bye($msg) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "$msg";
	exit;
}

if(!isset($_POST['sid'])) {
	bye_bye("No sid received.");	
}
$sessionid = $_POST['sid'];

$info_file = "$tmp_dir/$sessionid"."_flength";
$data_file = "$tmp_dir/$sessionid"."_postdata";
$error_file = "$tmp_dir/$sessionid"."_err";

# Send error code if error file exists
if(file_exists($error_file)) {
	$mes = file_get_contents($error_file);
	bye_bye($mes);
}

$percent_done = 0;
$started = TRUE;
if ($fp = @fopen($info_file,"r")) {
		$fd = fread($fp,1000);
		fclose($fp);
		$total_size = $fd;
} else {
	$started = FALSE;
}
if ($started == TRUE) {
	$current_size = @filesize($data_file);
	$percent_done = intval(($current_size / $total_size) * 100);
}
echo $percent_done;
exit;
?>