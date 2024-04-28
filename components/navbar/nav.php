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
                            <?php if (isset($_SESSION['USER_NAME'])) {
                                echo "<a href='./userhomepage.php'><i class='bx bx-user'></i>" . ucfirst($_SESSION['USER_NAME']) . "</a>";
                            }else{
                            echo "<a href='./account.php'><i class='bx bx-user'></i>Account</a>";
                            }
                            ?>
                        </div>
                        <div class="icon cart">
                            <a href="./cart.php">
                                <i class='bx bx-cart'></i>Cart
                                <?php
                                    if (isset($_SESSION['cart'])) {
                                        $count = count($_SESSION['cart']);
                                        echo "<span>" . $count . "</span>";
                                    } else {
                                        echo "<span>0</span>";
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