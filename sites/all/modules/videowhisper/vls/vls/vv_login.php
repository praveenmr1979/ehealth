<?php
chdir("../../../../../..");
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/' . './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

$_SERVER['SCRIPT_NAME'] = str_replace(variable_get('vls_path', ''), '/', $_SERVER['SCRIPT_NAME']);

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
error_reporting(0);

$username = $_COOKIE['vvusername'];
if (!$username) {
  $username = "VV" . base_convert((REQUEST_TIME -1224350000) . rand(0, 10), 10, 36);
}

$room = $_COOKIE['r'];

$msg = "";
$loggedin = 1;


$picture='';
global $user;
$uid=$user->uid;
if($uid){
$us=db_select('users','u')->fields('u',array('picture'))->condition('u.uid',(int)$uid)->execute();
if($us){
	$us=$us->fetch();
	$file=file_load($us->picture);
	$picture=file_create_url($file->uri);
}
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

if (!$room) {
  $loggedin = 0;
  $msg = urlencode("<a href=\"index.php\">You need a cookie enabled browser!</a>");
}
else {
  $myroom = db_query("SELECT * FROM {vls_rooms} WHERE room = :room", array(':room' => array($room)));
  if ($myroom !== false) {
    $row = $myroom->fetchAssoc();
  }
  else {
    $loggedin = 0;
    $msg = urlencode("Room $room is not available!");
  }
}

$rtmp_server = variable_get('vls_rtmp3', "rtmp://server-domain-or-ip/videowhisper");
if ($rtmp_server == "rtmp://server-domain-or-ip/videowhisper") {
  $loggedin = 0;
  $msg = urlencode("RTMP server not configured!<BR><a href=\"../admin/settings/vconf\">Make sure module is enabled and check admin settings for Drupal, Administer > 2 Way Video Chat.</a>");
}
$rtmp_amf = variable_get('vls_amf3', "AMF3");
$bufferLive = variable_get('vls_bufferlive4', "0.5");
$bufferFull = variable_get('vls_bufferfull4', "0.5");
$tokenKey = variable_get('vls_tokenkey3', "0.2");

//replace bad words or expressions
$filterRegex = urlencode($row[filterregex]);
$filterReplace = urlencode($row[filterregex]);

//fill your layout code between <<<layoutEND and layoutEND;
$layoutCode = $row[layoutcode];
$welcome = $row[welcome2];
$fillwindow = $row[fillwindow];
$floodprotection = $row[floodprotection2];
$offlinemessage = $row[offlinemessage];

$write_text = '1';
if (user_access('use write_text2', $user)) {
  $write_text = $row[write_text2];
}
$enabledchat = '1';
if (user_access('use enabledchat', $user)) {
  $enabledchat = $row[enabledchat];
}
$enabledvideo = '1';
if (user_access('use enabledvideo', $user)) {
  $enabledvideo = $row[enabledvideo];
}
$enabledusers = '1';
if (user_access('use enabledusers', $user)) {
  $enabledusers = $row[enabledusers];
}
$showtimer = '1';
if (user_access('use showtimer', $user)) {
  $showtimer = $row[showtimer];
}

if ($row['disablead']) {
  $adlink = '';
}
if (variable_get('vls_adserver', '')) {
  $adlink = urlencode(variable_get('vls_adserver', ''));
}
$adstimeout = variable_get('vls_adtimeout', '15000');
$adsinterval = variable_get('vls_adinterval', '240000');


if ($adlink) {
  $adstr = "&ws_ads=$adlink&adsTimeout=$adstimeout&adsInterval=$adsinterval";
}
$rtmfp_server=variable_get('vls_rtmfp', "rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/");

$sgr=trim(variable_get('vls_sgroup', "VideoWhisper"));

$pgr=trim(variable_get('vls_pgroup', $sgr));




?>serverRTMFP=<?php echo urlencode($rtmfp_server)?>&p2pGroup=<?php $pgr?>&supportRTMP=1&supportP2P=1&alwaysRTMP=0&alwaysP2P=0&serverGroup=<?php echo $sgr?><?php
?>&server=<?=urlencode($rtmp_server)?>&serverAMF=<?=$rtmp_amf?>&tokenKey=<?=$tokenKey?>&bufferLive=<?$bufferLive?>&bufferFull=<?$bufferFull
?>&welcome=<?=urlencode($welcome)?>&username=<?=$username?>&userType=0&msg=<?=$msg?>&visitor=1&loggedin=<?=$loggedin?>&showCredit=1<?php
?>&disconnectOnTimeout=1&offlineMessage=<?=urlencode($offlinemessage)?><?php echo $adstr?>&writeText=<?php echo $write_text?>&loadstatus=1&userPicture=<?=$userPicture?>&userLink=<?=$userLink?>