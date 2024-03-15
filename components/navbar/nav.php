<section class="nav">
<div class="container">
  <nav class="navbar">
    <div class="navElements">
      <div class="logo">
        <a href="./index.php">
          <img src="./images/logo.jpg" alt="logo">
        </a>
      </div>
      <div class="navbar-list">
        <div class="icon"><a href="../E-Commerce/index.php"><i class='bx bx-home-alt '></i>Home</a></div>
        <div class="icon"><a href="../E-Commerce/shops.php"><i class='bx bx-store'></i>Shops</a></div>
        <div class="icon"><a href="../E-Commerce/products.php"><i class='bx bx-package'></i>Products</a></div>
        <div class="icon"><a href="../E-Commerce/about.php"><i class='bx bx-info-circle'></i>About</a></div>
        <div class="icon"><a href="../E-Commerce/account.php"><i class='bx bx-user'></i>Account</a></div>
        <div class="close nav-toggle">
          <i class='bx bx-x'></i>
        </div>
        <div class="icons">
          <div class="icon search-icon-nav" onclick="showSearch()"><a href="#"><i
                class='bx bx-search-alt'></i>Search</a></div>

          <div class="icon cart"><a href="../cart.php"><i class='bx bx-cart'></i>Cart
              <?php
              if (isset($_SESSION['cart'])) {
                  $count = count($_SESSION['cart']);
                echo "<span>" . $count . "</span>";
              }
              else {
                  echo "<span>" . 0 . "</span>";
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
</div>
</section>
<div class="search-bar">
  <form action="" class="search-form">
    <input type="search" placeholder="Search" />
    <button type="submit"><i class='bx bx-search-alt'></i></button>
  </form>
</div>
</nav>
