<?php
chdir("../../../../../..");
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

$_SERVER['SCRIPT_NAME'] = str_replace(variable_get('vconf_path', ''), '/', $_SERVER['SCRIPT_NAME']);

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
global $user;
error_reporting(0);



$loggedin = 1;
$msg = "";

///setcookie('username',$username,0,'/','.127.0.0.1');
$uid = $user->uid;

$vls_uid = $_COOKIE['vconf_uid'];
$vls_hash = $_COOKIE['vconf_hash'];


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

$room = $_COOKIE['vconf_room'];
$room = $_GET['room_name'];

$loggedin = 1;
$msg = "";
//$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);
$ff = db_query("select * from  {vwrooms_users} where id=? and hash=? and room=? order by o desc", array($vls_uid, $vls_hash, $room))->fetchObject();
$user = user_load($ff->uid);
$uid = $user->uid;

$owner = false;
if ($ff->o) {
  $owner = true;
}
$picture='';

$us=db_select('users','u')->fields('u',array('picture'))->condition('u.uid',$uid)->execute();
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

$rid = $ff->room_nid;


$paidroom = 0;

$paidroom = 0;


if (module_exists('vwcredits')) {
  $fr = db_query("select * from {vwcredits_cost} where room_nid=? ", array($ff->room_nid))->fetchObject();
  $time = REQUEST_TIME;

  if ($fr->uid) { //constinfo
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
      $ctx = addslashes("vconf $user->name $room");


      if ($fr->ownersroomcost) {
        $comment = t('Owners room cost for @r', array('@r' => $room));
        $xuu =			db_query("select tid from {vwcredits_transaction} where type='ownersroomcost' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersroomcost' ,
          context='$ctx ownersroomcost',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }

      }
      $ctx = addslashes("vconf $user->name $room");

      if ($fr->ownerscost) {
        $comment = t('Owners cost for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownerscost' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into
          {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,
          type='ownerscost' ,context='$ctx ownerscost',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }

      }

      if ($fr->ownersgain) {
        $comment = t('Owners gain for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownersgain' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();

        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersgain' ,
          context='$ctx ownersgain',trans_type='C',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }


      }



    }
    else { //notowner
      $time = REQUEST_TIME;
      // TODO Convert "user_load" to "user_load_multiple" if "$fr->uid" is other than a uid.
      // To return a single user object, wrap "user_load_multiple" with "array_shift" or equivalent.
      // Example: array_shift(user_load_multiple(array(), $fr->uid))
      $own = user_load($fr->uid)	;
      $ctx = addslashes("vconf $own->name $room");

      if ($fr->ownerscost) {
        $comment = t('Owners cost for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownerscost' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,
          type='ownerscost' ,context='$ctx ownerscost',trans_type='D',trans_time=now(),comment=?", array($fr->uid, $rid, $comment));
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
        $ctx = addslashes("vconf roomcost $user->name $room $own->name");

        $comment = t('Room cost for @r', array('@r' => $room));
        $xuu = db_query("select tid from {vwcredits_transaction} where type='roomcost' and room_nid=? and applied=0 and uid=?", array($rid, $user->uid));
        $x = $xuu->fetchField();
        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='roomcost' ,
          context='$ctx',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }


      }


    }


  }
  //costinfo
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

$uname = $username = $ff->uname;


















if (!$room || !$uname) {
  $loggedin = 0;
  $msg = "<a href=\"index.php\">You need a cookie enabled browser! room </a>";
}
else {
  $myroom = db_query("SELECT * FROM {vconf_rooms} WHERE room = :room", array(':room' => array($room)));
  if ($myroom !== false) {
    $row = $myroom->fetchAssoc();
  }
  else {
    $loggedin = 0;
    $msg = "Room $room is not available!";
  }
}
$admin = 0;
if ($user->uid) {

  if ($owner || in_array($user->name, explode(',', $row['moderators'])) || in_array($user->mail, explode(',', $row['moderators']))) {
    $admin = 1;
  }


}


$room = preg_replace("/[^0-9a-zA-Z_]/", "-", $room);
$rtmp_server = 0;
if ($paidroom) {

  $rtmp_server = trim(variable_get('vconf_paidrtmp2', ""));

}
if (!$rtmp_server) {
  $rtmp_server = variable_get('vconf_rtmp2', "rtmp://server-domain-or-ip/videowhisper");
}


if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $msg = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/vconf\">Make sure module is enabled and check admin settings for Drupal, Administer > Live Streaming.</a>");
}








if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $msg = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/vconf\">Make sure module is enabled and check admin settings for Drupal, Administer > 2 Way Video Chat.</a>");
}
$rtmp_amf = variable_get('vconf_amf2', "AMF3");
$bufferLive = variable_get('vconf_bufferlive2', "0.5");
$bufferFull = variable_get('vconf_bufferfull2', "0.5");
$bufferLivePlayback = variable_get('vconf_bufferliveplayback2', "0.2");
$bufferFullPlayback = variable_get('vconf_bufferfullplayback2', "0.5");
$disableuploaddetection = variable_get('vconf_disableuploaddetection2', "1");
$disablebandwidthdetection = variable_get('vconf_disablebandwidthdetection2', "1");
$limitbybandwidth = variable_get('vconf_limitbybandwidth2', "1");


