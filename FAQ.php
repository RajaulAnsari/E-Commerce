<?php
session_start();
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CleckHub | FAQ</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./components/searchbox/searchbox.css">

    <style>
    .faq-section {
        width: 100%;
        max-width: 700px;
    }

    .box {
        background-color: #f8f8f8;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .question {
        font-weight: bold;
        margin-bottom: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .answer {
        display: none;
        margin-top: 10px;
        /* <br> for question and answer */
    }

    .question.active+.answer {
        display: block;
    }

    .arrow:before {
        content: '\25B6';
        /* ▶️ unicode */
        float: right;
    }

    .question.active .arrow:before {
        content: '\25BC';
        /* Unicode 🔽 */
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

    <center>
        <h1 style=font-size:70px>FAQ</h1>
    </center>


    <br>
    <center>
        <div class="container">
            <div class="faq-section">
                <div class="box">
                    <div class="question" onclick="toggleAnswer(this)">1. What products/services do you offer? <span
                            class="arrow"></span></div>
                    <div class="answer">
                        We offer a diverse range of products and services to meet the needs of the customers. These
                        typically include groceries such as fresh produce, dairy, meat, seafood, and packaged foods,
                        alongside household essentials like cleaning supplies, paper products, and personal care items.
                        We also provide special discounts and offers.

                    </div>
                </div>
            </div>

            <div class="faq-section">
                <div class="box">
                    <div class="question" onclick="toggleAnswer(this)">2. What payment methods do you accept? <span
                            class="arrow"></span></div>
                    <div class="answer">
                        We currently accept PayPal as our sole payment method. This allows for secure and convenient
                        transactions for our customers. If you have any questions or need assistance with using PayPal,
                        please feel free to contact us.
                    </div>
                </div>
            </div>

            <div class="faq-section">
                <div class="box">
                    <div class="question" onclick="toggleAnswer(this)">3. What is your return/exchange policy? <span
                            class="arrow"></span></div>
                    <div class="answer">
                        We currently do not accept returns or exchanges. All sales are final. If you have any questions
                        or concerns about a product you purchased, please contact our customer service team for
                        assistance.
                    </div>
                </div>
            </div>

            <div class="faq-section">
                <div class="box">
                    <div class="question" onclick="toggleAnswer(this)">4. How can I track my order? <span
                            class="arrow"></span></div>
                    <div class="answer">
                        As we do not offer delivery services, there is no order tracking available. All purchases must
                        be made and picked up in-store.
                    </div>
                </div>
            </div>

            <div class="faq-section">
                <div class="box">
                    <div class="question" onclick="toggleAnswer(this)">5. How can I contact customer support?<span
                            class="arrow"></span></div>
                    <div class="answer">
                        You can contact our customer support team by visiting our Contact Us page. We are available to
                        assist you with any questions or concerns you may have.
                    </div>
                </div>
            </div>

        </div>
    </center>
    <br>
    <center>
        <h5><a href="contactus.php"><i> Issue not resolved?</i></a></h5>
    </center>
    <br><br>

    <?php
    include "./components/footer/footer.php";
    ?>
    <script src="./js/main.js" async defer></script>

    <script>
    function toggleAnswer(question) {
        var answer = question.nextElementSibling;
        if (answer.style.display === "block") {
            answer.style.display = "none";
        } else {
            answer.style.display = "block";
        }
    }
    </script>

</body>