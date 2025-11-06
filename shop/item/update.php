//item/update.php

<?php
session_start();
include('../includes/config.php');

// ✅ Ensure only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $item_id        = $_POST['item_id'];
    $title          = trim($_POST['title']);
    $description    = trim($_POST['description']);
    $cost_price     = floatval($_POST['cost_price']);
    $sell_price     = floatval($_POST['sell_price']);
    $category       = trim($_POST['category']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $upload_dir     = '../uploads/items/';

    // ✅ Create uploads folder if missing
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // ✅ Handle image upload (optional)
    $image_path = null;
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image_path']['tmp_name'];
        $basename = basename($_FILES['image_path']['name']);
        $target_path = $upload_dir . time() . '_' . $basename;

        if (move_uploaded_file($tmp_name, $target_path)) {
            $image_path = $target_path;
        }
    }

    // ✅ Update item table
    if ($image_path) {
        $sql = "UPDATE item 
                SET title = ?, description = ?, cost_price = ?, sell_price = ?, image_path = ?, category = ?, stock_quantity = ?
                WHERE item_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssddssii", 
            $title, $description, $cost_price, $sell_price, $image_path, $category, $stock_quantity, $item_id
        );
    } else {
        $sql = "UPDATE item 
                SET title = ?, description = ?, cost_price = ?, sell_price = ?, category = ?, stock_quantity = ?
                WHERE item_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssddsii", 
            $title, $description, $cost_price, $sell_price, $category, $stock_quantity, $item_id
        );
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Item updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating item: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    header('Location: ../admin/items.php');
    exit;
} else {
    $_SESSION['message'] = "Invalid request.";
    header('Location: ../admin/items.php');
    exit;
}
?>
