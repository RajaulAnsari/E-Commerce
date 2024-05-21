<?php
include 'connection.php';

$shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : null;
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'none';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Construct base SQL query
$sql = "SELECT p.*, 
        COALESCE(avg_review.avg_review_score, 0) AS average_review_score,
        CASE 
            WHEN d.DISCOUNT_AMOUNT IS NULL THEN p.product_price
            ELSE p.product_price - d.DISCOUNT_AMOUNT
        END AS discounted_price
        FROM product p 
        LEFT JOIN (
            SELECT product_id, AVG(REVIEW_SCORE) AS avg_review_score
            FROM review
            GROUP BY product_id
        ) avg_review ON p.product_id = avg_review.product_id
        LEFT JOIN discount d ON p.product_id = d.product_id
        WHERE p.PRODUCT_ADMIN_VERIFICATION = 1";

// Filter by shop ID
if ($shop_id) {
    $sql .= " AND p.SHOP_ID = :shop_id";
}

// Add category filter if selected
if ($categoryFilter !== 'all') {
    $sql .= " AND LOWER(p.category_name) = LOWER(:categoryFilter)";
}

// Add search filter if provided
if (!empty($searchQuery)) {
    $sql .= " AND (LOWER(p.product_name) LIKE LOWER(:searchQuery) 
            OR LOWER(p.product_description) LIKE LOWER(:searchQuery) 
            OR LOWER(p.category_name) LIKE LOWER(:searchQuery))";
}

// Constructing SQL query with sorting
if ($sort === 'price_asc') {
    $sql .= " ORDER BY discounted_price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY discounted_price DESC";
} elseif ($sort === 'rating_asc') {
    $sql .= " ORDER BY avg_review.avg_review_score ASC";
} elseif ($sort === 'rating_desc') {
    $sql .= " ORDER BY avg_review.avg_review_score DESC";
} elseif ($sort === 'discount_price_asc') {
    $sql .= " ORDER BY discounted_price ASC";
} elseif ($sort === 'discount_price_desc') {
    $sql .= " ORDER BY discounted_price DESC";
}

$stmt = oci_parse($conn, $sql);

// Bind parameters
if ($shop_id) {
    oci_bind_by_name($stmt, ":shop_id", $shop_id);
}
if ($categoryFilter !== 'all') {

    oci_bind_by_name($stmt, ":categoryFilter", $categoryFilter);
}
if (!empty($searchQuery)) {
    $searchQuery = "%$searchQuery%"; // Add wildcards to search query
    oci_bind_by_name($stmt, ":searchQuery", $searchQuery);
}

oci_execute($stmt);

echo "<br>";
echo "<div class='container'>";
echo "<div class='shop-page-title'>";
echo "<h2>Products</h2>";
echo "</div>";
echo "<br>";

echo "<div class='category-filter'>";
echo "<form method='GET'>";
echo "<input type='hidden' name='shop_id' value='" . htmlspecialchars($shop_id) . "'>";
echo "<label for='category'>Sort by Category: </label>";
echo "<select name='category' id='category' onchange='this.form.submit()'>";
echo "<option value='all' " . ($categoryFilter === 'all' ? 'selected' : '') . ">All</option>";
echo "<option value='Fruits' " . ($categoryFilter === 'Fruits' ? 'selected' : '') . ">Fruits</option>";
echo "<option value='Vegetables' " . ($categoryFilter === 'Vegetables' ? 'selected' : '') . ">Vegetables</option>";
echo "<option value='Fish' " . ($categoryFilter === 'Fish' ? 'selected' : '') . ">Fish</option>";
echo "<option value='Bakery' " . ($categoryFilter === 'Bakery' ? 'selected' : '') . ">Bakery</option>";
echo "<option value='Meat' " . ($categoryFilter === 'Meat' ? 'selected' : '') . ">Meat</option>";
echo "</select>";

