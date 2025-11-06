<?php
// logout.php - securely destroy session and redirect
session_start();

// clear session array
$_SESSION = [];

// delete session cookie if present
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destroy session
session_destroy();

// optional message after logout (displayed on redirected page if includes/alert.php reads it)
session_start();
$_SESSION['message'] = 'You have been logged out.';
header('Location: ../index.php');
exit();
