<?php
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

    // Retrieve product ID sent via AJAX
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

    // Insert the product into the cart table
    if (!empty($product_id)) {
        $insert_query = "DECLARE
        cart_id NUMBER;
    BEGIN
        INSERT INTO CART (CART_ITEMS, USER_ID) VALUES (1, :user_id) RETURNING CART_ID INTO cart_id;
        
        INSERT INTO CART_PRODUCT (PRODUCT_ID, CART_ID) VALUES (:product_id, cart_id);
        
        COMMIT;
    END;";
        
        $stmt = oci_parse($conn, $insert_query);
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_bind_by_name($stmt, ":user_id", $user_id);
        oci_bind_by_name($stmt, ":cart_id", $cart_id, 32); // Assuming CART_ID is of NUMBER(32)
        
        // Execute the statement
        $success = oci_execute($stmt);

        if ($success) {
            // Commit the transaction
            oci_commit($conn);
            echo "success"; // Send success response to AJAX
        } else {
            // Handle errors
            $error = oci_error($stmt);
            echo "Error: " . $error['message'];
        }

        oci_free_statement($stmt);
        oci_close($conn);
    } else {
        //echo "error: Product ID not provided"; // Send error response to AJAX
    }
} else {
    echo"
    <script>
        alert('Please login to view your cart');
        window.location.href = 'usersignin.php';
    </script>
    ";
}
include "cart_js.php";
?>






<div class="container">
    </br>
    <div class="cart-title">
        <h2>Cart</h2>
    </div>
</div>

<div class="container">
    </br>
    <div class="cart-table">
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Remove</th>
            </tr>
            <?php
            $qry = "
        SELECT 
            p.PRODUCT_ID,
            p.PRODUCT_NAME,
            p.PRODUCT_PRICE,
            p.PRODUCT_IMAGE,
            c.CART_ITEMS,
            cp.CART_ID
        FROM 
            PRODUCT p
        JOIN 
            CART_PRODUCT cp ON p.PRODUCT_ID = cp.PRODUCT_ID
        JOIN 
            CART c ON cp.CART_ID = c.CART_ID
        ";

            $res = oci_parse($conn, $qry);
            oci_execute($res);

            while($row = oci_fetch_assoc($res))
            {
                $pid = $row['PRODUCT_ID'];
                $qty = $row['CART_ITEMS'];
                $pname = $row['PRODUCT_NAME'];
                $pprice = $row['PRODUCT_PRICE'];
                $ptotal = $pprice * $qty;
                $pimage = $row['PRODUCT_IMAGE'];
            ?>
            <tr>
                <td>
                    <img src="./images/Vegetables-Fruits/<?php echo $pimage; ?>" alt="<?php echo $pname; ?>" width="50"
                        height="50">
                    <?php echo $pname; ?>
                </td>
                <td><?php echo $pprice; ?></td>
                <td>
                    <form action="cart.php" method="post">
                        <input type="number" name="quantity" value="<?php echo $qty; ?>" min="1" max="10">
                        <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                        <input type="submit" value="Update">
                    </form>
                </td>
                <td><?php echo $ptotal; ?></td>
                <td><a href="removecart.php?pid=<?php echo $pid; ?>">Remove</a></td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>

    <div class="cart-total">
        <h3>Total: <?php $qry = "
        SELECT SUM(p.PRODUCT_PRICE * c.CART_ITEMS) AS TOTAL 
        FROM CART c 
        JOIN CART_PRODUCT cp ON c.CART_ID = cp.CART_ID
        JOIN PRODUCT p ON cp.PRODUCT_ID = p.PRODUCT_ID
        WHERE c.USER_ID = '$user_id'";
        
        $res = oci_parse($conn, $qry);
        oci_execute($res);
        
        $row = oci_fetch_assoc($res);
        $total = $row['TOTAL'];
        
        echo " $";
        echo $total; 
        echo "</h3>";
?>
    </div>

    <div class="cart-checkout">
        <a href="checkout.php">Checkout</a>
    </div>
</div>

</br>