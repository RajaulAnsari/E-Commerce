<br?PHP session_start(); ?>

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CleckHub | Thanks</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
        <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

        <link rel="stylesheet" href="./css/main.css">
        <link rel="stylesheet" href="./components/searchbox/searchbox.css">
        <link rel="stylesheet" href="./components/shop/shoppage.css">
        <style>
        .thanks {
            text-align: center;
            margin-top: 50px;
        }

        .thanks a {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
        }

        .thanks a:hover {
            background-color: #45a049;
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
        </br></br>
        </br></br>
        <div class="container">
            <div class="thanks">
                <h1>Thanks for contacting us!</h1>
                <p>We will get back to you as soon as possible.</p>
                </br>
                <a href='./index.php'>Go back to home</a>
            </div>
            </br></br>
            </br></br>


            <?PHP
    include "./components/footer/footer.php"; ?>


            <script src="./js/main.js" async defer></script>
    </body>

    </html>