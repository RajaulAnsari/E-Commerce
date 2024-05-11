<section id="shop-page">
    <div class="container">
        <div class="shop-page-title">
            <h2>Shops</h2>
        </div>
        <div class="shops">
            <?php
            // Include connection.php file
            include 'connection.php';

            // Query to fetch shop name and image
            $sql = "SELECT * FROM \"SHOP\"";
            $stmt = oci_parse($conn, $sql);

            // Execute the query
            oci_execute($stmt);

            // Loop through the results
            while ($row = oci_fetch_assoc($stmt)) {
                echo "<div class='shop'>";
                echo "<a href='./shop.php?id=" . $row['SHOP_ID'] . "'>";
                echo "<img src='./images/shop/" . $row['SHOP_IMAGE'] . "' alt=''>";
                echo "<h5>" . $row['SHOP_NAME'] . "</h5>";
                echo "</a>";
                echo "</div>";
            }

            // Check if no shops found
            if (oci_num_rows($stmt) == 0) {
                echo "No shops found!";
            }

            // Close the connection
            oci_free_statement($stmt);
            oci_close($conn);
            ?>
        </div>
    </div>
</section>