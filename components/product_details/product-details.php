<?php
include 'connection.php';

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

// Fetch product details
$sql = "SELECT p.*, 
            COALESCE(d.DISCOUNT_AMOUNT, 0) AS DISCOUNT_AMOUNT,
            NVL(r.REVIEW_SCORE, 0) AS REVIEW_SCORE,
            NVL2(r.REVIEW_SCORE, p.product_price - d.DISCOUNT_AMOUNT, p.product_price) AS discounted_price
        FROM product p 
        LEFT JOIN discount d ON p.product_id = d.product_id 
        LEFT JOIN (SELECT product_id, AVG(review_score) AS REVIEW_SCORE FROM review GROUP BY product_id) r
            ON p.product_id = r.product_id 
        WHERE p.product_id = :product_id";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":product_id", $product_id);
oci_execute($stmt);

// Display product details
if ($row = oci_fetch_assoc($stmt)) {
    echo "<div class='container'>";
    echo "<br>";
    echo "<div class='shop-page-title'>";
    echo "<h2>Product Details</h2>";
    echo "</div>";
    echo "<br>";

    echo "<div class='product-container'>";
    echo "<div class='product-item'>";
    
    // Product image
    echo "<div class='product-image'>";
    echo "<img src='./images/Vegetables-Fruits/" . htmlspecialchars($row['PRODUCT_IMAGE']) . "' alt='Product Photo'>";
    echo "</div>";
    
    // Product details
    echo "<div class='product-details'>";
    echo "<div class='name'>Product Name: " . htmlspecialchars($row['PRODUCT_NAME']) . "</div>";
    echo "<div class='price'>Original Price: $" . htmlspecialchars($row['PRODUCT_PRICE']) . "</div>";
    echo "<div class='discount-price'>Discounted Price: $" . htmlspecialchars($row['DISCOUNTED_PRICE']) . "</div>";
    echo "<div class='quantity'>Quantity: " . htmlspecialchars($row['PRODUCT_QUANTITY']) . "</div>";
    echo "<div class='description'>Description: " . htmlspecialchars($row['PRODUCT_DESCRIPTION']) . "</div>";
    echo "<div class='allergy'>Allergy Information: " . htmlspecialchars($row['ALLERGY_INFORMATION']) . "</div>";
    echo "<div class='stock'>Stock: " . htmlspecialchars($row['PRODUCT_STOCK']) . "</div>";
    echo "<div class='category'>Category: " . htmlspecialchars($row['CATEGORY_NAME']) . "</div>";
    echo "<div class='shop'>Shop ID: " . htmlspecialchars($row['SHOP_ID']) . "</div>";
    
    // Add buttons for adding to cart and wishlist
    echo "<div class='action-buttons'>";
    if (isset($_SESSION['uusername'])) {
        echo "<button class='add-to-cart' data-product-id='" . htmlspecialchars($row['PRODUCT_ID']) . "'><i class='bx bxs-cart bx-border-circle bx-tada-hover'></i></button>";
        echo "<button class='add-to-wishlist' data-product-id='" . htmlspecialchars($row['PRODUCT_ID']) . "'><i class='bx bxs-heart bx-border-circle bx-tada-hover'></i></button>";
    } else {
        echo "<button class='addToCartButton'><i class='bx bxs-cart bx-border-circle bx-tada-hover'></i></button>";
        echo "<button class='addToWishlistButton'><i class='bx bxs-heart bx-border-circle bx-tada-hover'></i></button>";
    }
    echo "</div>"; // .product-details
    echo "</div>"; // .action-buttons
    
    echo "</div>"; // .product-item
    echo "</div>"; // .product-container
} else {
    echo "<p>No product found with ID: " . htmlspecialchars($product_id) . "</p>";
}
oci_free_statement($stmt);

// Fetch and display reviews
$sql = "SELECT r.REVIEW_DATE, r.REVIEW_SCORE, r.REVIEW_COMMENT, u.FIRST_NAME
        FROM REVIEW r
        JOIN USER_CLECK u ON r.USER_ID = u.USER_ID
        WHERE r.PRODUCT_ID = :product_id";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":product_id", $product_id);
