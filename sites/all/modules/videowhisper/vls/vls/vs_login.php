<?php
//vs_login.php controls watch interface (video & chat & user list) login called by live_watch.swf

chdir("../../../../../..");
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

$_SERVER['SCRIPT_NAME'] = str_replace(variable_get('vls_path', ''), '/', $_SERVER['SCRIPT_NAME']);

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

global $user;
$username = $user->name;

$uid = $user->uid;


$vls_uid = $_COOKIE['vls_uid'];
$vls_hash = $_COOKIE['vls_hash'];


function baseURL() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {
    $pageURL .= "s";
  }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  }
  else {
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  }

  return substr($pageURL, 0, strrpos($pageURL, "/")) . "/";
}
$base = baseURL();




$room = $_GET['room_name'];
$time = REQUEST_TIME;

//$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);

$ffuu = db_query("select * from  {vwrooms_users} where id=? and hash=? and room=?", array($vls_uid, $vls_hash, $room));
/*correctable d*/
$ff = $ffuu->fetchObject();
$uid = $ff->uid;
$user = user_load($uid);
$username = $ff->uname;
$loggedin = 1;
$msg = "";
$paidroom = 0;

$picture='';

$us=db_select('users','u')->fields('u',array('picture'))->condition('u.uid',(int)$uid)->execute();
if($us){
	$us=$us->fetch();
	$file=file_load($us->picture);
	$picture=file_create_url($file->uri);
}

if ($picture) {
	//  $picture = str_replace(drupal_get_path('module', 'vconf') . '/vconf', '', $base_url) . '/' . $picture;
}
else {
	$picture = drupal_get_path('module', 'vconf')  .'/defaultpicture.png';
}
///else
	//configure a picture to show when this user is clicked
if ($user->uid) {

	$userPicture = urlencode($picture);
	$userLink = urlencode(url('user/'.$uid,array('absolute'=>true)));
}
else {

	$userPicture = urlencode( drupal_get_path('module', 'vconf')  .'/defaultpicture.png' );
	$userLink = urlencode($base_url);


}


if (module_exists('vwcredits')&&!$ff->o) {
  $fruu = db_query("select * from {vwcredits_cost} where room_nid=? ", array($ff->room_nid));
  $fr = $fruu->fetchObject();
  $rid = $fr->room_nid;
  $paidroom = 0;


  if (!$ff->id) {
    $loggedin = 0;
    $msg = "user not found ";
  }
  else if ($fr->uid) {

    $time = REQUEST_TIME;
    // TODO Convert "user_load" to "user_load_multiple" if "$fr->uid" is other than a uid.
    // To return a single user object, wrap "user_load_multiple" with "array_shift" or equivalent.
    // Example: array_shift(user_load_multiple(array(), $fr->uid))
    $own = user_load($fr->uid)	;
    $ctx = addslashes("vls  $room $own->name");


    if ($fr->ownerscost) {
      $comment = t('Owners cost for @r', array('@r' => $room));
      $xuu = db_query("select tid from {vwcredits_transaction} where type='ownerscost' and room_nid=? and applied=0", array($rid));
      $x = $xuu->fetchField();
      if (!$x) {
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownerscost' ,context='$ctx ownerscost',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
      }
    }


    if ($fr->ownersgain) {
      $comment = t('Owners gain for @r', array('@r' => $room));
      $xuu = db_query("select tid from {vwcredits_transaction} where type='ownersgain' and room_nid=? and applied=0", array($rid));
      $x = $xuu->fetchField();
      if (!$x) {
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersgain' ,context='$ctx ownersgain',trans_type='C',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
      }

    }




    if ($fr->roomcost) {

      $comment = t('room cost for @r', array('@r' => $room));
      $xuu =		db_query("select tid from {vwcredits_transaction} where type='roomcost' and room_nid=? and applied=0", array($rid));
      $x = $xuu->fetchField();
      if (!$x) {
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='roomcost' ,context='$ctx',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
      }
    }




  }
  else {

    //no cost info

  }
}
//vwcredits
else {
  if (!$ff->id) {
    $loggedin = 0;
    $msg = "User not found.";
  }

}


if (!$username) {
  $loggedin = 0;
  $msg = urlencode("<a href=\"index.php\">You need a cookie enabled browser!</a>");
}
else if (!$error) {
  $myroom = db_query("SELECT * FROM {vls_rooms} WHERE room = :room", array(':room' => array($room)));
  if ($myroom !== false) {
    $row = $myroom->fetchAssoc();
  }
  else {
    $loggedin = 0;
    $msg = urlencode("Room $room is not available!");
  }
}
if (!$row['visitors'] && !$user->uid) {
  $loggedin = 0;
  $msg = "Visitors not allowed";


}








