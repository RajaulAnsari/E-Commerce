<?PHP
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CleckHub | T&C</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./components/searchbox/searchbox.css">
    <link rel="stylesheet" href="./components/product/all_products.css">

    <style>
    .cttag li {
        list-style-type: disc;
        /* Disc for standard round bullets */
        margin-left: 50px;
        /* content: justify; */
        justify-content: justify;
    }
    </style>

</head>

<body>

    <?php
    include "./components/navbar/nav.php";
    ?>
    <?php
    include "./components/searchbox/searchbox.php";
    ?>

    <div class="container"><br>
        <h1 style=font-size:50px>Terms and Conditions</h1>

        Effective Date: <b>5/14/2024</b> <br><br>

        <br>
        <cttag class="cttag">
            <h2> 1. Use of the Website </h2>
            <ul>
                <li> By accessing or using the Website, you agree to comply with and be bound by these Terms and
                    Conditions.</li>
                <li> Users must be at least 18 years of age to use the Website.</li>
                <li> Users are required to provide accurate, current, and complete information when registering an
                    account or making a purchase on the Website.</li>
            </ul>

            <br>
            <h2> 2. Intellectual Property </h2>
            <ul>
                <li> All content, including text, graphics, logos, images, and software, available on the Website is
                    the
                    exclusive property of Cleckhub and is protected by intellectual property laws.</li>
                <li> Users are strictly prohibited from copying, reproducing, modifying, distributing, or displaying
                    any
                    content from the Website without prior written consent from Cleckhub.</li>
            </ul>

            <br>
            <h2> 3. User Conduct </h2>
            <ul>
                <li> Users agree to use the Website solely for lawful purposes and in compliance with all applicable
                    laws and regulations.</li>
                <li> Users shall not engage in any activity that disrupts, interferes with, or harms the operation
                    of
                    the Website or infringes upon the rights of other users.</li>
            </ul>

            <br>
            <h2> 4. Product Listings and Transactions </h2>
            <ul>
                <li> Cleckhub does not warrant the accuracy, completeness, or reliability of any product or service
                    listed on the Website.</li>
                <li> Users acknowledge and agree that all transactions conducted through the Website are at their
                    own
                    risk, and Cleckhub shall not be liable for any damages or losses arising from such transactions.
                </li>
            </ul>

            <br>
            <h2> 5. Limitation of Liability </h2>
            <ul>
                <li> In no event shall Cleckhub, its developers, or affiliates be liable for any indirect,
                    incidental,
                    special, consequential, or punitive damages arising out of or in connection with the use of the
                    Website.</li>
                <li> Cleckhub shall not be liable for any damages resulting from the inability to use the website,
                    unauthorized access to or alteration of user transmissions, or any other matter relating to the
                    Website.</li>
            </ul>

            <br>
            <h2> 6. Indemnification </h2>
            <ul>
                <li> Users agree to indemnify, defend, and hold harmless Cleckhub, its developers, and affiliates
                    from
                    and against any and all claims, damages, liabilities, costs, or expenses (including attorneys'
                    fees)
                    arising out of or related to the use of the Website or any breach of these Terms and Conditions.
                </li>
            </ul>

            <br>
            <h2> 7. Governing Law and Jurisdiction </h2>
            <ul>
                <li> These Terms and Conditions shall be governed by and construed in accordance with the laws of
                    [Our
                    Jurisdiction], without regard to its conflict of law provisions.</li>
                <li> Any dispute arising out of or relating to these Terms and Conditions shall be exclusively
                    resolved
                    behind closed doors.</li>
            </ul>

            <br>
            <h2> 8. Changes to Terms and Conditions </h2>
            <ul>
                <li> Cleckhub reserves the right to update or modify these Terms and Conditions at any time without
                    prior notice.</li>
                <li> Users are encouraged to review the Terms and Conditions periodically for any changes. Continued
                    use
                    of the Website following the posting of changes constitutes acceptance of those changes.</li>
            </ul>

            <br>
            <h2> 9. Contact Information </h2>
            <ul>
                <li> If you have any questions or concerns about these Terms and Conditions, please contact any of
                    the
                    developors.</li>
            </ul>
        </cttag>
    </div>

    <br><br>
    <?PHP
    include "./components/footer/footer.php";
    ?>
    <script src="./js/main.js" async defer></script>

</body>