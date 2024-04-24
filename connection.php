<?php
$conn = oci_connect('rajaul', 'root', '//localhost/XE');
if (!$conn) {
$e = oci_error();
trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
?>