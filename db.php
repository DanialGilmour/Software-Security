<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'student_portal';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $pass, $db);
} catch (mysqli_sql_exception $e) {
    // Secure Exception Handling: Log error internally, show generic message to user
    error_log($e->getMessage());
    die("An error occurred while connecting to the database. Please try again later.");
}

// Automatic Resource Cleanup
register_shutdown_function(function() use (&$conn) {
    if (isset($conn) && $conn instanceof mysqli) {
        mysqli_close($conn);
    }
});

session_start();

function logActivity($user_id, $action, $description = "") {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = mysqli_prepare($conn, "INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $action, $description, $ip);
    mysqli_stmt_execute($stmt);
}

function hasRole($roles) {
    if (!isset($_SESSION['user_role'])) return false;
    if (is_array($roles)) {
        return in_array($_SESSION['user_role'], $roles);
    }
    return $_SESSION['user_role'] === $roles;
}

// Sensitive Data Encryption
define('ENCRYPTION_KEY', 'SecureKey123!@#SecureKey123!@#12'); // 32 chars for AES-256
define('ENCRYPTION_METHOD', 'AES-256-CBC');

function encryptData($data) {
    $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decryptData($data) {
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}
?>
