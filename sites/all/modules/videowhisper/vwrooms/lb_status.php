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
$room = $_POST[r];
$session = $_POST[s];
$username = $_POST[u];
$message = $_POST[m];
$cam = $_POST[cam];
$mic = $_POST[mic];

$currentTime = $_POST[ct];
$lastTime = $_POST[lt];


$maximumSessionTime = 0; //900000ms=15 minutes; 0 for unlimited

$disconnect = "";

$pdir = getcwd();
//error_reporting(0);

chdir('../../../../../../');
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

$r = $_POST['r'];
$ct = ((int) $_POST['ct']) / 1000;
$lt = ((int) $_POST['lt']) / 1000;



$uid = $_COOKIE['vls_a_uid'];
$hash = $_COOKIE['vls_a_hash'];

$fc1 = db_query("SELECT *,unix_timestamp(now())-unix_timestamp(timestamp) as tu  from {vwrooms_users} where id=:i and hash=:h and room=:r",array(':i'=> $uid, ':h'=>$hash,':r'=> $r))->fetchObject();

/*
$fc1uu = db_query("select *,unix_timestamp(now())-unix_timestamp(timestamp) as tu from {vwrooms_users} where   id=%d and hash='%s' and room='%s' and o=1", array($uid, $hash, $r));


$fc1 = db_fetch_object($fc1uu);
*/
chdir($pdir);
$dx = db_query("select name from {variable} where name like 'vwcredits_saved'")->fetchField();

if ($dx && file_exists("../../vwcredits/lb_status.php")) {
  require_once( "../../vwcredits/lb_status.php");
}
else {

  if (!$fc1->id) {

    $disconnect = "User not found.";
  }

}
if (!$disconnect) {
  db_query("update {vwrooms_users} set timestamp=now() where id=$uid");
  $time = REQUEST_TIME;
  if ($fc1->tu) {
    db_query("update {vls_rooms} set timeused=timeused+$fc1->tu,timelastaccess=$time where nid=$fc1->room_nid");
  }
}



