<?php

require('api/routeros_api.class.php');
require('includes/functions.inc.php');
require('includes/capsman.inc.php');


if (isset($_GET['action'], $_GET['ip'])) {

  $api_caps = new RouterosAPI();
  $api_caps->debug = true;
  
  $IP_CAPS = $_GET['ip'];
  switch ($_GET['action'])
  {
    case "reboot":
   
      if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {
        $array = $api_caps->comm("/interface/ethernet/print",
                           array(".proplist"=>".id,mac-address"));
        for ($i=0; $i<count($array); $i++) {
          if ( $array[$i]['mac-address'] == $ap_mac) {
            $api_caps->comm("/system/reboot",
                      array(".id" => $array[$i][".id"]));
          }
        }
        $api_caps->disconnect();
      }
      break;
    default:
      echo "";
      break;  
  }
}

?>
