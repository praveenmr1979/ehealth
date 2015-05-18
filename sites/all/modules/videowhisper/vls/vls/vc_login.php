<?php
ob_start();
//This script controls login and parameters to broadcasting inteface (is called by live_broadcast.swf)
chdir("../../../../../..");

define('DRUPAL_ROOT', getcwd());


require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
$_SERVER['SCRIPT_NAME'] = str_replace(variable_get('vls_path', ''), '/', $_SERVER['SCRIPT_NAME']);
///conf_init();

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
error_reporting(0);

global $user;
$uname = $user->uid;

///setcookie('username',$username,0,'/','.127.0.0.1');
$uid = $user->uid;


$vls_uid = $_COOKIE['vls_a_uid'];
$vls_hash = $_COOKIE['vls_a_hash'];


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

$pr = print_r($_POST, true);
$pr .= "\r\n===get===\r\n";
$pr .= print_r($_GET, true);
$pr .= "\r\n===cookie===\r\n";
$pr .= print_r($_COOKIE, true);

//db_query("insert into log set log='%s' ,file='vc_login'",$pr);

$username = $room = $_GET['room_name'];

$loggedin = 1;
$msg = "";
//$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);
$ffuu = db_query("select * from  {vwrooms_users} where id=? and hash=? and room=? and o=1", array((int)$vls_uid, $vls_hash, $room));
$ff = $ffuu->fetchObject();

$rid = $ff->room_nid;
$uid = $ff->uid;
$user = user_load($uid);


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

if ($ff->o) {
  $owner = true;

}
if (!$owner) {
  $loggedin = 0;
  $msg = "room owner can only start";

}



else if (module_exists('vwcredits')) {

	$fr = db_query("select * from {vwcredits_cost} where room_nid=? and uid=?", array($ff->room_nid, $user->uid))->fetchObject();


	$time = REQUEST_TIME;

  if (!$user->uid) {
    ///db_query("insert into {vwcredits_cost} set uid=%d",array($user->uid));
    ///	db_query("insert into {vwcredits_tmp} set uid=%d",array($user->uid));

    $loggedin = 0;
    $msg = "user not found in db";
    ///	$ffq=db_query("select * from {vwcredits_cost} where room_nid='%s' and uid=%d",array($ff->room_nid,$user->uid));
    ////	$fr=db_fetch_object($ffq);
  }
  else if ($fr->uid) {


    $time = REQUEST_TIME;
    $ctx = addslashes( "vls $user->name $room");


    if ($fr->ownersroomcost) {

      $comment = t('Owners room cost for @r', array('@r' => $room));
      $xuu = db_query("select tid from {vwcredits_transaction} where type='ownersroomcost' and room_nid=? and applied=0", array($rid));
      $x = $xuu->fetchField();
      if (!$x) {
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersroomcost' ,context='$ctx ownersroomcost',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
      }

    }

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
      $xuu =		db_query("select tid from {vwcredits_transaction} where type='ownersgain' and room_nid=? and applied=0", array($rid));
      $x = $xuu->fetchField();
      if (!$x) {
        // TODO Please convert this statement to the D7 database API syntax.
     db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersgain' ,context='$ctx ownersgain',trans_type='C',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
      }

    }







  }
  else {
    //no cost info

  }

}
//vwcredit
else {

  if (!$ff->id) {
    $loggedin = 0;
    $msg = "user not found";

  }

}

if (in_array($username, explode(",", variable_get('vwrooms_bannednames', '')))) {
  $loggedin = 0;
  $msg = urlencode("<a href=\"http://www.videowhisper.com\">You are not allowed to broadcast. Contact for details.</a>");
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


//When broadcasting channel name = broadcaster name (because broadcaster stream and channel stream must have same name)
$room = preg_replace("/[^0-9a-zA-Z_]/", "-", $room);
$username = $room;
$rtmp_server = 0;
if ($paidroom) {

  $rtmp_server = trim(variable_get('vls_paidrtmp3', ""));

}
if (!$rtmp_server) {
  $rtmp_server = variable_get('vls_rtmp3', "rtmp://server-domain-or-ip/videowhisper");
}


if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $msg = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/vconf\">Make sure module is enabled and check admin settings for Drupal, Administer > Live Streaming.</a>");
}
$rtmp_server = urlencode($rtmp_server);

