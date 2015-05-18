<?php
//This script controls login and parameters to broadcasting inteface (is called by live_broadcast.swf)
chdir("../../../../../..");
define('DRUPAL_ROOT', getcwd());



require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
$_SERVER['SCRIPT_NAME'] = str_replace(variable_get('vconsult_path', ''), '/', $_SERVER['SCRIPT_NAME']);

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
global $user;

///error_reporting(E_ALL);
error_reporting(0);




///setcookie('username',$username,0,'/','.127.0.0.1');
$uid = $user->uid;

$room = $_GET['room_name'];
$vls_uid = $_COOKIE['vconsult_uid'];
$vls_hash = $_COOKIE['vconsult_hash'];


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


//$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);
$ff = db_query("select * from  {vwrooms_users} where id=? and hash=? and room=? order by o desc", array($vls_uid, $vls_hash, $room))->fetchObject();
$user = user_load($ff->uid);
$uid = $ff->uid;

$owner = false;
if ($ff->o) {
  $owner = true;
}
$rid = $ff->room_nid;


$paidroom = 0;


$loggedin = 1;
$msg = "";
if (!$ff->id) {
  $loggedin = 0;
  $msg = "record not found  ";
}

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
if (module_exists('vwcredits')) {
  $fr = db_query("select * from {vwcredits_cost} where room_nid=? ", array($ff->room_nid))->fetchObject();
  $time = REQUEST_TIME;






  if ($fr->uid > 0) { //costinfo
    //costinfo
    if (!$user->uid) {
      $loggedin = 0;
      $msg = "User not found";

    }

    else if ($owner && $fr->uid != $user->uid) {
      $loggedin = 0;
      $msg = "Invalid login.Not owner";

    }

    else if ($owner) {
      $time = REQUEST_TIME;
      ///	$o=user_load();
      $ctx =addslashes("vconsult $user->name $room");


      if ($fr->ownersroomcost) {
        $comment = t('Owners room cost for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownersroomcost' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersroomcost' ,context='$ctx ownersroomcost',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }
      }
      $ctx = addslashes("vconsult $user->name $room");

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



    }
    else { //notowner
      $time = REQUEST_TIME;
      // TODO Convert "user_load" to "user_load_multiple" if "$fr->uid" is other than a uid.
      // To return a single user object, wrap "user_load_multiple" with "array_shift" or equivalent.
      // Example: array_shift(user_load_multiple(array(), $fr->uid))
      $own = user_load($fr->uid)	;
      $ctx = addslashes("vconsult $own->name $room");

      if ($fr->ownerscost) {
        $comment = t('Owners cost for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownerscost' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownerscost' ,context='$ctx ownerscost',trans_type='D',trans_time=now(),comment=?", array($fr->uid, $rid, $comment));
        }





      }

      if ($fr->ownersgain) {

        $comment = t('Owners gain for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownersgain' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersgain' ,context='$ctx ownersgain',trans_type='C',trans_time=now(),comment=?", array($fr->uid, $rid, $comment));
        }





      }

      if ($fr->roomcost) {
        $ctx = addslashes("vconsult roomcost $user->name $room $own->name");

        $comment = t('Room cost for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='roomcost' and room_nid=? and applied=0 and uid=?", array($rid, $user->uid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='roomcost' ,context='$ctx',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }


      }


    }

  }
  else {

    //no cost info

  }

}
//credits.
else {

  if (!$ff->id) {
    $loggedin = 0;
    $msg = "user not found";
  }


}

$uname = $ff->uname;



if (!$uname) {
  $loggedin = 0;
  $msg = "<a href=\"index.php\">You need a cookie enabled browser! </a>";
}
else if (!$error) {
  $myroom = db_query("SELECT * FROM {vconsult_rooms} WHERE room = :room", array(':room' => array($room)));
  if ($myroom !== false) {
    $row = $myroom->fetchAssoc();
  }
  else {
    $loggedin = 0;
    $msg = "Room $room is not available!";
  }
}

if ($row['users'] && !$owner) {
  if (!$user->uid || !in_array($user->name, explode(',', $row['users']))) {

    $loggedin = 0;
    $msg = "user not allowed";

  }

}
/*correctable done*/
if (!$row['visitors'] && !$user->uid) {

  $loggedin = 0;
  $msg = "Visitors not allowed.";

}

//When broadcasting channel name = broadcaster name (because broadcaster stream and channel stream must have same name)



$room = preg_replace("/[^0-9a-zA-Z_]/", "-", $room);
$rtmp_server = 0;
if ($paidroom) {

  $rtmp_server = trim(variable_get('vconsult_paidrtmp3', ""));

}
if (!$rtmp_server) {
  $rtmp_server = variable_get('vconsult_rtmp3', "rtmp://server-domain-or-ip/videowhisper");
}


if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $msg = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/vconf\">Make sure module is enabled and check admin settings for Drupal, Administer > Live Streaming.</a>");
}
$rtmp_server = urlencode($rtmp_server);
$rtmfp_server = variable_get('vconsult_rtmfp', "rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/");

