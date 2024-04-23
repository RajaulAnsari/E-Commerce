<?php
// session_start();

$user = "rajaul";
$pass = "root";
$host = "//localhost/XE";
$dbconn = oci_connect($user, $pass, $host);

if (!$dbconn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
} else {
    echo "Connection Successful ok!";
}

oci_close($dbconn);
?>