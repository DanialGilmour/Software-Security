<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, name, password, role, mfa_secret, failed_attempts, locked_until FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        if ($user['locked_until'] !== null && strtotime($user['locked_until']) > time()) {
            $error = "Account is locked. Try again later.";
            logActivity($user['id'], "LOGIN_LOCKED", "Failed login on locked account");
        } else {
            if (password_verify($password, $user['password'])) {
                $reset_stmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                mysqli_stmt_bind_param($reset_stmt, "i", $user['id']);
                mysqli_stmt_execute($reset_stmt);

                if (!empty($user['mfa_secret'])) {
                    $_SESSION['mfa_pending_user_id'] = $user['id'];
                    $_SESSION['mfa_pending_user_name'] = $user['name'];
                    $_SESSION['mfa_pending_user_role'] = $user['role'];
                    header("Location: verify_mfa.php");
                    exit();
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    logActivity($user['id'], "LOGIN", "User logged in successfully");
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $attempts = $user['failed_attempts'] + 1;
                $locked_until = null;
                if ($attempts >= 5) {
                    $locked_until = date('Y-m-d H:i:s', time() + 15 * 60);
                    logActivity($user['id'], "ACCOUNT_LOCKED", "Account locked due to multiple failed login attempts");
                }
                $update_stmt = mysqli_prepare($conn, "UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
                mysqli_stmt_bind_param($update_stmt, "isi", $attempts, $locked_until, $user['id']);
                mysqli_stmt_execute($update_stmt);
                
                $error = "Invalid email or password.";
                if ($attempts >= 5) {
                    $error = "Account is locked. Try again later.";
                }
            }
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Portal - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Student Portal Login</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" required class="w-full border p-2 rounded">
            </div>
            <div class="mb-6">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" required class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Login</button>
        </form>
    </div>
</body>
</html>
