<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Shop</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

  <!-- Custom Style -->
  <link href="/shop/includes/style/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="/shop/home.php"><i class="fa-solid fa-store"></i> My Shop</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="/shop/index.php">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/shop/item/index.php">Items</a>
          </li>

          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <!-- Admin Dropdown -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa-solid fa-user-shield"></i> Admin Menu
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="/shop/item/index.php">Manage Items</a></li>
                  <li><a class="dropdown-item" href="/shop/admin/orders.php">Orders</a></li>
                  <li><a class="dropdown-item" href="/shop/admin/users.php">Users</a></li>
                </ul>
              </li>
            <?php else: ?>
              <!-- Customer Dropdown -->
              <li class="nav-item dropdown">
                <?php
                  // Resolve avatar path robustly:
                  $avatarSession = $_SESSION['avatar'] ?? null; // may be null, 'uploads/avatars/xxx' or 'xxx'
                  if (!empty($avatarSession)) {
                    // normalize: if it already contains 'uploads/' assume it's a relative path; otherwise treat as filename
                    if (strpos($avatarSession, 'uploads/') === 0 || strpos($avatarSession, '/uploads/') !== false) {
                      $avatarPath = '/' . ltrim($avatarSession, '/');                // '/uploads/avatars/..'
                    } elseif (strpos($avatarSession, 'uploads/avatars/') !== false) {
                      $avatarPath = '/' . ltrim($avatarSession, '/');
                    } else {
                      // assume it's a bare filename; point to uploads folder
                      $avatarPath = '/shop/uploads/avatars/' . ltrim($avatarSession, '/');
                    }
                  } else {
                    $avatarPath = 'https://bootdey.com/img/Content/avatar/avatar1.png';
                  }

                  // Display name fallback
                  $displayName = htmlspecialchars($_SESSION['name'] ?? ($_SESSION['email'] ?? 'My Account'));
                ?>
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="<?= htmlspecialchars($avatarPath) ?>" alt="avatar" class="rounded-circle me-2" width="32" height="32" style="object-fit:cover;">
                  <?= $displayName ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                  <li><a class="dropdown-item" href="/shop/user/profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                  <li><a class="dropdown-item" href="/shop/user/myorders.php"><i class="fa-solid fa-box"></i> My Orders</a></li>
                </ul>
              </li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>

        <form class="d-flex me-3" action="/shop/search.php" method="GET">
          <input class="form-control me-2" type="search" placeholder="Search" name="search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <ul class="navbar-nav mb-2 mb-lg-0">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="/shop/user/login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
            <li class="nav-item"><a class="nav-link" href="/shop/user/register.php">Register</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/shop/user/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
          <?php endif; ?>

          <li class="nav-item position-relative">
            <a class="nav-link" href="/shop/view_cart.php">
              <i class="fa-solid fa-cart-shopping"></i>
              <?php if (!empty($_SESSION['cart_products'])): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  <?= count($_SESSION['cart_products']); ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Include Bootstrap JS bundle (contains Popper) so dropdowns and other components work -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
