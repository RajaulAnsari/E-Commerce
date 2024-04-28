<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: usersignin.php");
    exit();
}

// Fetch user's data from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM USER_CLECK WHERE USER_NAME = :username";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":username", $username);

if (oci_execute($stmt)) {
    // Fetch the user data
    $userData = oci_fetch_assoc($stmt);

    if ($userData) {
        // Display user's data
        $displayName = isset($userData['USER_NAME']) ? $userData['USER_NAME'] : '';
        $fullName = isset($userData['FIRST_NAME'], $userData['LAST_NAME']) ? $userData['FIRST_NAME'] . ' ' . $userData['LAST_NAME'] : '';
        $email = isset($userData['EMAIL_ADDRESS']) ? $userData['EMAIL_ADDRESS'] : '';
        $imagePath = isset($userData['USER_IMAGE']) ? $userData['USER_IMAGE'] : ''; // Corrected column name

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
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Image uploaded successfully, update image path in the database
            $imagePath = $targetFile;
            // Update the database with the new image path
            $updateSql = "UPDATE USER_CLECK SET USER_IMAGE = :IMAGE_PATH WHERE USER_NAME = :username";
            $updateStmt = oci_parse($conn, $updateSql);
            oci_bind_by_name($updateStmt, ":IMAGE_PATH", $imagePath);
            oci_bind_by_name($updateStmt, ":username", $username);
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


if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the login page after logout
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>E-Commerce | User-Homepage</title>
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
        justify-content: center;
        height: 60vh;
        text-align: center;
    }

    .user-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 15px 0px;
    }

    .user-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .userprofile-container h1 {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .userprofile-container p {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .userprofile-container button {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        background-color: #4CAF50;
        color: white;
        transition: background-color 0.3s;
    }

    .userprofile-container button:hover {
        background-color: #45a049;
    }

    .user-dashboard h2 {
        font-family: "Times New Roman", Times, serif;
        text-align: center;
        text-transform: uppercase;
        background-color: #7c7777;
        padding: 10px;
        margin: 0 auto;
        border-radius: 12px 12px 12px 12px;
        font-size: 35px;
        color: black;
        /* width: 800px; */
    }
    </style>
</head>

<body>

    <?PHP
    include "./components/navbar/nav.php";
    ?>

    <?PHP
    include "./components/searchbox/searchbox.php";
    ?>

    <div class="userprofile-container">
        <section></section>
        <div class="user-dashboard">
            <h2>User Dashboard</h2>
        </div>
        <section></section>

        <!-- Display user's image -->
        <div class="user-image">
            <?php

            // Check if image path is not empty and file exists
            if (!empty($imagePath) && file_exists($imagePath)) {
                echo "<img src='$imagePath' alt='User Image'>";
            } else {
                echo "<img src='./images/boy.webp' alt='Default User Image'>";
            }
            ?>
        </div>

        <!-- Image upload form -->
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="image" id="image">
            <button type="submit" name="upload">Upload Image</button>
        </form>

        <!-- Display user's data including the image -->
        <h1>Welcome, <?php echo ucfirst($userData['USER_NAME']); ?>😊</h1>
        <p>Name: <?php echo $userData['FIRST_NAME'] . " " . $userData['LAST_NAME']; ?></p>
        <p>Email: <?php echo $userData['EMAIL_ADDRESS']; ?></p>

        <!-- Logout form -->
        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
    </br>


    <?PHP
    include "./components/footer/footer.php";
    ?>


    <script src="./js/main.js" async defer></script>
</body>

</html>