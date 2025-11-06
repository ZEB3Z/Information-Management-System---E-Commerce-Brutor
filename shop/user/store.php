<?php
session_start();
include("../includes/config.php");

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: register.php');
    exit();
}

// Collect input (safe defaults)
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmPass = trim($_POST['confirmPass'] ?? '');

// Keep old form values so register.php can repopulate inputs
$_SESSION['old'] = ['name' => $name, 'email' => $email];

// Basic validation
if ($name === '' || $email === '' || $password === '' || $confirmPass === '') {
    $_SESSION['message'] = 'Please fill out all required fields.';
    header('Location: register.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = 'Please provide a valid email address.';
    header('Location: register.php');
    exit();
}

if ($password !== $confirmPass) {
    $_SESSION['message'] = 'Passwords do not match.';
    header('Location: register.php');
    exit();
}

if (strlen($password) < 4) {
    $_SESSION['message'] = 'Password is too short (min 4 characters).';
    header('Location: register.php');
    exit();
}

// Check for duplicate email
$checkSql = "SELECT id FROM users WHERE email = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $checkSql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = 'That email is already registered. Try logging in.';
        header('Location: register.php');
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    // fallback error
    $_SESSION['message'] = 'Database error (prepare failed).';
    header('Location: register.php');
    exit();
}

// Insert user (password stored in plain text as requested)
$insertSql = "INSERT INTO users (`name`, `email`, `password`, `role`, `created_at`) VALUES (?, ?, ?, 'customer', NOW())";
if ($stmt = mysqli_prepare($conn, $insertSql)) {
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
    $ok = mysqli_stmt_execute($stmt);

    if ($ok) {
        $newId = mysqli_insert_id($conn);

        // Set session values to log user in
        $_SESSION['user_id'] = $newId;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'customer';

        // Clean up sticky data
        if (isset($_SESSION['old'])) {
            unset($_SESSION['old']);
        }

        $_SESSION['message'] = 'Registration successful. Welcome!';
        header('Location: profile.php');
        exit();
    } else {
        // Insert failed
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = 'Failed to create account. Please try again.';
        header('Location: register.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'Database error (insert prepare failed).';
    header('Location: register.php');
    exit();
}
