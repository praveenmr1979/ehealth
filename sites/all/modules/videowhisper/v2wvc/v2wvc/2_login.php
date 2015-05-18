<?php
chdir("../../../../../..");


define('DRUPAL_ROOT', getcwd());


require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
$_SERVER['SCRIPT_NAME'] = str_replace(variable_get('v2wvc_path', ''), '/', $_SERVER['SCRIPT_NAME']);

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
global $user;
error_reporting(0);

$username = $_COOKIE[user_name];
$room = $_GET['room_name'];
$loggedin = 1;




$uid = $user->uid;


$vls_uid = $_COOKIE['v2wvc_uid'];
$vls_hash = $_COOKIE['v2wvc_hash'];


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



//$username=preg_replace("/[^0-9a-zA-Z_]/","-",$username);
$ff = db_query("select * from  {vwrooms_users} where id=? and hash=? and room=? order by o desc", array($vls_uid, $vls_hash, $room))->fetchObject();
$user = user_load($ff->uid);
$uid = $user->id;
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


  if ($fr->uid) { //costinfo
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
      $o = $user;
      $ctx = addslashes("v2wvc $user->name $room");



      if ($fr->ownersroomcost) {
        $comment = t('Owners room cost for @r', array('@r' => $room));

        $xuu = db_query("select tid from {vwcredits_transaction} where type='ownersroomcost' and room_nid=? and applied=0", array($rid));
        $x = $xuu->fetchField();

        if (!$x) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("insert into {vwcredits_transaction} set  pts=$time,tts=0,uid=?,room_nid=?,type='ownersroomcost' ,context='$ctx ownersroomcost',trans_type='D',trans_time=now(),comment=?", array($user->uid, $rid, $comment));
        }
      }
      $ctx = addslashes("v2wvc $user->name $room");

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
      $ctx = addslashes("v2wvc $own->name $room");

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
        $ctx = addslashes("v2wvc roomcost $user->name $room $own->name");

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
  //costinfo

  else {

  }

}
//credits.
else {




}

$uname = $ff->uname;









$username = $uname;


if (!$room || !$uname) {
  $loggedin = 0;
  $message = urlencode("<a href=\"index.php\">You need a cookie enabled browser!  </a>");
}
else {
  $myroom = db_query("SELECT * FROM {v2wvc_rooms} WHERE room = :room", array(':room' => array($room)));
  if ($myroom !== false) {
    $row = $myroom->fetchAssoc();
  }
  else {
    $loggedin = 0;
    $message = urlencode("Room $room is not available!");
  }
}
$found = 0;
$mods = explode(",", $row['moderators']);
if ($user->uid && (in_array($user->mail, $mods) || in_array($user->name, $mods) || $owner)) {

  $found = 1;
}

$room = preg_replace("/[^0-9a-zA-Z_]/", "-", $room);
$rtmp_server = 0;
if ($paidroom) {

  $rtmp_server = trim(variable_get('v2wvc_paidrtmp3', ""));

}
if (!$rtmp_server) {
  $rtmp_server = variable_get('v2wvc_rtmp3', "rtmp://server-domain-or-ip/videowhisper");
}
if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $message = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/v2wvc\">Make sure module is enabled and check admin settings for Drupal, Administer > 2 Way Video Chat.</a>");
}
$rtmfp_server = variable_get('v2wvc_rtmfp3', "rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/");
$rtmp_amf = variable_get('v2wvc_amf3', "AMF3");
$bufferLive = variable_get('v2wvc_bufferlive3', "0.5");
$bufferFull = variable_get('v2wvc_bufferfull3', "0.5");
$bufferLivePlayback = variable_get('v2wvc_bufferliveplayback3', "0.2");
$bufferFullPlayback = variable_get('v2wvc_bufferfullplayback3', "0.5");

$disablebandwidthdetection = variable_get('v2wvc_disablebandwidthdetection3', "1");
$limitbybandwidth = variable_get('v2wvc_limitbybandwidth3', "1");
$autoSnapshots = variable_get('v2wvc_autosnapshots3', "1");
$snapshotsTime = variable_get('v2wvc_snapshotstime3', "20000");
$adServer = variable_get('v2wvc_adserver3', "2_ads.php");
$adsInterval = variable_get('v2wvc_adsinterval3', "600000");
$adsTimeout = variable_get('v2wvc_adstimeout3', "20000");
$requestSnapshot = variable_get('v2wvc_requestsnapshot3', "1");

$filterRegex = $row[filterregex];
$filterReplace = $row[filterreplace];
$verbose = $row[verbose];




$welcome = $row[welcome];
$camheight = $row[camheight];
$camwidth = $row[camwidth];
$camfps = $row[camfps];
$micrate = $row[micrate];
$cambandwidth = $row[cambandwidth];
$cammaxbandwidth = $row[cammaxbandwidth];
//$verbose=$row->verbose;




