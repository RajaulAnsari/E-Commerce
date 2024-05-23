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
oci_close($conn);
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
            <h2>Top Sales</h2>
            <div class="best-seller">
                <div class="best-p1">
                    <img src="./images/Vegetables-Fruits/POIRE.jpg" alt="img">
                    <div class="best-p1-txt">
                        <div class="name-of-p">
                            <p>POIRE</p>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bx-star'></i>
                            <i class='bx bx-star'></i>
                        </div>
                        <div class="price">
                            &dollar;3.24
                            <div class="colors">
                                <i class='bx bxs-circle red'></i>
                                <i class='bx bxs-circle blue'></i>
                                <i class='bx bxs-circle white'></i>
                            </div>
                        </div>
                        <div class="buy-now">
                            <button><a href="https://codepen.io/sanketbodke/full/mdprZOq">Buy Now</a></button>
                        </div>
                    </div>
                </div>
                <div class="best-p1">
                    <img src="./images/Vegetables-Fruits/POMME.jpg" alt="img">
                    <div class="best-p1-txt">
                        <div class="name-of-p">
                            <p>POMME</p>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bx-star'></i>
                            <i class='bx bx-star'></i>
                            <i class='bx bx-star'></i>
                        </div>
                        <div class="price">
                            &dollar;1.24
                            <div class="colors">
                                <i class='bx bxs-circle green'></i>
                                <i class='bx bxs-circle grey'></i>
                                <i class='bx bxs-circle brown'></i>
                            </div>
                        </div>
                        <div class="buy-now">
                            <button><a href="https://codepen.io/sanketbodke/full/mdprZOq">Buy Now</a></button>
                        </div>
                    </div>
                </div>
                <div class="best-p1">
                    <img src="./images/Vegetables-Fruits/PRUNE.jpg" alt="img">
                    <div class="best-p1-txt">
                        <div class="name-of-p">
                            <p>PRUNE</p>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bx-star'></i>
                        </div>
                        <div class="price">
                            &dollar;2.24
                            <div class="colors">
                                <i class='bx bxs-circle brown'></i>
                                <i class='bx bxs-circle green'></i>
                                <i class='bx bxs-circle blue'></i>
                            </div>
                        </div>
                        <div class="buy-now">
                            <button><a href="https://codepen.io/sanketbodke/full/mdprZOq">Buy Now</a></button>
                        </div>
                    </div>
                </div>
                <div class="best-p1">
                    <img src="./images/Vegetables-Fruits/RAISIN.jpg" alt="img">
                    <div class="best-p1-txt">
                        <div class="name-of-p">
                            <p>RAISIN</p>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                        <div class="price">
                            &dollar;3.67
                            <div class="colors">
                                <i class='bx bxs-circle red'></i>
                                <i class='bx bxs-circle grey'></i>
                                <i class='bx bxs-circle blue'></i>
                            </div>
                        </div>
                        <div class="buy-now">
                            <button><a href="https://codepen.io/sanketbodke/full/mdprZOq">Buy Now</a></button>
                        </div>
                    </div>
                </div>
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
</script>