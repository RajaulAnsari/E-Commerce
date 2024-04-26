<?php
session_start(); // Start session if not already started
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or any appropriate page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve cart contents for the logged-in user
$sql = "SELECT product.*, cart.quantity 
        FROM cart 
        JOIN product ON cart.product_id = product.product_id 
        WHERE cart.user_id = :user_id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":user_id", $user_id);
oci_execute($stmt);

?>

<h1>Cart</h1>

<?php if (oci_fetch($stmt)) { ?>
<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php do { ?>
        <tr>
            <td><?php echo oci_result($stmt, 'PRODUCTNAME'); ?></td>
            <td><?php echo oci_result($stmt, 'PRODUCTPRICE'); ?></td>
            <td><?php echo oci_result($stmt, 'QUANTITY'); ?></td>
            <td><?php echo oci_result($stmt, 'PRODUCTPRICE') * oci_result($stmt, 'QUANTITY'); ?></td>
        </tr>
        <?php } while (oci_fetch($stmt)); ?>
    </tbody>
</table>
<?php } else { ?>
<p>Your cart is empty.</p>
<?php } ?>

<?php
    oci_free_statement($stmt);
    oci_close($conn);
    ?>

<!-- Add your HTML for checkout button, continue shopping link, etc. here -->