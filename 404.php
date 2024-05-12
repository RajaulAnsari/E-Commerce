<?PHP
session_start();
?>


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CleckHub | About</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./components/searchbox/searchbox.css">

</head>

<body>




    <?PHP
    include "./components/navbar/nav.php";
    ?>
    <?PHP
    include "./components/searchbox/searchbox.php";
    ?>
    <center>
        <img src="./images/404.png" alt="404" style="width: 50%; height: 50%;">
        <h3>OOPS! look like you are lost.</h3>
    </center>
    <br><br>


    <?PHP
    include "./components/footer/footer.php";
    ?>
    <script src="./js/main.js" async defer></script>





</body>