<?

$pdir = getcwd();
chdir('../../../../../../');
define('DRUPAL_ROOT', getcwd());
error_reporting(0);

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

$iid = (int) $_COOKIE['vconsult_uid'];
$uhash = $_COOKIE['vconsult_hash'];

$u = $_POST['u'];
$s = $_POST['s'];
$r = $_COOKIE['vconsult_room'];
$ct = ((int) $_POST['ct']) / 1000;
$lt = ((int) $_POST['lt']) / 1000;
$r = $_POST['r'];
$fur = db_query("SELECT *,unix_timestamp(now())-unix_timestamp(timestamp) as tu  from {vwrooms_users} where id=:i and hash=:h and room=:r",array(':i'=> $iid, ':h'=>$uhash,':r'=> $r))->fetchObject();

/*
$furuu = db_query("select *,unix_timestamp(now())-unix_timestamp(timestamp) as tu from {vwrooms_users} where id=%d and hash='%s' and room='%s'", array($iid, $uhash, $r));
$fur = db_fetch_object($furuu);
*/
$owner = $fur->o;

chdir($pdir);
$dx = variable_get('vwcredits_saved', 0);
$dx = db_query("select name from {variable} where name like 'vwcredits_saved'")->fetchField();

if ($dx && file_exists("../../vwcredits/c_status.php")) {
  require_once ( "../../vwcredits/c_status.php");
}
else {
  ///	if(!$fur->id)
  ///	$disconnect="User not found.";


}
if (!$disconnect) {
  // TODO Please convert this statement to the D7 database API syntax.
  db_query("update {vwrooms_users} set timestamp=now() where id=$iid");
  $time = time();
  if ($fur->tu) {
    // TODO Please convert this statement to the D7 database API syntax.
    db_query("update {vconsult_rooms} set timeused=timeused+$fur->tu,timelastaccess=$time where nid=$fur->room_nid");
  }
}