$sgr = trim(variable_get('vconsult_sgroup', "VideoWhisper"));

$pgr = trim(variable_get('vconsult_pgroup', $sgr));


$rtmp_amf = variable_get('vconsult_amf3', "AMF3");
$bufferLive = variable_get('vconsult_bufferlive3', "0.5");
$bufferFull = variable_get('vconsult_bufferfull3', "0.5");
$bufferLivePlayback = variable_get('vconsult_bufferliveplayback3', "0.2");
$bufferFullPlayback = variable_get('vconsult_bufferfullplayback3', "0.5");
$disablebandwidthdetection = variable_get('vconsult_disablebandwidthdetection3', "1");
$disableuploaddetection = variable_get('vconsult_disableuploaddetection3', "1");
$limitbybandwidth = variable_get('vconsult_limitbybandwidth3', "1");

$pvmx = variable_get('vconsult_publicvideomax', 8);
$pvn = variable_get('vconsult_publicvideon', 3);
$pvc = variable_get('vconsult_publicvideocol', 2);
$pvr = variable_get('vconsult_publicvideorow', 0);
$pvw = variable_get('vconsult_publicvideowidth', 160);
$pvh = variable_get('vconsult_publicvideoheight', 120);
$pvx = variable_get('vconsult_publicvideox', 160);
$pvy = variable_get('vconsult_publicvideoy', 160);



$filterRegex = $row[filterregex];
$welcome = $row[welcome];
$filterReplace = $row[filterreplace];
$layoutCode = $row[layoutcode];
$background_url = $row[background_url];
$room_limit = $row[room_limit];
$fillwindow = $row[fillwindow];
$floodprotection = $row[floodprotection];




$publicvideosadd = '0';
if (user_access('use publicvideosadd', $user)) {
  $publicvideosadd = $row[publicvideosadd];
}

if (user_access('use publicvideoconfig', $user)) {
  $pvmx = $row['publicvideomax'] ? $row['publicvideomax'] : $pvmx;
  $pvn = $row['publicvideon'] ? $row['publicvideon'] : $pvn;
  $pvc = $row['publicvideocol'] ? $row['publicvideocol'] : $pvc;
  $pvr = $row['publicvideorow'] ? $row['publicvideorow'] : $pvr;
  $pvw = $row['publicvideowidth'] ? $row['publicvideowidth'] : $pvw;
  $pvh = $row['publicvideoheight'] ? $row['publicvideoheight'] : $pvh;
  $pvx = $row['publicvideox'] ? $row['publicvideox'] : $pvx;
  $pvy = $row['publicvideoy'] ? $row['publicvideoy'] : $pvy;



}
$found = 0;
$administrator = 0;
if ($user->uid) {

  if ($owner || in_array($user->name, explode(',', $row['moderators'])) || in_array($user->mail, explode(',', $row['moderators']))) {
    $administrator = 1;
    $found = 1;
/*correctable d*/
  }

}

//if(in_array($user->name,$row['moderators'])||$owner)


