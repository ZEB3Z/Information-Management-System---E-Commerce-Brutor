<?php
session_start();
include('../includes/config.php');

// Admin check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['message'] = 'Access denied. Admins only.';
    header('Location: ../index.php');
    exit();
}

// Only allow POST deletion
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: users.php');
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = 'Invalid user ID.';
    header('Location: users.php');
    exit();
}

// Prevent admin from deleting themselves
if ($id === (int)($_SESSION['user_id'])) {
    $_SESSION['message'] = "You cannot delete your own admin account while logged in.";
    header('Location: users.php');
    exit();
}

// Optionally: fetch avatar path to delete file from disk (if you want to remove avatar files)
$avatarSql = "SELECT avatar FROM users WHERE id = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $avatarSql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $avatarPath);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    // If you want to delete the file, check and unlink:
    // if ($avatarPath) { @unlink(__DIR__ . '/../' . $avatarPath); }
}

// Delete the user
$delSql = "DELETE FROM users WHERE id = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $delSql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        $_SESSION['success'] = 'User deleted successfully.';
    } else {
        $_SESSION['message'] = 'Failed to delete user. Try again.';
    }
} else {
    $_SESSION['message'] = 'Database error.';
}

header('Location: users.php');
exit();
