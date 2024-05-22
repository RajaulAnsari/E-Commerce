<?php
include 'connection.php';

if (isset($_GET['shopid'])) {
    $shop_id = $_GET['shopid'];

    // Update the database to mark the email as verified
    $sql = "UPDATE \"SHOP\" SET SHOP_ADMIN_VERIFICATION = 1 WHERE SHOP_ID =:shop_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":shop_id", $shop_id);

    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    echo"
    <script>
        alert('Shop verified successfully. Now Shop will display on website.');
        window.history.back();
    </script>
    ";
} else {
    echo "Invalid verification link.";
}
?>