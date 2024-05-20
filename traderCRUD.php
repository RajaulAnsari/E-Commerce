<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['tusername'])) {
    // Redirect to the login page if not logged in
    header("Location: tradersignin.php");
    exit();
}

$tusername = $_SESSION['tusername'];



// Fetch the trader's shop ID
$sql = "SELECT SHOP_ID FROM TRADER WHERE TUSER_NAME = :tusername";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":tusername", $tusername);
oci_execute($stmt);
$shopId = oci_fetch_assoc($stmt)['SHOP_ID'];
oci_free_statement($stmt);


// Fetch the trader's product categories
$sql = "SELECT DISTINCT PRODUCT_CATEGORY FROM TRADER WHERE SHOP_ID = :shop_id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":shop_id", $shopId);
oci_execute($stmt);

$categories = [];
while ($row = oci_fetch_assoc($stmt)) {
    $categories[] = $row['PRODUCT_CATEGORY'];
}

oci_free_statement($stmt);

// Function to fetch products
function fetchProducts($conn, $shopId) {
    $sql = "SELECT * FROM PRODUCT WHERE SHOP_ID = :shop_id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":shop_id", $shopId);
    oci_execute($stmt);
    $products = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $products[] = $row;
    }
    oci_free_statement($stmt);
    return $products;
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['product_image'])) {
    $targetDirectory = "./images/Vegetables-Fruits/"; // Directory where images will be stored
    $targetFile = $targetDirectory . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>alert ('File is not an image.')</script>";
        $uploadOk = 0;
    }

    // Move the uploaded file to the destination directory
    if ($uploadOk == 1 && move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, continue with your operations
        if (isset($_POST['add'])) {
            $productName = $_POST['product_name'];
            $productPrice = $_POST['product_price'];
            $productQuantity = $_POST['product_quantity'];
            $allergyInformation = $_POST['allergy_information'];
            $productImage = basename($_FILES["product_image"]["name"]);
            $productStock = $_POST['product_stock'];
            $productCategory = $_POST['product_category'];
            $productDescription = $_POST['product_description'];
            $discountAmount = $_POST['discount_amount'];

            // Fetch next value from the sequence for PRODUCT_ID
            $nextProductIdSql = "SELECT PRODUCT_ID_SEQ.NEXTVAL AS NEXT_ID FROM DUAL";
            $nextProductIdStmt = oci_parse($conn, $nextProductIdSql);
            oci_execute($nextProductIdStmt);
            $nextProductId = oci_fetch_assoc($nextProductIdStmt)['NEXT_ID'];
            oci_free_statement($nextProductIdStmt);

            // Insert the new product with the generated product ID
            $insertSql = "INSERT INTO PRODUCT (PRODUCT_ID, PRODUCT_NAME, PRODUCT_PRICE, PRODUCT_QUANTITY, ALLERGY_INFORMATION, PRODUCT_IMAGE, PRODUCT_STOCK, CATEGORY_NAME, PRODUCT_DESCRIPTION, SHOP_ID) 
                          VALUES (:product_id, :product_name, :product_price, :product_quantity, :allergy_information, :product_image, :product_stock, :product_category, :product_description, :shop_id)";
            $insertStmt = oci_parse($conn, $insertSql);
            oci_bind_by_name($insertStmt, ":product_id", $nextProductId);
            oci_bind_by_name($insertStmt, ":product_name", $productName);
            oci_bind_by_name($insertStmt, ":product_price", $productPrice);
            oci_bind_by_name($insertStmt, ":product_quantity", $productQuantity);
            oci_bind_by_name($insertStmt, ":allergy_information", $allergyInformation);
            oci_bind_by_name($insertStmt, ":product_image", $productImage);
            oci_bind_by_name($insertStmt, ":product_stock", $productStock);
            oci_bind_by_name($insertStmt, ":product_category", $productCategory);
            oci_bind_by_name($insertStmt, ":product_description", $productDescription);
            oci_bind_by_name($insertStmt, ":shop_id", $shopId);
            oci_execute($insertStmt);
            oci_free_statement($insertStmt);

            // Insert into DISCOUNT table if discount is provided
            if (!empty($discountAmount) || !empty($discountPercent)) {
                $nextDiscountIdSql = "SELECT DISCOUNT_ID_SEQ.NEXTVAL AS NEXT_ID FROM DUAL";
                $nextDiscountIdStmt = oci_parse($conn, $nextDiscountIdSql);
                oci_execute($nextDiscountIdStmt);
                $nextDiscountId = oci_fetch_assoc($nextDiscountIdStmt)['NEXT_ID'];
                oci_free_statement($nextDiscountIdStmt);

                $insertDiscountSql = "INSERT INTO DISCOUNT (DISCOUNT_ID, DISCOUNT_AMOUNT, PRODUCT_ID, USER_ID) 
                                      VALUES (:discount_id, :discount_amount, :product_id, :user_id)";
                $insertDiscountStmt = oci_parse($conn, $insertDiscountSql);
                oci_bind_by_name($insertDiscountStmt, ":discount_id", $nextDiscountId);
                oci_bind_by_name($insertDiscountStmt, ":discount_amount", $discountAmount);
                oci_bind_by_name($insertDiscountStmt, ":product_id", $nextProductId);
                oci_bind_by_name($insertDiscountStmt, ":user_id", $_SESSION['user_id']); // Assuming user_id is stored in session
                oci_execute($insertDiscountStmt);
                oci_free_statement($insertDiscountStmt);
            }

            echo "<script>alert('Product added successfully.')</script>";
            echo "<script>window.location = 'traderCRUD.php';</script>";

        } elseif (isset($_POST['update'])) {
            $productId = $_POST['product_id'];
            $productName = $_POST['product_name'];
            $productPrice = $_POST['product_price'];
            $productCategory = $_POST['product_category'];
            $productImage = basename($_FILES["product_image"]["name"]);
            $productDescription = $_POST['product_description'];
            $discountAmount = $_POST['discount_amount'];

            $updatesql = "UPDATE PRODUCT SET PRODUCT_NAME = :product_name, PRODUCT_IMAGE = :product_image, PRODUCT_PRICE = :product_price, CATEGORY_NAME = :product_category, 
                          PRODUCT_DESCRIPTION = :product_description WHERE PRODUCT_ID = :product_id AND SHOP_ID = :shop_id";
            $updatestmt = oci_parse($conn, $updatesql);
            oci_bind_by_name($updatestmt, ":product_name", $productName);
            oci_bind_by_name($updatestmt, ":product_price", $productPrice);
            oci_bind_by_name($updatestmt, ":product_category", $productCategory);
            oci_bind_by_name($updatestmt, ":product_description", $productDescription);
            oci_bind_by_name($updatestmt, ":product_id", $productId);
            oci_bind_by_name($updatestmt, ":product_image", $productImage);
            oci_bind_by_name($updatestmt, ":shop_id", $shopId);
            oci_execute($updatestmt);
            oci_free_statement($updatestmt);

            // Update DISCOUNT table if discount is provided
            if (!empty($discountAmount) || !empty($discountPercent)) {
                $updateDiscountSql = "MERGE INTO DISCOUNT d
                                      USING (SELECT :product_id AS PRODUCT_ID FROM dual) p
                                      ON (d.PRODUCT_ID = p.PRODUCT_ID)
                                      WHEN MATCHED THEN
                                        UPDATE SET d.DISCOUNT_AMOUNT = :discount_amount
                                      WHEN NOT MATCHED THEN
                                        INSERT (DISCOUNT_ID, DISCOUNT_AMOUNT,  PRODUCT_ID, USER_ID)
                                        VALUES (DISCOUNT_ID_SEQ.NEXTVAL, :discount_amount, :product_id, :user_id)";
                $updateDiscountStmt = oci_parse($conn, $updateDiscountSql);
                oci_bind_by_name($updateDiscountStmt, ":discount_amount", $discountAmount);
                oci_bind_by_name($updateDiscountStmt, ":product_id", $productId);
                oci_bind_by_name($updateDiscountStmt, ":user_id", $_SESSION['user_id']); // Assuming user_id is stored in session
                oci_execute($updateDiscountStmt);
                oci_free_statement($updateDiscountStmt);
            }

            echo "<script>alert('Product updated successfully.')</script>";
            echo "<script>window.location = 'traderCRUD.php';</script>";

        }
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
    }
}

