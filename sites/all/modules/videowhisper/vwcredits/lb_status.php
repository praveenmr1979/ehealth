<?php
/*
 POST Variables:
 u=Username
 s=Session, usually same as username
 r=Room
 ct=session time (in milliseconds)
 lt=last session time received from this script in (milliseconds)
 cam, mic = 0 none, 1 disabled, 2 enabled
 */




$fc = db_query("select * from {vwcredits_cost} where room_nid=".(int) $fc1->room_nid)->fetchObject();

if (!$fc1->uid || !$fc1->room_nid) {

  $disconnect = "No User or room";

}

else if (!$fc->room_nid) {

  ///	if(!$fc1->room_nid)
  ///	$disconnect="No room";


}
else if ($fc->ownersroomcost == 0) {



}
else if ($fc->ownersroomcost) {


db_query("lock tables {vwcredits_transaction} write,{vwcredits_tmpcr} write,{vwcredits_credit} write");

  $ft = db_query("select * from {vwcredits_tmpcr}   where uid= ".(int) $fc->uid)->fetchObject();

  $ftt = db_query("select * from {vwcredits_transaction}   where uid=$fc1->uid and room_nid=$fc->room_nid and type='ownersroomcost'  and applied=0")->fetchObject();




  if (!$ft->uid || !$ftt->tid || $ft->uid != $fc->uid) {

    $disconnect = "No user data.";

  }
  else {
    $time = REQUEST_TIME;
    $p = $p1 = $time -$ftt->pts;
    if ($ftt->tts == 0) {
      $p = $p1 -$fc->graceperiod;
    }


    if ($p > 0) {
      $cost = $fc->ownersroomcost * ($p) / 60;


      if ($cost > $ft->bal) {

        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_tmpcr} set bal=0 where uid=$ft->uid");
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_credit} set bal=bal-$ft->bal where uid=$ft->uid");
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_transaction} set credit=credit+$ft->bal,applied=1,app_time=now(),pts=$time,tts=tts+:d1 where tid=:d2", array(':d1'=>$time -$ftt->pts,':d2'=> $ftt->tid));


        db_query("update {vwcredits_transaction} set applied=1,app_time=now() where room_nid=$fc->room_nid and type in ('roomcost','ownerscost','ownersgain','ownersroomcost') and applied=0");


        $disconnect = "Your balance has expired.";

      }
      else {




        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_tmpcr} set bal=bal-$cost where uid=$ft->uid");
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_credit} set bal=bal-$cost where uid=$ft->uid");
        //db_query("update {vwcredits_transfer}  set  ats=$ct, lts=lts+$p1,acost=acost+$cost,pts=$time,credit=credit+$gain  ,applied=0  where  tid=%d",$ftt->tid);
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_transaction} set credit=credit+$cost,pts=$time,tts=tts+:d1 where tid=:d2", array(':d1'=>$time -$ftt->pts, ':d2'=>$ftt->tid));

        // TODO Please convert this statement to the D7 database API syntax.
      //l  db_query("update {vwcredits_transaction} set pts=$time where uid=$ft->uid and type='ownerscost' and room_nid=$fc1->room_nid and applied=0");
        // TODO Please convert this statement to the D7 database API syntax.
     //l  db_query("update {vwcredits_transaction} set pts=$time where uid=$ft->uid and type='ownersgain' and room_nid=$fc1->room_nid and applied=0");



      }



    }
  }
  //not disconnected
  ///db_query("insert into log set log='$p cost $cost user $fc->uid room $fc->room_nid tid $ftt->tid disconnct $disconnect uid $ft->uid' , file='lb'");
db_query("unlock tables");
 // vwrooms_unlock_tables();

}






?>
