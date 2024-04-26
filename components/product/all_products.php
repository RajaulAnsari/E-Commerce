<?php
echo "<div class='container'>";
include 'connection.php';

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'none';
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
} elseif ($sort === 'rating_asc') {
    $sql .= " ORDER BY rating ASC";
} elseif ($sort === 'rating_desc') {
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

echo "<label value='none'>&nbsp &nbsp &nbsp Sort by Price & Rating : </label>";
echo "<select name='sort' id='sort' onchange='this.form.submit()'>";
echo "<option value='none' " . ($sort === 'none' ? 'selected' : '') . ">None</option>";
echo "<option value='price_asc' " . ($sort === 'price_asc' ? 'selected' : '') . ">Price: Low to High</option>";
echo "<option value='price_desc' " . ($sort === 'price_desc' ? 'selected' : '') . ">Price: High to Low</option>";
echo "<option value='rating_asc' " . ($sort === 'rating_asc' ? 'selected' : '') . ">Rating: Low to High</option>";
echo "<option value='rating_desc' " . ($sort === 'rating_desc' ? 'selected' : '') . ">Rating: High to Low</option>";
echo "</select>";

echo "</form>";
echo "</div>";

echo "<div class='product-container'>";
$index = 0;
if (oci_fetch($stmt)) { // Check if there are any results
    do {
        echo "
        <div class='product-item'>
            <img src='./images/Vegetables-Fruits/" . oci_result($stmt, 'PRODUCTIMAGE') . "' alt='Product Photo' class='product-image'>
            <div class='product-details'>
                <div class='product-name'>" . oci_result($stmt, 'PRODUCTNAME') . "</div>
                <div class='shop-id'>Shop ID : " . oci_result($stmt, 'SHOPID') . "</div>
                <div class='rating'>" . generateStars(oci_result($stmt, 'RATING')) . "</div>
                <div class='price'>Price : $" . oci_result($stmt, 'PRODUCTPRICE') . "</div>
                <div class='add-to-cart'>
                    <form action='add_to_cart.php' method='POST'>
                        <input type='hidden' name='product_id' value='" . oci_result($stmt, 'PRODUCTID') . "'>
                        <button type='submit' name='add_to_cart'>Add To Cart</button>
                    </form>
                </div>
            </div>
        </div>
        ";
        $index++;
    } while (oci_fetch($stmt));
} else {
    echo "<p>No products found.</p>";
}
for ($i=$index; $i<3; $i++) {
    echo "<div class='empty-product-item'></div>";
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