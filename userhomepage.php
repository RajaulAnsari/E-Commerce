<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['uusername'])) {
    // Redirect to the login page if not logged in
    header("Location: usersignin.php");
    exit();
}

// Fetch user's data from the database
$uusername = $_SESSION['uusername'];
$sql = "SELECT * FROM USER_CLECK WHERE UUSER_NAME = :uusername";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":uusername", $uusername);

if (oci_execute($stmt)) {
    // Fetch the user data
    $userData = oci_fetch_assoc($stmt);

    if ($userData) {
        // Display user's data
        $displayName = isset($userData['UUSER_NAME']) ? $userData['UUSER_NAME'] : '';
        $fullName = isset($userData['FIRST_NAME'], $userData['LAST_NAME']) ? $userData['FIRST_NAME'] . ' ' . $userData['LAST_NAME'] : '';
        $email = isset($userData['EMAIL_ADDRESS']) ? $userData['EMAIL_ADDRESS'] : '';
        $imagePath = isset($userData['USER_IMAGE']) ? $userData['USER_IMAGE'] : '';
        $phone = isset($userData['PHONE_NUMBER']) ? $userData['PHONE_NUMBER'] : '';
        $address = isset($userData['ADDRESS']) ? $userData['ADDRESS'] : '';


        // Close the statement
        oci_free_statement($stmt);
    } else {
        echo "User data not found.";
        exit();
    }
} else {
    echo "Error executing SQL query: " . oci_error($stmt);
    exit();
}

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $targetDirectory = "./images/UserProfile/"; // Directory where images will be stored
    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>alert ('File is not an image.')</script>";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 1000000) {
        echo "<script>alert('Sorry, your file is too large. Please upload image size <=1MB')</script>";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "<script> alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    elseif ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.')</script>";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Image uploaded successfully, update image path in the database
            $imagePath = basename($targetFile);
            // Update the database with the new image path
            $updateSql = "UPDATE USER_CLECK SET USER_IMAGE = :IMAGE_PATH WHERE UUSER_NAME = :uusername";
            $updateStmt = oci_parse($conn, $updateSql);
            oci_bind_by_name($updateStmt, ":IMAGE_PATH", $imagePath);
            oci_bind_by_name($updateStmt, ":uusername", $uusername);
            oci_execute($updateStmt);
            oci_free_statement($updateStmt);
            // Refresh the page to display the new image
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}


// Handle phone number, address, and password updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $newPhone = $_POST["new_phone"];
    $newAddress = $_POST["new_address"];
    $newPassword = $_POST["new_password"];

    // Update phone number
    $updatePhoneSql = "UPDATE USER_CLECK SET PHONE_NUMBER = :new_phone WHERE UUSER_NAME = :uusername";
    $updatePhoneStmt = oci_parse($conn, $updatePhoneSql);
    oci_bind_by_name($updatePhoneStmt, ":new_phone", $newPhone);
    oci_bind_by_name($updatePhoneStmt, ":uusername", $uusername);
    oci_execute($updatePhoneStmt);
    oci_free_statement($updatePhoneStmt);

    // Update address
    $updateAddressSql = "UPDATE USER_CLECK SET ADDRESS = :new_address WHERE UUSER_NAME = :uusername";
    $updateAddressStmt = oci_parse($conn, $updateAddressSql);
    oci_bind_by_name($updateAddressStmt, ":new_address", $newAddress);
    oci_bind_by_name($updateAddressStmt, ":uusername", $uusername);
    oci_execute($updateAddressStmt);
    oci_free_statement($updateAddressStmt);

    // Redirect to refresh the page and display updated information
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}



if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the login page after logout
    header("Location: index.php");
    exit();
}

if (isset($_POST['wishlist'])) {
    // Redirect to the wishlist page
    header("Location: wishlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CleckHub | User-Homepage</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./components/account/signin/user-trader-signin.css">
    <link rel="stylesheet" href="./components/searchbox/searchbox.css">

    <style>
    .userprofile-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        margin: 50px auto;
        max-width: 500px;
    }

    .user-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .user-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .userprofile-container h1 {
        font-size: 28px;
        margin-bottom: 10px;
        color: black;
    }

    .userprofile-container p {
        font-size: 18px;
        margin-bottom: 10px;
        color: black;
    }

    .userprofile-container button,
    .edit button {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 25px;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: black;
        transition: background 0.3s, transform 0.3s;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
    }

    .userprofile-container button:hover,
    .edit button:hover {
        background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
        transform: translateY(-2px);
    }

    .user-dashboard h2 {
        font-family: "Times New Roman", Times, serif;
        text-align: center;
        text-transform: uppercase;
        background-color: #00ADB5;
        padding: 10px;
        border-radius: 12px;
        font-size: 35px;
        color: white;
        width: 100%;
        margin-bottom: 20px;
        color: black;
    }

    .edit input {
        width: calc(100% - 30px);
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 25px;
        background-color: #f1f1f1;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .edit input:focus {
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .edit label {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
        color: black;
    }

    .edit {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .edit .field-container {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .edit button.edit-btn {
        width: 80px;
        padding: 10px 0;
    }

    form {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    form input[type="file"] {
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 25px;
        padding: 10px;
        background-color: #f1f1f1;
        width: 100%;
    }

    form button {
        width: 100%;
        margin-top: 10px;
    }
    </style>
</head>

<body>

    <?PHP include "./components/navbar/nav.php"; ?>
    <?PHP include "./components/searchbox/searchbox.php"; ?>

    <div class="userprofile-container">
        <!-- <section></section> -->
        <div class="user-dashboard">
            <h2>User Dashboard</h2>
        </div>
        <!-- <section></section> -->
        <div class="user-image">
            <?php
            if (!empty($imagePath) && file_exists($imagePath)) {
                echo "<img src='$imagePath' alt='User Image'>";
            } else {
                echo "<img src='./images/boy.webp' alt='Default User Image'>";
            }
            ?>
        </div>
        <!-- </br> -->
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="image" id="image" required>
            <button type="submit" name="upload">Upload Image</button>
        </form>

        <h1>Welcome, <?php echo ucfirst($userData['UUSER_NAME']); ?>😊</h1>
        <p>Name: <?php echo $fullName; ?></p>
        <p>Email: <?php echo $email; ?></p>

        <form method="post" class="edit">
            <!-- <div class="field-container"> -->
            <label for="new_phone">Phone Number:</label>
            <input type="text" id="new_phone" name="new_phone" value="<?php echo $phone; ?>" readonly>
            <button type="button" onclick="toggleEditable('new_phone')">Edit</button><br><br>
            <!-- </div>
            <div class="field-container"> -->
            <label for="new_address">Address: </label>&nbsp
            <input type="text" id="new_address" name="new_address" value="<?php echo $address; ?>" readonly>
            <button type="button" onclick="toggleEditable('new_address')">Edit</button><br><br>
            <!-- </div> -->
            <button type="submit" name="update_profile">Update Profile</button>
        </form>
        <br>
        <form method="post">
            <button type="submit" name="wishlist">My Wishlist</button>
        </form>

        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>

    <div class="footer">
        <?PHP include "./components/footer/footer.php"; ?>
    </div>

    <script src="./js/main.js" async defer></script>
    <script>
    function toggleEditable(fieldId) {
        var field = document.getElementById(fieldId);
        field.readOnly = !field.readOnly;
        if (!field.readOnly) {
            field.focus();
        }
    }
    </script>
</body>

</html>