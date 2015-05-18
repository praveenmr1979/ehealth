<?php
/*
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
cam, mic = 0 none, 1 disabled, 2 enabled
*/
$room=$_POST[r];
$session=$_POST[s];
$username=$_POST[u];
$message=$_POST[m];
$cam=$_POST[cam];
$mic=$_POST[mic];

$currentTime=$_POST[ct];
$lastTime=$_POST[lt];


$maximumSessionTime=0; //900000ms=15 minutes; 0 for unlimited

$disconnect="";
	
	require_once("../../vwrooms/lb_status.php");
	
?>timeTotal=<?=$maximumSessionTime?>&timeUsed=<?=$currentTime?>&lastTime=<?=$currentTime?>&disconnect=<?=$disconnect?>&loadstatus=1