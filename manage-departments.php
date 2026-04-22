<?php
session_start();
include("../config/connection.php");

// 🔐 admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ➕ Insert department
if (isset($_POST['add_department'])) {

    $name = mysqli_real_escape_string($conn, $_POST['department_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    if (!empty($name)) {

        $check = "SELECT * FROM departments WHERE department_name='$name'";
        $res = mysqli_query($conn, $check);

        if (mysqli_num_rows($res) > 0) {
            $msg = "❌ Department already exists";
        } else {

            $insert = "INSERT INTO departments (department_name, description)
                       VALUES ('$name', '$desc')";

            if (mysqli_query($conn, $insert)) {
                $msg = "✅ Department added successfully";
            } else {
                $msg = "❌ Error: " . mysqli_error($conn);
            }
        }
    } else {
        $msg = "❌ Department name required";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
    <link rel="stylesheet" href="../assets/css/admin/manage-departments.css">
</head>
<body>

<div class="page-wrapper">
    <div class="department-card">
        <h2 class="page-title">Manage Departments</h2>

        <?php if (isset($msg)) { ?>
            <p class="message"><?php echo $msg; ?></p>
        <?php } ?>

        <form method="POST" class="department-form">
            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="department_name" placeholder="Enter department name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Enter department description"></textarea>
            </div>

            <button type="submit" name="add_department" class="btn-submit">Add Department</button>
        </form>
    </div>

    <div class="table-card">
        <h3 class="table-title">Department List</h3>

        <div class="table-responsive">
            <table class="department-table">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                </tr>

                <?php
                $query = "SELECT * FROM departments ORDER BY department_id DESC";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['department_id']; ?></td>
                    <td><?php echo $row['department_name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

</body>
</html>