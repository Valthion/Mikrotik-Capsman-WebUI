/*
 * js/capsman.js
 * 
 * Copyright 2017 Arief Yudhawarman <yudi@inferno>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */
 
var timeoutID;
var AP;

function refreshSelect(name) {
  param = $("div.RefreshSelect select").val();
  $("p.RefreshTime").text(param);
  clearTimeout(timeoutID);
  executeQuery(name);
}

function showSub(mac,ip) {
  $("#dialog").empty(".log");
  $("#dialog").dialog("option", "title", "Loading...").dialog("open");
  $("#dialog").load("showSub.php?mac="+mac+"&ip="+ip, function() {
  $(this).dialog("option", "title", "Subscriber Info") });
}

function configAP(name,ip,channel,power,subs,rx,tx) {
  AP = name;
  $("#AP_dialog").dialog("open");
  $("#AP_dialog").dialog("option", "title", AP+" Info")
  $("#jscript1").load("getCap.php?name="+AP+"&ip="+ip+"&channel="+channel+"&power="+power+"&subs="+subs+"&rx_tot="+rx+"&tx_tot="+tx);
}

function actions(name,val1,val2) {
  $.get("setSub.php", { action:val1, mac:val2 });
  clearTimeout(timeoutID);
  setTimeout(executeQuery(name),1000);
}

function executeQuery(name) {
  clearTimeout(timeoutID);
  param = $("p.RefreshTime").text();
  $.ajax({
    url: name,
    success: function(data) {
      $('#result').html(data);
    },
    complete: function() {
      $("div.RefreshSelect select").val(param);
      param = param * 1000;
      timeoutID = setTimeout(function(){executeQuery(name);}, param);
    }
  });
}

$(function() {

  $.ajaxSetup ({
      cache: false
  });

  $("#dialog").dialog({
    autoOpen: false,
    width: 610,
    height: 225,
    modal: true,
    close: function( event, ui ) { $(this).dialog({height:225})}
  });

  $('#AP_info').tabs();
  $('#AP_dialog').dialog({ 
    autoOpen: false, 
    modal:true,
    width:300,
    height:350
  });

  $("#btnCaps").click(function(){
    clearTimeout(timeoutID);
    executeQuery('caps.php');
  });
  $("#btnUser").click(function(){
    clearTimeout(timeoutID);
    executeQuery('subscribers.php');
  });
  $("#btnBlck").click(function(){
    clearTimeout(timeoutID);
    executeQuery('accessList.php');
  });
  $("#btnInfo").click(function(){
    clearTimeout(timeoutID);
    $('#result').html('<center><img src="images/CAPsMAN.jpg" style="width:600px;height:427px;" alt="CAPsMAN"/></center>');
  });
  
  $("#btnApply").click(function(){
    channelf = $("#sel_channelf").val();
    txpower = $("#sel_txpower").val();
    ssid = $("#ssid").val();
    pass = $("#pass").val();
    
	  var data = new Object();
	  data.AP = AP;
  	data.channelf = channelf;
	  data.txpower = txpower;
    data.ssid = ssid;
    data.pass = pass;

			
	  var options = new Object();
  	options.data = data;
	  options.dataType = 'text';
	  options.type = 'post';
	  options.url = 'configCaps.php';

  	$.ajax(options);
  });
  
});