$rtmp_server = "";
if ($paidroom) {

  $rtmp_server = variable_get('vls_paidrtmp3', "");

}
if (!$rtmp_server) {
  $rtmp_server = variable_get('vls_rtmp3', "rtmp://server-domain-or-ip/videowhisper");
}


if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $msg = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/vconf\">Make sure module is enabled and check admin settings for Drupal, Administer > 2 Way Video Chat.</a>");
}
$rtmp_server = urlencode($rtmp_server);

$rtmp_amf = variable_get('vls_amf3', "AMF3");
$bufferLive = variable_get('vls_bufferlive4', "0.5");
$bufferFull = variable_get('vls_bufferfull4', "0.5");
$tokenKey = variable_get('vls_tokenkey3', "0.2");
$statusinterval = variable_get('vls_statusinterval', '20000');


//replace bad words or expressions
$filterRegex = urlencode($row[filterregex]);
$filterReplace = urlencode($row[filterregex]);

//fill your layout code between <<<layoutEND and layoutEND;



$layoutCode = $row[layoutcode];

if ($row[welcome2]) {
  $welcome = urlencode($row[welcome2]);
}

$fillwindow = $row[fillwindow];
$floodprotection = $row[floodprotection2];

$offlinemessage = urlencode($row[offlinemessage]);

$write_text = variable_get('vls_write_text4', 1);
if (user_access('use write_text2', $user)) {
  $write_text = $row[write_text2];
}
$enabledchat = !variable_get('vls_disablechat', 0);
if (user_access('use enabledchat', $user)) {
  $enabledchat = $row[enabledchat];
}
$enabledvideo = !variable_get('vls_disablevideo', 0);
if (user_access('use enabledvideo', $user)) {
  $enabledvideo = $row[enabledvideo];
}
$enabledusers = !variable_get('vls_disableuser', 0);
if (user_access('use enabledusers', $user)) {
  $enabledusers = $row[enabledusers];
}
$showtimer = variable_get('vls_showtimer3', 1);
if (user_access('use showtimer', $user)) {
  $showtimer = $row[showtimer];
}

//panels
$disableChat = 0;
if (!$enabledchat) {
  $disableChat = 1;
}
$disabledUsers = 0;
if (!$enabledusers) {
  $disableUsers = 1;
}
$disableVideo = 0;
if (!$enabledvideo) {
  $disableVideo = 1;
}

if (!$welcome) {
  $welcome = urlencode("Welcome on <B>$room</B> live streaming channel!");
}

$adlink = '';
if (variable_get('vls_adserver', '')) {
  $adlink = urlencode(variable_get('vls_adserver', ''));
}
$adstimeout = variable_get('vls_adtimeout', '15000');
$adsinterval = variable_get('vls_adinterval', '240000');

if ($row['disablead']) {
  $adlink = '';
}
$visitor = !$user->uid;

$str = "server=$rtmp_server&serverAMF=$rtmp_amf&tokenKey=$tokenKey&welcome=$welcome&username=$username&userType=$userType&msg=$msg&visitor=$visitor&loggedin=$loggedin&showCredit=1&disconnectOnTimeout=1&offlineMessage=$offlinemessage";
if ($adlink) {
  $str .= "&ws_ads=$adlink&adsTimeout=$adstimeout&adsInterval=$adsinterval";
}
$rtmfp_server=variable_get('vls_rtmfp', "rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/");

$sgr=trim(variable_get('vls_sgroup', "VideoWhisper"));

$pgr=trim(variable_get('vls_pgroup', $sgr));
?>serverRTMFP=<?php echo urlencode($rtmfp_server)?>&p2pGroup=<?php $pgr?>&supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&serverGroup=<?php echo $sgr?>&<?php
$str .= "&disableVideo=$disableVideo&disableChat=$disablechat&disableUsers=$disableUsers&layoutCode=$layoutCode&fillWindow=$fillwindow";
$str .= "&filterRegex=$filterRegex&filterReplace=$filterReplace&writeText=$write_text&floodProtection=$floodprotection";
$str .= "&statusInterval=$statusinterval";
$str .= "&loadstatus=1";
echo $str;
?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>