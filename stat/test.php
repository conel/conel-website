<?php

$ipaddress = getenv('REMOTE_ADDR');
//$ipaddress = '86.11.26.214'; 

print 'client ip: ' . $ipaddress . '<br>';

$proportal_url = 'https://clg.conel.ac.uk/ProPortal';

if (preg_match("/(^127\.0\.0\.1)|(^10\.)|(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)|(^192\.168\.)/", $ipaddress)) $proportal_url = 'http://ldmis-app/ProPortal';

print 'proportal_url: ' . $proportal_url . '<br>';
?>
