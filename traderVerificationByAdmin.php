<?php
include 'connection.php';

if (isset($_GET['email']) && isset($_GET['shopid'])) {
    // Trader and shop verification
    $email = $_GET['email'];
    $shop_id = $_GET['shopid'];

    // Update the database to mark the trader email as verified
    $sql_trader = "UPDATE \"TRADER\" SET TRADER_ADMIN_VERIFICATION = 1 WHERE EMAIL_ADDRESS = :email";
    $stmt_trader = oci_parse($conn, $sql_trader);
    oci_bind_by_name($stmt_trader, ":email", $email);
    oci_execute($stmt_trader);

    // Update the database to mark the shop as verified
    $sql_shop = "UPDATE \"SHOP\" SET SHOP_ADMIN_VERIFICATION = 1 WHERE SHOP_ID = :shop_id";
    $stmt_shop = oci_parse($conn, $sql_shop);
    oci_bind_by_name($stmt_shop, ":shop_id", $shop_id);
    oci_execute($stmt_shop);

    oci_free_statement($stmt_trader);
    oci_free_statement($stmt_shop);
    oci_close($conn);

    echo "
    <script>
        alert('Trader and Shop verified successfully. The trader can now log in and the shop will be displayed on the website.');
        window.history.back();
    </script>
    ";
} elseif (isset($_GET['email'])) {
    // Trader verification
    $email = $_GET['email'];

    // Update the database to mark the trader email as verified
    $sql = "UPDATE \"TRADER\" SET TRADER_ADMIN_VERIFICATION = 1 WHERE EMAIL_ADDRESS = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    echo "
    <script>
        alert('Trader verified successfully. The trader can now log in.');
        window.history.back();
    </script>
    ";
} elseif (isset($_GET['shopid'])) {
    // Shop verification
    $shop_id = $_GET['shopid'];

    // Update the database to mark the shop as verified
    $sql = "UPDATE \"SHOP\" SET SHOP_ADMIN_VERIFICATION = 1 WHERE SHOP_ID = :shop_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":shop_id", $shop_id);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    echo "
    <script>
        alert('Shop verified successfully. The shop will now be displayed on the website.');
        window.history.back();
    </script>
    ";
} else {
    echo "Invalid verification link.";
}
?>