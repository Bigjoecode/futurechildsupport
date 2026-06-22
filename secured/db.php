<?php
//$dbserver = "localhost";
//$dbusname = "root";
//$dbpass = "";
//$dbname = "donate_site";

$dbserver = "localhost";
$dbusname = "uscintco_user2";
$dbpass = "Chidimanager100%";
$dbname = "uscintco_future";

$dbconnec = mysqli_connect($dbserver, $dbusname, $dbpass, $dbname);
if ($dbconnec->connect_error) {
  die('<p>Failed to connect to MySQL: ' . mysqli_connect_error() . '</p>');
}
?>