<?php
session_start();
include("../includes/header.php");
?>

<div class="container-fluid container-lg my-5">
    <?php include("../includes/alert.php"); ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Create an account</h4>

                    <form action="store.php" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= isset($_SESSION['old']['name']) ? htmlspecialchars($_SESSION['old']['name']) : '' ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= isset($_SESSION['old']['email']) ? htmlspecialchars($_SESSION['old']['email']) : '' ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Minimum 4 characters</div>
                        </div>

                        <div class="mb-3">
                            <label for="password2" class="form-label">Confirm password</label>
                            <input type="password" class="form-control" id="password2" name="confirmPass" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">Register</button>
                            <a href="login.php" class="small">Already have an account? Login</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // Clear old values after rendering (so they don't persist indefinitely)
            if (isset($_SESSION['old'])) {
                unset($_SESSION['old']);
            }
            ?>
        </div>
    </div>
</div>

<?php
include("../includes/footer.php");
?>
