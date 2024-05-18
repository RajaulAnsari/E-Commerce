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

    // Check if the product is already in the cart
    $check_query = "SELECT COUNT(*) AS num_products FROM CART_PRODUCT WHERE PRODUCT_ID = :product_id AND CART_ID IN (SELECT CART_ID FROM CART WHERE USER_ID = :user_id)";
    $check_stmt = oci_parse($conn, $check_query);
    oci_bind_by_name($check_stmt, ":product_id", $product_id);
    oci_bind_by_name($check_stmt, ":user_id", $user_id);
    oci_execute($check_stmt);
    $check_row = oci_fetch_assoc($check_stmt);
    $num_products = $check_row['NUM_PRODUCTS'];
    oci_free_statement($check_stmt);

    // Check if adding this product will exceed the limit of 20 products
    $cart_items_query = "SELECT COUNT(CART_PRODUCT_ID) AS total_items FROM CART_PRODUCT WHERE CART_ID IN (SELECT CART_ID FROM CART WHERE USER_ID = :user_id)";
    $cart_items_stmt = oci_parse($conn, $cart_items_query);
    oci_bind_by_name($cart_items_stmt, ":user_id", $user_id);
    oci_execute($cart_items_stmt);
    $cart_items_row = oci_fetch_assoc($cart_items_stmt);
    $total_items = $cart_items_row['TOTAL_ITEMS'];
    oci_free_statement($cart_items_stmt);

    // Insert the product into the cart table
    if (!empty($product_id)&& $num_products == 0 && $total_items <20) {
        $insert_query = "DECLARE
        cart_id NUMBER;
    BEGIN
        INSERT INTO CART (CART_ITEMS, USER_ID, CART_CREATED,CART_UPDATED) VALUES (1, :user_id, SYSDATE,SYSDATE) RETURNING CART_ID INTO cart_id;
        
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
    }
    oci_close($conn);
} else {
    echo"
    <script>
        alert('Please login to view your cart');
        window.location.href = 'usersignin.php';
    </script>
    ";
}
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
        WHERE
            c.USER_ID = '$user_id'
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
                    <form action="updatecart.php" method="post">
                        <input style="padding:2px; border:2px solid; border-radius:10px; font-size:15px;" type="number"
                            name="quantity" value="<?php echo $qty; ?>" min="1" max="10">
                        <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                        <input
                            style="background-color: green; color: white; border: none; padding: 5px 10px; cursor: pointer;border-radius:5px;"
                            type="submit" value="Update">
                    </form>
                </td>
                <td><?php echo $ptotal; ?></td>
                <td>
                    <form action="removecart.php" method="post">
                        <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                        <button type="submit"
                            style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;border-radius:5px;"
                            onclick="return confirm('Are you sure you want to remove this item?')">Remove</button>
                    </form>
                </td>
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

    <?php
$paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$paypalID = 'sb-iggrw30554138@business.example.com'; //Business Email

?>
    <div class="cart-checkout">
        <form action="<?php echo $paypalURL; ?>" method="post">

            <input type="hidden" name="business" value="<?php echo $paypalID;?>">

            <!-- Specify a Buy Now button. -->
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="item_name" value=".">
            <input type="hidden" name="item_number" value=".">
            <input type="hidden" name="amount" value="<?php echo $total;?>">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="quantity" value="<?php echo $total_items;?>">

            <!-- Specify URLs -->
            <input type='hidden' name='cancel_return' value='http://localhost/E-Commerce/cart.php'>
            <input type='hidden' name='return' value='http://localhost/E-Commerce/success.php'>

            <!-- Display the payment button. -->
            <input type="image" name="submit" border="0"
                src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
                alt="PayPal - The safer, easier way to pay online">
            <!-- <img alt="" border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif"> -->
        </form>
    </div>

    </br>
    </br>