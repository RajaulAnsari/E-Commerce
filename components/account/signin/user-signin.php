<?php
// Start or resume session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and trim whitespace
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "SELECT * FROM USERS WHERE USER_NAME = :USERNAME";

    $stmt = oci_parse($conn, $sql);

    // Bind parameters
    oci_bind_by_name($stmt, ":USERNAME", $username);

    // Execute the statement
    oci_execute($stmt);

    // Fetch the row associated with the username
    $row = oci_fetch_assoc($stmt);

    // Check if $row contains a valid result
    if ($row !== false) {
        // Verify password
        if (password_verify($password, $row['PASSWORD'])) {
            // Check if user is verified
            if ($row['IS_VERIFIED'] == 1) {
                // Store the username in session
                $_SESSION['username'] = $username;

                // Redirect to dashboard or other page
                header("Location: userhomepage.php");
                exit();
            } else {
                echo "Account not verified. Please verify your email before logging in.";
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Username not found!";
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
            <p>Not A User? <a href="./usersignup.php">Register Now</a></p>
        </div>
        <div class="image-container">
            <img src="./images/User-Trader/Users.png" alt="banner">
        </div>
        <div style="width: 30px;"></div>
        <div class="form-container">
            <div class="user-form">
                <form class="uform" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="username" placeholder="Username" required><br>
                    <input type="password" name="password" id="password" placeholder="Password" required><br>
                    <label class="remember"><input type="checkbox" name="remember" value="remember">&nbsp Remember
                        Me</label><br>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
    <br>
</div>