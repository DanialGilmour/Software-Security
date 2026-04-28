<?php
require 'db.php';
require 'mfa.php';

if (!isset($_SESSION['mfa_pending_user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['mfa_code'];
    $user_id = $_SESSION['mfa_pending_user_id'];
    
    $stmt = mysqli_prepare($conn, "SELECT id, name, role, mfa_secret FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        if (MFA::verifyCode($user['mfa_secret'], $code)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            unset($_SESSION['mfa_pending_user_id']);
            unset($_SESSION['mfa_pending_user_name']);
            unset($_SESSION['mfa_pending_user_role']);
            
            logActivity($user['id'], "LOGIN", "User logged in successfully with 2FA");
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid 2FA code.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Portal - 2FA Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Two-Factor Authentication</h2>
        <p class="text-gray-600 mb-4 text-center">Please enter the 6-digit code from your authenticator app.</p>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-6">
                <label class="block mb-1 font-medium">Authentication Code</label>
                <input type="text" name="mfa_code" required pattern="\d{6}" maxlength="6" class="w-full border p-2 rounded text-center text-xl tracking-widest" placeholder="000000">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 font-bold">Verify</button>
            <div class="mt-4 text-center">
                <a href="index.php" class="text-blue-500 hover:underline text-sm">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>
