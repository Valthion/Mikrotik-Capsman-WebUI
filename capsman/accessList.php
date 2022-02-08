<?php

require('api/routeros_api.class.php');
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
  $reject = $api_caps->comm("/caps-man/access-list/print",
                      array(".proplist"=>"mac-address",
                            "?action"=>"reject"));

  $length = count($reject);
  $n_reject = 0;    
  
  include('includes/selectRefresh.inc.php');

  echo <<<EOF
<table class="subscribers">
<tr>
  <th>Name</th>
  <th>MAC Address</th>
  <th>User</th>
  <th>Action</th>
</tr>
EOF;

  for ($i=0; $i<$length; $i++) {
    if (!empty($reject[$i]['mac-address'])) {
      $mac = $reject[$i]['mac-address'];
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
      echo "<td>$hostname</td><td>$mac</td><td>$username</td><td style=\"text-align:center;\"><button style=\"margin:0 0;\" onClick=\"actions('".$_SERVER["PHP_SELF"]."','unblock','$mac');\"><span class=\"ui-icon ui-icon-check\"></span></button>\n";
      $n_reject++;
      echo "</tr>\n";
    }
  }
  echo "</table>\n";
  echo "<p class=\"p1\">Blocked users $n_reject</p>\n";

  $api_caps->disconnect();
}

?>
