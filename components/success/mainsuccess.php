<?php
// session_start();

// Check if the user is logged in
if (!isset($_SESSION['uusername'])) {
    header("Location: usersignin.php");
    exit();
}

// Include database connection
include 'connection.php';

// Retrieve the user ID from the USER_CLECK table based on the uusername
$uusername = $_SESSION['uusername'];
$sqlUserId = "SELECT USER_ID FROM USER_CLECK WHERE UUSER_NAME = :uusername";
$stmtUserId = oci_parse($conn, $sqlUserId);
oci_bind_by_name($stmtUserId, ":uusername", $uusername);
oci_execute($stmtUserId);
$row = oci_fetch_assoc($stmtUserId);
$userId = $row['USER_ID'];

// Check if payment was successful (this part depends on how you verify PayPal payment success)
$paymentSuccess = true; // This is a placeholder. Replace it with actual payment verification logic.

if ($paymentSuccess && isset($_SESSION['collectionDate']) && isset($_SESSION['collectionTime'])) {
    $collectionDate = $_SESSION['collectionDate'];
    $collectionTime = $_SESSION['collectionTime'];
    $totalAmount = $_SESSION['cart_total']; // Assuming you have the total amount in session
    
    // Query to find maximum existing order ID
    $sqlMaxOrderId = "SELECT MAX(ORDER_ID) AS MAX_ORDER_ID FROM ORDERS";
    $stmtMaxOrderId = oci_parse($conn, $sqlMaxOrderId);
    oci_execute($stmtMaxOrderId);
    $rowMaxOrderId = oci_fetch_assoc($stmtMaxOrderId);
    $maxOrderId = $rowMaxOrderId['MAX_ORDER_ID'];
    $orderId = $maxOrderId + 1; // Increment the maximum order ID by 1 to generate the new order ID

    // Insert payment data into PAYMENT table
    $paymentDate = date('Y-m-d'); // Assuming payment date is current date
    $paymentTime = date('H:i:s'); // Assuming payment time is current time
    $paymentMethod = 'PayPal'; // Example, replace with actual payment method

    $sqlPayment = "INSERT INTO PAYMENT (PAYMENT_ID, PAYMENT_AMOUNT, PAID_VIA, PAYMENT_DATE, PAYMENT_TIME, USER_ID, ORDER_ID)
                   VALUES (PAYMENT_ID_SEQ.NEXTVAL, :totalAmount, :paymentMethod, TO_DATE(:paymentDate, 'YYYY-MM-DD'), TO_TIMESTAMP(:paymentTime, 'HH24:MI:SS'), :userId, :orderId)";

    $stmtPayment = oci_parse($conn, $sqlPayment);
    oci_bind_by_name($stmtPayment, ":totalAmount", $totalAmount);
    oci_bind_by_name($stmtPayment, ":paymentMethod", $paymentMethod);
    oci_bind_by_name($stmtPayment, ":paymentDate", $paymentDate);
    oci_bind_by_name($stmtPayment, ":paymentTime", $paymentTime);
    oci_bind_by_name($stmtPayment, ":userId", $userId);
    oci_bind_by_name($stmtPayment, ":orderId", $orderId);

    if (oci_execute($stmtPayment)) {
        echo "<script>alert('Payment data inserted successfully');</script>";
    } else {
        $e = oci_error($stmtPayment);
        echo "<script>alert('Error: Unable to insert payment data. " . $e['message'] . "');</script>";
    }

    oci_free_statement($stmtPayment);

    // Insert order data into ORDERS table
    $orderDate = date('Y-m-d'); // Assuming order date is current date
    $status = 'Pending'; // Initial status of the order

    $sqlOrder = "INSERT INTO ORDERS (ORDER_ID, ORDER_DATE, ORDER_STATUS, USER_ID)
                 VALUES (:orderId, TO_DATE(:orderDate, 'YYYY-MM-DD'), :status, :userId)";

    $stmtOrder = oci_parse($conn, $sqlOrder);
    oci_bind_by_name($stmtOrder, ":orderId", $orderId);
    oci_bind_by_name($stmtOrder, ":orderDate", $orderDate);
    oci_bind_by_name($stmtOrder, ":status", $status);
    oci_bind_by_name($stmtOrder, ":userId", $userId);

    if (oci_execute($stmtOrder)) {
        echo "<script>alert('Order data inserted successfully');</script>";
    } else {
        $e = oci_error($stmtOrder);
        echo "<script>alert('Error: Unable to insert order data. " . $e['message'] . "');</script>";
    }

    oci_free_statement($stmtOrder);

    // Clear the session variables
    unset($_SESSION['collectionDate']);
    unset($_SESSION['collectionTime']);
    unset($_SESSION['cart_total']);
    unset($_SESSION['cart_total_items']);

    // Redirect to a success page or provide a success message
    echo "<script>alert('Payment successful!');</script>";
    // You can redirect to a success page after displaying the message
    // header("Location: success.php");
    // exit();
} else {
    echo "<script>alert('Error: Collection date or time not found or payment unsuccessful');</script>";
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