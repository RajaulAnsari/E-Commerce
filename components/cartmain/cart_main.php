<?php
// Debugging
//var_dump($_POST);

include "connection.php";

// Check if the user is logged in
if(isset($_SESSION['uusername'])) {
    // Retrieve user ID from the session
    $user = $_SESSION['uusername'];
    $qry = "SELECT * FROM USER_CLECK WHERE UUSER_NAME = '$user'";
    $res = oci_parse($conn, $qry);
    oci_execute($res);
    $row = oci_fetch_assoc($res);
    $user_id = $row['USER_ID'];

    // Retrieve product details sent via AJAX
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
    $product_price = isset($_POST['product_price']) ? $_POST['product_price'] : '';

    // Insert the product into the cart table
    if(!empty($product_id) && !empty($product_name) && !empty($product_price)) {
        $insert_query="INSERT INTO CART c, CART_PRODUCT cp, PRODUCT p WHERE c.CART_ID = cp.CART_ID AND cp.PRODUCT_ID = p.PRODUCT_ID AND c.USER_ID = :user_id AND cp.PRODUCT_ID = :product_id AND c.CART_ITEMS = 1";
        //$insert_query = "INSERT INTO CART (PRODUCT_ID, PRODUCT_NAME, PRODUCT_PRICE, PRODUCT_QUANTITY, USER_ID) VALUES (:product_id, :product_name, :product_price, 1, :user_id)";
        $stmt = oci_parse($conn, $insert_query);
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_bind_by_name($stmt, ":product_name", $product_name);
        oci_bind_by_name($stmt, ":product_price", $product_price);
        oci_bind_by_name($stmt, ":user_id", $user_id);
    }
} else{
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
            $qry = "SELECT CART.*, PRODUCT.PRODUCT_NAME, PRODUCT.PRODUCT_PRICE, PRODUCT.PRODUCT_IMAGE FROM CART INNER JOIN PRODUCT ON CART.PRODUCT_ID = PRODUCT.PRODUCT_ID WHERE USER_ID = '$user_id'";

            $res = oci_parse($conn, $qry);
            oci_execute($res);

            while($row = oci_fetch_assoc($res))
            {
                $pid = $row['PRODUCT_ID'];
                $qty = $row['PRODUCT_QUANTITY'];
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
        <h3>Total: <?php $qry = "SELECT SUM(PRODUCT_PRICE * PRODUCT_QUANTITY) AS TOTAL FROM CART WHERE USER_ID = '$user_id'";
        
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