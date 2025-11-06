<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

// Admin-only check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['message'] = 'Access denied. Admins only.';
    header('Location: ../index.php');
    exit();
}

// Validation of id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = 'Invalid user ID.';
    header('Location: users.php');
    exit();
}

// Fetch user record (read-only)
$sql = "SELECT id, name, email, role, avatar, created_at, updated_at FROM users WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$user) {
    $_SESSION['message'] = 'User not found.';
    header('Location: users.php');
    exit();
}
?>
<div class="container py-5">
    <?php include('../includes/alert.php'); ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                <?php
                $avatar = $user['avatar'] ? ('/shop/' . ltrim($user['avatar'], '/')) : 'https://bootdey.com/img/Content/avatar/avatar1.png';
                ?>
                <img src="<?= htmlspecialchars($avatar) ?>" alt="avatar" class="rounded-circle me-3" width="80" height="80" style="object-fit:cover;">
                <div>
                    <h4 class="mb-0"><?= htmlspecialchars($user['name']) ?></h4>
                    <div class="text-muted"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>

            <dl class="row">
                <dt class="col-sm-3">Role</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($user['role']) ?></dd>

                <dt class="col-sm-3">Registered</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($user['created_at']) ?></dd>

                <dt class="col-sm-3">Last updated</dt>
                <dd class="col-sm-9"><?= htmlspecialchars($user['updated_at'] ?? '-') ?></dd>
            </dl>

            <div class="mt-4">
                <a href="users.php" class="btn btn-outline-secondary">Back to users</a>
                <!-- Admin can delete from here as well -->
                <form action="delete_user.php" method="POST" style="display:inline-block; margin-left:8px;" onsubmit="return confirm('Delete this user?');">
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                    <button class="btn btn-danger" type="submit">Delete user</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
