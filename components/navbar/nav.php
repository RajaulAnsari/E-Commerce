<section class="nav">
    <div class="container">
        <nav class="navbar">
            <div class="navElements">
                <div class="logo">
                    <a href="./index.php">
                        <img src="./images/icons/FullIcoLogo.png" alt="logo">
                    </a>
                </div>
                <div class="navbar-list">
                    <div class="icon"><a href="./index.php"><i class='bx bx-home-alt'></i>Home</a></div>
                    <div class="icon"><a href="./shops.php"><i class='bx bx-store'></i>Shops</a></div>
                    <div class="icon"><a href="./products.php"><i class='bx bx-package'></i>Products</a></div>
                    <div class="icon"><a href="./about.php"><i class='bx bx-info-circle'></i>About</a></div>
                    <div class="icon search-icon-nav" onclick="openSearch()"><a href="#"><i
                                class='bx bx-search-alt'></i>Search</a></div>
                    <div class="close nav-toggle"><i class='bx bx-x'></i></div>
                    <div class="icons">
                        <div class="icon">
                            <?php if (isset($_SESSION['uusername'])) {
                                echo "<a href='./userhomepage.php'><i class='bx bx-user'></i>" . ucfirst($_SESSION['uusername']) . "</a>";
                            }elseif (isset($_SESSION['tusername'])) {
                                echo "<a href='./traderhomepage.php'><i class='bx bx-user'></i>" . ucfirst($_SESSION['tusername']) . "</a>";
                            }
                            else{
                            echo "<a href='./account.php'><i class='bx bx-user'></i>Account</a>";
                            }
                            ?>
                        </div>
                        <div class="icon cart">
                            <a href="./cart.php">
                                <i class='bx bx-cart'></i>Cart
                                <?php
                                    if (isset($_SESSION['uusername'])) {
                                        include('./connection.php');
                                        $username = $_SESSION['uusername'];
                                        
                                        // Query USER_CLECK table to get USER_ID based on username
                                        $user_query = "SELECT USER_ID FROM USER_CLECK WHERE UUSER_NAME = '$username'";
                                        $user_stmt = oci_parse($conn, $user_query);
                                        oci_execute($user_stmt);
                                        $user_row = oci_fetch_assoc($user_stmt);
                                        $user_id = $user_row ? $user_row['USER_ID'] : null;
                                        oci_free_statement($user_stmt);
                                        
                                        if ($user_id !== null) {
                                            // Query to count items in the user's cart based on USER_ID
                                            $cart_query = "SELECT COUNT(cp.PRODUCT_ID) AS NUMBER_OF_ROWS 
                                                        FROM CART_PRODUCT cp 
                                                        JOIN CART c ON cp.CART_ID = c.CART_ID 
                                                        WHERE c.USER_ID = '$user_id'";
                                            
                                            $stmt = oci_parse($conn, $cart_query);
                                            oci_define_by_name($stmt, 'NUMBER_OF_ROWS', $number_of_rows);
                                            oci_execute($stmt);
                                            oci_fetch($stmt);
                                            
                                            echo "<span>" . $number_of_rows . "</span>";
                                        } else {
                                            echo "<span>0</span>";
                                        }
                                    } else {
                                        if (isset($_SESSION['cart'])) {
                                            $count = count($_SESSION['cart']);
                                            echo "<span>$count</span>";
                                        } else {
                                            echo "<span>0</span>";
                                        }
                                    }
                                    ?>


                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="fourline nav-toggle">
                <i class='bx bx-menu'></i>
            </div>
        </nav>
    </div>
</section>