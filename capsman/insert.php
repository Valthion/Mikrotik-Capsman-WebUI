<?php

require('api/routeros_api.class.php');
require('includes/mysql.inc.php');

/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$link = mysqli_connect("localhost", "root", "", "capsman");
  
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
  
// Escape user inputs for security
$mac = mysqli_real_escape_string($link, $_REQUEST['mac']);
$hostname = mysqli_real_escape_string($link, $_REQUEST['hostname']);
$username = mysqli_real_escape_string($link, $_REQUEST['username']);
  
// Attempt insert query execution
$sql = "INSERT INTO hostname(mac, hostname, user) VALUES ('$mac', '$hostname', '$username')";
if(mysqli_query($link, $sql)){
    echo "Records added successfully.";
    //membuat metode redirect dengan kode 301
    header("location: index.php",true, 301);
    //membuat kode di bawah header tidak diproses oleh website sehingga lebih aman
    exit();
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
  
// Close connection
mysqli_close($link);
?>