$rtmfp_server = variable_get('vconf_rtmfp', "rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/");

$sgr = trim(variable_get('vconf_sgroup', "VideoWhisper"));

$pgr = trim(variable_get('vconf_pgroup', $sgr));


$filterRegex = urlencode($row[filterregex]);
$filterReplace = urlencode($row[filterreplace]);
$layoutCode = $row[layoutcode];
$background_url = $row[background_url];
$tutorial = $row[tutorial];
$fillwindow = $row[fillwindow];

$autoviewcams = '0';
if (user_access('use autoviewcams', $user)) {
  $autoviewcams = ($row[autoviewcams] == 1);
}
$panelfiles = '0';
if (user_access('use panelfiles', $user)) {
  $panelfiles = ($row[panelfiles] == 1);
}
$file_upload = '0';
if (user_access('use file_upload', $user)) {
  $file_upload = ($row[file_upload] == 1);
}
$showcamsettings = '0';
if (user_access('use showcamsettings', $user)) {
  $showcamsettings = ($row[showcamsettings] == 1);
}
$file_delete = '0';
if (user_access('use file_delete', $user)) {
  $file_delete = ($row[file_delete] == 1);
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
$showtimer = '0';
if (user_access('use showtimer', $user)) {
  $showtimer = ($row[showtimer] == 1);
}

$pu = 0;
if (user_access('use panelusers', $user)) {
  $pu = ($row['panelusers'] == 1);
}
$pr = 0;
if (user_access('use panelroom', $user)) {
  $pr = ($row['panelroom'] == 1);
}
$writetext = '0';
if (user_access('use write_text', $user)) {
	$writetext = ($row[write_text] == 1);
}

/*correctable*/
if ($admin) {
	$writetext = '0';
	if (user_access('use write_text', $user)) {
		$writetext = $row[write_text];
	}


  if (user_access('use showtimer', $user)) {
    $showtimer = $row[showtimer];
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

  if (user_access('use file_delete', $user)) {
    $file_delete = $row[file_delete];
  }

  if (user_access('use showcamsettings', $user)) {
    $showcamsettings = $row[showcamsettings];
  }

  if (user_access('use file_upload', $user)) {
    $file_upload = $row[file_upload];
  }

  if (user_access('use panelfiles', $user)) {
    $panelfiles = $row[panelfiles];
  }

  if (user_access('use autoviewcams', $user)) {
    $autoviewcams = $row[autoviewcams];
  }
  $pu=0;

  if (user_access('use panelusers', $user)) {
    $pu = ($row['panelusers']);
  }
  $pr = 0;
  if (user_access('use panelroom', $user)) {
    $pr = ($row['panelroom']);
  }





}

$adlink = '';
if (variable_get('vconf_adserver', '')) {
  $adlink = urlencode(variable_get('vconf_adserver', ''));
}
$adstimeout = variable_get('vconf_adtimeout', '15000');
$adsinterval = variable_get('vconf_adinterval', '240000');
if ($row['disablead']) {
  $adlink = '';
}



//sound and video
$disabledSound = 0;
if (!$enabledsound) {
  $disabledSound = 1;
}
$disabledVideo = 0;
if (!$enabledvideo) {
  $disabledVideo = 1;
}

if (!$room) {
  $room = "Lobby";
}

$rw = variable_get('vconf_regularwatch', 1);
$nw = variable_get('vconf_newwatch', 1);
$pt = variable_get('vconf_privatetxt', 1);
$fp = variable_get('vwrooms_floodprotect', 3);

$userType = $_COOKIE['usertype'];

/*
 firstParameter=fix&serverRTMFP=rtmfp%3A%2F%2Fstratus.adobe.com%2Ff1533cc06e4de4b56399b10d-1a624022ff71%2F&p2pGroup=&
 supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&serverGroup=VideoWhisper&server=rtmp%3A%2F%2F207.7.80.65%2Fdevwebca_vw&serverAMF=AMF3
 &username=aaaaaa&loggedin=1&userType=1&administrator=0&room=InstantRoom_ihmxl0&welcome=Welcome+to+InstantRoom_ihmxl0%21+%3CBR%3E%3Cfont+color%3D%22%233CA2DE%22%3E%26%23187%3B%3C%2
 Ffont%3E+Click+top+left+preview+panel+for+more+options+including+selecting+different+camera+and+microphone.+%3CBR%
 3E%3Cfont+color%3D%22%233CA2DE%22%3E%26%23187%3B%3C%2Ffont
 %3E+Click+any+participant+from+users+list+for+more+options+including+extra+video+panels.+%3CBR%3E%3Cfont+color%3D%22%233CA2DE%22%3E%26%23187%3B%3C%2Ffont%3E+
 Try+pasting+urls%2C+youtube+movie+urls%2C+picture+urls%2C+emails%2C+twitter+accounts+as+%40videowhisper+in+your+text+chat.+%3CBR%3E%3Cfont+color%3D%22%233CA2DE%22%3E%26%23187%3B%3C%2Ffont%3E+Download+daily+chat+logs+from+file+list.&userPicture=http%3A%2F%2Fdev2.webcampresence.com%2Fd1%2Fhttp%3A%2F%2Fdev2.webcampresence.com%2Fd1%2F&userLink=%2Fuser%2F5&webserver=&msg=&tutorial=1&room_delete=0&room_create=0&file_upload=1&file_delete=1&panelFiles=1&showTimer=1&showCredit=1&disconnectOnTimeout=0&camWidth=176&camHeight=144&camFPS=20&micRate=22&camBandwidth=50000&limitByBandwidth=1&showCamSettings=1&camMaxBandwidth=100000&disableBandwidthDetection=1&bufferLive=0.1&bufferFull=0.1&bufferLivePlayback=0.1&bufferFullPlayback=0.1&advancedCamSettings=1&configureSource=1&disableVideo=0&disableSound=0&disableUploadDetection=1&background_url=&autoViewCams=1&layoutCode=&fillWindow=0&filterRegex=%28%3Fi%29%28fuck%7Ccunt%29%28%3F-i%29&filterReplace=+%2A%2A+&writeText=&floodProtection=3&regularWatch=1&newWatch=1&privateTextchat=1&statusInterval=20000&panelRooms=0&panelUsers=0&loadstatus=1
 */

if (!$welcome) {
  $welcome = "Welcome to $room! <BR><font color=\"#3CA2DE\">&#187;</font> Click top left preview panel for more options including selecting different camera and microphone. <BR><font color=\"#3CA2DE\">&#187;</font> Click any participant from users list for more options including extra video panels. <BR><font color=\"#3CA2DE\">&#187;</font> Try pasting urls, youtube movie urls, picture urls, emails, twitter accounts as @videowhisper in your text chat. <BR><font color=\"#3CA2DE\">&#187;</font> Download daily chat logs from file list.";
}
?>firstParameter=fix&serverRTMFP=<?php echo urlencode($rtmfp_server)?>&p2pGroup=<?php $pgr?>&supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&serverGroup=<?php echo $sgr?>&<?php
?>server=<?=urlencode($rtmp_server)?>&serverAMF=<?=$rtmp_amf?>&username=<?=urlencode($username)?>&loggedin=<?=$loggedin
?>&userType=<?=$userType?>&administrator=<?=$admin?>&room=<?=urlencode($room)?>&welcome=<?=urlencode($welcome)
?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>&webserver=&msg=<?=urlencode($msg)?>&tutorial=<?=$tutorial
?>&room_delete=0&room_create=0&file_upload=<?=$file_upload?>&file_delete=<?=$file_delete?>&panelFiles=<?=$panelfiles
?>&showTimer=<?=$showtimer?>&showCredit=1&disconnectOnTimeout=0&camWidth=<?=$row[camwidth]?>&camHeight=<?=$row[camheight]?>&camFPS=<?=$row[camfps]?>&micRate=<?=$row[micrate]?>&camBandwidth=<?=$row[bandwidth]//done correctable
?>&limitByBandwidth=<?=$limitbybandwidth?>&showCamSettings=<?=$showcamsettings?>&camMaxBandwidth=<?=$row[maxbandwidth]?>&disableBandwidthDetection=<?=$disablebandwidthdetection
?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull?>&bufferLivePlayback=<?=$bufferLivePlayback
?>&bufferFullPlayback=<?=$bufferFullPlayback?>&advancedCamSettings=<?=$advancedcamsettings
?>&configureSource=<?=$configuresource?>&disableVideo=<?=$disabledVideo?>&disableSound=<?=$disabledSound
?>&disableUploadDetection=<?=$disableuploaddetection?>&background_url=<?=$background_url?>&autoViewCams=<?=$autoviewcams
?>&layoutCode=<?=urlencode($row[layoutcode])?>&fillWindow=<?=$fillwindow?>&filterRegex=<?=$filterRegex
?>&filterReplace=<?=$filterReplace?>&writeText=<?php echo $write_text?>&floodProtection=<?php echo $fp?>&regularWatch=<?php echo $rw?>&newWatch=<?php echo $nw?>&privateTextchat=<?php echo $pt ?><?php
if($adlink)
echo "&ws_ads=$adlink&adsTimeout=$adstimeout&adsInterval=$adsinterval";
?>&statusInterval=<?php echo variable_get('vconf_status',10000)?><?php
echo "&panelRooms=$pr&panelUsers=$pu"
?>&loadstatus=1