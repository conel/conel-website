<?php

$fp = fopen('vle.log', 'at');
fwrite($fp, date('d/m/y H:i:s').' > '.$_SERVER['REMOTE_ADDR']."\n");
fclose($fp);

header('Location: https://vle.conel.ac.uk');

?>