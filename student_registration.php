<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // GLOBAL CSRF CHECK
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    // Handle Registration
    if (isset($_POST['register_id'])) {
        $sub_id = $_POST['register_id'];
        $stmt = mysqli_prepare($conn, "INSERT INTO registrations (user_id, subject_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $sub_id);
        mysqli_stmt_execute($stmt);
        logActivity($user_id, "REGISTER_COURSE", "Course ID: $sub_id");
    }

    // Handle Drop
    if (isset($_POST['drop_id'])) {
        $reg_id = $_POST['drop_id'];
        // SECURE: Only delete if the registration belongs to the logged-in user
        $stmt = mysqli_prepare($conn, "DELETE FROM registrations WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $reg_id, $user_id);
        mysqli_stmt_execute($stmt);
        logActivity($user_id, "DROP_COURSE", "Registration ID: $reg_id");
    }
}

// Get available subjects safely
$stmt_avail = mysqli_prepare($conn, "SELECT * FROM subjects WHERE id NOT IN (SELECT subject_id FROM registrations WHERE user_id = ?)");
mysqli_stmt_bind_param($stmt_avail, "i", $user_id);
mysqli_stmt_execute($stmt_avail);
$available = mysqli_stmt_get_result($stmt_avail);

// Get my subjects safely
$stmt_my = mysqli_prepare($conn, "SELECT r.id as reg_id, s.code, s.name, s.credit_hours FROM registrations r JOIN subjects s ON r.subject_id = s.id WHERE r.user_id = ?");
mysqli_stmt_bind_param($stmt_my, "i", $user_id);
mysqli_stmt_execute($stmt_my);
$my_subjects = mysqli_stmt_get_result($stmt_my);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    <h2 class="text-2xl font-bold mt-4 mb-8">Course Registration</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-xl mb-4">Available Courses</h3>
            <table class="w-full text-left">
                <thead><tr class="border-b"><th>Code</th><th>Name</th><th>Credits</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($available)): ?>
                    <tr class="border-b py-2">
                        <td><?php echo $row['code']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['credit_hours']; ?></td>
                        <td>
                            <form method="POST"><input type="hidden" name="register_id" value="<?php echo $row['id']; ?>"><button type="submit" class="text-blue-600 hover:underline">Register</button></form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-xl mb-4">My Courses</h3>
            <table class="w-full text-left">
                <thead><tr class="border-b"><th>Code</th><th>Name</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($my_subjects)): ?>
                    <tr class="border-b py-2">
                        <td><?php echo $row['code']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>
                            <form method="POST"><input type="hidden" name="drop_id" value="<?php echo $row['reg_id']; ?>"><button type="submit" class="text-red-600 hover:underline">Drop</button></form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
