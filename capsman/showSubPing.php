<?php

require('api/routeros_api.class.php');
require('includes/capsman.inc.php');

$count = 10;

echo "<hr>\n";
echo "<pre style=\"padding-left:1em;\">\n";

if (isset($_GET['ip'])) {

  $ip = $_GET['ip'];

  $api_gw = new RouterosAPI();
  $api_gw->debug = false;
  
  if ($api_gw->connect(IP_GW, API_USR, API_PASS)) {
  
    $api_gw->write('/ping',false);
    $api_gw->write("=address=$ip",false); 
    $api_gw->write("=count=$count",false);
    $api_gw->write('=interval=1');
    $array = $api_gw->read(false);
    $j = 0; 
    $arrping[0] = "";
    for ($i=0; $i<count($array); $i++) {
      if (preg_match('=seq=', $array[$i])) {
        $j++;
        $arrping[$j] = "";
      }
      $arrping[$j] .= $array[$i].";";
    }
  
    $api_gw->disconnect();
  
    echo "SEQ\tHOST\t\tSIZE\tTTL\tTIME\tSTATUS\n";
    for ($i=1; $i<count($arrping); $i++) {
      $stra = explode(';',$arrping[$i]);
  
      // check if timeout happen
      if (preg_match('#=status=timeout#', $arrping[$i])) {
        for($j=0; $j<3; $j++) {
          $strb = explode('=',$stra[$j]);
          if ($j == 1) {
            echo $strb[2]."\t\t\t\t";  
          } else {
            echo $strb[2]."\t";
          }
        }
        echo "\n";
        if (preg_match('!done', $arrping[$i])) {
          if (preg_match('min-rtt',$arrping[$i])) {
            for($j=5; $j<11; $j++) {
              $strb = explode('=',$stra[$j]);
              $strb[2] = preg_match('packet-loss',$strb[1])? $strb[2].'%' : $strb[2];
              echo $strb[1]."=".$strb[2]." ";
            }
          } else {
            for($j=3; $j<6; $j++) {
              $strb = explode('=',$stra[$j]);
              $strb[2] = preg_match('packet-loss',$strb[1])? $strb[2].'%' : $strb[2];
              echo $strb[1]."=".$strb[2]." ";  
            }
          }
          echo "\n";
        }
      } else {
        for($j=0; $j<5; $j++) {
          $strb = explode('=',$stra[$j]);
          echo $strb[2]."\t";  
        }
        echo "\n";
        if (preg_match('#!done#', $arrping[$i])) {
          for($j=5; $j<11; $j++) {
            $strb = explode('=',$stra[$j]);
            $strb[2] = preg_match('#packet-loss#',$strb[1])? $strb[2].'%' : $strb[2];
            echo $strb[1]."=".$strb[2]." ";  
          }
          echo "\n";
        }
      }
    }
  }
}

echo "</pre>\n";

?>
