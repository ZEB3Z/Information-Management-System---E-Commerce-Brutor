<?php
session_start();
include('../includes/adminHeader.php');
include('../includes/config.php');

// ✅ Security check — only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit;
}

// ✅ Handle search functionality
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "
    SELECT i.item_id, i.title, i.description, i.cost_price, i.sell_price, i.image_path, 
           COALESCE(s.quantity, 0) AS quantity, i.created_at, i.updated_at
    FROM item i
    LEFT JOIN stock s ON i.item_id = s.item_id
";
if (!empty($keyword)) {
    $sql .= " WHERE i.title LIKE '%$keyword%' OR i.description LIKE '%$keyword%'";
}
$sql .= " ORDER BY i.item_id DESC";

$result = mysqli_query($conn, $sql);
$itemCount = $result ? mysqli_num_rows($result) : 0;
?>

<body class="bg-light">
<div class="container py-5">

    <!-- Header section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">🗂️ Manage Items (<?= $itemCount ?>)</h2>
        <a href="../item/create.php" class="btn btn-primary btn-lg shadow-sm">
            <i class="fa-solid fa-plus"></i> Add Item
        </a>
    </div>

    <!-- Search form -->
    <form class="mb-4" method="GET" action="items.php">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search items..." 
                   value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>

    <!-- Items Grid -->
    <?php if ($itemCount > 0): ?>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= !empty($row['image_path']) ? $row['image_path'] : '../item/images/default.png' ?>"
                             class="card-img-top"
                             alt="Item Image"
                             style="height: 200px; object-fit: cover; border-bottom: 1px solid #eee;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['title'] ?: $row['description']) ?></h5>
                            <p class="card-text mb-1"><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
                            <p class="card-text mb-1"><strong>Cost Price:</strong> ₱<?= number_format($row['cost_price'], 2) ?></p>
                            <p class="card-text mb-1"><strong>Sell Price:</strong> ₱<?= number_format($row['sell_price'], 2) ?></p>
                            <p class="card-text mb-1"><strong>Stock:</strong> <?= (int)$row['quantity'] ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-3">
                            <a href="../item/edit.php?id=<?= $row['item_id'] ?>" 
                               class="btn btn-sm btn-outline-primary me-2">
                                <i class="fa-regular fa-pen-to-square"></i> Edit
                            </a>
                            <a href="../item/delete.php?id=<?= $row['item_id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete this item?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
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
