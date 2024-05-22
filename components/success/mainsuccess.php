<?php
// Include database connection
include 'connection.php';

// Start the session
// session_start();

// Check if the user is logged in
if (!isset($_SESSION['uusername'])) {
    header("Location: usersignin.php");
    exit();
}


// Retrieve the user ID from the USER_CLECK table based on the uusername
$user = $_SESSION['uusername'];
$qry = "SELECT USER_ID FROM USER_CLECK WHERE UUSER_NAME = :username";
$res = oci_parse($conn, $qry);
oci_bind_by_name($res, ":username", $user);
oci_execute($res);
$row = oci_fetch_assoc($res);
$user_id = $row['USER_ID'];


// getting cart id
$sqlcartid="SELECT CART_ID FROM CART WHERE USER_ID=:user_id";
$stmtcartid=oci_parse($conn,$sqlcartid);
oci_bind_by_name($stmtcartid,":user_id",$user_id);
oci_execute($stmtcartid);
$rowcartid=oci_fetch_assoc($stmtcartid);
$cart_id=$rowcartid['CART_ID'];




// Fetch the user data
$row = oci_fetch_assoc($res);

// Check if user exists and retrieve the user ID
if ($user_id) {
    // $user_id = $row['USER_ID'];

    // Check if payment was successful (this part depends on how you verify PayPal payment success)
    $paymentSuccess = true; // This is a placeholder. Replace it with actual payment verification logic.

    // Check if payment was successful and session variables are set
    if ($paymentSuccess && isset($_SESSION['collectionDate']) && isset($_SESSION['collectionTime'])) {
        $collectionDate = $_SESSION['collectionDate'];
        $collectionTime = $_SESSION['collectionTime'];


        // Convert collectionDate to 'YYYYMMDD' format
        $date = DateTime::createFromFormat('F j, l', $collectionDate);
        if ($date) {
            $formattedDate = $date->format('Y-m-d');
        } else {
            echo "<script>alert('Error: Invalid date format.');</script>";
            exit();
        }


        $total_items = $_SESSION['cart_total_items']; // Assuming you have the total items in session
        $totalAmount = $_SESSION['cart_total']; // Assuming you have the total amount in session
        // $product_id = $_SESSION['product_id'];

        //review access
        $sqlReviewAccess="INSERT INTO REVIEW_ACCESS (PRODUCT_ID,USER_ID,IS_COLLECTED)
        SELECT DISTINCT PRODUCT_ID, :user_id,0
        FROM CART_PRODUCT";
        $stmtReviewAccess=oci_parse($conn,$sqlReviewAccess);
        oci_bind_by_name($stmtReviewAccess,":user_id",$user_id);
        oci_execute($stmtReviewAccess);

        
        
        // Proceed with payment and order insertion logic
        // Fetch the cart ID based on the product ID
        $sqlCartId = "SELECT PRODUCT_ID FROM CART_PRODUCT WHERE PRODUCT_ID = :product_id";
        $stmtCartId = oci_parse($conn, $sqlCartId);
        oci_bind_by_name($stmtCartId, ":product_id", $product_id);
        oci_execute($stmtCartId);
        $rowCartId = oci_fetch_assoc($stmtCartId);
        
        // Fetch the cart ID into a variable

        $sqlCollectionSlot="INSERT INTO COLLECTION_SLOT (COLLECTION_SLOT_ID, COLLECTION_DATE, COLLECTION_TIME) 
                            VALUES (COLLECTION_SLOT_ID_SEQ.NEXTVAL,TO_DATE( :collectionDate,'YYYY-MM-DD'), :collectionTime)
                            RETURNING COLLECTION_SLOT_ID INTO :collection_slot_id";
        $stmtCollectionSlot = oci_parse($conn, $sqlCollectionSlot);
        oci_bind_by_name($stmtCollectionSlot, ":collectionDate", $formattedDate);
        oci_bind_by_name($stmtCollectionSlot, ":collectionTime", $collectionTime);
        oci_bind_by_name($stmtCollectionSlot, ":collection_slot_id", $collection_slot_id, -1,SQLT_INT); // Assuming COLLECTION_SLOT_ID is of NUMBER(32), used for returning the generated COLLECTION_SLOT_ID
        // var_dump($collection_slot_id);


        if (oci_execute($stmtCollectionSlot)) {
            
        // Insert order data into ORDERS table
        $sqlOrder = "INSERT INTO ORDERS (ORDER_ID, ORDER_QUANTITY, ORDER_DATE, TOTAL_AMOUNT, INVOICE_NO, COLLECTION_SLOT_ID, CART_ID)
                     VALUES (ORDER_ID_SEQ.NEXTVAL, :orderQuantity, SYSDATE, :totalAmount, 1,  :collection_slot_id,:cart_id)";
        $stmtOrder = oci_parse($conn, $sqlOrder);
        oci_bind_by_name($stmtOrder, ":orderQuantity", $total_items); // Assuming you have the total quantity in session
        oci_bind_by_name($stmtOrder, ":totalAmount", $totalAmount);
        oci_bind_by_name($stmtOrder, ":cart_id", $cart_id); // Assuming cart ID is not provided at this point
        oci_bind_by_name($stmtOrder, ":collection_slot_id", $collection_slot_id);


        // Execute the order insertion query
        if (oci_execute($stmtOrder)) {
            // Order data inserted successfully
            
            // Insert payment data into PAYMENT table
            $paymentMethod = 'PayPal'; // Example, replace with actual payment method
            $sqlPayment = "INSERT INTO PAYMENT (PAYMENT_ID, PAYMENT_AMOUNT, PAID_VIA, PAYMENT_DATE, PAYMENT_TIME, USER_ID, ORDER_ID)
                           VALUES (PAYMENT_ID_SEQ.NEXTVAL, :totalAmount, :paymentMethod, SYSDATE, SYSDATE, :user_id, (SELECT MAX(ORDER_ID) FROM ORDERS))";
            $stmtPayment = oci_parse($conn, $sqlPayment);
            oci_bind_by_name($stmtPayment, ":totalAmount", $totalAmount);
            oci_bind_by_name($stmtPayment, ":paymentMethod", $paymentMethod);
            oci_bind_by_name($stmtPayment, ":user_id", $user_id);

            // Execute the payment insertion query
            if (oci_execute($stmtPayment)) {
                // Commit the transaction
                oci_commit($conn);
                echo "<div class='container'>";
                echo "<div class='success'>";
                echo "<h1>Payment Successful!</h1>";
                echo "<p>Thank you for your purchase. Your payment has been successfully processed.</p>";
                echo "<a href='index.php'>Return to Home</a>";
                echo "</div>";
                echo "</div>";
            } else {
                // Rollback the transaction
                oci_rollback($conn);
                $e = oci_error($stmtPayment);
                echo "<script>alert('Error: Unable to insert payment data. " . $e['message'] . "');</script>";
            }

            oci_free_statement($stmtPayment);

            // Clear the session variables
            unset($_SESSION['collectionDate']);
            unset($_SESSION['collectionTime']);
            unset($_SESSION['cart_total']);
            unset($_SESSION['cart_total_items']);
        } else {
            // Rollback the transaction
            oci_rollback($conn);
            $e = oci_error($stmtOrder);
            echo "<script>alert('Error: Unable to insert order data. " . $e['message'] . "');</script>";
        }
        oci_free_statement($stmtOrder);
    }else{
        // Rollback the transaction
        oci_rollback($conn);
        $e = oci_error($stmtCollectionSlot);
        echo "<script>alert('Error: Unable to insert collection slot data. " . $e['message'] . "');</script>";
    }
    oci_free_statement($stmtCollectionSlot);
    } else {
        echo "<script>alert('Error: Collection date or time not found or payment unsuccessful');</script>";
    }
} else {
    echo "<script>alert('Error: User not found.');</script>";
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



</br></br></br>