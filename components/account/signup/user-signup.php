<?php
include './mailing.php';
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $uusername = $_POST['uusername'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<p style='color: red;'>Passwords do not match</p>";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement
        $sql = "INSERT INTO USER_CLECK (FIRST_NAME, LAST_NAME, UUSER_NAME, EMAIL_ADDRESS, PASSWORD, ADDRESS, PHONE_NUMBER,IS_VERIFIED) 
                VALUES (:firstname, :lastname, :uusername, :email, :password, :address, :contact,0)";

        $stmt = oci_parse($conn, $sql);

        // Bind parameters
        oci_bind_by_name($stmt, ":firstname", $firstname);
        oci_bind_by_name($stmt, ":lastname", $lastname);
        oci_bind_by_name($stmt, ":uusername", $uusername);
        oci_bind_by_name($stmt, ":email", $email);
        oci_bind_by_name($stmt, ":password", $hashed_password);
        oci_bind_by_name($stmt, ":address", $address);
        oci_bind_by_name($stmt, ":contact", $contact);

        // Execute the statement
        $result = oci_execute($stmt);

        if ($result) {
            // Define email subject and body
            $subject = "Verify Your Email Address";
            $html = "
            <div style='font-family: Arial, sans-serif; border: 2px solid #007bff; border-radius: 10px; padding: 20px; background-color: #f9f9f9;'>
        <h2 style='color: green; margin-bottom: 20px;'>Welcome to CleckHub!</h2>
        <p>Hello $firstname,</p>
        <p>Thank you for registering with CleckHub. To complete your registration, please click the button below to verify your email address:</p>
        <a href='http://{$_SERVER['HTTP_HOST']}/E-Commerce/userverify.php?email=" . urlencode($email) . "' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Verify Email Address</a>
        <p style='margin-top: 20px; font-size: 14px; color: #777;'>If you did not register for CleckHub, please ignore this email.</p>
    </div>
";
            
                        
            // Send verification email
            sendVerificationEmail($email, $subject, $html);

            echo "<script>alert('Registration successful! Verification Required.');</script>";
            echo "<script>window.location = './usersignin.php'</script>";
        } else {
            echo "Error: Unable to register. Please try again later.";
        }

        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>


<div class="container">
    <div class="user-div">
        <div class="greet">
            <p>Welcome to CleckHub</p>
            <p>Feels good to have you! 😊</p>
        </div>
        <div class="register">
            <p>Already A User? <a href="./usersignin.php">Login Now</a></p>
        </div>
        <div class="image-container">
            <img src="./images/User-Trader/Users.png" alt="banner">
        </div>
        <div style="width: 30px;"></div>
        <div class="form-container">
            <div class="user-form">
                <form class="uform" id="registrationForm" method="post"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="firstname" placeholder="Firstname" required><br>
                    <input type="text" name="lastname" placeholder="Lastname" required><br>
                    <input type="text" name="uusername" placeholder="Username" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" id="password" placeholder="Password" required><br>
                    <div id="passwordError1" style="color: red; display: none;">Password must contain at least one
                        uppercase letter, one lowercase letter, one number, and be at least 8 characters.</div>
                    <input type="password" name="confirmpassword" id="confirmPassword" placeholder="Confirm Password"
                        required><br>
                    <div id="passwordError" style="color: red; display: none;">Passwords do not match</div>
                    <input type="text" name="address" placeholder="Address" required><br>
                    <input type="number" name="contact" placeholder="Contact" required><br>
                    <label class="remember"><input type="checkbox" name="remember" value="remember" required>&nbsp I
                        agree the
                        terms and conditions</label><br>
                    <button type="submit" id="submitButton">Register</button>
                </form>
            </div>
        </div>
    </div>
    <br>
</div>