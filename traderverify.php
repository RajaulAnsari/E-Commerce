<?php
include 'connection.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Update the database to mark the email as verified
    $sql = "UPDATE \"TRADER\" SET IS_VERIFIED = 1 WHERE EMAIL_ADDRESS = :email";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":email", $email);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    echo"
    <script>
        alert('Email address verified successfully. You can now login.');
        window.location.href = 'tradersignin.php';
    </script>
    ";
} else {
    echo "Invalid verification link.";
}
?>