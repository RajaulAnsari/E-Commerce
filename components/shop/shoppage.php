<section id="shop-page">
    <div class="container">
        <div class="shop-page-title">
            <h2>Shops</h2>
        </div>
        <div class="shops">
            <?php
            include 'connection.php';

            $sql = "SELECT * FROM SHOP";
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt);

            if (oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW) == 0) {
                echo "No shops found!";
            } else {
                foreach ($result as $row) {
                    echo "<div class='shop'>";
                    echo "<a href='./products.php?shop_id=" . $row['SHOP_ID'] . "'>";
                    echo "<img src='./images/shop/" . $row['SHOP_IMAGE'] . "' alt=''>";
                    echo "<h5>" . $row['SHOP_NAME'] . "</h5>";
                    echo "</a>";
                    echo "</div>";
                }
            }

            oci_free_statement($stmt);
            oci_close($conn);
            ?>
        </div>
    </div>
</section>