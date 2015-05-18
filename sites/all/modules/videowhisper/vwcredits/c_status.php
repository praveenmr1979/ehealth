<?php

//$fc = db_fetch_object(db_query("select * from {vwcredits_cost} where room_nid=%d", $fur->room_nid));

//$ownbal = db_query("select bal from {vwcredits_tmpcr} where uid=%d", $fc->uid)->fetchField();

$fc = db_query("select * from {vwcredits_cost} where room_nid=".(int)$fur->room_nid)->fetchObject();

$ownbal = db_query('select bal from {vwcredits_tmpcr} where uid='. (int)$fc->uid)->fetchField();




if (!$fur->id) {
  ///$disconnect="user not found ";

}


else if (!$fc->room) {

  if (!$fur->room_nid) {

    $disconnect = "user/room not found";

  }

}
else if ($ownbal <= 0) {


  if ($fc->ownerscost > 0) {
    $disconnect = "No owners balance";
    //db_query("update {vwcredits_transaction} set applied=1,app_time=now() where room_nid=%d and type in ('roomcost','ownerscost','ownersgain','ownersroomcost') and applied=0", $fur->room_nid);
    db_query("update {vwcredits_transaction} set applied=1,app_time=now() where room_nid=$fur->room_nid  and type in ('roomcost','ownerscost','ownersgain','ownersroomcost') and applied=0");

  }


}
else {

  if ($fur->uid == $fc->uid) {
    $owner = 1;
    $trs = 'ownersroomcost';
  }
  else {
    $trs = 'roomcost';
    $owner = 0;
  }


  db_query("lock tables {vwcredits_credit} write,{vwcredits_tmpcr} write,{vwcredits_transaction} write");

  //$ft = db_fetch_object(db_query("select * from {vwcredits_tmpcr}   where uid=%d ", $fur->uid));
  $ft = db_query('select * from {vwcredits_tmpcr}   where uid=:d ', array(':d'=>$fur->uid))->fetchObject();



  // TODO Please convert this statement to the D7 database API syntax.
  ///$ftt = db_fetch_object(db_query("select * from {vwcredits_transaction}   where uid=%d and room_nid=%d and type='$trs'  and applied=0", $fur->uid, $fc->room_nid));

  $ftt = db_query("select * from {vwcredits_transaction}   where uid=$fur->uid and room_nid=$fc->room_nid and type='$trs'  and applied=0")->fetchObject();


  if (!$ft->uid || !$ftt->tid) {

    $disconnect = "No data or user disconnected.  $fur->uid , $fc->room_nid $trs";

  }
  else {


    $time = REQUEST_TIME;
    $p = $p1 = $time -$ftt->pts;
    if ($ftt->tts == 0) {
      $p = $p1 -$fc->graceperiod;
    }
    $gain = 0;
    if ($fc->uid == $ft->uid) {
      $t = "ownersroomcost";
    }
    else {
      $t = "roomcost";
    }
    ///$disconnect=" $t p $p p1 $p1 t $time";
    if ($p > 0) {
      if ($fc->uid == $ft->uid) {

        if (!$owner) {
          $disconnect = 'wrong user;';
        }

        $cost = $fc->ownersroomcost / 60;


      }
      else {

        if ($owner) {
          $disconnect = 'wrong user';
        }

        $gain = $fc->ownersgain;
        $cost = $fc->roomcost / 60;
        $ownercost = $fc->ownerscost / 60;


      }

      $cost = $cost * ($p);

      $gain1 = $ft->bal * $gain;
      $ownercost = $ownercost * $p;
      $gain = $cost * $gain;
      $noc = $gain -$ownercost;


      if ($cost > $ft->bal) {
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_tmpcr} set bal=0 where uid=$ft->uid");
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_credit} set bal=bal-$ft->bal where uid=$ft->uid");
        // TODO Please convert this statement to the D7 database API syntax.

        //db_query("update {vwcredits_transaction} set credit=credit+$ft->bal,applied=1,app_time=now(),pts=$time,tts=tts+%d where tid=%d", $time -$ftt->pts, $ftt->tid);
        db_query("update {vwcredits_transaction} set credit=credit+$ft->bal,applied=1,app_time=now(),pts=$time,tts=tts+:d1 where tid=:d2", array(':d1'=>$time -$ftt->pts,':d2'=> $ftt->tid));


        if (!$owner) {
          $noc = $gain1 -$ownercost;
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("update {vwcredits_tmpcr} set bal=bal+$noc  where uid=$fc->uid");
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("update {vwcredits_credit} set bal=bal+$noc  where uid=$fc->uid");
          // TODO Please convert this statement to the D7 database API syntax.
          //db_query("update {vwcredits_transaction} set credit=credit+$ownercost,pts=$time where uid=%d and type='ownerscost' and room_nid=%d and applied=0", $fc->uid, $fur->room_nid);
          // TODO Please convert this statement to the D7 database API syntax.
          //db_query("update {vwcredits_transaction} set credit=credit+$gain1,pts=$time where uid=%d and type='ownersgain' and room_nid=%d and applied=0", $fc->uid, $fur->room_nid);
          ///noc<0
          db_query("update {vwcredits_transaction} set credit=credit+$ownercost,pts=$time where uid=$fc->uid  and type='ownerscost' and room_nid=$fur->room_nid and applied=0");
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("update {vwcredits_transaction} set credit=credit+$gain,pts=$time where uid=$fc->uid  and type='ownersgain' and room_nid=$fur->room_nid and applied=0");



        }
        else {
        	db_query("update {vwcredits_transaction} set applied=1,app_time=now() where room_nid=$fur->room_nid and type in ('roomcost','ownerscost','ownersgain','ownersroomcost') and applied=0");

        	////          db_query("update {vwcredits_transaction} set applied=1,app_time=now() where room_nid=%d and type in ('roomcost','ownerscost','ownersgain','ownersroomcost') and applied=0", $fur->room_nid);


        }

        $disconnect = "Your balance has expired.";

      }
      else {

        $noc = $gain -$ownercost;

        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_tmpcr} set bal=bal-$cost where uid=$ft->uid");
        // TODO Please convert this statement to the D7 database API syntax.
        db_query("update {vwcredits_credit} set bal=bal-$cost where uid=$ft->uid");
        //db_query("update {vwcredits_transfer}  set  ats=$ct, lts=lts+$p1,acost=acost+$cost,pts=$time,credit=credit+$gain  ,applied=0  where  tid=%d",$ftt->tid);
        // TODO Please convert this statement to the D7 database API syntax.
        ////db_query("update {vwcredits_transaction} set credit=credit+$cost,pts=$time,tts=tts+%d where tid=%d", $time -$ftt->pts, $ftt->tid);
        db_query("update {vwcredits_transaction} set credit=credit+$cost,pts=$time,tts=tts+:d1 where tid=:d2", array(':d1'=>$time -$ftt->pts,':d2'=> $ftt->tid));


        if (!$owner) {
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("update {vwcredits_tmpcr} set bal=bal+$noc  where uid=$fc->uid");
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("update {vwcredits_credit} set bal=bal+$noc  where uid=$fc->uid");
          // TODO Please convert this statement to the D7 database API syntax.
          ///db_query("update {vwcredits_transaction} set credit=credit+$ownercost,pts=$time where uid=%d and type='ownerscost' and room_nid=%d and applied=0", $fc->uid, $fur->room_nid);
          // TODO Please convert this statement to the D7 database API syntax.
          //db_query("update {vwcredits_transaction} set credit=credit+$gain,pts=$time where uid=%d and type='ownersgain' and room_nid=%d and applied=0", $fc->uid, $fur->room_nid);

          db_query("update {vwcredits_transaction} set credit=credit+$ownercost,pts=$time where uid=$fc->uid  and type='ownerscost' and room_nid=$fur->room_nid and applied=0");
          // TODO Please convert this statement to the D7 database API syntax.
          db_query("update {vwcredits_transaction} set credit=credit+$gain,pts=$time where uid=$fc->uid  and type='ownersgain' and room_nid=$fur->room_nid and applied=0");

        }
        else {

        	db_query("update {vwcredits_transaction} set pts=$time where uid=$fc->uid  and type='ownerscost' and room_nid=$fur->room_nid and applied=0");
        	// TODO Please convert this statement to the D7 database API syntax.
        	db_query("update {vwcredits_transaction} set pts=$time where uid=$fc->uid  and type='ownersgain' and room_nid=$fur->room_nid and applied=0");

          // TODO Please convert this statement to the D7 database API syntax.
///          db_query("update {vwcredits_transaction} set pts=$time where uid=%d and type='ownerscost' and room_nid=%d and applied=0", $fc->uid, $fur->room_nid);
          // TODO Please convert this statement to the D7 database API syntax.
   ///       db_query("update {vwcredits_transaction} set pts=$time where uid=%d and type='ownersgain' and room_nid=%d and applied=0", $fc->uid, $fur->room_nid);

        }





      }



    }
    //p>0
  }
  //nodata
  db_query("unlock tables");


  //vwrooms_unlock_tables();


}

