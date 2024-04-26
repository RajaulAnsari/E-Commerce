<?php
session_start(); // Start session if not already started
include 'connection.php';

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Assuming you have a 'cart' table with at least 'user_id' and 'product_id' columns
    $user_id = $_SESSION['user_id']; // Assuming you have stored user id in session

    // Insert the product into the cart table
    $sql = "INSERT INTO cart (user_id, product_id) VALUES (:user_id, :product_id)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":user_id", $user_id);
    oci_bind_by_name($stmt, ":product_id", $product_id);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    // Redirect to the cart page
    // header("Location: cart_main.php");
    exit();
} else {
    // Redirect to the home page or any appropriate page if the request is not from the form
    header("Location: index.php");
    exit();
}
?>