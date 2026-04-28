<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || !hasRole(['registrar', 'clerk', 'lecturer'])) {
    header("Location: dashboard.php");
    exit();
}

$can_grade = hasRole(['registrar', 'clerk']);

if (isset($_POST['assign_grade'])) {
    $reg_id = $_POST['reg_id'];
    $grade = $_POST['grade'];
    $stmt = mysqli_prepare($conn, "UPDATE registrations SET grade = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $grade, $reg_id);
    mysqli_stmt_execute($stmt);
    logActivity($_SESSION['user_id'], "ASSIGN_GRADE", "Assigned grade $grade to registration ID $reg_id");
}

$students = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'student'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    <h2 class="text-2xl font-bold mt-4 mb-8">Student Records & Grading</h2>

    <div class="space-y-8">
        <?php while($s = mysqli_fetch_assoc($students)): ?>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8'); ?> <span class="text-gray-500 font-normal">(<?php echo htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8'); ?>)</span></h3>
            
            <?php
            $sid = $s['id'];
            $stmt = mysqli_prepare($conn, "SELECT r.id, s.code, s.name, r.grade FROM registrations r JOIN subjects s ON r.subject_id = s.id WHERE r.user_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $sid);
            mysqli_stmt_execute($stmt);
            $regs = mysqli_stmt_get_result($stmt);
            ?>
            <table class="w-full text-left text-sm">
                <thead><tr class="text-gray-500"><th>Course</th><th>Current Grade</th><th>Assign Grade</th></tr></thead>
                <tbody>
                    <?php while($r = mysqli_fetch_assoc($regs)): ?>
                    <tr class="border-t py-1">
                        <td><?php echo htmlspecialchars($r['code'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($r['grade'] ?? 'Pending', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if ($can_grade): ?>
                            <form method="POST" class="flex gap-2">
                                <input type="hidden" name="reg_id" value="<?php echo $r['id']; ?>">
                                <select name="grade" class="border rounded px-1">
                                    <option value="A" <?php echo $r['grade']=='A'?'selected':'';?>>A</option>
                                    <option value="B" <?php echo $r['grade']=='B'?'selected':'';?>>B</option>
                                    <option value="C" <?php echo $r['grade']=='C'?'selected':'';?>>C</option>
                                    <option value="D" <?php echo $r['grade']=='D'?'selected':'';?>>D</option>
                                    <option value="F" <?php echo $r['grade']=='F'?'selected':'';?>>F</option>
                                </select>
                                <button name="assign_grade" class="text-blue-600 font-bold">Update</button>
                            </form>
                            <?php else: ?>
                            <span class="text-gray-400 italic">No permission to grade</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
