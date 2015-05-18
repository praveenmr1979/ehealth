<?php
if (isset($GLOBALS["HTTP_RAW_POST_DATA"]))
{
  $stream=$_GET['name'];
  include_once("incsan.php");
  sanV($stream);
  if (!$stream) exit;

  //do not allow uploads to other folders
  if ( strstr($stream,"/") || strstr($stream,"..") ) exit;

	// get bytearray
	$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];

	$t=time();
	// save file
  $fp=fopen("snapshots/$stream/$t.jpg","w");
  if ($fp)
  {
    fwrite($fp,$jpg);
    fclose($fp);
  }
}
?>loadstatus=1