<?php 
$bgcolor="#223333";
$wmode="transparent";

?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$username?> : VideoWhisper.com Conference</title>
</head>
<body bgcolor="#5a5152" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
$bgcolor="#5a5152";
?>
<object width="100%" height="100%">
<param name="movie" value="<?=$swfurl?>"></param>
<param name="base" value="<?=$base?>" />
<param bgcolor="<?=$bgcolor?>"><param name="scale" value="noscale" /> </param>
<param name="salign" value="lt"></param><param name="allowFullScreen" value="true"></param>
<param name="allowscriptaccess" value="always"></param>
<embed width="100%" height="100%" scale="noscale" salign="lt" src="<?=$swfurl?>" base="<?=$base?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" bgcolor="<?=$bgcolor?>"></embed>
</object>
<noscript>
<p align=center><a href="http://www.videowhisper.com/?p=Video+Conference"><strong>VideoWhisper Video Conference
Software</strong></a></p>
<p align="center"><strong>This content requires the Adobe Flash Player:
<a href="http://www.macromedia.com/go/getflash/">Get Flash</a></strong>!</p>
</noscript>
</body>
</html>