$emoticons = '0';
if (user_access('use emoticons', $user)) {
  $emoticons = ($row[emoticons] == 1);
}
$enablenext = '0';
if (user_access('use enablenext', $user)) {
  $enablenext = ($row[enablenext] == 1);
}
$enablesoundfx = '0';
if (user_access('use enablesoundfx', $user)) {
  $enablesoundfx = ($row[enablesoundfx] == 1);
}
$showcamsettings = '0';
if (user_access('use showcamsettings', $user)) {
  $showcamsettings = ($row[showcamsettings] == 1);
}
$configureconnection = '0';
if (user_access('use configureconnection', $user)) {
  $configureconnection = ($row[configureconnection] == 1);
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
$showtextchat = '0';
if (user_access('use showtextchat', $user)) {
  $showtextchat = ($row[showtextchat] == 1);
}
$enableserver = '0';
if (user_access('use enableserver', $user)) {
  $enableserver = ($row[enableserver] == 1);
}
$sendtextchat = '0';
if (user_access('use sendtextchat', $user)) {
  $sendtextchat = ($row[sendtextchat] == 1);
}
$showtimer = '0';
if (user_access('use showtimer', $user)) {
  $showtimer = ($row[showtimer] == 1);
}
$enablep2p = '0';
if (user_access('use enablep2p', $user)) {
  $enablep2p = ($row[enablep2p] == 1);
}
$enablebuzz = '0';
if (user_access('use enablebuzz', $user)) {
  $enablebuzz = ($row[enablebuzz] == 1);
}

$day = date("y-M-j", REQUEST_TIME);
$chat = "uploads/$room/Log$day.html";
$chatlog = "The transcript of this conversation, including snapshots is available at <U><A HREF=\"$chat\" TARGET=\"_blank\">$chat</A></U>.";

if (!$welcome) {
  $welcome = "Welcome to $room! This will try to use P2P video streaming if possible between peers and stream trough server if that's not possible: use connection button to toggle if available. There is a next button that can lead to a different room if enabled. High quality snapshots of other person can be taken on request. $chatlog";
}

//sound and video

//verboseLevel (higher reports more to user):
//0 = Nothing
//1 = Failure
//2 = Warning / Recoverable Failure
//3 = Success
//4 = Action
if ($message) {
  $msg = $message;
}


if ($found == 1) {
  if (user_access('use emoticons', $user)) {
    $emoticons = $row[emoticons];
  }
  if (user_access('use enablenext', $user)) {
    $enablenext = $row[enablenext];
  }
  if (user_access('use enablesoundfx', $user)) {
    $enablesoundfx = $row[enablesoundfx];
  }
  if (user_access('use showcamsettings', $user)) {
    $showcamsettings = $row[showcamsettings];
  }
  if (user_access('use configureconnection', $user)) {
    $configureconnection = $row[configureconnection];
  }
  if (user_access('use configuresource', $user)) {
    $configuresource = $row[configuresource];
  }
  if (user_access('use enabledsound', $user)) {
    $enabledsound = $row[enabledsound];
  }
  if (user_access('use enabledvideo', $user)) {
    $enabledvideo = $row[enabledvideo];
  }
  if (user_access('use showtextchat', $user)) {
    $showtextchat = $row[showtextchat];
  }
  if (user_access('use enableserver', $user)) {
    $enableserver = $row[enableserver];
  }
  if (user_access('use sendtextchat', $user)) {
    $sendtextchat = $row[sendtextchat];
  }
  if (user_access('use showtimer', $user)) {
    $showtimer = $row[showtimer];
  }
  if (user_access('use enablep2p', $user)) {
    $enablep2p = $row[enablep2p];
  }
  if (user_access('use enablebuzz', $user)) {
    $enablebuzz = $row['enablebuzz'];
  }






}
$disabledSound = 0;
if (!$enabledsound) {
  $disabledSound = 1;
}
$disabledVideo = 0;
if (!$enabledvideo) {
  $disabledVideo = 1;
}




?>fixOutput=decoy&server=<?=urlencode($rtmp_server)?>&serverAMF=<?=$rtmp_amf?>&serverRTMFP=<?=urlencode($rtmfp_server)
?>&serverGroup=VideoWhisper&room=<?=urlencode($room)?>&welcome=<?=urlencode($welcome);
?>&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>>&username=<?=$username?>&msg=<?=$msg?>&loggedin=<?=$loggedin?>&showTimer=<?=$showtimer
?>&showCredit=1&disconnectOnTimeout=0&camWidth=<?=$row[camwidth]?>&camHeight=<?=$row[camheight]?>&camFPS=<?=$row[camfps]?>&micRate=<?=$row[micrate]?>&camBandwidth=<?=$row[bandwidth]?>&limitByBandwidth=<?=$limitbybandwidth
?>&showCamSettings=<?=$showcamsettings?>&camMaxBandwidth=<?=$row[maxbandwidth]?>&disableBandwidthDetection=<?=$disablebandwidthdetection
?>&verboseLevel=<?=$row[verbose]?>&disableVideo=<?=$disableVideo?>&disableSound=<?=$disableSound?>&bufferLive=<?=$bufferLive?>&bufferFull=<?=$bufferFull?>&bufferLivePlayback=<?=$bufferLivePlayback
?>&bufferFullPlayback=<?=$bufferFullPlayback?>&filterRegex=<?=urlencode($filterRegex)?>&filterReplace=<?=urlencode($filterReplace)
?>&disableEmoticons=<?=$emoticons?>&writeText=1&enableText=1&showTextChat=<?=$showtextchat?>&sendTextChat=<?=$sendtextchat
?>&enableP2P=<?=$enablep2p?>&enableServer=<?=$enableserver?>&configureConnection=<?=$configureconnection
?>&configureSource=<?=$configuresource?>&enableNext=<?=$enablenext?>&enableBuzz=<?=$enablebuzz
?>&enableSoundFx=<?=$enablesoundfx?>&requestSnapshot=<?=$requestSnapshot?>&autoSnapshots=<?=$autoSnapshots
?>&snapshotsTime=<?php echo $snapshotsTime;
if($adServer&&!$row['disablead']):?>&adServer=<?=urlencode($adServer)?>&adsInterval=<?=$adsInterval?>&adsTimeout=<? echo $adsTimeout;
endif;?>&loadstatus=1