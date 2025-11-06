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

// Fetch users
$sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<div class="container py-5">
    <?php include('../includes/alert.php'); ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Users</h3>
    </div>

    <?php if ($res && mysqli_num_rows($res) > 0): ?>
        <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td class="text-end">
                        <a href="view_user.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fa-solid fa-eye"></i> View
                        </a>

                        <!-- Delete button: uses delete_user.php (POST via small form) -->
                        <form action="delete_user.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this user? This action cannot be undone');">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No users found.</div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
