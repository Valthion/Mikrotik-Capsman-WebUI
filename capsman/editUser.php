<?php

require('api/routeros_api.class.php');
require('includes/mysql.inc.php');

if (isset($_GET['action']) && isset($_GET['mac']) && isset($_GET['username']) && isset($_GET['hostname'])) {
  $mac = $_GET['mac'];
  $username = $_GET['username'];
  $hostname = $_GET['hostname'];
  if ($_GET['action'] == 'set') {
    $username = mysqli_real_escape_string($dbc, trim($username));
    // check if the mac is already there
    $q = "SELECT user,hostname FROM hostname WHERE mac='$mac'";
    $r = @mysqli_query($dbc, $q);
    if ($r) {
      $num = @mysqli_num_rows($r);
      if ($num == 1) { // matched then update username
        $q = "UPDATE hostname set user='$username' WHERE mac='$mac'";
        $r = @mysqli_query($dbc, $q);
      }
      $r = @mysqli_query($dbc, $q);
    }
  }
}

?>
