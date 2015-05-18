<?php
/*
POST Variables:
u=Username
s=Session, usually same as username
r=Room
ct=session time (in milliseconds)
lt=last session time received from this script in (milliseconds)
*/

$room=$_COOKIE['v2wvc_room'];
error_reporting(0);

$session=$_POST['s'];
$username=$_POST['u'];

$currentTime=$_POST['ct'];
$lastTime=$_POST['lt'];

$maximumSessionTime=0; //900000ms=15 minutes

require_once("../../vwrooms/2_status.php");

?>timeTotal=<?=$maximumSessionTime?>&timeUsed=<?=$currentTime?>&lastTime=<?=$currentTime?>&disconnect=<?=$disconnect?>&loadstatus=1