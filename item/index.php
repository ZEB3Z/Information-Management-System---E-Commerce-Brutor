//item/index.php

<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

// Determine user login state
$userStatus = "You are browsing as a <strong>guest</strong>.";
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $userStatus = "You are logged in as an <strong>admin</strong>.";
    } else {
        $userStatus = "You are logged in as a <strong>customer</strong>.";
    }
}

// Handle search, category, and sort inputs
$keyword   = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$category  = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort      = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';

// --- Build dynamic query ---
$sql = "SELECT * FROM item WHERE 1=1";

// Apply search
if (!empty($keyword)) {
    $sql .= " AND (title LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%')";
}

// Apply category filter
if (!empty($category) && $category !== 'all') {
    $sql .= " AND category = '" . mysqli_real_escape_string($conn, $category) . "'";
}

// Apply sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY sell_price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY sell_price DESC";
        break;
    case 'newest':
        $sql .= " ORDER BY created_at DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    default:
        $sql .= " ORDER BY title ASC";
        break;
}

$result = mysqli_query($conn, $sql);
$itemCount = mysqli_num_rows($result);

// --- Fetch distinct categories for the dropdown ---
$categoryQuery = mysqli_query($conn, "SELECT DISTINCT category FROM item WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC");
$categories = [];
while ($cat = mysqli_fetch_assoc($categoryQuery)) {
    $categories[] = $cat['category'];
}
?>

<body class="bg-light">
<div class="container py-5">

    <!-- Login status banner -->
    <div class="alert alert-info text-center fw-semibold shadow-sm mb-4">
        <?= $userStatus ?>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center fw-semibold"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['message'])): ?>
        <div class="alert alert-danger text-center fw-semibold"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <!-- Header section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Items (<?= $itemCount ?>)</h2>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="create.php" class="btn btn-primary btn-lg shadow-sm">
                <i class="fa-solid fa-plus"></i> Add Item
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters section -->
    <form method="GET" class="row g-3 align-items-center mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search items..." value="<?= htmlspecialchars($keyword) ?>">
        </div>

        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="all">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($cat === $category) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="default" <?= ($sort === 'default') ? 'selected' : '' ?>>Sort by: Default (A–Z)</option>
                <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest First</option>
                <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Oldest First</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">
                <i class="fa-solid fa-filter"></i> Apply
            </button>
        </div>
    </form>

    <!-- Item Display Section -->
    <?php if ($itemCount > 0): ?>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= !empty($row['image_path']) ? $row['image_path'] : '../item/images/default.png' ?>"
                             class="card-img-top img-fluid"
                             alt="<?= htmlspecialchars($row['title']) ?>"
                             style="height: 200px; object-fit: cover; border-bottom: 1px solid #eee;">

                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text text-muted small mb-2"><?= htmlspecialchars($row['description']) ?></p>
                            <p class="card-text small text-muted mb-1">
                                Category: <strong><?= htmlspecialchars($row['category'] ?? 'N/A') ?></strong>
                            </p>
                            <p class="card-text small <?= ($row['stock_quantity'] <= 0 ? 'text-danger' : 'text-success') ?>">
                                Stock: <?= (int)($row['stock_quantity'] ?? 0) ?>
                            </p>
                            <p class="card-text fw-semibold text-success mb-0">
                                ₱<?= number_format($row['sell_price'], 2) ?>
                            </p>
                        </div>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <div class="card-footer bg-transparent border-0 text-center pb-3">
                                <a href="edit.php?id=<?= $row['item_id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </a>
                                <a href="delete.php?id=<?= $row['item_id'] ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Are you sure you want to delete this item?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-4">No items found.</div>
    <?php endif; ?>

</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
