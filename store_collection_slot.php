<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store collection date and time in session
    $_SESSION['collectionDate'] = $_POST['collectionDate'];
    $_SESSION['collectionTime'] = $_POST['collectionTime'];

    // Redirect to PayPal
    header("Location: https://www.sandbox.paypal.com/cgi-bin/webscr?business=sb-iggrw30554138@business.example.com&cmd=_xclick&item_name=Order+from+E-Commerce&item_number=1&amount={$_SESSION['cart_total']}&currency_code=USD&quantity={$_SESSION['cart_total_items']}&return=http://localhost/E-Commerce/success.php&cancel_return=http://localhost/E-Commerce/cart.php");
    exit();
}
?>