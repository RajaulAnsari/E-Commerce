<?php
include "connection.php";
session_start();

if (isset($_SESSION['uusername']) && isset($_POST['pid'])) {
    $pid = $_POST['pid'];
    $user = $_SESSION['uusername'];
    
    // Retrieve user ID from the session
    $qry = "SELECT USER_ID FROM USER_CLECK WHERE UUSER_NAME = :user_name";

    $res = oci_parse($conn, $qry);
    oci_bind_by_name($res, ":user_name", $user);
    oci_execute($res);
    $row = oci_fetch_assoc($res);
    $uid = $row['USER_ID'];
    
    // Remove the item from the cart
    $remove_query = "DELETE FROM PRODUCT_WISHLIST pw 
    WHERE pw.PRODUCT_ID = :product_id AND pw.WISHLIST_ID 
    IN (SELECT w.WISHLIST_ID FROM WISHLIST w WHERE w.USER_ID = :user_id)";
    $stmt = oci_parse($conn, $remove_query);
    oci_bind_by_name($stmt, ":product_id", $pid);
    oci_bind_by_name($stmt, ":user_id", $uid);
    $success = oci_execute($stmt);

    if ($success) {
        // Commit the transaction
        oci_commit($conn);
        header("Location: wishlist.php"); // Redirect back to the cart page
    } else {
        // Handle errors
        $error = oci_error($stmt);
        echo "Error: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
} else {
    // Missing parameters or user not logged in
    echo '<script>alert("Missing parameters or user not logged in"); window.location.href = "cart.php";</script>';
}
?>