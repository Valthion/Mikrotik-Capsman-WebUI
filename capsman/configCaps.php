<?php

require('api/routeros_api.class.php');
require('includes/functions.inc.php');
require('includes/capsman.inc.php');

echo "start";
if ( isset($_POST['AP']) && isset($_POST['channelf']) && isset($_POST['txpower']) && isset($_POST['ssid'])  && isset($_POST['pass']) ) {
echo "masuk";
  $name = $_POST['AP'];
  $channel = $_POST['channelf'];
  // $width = $_POST['channelw'];
  $power = $_POST['txpower'];
  $ssid = $_POST['ssid'];
  $pass = $_POST['pass'];

  $api_caps = new RouterosAPI();
  $api_caps->debug = true;
  
    if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {
  
    $array = $api_caps->comm("/caps-man/interface/print",
             array(".proplist"=>".id,name,master-interface,current-channel",
                   "?master"=>"true"));
    for ($i=0; $i<count($array); $i++) {
      if ($array[$i]['name'] == $name) {
        if(strlen($pass)>0){
          $res = $api_caps->comm("/caps-man/interface/set",
              array(".id" => $array[$i]['.id'],
                    "channel" => $channel,
                    "configuration.ssid" => $ssid,
                    "security.authentication-types" => 'wpa2-psk',
                    "security.passphrase" => $pass,
                    "channel.tx-power" => $power));
        }else{
          $res = $api_caps->comm("/caps-man/interface/set",
              array(".id" => $array[$i]['.id'],
                    "channel" => $channel,
                    "configuration.ssid" => $ssid,
                    "channel.tx-power" => $power));
          $res = $api_caps->comm("/caps-man/interface/unset",
                  array("value-name"=>"security.passphrase",
                   ".id"=>$array[$i]['.id']));
          $res = $api_caps->comm("/caps-man/interface/unset",
                   array("value-name"=>"security.authentication-types",
                    ".id"=>$array[$i]['.id']));
        }
      }
    }
    }

    $api_caps->disconnect();
    exit(0);
  }

?>
