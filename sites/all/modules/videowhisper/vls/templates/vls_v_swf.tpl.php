<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style type="text/css">
<!--

body {
	background-color: #000;
}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$n?> Live Video Streaming</title>
</head>
<body  leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php 
$baseurl=preg_replace('/^\/$/','',$base); 
$bgcolor="#333333";
$wmode="transparent";
?>

<div id="videowhisper_video">

<object width="100%" height="100%" type="application/x-shockwave-flash" data="<?=$swfurl?>">
<param name="movie" value="<?=$swfurl?>"></param><<param name="scale" value="noscale" /> </param><param name="salign" value="lt"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
<param name="base" value="<?=$baseurl?>"/><param name="wmode" value="<?=$wmode?>" /></object>

</div>

</body>
</htm>