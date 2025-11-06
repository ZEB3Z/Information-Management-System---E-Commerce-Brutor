//item/delete.php

<?php
session_start();
include('../includes/config.php');

// Ensure only admins can delete
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit;
}

// Check if valid ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid item ID.";
    header('Location: index.php');
    exit;
}

$itemId = intval($_GET['id']);

// Step 1: Delete related stock first
mysqli_query($conn, "DELETE FROM stock WHERE item_id = $itemId");

// Step 2: Delete the item itself
$deleteItem = mysqli_query($conn, "DELETE FROM item WHERE item_id = $itemId");

// Step 3: Redirect with confirmation
if ($deleteItem) {
    $_SESSION['success'] = "Item deleted successfully.";
} else {
    $_SESSION['message'] = "Failed to delete item.";
}

header('Location: index.php');
exit;
?>