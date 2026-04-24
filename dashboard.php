<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// Fetch stats or data based on role
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 flex justify-between">
        <h1 class="font-bold">Student Portal</h1>
        <div>
            <span>Welcome, <?php echo $_SESSION['user_name']; ?> (<?php echo ucfirst($role); ?>)</span>
            <a href="logout.php" class="ml-4 underline">Logout</a>
        </div>
    </nav>

    <div class="p-8 max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php if ($role != 'student'): ?>
                <?php if (hasRole(['registrar', 'lecturer', 'clerk'])): ?>
                <div class="bg-white p-6 rounded shadow border-l-4 border-indigo-500">
                    <h3 class="font-bold text-lg mb-2">Subject Management</h3>
                    <p class="text-gray-600 mb-4">Add, edit, or delete subjects from the catalog.</p>
                    <a href="admin_subjects.php" class="text-indigo-600 font-bold hover:underline">Manage Subjects &rarr;</a>
                </div>
                <div class="bg-white p-6 rounded shadow border-l-4 border-indigo-500">
                    <h3 class="font-bold text-lg mb-2">Student Records</h3>
                    <p class="text-gray-600 mb-4">View students and assign grades.</p>
                    <a href="admin_students.php" class="text-indigo-600 font-bold hover:underline">Manage Students &rarr;</a>
                </div>
                <?php endif; ?>

                <?php if (hasRole(['registrar', 'maintenance'])): ?>
                <div class="bg-white p-6 rounded shadow border-l-4 border-indigo-500">
                    <h3 class="font-bold text-lg mb-2">Audit Logs</h3>
                    <p class="text-gray-600 mb-4">Monitor system activity and security logs.</p>
                    <a href="admin_logs.php" class="text-indigo-600 font-bold hover:underline">View Logs &rarr;</a>
                </div>
                <?php endif; ?>

                <?php if (hasRole('registrar')): ?>
                <div class="bg-white p-6 rounded shadow border-l-4 border-indigo-500">
                    <h3 class="font-bold text-lg mb-2">User Management</h3>
                    <p class="text-gray-600 mb-4">Register new students (Ends in 1) or Admins (Ends in 2-8).</p>
                    <a href="admin_user_management.php" class="text-indigo-600 font-bold hover:underline">Manage Users &rarr;</a>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="bg-white p-6 rounded shadow border-l-4 border-green-500">
                    <h3 class="font-bold text-lg mb-2">Course Registration</h3>
                    <p class="text-gray-600 mb-4">Browse and register for available subjects.</p>
                    <a href="student_registration.php" class="text-green-600 font-bold hover:underline">Register Now &rarr;</a>
                </div>
                <div class="bg-white p-6 rounded shadow border-l-4 border-green-500">
                    <h3 class="font-bold text-lg mb-2">My Transcript</h3>
                    <p class="text-gray-600 mb-4">View your registered subjects and grades.</p>
                    <a href="student_transcript.php" class="text-green-600 font-bold hover:underline">View Transcript &rarr;</a>
                </div>
                <div class="bg-white p-6 rounded shadow border-l-4 border-green-500">
                    <h3 class="font-bold text-lg mb-2">Security Settings</h3>
                    <p class="text-gray-600 mb-4">Update your password or MFA settings.</p>
                    <a href="settings.php" class="text-green-600 font-bold hover:underline">Settings &rarr;</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
