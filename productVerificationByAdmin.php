<?php
include 'connection.php';

if (isset($_GET['productid'])) {
    $productid = $_GET['productid'];

    // Update the database to mark the email as verified
    $sql = "UPDATE \"PRODUCT\" SET PRODUCT_ADMIN_VERIFICATION = 1 WHERE PRODUCT_ID = :productid";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":productid", $productid);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    echo"
    <script>
        alert('Product verified successfully. Now Product will display on website.');
        window.history.back();
    </script>
    ";
} else {
    echo "Invalid verification link.";
}
?>