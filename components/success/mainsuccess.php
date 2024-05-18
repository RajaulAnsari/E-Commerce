<?php
// session_start();

// Check if the user is logged in
if (!isset($_SESSION['uusername'])) {
    header("Location: usersignin.php");
    exit();
}
?>

<style>
.success {
    text-align: center;
    margin-top: 100px;
}

.success h1 {
    color: #4CAF50;
}

.success p {
    margin: 20px 0;
    font-size: 18px;
}

.success a {
    display: inline-block;
    padding: 10px 20px;
    color: white;
    background-color: #4CAF50;
    text-decoration: none;
    border-radius: 5px;
}

.success a:hover {
    background-color: #45a049;
}
</style>
<div class="container">
    <div class="success">
        <h1>Payment Successful!</h1>
        <p>Thank you for your purchase. Your payment has been successfully processed.</p>
        <a href="index.php">Return to Home</a>
    </div>
</div>
</br></br></br>