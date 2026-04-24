<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || !hasRole(['registrar', 'maintenance'])) {
    header("Location: dashboard.php");
    exit();
}

$logs = mysqli_query($conn, "SELECT l.*, u.email FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Logs - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    <h2 class="text-2xl font-bold mt-4 mb-8">System Audit Logs</h2>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50"><tr><th class="p-4">Time</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
            <tbody>
                <?php while($l = mysqli_fetch_assoc($logs)): ?>
                <tr class="border-t">
                    <td class="p-4"><?php echo $l['created_at']; ?></td>
                    <td><?php echo $l['email'] ?? 'System'; ?></td>
                    <td class="font-bold"><?php echo $l['action']; ?></td>
                    <td><?php echo $l['description']; ?></td>
                    <td class="text-gray-500"><?php echo $l['ip_address']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
