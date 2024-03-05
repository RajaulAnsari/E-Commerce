<?PHP
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>E-Commerce</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icon.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    
    <link rel="stylesheet" href="./css/main.css">
    <!-- <link rel="stylesheet" href="../components/navbar/nav.css"> -->

    
  
</head>

<body>

    <?PHP
    require "./components/navbar/nav.php";
    ?>

    <?PHP
    require "./components/body/body.php";
    ?>

    <?PHP
    require "./components/footer/footer.php";
    ?>


    <script src="./js/main.js" async defer></script>
</body>

</html>