$slideshow = '0';
if (user_access('use slideshow', $user)) {
  $slideshow = $row[slideshow];
}
$externalstream = '0';
if (user_access('use externalstream', $user)) {
  $externalstream = $row[externalstream];
}
$showcamsettings = '0';
if (user_access('use showcamsettings', $user)) {
  $showcamsettings = $row[showcamsettings];
}
$privatetextchat = '0';
if (user_access('use privatetextchat', $user)) {
  $privatetextchat = $row[privatetextchat];
}
$configuresource = '0';
if (user_access('use configuresource', $user)) {
  $configuresource = $row[configuresource];
}
$enabledsound = '1';
if (user_access('use enabledsound', $user)) {
  $enabledsound = $row[enabledsound];
}
$enabledvideo = '1';
if (user_access('use enabledvideo', $user)) {
  $enabledvideo = $row[enabledvideo];
}
$advancedcamsettings = '0';
if (user_access('use advancedcamsettings', $user)) {
  $advancedcamsettings = $row[advancedcamsettings];
}
$regularwatch = '0';
if (user_access('use regularwatch', $user)) {
  $regularwatch = $row[regularwatch];
}
$regularcams = '0';
if (user_access('use regularcams', $user)) {
  $regularcams = $row[regularcams];
}
$change_background = '0';
if (user_access('use change_background', $user)) {
  $change_background = $row[change_background]==1;
}
$files_enabled = '0';
if (user_access('use files_enabled', $user)) {
  $files_enabled = $row[files_enabled];
}
$file_upload = '0';
if (user_access('use file_upload', $user)) {
  $file_upload = $row[file_upload];
}
$file_delete = '0';
if (user_access('use file_delete', $user)) {
  $file_delete = $row[file_delete];
}
$chat_enabled = '0';
if (user_access('use chat_enabled', $user)) {
  $chat_enabled = $row[chat_enabled];
}
$users_enabled = '0';
if (user_access('use users_enabled', $user)) {
  $users_enabled = ($row[users_enabled] = 1);
}
$writetext = '0';
if (user_access('use write_text', $user)) {
  $writetext = ($row[write_text] == 1);
}
$autoplayserver = "";
$autoplaystream = "";

if ($found) {




  $writetext = $row[write_text];
}



$slideshow = '0';
if (user_access('use slideshow', $user)) {
  $slideshow = ($row[slideshow] == 1);
}
$externalstream = '0';
if (user_access('use externalstream', $user)) {
  $externalstream = ($row[externalstream] == 1);
}
$showcamsettings = '0';
if (user_access('use showcamsettings', $user)) {
  $showcamsettings = ($row[showcamsettings] == 1);
}
$privatetextchat = '0';
if (user_access('use privatetextchat', $user)) {
  $privatetextchat = ($row[privatetextchat] == 1);
}
$configuresource = '0';
if (user_access('use configuresource', $user)) {
  $configuresource = ($row[configuresource] == 1);
}
$enabledsound = '1';
if (user_access('use enabledsound', $user)) {
  $enabledsound = ($row[enabledsound] == 1);
}
$enabledvideo = '1';
if (user_access('use enabledvideo', $user)) {
  $enabledvideo = ($row[enabledvideo] == 1);
}
$advancedcamsettings = '0';
if (user_access('use advancedcamsettings', $user)) {
  $advancedcamsettings = ($row[advancedcamsettings] == 1);
}
$regularwatch = '0';
if (user_access('use regularwatch', $user)) {
  $regularwatch = ($row[regularwatch] == 1);
}
$regularcams = '0';
if (user_access('use regularcams', $user)) {
  $regularcams = ($row[regularcams] == 1);
}
$change_background = '0';
if (user_access('use change_background', $user)) {
  $change_background = ($row[change_background] == 1);
}
$files_enabled = '0';
if (user_access('use files_enabled', $user)) {
  $files_enabled = ($row[files_enabled] == 1);
}
$file_upload = '0';
if (user_access('use file_upload', $user)) {
  $file_upload = ($row[file_upload] == 1);
}
$file_delete = '0';
if (user_access('use file_delete', $user)) {
  $file_delete = ($row[file_delete] == 1);
}
$chat_enabled = '0';
if (user_access('use chat_enabled', $user)) {
  $chat_enabled = ($row[chat_enabled] == 1);
}
$users_enabled = '0';
if (user_access('use users_enabled', $user)) {
  $users_enabled = ($row[users_enabled] == 1);
}
$writetext = '0';
if (user_access('use write_text', $user)) {
  $writetext = ($row[write_text] == 1);
}
$autoplayserver = "";
$autoplaystream = "";

if ($found) {
  if (user_access('use write_text', $user)) {
    $writetext = $row[write_text];
  }

  if (user_access('use users_enabled', $user)) {
    $users_enabled = $row[users_enabled];
  }

  if (user_access('use chat_enabled', $user)) {
    $chat_enabled = $row[chat_enabled];
  }

  if (user_access('use file_delete', $user)) {
    $file_delete = $row[file_delete];
  }

  if (user_access('use file_upload', $user)) {
    $file_upload = $row[file_upload];
  }

  if (user_access('use files_enabled', $user)) {
    $files_enabled = $row[files_enabled];
  }

  if (user_access('use change_background', $user)) {
    $change_background = $row[change_background];
  }

  if (user_access('use regularcams', $user)) {
    $regularcams = $row[regularcams];
  }

  if (user_access('use regularwatch', $user)) {
    $regularwatch = $row[regularwatch];
  }

  if (user_access('use advancedcamsettings', $user)) {
    $advancedcamsettings = $row[advancedcamsettings];
  }

  if (user_access('use enabledvideo', $user)) {
    $enabledvideo = $row[enabledvideo];
  }

  if (user_access('use enabledsound', $user)) {
    $enabledsound = $row[enabledsound];
  }

  if (user_access('use configuresource', $user)) {
    $configuresource = $row[configuresource];
  }

  if (user_access('use privatetextchat', $user)) {
    $privatetextchat = $row[privatetextchat];
  }

  if (user_access('use showcamsettings', $user)) {
    $showcamsettings = $row[showcamsettings];
  }

  if (user_access('use externalstream', $user)) {
    $externalstream = $row[externalstream];
  }

  if (user_access('use slideshow', $user)) {
    $slideshow = $row[slideshow];
  }


}



