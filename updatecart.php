<?php
include "connection.php";
session_start();

if (isset($_SESSION['uusername']) && isset($_POST['quantity']) && isset($_POST['pid'])) {
    $quantity = $_POST['quantity'];
    $pid = $_POST['pid'];
    $user = $_SESSION['uusername'];
    
    // Retrieve user ID from the session
    $qry = "SELECT USER_ID FROM USER_CLECK WHERE UUSER_NAME = :user_name";

    $res = oci_parse($conn, $qry);
    oci_bind_by_name($res, ":user_name", $user);
    oci_execute($res);
    $row = oci_fetch_assoc($res);
    $uid = $row['USER_ID'];
    
    // Update the quantity in the cart table
    if (!empty($quantity) && !empty($pid)) {
        $update_query = "
        UPDATE CART c
        SET c.CART_ITEMS = :cart_items
        WHERE c.USER_ID = :user_id
        AND EXISTS (
            SELECT 1
            FROM CART_PRODUCT cp
            WHERE cp.CART_ID = c.CART_ID
            AND cp.PRODUCT_ID = :product_id
        )";
        $stmt = oci_parse($conn, $update_query);
        oci_bind_by_name($stmt, ":cart_items", $quantity);
        oci_bind_by_name($stmt, ":user_id", $uid);
        oci_bind_by_name($stmt, ":product_id", $pid);
        $success = oci_execute($stmt);
        
        // Execute the update query
        if ($success) {
            // Commit the transaction
            oci_commit($conn);
            echo "<script>alert('Quantity updated successfully'); window.location.href='cart.php';</script>";
        } else {
            // Handle errors
            $error = oci_error($stmt);
            echo "Error: " . $error['message'];
        }

        oci_free_statement($stmt);
    }
    oci_close($conn);
} else {
    // Missing parameters or user not logged in
    echo '<script>alert("Missing parameters or user not logged in"); window.location.href = "cart.php";</script>';
}
?>