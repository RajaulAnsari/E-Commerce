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

    // Check if the product is already in the wishlist
    $check_query = "SELECT COUNT(*) AS num_products FROM PRODUCT_WISHLIST WHERE PRODUCT_ID = :product_id AND WISHLIST_ID IN (SELECT WISHLIST_ID FROM WISHLIST WHERE USER_ID = :user_id)";
    $check_stmt = oci_parse($conn, $check_query);
    oci_bind_by_name($check_stmt, ":product_id", $product_id);
    oci_bind_by_name($check_stmt, ":user_id", $user_id);
    oci_execute($check_stmt);
    $check_row = oci_fetch_assoc($check_stmt);
    $num_products = $check_row['NUM_PRODUCTS'];
    oci_free_statement($check_stmt);


    // Insert the product into the wishlist table
    if (!empty($product_id) && $num_products == 0) {
        $insert_query = "DECLARE
            wishlist_id NUMBER;
        BEGIN
            INSERT INTO WISHLIST (WISHLIST_ITEMS,USER_ID, WISHLIST_CREATED, WISHLIST_UPDATE) VALUES (1,:user_id, SYSDATE, SYSDATE) RETURNING WISHLIST_ID INTO wishlist_id;
            INSERT INTO PRODUCT_WISHLIST (PRODUCT_ID, WISHLIST_ID) VALUES (:product_id, wishlist_id);
            COMMIT;
        END;";
        
        $stmt = oci_parse($conn, $insert_query);
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_bind_by_name($stmt, ":user_id", $user_id);
        oci_bind_by_name($stmt, ":wishlist_id", $cart_id, 32); // Assuming WISHLIST_ID is of NUMBER(32)
        
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
        alert('Please login to view your wishlist');
        window.location.href = 'usersignin.php';
    </script>
    ";
}
?>

<div class="container">
    </br>
    <div class="wishlist-title">
        <h2>My Wishlist</h2>
    </div>
</div>

<div class="container">
    </br>
    <div class="wishlist-table">
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Remove</th>
                <th>Add to Cart</th>
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
            wp.WISHLIST_ID
        FROM 
            PRODUCT p
        JOIN 
            PRODUCT_WISHLIST wp ON p.PRODUCT_ID = wp.PRODUCT_ID
        JOIN 
            WISHLIST w ON wp.WISHLIST_ID = w.WISHLIST_ID
        LEFT JOIN 
            DISCOUNT d ON p.PRODUCT_ID = d.PRODUCT_ID
        WHERE
            w.USER_ID = '$user_id'
    ";

            $res = oci_parse($conn, $qry);
            oci_execute($res);

            while($row = oci_fetch_assoc($res)) {
                $pid = $row['PRODUCT_ID'];
                $pname = $row['PRODUCT_NAME'];
                $pprice = $row['DISCOUNTED_PRICE'];
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
                    <form action="removewishlist.php" method="post">
                        <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                        <button type="submit"
                            style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;"
                            onclick="return confirm('Are you sure you want to remove this item?')">Remove</button>
                    </form>
                </td>
                <td>
                    <form action="cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                        <button type="submit"
                            style="background-color: green; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px;">Add
                            to Cart</button>
                    </form>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>

</br>
</br>