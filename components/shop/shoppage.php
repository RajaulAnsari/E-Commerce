<section id="shop-page">
    <div class="container">
        <div class="shop-page-title">
            <h2>Shops</h2>
        </div>
        <br>
        <div class="shops">
            <?php
            include 'connection.php';

            $sql = "SELECT * FROM SHOP";
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt);

            $result = [];
            oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

            if (empty($result)) {
                echo "No shops found!";
            } else {
                foreach ($result as $row) {
                    if ($row['SHOP_ADMIN_VERIFICATION'] == 1) {
                        echo "<div class='shop'>";
                        echo "<a href='./products.php?shop_id=" . $row['SHOP_ID'] . "'>";
                        echo "<img src='./images/shop/" . $row['SHOP_IMAGE'] . "' alt=''>";
                        echo "<hr style='border-width: 2px; color:green;'>";
                        echo "<h5>" . $row['SHOP_NAME'] . "</h5>";
                        echo "</a>";
                        echo "</div>";
                    }
                }
            }

            oci_free_statement($stmt);
            oci_close($conn);
            ?>
        </div>
        <br>
    </div>
</section>