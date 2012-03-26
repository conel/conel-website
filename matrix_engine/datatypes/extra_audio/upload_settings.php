<?PHP
#This file will be used directly by both php and perl, so it must be valid in both languages.
$max_upload = 10000000; #Max size allowed for uploaded files
$tmp_dir="/tmp"; #temporary directory, must be writable by both cgi-script and php scripts
$upload_dir="/tmp"; #Where to permanently store the uploaded files, when the upload is completed
							#This directory must be writable by php
$cgi_dir = "/cgi-bin"; #Webpath to folder that contains upload.cgi (can be entire url)
?>