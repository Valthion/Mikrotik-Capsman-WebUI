<?php

require('includes/mysql.inc.php');

if (isset($_GET['mac']) && isset($_GET['ip'])) {

  $mac = $_GET['mac'] ; // mac yang akan dicari
  $ip = $_GET['ip'];
  
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
  
  echo <<<EOF
<script>
$(function() {
  $("#btnAppUsr").click(function(){
    mac = $("#inp_mac").text();
	username = $("#inp_username").val();
	hostname = $("#inp_hostname").val();
	$.get("editUser.php?action=set&mac="+mac+"&username="+username+"&hostname="+hostname);
  });
    $("#btnPing").click(function(){
    ip = $("#inp_ip").text();
      $("#dialog").dialog({height:470});
      $("#showLogPing").html("<hr><p>Wait a moment.</p>");
    // $("#showLogPing").text("Wait a moment.");
    $("#showLogPing").load("showSubPing.php?ip="+ip);
  });
  $("#btnadd").click(function(){
    mac = $("#inp_mac").text();
	username = $("#inp_username").val();
	hostname = $("#inp_hostname").val();
	$.get("insert.php?action=set&mac="+mac+"&username="+username+"&hostname="+hostname);
  });
});
</script>
<style>
table.log {
  padding: 0px 5px 0px 2px;
}
th {
  text-align: left;
}
</style>
  <table class="log">
EOF;

  if ($hostname == 'Noname') {
    echo "<tr><th>Name</th><td></td><td>$hostname</td><td>&nbsp;&nbsp;</td>
	  <td style=\"font-weight:bold;\">Hostname</td><td><input type=\"text\" id=\"inp_hostname\" name=\"hostname\" title=\"Assign new hostname here.\"></td>";
  } else {
	echo "<tr><th>Name</th><td>&nbsp;&nbsp;</td><td id=\"inp_hostname\">$hostname</td><td colspan=4></td></tr>";
  }	
  echo <<<EOF1
	  <tr><th>IP Address</th><td></td><td id="inp_ip">$ip</td><td colspan=4></td></tr>
	  <tr><th>MAC Address</th><td></td><td id="inp_mac">$mac</td><td colspan=4></td></tr>
	  <tr><th>User</th><td></td><td>$username</td><td>&nbsp;&nbsp;</td>
	  <td style="font-weight:bold;">Username</td><td><input type="text" id="inp_username" name="username" title="Assign new user here."></td>
	  <td><button id="btnAppUsr" style="margin:0 0;">Update</button></td>
    <td><button id="btnadd" style="margin:0 0;">Add</button></td>
    </tr>
	  <tr><td colspan=6>&nbsp;</td></tr>
	  <tr><th></th>
	  <td></td><td colspan=2><button id="btnPing" style="font-size:1em;">Ping Subscriber</button></td></tr>
  </table>
  <!-- hr -->
  <div id="showLogPing"></div>
EOF1;

}

?>
