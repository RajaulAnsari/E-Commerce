<section id="product-page">

    <?php
    include 'connection.php';

    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'none';
    $rating = isset($_GET['rating']) ? $_GET['rating'] : 'none';
    $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

    // Construct base SQL query
    // $sql = "SELECT * FROM product WHERE 1=1";
    // $sql1 = "SELECT * FROM review WHERE PRODUCT_ID = :product_id";
    // $sql ="select * from review r join product p on r.product_id = p.product_id where 1=1";
    $sql ="SELECT * FROM product p LEFT JOIN review r ON p.product_id=r.product_id WHERE 1=1";

    // Add category filter if selected
    if ($categoryFilter !== 'all') {
        $sql .= " AND CATEGORY_NAME = :categoryFilter";
    }

    // Add search filter if provided
    if (!empty($searchQuery)) {
        $sql .= " AND LOWER(product_name) LIKE LOWER(:searchQuery) OR LOWER(product_description) LIKE LOWER(:searchQuery) OR LOWER(category_name) LIKE LOWER(:searchQuery) ";
    }

    // Constructing SQL query with sorting
    if ($sort === 'price_asc') {
        $sql .= " ORDER BY product_price ASC";
    } elseif ($sort === 'price_desc') {
        $sql .= " ORDER BY product_price DESC";
    } elseif ($sort === 'rating_asc') {
        $sql .= " ORDER BY REVIEW_SCORE ASC";
    } elseif ($sort === 'rating_desc') {
        $sql .= " ORDER BY REVIEW_SCORE DESC";
    }

    $stmt = oci_parse($conn, $sql);

    // Bind parameters
    if ($categoryFilter !== 'all') {
        oci_bind_by_name($stmt, ":categoryFilter", $categoryFilter);
    }
    if (!empty($searchQuery)) {
        $searchQuery = "%$searchQuery%"; // Add wildcards to search query
        oci_bind_by_name($stmt, ":searchQuery", $searchQuery);
    }

    oci_execute($stmt);
    echo "<div class='container'>";
    echo "<div class='shop-page-title'>";
    echo "<h2>Products</h2>";
    echo "</div>";
    echo "</br>";

    echo "<div class='category-filter'>";
    echo "<form method='GET'>";
    echo "<label for='category'>Sort by Category : </label>";
    echo "<select name='category' id='category' onchange='this.form.submit()'>";
    echo "<option value='all' " . ($categoryFilter === 'all' ? 'selected' : '') . ">All</option>";
    echo "<option value='Fruits' " . ($categoryFilter === 'Fruits' ? 'selected' : '') . ">Fruits</option>";
    echo "<option value='Vegetables' " . ($categoryFilter === 'Vegetables' ? 'selected' : '') . ">Vegetables</option>";
    echo "</select>";

    echo "<label value='none'>&nbspSort by Price or Rating : </label>";
    echo "<select name='sort' id='sort' onchange='this.form.submit()'>";
    echo "<option value='none' " . ($sort === 'none' ? 'selected' : '') . ">None</option>";
    echo "<option value='price_asc' " . ($sort === 'price_asc' ? 'selected' : '') . ">Price: Low to High</option>";
    echo "<option value='price_desc' " . ($sort === 'price_desc' ? 'selected' : '') . ">Price: High to Low</option>";
    echo "<option value='rating_asc' " . ($sort === 'rating_asc' ? 'selected' : '') . ">Rating: Low to High</option>";
    echo "<option value='rating_desc' " . ($sort === 'rating_desc' ? 'selected' : '') . ">Rating: High to Low</option>";
    echo "</select>";

    echo "</form>";
    echo "</div>";
    echo "</br>";
    echo "</br>";

    echo "<div class='product-container'>";
    if (oci_fetch($stmt)) { // Check if there are any results
        do {
            echo "
            <div class='best-p1'>
                <img src='./images/Vegetables-Fruits/" . oci_result($stmt, 'PRODUCT_IMAGE') . "' alt='Product Photo'>
                <div class='best-p1-txt'>
                    <div class='name-of-p'>
                        <p>" . oci_result($stmt, 'PRODUCT_NAME') . "</p>
                    </div>
                    <div class='name-of-p'>
                        <p> Shop ID : " . oci_result($stmt, 'SHOP_ID') . "</p>
                    </div>
                    <div class='rating'>
                        " . generateStars(oci_result($stmt, 'REVIEW_SCORE')) . "
                    </div>
                    <div class='price'>
                        Price : $" . oci_result($stmt, 'PRODUCT_PRICE') . "
                    </div>
                    <div class='buy-now'>
                        <button class='add-to-cart' data-product-id='" . oci_result($stmt, 'PRODUCT_ID') ."'>Add To Cart</button>
                    </div>
                </div>
            </div>
            ";
        } while (oci_fetch($stmt));
    } else {
        echo "<p>No products found.</p>";
    }
    echo "</div>";

    oci_free_statement($stmt);
    oci_close($conn);
    echo "</div>";

    function generateStars($rating) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= ($i <= $rating) ? '<i class="bx bxs-star"></i>' : '<i class="bx bx-star"></i>';
        }
        return $stars;
    }
    ?>
</section>

<script>
document.querySelectorAll('.add-to-cart').forEach(item => {
    item.addEventListener('click', addToCart);
});

function addToCart() {
    const productId = this.getAttribute('data-product-id');
    // Debugging statements
    console.log('Product ID:', productId);

    // Send AJAX request to add the product to the cart
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Optionally, you can show a message or update the UI to indicate that the product was added to the cart
            alert('Product added to cart successfully');
        } else {
            console.error('Error adding product to cart');
        }
    };
    xhr.send(`product_id=${productId}`);
}
</script>