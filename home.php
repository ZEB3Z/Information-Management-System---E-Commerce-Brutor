<?php
include('./includes/header.php');
include('./includes/config.php');
?>

<div class="container mt-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h1 class="display-5 fw-bold">Welcome to <span class="text-success">My Shop</span>!</h1>
      <p class="lead mt-3">
        Discover great deals and quality products — all in one place.  
        Whether you’re looking to shop or manage your store, you’re in the right place.
      </p>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="/shop/user/login.php" class="btn btn-success btn-lg mt-3">
          <i class="fa-solid fa-right-to-bracket"></i> Get Started
        </a>
      <?php else: ?>
        <a href="/shop/item/index.php" class="btn btn-success btn-lg mt-3">
          <i class="fa-solid fa-bag-shopping"></i> Start Shopping
        </a>
      <?php endif; ?>
    </div>

    <div class="col-md-6 text-center">
      <img src="/shop/banner.PNG" alt="Shop Banner" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
    </div>
  </div>

  <hr class="my-5">

  <div class="text-center mb-4">
    <h2 class="fw-bold">Featured Items</h2>
    <p class="text-muted">A quick look at some of our latest products</p>
  </div>

  <div class="row">
    <?php
    // Fetch 4 latest items from DB
    $sql = "SELECT item_id, description, sell_price, image_path FROM item ORDER BY item_id DESC LIMIT 4";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0):
      while ($item = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 mb-4">
          <div class="card h-100 shadow-sm">
            <img src="/shop/item/<?php echo htmlspecialchars($item['image_path']); ?>" class="card-img-top" alt="Item Image" style="height: 180px; object-fit: cover;">
            <div class="card-body text-center">
              <h5 class="card-title"><?php echo htmlspecialchars($item['description']); ?></h5>
              <p class="card-text text-success fw-bold">₱<?php echo number_format($item['sell_price'], 2); ?></p>
              <a href="/shop/index.php" class="btn btn-outline-success btn-sm">View More</a>
            </div>
          </div>
        </div>
      <?php endwhile;
    else: ?>
      <p class="text-center text-muted">No featured items available right now.</p>
    <?php endif; ?>
  </div>
</div>

<?php include('./includes/footer.php'); ?>
