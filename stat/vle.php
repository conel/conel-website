<?php

$ipaddress = getenv('REMOTE_ADDR');
//$ipaddress = '86.11.26.214'; // this is for testing an example outside ip

//from outside
$proportal_url = 'https://clg.conel.ac.uk/ProPortal';

//from inside
if (preg_match("/(^127\.0\.0\.1)|(^10\.)|(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)|(^192\.168\.)|(^195\.194\.75\.70)/", $ipaddress)) $proportal_url = 'http://ldmis-app/ProPortal';

?>
<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			html, body {
				margin:0; 
				padding:0;
				height:100%;
			}
			 
			body {
				background-color: #ddd;
			}
			
			img {
				border: none;
			}
			
			#floater {
				position:relative; float:left;
				height:50%;	
				margin-bottom:-200px;
				width:1px;
			}
			 
			#centered {
				position:relative; 
				clear:left;
				height:365px; 
				width:667px; 
				margin:0 auto;
			}		
		</style>
	</head>
	<body>	
		<!--div id="floater"></div-->
		<div id="centered">
			<img src="lpcs.jpg" usemap="#map" />	
		</div>
		<map name="map">
		  <area shape="rect" coords="25,340,290,200" href="https://vle.conel.ac.uk">
		  <area shape="rect" coords="380,340,645,200" href="<?php echo $proportal_url; ?>">
		</map> 		
	</body>
</html>
