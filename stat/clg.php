<?php

$fp = fopen('clg.log', 'at');
fwrite($fp, date('d/m/y H:i:s').' > '.$_SERVER['REMOTE_ADDR']."\n");
fclose($fp);

header('Location: https://clg.conel.ac.uk');

?>