<?php
$pdir = getcwd();
chdir('../../../../../../');
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

/*
 POST Variables:
 u=Username
 s=Session, usually same as username
 r=Room
 ct=session time (in milliseconds)
 lt=last session time received from this script in (milliseconds)
 */

$iid = (int) $_COOKIE['v2wvc_uid'];
$uhash = $_COOKIE['v2wvc_hash'];

$u = $_POST['u'];
$s = $_POST['s'];
$ct = ((int) $_POST['ct']) / 1000;
$lt = ((int) $_POST['lt']) / 1000;
$room = $_POST['r'];
$fur = db_query("SELECT *,unix_timestamp(now())-unix_timestamp(timestamp) as tu  from {vwrooms_users} where id=:i and hash=:h and room=:r",array(':i'=> $iid, ':h'=>$uhash,':r'=> $room))->fetchObject();


$owner = $fur->o;
chdir($pdir);
$dx = variable_get('vwcredits_saved', 0);
$dx = db_query("select name from {variable} where name like 'vwcredits_saved'")->fetchField();

if ($dx && file_exists("../../vwcredits/2_status.php")) {
  require_once ( "../../vwcredits/2_status.php");
}
else {
  //if(!$fur->id)
  //$disconnect="User not found.";




}
if (!$disconnect) {

  db_query("UPDATE {vwrooms_users} set timestamp=now() where id=:i", array(':i'=>$iid));

  $time = time();

  if ($fur->tu) {
    db_query("UPDATE {v2wvc_rooms} set timeused=timeused+$fur->tu,timelastaccess=$time where nid=$fur->room_nid");
  }

}
