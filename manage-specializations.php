<?php
session_start();
include("../config/connection.php");

// 🔐 admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ➕ Insert specialization
if (isset($_POST['add_specialization'])) {

    $dept_id = $_POST['department_id'];
    $name = mysqli_real_escape_string($conn, $_POST['specialization_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    if (!empty($name) && !empty($dept_id)) {

        $check = "SELECT * FROM specializations 
                  WHERE specialization_name='$name' AND department_id='$dept_id'";
        $res = mysqli_query($conn, $check);

        if (mysqli_num_rows($res) > 0) {
            $msg = "❌ Specialization already exists in this department";
        } else {

            $insert = "INSERT INTO specializations (department_id, specialization_name, description)
                       VALUES ('$dept_id', '$name', '$desc')";

            if (mysqli_query($conn, $insert)) {
                $msg = "✅ Specialization added successfully";
            } else {
                $msg = "❌ Error: " . mysqli_error($conn);
            }
        }
    } else {
        $msg = "❌ All fields required";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Specializations</title>
    <link rel="stylesheet" href="../assets/css/admin/manage-specialization.css">
</head>
<body>

<div class="page-wrapper">
    <div class="spec-card">
        <h2 class="page-title">Manage Specializations</h2>

        <?php if (isset($msg)) { ?>
            <p class="message"><?php echo $msg; ?></p>
        <?php } ?>

        <!-- Add Form -->
        <form method="POST" class="spec-form">

            <div class="form-group">
                <label>Select Department</label>
                <select name="department_id" required>
                    <option value="">-- Select Department --</option>

                    <?php
                    $dept_query = "SELECT * FROM departments";
                    $dept_result = mysqli_query($conn, $dept_query);

                    while ($dept = mysqli_fetch_assoc($dept_result)) {
                    ?>
                        <option value="<?php echo $dept['department_id']; ?>">
                            <?php echo $dept['department_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Specialization Name</label>
                <input type="text" name="specialization_name" placeholder="Enter specialization name" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Enter description"></textarea>
            </div>

            <button type="submit" name="add_specialization" class="btn-submit">
                Add Specialization
            </button>
        </form>
    </div>

    <div class="table-card">
        <h3 class="table-title">Specialization List</h3>

        <div class="table-responsive">
            <table class="spec-table">
                <tr>
                    <th>ID</th>
                    <th>Department</th>
                    <th>Specialization</th>
                    <th>Description</th>
                </tr>

                <?php
                $query = "SELECT s.*, d.department_name 
                          FROM specializations s
                          JOIN departments d ON s.department_id = d.department_id
                          ORDER BY s.specialization_id DESC";

                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['specialization_id']; ?></td>
                    <td><?php echo $row['department_name']; ?></td>
                    <td><?php echo $row['specialization_name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

</body>
</html>