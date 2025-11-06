<?php
session_start();
include("../includes/header.php");
include("../includes/config.php");

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'You are already logged in.';
    header('Location: ../index.php');
    exit();
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if ($email === '' || $password === '') {
        $_SESSION['message'] = 'Please enter both email and password.';
        header('Location: login.php');
        exit();
    }

    // Prepared statement - compare plaintext password (per project requirement)
    $sql = "SELECT id, name, email, role, avatar FROM users WHERE email = ? AND password = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $id, $db_name, $db_email, $role, $avatar);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // Login success: regenerate session id and set session vars
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$id;
            $_SESSION['email'] = $db_email;
            $_SESSION['role'] = $role ?: 'customer';
            $_SESSION['name'] = $db_name ?: $db_email;   // fallback to email if name empty
            $_SESSION['avatar'] = $avatar;               // may be null

            $_SESSION['message'] = "Login successful. Welcome, " . htmlspecialchars($_SESSION['name']) . "!";
            header('Location: ../index.php');
            exit();
        } else {
            mysqli_stmt_close($stmt);
            $_SESSION['message'] = 'Wrong email or password.';
            // Keep the entered email for convenience
            $_SESSION['old'] = ['email' => $email];
            header('Location: login.php');
            exit();
        }
    } else {
        // Prepare failed
        $_SESSION['message'] = 'Database error. Please try again later.';
        header('Location: login.php');
        exit();
    }
}
?>

<div class="container my-5">
    <?php include("../includes/alert.php"); ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Sign in</h4>

                    <form method="POST" action="login.php" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                required
                                value="<?= isset($_SESSION['old']['email']) ? htmlspecialchars($_SESSION['old']['email']) : '' ?>"
                            />
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                            />
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">Sign in</button>
                            <a href="register.php" class="small">Not a member? Register</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // Clear sticky old input after rendering
            if (isset($_SESSION['old'])) {
                unset($_SESSION['old']);
            }
            ?>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
