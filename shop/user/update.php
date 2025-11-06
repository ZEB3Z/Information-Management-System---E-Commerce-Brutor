<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $town = $_POST['town'];
    $zip_code = $_POST['zip_code'];
    $phone_number = $_POST['phone_number'];
    $title = $_POST['title'];

    // Image upload handling
    $avatarFileName = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $targetDir = "../uploads/avatars/";
        $avatarFileName = time() . '_' . basename($_FILES["avatar"]["name"]);
        $targetFilePath = $targetDir . $avatarFileName;

        // Check and move uploaded file
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFilePath)) {
            // Update avatar filename in DB
            $updateAvatar = ", avatar='$avatarFileName'";
        } else {
            $updateAvatar = "";
        }
    } else {
        $updateAvatar = "";
    }

    $sql = "UPDATE users 
            SET first_name='$first_name',
                last_name='$last_name',
                address='$address',
                town='$town',
                zip_code='$zip_code',
                phone_number='$phone_number',
                title='$title'
                $updateAvatar
            WHERE id=$user_id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Profile updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating profile: " . $conn->error;
    }

    header("Location: profile.php");
    exit();
}
?>