oci_execute($stmt);

echo "<div class='review-container'>";
echo "<h3>Reviews</h3>";

$hasReviews = false;

while ($row = oci_fetch_assoc($stmt)) {
    $hasReviews = true;
    echo "<div class='review-item'>";
    echo "<div class='review-user'>";
    echo "<span>Name: " . htmlspecialchars($row['FIRST_NAME']) . "</span>";
    echo "</div>";
    echo "<div class='review-date'>" . htmlspecialchars($row['REVIEW_DATE']) . "</div>";
    echo "<div class='review-score'>Rating: " . htmlspecialchars($row['REVIEW_SCORE']) . "</div>";
    echo "<div class='review-comment'>Comment: " . htmlspecialchars($row['REVIEW_COMMENT']) . "</div>";
    echo "</div>"; // .review-item
}

if (!$hasReviews) {
    echo "<p>Not yet reviewed.</p>";
}

echo "</div>"; // .review-container

oci_free_statement($stmt);
oci_close($conn);
?>



<br>

<div class="add-review">
    <h3>Add Your Review</h3>
    <!-- Form to add a review -->
    <form action="" method="POST">
        <!-- Include input fields for review score, comment, etc. -->
        <!-- For example: -->
        <label for="review_score">Rating:</label>
        <input type="number" name="review_score" id="review_score" min="1" max="5" required>
        <label for="review_comment">Comment:</label>
        <textarea name="review_comment" id="review_comment" required></textarea>
        <input type="submit" name="submit_review" value="Submit Review">
    </form>
</div>

<?php
include 'connection.php';

// Get the username from the session or wherever it's stored
$uusername = isset($_SESSION['uusername']) ? $_SESSION['uusername'] : null;

if ($uusername) {
    // Query to retrieve the user ID based on the uusername
    $userQuery = "SELECT USER_ID FROM USER_CLECK WHERE UUSER_NAME = :uusername";

    $stmt = oci_parse($conn, $userQuery);
    oci_bind_by_name($stmt, ":uusername", $uusername);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    if ($row) {
        // User ID found
        $user_id = $row['USER_ID'];

        // Proceed with the review submission process using the retrieved user ID
        if (isset($_POST['submit_review'])) {
            // Process form submission to add review
            $review_score = isset($_POST['review_score']) ? $_POST['review_score'] : null;
            $review_comment = isset($_POST['review_comment']) ? $_POST['review_comment'] : null;

            if ($user_id && $product_id && $review_score && $review_comment) {
                // Perform database insertion
                $sql = "INSERT INTO REVIEW (REVIEW_ID, REVIEW_DATE, REVIEW_SCORE, REVIEW_COMMENT, USER_ID, PRODUCT_ID) 
                        VALUES (REVIEW_ID_SEQ.NEXTVAL, SYSDATE, :review_score, :review_comment, :user_id, :product_id)";

                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ":review_score", $review_score);
                oci_bind_by_name($stmt, ":review_comment", $review_comment);
                oci_bind_by_name($stmt, ":user_id", $user_id);
                oci_bind_by_name($stmt, ":product_id", $product_id);

                if (oci_execute($stmt)) {
                    echo "<script>alert('Review added successfully');</script>";
                    // Redirect the user to a different page
                    echo "<script>window.location.href = 'productdetails.php?product_id=$product_id';</script>";
                } else {
                    echo "<script>alert('Error adding review');</script>";
                }
                

                oci_free_statement($stmt);
            } else {
                echo "<script>alert('Please fill out all fields');</script>";
            }
        }

        // Free statement and close connection
        oci_free_statement($stmt);
        oci_close($conn);
    } else {
        // No user found with the provided uusername
        echo "User not found.";
    }
} else {
    // uusername not set in the session
    echo "uusername not set.";
}
?>



</div>
</br>

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
            // window.location.href = 'productdetails.php';
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