// Fetch products after any operations
$products = fetchProducts($conn, $shopId);

// Handle edit and delete actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $productId = $_POST['product_id'];

        $sql = "DELETE FROM PRODUCT WHERE PRODUCT_ID = :product_id AND SHOP_ID = :shop_id";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":product_id", $productId);
        oci_bind_by_name($stmt, ":shop_id", $shopId);
        oci_execute($stmt);
        oci_free_statement($stmt);

        echo "<script>alert('Product deleted successfully.')</script>";
        echo "<script>window.location = 'traderCRUD.php';</script>";
    }
}

oci_close($conn);
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CleckHub | Trader-CRUD</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="icon" href="./images/icons/SVG/SvgIcoLogo.svg">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./components/account/signin/user-trader-signin.css">
    <link rel="stylesheet" href="./components/searchbox/searchbox.css">

    <style>
    .crud-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        text-align: center;
    }

    .crud-form,
    .crud-table {
        width: 80%;
        margin-bottom: 20px;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .crud-form input,
    .crud-form select,
    .crud-form textarea {
        width: calc(100% - 22px);
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .crud-form button {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        background-color: #4CAF50;
        color: white;
        transition: background-color 0.3s;
    }

    .crud-form button:hover {
        background-color: #45a049;
    }

    .crud-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .crud-table th,
    .crud-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .crud-table th {
        background-color: #f2f2f2;
    }

    .crud-table tr:hover {
        background-color: #f1f1f1;
    }

    td button {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        background-color: #4CAF50;
        color: white;
        transition: background-color 0.3s;
    }
    </style>
</head>

<body>

    <?PHP
    include "./components/navbar/nav.php";
    ?>

    <?PHP
    include "./components/searchbox/searchbox.php";
    ?>

    <div class="crud-container">
        <h2>Manage Products</h2>

        <!-- Add/Edit Product Form -->
        <div class="crud-form">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="product_id">
                <input type="text" name="product_name" id="product_name" placeholder="Product Name" required>
                <input type="number" name="product_price" id="product_price" placeholder="Product Price" required
                    step="0.01" min="0">
                <input type="number" name="product_quantity" id="product_quantity" placeholder="Product Quantity"
                    required min="0">
                <input type="text" name="allergy_information" id="allergy_information"
                    placeholder="Allergy Information">
                <input type="file" name="product_image" id="product_image" accept="image/*" placeholder="Product Image">
                <input type="number" name="product_stock" id="product_stock" placeholder="Product Stock" required
                    min="0">
                <select name="product_category" id="product_category" required>
                    <option value="" disabled selected>Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                    <?php endforeach; ?>
                </select>

                <textarea name="product_description" id="product_description" placeholder="Product Description"
                    required></textarea>
                <input type="number" name="discount_amount" id="discount_amount" placeholder="Discount Amount" min="0">
                <button type="submit" name="add" id="add_button">Add Product</button>
                <button type="submit" name="update" id="update_button" style="display:none;">Update Product</button>
            </form>
        </div>

        <!-- Display Products -->
        <div class="crud-table">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['PRODUCT_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($product['PRODUCT_PRICE']); ?></td>
                        <td><?php echo htmlspecialchars($product['CATEGORY_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($product['PRODUCT_DESCRIPTION']); ?></td>
                        <td>
                            <button
                                onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">Edit</button>
                            <form method="post" style="display:inline; ">
                                <input type="hidden" name="product_id" value="<?php echo $product['PRODUCT_ID']; ?>">
                                <button type="submit" name="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function editProduct(product) {
        document.getElementById('product_id').value = product.PRODUCT_ID;
        document.getElementById('product_name').value = product.PRODUCT_NAME;
        document.getElementById('product_price').value = product.PRODUCT_PRICE;
        document.getElementById('product_category').value = product.CATEGORY_NAME;
        document.getElementById('product_description').value = product.PRODUCT_DESCRIPTION;
        document.getElementById('discount_amount').value = product.DISCOUNT_AMOUNT || '';
        document.getElementById('add_button').style.display = 'none';
        document.getElementById('update_button').style.display = 'block';
    }
    </script>

    <?PHP
    include "./components/footer/footer.php";
    ?>

    <script src="./js/main.js" async defer></script>
</body>

</html>