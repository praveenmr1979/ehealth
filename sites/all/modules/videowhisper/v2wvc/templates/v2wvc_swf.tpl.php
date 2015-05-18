<?
$bgcolor="#333333";
$wmode="transparent";
?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
<title>2 Way Video Chat by VideoWhisper.com</title>
<style type="text/css">
<!--
BODY
{
	margin:0px;
	background-color: #333;
}

#2wayvideochat
{
	width:100%;
	height:700px;
	z-index:0;
	vertical-align: middle;
	text-align: center;
}
-->
</style>
</head>
<BODY>
<CENTER>

<SCRIPT language="JavaScript">
//the code below allows activating certain functions from javascript
function getFlashMovie(movieName) {
  var isIE = navigator.appName.indexOf("Microsoft") != -1;
  return (isIE) ? window[movieName] : document[movieName];
}

//flash = flash html object name (ie "videowhisper_chat")
//action = next / snapshot / snapshot_self / buzz / p2p_toggle
function videowhisperCallActionscript(flash, action) 
{
    var movie = getFlashMovie(flash);
	movie.videowhisperToActionscript(action);
}
</SCRIPT>

<div id="2wayvideochat">
<object width="1000" height="700">
<param base="<?=$base?>"></param>
<param name="movie" id="movie" value="<?=$swfurl?>" />
<param name="bgcolor" value="<?=$bgcolor?>" />
<param name="salign" value="lt" /><param name="scale" value="noscale" />
<param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /> 
<param name="base" value="<?=$base?>" /> 
<param name="wmode" value="<?=$wmode?>" /> 
<embed name="videowhisper_chat" width="1000" height="700" scale="noscale" salign="lt" src="<?=$swfurl?>" bgcolor="<?=$bgcolor?>" base="<?=$base?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="<?=$wmode?>">
</embed>
</object>
<noscript>
<p align=center>VideoWhisper  <a href="http://www.videowhisper.com/?p=2+Way+Video+Chat"><strong>Video Chat Script</strong></a></p>
<p align="center"><strong>This content requires the Adobe Flash Player:
<a href="http://get.adobe.com/flashplayer/">Get Latest Flash</a></strong>!</p>
</noscript>
</div>
</CENTER>
</BODY>
</html>
	<?php 