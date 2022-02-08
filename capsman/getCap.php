<?php

require('api/routeros_api.class.php');
require('includes/functions.inc.php');
require('includes/capsman.inc.php');

if ( isset($_GET['name']) && isset($_GET['ip']) && 
     isset($_GET['channel']) && isset($_GET['power']) && 
     isset($_GET['subs']) && isset($_GET['rx_tot']) &&
     isset($_GET['tx_tot']) ) {

  $ap_name = $_GET['name'];
  $ap_ip = $_GET['ip'];
  $ap_channel = $_GET['channel'];
  $ap_power = $_GET['power'];
  $ap_subs = $_GET['subs'];
  $ap_rx_tot = formatn($_GET['rx_tot']);
  $ap_tx_tot = formatn($_GET['tx_tot']);

  $api_cap = new RouterosAPI();
  $api_cap->debug = false;

  if ($api_cap->connect($ap_ip, API_USR, API_PASS)) {
    $array_cap = $api_cap->comm("/system/resource/print",
           array(".proplist"=>"uptime,version,cpu-load,board-name"));
    $array_cap2 = $api_cap->comm("/interface/ethernet/print",
           array(".proplist"=>"mac-address", "?name"=>"ether1"));
    $api_cap->disconnect();
  }

  $ap_model = $array_cap[0]['board-name'];
  $ap_version = $array_cap[0]['version'];
  $ap_cpu = $array_cap[0]['cpu-load'];
  $ap_uptime = $array_cap[0]['uptime'];
  $ap_mac = $array_cap2[0]['mac-address'];

  echo "<script>\n";
  echo "$(function() {\n";
  echo "  $('#ap_name').text('$ap_name');\n";
  echo "  $('#ap_mac').text('$ap_mac');\n";
  echo "  $('#ap_ip').text('$ap_ip');\n";
  echo "  $('#ap_model').text('$ap_model');\n";
  echo "  $('#ap_version').text('$ap_version');\n";
  echo "  $('#ap_cpu').text('$ap_cpu%');\n";
  echo "  $('#ap_uptime').text('$ap_uptime');\n";
  echo "  $('#ap_subs').text($ap_subs);\n";
  echo "  $('#ap_tx_rx').text('$ap_tx_tot / $ap_rx_tot');\n";
  echo "  // set option select \n";
  echo "  $('#sel_channelf').val('$ap_channel');\n";
  echo "  $('#ap_power').text('$ap_power dBm');\n";
  echo "  $('#sel_txpower').val($ap_power);\n";
  echo "});\n";
  echo "</script>\n";
}    

?>
