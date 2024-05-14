<?php
include "connection.php";

if(isset($_POST['quantity']) && isset($_POST['pid'])) {
    $quantity = $_POST['quantity'];
    $pid = $_POST['pid'];

    // Update the quantity in the cart table
    $update_query = "UPDATE CART SET PRODUCT_QUANTITY = :quantity WHERE PRODUCT_ID = :pid";
    $stmt = oci_parse($conn, $update_query);
    oci_bind_by_name($stmt, ":quantity", $quantity);
    oci_bind_by_name($stmt, ":pid", $pid);

    if (oci_execute($stmt)) {
        // Quantity updated successfully
        echo "echo '<script>alert(`Quantity updated successfully`); window.location.href = 'cart.php';</script>'";

    } else {
        // Failed to update quantity
        echo "echo '<script>alert(`Failed to update quantity`); window.location.href = 'cart.php';</script>'";

    }
} else {
    // Missing parameters
    echo "echo '<script>alert(`Missing parameters`); window.location.href = 'cart.php';</script>'";

}
?>