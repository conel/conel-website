<?php

$ipaddress = getenv('REMOTE_ADDR');
//$ipaddress = '86.11.26.214'; // this is for testing an example outside ip

//from outside
$proportal_url = 'https://clg.conel.ac.uk/ProPortal';

//from inside
if (preg_match("/(^127\.0\.0\.1)|(^10\.)|(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)|(^192\.168\.)/", $ipaddress)) $proportal_url = 'http://ldmis-app/ProPortal';

?>
<html>
	<head>
		<style>
			a img {border: 0}
		</style>
	</head>
	<body>
		<table height="50%" align="center" border="0">
			<tr>
				<td>
					<a href="https://vle.conel.ac.uk"><img src="moodle.jpg" /></a>
				</td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $proportal_url; ?>"><img src="proportal.jpg" /></a>
				</td>
			</tr>
		</table>
	</body>
</html>