$rtmp_amf = variable_get('vls_amf3', "AMF3");
$bufferLive = variable_get('vls_bufferlive3', "0.5");
$bufferFull = variable_get('vls_bufferfull3', "0.5");
$tokenKey = variable_get('vls_tokenkey3', "0.2");
$disablebandwidthdetection = variable_get('vls_disablebandwidthdetection3', "1");
$limitbybandwidth = variable_get('vls_limitbybandwidth3', "1");
$generatesnapshots = variable_get('vls_generatesnapshots3', "1");
$snapshotstime = variable_get('vls_snapshotstime3', "60000");
$extchat = variable_get('vls_external', "20000");

$fillwindow = variable_get('vls_fillwindow3', '1');
$layoutCode = variable_get('vls_layoutcode3', '');
$offlinemsg = urlencode(variable_get('vls_offlinemessage3', 'Chanel Offline'));
$welcome = urlencode(variable_get('vls_welcome', 'Welcome!'));
//$disablevideo = variable_get('vls_disablevideo', 0);
//$disablechat = variable_get('vls_disablechat', 0);
//$disableusers = variable_get('vls_disableusers', 0);
$statusinterval = variable_get('vls_statusinterval', '20000');

if ($generatesnapshots) {

  $sitepath = variable_get('vls_path', '/vls/');
  $p =  realpath("." . $sitepath . 'snapshots');
  if (!is_writable($p)) {
    $loggedin = 0;
    $msg = "snapshots directory $p not writable";
  }
  else {
    $dir = false;

    if (!file_exists($p . '/' . $room)) {
      $dir = mkdir($p . '/' . $room, 0777);
    }
    else if (!is_writable($p . '/' . $room)) {
      chmod($p . '/' . $room, 0777);
      $dir = 1;
      if (!is_writable($p . '/' . $room)) {
        $dir = 0;
      }


    }
    else {
      $dir = 1;
    }
    if (!$dir) {
      $loggedin = 0;
      $msg = "cannot create snapshot directory" . $p . '/' . $room;

    }

  }

}


/*
 'vls_visitorsallowed'
 credits
 expire
 */

if (user_access('use welcome of broadcasting settings', $user)) {
  $welcome = urlencode($row[welcome]);
}

$room_limit = variable_get('vls_room_limit3', "100");
if (user_access('use room_limit', $user)) {
  $room_limit = $row[room_limit];
}


variable_get('vls_labelcolor3', "FFFFFF");
if (user_access('use labelcolor', $user)) {
  $labelcolor = $row[labelcolor];
}


$onlyvideo = variable_get('vls_onlyvideo3', "0");
if (user_access('use onlyvideo', $user)) {
  $onlyvideo = $row[onlyvideo];
}


$noembeds =	 variable_get('vls_noembeds3', "0");
if (user_access('use noembeds', $user)) {
  $noembeds = $row[noembeds];
}


$floodprotection = variable_get('vls_floodprotection3', "3");
if (user_access('use floodprotection of broadcasting settings', $user)) {
  $floodprotection = $row[floodprotection];
}



$write_text = variable_get('vls_write_text3', "1");
if (user_access('use write_text', $user)) {
  $write_text = $row[write_text];
}


$showcamsettings = variable_get('vls_showcamsettings3', "1");
if (user_access('use showcamsettings', $user)) {
  $showcamsettings = $row[showcamsettings];
}

$configuresource = variable_get('vls_configuresource3', "1");
if (user_access('use configuresource', $user)) {
  $configuresource = $row[configuresource];
}


$advancedcamsettings = variable_get('vls_advancedcamsettings3', "1");
if (user_access('use advancedcamsettings', $user)) {
  $advancedcamsettings = $row[advancedcamsettings];
}

