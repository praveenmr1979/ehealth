<?php
$username=$_POST['username'];
$key=$_POST['key'];
if (!$username||$username=="Guest") $username="Guest".rand(1000,9999);
$r=$_POST['r'];

if ($key) setcookie("admin_key",urlencode($key),time()+50000);
setcookie("user_name",urlencode($username),time()+50000);
setcookie("room_name",urlencode($r),time()+50000);

$swfurl="consultation.swf?room=" .urlencode($r);
$bgcolor="#223333";
$baseurl="";
$wmode="transparent";

?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
<title>Video Consultation by VideoWhisper.com</title>
<style type="text/css">
<!--
a {
	color: #57AD01;
}
input {
	border: 1px solid #CCC;
	color: #666;
	font-weight: normal;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 15px;
	color: #666;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	letter-spacing: -1px;
}
.info
{
	text-align:left;
	padding: 10px;
	margin: 10px;
	background-color: #F3FFCE;
	border: 1px dotted #F90;
}

-->
</style>
</head>
<body bgcolor="#223333">
<center>

<div id="videoconsultation">
<object width="100%" height="100%">
<param name="movie" id="movie" value="<?=$swfurl?>" />
<param name="bgcolor" value="<?=$bgcolor?>" />
<param name="salign" value="lt" /><param name="scale" value="noscale" />
<param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> 
<param name="base" value="<?=$baseurl?>" /> <param name="wmode" value="<?=$wmode?>" /> 
<embed name="videowhisper_chat" width="100%" height="100%" scale="noscale" salign="lt" src="<?=$swfurl?>" bgcolor="<?=$bgcolor?>" base="<?=$baseurl?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="<?=$wmode?>">
</embed>
</object>
<noscript>
<p align=center>
<a href="http://www.videowhisper.com/?p=Video+Consultation"><img src="<?php echo $url?>/templates/consultation/logo.png" alt="Online Video Consultation and Presentation Software" width="196" height="28" border="0"> Online Video Consultation and Presentation Software</a></p>
<p align="center"><strong>This content requires the Adobe Flash Player:
<a href="http://get.adobe.com/flashplayer/">Get Latest Flash</a></strong>!</p>
</noscript>
</div>

</center>
</body>
</html>
