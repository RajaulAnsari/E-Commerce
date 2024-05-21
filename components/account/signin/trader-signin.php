<?php
// Start or resume session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and trim whitespace
    $tusername = trim($_POST['tusername']);
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "SELECT * FROM TRADER WHERE TUSER_NAME = :tusername";

    $stmt = oci_parse($conn, $sql);

    // Bind parameters
    oci_bind_by_name($stmt, ":tusername", $tusername);

    // Execute the statement
    oci_execute($stmt);

    // Fetch the row associated with the tusername
    $row = oci_fetch_assoc($stmt);

    // Check if $row contains a valid result
    if ($row !== false) {
        // Verify password
        if (password_verify($password, $row['TRADER_PASSWORD'])) {
            // Check if user is verified
            if ($row['IS_VERIFIED'] == 1) {
                if ($row['TRADER_ADMIN_VERIFICATION'] == 1){
                // Store the tusername in session
                $_SESSION['tusername'] = $tusername;

                // Redirect to dashboard or other page
                header("Location: traderhomepage.php");
                exit();
                } else {
                    echo "Account not verified by admin. Please wait for admin verification.";
                }
            } else {
                echo "Account not verified. Please verify your email before logging in.";
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "tusername not found!";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>


<div class="container">
    <div class="user-div">
        <div class="greet">
            <p>Hello There!</p>
            <p>Glad to see you back! 😊</p>
        </div>
        <div class="register">
            <p>Not A Trader? <a href="./tradersignup.php">Register Now</a></p>
        </div>
        <div class="image-container">
            <img src="./images/User-Trader/Traders.png" alt="banner">
        </div>
        <div style="width: 30px;"></div>
        <div class="form-container">
            <div class="user-form">
                <form class="uform" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="tusername" placeholder="Username" required><br>
                    <input type="password" name="password" id="password" placeholder="Password" required><br>
                    <label class="remember"><input type="checkbox" name="remember" value="remember">&nbsp Remember
                        Me</label><br>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
    </br>
</div>