<?php
include 'connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['uusername'])) {
    header("Location: usersignin.php");
    exit();
}

// Check if collection date and time are set in the session
if (!isset($_SESSION['collectionDate']) || !isset($_SESSION['collectionTime'])) {
    echo "Error: Collection date or time not found in session.";
    exit();
}

// Retrieve the collection date and time from the session
$collectionDate = $_SESSION['collectionDate'];
$collectionTime = $_SESSION['collectionTime'];


// Define PayPal parameters
$businessEmail = "sb-iggrw30554138@business.example.com"; // Your PayPal sandbox business email
$currencyCode = "USD"; // Currency code
$paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr"; // PayPal sandbox URL
$returnUrl = "http://localhost/E-Commerce/success.php"; // Return URL after successful payment
$cancelUrl = "http://localhost/E-Commerce/cart.php"; // Cancel URL

// Redirect to PayPal login page for payment processing
$paypalParams = array(
    'business' => $businessEmail,
    'cmd' => '_xclick',
    'item_name' => 'Order from E-Commerce',
    'amount' => $_SESSION['cart_total'],
    'currency_code' => $currencyCode,
    // 'quantity' => $_SESSION['cart_total_items'],
    'return' => $returnUrl,
    'cancel_return' => $cancelUrl
);

$paypalRedirectUrl = $paypalUrl . '?' . http_build_query($paypalParams);

header("Location: $paypalRedirectUrl");
exit();
?>