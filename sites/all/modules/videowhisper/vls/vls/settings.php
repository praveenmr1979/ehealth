<?php

$rtmp_server = "rtmp://localhost:1935/videowhisper";
// rtmp://your-server-ip-or-domain/application


$rtmp_amf = "Red5";
// AMF3 : Red5, Wowza, FMIS3, FMIS3.5
// AMF0 : FCS1.5, FMS2
// blank for flash default

$tokenKey = "VideoWhisper";
// This can be used to secure access as configured in RTMP server settings (secureTokenSharedSecret).

$ban_names=Array("ban_name1", "ban_name2");
//ban channel or user names

$db  = 'hoteldb';
$dbuser='root';
$dbpass='';
$dbhost='localhost';
$db_prefix = '';

?>