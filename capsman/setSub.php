<?php

require('api/routeros_api.class.php');
require('includes/functions.inc.php');
require('includes/capsman.inc.php');


if (isset($_GET['action'], $_GET['mac'])) {

  $api_caps = new RouterosAPI();
  $api_caps->debug = true;
  
  $mac = $_GET['mac'];
  switch ($_GET['action'])
  {
    case "reconnect":
   
      if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {
        $array = $api_caps->comm("/caps-man/registration-table/print",
                           array(".proplist"=>".id,mac-address"));
        for ($i=0; $i<count($array); $i++) {
          if ( $array[$i]['mac-address'] == $mac) {
            $api_caps->comm("/caps-man/registration-table/remove",
                      array(".id" => $array[$i][".id"]));
          }
        }
        $api_caps->disconnect();
      }
      break;
    case "block":
      if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {
        $pos = 0;
        //$api_caps->comm("/caps-man/access-list/add", 
        //array("mac-address" => $mac,
        //      "action"=>"reject"));
        $api_caps->comm("/caps-man/access-list/add", 
        array("mac-address" => $mac,
              "action"=>"reject",
              "place-before"=>$pos));
        $api_caps->disconnect();
      }
      break;
    case "unblock":
      if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {

        $array = $api_caps->comm("/caps-man/access-list/print",
                           array(".proplist"=>".id,mac-address",
                                 "?action"=>"reject"));
        for ($i=0; $i<count($array); $i++) {
          if ( !empty($array[$i]['mac-address']) ) {
            if ( $array[$i]['mac-address'] == $mac) {
              $api_caps->comm("/caps-man/access-list/remove",
                        array(".id" => $array[$i][".id"]));
            }
          }
        }  
      }
      break;      
    default:
      echo "";
      break;  
  }
}

?>
