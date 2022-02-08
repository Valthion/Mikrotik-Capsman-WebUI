<?php
 
function formatn($n) {
  $kilo = 1000;
  $mega = 1000000;
  $giga = 1000000000;
  if ($n>$giga) { return round($n/$giga,2)."G"; }
  if ($n>$mega) { return round($n/$mega,2)."M"; }
  if ($n>$kilo) { return round($n/$kilo,2)."K"; }
  return $n."B";
}

function formatMac($mac) {
  $array = explode(':',$mac);
  return implode('',$array);
}

function uptime($s) {
  $pattern='/[\d]*[dhms]*/';
  $r = "";
  if (preg_match_all ($pattern, $s, $matches) ) { 
    $length = count($matches[0]);
    for ($i=0; $i<($length-2); $i++) {
      $r = $r . $matches[0][$i]." ";
    }
    trim($r);
  } 
  return $r;
}

?>
