<?php
session_start();
include("../includes/header.php");
include("../includes/config.php");

// Require login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please login to access your profile.';
    header('Location: login.php');
    exit();
}

$uid = (int) $_SESSION['user_id'];

/* ---------------------------
   Handle POST (profile update & avatar upload)
   --------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $town = trim($_POST['town'] ?? '');
    $zipcode = trim($_POST['zipcode'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $errors = [];
    if ($fname === '') $errors[] = 'First name is required.';
    if ($lname === '') $errors[] = 'Last name is required.';

    $avatar_db_path = null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Error uploading the image.';
        } else {
            $maxSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                $errors[] = 'Image exceeds maximum size of 5MB.';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif'
                ];

                if (!array_key_exists($mime, $allowed)) {
                    $errors[] = 'Only JPG, PNG, and GIF images are allowed.';
                } else {
                    $uploadDir = __DIR__ . '/../uploads/avatars/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                    $ext = $allowed[$mime];
                    $basename = 'user_' . $uid . '_' . time();
                    $filename = $basename . '.' . $ext;
                    $targetPath = $uploadDir . $filename;

                    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $errors[] = 'Failed to move the uploaded file.';
                    } else {
                        $avatar_db_path = 'uploads/avatars/' . $filename;
                    }
                }
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: profile.php');
        exit();
    }

    $fullName = trim($fname . ' ' . $lname);

    if ($avatar_db_path !== null) {
        $sql = "UPDATE users SET name = ?, avatar = ? WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssi', $fullName, $avatar_db_path, $uid);
    } else {
        $sql = "UPDATE users SET name = ? WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $fullName, $uid);
    }

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Update or insert customer info
    $checkSql = "SELECT customer_id FROM customer WHERE fname = ? AND lname = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, 'ss', $fname, $lname);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    $exists = (mysqli_stmt_num_rows($checkStmt) === 1);
    mysqli_stmt_close($checkStmt);

    if ($exists) {
        $updateSql = "UPDATE customer SET title = ?, addressline = ?, town = ?, zipcode = ?, phone = ? WHERE fname = ? AND lname = ? LIMIT 1";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, 'sssssss', $title, $address, $town, $zipcode, $phone, $fname, $lname);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    } else {
        $insertSql = "INSERT INTO customer (title, fname, lname, addressline, town, zipcode, phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($insertStmt, 'sssssss', $title, $fname, $lname, $address, $town, $zipcode, $phone);
        mysqli_stmt_execute($insertStmt);
        mysqli_stmt_close($insertStmt);
    }

    $_SESSION['name'] = $fullName;
    if ($avatar_db_path !== null) {
        $_SESSION['avatar'] = $avatar_db_path;
    }

    $_SESSION['success'] = 'Profile saved successfully.';
    header('Location: profile.php');
    exit();
}

/* ---------------------------
   Load existing data for display
   --------------------------- */

$userSql = "SELECT id, name, email, avatar FROM users WHERE id = ? LIMIT 1";
$userStmt = mysqli_prepare($conn, $userSql);
mysqli_stmt_bind_param($userStmt, 'i', $uid);
mysqli_stmt_execute($userStmt);
mysqli_stmt_bind_result($userStmt, $db_id, $db_name, $db_email, $db_avatar);
mysqli_stmt_fetch($userStmt);
mysqli_stmt_close($userStmt);

$firstName = '';
$lastName = '';
if (!empty($db_name)) {
    $parts = preg_split('/\s+/', trim($db_name));
    if (count($parts) === 1) $firstName = $parts[0];
    else {
        $firstName = array_shift($parts);
        $lastName = implode(' ', $parts);
    }
}

