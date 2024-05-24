<!-- Main page banner  -->
<?php
include 'connection.php';

$sql = "SELECT DISTINCT CATEGORY_NAME FROM PRODUCT";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
$product_category = array();
while ($row = oci_fetch_assoc($stmt)) {
    $product_categories[] = $row['CATEGORY_NAME'];
}
oci_free_statement($stmt);


$sqlr = "
SELECT * FROM (
    SELECT p.PRODUCT_ID, p.PRODUCT_NAME, p.PRODUCT_IMAGE, COUNT(DISTINCT ra.PRODUCT_ID) AS product_count
    FROM REVIEW_ACCESS ra
    LEFT JOIN PRODUCT p ON ra.PRODUCT_ID = p.PRODUCT_ID
    WHERE ra.IS_COLLECTED = 1
    GROUP BY p.PRODUCT_ID, p.PRODUCT_NAME, p.PRODUCT_IMAGE
    ORDER BY product_count DESC
) WHERE ROWNUM <= 4
";
$stmtr = oci_parse($conn, $sqlr);
oci_execute($stmtr);
$best_seller = array();
while ($row = oci_fetch_assoc($stmtr)) {
    $best_seller[] =array(
        'PRODUCT_NAME' => $row['PRODUCT_NAME'],
        'PRODUCT_IMAGE' => $row['PRODUCT_IMAGE'],
        'PRODUCT_ID' => $row['PRODUCT_ID']
    );
}
oci_free_statement($stmtr);


?>

<div class="container">
    <section>
        <div class="slideshow-container">
            <div class="mySlides fade">
                <img src="./images/HomeSlider/HomeSlider1.jpg" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="./images/HomeSlider/HomeSlider2.jpg" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="./images/HomeSlider/HomeSlider3.jpg" style="width:100%">
            </div>
            <div class="mySlides fade">
                <img src="./images/HomeSlider/HomeSlider4.jpeg" style="width:100%">
            </div>
        </div>
        <br>
        <div style="text-align:center">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <!-- <span class="dot"></span> -->

        </div>
    </section>

    <!-- collection -->
    <div class="container">
        <section id="collection">
            <h1>Product Categories</h1>
            </br>
            <div class="category-container">
                <?php
            // Assuming $product_categories is already populated with unique category names
            foreach ($product_categories as $category) {
                echo "<div class='category-item'>
                        <div class='buy-now'>
                            <button class='category-button' data-category='$category'>$category</button>
                        </div>
                      </div>";
            }
            ?>
            </div>
        </section>
    </div>


    <!-- Top sells -->
    <section id="sellers">
        <div class="seller container">
            <h2>Most Selling Products</h2>
            <div class="best-seller">
                <?php
            // Assuming $best_seller is already populated with the top 4 most selling products
            foreach ($best_seller as $product) {
                // Concatenate the image directory path with the filename
                $image_path = './images/products/' . $product['PRODUCT_IMAGE'];

                echo "<div class='seller-item' data-category='" . $product['PRODUCT_ID'] . "'>
                        <img src='" . $image_path . "' alt='" . $product['PRODUCT_NAME'] . "'>
                        <hr style='border-width: 2px;'>
                        <div class='buy-now1'>
                            <h1 class='seller-button'>" . $product['PRODUCT_NAME'] . "</h1>
                        </div>
                      </div>";
            }
            ?>
            </div>
        </div>
    </section>





    <!-- Maps -->

    <section id="maps">
        <div class="contact container">
            <div class="map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.8142914216164!2d85.31694337615131!3d27.692134076191408!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19b19295555f%3A0xabfe5f4b310f97de!2sThe%20British%20College%2C%20Kathmandu!5e0!3m2!1sen!2snp!4v1709404091285!5m2!1sen!2snp"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

</div>



<script>
// JavaScript code to handle category button clicks
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.category-button');

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            window.location.href = `products.php?category=${encodeURIComponent(category)}`;
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const topSellers = document.querySelectorAll('.best-seller .seller-item');

    topSellers.forEach(seller => {
        seller.addEventListener('click', function() {
            const productId = this.getAttribute('data-category');
            window.location.href = `productdetails.php?product_id=${productId}`;
        });
    });
});
</script>