<?php

require('api/routeros_api.class.php');
require('includes/functions.inc.php');
require('includes/mysql.inc.php');
require('includes/capsman.inc.php');

$api_gw = new RouterosAPI();
$api_gw->debug = false;

if ($api_gw->connect(IP_GW, API_USR, API_PASS)) {
  $array = $api_gw->comm("/ip/arp/print",
          array(".proplist"=>"address,mac-address"));
  for ($i=0; $i<count($array); $i++) {
    $mac = $array[$i]['mac-address'];
    $ip = !empty($array[$i]['address'])? $array[$i]['address'] : '0.0.0.0' ;
    $arplist[$mac] = $ip;
  }
  unset($array);
  $api_gw->disconnect();
}

$api_caps = new RouterosAPI();
$api_caps->debug = false;

if ($api_caps->connect(IP_CAPS, API_USR, API_PASS)) {
  $array = $api_caps->comm("/caps-man/registration-table/print",
            array(".proplist"=>"interface,ssid,mac-address,tx-rate,rx-rate,rx-signal,uptime,packets,bytes"));

  // sort array by interface
  // Ref: http://php.net/manual/en/function.array-multisort.php
  foreach ($array as $key => $row) {
    $interface[$key]  = $row['interface'];
    $ssid[$key]  = $row['ssid'];
  }
  array_multisort($interface, SORT_ASC, $ssid, SORT_ASC, $array);

  $length = count($array);

  include('includes/selectRefresh.inc.php');
  echo <<<EOF
<table class="subscribers">
<tr>
  <th>Name</th>
  <th>User</th>
  <th>IP Address</th>
  <th>SSID</th>
  <th style="width:6%">AP</th>
  <th style="width:5%">Signal</th>
  <th style="width:6%">Tx</th>
  <th style="width:6%">Rx</th>
  <th style="width:12%">Uptime</th>
  <th style="width:12%">Actions</th>
</tr>
EOF;

  for ($i=0; $i<$length; $i++) {
    $interface = $array[$i]['interface'];
    $ssid = $array[$i]['ssid'];
    $mac = $array[$i]['mac-address'];
    //$rx_rate = $array[$i]['rx-rate'];
    $signal = $array[$i]['rx-signal'];
    list($tx,$rx) = explode (',',$array[$i]['bytes']);
    $uptime = uptime($array[$i]['uptime']);
    $ip_sub = !empty($arplist[$mac])? $arplist[$mac] : '0.0.0.0';
    // default value if not found at database
    $username = 'Unidentified User';
    $hostname = 'Noname';

    $q = "SELECT hostname,user FROM hostname WHERE mac='$mac'";
    $r = @mysqli_query($dbc, $q);
    if ($r) {
      $num = @mysqli_num_rows($r);
      if ($num == 1) { // Match was made.
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        $username = $row['user'];
        $hostname = $row['hostname'];
      }
    }
    echo "<tr>";
    echo "<td><a href=\"javascript:void(0)\" onclick=\"showSub('$mac','$ip_sub');\" title=\"$mac\">$hostname</a></td>
    <td>$username</td>
    <td>$ip_sub</td>
    <td>$ssid</td>
    <td>$interface</td>
    <td>$signal</td>
    <td>".formatn($tx)."</td>
    <td>".formatn($rx)."</td>
    <td>$uptime</td>
    <td style=\"text-align:center;\">
    <button style=\"margin:0 0;\" onClick=\"actions('".$_SERVER["PHP_SELF"]."','reconnect','$mac');\"><span class=\"ui-icon ui-icon-refresh\"></span></button>
    <button style=\"margin:0 0;\" onClick=\"actions('".$_SERVER["PHP_SELF"]."','block','$mac');\"><span class=\"ui-icon ui-icon-closethick\"></span></button></td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "<p class=\"p1\">Connected users $length</p>\n";
 
  $api_caps->disconnect();
}

?>
