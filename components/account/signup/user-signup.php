<?php
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
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
        $sql = "INSERT INTO \"USER_CLECK\" (FIRST_NAME, LAST_NAME, USER_NAME, EMAIL_ADDRESS, PASSWORD, ADDRESS, PHONE_NUMBER) 
                VALUES (:firstname, :lastname, :username, :email, :password, :address, :contact)";

        $stmt = oci_parse($conn, $sql);

        // Bind parameters
        oci_bind_by_name($stmt, ":firstname", $firstname);
        oci_bind_by_name($stmt, ":lastname", $lastname);
        oci_bind_by_name($stmt, ":username", $username);
        oci_bind_by_name($stmt, ":email", $email);
        oci_bind_by_name($stmt, ":password", $hashed_password);
        oci_bind_by_name($stmt, ":address", $address);
        oci_bind_by_name($stmt, ":contact", $contact);

        // Execute the statement
        $result = oci_execute($stmt);

        if ($result) {
            echo "Registration successful!";
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
                    <input type="text" name="username" placeholder="Username" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" id="password" placeholder="Password" required><br>
                    <input type="password" name="confirmpassword" id="confirmPassword" placeholder="Confirm Password"
                        required><br>
                    <div id="passwordError" style="color: red; display: none;">Passwords do not match</div>
                    <input type="text" name="address" placeholder="Address" required><br>
                    <input type="number" name="contact" placeholder="Contact" required><br>
                    <label class="remember"><input type="checkbox" name="remember" value="remember">&nbsp I agree the
                        terms and conditions</label><br>
                    <button type="submit" id="submitButton">Register</button>
                </form>
            </div>
        </div>
    </div>
    <br>
</div>