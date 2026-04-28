<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || !hasRole('registrar')) {
    header("Location: dashboard.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];
    $role = $_POST['role'];

    // Secure Input Validation & Buffer Size Checking
    if (strlen($name) > 100 || strlen($email) > 100 || strlen($raw_password) > 255) {
        $error = "Input exceeds allocated buffer size.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($raw_password) < 12 || !preg_match('/[A-Z]/', $raw_password) || !preg_match('/[^a-zA-Z\d]/', $raw_password)) {
        // Password Complexity
        $error = "Password must be at least 12 characters long, contain at least one uppercase letter, and one special character.";
    } else {
        $password = password_hash($raw_password, PASSWORD_BCRYPT);

    // Vertical Access ID Logic: Last digit determines access level
    $last_digit_map = [
        'registrar'   => 2,
        'clerk'       => 4,
        'lecturer'    => 6,
        'maintenance' => 8,
        'student'     => 1
    ];
    
    $target_digit = $last_digit_map[$role];
    
    $query = "SELECT MAX(id) as max_id FROM users WHERE id % 10 = $target_digit";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['max_id']) {
        $new_id = $row['max_id'] + 10;
    } else {
        $new_id = $target_digit;
    }

    // Insert with manual ID
    $stmt = mysqli_prepare($conn, "INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issss", $new_id, $name, $email, $password, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = "User registered successfully with ID: $new_id";
        logActivity($_SESSION['user_id'], "REGISTER_USER", "Registered new $role: $email (ID: $new_id)");
    } else {
        $error = "Registration failed: " . mysqli_error($conn);
    }
    } // Close the else block for input validation
}

$users = mysqli_query($conn, "SELECT id, name, email, role FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
        <!-- Registration Form -->
        <div class="bg-white p-6 rounded shadow h-fit">
            <h3 class="font-bold text-xl mb-4">Register New User</h3>
            <?php if ($success): ?><p class="text-green-600 mb-4"><?php echo $success; ?></p><?php endif; ?>
            <?php if ($error): ?><p class="text-red-600 mb-4"><?php echo $error; ?></p><?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div><label class="block text-sm">Full Name</label><input name="name" required class="w-full border p-2 rounded"></div>
                <div><label class="block text-sm">Email</label><input name="email" type="email" required class="w-full border p-2 rounded"></div>
                <div><label class="block text-sm">Initial Password</label><input name="password" type="password" required class="w-full border p-2 rounded"></div>
                <div>
                    <label class="block text-sm">Role (Vertical Access Level)</label>
                    <select name="role" class="w-full border p-2 rounded">
                        <option value="student">Student (Ends in 1)</option>
                        <option value="registrar">Registrar (Ends in 2) - Full Access</option>
                        <option value="clerk">Clerk (Ends in 4) - Subjects & Grading</option>
                        <option value="lecturer">Lecturer (Ends in 6) - View Only (No Grading)</option>
                        <option value="maintenance">Maintenance (Ends in 8) - Logs Only</option>
                    </select>
                </div>
                <button name="register_user" class="bg-indigo-600 text-white w-full py-2 rounded font-bold">Register User</button>
            </form>
        </div>

        <!-- User List -->
        <div class="lg:col-span-2 bg-white p-6 rounded shadow">
            <h3 class="font-bold text-xl mb-4">System Users</h3>
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50">
                    <tr><th class="p-2">ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($users)): ?>
                    <tr class="border-b">
                        <td class="p-2 font-mono font-bold"><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <span class="px-2 py-1 rounded text-xs font-bold <?php echo $row['role']=='admin'?'bg-purple-100 text-purple-700':'bg-green-100 text-green-700';?>">
                                <?php echo htmlspecialchars(strtoupper($row['role']), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