$customer = null;
if ($firstName !== '' || $lastName !== '') {
    $custSql = "SELECT title, fname, lname, addressline, town, zipcode, phone
                FROM customer WHERE fname = ? AND lname = ? LIMIT 1";
    $custStmt = mysqli_prepare($conn, $custSql);
    mysqli_stmt_bind_param($custStmt, 'ss', $firstName, $lastName);
    mysqli_stmt_execute($custStmt);
    $custResult = mysqli_stmt_get_result($custStmt);
    if ($custResult && mysqli_num_rows($custResult) === 1) {
        $customer = mysqli_fetch_assoc($custResult);
    }
    mysqli_stmt_close($custStmt);
}

$pref = [
    'fname' => $customer['fname'] ?? $firstName,
    'lname' => $customer['lname'] ?? $lastName,
    'title' => $customer['title'] ?? '',
    'address' => $customer['addressline'] ?? '',
    'town' => $customer['town'] ?? '',
    'zipcode' => $customer['zipcode'] ?? '',
    'phone' => $customer['phone'] ?? '',
    'email' => $db_email,
    'avatar' => $db_avatar ?? null
];

// 👇 ensure the newest uploaded avatar is displayed immediately
if (!empty($_SESSION['avatar'])) {
    $pref['avatar'] = $_SESSION['avatar'];
}

?>

<div class="container-xl px-4 mt-4">
    <?php include("../includes/alert.php"); ?>

    <nav class="nav nav-borders">
        <a class="nav-link active ms-0" href="#">Profile</a>
    </nav>

    <hr class="mt-0 mb-4">

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- LEFT COLUMN -->
            <div class="col-xl-4">
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body text-center">
                        <?php
                        $avatarUrl = $pref['avatar']
                            ? ('/shop/' . $pref['avatar'] . '?v=' . time())
                            : 'http://bootdey.com/img/Content/avatar/avatar1.png';
                        ?>
                        <img id="avatarPreview" class="img-account-profile rounded-circle mb-2"
                             src="<?php echo htmlspecialchars($avatarUrl); ?>"
                             alt="Profile avatar" style="width:140px; height:140px; object-fit:cover;">

                        <div class="small font-italic text-muted mb-3">JPG or PNG no larger than 5 MB</div>
                        <input id="avatarInput" class="form-control mb-3" type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif">
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small mb-1" for="email">Email (readonly)</label>
                            <input class="form-control" id="email" type="email" readonly
                                   value="<?php echo htmlspecialchars($pref['email']); ?>">
                        </div>

                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputFirstName">First name</label>
                                <input class="form-control" id="inputFirstName" type="text"
                                       name="fname" value="<?php echo htmlspecialchars($pref['fname']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputLastName">Last name</label>
                                <input class="form-control" id="inputLastName" type="text"
                                       name="lname" value="<?php echo htmlspecialchars($pref['lname']); ?>" required>
                            </div>
                        </div>

                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="address">Address</label>
                                <input class="form-control" id="address" type="text"
                                       name="address" value="<?php echo htmlspecialchars($pref['address']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="town">Town</label>
                                <input class="form-control" id="town" type="text"
                                       name="town" value="<?php echo htmlspecialchars($pref['town']); ?>">
                            </div>
                        </div>

                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="zip">Zip code</label>
                                <input class="form-control" id="zip" type="text"
                                       name="zipcode" value="<?php echo htmlspecialchars($pref['zipcode']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="title">Title</label>
                                <input class="form-control" id="title" type="text"
                                       name="title" value="<?php echo htmlspecialchars($pref['title']); ?>">
                            </div>
                        </div>

                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputPhone">Phone number</label>
                                <input class="form-control" id="inputPhone" type="tel"
                                       name="phone" value="<?php echo htmlspecialchars($pref['phone']); ?>">
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit" name="submit">Save all changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<!-- ✅ Live preview for avatar -->
<script>
document.getElementById('avatarInput')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('avatarPreview');
        preview.src = URL.createObjectURL(file);
        preview.onload = () => URL.revokeObjectURL(preview.src);
    }
});
</script>

<?php include("../includes/footer.php"); ?>
