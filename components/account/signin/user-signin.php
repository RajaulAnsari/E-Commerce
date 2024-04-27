<?php
// Start or resume session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set session cookie parameters to a suitable lifetime (e.g., 1 day)
// ini_set('session.cookie_lifetime', 86400); // 86400 seconds = 1 day

include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and trim whitespace
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "SELECT * FROM \"user\" WHERE username =:username";

    $stmt = oci_parse($conn, $sql);

    // Bind parameters
    oci_bind_by_name($stmt, ":username", $username);

    // Execute the statement
    $result = oci_execute($stmt);

    if ($result) {
        $row = oci_fetch_assoc($stmt);

        // Verify password
        if (password_verify($password, $row['PASSWORD'])) {
            // Store the username in session
            $_SESSION['username'] = $username;

            echo "Login successful!";
            // Redirect to dashboard or other page
            header("Location: userhomepage.php");
            exit();
        } else {
            echo "Invalid username or password!";
        }
    } else {
        echo "User not found!";
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
                    <input type="username" name="username" placeholder="Username" required><br>
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