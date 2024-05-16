<?php
session_start();
include "connection.php";

// Check if the user is logged in
if (isset($_SESSION['uusername'])) {
    // Retrieve user ID from the session
    $user = $_SESSION['uusername'];
    $qry = "SELECT * FROM USER_CLECK WHERE UUSER_NAME = '$user'";
    $res = oci_parse($conn, $qry);
    oci_execute($res);
    $row = oci_fetch_assoc($res);
    $user_id = $row['USER_ID'];

    // Retrieve product ID and quantity sent via POST
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update the cart quantity in the database
    $update_query = "UPDATE CART_PRODUCT SET CART_ITEMS = :quantity WHERE PRODUCT_ID = :product_id AND CART_ID IN (SELECT CART_ID FROM CART WHERE USER_ID = :user_id)";
    $update_stmt = oci_parse($conn, $update_query);
    oci_bind_by_name($update_stmt, ":quantity", $quantity);
    oci_bind_by_name($update_stmt, ":product_id", $product_id);
    oci_bind_by_name($update_stmt, ":user_id", $user_id);
    $success = oci_execute($update_stmt);

    if ($success) {
        oci_commit($conn);
        
        // Update session variable with new cart quantity
        $_SESSION['cart'][$product_id] = $quantity;

        echo "success";
    } else {
        echo "Error updating quantity";
    }

    oci_free_statement($update_stmt);
    oci_close($conn);
} else {
    echo "User not logged in";
}
?>