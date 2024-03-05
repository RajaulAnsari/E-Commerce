<?php
session_start();

$user = "admin";
$pass = "pass";
$host = "localhost/XE";
$dbconn = oci_connect($user, $pass, $host);

if (!$dbconn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
} else {
    echo "Connection Successful";
}

oci_close($dbconn);
?>