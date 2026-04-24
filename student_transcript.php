<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT s.code, s.name, s.credit_hours, r.grade FROM registrations r JOIN subjects s ON r.subject_id = s.id WHERE r.user_id = $user_id";
$result = mysqli_query($conn, $query);

// Simple CGPA Calc
$total_points = 0; $total_credits = 0;
$grade_map = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, 'F' => 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transcript - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    <h2 class="text-2xl font-bold mt-4 mb-8">Academic Transcript</h2>

    <div class="bg-white p-6 rounded shadow max-w-4xl">
        <table class="w-full text-left mb-8">
            <thead><tr class="border-b bg-gray-50"><th>Code</th><th>Course</th><th>Credits</th><th>Grade</th></tr></thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="border-b py-2">
                    <td><?php echo $row['code']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['credit_hours']; ?></td>
                    <td class="font-bold"><?php echo $row['grade'] ?? 'Pending'; ?></td>
                </tr>
                <?php 
                    if (isset($grade_map[$row['grade']])) {
                        $total_points += $grade_map[$row['grade']] * $row['credit_hours'];
                        $total_credits += $row['credit_hours'];
                    }
                endwhile; ?>
            </tbody>
        </table>

        <div class="bg-gray-50 p-4 rounded text-right">
            <span class="text-gray-600 text-sm">Cumulative GPA:</span>
            <span class="text-3xl font-bold ml-4"><?php echo $total_credits > 0 ? number_format($total_points / $total_credits, 2) : '0.00'; ?></span>
        </div>
    </div>
</body>
</html>
