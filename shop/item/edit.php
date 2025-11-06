//item/edit.php
<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

// ✅ Check admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit;
}

// ✅ Validate item ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid item ID.";
    header('Location: ../admin/items.php');
    exit;
}

$itemId = $_GET['id'];

// ✅ Fetch item details
$sql = "SELECT * FROM item WHERE item_id = $itemId AND deleted_at IS NULL";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    $_SESSION['message'] = "Item not found.";
    header('Location: ../admin/items.php');
    exit;
}
?>

<body class="bg-light">
<div class="container py-5">
    <h2 class="fw-bold mb-4">✏️ Edit Item</h2>

    <form method="POST" action="update.php" enctype="multipart/form-data" class="p-4 border rounded bg-white shadow-sm">
        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">

        <!-- Title -->
        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Title</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="<?= htmlspecialchars($item['title']) ?>" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <input type="text" name="description" id="description" class="form-control"
                   value="<?= htmlspecialchars($item['description']) ?>" required>
        </div>

        <!-- Cost Price -->
        <div class="mb-3">
            <label for="cost_price" class="form-label fw-semibold">Cost Price</label>
            <input type="number" name="cost_price" id="cost_price" class="form-control"
                   value="<?= htmlspecialchars($item['cost_price']) ?>" step="0.01" required>
        </div>

        <!-- Sell Price -->
        <div class="mb-3">
            <label for="sell_price" class="form-label fw-semibold">Sell Price</label>
            <input type="number" name="sell_price" id="sell_price" class="form-control"
                   value="<?= htmlspecialchars($item['sell_price']) ?>" step="0.01" required>
        </div>

        <!-- Category -->
        <div class="mb-3">
            <label for="category" class="form-label fw-semibold">Category</label>
            <select name="category" id="category" class="form-select" required>
                <option value="">Select a category</option>
                <option value="Engine" <?= ($item['category'] === 'Engine' ? 'selected' : '') ?>>Engine</option>
                <option value="Electrical" <?= ($item['category'] === 'Electrical' ? 'selected' : '') ?>>Electrical</option>
                <option value="Bodywork" <?= ($item['category'] === 'Bodywork' ? 'selected' : '') ?>>Bodywork</option>
                <option value="Consumables" <?= ($item['category'] === 'Consumables' ? 'selected' : '') ?>>Consumables</option>
                <option value="Other" <?= ($item['category'] === 'Other' ? 'selected' : '') ?>>Other</option>
            </select>
        </div>

        <!-- Stock Quantity -->
        <div class="mb-3">
            <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity</label>
            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control"
                   value="<?= htmlspecialchars($item['stock_quantity']) ?>" min="0" required>
        </div>

        <!-- Current Image -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Current Image</label><br>
            <img src="<?= !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : './images/default.png' ?>"
                 alt="Item Image" width="120" class="rounded border">
        </div>

        <!-- Upload New Image -->
        <div class="mb-4">
            <label for="image_path" class="form-label fw-semibold">Upload New Image (optional)</label>
            <input type="file" name="image_path" id="image_path" class="form-control" accept="image/*">
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="../admin/items.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" name="update">
                <i class="fa-solid fa-check"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
