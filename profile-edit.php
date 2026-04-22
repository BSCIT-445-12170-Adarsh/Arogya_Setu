<?php
session_start();
include("../config/connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* admin basic + extra profile data */
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

/* agar admins table me row hi nahi hai to create kar do */
if (empty($admin['user_id'])) {
    $insert_admin = mysqli_query($conn, "
        INSERT INTO admins (user_id, profile_image, address, city, state, country, pincode, created_at)
        VALUES ('$user_id', '', '', '', '', '', '', NOW())
    ");

    if (!$insert_admin) {
        die("Insert Error: " . mysqli_error($conn));
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
}

if (isset($_POST['update_profile'])) {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);

    $profile_image = $admin['profile_image'] ?? '';

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['profile_image']['name'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_size = $_FILES['profile_image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            if ($file_size <= 2 * 1024 * 1024) {
                $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
                $upload_path = "../uploads/admin/" . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    if (!empty($admin['profile_image']) && file_exists("../uploads/admin/" . $admin['profile_image'])) {
                        unlink("../uploads/admin/" . $admin['profile_image']);
                    }
                    $profile_image = $new_file_name;
                } else {
                    $msg = "Image upload failed";
                }
            } else {
                $msg = "Image size must be less than 2MB";
            }
        } else {
            $msg = "Only JPG, JPEG, PNG, and WEBP files are allowed";
        }
    }

    if (empty($msg)) {
        $update = mysqli_query($conn, "
            UPDATE admins SET
                profile_image = '$profile_image',
                address = '$address',
                city = '$city',
                state = '$state',
                country = '$country',
                pincode = '$pincode'
            WHERE user_id = '$user_id'
        ");

        if ($update) {
            $msg = "Profile updated successfully";

            $query = mysqli_query($conn, "
                SELECT u.full_name, u.email, u.phone, a.*
                FROM users u
                LEFT JOIN admins a ON u.id = a.user_id
                WHERE u.id = '$user_id' AND u.role = 'admin'
                LIMIT 1
            ");

            if ($query) {
                $admin = mysqli_fetch_assoc($query);
            }
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Profile</title>
    <link rel="stylesheet" href="../assets/css/admin/profile-edit.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Edit Admin Profile</h2>

        <?php if (!empty($msg)) { ?>
            <p><?php echo htmlspecialchars($msg); ?></p>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Full Name</label>
            <input type="text" value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>" readonly>

            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" readonly>

            <label>Phone</label>
            <input type="text" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" readonly>

            <label>Profile Image</label>
            <input type="file" name="profile_image" accept="image/*">

            <?php if (!empty($admin['profile_image'])) { ?>
                <img src="../uploads/admin/<?php echo htmlspecialchars($admin['profile_image']); ?>"
                     alt="Admin Profile Image"
                     width="100"
                     height="100"
                     style="object-fit: cover; border-radius: 50%; display: block; margin: 10px 0;">
            <?php } ?>

            <label>Address</label>
            <textarea name="address"><?php echo htmlspecialchars($admin['address'] ?? ''); ?></textarea>

            <label>City</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($admin['city'] ?? ''); ?>">

            <label>State</label>
            <input type="text" name="state" value="<?php echo htmlspecialchars($admin['state'] ?? ''); ?>">

            <label>Country</label>
            <input type="text" name="country" value="<?php echo htmlspecialchars($admin['country'] ?? ''); ?>">

            <label>Pincode</label>
            <input type="text" name="pincode" value="<?php echo htmlspecialchars($admin['pincode'] ?? ''); ?>">

            <button type="submit" name="update_profile" class="btn btn-success">Update</button>
            <a href="profile.php" class="btn btn-primary">Back</a>
        </form>
    </div>
</div>

</body>
</html>