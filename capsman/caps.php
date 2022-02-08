<?php

require('api/routeros_api.class.php');
require('includes/functions.inc.php');
require('includes/capsman.inc.php');

$api_gw = new RouterosAPI();
$api_gw->debug = false;

if ($api_gw->connect(IP_GW, API_USR, API_PASS)) {
  $array = $api_gw->comm("/ip/dhcp-server/lease/print",
          array(".proplist"=>"active-address,host-name",
                "?disabled"=>"false",
                "?status"=>"bound"));
  for ($i=0; $i<count($array); $i++) {
    $hostname = !empty($array[$i]['host-name'])? $array[$i]['host-name'] : 'N/A';
    $arplist[$hostname] = $array[$i]['active-address'];
  }
  unset($array);
  //$api_gw->disconnect();
}

$api_caps = new RouterosAPI();
$api_caps->debug = false;

//sistem reboot
if (isset($_GET['action'], $_GET['ip'])) {

  $api_caps = new RouterosAPI();
  $api_caps->debug = true;
  
  $target = $_GET['ip'];
  switch ($_GET['action'])
  {
    case "reboot":
   echo $target;
      if ($api_caps->connect($target, API_USR, API_PASS)) {
        $api_caps->comm("/system/reboot");
        header("location: index.php", true, 301);
        exit();
      }
      break;
      $api_caps->disconnect(); 
  }
  //For reboot, make sure that usrapi has full access!!
}

if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {

  // create array $ap_name "ap_name"=>"remote-cap-identity"
  // if cap shutdown then it doesn't show up
  $array = $api_caps->comm("/caps-man/radio/print",
             array(".proplist"=>"interface,remote-cap-identity"));
  for ($i=0; $i<count($array); $i++) {
    $interface = $array[$i]['interface'];
    $apIdentity[$interface] = $array[$i]['remote-cap-identity'];
  }
  unset($array);

  $array_conf = $api_caps->comm("/caps-man/configuration/print",
  array(".proplist"=>"name,ssid"));
    for ($i=0; $i<count($array_conf); $i++) {
    $name_conf = $array_conf[$i]['name'];
    $config_slave[$name_conf] = $array_conf[$i]['ssid'];
    }

  $array = $api_caps->comm("/caps-man/interface/print",
           array(".proplist"=>"name,configuration,configuration.ssid,channel.tx-power", "?master"=>"true"));
  for ($i=0; $i<count($array); $i++) {
    $name = $array[$i]['name'];
    $config[$name] = isset($array[$i]['configuration.ssid'])?$array[$i]['configuration.ssid']:$config_slave[$array[$i]['configuration']];
  }

  // search for ap master
  $master = $api_caps->comm("/caps-man/interface/print",
           array(".proplist"=>"name,master-interface,configuration,configuration.ssid,current-channel,l2mtu,inactive",
                 "?master"=>"true"));

  $m_length = count($master); // num of AP Master

  $connectedAP = $m_length;
  for ($i=0; $i<$m_length; $i++) {
    $name = $master[$i]['name'];
    $aps[$i]['name'] = $name;
    $aps[$i]['channel'] = $master[$i]['current-channel'];
    $aps[$i]['mtu'] = $master[$i]['l2mtu'];
    $configuration = $master[$i]['configuration'];
    $aps[$i]['inactive'] = $master[$i]['inactive'];
    if ($aps[$i]['inactive'] == 'false') {
      $remote_cap_identity = $apIdentity[$name];
      $aps[$i]['ip'] = $arplist[$remote_cap_identity];
    }
    $aps[$i]['ssid'] = isset($master[$i]['configuration.ssid'])?$master[$i]['configuration.ssid']:$config_slave[$array[$i]['configuration']];
    $aps[$i]['power'] = str_replace("dBm)","",explode(",",str_replace("(",",",explode("/",$master[$i]['current-channel'])[2]))[1]);
    $aps[$i]['count'] = 0;
    $aps[$i]['count-tot'] = 0;
    $aps[$i]['rx-byte'] = 0;
    $aps[$i]['rx-tot-byte'] = 0;
    $aps[$i]['tx-byte'] = 0;
    $aps[$i]['tx-tot-byte'] = 0;
  }

  // search for ap slave
  $slave = $api_caps->comm("/caps-man/interface/print",
             array( ".proplist"=>"name,master-interface,configuration,configuration.ssid",
                    "?master"=>"false"));
  $s_length = count($slave);

  for ($j=0; $j<$m_length; $j++) {
    $k = 0;
    for ($i=0; $i<$s_length; $i++) {
      $name = $slave[$i]['name'];
      $master = $slave[$i]['master-interface'];
      if ($master == $aps[$j]['name']) {
        $aps[$j]['slaves'][$k]['name'] = $name;
        $configuration = $slave[$i]['configuration'];
        $aps[$j]['slaves'][$k]['ssid'] = isset($slave[$i]['configuration.ssid'])? $slave[$i]['configuration.ssid']:$config_slave[$configuration];
        $aps[$j]['slaves'][$k]['count'] = 0;
        $aps[$j]['slaves'][$k]['rx-byte'] = 0;
        $aps[$j]['slaves'][$k]['tx-byte'] = 0;
        $k++;
      }
    }
  }

  // count subscriber
  $array = $api_caps->comm("/caps-man/registration-table/print",
                    array(".proplist"=>"interface"));
  for ($i=0; $i<count($array); $i++) {
    $name = $array[$i]['interface'];
    for ($j=0; $j<$m_length; $j++) {
      if ($name == $aps[$j]['name']) {
        $aps[$j]['count']++;
        $aps[$j]['count-tot']++;
      }
      $n_slaves = count ($aps[$j]['slaves']);
      for ($k=0; $k<$n_slaves; $k++) {
        if ($name == $aps[$j]['slaves'][$k]['name']) {
          $aps[$j]['slaves'][$k]['count']++;
          $aps[$j]['count-tot']++;
        }
      }
    } 
  }

  // count tx and rx bytes
  $array = $api_caps->comm("/interface/print",
           array(".proplist"=> "name,rx-byte,tx-byte",
                 "?type"=>"cap"));
  for ($i=0; $i<count($array); $i++) {
    $name = $array[$i]['name'];
    for ($j=0; $j<$m_length; $j++) {
      if ($name == $aps[$j]['name']) {
        $aps[$j]['rx-byte'] = $array[$i]['rx-byte'];
        $aps[$j]['rx-tot-byte'] += $array[$i]['rx-byte'];
        $aps[$j]['tx-byte'] = $array[$i]['tx-byte'];
        $aps[$j]['tx-tot-byte'] += $array[$i]['tx-byte'];
      }
      $n_slaves = count ($aps[$j]['slaves']);
      for ($k=0; $k<$n_slaves; $k++) {
        if ($name == $aps[$j]['slaves'][$k]['name']) {
          $aps[$j]['slaves'][$k]['rx-byte'] = $array[$i]['rx-byte'];
          $aps[$j]['rx-tot-byte'] += $aps[$j]['slaves'][$k]['rx-byte'];
          $aps[$j]['slaves'][$k]['tx-byte'] = $array[$i]['tx-byte'];
          $aps[$j]['tx-tot-byte'] += $aps[$j]['slaves'][$k]['tx-byte'];
        }
      }
    }
  }

  $api_caps->disconnect();
}