$enabledchat = variable_get('vls_enabledchat3', "1");
if (user_access('use enabledchat', $user)) {
	$enablechat = $row['enabledchat'];
}
$enabledvideo = variable_get('vls_enabledvideo3', "1");
if (user_access('use enabledvideo', $user)) {
	$enablechat = $row['enabledvideo'];
}
$enableduser = variable_get('vls_enabledusers3', "1");
if (user_access('use enabledusers', $user)) {
	$enableuser = $row['enabledusers'];
}


$showtimer = variable_get('vls_showtimer3', "1");
if (user_access('use showtimer', $user)) {
  $showtimer = $row[showtimer];
}
$base = baseURL();
$linkcode = urlencode($base . "channel.php?n=" . urlencode($room));
$imagecode = urlencode($base . "snapshots/" . urlencode($room) . ".jpg");
$swfurl = urlencode($base . "live_watch.swf?n=" . urlencode($room));
$swfurl2 = urlencode($base . "live_video.swf?n=" . urlencode($room));

$embedcode = <<<EMBEDEND
<object width="640" height="350"><param name="movie" value="$swfurl" /><param name="base" value="$base" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="scale" value="noscale" /><param name="salign" value="lt" /><embed src="$swfurl" base="$base" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="350" scale="noscale" salign="lt" ></embed></object>
EMBEDEND;
$embedvcode = <<<EMBEDEND2
<object width="320" height="240"><param name="movie" value="$swfurl2" /><param name="base" value="$base" /><param name="scale" value="exactfit"/><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><embed src="$swfurl2" base="$base" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="240" scale="exactfit" ></embed></object>
EMBEDEND2;
$embedcode = urlencode($embedcode);
$embedvcode = urlencode($embedvcode);
///$msg=urlencode("$loggedin cannot log in $room $user->name uid $vls_uid hash $vls_hash");
///$loggedin=1;




$disablevideo=!$enabledvideo;


$rtmfp_server = variable_get('vls_rtmfp', "rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/");

$sgr = trim(variable_get('vls_sgroup', "VideoWhisper"));

$pgr = trim(variable_get('vls_pgroup', $sgr));

////$msg=urlencode(" $msg logged $loggedin rtmfp $rtmfp_server sgr $sgr");

$str = "fixFirst=VideoWhisper&server=$rtmp_server&serverAMF=$rtmp_amf&tokenKey=$tokenKey&room=$room&welcome=$welcome&username=$username&userType=3&webserver=&msg=$msg&loggedin=$loggedin";

$str .= "&linkcode=$linkcode&embedcode=$embedcode&embedvcode=$embedvcode&imagecode=$imagecode";
$str .= "&room_limit=$room_limit&showTimer=$showtimer&showCredit=1&disconnectOnTimeout=1";
$str .= "&camWidth=$row[camwidth]&camHeight=$row[camheight]&camFPS=$row[camfps]&micRate=$row[micrate]&camBandwidth=$row[bandwidth]&limitByBandwidth=$limitbybandwidth";
$str .= "&bufferLive=$bufferLive&bufferFull=$bufferFull";
$str .= "&showCamSettings=$showcamsettings&camMaxBandwidth=$row[maxbandwidth]configureSource=$configuresource&disableBandwidthDetection=$disablebandwidthdetection&advancedCamSettings=$advancedcamsettings";
$str .= "&generateSnapshots=$generatesnapshots&snapshotsTime=$snapshotstime";
$str .= "&onlyVideo=$onlyvideo&noVideo=$disablevideo&noEmbeds=$noembeds";
$str .= "&labelColor=$labelcolor&writeText=$write_text&floodProtection=$floodprotection";
$str .= "&externalInterval=$extchat&enabledChat=$enabledchat&enabledVideo=$enabledvideo&enabledUsers=$enableduser";
$str .= "&statusInterval=$statusinterval";
echo $str;
?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&serverRTMFP=<?php echo urlencode($rtmfp_server)?>&p2pGroup=<?php $pgr?>&supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&serverGroup=<?php echo $sgr;?>&loadstatus=1
