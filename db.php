<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'student_portal';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_set_cookie_params([
    'httponly' => true, // Prevents JavaScript from stealing your session
    'samesite' => 'Strict' // Prevents CSRF
]);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

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
?>
