<?php
include './mailing.php';
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
    $productcategories = $_POST['productcategories'];
    $shopname = $_POST['shopname'];
    $shopaddress = $_POST['shopaddress'];
    $shopdescription = $_POST['shopdescription'];
    $shopimage = $_FILES['shopimage']['name'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<p style='color: red;'>Passwords do not match</p>";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement for TRADER
        $sql_trader = "INSERT INTO \"TRADER\" 
                       (TRADER_ID, TRADER_FIRST_NAME, TRADER_LAST_NAME, EMAIL_ADDRESS, TRADER_PASSWORD, TRADER_ADDRESS, CONTACT_NO, IS_VERIFIED, PRODUCT_CATEGORY, SHOP_NAME, TUSER_NAME) 
                       VALUES (TRADER_ID_SEQ.NEXTVAL, :firstname, :lastname, :email, :password, :address, :contact, 0, :productcategories, :shopname, :username)
                       RETURNING TRADER_ID INTO :trader_id";

        $stmt_trader = oci_parse($conn, $sql_trader);

        // Bind parameters
        oci_bind_by_name($stmt_trader, ":firstname", $firstname);
        oci_bind_by_name($stmt_trader, ":lastname", $lastname);
        oci_bind_by_name($stmt_trader, ":username", $username);
        oci_bind_by_name($stmt_trader, ":email", $email);
        oci_bind_by_name($stmt_trader, ":password", $hashed_password);
        oci_bind_by_name($stmt_trader, ":address", $address);
        oci_bind_by_name($stmt_trader, ":contact", $contact);
        oci_bind_by_name($stmt_trader, ":productcategories", $productcategories);
        oci_bind_by_name($stmt_trader, ":shopname", $shopname);

        // Bind the TRADER_ID output parameter
        oci_bind_by_name($stmt_trader, ":trader_id", $trader_id, -1, OCI_B_INT);

        // Execute the statement
        $result_trader = oci_execute($stmt_trader);

        if ($result_trader) {
            // Upload shop image
            if (!empty($shopimage)) {
                $target_dir = "./images/shop/";
                $target_file = $target_dir . basename($shopimage);
                move_uploaded_file($_FILES["shopimage"]["tmp_name"], $target_file);

                // Store only the basename of the uploaded file
                $shopimage_name = basename($shopimage);
            } else {
                $shopimage_name = null;
            }

            // Prepare the SQL statement for SHOP
            $sql_shop = "INSERT INTO \"SHOP\" 
                         (SHOP_ID, SHOP_NAME, SHOP_ADDRESS, PHONE_NUMBER, SHOP_DESCRIPTION, USER_ID, SHOP_IMAGE) 
                         VALUES (SHOP_ID_SEQ.NEXTVAL, :shopname, :shopaddress, :contact, :shopdescription, :userid, :shopimage)
                         RETURNING SHOP_ID INTO :shop_id";

            $stmt_shop = oci_parse($conn, $sql_shop);

            // Bind parameters
            oci_bind_by_name($stmt_shop, ":shopname", $shopname);
            oci_bind_by_name($stmt_shop, ":shopaddress", $shopaddress);
            oci_bind_by_name($stmt_shop, ":contact", $contact);
            oci_bind_by_name($stmt_shop, ":shopdescription", $shopdescription);
            oci_bind_by_name($stmt_shop, ":userid", $trader_id);
            oci_bind_by_name($stmt_shop, ":shopimage", $shopimage_name);

            // Bind the SHOP_ID output parameter
            oci_bind_by_name($stmt_shop, ":shop_id", $shop_id, -1, OCI_B_INT);

            // Execute the statement
            $result_shop = oci_execute($stmt_shop);

            if ($result_shop) {
                // Update the TRADER table with the SHOP_ID
                $sql_update_trader = "UPDATE \"TRADER\" SET SHOP_ID = :shop_id WHERE TRADER_ID = :trader_id";
                $stmt_update_trader = oci_parse($conn, $sql_update_trader);
                oci_bind_by_name($stmt_update_trader, ":shop_id", $shop_id);
                oci_bind_by_name($stmt_update_trader, ":trader_id", $trader_id);
                oci_execute($stmt_update_trader);

                // Define email subject and body
                $subject = "Verify Your Email Address";
                $html = "
                <div style='font-family: Arial, sans-serif; border: 2px solid #007bff; border-radius: 10px; padding: 20px; background-color: #f9f9f9;'>
                    <h2 style='color: green; margin-bottom: 20px;'>Welcome to CleckHub!</h2>
                    <p>Hello $firstname,</p>
                    <p>Thank you for registering with CleckHub. To complete your registration, please click the button below to verify your email address:</p>
                    <a href='http://{$_SERVER['HTTP_HOST']}/E-Commerce/traderverify.php?email=" . urlencode($email) . "' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Verify Email Address</a>
                    <p style='margin-top: 20px; font-size: 14px; color: #777;'>If you did not register for CleckHub, please ignore this email.</p>
                </div>
                ";

                // Send verification email
                sendVerificationEmail($email, $subject, $html);

                echo "<script>alert('Registration successful! Verification Required.');</script>";
                echo "<script>window.location = './tradersignin.php'</script>";
            } else {
                echo "Error: Unable to register shop. Please try again later.";
            }
        } else {
            echo "Error: Unable to register trader. Please try again later.";
        }

        oci_free_statement($stmt_trader);
        oci_free_statement($stmt_shop);
        oci_free_statement($stmt_update_trader);
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
            <p>Not A Trader? <a href="./tradersignin.php">Login Now</a></p>
        </div>
        <div class="image-container">
            <img src="./images/User-Trader/Traders.png" alt="banner">
        </div>
        <div style="width: 30px;"></div>
        <div class="form-container">
            <div class="user-form">
                <form class="uform" id="registrationForm" method="post" enctype="multipart/form-data"
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
                    <input type="tel" name="contact" placeholder="Contact" required><br>

                    <select name="productcategories" required>
                        <option value="" disabled selected>Select Product Category</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Meat">Meat</option>
                    </select><br>

                    <input type="text" name="shopname" placeholder="Shop Name" required><br>
                    <input type="text" name="shopaddress" placeholder="Shop Address" required><br>
                    <input type="text" name="shopdescription" placeholder="Shop Description" required><br>
                    <input type="file" name="shopimage" placeholder="Shop Image"><br>
                    <label class="remember"><input type="checkbox" name="remember" value="remember">&nbsp I agree to the
                        terms and conditions</label><br>
                    <button type="submit" id="submitButton">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
</br>