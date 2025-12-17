<?php
session_start();
require('Dbconnection.php');


if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];


$sql = "SELECT * FROM admin WHERE id = $admin_id LIMIT 1";
$result = mysqli_query($con, $sql);
$admin = mysqli_fetch_assoc($result);


if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);

    $update = "UPDATE admin SET name='$name', email='$email', mobile='$mobile' WHERE id=$admin_id";
    if (mysqli_query($con, $update)) {
        echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-4">Admin Profile</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo $admin['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $admin['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile:</label>
                <input type="text" name="mobile" class="form-control" value="<?php echo $admin['mobile']; ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
            <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</div>
</body>
</html>