echo "<label for='sort'>&nbspSort by Price or Rating: </label>";
echo "<select name='sort' id='sort' onchange='this.form.submit()'>";
echo "<option value='none' " . ($sort === 'none' ? 'selected' : '') . ">None</option>";
echo "<option value='rating_asc' " . ($sort === 'rating_asc' ? 'selected' : '') . ">Rating: Low to High</option>";
echo "<option value='rating_desc' " . ($sort === 'rating_desc' ? 'selected' : '') . ">Rating: High to Low</option>";
echo "<option value='discount_price_asc' " . ($sort === 'discount_price_asc' ? 'selected' : '') . ">Price: Low to High</option>";
echo "<option value='discount_price_desc' " . ($sort === 'discount_price_desc' ? 'selected' : '') . ">Price: High to Low</option>";
echo "</select>";
echo "</form>";
echo "</div>";
echo "<br><br>";

echo "<div class='product-container'>";
$hasResults = false;
while ($row = oci_fetch_assoc($stmt)) {
    $hasResults = true;
    echo "
    <div class='best-p1'>
    <a href='productdetails.php?product_id=" . htmlspecialchars($row['PRODUCT_ID']) . "'>
        <img src='./images/Vegetables-Fruits/" . htmlspecialchars($row['PRODUCT_IMAGE']) . "' alt='Product Photo'>
    </a>
        <div class='best-p1-txt'>
            <div class='name-of-p'>
                <p>" . htmlspecialchars($row['PRODUCT_NAME']) . "</p>
            </div>
            <div class='name-of-p'>
                <p> Shop ID: " . htmlspecialchars($row['SHOP_ID']) . "</p>
            </div>
            <div class='rating'>
                " . generateStars($row['AVERAGE_REVIEW_SCORE']) . "
            </div>
            <div class='price' style='font-size:20px;'>
                Price: <span style='text-decoration: line-through;'>$" . htmlspecialchars($row['PRODUCT_PRICE']) . "</span>
                &nbsp&nbsp&nbsp $" . htmlspecialchars($row['DISCOUNTED_PRICE']) . "
            </div>";
            
    // Check if user is logged in
    if (isset($_SESSION['uusername'])) {
        echo "<div class='buy-now'>
            <button class='add-to-cart' data-product-id='" . htmlspecialchars($row['PRODUCT_ID']) . "'><i class='bx bxs-cart bx-border-circle bx-tada-hover'></i></button>
            <button class='add-to-wishlist' data-product-id='" . htmlspecialchars($row['PRODUCT_ID']) . "'><i class='bx bxs-heart bx-border-circle bx-tada-hover'></i></button>
        </div>";
    } else {
        echo "<div class='buy-now'>
            <button class='addToCartButton'><i class='bx bxs-cart bx-border-circle bx-tada-hover'></i></button>
            <button class='addToWishlistButton'><i class='bx bxs-heart bx-border-circle bx-tada-hover'></i></button>
        </div>";
    }

    echo "</div>
</div>";
}
if (!$hasResults) {
    echo "<p>No products found.</p>";
}
echo "</div><br><br>";

oci_free_statement($stmt);
oci_close($conn);
echo "</div>";

function generateStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= round($rating)) ? '<i class="bx bxs-star"></i>' : '<i class="bx bx-star"></i>';
    }
    return $stars;
}
?>
</section>

<script>
document.querySelectorAll('.add-to-cart').forEach(item => {
    item.addEventListener('click', addToCart);
});

document.querySelectorAll('.add-to-wishlist').forEach(item => {
    item.addEventListener('click', addToWishlist);
});

function addToCart() {
    const productId = this.getAttribute('data-product-id');
    console.log('Product ID:', productId);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cart.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Product added to cart successfully');
            window.location.reload();
        } else {
            console.error('Error adding product to cart');
        }
    };
    xhr.send(`product_id=${productId}`);
}

function addToWishlist() {
    const productId = this.getAttribute('data-product-id');
    console.log('Product ID:', productId);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'wishlist.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Product added to wishlist successfully');
            window.location.reload();
        } else {
            console.error('Error adding product to wishlist');
        }
    };
    xhr.send(`product_id=${productId}`);
}

document.querySelectorAll('.addToCartButton').forEach(item => {
    item.addEventListener('click', function() {
        // Redirect to usersignin.php
        alert('Please sign in to add product to cart');
        window.location.href = 'usersignin.php';
    });
});

document.querySelectorAll('.addToWishlistButton').forEach(item => {
    item.addEventListener('click', function() {
        // Redirect to usersignin.php
        alert('Please sign in to add product to wishlist');
        window.location.href = 'usersignin.php';
    });
});
</script>