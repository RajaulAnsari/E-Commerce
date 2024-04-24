<?php
echo "<div class='container'>";
include 'connection.php';

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'none';
$rating = isset($_GET['rating']) ? $_GET['rating'] : 'none';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Construct base SQL query
$sql = "SELECT * FROM product WHERE 1=1";

// Add category filter if selected
if ($categoryFilter !== 'all') {
    $sql .= " AND categoryname = :categoryFilter";
}

// Add search filter if provided
if (!empty($searchQuery)) {
    $sql .= " AND LOWER(productname) LIKE LOWER(:searchQuery)";
}

// Constructing SQL query with sorting
if ($sort === 'price_asc') {
    $sql .= " ORDER BY productprice ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY productprice DESC";
}

if ($rating === 'rating_asc') {
    $sql .= " ORDER BY rating ASC";
} elseif ($rating === 'rating_desc') {
    $sql .= " ORDER BY rating DESC";
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

echo "<div class='category-filter'>";
echo "<form method='GET'>";
echo "<label for='category'>Sort by Category : </label>";
echo "<select name='category' id='category' onchange='this.form.submit()'>";
echo "<option value='all' " . ($categoryFilter === 'all' ? 'selected' : '') . ">All</option>";
echo "<option value='fruits' " . ($categoryFilter === 'fruits' ? 'selected' : '') . ">Fruits</option>";
echo "<option value='vegetables' " . ($categoryFilter === 'vegetables' ? 'selected' : '') . ">Vegetables</option>";
echo "</select>";

echo "<label value='none'>Sort by Price : </label>";
echo "<select name='sort' id='sort' onchange='this.form.submit()'>";
echo "<option value='none' " . ($sort === 'none' ? 'selected' : '') . ">None</option>";
echo "<option value='price_asc' " . ($sort === 'price_asc' ? 'selected' : '') . ">Price: Low to High</option>";
echo "<option value='price_desc' " . ($sort === 'price_desc' ? 'selected' : '') . ">Price: High to Low</option>";
echo "</select>";

echo "<label value='none'>Sort by Rating : </label>";
echo "<select name='rating' id='rating' onchange='this.form.submit()'>";
echo "<option value='none' " . ($rating === 'none' ? 'selected' : '') . ">None</option>";
echo "<option value='rating_asc' " . ($rating === 'rating_asc' ? 'selected' : '') . ">Rating: Low to High</option>";
echo "<option value='rating_desc' " . ($rating === 'rating_desc' ? 'selected' : '') . ">Rating: High to Low</option>";
echo "</select>";

echo "</form>";
echo "</div>";

echo "<div class='product-container'>";
if (oci_fetch($stmt)) { // Check if there are any results
    do {
        echo "
        <div class='best-p1'>
            <img src='./images/Vegetables-Fruits/" . oci_result($stmt, 'PRODUCTIMAGE') . "' alt='Product Photo'>
            <div class='best-p1-txt'>
                <div class='name-of-p'>
                    <p>" . oci_result($stmt, 'PRODUCTNAME') . "</p>
                </div>
                <div class='name-of-p'>
                    <p> Shop ID : " . oci_result($stmt, 'SHOPID') . "</p>
                </div>
                <div class='rating'>
                    " . generateStars(oci_result($stmt, 'RATING')) . "
                </div>
                <div class='price'>
                    Price : $" . oci_result($stmt, 'PRODUCTPRICE') . "
                </div>
                <div class='buy-now'>
                    <button><a href='#'>Add To Cart</a></button>
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