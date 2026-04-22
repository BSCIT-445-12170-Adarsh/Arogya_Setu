<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$adminName  = $_SESSION['name'] ?? 'Admin';
$adminEmail = $_SESSION['email'] ?? 'admin@example.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css"/>
</head>
<body>

    <div class="main-wrapper">

        <aside class="sidebar">
            <h2>Admin Panel</h2>

            <div class="admin-profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($adminName, 0, 1)); ?>
                </div>
                <h3><?php echo htmlspecialchars($adminName); ?></h3>
                <p><?php echo htmlspecialchars($adminEmail); ?></p>
            </div>

            <div class="menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="manage-departments.php">Manage Departments</a>
                <a href="manage-specializations.php">Manage Specializations</a>
                <a href="add-doctor.php">Add Doctor</a>
                <a href="../auth/logout.php" class="logout">Logout</a>
            </div>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Welcome, <?php echo htmlspecialchars($adminName); ?> 👋</h1>
                <p>You are logged in as admin. Manage the hospital system from this dashboard.</p>
            </div>

            <div class="info-boxes">
                <div class="info-box">
                    <h4>Logged In User</h4>
                    <p><?php echo htmlspecialchars($adminName); ?></p>
                </div>

                <div class="info-box">
                    <h4>Role</h4>
                    <p>Administrator</p>
                </div>

                <div class="info-box">
                    <h4>Email</h4>
                    <p style="font-size:16px;"><?php echo htmlspecialchars($adminEmail); ?></p>
                </div>
            </div>

            <div class="cards">
                <div class="card">
                <h3>My Profile</h3>
                <p>View your personal information.</p>
                <a href="profile.php">Open</a>
            </div>

            <div class="card">
                <h3>Edit Profile</h3>
                <p>Update your details anytime.</p>
                <a href="profile-edit.php">Edit</a>
            </div>

                <div class="card">
                    <h3>Manage Specializations</h3>
                    <p>Create and control doctor specializations in a clean structured way.</p>
                    <a href="manage-specializations.php">Open Section</a>
                </div>

                <div class="card">
                    <h3>Manage departments</h3>
                    <p>Create and control doctor departments in a clean structured way.</p>
                    <a href="manage-departments.php">Open Section</a>
                </div>

                <div class="card">
                    <h3>Add Doctor</h3>
                    <p>Register new doctors and store all related information properly.</p>
                    <a href="add-doctor.php">Open Section</a>
                </div>

                <div class="card">
                    <h3>Logout</h3>
                    <p>Securely logout from the admin panel when your work is complete.</p>
                    <a href="../auth/logout.php">Logout</a>
                </div>
            </div>
        </main>

    </div>

</body>
</html>