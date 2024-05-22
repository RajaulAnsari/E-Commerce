<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>E-Commerce | Contact Us</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./components/searchbox/searchbox.css">

    <style>
    .login-container {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        width: 350px;
        text-align: center;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    h1 {
        margin-bottom: 12px;
        font-size: 30px;
    }

    label {
        margin-bottom: 8px;
    }

    input {
        padding: 8px;
        margin-bottom: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #e6e6e4;
    }

    button {
        background-color: #357106;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    </style>
</head>

<body>

    <?php
    include "./components/navbar/nav.php";
    include "./components/searchbox/searchbox.php";
    ?>

    <br>
    <center>
        <div class="login-container">
            <h1>Contact Us</h1>
            <form action="https://api.web3forms.com/submit" method="POST">
                <input type="hidden" name="access_key" value="1ee057a5-3f8e-4cf4-b4ff-3fdb1258dc62">
                <input type="hidden" name="subject" value="New Contact from E-Commerce Website">
                <input type="hidden" name="redirect" value="http://localhost/E-Commerce/thanksForContactingUs.php">

                <label for="name">Name:</label>
                <input type="text" name="name" required></br>
                <label for="email">Email:</label>
                <input type="email" name="email" value="" required></br>
                <label for="message">Message:</label>
                <textarea rows="4" cols="50" name="message" required></textarea></br>
                <div class="h-captcha" data-captcha="true"></div>
                <button type="submit">Submit</button>
            </form>
        </div>
    </center>

    <br><br>

    <?php
    include "./components/footer/footer.php";
    ?>

    <script src="./js/main.js" async defer></script>
    <script src="./js/slider.js" async defer></script>
    <script src="https://web3forms.com/client/script.js" async defer></script>

</body>

</html>