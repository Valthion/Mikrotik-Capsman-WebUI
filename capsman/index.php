<?php

include("includes/header.inc.php");

?>

<body>
<div id="jscript1"></div> <!-- The script will be loaded after calling a script -->
<div class="container">
  <div class="wrapper">
    <div class="mainimg">
    <h1>MikroTik CapsMan Controller</h1>
    <h2>Powered by jQuery & Ajax</h2>
    </div>
    <hr>
    <center>
    <button id="btnCaps">Managed Access Points</button>
    <button id="btnUser">Active Wireless Users</button>
    <button id="btnBlck">Blocked Users</button>
    <button id="btnInfo">Info</button>
    
    <p class="RefreshTime">120</p>
    </center>
    <div class="spacer">&nbsp;</div>
    <div id="result">
      <img style="display: block;margin: 0 auto;width:600px;height:427px;" src="images/CAPsMAN.jpg" alt="CAPsMAN"/>
    </div>
    <div id="dialog" title="Subscriber Info"></div>
    <div id="AP_dialog" title="AP Info">
      <div id="AP_info">
        <ul>
	      <li><a href="#AP_details">Details</a></li>
        <li><a href="#AP_configuration">Configuration</a></li>
	    </ul>
        <div id="AP_details">
	      <table class="log" width="100%">
			<tr><th>Name</th><td id="ap_name"></td></tr>
        <tr><th>MAC Address</th><td id="ap_mac"></td></tr>
        <tr><th>IP Address</th><td id="ap_ip"></td></tr>
		    <tr><th>Model</th><td id="ap_model"></td></tr>
		    <tr><th>Version</th><td id="ap_version"></td></tr>
		    <tr><th>CPU Load</th><td id="ap_cpu"></td></tr>
		    <tr><th colspan=2><hr></th></tr>
		    <tr><th>Uptime</th><td id="ap_uptime"></td></tr>
		    <tr><th>Subscribers</th><td id="ap_subs"></td></tr>
		    <tr><th>Tx / Rx</th><td id="ap_tx_rx">Tx / Rx</td></tr>
        <tr><th>Tx Power</th><td id="ap_power"></td></tr>
	      </table>        
        </div> <!-- div id="AP_details" -->
        <div id="AP_configuration">
          <table class="log" width="100%">
		    <tr><th>Radio Band</th><td>2GHz-b/g/n</td></tr>
		    <tr><th>Channel Width</th><td>20MHz</td></tr>
		    <tr><th>Channel Frequency</th><td>
		    <select id="sel_channelf" name="channelfreq" width=20>
			  <option value="channel1">1 - (2412MHz)</option>
			  <option value="channel2">2 - (2417MHz)</option>
			  <option value="channel3">3 - (2422MHz)</option>
			  <option value="channel4">4 - (2427MHz)</option>
			  <option value="channel5">5 - (2432MHz)</option>
			  <option value="channel6">6 - (2437MHz)</option>
			  <option value="channel7">7 - (2442MHz)</option>
			  <option value="channel8">8 - (2447MHz)</option>
			  <option value="channel9">9 - (2452MHz)</option>
			  <option value="channel10">10 - (2457MHz)</option>
			  <option value="channel11">11 - (2462MHz)</option>
            </select>
	        </td></tr>
          <tr><th>Tx Power</th><td>
		    <select id="sel_txpower" name="txpower" width=20>
		      <option value="1">1</option>
			  <option value="2">2</option>
			  <option value="3">3</option>
			  <option value="4">4</option>
			  <option value="5">5</option>
			  <option value="6">6</option>
			  <option value="7">7</option>
			  <option value="8">8</option>
			  <option value="9">9</option>
			  <option value="10">10</option>
			  <option value="11">11</option>
			  <option value="12">12</option>
			  <option value="13">13</option>
			  <option value="14">14</option>
			  <option value="15">15</option>
			  <option value="16">16</option>
			  <option value="17">17</option>
			  <option value="18">18</option>
			  <option value="19">19</option>
			  <option value="20">20</option>
			  <option value="21">21</option>
			  <option value="22">22</option>
            </select>
		    </td></tr>
          <tr><th>SSID</th><td>
            <input type="text" id="ssid" name="ssid"><br>
		  <tr><th>Set Password (8 Characters Min)</th><td>
            <input type="text" id="pass" name="pass"><br>
          </td></tr>
          </table>
          <div class="spacer"></div>
          <button id="btnApply" style=\"margin:0 0;font-size:.9em;\">Apply</button>
	    </div> <!-- div id="AP_configuration" -->
      </div>   <!-- div id="AP_info" -->
    </div>     <!-- div id="AP_dialog" -->

  </div> <!-- wrapper -->

<?php

include("includes/footer.inc.php");

?>
