//item/store.php


<?php
session_start();
include('../includes/config.php');

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Store user input for repopulation if validation fails
$_SESSION['title'] = trim($_POST['title'] ?? '');
$_SESSION['desc']  = trim($_POST['description'] ?? '');
$_SESSION['cost']  = trim($_POST['cost_price'] ?? '');
$_SESSION['sell']  = trim($_POST['sell_price'] ?? '');

if (isset($_POST['submit'])) {
    $title = $_SESSION['title'];
    $desc  = $_SESSION['desc'];
    $cost  = $_SESSION['cost'];
    $sell  = $_SESSION['sell'];
    $targetPath = null;
    $category = $_POST['category'] ?? '';
$stockQty = $_POST['stock_quantity'] ?? 0;


    $hasError = false;

    // --- VALIDATION ---
    if (empty($title)) {
        $_SESSION['titleError'] = "Please enter an item title.";
        $hasError = true;
    }

    if (empty($desc)) {
        $_SESSION['descError'] = "Please enter an item description.";
        $hasError = true;
    }

    if (empty($cost) || !is_numeric($cost) || $cost < 0) {
        $_SESSION['costError'] = "Invalid cost price.";
        $hasError = true;
    }

    if (empty($sell) || !is_numeric($sell) || $sell < 0) {
        $_SESSION['sellError'] = "Invalid sell price.";
        $hasError = true;
    }

    // --- IMAGE UPLOAD HANDLING ---
    if (!empty($_FILES['image_path']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['image_path']['type'];
        $fileSize = $_FILES['image_path']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['imageError'] = "Only JPG and PNG files are allowed.";
            $hasError = true;
        } elseif ($fileSize > 5 * 1024 * 1024) { // 5MB limit
            $_SESSION['imageError'] = "Image file size exceeds 5MB.";
            $hasError = true;
        } else {
            // Create images directory if not exists
            $uploadDir = __DIR__ . '/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique file name
            $uniqueName = uniqid('item_', true) . '.' . pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION);
            $targetPath = 'images/' . $uniqueName;

            // Move file to target directory
            if (!move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadDir . $uniqueName)) {
                $_SESSION['imageError'] = "Failed to upload image.";
                $hasError = true;
            }
        }
    }

    // Redirect back if there are validation errors
    if ($hasError) {
        header("Location: create.php");
        exit;
    }

    // --- DATABASE INSERT ---
    $stmt = $conn->prepare("INSERT INTO item (title, description, cost_price, sell_price, image_path, category, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssddssi", $title, $desc, $cost, $sell, $targetPath, $category, $stockQty);

    $insertSuccess = $stmt->execute();
    $stmt->close();

    if ($insertSuccess) {
        // Clear session data
        unset($_SESSION['title'], $_SESSION['desc'], $_SESSION['cost'], $_SESSION['sell']);
        $_SESSION['success'] = "Item added successfully!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to add item. Please try again.";
        header("Location: create.php");
        exit;
    }
} else {
    header("Location: create.php");
    exit;
}
?>
