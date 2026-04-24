<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || !hasRole(['registrar', 'clerk', 'lecturer'])) {
    header("Location: dashboard.php");
    exit();
}

$can_edit = hasRole(['registrar', 'clerk']);

if (isset($_POST['add_subject'])) {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $credits = $_POST['credits'];
    $stmt = mysqli_prepare($conn, "INSERT INTO subjects (code, name, credit_hours) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssi", $code, $name, $credits);
    mysqli_stmt_execute($stmt);
    logActivity($_SESSION['user_id'], "ADD_SUBJECT", "Added subject $code");
}

if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    mysqli_query($conn, "DELETE FROM subjects WHERE id = $id");
    logActivity($_SESSION['user_id'], "DELETE_SUBJECT", "Deleted subject ID $id");
}

$subjects = mysqli_query($conn, "SELECT * FROM subjects");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects - Secure Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <a href="dashboard.php" class="text-blue-600 underline">&larr; Back to Dashboard</a>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
        <?php if ($can_edit): ?>
        <div class="bg-white p-6 rounded shadow h-fit">
            <h3 class="font-bold text-xl mb-4">Add New Subject</h3>
            <form method="POST">
                <div class="mb-4"><label class="block text-sm">Code</label><input name="code" required class="w-full border p-2 rounded"></div>
                <div class="mb-4"><label class="block text-sm">Name</label><input name="name" required class="w-full border p-2 rounded"></div>
                <div class="mb-4"><label class="block text-sm">Credits</label><input name="credits" type="number" required class="w-full border p-2 rounded"></div>
                <button name="add_subject" class="bg-blue-600 text-white w-full py-2 rounded">Add Subject</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="lg:col-span-2 bg-white p-6 rounded shadow">
            <h3 class="font-bold text-xl mb-4">Course Catalog</h3>
            <table class="w-full text-left">
                <thead><tr class="border-b"><th>Code</th><th>Name</th><th>Credits</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($subjects)): ?>
                    <tr class="border-b">
                        <td class="py-2"><?php echo $row['code']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['credit_hours']; ?></td>
                        <td>
                            <?php if ($can_edit): ?>
                            <form method="POST"><input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>"><button type="submit" class="text-red-600">Delete</button></form>
                            <?php else: ?>
                            <span class="text-gray-400 italic">View Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
