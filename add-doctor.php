<?php
session_start();
include("../config/connection.php");

// 🔐 admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ➕ Add doctor
if (isset($_POST['add_doctor'])) {

    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];

    $dept_id = $_POST['department_id'];
    $spec_id = $_POST['specialization_id'];

    // check existing
    $check = "SELECT * FROM users WHERE email='$email' OR phone='$phone'";
    $res = mysqli_query($conn, $check);

    if (mysqli_num_rows($res) > 0) {
        $msg = "❌ Email or Phone already exists";
    } else {

        // insert user
        $insertUser = "INSERT INTO users (full_name, email, phone, password, role)
                       VALUES ('$name', '$email', '$phone', '$password', 'doctor')";

        if (mysqli_query($conn, $insertUser)) {

            $user_id = mysqli_insert_id($conn);

            // insert doctor
            $insertDoctor = "INSERT INTO doctors (user_id, department_id, specialization_id)
                             VALUES ('$user_id', '$dept_id', '$spec_id')";

            if (mysqli_query($conn, $insertDoctor)) {
                $msg = "✅ Doctor added successfully";
            } else {
                $msg = "❌ Doctor insert error";
            }

        } else {
            $msg = "❌ User insert error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Doctor</title>
    <link rel="stylesheet" href="../assets/css/admin/add-doctor.css">
</head>
<body>

<div class="page-wrapper">
    <div class="doctor-card">
        <h2 class="page-title">Add Doctor</h2>

        <?php if (isset($msg)) { ?>
            <p class="message"><?php echo $msg; ?></p>
        <?php } ?>

        <form method="POST" class="doctor-form">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter full name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter email" required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="Enter phone number" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="form-group">
                <label>Select Department</label>
                <select name="department_id" required>
                    <option value="">-- Select Department --</option>
                    <?php
                    $dept = mysqli_query($conn, "SELECT * FROM departments");
                    while ($d = mysqli_fetch_assoc($dept)) {
                        echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Specialization</label>
                <select name="specialization_id" required>
                    <option value="">-- Select Specialization --</option>
                    <?php
                    $spec = mysqli_query($conn, "SELECT * FROM specializations");
                    while ($s = mysqli_fetch_assoc($spec)) {
                        echo "<option value='{$s['specialization_id']}'>{$s['specialization_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="add_doctor" class="btn-submit">Add Doctor</button>
        </form>
    </div>
</div>

</body>
</html>