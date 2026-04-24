<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 12) {
        $error = "Password must be at least 12 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Password updated successfully!";
            logActivity($user_id, "CHANGE_PASSWORD", "User updated their password");
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    
    <div class="max-w-md mx-auto mt-12 bg-white p-8 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Security Settings</h2>

        <?php if ($success): ?>
            <p class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                <p class="text-xs text-gray-500 mt-1">Minimum 12 characters, including numbers and symbols.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 font-bold">Update Password</button>
        </form>
    </div>
</body>
</html>
