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

    if (!empty($quantity) && !empty($pid)) {
        // Retrieve the cart ID for the user and product
        $cart_id_query = "
        SELECT c.CART_ID
        FROM CART c
        JOIN CART_PRODUCT cp ON c.CART_ID = cp.CART_ID
        WHERE c.USER_ID = :user_id AND cp.PRODUCT_ID = :product_id";
        
        $cart_id_stmt = oci_parse($conn, $cart_id_query);
        oci_bind_by_name($cart_id_stmt, ":user_id", $uid);
        oci_bind_by_name($cart_id_stmt, ":product_id", $pid);
        oci_execute($cart_id_stmt);
        $cart_row = oci_fetch_assoc($cart_id_stmt);
        $cart_id = $cart_row['CART_ID'];

        if ($cart_id) {
            // Update the quantity in the CART table
            $update_cart_query = "
            UPDATE CART
            SET CART_ITEMS = :cart_items
            WHERE CART_ID = :cart_id";

            $update_cart_stmt = oci_parse($conn, $update_cart_query);
            oci_bind_by_name($update_cart_stmt, ":cart_items", $quantity);
            oci_bind_by_name($update_cart_stmt, ":cart_id", $cart_id);
            $success_cart = oci_execute($update_cart_stmt);

            // Update the quantity in the CART_PRODUCT table
            $update_cart_product_query = "
            UPDATE CART_PRODUCT
            SET QUANTITY = :quantity
            WHERE CART_ID = :cart_id AND PRODUCT_ID = :product_id";

            $update_cart_product_stmt = oci_parse($conn, $update_cart_product_query);
            oci_bind_by_name($update_cart_product_stmt, ":quantity", $quantity);
            oci_bind_by_name($update_cart_product_stmt, ":cart_id", $cart_id);
            oci_bind_by_name($update_cart_product_stmt, ":product_id", $pid);
            $success_cart_product = oci_execute($update_cart_product_stmt);

            // Commit the transaction if both updates succeed
            if ($success_cart && $success_cart_product) {
                oci_commit($conn);
                echo "<script>alert('Quantity updated successfully'); window.location.href='cart.php';</script>";
            } else {
                // Handle errors
                $error_cart = oci_error($update_cart_stmt);
                $error_cart_product = oci_error($update_cart_product_stmt);
                echo "Error in CART update: " . $error_cart['message'] . "<br>";
                echo "Error in CART_PRODUCT update: " . $error_cart_product['message'];
            }

            oci_free_statement($update_cart_stmt);
            oci_free_statement($update_cart_product_stmt);
        } else {
            echo "<script>alert('Cart not found'); window.location.href='cart.php';</script>";
        }

        oci_free_statement($cart_id_stmt);
    }
    oci_close($conn);
} else {
    // Missing parameters or user not logged in
    echo '<script>alert("Missing parameters or user not logged in"); window.location.href = "cart.php";</script>';
}
?>