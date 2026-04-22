<?php
session_start();
include("../config/connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_SESSION['admin_id'])) {
    $user_id = $_SESSION['admin_id'];
} else {
    die("Admin session not found. Please login again.");
}

$query = mysqli_query($conn, "
    SELECT u.full_name, u.email, u.phone, a.*
    FROM users u
    LEFT JOIN admins a ON u.id = a.user_id
    WHERE u.id = '$user_id' AND u.role = 'admin'
    LIMIT 1
");
if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    die("Admin profile not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../assets/css/admin/admin-profile.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="profile-container">
    <div class="profile-card">

        <h2 class="profile-title">Admin Profile</h2>

        <div class="profile-top">
            <div class="profile-image">
                <?php if (!empty($admin['profile_image'])) { ?>
                    <img src="../uploads/admin/<?php echo htmlspecialchars($admin['profile_image']); ?>" alt="Admin Image">
                <?php } else { ?>
                    <div class="no-image">No Image</div>
                <?php } ?>
            </div>

            <div class="profile-basic">
                <h3><?php echo htmlspecialchars($admin['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($admin['email']); ?></p>
                <p><?php echo htmlspecialchars($admin['phone']); ?></p>
            </div>
        </div>

        <div class="profile-details">
            <div class="detail-box">
                <span>Full Name</span>
                <p><?php echo htmlspecialchars($admin['full_name']); ?></p>
            </div>

            <div class="detail-box">
                <span>Email</span>
                <p><?php echo htmlspecialchars($admin['email']); ?></p>
            </div>

            <div class="detail-box">
                <span>Phone</span>
                <p><?php echo htmlspecialchars($admin['phone']); ?></p>
            </div>

            <div class="detail-box">
                <span>Address</span>
                <p><?php echo htmlspecialchars($admin['address'] ?? ''); ?></p>
            </div>

            <div class="detail-box">
                <span>City</span>
                <p><?php echo htmlspecialchars($admin['city'] ?? ''); ?></p>
            </div>

            <div class="detail-box">
                <span>State</span>
                <p><?php echo htmlspecialchars($admin['state'] ?? ''); ?></p>
            </div>

            <div class="detail-box">
                <span>Country</span>
                <p><?php echo htmlspecialchars($admin['country'] ?? ''); ?></p>
            </div>

            <div class="detail-box">
                <span>Pincode</span>
                <p><?php echo htmlspecialchars($admin['pincode'] ?? ''); ?></p>
            </div>
        </div>

        <div class="profile-actions">
            <a href="profile-edit.php" class="btn">Edit Profile</a>
        </div>

    </div>
</div>

</body>
</html>