$autoplayserver = variable_get('vconsult_autoplayserver', '');


if (user_access('use autoplay stream', $user) && $autoplayserver) {
  $autoplaystream = $row['autoplaystream'];
  $autoplayserver = $row['autoplayserver'];

}

if (!$autoplaystream) {
  $autoplayserver = '';
}
if (!$background_url) {
  $background_url = '';
  $change_background = '';
}
if (!$enabledsound) {
	$disabledSound = 1;
}
$disabledVideo = 0;
if (!$enabledvideo) {
	$disabledVideo = 1;
}
/*correctable d*/

?>firstParamter=fix&server=<?=$rtmp_server?>&serverAMF=<?=$rtmp_amf?>&room=<?=$room?>&welcome=<?=urlencode($welcome)?>&username=<?=$uname;?>&msg=<?=urlencode($msg)?>&visitor=<?php $row['visitor']?>&loggedin=<?=$loggedin;?>&background_url=<?=urlencode($background_url)?>&change_background=<?=$change_background
?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&room_limit=<?=$room_limit?>&administrator=<?=$administrator?>&showTimer=1&showCredit=1&disconnectOnTimeout=1&regularCams=<?=$regularcams
?>&regularWatch=<?=$regularwatch?>&camWidth=<?=$row[camwidth]?>&camHeight=<?=$row[camheight]?>&camFPS=<?=$row[camfps]
?>&micRate=<?=$row[micrate]?>&camBandwidth=<?=$row[bandwidth]//correctable?>&limitByBandwidth=<?=$limitbybandwidth
?>&showCamSettings=<?=$showcamsettings?>&advancedCamSettings=<?php echo $advancedcamsettings ?>&camMaxBandwidth=<?= $row['maxbandwidth']//correctable/variable_get('vconsult_cammaxbandwidth3', "131072")
?>&disableBandwidthDetection=<?=$disablebandwidthdetection?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull
?>&bufferLivePlayback=<?=$bufferLivePlayback?>&bufferFullPlayback=<?=$bufferFullPlayback?>&configureSource=<?=$configuresource
?>&disableVideo=<?=$disabledVideo?>&disableSound=<?=$disabledSound?>&disableUploadDetection=<?=$disableuploaddetection
?>&files_enabled=<?=$files_enabled?>&file_upload=<?=$file_upload?>&file_delete=<?=$file_delete
?>&chat_enabled=<?=$chat_enabled?>&floodProtection=<?=$floodprotection?>&writeText=<?=urlencode($writetext)
?>&privateTextchat=<?=$privatetextchat?>&externalStream=<?=$externalstream?>&slideShow=<?=$slideshow?>&users_enabled=<?=$users_enabled
?>&publicVideosN=<?php echo $pvn?>&publicVideosAdd=<?=$publicvideosadd
?>&publicVideosMax=<?php echo $pvmx ?>&publicVideosW=<?php echo $pvw?>&publicVideosH=<?php echo $pvh?>&publicVideosX=<?php echo $pvx?>&publicVideosY=<?php echo $pvy?>&publicVideosColumns=<?php echo $pvc?>&publicVideosRows=<?php echo $pvr?>&layoutCode=<?=urlencode($layoutCode)
?>&fillWindow=<?=$fillwindow?>&filterRegex=<?=urlencode($filterRegex)?><?php
?>&autoplayServer=<?php echo $autoplayserver?urlencode($autoplayserver):''?>&autoplayStream=<?php echo $autoplaystream?urlencode($autoplaystream):''
?>&filterReplace=<?=urlencode($filterReplace)?>&loadstatus=1&debugmessage=<?
echo urlencode($debug)?>&serverRTMFP=<?php echo urlencode($rtmfp_server)?>&p2pGroup=<?php urlencode($pgr)?>&supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&serverGroup=<?php echo urlencode($sgr)?>