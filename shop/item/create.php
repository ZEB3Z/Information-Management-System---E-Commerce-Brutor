//item/create.php


<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa-solid fa-plus"></i> Add New Item</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="store.php" enctype="multipart/form-data">

                        <!-- Item Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Item Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter item title" required
                                   value="<?= isset($_SESSION['title']) ? htmlspecialchars($_SESSION['title']) : '' ?>">
                            <?php if (isset($_SESSION['titleError'])): ?>
                                <small class="text-danger"><?= $_SESSION['titleError']; unset($_SESSION['titleError']); ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Enter a short item description"><?= isset($_SESSION['desc']) ? htmlspecialchars($_SESSION['desc']) : '' ?></textarea>
                            <?php if (isset($_SESSION['descError'])): ?>
                                <small class="text-danger"><?= $_SESSION['descError']; unset($_SESSION['descError']); ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Cost Price -->
                        <div class="mb-3">
                            <label for="cost" class="form-label fw-semibold">Cost Price</label>
                            <input type="number" step="0.01" class="form-control" id="cost" name="cost_price"
                                   placeholder="Enter item cost price" required
                                   value="<?= isset($_SESSION['cost']) ? htmlspecialchars($_SESSION['cost']) : '' ?>">
                            <?php if (isset($_SESSION['costError'])): ?>
                                <small class="text-danger"><?= $_SESSION['costError']; unset($_SESSION['costError']); ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Sell Price -->
                        <div class="mb-3">
                            <label for="sell" class="form-label fw-semibold">Sell Price</label>
                            <input type="number" step="0.01" class="form-control" id="sell" name="sell_price"
                                   placeholder="Enter selling price" required
                                   value="<?= isset($_SESSION['sell']) ? htmlspecialchars($_SESSION['sell']) : '' ?>">
                            <?php if (isset($_SESSION['sellError'])): ?>
                                <small class="text-danger"><?= $_SESSION['sellError']; unset($_SESSION['sellError']); ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Category -->
<div class="mb-3">
    <label for="category" class="form-label fw-semibold">Category</label>
    <select class="form-select" id="category" name="category" required>
        <option value="">Select a category</option>
        <option value="Engine">Engine</option>
        <option value="Electrical">Electrical</option>
        <option value="Bodywork">Bodywork</option>
        <option value="Consumables">Consumables</option>
        <option value="Other">Other</option>
    </select>
</div>

<!-- Stock Quantity -->
<div class="mb-3">
    <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity</label>
    <input type="number" min="0" class="form-control" id="stock_quantity" name="stock_quantity" placeholder="Enter initial stock" required>
</div>


                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold">Upload Image</label>
                            <input class="form-control" type="file" id="image" name="image_path" accept="image/*">
                            <div class="form-text">Accepted formats: JPG, PNG (max 5MB)</div>
                            <?php if (isset($_SESSION['imageError'])): ?>
                                <small class="text-danger"><?= $_SESSION['imageError']; unset($_SESSION['imageError']); ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fa-solid fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fa-solid fa-check"></i> Save Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
