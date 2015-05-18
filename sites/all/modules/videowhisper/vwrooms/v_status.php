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

$iid = (int) $_COOKIE['vls_uid'];
$uhash = $_COOKIE['vls_hash'];

$u = $_POST['u'];
$s = $_POST['s'];
$r = $_POST['r'];
$ct = ((int) $_POST['ct']) / 1000;
$lt = ((int) $_POST['lt']) / 1000;
chdir($pdir);

$dx = variable_get('vwcredits_saved', 0);

$fur = db_query("SELECT *,unix_timestamp(now())-unix_timestamp(timestamp) as tu  from {vwrooms_users} where
id=:i and hash=:h and room=:r",array(':i'=> $iid, ':h'=>$uhash,':r'=> $r))->fetchObject();


/*
$furuu = db_query("select *,unix_timestamp(now())-unix_timestamp(timestamp) as tu from {vwrooms_users} where id=%d and hash='%s' and room='%s'", array($iid, $uhash, $r));
$fur = db_fetch_object($furuu);
*/
$owner = $fur->o;
$dx = variable_get('vwcredits_saved', 0);

$dx = db_query("select name from {variable} where name like 'vwcredits_saved'")->fetchField();

if ($dx && file_exists("../../vwcredits/v_status.php")&&($mymodule!='vls'||($mymodule=='vls'&&!$owner))) {
  require_once( "../../vwcredits/v_status.php");
}
else {
  ///if(!$fur->id)
  //$disconnect="User not found.";


}
if (!$disconnect) {
  db_query("update {vwrooms_users} set timestamp=now() where id=$iid");
  $time = REQUEST_TIME;
  if ($fur->tu) {
    db_query("update {vls_rooms} set timeused=timeused+$fur->tu,timelastaccess=$time where nid=$fur->room_nid");
  }
}


///$disconnect=""; //anything else than "" will disconnect with that message