include('includes/selectRefresh.inc.php');

echo <<<EOF
<table class="aps">
<tr>
  <th>AP</th>
  <th>IP Address</th>
  <th>SSID</th>
  <th>Channel</th>
  <th>Clients</th>
  <th>Tx</th>
  <th>Rx</th>
  <th>MTU</th>
  <th>Action</th>
</tr>
EOF;
for ($i=0; $i<$m_length; $i++)
{
  $ap_name = $aps[$i]['name'];
  $ap_channel = $aps[$i]['channel'];
  $ap_mtu = $aps[$i]['mtu'];
  $ap_subs = $aps[$i]['count-tot'];
  $ap_rx_tot = $aps[$i]['rx-tot-byte'];
  $ap_tx_tot = $aps[$i]['tx-tot-byte'];
  $ap_power = $aps[$i]['power'];
  echo "<tr class=\"master\">";
  if ($aps[$i]['inactive'] == 'false') {
    $ap_ip = $aps[$i]['ip'];
    echo "<td><a href=\"javascript:void(0)\" onClick=\"configAP('$ap_name','$ap_ip','$ap_channel','$ap_power','$ap_subs','$ap_rx_tot','$ap_tx_tot');\">".$aps[$i]['name']."</a></td>
    <td>".$aps[$i]['ip']."</td>
    <td>".$aps[$i]['ssid']."</td>
    <td>".$aps[$i]['channel']."</td>
    <td>".$aps[$i]['count']."</td>
    <td>".formatn($aps[$i]['tx-byte'])."</td>
    <td>".formatn($aps[$i]['rx-byte'])."</td>
    <td>".$aps[$i]['mtu']."</td>
    <td style=\"text-align:center;\">
    <form action=\"action\">
      <a href=\"caps.php?action=reboot&ip=".$ap_ip."\" class=\"btn btn-info\" role=\"button\">Restart</a>
    </form>
    </td>\n";
  } else {
    echo "<td style=\"color:gray;\">".$aps[$i]['name']."</td><td></td><td style=\"color:gray;\">".
$aps[$i]['ssid']."</td><td style=\"color:gray;\">".$aps[$i]['channel']."</td><td style=\"color:gray;\">".
$aps[$i]['count']."</td><td style=\"color:gray;\">".formatn($aps[$i]['tx-byte']).
"</td><td style=\"color:gray;\">".formatn($aps[$i]['rx-byte'])."</td><td style=\"color:gray;\">".$aps[$i]['mtu'].
"</td><td style=\"text-align:center;\"></td>\n";
    $connectedAP--;
  }
  echo "</tr>";
  $n_slaves = count ($aps[$i]['slaves']);
  for ($j=0; $j<$n_slaves; $j++) {
    echo "<tr>";
    if ($aps[$i]['inactive'] == 'false') {
      echo "<td colspan=2>&nbsp;&nbsp;".$aps[$i]['slaves'][$j]['name']."</td><td colspan=2>".$aps[$i]['slaves'][$j]['ssid']."</td><td>".$aps[$i]['slaves'][$j]['count']."</td><td>".formatn($aps[$i]['slaves'][$j]['tx-byte'])."</td><td colspan=3>".formatn($aps[$i]['slaves'][$j]['rx-byte'])."</td>\n";
    } else {
      echo "<td colspan=2 style=\"color:gray;\">&nbsp;&nbsp;".$aps[$i]['slaves'][$j]['name']."</td><td colspan=2 style=\"color:gray;\">".$aps[$i]['slaves'][$j]['ssid']."</td><td style=\"color:gray;\">".$aps[$i]['slaves'][$j]['count']."</td><td style=\"color:gray;\">".formatn($aps[$i]['slaves'][$j]['tx-byte'])."</td><td colspan=3 style=\"color:gray;\">".formatn($aps[$i]['slaves'][$j]['rx-byte'])."</td>\n";
    }
    echo "</tr>";
  }
}
echo "</table>\n";
echo "<div class=\"spacer\"></div>";
echo "<p class=\"p1\">Connected AP $connectedAP</p>\n";

?>
