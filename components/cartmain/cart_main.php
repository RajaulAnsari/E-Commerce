<?php
include "connection.php";

// Start the session
// session_start();

// Check if the user is logged in
if (isset($_SESSION['uusername'])) {
    // Retrieve user ID from the session
    $user = $_SESSION['uusername'];
    $qry = "SELECT * FROM USER_CLECK WHERE UUSER_NAME = :username";
    $res = oci_parse($conn, $qry);
    oci_bind_by_name($res, ":username", $user);
    oci_execute($res);
    $row = oci_fetch_assoc($res);
    $user_id = $row['USER_ID'];

    // Retrieve product ID sent via AJAX
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

    // Check if the product is already in the cart
    $check_query = "SELECT COUNT(*) AS num_products FROM CART_PRODUCT WHERE PRODUCT_ID = :product_id AND CART_ID IN (SELECT CART_ID FROM CART WHERE USER_ID = :user_id)";
    $check_stmt = oci_parse($conn, $check_query);
    oci_bind_by_name($check_stmt, ":product_id", $product_id, SQLT_INT); // Assuming PRODUCT_ID is of type INT
    oci_bind_by_name($check_stmt, ":user_id", $user_id);
    oci_execute($check_stmt);
    $check_row = oci_fetch_assoc($check_stmt);
    $num_products = $check_row['NUM_PRODUCTS'];
    oci_free_statement($check_stmt);

    //product id
    // $pid = "SELECT PRODUCT_ID FROM CART_PRODUCT WHERE PRODUCT_ID = (SELECT PRODUCT_ID FROM CART WHERE CART_ID = :cart_id)";
    // $pid_stmt = oci_parse($conn, $pid);
    // oci_bind_by_name($pid_stmt, ":product_id", $product_id);
    // oci_execute($pid_stmt);
    // $pid_row = oci_fetch_assoc($pid_stmt);
    // $pid = $pid_row['PRODUCT_ID'];
    // oci_free_statement($pid_stmt);

    // var_dump($pid);


    // Check if adding this product will exceed the limit of 20 products
    $cart_items_query = "SELECT COUNT(CART_PRODUCT_ID) AS total_items FROM CART_PRODUCT WHERE CART_ID IN (SELECT CART_ID FROM CART WHERE USER_ID = :user_id)";
    $cart_items_stmt = oci_parse($conn, $cart_items_query);
    oci_bind_by_name($cart_items_stmt, ":user_id", $user_id);
    oci_execute($cart_items_stmt);
    $cart_items_row = oci_fetch_assoc($cart_items_stmt);
    $total_items = $cart_items_row['TOTAL_ITEMS'];
    oci_free_statement($cart_items_stmt);

    // Insert the product into the cart table
    if (!empty($product_id) && $num_products == 0 && $total_items < 20) {
        // Start a transaction
        // oci_begin($conn);

        // Get CART_ID from CART_PRODUCT table based on PRODUCT_ID
        $get_cart_id_query = "SELECT CART_ID FROM CART_PRODUCT WHERE PRODUCT_ID = :product_id";
        $get_cart_id_stmt = oci_parse($conn, $get_cart_id_query);
        oci_bind_by_name($get_cart_id_stmt, ":product_id", $product_id);
        oci_execute($get_cart_id_stmt);
        $cart_id_row = oci_fetch_assoc($get_cart_id_stmt);
        $cart_id = $cart_id_row['CART_ID'];
        oci_free_statement($get_cart_id_stmt);

        $insert_query = "BEGIN 
            INSERT INTO CART (CART_ITEMS, USER_ID, CART_CREATED, CART_UPDATED) VALUES (1, :user_id, SYSDATE, SYSDATE) RETURNING CART_ID INTO :cart_id;
            INSERT INTO CART_PRODUCT (PRODUCT_ID, CART_ID) VALUES (:product_id, :cart_id);
        END;";
        
        $stmt = oci_parse($conn, $insert_query);
        oci_bind_by_name($stmt, ":user_id", $user_id);
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_bind_by_name($stmt, ":cart_id", $cart_id, 32); // Assuming CART_ID is of NUMBER(32), used for returning the generated CART_ID
        
        // Execute the statement
        $success = oci_execute($stmt);

        if ($success) {
            // Commit the transaction
            oci_commit($conn);
            echo "<script>
                alert('Product added to cart');
                window.location.href = 'wishlist.php';
            </script>";
        } else {
            // Rollback the transaction
            oci_rollback($conn);
            $error = oci_error($stmt);
            echo "Error: " . $error['message'];
        }

        oci_free_statement($stmt);
    }
    oci_close($conn);

} else {
    echo "<script>
        alert('Please login to view your cart');
        window.location.href = 'usersignin.php';
    </script>";
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
                CASE 
                    WHEN d.DISCOUNT_AMOUNT IS NOT NULL THEN p.PRODUCT_PRICE - d.DISCOUNT_AMOUNT
                    ELSE p.PRODUCT_PRICE
                END AS DISCOUNTED_PRICE,
                p.PRODUCT_IMAGE,
                c.CART_ITEMS,
                cp.CART_ID
            FROM 
                PRODUCT p
            JOIN 
                CART_PRODUCT cp ON p.PRODUCT_ID = cp.PRODUCT_ID
            JOIN 
                CART c ON cp.CART_ID = c.CART_ID
            LEFT JOIN 
                DISCOUNT d ON p.PRODUCT_ID = d.PRODUCT_ID
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
                $pprice = $row['DISCOUNTED_PRICE'];
                $ptotal = $pprice * $qty;
                $pimage = $row['PRODUCT_IMAGE'];
            ?>
            <tr>
                <td>
                    <img src="./images/products/<?php echo $pimage; ?>" alt="<?php echo $pname; ?>" width="50"
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
        SELECT SUM(
            CASE 
                WHEN d.DISCOUNT_AMOUNT IS NOT NULL THEN (p.PRODUCT_PRICE - d.DISCOUNT_AMOUNT) * c.CART_ITEMS
                ELSE p.PRODUCT_PRICE * c.CART_ITEMS
            END
        ) AS TOTAL 
        FROM CART c 
        JOIN CART_PRODUCT cp ON c.CART_ID = cp.CART_ID
        JOIN PRODUCT p ON cp.PRODUCT_ID = p.PRODUCT_ID
        LEFT JOIN DISCOUNT d ON p.PRODUCT_ID = d.PRODUCT_ID
        WHERE c.USER_ID = '$user_id'
    ";
        
        $res = oci_parse($conn, $qry);
        oci_execute($res);
        
        $row = oci_fetch_assoc($res);
        $total = $row['TOTAL'];
        
        echo " $";
        echo $total; 
        echo "</h3>";


        // Store total and total items in session variables
        $_SESSION['cart_total'] = $total;
        $_SESSION['cart_total_items'] = $total_items;
        $_SESSION['product_id'] = $product_id;
        // $_SESSION['cart_id'] = $cart_id;

?>
    </div>
    <div class="cart-checkout">
        <form action="checkout.php" method="post">
            <button type="submit"
                style="background-color: green; color: white; border: none; padding: 5px 10px; cursor: pointer;border-radius:5px;">Checkout</button>
        </form>
    </div>
</div>
</br>
</br>